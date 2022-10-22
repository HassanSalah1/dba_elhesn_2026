<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAAjDXwdcM:APA91bHiqxqSXHIamC09-s8AYrEyYtZhrk-vPzHhm9n8F-LQ79HpQg2huAec2V8N-QfwkpVgWUdGLd83xrkImy_rW1h9u1MxXewx7L8cjCW201T-A5DkBQm9N9TCmiu69Y6TFLTWgTpg'),
        'sender_id' => env('FCM_SENDER_ID', '602200372675'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
