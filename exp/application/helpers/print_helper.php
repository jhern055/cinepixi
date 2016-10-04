<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter URL Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/url_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */

//  para imprimir 
if ( ! function_exists('pr'))
{
    function pr($d, $echo = TRUE)
	{
	   if($echo)
	   {
		   echo '<pre><font color=#C00000>'.print_r($d, true).'</font></pre>';
	   }
	   else
	   {
	      return '<pre><font color=#C00000>'.print_r($d, true).'</font></pre>';
	   } 
	}
}
 
if ( ! function_exists('prd'))
{
    function prd($d) 
	{ 	
	   mpr($d); 	
	   die; 
	}
}
 
if ( ! function_exists('prr'))
{
    function prr($d) 	
	{
	   echo '<pre>'.var_dump($d, true).'</pre>'; 
	}
}

if ( ! function_exists('rt'))
{

	function rt($success=null,$msg=null,$data){

	      if(is_object($data) || is_array($data)) {

	      	if($success)
			return print_r(json_encode(array("status"=>1,"msg"=>$msg,"data"=>$data) ) );
			else if($msg)
					return print_r(json_encode(array("status"=>0,"msg"=>var_export($msg,true),"data"=>$data) ) );
					else return print_r(json_encode(array("status"=>0,"msg"=>var_export($data,true),"data"=>false) ) );
	      
	      } else {

	      	if($success)
			return print_r(json_encode(array("status"=>1,"msg"=>$msg,"data"=>$data) ) );
			else if($msg)
					return print_r(json_encode(array("status"=>0,"msg"=>$msg,"data"=>$data) ) );
					else return print_r(json_encode(array("status"=>0,"msg"=>var_export($data,true),"data"=>$data) ) );

	      } 

	}

}

if ( ! function_exists('pj'))
{
	function pj($array){

		print_r(json_encode($array) );

	}

}
/* End of file url_helper.php */
/* Location: ./system/helpers/url_helper.php */