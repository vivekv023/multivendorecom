<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $userType = $request->user_type;
        $email = $request->email;
        $password = $request->password;

        if ($userType === 'vendor') {
            return $this->vendorLogin($email, $password, $request);
        } elseif ($userType === 'admin' || $userType === 'customer') {
            return $this->userLogin($email, $password, $request, $userType);
        }

        return back()->withErrors(['email' => 'Invalid login type']);
    }

    protected function vendorLogin(string $email, string $password, LoginRequest $request)
    {
        $vendor = Vendor::where('email', $email)->first();

        if (!$vendor) {
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        if (!$vendor->is_active) {
            return back()->withErrors(['email' => 'Your account has been deactivated']);
        }

        if (!Hash::check($password, $vendor->password)) {
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        Auth::guard('vendor')->login($vendor, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->route('vendor.dashboard');
    }

    protected function userLogin(string $email, string $password, LoginRequest $request, string $userType)
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if ($userType === 'admin') {
            $user = User::where('email', $email)->first();
            
            if (!$user || !Hash::check($password, $user->password)) {
                return back()->withErrors(['email' => 'Invalid credentials']);
            }

            if ($user->role !== 'admin') {
                return back()->withErrors(['email' => 'You are not authorized as admin']);
            }

            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->route('admin.orders.index');
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $this->cartService->mergeGuestCartToUserCart(Auth::id());
            
            $cartCount = $this->cartService->getCartItemCount();
            if ($cartCount > 0 && $request->has('redirect_to') && $request->redirect_to === 'checkout') {
                return redirect()->route('checkout.index');
            }
            
            return redirect()->route('products.index');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        if (Auth::guard('vendor')->check()) {
            Auth::guard('vendor')->logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('products.index');
    }
}
