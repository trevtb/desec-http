<?php
session_start();

function __autoload($classfile) {
    if ($classfile != "" && is_file("../classes/".$classfile.".php")) {
        require "../classes/".$classfile.".php";
    } //endif
} //endfunction autoload

if (isset($_GET['setcamwidgetrefmode'])) {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif
    if ($_GET['setcamwidgetrefmode'] != 'live' && $_GET['setcamwidgetrefmode'] != 'poll') {
        echo "FEHLER: Ungültiger Wert.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    $camhelper->setCamWidgetRefreshMode($_SESSION['user_id'], $_GET['setcamwidgetrefmode']);

    echo "1";
} else if (isset($_GET['setcamwidgetcamid']) && $_GET['setcamwidgetcamid'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif
    if (!is_numeric($_GET['setcamwidgetcamid'])) {
        echo "FEHLER: Ungültiger Wert.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    if ($camhelper->camExists($_POST['id'])) {
        $camhelper->setCamWidgetCamId($_SESSION['user_id'], $_POST['id']);
        echo "1";
    } else {
        echo "0";
    } //endif
} else if (isset($_GET['setwidgetstatus']) && $_GET['setwidgetstatus'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_POST['name'])) {
        echo "FEHLER: Keinen Namen übergeben.";
        exit();
    } //endif

    if (!isset($_POST['status']) || !is_numeric($_POST['status']) || intval($_POST['status']) < 0 || intval($_POST['status']) > 1) {
        echo "FEHLER: Ungültigen Statuswert übergeben.";
        exit();
    } //endif

    $status = intval($_POST['status']);
    $name = '';

    switch($_POST['name']) {
        case 'users':
            $name = 'users';
            break;
        case 'cameras':
            $name = 'cameras';
            break;
    } //endswitch

    if ($name != '') {
        $widgethelper = new WidgetHelper();
        $widgethelper->setWidgetActive($name, $_SESSION['user_id'], $status);
        echo "1";
    } else {
        echo "0";
    } //endif
} //endif

?>