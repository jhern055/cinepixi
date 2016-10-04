<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('load_controller'))
{
    function load_controller($controller, $method = 'index')
    {
        require_once(APPPATH . 'controllers/' . $controller . '.php');

        $controller = new $controller();

        return $controller->$method();
    }
}
if (!function_exists('controller_exists'))
{
    function controller_exists($controller)
    {
        if(file_exists(APPPATH . 'controllers/' . $controller . '.php'))
        return true;
    }
}