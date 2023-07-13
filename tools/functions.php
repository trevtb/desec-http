<?php
session_start();

function __autoload($classfile) {
    if ($classfile != "" && is_file("../classes/".$classfile.".php")) {
        require "../classes/".$classfile.".php";
    } //endif
} //endfunction autoload

if (isset($_POST['login']) && isset($_POST['accountname']) && isset($_POST['password'])) {
    $bfprot = new BFProtect();
    if (isset($_SESSION['login_ftstamp'])) {
        if ($bfprot->getTimePassed() >= $bfprot->getTimeLimit()) {
            $bfprot->reset();
        } else {
            $ts = $bfprot->getTimeLimit() - $bfprot->getTimePassed();
            $_SESSION['login_tleft'] = $ts;
            header("Location: /");
        } //endif
    } //endif

    if (!isset($_SESSION['login_ftstamp']) || isset($_SESSION['login_ftstamp']) && $bfprot->getTimePassed() >= $bfprot->getTimeLimit()) {
        $pdocon = PDOConnection::getPdoConnection();
        $res = $pdocon->prepare("SELECT * FROM `user` WHERE `accountname`=? AND `password`=?;");
        $res->execute(array($_POST['accountname'], $_POST['password']));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (isset($ret[0]['id'])) {
            $bfprot->reset();
            $login = new LoginHandler();
            $login->doLogin($ret[0]);
            return;
        } //endif
    } //endif

    $bfprot->increment();
    if (isset($_SESSION['login_ftstamp'])) {
        if ($bfprot->getTimePassed() >= $bfprot->getTimeLimit()) {
            $bfprot->reset();
        } else {
            $ts = $bfprot->getTimeLimit() - $bfprot->getTimePassed();
            $_SESSION['login_tleft'] = $ts;
        } //endif
    } //endif
    header("Location: /login-fail");
} else if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /");
} else if (isset($_POST['profile-data-update']) &&
    isset($_POST['dataform_id']) && isset($_POST['name']) && isset($_POST['surname']) &&
    isset($_POST['accountname']) && isset($_POST['email'])) {

    if (!isset($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt." ;
        exit();
    } //endif

    if ($_SESSION['user_id'] != $_POST['dataform_id']) {
        echo "FEHLER: Das Update fremder Benutzer ist nicht erlaubt.";
        exit();
    } //endif

    $val = new Validator();
    $usrhelper = new UserHelper();
    $user = $usrhelper->getUserById($_SESSION['user_id']);

    if ($_SESSION['user_admin'] == '0' && $_POST['accountname'] != $user['accountname']) {
        echo "FEHLER: Sie dürfen Ihren Accountnamen nicht ändern.";
        exit();
    } //endif

    if (!$val->isValidName($_POST['name']) || !$val->isValidName($_POST['surname'])) {
        echo "FEHLER: Ungültiger Vor- und/oder Nachname.";
        exit();
    } //endif
    if (!$val->isValidUsername($_POST['accountname'])) {
        echo "FEHLER: Ungültiger Accountname.";
        exit();
    } //endif
    if (!$val->isValidMailAddress($_POST['email'])) {
        echo "FEHLER: Ungültige Mailadresse.";
        exit();
    } //endif
    if ($_POST['accountname'] != $_SESSION['user_accountname'] && $val->userExists($_POST['accountname'])) {
        echo "FEHLER: Der Accountname existiert bereits.";
        exit();
    } //endif
    if ($_POST['email'] != $_SESSION['user_email'] && $val->mailExists($_POST['email'])) {
        echo "FEHLER: Die Mailadresse existiert bereits.";
        exit();
    } //endif

    $_SESSION['user_accountname'] = $_POST['accountname'];
    $_SESSION['user_name'] = $_POST['name'];
    $_SESSION['user_surname'] = $_POST['surname'];
    $_SESSION['user_email'] = $_POST['email'];

    $usrhelper->changeUserData($_POST['dataform_id'], $_POST['accountname'], $_POST['email'], $_POST['name'], $_POST['surname'], $user['admin']);

    header("Location: /profile-data-update-success");
} else if (isset($_POST['profile-password-update']) &&
    isset($_POST['passform_id']) && isset($_POST['password1']) && isset($_POST['password2'])) {

    if (!isset($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt." ;
        exit();
    } //endif

    if ($_SESSION['user_id'] != $_POST['passform_id']) {
        echo "FEHLER: Das Update von Passwörtern fremder Benutzer ist nicht erlaubt.";
        exit();
    } //endif

    if ($_POST['password1'] != $_POST['password2']) {
        echo "FEHLER: Die Passwörter stimmen nicht überein.";
        exit();
    } //endif

    $val = new Validator();
    $usrhelper = new UserHelper();
    $user = $usrhelper->getUserById($_SESSION['user_id']);
    $usrhelper->updatePassword($_SESSION['user_id'], $_POST['password1']);

    header("Location: /profile-password-update-success");
} //endif