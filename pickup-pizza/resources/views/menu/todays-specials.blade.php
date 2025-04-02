@extends('layouts.app')

@section('title', 'Today\'s Specials')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Today's Specials</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4 fw-bold text-center">Today's Specials</h1>
            <p class="lead text-center">Great deals available today!</p>
        </div>
    </div>
    
    @if($daySpecials->isNotEmpty())
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h2 class="h4 mb-0">{{ $today }} Special</h2>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        @foreach($daySpecials as $special)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 hover-shadow">
                                <div class="card-body p-4">
                                    <h3 class="h5 fw-bold mb-3">{{ $special->name }}</h3>
                                    <p class="mb-3">{{ $special->description }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fs-4 fw-bold text-danger">${{ number_format($special->price, 2) }}</span>
                                        <a href="{{ route('menu.product', ['category' => $special->category->slug, 'product' => $special->slug]) }}" class="btn btn-outline-danger">View Deal</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if($walkInSpecials->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0">Pickup Specials</h2>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        @foreach($walkInSpecials as $special)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 hover-shadow">
                                <div class="card-body p-4">
                                    <h3 class="h5 fw-bold mb-3">{{ $special->name }}</h3>
                                    <p class="mb-3">{{ $special->description }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fs-4 fw-bold text-primary">${{ number_format($special->price, 2) }}</span>
                                        <a href="{{ route('menu.product', ['category' => $special->category->slug, 'product' => $special->slug]) }}" class="btn btn-outline-primary">View Deal</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 