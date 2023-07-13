<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: /home");
    exit();
} //endif

if (isset($_GET['fail']) && $_GET['fail'] == '1') {
    $fail = true;
} else {
    $fail = false;
} //endif

if (isset($_SESSION['login_tleft']) && is_numeric($_SESSION['login_tleft'])) {
    $bf = intval($_SESSION['login_tleft']);
} else {
    $bf = '';
} //endif

$bfprot = new BFProtect();

$_SESSION['pagelink'] = '/';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <title>DESEC core :: login</title>

    <!-- Bootstrap core CSS -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="/plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- DATA TABLES -->
    <link href="/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skin -->
    <link href="/dist/css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/plugins/html5shiv/html5shiv.min.js"></script>
    <script src="/plugins/respond/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-page">
    <div class="container-fluid">
    <?php if ($fail && $bf == ''):?>
        <div class="row">
            <p class="bg-danger" style="height: auto; padding-left: 10px; padding-top: 7px; padding-bottom: 7px; width: 100%; color: #d33724;">
                <i class="fa fa-warning"></i>&nbsp;Der eingegebene Benutzername oder das eingegebene Passwort waren falsch.
            </p>
        </div>
    <?php
    endif;
    ?>
    <?php if ($fail && $bf != ''):?>
        <?php
        $mins = intval($bf / 60);
        ?>
        <div class="row" >
            <p class="bg-danger" style="height: auto; padding-left: 10px; padding-top: 7px; padding-bottom: 7px; color: #d33724;">
                <i class="fa fa-warning"></i>&nbsp;Auf grund zu vieler falscher Loginversuche wurde die Loginfunktion f&uuml;r <?php echo intval($bfprot->getTimeLimit() / 60); ?> Minuten deaktiviert.
                <?php
                if ($mins > 1) {
                    echo "Es verbleiben noch ".$mins." Minuten.";
                } else if ($mins == 1) {
                    echo "Es verbleibt noch 1 Minute.";
                } else {
                    echo "Es verleibt noch weniger als eine Minute.";
                } //endif
                ?>
            </p>
        </div>
        <?php
        endif;
        ?>
        <div class="login-box">
            <div class="login-logo">
                <span class="logo-lg"><img src="/dist/img/logo.png" /> <b>DESEC</b> CORE</span>
            </div>
            <div class="login-box-body">
                <p class="login-box-msg">Einloggen um zu beginnen.</p>
                <form id="loginform" role="form" action="/login-start" method="post">
                    <input type="hidden" name="login" value="login">
                    <div class="form-group has-feedback">
                        <input id="login-username" type="text" class="form-control" name="accountname" value="" placeholder="Benutzername">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input id="login-password" type="password" class="form-control" name="password" placeholder="Passwort">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-xs-offset-4">
                            <input type="submit" id="btn-login" class="btn btn-primary btn-block btn-flat" value="Einloggen">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- REQUIRED JS SCRIPTS -->
    <!-- jQuery 2.1.4 -->
    <script src="/plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/plugins/jquery-sha1/jquery.sha1.js" type="text/javascript"></script>
    <script src="/dist/js/pages/login.js" type="text/javascript"></script>
</body>
</html>