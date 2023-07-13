<?php
session_start();

function __autoload($classfile) {
    if ($classfile != "" && is_file("../classes/".$classfile.".php")) {
        require "../classes/".$classfile.".php";
    } //endif
} //endfunction autoload

if (isset($_GET['addgroup']) && $_GET['addgroup'] == '1') {
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    $camhelper = new CamHelper();
    $validator = new Validator();
    if (!isset($_POST['graddname']) || !$validator->isValidStdString($_POST['graddname'])) {
        echo "FEHLER: Ungültigen Gruppennamen übergeben.";
        exit();
    } //endif
    if ($camhelper->groupNameExists($_POST['graddname'])) {
        echo "FEHLER: Eine Gruppe mit diesem Namen existiert bereits.";
        exit();
    } //endif
    if (isset($_POST['graddcams']) && !is_array($_POST['graddcams'])) {
        echo "FEHLER: Ungültiger Gerätewert";
        exit();
    } //endif
    if (!isset($_POST['graddcams'])) {
        $cams = array();
    } else {
        $cams = $_POST['graddcams'];
        foreach ($cams as $cam) {
            if (!$camhelper->camExists($cam)) {
                echo "FEHLER: Eine der übergebenen Kamera IDs existiert nicht.";
                exit();
            } //endif
        } //endforeach
    } //endif

    $camhelper->addGroup($_POST['graddname'], $cams);

    echo "1";
} else if (isset($_GET['changegroup']) && $_GET['changegroup'] == '1') {
    if (!isset($_POST['grchangeid']) || !is_numeric($_POST['grchangeid'])) {
        echo "FEHLER: Keine ID übergeben.";
        exit();
    } //endif
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    if (!$camhelper->groupExists($_POST['grchangeid'])) {
        echo "FEHLER: Eine Gruppe mit dieser ID existiert nicht.";
        exit();
    } //endif

    $validator = new Validator();
    if (!isset($_POST['grchangename']) || !$validator->isValidStdString($_POST['grchangename'])) {
        echo "FEHLER: Ungültigen Gruppennamen übergeben.";
        exit();
    } //endif

    $group = $camhelper->getGroupById($_POST['grchangeid']);
    if ($group['name'] != $_POST['grchangename'] && $camhelper->groupNameExists($_POST['grchangename'])) {
        echo "FEHLER: Der Gruppenname existiert bereits.";
        exit();
    } //endif

    if (isset($_POST['grchangecams']) && !is_array($_POST['grchangecams'])) {
        echo "FEHLER: Ungültiger Gerätewert";
        exit();
    } //endif
    if (!isset($_POST['grchangecams'])) {
        $cams = array();
    } else {
        $cams = $_POST['grchangecams'];
        foreach ($cams as $cam) {
            if (!$camhelper->camExists($cam)) {
                echo "FEHLER: Eine der übergebenen Kamera IDs existiert nicht.";
                exit();
            } //endif
        } //endforeach
    } //endif

    $camhelper->changeGroup($_POST['grchangeid'], $_POST['grchangename'], $cams);

    echo "1";
} else if (isset($_GET['groupname_exists']) && $_GET['groupname_exists'] == '1') {
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    $validator = new Validator();
    if (!isset($_GET['groupname']) || !$validator->isValidStdString($_GET['groupname'])) {
        echo "FEHLER: Ungültigen Gruppennamen übergeben.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    if ($camhelper->groupNameExists($_GET['groupname'])) {
        echo "1";
        exit();
    } //endif
    echo "0";
} else if (isset($_GET['getgroupdata']) && ($_GET['getgroupdata'] == '1' || $_GET['getgroupdata'] == '2')) {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif

    if ($_GET['getgroupdata'] == '1') {
        $table = 'group';
        $primaryKey = 'id';
        $columns = array(
            array('db' => 'id', 'dt' => 'id'),
            array('db' => 'name', 'dt' => 'name'),
            array(
                'db' => 'id',
                'dt' => 'cams',
                'formatter' => function ($d, $row) {
                    $dbcon = PDOConnection::getPdoConnection();
                    $res = $dbcon->prepare("SELECT `camera`.`name` FROM `group_map` INNER JOIN `camera` ON `group_map`.`camid` = `camera`.`id` WHERE `group_map`.`groupid`=?;");
                    $res->execute(array($d));
                    $ret = $res->fetchAll(PDO::FETCH_ASSOC);

                    $retVal = '';
                    for ($i = 0; $i < count($ret); $i++) {
                        $retVal .= '<i class="fa fa-video-camera groupicon"></i> ';
                        $retVal .= $ret[$i]['name'];
                        if ($i != count($ret) - 1) {
                            $retVal .= ',&nbsp;&nbsp;';
                        } //endif
                    } //endfor
                    if (count($ret) == 0) {
                        $retVal .= '<i class="fa fa-minus-circle groupicon"></i>&nbsp;&nbsp;Keine Ger&auml;te vorhanden.';
                    } //endif

                    return $retVal;
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
    } else {
        $dbcon = PDOConnection::getPdoConnection();
        $res = $dbcon->prepare("SELECT * FROM `group`;");
        $res->execute();
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($ret);
    } //endif
} else if (isset($_GET['getgroupbyid']) && $_GET['getgroupbyid'] == '1') {
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo "FEHLER: Ungültige ID übergeben.";
        exit();
    } //endif
    $camhelper = new CamHelper();
    $group = $camhelper->getGroupById($_POST['id']);

    echo json_encode($group);
} else if (isset($_GET['getgroupmembers-by-gid']) && $_GET['getgroupmembers-by-gid'] == '1') {
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo "FEHLER: Ungültige ID übergeben.";
        exit();
    } //endif
    $camhelper = new CamHelper();
    $groupmembers = $camhelper->getGroupMembersByGid($_POST['id']);

    echo json_encode($groupmembers);
} else if (isset($_GET['getgroupmembers-by-cid']) && $_GET['getgroupmembers-by-cid'] == '1') {
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo "FEHLER: Ungültige ID übergeben.";
        exit();
    } //endif
    $camhelper = new CamHelper();
    $groupmembers = $camhelper->getGroupMembersByCid($_POST['id']);

    echo json_encode($groupmembers);
} else if (isset($_GET['delgroup']) && $_GET['delgroup'] == '1') {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo "FEHLER: Keine ID übergeben.";
        exit();
    } //endif
    if (!isset($_SESSION['user_admin']) || ($_SESSION['user_admin'] != '1' && $_SESSION['user_admin'] != '2')) {
        echo "FEHLER: Sie sind kein Administrator.";
        exit();
    } //endif

    $camhelper = new CamHelper();
    if (!$camhelper->groupExists($_POST['id'])) {
        echo "FEHLER: Eine Gruppe mit dieser ID existiert nicht.";
        exit();
    } //endif

    $camhelper->deleteGroup($_POST['id']);

    echo "1";
} else if (isset($_GET['lmaddgroup']) && $_GET['lmaddgroup'] == '1') {
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
        echo "FEHLER: Die Gruppe existiert nicht.";
        exit();
    } //endif

    $groups = $camhelper->liveMonAddGroup($_SESSION['user_id'], $_POST['id']);
    if ($groups == '-1' || count($groups) == 0) {
        echo -1;
    } else {
        echo json_encode($groups);
    } //endif
} else if (isset($_GET['lmdelgroup']) && $_GET['lmdelgroup'] == '1') {
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
        echo "FEHLER: Die Gruppe existiert nicht.";
        exit();
    } //endif

    $groups = $camhelper->liveMonDelGroup($_SESSION['user_id'], $_POST['id']);
    if ($groups == '-1' || count($groups) == 0) {
        echo -1;
    } else {
        echo json_encode($groups);
    } //endif
} //endif

?>