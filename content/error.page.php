<?php
class error implements Page{
    function __meta__($var)
    {
    }

    function __head__($var)
    {

    }

    function __title__($var)
    {

    }

    function index($var)
    {

    }

    function _404($var){
        echo 'page not found';
    }

    function _503($var){
        echo 'you are in restricted zone';
    }

}