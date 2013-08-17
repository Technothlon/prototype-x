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
if (!defined('xDEC')) exit;
class Node
{
    public $next, $prev, $data, $step, $index;

    public function __construct($next, $prev, $data, $step)
    {
        $this->next = $next;
        $this->prev = $prev;
        $this->data = $data;
        $this->step = $step;
    }
}

class Queue
{
    public $data, $hash;

    function __construct()
    {
        $this->data = array();
        $this->hash = array();
    }

    public function enqueue(Node $board)
    {
        $hash = md5(serialize($board->data->map));
        if (array_key_exists($hash, $this->hash)) return -1;
        $board->index = count($this->data);
        $d = $board->data->map;
        //echo '<h1>' . (count($this->data)) . '</h1>';
        //echo '<table style="display: block">';
        foreach ($d as $t) {
            //echo '<tr>';
            foreach ($t as $p) {
                //echo '<td style="width: 32px; height: 32px">' . $p . '</td>';
            }
            //echo '</tr>';
        }
        //echo '</table>';
        //echo '<br><br>';
        $this->hash[$hash] = true;
        array_push($this->data, $board);
        return count($this->data);
    }

    public function dequeue()
    {
        array_pop($this->hash);
        array_pop($this->data);
    }
}