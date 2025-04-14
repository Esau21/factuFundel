@extends('layouts.sneatTheme.base')
@section('title', 'Profile')

@section('content')
<!-- Profile Card -->
<div class="row g-6 d-flex justify-content-center">
    <div class="col-xl-4 col-lg-6 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="mx-auto my-4">
                    <img src="https://ui-avatars.com/api/?name={{ $user?->name }}&background=F7374F&color=fff&size=128"
                        alt="Avatar Image"
                        class="rounded-circle border border-3 border-light shadow-sm"
                        style="width: 100px; height: 100px;" />
                </div>

                <h4 class="mb-1 fw-semibold text-capitalize">{{ $user->name }}</h4>

                <div class="mb-3">
                    <small class="text-muted d-block">Cargo</small>
                    <span class="fw-medium">{{ $user->profile }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-center gap-2 mb-4 flex-wrap">
                    <span class="badge bg-label-secondary">{{ $user->email }}</span>
                    <span class="badge 
                        {{ $user->status === 'Active' ? 'bg-label-success' : ($user->status === 'Locked' ? 'bg-label-danger' : 'bg-label-secondary') }}">
                        {{ $user->status }}
                    </span>
                </div>

                <div>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-primary d-inline-flex align-items-center">
                        <i class="bx bx-left-arrow-alt me-2"></i>Regresar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Profile Card -->
@endsection
