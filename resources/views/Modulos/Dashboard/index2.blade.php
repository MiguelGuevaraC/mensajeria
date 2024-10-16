﻿

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?php
    $vista = 'Dashboard';
    $categoriaActual = 'Reporte';
    $OpcionActual = 'dashboard';
    
    ?>

    <title>Mensajería | {{ $vista }}</title>
    <link type="image/png" href="plantillaNuevo\img\logo.png" rel="icon">


    <!--STYLESHEET-->
    <!--=================================================-->

    <!--Open Sans Font [ OPTIONAL ]-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>


    <!--Bootstrap Stylesheet [ REQUIRED ]-->
    <link href="plantillaNuevo\css\bootstrap.min.css" rel="stylesheet">


    <!--Nifty Stylesheet [ REQUIRED ]-->
    <link href="plantillaNuevo\css\nifty.min.css" rel="stylesheet">


    <!--Nifty Premium Icon [ DEMONSTRATION ]-->
    <link href="plantillaNuevo\css\demo\nifty-demo-icons.min.css" rel="stylesheet">


    <!--=================================================-->
    <!--Custom scheme [ OPTIONAL ]-->









    <!--=================================================-->




    <link rel="stylesheet" href="/mensajeria/Cdn-Locales/pkgAwsome/css/all.css" />

    <!--Demo [ DEMONSTRATION ]-->



    <link href="plantillaNuevo\css\themes\type-c\theme-navy.min.css" rel="stylesheet">

    <!--Unite Gallery [ OPTIONAL ]-->

    <link rel="stylesheet" href="{{ asset('css/appPlantilla.css') }}">
    <style>
        #page-content1 {
            padding: 20px 50px;
        }

        .card-body {
            background: #f9f9f9;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 128, 0, 0.2);
            border: 1px solid #c1e2c1;

        }

        .card-body h4,
        .card-body p {
            color: : #004190;
        }

        .card-header {
            background: #ffffff0d;
            border-radius: 20px
        }

        .card {
            background: #1000460d;
            border-radius: 20px
        }

        .card-text {
            padding: 4px;
        }

        /* Estilo base para la navbar */
        .navbar-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        /* Asegúrate de que los elementos dentro de .nav se alineen correctamente */
        .navbar-top-links {
            display: flex;
            align-items: center;
        }

        /* Ajuste para dispositivos móviles */
        @media (max-width: 300px) {
            .navbar-top-links {
                flex-direction: column;
                width: 100%;
                margin: 0;
            }

            .navbar-top-links .dropdown {
                width: 100%;
            }


        }

        /* Estilo para el botón de menú de navegación en dispositivos móviles */
        .tgl-menu-btn {
            display: block;
        }

        .mainnav-toggle {
            display: flex;
            align-items: center;
            padding: 10px;
        }

        /* Estilo para el icono del usuario y perfil */
        .ic-user {
            display: flex;
            align-items: center;
        }

        /* Ajustes para asegurar que el menú desplegable se alinee correctamente */
        .dropdown-menu-right {
            right: 0;
        }
    </style>

</head>

<!--TIPS-->
<!--You may remove all ID or Class names which contain "demo-", they are only used for demonstration. -->

<body>
    <div id="container" class="effect aside-float aside-bright slide mainnav-out navbar-fixed">

        <!--NAVBAR-->
        <!--===================================================-->
        <header id="navbar">
            <div id="navbar-container" class="boxed">

                <!--Brand logo & name-->
                <!--================================-->
                <div class="navbar-header">
                    <a href='vistaInicio' class="navbar-brand">
                        {{-- <img src="plantillaNuevo\img\logo.png" alt="Nifty Logo" class="brand-icon"> --}}
                        <div class="brand-title">
                            <span class="brand-text">Mensajería</span>
                        </div>
                    </a>
                </div>
                <!--================================-->
                <!--End brand logo & name-->


                <!--Navbar Dropdown-->
                <!--================================-->
                <div class="navbar-content">
                    <ul class="nav navbar-top-links">

                        <!--Navigation toogle button-->
                        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                        <li class="tgl-menu-btn">
                            <a class="mainnav-toggle" href="#">
                                <i class="demo-pli-list-view"></i>

                            </a>
                        </li>


                    </ul>
                    <ul class="nav navbar-top-links">
                        <?php foreach ($groupMenu as $item): ?>

                        @foreach ($item['option_menus'] as $menu)
                            <li id="dropdown-<?php echo $item['id']; ?>" class="dropdown">
                                <a href="{{ $menu['route'] }}" data-tooltip="{{ $menu['name'] }}"
                                    class="btn-profesional dropdown-toggle text-right">
                                    <span class="ic-user pull-right">
                                        <i style="font-size:21px" class="{{ $menu['icon'] }}"></i>
                                    </span>
                                </a>
                            </li>
                        @endforeach

                        <?php endforeach; ?>

                        <li id="dropdown-user" class="dropdown">
                            <a href="#" data-tooltip="Perfil" data-toggle="dropdown"
                                class="btn-profesional dropdown-toggle text-right">
                                <span class="ic-user pull-right">
                                    <i style="font-size:21px" class="fa-solid fa-user"></i>
                                </span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right panel-default">
                                <ul class="head-list">
                                    <li>
                                        <a href="perfilD"><i class="demo-pli-male icon-lg icon-fw"></i> Perfil</a>
                                    </li>
                                    <li>
                                        <a href="logout"><i class="demo-pli-unlock icon-lg icon-fw"></i> Salir</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>



                </div>

                <!--================================-->
                <!--End Navbar Dropdown-->

            </div>
        </header>
        <!--===================================================-->
        <!--END NAVBAR-->

        <div class="boxed">

            <!--CONTENT CONTAINER-->
            <!--===================================================-->
            <div id="content-container">
                <!-- Page Head -->
                <div id="page-head" class="mb-4">


                    <ol class="breadcrumb">
                        <li><a href="vistaInicio"><i class="demo-pli-home"></i></a></li>
                        <li><a href="{{ $OpcionActual }}">{{ $categoriaActual }}</a></li>
                    </ol>
                </div>

                <!-- Page Content -->
                <div id="page-content1">


                    <!-- Widgets -->
                    <div class="row text-center ">
                        <!-- Total de Mensajes Enviados -->

                        <!-- Mensajes Pendientes -->
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p style="text-align: left" class="card-title"><b>Filtro Fechas</b></p>

                                    <input type="date" id="fechaInicio" class="form-control mb-3">

                                    <input type="date" id="fechaFin" class="form-control mb-3">
                                    <button id="filtrar" style="color:white;background: #7dc671"
                                        class="btn w-100">Filtrar</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="icon text-primary mb-3">
                                        <i class="fa fa-envelope fa-3x" style="color:#004190"></i>
                                    </div>
                                    <h4 class="card-title">Mensajes Enviados</h4>
                                    <p class="card-text display-4" id="totalEnviados">0</p>
                                </div>
                            </div>
                        </div>

                        <!-- Mensajes Fallidos -->
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="icon mb-3">
                                        <i class="fa fa-dollar fa-3x" style="color: #004190"></i>
                                    </div>
                                    <h4 class="card-title">Costo Unitario</h4>
                                    <p class="card-text display-4" id="costoUnitario">0</p>
                                </div>
                            </div>
                        </div>

                        <!-- Costo Total -->
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="icon text-success mb-3">

                                        <i class="fa-solid fa-hand-holding-dollar fa-3x" style="color: #004190"></i>
                                    </div>
                                    <h4 class="card-title">Costo Total</h4>
                                    <p class="card-text display-4" id="costoTotal">$0.00</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos del Dashboard -->
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-header" style="background:#ffffff0d;">
                                    <h4 class="card-title">Mensajes Enviados a lo Largo del Tiempo</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="mensajesEnviadosChart" style="height: 400px; width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-header" style="background:#ffffff0d;">
                                    <h4 class="card-title">Análisis de Costos</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="analisisCostosChart" style="height: 400px; width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla del Dashboard -->
                    {{-- <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body"> --}}
                    {{-- <h4 class="card-title text-center">Detalles de Mensajes de WhatsApp</h4> --}}
                    {{-- <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Fecha</th>
                                                    <th>Contenido del Mensaje</th>
                                                    <th>Estado</th>
                                                    <th>Costo</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detallesMensajes">
                                                <!-- Aquí se rellenarán los datos según el filtro de fechas -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>

            <!-- Include Bootstrap CSS -->
            <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

            <!-- Include Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



            {{-- <script src="{{ asset('js/JqueryInicio/JqueryDashboard.js') }}"></script> --}}


       



            <!--===================================================-->
            <!--END CONTENT CONTAINER-->






            <!--MAIN NAVIGATION-->
            <!--===================================================-->
            <nav id="mainnav-container">
                <div id="mainnav">

                    <!--Menu-->
                    <!--================================-->
                    <div id="mainnav-menu-wrap">
                        <div class="nano">
                            <div class="nano-content">

                                <!--Profile Widget-->



                                <!--Shortcut buttons-->
                                <!--================================-->

                                <!--================================-->
                                <!--End shortcut buttons-->
                                <div id="mainnav-profile" class="mainnav-profile">
                                    <div class="profile-wrap text-center">
                                        <div class="pad-btm">
                                            <img class="img-circle img-md"
                                                src="plantillaNuevo\img\profile-photos\1.png" alt="Profile Picture">
                                        </div>
                                        <a href="#profile-nav" class="box-block" data-toggle="collapse"
                                            aria-expanded="false">
                                            <span class="pull-right dropdown-toggle">
                                                <i class="dropdown-caret"></i>
                                            </span>
                                            <p class="mnp-name">{{ strtoupper($user->username) . " | ".strtoupper($user->company->businessName) }}</p>

                                            <span class="mnp-desc"></span>
                                        </a>
                                    </div>
                                    
                                </div>

                                <ul id="mainnav-menu" class="list-group">
                                    <?php foreach ($groupMenuLeft as $categoria): ?>
                                    <?php if (!empty($categoria['option_menus']) && count($categoria['option_menus']) > 0): ?>
                                    <li class="<?= $categoria['nombre'] == $categoriaActual ? 'active-sub' : '' ?>">
                                        <a href="#">
                                            <i class="<?= $categoria['icon'] ?>"></i>
                                            <span class="menu-title"><?= strtoupper($categoria['name']) ?></span>
                                            <i class="arrow"></i>
                                        </a>
                                        <ul class="<?= $categoria['name'] == $categoriaActual ? 'collapse in' : '' ?>">
                                            <?php foreach ($categoria['option_menus'] as $item): ?>
                                            <li class="<?= $item['route'] == $OpcionActual ? 'active-link' : '' ?>">
                                                <a class="optionsMenu" href="<?= $item['route'] ?>">
                                                    <i class="<?= $item['icon'] ?>"></i> <?= $item['name'] ?>
                                                </a>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>






                            </div>
                        </div>
                    </div>
                    <!--================================-->
                    <!--End menu-->

                </div>
            </nav>
            <!--===================================================-->
            <!--END MAIN NAVIGATION-->

        </div>



        <!-- FOOTER -->
        <!--===================================================-->
        <footer id="footer">

            <!-- Visible when footer positions are fixed -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="show-fixed pad-rgt pull-right">
                You have <a href="#" class="text-main"><span class="badge badge-danger">3</span> pending
                    action.</a>
            </div>



            <!-- Visible when footer positions are static -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->




            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <!-- Remove the class "show-fixed" and "hide-fixed" to make the content always appears. -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

            <p class="pad-lft">&#0169; 2024 Garzasoft</p>



        </footer>
        <!--===================================================-->
        <!-- END FOOTER -->


        <!-- SCROLL PAGE BUTTON -->
        <!--===================================================-->
        <button class="scroll-top btn">
            <i class="pci-chevron chevron-up"></i>
        </button>
        <!--===================================================-->
    </div>
    <!--===================================================-->
    <!-- END OF CONTAINER -->





    <!--JAVASCRIPT-->
    <!--=================================================-->

    <!--jQuery [ REQUIRED ]-->
    <script src="plantillaNuevo\js\jquery.min.js"></script>


    <!--BootstrapJS [ RECOMMENDED ]-->
    <script src="plantillaNuevo\js\bootstrap.min.js"></script>


    <!--NiftyJS [ RECOMMENDED ]-->
    <script src="plantillaNuevo\js\nifty.min.js"></script>


    <!-- PARA MODAL DE ALERTAS SWEETALERT2-->
    <script src="/mensajeria/Cdn-Locales/pkgSweetAlert/dist/sweetalert2.all.js"></script>

    <!--=================================================-->

    <!--Demo script [ DEMONSTRATION ]-->
    <script src="plantillaNuevo\js\demo\nifty-demo.min.js"></script>


    <!--Unite Gallery [ OPTIONAL ]-->
    <script src="plantillaNuevo\plugins\unitegallery\js\unitegallery.min.js"></script>
    <script src="plantillaNuevo\plugins\unitegallery\themes\tiles\ug-theme-tiles.js"></script>



    <!--Custom script [ DEMONSTRATION ]-->
    <!--===================================================-->

    <script src="{{ asset('js/app.js') }}"></script>

    <script src="/mensajeria/Cdn-Locales/pkgAwsome/js/all.js"></script>


    <script>
        $(document).ready(function() {
            var mensajesEnviadosChart, analisisCostosChart;

            function initializeDates() {
                var currentDate = new Date();
                var firstDayOfYear = new Date(currentDate.getFullYear(), 0, 1);
                var formattedFirstDayOfYear = firstDayOfYear.toISOString().split("T")[0];
                var formattedCurrentDate = currentDate.toISOString().split("T")[0];

                $("#fechaInicio").val(formattedFirstDayOfYear);
                $("#fechaFin").val(formattedCurrentDate);

                fetchDataAndUpdateCharts(formattedFirstDayOfYear, formattedCurrentDate);
            }

            initializeDates();

            $("#filtrar").click(function() {
                var fechaInicioInput = $("#fechaInicio").val();
                var fechaFinInput = $("#fechaFin").val();

                var startDate = fechaInicioInput ? new Date(fechaInicioInput).toISOString().split("T")[0] :
                    new Date().toISOString().split("T")[0];
                var endDate = fechaFinInput ? new Date(fechaFinInput).toISOString().split("T")[0] :
                    new Date().toISOString().split("T")[0];

                fetchDataAndUpdateCharts(startDate, endDate);
            });

            function fetchDataAndUpdateCharts(startDate, endDate) {
                $.ajax({
                    url: "dataDashboard",
                    method: "GET",
                    data: {
                        fechaStart: startDate,
                        fechaEnd: endDate,
                    },
                    success: function(response) {
                        updateCharts(response, startDate, endDate);
                    },
                });
            }

            function updateCharts(data, startDate, endDate) {
                var mensajesData = {};
                var costosData = {};

                function getMonthLabel(date) {
                    var monthNames = [
                        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
                    ];
                    var month = new Date(date).getMonth();
                    var year = new Date(date).getFullYear();
                    return monthNames[month] + " " + year;
                }

                function getMonthYearKey(date) {
                    var month = new Date(date).getMonth() + 1;
                    var year = new Date(date).getFullYear();
                    return year + '-' + (month < 10 ? '0' : '') + month; // Formato YYYY-MM
                }

                function addToMonthData(date, value, dataMap) {
                    var monthYearKey = getMonthYearKey(date);
                    var monthLabel = getMonthLabel(date);
                    if (!dataMap[monthYearKey]) {
                        dataMap[monthYearKey] = {
                            label: monthLabel,
                            value: 0
                        };
                    }
                    dataMap[monthYearKey].value += value;
                }

                // Filtrar y agregar datos al mes correspondiente
                Object.keys(data.mensajesPorFecha).forEach((date) => {
                    if (date >= startDate && date <= endDate) {
                        addToMonthData(date, data.mensajesPorFecha[date], mensajesData);
                    }
                });

                Object.keys(data.costosPorFecha).forEach((date) => {
                    if (date >= startDate && date <= endDate) {
                        addToMonthData(date, data.costosPorFecha[date], costosData);
                    }
                });

                // Ordenar las etiquetas por fecha real para mantener el orden cronológico
                var sortedLabels = Object.keys(mensajesData).sort(function(a, b) {
                    return new Date(a + '-01') - new Date(b + '-01');
                });

                var sortedMensajesData = sortedLabels.map((key) => mensajesData[key].value);
                var sortedCostosData = sortedLabels.map((key) => costosData[key].value);
                var sortedLabelTexts = sortedLabels.map((key) => mensajesData[key].label);

                // Destruir gráficos anteriores si existen
                if (mensajesEnviadosChart) {
                    mensajesEnviadosChart.destroy();
                }

                if (analisisCostosChart) {
                    analisisCostosChart.destroy();
                }

                // Crear gráfico de mensajes enviados
                var ctx1 = document.getElementById("mensajesEnviadosChart").getContext("2d");
                mensajesEnviadosChart = new Chart(ctx1, {
                    type: "line",
                    data: {
                        labels: sortedLabelTexts,
                        datasets: [{
                            label: "Mensajes Enviados",
                            data: sortedMensajesData,
                            backgroundColor: "rgba(54, 162, 235, 0.6)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 2,
                            fill: true,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: "rgba(200, 200, 200, 0.3)",
                                },
                            },
                            x: {
                                grid: {
                                    color: "rgba(200, 200, 200, 0.3)",
                                },
                            },
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: "black",
                                },
                            },
                        },
                    },
                });

                // Crear gráfico de costos
                var ctx2 = document.getElementById("analisisCostosChart").getContext("2d");
                analisisCostosChart = new Chart(ctx2, {
                    type: "bar",
                    data: {
                        labels: sortedLabelTexts,
                        datasets: [{
                            label: "Costo (S/)",
                            data: sortedCostosData,
                            backgroundColor: "rgba(75, 192, 192, 0.6)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 1,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: "rgba(200, 200, 200, 0.3)",
                                },
                            },
                            x: {
                                grid: {
                                    color: "rgba(200, 200, 200, 0.3)",
                                },
                            },
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: "black",
                                },
                            },
                        },
                    },
                });

                // Actualizar valores de total de mensajes y costo total
                $("#costoUnitario").text("S/ " + data.costoUnitario);
                $("#totalEnviados").text(data.totalMensajes);
                $("#costoTotal").text("S/ " + data.costoTotal.toFixed(2));
            }
        });
    </script>

</body>

</html>
