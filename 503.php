<?php
function __autoload($classfile) {
    if ($classfile != "" && is_file("classes/".$classfile.".php")) {
        require "classes/".$classfile.".php";
    } //endif
} //endfunction autoload

session_start();

$img = "dist/img/nocon_320.jpg";
header ('Content-Type: image/jpeg');
readfile($img);

?>
