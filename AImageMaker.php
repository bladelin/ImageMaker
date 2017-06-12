<?php
/**
 * AImageMaker Class File
 *
 * @author Jun Lin
 * @copyright Copyright &copy; 2010-2011.
 *
*/

abstract class AImageMaker {
    public $debug                = false;

    public $srcImg               = '';
    public $newImg               = '';
    public $width                = '';
    public $height               = '';
    public $quality              = '90';
    public $createThumb          = false;       //using subname to new file?
    public $thumbMarker          = '_thumb';    //new file subname postfix notation

    // Watermark publics
    public $wmText               = 'Hello World';// Watermark text if graphic is not used
    public $wmType               = 'text';        // Type of watermarking.  Options:  text/overlay
    public $wmFontPath           = 'fonts/GOTHICB.TTF';// TT font
    public $wmFontSize           = 90;            // Font size (different versions of GD will either use points or pixels)
    public $wmFontColor          = 'FFFFFF';    // Text color

    public $wmOverlayPath        = '';            // Watermark image path
    public $wmVrtAlign           = 'B';            // Vertical alignment:   T M B
    public $wmHorAlign           = 'R';            // Horizontal alignment: L R C
    public $wmPadding            = 0;            // Padding around text
    public $wmHorOffset          = 0;            // Lets you push text to the right
    public $wmVrtOffset          = 0;            // Lets you push text to the top
    public $wmDropShadow         = true;
    public $wmShadowColor        = '999999';        // Dropshadow color
    public $wmShadowDistance     = 2;            // Dropshadow distance
    public $wmTruetype           = true;

    //private Image source info
    public $srcFolder            = '';
    public $destFolder           = '';
    public $mimeType             = '';
    public $origWidth            = '';
    public $origHeight           = '';
    public $imageType            = '';

    public $srcfullPath          = '';
    public $destfullPath         = '';


    public function __set($key,$val) {
        $this->$key = $val;
    }

    public function explodeName($srcImg) {
        $ext = strrchr($srcImg, '.');
        $name = ($ext === FALSE) ? $srcImg : substr($srcImg, 0, -strlen($ext));
        return array('ext' => $ext, 'name' => $name);
    }

    public function debug($flag = 0 , $var = '', $cmd ='') {
        if($this->debug && $flag>0) {
            if(!empty($var)) {
                switch($cmd) {
                    case 'print_r': print_r($var); break;
                    default :
                        if(is_array($var)) print_r($var);
                        else echo ($var);
                    break;
                }
            }
            echo "\n";
        }
        if($flag===2) die("Interrupt...\n");
    }

    public function setError() {}
}

?>
