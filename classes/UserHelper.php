<?php

class UserHelper {

    private $dbcon = null;

    function __construct() {
        $this->dbcon = PDOConnection::getPdoConnection();
    } //endconstructor

    function pollOnline($uid) {
        $dn = date('Y-m-d H:i:s');
        $sql = "UPDATE `user` SET `lastonline`=? WHERE `id`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($dn, $uid));
    } //endfunction pollOnline

    function deleteUser($duid) {
        $sql = "DELETE FROM `user` WHERE `id`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($duid));

        $sql = "DELETE FROM `user_settings` WHERE `user_id`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($duid));

        $sql = "DELETE FROM `widget_settings` WHERE `userid`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($duid));
    } //endfunction deleteUser

    function userExists($user) {
        $retVal = false;

        $sql = 'SELECT COUNT(*) FROM `user` WHERE `accountname`=?';
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($user));

        if ($res->fetchColumn()) {
            $retVal = true;
        } //endif

        return $retVal;
    } //endfunction userExists

    function emailExists($email) {
        $retVal = false;

        $sql = 'SELECT COUNT(*) FROM `user` WHERE `email`=?';
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($email));

        if ($res->fetchColumn()) {
            $retVal = true;
        } //endif

        return $retVal;
    } //endfunction emailExists

    function createUser($accountname, $email, $name, $surname, $password, $admin) {
        $res = $this->dbcon->prepare("INSERT INTO `user` (`accountname`, `email`, `name`, `surname`, `password`, `admin`) VALUES (?, ?, ?, ?, ?, ?);");
        $res->execute(array($accountname, $email, $name, $surname, sha1($password), $admin));
    } //endfunction createUser

    function getUserByMail($email) {
        $res = $this->dbcon->prepare("SELECT * FROM `user` WHERE `email`=?;");
        $res->execute(array($email));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0]['id'])) {
            $retVal = array();
        } else {
            $retVal = $ret[0];
        } //endif

        return $retVal;
    } //endfunction getUserByMail

    function getUserById($id) {
        $res = $this->dbcon->prepare("SELECT * FROM `user` WHERE `id`=?;");
        $res->execute(array($id));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0]['id'])) {
            $retVal = array();
        } else {
            $retVal = $ret[0];
        } //endif

        return $retVal;
    } //endfunction getUserById

    function changeUserData($id, $accountname, $email, $name, $surname, $admin) {
        $sql = "UPDATE `user` SET `accountname`=?, `email`=?, `name`=?, `surname`=?, `admin`=? WHERE `id`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array($accountname, $email, $name, $surname, $admin, $id));
    } //endfunction changeUserData

    function updatePassword($id, $password) {
        $sql = "UPDATE `user` SET `password`=? WHERE `id`=?;";
        $res = $this->dbcon->prepare($sql);
        $res->execute(array(sha1($password), $id));
    } //endfunction updatePassword

    function getUsers() {
        $res = $this->dbcon->prepare("SELECT * FROM `user`;");
        $res->execute();
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0]['id'])) {
            $retVal = array();
        } else {
            $retVal = $ret;
        } //endif

        return $retVal;
    } //endfunction

} //endclass UserHelper