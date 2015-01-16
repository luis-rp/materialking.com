<?php

class Admodel extends Model {

    function Admodel() {
        parent::Model();
        $this->load->library('session');
    }
    function saveAd($files=""){
    	$filedata = "";
    	if(!@$files['error'])
    	foreach($files['upload_data'] as $file){
    		$filedata .= $file['file_name']."|";

    	}
    	$tag = $this->input->post('tags');
    	$tag = str_replace("\n",",",$tag);

    	$filedata = trim($filedata, "|");
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
    	"image"=>$filedata,
    	"description"=>$this->input->post("description"),
    	"published"=>		date('Y-m-d'),
    	"tags" => $tag
    	);
    	$res = $this->db->insert('ads', $newAd);    	
    	return $res;
    }

    function updateAd($files=""){
    	$filedata = "";
    	if(!@$files['error'])
    	foreach($files['upload_data'] as $file){
    		$filedata .= $file['file_name']."|";

    	}
    	$tag = $this->input->post('tags');
    	$tag = str_replace("\n",",",$tag);

    	$filedata = trim($filedata, "|");
    	if($filedata==""){
    		
    	$this->db->where("id",$this->input->post("adsid"));
    	$res = $this->db->get("ads")->result();
    	$filedata = $res[0]->image;
    	}    	
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
    	"image"=>$filedata,
    	"description"=>$this->input->post("description"),
    	"published"=>		date('Y-m-d'),
    	"tags" => $tag
    	);

    	$id = $this->input->post("adsid");
    	$this->db->update('ads', $newAd,array("id"=>$id));
    }

    public function deleteAd($id)
    {
    	$where = array('id'=>$id);
    	$query = $this->db->delete('ads',$where);
    	return 1;
    }
}
?>