<?php
$email = 'Hajer';
$password = '27128';
$attempt = \Illuminate\Support\Facades\Auth::attempt(['email' => $email, 'password' => $password]);
echo "Login by email attempt: " . ($attempt ? 'Success' : 'Fail') . "\n";
if ($attempt) {
    $user = auth()->user();
    echo "User role: " . $user->role . "\n";
} else {
    // maybe they login by name?
    $attemptName = \Illuminate\Support\Facades\Auth::attempt(['name' => $email, 'password' => $password]);
    echo "Login by name attempt: " . ($attemptName ? 'Success' : 'Fail') . "\n";
}
