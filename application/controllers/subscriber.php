<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');
class subscriber extends CI_Controller
{
	public function subscriber(){
		parent::__construct();
	}
	
	public function addsubscriber(){
		
			$name = $this->input->post("name");
			$mail = $this->input->post("mail");
			$cid = $this->input->post("cid");
			
			$this->db->where("mail",$mail);
			$this->db->where("cid",$cid);
			if(empty($this->db->get("newsletter_subscribers")->result())){
				$this->db->insert("newsletter_subscribers",array("cid"=>$cid,"name"=>$name,"mail"=>$mail));
				$this->session->set_flashdata('message',"Thank you for the subscription");
				$this->db->where("id",$cid);
				$supplier = $this->db->get("company")->row();
				
				redirect(base_url('site/supplier/' . $supplier->username));
			}else{
				//The mail already exists
				$this->session->set_flashdata('message',"The user already exists");
				$this->db->where("id",$cid);
				$supplier = $this->db->get("company")->row();
				redirect(base_url('site/supplier/' . $supplier->username));
			}
	}
}