<?php
/**
 * Developer: Rahul Kadyan
 * Date: 06/08/13
 * Time: 3:19 PM
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
class Node{
    public $next, $prev, $data, $step;
    public function __construct($next, $prev, $data, $step){
        $this->next = $next;
        $this->prev = $prev;
        $this->data = $data;
        $this->step = $step;
    }
}
class Queue {
    private $data, $hash;
    function __construct(){
        $this->data = array();
        $this->hash = array();
    }
    public function enqueue($board){
        $hash = md5($board);
        if(array_key_exists($hash, $this->hash)) return;
        $this->hash[$hash] = true;
        array_push($this->data, $board);
    }
    public function dequeue($board){
        array_pop($this->hash);
        array_pop($this->data);
    }
}