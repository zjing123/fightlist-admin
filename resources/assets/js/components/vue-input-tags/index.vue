<template>
  <div>
    <div class="tagsContainer form-control clearfix" :class="{'form-control-focus': focused}" @click="inputFocus">
      <div class="tagList">
        <div class="tag" v-for="(tag, index) in tagsCopy" :key="index" ref="tag">
          <div class="tagItem" :class="[{ 'deletion-mark': isMarked(index), hidden: tagsEditStatus[index] }]" @click.stop="performEditTag(index)">
            <span>{{tag.text}}</span>
            <div>
              <i class="icon-delete" @click.stop="performDeleteTag(index)"></i>
            </div>
          </div>
          <input
            type="text"
            v-if="tagsEditStatus[index]"
            :maxlength="maxlength"
            v-model="tag.text"
            @input="createChangedTag(index, tag)"
            @blur="cancelEdit(index)"
            @keydown.enter="performSaveTag(index)"
            class="updateInput"
            @click.stop
            />
        </div>
        <div class="new-tag-input-wrapper">
          <input
            type="text"
            class="new-tag-input"
            ref="newTagInput"
            @blur="blurInput"
            :placeholder="placeholder"
            v-model="newTag"
            :maxlength="maxlength"
            @keydown.enter="performAddTags(newTag)"
            @keydown.8="invokeDelete"
            :disabled="disabled"
            />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { createTag, createTags, createClasses } from './create-tags';

export default {
  props: {
    tags: {
      type: Array,
      default: []
    },
    placeholder: {
      type: String,
      default: ''
    },
    readOnly: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    maxlength: {
      type: Number,
    },
    separators: {
      type: Array,
      default: () => [';'],
      validator (value) {
        return !value.some(s => {
          const invalidType = typeof s !== 'string';
          if(invalidType) console.warn('[component][input-tags][separators]Separators must be type of string.Found:', s);
          return invalidType;
        })
      }
    },
    validation: {
      type: Array,
      default: () => [],
      validator (value) {
        return !value.some(v => {
          const missingRule = !v.rule;
          if(missingRule) console.warn('[component][input-tags][validation]Property "rule" is missing:', v);

          const invalidRule = v.rule && typeof v.rule !== 'string';
          if(invalidRule) console.warn('[component][input-tags][validation]A rule must be type of string. Found:', v);

          const missingType = !v.type;
          if(missingType) console.warn('[component][input-tags][validation]Property "type" is missing:', v);

          const invalidType = v.type && typeof v.type !== 'string';
          if(invalidType) console.warn('[component][input-tags][validation]Property "type" must be type of sting. Found:', v);

          return invalidRule || missingRule || invalidType || missingType;
        });
      }
    },
    isTrim: {
      type: Boolean,
      default: true
    },
    avoidAddingDuplicates: {
      type: Boolean,
      default: true
    },
    allowEditTags: {
      type: Boolean,
      default: true
    },
    deleteOnBackslash: {
      type: Boolean,
      default: true
    },
    addOnBlur: {
      type: Boolean,
      default: true
    },
    maxTag: {
      type: Number,
    }
  },
  data () {
    return {
      newTag: '',
      tagsCopy: null,
      tagsEditStatus: null,
      focused: false,
      deletionMark: null,
      deletionMarkTime: null
    }
  },
  methods: {
    performAddTags (tag) {
      if(this.disabled) return;
      if(typeof tag === 'string' && tag.length === 0) return;
      let tags = [];
      if(typeof tag === 'object') tags = [tag];
      if(typeof tag === 'string') tags = this.createTagTexts(tag);
      tags.forEach(tag => {
        tag = createTag(tag, this.tags, this.validation, false);
        if(!this._events['before-adding-tag'])this.addTag(tag);
        this.$emit('before-adding-tag', {
          tag,
          addTag: (goOn) => this.addTag(tag, goOn),
        });
      });
    },
    addTag (tag, goOn) {
      if(goOn === false) return;
      const maxItemReached = this.maxTags && this.maxTags === this.tagsCopy.length;
      if(maxItemReached) return this.$emit('max-tags-reached');
      const dup = this.avoidAddingDuplicates && this.tagsCopy.map(t => t.text).includes(tag.text);
      if(dup) return this.$emit('adding-duplicate', tag);
      if(!tag.valid && this.hasForbiddingAddRule(tag.tiClasses)) return;
      this.newTag = '';
      this.tagsCopy.push(tag);
      this.$emit('tags-changed', this.tagsCopy);
    },
    createTagTexts (string) {
      const regex = new RegExp(this.separators.map(s => this.quote(s)).join('|'));
      return string.split(regex).map(text => {
        return { text };
      });
    },
    hasForbiddingAddRule (tiClasses) {
      return tiClasses.some(type => {
        const rule = this.validation.find(rule => type === rule.type);
        return rule ? rule.disableAdd : false;
      });
    },
    quote (regex) {
      return regex.replace(/([()[{*+.$^\\|?])/g, '\\$1');
    },
    performEditTag (index) {
      if(!this.allowEditTags) return;
      if(!this._events['before-editing-tag']) this.editTag(index);
      this.$emit('before-editing-tag', {
        index,
        tag: this.tagsCopy[index],
        editTag: (goOn) => this.editTag(index, goOn),
      });
      console.log('edit', index)
    },
    editTag(index,goOn) {
      if(!this.allowEditTags || goOn === false) return;
      this.toggleEdit(index);
      this.focus(index);
    },
    focus (index) {
      this.$nextTick(() => {
        const el = this.$refs.tag[index].querySelector('input.updateInput');
        if(el) el.focus();
      });
    },
    toggleEdit (index) {
      if(!this.allowEditTags || this.disabled) return;
      this.$set(this.tagsEditStatus, index, !this.tagsEditStatus[index]);
    },
    cancelEdit (index) {
      this.tagsCopy[index] = Object.assign({},
        createTag(this.tags[index], this.tags, this.validation)
      );
      this.$set(this.tagsEditStatus, index, false);
    },
    createChangedTag (index, tag) {
      /*使用setTimeout延时10毫秒防止inpput不能获取中文字符的问题*/
      setTimeout(() => {
        const tags = this.tagsCopy;
        this.$set(this.tagsCopy, index, createTag(tags[index], tags, this.validation));
      }, 10);
    },
    performSaveTag (index) {
      const tag = this.tagsCopy[index];
      if(this.disabled) return;
      if(tag.text.length === 0) return;
      if(!this._events['before-saving-tag']) this.saveTag(index, tag);
      this.$emit('before-saveing-tag', {
        index,
        tag,
        saveTag: (goOn) => this.saveTag(index, tag, goOn),
      });
    },
    saveTag (index, tag, goOn) {
      if(goOn === false) return;
      const dup = this.avoidAddingDuplicates && this.tags.map(t => t.text).includes(tag.text);
      if(dup) return this.$emit('saving-duplicate', tag);
      if(!tag.valid && this.hasForbiddingAddRule(tag.tiClasses)) return;
      this.$set(this.tagsCopy, index, tag);
      this.toggleEdit(index);
      this.$emit('tags-changed', this.tagsCopy);
    },
    invokeDelete () {
      if(!this.deleteOnBackslash || this.newTag.length > 0) return;
      const lastIndex = this.tagsCopy.length - 1;
      console.log(lastIndex)
      if(this.deletionMark === null) {
        this.deletionMarkTime = setTimeout(() => this.deletionMark = null, 1000);
        this.deletionMark = lastIndex;
      } else {
        this.performDeleteTag(lastIndex);
      }
    },
    performDeleteTag (index) {
      if(!this._events['before-deleting-tag']) this.deleteTag(index);
      this.$emit('before-deleting-tag', {
        index,
        tag: this.tagsCopy[index],
        deleteTag: (goOn) => this.deleteTag(index, goOn)
      });
      console.log('dele', index);
    },
    deleteTag (index, goOn) {
      if(goOn === false) return;
      if(this.disabled) return;
      this.deletionMark = null;
      clearTimeout(this.deletionMarkTime);
      this.tagsCopy.splice(index, 1);
      this.$emit('tags-changed', this.tagsCopy);
    },
    isMarked (index) {
      return this.deletionMark === index;
    },
    inputFocus () {
      if(this.disabled) {
        return;
      }
      this.$refs.newTagInput.focus();
      this.focused = true;
    },
    blurInput () {
      this.focused = false;
    },
    initTags () {
      this.tagsCopy = createTags(this.tags, this.validation);
      this.tagsEditStatus = this.clone(this.tags).map(() => false);
      console.log('tags', this.tags);
      console.log('tagsCopy', this.tagsCopy);
    },
    clone (items) {
      return JSON.parse(JSON.stringify(items));
    }
  },
  watch: {
    tags: {
      handler () {
        this.initTags();
      },
      deep: true
    }
  },
  created () {
    this.initTags();
  }
};
</script>
<style scoped lang="scss">
  @import './assets/styles/colors';

  @font-face {
    font-family: 'icomoon';
    src:  url('./assets/fonts/custom/icomoon.eot?7grlse');
    src:  url('./assets/fonts/custom/icomoon.eot?7grlse#iefix') format('embedded-opentype'),
      url('./assets/fonts/custom/icomoon.ttf?7grlse') format('truetype'),
      url('./assets/fonts/custom/icomoon.woff?7grlse') format('woff');
    font-weight: normal;
    font-style: normal;
  }

  [class^="icon-"],[class*="icon-"] {
    font-family: 'icomoon' !important;
    speak: none;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
  }

  .tagsContainer {
    min-height: 100px;
    height:auto;
  }

  .tagsContainer {
    .tagList, .tagInput {
      float: left;
    }

    .tag {
      float: left;
    }

    .deletion-mark {
      background-color: $error !important;
    }

    .tag {
      .tagItem {
        height:30px;
        line-height:30px;
        background-color: $itemColor;
        padding: 5px 10px;
        text-align: center;
        float: left;
        margin: 5px;
        border-radius: 10px;
        color: #ffffff;
        font-size: 1em;
        letter-spacing: 2px;
        cursor: pointer;

        .icon-delete {
          float: right;
          width: 15px;
          height: 15px;
          background-image: url('./assets/img/x.png');
          background-repeat: round;
          margin-top: -40px;
          margin-right: -10px;
          display: none;
        }
      }

        .tagItem:after,.tagItem:before {
            box-sizing: content-box !important;
        }

      .hidden {
        visibility: hidden;
        height: 0px;
        padding-left: 16px;
      }

      .tagItem:hover {
        background-color: $hoverColor;

        .icon-delete {
          display: block;
        }
      }

      .updateInput {
        outline: none;
        height: 40px;
        line-height: 40px;
        font-size: 18px;
        border: solid thin $updateColor;
        border-radius: 5px;
        margin: 5px;
        padding: 0px 5px;
        text-align: center;
        float: left;
      }
    }

    .new-tag-input {
      outline: none;
    	height: 40px;
    	line-height: 40px;
    	font-size: 18px;
    	border: none;
    	border-radius: 5px;
    	margin: 5px;
    	padding: 0px 5px;
    	text-align: left;
      width: auto;
    }

    .new-tag-input:focus {
      border: none;
    }

    .new-tag-input-wrapper {
      float: left;
    }
  }

  .form-control {
    background-color: #ffffff;
    background-image: none;
    border: 1px solid #e4eaec;
    border-radius: 3px;
    transition: box-shadow .25s linear,border .25s linear,color .25s linear,background-color .25s linear
  }

  .form-control-focus {
    border-color: #62a8ea;
    box-shadow: none
  }

  .clearfix::after {
    display: block;
    content: "";
    clear: both;
  }
</style>
