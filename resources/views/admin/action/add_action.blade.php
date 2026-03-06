@extends('layouts/contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
@endsection

@section('page-style')
    <link href="{{url('/css/jquery.loader.css')}}" rel="stylesheet" />
    <link href="{{url('/css/dropify.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .dropify-message p {
            line-height: 3.5rem !important;
            font-size: 22px !important;
        }
    </style>
@endsection

@section('content')
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{$title}}</h4>
                    </div>
                    <div class="card-body">
                        <form id="general-form">
                            <div class="row">

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="title_ar">{{trans('admin.title_ar')}}</label>
                                        <input type="text" class="form-control" id="title_ar" name="title_ar"
                                            @if(isset($action) && $action) value="{{$action->title_ar}}" @endif
                                            placeholder="{{trans('admin.title_ar')}}" />
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="title_en">{{trans('admin.title_en')}}</label>
                                        <input type="text" class="form-control" id="title_en" name="title_en"
                                            @if(isset($action) && $action) value="{{$action->title_en}}" @endif
                                            placeholder="{{trans('admin.title_en')}}" />
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="start_date">{{trans('admin.start_date')}}</label>
                                        <input type="date" class="form-control" min="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" id="start_date" name="start_date"
                                            @if(isset($action) && $action) value="{{$action->start_date}}" @endif
                                            placeholder="{{trans('admin.start_date')}}" />
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="end_date">{{trans('admin.end_date')}}</label>
                                        <input type="date" class="form-control" min="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" id="end_date" name="end_date"
                                            @if(isset($action) && $action) value="{{$action->end_date}}" @endif
                                            placeholder="{{trans('admin.end_date')}}" />
                                    </div>
                                </div>


                                <div class="col-xl-12 col-md-12 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                            for="description_ar">{{trans('admin.description_ar')}}</label>
                                        <textarea class="form-control textarea-editor" id="description_ar"
                                            name="description_ar"
                                            placeholder="{{trans('admin.description_ar')}}">@if(isset($action) && $action) {{$action->description_ar}} @endif</textarea>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-12 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                            for="description_en">{{trans('admin.description_en')}}</label>
                                        <textarea class="form-control textarea-editor" id="description_en"
                                            name="description_en"
                                            placeholder="{{trans('admin.description_en')}}">@if(isset($action) && $action) {{$action->description_en}} @endif</textarea>
                                    </div>
                                </div>

                                {{-- <div class="col-xl-6 col-md-6 col-12">--}}
                                    {{-- <div class="mb-1">--}}
                                        {{-- <label class="form-label" --}} {{--
                                            for="video_url">{{trans('admin.video_url')}}</label>--}}
                                        {{-- <input type="url" class="form-control" id="video_url" --}} {{--
                                            name="video_url" --}} {{-- @if(isset($action) && $action)
                                            value="{{$action->video_url}}" @endif--}} {{--
                                            placeholder="{{trans('admin.video_url')}}" />--}}
                                        {{-- </div>--}}
                                    {{-- </div>--}}


                                <div class="col-xl-12 col-md-12 col-12">
                                    <div class="mb-1" id="dropify_image">
                                    </div>
                                </div>

                                <hr>

                                {{-- <div class="mb-1">--}}
                                    {{-- <label class="form-label" for="images">{{trans('admin.images')}}</label>--}}
                                    {{-- <input name="images[]" class="form-control dt-full-name" id="images" type="file"
                                        --}} {{-- multiple />--}}
                                    {{-- </div>--}}

                                {{-- <div class="mb-1 row" id="images_div">--}}
                                    {{-- @if(isset($action) && $action)--}}
                                    {{-- @foreach($action->images() as $image)--}}
                                    {{-- @if($action->image()->id === $image->id) @continue @endif--}}
                                    {{-- <div style="height: 150px;" class="col-md-4" id="image_{{$image->id}}">--}}
                                        {{-- <img style="max-width: 100%;max-height: 100%;" src="{{url($image->image)}}"
                                            --}} {{-- class="img-responsive" />--}}
                                        {{-- <button type="button" onclick="removeImage('{{$image->id}}')">--}}
                                            {{-- حذف--}}
                                            {{-- </button>--}}
                                        {{-- </div>--}}
                                    {{-- @endforeach--}}
                                    {{-- @endif--}}
                                    {{-- </div>--}}

                                <br>
                                <hr>
                                <br>

                                <div class="col-xl-12 col-md-12 col-12">
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
@endsection
@section('page-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.9.11/tinymce.min.js"></script>
    <script src="{{url('/js/scripts/custom/dropify.min.js')}}"></script>
    <script src="{{url('/js/scripts/custom/jquery.loader.js')}}"></script>
    <script>
        const csrf_token = '{{csrf_token()}}';
    </script>
    <script src="{{url('/js/scripts/custom/utils.js')}}"></script>
    <script src="{{url('/js/scripts/custom/tinymce-config.js')}}"></script>
    <script>
        $(function () {
            @if(isset($action) && $action)
                initDropify('{{$action->image}}');
            @else
                initDropify();
            @endif

            initTinyMCE('.textarea-editor', '{{url('/admin/upload/image')}}', csrf_token);

            $('#general-form').submit(function (e) {
                e.preventDefault();
                sendAjaxRequest(this, '{{url(isset($action) && $action ? '/admin/action/edit/' . $action->id : '/admin/action/add')}}', {
                    error_message: '{{trans('admin.general_error_message')}}',
                    error_title: '',
                    loader: true,
                    load_page: '{{url('/admin/actions')}}'
                });
            });
        });

        function initDropify(image = null) {
            let html = '<label class="control-label" for="image">' +
                '{{trans('admin.image')}}</label>' +
                '<input name="image" type="file" class="dropify" data-default-file="' + (image ? image : '') + '" ' +
                'data-max-file-size="20M" data-allowed-file-extensions="png jpg jpeg"/>';
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

        function removeImage(id) {
            var form = new FormData();
            form.append('id', id);
            $.ajax({
                url: '{{url('/admin/action/remove_image', [], env('APP_ENV') === 'local' ? false : true)}}',
                method: 'POST',
                data: form,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': csrf_token },
                success: function (response) {
                    $('#image_' + id).remove();
                },
                error: function () {
                    toastr['error']('{{trans('admin.general_error_message')}}', '{{trans('admin.error_title')}}');
                }
            });
        }
    </script>
@endsection