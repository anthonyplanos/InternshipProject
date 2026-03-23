<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! hash_equals((string) $request->route('id'), (string) $user->getKey())) {
            return redirect()
                ->route('verification.notice')
                ->with('verification_error', 'This verification link belongs to a different account. Please sign in using the same email address that received the link.');
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect()
                ->route('verification.notice')
                ->with('verification_error', 'This verification link does not match your account email. Please request a new verification email.');
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($request->user()->getPostLoginRedirectPath().'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended($request->user()->getPostLoginRedirectPath().'?verified=1');
    }
}
