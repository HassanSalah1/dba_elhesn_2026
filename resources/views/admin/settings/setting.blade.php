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
                                               for="{{\App\Entities\Key::EMAIL}}">{{trans('admin.'.\App\Entities\Key::EMAIL)}}</label>
                                        <input type="email" class="form-control" id="{{\App\Entities\Key::EMAIL}}"
                                               name="{{\App\Entities\Key::EMAIL}}"
                                               @if(isset($email) && $email) value="{{$email->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::EMAIL)}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::PHONE}}">{{trans('admin.'.\App\Entities\Key::PHONE)}}</label>
                                        <input type="tel" class="form-control"
                                               id="{{\App\Entities\Key::PHONE}}"
                                               name="{{\App\Entities\Key::PHONE}}"
                                               @if(isset($phone) && $phone) value="{{$phone->value}}"
                                               @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::PHONE)}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::FACEBOOK}}">{{trans('admin.'.\App\Entities\Key::FACEBOOK)}}</label>
                                        <input type="url" class="form-control" id="{{\App\Entities\Key::FACEBOOK}}"
                                               name="{{\App\Entities\Key::FACEBOOK}}"
                                               @if(isset($facebook) && $facebook) value="{{$facebook->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::FACEBOOK)}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::TWITTER}}">{{trans('admin.'.\App\Entities\Key::TWITTER)}}</label>
                                        <input type="url" class="form-control" id="{{\App\Entities\Key::TWITTER}}"
                                               name="{{\App\Entities\Key::TWITTER}}"
                                               @if(isset($twitter) && $twitter) value="{{$twitter->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::TWITTER)}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::INSTAGRAM}}">{{trans('admin.'.\App\Entities\Key::INSTAGRAM)}}</label>
                                        <input type="url" class="form-control" id="{{\App\Entities\Key::INSTAGRAM}}"
                                               name="{{\App\Entities\Key::INSTAGRAM}}"
                                               @if(isset($instagram) && $instagram) value="{{$instagram->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::INSTAGRAM)}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::YOUTUBE}}">{{trans('admin.'.\App\Entities\Key::YOUTUBE)}}</label>
                                        <input type="url" class="form-control" id="{{\App\Entities\Key::YOUTUBE}}"
                                               name="{{\App\Entities\Key::YOUTUBE}}"
                                               @if(isset($youtube) && $youtube) value="{{$youtube->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::YOUTUBE)}}"/>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="{{\App\Entities\Key::CLUB_STRUCTURE}}">هيكل النادي من اعضاء مجلس ادارة وموظفين</label>
                                        <input type="file" class="form-control" id="{{\App\Entities\Key::CLUB_STRUCTURE}}" accept="application/pdf"
                                               name="{{\App\Entities\Key::CLUB_STRUCTURE}}" @if(isset($CLUB_STRUCTURE) && $CLUB_STRUCTURE) value="{{$CLUB_STRUCTURE->value}}" @endif
                                               placeholder="{{trans('admin.'.\App\Entities\Key::CLUB_STRUCTURE)}}"/>
                                    </div>
                                </div>

                                <input type="hidden" name="latitude"
                                       @if(isset($latitude) && $latitude) value="{{$latitude->value}}" @endif>
                                <input type="hidden" name="longitude"
                                       @if(isset($longitude) && $longitude) value="{{$longitude->value}}" @endif>
                                <div class="col-xl-12 col-md-12 col-12">
                                    <div class="mb-1">
                                        <div id="map" style="height: 250px"></div>
                                    </div>
                                </div>

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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDhoc3Xe0kB2rp77fI1lLLm6MH3I4UDdQw" async defer>
    </script>
    <script>
        let uLatitude = 24.6962019;
        @if(isset($latitude) && $latitude && $latitude->value !== null)
            uLatitude = parseFloat('{{$latitude->value}}');
            @endif
        let uLongitude = 46.7662033;
        @if(isset($longitude) && $longitude && $longitude->value !== null)
            uLongitude = parseFloat('{{$longitude->value}}');
            @endif
        let address = null;
        $(function () {
            initMap(uLatitude, uLongitude);

            $('#general-form').submit(function (e) {
                e.preventDefault();
                sendAjaxRequest(this, '{{url('/admin/setting/save', [] , env('APP_ENV') === 'local' ?  false : true)}}', {
                    error_message: '{{trans('admin.general_error_message')}}',
                    error_title: '',
                    loader: true,
                });
            });
        });

        function initMap(latitude, longitude) {
            $('input[name=latitude]').val(latitude);
            $('input[name=longitude]').val(longitude);

            let latlng = new google.maps.LatLng(latitude, longitude);
            map = new google.maps.Map(document.getElementById('map'), {
                center: latlng,
                zoom: 12,
                scrollwheel: false//set to true to enable mouse scrolling while inside the map area
            });
            var marker = new google.maps.Marker({
                position: latlng,
                title: 'marker',
                draggable: false,
                map: map
            });

            google.maps.event.addListener(map, "click", function (e) {
                //lat and lng is available in e object
                var newLatLng = e.latLng;
                marker.setMap(null);
                marker = new google.maps.Marker({
                    position: newLatLng,
                    title: 'marker',
                    draggable: false,
                    map: map
                });
                geocoder = new google.maps.Geocoder();
                geocoder.geocode({
                    'latLng': newLatLng
                }, function (results, status) {
                    console.log(results[0].formatted_address);
                    // if (infowindow != null) {
                    //if doesn't exist then create a empty InfoWindow object
                    infowindow = new google.maps.InfoWindow();
                    // }
                    //Set the content of InfoWindow
                    infowindow.setContent(results[0].formatted_address);
                    //Tie the InfoWindow to the market
                    infowindow.open(map, marker);
                    address = results[0].formatted_address;
                });

                uLatitude = newLatLng.lat();
                uLongitude = newLatLng.lng();
                $('input[name=latitude]').val(uLatitude);
                $('input[name=longitude]').val(uLongitude);
            });
        }
    </script>
@endsection
