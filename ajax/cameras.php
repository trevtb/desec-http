<?php
session_start();

function __autoload($classfile) {
    if ($classfile != "" && is_file("../classes/".$classfile.".php")) {
        require "../classes/".$classfile.".php";
    } //endif
} //endfunction autoload

if (isset($_GET['addcam']) && $_GET['addcam'] == '1') {
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    $camhelper = new CamHelper();
    $validator = new Validator();
    if (!isset($_POST['cahost']) || !$validator->isValidStdString($_POST['cahost'])) {
        echo "FEHLER: Ungültiger Hostname.";
        exit();
    } //endif
    if (!isset($_POST['caport']) || !$validator->isValidPort($_POST['caport'])) {
        echo "FEHLER: Ungültige Portnummer.";
        exit();
    } //endif
    if (!isset($_POST['capath']) || !$validator->isValidStdString($_POST['capath'])) {
        echo "FEHLER: Ungültige Pfadangabe.";
        exit();
    } //endif
    if (!isset($_POST['cassl']) || $_POST['cassl'][0] != '1') {
        $ssl = 0;
    } else {
        $ssl = 1;
    } //endif

    if (!isset($_POST['caauth']) || $_POST['caauth'][0] != '1') {
        $auth = 0;
    } else {
        $auth = 1;
    } //endif
    if ($auth == 1 && (!isset($_POST['causer']) || $_POST['causer'] == '' || !isset($_POST['capassword']) || $_POST['capassword'] == '')) {
        echo "FEHLER: Keine Benutzerdaten für die HTTP Authentifizierung übergeben.";
        exit();
    } //endif
    if (isset($_POST['causer']) && $validator->isValidUsername($_POST['causer'])) {
        $user = $_POST['causer'];
    } else {
        $user = '';
    } //endif
    if (isset($_POST['capassword']) && $validator->isValidPassword($_POST['capassword'])) {
        $pass = $_POST['capassword'];
    } else {
        $pass = '';
    } //endif

    if (!isset($_POST['caname']) || !$validator->isValidStdString($_POST['caname'])) {
        echo "FEHLER: Ungültigen Kameranamen übergeben.";
        exit();
    } //endif
    if ($camhelper->camNameExists($_POST['caname'])) {
        echo "FEHLER: Eine Kamera mit diesem Namen existiert bereits.";
        exit();
    } //endif
    if (!isset($_POST['caresolution']) ||
        !$validator->isValidStdString($_POST['caresolution']) ||
        ($_POST['caresolution'] != '320x240' && $_POST['caresolution'] != '640x480' && $_POST['caresolution'] != '1280x720')
    ) {
        echo "FEHLER: Ungültige Auflösung.";
        exit();
    } //endif

    if (!isset($_POST['catype']) || ($_POST['catype'] != 'desec' && $_POST['catype'] != 'ip')) {
        echo "FEHLER: Ungültigen Typ übergeben.";
        exit();
    } //endif

    if (!isset($_POST['caaddgroups'])) {
        $groups = array();
    } else {
        $groups = $_POST['caaddgroups'];
        foreach ($groups as $group) {
            if (!$camhelper->groupExists($group)) {
                echo "FEHLER: Eine der übergebenen Gruppen IDs existiert nicht.";
                exit();
            } //endif
        } //endforeach
    } //endif

    $camhelper->addCam($_POST['caname'], $_POST['cahost'], $_POST['caport'], $_POST['capath'], $ssl, $auth, $user, $pass, $_POST['caresolution'], $_POST['catype'], $groups);

    echo "1";
} else if (isset($_GET['changecam']) && $_GET['changecam'] == '1') {
    if (!isset($_POST['ccid']) || !is_numeric($_POST['ccid'])) {
        echo "FEHLER: Keine ID übergeben.";
        exit();
    } //endif
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif

    $camhelper = new CamHelper();

    if (!$camhelper->camExists($_POST['ccid'])) {
        echo "FEHLER: Ein Kameraeintrag mit dieser ID existiert nicht.";
        exit();
    } //endif
    $validator = new Validator();
    if (!isset($_POST['cchost']) || !$validator->isValidStdString($_POST['cchost'])) {
        echo "FEHLER: Ungültiger Hostname.";
        exit();
    } //endif
    if (!isset($_POST['ccport']) || !$validator->isValidPort($_POST['ccport'])) {
        echo "FEHLER: Ungültige Portnummer.";
        exit();
    } //endif
    if (!isset($_POST['ccpath']) || !$validator->isValidStdString($_POST['ccpath'])) {
        echo "FEHLER: Ungültige Pfadangabe.";
        exit();
    } //endif
    if (!isset($_POST['ccssl']) || $_POST['ccssl'][0] != '1') {
        $ssl = 0;
    } else {
        $ssl = 1;
    } //endif

    if (!isset($_POST['ccauth']) || $_POST['ccauth'][0] != '1') {
        $auth = 0;
    } else {
        $auth = 1;
    } //endif
    if ($auth == 1 && (!isset($_POST['ccuser']) || $_POST['ccuser'] == '' || !isset($_POST['ccpassword']) || $_POST['ccpassword'] == '')) {
        echo "FEHLER: Keine Benutzerdaten für die HTTP Authentifizierung übergeben.";
        exit();
    } //endif
    if (isset($_POST['ccuser']) && $validator->isValidUsername($_POST['ccuser'])) {
        $user = $_POST['ccuser'];
    } else {
        $user = '';
    } //endif
    if (isset($_POST['ccpassword']) && $validator->isValidPassword($_POST['ccpassword'])) {
        $pass = $_POST['ccpassword'];
    } else {
        $pass = '';
    } //endif

    if (!isset($_POST['ccname']) || !$validator->isValidStdString($_POST['ccname'])) {
        echo "FEHLER: Ungültigen Kameranamen übergeben.";
        exit();
    } //endif

    if (!isset($_POST['ccresolution']) ||
        !$validator->isValidStdString($_POST['ccresolution']) ||
        ($_POST['ccresolution'] != '320x240' && $_POST['ccresolution'] != '640x480' && $_POST['ccresolution'] != '1280x720')) {
        echo "FEHLER: Ungültige Auflösung.";
        exit();
    } //endif

    if (!isset($_POST['cctype']) || ($_POST['cctype'] != 'desec' && $_POST['cctype'] != 'ip')) {
        echo "FEHLER: Ungültigen Typ übergeben.";
        exit();
    } //endif

    if (!isset($_POST['ccchangegroups'])) {
        $groups = array();
    } else {
        $groups = $_POST['ccchangegroups'];
        foreach ($groups as $group) {
            if (!$camhelper->groupExists($group)) {
                echo "FEHLER: Eine der übergebenen Gruppen IDs existiert nicht.";
                exit();
            } //endif
        } //endforeach
    } //endif

    $rewrite = $camhelper->changeCam($_POST['ccid'], $_POST['ccname'], $_POST['cchost'], $_POST['ccport'], $_POST['ccpath'], $ssl, $auth, $user, $pass, $_POST['ccresolution'], $_POST['cctype'], $groups);
    if ($rewrite) {
        echo "1";
    } else {
        echo "0";
    } //endif
} else if (isset($_GET['camname_exists']) && $_GET['camname_exists'] == '1') {
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    $validator = new Validator();
    if (!isset($_GET['camname']) || !$validator->isValidStdString($_GET['camname'])) {
        echo "FEHLER: Ungültiger Kameraname übergeben.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    if ($camhelper->camNameExists($_GET['camname'])) {
        echo "1";
        exit();
    } //endif
    echo "0";
} else if (isset($_GET['getcamdata']) && ($_GET['getcamdata'] == '1' || $_GET['getcamdata'] == '2')) {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif

    if ($_GET['getcamdata'] == '1') {
        $table = 'camera';
        $primaryKey = 'id';

        $columns = array(
            array('db' => 'id', 'dt' => 'id'),
            array('db' => 'name', 'dt' => 'name'),
            array('db' => 'type', 'dt' => 'type'),
            array('db' => 'host', 'dt' => 'host'),
            array('db' => 'port', 'dt' => 'port'),
            array('db' => 'path', 'dt' => 'path'),
            array('db' => 'ssl', 'dt' => 'ssl'),
            array('db' => 'auth', 'dt' => 'auth'),
            array('db' => 'id', 'dt' => 'id')
        );

        $sql_details = array(
            'user' => Config::$DBUSER,
            'pass' => Config::$DBPASS,
            'db' => Config::$DBNAME,
            'host' => Config::$DBHOST
        );

        echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns));
    } else {
        $dbcon = PDOConnection::getPdoConnection();
        $res = $dbcon->prepare("SELECT `id`, `name` FROM `camera`;");
        $res->execute();
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($ret);
    } //endif
} else if (isset($_GET['getcambyid']) && $_GET['getcambyid'] == '1') {
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo "FEHLER: Ungültige ID übergeben.";
        exit();
    } //endif
    $camhelper = new CamHelper();
    $cam = $camhelper->getCamById($_POST['id']);

    echo json_encode($cam);
} else if (isset($_GET['delcam']) && $_GET['delcam'] == '1') {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo "FEHLER: Keine ID übergeben.";
        exit();
    } //endif
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    if (!$camhelper->camExists($_POST['id'])) {
        echo "FEHLER: Eine Kamera mit dieser ID existiert nicht.";
        exit();
    } //endif

    $camhelper->deleteCam($_POST['id']);

    echo "1";
} else if (isset($_GET['checkcreation']) && $_GET['checkcreation'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    $camhelper = new CamHelper();
    $status = $camhelper->isCreatingCams();
    echo $status;
} else if (isset($_GET['camlogin']) && $_GET['camlogin'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    echo "1";
} else if (isset($_GET['getframe']) && $_GET['getframe'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo "FEHLER: Keine id übergeben.";
        exit();
    } //endif
    if (!isset($_GET['size']) || ($_GET['size'] != 'thumb' && $_GET['size'] != 'big')) {
        echo "FEHLER: Ungültige Größe übergeben.";
        exit();
    } //endif
    $camhelper = new CamHelper();
    $cam = $camhelper->getCamById($_GET['id']);
    if (empty($cam)) {
        echo "FEHLER: Die übergebene ID existiert nicht.";
        exit();
    } //endif

    header("Content-type: image/jpeg");
    echo $camhelper->getFrame($_GET['id'], $_GET['size']);
} else if (isset($_GET['getcamrefmode']) && $_GET['getcamrefmode'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    $retVal = $camhelper->getCamRefreshMode($_SESSION['userid']);

    echo $retVal;
} else if (isset($_GET['setcamrefmode'])) {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif
    if ($_GET['setcamrefmode'] != 'live' && $_GET['setcamrefmode'] != 'poll') {
        echo "FEHLER: Ungültiger Wert.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    $camhelper->setCamRefreshMode($_SESSION['user_id'], $_GET['setcamrefmode']);

    echo "1";
} else if (isset($_GET['getcamlogin']) && $_GET['getcamlogin'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    $retVal = $camhelper->getCamAuthLogin();

    echo json_encode($retVal);
} else if (isset($_GET['camiscreated'])) {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif
    if (!is_numeric($_GET['camiscreated'])) {
        echo "FEHLER: Ungültigen Wert übergeben.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    if (!$camhelper->camExists($_GET['camiscreated'])) {
        echo "FEHLER: Eine Kamera mit dieser ID existiert nicht.";
        exit();
    } //endif

    $created = $camhelper->camIsCreated($_GET['camiscreated']);
    if ($created) {
        echo "1";
    } else {
        echo "0";
    } //endif
} else if (isset($_GET['testcamconnection']) && $_GET['testcamconnection'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif
    if (!is_numeric($_POST['id'])) {
        echo "FEHLER: Ungültigen Wert übergeben.";
        exit();
    } //endif
    $camhelper = new CamHelper();
    if (!$camhelper->camExists($_POST['id'])) {
        echo "FEHLER: Die Kamera existiert nicht.";
        exit();
    } //endif

    $status = $camhelper->getCamStatus($_POST['id']);
    if ($status == '0') {
        echo "offline";
    } else if ($status == '401') {
        echo "login";
    } else {
        echo "ok";
    } //endif
} else if (isset($_GET['lmaddcam']) && $_GET['lmaddcam'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif
    if (!is_numeric($_POST['id'])) {
        echo "FEHLER: Ungültigen Wert übergeben.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    if (!$camhelper->camExists($_POST['id'])) {
        echo "FEHLER: Die Kamera existiert nicht.";
        exit();
    } //endif

    $groups = $camhelper->liveMonAddDevice($_SESSION['user_id'], $_POST['id']);
    if ($groups == '-1' || count($groups) == 0) {
        echo -1;
    } else {
        echo json_encode($groups);
    } //endif
} else if (isset($_GET['lmdelcam']) && $_GET['lmdelcam'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif
    if (!is_numeric($_POST['id'])) {
        echo "FEHLER: Ungültigen Wert übergeben.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    if (!$camhelper->camExists($_POST['id'])) {
        echo "FEHLER: Die Kamera existiert nicht.";
        exit();
    } //endif

    $groups = $camhelper->liveMonDelDevice($_SESSION['user_id'], $_POST['id']);
    if ($groups == '-1' || count($groups) == 0) {
        echo -1;
    } else {
        echo json_encode($groups);
    } //endif
} else if (isset($_GET['camsbygid']) && $_GET['camsbygid'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif
    if (!is_numeric($_POST['id'])) {
        echo "FEHLER: Ungültigen Wert übergeben.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    if (!$camhelper->groupExists($_POST['id'])) {
        echo "FEHLER: Die Kamera existiert nicht.";
        exit();
    } //endif

    $cams = $camhelper->getCamsByGroupId($_POST['id']);
    if (count($cams) > 0) {
        echo json_encode($cams);
    } else {
        echo -1;
    } //endif
} //endif

?>