<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

include_once("loginhandler.php");
$page = 'camimgmanager';

$basedir = Config::$FTPROOT;
if (mb_substr($basedir, -1) != '/') {
    $basedir .= '/';
} //endif

$folder = '';
if (!isset($_GET['name']) || $_GET['name'] == '') {
    echo "FEHLER: Kein Ordnername übergeben.";
    exit();
} else {
    $folder = $_GET['name'];
} //endif

if (strpos($folder, '..') !== false || mb_substr($folder, -1) == '/') {
    echo "FEHLER: Ungültigen Ordnernamen übergeben.";
    exit();
} //endif
if (!is_dir($basedir.$folder)) {
    echo "FEHLER: Das Verzeichnis existiert nicht.";
    exit();
} //endif

$prettystring = DateTime::createFromFormat('j-m-y', $folder);
$prettystring = $prettystring->format('d.m.Y');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DESEC core | Bilderverwaltung: <?php echo $prettystring; ?></title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome icons -->
    <link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="/plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Bootstrap Image Gallery -->
    <link href="/plugins/bootstrap-image-gallery/css/blueimp-gallery.min.css" rel="stylesheet" type="text/css" />
    <link href="/plugins/bootstrap-image-gallery/css/bootstrap-image-gallery.min.css" rel="stylesheet" type="text/css" />
    <!-- Bootstrap time Picker -->
    <link href="/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet"/>
    <!-- Theme style -->
    <link href="/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE skin -->
    <link href="/dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/theme.css" rel="stylesheet" type="text/css" />
    <!-- Page CSS -->
    <link href="/dist/css/pages/camimgmanager.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/plugins/html5shiv/html5shiv.min.js"></script>
    <script src="/plugins/respond/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-blue sidebar-mini">
    <div id="blueimp-gallery" class="blueimp-gallery" data-use-bootstrap-modal="false">
        <!-- The container for the modal slides -->
        <div class="slides"></div>
        <!-- Controls for the borderless lightbox -->
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
        <!-- The modal dialog, which will be used to wrap the lightbox content -->
        <div class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body next"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left prev">
                            <i class="glyphicon glyphicon-chevron-left"></i>
                            Previous
                        </button>
                        <button type="button" class="btn btn-primary next">
                            Next
                            <i class="glyphicon glyphicon-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="wrapper">
    <?php
    include_once("header.php");
    include_once("main_sidebar.php");
    ?>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                <i class="fa fa-file-image-o"></i> Bilderverwaltung - <?php echo $prettystring; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-file-image-o"></i> Home</a></li>
                <li><a href="/camimgmanager">Bilderverwaltung</a></li>
                <li class="active"><?php echo $prettystring; ?></li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 visible-xs visible-sm">
                    <div class="box box-primary collapsed-box" style="text-align: left;">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter</h3>
                            <div class="box-tools pull-right">
                                <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="display: none;">
                            <div><label class="fltr_label">Zeitraum</label></div>
                            <div class="btn-group" style="max-width: 95px;">
                                <div class="input-group bootstrap-timepicker timepicker">
                                    <input id="tpstart1" type="text" class="form-control tpstart fltr_withborder" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc;">
                                    <div class="input-group-addon fltr_withborder" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc;">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                </div>
                            </div>
                            <span style="margin-left: 5px; margin-right: 5px;">bis</span>
                            <div class="btn-group" style="max-width: 95px;">
                                <div class="input-group bootstrap-timepicker timepicker">
                                    <input id="tpend1" type="text" class="form-control tpend fltr_withborder" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc;">
                                    <div class="input-group-addon fltr_withborder" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc;">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="fltr_errormsg" style="display: none;">
                                <label style="margin-top: 10px; color: #dd4b39;">
                                    <i class="fa fa-times-circle-o"></i>
                                    Der End- liegt vor dem Startzeitpunkt.
                                </label>
                            </div>
                            <div class="clearfix"></div>
                            <div class="btn-group" style="margin-top: 7px; margin-right: 7px;">
                                <button class="btn btn-default fltr_reset_button">Zur&uuml;cksetzen</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                    <a href="/camimgmanager" class="btn btn-default" style="float:left; background: #fff; padding: 5px 10px; border: 1px solid #ccc; margin-right: 20px; margin-top: 5px;">
                        Zur&uuml;ck
                    </a>
                    <div style="float: left; margin-top: 5px;">
                        <a href="javascript: selectAll();" class="btn btn-default" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc; margin-right: 5px;">Alle ausw&auml;hlen</a>
                    </div>
                    <div class="btn-group" style="float: left; margin-top: 5px;">
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc; margin-right: 20px;">
                            Markierte Elemente
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="javascript: downloadImages();"><i class="fa fa-download"></i> Herunterladen</a></li>
                            <li><a href="javascript: showDelImages();"><i class="fa fa-trash"></i> L&ouml;schen</a></li>
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

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 media-manager" id="imagelist"></div>
                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 text-center hidden-xs hidden-sm" style="position: fixed; right: 0; padding-left: 60px; text-align: center;">
                    <div class="box box-primary" style="text-align: left;">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter</h3>
                            <div class="box-tools pull-right">
                                <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div style="padding-bottom: 5px;"><label class="fltr_label">Zeitraum</label></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-2 col-sm-2 col-md-3 col-lg-2 text-left">
                                    <div style="padding: 5px 10px;" class="fltr_label">Von:</div>
                                </div>
                                <div class="col-xs-10 col-sm-10 col-md-9 col-lg-10">
                                    <div class="input-group bootstrap-timepicker timepicker">
                                        <input id="tpstart2" type="text" class="form-control tpstart fltr_withborder" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc;">
                                        <div class="input-group-addon fltr_withborder" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc; border-left: none;">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-top: 5px;">
                                <div class="col-xs-2 col-sm-2 col-md-3 col-lg-2 text-left">
                                    <div style="padding: 5px 10px; text-align: left;" class="fltr_label">Bis:</div>
                                </div>
                                <div class="col-xs-10 col-sm-10 col-md-9 col-lg-10">
                                    <div class="input-group bootstrap-timepicker timepicker">
                                        <input id="tpend2" type="text" class="form-control tpend fltr_withborder" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc;">
                                        <div class="input-group-addon fltr_withborder" style="background: #fff; padding: 5px 10px; border: 1px solid #ccc; border-left: none;">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row fltr_errormsg" style="display: none;">
                                <div class="col-xs-offset-2 col-sm-offset-2 col-md-offset-3 col-lg-offset-2 col-xs-10 col-sm-10 col-md-9 col-lg-10 text-left">
                                    <label style="margin-top: 10px; color: #dd4b39;">
                                        <i class="fa fa-times-circle-o"></i>
                                        Der End- liegt vor dem Startzeitpunkt.
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                                    <div class="btn-group" style="margin-top: 7px; margin-right: 7px;">
                                        <button class="btn btn-default fltr_reset_button">Zurücksetzen</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row pagingcont" id="bottompaging"></div>
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
                    <a href="#" class="btn btn-outline" id="modal-del-button">L&ouml;schen</a>
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
<!-- moment.js -->
<script src="/plugins/daterangepicker/moment.min.js" type="text/javascript"></script>
<!-- Sidebar JS -->
<script src="/dist/js/sidebar.js" type="text/javascript"></script>
<!-- Online status polling -->
<script src="/dist/js/poll-online.js" type="text/javascript"></script>
<!-- Bootstrap Image Gallery -->
<script src='/plugins/bootstrap-image-gallery/js/jquery.blueimp-gallery.min.js' type="text/javascript"></script>
<script src='/plugins/bootstrap-image-gallery/js/bootstrap-image-gallery.min.js' type="text/javascript"></script>
<!-- bootstrap time picker -->
<script src="/plugins/timepicker/bootstrap-timepicker.min.js" type="text/javascript"></script>
<!-- Page JS -->
<script src="/dist/js/pages/imgmanagerdetail.js" type="text/javascript"></script>
</body>
</html>
