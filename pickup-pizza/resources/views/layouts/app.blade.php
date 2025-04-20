<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PISA Pizza') }} - @yield('title', 'Home')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #e94532;
            --primary-hover: #d03a29;
            --secondary-color: #2c3e50;
            --accent-color: #ffd166;
            --light-bg: #f8f9fa;
            --dark-bg: #1a1c23;
            --text-color: #333;
            --light-text: #f8f9fa;
            --border-radius: 10px;
            --box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #fff;
        }
        
        main {
            flex: 1;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }
        
        .navbar {
            padding: 1rem 0;
            background-color: var(--dark-bg) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.5px;
        }
        
        .navbar-brand span {
            color: var(--primary-color);
        }
        
        .nav-link {
            font-weight: 500;
            font-size: 1.05rem;
            transition: var(--transition);
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .nav-link.active {
            color: var(--primary-color) !important;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            transition: var(--transition);
            box-shadow: 0 4px 6px rgba(233, 69, 50, 0.2);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(233, 69, 50, 0.3);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .card {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
        }
        
        footer {
            background-color: var(--dark-bg);
            color: var(--light-text);
            padding: 3rem 0 2rem;
        }
        
        footer h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .cart-item-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            font-size: 12px;
            width: 22px;
            height: 22px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .breadcrumb-item.active {
            color: var(--secondary-color);
            font-weight: 500;
        }
        
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            color: var(--light-text);
            margin-right: 10px;
            transition: var(--transition);
        }
        
        .social-icon:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}"><span>PISA</span> Pizza</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item mx-2">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link {{ request()->routeIs('menu*') ? 'active' : '' }}" href="{{ route('menu.index') }}">Menu</a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link position-relative p-2" href="{{ route('cart.index') }}">
                                <i class="bi bi-cart3 fs-5"></i>
                                @if(session()->has('cart') && count(session('cart')) > 0)
                                    <span class="cart-item-count animate__animated animate__bounceIn">{{ count(session('cart')) }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        @if(session('success'))
            <div class="container mt-4">
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="container mt-4">
                <div class="alert alert-danger animate__animated animate__fadeIn">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            </div>
        @endif
        
        @yield('content')
    </main>

    <footer class="mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Pisa Pizza</h5>
                    <p>The Real Italian Taste and Best wings in town.</p>
                    <div class="mt-3">
                        <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Hours</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-clock me-2"></i>Monday: 11:00 AM - 11:00 PM</li>
                        <li class="mb-2"><i class="bi bi-clock me-2"></i>Tuesday: 11:00 AM - 11:00 PM</li>
                        <li class="mb-2"><i class="bi bi-clock me-2"></i>Wednesday: 11:00 AM - 11:00 PM</li>
                        <li class="mb-2"><i class="bi bi-clock me-2"></i>Thursday: 11:00 AM - 11:00 PM</li>
                        <li class="mb-2"><i class="bi bi-clock me-2"></i>Friday: 11:00 AM - 12:00 AM</li>
                        <li class="mb-2"><i class="bi bi-clock me-2"></i>Saturday: 12:00 PM - 12:00 AM</li>
                        <li class="mb-2"><i class="bi bi-clock me-2"></i>Sunday: 12:00 PM - 10:00 PM</li>
                        <li class="mb-2"><i class="bi bi-clock me-2"></i>Holidays: 12:00 PM - 10:00 PM</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>55 Parkdale Ave. N<br>Hamilton, ON L8H 5W7</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i>+1 (905) 547-5777</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i>support@pisapizza.ca</li>
                    </ul>
                </div>
            </div>
            <div class="row mt-4 pt-4 border-top border-secondary">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} Pisa Pizza. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    @stack('scripts')
</body>
</html> 