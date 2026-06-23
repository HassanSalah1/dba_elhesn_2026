@extends("general.email.template")

@section('subject')
    {{ trans('admin.forget_password_subject') }}
@stop

@section('title')
    @if(isset($data['lang']) && $data['lang'] == 'ar')
        <h1 style="font-size: 24px; font-weight: 700; color: #0b8a45; margin: 0 0 15px 0; line-height: 1.3; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: right;">استعادة كلمة المرور 🔑</h1>
        <p style="font-size: 15px; color: #666666; line-height: 1.6; margin: 0 0 25px 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: right;">
            مرحباً {{ $data['user']->name ?? '' }}، لقد طلبتم استعادة كلمة المرور الخاصة بحسابكم في تطبيق نادي دبا الحصن.
        </p>
    @else
        <h1 style="font-size: 24px; font-weight: 700; color: #0b8a45; margin: 0 0 15px 0; line-height: 1.3; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: left;">Reset Password 🔑</h1>
        <p style="font-size: 15px; color: #666666; line-height: 1.6; margin: 0 0 25px 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: left;">
            Hello {{ $data['user']->name ?? '' }}, you have requested to reset your password for your Dibba Al-Hisn Club account.
        </p>
    @endif
@stop

@section('message')
    @if(isset($data['lang']) && $data['lang'] == 'ar')
        <div style="background-color: #f2f8f5; border: 1px solid #d1ebd9; border-radius: 16px; padding: 25px; margin-bottom: 25px; text-align: center; direction: rtl;">
            <p style="font-size: 15px; color: #555555; margin: 0 0 15px 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">يرجى الضغط على الزر أدناه لتعيين كلمة مرور جديدة:</p>
            <a href="{{ url('/change/password/'.$data['code']) }}" style="display: inline-block; background-color: #0b8a45; color: #ffffff; font-weight: bold; text-decoration: none; padding: 12px 30px; border-radius: 12px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 16px; box-shadow: 0 4px 12px rgba(11, 138, 69, 0.2);">تعيين كلمة المرور</a>
            <p style="font-size: 12px; color: #888888; margin: 15px 0 0 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">أو يمكنك نسخ ولصق الرابط التالي في المتصفح الخاص بك:</p>
            <p style="font-size: 12px; color: #0b8a45; word-break: break-all; margin: 5px 0 0 0; font-family: monospace;">{{ url('/change/password/'.$data['code']) }}</p>
        </div>
    @else
        <div style="background-color: #f2f8f5; border: 1px solid #d1ebd9; border-radius: 16px; padding: 25px; margin-bottom: 25px; text-align: center; direction: ltr;">
            <p style="font-size: 15px; color: #555555; margin: 0 0 15px 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Please click the button below to reset your password:</p>
            <a href="{{ url('/change/password/'.$data['code']) }}" style="display: inline-block; background-color: #0b8a45; color: #ffffff; font-weight: bold; text-decoration: none; padding: 12px 30px; border-radius: 12px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 16px; box-shadow: 0 4px 12px rgba(11, 138, 69, 0.2);">Reset Password</a>
            <p style="font-size: 12px; color: #888888; margin: 15px 0 0 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Or copy and paste this link into your browser:</p>
            <p style="font-size: 12px; color: #0b8a45; word-break: break-all; margin: 5px 0 0 0; font-family: monospace;">{{ url('/change/password/'.$data['code']) }}</p>
        </div>
    @endif
@stop
