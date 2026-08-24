@extends('layouts/contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
@endsection

@section('page-style')
    <link href="{{url('/css/jquery.loader.css')}}" rel="stylesheet" />
    <style>
        .cover-preview-container {
            border: 2px dashed #d8d6de;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            background: #fafafc;
            margin-top: 10px;
        }
        .cover-preview-img {
            max-width: 100%;
            max-height: 220px;
            border-radius: 6px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
    </style>
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
                        <form id="general-form" enctype="multipart/form-data">
                            <div class="row">
                                <!-- Cover Image Section -->
                                <div class="col-12 mb-2">
                                    <label class="form-label font-medium-1" for="magles_image">
                                        <strong>صورة الغلاف (الكفر)</strong>
                                        <span class="text-muted font-small-3 ms-1">(صورة أفقية تغطي المساحة بالكامل - المقاس الموصى به: العرض ضعف الارتفاع 2:1)</span>
                                    </label>
                                    <input type="file" class="form-control" id="magles_image"
                                           name="{{\App\Entities\Key::MAGLES_IMAGE}}"
                                           accept="image/*" />
                                    
                                    <div class="cover-preview-container mt-1">
                                        @if(isset($magles_image) && $magles_image && $magles_image->value)
                                            <div id="preview-wrapper">
                                                <img id="cover-preview" src="{{url($magles_image->value)}}" alt="Cover Preview" class="cover-preview-img mb-1" />
                                                <div><span class="badge bg-light-success">الصورة الحالية</span></div>
                                            </div>
                                        @else
                                            <div id="preview-wrapper">
                                                <img id="cover-preview" src="" alt="Cover Preview" class="cover-preview-img mb-1" style="display: none;" />
                                                <div id="no-preview-text" class="text-muted"><i data-feather="image"></i> لم يتم رفع صورة غلاف بعد</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="exampleFormControlTextarea1">
                                            {{trans('admin.' . \App\Entities\Key::MAGLES_AR)}}
                                        </label>
                                        <textarea class="form-control" name="{{\App\Entities\Key::MAGLES_AR}}"
                                            id="exampleFormControlTextarea1" rows="3"
                                            placeholder=" {{trans('admin.' . \App\Entities\Key::MAGLES_AR)}}">@if(isset($magles_ar) && $magles_ar){{$magles_ar->value}}@endif</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="exampleFormControlTextarea2">
                                            {{trans('admin.' . \App\Entities\Key::MAGLES_EN)}}
                                        </label>
                                        <textarea class="form-control" name="{{\App\Entities\Key::MAGLES_EN}}"
                                            id="exampleFormControlTextarea2" rows="3"
                                            placeholder=" {{trans('admin.' . \App\Entities\Key::MAGLES_EN)}}">@if(isset($magles_en) && $magles_en){{$magles_en->value}}@endif</textarea>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary mt-1" type="submit">{{trans('admin.save')}}</button>
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

            // Live cover image preview
            $('#magles_image').on('change', function(e) {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $('#cover-preview').attr('src', event.target.result).show();
                        $('#no-preview-text').hide();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#general-form').submit(function (e) {
                e.preventDefault();
                sendAjaxRequest(this, '{{url('/admin/magles/save', [], env('APP_ENV') === 'local' ? false : true)}}', {
                    error_message: '{{trans('admin.general_error_message')}}',
                    error_title: '',
                    loader: true,
                });
            });
        });
    </script>
@endsection