<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('encode_url')){
    function encode_url($string, $key="", $url_safe=TRUE){

        if($key==null || $key=="")
        {
            $key="tyz_mydefaulturlencryption";
        }
        $CI =& get_instance();
        $ret = $CI->encrypt->encode($string, $key);

        if ($url_safe)
        {
            $ret = strtr(
                    $ret,
                    array(
                        '+' => '.',
                        '=' => '-',
                        '/' => '~'
                    )
                );
        }

        return $ret;
    }

}

if ( ! function_exists('decode_url')){

    function decode_url($string, $key=""){
         if($key==null || $key=="")
        {
            $key="tyz_mydefaulturlencryption";
        }
            $CI =& get_instance();
        $string = strtr(
                $string,
                array(
                    '.' => '+',
                    '-' => '=',
                    '~' => '/'
                )
            );

        $ret = $CI->encrypt->decode($string, $key);
        return $ret;
    }

}

//  ENCRIPATR EL ID 
if ( ! function_exists('encode_id')){
    function encode_id($string, $key="", $url_safe=TRUE){
        
        if($key==null || $key=="")
        {
            $key="#%*34dalk";
        }
        $CI =& get_instance();
        $ret = $CI->encrypt->encode($string, $key);

        if ($url_safe)
        {

            $ret = strtr(
                    $ret,
                    array(
                        '+' => '.',
                        '=' => '-',
                        '/' => '~'
                    )
                );

        }

        return $ret;
    }

}

if ( ! function_exists('decode_id')){

    function decode_id($string, $key=""){
         if($key==null || $key=="")
        {
            $key="#%*34dalk";

        }
            $CI =& get_instance();

        $string = strtr(
                $string,
                array(
                    '.' => '+',
                    '-' => '=',
                    '~' => '/'
                )
            );
        $ret = $CI->encrypt->decode($string, $key);
        return $ret;
    }

}
// /////////////////////////////////
 ?>