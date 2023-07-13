<?php

class Validator {
    private $lxclean;

    function __construct() {
        $this->lxclean = new lx_externalinput_clean();
    } //endconstructor

    function userExists($username) {
        $user = rawurldecode($username);
        $dbcon = PDOConnection::getPdoConnection();
        $sql = 'SELECT COUNT(*) FROM `user` WHERE `accountname`=?';
        $res = $dbcon->prepare($sql);
        $res->execute(array($user));

        if($res->fetchColumn()) {
            return true;
        } else {
            return false;
        } //endif
    } //endfunction userExists

    function mailExists($address) {
        $mail = rawurldecode($address);
        $dbcon = PDOConnection::getPdoConnection();
        $sql = 'SELECT COUNT(*) FROM `user` WHERE `email`=?';
        $res = $dbcon->prepare($sql);
        $res->execute(array($mail));

        if($res->fetchColumn()) {
            return true;
        } else {
            return false;
        } //endif
    } //endfunction mailExists

    function isValidStdString($val) {
        if (strlen($val) >= 1 && strlen($val) <= 32) {
            return true;
        } else {
            return false;
        } //endif
    } //endfunction isValidStdString

    function isValidPassword($pass) {
        $pattern = '/^[A-Za-z0-9_\-.:!?*+#&%§<>üäöÜÄÖß]+$/i';
        if (strlen($pass) >= 6 && strlen($pass) <= 32 &&
            preg_match($pattern, $pass)) {
            return true;
        } else {
            return false;
        } //endif
    } //endfunction isValidPassword

    function isValidMailAddress($addr) {
        if (!filter_var($addr, FILTER_VALIDATE_EMAIL) || strlen($addr) > 64) {
            return false;
        } else {
            return true;
        } //endif
    } //endfunction isValidMailAddress

    function isValidName($name) {
        $pattern = '/^[a-zA-ZÄäÖöÜüÀÁáÂâÈèÉéÊêÙùÚúßÇç]+$/i';
        if (preg_match($pattern, $name) && $this->isValidStdString($name)) {
            return true;
        } else {
            return false;
        } //endif
    } //endfunction isValidName

    function isValidUsername($uname) {
        $pattern = '/^[a-zA-Z0-9_\-]+$/i';
        if (preg_match($pattern, $uname) && $this->isValidStdString($uname)) {
            return true;
        } else {
            return false;
        } //endif
    } //endfunction isValidUsername

    function xssProtect($data) {
        return $this->lxclean->basic($data);
    } //endfunction cssProtect

    function isValidPort($port) {
        if ($port != '' &&
            is_numeric($port) &&
            intval($port) > 0 &&
            intval($port) <= 65535) {
            return true;
        } //endif

        return false;
    } //endfunction isValidPort

} //endclass Validator