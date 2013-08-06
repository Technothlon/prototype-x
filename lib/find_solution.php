<?php
/**
 * Developer: Rahul Kadyan
 * Date: 06/08/13
 * Time: 3:20 PM
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

$queue = new Queue();
$ele = array(
    '-1' => new Hor(2, -1, [ [2,0],[2,1] ]),
    '1' => new Ver(2, 1, [ [0,0],[0,1] ]),
    '2' => new Hor(2, 2, [ [0,1],[0,2] ]),
    '3' => new Ver(2, 3, [ [1,2],[2,2] ]),
    '4' => new ver(3, 4, [ [0,3],[1,3],[2,3] ]),
    '5' => new Ver(2, 5, [ [0,4],[1,4] ]),
    '6' => new Ver(2, 6, [ [2,4],[3,4] ]),
    '7' => new Hor(3, 7, [ [3,1],[3,2],[3,3] ]),
    '8' => new Ver(2, 8, [ [4,1],[5,1] ]),
    '9' => new Ver(2, 9, [ [4,2],[5,2] ]),
    '10' => new Hor(2, 10, [ [5,3],[5,4] ]),
);
$board = new Board(
    [
        [ 1, 2, 2, 4, 5, 0],
        [ 1, 0, 3, 4, 5, 0],
        [-1,-1, 3, 4, 6, 0],
        [ 0, 7, 7, 7, 6, 0],
        [ 0, 8, 9, 0, 0, 0],
        [ 0, 8, 9,10,10, 0]
    ],
    $ele
);

$queue->enqueue(new Node(null, null, $board, array(0,0)));
while($board->solved()){

}

function __try($bd){
    foreach($bd->ele as $block){
        if($block->movable(1, $bd->map)){
        }
        if($block->movable(-1, $bd->map)){
        }
    }
}