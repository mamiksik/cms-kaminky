<?php
/**
 * @author Tomáš Blatný
 */

require __DIR__ . '/../vendor/autoload.php';

$ignored = array('.', '..');

foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/../temp/cache')) as $filename => $fileinfo) {
    if(!in_array($fileinfo->getFilename(), $ignored)) {
        echo "Removing $filename<br>";
        if(is_dir($filename)) {
            @rmdir($filename);
        } else {
            @unlink($filename);
        }
    }
}