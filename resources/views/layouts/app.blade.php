@extends('layouts.sneatTheme.base')
@section('content')
    <div class="row">
        <div class="col-xxl-8 mb-6 order-0">
            <div class="card">
                <div class="d-flex align-items-start row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <div class="alert alert-primary d-flex align-items-center" role="alert">
                                <i class="bi bi-person-circle me-2 fs-4"></i>
                                <div>
                                    Bienvenido de vuelta, <strong>{{ Auth::user()->name ?? 'Usuario' }}</strong> 👋<br>
                                    Empresa: <span
                                        class="fw-semibold">{{ Auth::user()->empresa?->nombre ?? 'Sin empresa' }}</span>
                                </div>
                            </div>
                            <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-6">
                            <img src="{{ empresaLogo() }}" height="175" alt="View Badge User" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-lg-12 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                <div class="avatar flex-shrink-0">
                                    <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success"
                                        class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-1">Usuarios</p>
                            <h4 class="card-title mb-3">{{ $users }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                <div class="avatar flex-shrink-0">
                                    <img src="../assets/img/icons/unicons/wallet-info.png" alt="wallet info"
                                        class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-1">Ventas</p>
                            <h4 class="card-title mb-3">${{ number_format($totalVentas, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Revenue -->
        <div class="col-12 col-xxl-8 order-2 order-md-3 order-xxl-2 mb-6 total-revenue">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-lg-8">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">Total Revenue</h5>
                            </div>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="totalRevenue" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalRevenue">
                                    <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                    <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                    <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                </div>
                            </div>
                        </div>
                        <div id="totalRevenueChart" class="px-3"></div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card-body px-xl-9 py-12 d-flex align-items-center flex-column">
                            <div class="text-center mb-6">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary">
                                        <script>
                                            document.write(new Date().getFullYear() - 1);
                                        </script>
                                    </button>
                                    <button type="button"
                                        class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="javascript:void(0);">2021</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);">2020</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);">2019</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div id="growthChart"></div>
                            <div class="text-center fw-medium my-6">62% Company Growth</div>

                            <div class="d-flex gap-11 justify-content-between">
                                <div class="d-flex">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded-2 bg-label-primary"><i
                                                class="icon-base bx bx-dollar icon-lg text-primary"></i></span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <small>
                                            <script>
                                                document.write(new Date().getFullYear() - 1);
                                            </script>
                                        </small>
                                        <h6 class="mb-0">$32.5k</h6>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded-2 bg-label-info"><i
                                                class="icon-base bx bx-wallet icon-lg text-info"></i></span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <small>
                                            <script>
                                                document.write(new Date().getFullYear() - 2);
                                            </script>
                                        </small>
                                        <h6 class="mb-0">$41.2k</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Total Revenue -->
        <div class="col-12 col-md-8 col-lg-12 col-xxl-4 order-3 order-md-2 profile-report">
            <div class="row">
                <div class="col-6 mb-6 payments">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                <div class="avatar flex-shrink-0">
                                    <img src="../assets/img/icons/unicons/paypal.png" alt="paypal" class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-1">Cheques bancarios</p>
                            <h4 class="card-title mb-3">${{ $totalPagosBancariosCheque }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-6 transactions">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                <div class="avatar flex-shrink-0">
                                    <img src="../assets/img/piggy-bank.png" alt="Credit Card"
                                        class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-base bx bx-dots-vertical-rounded text-body-secondary"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-1">Transacciones</p>
                            <h4 class="card-title mb-3">${{$totalTransaccionesCuentasBancarias}}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-6 profile-report">
                    <div class="card h-100">
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between align-items-center flex-sm-row flex-column gap-10 flex-wrap">
                                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                    <div class="card-title mb-6">
                                        <h5 class="text-nowrap mb-1">Profile Report</h5>
                                        <span class="badge bg-label-warning">YEAR 2022</span>
                                    </div>
                                    <div class="mt-sm-auto">
                                        <span class="text-success text-nowrap fw-medium"><i
                                                class="icon-base bx bx-up-arrow-alt"></i> 68.2%</span>
                                        <h4 class="mb-0">$84,686k</h4>
                                    </div>
                                </div>
                                <div id="profileReportChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    'use strict';
    const cardColor = '#fff';
    const borderColor = '#e0e0e0';
    const legendColor = '#6e6b7b';
    const labelColor = '#6e6b7b';
    const fontFamily = 'Public Sans, sans-serif';
    const config = {
        colors: {
            primary: '#696cff'
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        const totalRevenueChartEl = document.querySelector('#totalRevenueChart');

        async function cargarDatosVentas() {
            try {
                const res = await fetch('/ventas-por-mes');
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                const datos = await res.json();

                const currentYear = new Date().getFullYear();

                const totalRevenueChartOptions = {
                    series: [{
                        name: currentYear - 1,
                        data: datos
                    }],
                    chart: {
                        height: 300,
                        stacked: true,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '30%',
                            borderRadius: 8,
                            startingShape: 'rounded',
                            endingShape: 'rounded',
                            borderRadiusApplication: 'around'
                        }
                    },
                    colors: [config.colors.primary],
                    dataLabels: {
                        enabled: false
                    },
                    // ❌ Esta sección eliminada porque causa el error en tipo 'bar'
                    // stroke: {
                    //   curve: 'smooth',
                    //   width: 6,
                    //   lineCap: 'round',
                    //   colors: [cardColor]
                    // },
                    legend: {
                        show: true,
                        horizontalAlign: 'left',
                        position: 'top',
                        markers: {
                            size: 4,
                            radius: 12,
                            shape: 'circle',
                            strokeWidth: 0
                        },
                        fontSize: '13px',
                        fontFamily: fontFamily,
                        fontWeight: 400,
                        labels: {
                            colors: legendColor,
                            useSeriesColors: false
                        },
                        itemMargin: {
                            horizontal: 10
                        }
                    },
                    grid: {
                        strokeDashArray: 7,
                        borderColor: borderColor,
                        padding: {
                            top: 0,
                            bottom: -8,
                            left: 20,
                            right: 20
                        }
                    },
                    fill: {
                        opacity: [1]
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                            'Oct', 'Nov', 'Dec'
                        ],
                        labels: {
                            style: {
                                fontSize: '13px',
                                fontFamily: fontFamily,
                                colors: labelColor
                            }
                        },
                        axisTicks: {
                            show: false
                        },
                        axisBorder: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '13px',
                                fontFamily: fontFamily,
                                colors: labelColor
                            }
                        }
                    },
                    responsive: [],
                    states: {
                        hover: {
                            filter: {
                                type: 'none'
                            }
                        },
                        active: {
                            filter: {
                                type: 'none'
                            }
                        }
                    }
                };

                if (typeof totalRevenueChartEl !== 'undefined' && totalRevenueChartEl !== null) {
                    const totalRevenueChart = new ApexCharts(totalRevenueChartEl, totalRevenueChartOptions);
                    totalRevenueChart.render();
                }

            } catch (error) {
                console.error('Error al cargar datos de ventas:', error);
            }
        }

        cargarDatosVentas();
    });
</script>
