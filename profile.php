<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

include_once("loginhandler.php");
$page = 'profile';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DESEC core | Accounteinstellungen</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Bootstrap validator -->
    <link href="/plugins/bootstrap-validator/bootstrapValidator.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="/plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE style -->
    <link href="/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skin -->
    <link href="/dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />

    <!-- Theme css -->
    <link href="/dist/css/theme.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/plugins/html5shiv/html5shiv.min.js"></script>
    <script src="/plugins/respond/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-blue sidebar-mini">
    <script type="text/javascript">
        window.user_accountname = '<?php echo $_SESSION['user_accountname']; ?>';
        window.user_email = '<?php echo $_SESSION['user_email']; ?>';
    </script>

    <div class="wrapper">
        <?php
        include_once("header.php");
        include_once("main_sidebar.php");
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <?php if (isset($_GET['toggle']) && $_GET['toggle'] == 'datasuccess'): ?>
                <p class="bg-success" style="height: auto; padding-left: 10px; padding-top: 7px; padding-bottom: 7px; color: #00a65a;">
                    <i class="fa fa-check"></i>&nbsp;Die Aktualisierung der Benutzerdaten war erfolgreich.
                </p>
            <?php elseif (isset($_GET['toggle']) && $_GET['toggle'] == 'passwordsuccess'): ?>
                <p class="bg-success" style="height: auto; padding-left: 10px; padding-top: 7px; padding-bottom: 7px; color: #00a65a;">
                    <i class="fa fa-check"></i>&nbsp;Die &Auml;nderung des Passwortes war erfolgreich.
                </p>
            <?php endif; ?>
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <i class="fa fa-user"></i> Accounteinstellungen
                </h1>
                <ol class="breadcrumb">
                    <li><a href="/home"><i class="fa fa-user"></i> Home</a></li>
                    <li class="active">Accounteinstellungen</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Pers&ouml;nliche Daten</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" id="userDataForm" name="userDataForm" data-toggle="validator" method="post" action="/profile-data-update">
                                <input type="hidden" id="profile-data-update" name="profile-data-update" value="1" />
                                <input type="hidden" id="dataform_id" name="dataform_id" value="<?php echo $_SESSION['user_id']; ?>" />
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="accountname" class="control-label">Accountname</label>
                                        <input type="text" class="form-control" id="accountname" name="accountname"
                                               placeholder="Accountnamen eingeben" value="<?php echo $_SESSION['user_accountname']; ?>"<?php echo ($_SESSION['user_admin'] == '0') ? ' readonly="readonly"' : ''; ?> required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name" class="control-label">Vorname</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Vornamen eingeben" value="<?php echo $_SESSION['user_name']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="surname" class="control-label">Nachname</label>
                                        <input type="text" class="form-control" id="surname" name="surname" placeholder="Nachnamen eingeben" value="<?php echo $_SESSION['user_surname']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="control-label">E-Mail Adresse</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Mailadresse eingeben" value="<?php echo $_SESSION['user_email']; ?>" required>
                                    </div>
                                </div><!-- /.box-body -->

                                <div class="box-footer">
                                    <input type="submit" class="btn btn-primary" value="Speichern" />
                                </div>
                            </form>
                        </div><!-- /.box -->
                    </div><!--/.col (left) -->

                    <div class="col-md-6">
                        <div class="box box-success">
                            <div class="box-header">
                                <h3 class="box-title">Passwort &auml;ndern</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" id="userPasswordForm" name="userPasswordForm" data-toggle="validator" method="post" action="/profile-password-update">
                                <input type="hidden" id="profile-password-update" name="profile-password-update" value="1" />
                                <input type="hidden" id="passform_id" name="passform_id" value="<?php echo $_SESSION['user_id']; ?>" />
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="password1" class="control-label">Passwort</label>
                                        <input type="password" class="form-control" id="password1" name="password1" autocomplete="off" placeholder="Passwort eingeben" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="password2" class="control-label">Wiederholen</label>
                                        <input type="password" class="form-control" id="password2" name="password2" autocomplete="off" placeholder="Passwort wiederholen" value="" />
                                    </div>
                                </div><!-- /.box-body -->

                                <div class="box-footer">
                                    <input type="submit" class="btn btn-primary" value="Speichern" />
                                </div>
                            </form>
                        </div><!-- /.box -->
                    </div><!--/.col (left) -->
                </div>   <!-- /.row -->
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->

        <?php
        include_once("footer.php");
        include_once("control_sidebar.php");
        ?>
    </div><!-- ./wrapper -->

    <!-- jQuery 2.1.4 -->
    <script src="/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='/plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="/dist/js/app.min.js" type="text/javascript"></script>
    <!-- Bootstrap Validator -->
    <script src="/plugins/bootstrap-validator/bootstrapValidator.min.js"></script>

    <!-- Sidebar JS -->
    <script src="/dist/js/sidebar.js" type="text/javascript"></script>
    <!-- Poll online: auto transmit online status to the server.-->
    <script src="/dist/js/poll-online.js" type="text/javascript"></script>
    <!-- Page JS -->
    <script src="/dist/js/pages/profile.js" type="text/javascript"></script>
  </body>
</html>
