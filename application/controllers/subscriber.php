<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');
class subscriber extends CI_Controller
{
	public function subscriber(){
		parent::__construct();
		$this->load->model('companymodel', '', TRUE);
		$this->load->library('email');
	}
	
	public function addsubscriber(){
		
			$name = $this->input->post("name");
			$mail = $this->input->post("mail");
			$cid = $this->input->post("cid");
			
			$this->db->where("mail",$mail);
			$this->db->where("cid",$cid);
			$res = $this->db->get("newsletter_subscribers")->result();
			if(empty($res)){

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
	
	public function sendNewsletter($tid){
		
		$this->db->where("id",$tid);
		$template = $this->db->get("newsletter_template")->row();
		
		$this->db->where("cid",$this->session->userdata('company')->id);
		$subscribers = $this->db->get("newsletter_subscribers")->result();
		
		$company = $this->companymodel->getcompanybyid($this->session->userdata('company')->id);
		foreach($subscribers as $subs){
			

			$this->email->clear();
			$this->email->to($subs->mail);
			$this->email->from('your@example.com');
			$this->email->subject('Newsletter from  '.$company->title);
			$body = $template->body;
			$body = str_replace("{name}",$subs->name,$body);
			$body = str_replace("{mail}",$subs->mail,$body);
			$this->email->message($body);
			$this->email->send();
			
			echo $this->email->print_debugger();
		}
		$this->session->set_flashdata('message', 'The newsletter was sent');
		//redirect("company/mailinglist");
	}
}
