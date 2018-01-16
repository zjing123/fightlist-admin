@extends('voyager::master')

@section('page_title', __('voyager.generic.viewing').' '.$dataType->display_name_plural)

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->display_name_plural }}
        </h1>
        @can('add',app($dataType->model_name))
            <a href="{{ route('voyager.'.$dataType->slug.'.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager.generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete',app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            <form method="get">
                                <div id="search-input">
                                    <select id="search_key" name="key">
                                        @foreach($searchable as $key)
                                                <option value="{{ $key }}" @if($search->key == $key){{ 'selected' }}@endif>{{ ucwords(str_replace('_', ' ', $key)) }}</option>
                                        @endforeach
                                    </select>
                                    <select id="filter" name="filter">
                                        <option value="contains" @if($search->filter == "contains"){{ 'selected' }}@endif>contains</option>
                                        <option value="equals" @if($search->filter == "equals"){{ 'selected' }}@endif>=</option>
                                    </select>
                                    <div class="input-group col-md-12">
                                        <input type="text" class="form-control" placeholder="Search" name="s" value="{{ $search->value }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-lg" type="submit">
                                                <i class="voyager-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        @endif
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Title</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th class="actions">{{ __('voyager.generic.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTypeContent as $data)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="row_id" id="checkbox_{{ $data->id }}" value="{{ $data->id }}">
                                            </td>
                                            <td>
                                                @foreach($data->entries as $entry)
                                                    @if($entry->lang == 'zh_CN')
                                                        {{ $entry->value }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                {{ $data->created_at }}
                                            </td>
                                            <td>
                                                {{ $data->updated_at }}
                                            </td>
                                            <td class="no-sort no-click" id="bread-actions">
                                                @php

                                                @endphp
                                                @can('delete', $data)
                                                    <a href="javascript:;" title="{{ __('voyager.generic.delete') }}" class="btn btn-sm btn-danger pull-right delete" data-id="{{ $data->{$data->getKeyName()} }}" id="delete-{{ $data->{$data->getKeyName()} }}">
                                                        <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager.generic.delete') }}</span>
                                                    </a>
                                                @endcan
                                                @can('edit', $data)
                                                    <a href="{{ route('voyager.'.$dataType->slug.'.edit', $data->{$data->getKeyName()}) }}" title="{{ __('voyager.generic.edit') }}" class="btn btn-sm btn-primary pull-right edit">
                                                        <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">{{ __('voyager.generic.edit') }}</span>
                                                    </a>
                                                @endcan
                                                @can('read', $data)
                                                    <a href="{{ route('voyager.'.$dataType->slug.'.show', $data->{$data->getKeyName()}) }}" title="{{ __('voyager.generic.view') }}" class="btn btn-sm btn-warning pull-right">
                                                        <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">{{ __('voyager.generic.view') }}</span>
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($isServerSide)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">{{ trans_choice(
                                    'voyager.generic.showing_entries', $dataTypeContent->total(), [
                                        'from' => $dataTypeContent->firstItem(),
                                        'to' => $dataTypeContent->lastItem(),
                                        'all' => $dataTypeContent->total()
                                    ]) }}</div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->appends([
                                    's' => $search,
                                    'order_by' => $orderBy,
                                    'sort_order' => $sortOrder
                                ])->links() }}
                            </div>
                        @endif
                    </div>
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
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager.generic.delete_question') }} {{ strtolower($dataType->display_name_singular) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                        {{ method_field("DELETE") }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                                 value="{{ __('voyager.generic.delete_confirm') }} {{ strtolower($dataType->display_name_singular) }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager.generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('css')
@if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
<link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
@endif
@stop

@section('javascript')
    <!-- DataTables -->
    @if(!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    <script>
        $(document).ready(function () {
            @if (!$dataType->server_side)
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge([
                        "order" => [],
                        "language" => __('voyager.datatable'),
                    ],
                    config('voyager.dashboard.data_tables', []))
                , true) !!});
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
            @endif
        });


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
