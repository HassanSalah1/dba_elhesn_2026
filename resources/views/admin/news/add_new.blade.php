@extends('layouts/contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('vendors/css/forms/select/select2.min.css'))}}">
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
                                            @if(isset($new) && $new) value="{{$new->title_ar}}" @endif
                                            placeholder="{{trans('admin.title_ar')}}" />
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="title_en">{{trans('admin.title_en')}}</label>
                                        <input type="text" class="form-control" id="title_en" name="title_en"
                                            @if(isset($new) && $new) value="{{$new->title_en}}" @endif
                                            placeholder="{{trans('admin.title_en')}}" />
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="category_id">{{trans('admin.category')}}</label>
                                        <select class="form-control" name="category_id" id="category_id">
                                            @foreach($categories as $category)
                                                <option @if(isset($new) && $new->category_id === $category->id) selected @endif
                                                    value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-12 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                            for="short_description_ar">{{trans('admin.short_description_ar')}}</label>
                                        <textarea class="form-control" id="short_description_ar" name="short_description_ar"
                                            placeholder="{{trans('admin.short_description_ar')}}">@if(isset($new) && $new) {{$new->short_description_ar}} @endif</textarea>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-12 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                            for="short_description_en">{{trans('admin.short_description_en')}}</label>
                                        <textarea class="form-control" id="short_description_en" name="short_description_en"
                                            placeholder="{{trans('admin.short_description_en')}}">@if(isset($new) && $new) {{$new->short_description_en}} @endif</textarea>
                                    </div>
                                </div>


                                <div class="col-xl-12 col-md-12 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                            for="description_ar">{{trans('admin.description_ar')}}</label>
                                        <textarea class="form-control textarea-editor" id="description_ar"
                                            name="description_ar"
                                            placeholder="{{trans('admin.description_ar')}}">@if(isset($new) && $new) {{$new->description_ar}} @endif</textarea>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-12 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                            for="description_en">{{trans('admin.description_en')}}</label>
                                        <textarea class="form-control textarea-editor" id="description_en"
                                            name="description_en"
                                            placeholder="{{trans('admin.description_en')}}">@if(isset($new) && $new) {{$new->description_en}} @endif</textarea>
                                    </div>
                                </div>

                                {{-- <div class="col-xl-6 col-md-6 col-12">--}}
                                    {{-- <div class="mb-1">--}}
                                        {{-- <label class="form-label" --}} {{--
                                            for="video_url">{{trans('admin.video_url')}}</label>--}}
                                        {{-- <input type="url" class="form-control" id="video_url" --}} {{--
                                            name="video_url" --}} {{-- @if(isset($new) && $new) value="{{$new->video_url}}"
                                            @endif--}} {{-- placeholder="{{trans('admin.video_url')}}" />--}}
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
                                    {{-- @if(isset($new) && $new)--}}
                                    {{-- @foreach($new->images() as $image)--}}
                                    {{-- @if($new->image()->id === $image->id) @continue @endif--}}
                                    {{-- <div style="height: 150px;" class="col-md-4" id="image_{{$image->id}}">--}}
                                        {{-- <img style="max-width: 100%;max-height: 100%;" --}} {{--
                                            src="{{url($image->image)}}" --}} {{-- class="img-responsive" />--}}
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
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js'))}}"></script>
@endsection
@section('page-script')
    initTinyMCE('.textarea-editor', '{{url('/admin/upload/image')}}', csrf_token);

    $('#general-form').submit(function (e) {
    e.preventDefault();
    sendAjaxRequest(this, '{{url(isset($new) && $new ? '/admin/new/edit/' . $new->id : '/admin/new/add')}}', {
    error_message: '{{trans('admin.general_error_message')}}',
    error_title: '',
    loader: true,
    load_page: '{{url('/admin/news')}}'
    });
    });
    });

    function initDropify(image = null) {
    let html = '<label class="control-label" for="image">' +
        '{{trans('admin.image')}}</label>' +
    '<input name="image" type="file" class="dropify" data-default-file="' + (image ? image : '') + '" ' +
                    ' data-max-file-size="20M" data-allowed-file-extensions="png jpg jpeg" />';
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
    url: '{{url('/admin/new/remove_image', [], env('APP_ENV') === 'local' ? false : true)}}',
    method: 'POST',
    data: form,
    processData: false,
    contentType: false,
    headers: {'X-CSRF-TOKEN': csrf_token},
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