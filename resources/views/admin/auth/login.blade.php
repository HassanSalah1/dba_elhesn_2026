@php
    $configData = \App\Helpers\Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', $title)

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
    <style>
        /* Custom premium styles inspired by the logo's color palette */
        :root {
            --logo-primary: #584cdb;
            --logo-secondary: #8d82fd;
            --logo-accent: #79addd;
            --logo-light: #f4f5fa;
            --logo-bg-grad: linear-gradient(135deg, var(--logo-primary) 0%, var(--logo-secondary) 100%);
        }
        
        .auth-wrapper.auth-cover {
            background-color: var(--logo-light);
        }
        
        /* Left illustration panel background with elegant subtle gradient */
        .auth-wrapper.auth-cover .auth-inner .col-lg-8 {
            background: linear-gradient(135deg, rgba(88, 76, 219, 0.04) 0%, rgba(121, 173, 221, 0.04) 100%);
            border-right: 1px solid rgba(88, 76, 219, 0.08);
        }
        
        /* Brand logo block styling */
        .auth-wrapper .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }
        
        .auth-wrapper .brand-logo:hover {
            opacity: 0.85;
        }
        
        .auth-wrapper .brand-logo .brand-text {
            color: var(--logo-primary) !important;
            font-weight: 700;
            letter-spacing: 0.5px;
            font-family: 'Montserrat', sans-serif;
        }
        
        /* Custom input fields focus styling */
        .form-control:focus {
            border-color: var(--logo-primary) !important;
            box-shadow: 0 0 0 0.2rem rgba(88, 76, 219, 0.15) !important;
        }
        
        .input-group-merge .form-control:focus ~ .input-group-text,
        .input-group-merge .form-control:focus ~ .input-group-text:hover {
            border-color: var(--logo-primary) !important;
        }
        
        /* Custom main button button-primary override */
        .btn-primary {
            background: var(--logo-bg-grad) !important;
            border-color: var(--logo-primary) !important;
            box-shadow: 0 4px 12px rgba(88, 76, 219, 0.3) !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background: linear-gradient(135deg, #4c41c2 0%, #7c71ed 100%) !important;
            box-shadow: 0 6px 16px rgba(88, 76, 219, 0.4) !important;
            transform: translateY(-1px);
        }
        
        /* Checkbox color personalization */
        .form-check-input:checked {
            background-color: var(--logo-primary) !important;
            border-color: var(--logo-primary) !important;
        }
        
        /* Smooth illustration animation */
        .auth-cover img.img-fluid {
            transition: transform 0.5s ease;
        }
        .auth-cover img.img-fluid:hover {
            transform: scale(1.02);
        }

        /* Fixed responsive viewport layout rules for Vuexy Auth Cover */
        @media (max-width: 991.98px) {
            .auth-wrapper.auth-cover .brand-logo {
                position: relative !important;
                top: 0 !important;
                left: 0 !important;
                margin: 2.5rem auto 1.5rem !important;
                justify-content: center !important;
                padding-left: 0 !important;
            }
            .auth-wrapper.auth-cover .auth-bg {
                background-color: transparent !important;
                padding-top: 1rem !important;
                padding-bottom: 2rem !important;
            }
            .auth-inner {
                background-color: var(--logo-light);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
        }
    </style>
@endsection

@section('content')
    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row m-0">
            <!-- Brand logo-->
            <a class="brand-logo" href="#">
                <img src="{{asset('images/logo/logo.svg')}}" alt="Logo" style="height: 38px; width: auto;" />
                <h2 class="brand-text text-primary ms-1 mb-0">{{trans('admin.app_name')}}</h2>
            </a>
            <!-- /Brand logo-->

            <!-- Left Text-->
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    <img style="max-height: 520px; width: auto; object-fit: contain;" class="img-fluid" src="{{asset('images/logo/dh_logo.png')}}" alt="Login V2"/>
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Login-->
            <div class="d-flex col-12 col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    <h2 class="card-title fw-bold mb-1">{{trans('admin.welcome_text')}}</h2>
                    <p class="card-text mb-2">{{trans('admin.Please sign-in to your account')}}</p>
                    @if(session('error'))
                        <div class="alert alert-danger mb-2" role="alert">{{ session('error') }}</div>
                    @endif
                    <form class="auth-login-form mt-2" action="/admin/auth/login" method="POST">
                        @csrf
                        <div class="mb-1">
                            <label class="form-label" for="login-email">{{trans('admin.Email')}}</label>
                            <input class="form-control" id="login-email" type="text" name="email"
                                   value="{{ old('email') }}"
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
                    url: '/admin/auth/login',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'},
                    success: function (response) {
                        window.location = '/admin/home?first_time=1';
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
