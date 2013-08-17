<?php
/**
 * Developer: Rahul Kadyan
 * Date: 08/08/13
 * Time: 2:59 AM
 * Product: JetBrains PhpStorm
 * Copyright (C) 2013 Rahul Kadyan
 *  
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction, 
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, 
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
 * DEALINGS IN THE SOFTWARE.
 */
if(!defined('xDEC')) exit;
?>
<div style=" webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;">
    <div style="z-index: 10001; background: #00a287; padding: 8px 16px; cursor: pointer; color: #ccc; text-align: center; font-family: helvetica, geneva, sans-serif; font-size: 16px; " onclick="submit()">Submit</div>
    <canvas id="GameCanvas" style="cursor: pointer; position: absolute;top: 32px; left: 0; z-index: 0" width="1280" height="720"></canvas>
    <canvas id="HoverCanvas" style="cursor: pointer; position: absolute;top: 32px; left: 0; z-index: 1" width="1280" height="720"></canvas>
    <a style="float: left; display: inline-block; padding: 8px 16px; margin: 16px; color: #cccccc; background: #ff6700; text-decoration: none; font-family: helvetica, geneva, sans-serif; position: absolute; bottom: 0; right: 0; z-index: 2; cursor: po
    " href="<?php echo get('home_url'); ?>">back</a>
</div>
<script>
    var a = [
        {
            image:"<?php echo get('home_url'); ?>content/home/block.png",
            json:"<?php echo get('home_url'); ?>content/home/block.json"
        }
    ];

    DT.set(document.getElementById("GameCanvas"), document.getElementById("HoverCanvas"));
    loadSpriteSheets(a);

    var wait = window.setInterval(function(){
        if(isSpriteSheetsDictionaryReady()){
            window.clearInterval(wait);
            log(ERROR.info, "drawing", "sprite");
            renderLoop();
            playLoop();

        }
    },0);
</script>