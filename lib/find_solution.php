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
if (!defined('xDEC')) exit;
$queue = new Queue();
$board = new Board(
    $_level,
    $entities
);
//echo '<pre>';
//print_r($board);
//echo '</pre>';
$queue->enqueue(new Node(null, null, $board, array(0, 0)));
$solved = $board->solved();
$solMap = null;
$cur = $board;
$i = 0;
$sol = 0;
$j = 0;
$k = 1;
$str = '';
while (!$solved) {
    $b = $queue->data[$i];
    //echo '<h2>trying: ' . $i . '</h2><br>';

//    //echo '<table style="display: inline">';
//    foreach ($d as $t) {
//        //echo '<tr>';
//        foreach ($t as $p) {
//            //echo '<td style="width: 32px; height: 32px; font-weight:800">' . $p . '</td>';
//        }
//        //echo '</tr>';
//    }
    //echo '</table>';
    //echo '<div style="display: inline;"><br>';
    //echo '<pre>';
    //print_r($queue->data[count($queue->data) - 1]->data->ele);
    //echo '</pre>';
    //echo '</pre></div><br><br><div style="width: 300px; height: 300px; overflow: scroll; display: block">';
    //Trying solving
    $bd = clone $b->data;
    foreach ($bd->ele as $id => $block) {
        if ($block->movable(1, $bd->map)) {
            $temp = clone $bd;
            $cb = clone $block;
            $temp->map = $cb->move(1, $temp->map);
            $temp->ele[$id] = $cb;
            $sid = $queue->enqueue(new Node(null, $queue->data[$i], $temp, new Move(1, $block->id, null)));
            if ($temp->solved()) {
                $solved = true;
                $solMap = $temp;
                $sol = $sid - 1;
                break;
            }
        }
        if ($block->movable(-1, $bd->map)) {
            $temp = clone $bd;
            $cb = clone $block;
            $temp->ele[$id] = $cb;
            $temp->map = $temp->ele[$id]->move(-1, $temp->map);
            $sid = $queue->enqueue(new Node(null, $queue->data[$i], $temp, new Move(-1, $block->id, null)));
            if ($temp->solved()) {
                $solved = true;
                $solMap = $temp;
                $sol = $sid - 1;
                break;
            }
        }
    }
    //echo '</div>';
    $b->data = null;
    if ($i + 1 < count($queue->data))
        $i++;
    else break;
}
if ($solMap) {
    //echo 'may be solved: <br>';
    $d = $solMap->map;
    //echo '<table style="display: block">';
//    foreach ($d as $t) {
//        //echo '<tr>';
//        foreach ($t as $p) {
//            //echo '<td style="width: 32px; height: 32px">' . $p . '</td>';
//        }
//        //echo '</tr>';
//    }
    //echo '</table>';
    //echo '<br><br>';
    $s = $queue->data[$sol];
    $last = $s->step->element;
    while ($s instanceof Node && $s->prev) {
        $j++;
        //$str = 'LevelDictionary[window.loadedLevel].pushAuto(' . $s->step->element . ',' . $s->step->step . '); ' . $str;
        $str = '['. $s->step->element . ',' . $s->step->step . '],' . $str;
        //echo $s->step->element . ':' . $s->step->step . ', ';
        if ($s->step->element != $last) ++$k;
        $s = $s->prev;
    }
    $str = '['.rtrim($str, ',').']';
//echo 'TOTAL : ' . $j . ' SMART: ' . $k;
//echo '<br><br><br><br><br>';
//echo '<textarea>' . $str . '</textarea>';
    $solution = $str;
    $moves = $j;
    echo 'level saved. ';
    echo '<a href="'.get('home_url') . 'home/play">Play it now</a><br>';
} else {
    echo 'Some error occurred'; exit;
}


