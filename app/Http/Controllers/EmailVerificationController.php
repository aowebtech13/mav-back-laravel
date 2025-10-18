<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class EmailVerificationController extends Controller
{
    /**
     * Handle email verification
     */
    public function verify(Request $request, $id, $hash): JsonResponse|RedirectResponse
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification link'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Email already verified'
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully'
        ]);
    }

    /**
     * Resend verification email for authenticated user
     */
    public function resend(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified'
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent'
        ]);
    }

    /**
     * Check if user's email is verified
     */
    public function status(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'email_verified' => $request->user()->hasVerifiedEmail()
        ]);
    }
}
