@extends("general.email.template")

@section('subject')
    {{ $data['subject'] }}
@stop

@section('title')
    @if(isset($data['lang']) && $data['lang'] == 'ar')
        <h1 style="font-size: 24px; font-weight: 700; color: #0b8a45; margin: 0 0 15px 0; line-height: 1.3; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: right;">{{ $data['subject'] }}</h1>
    @else
        <h1 style="font-size: 24px; font-weight: 700; color: #0b8a45; margin: 0 0 15px 0; line-height: 1.3; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: left;">{{ $data['subject'] }}</h1>
    @endif
@stop

@section('message')
    <div style="background-color: #f9f9f9; border: 1px solid #eeeeee; border-radius: 16px; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 15px; color: #333333; line-height: 1.6; text-align: {{ isset($data['lang']) && $data['lang'] == 'ar' ? 'right' : 'left' }};">
        {!! $data['message'] !!}
    </div>
@stop