@extends('layouts.contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">

@endsection

@section('page-style')
    <link href="{{url('/css/jquery.loader.css')}}" rel="stylesheet"/>
    <link href="{{url('/css/dropify.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{url('/css/custom/fancybox.css')}}" rel="stylesheet" type="text/css"/>
    <style>
        .dropify-message p {
            line-height: 3.5rem !important;
            font-size: 22px !important;
        }
    </style>
@endsection

@section('form_input')

    <div class="mb-1">
        <label class="form-label" for="title_ar">{{trans('admin.name_ar')}}</label>
        <input type="text" name="title_ar"
               class="form-control dt-full-name"
               id="title_ar"
               placeholder="{{trans('admin.name_ar')}}"/>
    </div>


    <div class="mb-1">
        <label class="form-label" for="title_en">{{trans('admin.name_en')}}</label>
        <input type="text" name="title_en"
               class="form-control dt-full-name"
               id="title_en"
               placeholder="{{trans('admin.name_en')}}"/>
    </div>

    <div class="mb-1">
        <label class="form-label" for="description_ar">{{trans('admin.description_ar')}}</label>
        <textarea name="description_ar"
                  class="form-control dt-full-name" id="description_ar"
                  placeholder="{{trans('admin.description_ar')}}"></textarea>
    </div>

    <div class="mb-1">
        <label class="form-label" for="description_en">{{trans('admin.description_en')}}</label>
        <textarea name="description_en"
                  class="form-control dt-full-name" id="description_en"
                  placeholder="{{trans('admin.description_en')}}"></textarea>
    </div>

    <div class="mb-1">
        <label class="form-label" for="history_ar">{{trans('admin.history_ar')}}</label>
        <textarea name="history_ar"
                  class="form-control dt-full-name" id="history_ar"
                  placeholder="{{trans('admin.history_ar')}}"></textarea>
    </div>

    <div class="mb-1">
        <label class="form-label" for="history_en">{{trans('admin.history_en')}}</label>
        <textarea name="history_en"
                  class="form-control dt-full-name" id="history_en"
                  placeholder="{{trans('admin.history_en')}}"></textarea>
    </div>

    <div class="mb-2 p-2 border rounded bg-light">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label fw-bold mb-0 text-primary">
                <i data-feather="award"></i> {{trans('admin.championships_ar')}} / {{trans('admin.championships_en')}}
            </label>
            <button type="button" class="btn btn-sm btn-primary" id="add_championship_btn">
                <i data-feather="plus"></i> {{trans('admin.add')}}
            </button>
        </div>
        <div class="row text-muted small fw-bold mb-1">
            <div class="col-md-5">🇦🇪 البطولة (بالعربية)</div>
            <div class="col-md-5">🇬🇧 البطولة (بالإنجليزية)</div>
            <div class="col-md-2 text-center">إجراء</div>
        </div>
        <div id="championships_repeater_container">
            <!-- Dynamic championship rows will be added here -->
        </div>
    </div>

    <div class="mb-1">
        <label class="form-label" for="order">{{trans('admin.order')}}</label>
        <input type="number" name="order" min="1"
               class="form-control dt-full-name"
               id="order" placeholder="{{trans('admin.order')}}"/>
    </div>

    <div class="mb-1" id="dropify_image">
    </div>

@stop

@section('content')
    <!-- Basic table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">
                            <button type="button" class="btn btn-primary" id="add_btn"
                                    data-bs-toggle="modal" data-bs-target=".general_modal">
                                {{trans('admin.add_sportGame')}}
                            </button>
                        </h4>
                    </div>
                    <div class="card-datatable">
                        @include('panels.table')
                    </div>
                    <!-- Modal to add new record -->
                    @include('panels.modal')
                </div>
            </div>
        </div>
    </section>
    <!--/ Basic table -->
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>

@endsection
@section('page-script')
    <script src="{{url('/js/scripts/custom/jquery.loader.js')}}"></script>
    <script src="{{url('/js/scripts/custom/dropify.min.js')}}"></script>
    <script src="{{url('/js/scripts/custom/fancybox.min.js')}}"></script>
    <script>
        let add = false;
        let edit = false;
        let pub_id;
        let csrf_token = '{{csrf_token()}}';
    </script>
    <script src="{{url('/js/scripts/custom/utils.js')}}"></script>
    <script>
        $(function () {

            addModal({
                title: '{{trans('admin.add_sportGame')}}',
                dropify: true
            });

            onClose();

            loadDataTables('{{ url("/admin/sportGames/data") }}',
                ['title', 'description', 'actions'], '',
                {
                    'show': '{{trans('admin.show')}}',
                    'first': '{{trans('admin.first')}}',
                    'last': '{{trans('admin.last')}}',
                    'filter': '{{trans('admin.filter')}}',
                    'filter_type': '{{trans('admin.type_filter')}}',
                    fancy: true
                });

            $('#general-form').submit(function (e) {
                e.preventDefault();
                sendModalAjaxRequest(this, '{{url('/admin/sportGame/add')}}',
                    '{{url('/admin/sportGame/edit')}}', {
                        error_message: '{{trans('admin.general_error_message')}}',
                        error_title: '',
                        loader: true,
                    });
            });

        });


        function initDropify(image = null) {
            let html = '<label class="control-label" for="image">' +
                '{{trans('admin.image')}}</label>' +
                '<input name="image" type="file" class="dropify" data-default-file="' + (image ? image : '') + '" ' +
                'data-max-file-size="3M" data-allowed-file-extensions="png jpg jpeg"/>';
            $('#dropify_image').html(html);
            $('.dropify').dropify({
                messages: {
                    'default': '{{trans('admin.dropify_default')}}',
                    'replace': '{{trans('admin.dropify_replace')}}',
                    'remove': '{{trans('admin.dropify_remove')}}',
                    'error': '{{trans('admin.dropify_error')}}'
                },
                error: {
                    'fileSize': '{{trans('admin.dropify_error')}}',
                }
            });
        }

        function editSportGame(item) {
            var id = $(item).attr('id');
            var form = new FormData();
            form.append('id', id);
            $('.modal-title').text('{{trans('admin.edit_sportGame')}}');
            pub_id = id;
            $.ajax({
                url: '{{url('/admin/sportGame/data')}}',
                method: 'POST',
                data: form,
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': csrf_token},
                success: function (response) {
                    $('#general-form input[name=title_ar]').val(response.data.title_ar);
                    $('#general-form input[name=title_en]').val(response.data.title_en);
                    $('#general-form input[name=order]').val(response.data.order);
                    $('#general-form textarea[name=description_ar]').val(response.data.description_ar);
                    $('#general-form textarea[name=description_en]').val(response.data.description_en);
                    $('#general-form textarea[name=history_ar]').val(response.data.history_ar);
                    $('#general-form textarea[name=history_en]').val(response.data.history_en);
                    
                    $('#championships_repeater_container').empty();
                    let arList = [];
                    let enList = [];
                    if (response.data.championships_ar) {
                        if (typeof response.data.championships_ar === 'string') {
                            arList = response.data.championships_ar.split('\n').map(s => s.trim()).filter(Boolean);
                        } else if (Array.isArray(response.data.championships_ar)) {
                            arList = response.data.championships_ar;
                        }
                    }
                    if (response.data.championships_en) {
                        if (typeof response.data.championships_en === 'string') {
                            enList = response.data.championships_en.split('\n').map(s => s.trim()).filter(Boolean);
                        } else if (Array.isArray(response.data.championships_en)) {
                            enList = response.data.championships_en;
                        }
                    }
                    let maxLen = Math.max(arList.length, enList.length);
                    if (maxLen > 0) {
                        for (let i = 0; i < maxLen; i++) {
                            addChampionshipRow(arList[i] || '', enList[i] || '');
                        }
                    } else {
                        addChampionshipRow();
                    }

                    initDropify(response.data.image ? response.data.image : null);
                    $('.general_modal').modal('toggle');
                    edit = true;
                    add = false;

                },
                error: function () {
                    toastr['error']('{{trans('admin.general_error_message')}}', '{{trans('admin.error_title')}}');
                }
            });

        }

        function deleteSportGame(item) {
            ban(item, '{{url('/admin/sportGame/delete')}}', {
                error_message: '{{trans('admin.general_error_message')}}',
                error_title: '{{trans('admin.error_title')}}',
                ban_title: "{{trans('admin.delete_action')}}",
                ban_message: "{{trans('admin.delete_message')}}",
                inactivate: "{{trans('admin.delete_action')}}",
                cancel: "{{trans('admin.cancel')}}"
            });
        }

        function restoreSportGame(item) {
            ban(item, '{{url('/admin/sportGame/restore')}}', {
                error_message: '{{trans('admin.general_error_message')}}',
                error_title: '{{trans('admin.error_title')}}',
                ban_title: "{{trans('admin.restore_action')}}",
                ban_message: "{{trans('admin.restore_message')}}",
                inactivate: "{{trans('admin.restore_action')}}",
                cancel: "{{trans('admin.cancel')}}"
            });
        }

        function addChampionshipRow(arVal = '', enVal = '') {
            let rowHtml = `
                <div class="row align-items-center mb-1 championship-item-row">
                    <div class="col-md-5">
                        <input type="text" name="championships_ar[]" class="form-control" placeholder="مثال: الدوري الإماراتي" value="${arVal}">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="championships_en[]" class="form-control" placeholder="Ex: UAE League" value="${enVal}">
                    </div>
                    <div class="col-md-2 text-center">
                        <button type="button" class="btn btn-icon btn-outline-danger btn-sm remove-championship-btn" title="حذف">
                            <i data-feather="trash-2"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#championships_repeater_container').append(rowHtml);
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }

        $(document).on('click', '#add_championship_btn', function () {
            addChampionshipRow();
        });

        $(document).on('click', '.remove-championship-btn', function () {
            $(this).closest('.championship-item-row').remove();
        });

        $(document).on('click', '#add_btn', function () {
            $('#championships_repeater_container').empty();
            addChampionshipRow();
        });
    </script>
@endsection
