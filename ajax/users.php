<?php
session_start();

function __autoload($classfile) {
    if ($classfile != "" && is_file("../classes/".$classfile.".php")) {
        require "../classes/".$classfile.".php";
    } //endif
} //endfunction autoload

if (isset($_GET['user_exists']) && isset($_GET['user'])) {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    $usrhelp = new UserHelper();
    if ($usrhelp->userExists($_GET['user'])) {
        echo "1";
    } else {
        echo "0";
    } //endif
} else if (isset($_GET['email_exists']) && isset($_GET['email'])) {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    $usrhelp = new UserHelper();
    if ($usrhelp->emailExists($_GET['email'])) {
        echo "1";
    } else {
        echo "0";
    } //endif
} else if (isset($_GET['deluser']) && $_GET['deluser'] == '1') {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo "FEHLER: Keine ID übergeben.";
        exit();
    } //endif
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif

    $userhelper = new UserHelper();
    $user = $userhelper->getUserById($_POST['id']);
    if ($user['admin'] == '2') {
        echo "FEHLER: Superuser dürfen nicht gelöscht werden.";
        exit();
    } //endif
    if ($user['admin'] == '1' && $_SESSION['user_admin'] != '2') {
        echo "FEHLER: Administratoren dürfen keine anderen Administratoren löschen.";
        exit();
    } //endif

    $userhelper->deleteUser($_POST['id']);

    echo "1";
} else if (isset($_GET['getuserdata']) && $_GET['getuserdata'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif

    $table = 'user';
    $primaryKey = 'id';

    $columns = array(
        array('db' => 'id', 'dt' => 'id'),
        array('db' => 'accountname', 'dt' => 'accountname'),
        array('db' => 'email', 'dt' => 'email'),
        array('db' => 'name', 'dt' => 'name'),
        array('db' => 'surname', 'dt' => 'surname'),
        array('db' => 'admin', 'dt' => 'admin'),
        array(
            'db'        => 'lastonline',
            'dt'        => 'lastonline',
            'formatter' => function($d, $row) {
                if ($d != '0000-00-00 00:00:00' && $d != '') {
                    $date = new DateTime($d);
                    return $date->format('d.m.Y - H:i');
                } else {
                    return "Noch nie.";
                } //endif
            }
        ),
        array('db' => 'id', 'dt' => 'id')
    );

    $sql_details = array(
        'user' => Config::$DBUSER,
        'pass' => Config::$DBPASS,
        'db' => Config::$DBNAME,
        'host' => Config::$DBHOST
    );

    echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns));
} else if (isset($_GET['getuserbyid']) && $_GET['getuserbyid'] == '1') {
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo "FEHLER: Ungültige ID übergeben.";
        exit();
    } //endif
    $userhelper = new UserHelper();
    $user = $userhelper->getUserById($_POST['id']);

    echo json_encode($user);
} else if (isset($_GET['changeuserpwd']) && $_GET['changeuserpwd'] == '1') {
    if (!isset($_POST['cpid']) || !is_numeric($_POST['cpid'])) {
        echo "FEHLER: Keine ID übergeben.";
        exit();
    } //endif
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    if (!isset($_POST['cppassword1']) || !isset($_POST['cppassword2']) || $_POST['cppassword1'] != $_POST['cppassword2'] || empty($_POST['cppassword1'])) {
        echo "FEHLER: Kein Passwort übergeben, oder Passwörter stimmen nicht überein.";
        exit();
    } //endif

    $val = new Validator();
    if (!$val->isValidPassword($_POST['cppassword1'])) {
        echo "FEHLER: Ungültiges Passwort.";
        exit();
    } //endif

    $userhelper = new UserHelper();
    $user = $userhelper->getUserById($_POST['cpid']);
    if (empty($user)) {
        echo "FEHLER: Der Benutzer existiert nicht.";
        exit();
    } //endif
    if ($user['admin'] == '2') {
        echo "FEHLER: Superuser dürfen nicht editiert werden.";
        exit();
    } //endif
    if ($user['admin'] == '1' && $_SESSION['user_admin'] != '2') {
        echo "FEHLER: Administratoren dürfen keine anderen Administratoren editieren.";
        exit();
    } //endif

    $userhelper->updatePassword($_POST['cpid'], $_POST['cppassword1']);

    echo "1";
} else if (isset($_GET['changeuserdata']) && $_GET['changeuserdata'] == '1') {
    if (!isset($_POST['cuid']) || !is_numeric($_POST['cuid'])) {
        echo "FEHLER: Keine ID übergeben.";
        exit();
    } //endif
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    if (!isset($_POST['custatus']) || ($_POST['custatus'] != '0' && $_POST['custatus'] != '1')) {
        echo "FEHLER: Kein gültiger Wert für den Status übergeben.";
        exit();
    } //endif
    $userhelper = new UserHelper();
    $user = $userhelper->getUserById($_POST['cuid']);
    if (empty($user)) {
        echo "FEHLER: Benutzer nicht gefunden.";
        exit();
    } //endif
    if ($user['admin'] == '2') {
        echo "FEHLER: Superuser dürfen nicht editiert werden.";
        exit();
    } //endif
    if ($user['admin'] == '1' && $_SESSION['user_admin'] != '2') {
        echo "FEHLER: Administratoren dürfen keine anderen Administratoren editieren.";
        exit();
    } //endif
    if (isset($_POST['custatus']) && $_POST['custatus'] != $user['admin'] && $_SESSION['user_admin'] != '2') {
        echo "FEHLER: Administratoren dürfen keine Accountrechte vergeben.";
        exit();
    } //endif
    $val = new Validator();
    if (!isset($_POST['cuemail']) || !$val->isValidMailAddress($_POST['cuemail'])) {
        echo "FEHLER: Keine gültige Mailadresse übergeben.";
        exit();
    } //endif
    if (!isset($_POST['cuaccountname']) || !$val->isValidUsername($_POST['cuaccountname'])) {
        echo "FEHLER: Ungültigen Accountnamen übergeben.";
        exit();
    } //endif
    if (!isset($_POST['cuname']) || !isset($_POST['cusurname']) || !$val->isValidName($_POST['cuname']) || !$val->isValidName($_POST['cusurname'])) {
        echo "FEHLER: Ungültiger Name.";
        exit();
    } //endif

    $userhelper->changeUserData($_POST['cuid'], $_POST['cuaccountname'], $_POST['cuemail'], $_POST['cuname'], $_POST['cusurname'], $_POST['custatus']);

    echo "1";
} else if (isset($_GET['adduser']) && $_GET['adduser'] == '1') {
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    if (!isset($_POST['castatus']) || ($_POST['castatus'] != '0' && $_POST['castatus'] != '1')) {
        echo "FEHLER: Kein gültiger Wert für den Status übergeben.";
        exit();
    } //endif
    if ($_POST['castatus'] != '0' && $_SESSION['user_admin'] != '2') {
        echo "FEHLER: Nur Superadmins können Administratoren erstellen.";
        exit();
    } //endif
    $userhelper = new UserHelper();
    $val = new Validator();
    if (!isset($_POST['caemail']) || !$val->isValidMailAddress($_POST['caemail'])) {
        echo "FEHLER: Keine gültige Mailadresse übergeben.";
        exit();
    } //endif
    if (!isset($_POST['caaccountname']) || !$val->isValidUsername($_POST['caaccountname'])) {
        echo "FEHLER: Ungültigen Accountnamen übergeben.";
        exit();
    } //endif
    if ($userhelper->userExists($_POST['caaccountname'])) {
        echo "FEHLER: Ein Benutzer mit diesem Namen existiert bereits.";
        exit();
    } //endif
    if (!isset($_POST['caname']) || !isset($_POST['casurname']) || !$val->isValidName($_POST['caname']) || !$val->isValidName($_POST['casurname'])) {
        echo "FEHLER: Ungültiger Name.";
        exit();
    } //endif
    if (!isset($_POST['capassword1']) || !isset($_POST['capassword2']) || $_POST['capassword1'] != $_POST['capassword2'] || empty($_POST['capassword1'])) {
        echo "FEHLER: Kein Passwort übergeben, oder Passwörter stimmen nicht überein.";
        exit();
    } //endif
    if (!$val->isValidPassword($_POST['capassword1'])) {
        echo "FEHLER: Ungültiges Passwort.";
        exit();
    } //endif

    $userhelper->createUser($_POST['caaccountname'], $_POST['caemail'], $_POST['caname'], $_POST['casurname'], $_POST['capassword1'], $_POST['castatus']);

    echo "1";
} //endif

?>