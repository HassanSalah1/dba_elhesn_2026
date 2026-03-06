@extends('layouts/contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
@endsection

@section('page-style')
    <link href="{{url('/css/jquery.loader.css')}}" rel="stylesheet" />
@endsection

@section('content')
    <!-- Basic Textarea start -->
    <section class="basic-textarea">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{$title}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text"></p>
                        <form id="general-form">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="exampleFormControlTextarea1">
                                            {{trans('admin.' . \App\Entities\Key::CITY_DESCRIPTION_AR)}}
                                        </label>
                                        <textarea class="form-control" name="{{\App\Entities\Key::CITY_DESCRIPTION_AR}}"
                                            id="exampleFormControlTextarea1" rows="3"
                                            placeholder=" {{trans('admin.' . \App\Entities\Key::CITY_DESCRIPTION_AR)}}">@if(isset($about_ar) && $about_ar){{$about_ar->value}}@endif</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="exampleFormControlTextarea1">
                                            {{trans('admin.' . \App\Entities\Key::CITY_DESCRIPTION_EN)}}
                                        </label>
                                        <textarea class="form-control" name="{{\App\Entities\Key::CITY_DESCRIPTION_EN}}"
                                            id="exampleFormControlTextarea1" rows="3"
                                            placeholder=" {{trans('admin.' . \App\Entities\Key::CITY_DESCRIPTION_EN)}}">@if(isset($about_en) && $about_en){{$about_en->value}}@endif</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">{{trans('admin.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Textarea end -->
@endsection

@section('vendor-script')
@endsection
@section('page-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.9.11/tinymce.min.js"></script>
    <script src="{{url('/js/scripts/custom/jquery.loader.js')}}"></script>
    <script>
        var csrf_token = '{{csrf_token()}}';
    </script>
    <script src="{{url('/js/scripts/custom/utils.js')}}"></script>
    <script src="{{url('/js/scripts/custom/tinymce-config.js')}}"></script>
    <script>
        $(function () {
            initTinyMCE('textarea', '{{url('/admin/upload/image', [], env('APP_ENV') === 'local' ? false : true)}}', csrf_token);

            $('#general-form').submit(function (e) {
                e.preventDefault();
                sendAjaxRequest(this, '{{url('/admin/about/save', [], env('APP_ENV') === 'local' ? false : true)}}', {
                    error_message: '{{trans('admin.general_error_message')}}',
                    error_title: '',
                    loader: true,
                });
            });
        });
    </script>
@endsection