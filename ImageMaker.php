<?php
/**
 * ImageMaker Class File
 *
*/

/**
 * @author Blade Lin <blade_lin@hotmail.com>
 * @version $Id$
 * @package utility tools
 * @since 1.0
 *
 * Default Config variables.
 * you can reset the variable of array when you initlized the class
 * or using object properties directly.
 *
 * quality             = '90';
 * createThumb         = false;       //using subname to new file?
 * thumbMarker         = '_thumb';    //new file subname postfix notation
 * // Watermark
 * wmText              = 'Hello World';// Watermark text if graphic is not used
 * wmType              = 'text';       // Type of watermarking.  Options:  text/overlay
 * wmFontPath          = 'fonts/GOTHICB.TTF';// TT font
 * wmFontSize          = 90;           // Font size (different versions of GD will either use points or pixels)
 * wmFontColor         = 'FFFFFF'; // Text color
 * // Watermark Shadow
 * wmVrtAlign          = 'B';          // Vertical alignment:   T M B
 * wmHorAlign          = 'R';          // Horizontal alignment: L R C
 * wmPadding           = 0;            // Padding around text
 * wmHorOffset         = 0;            // Lets you push text to the right
 * wmVrtOffset         = 0;            // Lets you push text to the top
 * wmDropShadow        = true;
 * wmShadowColor       = '999999';     // Dropshadow color
 * wmShadowDistance    = 4;            // Dropshadow distance
**/

include ("AImageMaker.php");

class ImageMaker extends AImageMaker {
    public $debug = true;

    public function __construct($config=array()) {
        if(count($config)>0) {
        	foreach($config as $key => $val) {
                $this->$key=$val;
            }
        }

        $this->setImgInfo();
        $this->debug(0,$this,"print_r");
    }

    public function setImgInfo() {
        if($this->srcImg != '') {
            $this->srcFullPath = $this->srcFolder.$this->srcImg;
            if($this->createThumb) {
                $xp = $this->explodeName($this->srcImg);
                $filename = $xp['name'];
                $fileExt = $xp['ext'];
                $this->destFullPath = $this->destFolder.$filename.$this->thumbMarker.$fileExt;
            }
            elseif($this->newImg!='')
                $this->destFullPath = $this->destFolder.$this->newImg;
            else {
                $this->destFullPath = $this->destFolder.$this->srcImg;
            }

            if(is_file($this->srcFullPath)) {
                $vals = getimagesize($this->srcFullPath);
                $types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
                $mime = (isset($types[$vals['2']])) ? 'image/'.$types[$vals['2']] : 'image/jpg';
                /*
                if ($return == TRUE) {
                    $v['width']            = $vals['0'];
                    $v['height']        = $vals['1'];
                    $v['imageType']    = $vals['2'];
                    $v['sizeStr']        = $vals['3'];
                    $v['mimeType']        = $mime;
                    return $v;
                }*/
                $this->origWidth    = $vals['0'];
                $this->origHeight    = $vals['1'];
                $this->imageType    = $vals['2'];
                $this->sizeStr        = $vals['3'];
                $this->mimeType    = $mime;
                return true;
            }
        }
    }

    public function getColor(&$img,$color = "000000") {
        $color = str_replace('#', '', $color);
        if ($color != '' && (strlen($color) == 6)) {
            $R = hexdec(substr($color, 0, 2));
            $G = hexdec(substr($color, 2, 2));
            $B = hexdec(substr($color, 4, 2));
            $color    = imagecolorclosest($img, $R, $G, $B);
            return $color;
        }
    }

    public function createImg($path = '', $type = '') {
        if ($path === '')
            $path = $this->srcFullPath;
        if ($type === '')
            $type = $this->imageType;
        switch ($type) {
            case 1 :
                if ( ! function_exists('imagecreatefromgif')) {
                    //$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_gif_not_supported'));
                    return FALSE;
                }
                return imagecreatefromgif($path);
                break;
            case 2 :
                if ( ! function_exists('imagecreatefromjpeg')) {
                    //$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_jpg_not_supported'));
                    return FALSE;
                }
                return imagecreatefromjpeg($path);
                break;
            case 3 :
                if ( ! function_exists('imagecreatefrompng')){
                    //$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_png_not_supported'));
                    return FALSE;
                }
                return imagecreatefrompng($path);
                break;
        }
        return FALSE;
    }

    public function render($res) {
        switch ($this->imageType) {
            case 1 : //GIF
                if ( ! @imagegif($res, $this->destFullPath)) {
                    $this->setError('imglib_save_failed');
                    return FALSE;
                }
                break;
            case 2 : //JPG
                if ( ! @imagejpeg($res, $this->destFullPath, $this->quality)) {
                    $this->setError('imglib_save_failed');
                    return FALSE;
                }
                break;
            case 3 : //PNG
                if ( ! @imagepng($res, $this->destFullPath)) {
                    $this->setError('imglib_save_failed');
                    return FALSE;
                }
                break;
            default    :
                $this->setSError(array('imglib_unsupported_imagecreate'));
                    return FALSE;
                break;
        }
        return TRUE;
    }



    public function watermarkText($imgText='') {
        $image = $this->createImg($this->srcFolder.$this->srcImg) ;
        $imgText!='' ? $text = $imgText : $text = $this->wmText;
        $font = $this->wmFontPath;
        $color = $this->getColor($image,$this->wmFontColor);
        $shadow = $this->getColor($image,$this->wmShadowColor);
        $fontSize = $this->wmFontSize;

        //caculate the image width, height to define type of position.
        if ($this->wmVrtAlign == 'B')
            $this->wmVrtOffset = $this->wmVrtOffset * -1;

        if ($this->wmHorAlign == 'R')
            $this->wmHorOffset = $this->wmHorOffset * -1;

        if ($this->wmTruetype == TRUE) {
            if ($this->wmFontSize == '')
                $this->wmFontSize = '17';
            $fontwidth  = $this->wmFontSize-($this->wmFontSize/4);
            $fontheight = $this->wmFontSize;
            $this->wmVrtOffset += $this->wmFontSize;
        }
        /*
        else
        {
            $fontwidth  = imagefontwidth($this->wmFontSize);
            $fontheight = imagefontheight($this->wmFontSize);
        }
        */
        // Set base X and Y axis values
        $xAxis = $this->wmHorOffset + $this->wmPadding;
        $yAxis = $this->wmVrtOffset + $this->wmPadding;

        // Set verticle alignment
        if ($this->wmDropShadow == FALSE)
            $this->wmShadowDistance = 0;
        //debug(2,$this->wmHorAlign);
        $this->wmVrtAlign = strtoupper(substr($this->wmVrtAlign, 0, 1));
        $this->wmHorAlign = strtoupper(substr($this->wmHorAlign, 0, 1));
        switch ($this->wmVrtAlign)
        {
            case "T" :
                break;
            case "M":    $yAxis += ($this->origHeight/2)+($fontheight/2);
                break;
            case "B":    $yAxis += ($this->origHeight - $fontheight - $this->wmShadowDistance - ($fontheight/2));
                break;
        }

        $xShad = $xAxis + $this->wmShadowDistance;
        $yShad = $yAxis + $this->wmShadowDistance;

        // Set horizontal alignment
        switch ($this->wmHorAlign) {
            case "L":
                break;
            case "R":
                if ($this->wmDropShadow)
                    $xShad += ($this->origWidth - $fontwidth*strlen($text));
                    $xAxis += ($this->origWidth - $fontwidth*strlen($text));
                break;
            case "C":
                if ($this->wmDropShadow)
                    $xShad += floor(($this->origWidth - $fontwidth*strlen($text))/2);
                    $xAxis += floor(($this->origWidth  -$fontwidth*strlen($text))/2);
                break;
        }
        imagettftext($image,$fontSize, 0, $xShad, $yShad, $shadow, $font, $text);
        imagettftext($image,$fontSize, 0, $xAxis, $yAxis, $color,$font, $text);
        $this->render($image);
        imagedestroy($image);
    }

    //I think It's necessary to add below functions in the furture.
    public function rotate() {}
    public function resize() {}
    public function crop() {}
    public function watermarkImg() {}
}

class ImageBatchMaker extends ImageMaker {

    public function drawText2ImgbyFolder($folderPath='') {
        $fileList=array('png','jpg','gif');
        if(empty($folderPath))
            $folderPath = $this->srcFolder;
        if(is_dir($folderPath)) {
            $handle = opendir($folderPath);
            while ($file = readdir($handle)) {
                if($file != '.' && $file !='..' && is_dir($folderPath.$file)) {
                    //$this->debug(0,$folderPath.$file.'/');
                    $this->drawText2ImgbyFolder($folderPath.$file.'/');
                }
                if($file != '.' && $file !='..' && in_array(strtolower(substr($file,-3,3)),$fileList)) {
                    if(is_file($folderPath.$file)) {
                        $this->srcFolder = $folderPath.'/';
                        $this->srcImg = $file;
                        $this->debug(1,$folderPath.$file);
                        $this->setImgInfo();
                        $this->watermarkText();
                        //$this->debug(0,$this->srcImg);

                    }
                }
            }//while
        } else
            echo sprintf("\nThe \"%s\" isn't exists, check it plz.\n ",$this->srcFolder);
    }
}
?>
