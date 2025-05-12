<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span class="text-primary">
                    <img src="{{ empresaLogo() }}" class="img-fluid" width="100" alt="LOGO">
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
                <i class="menu-icon tf-icons bx bxs-book-content"></i>
                <div class="text-truncate" data-i18n="Categorias">Empresas</div>
            </a>
        </li>



        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Modulos</span>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxs-shopping-bags"></i>
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
                <i class="menu-icon tf-icons bx bxs-briefcase"></i>
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
                <i class="menu-icon tf-icons bx bxs-cart-alt"></i>
                <div class="text-truncate" data-i18n="Authentications">Ventas</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('sales.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Basic">Pos venta</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('sales.getdays') }}" class="menu-link">
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
