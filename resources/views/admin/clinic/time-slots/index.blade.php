@extends('layouts.contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
@endsection

@section('page-style')
    <link href="{{url('/css/jquery.loader.css')}}" rel="stylesheet"/>
@endsection

@section('form_input')
    <div class="mb-1">
        <label class="form-label" for="day_of_week">{{trans('admin.day_of_week')}}</label>
        <select name="day_of_week" id="day_of_week" class="form-select">
            <option value="Saturday">السبت</option>
            <option value="Sunday">الأحد</option>
            <option value="Monday">الإثنين</option>
            <option value="Tuesday">الثلاثاء</option>
            <option value="Wednesday">الأربعاء</option>
            <option value="Thursday">الخميس</option>
            <option value="Friday">الجمعة</option>
        </select>
    </div>

    <div class="mb-1">
        <label class="form-label" for="start_time">{{trans('admin.start_time')}}</label>
        <input type="time" name="start_time" class="form-control" id="start_time" required/>
    </div>

    <div class="mb-1">
        <label class="form-label" for="end_time">{{trans('admin.end_time')}}</label>
        <input type="time" name="end_time" class="form-control" id="end_time" required/>
    </div>

    <div class="mb-1">
        <label class="form-label" for="max_bookings">{{trans('admin.max_bookings')}}</label>
        <input type="number" name="max_bookings" min="1" value="1" class="form-control" id="max_bookings" required/>
    </div>

    <div class="mb-1">
        <label class="form-label" for="status">{{trans('admin.status')}}</label>
        <select name="status" id="status" class="form-select">
            <option value="1">{{trans('admin.active_status')}}</option>
            <option value="0">{{trans('admin.blocked_status')}}</option>
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
                                {{trans('admin.add')}}
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
                dropify: false
            });

            onClose();

            loadDataTables('{{ url("/admin/clinic/time-slots/data") }}',
                ['day_of_week', 'start_time', 'end_time', 'max_bookings', 'status', 'actions'], '',
                {
                    'show': '{{trans('admin.show')}}',
                    'first': '{{trans('admin.first')}}',
                    'last': '{{trans('admin.last')}}',
                    'filter': '{{trans('admin.filter')}}',
                    'filter_type': '{{trans('admin.type_filter')}}',
                    fancy: false
                });

            $('#general-form').submit(function (e) {
                e.preventDefault();
                sendModalAjaxRequest(this, '{{url('/admin/clinic/time-slot/add')}}',
                    '{{url('/admin/clinic/time-slot/edit')}}', {
                        error_message: '{{trans('admin.general_error_message')}}',
                        error_title: '',
                        loader: true,
                    });
            });
        });

        function editSlot(item) {
            var id = $(item).attr('id');
            var form = new FormData();
            form.append('id', id);
            $('.modal-title').text('{{trans('admin.edit')}}');
            pub_id = id;
            $.ajax({
                url: '{{url('/admin/clinic/time-slot/data')}}',
                method: 'POST',
                data: form,
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': csrf_token},
                success: function (response) {
                    $('#general-form select[name=day_of_week]').val(response.data.day_of_week);
                    // Extract HH:MM if it has seconds
                    let start = response.data.start_time;
                    if(start.split(':').length === 3) {
                        start = start.substring(0, 5);
                    }
                    let end = response.data.end_time;
                    if(end.split(':').length === 3) {
                        end = end.substring(0, 5);
                    }
                    $('#general-form input[name=start_time]').val(start);
                    $('#general-form input[name=end_time]').val(end);
                    $('#general-form input[name=max_bookings]').val(response.data.max_bookings);
                    $('#general-form select[name=status]').val(response.data.status);
                    $('.general_modal').modal('toggle');
                    edit = true;
                    add = false;
                },
                error: function () {
                    toastr['error']('{{trans('admin.general_error_message')}}', '{{trans('admin.error_title')}}');
                }
            });
        }

        function deleteSlot(item) {
            ban(item, '{{url('/admin/clinic/time-slot/delete')}}', {
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
