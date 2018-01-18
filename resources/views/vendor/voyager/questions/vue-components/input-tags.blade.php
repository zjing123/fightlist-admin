@section('input-tags-template')
    <div @click="focusInput" class="form-control vue-input-tag-wrapper">
        <span v-for="(tag, index) in tags" :key="index" class="input-tag">
            <span v-text="tag"></span>
            <a v-if="!readOnly" @click.prevent.stop="remove(index)" class="remove"></a>
        </span>
        <input class="new-tag"
               v-if="!readOnly"
               :placeholder="placeholder"
               type="text"
               v-model="newTag"
               @:keydown.delete.stop="removeLastTag()"
               @:keydown.enter.prevent.stop="addNewTag(newTag)"
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
               newTag: ''
           }
        },
        methods: {
            focusInput () {
                if(this.readOnly) {
                    return;
                }
                this.$el.querySelector('.new-tag').focus();
            },
            addNewTag (tag) {
                if(tag && this.tags.indexof(tag) === -1) {
                    if(isTrim) {
                        tag = tag.trim();
                    }
                    this.tags.push(tag);
                    this.tagChange();
                }
                this.newTag = '';
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

@section('css')
    aaassssssssssss
@endsection