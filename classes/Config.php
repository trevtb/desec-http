<?php

class Config {

    // DATABASE
    public static $DBHOST = 'localhost';
    public static $DBNAME = 'deseccore';
    public static $DBUSER = 'root';
    public static $DBPASS = 'hence_13_09hi';

    // ENCRYPTION AND AUTHENTICATION
    public static $PLOGINSALT = 'LOBO77990876';
    public static $HTPASSWDFILE = '/home/pi/.htpasswd';

    // GENERAL
    public static $IP = '192.168.37.8';
    public static $SSL = '0';

    // IMAGE GALLERY
    public static $FTPROOT = '/var/www/ftp';
} //endclass Config