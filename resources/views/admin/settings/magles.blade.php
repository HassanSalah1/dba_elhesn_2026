@extends('layouts/contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
@endsection

@section('page-style')
    <link href="{{url('/css/jquery.loader.css')}}" rel="stylesheet"/>
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
                                            {{trans('admin.'.\App\Entities\Key::MAGLES_AR)}}
                                        </label>
                                        <textarea
                                            class="form-control" name="{{\App\Entities\Key::MAGLES_AR}}"
                                            id="exampleFormControlTextarea1"
                                            rows="3"
                                            placeholder=" {{trans('admin.'.\App\Entities\Key::MAGLES_AR)}}"
                                        >@if(isset($magles_ar) && $magles_ar){{$magles_ar->value}}@endif</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="exampleFormControlTextarea1">
                                            {{trans('admin.'.\App\Entities\Key::MAGLES_EN)}}
                                        </label>
                                        <textarea
                                            class="form-control" name="{{\App\Entities\Key::MAGLES_EN}}"
                                            id="exampleFormControlTextarea1"
                                            rows="3"
                                            placeholder=" {{trans('admin.'.\App\Entities\Key::MAGLES_EN)}}"
                                        >@if(isset($magles_en) && $magles_en){{$magles_en->value}}@endif</textarea>
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
    <script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
    <script src="{{url('/js/scripts/custom/jquery.loader.js')}}"></script>
    <script>
        var csrf_token = '{{csrf_token()}}';
    </script>
    <script src="{{url('/js/scripts/custom/utils.js')}}"></script>
    <script>
        $(function () {
            setInterval(function () {
                $('.mce-notification-inner').css('display', 'none');
                $('#mceu_90').css('display', 'none');
                $('#mceu_91').css('display', 'none');
                $('#mceu_92').css('display', 'none');
                $('#mceu_93').css('display', 'none');
                $('#mceu_46').css('display', 'none');
                $('#mceu_45').css('display', 'none');
                $('#mceu_270').css('display', 'none');
                $('#mceu_271').css('display', 'none');
                $('#mceu_272').css('display', 'none');
                $('#mceu_273').css('display', 'none');
                $('#mceu_274').css('display', 'none');
                $('#mceu_275').css('display', 'none');
            }, 1000);

            if ($("textarea").length > 0) {
                tinymce.init({
                    selector: "textarea",
                    theme: "modern",
                    height: 300,
                    relative_urls: false,
                    remove_script_host: false,
                    plugins: [
                        "advlist autolink link image imagetools lists charmap  print preview hr anchor pagebreak spellchecker",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
                        "save table contextmenu directionality emoticons template paste textcolor"
                    ],
                    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
                    style_formats: [
                        {title: 'Bold text', inline: 'b'},
                        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                        {title: 'Example 1', inline: 'span', classes: 'example1'},
                        {title: 'Example 2', inline: 'span', classes: 'example2'},
                        {title: 'Table styles'},
                        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                    ],
                    images_upload_handler: function (blobInfo, success, failure) {
                        var xhr, formData;
                        xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', '{{url('/admin/upload/image', [] , env('APP_ENV') === 'local' ?  false : true)}}');
                        var token = '{{ csrf_token() }}';
                        xhr.setRequestHeader("X-CSRF-Token", token);
                        xhr.onload = function () {
                            var json;
                            if (xhr.status != 200) {
                                failure('HTTP Error: ' + xhr.status);
                                return;
                            }
                            json = JSON.parse(xhr.responseText);

                            if (!json || typeof json.location != 'string') {
                                failure('Invalid JSON: ' + xhr.responseText);
                                return;
                            }
                            success(json.location);
                        };
                        formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        xhr.send(formData);
                    },
                });
            }

            $('#general-form').submit(function (e) {
                e.preventDefault();
                sendAjaxRequest(this, '{{url('/admin/magles/save', [] , env('APP_ENV') === 'local' ?  false : true)}}', {
                    error_message: '{{trans('admin.general_error_message')}}',
                    error_title: '',
                    loader: true,
                });
            });
        });
    </script>
@endsection
