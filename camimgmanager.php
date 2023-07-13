<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

include_once("loginhandler.php");
$page = 'camimgmanager';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DESEC core | Bilderverwaltung</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome icons -->
    <link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="/plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE skin -->
    <link href="/dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/theme.css" rel="stylesheet" type="text/css" />
    <!-- Page CSS -->
    <link href="/dist/css/pages/camimgmanager.css" rel="stylesheet" type="text/css" />
    <!-- Date range picker -->
    <link href="/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />

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
                <i class="fa fa-file-image-o"></i> Bilderverwaltung
            </h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-file-image-o"></i> Home</a></li>
                <li class="active">Bilderverwaltung</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; max-width: 200px; float: left; margin-right: 20px; margin-top: 5px; border-radius: 3px;">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                        <span></span> <b class="caret"></b>
                    </div>
                    <div style="float: left;">
                        <a href="javascript: selectAll();" class="btn btn-default" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc; margin-top: 5px; margin-right: 5px;">Alle ausw&auml;hlen</a>
                    </div>
                    <div class="btn-group" style="float: left;">
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc; margin-top: 5px; margin-right: 20px;">
                            Markierte Elemente
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="javascript: downloadFolders();"><i class="fa fa-download"></i> Herunterladen</a></li>
                            <li><a href="javascript: showDelFolders();"><i class="fa fa-trash"></i> L&ouml;schen</a></li>
                        </ul>
                    </div>
                    <div class="pagingcont" style="float: left;" id="toppaging"></div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 text-right">
                    <div style="display: inline-block; margin-top: 5px;">
                        <span>Sortierung:</span>
                        <div class="input-group-btn" style="display: inline;">
                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="sortselect">Absteigend <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="javascript: setSort('asc');">Aufsteigend</a></li>
                                <li><a href="javascript: setSort('desc');">Absteigend</a></li>
                            </ul>
                        </div>
                    </div>
                    <div style="display: inline-block; margin-top: 5px;">
                        <span>Ergebnisse pro Seite:</span>
                        <div class="input-group-btn" style="display: inline;">
                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="countselect">25 <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="javascript: setPageLimit('25');">25</a></li>
                                <li><a href="javascript: setPageLimit('50');">50</a></li>
                                <li><a href="javascript: setPageLimit('100');">100</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" style="padding-bottom: 20px;">
                <hr style="border-color: #d2d6de; border-image: none; border-style: solid none none; border-width: 1px 0 0;" />
            </div>

            <div class="row media-manager" id="folderlist"></div>

            <div class="row pagingcont" style="margin-top: 20px;" id="bottompaging"></div>
        </section>
    </div>

    <div class="modal modal-danger fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="delete-modal-label" aria-hidden="true">
        <div class="modal-dialog" id="deletedialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="delete-modal-label"></h4>
                </div>
                <div class="modal-body">
                    <p class="modal-text"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                    <a href="" class="btn btn-outline" id="modal-del-button">L&ouml;schen</a>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>


    <?php
    include_once("footer.php");
    include_once("control_sidebar.php");
    ?>
</div>

<!-- jQuery 2.1.4 -->
<script src="/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- SlimScroll -->
<script src="/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- FastClick -->
<script src='/plugins/fastclick/fastclick.min.js' type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="/dist/js/app.min.js" type="text/javascript"></script>
<!-- Date range picker -->
<script src="/plugins/daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- Sidebar JS -->
<script src="/dist/js/sidebar.js" type="text/javascript"></script>
<!-- Online status polling -->
<script src="/dist/js/poll-online.js" type="text/javascript"></script>
<!-- Page JS -->
<script src="/dist/js/pages/camimgmanager.js" type="text/javascript"></script>
</body>
</html>
