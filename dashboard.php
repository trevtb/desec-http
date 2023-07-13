<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

include_once("loginhandler.php");
$page = 'dashboard';
$pdocon = PDOConnection::getPdoConnection();

$widgethelper = new WidgetHelper();

if ($widgethelper->getWidgetActive('cameras', $_SESSION['user_id']) == '1') {
    $camwidget = true;
} else {
    $camwidget = false;
} //endif

if ($widgethelper->getWidgetActive('users', $_SESSION['user_id']) == '1') {
    $userwidget = true;
} else {
    $userwidget = false;
} //endif
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
    <link href="/dist/css/pages/dashboard.css" rel="stylesheet" type="text/css" />
    <!-- Bootstrap dialog -->
    <link href="/plugins/bootstrap-dialog/bootstrap-dialog.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/plugins/html5shiv/html5shiv.min.js"></script>
    <script src="/plugins/respond/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-blue sidebar-mini">

<script type="text/javascript">
    window.userwidget = '<?php echo ($userwidget) ? '1':'0'; ?>';
    window.camwidget = '<?php echo ($camwidget) ? '1':'0'; ?>';
</script>

<div class="wrapper">
    <?php
    include_once("header.php");
    include_once("main_sidebar.php");
    ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                <i class="fa fa-dashboard"></i> Dashboard
            </h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
        </section>

        <section class="content">
            <div class="row" id="widgetarea">

                <div class="col-md-3" id="userwidget" style="<?php echo ($userwidget) ? '': 'display: none;';?>">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-users"></i>&nbsp;Benutzer online</h3>
                            <div class="box-tools pull-right">
                                <a href="javascript:reloadWidget('user');" title="Widget neu laden" class="btn btn-box-tool"><i class="fa fa-refresh" id="userwidget_reficon"></i></a>
                            </div>
                        </div>
                        <div class="box-body"></div>
                    </div>
                </div>

                <div class="col-md-5" id="camerawidget" style="<?php echo ($camwidget) ? '': 'display: none;';?>">
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs pull-right" id="camwidget_tabs">
                            <li class=""><a href="javascript:reloadWidget('cam');" title="Widget neu laden"><i class="fa fa-refresh" id="camerawidget_reficon"></i></a></li>
                            <li id="camwidget_settingstab" class=""><a data-toggle="tab" href="#camwidget_settings" aria-expanded="false">Einstellungen</a></li>
                            <li id="camwidget_monitortab" class=""><a data-toggle="tab" href="#camwidget_monitor" aria-expanded="true">Monitor</a></li>
                            <li class="pull-left header">
                                <i class="fa fa-video-camera"></i>
                                Kamera&uuml;bersicht
                            </li>
                        </ul>
                        <div class="tab-content no-padding" id="camerawidget_body">
                        </div>
                    </div>
                </div>

            </div>
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
<script src="/dist/js/pages/dashboard.js" type="text/javascript"></script>
</body>
</html>