<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');
class classified extends CI_Controller
{
    public function classified()
    {
    	ini_set("memory_limit", "512M");
    	ini_set("max_execution_time", 700);
    	parent::__construct();
    	$data['title'] = 'Home';
    	$this->load = new My_Loader();
    	$this->load->template('../../templates/classified/template', $data);
    }
    
    public function index(){
    	$data['title'] = "Classified area";
    	$this->load->view('classified/index', $data);
    }
    public function ad($id){
    	 
    	$sql = "SELECT c.title c_title,c.address c_address,c.logo c_logo,c.username c_username,a.id a_id,a.title a_title,a.description a_description,a.price a_price,a.location a_location,a.latitude a_latitude,a.longitude a_longitude,a.published a_published, a.image a_image,a.views a_views FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('ads')." a WHERE a.id=".$id." AND a.user_id=c.id";
    	$data = $this->db->query($sql)->row_array();
    	
    	
    	$sql_rel =  "SELECT * FROM ".$this->db->dbprefix('ads')." WHERE category=(SELECT category FROM ".$this->db->dbprefix('ads')." WHERE id=".$id.") AND id<>".$id;
    	$data['related'] = $this->db->query($sql_rel)->result_array();
    	$this->load->view('classified/ad',$data);
    }
}