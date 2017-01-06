<?php

class getImagePath {

    public static function getOneimagePath($id) {
        $targetDir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'banner' . DIRECTORY_SEPARATOR . $id;
        $target_files = sfFinder::type('file')->in($targetDir);
        $pos = stripos($target_files[0], "upload");
        $final = substr($target_files[0], $pos);
        return $final;
    }

    public static function getImageToken($file) {
        $pos1 = strrpos($file, "/");
        $pos2 = strrpos($file, ".");
        $final = substr($file, $pos1 + 1, 32);
        return $final;
    }

}
