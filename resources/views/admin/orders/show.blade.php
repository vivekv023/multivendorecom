@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
            <li class="breadcrumb-item active">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>₹{{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>₹{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th class="text-success">₹{{ number_format($order->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Address</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->shipping_address }}</p>
                    @if($order->notes)
                    <hr>
                    <p class="mb-0"><strong>Notes:</strong> {{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Order Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>Order #:</strong> {{ $order->order_number }}</p>
                    <p><strong>Customer:</strong> {{ $order->user->name }}</p>
                    <p><strong>Vendor:</strong> {{ $order->vendor->name }}</p>
                    <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                    <p><strong>Total:</strong> <span class="text-success">₹{{ number_format($order->total_amount, 2) }}</span></p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Update Status</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>

            @if($order->payment)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>Amount:</strong> ₹{{ number_format($order->payment->amount, 2) }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $order->payment->status == 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($order->payment->status) }}
                        </span>
                    </p>
                    <p><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}</p>
                    <p><strong>Transaction ID:</strong> <small>{{ $order->payment->transaction_id }}</small></p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
