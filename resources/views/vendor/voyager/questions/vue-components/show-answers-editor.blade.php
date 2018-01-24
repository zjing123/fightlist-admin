@section('show-answers-template')
    <table class="table table-condensed table-hover">
        <thead>
            <th class="th" v-for="(language, index) in table.headers" :key="index" v-text="language.title"></th>
            <th class="th">Action</th>
        </thead>
        <tbody>
        <tr v-for="(answer, index) in contents" :key="index">
            <td v-for="(entry, index) in answer.entries">
                <span  v-text="entry.value" v-if="!edit"></span>
                <input type="text" v-if="edit" v-model="entry.value">
            </td>

            <td class="no-sort no-click" id="bread-actions">
                <a href="javascript:;" title="{{ __('voyager.generic.delete') }}" class="btn btn-sm btn-danger pull-right delete" :data-id="answer.id" :id="'delete-' + answer.id">
                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager.generic.delete') }}</span>
                </a>
                <a :href="getEditLink(answer.id)" title="{{ __('voyager.generic.edit') }}" class="btn btn-sm btn-primary pull-right edit">
                    <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">{{ __('voyager.generic.edit') }}</span>
                </a>
            </td>
        </tr>
        </tbody>
    </table>
@endsection

<script>
    Vue.component('show-answers-editor', {
        props: {
            table: {
              type: Array,
              default: []
            },
            questionId: {
                type: Number,
                required: true
            },
            edit: {
                type: Boolean,
                default: false
            }
        },
        template: `@yield('show-answers-template')`,
        data () {
            return {
                contentsCopy: null
            }
        },
        computed: {
            contents () {
                let langs = this.table.headers.map(function (lang) {
                    return lang.locale;
                });

                let contents =  this.table.body.map(function (item) {
                    item.entries.sort(function (a, b) {
                        return langs.indexOf(a.lang) - langs.indexOf(b.lang);
                    });

                    return item;
                });

                return contents;
            }
        },
        methods: {
            initContent () {
                console.log('init content');
                let langs = this.table.headers.map(function (lang) {
                    return lang.locale;
                });

                let entries = this.table.body.map(function (item) {
                    return item.entries;
                });

                this.contentsCopy = entries.map(function (item) {
                    return item.sort(function (a, b) {
                        return langs.indexOf(a.lang) - langs.indexOf(b.lang);
                    });
                });
            },
            getEditLink(answerId) {
                let questionId = {!! $dataTypeContent->id !!}
                return '/admin/questions/'+ this.questionId +'/answer/' + answerId + '/edit';
            }
        },
        created () {
            this.initContent();
            console.log('question_id', this.questionId)
        }
    });
</script>

<style type="text/css">
    .th {
        border-color: #EAEAEA;
        background: #F8FAFC;
    }
</style>