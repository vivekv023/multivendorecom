@extends('layouts.app')

@section('title', 'Manage Orders')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container py-4">
    <div class="page-header">
        <h2><i class="fas fa-clipboard-list me-2"></i>Manage Orders</h2>
    </div>

    <div class="filter-card">
        <h5><i class="fas fa-filter me-2"></i>Filters</h5>
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search Order</label>
                <input type="text" name="search" class="form-control" placeholder="Search order number..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Customer</label>
                <select name="user_id" class="form-select">
                    <option value="">All Customers</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Vendor</label>
                <select name="vendor_id" class="form-select">
                    <option value="">All Vendors</option>
                    @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                        {{ $vendor->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-500">Total Orders: {{ $orders->total() }}</span>
        </div>
        
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Vendor</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><span class="order-number">{{ $order->order_number }}</span></td>
                        <td>
                            <div class="customer-info">
                                <span class="customer-name">{{ $order->user->name }}</span>
                                <br><span class="customer-email">{{ $order->user->email }}</span>
                            </div>
                        </td>
                        <td><span class="vendor-badge">{{ $order->vendor->name }}</span></td>
                        <td><span class="amount">₹{{ number_format($order->total_amount, 2) }}</span></td>
                        <td>
                            <span class="status-badge {{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td style="white-space: nowrap;">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="View Order">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p class="text-muted mb-0">No orders found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="pagination-wrapper">
            <span class="pagination-info">
                Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries
            </span>
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
