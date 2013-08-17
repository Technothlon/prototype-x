<?php
/**
 * Developer: Rahul Kadyan
 * Date: 06/08/13
 * Time: 2:37 PM
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

class home implements Admin
{
    public function allowed($method, $user)
    {
        switch ($method) {
            case 'index':
            case 'login':
            case 'logout':
                return true;
                break;
            default:
                return get('Auth')->logged();
        }
    }

    function __head__($var)
    {
        echo '<link rel="stylesheet" type="text/css" href="' . get('home_url') . 'content/home/style.css">';
        echo '<script src="' . get('home_url') . 'content/home/jquery.min.js"></script>';
        echo '<script src="' . get('home_url') . 'content/home/core.js"></script>';
        switch (get('request')) {
            case 'play':
                echo '<script src="' . get('home_url') . 'content/home/game-data.js"></script>';
                break;
            case 'draw':
                echo '<script src="' . get('home_url') . 'content/home/game-draw.js"></script>';
                break;
        }
        echo '<script>window.home_url = "' . get('home_url') . '";</script>';
        ?>
        <style>
            <?php
            get('Database')->select(
                Play::$name,
                array(
                    Play::$field_level
                ),
                'WHERE `?`=? GROUP BY `?`',
                array(
                    Play::$field_uid,
                    get('Auth')->logged_id(),
                    Play::$field_level
                )
            );
            while($row = get('Database')->row()){
                echo '#level_'.$row[Play::$field_level].'{
                    background: #00a287 !important;
                    color: #fff !important;
                }
                ';
            }
        ?>
        </style>
    <?php
    }

    function __title__($var)
    {
        echo 'Prototype X';
    }

    function index($var)
    {
        if (get('Auth')->logged()) {
            echo '<h1 class="head">Prototype X</h1>';
            echo '<a class="link" href="' . get('home_url') . 'home/play">Compete</a>';
            echo '<a class="link" href="' . get('home_url') . 'home/draw">Create</a>';
            echo '<a class="link" href="' . get('home_url') . 'home/logout">Logout</a>';
        } else {
            ?>
            <div style="width: 350px; margin: 15% auto; text-align: center">
                <img src="<?php echo get('home_url'); ?>images/techno.png" style="display: inline-block">

                <form action="<?php echo get('home_url'); ?>home/login" method="post">
                    <div>
                        <label>
                            <input class="text" type="text" name="username" placeholder="Team ID">
                        </label>
                    </div>
                    <div>
                        <label>
                            <input class="text" type="password" name="password" placeholder="Password">
                        </label>
                    </div>
                    <div>
                        <label>
                            <input class="button" type="submit" value="Login">
                        </label>
                    </div>
                </form>
            </div>
        <?php
        }
    }

    function login($var)
    {
        if (isset($_POST['username']) && isset($_POST['password']))
            get('Auth')->login($_POST['username'], $_POST['password']);
        header('Location: ' . get('home_url'));
    }

    function logout($var)
    {
        get('Auth')->logout();
        header('Location: ' . get('home_url'));
    }

    function pop($var){
        $d = @fsockopen('tls://tamdil.iitg.ernet.in', 995, $errno, $errstr, 15);
        if(!$d)
        echo $errno.' '.$errstr;
        else{
            socket_set_blocking($d, -1);
            stream_set_timeout($d, 15, 0);
            echo $reply = fgets($d, 128);
            if (substr($reply, 0, 3) == '+OK'){
                echo '<br>';
                echo $query = "USER r.kadyan\r\n";
                echo '<br>';
               fwrite($d, $query, strlen($query));
                echo $reply = fgets($d, 128);
                echo '<br>';
                if(substr($reply, 0, 3) == '+OK'){
                     $query = "PASS xenture\r\n";
                    echo '<br>';
                    fwrite($d, $query, strlen($query));
                    echo $reply = fgets($d, 128);
                    echo '<br>';
                    if(substr($reply, 0, 3) == '+OK'){
                        echo $query = "LIST\r\n";
                        echo '<br>';
                        fwrite($d, $query, strlen($query));
                        echo $reply = fgets($d, 128);
                        if(substr($reply, 0, 3) == '+OK' && preg_match('/(\d+)/', $reply, $array)) {
                            $mails =  intval($array[1]);
                            echo '<br>';
                            while($mails--){
                                echo $reply = fgets($d, 128);
                                echo '<br>';
                            }
                            echo $query = "RETR 11\r\n";
                            echo '<br>';
                            fwrite($d, $query, strlen($query));
                            echo '<pre>';
                            echo fgets($d, 1024);
                            while($ch = fgets($d, 1024)) {
                                if($ch == "..\r\n")
                                    echo ".\r\n";
                                else
                                echo $ch;
                                if($ch == ".\r\n") break;
                            }

                            //while( strlen($ch = fgets($d)) > 2) echo $ch;//.' ;'.ord($ch).'; ';
                            echo '</pre>';

                        }
                        //while(!feof($d)) echo ($reply = fgets($d, 1024)).'<br>';
                        echo '<br>';
                    }
                }
                echo $query = "QUIT\r\n";
                echo '<br>';
                fwrite($d, $query, strlen($query));
            }
            fclose($d);
        }

        fclose($d);
        /*if($m_box = imap_open ("{tamdil.iitg.ernet.in:995/pop3/ssl}", "r.kadyan", "xenture")){
            echo 'connected';
            if(is_array($folders = imap_list($m_box, "{202.141.80.11:995/pop3/ssl/novalidate-cert}", "*"))){
                foreach($folders as $f){
                    echo imap_utf7_decode($f);
                }
            }
            imap_close($m_box);
        }else{
            var_dump($m_box);
            echo 'Error connecting server';
        }*/
    }

    function __meta__($var)
    {
        switch ($var) {
            case 'get':
                return 'independent';
                break;
            case 'form':
                return 'independent';
                break;
            case 'login':
                return 'independent';
                break;
            case 'logout':
                return 'independent';
                break;
            case 'image':
                return 'independent';
                break;
            default:
                null;
        }
    }

    function play($var)
    {
        require_once(CONTENT . 'home/play.php');
        get('Database')->select(
            Level::$name,
            array(
                Level::$field_id
            ),
            'ORDER BY `?` ASC',
            array(
                Level::$field_moves
            )
        );
        echo '<div id="levels" style="">';
        $i = 1;
        while ($row = get('Database')->row()) {
            echo '<div id="level_' . $row[Level::$field_id] . '" style="cursor: pointer; display:inline-block; padding: 8px 16px; margin:8px; background: #ccc; text-align: center; font-family: helvetica, geneva, sans-serif; color: #999" onclick="loadLevelNow(' . $row[Level::$field_id] . ');"><div style="padding: 0 0 8px 0">Level ' . $i . '</div>
            <image style="width: 128px; height: 128px" src="' . get('home_url') . 'home/image?id=' . $row[Level::$field_id] . '">
            </div>';
            ++$i;
        }
        echo '</div>';
        if ($var == 'assist') {
            echo '<button onclick="autoSolve();" style="position: fixed; right: 16px; bottom: 16px;" name="Auto Solve">Auto Solve</button>';
            ?>
            <script>
                function autoSolve() {
                    $.ajax({
                        url: "<?php echo get('home_url'); ?>home/get/solution?level=" + window.loadedLevel,
                        method: 'post'
                    }).done(function (text) {
                            var data = JSON.parse(text);
                            for (var i = 0; i < data.length; ++i)
                                LevelDictionary[window.loadedLevel].pushAuto(data[i][0], data[i][1]);
                            LevelDictionary[window.loadedLevel].doAuto();
                        });
                    // alert('loading');
                }
            </script> <?php
        }
    }

    function draw($var)
    {
        require_once(CONTENT . 'home/draw.php');
    }

    function get($var)
    {
        if ($var == 'game' && isset($_GET['level'])) {
            get('Database')->select(
                Level::$name,
                array(
                    Level::$field_json
                ),
                'WHERE `?`=?',
                array(
                    Level::$field_id,
                    intval($_GET['level'])
                )
            );
            $data = get('Database')->row();
            $str = html_entity_decode($data[Level::$field_json]);
            $str = rtrim($str, '}');
            $str .= ', "id":"' . $_GET['level'] . '"}';
            echo $str;

        } elseif ($var == 'solution' && isset($_GET['level'])) {
            get('Database')->select(
                Level::$name,
                array(
                    Level::$field_solution
                ),
                'WHERE `?`=?',
                array(
                    Level::$field_id,
                    intval($_GET['level'])
                )
            );
            $data = get('Database')->row();
            $str = html_entity_decode($data[Level::$field_solution]);
            echo $str;
        }
    }

    function image($var)
    {
        if (isset($_GET['id'])) {
            header('Content-type: image/png');
            get('Database')->select(
                Level::$name,
                array(
                    Level::$field_image
                ),
                'WHERE `?`=?',
                array(
                    Level::$field_id,
                    $_GET['id']
                )
            );
            $data = get('Database')->row();
            echo base64_decode(substr($data[Level::$field_image], strpos($data[Level::$field_image], ",") + 1));
        }
    }

    function form($var)
    {
        if ($var == 'level') {
            if (isset($_POST['data'])) {
                $str = $_POST['data'];
                $data = (array)json_decode(stripslashes($str));
                $data['entities'] = (array)$data['entities'];
                $entities = array();

                require_once(BASE . 'lib/Queue.class.php');
                require_once(BASE . 'lib/Block.class.php');
                require_once(BASE . 'lib/Hor.class.php');
                require_once(BASE . 'lib/Ver.class.php');
                require_once(BASE . 'lib/Board.class.php');
                require_once(BASE . 'lib/Move.class.php');

                foreach ($data['entities'] as $key => $val) {
                    $d = (array)$val;
                    $map = array();
                    foreach ($d['map'] as $e) {
                        array_push($map, array($e[1], $e[0]));
                    }
                    if ($d['type'] == 'v')
                        $entities[$key] = new Ver(intval($d['length']), intval($key), $map);
                    else
                        $entities[$key] = new Hor(intval($d['length']), intval($key), $map);
                }
                //print_r($data['entities']["1"]);
                $_level = $data['level'];
                $solution = '';
                $moves = 0;
                $ele = array();
                require_once(BASE . 'lib/find_solution.php');
                foreach ($data['entities'] as $key => $val) {
                    $ele[$key] = $val = (array)$val;
                    $ele[$key]['map'] = $entities[$key]->position;
                }
                $ele["-1"]['type'] = 'e';
//                echo '<pre>';
//                echo print_r($ele);
//                echo '</pre>';

                $level_new = array('level' => $_level, 'entities' => $ele, 'moves' => $moves, 'create' => get('Auth')->logged_id());
                get('Database')->insert(
                    Level::$name,
                    array(
                        Level::$field_json => json_encode($level_new),
                        Level::$field_moves => $moves,
                        Level::$field_user => get('Auth')->logged_id(),
                        Level::$field_solution => $solution,
                        Level::$field_image => $_POST['image']
                    )
                );
            } else echo 'error';
        } else if ($var == 'store') {
            if (isset($_POST['data'])) {
                @get('Database')->insert(
                    Play::$name,
                    array(
                        Play::$field_uid => get('Auth')->logged_id(),
                        Play::$field_data => $_POST['data'],
                        Play::$field_moves => $_POST['moves'],
                        Play::$field_level => $_POST['level']
                    )
                );
            }
        }
    }

}