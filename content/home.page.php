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
            case 'trynow':
                echo '<script src="' . get('home_url') . 'content/home/game-data-try.js"></script>';
                break;
        }
        echo '<script>window.home_url = "' . get('home_url') . '";</script>';
        if ($var == 'index') {
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
        ?>
        <script>
            function showAlert(title, text, action, button1, button2, delay, always) {
                delay = 0 | delay;
                if (always == true) always = true;
                else always = false;
                var div = document.createElement('div');
                var b1 = document.createElement('div');
                var b2 = document.createElement('div');
                $(b1).addClass('button ok').html(button1).on('click', action);
                $(b2).addClass('button cancel').html(button2).on('click', function () {
                    $(div).remove();
                    $('#alert').hide();
                });
                $(div).addClass('center').html('<h1>' + title + '</h1><div>' + text + '</div>');
                if (button2.length > 0) $(div).append(b2);
                if (button1.length > 0) $(div).append(b1);
                window.setTimeout(function () {
                    $('#alert').append(div).show().on('click', function () {
                        if (!always) {
                            $(div).remove();
                            $('#alert').hide();
                        }
                    });
                }, delay);
            }
            <?php
            if($this->canShowHint()){
            ?>
        $(document).ready(function(){
            $('#alert').addClass('large');
            showAlert("Prototype x",
                "<h2>BARTENDER's PARADOX</h2>" +
                    "<p>A baby girl is mysteriously dropped off at an orphanage in Cleveland in 1945. \"Jane\" grows up lonely and dejected, not knowing who her parents are, until one day in 1963 she is strangely attracted to a drifter. She falls in love with him. But just when things are finally looking up for Jane, a series of disasters strike. First, she becomes pregnant by the drifter, who then disappears. Second, during the complicated delivery, doctors find that Jane has both sets of sex organs, and to save her life, they are forced to surgically convert \"her\" to a \"him.\" Finally, a mysterious stranger kidnaps her baby from the delivery room.</p>"+

                    "<p>Reeling from these disasters, rejected by society, scorned by fate, \"he\" becomes a drunkard and drifter. Not only has Jane lost her parents and her lover, but he has lost his only child as well. Years later, in 1970, he stumbles into a lonely bar, called Pop's Place, and spills out his pathetic story to an elderly bartender. The sympathetic bartender offers the drifter the chance to avenge the stranger who left her pregnant and abandoned, on the condition that he join the \"time travelers corps.\" Both of them enter a time machine, and the bartender drops off the drifter in 1963. The drifter is strangely attracted to a young orphan woman, who subsequently becomes pregnant.</p>"+

                    "<p>The bartender then goes forward 9 months, kidnaps the baby girl from the hospital, and drops off the baby in an orphanage back in 1945. Then the bartender drops off the thoroughly confused drifter in 1985, to enlist in the time travelers corps. The drifter eventually gets his life together, becomes a respected and elderly member of the time travelers` corps, and then disguises himself as a bartender and has his most difficult mission: a date with destiny, meeting a certain drifter at Pop's Place in 1970.</p>"+

                    "<p>The question is: Who is Jane's mother, father, grandfather, grandmother, son, daughter, granddaughter, and grandson? The girl, the drifter, and the bartender, of course, are all the same person. These paradoxes can made your head spin, especially if you try to untangle Jane's twisted parentage. If we draw Jane's family tree, we find that all the branches are curled inward back on themselves, as in a circle. We come to the astonishing conclusion that she is her own mother and father! She is an entire family tree unto herself.</p>",
                function () {
                }, '', '', 0, true);

        });
            <?php
            }
             ?>
        </script>
    <?php

    }

    function __title__($var)
    {
        echo 'Prototype X';
    }

    function leaderboard($var){
        if (!$this->canStart()) return;
        if(!$this->canPlay()) {
            echo '<h1 class="centre">Bocked! for now. Try after some time.</h2><div id="alert"></div>';
        } else {
            require_once(CONTENT . 'home/play.php');
            get('Database')->query('SELECT COUNT(`play`.`id`) as l, `login`.`team` FROM `play`, `login` WHERE `play`.`uid` = `login`.`id` GROUP BY `play`.`uid` ORDER BY l DESC');
            echo '<h1 class="head">Leader Board</h1>';
            echo '<table>';
            while($row = get('Database')->row()){
                echo '<tr><td></td><td></td></tr>';
            }
            echo '</table>';
        }

    }

    function index($var)
    {
        if (get('Auth')->logged()) {
            echo '<div id="alert"></div>';
            echo '<h1 class="head">Prototype X</h1>';
            if ($this->canStart()) {
                echo '<a class="link" href="' . get('home_url') . 'home/play">Compete</a>';
                echo '<a class="link" href="' . get('home_url') . 'home/draw">Create</a>';
                echo '<a class="link" href="' . get('home_url') . 'home/trynow">Try now</a>';
            } else {
                echo '<h2 class="head">Trying to connect <pre><span id="timer">.  </span></pre></h2>';
                echo '<h2 class="head">Retrying in <span id="timer2">30</span> seconds</h2>';
                ?>
                <script>
                    var connecting = 30;
                    var inter = window.setInterval(function () {
                        var dot = $('#timer');
                        if (dot.html() == '.  ') dot.html('.. ');
                        else if (dot.html() == '.. ') dot.html('...');
                        else if (dot.html() == '...') dot.html('   ');
                        else {
                            dot.html('.  ');
                            $('#timer2').html("" + (--connecting));
                            if (connecting == 0) window.location.href = window.location.href;
                        }
                    }, 1000 / 4);
                </script>
            <?php
            }
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

    function __meta__($var)
    {
        switch ($var) {
            case 'select':
            case 'setStart':
            case 'setCreate':
            case 'setPlay':
            case 'setShowHint':
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
        if (!$this->canStart()) return;
        if(!$this->canPlay()) {
            echo '<h1 class="centre">Bocked! for now. Try after some time.</h2><div id="alert"></div>';
        } else {
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
        echo '<div id="levels" style="margin-right: 100px">';
        $i = 1;
        while ($row = get('Database')->row()) {
            echo '<div id="level_' . $row[Level::$field_id] . '" style="cursor: pointer; display:inline-block; padding: 8px 16px; margin:8px; background: #ccc; text-align: center; font-family: helvetica, geneva, sans-serif; color: #999" onclick="loadLevelNow(' . $row[Level::$field_id] . ');"><div style="padding: 0 0 8px 0">Level ' . $i . '</div>
            <image style="width: 128px; height: 128px" src="' . get('home_url') . 'home/image?id=' . $row[Level::$field_id] . '">
            </div>';
            ++$i;
        }
        echo '</div>';
        if ($var == 'assist' && $this->isAdmin()) {
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
    }

    function draw($var)
    {
        if (!$this->canStart()) return;
        if(!$this->canCreate()) {
            echo '<h1 class="centre">Creating level is disabled.</h2><div id="alert"></div>';
        } else
            require_once(CONTENT . 'home/draw.php');
    }

    function get($var)
    {
        if (!$this->canStart()) return;
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
            $str = html_entity_decode(html_entity_decode($data[Level::$field_json]));
            $str = rtrim($str, '}');
            $str .= ', "id":"' . $_GET['level'] . '"}';
            echo $str;

        } elseif ($var == 'solution' && isset($_GET['level']) && $this->isAdmin()) {
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
        } elseif ($var == 'trail' && isset($_GET['level'])) {
            get('Database')->select(
                Trails::$name,
                array(
                    Trails::$field_json
                ),
                'WHERE `?`=?',
                array(
                    Trails::$field_id,
                    intval($_GET['level'])
                )
            );
            $data = get('Database')->row();
            $str = html_entity_decode($data[Level::$field_json]);
            $str = rtrim($str, '}');
            $str .= ', "id":"' . $_GET['level'] . '"}';
            echo $str;
        } elseif ($var == 'trail_sol' && isset($_GET['level']) && $this->isAdmin()) {
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

    function select($var)
    {
        if (!$this->canStart()) return;
        if(!$this->canCreate()) {
            echo 'This feature is no more available';
            return;
        }
        if (isset($_POST['level'])) {
            $id = $_POST['level'];
            $uid = get('Auth')->logged_id();
            get('Database')->select(
                Trails::$name,
                '*',
                "WHERE ?=? AND ?=?",
                array(
                    Trails::$field_id,
                    $id,
                    Trails::$field_user,
                    $uid
                )
            );
            $row = get('Database')->row();
            if ($row) {
                get('Database')->select(
                    Level::$name,
                    '*',
                    "WHERE ?=?",
                    array(
                        Level::$field_user,
                        $uid
                    )
                );
                $ent = get('Database')->row();
                if ($ent) {
                    get('Database')->update(
                        Level::$name,
                        array(
                            Level::$field_user => $uid,
                            Level::$field_json => $row[Trails::$field_json],
                            Level::$field_image => $row[Trails::$field_image],
                            Level::$field_moves => $row[Trails::$field_moves],
                            Level::$field_solution => $row[Trails::$field_solution]
                        ),
                        "WHERE ?='?'",
                        array(
                            Level::$field_id,
                            $ent[Level::$field_id]
                        )
                    );
                } else {
                    get('Database')->insert(
                        Level::$name,
                        array(
                            Level::$field_user => $uid,
                            Level::$field_json => $row[Trails::$field_json],
                            Level::$field_image => $row[Trails::$field_image],
                            Level::$field_moves => $row[Trails::$field_moves],
                            Level::$field_solution => $row[Trails::$field_solution]
                        )
                    );
                }
                echo 'Level submitted.';
            } else echo 'Some error occurred. Try submitting again.';
        } else echo 'Some error occurred. Try submitting again.';
    }

    function trynow($var)
    {
        if (!$this->canStart()) return;
        if(!$this->canCreate()) {
            echo '<h1 class="centre">Creating level is disabled.</h2><div id="alert"></div>';
        } else {
        require_once(CONTENT . 'home/trails.php');
        get('Database')->select(
            Trails::$name,
            array(
                Trails::$field_id
            ),
            'WHERE `?` = ? ORDER BY `?` ASC',
            array(
                Trails::$field_user,
                get('Auth')->logged_id(),
                Trails::$field_moves
            )
        );
        echo '<div id="levels" style="margin-right: 100px">
        <form id="draft" action="' . get('home_url') . 'home/select" method="GET" >

        ';
        $i = 1;
        while ($row = get('Database')->row()) {
            echo '
            <div id="level_' . $row[Trails::$field_id] . '" style="cursor: pointer; display:inline-block; padding:
             8px 16px; margin:8px; background: #ccc; text-align: center; font-family: helvetica, geneva, sans-serif; color:
              #999">
              <div style="padding: 0 0 8px 0">
              <label class="submit-draft">
              <input type="radio" name="level" form="draft" value="' . $row[Trails::$field_id] . '" id="draft_' . $row[Trails::$field_id] . '">Draft ' . $i . '
            </label>
              </div>
            <image onclick="loadLevelNow(' . $row[Trails::$field_id] . ');" style="width: 128px; height: 128px" src="' . get('home_url') . 'home/image/try?id=' . $row[Trails::$field_id] . '">
            </div>';
            ++$i;
        }
        echo '</div><input type="submit" form="draft" class="submit-draft button"></form>';
        ?>
        <script>
            $(document).ready(function () {
                $('form').on('submit', function (e) {
                    e.preventDefault();
                    var form = $(this);
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize()
                    }).done(function (text) {
                            showAlert('Prototype X', text, function () {
                            }, 'ok', '', 0);
                        });
                })
            });
        </script>
        <?php
        if ($var == 'assist' && $this->isAdmin()) {
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
    }

    function image($var)
    {
        if (!$this->canStart()) return;
        if (isset($_GET['id'])) {
            switch ($var) {
                case 'try':
                    header('Content-type: image/png');
                    get('Database')->select(
                        Trails::$name,
                        array(
                            Trails::$field_image
                        ),
                        'WHERE `?`=?',
                        array(
                            Trails::$field_id,
                            $_GET['id']
                        )
                    );
                    $data = get('Database')->row();
                    echo base64_decode(substr($data[Level::$field_image], strpos($data[Level::$field_image], ",") + 1));
                    break;
                default:
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

                $level_new = array('level' => $_level, 'entities' => $ele, 'moves' => $moves, 'create' => get('Auth')->logged_id());
                get('Database')->insert(
                    Trails::$name,
                    array(
                        Trails::$field_json => json_encode($level_new),
                        Trails::$field_moves => $moves,
                        Trails::$field_user => get('Auth')->logged_id(),
                        Trails::$field_solution => $solution,
                        Trails::$field_image => $_POST['image']
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

    private function canStart()
    {
        get('Database')->select(
            'control',
            '*',
            "WHERE `key`='start'",
            array()
        );
        $row = get('Database')->row();
        if ($row['value'] == '1')
            return true;
        return false;
    }

    private function canCreate()
    {
        get('Database')->select(
            'control',
            '*',
            "WHERE `key`='build'",
            array()
        );
        $row = get('Database')->row();
        if ($row['value'] == '1')
            return true;
        return false;
    }

    private function canPlay()
    {
        get('Database')->select(
            'control',
            '*',
            "WHERE `key`='play'",
            array()
        );
        $row = get('Database')->row();
        if ($row['value'] == '1')
            return true;
        return false;
    }

    function setStart($var)
    {
        if ($this->isAdmin()) {
            get('Database')->update(
                'control',
                array('value' => $var,
                    'key' => 'start'),
                "WHERE `key`='start'",
                array()
            );
        }
        header('Location: ' . get('home_url') . 'home/admin');
    }

    function setCreate($var)
    {
        if ($this->isAdmin()) {
            get('Database')->update(
                'control',
                array('value' => $var,
                    'key' => 'build'),
                "WHERE `key`='build'",
                array()
            );
        }
        header('Location: ' . get('home_url') . 'home/admin');
    }

    function setPlay($var)
    {
        if ($this->isAdmin()) {
            get('Database')->update(
                'control',
                array('value' => $var,
                    'key' => 'play'),
                "WHERE `key`='play'",
                array()
            );
        }
        header('Location: ' . get('home_url') . 'home/admin');
    }

    private function canShowHint()
    {
        get('Database')->select(
            'control',
            '*',
            "WHERE `key`='hint'",
            array()
        );
        $row = get('Database')->row();
        if ($row['value'] == '1')
            return true;
        return false;
    }

    function setShowHint($var)
    {
        if ($this->isAdmin()) {
            get('Database')->update(
                'control',
                array('value' => $var,
                    'key' => 'hint'),
                "WHERE `key`='hint'",
                array()
            );
        }
        header('Location: ' . get('home_url') . 'home/admin');
    }

    private function isAdmin()
    {
        if (get('Auth')->username() == 'kadyan')
            return true;
        return false;
    }

    public function admin($var)
    {
        if ($this->isAdmin()) {
            echo '<pre>';
            ?>
            Server Access: ( <?php echo ($this->canStart()) ? 'allow' : 'deny'; ?> )
            <a href="home/setStart/1">allow</a>
            <a href="home/setStart/0">deny</a>
            Create Access: ( <?php echo ($this->canCreate()) ? 'allow' : 'deny'; ?> )
            <a href="home/setCreate/1">allow</a>
            <a href="home/setCreate/0">deny</a>
            Play Access: ( <?php echo ($this->canPlay()) ? 'allow' : 'deny'; ?> )
            <a href="home/setPlay/1">allow</a>
            <a href="home/setPlay/0">deny</a>
            Hint Access: ( <?php echo ($this->canShowHint()) ? 'allow' : 'deny'; ?> )
            <a href="home/setShowHint/1">allow</a>
            <a href="home/setShowHint/0">deny</a>
        <?php
        }
    }
}