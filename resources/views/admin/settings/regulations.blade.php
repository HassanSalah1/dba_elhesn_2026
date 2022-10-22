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
        <label class="form-label" for="name_ar">{{trans('admin.name_ar')}}</label>
        <input type="text" name="name_ar"
               class="form-control dt-full-name"
               id="name_ar"
               placeholder="{{trans('admin.name_ar')}}"/>
    </div>


    <div class="mb-1">
        <label class="form-label" for="name_en">{{trans('admin.name_en')}}</label>
        <input type="text" name="name_en"
               class="form-control dt-full-name"
               id="name_en"
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
        <label class="form-label" for="order">{{trans('admin.order')}}</label>
        <input type="number" name="order" min="1"
               class="form-control dt-full-name"
               id="order" placeholder="{{trans('admin.order')}}"/>
    </div>

    <div class="mb-1">
        <label class="form-label" for="file">PDF</label>
        <input type="file" name="file"
               class="form-control dt-full-name"
               id="file" accept="application/pdf" />
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
                                {{trans('admin.add_team')}}
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
                title: '{{trans('admin.add')}}',
            });

            onClose();

            loadDataTables('{{ url("/admin/regulations/data") }}',
                ['name', 'description', 'actions'], '',
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
                sendModalAjaxRequest(this, '{{url('/admin/regulation/add')}}',
                    '{{url('/admin/regulation/edit')}}', {
                        error_message: '{{trans('admin.general_error_message')}}',
                        error_title: '',
                        loader: true,
                    });
            });

        });

        function editRegulation(item) {
            var id = $(item).attr('id');
            var form = new FormData();
            form.append('id', id);
            $('.modal-title').text('{{trans('admin.edit')}}');
            pub_id = id;
            $.ajax({
                url: '{{url('/admin/regulation/data')}}',
                method: 'POST',
                data: form,
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': csrf_token},
                success: function (response) {
                    $('#general-form input[name=name_ar]').val(response.data.name_ar);
                    $('#general-form input[name=name_en]').val(response.data.name_en);
                    $('#general-form input[name=order]').val(response.data.order);
                    $('#general-form textarea[name=description_ar]').val(response.data.description_ar);
                    $('#general-form textarea[name=description_en]').val(response.data.description_en);
                    $('.general_modal').modal('toggle');
                    edit = true;
                    add = false;

                },
                error: function () {
                    toastr['error']('{{trans('admin.general_error_message')}}', '{{trans('admin.error_title')}}');
                }
            });

        }

        function deleteRegulation(item) {
            ban(item, '{{url('/admin/regulation/delete')}}', {
                error_message: '{{trans('admin.general_error_message')}}',
                error_title: '{{trans('admin.error_title')}}',
                ban_title: "{{trans('admin.delete_action')}}",
                ban_message: "{{trans('admin.delete_message')}}",
                inactivate: "{{trans('admin.delete_action')}}",
                cancel: "{{trans('admin.cancel')}}"
            });
        }

        function restoreRegulation(item) {
            ban(item, '{{url('/admin/regulation/restore')}}', {
                error_message: '{{trans('admin.general_error_message')}}',
                error_title: '{{trans('admin.error_title')}}',
                ban_title: "{{trans('admin.restore_action')}}",
                ban_message: "{{trans('admin.restore_message')}}",
                inactivate: "{{trans('admin.restore_action')}}",
                cancel: "{{trans('admin.cancel')}}"
            });
        }
    </script>
@endsection
