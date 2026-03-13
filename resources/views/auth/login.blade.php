<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MultiVendor E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-body">
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-store fa-3x mb-3"></i>
            <h2>MultiVendor E-Commerce</h2>
            <p class="mb-0 opacity-75">Sign in to your account</p>
        </div>
        
        <div class="user-type-tabs">
            <button type="button" class="user-type-tab active" data-type="customer">
                <i class="fas fa-user"></i>
                Customer
            </button>
            <button type="button" class="user-type-tab" data-type="vendor">
                <i class="fas fa-store"></i>
                Vendor
            </button>
            <button type="button" class="user-type-tab" data-type="admin">
                <i class="fas fa-user-shield"></i>
                Admin
            </button>
        </div>

        <div class="card-body p-4">
            @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <input type="hidden" name="user_type" id="userType" value="{{ request('user_type', 'customer') }}">
                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required value="{{ old('email') }}">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>
            </form>
            <div class="text-center">
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Back to Store
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
