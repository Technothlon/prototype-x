<?php
if(!defined('xDEC')) exit;

/**
 * Class Database
 * @package xDec
 * @description MySQL database handler
 */
class Database {
    /**
     * @var array $_connection for keeping active connection
     * @var array $_result for keeping last result
     */
    private $_connection, $_result;

    private $rows_effected,
            $insert_id;

    /**
     * @return mixed
     */
    public function insertId()
    {
        return $this->insert_id;
    }

    /**
     * @return mixed
     */
    public function rowsEffected()
    {
        return $this->rows_effected;
    }

    /**
     *  Default constructor: check for database connection variables
     */
    public function __construct()
    {
        if(!(defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')))
           trigger_error("Check `config.ini.php` for database connection definitions", E_USER_ERROR);
    }

    function __destruct()
    {
        $this->close();
    }

    /**
     * Selects satisfying records from given table
     * @Since Version 1.0
     * @param string $table Name of table to select from
     * @param mixed $fields Array of fields or string containing field names to select
     * @param string $condition full formatted condition without WHERE word
     * @param mixed $args arguments to be used in condition
     * @param null $limit Number of results expected
     */
    public function select($table, $fields, $condition, $args, $limit = NULL){
        $limit = ( $limit == NULL ) ? '' : 'LIMIT ' . intval( $limit );
        if( is_array($fields) )
        {
            $f = '';
            foreach( $fields as $v )
                $f .= "`$v`,";
            $fields = substr( $f, 0, -1 );
        }
        foreach($args as $var){
            $condition = preg_replace('/\?/', (is_string($var)?get('Security')->mysql($var):$var), $condition, 1);
        }
        $select = "SELECT ".$fields." FROM ".$table." ".$condition." ".$limit;
        $this->_query( $select );
    }

    /**
     * Inserts a record in given table
     * @param string $table name of table
     * @param string $data Assoc array for data to insert
     */
    public function insert( $table, $data )
    {
        $fields = '';
        $values = '';
        foreach( $data as $f => $v )
        {
            $fields .= "`$f`,";
            $values .= ( is_numeric( $v ) && intval( $v ) == $v ) ? $v."," : "'".get('Security')->mysql($v)."',";
        }
        $fields = substr( $fields, 0, -1 );
        $values = substr( $values, 0, -1 );
        $insert = "INSERT INTO $table ({$fields}) VALUES ({$values})";
        $this->_query($insert);
    }

    /**
     * Updates given columns in a record
     * @param string $table Name of table
     * @param string $change Assoc array for data to change
     * @param string $condition full formatted condition without WHERE word
     * @param mixed $args arguments to be used in condition
     * @param int $limit number of rows to update
     */
    public function update( $table, $change, $condition, $args, $limit = NULL )
    {
        $update = "UPDATE {$table} SET";
        foreach( $change as $field => $value )
        {
            $update .= "`" . $field . "`=".(is_string($value)?"'".get('Security')->mysql($value)."'": $value).",";
        }
        $update = substr( $update, 0, -1 );
        foreach($args as $var){
            $condition = preg_replace('/\?/', (is_string($var)?get('Security')->mysql($var):$var), $condition, 1);
        }
        $update .=" ".$condition. (is_int($limit)?' LIMIT 0, '.$limit: '');
        $this->_query( $update );
    }

    /**
     * Deletes a record from given table
     * @param string $table Name of table
     * @param string $condition full formatted condition without WHERE word
     * @param mixed $args arguments to be used in condition
     * @param string $limit Number of expected deletions
     */
    public function delete( $table, $condition, $args, $limit = NULL)
    {
        foreach($args as $var){
            $condition = preg_replace('/\?/', (is_string($var)?get('Security')->mysql($var):$var), $condition, 1);
        }
        $delete = "DELETE FROM ".$table." ".$condition.(is_int($limit)? ' LIMIT 0, '.$limit: '');
        $this->_query( $delete );
    }

    /**
     * Returns result
     * @return mixed mysqli_result object
     */
    public function result(){
        return $this->_result;
    }

    /**
     * Returns row
     * @return mixed Assoc array for a row
     */
    public function row(){
        if(is_object($this->_result))
            return $this->_result->fetch_assoc();
        return null;
    }

    public function num_rows(){
        if($this->_result)
            return $this->_result->num_rows;
    }

    /**
     * Public interface for _query
     * @param string $str query string
     */
    public function query($str){
        $this->_query($str);
    }

    /**
     * Executes a query
     * @param $str string query
     */
    private function _query($str){
        if(is_object($this->_result))   $this->_result->close();
        $this->connect();
        if(defined('LOG_DB_QUERY') && LOG_DB_QUERY) get('Logger')->custom_log('db.log', $str);
        $this->_result = $this->_connection->query($str);
        if(!$this->_result){
            trigger_error($this->_connection->error, E_USER_NOTICE);
        }
        $this->rows_effected = $this->_connection->affected_rows;
        $this->insert_id = $this->_connection->insert_id;
        $this->close();
    }

    /**
     * Connects to MySQL server
     */
    private function connect(){
        $this->_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME,DB_PORT);
        if($this->_connection->errno){
            trigger_error($this->_connection->error, E_USER_ERROR);
        }
    }
    /**
     * Closes the active MySQL connection
     */
    private function close(){
        if($this->_connection instanceof mysqli)
            $this->_connection->close();
    }

    /**
     * Disabling cloning
     */
    private function __clone()
    {
    }
}