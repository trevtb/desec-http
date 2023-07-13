<?php

class FileHelper {
    function getZipArchive($folders) {
        $basedir = Config::$FTPROOT;
        if (mb_substr($basedir, -1) != '/') {
            $basedir .= '/';
        } //endif

        $traw = tempnam($basedir, '');
        unlink($traw);
        $traw = explode('/', $traw);
        $traw = $traw[count($traw)-1];
        $zip_file_name = $basedir.'desecimgs_' . $traw . '.zip';
        $za = new FlxZipArchive;
        $res = $za->open($zip_file_name, ZipArchive::CREATE);
        if ($res === TRUE) {
            foreach ($folders as $folder) {
                $folder = $basedir.$folder;
                if (strpos($folder, '..') !== false || mb_substr($folder, -1) == '/') {
                    return 0;
                } //endif
                $za->addDir($folder, basename($folder));
            } //endforeach
            $za->close();
        } else {
            return 0;
        } //endif

        return $traw;
    } //endfunction getZipArchive

    function getFilesZipArchive($files) {
        $basedir = Config::$FTPROOT;
        if (mb_substr($basedir, -1) != '/') {
            $basedir .= '/';
        } //endif

        $traw = tempnam($basedir, '');
        unlink($traw);
        $traw = explode('/', $traw);
        $traw = $traw[count($traw)-1];
        $zip_file_name = $basedir.'desecimgs_' . $traw . '.zip';
        $za = new FlxZipArchive;
        $res = $za->open($zip_file_name, ZipArchive::CREATE);
        if ($res === TRUE) {
            foreach ($files as $f) {
                $file = $basedir.$f;
                if (strpos($file, '..') !== false || mb_substr($file, -1) == '/') {
                    return 0;
                } //endif
                $za->addFile($file, basename($file));
            } //endforeach
            $za->close();
        } else {
            return 0;
        } //endif

        return $traw;
    } //endfunction getFilesZipArchive

} //endclass FileHelper