<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if ( ! function_exists('app_trim'))
{
    function app_trim($str, $charlist = " \t\n\r\0\x0B"){if(empty($str)){return $str;}return trim($str,$charlist);}
}
if ( ! function_exists('app_rtrim'))
{
    function app_rtrim($str, $charlist = " \t\n\r\0\x0B"){if(empty($str)){return $str;}return rtrim($str,$charlist);}
}
if ( ! function_exists('app_ltrim'))
{
    function app_ltrim($str, $charlist = " \t\n\r\0\x0B"){if(empty($str)){return $str;}return ltrim($str,$charlist);}
}
if ( ! function_exists('app_str_replace'))
{
    function  app_str_replace ($search, $replace, $subject, &$count = null){if(empty($subject) || empty($search)){return $subject;}return str_replace($search,$replace,$subject,$count);}
}