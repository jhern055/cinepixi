<?php
    include_once (dirname(__FILE__)."/db.php");
    $obj= new DB();

    function pr($string){
        echo "\n";
        print_r($string);
    }
    function clean_string($string){
        $string=str_replace("'", "\'", $string);
        return $string;
    }
    function select_path_id($name){
        global $obj;
            if(!empty($name)):
            $select="SELECT * FROM exp.cinepixi_pathFile where name=\"".$name."\"";
            $data=$obj->get_one_result($select,false);
            return $data["id"];
            endif;
    }
    function insert_update_path_or_file($data) {
        global $obj;
        $select="SELECT * FROM exp.cinepixi_pathFile where name='".$data["name"]."' and parentid='".$data["parentid"]."'";
        $last_id=0;

        if($obj->num_rows($select)):
        $obj->update("exp.cinepixi_pathFile",$data,array("name"=>$data["name"],"parentid"=>$data["parentid"] ),1);
            
            $select="SELECT * FROM exp.cinepixi_pathFile where name=\"".$data["name"]."\"";
            $data=$obj->get_one_result($select,false);

            $last_id=$data["id"];

        else:
            $obj->insert("exp.cinepixi_pathFile", $data );
            $last_id=$obj->lastid();
        endif;

        return $last_id;
    } 

    function sync_PATHS($dir,$parentid=0,$last_path) { 
$dir_clean="/media/dell/67F18E800D673AB3/1-Musica";
        
        $k=array();
        global $obj;

        $cdir = scandir($dir); 
        foreach ($cdir as $key => $value) {
            if (!in_array($value,array(".",".."))) {

                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {

                $dir_path=$dir . DIRECTORY_SEPARATOR . $value;
                $before="";
                $before=explode("/",substr($dir_path, 1));
                if(count($before)>2){
                array_pop($before);
   
                    if($name=end($before) and $name!=$last_path)
                    $parentid=select_path_id($name);

                }
                if($name==$last_path)
                $parentid=0;

                    $data=array(
                        "name"      =>clean_string($value),
                        "real_path" =>clean_string($dir_path),
                        "link"      =>clean_string(str_replace($dir_clean, "", $dir_path)),
                        "file"      =>"",
                        "parentid"  =>$parentid
                        );
                    $parentid=insert_update_path_or_file($data);
                    $k[$value]=sync_PATHS($dir_path,$parentid,$last_path);
                    // si el que inserto es path no modificar el parentid
                    $path_dir=str_replace(
                            array(" ","(",")"), 
                            array("\\ ","\(","\)"), 
                            $dir_path);
                    $_SESSION["paths"]["path_dir"][$parentid]=$path_dir;

                }else{
                
                $dir_path=$dir . DIRECTORY_SEPARATOR . $value;

                    // si no esta en este path uno mas arriba
                    if(!empty($_SESSION["paths"]["path_dir"][$parentid]) and !file_exists($_SESSION["paths"]["path_dir"][$parentid]."/".$value)){
                        $parentid-=1;
                        pr($value) ;
                    }

                    $k[] = $value; 
                    $data=array(
                         "name"      =>clean_string($value),
                         "real_path" =>"",
                         "link"      =>"",
                         "file"      =>1,
                         "parentid"  =>$parentid
                         );
                    insert_update_path_or_file($data);
                }
            
            }
        }

   return $k; 

    }

// $dir="/media/dell/67F18E800D673AB3/tmp_nueva";
// $dir="/media/dell/67F18E800D673AB3/tmp_nueva";
// $dir="/media/dell/67F18E800D673AB3/1-Musica";
$dir="/media/dell/67F18E800D673AB3/1-Musica";
    
$last_path=explode("/",$dir);
// example /DATA
$last_path=end($last_path);
$response=sync_PATHS($dir,0,$last_path);

// $response=fix_name($dir);
// print_r($_SESSION["paths"]);
// print_r($response);
function  real_dir($dir_path){
                   $path_dir=str_replace(
                            array(" ","(",")","-","'"), 
                            array("\\ ","\(","\)","\-","\'"), 
                            $dir_path);
return $path_dir;  
}

function fix_name($dir) { 
        
        $k=array();
        global $obj;

        $cdir = scandir($dir); 
        foreach ($cdir as $key => $value) {
            if (!in_array($value,array(".",".."))) {

                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {

                    $dir_path=$dir . DIRECTORY_SEPARATOR . $value;
                    $dir_path_fix=$dir . DIRECTORY_SEPARATOR . ucwords(strtolower($value));

                    $path_real=real_dir($dir_path);
                    $mv_path=real_dir($dir_path_fix);
                    // shell_exec("mv ".$path_real." ".$mv_path);
                    $k[$value]=fix_name($dir_path);

                }else{
                
                    if($value=str_replace("'", "", $value)){
                    $dir_path=$dir . DIRECTORY_SEPARATOR . $value;
                    $dir_path_fix=$dir . DIRECTORY_SEPARATOR . ucwords(strtolower($value));
                    
                    $path_real="";
                    $mv_path="";

                    $path_real=real_dir($dir_path);
                    $mv_path=real_dir($dir_path_fix);
                    
                        if($path_real!=$mv_path)
                        shell_exec("mv ".$path_real." ".$mv_path);
                    }

                    $k[] = $value; 
                }
            
            }
        }

   return $k; 

}

?>