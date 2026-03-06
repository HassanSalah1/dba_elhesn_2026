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
                                            {{trans('admin.' . \App\Entities\Key::CLUB_HISTORY_AR)}}
                                        </label>
                                        <textarea class="form-control" name="{{\App\Entities\Key::CLUB_HISTORY_AR}}"
                                            id="exampleFormControlTextarea1" rows="3"
                                            placeholder=" {{trans('admin.' . \App\Entities\Key::CLUB_HISTORY_AR)}}">@if(isset($history_ar) && $history_ar){{$history_ar->value}}@endif</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="exampleFormControlTextarea1">
                                            {{trans('admin.' . \App\Entities\Key::CLUB_HISTORY_EN)}}
                                        </label>
                                        <textarea class="form-control" name="{{\App\Entities\Key::CLUB_HISTORY_EN}}"
                                            id="exampleFormControlTextarea1" rows="3"
                                            placeholder=" {{trans('admin.' . \App\Entities\Key::CLUB_HISTORY_EN)}}">@if(isset($history_en) && $history_en){{$history_en->value}}@endif</textarea>
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
                console.log('test');
                sendAjaxRequest(this, '{{url('/admin/history/save', [], env('APP_ENV') === 'local' ? false : true)}}', {
                    error_message: '{{trans('admin.general_error_message')}}',
                    error_title: '',
                    loader: true,
                });
            });
        });
    </script>
@endsection