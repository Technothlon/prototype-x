<?php
/**
 * Developer: Rahul Kadyan
 * Date: 06/08/13
 * Time: 2:54 PM
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

class Ver extends Block {
    public function __construct($length, $id, $position){
        parent::__construct($length, $id, $position);
    }
    public function move($by, $map)
    {
        parent::move($by, $map);
    }

    public function movable($by, $map)
    {
        parent::movable($by, $map);
        $start = $this->position[0];
        $end = $this->position[$this->length - 1];
        $return = false;
        switch ($by) {
            case 1:
                try {
                    if ($map[$end[0][0] + 1][$end[0][1]] == 0)
                        $return = true;
                } catch (Exception $e) {
                }
                break;
            case -1:
                try {
                    if ($map[$start[0][0] - 1][$start[0][1]] == 0)
                        $return = true;
                } catch (Exception $e){

                }
        }
        return $return;
    }
}