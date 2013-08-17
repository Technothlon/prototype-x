<?php
/**
 * Developer: Rahul Kadyan
 * Date: 06/08/13
 * Time: 3:09 PM
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
class Hor extends Block
{
    public function __construct($length, $id, $position)
    {
        parent::__construct($length, $id, $position);
    }

    public function move($by, $map)
    {
        parent::move($by, $map);
        $start = $this->position[0];
        $end = $this->position[$this->length - 1];
        switch ($by) {
            case 1:
                $map[$end[0]][$end[1] + 1] = $this->id;
                $map[$start[0]][$start[1]] = 0;
                foreach ($this->position as $i => $p) {
                    $this->position[$i][1] = $p[1] + 1;
                }
                break;
            case -1:
                $map[$end[0]][$end[1]] = 0;
                $map[$start[0]][$start[1] - 1] = $this->id;
                foreach ($this->position as $i => $p) {
                    $this->position[$i][1] = $p[1] - 1;
                }
                break;
        }
        return $map;
    }

    public function movable($by, $map)
    {
        parent::movable($by, $map);
        $start = $this->position[0];
        $end = $this->position[$this->length - 1];
        //echo ' (' . $start[0] . ', ' . $start[1] . ' and ' . $end[0] . ', ' . $end[1] . ')<br>';
        $return = false;
        switch ($by) {
            case 1:
                if (($end[0] >= 0 && $end[0] < 6) && ($end[1] >= 0 && $end[1] < 5) && $map[$end[0]][$end[1] + 1] == 0)
                    $return = true;
                break;
            case -1:
                try {
                    if (($start[0] >= 0 && $start[0] < 6) && ($start[1] > 0 && $start[1] < 6) && $map[$start[0]][$start[1] - 1] == 0)
                        $return = true;
                } catch (Exception $e) {

                }
        }
        return $return;
    }
}