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
                <div class="text-truncate" data-i18n="Dashboards">Inicio</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item active">
                    <a href="{{ route('dashboard') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Home</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Layouts -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <img src="{{ asset('assets/img/ajustes.png') }}" class="menu-icon tf-icons" alt="img">
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
                <img src="{{ asset('assets/img/empresa.png') }}" class="menu-icon tf-icons" alt="img">
                <div class="text-truncate" data-i18n="Categorias">Empresas</div>
            </a>
        </li>



        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Modulos</span>
        </li>

        @can('bancos_view')
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <img src="{{ asset('assets/img/piggy-bank.png') }}" class="menu-icon tf-icons" alt="img">
                    <div class="text-truncate" data-i18n="Account Settings">Bancos</div>
                </a>
                <ul class="menu-sub">
                    @can('bancos_index')
                        <li class="menu-item">
                            <a href="{{ route('bancos.index') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Bancos">Bancos | Cuentas</div>
                            </a>
                        </li>
                    @endcan
                    @can('cheques_index')
                        <li class="menu-item">
                            <a href="{{ route('cheques.indexCheques') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Bancos">Cheques | Recibidos</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan


        @can('productos_y_mas_view')
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <img src="{{ asset('assets/img/box.png') }}" class="menu-icon tf-icons" alt="img">
                    <div class="text-truncate" data-i18n="LayoutsPC">Productos y mas</div>
                </a>
                <ul class="menu-sub">
                    @can('categorias_index')
                        <li class="menu-item">
                            <a href="{{ route('categorias.index') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Categorias">Categorias</div>
                            </a>
                        </li>
                    @endcan
                    @can('productos_index')
                        <li class="menu-item">
                            <a href="{{ route('productos.index') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Proveedores">Productos</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan


        @can('socios_negocios_view')
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <img src="{{ asset('assets/img/briefcase.png') }}" class="menu-icon tf-icons" alt="img">
                    <div class="text-truncate" data-i18n="Account Settings">Socios de negocios</div>
                </a>
                <ul class="menu-sub">
                    @can('clientes_index')
                        <li class="menu-item">
                            <a href="{{ route('clientes.index') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Clientes">Clientes</div>
                            </a>
                        </li>
                    @endcan
                    {{--  @can('proveedores_index')
                        <li class="menu-item">
                            <a href="{{ route('proveedores.index') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Proveedores">Proveedores</div>
                            </a>
                        </li>
                    @endcan --}}
                </ul>
            </li>
        @endcan

        @can('ventas_view')
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <img src="{{ asset('assets/img/wallet.png') }}" class="menu-icon tf-icons" alt="img">
                    <div class="text-truncate" data-i18n="Authentications">Ventas</div>
                </a>
                <ul class="menu-sub">
                    @can('ventas_index')
                        <li class="menu-item">
                            <a href="{{ route('sales.SalesIndex') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Basic">Ventas</div>
                            </a>
                        </li>
                    @endcan
                    @can('ventas_del_dia_index')
                        <li class="menu-item">
                            <a href="{{ route('sales.getdays') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Basic">Ventas del dia</div>
                            </a>
                        </li>
                    @endcan

                    @can('ventas_del_mes_index')
                        <li class="menu-item">
                            <a href="{{ route('sales.ventasDelMes') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Basic">Ventas del mes</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan


        @can('facturacion_view')
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <img src="{{ asset('assets/img/libro-digital.png') }}" class="menu-icon tf-icons" alt="img">
                    <div class="text-truncate" data-i18n="Facturacion">Facturacíon</div>
                </a>
                <ul class="menu-sub">
                    @can('facuracion_index')
                        <li class="menu-item">
                            <a href="{{ route('facturacion.index') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Basic">Documentos DTE</div>
                            </a>
                        </li>
                    @endcan
                    @can('correlativos_index')
                        <li class="menu-item">
                            <a href="{{ route('correlativos.correlativosDteIndex') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Basic">Correlativos DTE</div>
                            </a>
                        </li>
                    @endcan
                    @can('json_lector_index')
                        <li class="menu-item">
                            <a href="{{ route('sales.index') }}" class="menu-link">
                                <div class="text-truncate" data-i18n="Basic">Lector JSON</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
    </ul>
</aside>
