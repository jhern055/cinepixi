<?php

class Article_model extends CI_Model{

    public  $registred_by;
    public  $now;

	public function __construct() {
        parent::__construct();

    $this->now = date("Y-m-d H:i:s");
    $this->registred_by=$this->security->xss_clean($this->session->userdata("user_id"));

    }

    public function m_name($uri_string){

    $data=array();
    $q=$this->db->select("id,name,link")
                ->where("link",$uri_string)
                ->from("modules")
                ->get();

        if($q->result_array())
        foreach ($q->result_array() as $key => $value):
            $data=$value;
        endforeach;

    return $data;
    }
    
    public function insert_article_image($data){
        
        $this->db->insert("article_image",$data);
        
    return $this->db->insert_id();
    }

    public function get_articles_by_category($id_category){

    $data=array();
    $q=$this->db->select("article_id")
                ->where("category_id",$id_category)
                ->from("article_category")
                ->get()
                ;

        if($q->result_array())
        foreach ($q->result_array() as $key => $value):
        $data[$value["article_id"]]=$this->get_article_data($value["article_id"]);

        endforeach;

    return $data;
    }

// <article> 
    public function get_article_amount($query_search){

    $this->db->select('id');
    $this->db->from('article');

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $q=$this->db->get();

    return $q->num_rows();

    }

    public function get_article($start,$end,$query_search){

    $this->db->select("id,name");
    $this->db->from("article");

    if(!empty($query_search))
    foreach ($query_search as $k => $row)
    eval($row);

    $this->db->limit($start,$end);

    $this->db->order_by("id","asc");
    $q=$this->db->get();

    return $q->result_array();

    }

    public function get_article_data($id){

    $this->load->model("admin/stock/catalog/category/category_model");

    $data=array(
    "id"    =>"",
    "name"  =>"",
    "model" =>"",
    "sku"   =>"",
    "price" =>"",
    );

    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("article")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;

    return $data;

    }

    public function get_article_id($id){

    $this->load->model("admin/stock/catalog/category/category_model");

    $data=array(
    "id"    =>"",
    "name"  =>"",
    "model" =>"",
    "sku"   =>"",
    "price" =>"",
    );

    $q=$this->db->select(implode(",", array_keys($data)))
                ->where("id",$id)
                ->from("article")
                ->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $value)
    $data=$value;

    $data["categories"]=$this->category_model->get_category_to_option(false,false);

    if(!empty($data["id"]) ):
        
        if($data["article_category"]=$this->category_model->get_article_category($data["id"])):
            foreach ($data["article_category"] as $key => $value) {
            $data["article_category"][$key]["category_name"]=$this->category_model->get_category_text($value["category_id"]);
            }
        endif;

    endif;

    return $data;

    }

    // mismo registro
    public function record_same_article($data,$id){
        $ac=false;

        // if(!empty($id))
        $this->db->where_not_in("id",$id);

        $this->db->where($data);
        $row=$this->db->get("article");
        
        if($row->num_rows())
        $ac=true;    

        return $ac;
    }

    public function update_article($data,$id){
        
        $this->db->where("id",$id);
        $this->db->update("article",$data);

    return $id;
    }

    public function insert_article($data){
        
        $this->db->insert("article",$data);
        
    return $this->db->insert_id();
    }

    public function article_delete_it($id){

        $this->db->where("id",$id);
        if($this->db->delete("article"))
        return true;
    }
// </article>  
    public function get_articles_amount(){

       $q= $this->db->select('id')
                ->from('article')
                ->order_by('id','desc')
                ->get()
                ;
        return  $q->num_rows();
    }

    public function get_articles($start,$end,$vars_array){

    $this->db->select('id,name');
    $this->db->from('article');
    $this->db->order_by('id','desc');
    $this->db->limit($start,$end);

    if($vars_array)
    foreach ($vars_array as $k => $v)
    eval($v);

    $q=$this->db->get();

    return  $q->result_array();
    }

    public function get_article_text($id){

    $data["name"]="";
    $this->db->select('name');
    $this->db->where('id',$id);
    $this->db->from('article');

    $q=$this->db->get();

    if($q->result_array())
    foreach ($q->result_array() as $key => $row)
    $data=$row;

    return $data["name"];
    }

    public function get_articles_to_option($flip=null){

    $data=array();
    $this->db->select('id,name');
    $this->db->from('article');
    $this->db->order_by('id','desc');

    if($q=$this->db->get())
    foreach ($q->result_array() as $row) {
        if($flip)
        $data[$row["name"]]=$row["id"];
        else
        $data[$row["id"]]=$row["name"];
    }
    return $data;
    }


    // trear el registro
    public function get_article_by_id($id){

        $data=array();
        $this->db->select('*');
        $this->db->from('article');
        $this->db->where('id',$id);
        $this->db->limit(1);
        $q=$this->db->get();
        $data = $q->result_array();


        if($data)
        return $data[0];
    }

    // Traer el arreglo para usarse con el token input 

    public function get_articles_token_search($var_name=null){

    $this->db->select('id,name');
    $this->db->from('article');
    $this->db->order_by('id','desc');

    if($var_name)
    eval($var_name);

    if($q=$this->db->get())
    return  $q->result_array();

    }

    // INVANTARIO COMPRAS
    public function stock_movement_input_there($article,$purchase){

       $q=$this->db->select('id')
                ->where('purchase',$purchase)
                ->where('article',$article)
                ->get('stock_movement_input');

        return $q->num_rows();
    }

    public function insert_stock_movement_input($data_detail){

        $this->db->insert('stock_movement_input',$data_detail);
        return $this->db->insert_id();
    }

    public function update_stock_movement_input($article,$purchase,$data_detail){
        
        $this->db->where('article',$article);
        $this->db->where('purchase',$purchase);
        $this->db->update('stock_movement_input',$data_detail);
    }

    public function stock_movement_input_used($article){

       $q=$this->db->select('used')
                ->where('article',$article)
                ->get('stock_movement_input');
        $data=$q->result_array();
        if($data)
        return $data[0]["used"];
    }

    public function delete_stock_movement_input($purchase,$ids_discard){
        
        // ids de los articulos que estan como no  inventario
        foreach ($ids_discard as $k => $v) {

            if($this->stock_movement_input_used($v) <=0):    
            $this->db->where_in('article',$v);
            $this->db->where('purchase',$purchase);
            $this->db->delete('stock_movement_input');
            endif;
        }
    }

    // ...

    // INVANTARIO VENTAS
    // Checar cuantas entradas tengo en base de datos recibo el array del POST details "VENTAS"
    public function stock_movement_in_availability_check($ids_article_post){

    $details_in_stock=array();
    foreach ($ids_article_post as $k => $v) {

        
        $this->db->select_sum('quantity');
        $this->db->select_sum('used');
        $this->db->where('article',$v);
        $this->db->from('stock_movement_input');
     $q=$this->db->get();
     $qTmp=$q->result_array();

    $details_in_stock[$v]["quantity_available"]=$qTmp[0]["quantity"]-$qTmp[0]["used"];
    }

    return $details_in_stock;

    }

    // traer las cantidades de stock in
    public function stock_movement_in_quantity($article){

        $this->db->select('id,quantity');
        $this->db->where('article',$article);
        $this->db->from('stock_movement_input');
        $this->db->order_by('quantity','asc');
       $q= $this->db->get();
      return  $q->result_array();
    }

    public function stock_movement_in_substrac_used($id,$data){
    
        $this->db->where('id',$id);
        $this->db->update('stock_movement_input',$data);
    }

    public function stock_movement_output_there($stock_movement_input,$sale,$article){

       $q=$this->db->select('id')
                ->where('stock_movement_input',$stock_movement_input)
                ->where('sale',$sale)
                ->where('article',$article)
                ->get('stock_movement_output');
        // return $this->db->last_query();
        return $q->num_rows();
    }

    public function insert_stock_movement_output($data){
    
        $this->db->insert('stock_movement_output',$data);
    }

    public function update_stock_movement_output($id,$data){
    
        $this->db->where('id',$id);
        $this->db->update('stock_movement_output',$data);
    }

// <details>
    public function get_array_dad($module){

    $configs=array(
            "purchase"=>array(
                "from_dad"  =>"purchase",
                "from_det"  =>"purchase_details",
            ),
            "purchase_order"=>array(
                "from_dad"  =>"purchase_order",
                "from_det"  =>"purchase_order_details",
            ),
            "sale"=>array(
                "from_dad"  =>"sale",
                "from_det"  =>"sale_details",
            ),
            "remission"=>array(
                "from_dad"  =>"remission",
                "from_det"  =>"remission_details",
            ),
            "request"=>array(
                "from_dad"  =>"request",
                "from_det"  =>"request_details",
            ),
            "quatition"=>array(
                "from_dad"  =>"quatition",
                "from_det"  =>"quatition_details",
            ),
            "credit_note"=>array(
                "from_dad"  =>"credit_note",
                "from_det"  =>"credit_note_details",
            ),
            "opening_balance"=>array(
                "from_dad"  =>"opening_balance",
                "from_det"  =>"opening_balance_details",
            ),                        
        );

        // process
        $data=false;

        if($configs[$module])
         $data=$configs[$module];

        return $data;    
    }

    // trear el detalle de registro
    public function get_details_by_id($module,$id_record){

        $data=array();
        $config=$this->get_array_dad($module);
        $this->db->select('*');

        $this->db->where($config["from_dad"],$id_record);
        $this->db->from($config["from_det"]);

        $q=$this->db->get();

        $data = $q->result_array();

        if($data)
        foreach ($data as $k => $row) {
        $tmp_article=$this->get_article_by_id($row["article"]);
        $data[$k]["article_name"]=!empty($tmp_article["name"])?$tmp_article["name"]:"";
        }

        if($data)
        return $data;
    }

    public function record_details_there($module,$id,$id_record){

        $config=$this->get_array_dad($module);

        $this->db->select('id');
        $this->db->where('id',$id);

        $this->db->where($config["from_dad"],$id_record);

        $q=$this->db->get($config["from_det"]);

        return $q->num_rows();
    }

    public function update_details($module,$data,$id_record,$id){

        $config=$this->get_array_dad($module);
        $this->db->where('id',$id);
        $this->db->where($config["from_dad"],$id_record);

    // ..................
        $this->db->update($config["from_det"],$data);

        return $id;
    }

    public function insert_details($module,$data){
        $config=$this->get_array_dad($module);

        $this->db->insert($config["from_det"],$data);

        return $this->db->insert_id();
    }

    public function delete_details($module,$id_record,$timestamp){
        $config=$this->get_array_dad($module);

        $this->db->where($config["from_dad"],$id_record);
            
        $this->db->where('registred_on !=',$timestamp);
        $this->db->where('updated_on !=',$timestamp);

        $this->db->delete($config["from_det"]);
    }

    public function get_sale_details($module,$id,$id_record){
        $config=$this->get_array_dad($module);

        $this->db->where('id',$id);
        $this->db->select('*');

        $this->db->where($config["from_dad"],$id_record);
        $this->db->from($config["from_det"]);

        $q=$this->db->get();
        return $q->result_array();
    }

    public function update_dad_details($module,$data,$id_record){
        $config=$this->get_array_dad($module);

        $this->db->where('id',$id_record);
        $this->db->update($config["from_dad"],$data);
        
        return $id_record;
    }
// </details>
    public function processDetailArticle($source_module,$id_record){
    /* source_module: de que modulo viene 
   id_record: id que afectara el detalle */
        /*
        REVISAR antes de hacer el UPDATE o el INSERT checamos si tenemos existencias de los articulos 
        INVENTARIO
     */
    // $this->load->model("admin/stock/catalog/articl");

    // if(!empty($_POST["details"]) ){

    // $tmp_stock=function($details){return $this->stock_movement_output_details_availability_check($details);};
    // $return=$tmp_stock($_POST["details"]);

    // // MENSAJE SI NO TIENES DISPONIBLES EN EL INVENTARIO
    // if(empty($return["status"])):
    //  return print_r(
    //  json_encode(array(
    //      "status"=>false,
    //      "msg"=>$return["msg"],
    //      "data"=>$this->stock_movement_output_details_availability_check($_POST["details"]),
    //      "modal"=>1
    //  )));    
    // endif;
    // }
    // Insertar Detalle  o actualizar
$sub_total        ="";
$tax_ieps         ="";
$tax_iva          ="";
$tax_iva_retained ="";
$tax_isr          ="";
$total            ="";
    // if(empty($_POST["details"]) )
    // {return array("status"=>0,"msg"=>"Tiene que especificar almenos un articulo"); }

    if(!empty($_POST["details"]) )      
        foreach ($_POST["details"] as $k => $vdt) {

        if(empty($vdt["article"]))
        continue;   

        if(!empty($vdt["id"])):
        $id = strip_tags( $this->security->xss_clean(base64_decode($vdt["id"]))?:"");
        else:
        $id =0;
        endif;  

        $data               =array(
        $source_module      =>$id_record,
        "stockModification" =>isset($vdt["stockModification"])?trim(strip_tags( $this->security->xss_clean($vdt["stockModification"]) ) ):"",
        "quantity"          =>isset($vdt["quantity"])?trim(strip_tags( $this->security->xss_clean($vdt["quantity"]) ) ):"",
        "article"           =>isset($vdt["article"])?trim(strip_tags( $this->security->xss_clean($vdt["article"]) )) :"",
        "description"       =>isset($vdt["description"])?trim(strip_tags( $this->security->xss_clean($vdt["description"]) ) ):"",
        "price"             =>isset($vdt["price"])?trim(strip_tags( $this->security->xss_clean($vdt["price"]) ) ) :"",
        "totalSub"          =>isset($vdt["totalSub"])?trim(strip_tags( $this->security->xss_clean($vdt["totalSub"]) ) ):"",
        "taxIeps"           =>isset($vdt["taxIeps"])?trim(strip_tags( $this->security->xss_clean($vdt["taxIeps"]) ) ):"",
        "taxIva"            =>isset($vdt["taxIva"])?trim(strip_tags( $this->security->xss_clean($vdt["taxIva"]) ) ):"",
        "taxIvaRetained"    =>isset($vdt["taxIvaRetained"])?trim(strip_tags( $this->security->xss_clean($vdt["taxIvaRetained"]) ) ):"",
        "taxIsr"            =>isset($vdt["taxIsr"])?trim(strip_tags( $this->security->xss_clean($vdt["taxIsr"]) ) ):""
        );
    
    // procesar columnas de  la tabla padre
    $sub_total        +=$data["totalSub"];
    $tax_ieps         +=$data["taxIeps"];
    $tax_iva          +=$data["taxIva"];
    $tax_iva_retained +=$data["taxIvaRetained"];
    $tax_isr          +=$data["taxIsr"];
    // ...................

        // revisar si existe el registro si no insertarlo
        if($this->record_details_there($source_module,$id,$id_record)){
        
        $data_depend=array("updated_by" =>$this->registred_by,"updated_on" =>$this->now);

        $data=array_merge($data_depend,$data);  
        
            if(!$this->update_details($source_module,$data,$id_record,$id))
            { return array("status"=>0,"msg"=>"No se pudo actualizar el articulo"); }

        }

        else{

        $data_depend=array("registred_by" =>$this->registred_by,"registred_on" =>$this->now,$source_module =>$id_record);
        
        $data=array_merge($data_depend,$data);  

            if(!$this->insert_details($source_module,$data) )
            { return array("status"=>0,"msg"=>"No se pudo insertar el articulo"); } 

        }
        
        // INVENTARIO SI LLEGO HASTA AQUI ES PORQUE PASO LA VALIDACION DE LAS ENTRADAS
        // Grabar en un arreglo las cantidades de los articulos que vas a restar en la tabla de entrada
        // if(!empty($vdt["stockModification"]) ){
        // $articles_to_subtract[$vdt["article"]]["article"]=$vdt["article"];
        // $articles_to_subtract[$vdt["article"]]["quantity"][]=$vdt["quantity"];
        // }

        }

        // <actualizarPapa>
        if(!empty($sub_total)){
            $total=(($sub_total+$tax_ieps+$tax_iva)-$tax_iva_retained)-$tax_isr;
            $data_update=array(
                "sub_total"        =>number_format($sub_total,2,".",""),
                "tax_ieps"         =>number_format($tax_ieps,2,".",""),
                "tax_iva"          =>number_format($tax_iva,2,".",""),
                "tax_iva_retained" =>number_format($tax_iva_retained,2,".",""),
                "tax_isr"          =>number_format($tax_isr,2,".",""),
                "import"          =>number_format($total,2,".",""),
            );

            if(!$this->update_dad_details($source_module,$data_update,$id_record) )
            { return array("status"=>0,"msg"=>"No se pudo actualizar los importes del papa"); } 
        }
        // </actualizarPapa>

    // Borrar los articulos que  el elimino en el update
    if(!empty($id))
    $this->delete_details($source_module,$id_record,$this->now);
    // ...............................................................................

    // INVENTARIO
    // if(!empty($articles_to_subtract) ):

    // $tmp_stock=function($id_record,$timestamp,$articles_to_subtract){return $this->stock_movement_output_details_processing($id_record,$timestamp,$articles_to_subtract);};
    // $return=$tmp_stock($id_record,$this->now,$articles_to_subtract);

    // if(empty($return["status"]))
    // return print_r( json_encode( array("status"=>0,"msg"=>$return["msg"],"data"=>false,"modal"=>1) ));

    // endif;

    return array("status"=>1,"msg"=>"Se processo correctamente los detalles","data"=>false);

    }
}