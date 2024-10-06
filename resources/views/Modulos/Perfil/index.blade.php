<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?php
    $vista = 'Home';
    $categoriaActual = 'home';
    $OpcionActual = 'vistaInicio';
    
    ?>

    <title>Mensajería | {{ $vista }}</title>
    <link type="image/png" href="plantillaNuevo\img\logo.png" rel="icon">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    

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
    <link href="plantillaNuevo\css\demo\nifty-demo.min.css" rel="stylesheet">


    <link href="plantillaNuevo\css\themes\type-c\theme-navy.min.css" rel="stylesheet">

    <!--Unite Gallery [ OPTIONAL ]-->
    <link href="plantillaNuevo\plugins\unitegallery\css\unitegallery.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('css/appPlantilla.css') }}">
    <!--=================================================

    REQUIRED
    You must include this in your project.


    RECOMMENDED
    This category must be included but you may modify which plugins or components which should be included in your project.


    OPTIONAL
    Optional plugins. You may choose whether to include it in your project or not.


    DEMONSTRATION
    This is to be removed, used for demonstration purposes only. This category must not be included in your project.


    SAMPLE
    Some script samples which explain how to initialize plugins or components. This category should not be included in your project.


    Detailed information and more samples can be found in the document.

    =================================================-->

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
                <div id="page-head">

                    <!--Page Title-->
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <div class="container pt-4 bg-light rounded shadow-sm mt-3">
                        <div class="row text-center">
                            <!-- Usuario -->
                            <div class="col-12 col-md-4 mb-3">
                                <i class="fas fa-user-circle fa-3x text-primary mb-2"></i>
                                <h5 id="userNameUp" class="text-uppercase font-weight-bold">{{ Auth::user()->username }}</h5>
                                <p class="text-muted">Usuario</p>
                            </div>

                            <!-- Empresa -->
                            <div class="col-12 col-md-4 mb-3">
                                <i class="fas fa-building fa-3x text-success mb-2"></i>
                                <h5 class="text-uppercase font-weight-bold">{{ Auth::user()->company->businessName }}
                                </h5>
                                <p class="text-muted">Empresa</p>
                            </div>

                            <!-- Tipo de Usuario -->
                            <div class="col-12 col-md-4 mb-3">
                                <i class="fas fa-user-tag fa-3x text-info mb-2"></i>
                                <h5  class="text-uppercase font-weight-bold">{{ Auth::user()->typeUser->name }}</h5>
                                <p class="text-muted">Tipo de Usuario</p>
                            </div>
                        </div>
                    </div>




                    <!--Breadcrumb-->
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <ol class="breadcrumb">
                        <li><a href='vistaInicio'><i class="demo-pli-home"></i></a></li>
                        <li><a href="{{ $OpcionActual }}">{{ $categoriaActual }}</a></li>

                    </ol>
                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    <!--End breadcrumb-->

                </div>


                <div id="page-content">

                    <div class="row demo-nifty-panel">


                        <div class="col-lg-12">


                            <!--Panel with Tabs-->
                            <!--===================================================-->
                            <div class="panel panel-primary">

                                <!--Panel heading-->
                                <div class="panel-heading">
                                    <div class="panel-control">

                                        <!--Nav tabs-->
                                        <ul class="nav nav-tabs">
                                            <li><a data-toggle="tab" href="#demo-tabs-box-1">Actualizar Contraseña</a>
                                            </li>
                                            {{-- <li class="active"><a data-toggle="tab" href="#demo-tabs-box-2">Datos</a>
                                            </li> --}}
                                        </ul>

                                    </div>
                                </div>

                                <!--Panel body-->
                                <div class="panel-body">

                                    <!--Tabs content-->
                                    <div class="tab-content">
                                        <div id="demo-tabs-box-1" style="text-align: center"
                                            class="tab-pane fade in active">
                                            <form method="PUT" style="text-align: center" id="contraseñaNueva">
                                                @method('PUT')
                                                @csrf
                                                <br>
                                                <div class="mb-3 row">
                                                    <label for="username"
                                                        class="col-sm-5 col-form-label"><strong>Usuario</strong></label>
                                                    <div class="col-sm-6">
                                                        <div class="input-group mar-btm">
                                                            <input type="text" id="username" name="username"
                                                                class="form-control" placeholder="Usuario"
                                                                value="{{ Auth::user()->username }}" required>
                                                            <span class="input-group-addon" id="usuario"
                                                                style="cursor:pointer;">
                                                                <i class="fa-solid fa-user"></i>
                                                            </span>
                                                        </div>
                                                        <div class="error-messageE"></div>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="passOld"
                                                        class="col-sm-5 col-form-label"><strong>Contraseña
                                                            Anterior</strong></label>
                                                    <div class="col-sm-6">
                                                        <div class="input-group mar-btm">
                                                            <input type="password" id="passOld"
                                                                name="passOld" class="form-control"
                                                                placeholder="Contraseña Antigua" required>
                                                            <span class="input-group-addon"
                                                                id="mostrar-contrasenaAnterior"
                                                                style="cursor:pointer;">
                                                                <i class="fa-solid fa-eye"></i>
                                                            </span>
                                                        </div>
                                                        <div class="error-messageE"></div>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="passNew"
                                                        class="col-sm-5 col-form-label"><strong>Nueva
                                                            Contraseña</strong></label>
                                                    <div class="col-sm-6">
                                                        <div class="input-group mar-btm">
                                                            <input type="password" id="passNew"
                                                                name="passNew" class="form-control"
                                                                placeholder="Contraseña Nueva" required>
                                                            <span class="input-group-addon" id="mostrar-contrasena"
                                                                style="cursor:pointer;">
                                                                <i class="fa-solid fa-eye"></i>
                                                            </span>
                                                        </div>
                                                        <div class="error-messageE"></div>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label for="passConf"
                                                        class="col-sm-5 col-form-label"><strong>Confirmar
                                                            Contraseña</strong></label>
                                                    <div class="col-sm-6">
                                                        <div class="input-group mar-btm">
                                                            <input type="password" id="passConf"
                                                                name="passConf" class="form-control"
                                                                placeholder="Confirmar Contraseña" required>
                                                            <span class="input-group-addon"
                                                                id="mostrar-contrasenaConfirm"
                                                                style="cursor:pointer;">
                                                                <i class="fa-solid fa-eye"></i>
                                                            </span>
                                                        </div>
                                                        <div class="error-messageE"></div>
                                                    </div>
                                                </div>

                                                <input type="hidden" id="idPass">
                                                <br>
                                                <div class="">
                                                    <button class="btn btnPerfil btn-success" type="submit">Guardar
                                                        Cambios</button>
                                                </div>
                                            </form>

                                            <!-- jQuery Script -->

                                            <!-- jQuery Script -->




                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>

                    </div>


                </div>

            </div>
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
                                            <p class="mnp-name">
                                                {{ strtoupper($user->username) . ' | ' . strtoupper($user->company->businessName) }}
                                            </p>

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

   <!-- SweetAlert JS -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

    <!--Custom script [ DEMONSTRATION ]-->
    <!--===================================================-->

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/JqueryInicio/JqueryPerfil.js') }}"></script>
    <script src="/mensajeria/Cdn-Locales/pkgAwsome/js/all.js"></script>


    <script>
        $(document).on('nifty.ready', function() {


            $("#demo-gallery").unitegallery({
                tiles_type: "nested"
            });


            $("#demo-gallery-2").unitegallery({
                tiles_type: "nested",
                tiles_nested_optimal_tile_width: 150
            });


        });
    </script>
 
    


</body>

</html>
