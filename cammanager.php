<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

include_once("loginhandler.php");
$page = 'cammanager';

$dbcon = PDOConnection::getPdoConnection();
$camhelper = new CamHelper();
$creating = $camhelper->isCreatingCams();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DESEC core | Kameraverwaltung</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Bootstrap validator -->
    <link href="/plugins/bootstrap-validator/bootstrapValidator.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome icons -->
    <link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="/plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- DATA TABLES -->
    <link href="/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE skin -->
    <link href="/dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/theme.css" rel="stylesheet" type="text/css" />
    <!-- Datatables custom css -->
    <link href="/dist/css/datatables_custom.css" rel="stylesheet" type="text/css" />
    <!-- Page CSS -->
    <link href="/dist/css/pages/cammanager.css" rel="stylesheet" type="text/css" />

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
                <i class="fa fa-video-camera"></i> Kameraverwaltung
            </h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-video-camera"></i> Home</a></li>
                <li class="active">Kameraverwaltung</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#devices_tab">Ger&auml;te</a></li>
                            <li><a data-toggle="tab" href="#groups_tab">Gruppen</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="devices_tab" class="tab-pane active">
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <a href="javascript:addCam();" class="btn btn-primary pull-left"><i class="fa fa-plus-circle"></i>&nbsp;Kamera hinzuf&uuml;gen</a>
                                        <span class="pull-right" id="span_creating" style="font-size: 16px;<?php echo ($creating == 0) ? ' display: none;' : '';?>">
                                            <i class="fa fa-spinner fa-spin" style="color: #3c8dbc;"></i> Neue Konfiguration wird gerade erstellt, bitte warten.
                                        </span>
                                        <span class="pull-right" id="span_created" style="font-size: 16px;<?php echo ($creating == 1) ? ' display: none;' : '';?>">
                                            <i class="fa fa-check" style="color: #00a65a;"></i> Die Konfiguration ist auf dem aktuellen Stand.
                                        </span>
                                    </div>
                                </div>
                                <table id="cameratable" class="table table-bordered table-striped table-hover dt-responsive">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Typ</th>
                                        <th>Host</th>
                                        <th>Port</th>
                                        <th>Pfad</th>
                                        <th>SSL</th>
                                        <th>HTTP Auth</th>
                                        <th>Aktionen</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                            <div id="groups_tab" class="tab-pane">
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <a href="javascript:addGroup();" class="btn btn-primary pull-left"><i class="fa fa-plus-circle"></i>&nbsp;Gruppe hinzuf&uuml;gen</a>
                                    </div>
                                </div>
                                <table id="grouptable" class="table table-bordered table-striped table-hover dt-responsive">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Ger&auml;te</th>
                                        <th>Aktionen</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
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

    <div class="modal modal-primary fade" id="addcam-modal" tabindex="-1" role="dialog" aria-labelledby="addcam-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="addcam-modal-label"><i class="fa fa-video-camera"></i>&nbsp;&nbsp;Kamera hinzuf&uuml;gen</h4>
                </div>
                <form role="form" type="post" id="addcam-form" action="">
                    <div class="modal-body" style="color: #000000 !important; background-color: #ffffff !important;">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                <h4>Verbindungseinstellungen</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                <div class="row form-group">
                                    <label for="caname" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Name:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="caname" class="form-control" id="caname" value="" placeholder="Name für die Kamera" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label for="cahost" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Host:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="cahost" class="form-control" id="cahost" value="" placeholder="Hostname oder IP-Adresse" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label for="caport" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Port:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="caport" class="form-control" id="caport" value="" placeholder="TCP/IP Port" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label for="capath" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Pfad:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="capath" class="form-control" id="capath" value="" placeholder="Pfad zum Stream" />
                                    </div>
                                </div>
                                <div class="row form-group" id="caresolutiongroup">
                                    <label for="caresolution" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Aufl&ouml;sung:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <select name="caresolution" id="caresolution" class="form-control" style="width: 90%;">
                                            <option value="320x240">320x240</option>
                                            <option value="640x480" selected>640x480</option>
                                            <option value="1280x720">1280x720</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group" id="catypegroup">
                                    <label for="catype" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Typ:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <select name="catype" id="catype" class="form-control" style="width: 90%;">
                                            <option value="desec" selected>DESEC C-UNIT</option>
                                            <option value="ip">IP-Kamera</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9 checkbox" style="text-align: left;">
                                        <label id="cassllab">
                                            <input type="checkbox" name="cassl[]" value="1" id="cassl" style="vertical-align: middle;"required />&nbsp;&nbsp;<span style="vertical-align: middle;">Sichere Verbindung (SSL)</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9 checkbox" style="text-align: left;">
                                        <label id="caauthlab">
                                            <input type="checkbox" name="caauth[]" value="1" id="caauth" style="vertical-align: middle;"required />&nbsp;&nbsp;<span style="vertical-align: middle;">HTTP Authentifizierung</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label for="causer" id="causerlabel" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Benutzername:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="causer" class="form-control" id="causer" value="" placeholder="Benutzernamen eingeben" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label for="capassword" id="capasswordlabel" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Passwort:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="password" name="capassword" class="form-control" id="capassword" value="" autocomplete="off" placeholder="Passwort eingeben" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                <h4>Gruppeneinstellungen</h4>
                            </div>
                        </div>
                        <div class="row form-group" id="cagroupselectgroup">
                            <label for="caaddgroups" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Zu Gruppe(n) hinzuf&uuml;gen:</label>
                            <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                <div style="border: 1px solid #d2d6de; width: 90%; padding: 5px; max-height: 250px; overflow-y: auto; overflow-x: hidden;" id="cagroupsdiv"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                        <input type="submit" id="ca-btn" name="ca-btn" class="btn btn-outline" value="Hinzuf&uuml;gen" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-primary fade" id="changecam-modal" tabindex="-1" role="dialog" aria-labelledby="changecam-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="changecam-modal-label"><i class="fa fa-edit"></i>&nbsp;&nbsp;Kameraeinstellungen bearbeiten</h4>
                </div>
                <form role="form" type="post" id="changecam-form" action="">
                    <input type="hidden" id="ccid" name="ccid" value="" />
                    <div class="modal-body" style="color: #000000 !important; background-color: #ffffff !important;">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                <h4>Verbindungseinstellungen</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                <div class="row form-group">
                                    <label for="ccname" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Name:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="ccname" class="form-control" id="ccname" value="" placeholder="Name für die Kamera" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label for="cchost" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Host:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="cchost" class="form-control" id="cchost" value="" placeholder="Hostname oder IP-Adresse" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label for="ccport" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Port:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="ccport" class="form-control" id="ccport" value="" placeholder="TCP/IP Port" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label for="ccpath" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Pfad:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="ccpath" class="form-control" id="ccpath" value="" placeholder="Pfad zum Stream" />
                                    </div>
                                </div>
                                <div class="row form-group" id="ccresolutiongroup">
                                    <label for="ccresolution" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Aufl&ouml;sung:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <select name="ccresolution" id="ccresolution" class="form-control" style="width: 90%;">
                                            <option value="320x240">320x240</option>
                                            <option value="640x480" selected>640x480</option>
                                            <option value="1280x720">1280x720</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group" id="catypegroup">
                                    <label for="cctype" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Typ:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <select name="cctype" id="cctype" class="form-control" style="width: 90%;">
                                            <option value="desec" selected>DESEC C-UNIT</option>
                                            <option value="ip">IP-Kamera</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9 checkbox" style="text-align: left;">
                                        <label id="ccssllab">
                                            <input type="checkbox" name="ccssl[]" value="1" id="ccssl" style="vertical-align: middle;"required />&nbsp;&nbsp;<span style="vertical-align: middle;">Sichere Verbindung (SSL)</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9 checkbox" style="text-align: left;">
                                        <label id="ccauthlab">
                                            <input type="checkbox" name="ccauth[]" value="1" id="ccauth" style="vertical-align: middle;"required />&nbsp;&nbsp;<span style="vertical-align: middle;">HTTP Authentifizierung</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label for="causer" id="ccuserlabel" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Benutzername:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="ccuser" class="form-control" id="ccuser" value="" placeholder="Benutzernamen eingeben" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label for="ccpassword" id="ccpasswordlabel" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Passwort:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="password" name="ccpassword" class="form-control" id="ccpassword" value="" autocomplete="off" placeholder="Passwort eingeben" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                <h4>Gruppeneinstellungen</h4>
                            </div>
                        </div>
                        <div class="row form-group" id="ccgroupselectgroup">
                            <label for="ccaddgroups" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Zu Gruppe(n) hinzuf&uuml;gen:</label>
                            <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                <div style="border: 1px solid #d2d6de; width: 90%; padding: 5px; max-height: 250px; overflow-y: auto; overflow-x: hidden;" id="ccgroupsdiv"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                        <input type="submit" id="cc-btn" name="cc-btn" class="btn btn-outline" value="Speichern" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="deletecam-modal" tabindex="-1" role="dialog" aria-labelledby="deletecam-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="deletecam-modal-label"><i class="fa fa-remove"></i>&nbsp;&nbsp;Kamera entfernen</h4>
                </div>
                <form role="form" action="post" id="deletecam-form" action="">
                    <input type="hidden" id="cdid" name="cdid" value="" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                <p>M&ouml;chten Sie die Kamera wirklich entfernen?</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                        <button type="button" id="cd-btn" name="cd-btn" class="btn btn-outline">L&ouml;schen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-primary fade" id="addgroup-modal" tabindex="-1" role="dialog" aria-labelledby="addgroup-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="addgroup-modal-label"><i class="fa fa-sitemap"></i>&nbsp;&nbsp;Gruppe hinzuf&uuml;gen</h4>
                </div>
                <form role="form" type="post" id="addgroup-form" action="">
                    <div class="modal-body" style="color: #000000 !important; background-color: #ffffff !important;">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                <div class="row form-group">
                                    <label for="graddname" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Name:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="graddname" class="form-control" id="graddname" value="" placeholder="Name der Gruppe" />
                                    </div>
                                </div>
                                <div class="row form-group" id="graddcamsgroup">
                                    <label for="graddcams" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Kameras:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <div style="border: 1px solid #d2d6de; width: 90%; padding: 5px; max-height: 250px; overflow-y: auto; overflow-x: hidden;" id="graddcamsdiv"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                        <input type="submit" id="gradd-btn" name="gradd-btn" class="btn btn-outline" value="Hinzuf&uuml;gen" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="deletegroup-modal" tabindex="-1" role="dialog" aria-labelledby="deletegroup-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="deletegroup-modal-label"><i class="fa fa-remove"></i>&nbsp;&nbsp;Gruppe l&ouml;schen</h4>
                </div>
                <form role="form" action="post" id="deletegroup-form" action="">
                    <input type="hidden" id="grdelid" name="grdelid" value="" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                <p>M&ouml;chten Sie die Gruppe wirklich entfernen?<br />
                                <strong>Hinweis:</strong> Die der Gruppe zugeh&ouml;rigen Ger&auml;te werden nicht gel&ouml;scht.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                        <button type="button" id="grdel-btn" name="grdel-btn" class="btn btn-outline">L&ouml;schen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-primary fade" id="changegroup-modal" tabindex="-1" role="dialog" aria-labelledby="changegroup-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="changegroup-modal-label"><i class="fa fa-edit"></i>&nbsp;&nbsp;Gruppe bearbeiten</h4>
                </div>
                <form role="form" type="post" id="changegroup-form" action="">
                    <input type="hidden" id="grchangeid" name="grchangeid" value="" />
                    <div class="modal-body" style="color: #000000 !important; background-color: #ffffff !important;">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                <div class="row form-group">
                                    <label for="grchangename" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Name:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <input style="width: 90%;" type="text" name="grchangename" class="form-control" id="grchangename" value="" placeholder="Name der Gruppe" />
                                    </div>
                                </div>
                                <div class="row form-group" id="grchangecamsgroup">
                                    <label for="grchangecams" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Kameras:</label>
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                        <div style="border: 1px solid #d2d6de; width: 90%; padding: 5px; max-height: 250px; overflow-y: auto; overflow-x: hidden;" id="grchangecamsdiv"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                        <input type="submit" id="grchange-btn" name="grchange-btn" class="btn btn-outline" value="Speichern" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery 2.1.4 -->
<script src="/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- Bootstrap Validator -->
<script src="/plugins/bootstrap-validator/bootstrapValidator.min.js"></script>
<!-- jQuery Data Tables + Data Tables Responsive + Datatables Bootstrap -->
<script src="/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
<script src="/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<!-- SlimScroll -->
<script src="/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- FastClick -->
<script src='/plugins/fastclick/fastclick.min.js' type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="/dist/js/app.min.js" type="text/javascript"></script>

<!-- Sidebar JS -->
<script src="/dist/js/sidebar.js" type="text/javascript"></script>
<!-- Page JS, includes polling!!! -->
<script src="/dist/js/pages/cammanager.js" type="text/javascript"></script>
</body>
</html>
