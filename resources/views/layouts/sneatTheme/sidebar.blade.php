<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span class="text-primary">
                    <img src="{{ empresaLogo() }}" class="img-fluid" width="100"
                        alt="LOGO">
                </span>
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item active open">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboards">Dashboards</div>
                <span class="badge rounded-pill bg-danger ms-auto">5</span>
            </a>
            <ul class="menu-sub">
                <li class="menu-item active">
                    <a href="{{ route('dashboard') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Home</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/html/vertical-menu-template/app-academy-dashboard.html"
                        target="_blank" class="menu-link">
                        <div class="text-truncate" data-i18n="Academy">Notifiaciones</div>
                        <div class="badge rounded-pill bg-label-primary text-uppercase fs-tiny ms-auto">Pro</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Layouts -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div class="text-truncate" data-i18n="Layouts">Configuraciones</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('usuarios.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Without navbar">Usuarios</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('roles.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Fluid">Roles</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('permisos.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Container">Permisos</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('asignar.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Blank">Asignar permisos</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item">
            <a href="{{ route('empresas.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-category"></i>
                <div class="text-truncate" data-i18n="Categorias">Empresas</div>
            </a>
        </li>



        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Modulos</span>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-copy"></i>
                <div class="text-truncate" data-i18n="LayoutsPC">Productos y mas</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('categorias.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Categorias">Categorias</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('productos.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Proveedores">Productos</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <svg class="menu-icon tf-icons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                </svg>
                <div class="text-truncate" data-i18n="Account Settings">Socios de negocios</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('clientes.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Clientes">Clientes</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('proveedores.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Proveedores">Proveedores</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cart"></i>
                <div class="text-truncate" data-i18n="Authentications">Ventas</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('sales.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Basic">Pos venta</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div class="text-truncate" data-i18n="Basic">Ventas del dia</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div class="text-truncate" data-i18n="Basic">Ventas del mes</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Pages -->

        <li class="menu-item">
            <a href="{{ route('categorias.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-file-json"></i>
                <div class="text-truncate" data-i18n="Josn">Lector JSON</div>
            </a>
        </li>
    </ul>
</aside>
