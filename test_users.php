<?php
$users = \App\Models\User::whereIn('name', ['Hajer', 'almir', 'Asaad'])
    ->orWhereIn('email', ['Hajer', 'almir', 'Asaad'])
    ->get(['id', 'name', 'email', 'role']);
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
}
