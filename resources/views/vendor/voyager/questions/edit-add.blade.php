@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('page_title', __('voyager.generic.add') . ' Question')

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager.generic.add').' Question' }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div id="questionManger" class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                          class="form-edit-add"
                          action="{{ route('voyager.'.$dataType->slug.'.store') }}"
                          method="POST" enctype="multipart/form-data"
                          ref="form"
                          @keydown.enter.prevent
                          @submit.prevent="stringifyColumns">

                    <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            <!-- errors and messages -->
                            @include('voyager::shared._errors')
                            @include('voyager::shared._messages')

                            <!-- content -->
                            <div>
                                <add-question-editor
                                        v-for="column in columns"
                                        :column="column"
                                        @on-add-answer="addAnswer"
                                        @on-remove-answer="removeAnswer"
                                        @on-remove-last-answer="removeLastAnswer"
                                >
                                </add-question-editor>
                                <input type="hidden" :value="columnsJson" name="columns">
                            </div>

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary save">{{ __('voyager.generic.save') }}</button>
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                          enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                               onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager.generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager.generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager.generic.delete') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager.generic.delete_confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script src="https://cdn.bootcss.com/blueimp-md5/2.10.0/js/md5.js"></script>
    @include('voyager::questions.vue-components.add-question-editor')
    <script>
        new Vue({
            el: '#questionManger',
            data () {
                return {
                    columns: {},
                    originalColumns: {!! $columns !!},
                    columnsJson: {!! old('columns') ? old('columns') : "''" !!}
                }
            },
            created() {
                if (!!this.columnsJson) {
                    this.columns = JSON.parse(JSON.stringify(this.columnsJson))
                } else {
                    this.columns = JSON.parse(JSON.stringify(this.originalColumns))
                }
                console.log(this.columnsJson)
            },
            methods: {
                stringifyColumns($event) {
                    this.columnsJson = JSON.stringify(this.columns);
                    //this.$nextTick(() => this.$refs.form.submit());
                },
                addAnswer (answer) {
                    //console.log('addAnswer', answer)
                    console.log(this.columns)
                    var _self = this;
                    this.getEnglish(answer, function(translate) {
                        _self.columns.en.answers.push(translate.trim())
                        console.log('call', translate)
                    })
                },
                removeAnswer (index) {
                    this.columns.en.answers.splice(index, 1);
                },
                removeLastAnswer () {
                    this.columns.en.answers.pop();
                },
                getEnglish (query, callback) {
                    var appKey = '3f4c3da5e9c22294';
                    var appSecret = 'nvkki4jLqfMW6P2KiukabqLB3mWZQRBT';
                    var salt = (new Date).getTime();
                    var from = 'zh-CHS';
                    var to = 'en';
                    var str1 = appKey + query + salt +appSecret;
                    var sign = md5(str1);
                    $.ajax({
                        url: 'http://openapi.youdao.com/api',
                        type: 'post',
                        dataType: 'jsonp',
                        data: {
                            q: query,
                            appKey: appKey,
                            salt: salt,
                            from: from,
                            to: to,
                            sign: sign
                        },
                        success: function (data) {
                            callback(data.translation[0]);
                        }
                    });
                }
            }
        });
    </script>

    <script>
        var params = {}
        var $image

        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.type != 'date' || elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
            $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', function (e) {
                $image = $(this).siblings('img');

                params = {
                    slug:   '{{ $dataType->slug }}',
                    image:  $image.data('image'),
                    id:     $image.data('id'),
                    field:  $image.parent().data('field-name'),
                    _token: '{{ csrf_token() }}'
                }

                $('.confirm_delete_name').text($image.data('image'));
                $('#confirm_delete_modal').modal('show');
            });

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $image.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing image.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
