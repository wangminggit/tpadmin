<?php

class ImageUtils {

    public static function resize($img, $thumb_width, $thumb_height=null, $newfilename=null) {
        $max_width=$thumb_width;

        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2'))
        {
            trigger_error("GD is not loaded", E_USER_WARNING);
            return false;
        }

        //Get Image size info
        list($width_orig, $height_orig, $image_type) = getimagesize($img);

        switch ($image_type)
        {
            case 1: $im = imagecreatefromgif($img); break;
            case 2: $im = imagecreatefromjpeg($img);  break;
            case 3: $im = imagecreatefrompng($img); break;
            default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
        }

        /*** calculate the aspect ratio ***/
        $aspect_ratio = (float) $height_orig / $width_orig;

        /*** calulate the thumbnail width based on the height ***/
        if(!isset($thumb_height)){
            $thumb_height = round($thumb_width * $aspect_ratio);
        }

        $newImg = imagecreatetruecolor($thumb_width, $thumb_height);

        /* Check if this image is PNG or GIF, then set if Transparent*/
        if(($image_type == 1) OR ($image_type==3) OR ($image_type==4))
        {
            imagealphablending($newImg, false);
            imagesavealpha($newImg,false);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $thumb_width, $thumb_height, $transparent);
        }
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);

        //Generate the file, and rename it to $newfilename
        switch ($image_type)
        {
            case 1: imagegif($newImg,$newfilename); break;
            case 2: imagejpeg($newImg,$newfilename);  break;
            case 3: imagepng($newImg,$newfilename); break;
            case 4: imageico($newImg,$newfilename); break;
            default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
        }
        
        return $newfilename;
    }
    
    public static function resize_copy($img, $thumb_width, $thumb_height=null, $newfilename=null, $src_x=0, $src_y=0, $org_width=null, $org_height=null) {
        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2'))
        {
            trigger_error("GD is not loaded", E_USER_WARNING);
            return false;
        }

        //Get Image size info
        list($width_orig, $height_orig, $image_type) = getimagesize($img);

        switch ($image_type)
        {
            case 1: $im = imagecreatefromgif($img); break;
            case 2: $im = imagecreatefromjpeg($img);  break;
            case 3: $im = imagecreatefrompng($img); break;
            default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
        }

        /*** calculate the aspect ratio ***/
        $aspect_ratio = (float) $height_orig / $width_orig;

        /*** calulate the thumbnail width based on the height ***/
        if(!isset($thumb_height)){
            $thumb_height = round($thumb_width * $aspect_ratio);
        }

        $newImg = imagecreatetruecolor($thumb_width, $thumb_height);

        /* Check if this image is PNG or GIF, then set if Transparent*/
        if(($image_type == 1) OR ($image_type==3) OR ($image_type==4))
        {
            imagealphablending($newImg, false);
            imagesavealpha($newImg,false);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $thumb_width, $thumb_height, $transparent);
        }
        imagecopyresampled($newImg, $im, 0, 0, $src_x, $src_y, $thumb_width, $thumb_height, $org_width, $org_height);

        //Generate the file, and rename it to $newfilename
        switch ($image_type)
        {
            case 1: imagegif($newImg,$newfilename); break;
            case 2: imagejpeg($newImg,$newfilename);  break;
            case 3: imagepng($newImg,$newfilename); break;
            case 4: imageico($newImg,$newfilename); break;
            default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
        }
        
        return $newfilename;
    }
    
    public static function tailor($img, $thumb_width, $thumb_height, $newfilename = null, $x1 = 0, $y1 = 0) {
        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2')) {
            trigger_error("GD is not loaded", E_USER_WARNING);
            return false;
        }

        //Get Image size info
        list($width_orig, $height_orig, $image_type) = getimagesize($img);

        switch ($image_type) {
            case 1: $im = imagecreatefromgif($img);
                break;
            case 2: $im = imagecreatefromjpeg($img);
                break;
            case 3: $im = imagecreatefrompng($img);
                break;
            default: trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }

        $newImg = imagecreatetruecolor($thumb_width, $thumb_height);

        /* Check if this image is PNG or GIF, then set if Transparent */
        if (($image_type == 1) OR ( $image_type == 3) OR ( $image_type == 4)) {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, false);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $thumb_width, $thumb_height, $transparent);
        }
        
        imagecopy($newImg, $im, 0, 0, $x1, $y1, $thumb_width, $thumb_height);

        //Generate the file, and rename it to $newfilename
        switch ($image_type) {
            case 1: imagegif($newImg, $newfilename);
                break;
            case 2: imagejpeg($newImg, $newfilename);
                break;
            case 3: imagepng($newImg, $newfilename);
                break;
            case 4: imageico($newImg, $newfilename);
                break;
            default: trigger_error('Failed resize image!', E_USER_WARNING);
                break;
        }

        return $newfilename;
    }
    
    public static function resize_orig($img, $thumb_width, $thumb_height, $newfilename=null, $fix_background=false){
        $thumb_final_width = $thumb_width;
        $thumb_final_height = $thumb_height;

        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2'))
        {
            trigger_error("GD is not loaded", E_USER_WARNING);
            return false;
        }

        //Get Image size info
        list($width_orig, $height_orig, $image_type) = getimagesize($img);

        switch ($image_type)
        {
            case 1: $im = imagecreatefromgif($img); break;
            case 2: $im = imagecreatefromjpeg($img);  break;
            case 3: $im = imagecreatefrompng($img); break;
            default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
        }

        /*** calculate the aspect ratio ***/
        $aspect_ratio_hw = (float) $height_orig / $width_orig;

        /****  要求图片的尺寸高宽等同的情况下  ****/
        if($thumb_final_width == $thumb_final_height && ($height_orig > $thumb_final_height || $width_orig > $thumb_final_width)){
            if($width_orig > $height_orig){
                $thumb_height = $thumb_final_height * $aspect_ratio_hw;
            }
            if($width_orig < $height_orig){
                $thumb_width = $thumb_final_width / $aspect_ratio_hw;
            }
            if($width_orig == $height_orig){
                $thumb_width = $thumb_final_width;
                $thumb_height = $thumb_final_height;
            }
        }else{
            $thumb_width = $width_orig;
            $thumb_height = $height_orig;
        }
        
        $newImg = imagecreatetruecolor($thumb_width, $thumb_height);

        /* Check if this image is PNG or GIF, then set if Transparent*/
        if(($image_type == 1) OR ($image_type==3) OR ($image_type==4))
        {
            imagealphablending($newImg, false);
            imagesavealpha($newImg,false);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $thumb_width, $thumb_height, $transparent);
        }
        
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);

        /* 对已生成的比例图嵌入到规格尺寸背景图片中 */
        if($fix_background){
            $newImgFinal = imagecreatetruecolor($thumb_final_width, $thumb_final_height);
            if(($image_type == 1) OR ($image_type==3) OR ($image_type==4))
            {
                imagealphablending($newImgFinal, false);
                imagesavealpha($newImgFinal,false);
                $transparent = imagecolorallocatealpha($newImgFinal, 255, 255, 255, 127);
                imagefilledrectangle($newImgFinal, 0, 0, $thumb_final_width, $thumb_final_height, $transparent);
            }

            /* 计算比例图在背景图中的坐标位置 */
            $dst_x = ceil(($thumb_final_width - $thumb_width) / 2);
            $dst_y = ceil(($thumb_final_height - $thumb_height) / 2);

            imagecopy($newImgFinal, $newImg, $dst_x, $dst_y, 0, 0, $thumb_width, $thumb_height);
            
            $newImg = $newImgFinal;
        }
        
        //Generate the file, and rename it to $newfilename
        switch ($image_type)
        {
            case 1: imagegif($newImg,$newfilename); break;
            case 2: imagejpeg($newImg,$newfilename);  break;
            case 3: imagepng($newImg,$newfilename); break;
            case 4: imageico($newImg,$newfilename); break;
            default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
        }
        
        return $newfilename;
    }

}
