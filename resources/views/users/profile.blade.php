@extends('layouts.sneatTheme.base')
@section('title', 'Profile')

@section('content')
<!-- Profile Form Card -->
<div class="row g-6 d-flex justify-content-center">
    <div class="col-xl-5 col-lg-6 col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('usuarios.updateProfile') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="text-center mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ $user?->name }}&background=F7374F&color=fff&size=128"
                            alt="Avatar Image"
                            class="rounded-circle border border-3 border-light shadow-sm"
                            style="width: 100px; height: 100px;" />
                    </div>

                    <h4 class="text-center fw-semibold mb-4">Editar Perfil</h4>

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña <small class="text-muted">(Opcional)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-left-arrow-alt me-1"></i> Regresar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-save me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Profile Form Card -->
@endsection
