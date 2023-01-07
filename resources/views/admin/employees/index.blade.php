@extends('layouts/contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('vendors/css/forms/select/select2.min.css'))}}">

@endsection

@section('page-style')
    <link href="{{url('/css/jquery.loader.css')}}" rel="stylesheet"/>
@endsection

@section('form_input')
    <div class="mb-1">
        <label class="form-label" for="name">{{trans('admin.employee_name')}}</label>
        <input type="text" name="name"
               class="form-control dt-full-name" id="name"
               placeholder="{{trans('admin.employee_name')}}"/>
    </div>

    <div class="mb-1">
        <label class="form-label" for="phone">{{trans('admin.phone')}}</label>
        <input type="tel" name="phone"
               class="form-control dt-full-name" id="phone"
               placeholder="{{trans('admin.phone')}}"/>
    </div>

    <div class="mb-1">
        <label class="form-label" for="email">{{trans('admin.email')}}</label>
        <input type="email" name="email"
               class="form-control dt-full-name"
               id="email"
               placeholder="{{trans('admin.email')}}"/>
    </div>

    <div class="mb-1">
        <label class="form-label" for="password">{{trans('admin.password')}}</label>
        <input type="password" name="password"
               class="form-control dt-full-name" id="password"
               placeholder="{{trans('admin.password')}}"/>
    </div>

    <div class="mb-1">
        <label class="form-label" for="group_id">{{trans('admin.permissions')}}</label>
        <select class="form-control" name="group_id" id="group_id">
            @foreach($permissionGroups as $permissionGroup)
                <option value="{{$permissionGroup->id}}">{{$permissionGroup->group_name}}</option>
            @endforeach
        </select>
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
                                {{trans('admin.add_employee')}}
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
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js'))}}"></script>

@endsection
@section('page-script')
    <script src="{{url('/js/scripts/custom/jquery.loader.js')}}"></script>
    <script>
        let add = false;
        let edit = false;
        let pub_id;
        let csrf_token = '{{csrf_token()}}';
    </script>
    <script src="{{url('/js/scripts/custom/utils.js')}}"></script>
    <script>
        $(function () {

            $('#group_id').select2();
            addModal({
                title: '{{trans('admin.add_employee')}}',
                select_selector: ['group_id'],
            });

            onClose();

            loadDataTables('{{ url("/admin/employees/data", [] , env('APP_ENV') === 'local' ?  false : true)}}',
                ['name', 'phone', 'email', 'group_name', 'actions'], '',
                {
                    'show': '{{trans('admin.show')}}',
                    'first': '{{trans('admin.first')}}',
                    'last': '{{trans('admin.last')}}',
                    'filter': '{{trans('admin.filter')}}',
                    'filter_type': '{{trans('admin.type_filter')}}',
                });

            $('#general-form').submit(function (e) {
                e.preventDefault();
                sendModalAjaxRequest(this, '{{url('/admin/employee/add', [] , env('APP_ENV') === 'local' ?  false : true)}}',
                    '{{url('/admin/employee/edit', [] , env('APP_ENV') === 'local' ?  false : true)}}', {
                        error_message: '{{trans('admin.general_error_message')}}',
                        error_title: '',
                        loader: true,
                    });
            });

        });


        function editEmployee(item) {
            var id = $(item).attr('id');
            var form = new FormData();
            form.append('id', id);
            $('.modal-title').text('{{trans('admin.edit_employee')}}');
            pub_id = id;
            $.ajax({
                url: '{{url('/admin/employee/data', [] , env('APP_ENV') === 'local' ?  false : true)}}',
                method: 'POST',
                data: form,
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': csrf_token},
                success: function (response) {
                    $('#general-form input[name=name]').val(response.data.name);
                    $('#general-form input[name=email]').val(response.data.email);
                    $('#general-form input[name=phone]').val(response.data.phone);
                    $('#group_id').val(response.data.group_id + '').trigger('change');
                    $('#group_id').select2();
                    $('.general_modal').modal('toggle');
                    edit = true;
                    add = false;

                },
                error: function () {
                    toastr['error']('{{trans('admin.general_error_message')}}', '{{trans('admin.error_title')}}');
                }
            });

        }

        function deleteEmployee(item) {
            ban(item, '{{url('/admin/employee/delete', [] , env('APP_ENV') === 'local' ?  false : true)}}', {
                error_message: '{{trans('admin.general_error_message')}}',
                error_title: '{{trans('admin.error_title')}}',
                ban_title: "{{trans('admin.delete_action')}}",
                ban_message: "{{trans('admin.delete_message')}}",
                inactivate: "{{trans('admin.delete_action')}}",
                cancel: "{{trans('admin.cancel')}}"
            });
        }
    </script>
@endsection
