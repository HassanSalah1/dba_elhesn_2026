@php
    $configData = \App\Helpers\Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', $title)

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row m-0">
            <!-- Brand logo-->
            <a class="brand-logo" href="#">
                <h2 class="brand-text text-primary ms-1">{{trans('admin.app_name')}}</h2>
            </a>
            <!-- /Brand logo-->

            <!-- Left Text-->
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    <img style="height: 600px !important;" class="img-fluid" src="{{asset('images/logo/dh_logo.png')}}" alt="Login V2"/>
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Login-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    <h2 class="card-title fw-bold mb-1">{{trans('admin.welcome_text')}}</h2>
                    <p class="card-text mb-2">{{trans('admin.Please sign-in to your account')}}</p>
                    <form class="auth-login-form mt-2">
                        <div class="mb-1">
                            <label class="form-label" for="login-email">{{trans('admin.Email')}}</label>
                            <input class="form-control" id="login-email" type="text" name="email"
                                   placeholder="{{trans('admin.Email')}}"
                                   aria-describedby="login-email" autofocus="" tabindex="1"/>
                        </div>
                        <div class="mb-1">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="login-password">{{trans('admin.Password')}}</label>
                            </div>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input class="form-control form-control-merge" id="login-password" type="password"
                                       name="password" placeholder="············"
                                       aria-describedby="login-password" tabindex="2"/>
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>
                        </div>
                        <div class="mb-1">
                            <div class="form-check">
                                <input class="form-check-input" id="remember-me" name="remember" type="checkbox"
                                       tabindex="3"/>
                                <label class="form-check-label"
                                       for="remember-me"> {{trans('admin.Remember Me')}}</label>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100" tabindex="4">{{trans('admin.Sign in')}}</button>
                    </form>

                </div>
            </div>
            <!-- /Login-->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
@endsection

@section('page-script')
    <script>
        $(function () {
            let form = $('.auth-login-form');
            form.validate({
                onkeyup: function (element) {
                    $(element).valid();
                },
                rules: {
                    'email': {
                        required: true,
                        email: true
                    },
                    'password': {
                        required: true,
                        minlength: 6,
                    }
                },
                messages: {
                    email: {
                        required: '{{trans('admin.required_error_message')}}',
                        email: '{{trans('admin.enter_valid_email_message')}}'
                    },
                    password: {
                        required: '{{trans('admin.required_error_message')}}',
                        minlength: '{{trans('admin.minlength_error_message')}}',
                    }
                },
                submitHandler: function (formElement) {
                    let formData = new FormData(formElement);
                    $.ajax({
                    url: '{{url("/admin/auth/login" , [] , env('APP_ENV') === 'local' ?  false : true)}}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'},
                    success: function (response) {
                        window.location = '{{url('/admin/home?first_time=1' , [] , env('APP_ENV') === 'local' ?  false : true)}}';
                    },
                    error: function (response) {
                        if (response.status === 403) {
                            toastr['error'](response.responseJSON.message, {
                                closeButton: true,
                                closeMethod: 'fadeOut',
                                closeDuration: 300,
                                closeEasing: 'swing',
                                tapToDismiss: false
                            });
                        } else {
                            toastr['error']('{{trans('admin.general_error_message')}}', {
                                closeButton: true,
                                closeMethod: 'fadeOut',
                                closeDuration: 300,
                                closeEasing: 'swing',
                                tapToDismiss: false
                            });
                        }
                    }
                });//ajax request
                }
            });
        });
    </script>
@endsection
