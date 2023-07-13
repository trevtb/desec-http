<?php

class WidgetHelper {
    private $dbcon = null;

    function __construct() {
        $this->dbcon = PDOConnection::getPdoConnection();
    } //endconstructor

    function getWidgetActive($name, $userid) {
        $res = $this->dbcon->prepare("SELECT `value` FROM `widget_settings` WHERE `name`=? AND `wname`=? AND `userid`=?");
        $res->execute(array('active', $name, $userid));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0])) {
            $this->setWidgetActive($name, $userid);
            return '1';
        } else {
            return $ret[0]['value'];
        } //endif
    } //endfunction getWidgetActive

    function setWidgetActive($name, $userid, $value=0) {
        $res = $this->dbcon->prepare("SELECT `value` FROM `widget_settings` WHERE `name`=? AND `wname`=? AND `userid`=?;");
        $res->execute(array('active', $name, $userid));
        $ret = $res->fetchAll(PDO::FETCH_ASSOC);
        if (!isset($ret[0])) {
            $res = $this->dbcon->prepare("INSERT INTO `widget_settings` (`userid`, `wname`, `name`, `value`) VALUES (?, ?, ?, ?);");
            $res->execute(array($userid, $name, 'active', $value));
        } else {
            $res = $this->dbcon->prepare("UPDATE `widget_settings` SET `value`=? WHERE `name`=? AND `wname`=? AND `userid`=?;");
            $res->execute(array($value, 'active', $name, $userid));
        } //endif
    } //endfunction setWidgetActive
} //endclass WidgetHelper

?>