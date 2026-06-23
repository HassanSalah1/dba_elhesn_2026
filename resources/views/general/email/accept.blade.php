@extends("general.email.template")

@section('subject')
    {{ trans('email.account_verification_subject') }}
@stop

@section('title')
    @if(isset($data['lang']) && $data['lang'] == 'ar')
        <h1 style="font-size: 24px; font-weight: 700; color: #0b8a45; margin: 0 0 15px 0; line-height: 1.3; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: right;">أهلاً بكم في نادي دبا الحصن 🎉</h1>
        <p style="font-size: 15px; color: #666666; line-height: 1.6; margin: 0 0 25px 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: right;">
            تم تفعيل حسابك بنجاح. مرحباً بك في المستقبل الرقمي لنادي دبا الحصن الرياضي. استكشف الآن لوحتك التفاعلية المخصصة لمتابعة المباريات القادمة، حجز جلسات العيادة الطبية.
        </p>
    @else
        <h1 style="font-size: 24px; font-weight: 700; color: #0b8a45; margin: 0 0 15px 0; line-height: 1.3; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: left;">Welcome to Dibba El-Hisn Club 🎉</h1>
        <p style="font-size: 15px; color: #666666; line-height: 1.6; margin: 0 0 25px 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: left;">
            Account activated successfully. Welcome to the future of Dibba Al-Hisn Sports Club. Explore upcoming matches, book medical clinic sessions, and fully sports journey.
        </p>
    @endif
@stop

@section('message')
    @if(isset($data['lang']) && $data['lang'] == 'ar')
        <!-- Arabic Status Card -->
        <div style="background-color: #f2f8f5; border: 1px solid #d1ebd9; border-radius: 16px; padding: 20px; margin-bottom: 25px; text-align: right; direction: rtl;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
                <tr>
                    <td valign="middle" style="font-size: 16px; font-weight: bold; color: #0f5b30; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding-left: 15px;">
                        تم تفعيل الحساب بنجاح
                    </td>
                    <td width="36" valign="middle" align="left">
                        <div style="width: 36px; height: 36px; background-color: #0b8a45; border-radius: 50%; text-align: center; line-height: 36px; color: #ffffff; font-size: 18px; font-weight: bold; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">✓</div>
                    </td>
                </tr>
            </table>
        </div>
    @else
        <!-- English Status Card -->
        <div style="background-color: #f2f8f5; border: 1px solid #d1ebd9; border-radius: 16px; padding: 20px; margin-bottom: 25px; text-align: left; direction: ltr;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" dir="ltr">
                <tr>
                    <td width="36" valign="middle" align="left">
                        <div style="width: 36px; height: 36px; background-color: #0b8a45; border-radius: 50%; text-align: center; line-height: 36px; color: #ffffff; font-size: 18px; font-weight: bold; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">✓</div>
                    </td>
                    <td valign="middle" style="padding-left: 15px; font-size: 16px; font-weight: bold; color: #0f5b30; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                        Account activated successfully
                    </td>
                </tr>
            </table>
        </div>
    @endif
@stop