<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorLoginRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Services\VendorService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class VendorAuthController extends Controller
{
    public function __construct(
        protected VendorService $vendorService,
        protected OrderService $orderService
    ) {}

    public function showLoginForm()
    {
        return view('vendor.login');
    }

    public function login(VendorLoginRequest $request)
    {
        $vendor = $this->vendorService->authenticate(
            $request->email,
            $request->password
        );

        Auth::guard('vendor')->login($vendor, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->route('vendor.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.login');
    }

    public function dashboard()
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('vendor.login');
        }
        
        $vendor = Auth::guard('vendor')->user();
        
        $orders = $this->orderService->getVendorOrdersWithRelations($vendor);
        $stats = $this->orderService->getStatsForVendorModel($vendor);

        return view('vendor.dashboard', compact('vendor', 'orders', 'stats'));
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request, $order)
    {
        $vendor = Auth::guard('vendor')->user();
        
        $order = $this->vendorService->findOrderForVendor($vendor, $order);

        if (!$order) {
            abort(404);
        }

        Gate::authorize('vendor.orders.update', $order);
        
        $this->orderService->updateStatus($order, $request->status);

        return redirect()->back()->with('success', 'Order status updated successfully');
    }
}
