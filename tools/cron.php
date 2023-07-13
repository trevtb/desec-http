<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("/var/www/classes/".$classfile.".php")) {
        require "/var/www/classes/".$classfile.".php";
    } //endif
} //endfunction autoload

if (PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) {
    echo "FEHLER: Nur CLI erlaubt.";
    exit();
} //endif

$dircontent = array_diff(scandir(Config::$FTPROOT), array('..', '.'));
$basedir = Config::$FTPROOT;
$now   = time();
if (mb_substr($basedir, -1) != '/') {
    $basedir .= '/';
} //endif
foreach ($dircontent as $cont) {
    $file = $basedir.$cont;
    if (!is_dir($file) && is_file($file)) {
        if ($now - filemtime($file) >= 60*60*24) {
            unlink($file);
        } //endif
    } //endif
} //endforeach

$dbcon = PDOConnection::getPdoConnection();
$camhelper = new CamHelper();
$creating = $camhelper->isCreatingCams();

if ($creating != '1') {
    echo "DESECCORE: Nichts zu tun.\n";
    exit();
} //endif

function getHeader() {
    $retVal = '<VirtualHost *:80>
    ServerAdmin webmaster@localhost

    DocumentRoot /var/www
    <Directory />
        Options FollowSymLinks
        AllowOverride None
    </Directory>
    <Directory /var/www>
        Options FollowSymLinks
        AllowOverride All
        Order deny,allow
        Allow from all
    </Directory>

    ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/
    <Directory "/usr/lib/cgi-bin">
        AllowOverride None
        Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
        Order allow,deny
        Allow from all
    </Directory>

    <Location /cam>
        Satisfy any
        AuthType Basic
        AuthName \'Protected area\'
        AuthUserFile \''.Config::$HTPASSWDFILE.'\'
        Require valid-user
        Order deny,allow
        Deny from all
        Allow from '.Config::$IP.'
    </Location>

    SSLProxyEngine on
    ';

    return $retVal;
} //endfunction getHeader

function getProxies($cams) {
    $retVal = '';

    foreach ($cams as $cam) {
        if ($cam['ssl'] == '1') {
            $urlstart = 'https://';
        } else {
            $urlstart = 'http://';
        } //endif

        $retVal .= '
    <Location /cam/'.$cam['id'].'>
        ErrorDocument 503 /503.php
        ProxyPass '.$urlstart.$cam['host'].':'.$cam['port'].$cam['path'].'
        ProxyPassReverse '.$urlstart.$cam['host'].':'.$cam['port'].$cam['path'].'
        SetOutputFilter proxy-html
        RequestHeader unset Accept-Encoding';
        if ($cam['auth'] == '1') {
            $retVal .= '
        RequestHeader set Authorization "Basic '.base64_encode($cam['user'].':'.$cam['password']).'"';
        } //endif

        $retVal .= '
    </Location>';
    } //endforeach

    return $retVal;
} //endfunction getProxies

function getFooter() {
    $retVal = '

    ErrorLog ${APACHE_LOG_DIR}/error.log

    # Possible values include: debug, info, notice, warn, error, crit,
    # alert, emerg.
    LogLevel warn

    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
    ';

    return $retVal;
} //endfunction getFooter

$res = $dbcon->prepare("SELECT * FROM `camera`;");
$res->execute();
$cams = $res->fetchAll(PDO::FETCH_ASSOC);

$filecontent = getHeader() . getProxies($cams) . getFooter();
file_put_contents ("/etc/apache2/sites-available/default", $filecontent);

$auth = $camhelper->updateHttpAuth();
$httpwd = $auth['user'].':'.crypt($auth['pass'], base64_encode($auth['pass']));
file_put_contents(Config::$HTPASSWDFILE, $httpwd);

$output = shell_exec("service apache2 reload");

foreach ($cams as $cam) {
    $res = $dbcon->prepare("UPDATE `camera` SET `created`=? WHERE `id`=?;");
    $res->execute(array(1, $cam['id']));
} //endforeach
$camhelper->triggerVHostRewrite(false);

echo "DESECCORE: Die Apache VHost Konfiguration wurde neu erstellt.\n";
?>