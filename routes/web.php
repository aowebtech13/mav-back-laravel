<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api-docs', function () {
    return view('api-docs');
});

Route::get('/api', function () {
    return response()->json([
        'message' => 'Laravel Authentication API',
        'version' => '1.0',
        'documentation' => url('/api-docs'),
        'endpoints' => [
            'register' => 'POST /api/auth/register',
            'login' => 'POST /api/auth/login',
            'logout' => 'POST /api/auth/logout',
            'profile' => 'GET /api/auth/profile',
            'verify_email' => 'GET /api/email/verify/{id}/{hash}',
            'resend_verification' => 'POST /api/email/resend',
            'verification_status' => 'GET /api/email/status'
        ]
    ]);
});
