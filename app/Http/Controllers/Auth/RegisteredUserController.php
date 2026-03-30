<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $normalizedEmail = strtolower(trim((string) $request->input('email')));

        if (User::onlyTrashed()->where('email', $normalizedEmail)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This account is deactivated. Ask the admin to reactivate your account.',
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'acknowledge_policy' => ['accepted'],
        ]);

        $user = User::create([
            'name' => trim((string) $request->name),
            'email' => $normalizedEmail,
            'role' => 'Employee',
            'password' => Hash::make($request->password),
        ]);

        Role::findOrCreate('Employee', 'web');
        $user->assignRole('Employee');

        event(new Registered($user));

        Auth::login($user);

        return redirect($user->getPostLoginRedirectPath());
    }
}
