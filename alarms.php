<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

include_once("loginhandler.php");
$page = 'alarmoverview';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DESEC core | Dashboard</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="/plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skin -->
    <link href="/dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/theme.css" rel="stylesheet" type="text/css" />
    <!-- Page style -->
    <link href="/dist/css/pages/alarms.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/plugins/html5shiv/html5shiv.min.js"></script>
    <script src="/plugins/respond/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-blue sidebar-mini">
<div class="wrapper">
    <?php
    include_once("header.php");
    include_once("main_sidebar.php");
    ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                <i class="fa fa-bell"></i> Alarm&uuml;bersicht
            </h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Alarm&uuml;bersicht</li>
            </ol>
        </section>

        <section class="content">
            <p>Hello World!</p>
        </section>
    </div>
    <?php
    include_once("footer.php");
    include_once("control_sidebar.php");
    ?>
</div>

<!-- REQUIRED JS SCRIPTS -->
<!-- jQuery 2.1.4 -->
<script src="/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- Bootstrap dialog -->
<script src="/plugins/bootstrap-dialog/bootstrap-dialog.min.js"></script>
<!-- AdminLTE App -->
<script src="/dist/js/app.min.js" type="text/javascript"></script>
<!-- SlimScroll -->
<script src="/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- FastClick -->
<script src='/plugins/fastclick/fastclick.min.js'></script>

<!-- Sidebar JS -->
<script src="/dist/js/sidebar.js" type="text/javascript"></script>
<!-- Page JS. Includes polling!! -->
<script src="/dist/js/pages/alarms.js" type="text/javascript"></script>
</body>
</html>