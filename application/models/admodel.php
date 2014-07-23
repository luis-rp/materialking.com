<?php

class Admodel extends Model {

    function Admodel() {
        parent::Model();
        $this->load->library('session');
    }
    function saveAd(){
    	$company = $this->session->userdata('company');
    	$newAd = array(
    	"user_id"=>$company->id,
    	"title"=>$this->input->post("title"),
    	"price"=>$this->input->post("price"),
    	"location"=>$this->input->post("location"),
    	"latitude"=>$this->input->post("latitude"),
    	"longitude"=>$this->input->post("longitude"),
    	"image"=>$_FILES["adfile"]["name"],
    	"description"=>$this->input->post("description"),
    	);
    	$this->db->insert('ads', $newAd);
    }
}
?>