@extends('voyager::master')

@section('page_title', __('voyager.generic.view').' '.$dataType->display_name_singular)

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ __('voyager.generic.viewing') }} {{ ucfirst($dataType->display_name_singular) }} &nbsp;

        {{--@can('edit', $dataTypeContent)--}}
        {{--<a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">--}}
            {{--<span class="glyphicon glyphicon-pencil"></span>&nbsp;--}}
            {{--{{ __('voyager.generic.edit') }}--}}
        {{--</a>--}}
        {{--@endcan--}}
        <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            {{ __('voyager.generic.return_to_list') }}
        </a>
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div id="content" class="panel panel-bordered" style="padding-bottom:5px;">
                    @include('voyager::shared._errors')
                    @include('voyager::shared._messages')
                    <form
                            ref="form"
                            action="{{ route('voyager.question.multi.update') }}"
                            @submit.prevent="stringifyTable"
                            @keydown.enter.prevent
                            method="POST"
                            enctype="multipart/form-data"
                    >
                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}
                        <!-- form start -->
                        <div class="panel-heading" style="border-bottom:0;">
                            <h3 class="panel-title">问题:</h3>
                        </div>
                        <div class="panel-body" style="padding-top:0;">
                            @foreach($dataTypeContent->entries as $entry)
                                @if($entry->lang == 'zh_CN')
                                    {{ $entry->value }}
                                @endif
                            @endforeach
                        </div>

                        <div class="panel-heading" style="border-bottom:0;">
                            <h3 class="panel-title">答案:
                                <a
                                    @click.stop="tiggleStatus"
                                    v-text="editStatus ? cancelText : editText "
                                    style="float: right;"
                                    class="btn"
                                    :class="[{'btn-danger': editStatus, 'delete': editStatus},{'btn-primary': !editStatus, edit: !editStatus}]"></a>
                            </h3>

                        </div>
                        <div class="panel-body" style="padding-top:0;">
                            <show-answers-editor :table="table" :question-id="questionId" :edit="editStatus"></show-answers-editor>
                        </div>

                        <input type="hidden" name="contents" v-model="tableJson"/>
                        <input type="hidden" name="question_id" value="{{ $dataTypeContent->id }}"/>

                        <div class="panel-footer">
                            <button type="submit" v-if="editStatus" class="btn btn-primary save">{{ __('voyager.generic.update') }}</button>
                            <a
                                @click.stop="tiggleStatus"
                                v-text="editStatus ? cancelText : editText "
                                class="btn"
                                :class="[{'btn-danger': editStatus, 'delete': editStatus},{'btn-primary': !editStatus, edit: !editStatus}]"></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager.generic.close') }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager.generic.delete_question') }} answer?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.questions.answer.index') }}" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="hidden" name="question_id" value="{{ $dataTypeContent->id }}"/>
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                               value="{{ __('voyager.generic.delete_confirm') }} answer">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager.generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('javascript')
    @include('voyager::questions.vue-components.show-answers-editor')
    <script>
        new Vue({
            el: '#content',
            data () {
                return {
                    table: {},
                    originalTable: {!! $table !!},
                    tableJson: null,
                    questionId: {!! $dataTypeContent->id !!},
                    editStatus: false,
                    editText: '编辑全部',
                    cancelText: '取消编辑'
                }
            },
            created() {
                if (!!this.originalTable) {
                    this.table = JSON.parse(JSON.stringify(this.originalTable))
                }

                console.log('questionId', this.questionId, typeof this.questionId)
            },
            methods: {
                stringifyTable() {
                    let content = this.table.body.map(item => item.entries);
                    this.tableJson = JSON.stringify(content);
                    this.$nextTick(() => this.$refs.form.submit());
                },
                tiggleStatus () {
                    this.editStatus = !this.editStatus;
                    console.log(this.editStatus)
                }
            },
            watch: {
                editStatus: {
                    handle () {
                        console.log(this.editStatus)
                    }
                }
            }
        });
    </script>

    @if ($isModelTranslatable)
    <script>
        $(document).ready(function () {
            $('.side-body').multilingual();
        });
    </script>
    <script src="{{ voyager_asset('js/multilingual.js') }}"></script>
    @endif

    <script>
        var deleteFormAction;
        $('td').on('click', '.delete', function (e) {
            var form = $('#delete_form')[0];

            if (!deleteFormAction) { // Save form action initial value
                deleteFormAction = form.action;
            }

            form.action = deleteFormAction.match(/\/[0-9]+$/)
                ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
                : deleteFormAction + '/' + $(this).data('id');
            console.log(form.action);

            $('#delete_modal').modal('show');
        });
    </script>
@stop
