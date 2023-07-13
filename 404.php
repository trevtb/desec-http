<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

session_start();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <title>404, Seite nicht gefunden</title>

    <!-- Bootstrap core CSS -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/dist/css/pages/404.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/plugins/html5shiv/html5shiv.min.js"></script>
    <script src="/plugins/respond/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="error-template">
                    <h1>
                        Ups!</h1>
                    <h2>
                        404 Nicht gefunden</h2>
                    <div class="error-details">
                        Tut uns leid, aber die angeforderte Seite wurde nicht gefunden!
                    </div>
                    <div class="error-actions">
                        <a href="/" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-home"></span>
                            Zur Startseite </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- REQUIRED JS SCRIPTS -->
    <!-- jQuery 2.1.4 -->
    <script src="/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>