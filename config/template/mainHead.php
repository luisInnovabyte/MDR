<?php
// En tu archivo mainHead.php (que se incluye en todas las páginas)
session_start();
?>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


<!-- Twitter -->
<!-- <meta name="twitter:site" content="@Routeline">
<meta name="twitter:creator" content="@Routeline"> -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="MDR ERP Manager">
<meta name="twitter:description" content="Sistema de gestión integral para empresas de alquiler de equipos audiovisuales - Optimiza tu negocio con MDR">
<meta name="twitter:image" content="https://mdr.ra98.es/config/template/Logo.png">

<!-- Facebook -->
<meta property="og:url" content="https://mdr.ra98.es/view/Home/">
<meta property="og:title" content="MDR ERP Manager">
<meta property="og:description" content="Sistema de gestión integral para empresas de alquiler de equipos audiovisuales - Control completo de inventario, clientes y facturación.">
<meta property="og:type" content="website">
<meta property="og:image" content="https://mdr.ra98.es/config/template/Logo.png">
<meta property="og:image:secure_url" content="https://mdr.ra98.es/config/template/Logo.png">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="600">

<!-- Meta -->
<meta name="theme-color" content="#2d3a4b">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<meta name="description" content="Sistema de gestión integral para empresas de alquiler de equipos audiovisuales - Control completo de inventario, clientes y facturación.">
<meta name="keywords" content="alquiler equipos audiovisuales, gestión inventario, ERP, facturación, control equipos, MDR, gestión empresarial">
<meta name="author" content="MDR ERP Manager">
<meta name="robots" content="no index, no follow">


<title>MDR ERP MANAGER</title>

<!-- vendor css -->
<!-- <link href="../../public/lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet"> -->
<link href="../../public/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
<link href="../../public/lib/rickshaw/rickshaw.min.css" rel="stylesheet">

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- <link href="../../public/lib/select2/css/select2.min.css" rel="stylesheet"> -->


<link href="../../public/lib/timepicker/jquery.timepicker.css" rel="stylesheet">
<link href="../../public/lib/spectrum-colorpicker/spectrum.css" rel="stylesheet">
<link href="../../public/lib/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
<link href="../../public/lib/ion-rangeslider/css/ion.rangeSlider.css" rel="stylesheet">
<link href="../../public/lib/ion-rangeslider/css/ion.rangeSlider.skinFlat.css">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">



<!-- Estos son los IMPORTADOS DE FORUM -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet"
    integrity="sha384-DJhypeLg79qWALC844KORuTtaJcH45J+36wNgzj4d1Kv1vt2PtRuV2eVmdkVmf/U" crossorigin="anonymous">
<!-- para los iconos  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<!-- para los iconos de bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- DataTables Responsive CSS -->
<link href="https://cdn.datatables.net/v/dt/dt-2.2.2/r-3.0.4/datatables.min.css" rel="stylesheet" integrity="sha384-gmZ5jterufNKMboaPX/+AZbRRbDF+z379XQUHv6DYWg2o8yTKtN/tMySXHexqf8y" crossorigin="anonymous">

<!-- DEL MODAL -->
<link href="../../public/lib/highlightjs/styles/github.css" rel="stylesheet">


<!-- https://codeseven.github.io/toastr/demo.html -->
<link rel="stylesheet" href="../../public/css/toastr.css">

<!-- Bracket CSS -->
<link rel="stylesheet" href="../../public/css/bracket.css">

<!-- FILTROS CSS -->
<link rel="stylesheet" href="../../public/css/filtros.css">


<style>
    /* Para centrar la tabla */
    div.dt-container {
        margin: 0 auto;
        width: 90%;
    }

    /* Para destacar aquellos valores que en la tabla de productos sean mayor de 300 (por ejemplo) */
    .highlight {
        color: lightgreen;
        background-color: black;
    }

    /* distintos usos - ocultar */
    .d-none {
        display: none;
    }



    /* Solo afecta al icono de copy*/
    .btn-copy::before {
        content: "\f0c5";
        /* Código del icono de Excel en FontAwesome */
        font-family: "Font Awesome 5 Free";
        /* Asegúrate de que FontAwesome esté cargado */
        font-weight: 900;
        /* Peso del icono */
        margin-right: 5px;
        /* Espacio entre el icono y el texto */
        color: rgb(46, 52, 58)
    }

    /* Solo afecta al icono de excel*/
    .btn-excel::before {
        content: "\f1c3";
        /* Código del icono de Excel en FontAwesome */
        font-family: "Font Awesome 5 Free";
        /* Asegúrate de que FontAwesome esté cargado */
        font-weight: 900;
        /* Peso del icono */
        margin-right: 5px;
        /* Espacio entre el icono y el texto */
        color: rgb(46, 52, 58)
    }

    /* Solo afecta al icono de pdf*/
    .btn-pdf::before {
        content: "\f1c1";
        /* Código del icono de Excel en FontAwesome */
        font-family: "Font Awesome 5 Free";
        /* Asegúrate de que FontAwesome esté cargado */
        font-weight: 900;
        /* Peso del icono */
        margin-right: 5px;
        /* Espacio entre el icono y el texto */
        color: rgb(46, 52, 58)
    }

    /* Solo afecta al icono de imprimir*/
    .btn-imprimir::before {
        content: "\f02f";
        /* Código del icono de Excel en FontAwesome */
        font-family: "Font Awesome 5 Free";
        /* Asegúrate de que FontAwesome esté cargado */
        font-weight: 900;
        /* Peso del icono */
        margin-right: 5px;
        /* Espacio entre el icono y el texto */
        color: rgb(46, 52, 58)
    }

    /* Solo afecta al icono de visibilidad*/
    .btn-visibility::before {
        content: "\f06e";
        /* Código del icono de Excel en FontAwesome */
        font-family: "Font Awesome 5 Free";
        /* Asegúrate de que FontAwesome esté cargado */
        font-weight: 900;
        /* Peso del icono */
        margin-right: 5px;
        /* Espacio entre el icono y el texto */
        color: whitesmoke;
        /* Color de fondo */
    }

    /* Botones para mostrar más */
    td.details-control {
        background: url('../../public/assets/images/details_open.png') no-repeat center center;
        cursor: pointer;
    }

    tr.shown td.details-control {
        background: url('../../public/assets/images/details_close.png') no-repeat center center;
    }

    /* CLASE PARA LA VALIDACION DE LOS CAMPOS */
    .small-invalid-feedback {
        font-size: 0.75rem;
        margin-top: 0.2rem;
        line-height: 1;
        color: #dc3545 !important;
    }

    .small-valid-feedback {
        font-size: 0.75rem;
        margin-top: 0.2rem;
        line-height: 1;
        color: rgb(112, 112, 112) !important;
    }

    /* Para poner los radio button en linea - aplicarselo a cada radio */
    .rdiobox {
        display: inline-block;
        margin-right: 15px;
    }

    /* *********************** */

    /* Por que a veces los inputs se desbordan en los modales.*/
    #modalMantenimiento .modal-dialog {
        overflow: hidden;
    }


    #modalMantenimiento input,
    #modalMantenimiento select,
    #modalMantenimiento textarea {
        /* width: 100%; */
        max-width: 100%;
        box-sizing: border-box;
    }


    /* Mensajes de error pequeños */
    /* Validación de campos */
    .small-invalid-feedback {
        font-size: 0.75rem;
        margin-top: 0.2rem;
        line-height: 1;
        color: #dc3545 !important;
    }

    /* Select de paises */
    #paises_sel_id {
        width: 200px;
    }

    /*  Para en el caso de agrupación diseñar cabecera */
    .group-header {
        background-color: #85daff !important;
        color: #203843 !important;
        font-weight: bold !important;
        padding: 8px !important;
    }


    .hightlight {
        color: lightgreen !important;
        background-color: black !important;
    }
</style>