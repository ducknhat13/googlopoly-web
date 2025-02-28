<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300..700&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Googlopoly Photos</title>
    <link rel="stylesheet" href="{{ asset('css/giaodien.css') }}">
</head>
<body>
    <!-- Header -->
    <header>
        <div>
            <h1 style="font-size: 24px; font-family: 'Quicksand', sans-serif;">
                <span style="color: #4285F4;">G</span><span style="color: #EA4335;">o</span><span style="color: #FBBC05;">o</span><span style="color: #4285F4;">g</span><span style="color: #34A853;">l</span><span style="color: #FBBC05;">o</span>poly <span style="color: white;">Photos</span>
            </h1>
        </div>
        <nav>
            <a href="#home">Home</a>
            <a href="#features">Features</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h2>Quản lý ảnh và video dễ dàng</h2>
        <p>Lưu giữ khoảnh khắc đáng nhớ của bạn.</p>
        <div class="buttons">
            <button class="btn btn-primary" onclick="document.getElementById('loginForm').classList.toggle('hidden')">Đăng Nhập</button>
            <button class="btn btn-secondary" onclick="document.getElementById('registerForm').classList.toggle('hidden')">Đăng Ký</button>
        </div>
    </section>

    <!-- Login Form -->
    <div id="loginForm" class="{{ $errors->has('login') ? '' : 'hidden' }}">
        <button class="close-btn" onclick="document.getElementById('loginForm').classList.add('hidden')">✖</button>
        <h3>Đăng Nhập</h3>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            @if ($errors->has('login'))
                <div class="error-message">
                    {{ $errors->first('login') }}
                </div>
            @endif
            <button class="btn-sign" type="submit">Đăng Nhập</button>
        </form>
    </div>

    <!-- Register Form -->
    <div id="registerForm" class="{{ $errors->has('email') || $errors->has('password') ? '' : 'hidden' }}">
        <button class="close-btn" onclick="document.getElementById('registerForm').classList.add('hidden')">✖</button>
        <h3>Đăng Ký</h3>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Tên" value="{{ old('name') }}" required>
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <input type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu" required>
            @if ($errors->has('email'))
                <div class="error-message">
                    {{ $errors->first('email') }}
                </div>
            @endif
            @if ($errors->has('password'))
                <div class="error-message">
                    {{ $errors->first('password') }}
                </div>
            @endif
            <button class="btn-sign" type="submit">Đăng Ký</button>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 Googlopoly Photos. All rights reserved.</p>
    </footer>
</body>
</html>