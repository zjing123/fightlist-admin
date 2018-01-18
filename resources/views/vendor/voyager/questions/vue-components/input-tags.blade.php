@section('input-tags-template')
    <div @click="focusInput" v-bind:class="{'form-control-focus': isFocus}" class="form-control vue-input-tag-wrapper" >
        <span v-for="(tag, index) in tags" :key="index" class="input-tag">
            <span contenteditable="isEdit" v-text="tag" ></span>
            <a v-if="!readOnly" @click.prevent.stop="remove(index)" class="remove"></a>
            <input @click="editTag($event, index)"/>
        </span>
        <input class="new-tag"
               @blur="blurInput"
               v-if="!readOnly"
               :placeholder="placeholder"
               type="text"
               v-model="newTag"
               @keydown.delete.stop="removeLastTag()"
               @keydown.enter="addNewTag(newTag)"
        >
    </div>
@endsection

<script>
    Vue.component('input-tag', {
        props: {
            tags: {
                type: Array,
                default: []
            },
            placeholder: {
                type: String,
                default: ''
            },
            onChange: {
                type: Function
            },
            readOnly: {
                type: Boolean,
                default: false
            },
            isTrim: {
                type: Boolean,
                default: true
            }
        },
        template: `@yield('input-tags-template')`,
        data () {
           return {
               newTag: '',
               isFocus: false,
               isEdit: false
           }
        },
        methods: {
            focusInput () {
                if(this.readOnly) {
                    return;
                }
                this.isFocus = true;
                this.$el.querySelector('.new-tag').focus();
            },
            blurInput () {
                if (this.readOnly) {
                    return;
                }
                this.isFocus = false;
            },
            addNewTag (tag) {
                if(tag && this.tags.indexOf(tag) === -1) {
                    if(this.isTrim) {
                        tag = tag.trim();
                    }
                    this.tags.push(tag);
                    this.tagChange();
                    this.$emit('on-add-tag', tag)
                }
                this.newTag = '';
            },
            editTag ($event, index) {
                $event.target.focus();
                console.log($event)
                this.isEdit = true;
            },
            remove (index) {
                this.tags.splice(index, 1);
                this.tagChange();
            },
            removeLastTag () {
                if (this.newTag) {
                    return;
                }
                this.tags.pop();
                this.tagChange();
            },
            tagChange () {
                if(this.onChange) {
                    this.onChange(JSON.parse(JSON.stringify(this.tags)));
                }
            }
        }
    });
</script>

<style type="text/css">
    .vue-input-tag-wrapper input.new-tag {
        border:none !important;
    }

    .vue-input-tag-wrapper {
        height: 100px;
        overflow: hidden;
        padding-top: 4px;
        padding-bottom: 4px;
        cursor: text;
        text-align: left;
        -webkit-appearance: textfield;
    }

    .form-control-focus {
        border-color: #62a8ea;
        box-shadow: none
    }

    .vue-input-tag-wrapper .input-tag {
        background-color: #cde69c;
        border-radius: 2px;
        border: 1px solid #a5d24a;
        color: #638421;
        display: inline-block;
        font-size: 13px;
        font-weight: 400;
        margin-bottom: 4px;
        margin-right: 4px;
        padding: 3px;

        /*background: #2ecc71;*/
        /*color: #fff;*/
        /*border: 0;*/
        /*border-radius: 3px;*/
        opacity: .9
    }

    .vue-input-tag-wrapper .input-tag .remove {
        cursor: pointer;
        font-weight: bold;
        color: #638421;
    }

    .vue-input-tag-wrapper .input-tag .remove:hover {
        text-decoration: none;
    }
    .vue-input-tag-wrapper .input-tag .remove::before {
        content: " x";
    }
</style>
