@extends('layouts.contentLayoutMaster')

@section('title', $title)

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    <link href="{{url('/css/custom/fancybox.css')}}" rel="stylesheet" type="text/css"/>
@endsection


@section('content')
    <!-- Basic table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">
                            {{@$subscribeObj->sport->title}}
                        </h4>
                    </div>
                    <div class="card-datatable">
                        <table class="table table-bordered">
                            <tr>
                                <td>{{trans('admin.player_full_name_ar')}}</td>
                                <td>{{$subscribeObj->player_full_name_ar}}</td>

                                <td>{{trans('admin.player_full_name_en')}}</td>
                                <td>{{$subscribeObj->player_full_name_en}}</td>
                            </tr>

                            <tr>
                                <td>{{trans('admin.birth_date')}}</td>
                                <td>{{$subscribeObj->birth_date}}</td>

                                <td>{{trans('admin.birth_place')}}</td>
                                <td>{{$subscribeObj->birth_place}}</td>
                            </tr>

                            <tr>
                                <td>{{trans('admin.nationality')}}</td>
                                <td>{{$subscribeObj->nationality}}</td>

                                <td>{{trans('admin.clothes_size')}}</td>
                                <td>{{$subscribeObj->clothes_size}}</td>
                            </tr>

                            <tr>
                                <td>{{trans('admin.shoe_size')}}</td>
                                <td>{{$subscribeObj->shoe_size}}</td>

                                @if($subscribeObj->is_another_club)
                                    <td>{{trans('admin.is_another_club')}}</td>
                                    <td>{{$subscribeObj->another_club_name}}</td>
                                @endif
                            </tr>

                            <tr>
                                <td>{{trans('admin.weight')}}</td>
                                <td>{{$subscribeObj->weight}}</td>

                                <td>{{trans('admin.height')}}</td>
                                <td>{{$subscribeObj->height}}</td>
                            </tr>

                            <tr>
                                <td>{{trans('admin.parent_full_name_ar')}}</td>
                                <td>{{$subscribeObj->parent_full_name_ar}}</td>

                                <td>{{trans('admin.parent_full_name_en')}}</td>
                                <td>{{$subscribeObj->parent_full_name_en}}</td>
                            </tr>

                            <tr>
                                <td>{{trans('admin.parent_email')}}</td>
                                <td>{{$subscribeObj->parent_email}}</td>

                                <td>{{trans('admin.guardian_phone')}}</td>
                                <td>{{$subscribeObj->guardian_phone}}</td>
                            </tr>

                            <tr>
                                <td>{{trans('admin.parent_category')}}</td>
                                <td>{{trans('admin.' . ($subscribeObj->parent_category == 1 ? 'resident' : 'citizen'))}}</td>

                                {{--                                <td>{{trans('admin.guardian_phone')}}</td>--}}
                                {{--                                <td>{{$subscribeObj->guardian_phone}}</td>--}}
                            </tr>


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Basic table -->
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.min.js')) }}"></script>
@endsection
@section('page-script')
    <script src="{{url('/js/scripts/custom/fancybox.min.js')}}"></script>
    <script src="{{url('/js/scripts/custom/utils.js')}}"></script>
    <script>
        $(function () {

            loadDataTables('{{ url("/admin/sport/subscription/data") }}',
                ['player_full_name_ar', 'player_full_name_en', 'parent_full_name_ar',
                    'parent_full_name_en', 'nationality', 'actions'], '',
                {
                    'show': '{{trans('admin.show')}}',
                    'first': '{{trans('admin.first')}}',
                    'last': '{{trans('admin.last')}}',
                    'filter': '{{trans('admin.filter')}}',
                    'filter_type': '{{trans('admin.type_filter')}}',
                    fancy: true
                });
        });

    </script>
@endsection
