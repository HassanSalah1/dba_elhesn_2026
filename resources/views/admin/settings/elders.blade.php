@extends('layouts/contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
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
                        <h4 class="card-title">{{trans('admin.settings_title')}}</h4>
                    </div>
                    <div class="card-body">
                        <form id="general-form">

                            <div class="row">

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::Image_1}}">صورة 1</label>
                                        <input type="file" class="form-control" id="{{\App\Entities\Key::Image_1}}"
                                               accept="image/*"
                                               name="{{\App\Entities\Key::Image_1}}"
                                               @if(isset($image1) && $image1) value="{{$image1->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::Image_1)}}"/>
                                    </div>
                                </div>


                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::name_1}}">اسم 1</label>
                                        <input type="text" class="form-control" id="{{\App\Entities\Key::name_1}}"
                                               name="{{\App\Entities\Key::name_1}}"
                                               @if(isset($name_1) && $name_1) value="{{$name_1->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::name_1)}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::position_1}}">منصب 1</label>
                                        <input type="text" class="form-control" id="{{\App\Entities\Key::position_1}}"
                                               name="{{\App\Entities\Key::position_1}}"
                                               @if(isset($position_1) && $position_1) value="{{$position_1->value}}"
                                               @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::position_1)}}"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::Image_2}}">صورة 2</label>
                                        <input type="file" class="form-control" id="{{\App\Entities\Key::Image_2}}"
                                               accept="image/*"
                                               name="{{\App\Entities\Key::Image_2}}"
                                               @if(isset($image2) && $image2) value="{{$image2->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::Image_2)}}"/>
                                    </div>
                                </div>


                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::name_2}}">اسم 2</label>
                                        <input type="text" class="form-control" id="{{\App\Entities\Key::name_2}}"
                                               name="{{\App\Entities\Key::name_2}}"
                                               @if(isset($name_2) && $name_2) value="{{$name_2->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::name_2)}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::position_2}}">منصب 2</label>
                                        <input type="text" class="form-control" id="{{\App\Entities\Key::position_2}}"
                                               name="{{\App\Entities\Key::position_2}}"
                                               @if(isset($position_2) && $position_2) value="{{$position_2->value}}"
                                               @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::position_2)}}"/>
                                    </div>
                                </div>
                            </div>


                            <div class="row">

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::Image_3}}">صورة 3</label>
                                        <input type="file" class="form-control" id="{{\App\Entities\Key::Image_3}}"
                                               accept="image/*"
                                               name="{{\App\Entities\Key::Image_3}}"
                                               @if(isset($image3) && $image3) value="{{$image3->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::Image_3)}}"/>
                                    </div>
                                </div>


                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::name_3}}">اسم 3</label>
                                        <input type="text" class="form-control" id="{{\App\Entities\Key::name_3}}"
                                               name="{{\App\Entities\Key::name_3}}"
                                               @if(isset($name_3) && $name_3) value="{{$name_3->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::name_3)}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::position_3}}">منصب 3</label>
                                        <input type="text" class="form-control" id="{{\App\Entities\Key::position_3}}"
                                               name="{{\App\Entities\Key::position_3}}"
                                               @if(isset($position_3) && $position_3) value="{{$position_3->value}}"
                                               @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::position_3)}}"/>
                                    </div>
                                </div>
                            </div>


                            <div class="row">

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::Image_1}}">صورة 4</label>
                                        <input type="file" class="form-control" id="{{\App\Entities\Key::Image_4}}"
                                               accept="image/*"
                                               name="{{\App\Entities\Key::Image_4}}"
                                               @if(isset($image4) && $image4) value="{{$image4->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::Image_4)}}"/>
                                    </div>
                                </div>


                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::name_4}}">اسم 4</label>
                                        <input type="text" class="form-control" id="{{\App\Entities\Key::name_4}}"
                                               name="{{\App\Entities\Key::name_4}}"
                                               @if(isset($name_4) && $name_4) value="{{$name_4->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::name_4)}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::position_4}}">منصب 4</label>
                                        <input type="text" class="form-control" id="{{\App\Entities\Key::position_4}}"
                                               name="{{\App\Entities\Key::position_4}}"
                                               @if(isset($position_4) && $position_4) value="{{$position_4->value}}"
                                               @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::position_4)}}"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
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
@endsection
@section('page-script')
    <script src="{{url('/js/scripts/custom/jquery.loader.js')}}"></script>
    <script>
        const csrf_token = '{{csrf_token()}}';
    </script>
    <script src="{{url('/js/scripts/custom/utils.js')}}"></script>
    <script>

        $(function () {

            $('#general-form').submit(function (e) {
                e.preventDefault();
                sendAjaxRequest(this, '{{url('/admin/elders/save', [] , env('APP_ENV') === 'local' ?  false : true)}}', {
                    error_message: '{{trans('admin.general_error_message')}}',
                    error_title: '',
                    loader: true,
                });
            });
        });

    </script>
@endsection
