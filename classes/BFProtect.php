<?php

class BFProtect {

    private $failcount = 5;
    private $timelimit = 900;

    function increment() {
        if (isset($_SESSION['login_fcount'])) {
            if (intval($_SESSION['login_fcount']) >= $this->failcount && !isset($_SESSION['login_ftstamp'])) {
                $_SESSION['login_ftstamp'] = time();
            } else {
                $_SESSION['login_fcount'] = intval($_SESSION['login_fcount']) + 1;
            } //endif
        } else {
            $_SESSION['login_fcount'] = 1;
        } //endif
    } //endfunction increment

    function reset() {
        unset($_SESSION['login_fcount']);
        unset($_SESSION['login_ftstamp']);
        unset($_SESSION['login_tleft']);
    } //endfunction reset

    function getTimeLimit() {
        return $this->timelimit;
    } //endfunction getTimeLimit

    function getTimePassed() {
        if (isset($_SESSION['login_ftstamp'])) {
            $tstamp = intval($_SESSION['login_ftstamp']);
            $current = time();
            $passed = $current - $tstamp;

            return $passed;
        } else {
            return -1;
        } //endif
    } //endfunction getTimePassed

} //endclass BFProtect