<?php

class CamHelper {
    private $dbcon = null;

    function __construct() {
        $this->dbcon = PDOConnection::getPdoConnection();
    } //endconstructor

    function addCam($name, $host, $port, $path, $ssl, $auth, $user, $password, $resolution, $type, $groups) {
        $fpchar = substr($path, 0, 1);
        if ($fpchar != '/') {
            $path = '/' . $path;
        } //endif
        $sql = "INSERT INTO `camera` (`name`, `host`, `port`, `path`, `ssl`, `auth`, `user`, `password`, `resolution`, `type`, `created`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($name, $host, $port, $path, $ssl, $auth, $user, $password, $resolution, $type, 0));
        $camid = $this->dbcon->lastInsertId();

        foreach ($groups as $groupid) {
            $sql = "INSERT INTO `group_map` (`groupid`, `camid`) VALUES (?, ?);";
            $res = $this->dbcon->prepare($sql);
            $res->execute(array($groupid, $camid));
        } //endforeach

        $this->triggerVHostRewrite();
    } //endfunction addCam

    function deleteCam($cdid) {
        $sql = "DELETE FROM `camera` WHERE `id`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($cdid));

        $sql = "DELETE FROM `widget_settings` WHERE `wname`=? AND `name`=? AND `value`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array('cameras', 'camid', $cdid));

        $sql = "DELETE FROM `group_map` WHERE `camid`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($cdid));

        $this->triggerVHostRewrite();
    } //endfunction deleteCam

    function changeCam($id, $name, $host, $port, $path, $ssl, $auth, $user, $password, $resolution, $type, $groups) {
        $camold = $this->getCamById($id);
        $vhtrigger = false;
        if ($camold['host'] != $host || $camold['port'] != $port || $camold['path'] != $path ||
            $camold['ssl'] != $ssl || $camold['auth'] != $auth || $camold['user'] != $user ||
            $camold['password'] != $password) {
            $vhtrigger = true;
        } //endif

        $fpchar = substr($path, 0, 1);
        if ($fpchar != '/') {
            $path = '/' . $path;
        } //endif
        $sql = "UPDATE `camera` SET `name`=?, `host`=?, `port`=?, `path`=?, `ssl`=?, `auth`=?, `user`=?, `password`=?, `resolution`=?, `type`=?, `created`=? WHERE `id`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($name, $host, $port, $path, $ssl, $auth, $user, $password, $resolution, $type, 0, $id));

        if (!empty($groups)) {
            $sql = "DELETE FROM `group_map` WHERE `camid`=?;";
            $res = $this->dbcon->prepare($sql);
            $res->execute(array($id));
        } //endif

        foreach ($groups as $gid) {
            $sql = "INSERT INTO `group_map` (`groupid`, `camid`) VALUES (?, ?);";
            $res = $this->dbcon->prepare($sql);
            $res->execute(array($gid, $id));
        } //endforeach

        if ($vhtrigger) {
            $this->triggerVHostRewrite();
            return true;
        } //endif
        return false;
    } //endfunction changeCam

    function camNameExists($name) {
        $retVal = false;

        $sql = 'SELECT COUNT(*) FROM `camera` WHERE `name`=?';
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($name));

        if ($res->fetchColumn()) {
            $retVal = true;
        } //endif

        return $retVal;
    } //endfunction camNameExists

    function camExists($id) {
        $retVal = false;

        $sql = 'SELECT COUNT(*) FROM `camera` WHERE `id`=?';
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($id));

        if ($res->fetchColumn()) {
            $retVal = true;
        } //endif

        return $retVal;
    } //endfunction camExists

    function getGroupById($id) {
        $res = $this->dbcon->prepare("SELECT * FROM `group` WHERE `id`=?;");
        $res->execute(array($id));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0]['id'])) {
            $retVal = array();
        } else {
            $retVal = $ret[0];
        } //endif

        return $retVal;
    } //endfunction getGroupById

    function getGroupMembersByGid($id) {
        $res = $this->dbcon->prepare("SELECT * FROM `group_map` WHERE `groupid`=?;");
        $res->execute(array($id));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0]['id'])) {
            $retVal = array();
        } else {
            $retVal = $ret;
        } //endif

        return $retVal;
    } //endfunction getGroupMembersByGid

    function getGroupMembersByCid($id) {
        $res = $this->dbcon->prepare("SELECT * FROM `group_map` WHERE `camid`=?;");
        $res->execute(array($id));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0]['id'])) {
            $retVal = array();
        } else {
            $retVal = $ret;
        } //endif

        return $retVal;
    } //endfunction getGroupMembersByCid

    function getCamById($id) {
        $res = $this->dbcon->prepare("SELECT * FROM `camera` WHERE `id`=?;");
        $res->execute(array($id));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0]['id'])) {
            $retVal = array();
        } else {
            $retVal = $ret[0];
        } //endif

        return $retVal;
    } //endfunction getCamById

    function triggerVHostRewrite($action=true) {
        if ($action) {
            $create = 1;
        } else {
            $create = 0;
        } //endif
        $res = $this->dbcon->prepare("UPDATE `settings` SET `value`=? WHERE `name`=?;");
        $res->execute(array($create, 'createcams'));
    } //endfunction triggerVHostRewrite

    function isCreatingCams() {
        $res = $this->dbcon->prepare("SELECT `value` FROM `settings` WHERE `name`=?;");
        $res->execute(array('createcams'));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);

        if (isset($ret[0]['value']) && is_numeric($ret[0]['value'])) {
            return intval($ret[0]['value']);
        } else {
            return 0;
        } //endif
    } //endfunction isCreatingCams

    function updateHttpAuth() {
        $bytesPWD = openssl_random_pseudo_bytes(8);
        $bytesUSR = openssl_random_pseudo_bytes(8);
        $pwd = bin2hex($bytesPWD);
        $user = bin2hex($bytesUSR);

        $res = $this->dbcon->prepare("UPDATE `settings` SET `value`=? WHERE `name`=?;");
        $res->execute(array($user, 'authuser'));
        $res = $this->dbcon->prepare("UPDATE `settings` SET `value`=? WHERE `name`=?;");
        $res->execute(array($pwd, 'authpass'));

        $retVal = array();
        $retVal['user'] = $user;
        $retVal['pass'] = $pwd;
        return $retVal;
    } //endfunction updateHttpAuth

    function getFrame($id, $size) {
        $camurl = ((Config::$SSL == '1') ? 'https://' : 'http://') . Config::$IP . '/cam/' . $id;

        $f = fopen($camurl, "r");
        if (!$f) {
            $imagick = new Imagick();
            $imagick->readImage("../dist/img/nocon_320.jpg");
            return $imagick->getImageBlob();
        } else {
            $r = null;
            while(substr_count($r, "\xFF\xD8") != 2) {
                $r .= fread($f, 512);
            } //endif
            $start = strpos($r, "\xFF\xD8");
            $end = strpos($r, "\xFF\xD9", $start)+2;
            $frame = substr($r, $start, $end-$start);
            fclose($f);

            $imagick = new Imagick();
            $imagick->readImageBlob($frame);
            if ($size == 'thumb') {
                $imagick->thumbnailImage(233, 175, false, false);
            } //endif

            return $imagick->getImageBlob();
        } //endif
    } //endfunction getFrame

    function getCamStatus($id) {
        $cam = $this->getCamById($id);
        $protocol = ($cam['ssl'] == '1') ? 'https://' : 'http://';
        $url = $protocol.$cam['host'].':'.$cam['port'].$cam['path'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        if ($cam['auth'] == '1') {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $cam['user'] . ':' . $cam['password']);
        } //endif
        if ($cam['ssl'] == '1') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        } //endif

        curl_exec($ch);
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $info;
    } //endfunction getCamStatus

    function groupNameExists($name) {
        $retVal = false;

        $sql = 'SELECT COUNT(*) FROM `group` WHERE `name`=?';
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($name));

        if ($res->fetchColumn()) {
            $retVal = true;
        } //endif

        return $retVal;
    } //endfunction groupNameExists

    function groupExists($id) {
        $retVal = false;

        $sql = 'SELECT COUNT(*) FROM `group` WHERE `id`=?';
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($id));

        if ($res->fetchColumn()) {
            $retVal = true;
        } //endif

        return $retVal;
    } //endfunction groupExists

    function addGroup($name, $camids) {
        $sql = "INSERT INTO `group` (`name`) VALUES (?);";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($name));
        $groupid = $this->dbcon->lastInsertId();

        foreach ($camids as $cid) {
            $sql = "INSERT INTO `group_map` (`groupid`, `camid`) VALUES (?, ?);";
            $res = $this->dbcon->prepare($sql);
            $res->execute(array($groupid, $cid));
        } //endforeach
    } //endfunction addGroup

    function changeGroup($id, $name, $camids) {
        $sql = "UPDATE `group` SET `name`=? WHERE `id`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($name, $id));

        if (!empty($camids)) {
            $sql = "DELETE FROM `group_map` WHERE `groupid`=?;";
            $res = $this->dbcon->prepare($sql);
            $res->execute(array($id));
        } //endif

        foreach ($camids as $cid) {
            $sql = "INSERT INTO `group_map` (`groupid`, `camid`) VALUES (?, ?);";
            $res = $this->dbcon->prepare($sql);
            $res->execute(array($id, $cid));
        } //endforeach
    } //endfunction changeGroup

    function deleteGroup($id) {
        $sql = "DELETE FROM `group` WHERE `id`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($id));

        $sql = "DELETE FROM `group_map` WHERE `groupid`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($id));
    } //endfunction deleteGroup

    function getCamRefreshMode($userid) {
        $res = $this->dbcon->prepare("SELECT `value` FROM `user_settings` WHERE `name`=? AND `user_id`=?;");
        $res->execute(array('camrefreshmode', $userid));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0])) {
            $this->setCamRefreshMode($userid);
            return 'poll';
        } else {
            return $ret[0]['value'];
        } //endif
    } //endfunction getCamRefreshMode

    function setCamRefreshMode($userid, $mode='poll') {
        $res = $this->dbcon->prepare("SELECT `value` FROM `user_settings` WHERE `name`=? AND `user_id`=?;");
        $res->execute(array('camrefreshmode', $userid));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0])) {
            $res = $this->dbcon->prepare("INSERT INTO `user_settings` (`user_id`, `name`, `value`) VALUES (?, ?, ?);");
            $res->execute(array($userid, 'camrefreshmode', $mode));
        } else {
            $res = $this->dbcon->prepare("UPDATE `user_settings` SET `value`=? WHERE `name`=? AND `user_id`=?;");
            $res->execute(array($mode, 'camrefreshmode', $userid));
        } //endif
    } //endfunction setCamRefreshMode

    function getCamWidgetRefreshMode($userid) {
        $res = $this->dbcon->prepare("SELECT `value` FROM `widget_settings` WHERE `name`=? AND `wname`=? AND `userid`=?;");
        $res->execute(array('mode', 'cameras', $userid));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0])) {
            $this->setCamWidgetRefreshMode($userid);
            return 'poll';
        } else {
            return $ret[0]['value'];
        } //endif
    } //endfunction getCamWidgetRefreshMode

    function setCamWidgetRefreshMode($userid, $mode='poll') {
        $res = $this->dbcon->prepare("SELECT `value` FROM `widget_settings` WHERE `name`=? AND `wname`=? AND `userid`=?;");
        $res->execute(array('mode', 'cameras', $userid));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0])) {
            $res = $this->dbcon->prepare("INSERT INTO `widget_settings` (`userid`, `wname`, `name`, `value`) VALUES (?, ?, ?, ?);");
            $res->execute(array($userid, 'cameras', 'mode', $mode));
        } else {
            $res = $this->dbcon->prepare("UPDATE `widget_settings` SET `value`=? WHERE `name`=? AND `wname`=? AND `userid`=?;");
            $res->execute(array($mode, 'mode', 'cameras', $userid));
        } //endif
    } //endfunction setCamWidgetRefreshMode

    function getCamWidgetCamId($userid) {
        $res = $this->dbcon->prepare("SELECT `value` FROM `widget_settings` WHERE `name`=? AND `wname`=? AND `userid`=?;");
        $res->execute(array('camid', 'cameras', $userid));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0])) {
            $this->setCamWidgetCamId($userid);
            return '-1';
        } else {
            return $ret[0]['value'];
        } //endif
    } //endfunction getCamWidgetCamId

    function setCamWidgetCamId($userid, $cid='-1') {
        $res = $this->dbcon->prepare("SELECT `value` FROM `widget_settings` WHERE `name`=? AND `wname`=? AND `userid`=?;");
        $res->execute(array('camid', 'cameras', $userid));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0])) {
            $res = $this->dbcon->prepare("INSERT INTO `widget_settings` (`userid`, `wname`, `name`, `value`) VALUES (?, ?, ?, ?);");
            $res->execute(array($userid, 'cameras', 'camid', $cid));
        } else {
            $res = $this->dbcon->prepare("UPDATE `widget_settings` SET `value`=? WHERE `name`=? AND `wname`=? AND `userid`=?;");
            $res->execute(array($cid, 'camid', 'cameras', $userid));
        } //endif
    } //endfunction setCamWidgetCamId

    function getCamAuthLogin() {
        $retVal = array();
        $res = $this->dbcon->prepare("SELECT `value` FROM `settings` WHERE `name`=?;");
        $res->execute(array('authuser'));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        $retVal['user'] = $ret[0]['value'];
        $res = $this->dbcon->prepare("SELECT `value` FROM `settings` WHERE `name`=?;");
        $res->execute(array('authpass'));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        $retVal['pass'] = $ret[0]['value'];

        return $retVal;
    } //endfunction getCamAuthLogin

    function camIsCreated($id) {
        $res = $this->dbcon->prepare("SELECT `created` FROM `camera` WHERE `id`=?;");
        $res->execute(array($id));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);

        if ($ret[0]['created'] == '0') {
            return false;
        } else {
            return true;
        } //endif
    } //endfunction camIsCreated

    function liveMonAddDevice($uid, $id) {
        $sql = 'SELECT COUNT(*) FROM `livemonitor_element` WHERE `uid`=? AND `type`=? AND `eid`=?;';
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($uid, 'camera', $id));

        if ($res->fetchColumn()) {
            return -1;
        } //endif

        $sql = 'INSERT INTO `livemonitor_element` (`uid`, `type`, `eid`) VALUES (?, ?, ?);';
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($uid, 'camera', $id));

        $sql = "SELECT `groupid` FROM `group_map` WHERE `camid`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($id));
        $groups = $res->fetchAll(PDO::FETCH_ASSOC);

        $retgroups = array();

        foreach ($groups as $group) {
            $groupMembers = $this->getGroupMembersByGid($group['groupid']);
            $ids = '';
            for ($i=0; $i<count($groupMembers); $i++) {
                $ids .= $groupMembers[$i]['camid'];
                if ($i < count($groupMembers) - 1) {
                    $ids .= ', ';
                } //endif
            } //endfor

            $sql = "SELECT COUNT(*) AS `elementcount` FROM `livemonitor_element` WHERE `uid`=? AND `type`=? AND `eid` IN (".$ids.");";
            $res = $this->dbcon->prepare($sql);
            $res->execute(array($uid, 'camera'));
            $count = $res->fetchAll(PDO::FETCH_ASSOC);

            if (count($count) > 0 && intval($count[0]['elementcount']) == count($groupMembers)) {
                $sql = "SELECT COUNT(*) FROM `livemonitor_element` WHERE `uid`=? AND `type`=? AND `eid`=?;";
                $res = $this->dbcon->prepare($sql);
                $res->execute(array($uid, 'group', $group['groupid']));

                if (!$res->fetchColumn()) {
                    $sql = "INSERT INTO `livemonitor_element` (`uid`, `type`, `eid`) VALUES (?, ?, ?);";
                    $res = $this->dbcon->prepare($sql);
                    $res->execute(array($uid, 'group', $group['groupid']));
                    $retgroups[] = $group['groupid'];
                } //endif
            } //endif
        } //endforeach

        return $retgroups;
    } //endfunction liveMonAddDevice

    function liveMonDelDevice($uid, $id) {
        $sql = 'SELECT COUNT(*) FROM `livemonitor_element` WHERE `uid`=? AND `type`=? AND `eid`=?;';
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($uid, 'camera', $id));

        if (!$res->fetchColumn()) {
            return -1;
        } //endif

        $sql = 'DELETE FROM `livemonitor_element` WHERE `uid`=? AND `type`=? AND `eid`=?;';
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($uid, 'camera', $id));

        $sql = "SELECT `group_map`.`groupid` FROM `group_map` INNER JOIN `livemonitor_element`
                ON `group_map`.`groupid`=`livemonitor_element`.`eid`
                WHERE `livemonitor_element`.`uid`=? AND `livemonitor_element`.`type`=? AND `group_map`.`camid`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($uid, 'group', $id));
        $groups = $res->fetchAll(PDO::FETCH_ASSOC);

        $retgroups = array();

        foreach ($groups as $group) {
            $sql = "DELETE FROM `livemonitor_element` WHERE `uid`=? AND `type`=? AND `eid`=?;";
            $res = $this->dbcon->prepare($sql);
            $res->execute(array($uid, 'group', $group['groupid']));

            $retgroups[] = $group['groupid'];
        } //endforeach

        return $retgroups;
    } //endfunction liveMonDelDevice

    function liveMonAddGroup($uid, $id) {
        $sql = "SELECT COUNT(*) FROM `livemonitor_element` WHERE `uid`=? AND `type`=? AND `eid`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($uid, 'group', $id));
        if ($res->fetchColumn()) {
            return -1;
        } //endif

        $sql = "SELECT `camid` FROM `group_map` WHERE `groupid`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($id));
        $cams = $res->fetchAll(PDO::FETCH_ASSOC);

        $retgroups = array();
        foreach ($cams as $cam) {
            $sql = "SELECT COUNT(*) FROM `livemonitor_element` WHERE `uid`=? AND `type`=? AND `eid`=?;";
            $res = $this->dbcon->prepare($sql);
            $res->execute(array($uid, 'camera', $cam['camid']));
            if (!$res->fetchColumn()) {
                $tgrs = $this->liveMonAddDevice($uid, $cam['camid']);
                foreach ($tgrs as $tgr) {
                    $exists = false;
                    foreach ($retgroups as $tret) {
                        if ($tret == $tgr) {
                            $exists = true;
                        } //endif
                    } //endforeach
                    if (!$exists) {
                        $retgroups[] = $tgr;
                    } //endif
                } //endforeach
            } //endif
        } //endforeach

        return $retgroups;
    } //endfunction liveMonAddGroup

    function liveMonDelGroup($uid, $id) {
        $sql = "SELECT COUNT(*) FROM `livemonitor_element` WHERE `uid`=? AND `type`=? AND `eid`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($uid, 'group', $id));
        if (!$res->fetchColumn()) {
            return -1;
        } //endif

        $sql = "SELECT `camid` FROM `group_map` WHERE `groupid`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($id));
        $cams = $res->fetchAll(PDO::FETCH_ASSOC);

        $retgroups = array();
        foreach ($cams as $cam) {
            $sql = "SELECT COUNT(*) FROM `livemonitor_element` WHERE `uid`=? AND `type`=? AND `eid`=?;";
            $res = $this->dbcon->prepare($sql);
            $res->execute(array($uid, 'camera', $cam['camid']));
            if ($res->fetchColumn()) {
                $tgrs = $this->liveMonDelDevice($uid, $cam['camid']);
                foreach ($tgrs as $tgr) {
                    $exists = false;
                    foreach ($retgroups as $tret) {
                        if ($tret == $tgr) {
                            $exists = true;
                        } //endif
                    } //endforeach
                    if (!$exists) {
                        $retgroups[] = $tgr;
                    } //endif
                } //endforeach
            } //endif
        } //endforeach

        return $retgroups;
    } //endfunction liveMonDelGroup

    function getCamsByGroupId($id) {
        $sql = "SELECT
                  `camera`.`id`,
                  `camera`.`name`,
                  `camera`.`host`,
                  `camera`.`port`,
                  `camera`.`path`,
                  `camera`.`ssl`,
                  `camera`.`auth`,
                  `camera`.`user`,
                  `camera`.`password`,
                  `camera`.`resolution`,
                  `camera`.`type`,
                  `camera`.`created`
                FROM `camera` INNER JOIN `group_map` ON `camera`.`id`=`group_map`.`camid` WHERE `group_map`.`groupid`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($id));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    } //endfunction getCamsByGroupId

} //endclass CamHelper

?>