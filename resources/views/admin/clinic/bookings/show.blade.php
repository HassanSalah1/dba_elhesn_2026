@extends('layouts.contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
@endsection

@section('content')
    <section class="invoice-preview-wrapper">
        <div class="row invoice-preview">
            <!-- Booking Details -->
            <div class="col-xl-9 col-md-8 col-12">
                <div class="card invoice-preview-card">
                    <div class="card-body invoice-padding pb-0">
                        <!-- Header -->
                        <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                            <div>
                                <div class="logo-wrapper">
                                    <h3 class="text-primary font-weight-bold">{{ trans('admin.clinic_title') }}</h3>
                                </div>
                                <p class="card-text mb-25">
                                    <strong>{{ trans('admin.player_name') }}: </strong> 
                                    {{ $bookingObj->user ? $bookingObj->user->name : '' }}
                                </p>
                                <p class="card-text mb-0">
                                    <strong>{{ trans('admin.phone') }}: </strong>
                                    {{ $bookingObj->user ? $bookingObj->user->phone : '' }}
                                </p>
                            </div>
                            <div class="mt-md-0 mt-2">
                                <h4 class="invoice-title">
                                    {{ trans('admin.booking_number') }} <span class="invoice-number">#{{ $bookingObj->id }}</span>
                                </h4>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title"><strong>{{ trans('admin.booking_date') }}:</strong> {{ $bookingObj->booking_date }}</p>
                                </div>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title"><strong>{{ trans('admin.booking_time') }}:</strong> {{ $bookingObj->timeSlot ? $bookingObj->timeSlot->start_time . ' - ' . $bookingObj->timeSlot->end_time : '' }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- /Header -->
                    </div>

                    <hr class="invoice-spacing" />

                    <!-- Booking Info -->
                    <div class="card-body invoice-padding pt-0">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <h5 class="mb-1"><strong>{{ trans('admin.booking_status') }}:</strong></h5>
                                @php
                                    $badgeClass = 'badge-light-warning';
                                    if ($bookingObj->status === 'confirmed') $badgeClass = 'badge-light-primary';
                                    if ($bookingObj->status === 'completed') $badgeClass = 'badge-light-success';
                                    if ($bookingObj->status === 'cancelled') $badgeClass = 'badge-light-danger';
                                @endphp
                                <span class="badge rounded-pill {{ $badgeClass }} fs-6">{{ trans('api.booking_status_' . $bookingObj->status) }}</span>
                            </div>

                            @if($bookingObj->is_for_other)
                                <div class="col-12 mb-2 alert alert-info">
                                    <h5 class="alert-heading">{{ trans('api.booking_for_other') }}</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-0"><strong>{{ trans('admin.name') }}:</strong> {{ $bookingObj->other_name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-0"><strong>{{ trans('admin.phone') }}:</strong> {{ $bookingObj->other_country_code . ' ' . $bookingObj->other_phone }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-12 mb-2">
                                <h5><strong>{{ trans('admin.injury_type') }}:</strong></h5>
                                <p class="card-text">{{ $bookingObj->injury_type ?: 'N/A' }}</p>
                            </div>

                            <div class="col-12 mb-2">
                                <h5><strong>{{ trans('admin.description') }}:</strong></h5>
                                <p class="card-text">{{ $bookingObj->description ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="invoice-spacing" />

                    <!-- Attachments -->
                    <div class="card-body invoice-padding pt-0">
                        <h5><strong>{{ trans('admin.images') }}:</strong></h5>
                        @if($bookingObj->attachments->count() > 0)
                            <div class="row mt-1">
                                @foreach($bookingObj->attachments as $attachment)
                                    <div class="col-md-4 col-sm-6 mb-1">
                                        <div class="border rounded p-1 d-flex flex-column align-items-center">
                                            @if(in_array(strtolower($attachment->file_type), ['jpeg', 'jpg', 'png']))
                                                <a href="{{ $attachment->file_url }}" target="_blank">
                                                    <img src="{{ $attachment->file_url }}" class="img-thumbnail mb-1" style="max-height: 150px;" />
                                                </a>
                                            @else
                                                <div class="p-2 mb-1 bg-light rounded text-center" style="height: 150px; width: 100%; display: flex; align-items: center; justify-content: center;">
                                                    <span class="text-uppercase fw-bold">{{ $attachment->file_type }}</span>
                                                </div>
                                            @endif
                                            <a href="{{ $attachment->file_url }}" class="btn btn-sm btn-outline-primary" target="_blank" download>
                                                {{ $attachment->file_name }} ({{ number_format($attachment->file_size / 1024, 2) }} KB)
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">{{ trans('admin.general_error_message') }} (لا توجد مرفقات)</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="col-xl-3 col-md-4 col-12 invoice-actions mt-md-0 mt-2">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-1">{{ trans('admin.change_status') }}</h5>
                        <div class="mb-1">
                            <select id="status-selector" class="form-select mb-1">
                                <option value="pending" {{ $bookingObj->status == 'pending' ? 'selected' : '' }}>{{ trans('api.booking_status_pending') }}</option>
                                <option value="confirmed" {{ $bookingObj->status == 'confirmed' ? 'selected' : '' }}>{{ trans('api.booking_status_confirmed') }}</option>
                                <option value="completed" {{ $bookingObj->status == 'completed' ? 'selected' : '' }}>{{ trans('api.booking_status_completed') }}</option>
                                <option value="cancelled" {{ $bookingObj->status == 'cancelled' ? 'selected' : '' }}>{{ trans('api.booking_status_cancelled') }}</option>
                            </select>
                            <button id="update-status-btn" class="btn btn-primary w-100">{{ trans('admin.save') }}</button>
                        </div>
                        <a href="{{ url('/admin/clinic/bookings') }}" class="btn btn-outline-secondary w-100 mt-1">
                            {{ trans('admin.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        $(function () {
            $('#update-status-btn').click(function () {
                let status = $('#status-selector').val();
                let id = '{{ $bookingObj->id }}';
                
                Swal.fire({
                    title: '{{ trans("admin.change_status_message") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ trans("admin.save") }}',
                    cancelButtonText: '{{ trans("admin.cancel") }}',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ url("/admin/clinic/booking/status") }}',
                            method: 'POST',
                            data: {
                                id: id,
                                status: status,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ trans("admin.success_title") }}',
                                    text: '{{ trans("admin.process_success_message") }}',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                }).then(function () {
                                    window.location.reload();
                                });
                            },
                            error: function () {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ trans("admin.error_title") }}',
                                    text: '{{ trans("admin.general_error_message") }}',
                                    customClass: {
                                        confirmButton: 'btn btn-danger'
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
