<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_uuid'))
{
function get_uuid($invoice_electronic_xml_file_path){

  // $UUID="";
  if(file_exists($invoice_electronic_xml_file_path)){

    $xml_file = simplexml_load_file($invoice_electronic_xml_file_path); 
    $ns = $xml_file->getNamespaces(true);
    $xml_file->registerXPathNamespace('c', $ns['cfdi']);
    $xml_file->registerXPathNamespace('t', $ns['tfd']);

    foreach ($xml_file->xpath('//t:TimbreFiscalDigital') as $tfd):
    $UUID=$tfd['UUID'];
    $UUID=(string)$UUID;
    endforeach;
  }
  
  if(!empty($UUID))
  return $UUID;
  
}

}

if ( ! function_exists('xmlstr_to_array'))
{
  function xmlstr_to_array($xmlstr) {
    $doc = new DOMDocument();
    $doc->loadXML($xmlstr);
    $root = $doc->documentElement;
    $output = domnode_to_array($root);
    $output['@root'] = $root->tagName;
    return $output;
  }
}

if ( ! function_exists('domnode_to_array'))
{
function domnode_to_array($node) {
  $output = array();
  switch ($node->nodeType) {
    case XML_CDATA_SECTION_NODE:
    case XML_TEXT_NODE:
      $output = trim($node->textContent);
    break;
    case XML_ELEMENT_NODE:
      for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
        $child = $node->childNodes->item($i);
        $v = domnode_to_array($child);
        if(isset($child->tagName)) {
          $t = $child->tagName;
          if(!isset($output[$t])) {
            $output[$t] = array();
          }
          $output[$t][] = $v;
        }
        elseif($v || $v === '0') {
          $output = (string) $v;
        }
      }
      if($node->attributes->length && !is_array($output)) { //Has attributes but isn't an array
        $output = array('@content'=>$output); //Change output into an array.
      }
      if(is_array($output)) {
        if($node->attributes->length) {
          $a = array();
          foreach($node->attributes as $attrName => $attrNode) {
            $a[$attrName] = (string) $attrNode->value;
          }
          $output['@attributes'] = $a;
        }
        foreach ($output as $t => $v) {
          if(is_array($v) && count($v)==1 && $t!='@attributes') {
            $output[$t] = $v[0];
          }
        }
      }
    break;
  }
  return $output;
}

}
?>