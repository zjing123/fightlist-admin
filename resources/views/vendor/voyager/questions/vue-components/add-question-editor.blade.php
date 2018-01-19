@section('add-question-editor-template')

<div class="form-group" style="margin-top:20px;">
    <h3 v-text="column.title"></h3>
    <label :for="column.name.question">Question</label>
    <input
            {{--required--}}
           type="text"
           :data-name="column.name.question"
           class="form-control"
           :name="column.name.question"
           placeholder=""
           v-model="column.question"
           >

    <label :for="column.name.answer" v-show="column.showAnswer">Answer</label>
    <input-tag
            :tags="column.answers"
            :readOnly="column.readOnly"
            @on-add-tag="onAdd"
            @remove-tag="onRemove"
            @remove-last-tag="onRemoveLast">
    </input-tag>
</div>

@endsection

@include('voyager::questions.vue-components.input-tags')

<script>
    Vue.component('add-question-editor', {
        props: {
            column: {
                type: Object,
                required: true
            }
        },
        template: `@yield('add-question-editor-template')`,
        methods: {
            addDelimiter () {
                this.column.answers = this.column.answers + '|';
            },
            onAdd(tag) {
                console.log('prev',tag)
                this.$emit('on-add-answer', tag)
            },
            onRemove (index) {
                this.$emit('on-remove-answer', index);
            },
            onRemoveLast () {
                this.$emit('on-remove-last-answer');
            }
        }
    });
</script>

@section('css')

@endsection