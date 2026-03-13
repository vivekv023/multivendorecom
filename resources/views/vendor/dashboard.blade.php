@extends('layouts.app')

@section('title', 'Vendor Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="dashboard-header">
        <h2><i class="fas fa-store me-2"></i>Vendor Dashboard</h2>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ $stats['total_orders'] }}</div>
                            <div class="stat-label">Total Orders</div>
                        </div>
                        <div class="stat-icon orders">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ $stats['pending_orders'] }}</div>
                            <div class="stat-label">Pending</div>
                        </div>
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ $stats['processing_orders'] }}</div>
                            <div class="stat-label">Processing</div>
                        </div>
                        <div class="stat-icon processing">
                            <i class="fas fa-cog"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">₹{{ number_format($stats['total_revenue'], 2) }}</div>
                            <div class="stat-label">Revenue</div>
                        </div>
                        <div class="stat-icon revenue">
                            <i class="fas fa-indian-rupee-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-list me-2"></i>My Orders</h5>
            <span class="badge bg-primary">{{ $orders->total() }} Orders</span>
        </div>
        
        <div class="card-body p-0">
            @if($orders->isEmpty())
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h5 class="text-muted">No Orders Yet</h5>
                <p class="text-muted mb-0">When customers place orders, they will appear here.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <span class="order-number">{{ $order->order_number }}</span>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <span class="customer-name">{{ $order->user->name }}</span>
                                    <br><span class="customer-email">{{ $order->user->email }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $order->items->count() }} items</span>
                            </td>
                            <td>
                                <span class="amount">₹{{ number_format($order->total_amount, 2) }}</span>
                            </td>
                            <td>
                                <form action="{{ route('vendor.orders.update-status', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select status-select" onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                @if($order->payment)
                                    <span class="badge bg-{{ $order->payment->status == 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->payment->status) }}
                                    </span>
                                    <div><small class="text-muted">{{ $order->payment->formatted_method ?? 'Payment' }}</small></div>
                                @else
                                    <span class="badge bg-secondary">No Payment</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @if($orders->hasPages())
        <div class="card-footer bg-white">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

@foreach($orders as $order)
<div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-bag me-2"></i>Order #{{ $order->order_number }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <h6 class="border-bottom pb-2 mb-2"><i class="fas fa-user me-2"></i>Customer Information</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $order->user->name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $order->user->email }}</p>
                            <p class="mb-0"><strong>Phone:</strong> {{ $order->user->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <h6 class="border-bottom pb-2 mb-2"><i class="fas fa-info-circle me-2"></i>Order Information</h6>
                            <p class="mb-1"><strong>Status:</strong> 
                                <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                            <p class="mb-1"><strong>Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                            <p class="mb-0"><strong>Total:</strong> <span class="text-success fw-bold">₹{{ number_format($order->total_amount, 2) }}</span></p>
                        </div>
                    </div>
                </div>
                
                @if($order->shipping_address)
                <div class="mb-4">
                    <h6><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $order->shipping_address }}
                    </div>
                </div>
                @endif
                
                @if($order->notes)
                <div class="mb-4">
                    <h6><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $order->notes }}
                    </div>
                </div>
                @endif
                
                <div>
                    <h6><i class="fas fa-box me-2"></i>Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div>{{ $item->product->name }}</div>
                                        <small class="text-muted">{{ $item->product->vendor->name }}</small>
                                    </td>
                                    <td>₹{{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end">₹{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th class="text-end text-success">₹{{ number_format($order->total_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
