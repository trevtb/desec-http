<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

include_once("loginhandler.php");
$page = 'usermanager';

if (!isset($_SESSION['user_admin']) || $_SESSION['user_admin'] == '0') {
    echo "FEHLER: Geschützter Bereich.";
    exit();
} //endif
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DESEC core | Benutzerverwaltung</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Bootstrap Validator -->
    <link href="/plugins/bootstrap-validator/bootstrapValidator.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="/plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- DATA TABLES -->
    <link href="/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE -->
    <link href="/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skin -->
    <link href="/dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />

    <!-- General CSS for all pages -->
    <link href="/dist/css/theme.css" rel="stylesheet" type="text/css" />
    <!-- Datatables custom css -->
    <link href="/dist/css/datatables_custom.css" rel="stylesheet" type="text/css" />
    <!-- Page CSS -->
    <link href="/dist/css/pages/usermanager.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/plugins/html5shiv/html5shiv.min.js"></script>
    <script src="/plugins/respond/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-blue sidebar-mini">
    <script type="text/javascript">
        window.user_admin = '<?php echo $_SESSION['user_admin'];?>';
    </script>

    <div class="wrapper">
        <?php
        include_once("header.php");
        include_once("main_sidebar.php");
        ?>

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <i class="fa fa-users"></i> Benutzerverwaltung
                </h1>
                <ol class="breadcrumb">
                    <li><a href="/home"><i class="fa fa-users"></i> Home</a></li>
                    <li class="active">Benutzerverwaltung</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Benutzer&uuml;bersicht</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <table id="usertable" class="table table-bordered table-striped table-hover dt-responsive">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                            <div style="padding-bottom: 15px;">
                                                <a href="javascript:addUser();" class="btn btn-primary"><i class="fa fa-plus-circle"></i>&nbsp;Benutzer hinzufügen</a>
                                            </div>
                                        </div>
                                    </div>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Accountname</th>
                                            <th>E-Mail</th>
                                            <th>Vorname</th>
                                            <th>Nachname</th>
                                            <th>Status</th>
                                            <th>Zuletzt online</th>
                                            <th>Aktionen</th>
                                        </tr>
                                    </thead>
                                </table>
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

        <div class="modal modal-primary fade" id="adduser-modal" tabindex="-1" role="dialog" aria-labelledby="adduser-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="adduser-modal-label"><i class="fa fa-user"></i>&nbsp;&nbsp;Neuen Account erstellen</h4>
                    </div>
                    <form role="form" type="post" id="adduser-form" action="">
                        <div class="modal-body" style="color: #000000 !important; background-color: #ffffff !important;">
                            <div class="row">
                                <input type="hidden" id="caid" name="caid" value="" />
                                <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                    <div class="row form-group">
                                        <label for="caaccountname" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Accountname:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="text" name="caaccountname" class="form-control" id="caaccountname" value="" placeholder="Accountnamen eingeben" />
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="caemail" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">E-Mail:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="email" name="caemail" class="form-control" id="caemail" value="" placeholder="Mailadresse eingeben" />
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="caname" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Vorname:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="text" name="caname" class="form-control" id="caname" value="" placeholder="Vornamen eingeben" />
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="casurname" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Nachname:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="text" name="casurname" class="form-control" id="casurname" value="" placeholder="Nachnamen eingeben" />
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="capassword1" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Passwort:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="password" name="capassword1" class="form-control" id="capassword1" value="" placeholder="Passwort eingeben" />
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="capassword2" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Wiederholen:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="password" name="capassword2" class="form-control" id="capassword2" value="" placeholder="Passwort wiederholen" />
                                        </div>
                                    </div>
                                    <div class="row form-group" id="castatusgroup">
                                        <label for="castatus" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Status:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <select name="castatus" id="castatus" class="form-control" style="width: 90%;">
                                                <option value="0">Benutzer</option>
                                                <option value="1">Administrator</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                            <input type="submit" id="ca-btn" name="ca-btn" class="btn btn-outline" value="Erstellen" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal modal-primary fade" id="changeudata-modal" tabindex="-1" role="dialog" aria-labelledby="changeudata-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="changeudata-modal-label"><i class="fa fa-edit"></i>&nbsp;&nbsp;Accountdaten bearbeiten</h4>
                    </div>
                    <form role="form" type="post" id="cudata-form" action="">
                        <div class="modal-body" style="color: #000000 !important; background-color: #ffffff !important;">
                            <div class="row">
                                <input type="hidden" id="cuid" name="cuid" value="" />
                                <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                    <div class="row form-group">
                                        <label for="cuaccountname" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Accountname:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="text" name="cuaccountname" class="form-control" id="cuaccountname" value="" placeholder="Accountnamen eingeben" />
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="cuemail" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">E-Mail:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="email" name="cuemail" class="form-control" id="cuemail" value="" placeholder="Mailadresse eingeben" />
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="cuname" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Vorname:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="text" name="cuname" class="form-control" id="cuname" value="" placeholder="Vornamen eingeben" />
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="cusurname" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Nachname:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="text" name="cusurname" class="form-control" id="cusurname" value="" placeholder="Nachnamen eingeben" />
                                        </div>
                                    </div>
                                    <div class="row form-group" id="custatusgroup">
                                        <label for="custatus" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Status:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <select name="custatus" id="custatus" class="form-control" style="width: 90%;">
                                                <option value="0">Benutzer</option>
                                                <option value="1">Administrator</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                            <input type="submit" id="cudata-btn" name="cuda-btn" class="btn btn-outline" value="Speichern" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal modal-success fade" id="changeupwd-modal" tabindex="-1" role="dialog" aria-labelledby="changeupwd-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="changeupwd-modal-label"><i class="fa fa-lock"></i>&nbsp;&nbsp;Neues Passwort setzen</h4>
                    </div>
                    <form role="form" action="post" id="cupwd-form" action="">
                        <div class="modal-body" style="color: #000000 !important; background-color: #ffffff !important;">
                            <div class="row">
                                <input type="hidden" id="cpid" name="cpid" value="" />
                                <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                    <div class="row form-group">
                                        <label for="cppassword1" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Passwort:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="password" name="cppassword1" class="form-control" id="cppassword1" value="" placeholder="Neues Passwort eingeben" />
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <label for="cppassword2" class="control-label col-xs-12 col-sm-4 col-md-3 col-lg-3">Wiederholen:</label>
                                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9" style="text-align: left;">
                                            <input style="width: 90%;" type="password" name="cppassword2" class="form-control" id="cppassword2" value="" placeholder="Neues Passwort wiederholen" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                            <input id="cupwd-btn" type="submit" class="btn btn-outline" value="Speichern" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal modal-danger fade" id="deleteuser-modal" tabindex="-1" role="dialog" aria-labelledby="deleteuser-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="deleteuser-modal-label"><i class="fa fa-remove"></i>&nbsp;&nbsp;Account l&ouml;schen</h4>
                    </div>
                    <form role="form" action="post" id="deleteuser-form" action="">
                        <input type="hidden" id="duid" name="duid" value="" />
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12-col-lg-12">
                                    <p>M&ouml;chten Sie den Account wirklich unwiderruflich l&ouml;schen?</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Abbrechen</button>
                            <button type="button" id="du-btn" name="du-btn" class="btn btn-outline">L&ouml;schen</button>
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
    <script src='/plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="/dist/js/app.min.js" type="text/javascript"></script>

    <!-- Sidebar JS -->
    <script src="/dist/js/sidebar.js" type="text/javascript"></script>
    <!-- Poll online: auto transmit online status to the server.-->
    <script src="/dist/js/poll-online.js" type="text/javascript"></script>
    <!-- Page JS -->
    <script src="/dist/js/pages/usermanager.js" type="text/javascript"></script>
</body>
</html>
