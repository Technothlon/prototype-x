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
<div style="">
    <canvas id="GameCanvas" style="cursor: pointer; z-index: 0" width="1024" height="600"></canvas>
</div>

<div id="mouseDump" style="position: fixed; top:15px; right:5px"></div>
<div id="mouseClick" style="position: fixed; top:30px; right:5px"></div>
<div id="map" style="position: fixed; top:80px; right:5px"></div>
<div id="mapEntity" style="position: fixed; top:0px; right:450px"></div>
<script>
    var a = [
        {
            image:"<?php echo get('home_url'); ?>content/home/block.png",
            json:"<?php echo get('home_url'); ?>content/home/block.json"
        }
    ];
    $('#GameCanvas').css('display', 'none');
    DT.set(document.getElementById("GameCanvas"), document.getElementById("HoverCanvas"));
    loadSpriteSheets(a);
    function loadLevelNow(id){
        $('#GameCanvas').css('display', 'block');
        $('#mouseDump').html("");
        window.once = true;
        window.loadedLevel = id+"";
        //window.alert('New Level');
        var level = "<?php echo get('home_url'); ?>home/get/game?level="+id;
        var start = new Level(level);
        var wait = window.setInterval(function(){
            if(isSpriteSheetsDictionaryReady()){
                window.clearInterval(wait);
                log(ERROR.info, "drawing", "sprite");
                renderLoop();
                playLoop();
            }
            //console.log('0:loading');
        },0);
    }
</script>
