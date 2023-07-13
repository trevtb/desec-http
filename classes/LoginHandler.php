<?php

class LoginHandler {
    public function doLogin($data) {
        $_SESSION['user_email'] = $data['email'];
        $_SESSION['user_accountname'] = $data['accountname'];
        $_SESSION['user_name'] = $data['name'];
        $_SESSION['user_surname'] = $data['surname'];
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['user_admin'] = $data['admin'];

        $pdocon = PDOConnection::getPdoConnection();
        $res = $pdocon->prepare("UPDATE `user` SET `lastonline`=NOW() WHERE `id`=?;");
        $res->execute(array($data['id']));

        if (isset($_SESSION['pagelink'])) {
            $tgt = $_SESSION['pagelink'];
        } else {
            $tgt = '/home';
        } //endif
        header("Location: ".$tgt);
        exit();
    } //endfunction doLogin
} //endclass LoginHandler