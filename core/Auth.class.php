<?php
if(!defined('xDEC')) exit;
//TODO add advanced authentication techniques
//TODO remove dependency on Database class
/**
 * Class Auth
 * Provides basic authentication
 * @package xDec
 */
class Auth {
    private static $table;
    private static $fields = array(
        'user_id' => 'id',
        'username' => 'username',
        'password' => 'password'
    );
    public function __construct(){
        Auth::$table = Login::$name;
        Auth::$fields['user_id'] = Login::$field_id;
        Auth::$fields['username'] = Login::$field_username;
        Auth::$fields['password'] = Login::$field_password;
    }
    /**
     * Simple login
     * @param string $user username
     * @param string $pass password
     * @return bool true for successful login else false
     */
    public function login($user, $pass){
        get('Database')->select(
            Auth::$table,
            array(
                Auth::$fields['user_id']
            ),
            'WHERE '.quot(Auth::$fields['username'])."='?' AND ".quot(Auth::$fields['password'])."='?'",
            array($user, sha1($pass)),
            1
        );
        if(get('Database')->num_rows() == 1){
            $row = get('Database')->row();
            $_SESSION['xdec_user_id'] = $row[Auth::$fields['user_id']];
            $_SESSION['username'] = $user;
            $_SESSION['user_permissions'] = '*/*';
            return true;
        }
        unset($_SESSION['xdec_user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['user_permissions']);
        return false;
    }

    /**
     * logout function
     */
    public function logout(){
        unset($_SESSION['xdec_user_id']);
        unset($_SESSION['username']);
        $_SESSION = array();
    }

    /**
     * @return bool true for logged in else false
     */
    public function logged(){
        return isset($_SESSION['xdec_user_id']);
    }

    public function logged_id(){
        return($_SESSION['xdec_user_id']);
    }

}