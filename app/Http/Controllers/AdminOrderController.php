<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminOrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user || !$user->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin only.');
        }

        $filters = $request->only(['vendor_id', 'user_id', 'status', 'search']);
        
        $orders = $this->orderService->getAllForAdmin($filters);
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $vendors = Vendor::orderBy('name')->get();
        $users = User::where('role', 'customer')->orderBy('name')->get();

        return view('admin.orders.index', compact('orders', 'statuses', 'vendors', 'users'));
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();
        
        if (!$user || !$user->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin only.');
        }

        $order = $this->orderService->getById($order->id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $user = $request->user();
        
        if (!$user || !$user->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin only.');
        }

        $this->orderService->updateStatus($order, $request->status);

        return redirect()->back()->with('success', 'Order status updated successfully');
    }
}
