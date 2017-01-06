<?php

class UtilsFile {

    public static function deleteDirectory($dir) {
        if (!file_exists($dir))
            return true;
        if (!is_dir($dir) || is_link($dir))
            return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..')
                continue;
            if (!self::deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!self::deleteDirectory($dir . "/" . $item))
                    return false;
            };
        }
        return rmdir($dir);
    }

    public static function deleteDirectoryFiles($dir) {
        if (!file_exists($dir))
            return true;
        if (!is_dir($dir) || is_link($dir))
            return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..')
                continue;
            if (!self::deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!self::deleteDirectory($dir . "/" . $item))
                    return false;
            };
        }
        return true;
    }

    public static function recurse_copy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst, 0777, true);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    self::recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Get the directory size
     * @param directory $directory
     * @return integer
     */
    public static function dirSize($directory) {
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            $size+=$file->getSize();
        }
        return $size;
    }

    public static function format_bytes($size) {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++)
            $size /= 1024;
        return round($size, 2) . $units[$i];
    }

    public static function format_kbytes($size) {
        $units = array(' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++)
            $size /= 1024;
        return round($size, 2) . $units[$i];
    }

    public static function dircopy($src_dir, $dst_dir, $UploadDate = false, $verbose = false, $use_cached_dir_trees = false) {
        static $cached_src_dir;
        static $src_tree;
        static $dst_tree;
        $num = 0;

        if (($slash = substr($src_dir, - 1)) == "\\" || $slash == "/")
            $src_dir = substr($src_dir, 0, strlen($src_dir) - 1);
        if (($slash = substr($dst_dir, - 1)) == "\\" || $slash == "/")
            $dst_dir = substr($dst_dir, 0, strlen($dst_dir) - 1);
        if (!$use_cached_dir_trees || !isset($src_tree) || $cached_src_dir != $src_dir) {
            $src_tree = self::get_dir_tree($src_dir, true, $UploadDate);
            $cached_src_dir = $src_dir;
            $src_changed = true;
        }
        if (!$use_cached_dir_trees || !isset($dst_tree) || $src_changed)
            $dst_tree = self::get_dir_tree($dst_dir, true, $UploadDate);
        if (!is_dir($dst_dir)) {
            @mkdir($dst_dir, 0777, true);
        }

        foreach ($src_tree as $file => $src_mtime) {
            if (!isset($dst_tree[$file]) && $src_mtime === false) {
                @ mkdir("$dst_dir/$file");
            } elseif (!isset($dst_tree[$file]) && $src_mtime || isset($dst_tree[$file]) && $src_mtime > $dst_tree[$file]) {
                if (copy("$src_dir/$file", "$dst_dir/$file")) {
                    if ($verbose)
                        echo "Copied '$src_dir/$file' to '$dst_dir/$file'<br>\r\n";
                    touch("$dst_dir/$file", $src_mtime);
                    $num++;
                } else
                    echo "<font color='red'>File '$src_dir/$file' could not be copied!</font><br>\r\n";
            }
        }
        return $num;
    }

    public static function get_dir_tree($dir, $root = true, $UploadDate) {
        static $tree;
        static $base_dir_length;

        if ($root) {
            $tree = array();
            $base_dir_length = strlen($dir) + 1;
        }

        if (is_file($dir)) {
            if ($UploadDate != false) {
                if (filemtime($dir) > strtotime($UploadDate))
                    $tree[substr($dir, $base_dir_length)] = date('Y-m-d H:i:s', filemtime($dir));
            } else
                $tree[substr($dir, $base_dir_length)] = date('Y-m-d H:i:s', filemtime($dir));
        }elseif ((is_dir($dir) && substr($dir, - 4) != ".svn") && $di = dir($dir)) {
            if (!$root)
                $tree[substr($dir, $base_dir_length)] = false;
            while (($file = $di->read()) !== false)
                if ($file != "." && $file != "..")
                    self::get_dir_tree("$dir/$file", false, $UploadDate);
            $di->close();
        }
        if ($root)
            return $tree;
    }

    public static function chmodR($path, $filemode, $dirmode) {
        if (is_dir($path)) {
            if (!chmod($path, $dirmode)) {
                $dirmode_str = decoct($dirmode);
                print "Failed applying filemode '$dirmode_str' on directory '$path'\n";
                print "  `-> the directory '$path' will be skipped from recursive chmod\n";
                return;
            }
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {  // skip self and parent pointing directories
                    $fullpath = $path . '/' . $file;
                    self::chmodR($fullpath, $filemode, $dirmode);
                }
            }
            closedir($dh);
        } else {
            if (is_link($path)) {
                print "link '$path' is skipped\n";
                return;
            }
            if (!chmod($path, $filemode)) {
                $filemode_str = decoct($filemode);
                print "Failed applying filemode '$filemode_str' on file '$path'\n";
                return;
            }
        }
    }

    public static function getImagesPathFromDir($dir) {
        $files = array();
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                        $dir2 = $dir . DIRECTORY_SEPARATOR . $file;
                        $files[] = self::getImagesPathFromDir($dir2);
                    } else {
                        if (in_array(self::getExtension($file), UtilsImage::getImageExts())) {
                            $files[] = $dir . DIRECTORY_SEPARATOR . $file;
                        }
                    }
                }
            }
            closedir($handle);
        }

        return self::array_flat($files);
    }

    public static function getOneLayerFiles($dir) {
        $files = array();
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $files[] = $dir . DIRECTORY_SEPARATOR . $file;
                }
            }
            closedir($handle);
        }
        return $files;
    }

    public static function array_flat($array) {
        $tmp = array();
        foreach ($array as $a) {
            if (is_array($a)) {
                $tmp = array_merge($tmp, self::array_flat($a));
            } else {
                $tmp[] = $a;
            }
        }
        return $tmp;
    }

    public static function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    public static function getFileName($file) {
        $i = strrpos($file, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($file) - $i;
        $file_name = substr($file, 0, $i);
        return $file_name;
    }

    public static function getFileMimeType($file) {
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $file);
            finfo_close($finfo);
        } else {
            require_once dirname(__FILE__) . './mime.php';
            $type = mime_content_type($file);
        }

        if (!$type || in_array($type, array('application/octet-stream', 'text/plain'))) {
            $secondOpinion = exec('file -b --mime-type ' . escapeshellarg($file), $foo, $returnCode);
            if ($returnCode === 0 && $secondOpinion) {
                $type = $secondOpinion;
            }
        }

        if (!$type || in_array($type, array('application/octet-stream', 'text/plain'))) {
            require_once dirname(__FILE__) . './mime.php';
            $exifImageType = exif_imagetype($file);
            if ($exifImageType !== false) {
                $type = image_type_to_mime_type($exifImageType);
            }
        }

        return $type;
    }

    public static function FileSize($file, $field) {
        $msg = "";
        $size = $file['size']['photo'];
        $extension = self::getExtension($file['name'][$field]);
        $allowType = array('jpg');
        if (!in_array($extension, $allowType)) {
            $msg = "图片类型错误";
        }
        if ($size > 307200) {
            $msg = $msg . " 图片太大";
        }
        return $msg;
    }

    public static function FileType($file, $field) {
        $i = strrpos($file['name'][$field], ".");
        $ext = substr($file['name'][$field], $i + 1);
        $allowType = array('jpg');
        if (!in_array($ext, $allowType)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public static function downloadfile($file, $filename) {
        $file_name = basename($filename);
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $encoded_filename = urlencode($file_name);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);
        header("Content-type: application/octet-stream");
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $file_name . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
        }
        
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));

        // Send the file contents.
        ob_end_flush();
        readfile($file);
    }

}
