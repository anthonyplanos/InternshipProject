<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Notifications\VerifyPendingEmail;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $user->name = $validated['name'];

        // If there's a verified pending email, save it to the email column
        if (filled($user->pending_email) && filled($user->pending_email_verified_at)) {
            $user->email = $user->pending_email;
            $user->email_verified_at = $user->pending_email_verified_at;
            $user->pending_email = null;
            $user->pending_email_verified_at = null;
        } elseif (strcasecmp($validated['email'], $user->email) !== 0) {
            // If user is trying to change email without verification, reject
            return Redirect::route('profile.edit')
                ->withInput($request->only('name', 'email'))
                ->withErrors([
                    'email' => __('Verify the new email address before saving your profile.'),
                ]);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function sendPendingEmailVerification(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'new_email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($user->id),
            ],
        ]);

        $newEmail = $validated['new_email'];

        if (strcasecmp($newEmail, (string) $user->email) === 0) {
            return Redirect::route('profile.edit')
                ->withInput(['name' => $user->name, 'email' => $newEmail])
                ->withErrors(['email' => __('Enter a different email address to verify.')]);
        }

        $user->forceFill([
            'pending_email' => $newEmail,
            'pending_email_verified_at' => null,
        ])->save();

        Notification::route('mail', $newEmail)->notify(new VerifyPendingEmail($user, $newEmail));

        return Redirect::route('profile.edit')
            ->withInput(['name' => $user->name, 'email' => $newEmail])
            ->with('status', 'pending-email-verification-link-sent');
    }

    /**
     * @throws AuthorizationException
     */
    public function verifyPendingEmail(Request $request, int $id, string $hash): RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->getKey() !== $id) {
            throw new AuthorizationException();
        }

        $pendingEmail = (string) $user->pending_email;

        if ($pendingEmail === '' || ! hash_equals(sha1(strtolower($pendingEmail)), $hash)) {
            throw new AuthorizationException();
        }

        $user->forceFill([
            'pending_email_verified_at' => now(),
        ])->save();

        return Redirect::route('profile.edit')
            ->withInput(['name' => $user->name, 'email' => $pendingEmail])
            ->with('status', 'pending-email-verified');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
