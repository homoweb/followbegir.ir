<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * The panel login page.
     */
    public function showLogin(): Response
    {
        return Inertia::render('Panel/Auth/Login');
    }

    /**
     * The panel registration page.
     */
    public function showRegister(): Response
    {
        return Inertia::render('Panel/Auth/Register');
    }

    /**
     * Authenticate a user on the panel domain.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'ایمیل را وارد کنید.',
            'email.email' => 'ایمیل معتبر نیست.',
            'password.required' => 'رمز عبور را وارد کنید.',
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if ($user === null || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'اطلاعات ورود نادرست است.']);
        }

        if (! $user->isActive()) {
            return back()->withErrors(['email' => 'حساب کاربری شما غیرفعال است. با پشتیبانی تماس بگیرید.']);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Resume a guest checkout draft started on the main site.
        $draft = $request->session()->get('checkout_draft');

        if (is_array($draft) && isset($draft['product_id'])) {
            return redirect()->away(route('main.checkout.resume'));
        }

        return redirect()->intended(route('panel.orders.index'));
    }

    /**
     * Create a new panel account.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'نام را وارد کنید.',
            'email.required' => 'ایمیل را وارد کنید.',
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'password.required' => 'رمز عبور را وارد کنید.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد.',
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => true,
        ]);

        $user->assignRole('user');

        Auth::login($user);
        $request->session()->regenerate();

        $draft = $request->session()->get('checkout_draft');

        if (is_array($draft) && isset($draft['product_id'])) {
            return redirect()->away(route('main.checkout.resume'));
        }

        return redirect()->route('panel.orders.index');
    }

    /**
     * Log the user out of the panel and return them to the landing page.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('main.home');
    }
}
