<?php

namespace App\Repositories\General;

use App\Entities\DeviceType;
use App\Entities\HttpCode;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Exception\ImageException;
use Intervention\Image\Facades\Image;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class UtilsRepository
{

    public static function translateDate($date)
    {
        $month = ['january', 'february', 'march', 'april', 'may', 'june',
            'july', 'august', 'september', 'october', 'november', 'december'
        ];
        $monthAR = [trans('api.January'), trans('api.February'), trans('api.March'),
            trans('api.April'), trans('api.May'), trans('api.June'), trans('api.July'),
            trans('api.August'), trans('api.September'), trans('api.October'),
            trans('api.November'), trans('api.December')
        ];
        return str_replace($month, $monthAR, strtolower($date));
    }

    public static function translateTime($date)
    {
        return str_replace([
            'am', 'pm'
        ], [
            trans('api.am'),
            trans('api.pm')
        ], strtolower($date));
    }

    public function sortByDate($array)
    {
        usort($array, [$this, 'date_compare']);
        return array_reverse($array);
    }

    public function date_compare($a, $b)
    {
        $t1 = strtotime($a['messages']['created_at']);
        $t2 = strtotime($b['messages']['created_at']);
        return $t1 - $t2;
    }

    // create verification code
    public static function createVerificationCode($id = 0, $length)
    {
        return substr(
            mt_rand(000000000000000, mt_getrandmax()) . strtotime(date('Y-m-d')) . $id .
            mt_rand(00000000000000, mt_getrandmax()), 0, $length);
    }

    public static function getMsgCode($key)
    {
        $codes = [
            'validation_errors' => 33,
            'social_unique' => 34,
            'email_unique' => 37,
            'success' => 200,
            'error' => 404,
            'not_login' => 303,
            'not_accessible' => 606,
            'inactive_status' => 38,
            'requested_status' => 88,
            'blocked_status' => 39,
            'not_found' => 40,
            'credentials' => 41,
            'Confirmed' => 42,
            'old_password' => 43,
            'not_receive_order' => 44,
            'change_status' => 666,
            'phone_error' => 46,

        ];
        return $codes[$key];
    }

    public static function handleResponseApi(array $response)
    {
        $responseArray = [];
        if (isset($response['message']) && !empty($response['message'])) {
            $responseArray['message'] = $response['message'];
        }
        if (isset($response['data'])) {
            $responseArray['data'] = $response['data'];
        }
        return response()->json($responseArray, $response['code']);
    }

    public static function handleResponseFromApi($response)
    {
        $content = json_decode($response->content(), true);
        if ($response->status() === HttpCode::SUCCESS || $response->status() === HttpCode::NOT_VERIFIED) {
            flash()->success($content['message']);
        } else {
            flash()->error($content['message']);
        }
        return $content;
    }

    public static function getHttpCodes($key)
    {
        $codes = HttpCode::getArray();
        return $codes[$key];
    }

    // send email
    public static function sendEmail($data)
    {
        try {
            $email = 'info@dhclub.ae';
            $data['from'] = $email;
            $data['lang'] = App::getLocale();
            Mail::send($data['template'], ['data' => $data,], function ($message) use ($data, $email) {
                $message->to($data['email']);
                $message->setPriority(1);
                $message->from($email);
                $message->subject($data['subject']);
            });
        } catch (\Exception $ex) {
            echo  $ex->getMessage();
        }
    }


    // return json response for web
    public static function response($response, $success_message = null, $success_title = null,
                                    $error_message = null, $error_title = null)
    {
        if (is_object($response) || is_array($response)) {
            return response()->json([
                'data' => $response,
                'message' => $success_message,
                'title' => $success_title
            ], 200);
        } else if ($response === true) {
            return response()->json([
                'message' => $success_message,
                'title' => $success_title
            ], 200);
        } else {
            if ($error_message !== null && $error_title !== null) {
                return response()->json([
                    'message' => $error_message,
                    'title' => $error_title
                ], 403);
            }
            return response()->json([
                'message' => trans('admin.general_error_message'),
                'title' => trans('admin.error_title')
            ], 403);
        }
    }

    // upload image
    public static function createImage($request, $image_name, $image_path, $image_id, $width = 0, $height = 0)
    {
        if ($request->hasFile($image_name)) {
            $image = $request[$image_name];
            $file_name = $image_id . '.png';

            if (!file_exists(public_path($image_path))) {
                mkdir(public_path($image_path), 0755, true);
            }
            try {
                // finally we save the image as a new file
                $img = Image::make($image);
                if ($width != 0 && $height != 0) {
                    $img->resize($width, $height);
                }
                $img->save($image_path . $file_name);
                return $image_path . $file_name;
            } catch (ImageException $ex) {
            }
        }
        return false;
    }


    // upload image
    public static function createImageBase64($base64, $image_path, $image_id, $width = 0, $height = 0)
    {
        $file_name = $image_id . '.png';
        if (!file_exists(public_path($image_path))) {
            mkdir(public_path($image_path), 0755, true);
        }
        try {
            // finally we save the image as a new file
            $img = Image::make($base64);
            if ($width != 0 && $height != 0) {
                $img->resize($width, $height);
            }
            $img->save($image_path . $file_name);
            return $image_path . $file_name;
        } catch (ImageException $ex) {
        }
        return null;
    }

    // upload image
    public static function uploadImage($request, $image, $image_path, $image_id, $width = 0, $height = 0)
    {
        $file_name = $image_id . '.png';

        if (!file_exists(public_path($image_path))) {
            mkdir(public_path($image_path), 0755, true);
        }
        try {
            // finally we save the image as a new file
            $img = Image::make($image);
            if ($width != 0 && $height != 0) {
                $img->resize($width, $height);
            }
            $img->save($image_path . $file_name);
            return $image_path . $file_name;
        } catch (ImageException $ex) {
        }
        return false;
    }

    public static function uploadFiles($request, $file_name, $filePath, $file_id)
    {
        if ($request->hasFile($file_name)) {
            $file = $request->file($file_name);
            $newFileName = $file_id . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path($filePath))) {
                mkdir(public_path($filePath), 0755, true);
            }
            // move file from ~/tmp to "uploads" directory
            if (!$file->move($filePath, $newFileName)) {
                return false;
            }
            return $filePath . $newFileName;
        }
        return false;
    }


    // send android push notification
    public static function sendAndroidFCM($notification_data, $device_tokens)
    {
        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60 * 20);
        $option = $optionBuiler->build();
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($notification_data);
        $data = $dataBuilder->build();
        $downstreamResponse = FCM::sendTo($device_tokens, $option, null, $data);
        if ($downstreamResponse->numberSuccess()) {
            return true;
        } else {
            return false;
        }
    }

    // send ios push notification
    public static function sendIosFCM($notification_data, $notification_data_obj, $device_tokens)
    {
        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60 * 20);
        $notificationBuilder = new PayloadNotificationBuilder($notification_data['title']);
        $notificationBuilder->setBody($notification_data['message'])
            ->setSound('default');
        $option = $optionBuiler->build();
        $notification = $notificationBuilder->build();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($notification_data_obj);

        $data = $dataBuilder->build();
        $downstreamResponse = FCM::sendTo($device_tokens, $option, $notification, $data);
        if ($downstreamResponse->numberSuccess()) {
            return true;
        } else {
            return false;
        }
    }

    public static function sendSMS($phone, $message, $lang)
    {
        $url = 'https://app.chat-api.com/sendMessage?token=';

        $ch = curl_init();
        $data = [
            "phone" => $phone,
            "body" => $message,
        ];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $server_output = curl_exec($ch);
        curl_close($ch);
    }

    private static function objectToArray($d)
    {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }
        if (is_array($d)) {
            return array_map(__FUNCTION__, $d);
        } else {
            return $d;
        }
    }


    public static function sendFCMNotification($user, array $notification_obj, array $extraData)
    {
        // if user has device token
        if ($user->device_token != null) {
            $notification_data = [
                'title' => trans('api.' . $notification_obj['title_key']),
                'message' => str_replace([
                    '{reason}',
                    '{product_name}',
                    '{days}',
                    '{user_name}',
                ], [
                        isset($extraData['reason']) ? $extraData['reason'] : '',
                        isset($extraData['product_name']) ? $extraData['product_name'] : '',
                        isset($extraData['days']) ? $extraData['days'] : '',
                        isset($extraData['user_name']) ? $extraData['user_name'] : '',
                    ]
                    , trans('api.' . $notification_obj['message_key']))
            ];
            $notification_data_obj = array_merge($notification_data, $notification_obj);
            // send  push notification to user based on device type
            if ($user->device_type == DeviceType::IOS) {
                self::sendIosFCM($notification_data, $notification_data_obj, $user->device_token);
            } else if ($user->device_type == DeviceType::ANDROID) {
                self::sendAndroidFCM($notification_data_obj, $user->device_token);
            }
        }
        Notification::create($notification_obj);
    }

    public static function sendNotification(array $tokens, string $title, string $body,
                                            array $notificationData)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmKey = 'AAAAjDXwdcM:APA91bHiqxqSXHIamC09-s8AYrEyYtZhrk-vPzHhm9n8F-LQ79HpQg2huAec2V8N-QfwkpVgWUdGLd83xrkImy_rW1h9u1MxXewx7L8cjCW201T-A5DkBQm9N9TCmiu69Y6TFLTWgTpg';

        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => $title,
                "body" => $body,
                'sound' => 'default'
            ],
            "data" => $notificationData,
            'priority' => 'high'
        ];
        $RESPONSE = json_encode($data);
        $headers = [
            'Authorization:key=' . $FcmKey,
            'Content-Type: application/json',
        ];
        // CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $RESPONSE);
        $output = curl_exec($ch);
        curl_close($ch);
    }

}

?>
