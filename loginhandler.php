<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
} //endif
if (!isset($_SESSION['user_id'])) {
    header("Location: /");
    exit();
} //endif
if (!isset($usertype)) {
    if ($_SESSION['user_admin'] == '2') {
        $usertype = "Superadmin";
    } elseif ($_SESSION['user_admin'] == '1') {
        $usertype = "Administrator";
    } else {
        $usertype = "Benutzer";
    } //endif
} //endif