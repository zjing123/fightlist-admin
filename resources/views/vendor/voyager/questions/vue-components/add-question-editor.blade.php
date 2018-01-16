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
    <textarea
            {{--required--}}
            class="form-control"
            :data-name="column.name.answer"
            :name="column.name.answer"
            v-model="column.answers"
            v-show="column.showAnswer"
            @keyup.space="addDelimiter"
    ></textarea>
</div>

@endsection

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
            }
        }
    });
</script>