@extends('layouts/contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('vendors/css/forms/select/select2.min.css'))}}">
@endsection

@section('page-style')
    <link href="{{url('/css/jquery.loader.css')}}" rel="stylesheet"/>
@endsection

@section('content')
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{trans('admin.send_notification_title')}}</h4>
                    </div>
                    <div class="card-body">
                        <form id="general-form">
                            <div class="row">

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="title_ar">{{trans('admin.title_ar')}}</label>
                                        <input type="text" class="form-control" id="title_ar"
                                               name="title_ar" placeholder="{{trans('admin.title_ar')}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="title_en">{{trans('admin.title_en')}}</label>
                                        <input type="text" class="form-control" id="title_en"
                                               name="title_en" placeholder="{{trans('admin.title_en')}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-12 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="message_ar">{{trans('admin.message_ar')}}</label>
                                        <textarea class="form-control" id="message_ar"
                                                  name="message_ar"
                                                  placeholder="{{trans('admin.message_ar')}}"></textarea>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-12 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="message_en">{{trans('admin.message_en')}}</label>
                                        <textarea class="form-control" id="message_en"
                                                  name="message_en"
                                                  placeholder="{{trans('admin.message_en')}}"></textarea>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-12 col-12">
                                    <label class="form-label" for="user_id">{{trans('admin.users')}}</label>
                                    <select id="user_id" class="select2 form-select" name="user_id[]" multiple>
                                        @foreach($users as $key => $user)
                                            <option value="{{$user->id}}">{{$user->name.' '.$user->full_phone}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br>
                                <div class="col-xl-12 col-md-12 col-12">
                                    <h5 style="color: #9d2d2d;">{{trans('admin.notification_note')}}</h5>
                                </div>


                                <hr>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <button type="submit" class="btn btn-primary">{{trans('admin.save')}}</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js'))}}"></script>

@endsection
@section('page-script')
    <script src="{{url('/js/scripts/custom/jquery.loader.js')}}"></script>
    <script>
        const csrf_token = '{{csrf_token()}}';
    </script>
    <script src="{{url('/js/scripts/custom/utils.js')}}"></script>
    <script>
        $(function () {

            $('#user_id').select2();

            $('#general-form').submit(function (e) {
                e.preventDefault();
                sendAjaxRequest(this, '{{url('/admin/notification/send', [] , env('APP_ENV') === 'local' ?  false : true)}}', {
                    error_message: '{{trans('admin.general_error_message')}}',
                    error_title: '',
                    loader: true,
                    clear: true
                });
            });
        });
    </script>
@endsection
