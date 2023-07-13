<?php
session_start();

function __autoload($classfile) {
    if ($classfile != "" && is_file("../classes/".$classfile.".php")) {
        require "../classes/".$classfile.".php";
    } //endif
} //endfunction autoload

if (isset($_GET['getfolderlist']) && $_GET['getfolderlist'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    $dircontent = array_diff(scandir(Config::$FTPROOT), array('..', '.'));
    $retval = array();
    $dir = Config::$FTPROOT;
    if (mb_substr($dir, -1) != '/') {
        $dir .= '/';
    } //endif
    foreach ($dircontent as $cont) {
        if (is_dir($dir.$cont)) {
            if (strpos($dir.$cont,'..') !== false) {
                echo 'FEHLER: Ungültige Pfadangabe.';
                exit();
            } //endif
            array_push($retval, $cont);
        } //endif
    } //endforeach

    usort($retval, function($a, $b) {
        $date1 = DateTime::createFromFormat('j-m-y', $a);
        $date2 = DateTime::createFromFormat('j-m-y', $b);
        if ($date1 == $date2) {
            return 1;
        } //endif

        return ($date1 < $date2) ? 1 : -1;
    });

    echo json_encode($retval);
} else if (isset($_GET['getimglist']) && $_GET['getimglist'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    $dir = Config::$FTPROOT;
    if (mb_substr($dir, -1) != '/') {
        $dir .= '/';
    } //endif
    if (!isset($_POST['foldername']) || !is_dir($dir.$_POST['foldername'])) {
        echo "FEHLER: Ungültiger Verzeichnisname.";
        exit();
    } //endif
    $dir .= $_POST['foldername'];
    if (strpos($dir,'..') !== false) {
        echo 'FEHLER: Ungültige Pfadangabe.';
        exit();
    } //endif

    $dircontent = array_diff(scandir($dir), array('..', '.'));

    echo json_encode($dircontent);
} else if (isset($_GET['delfolder']) && $_GET['delfolder'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_POST['name']) || $_POST['name'] == '') {
        echo "FEHLER: Keinen Ordnernamen übergeben.";
        exit();
    } //endif

    $dir = Config::$FTPROOT;
    if (mb_substr($dir, -1) != '/') {
        $dir .= '/';
    } //endif
    $dir .= $_POST['name'];
    if (!is_dir($dir)) {
        echo "0";
        exit();
    } //endif

    if (strpos($dir,'..') !== false) {
        echo 'FEHLER: Ungültige Pfadangabe.';
        exit();
    } //endif

    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
        RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        } //endif
    } //endforeach
    rmdir($dir);

    echo "1";
} else if (isset($_GET['delfolders']) && $_GET['delfolders'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_POST['names']) || !is_array($_POST['names'])) {
        echo "FEHLER: Keine Ordnernamen übergeben.";
        exit();
    } //endif

    $folders = $_POST['names'];
    if (count($folders) == 0) {
        echo "FEHLER: Ungültiges Format.";
        exit();
    } //endif

    $basedir = Config::$FTPROOT;
    if (mb_substr($basedir, -1) != '/') {
        $basedir .= '/';
    } //endif
    foreach ($folders as $folder) {
        $dir = $basedir . $folder;
        if (strpos($dir,'..') !== false) {
            echo 'FEHLER: Ungültige Pfadangabe.';
            exit();
        } //endif
        if (is_dir($dir)) {
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir()){
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                } //endif
            } //endforeach
            rmdir($dir);
        } //endif
    } //endforeach

    echo "1";
} else if (isset($_GET['getfolderzip']) && $_GET['getfolderzip'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_POST['name']) || $_POST['name'] == '') {
        echo "FEHLER: Keinen Ordnernamen übergeben.";
        exit();
    } //endif

    if (strpos($_POST['name'],'..') !== false) {
        echo 'FEHLER: Ungültige Pfadangabe.';
        exit();
    } //endif

    $filehelper = new FileHelper();
    $folder = array();
    array_push($folder, $_POST['name']);
    $fnraw = $filehelper->getZipArchive($folder);
    if (is_numeric($fnraw)) {
        echo "FEHLER: Zip Archiv konnte nicht erstellt werden.";
        exit();
    } //endif

    echo $fnraw;
} else if (isset($_GET['getfolderszip']) && $_GET['getfolderszip'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_POST['names']) || !is_array($_POST['names'])) {
        echo "FEHLER: Keine Ordnernamen übergeben.";
        exit();
    } //endif

    $folders = $_POST['names'];
    if (count($folders) == 0) {
        echo "FEHLER: Ungültiges Format.";
        exit();
    } //endif

    foreach ($folders as $fldr) {
        if (strpos($fldr, '..') !== false) {
            echo 'FEHLER: Ungültige Pfadangabe.';
            exit();
        } //endif
    } //endforeach

    $filehelper = new FileHelper();
    $fnraw = $filehelper->getZipArchive($folders);

    if (is_numeric($fnraw)) {
        echo "FEHLER: Zip Archiv konnte nicht erstellt werden.";
        exit();
    } //endif

    echo $fnraw;
} else if (isset($_GET['getdelzip']) && $_GET['getdelzip'] != '') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    $basedir = Config::$FTPROOT;
    if (mb_substr($basedir, -1) != '/') {
        $basedir .= '/';
    } //endif
    $fname = 'desecimgs_'.$_GET['getdelzip'].'.zip';

    if (strpos($fname, '..') !== false) {
        echo 'FEHLER: Ungültige Pfadangabe.';
        exit();
    } //endif

    if (!is_file($basedir.$fname)) {
        echo "FEHLER: Die angeforderte Datei existiert nicht.";
        exit();
    } //endif

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"".$fname."\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($basedir.$fname));
    ob_end_flush();
    readfile($basedir.$fname);
    unlink($basedir.$fname);
} else if (isset($_GET['delimage']) && $_GET['delimage'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_POST['name']) || $_POST['name'] == '' || strpos($_POST['name'], '/') !== false) {
        echo "FEHLER: Keinen Dateinamen übergeben.";
        exit();
    } //endif
    if (!isset($_POST['folder']) || $_POST['folder'] == '' || strpos($_POST['folder'], '/') !== false) {
        echo "FEHLER: Keinen Dateinamen übergeben.";
        exit();
    } //endif

    $dir = Config::$FTPROOT;
    if (mb_substr($dir, -1) != '/') {
        $dir .= '/';
    } //endif
    $dir .= $_POST['folder'];
    if (!is_dir($dir)) {
        echo "0";
        exit();
    } //endif
    $file = $dir . '/' . $_POST['name'];

    if (strpos($file, '..') !== false || !is_file($file)) {
        echo 'FEHLER: Ungültiger Dateiname.';
        exit();
    } //endif

    unlink($file);

    echo "1";
} else if (isset($_GET['delimages']) && $_GET['delimages'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_POST['names']) || !is_array($_POST['names'])) {
        echo "FEHLER: Keine Ordnernamen übergeben.";
        exit();
    } //endif

    $files = $_POST['names'];
    if (count($files) == 0) {
        echo "FEHLER: Ungültiges Format.";
        exit();
    } //endif

    $basedir = Config::$FTPROOT;
    if (mb_substr($basedir, -1) != '/') {
        $basedir .= '/';
    } //endif
    foreach ($files as $file) {
        $f = $basedir . $file;
        if (strpos($f, '..') !== false || !is_file($f)) {
            echo 'FEHLER: Ungültiger Dateiname.';
            exit();
        } //endif

        unlink($f);
    } //endforeach

    echo "1";
} else if (isset($_GET['getzippedfiles']) && $_GET['getzippedfiles'] == '1') {
    if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
        echo "FEHLER: Nicht eingeloggt.";
        exit();
    } //endif

    if (!isset($_POST['names']) || !is_array($_POST['names'])) {
        echo "FEHLER: Keine Dateinamen übergeben.";
        exit();
    } //endif

    $files = $_POST['names'];
    if (count($files) == 0) {
        echo "FEHLER: Ungültiges Format.";
        exit();
    } //endif

    foreach ($files as $f) {
        if (strpos($f, '..') !== false) {
            echo 'FEHLER: Ungültige Pfadangabe.';
            exit();
        } //endif
    } //endforeach

    $filehelper = new FileHelper();
    $fnraw = $filehelper->getFilesZipArchive($files);

    if (is_numeric($fnraw)) {
        echo "FEHLER: Zip Archiv konnte nicht erstellt werden.";
        exit();
    } //endif

    echo $fnraw;
} //endif

?>