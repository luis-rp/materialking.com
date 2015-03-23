<?php

class Admodel extends Model {

    function Admodel() {
        parent::Model();
        $this->load->library('session');
    }
    function saveAd(){
    	/*$filedata = "";
    	if(!@$files['error'])
    	foreach($files['upload_data'] as $file){
    		$filedata .= $file['file_name']."|";

    	}*/
    	
    	$tag = $this->input->post('tags');
    	$tag = str_replace("\n",",",$tag);

    	//$filedata = trim($filedata, "|");
    	$company = $this->session->userdata('company');
    	$newAd = array(
    	"user_id"=>$company->id,
    	"title"=>$this->input->post("title"),
    	"price"=>$this->input->post("price"),
    	"address"=>$this->input->post("address"),
    	"category"=>$this->input->post("category"),
    	"itemid"=>$this->input->post("items"),
    	"latitude"=>$this->input->post("latitude"),
    	"longitude"=>$this->input->post("longitude"),
    	"image"=>"",
    	"description"=>$this->input->post("description"),
    	"published"=>		date('Y-m-d'),
    	"tags" => $tag,
    	"priceunit"=>$this->input->post("priceunit")
    	);
    	$res = $this->db->insert('ads', $newAd);
        $addid = $this->db->insert_id();
        //echo "<pre>"; print_r($_FILES['UploadFile']['name']); 
        //echo "<pre>"; print_r($_POST['alternate_imagename']); die;
    	if(isset($_FILES['UploadFile']['name']))
            {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/ads/';
            	$count=0;
            	foreach ($_FILES['UploadFile']['name'] as $filename)
            	{
            		$temp=$target;
            		$tmp=$_FILES['UploadFile']['tmp_name'][$count];
            		$origionalFile=$_FILES['UploadFile']['name'][$count];
            		$alternate_imagename = (isset($_POST['alternate_imagename'][$count]) && $_POST['alternate_imagename'][$count] != '') ? $_POST['alternate_imagename'][$count] : '';
            		$count=$count + 1;
            		$temp=$temp.basename($filename);
            		move_uploaded_file($tmp,$temp);
            		$temp='';
            		$tmp='';                  
					if(isset($filename) && $filename!=''){
            		$this->db->insert('AdImage', array('adid'=>$addid,'company' => $company->id, 'image' => $filename,'alternate_imagename'=>$alternate_imagename));}           		
            	}
            }   	   	
    	return $res;
    }

    function updateAd($id){
    	if(!$this->session->userdata('company'))
    	die;
    	/*$filedata = "";
    	if(!@$files['error'])
    	foreach($files['upload_data'] as $file){
    		$filedata .= $file['file_name']."|";

    	}*/
    	$tag = $this->input->post('tags');
    	$tag = str_replace("\n",",",$tag);

    	//$filedata = trim($filedata, "|");
    	/*if($filedata==""){
    		
    	$this->db->where("id",$this->input->post("adsid"));
    	$res = $this->db->get("ads")->result();
    	//$filedata = $res[0]->image;
    	}*/    	
    	$company = $this->session->userdata('company');
    	$newAd = array(
    	"user_id"=>$company->id,
    	"title"=>$this->input->post("title"),
    	"price"=>$this->input->post("price"),
    	"address"=>$this->input->post("address"),
    	"category"=>$this->input->post("category"),
    	"itemid"=>$this->input->post("items"),
    	"latitude"=>$this->input->post("latitude"),
    	"longitude"=>$this->input->post("longitude"),
    	"image"=>"",
    	"description"=>$this->input->post("description"),
    	"published"=>		date('Y-m-d'),
    	"tags" => $tag,
    	"priceunit"=>$this->input->post("priceunit")
    	);
    	$id = $this->input->post("adsid");
    	$this->db->update('ads', $newAd,array("id"=>$id));
    	
    	if(isset($_FILES['UploadFile']['name']))
            {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/ads/';
            	$count=0;
            	foreach ($_FILES['UploadFile']['name'] as $filename)
            	{
            		$temp=$target;
            		$tmp=$_FILES['UploadFile']['tmp_name'][$count];
            		$origionalFile=$_FILES['UploadFile']['name'][$count];
            		$alternate_imagename = (isset($_POST['alternate_imagename'][$count]) && $_POST['alternate_imagename'][$count] != '') ? $_POST['alternate_imagename'][$count] : '';
            		$count=$count + 1;
            		$temp=$temp.basename($filename);
            		move_uploaded_file($tmp,$temp);
            		$temp='';
            		$tmp='';

					if(isset($filename) && $filename!=''){
            		$this->db->insert('AdImage', array('adid'=>$id,'company' => $company->id, 'image' => $filename,'alternate_imagename'=>$alternate_imagename));}           		
            	}
            }   	
    }

    public function deleteAd($id)
    {
    	$image=$this->db->get_where('AdImage',array('adid'=>$id))->result();
        if(@count($image > 0))
        {
    	$this->db->delete('AdImage',array('adid'=>$id));}	
		$where = array('id'=>$id);
    	$query = $this->db->delete('ads',$where);   	
    	return 1;
    }
}
?>