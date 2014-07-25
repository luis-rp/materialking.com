<?php

class Admodel extends Model {

    function Admodel() {
        parent::Model();
        $this->load->library('session');
    }
    function saveAd($files=""){
    	$filedata = "";
    	foreach($files['upload_data'] as $file){
    		$filedata .= $file['file_name']."|";
    		
    	}
    	$company = $this->session->userdata('company');
    	$newAd = array(
    	"user_id"=>$company->id,
    	"title"=>$this->input->post("title"),
    	"price"=>$this->input->post("price"),
    	"location"=>$this->input->post("location"),
    	"latitude"=>$this->input->post("latitude"),
    	"longitude"=>$this->input->post("longitude"),
    	"image"=>$filedata,
    	"description"=>$this->input->post("description"),
    	"published"=>		date('Y-m-d')
    	);
    	$this->db->insert('ads', $newAd);
    }
}
?>