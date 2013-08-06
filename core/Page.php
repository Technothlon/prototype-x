<?php
if(!defined('xDEC')) exit;
interface Page{
    function __head__($var);
    function __title__($var);
    function index($var);
    function __meta__($var);
}