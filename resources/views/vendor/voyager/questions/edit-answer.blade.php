@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

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
                          action="{{ route('voyager.questions.answer.update', [$question->id, $answer->id]) }}"
                          method="POST"
                          enctype="multipart/form-data">

                    <!-- CSRF TOKEN -->
                        {{ method_field('PUT') }}
                        {{ csrf_field() }}

                        <div class="panel-body">

                            <!-- errors and messages -->
                            @include('voyager::shared._errors')
                            @include('voyager::shared._messages')

                            <!-- content -->
                            <div class="form-group" style="margin-top:20px;">
                                <h3>Question:</h3>
                                <p>
                                    @foreach($question->entries as $entry)
                                        @if($entry->lang == 'zh_CN')
                                            {{ $entry->value }}
                                        @endif
                                    @endforeach
                                </p>
                            </div>
                            <div class="form-group" style="margin-top:20px;">
                                <h3 v-text="column.title">answer:</h3>
                                @foreach($languages as $language)
                                    <label for="name">{{ $language->title }}</label>
                                    @foreach($answer->entries as $entry)
                                        @if($entry->lang == $language->locale)
                                        <input
                                                {{--required--}}
                                                type="text"
                                                class="form-control"
                                                name="answer[{{ $language->locale }}]"
                                                placeholder=""
                                                value="{{ $entry->value }}"
                                        >
                                        @endif
                                    @endforeach
                                @endforeach
                                <input type="hidden" value="{{ $answer->title }}" name="translation_id">
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
