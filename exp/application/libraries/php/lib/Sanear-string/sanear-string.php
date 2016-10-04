<?php
/**
 * Reemplaza todos los acentos por sus equivalentes sin ellos
 *
 * @param $string
 *  string la cadena a sanear
 *
 * @return $string
 *  string saneada
 */
function sanear_string($string){

    $string = trim($string);

    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );

    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );

    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );

    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );

    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );

    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );

    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", "", ":",
             "", ""),
        '',
        $string
    );


    return $string;
}

function sanitizeText($text){
  $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
  //$text = strtolower($text);
  $patron = array (
    // Espacios, puntos y comas por guion
    '/[\., ]+/' => ' ',

                // Vocales
                '/&agrave;/' => 'a',
                '/&egrave;/' => 'e',
                '/&igrave;/' => 'i',
                '/&ograve;/' => 'o',
                '/&ugrave;/' => 'u',

                '/&aacute;/' => 'a',
                '/&eacute;/' => 'e',
                '/&iacute;/' => 'i',
                '/&oacute;/' => 'o',
                '/&uacute;/' => 'u',

                '/&Aacute;/' => 'A',
                '/&Eacute;/' => 'E',
                '/&Iacute;/' => 'I',
                '/&Oacute;/' => 'O',
                '/&Uacute;/' => 'U',

                '/&acirc;/' => 'a',
                '/&ecirc;/' => 'e',
                '/&icirc;/' => 'i',
                '/&ocirc;/' => 'o',
                '/&ucirc;/' => 'u',

                '/&atilde;/' => 'a',
                '/&amp;etilde;/' => 'e',
                '/&amp;itilde;/' => 'i',
                '/&otilde;/' => 'o',
                '/&amp;utilde;/' => 'u',

                '/&auml;/' => 'a',
                '/&euml;/' => 'e',
                '/&iuml;/' => 'i',
                '/&ouml;/' => 'o',
                '/&uuml;/' => 'u',

                '/&auml;/' => 'a',
                '/&euml;/' => 'e',
                '/&iuml;/' => 'i',
                '/&ouml;/' => 'o',
                '/&uuml;/' => 'u',

                // Otras letras y caracteres especiales
                '/&aring;/' => 'a',
                '/&ntilde;/' => 'n',
                '/&Ntilde;/' => 'N',

                // Agregar aqui mas caracteres si es necesario

        );

        $text = preg_replace(array_keys($patron),array_values($patron),$text);
        //$text = strtoupper($text);
        return $text;
}
function LineToCheckOutput($output){
  $output = preg_replace('/\t{1,}/', ' ', $output);
  $output = preg_replace('/\n{1,}/', ' ', $output);
  $output = preg_replace('/\r{1,}/', ' ', $output);
  $output = preg_replace('/\s{1,}/', ' ', $output);
  $output = str_replace('> <', '><', $output);
  $output = str_replace(' >', '>', $output);
  return $output;
}