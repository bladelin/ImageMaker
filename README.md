# ImageMaker
Package a image maker for utility called

```
php goImageMaker.php
```
> check source and target folder permission before you do 

```
<?php

include("ImageMaker.php");

if(1) {
    $sourceFolder = '/home/test/src';
    $targetFolder = '/home/test/target';

    $config = array(
        # You can add the variable of string into $config array to setting and adjust something.
        # To know what else config you can adjust, please check AImageMaker.php for more information.
        'wmText' => 'Copyright © SMAP Technologies Inc.',
        'wmFontPath' => 'fonts/msjhbd.ttf',
        'srcFolder' => $sourceFolder,
        'destFolder' => $targetFolder,
        'wmShadowDistance' => 4,
        'wmFontSize' => 16,
        'wmShadowColor' => '#222222',
        'wmHorAlign' => 'R',
        'wmVrtAlign' => 'B',
        'wmVrtOffset' => 10,
        'wmHorOffset' => 10,
    );
    $Im = new ImageBatchMaker($config);
    $Im->drawText2ImgbyFolder();
}
