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


		
			$post = $this->input->post();
			$cid = $this->input->post("cid");
			unset($post["cid"]);
			unset($post["subscribe"]);
		

			
			
			$data;
			if ($this->session->userdata('site_loggedin'))
			{
				$data = array("cid"=>$cid,"uid"=>$this->session->userdata('userid'));
			}else{
				$data = array("cid"=>$cid);
			}
			$this->db->insert("newsletter_subscribers",$data);
			$lastid = $this->db->insert_id();
			
			foreach ($post as $key=>$value){
			
				$this->db->insert("newsletter_subscribers_data",array("subscriber_id"=>$lastid,"name"=>$key,"value"=>$value));
			}
			
			$supplier = $this->supplier_model->get_supplierbyid($cid);
			redirect('site/supplier/'.$supplier->username);
	
	}
	
	public function sendNewsletter($tid){
		
		$this->db->where("id",$tid);
		$template = $this->db->get("newsletter_template")->row();
		/*
		 * 	$this->db->select("newsletter_subscribers_data.name,newsletter_subscribers_data.value");
		$this->db->from("newsletter_subscribers");
		$this->db->join("newsletter_subscribers_data","newsletter_subscribers.id=newsletter_subscribers_data.subscriber_id");
		$this->db->where("newsletter_subscribers.cid",$this->session->userdata('company')->id);
		$subscribers = $this->db->get()->result();
		
		log_message("debug",var_export($subscribers,true));
		 */
		$this->db->where("cid",$this->session->userdata('company')->id);
		$subscribers = $this->db->get("newsletter_subscribers")->result();
		
		$company = $this->companymodel->getcompanybyid($this->session->userdata('company')->id);
		$ok=0;
		$errors=0;
		foreach($subscribers as $item){
			
			$template_content = $template->body;
			$this->db->where("subscriber_id",$item->id);
			$subscribers_data = $this->db->get("newsletter_subscribers_data")->result();
			$email;
			foreach($subscribers_data as $vars){
				$template_content = str_replace("{".$vars['name']."}",$vars['value'],$template_content);
				if($vars['name']="email")
					$email = $vars['name'];
			}
			$this->email->clear();
			$this->email->to($email);
			$this->email->from('your@example.com');
			$this->email->subject('Newsletter from  '.$company->title);
			$this->email->message($template_content);
			if($this->email->send())
				$ok++;
			else 
				$errors++;
		}
		
		$this->db->insert("newsletter_analytics",array("tid"=>$tid,"numSent"=>$ok,"numErrors"=>$errors));;
		
		/*foreach($subscribers as $subs){
			

			$this->email->clear();
			$this->email->to($subs->mail);
			$this->email->from('your@example.com');
			$this->email->subject('Newsletter from  '.$company->title);
			$body = $template->body;
			$body = str_replace("{name}",$subs->name,$body);
			$body = str_replace("{mail}",$subs->mail,$body);
			$this->email->message($body);
			$this->email->send();
			
			
		}*/
		$this->session->set_flashdata('message', 'The newsletter was sent');
		redirect("company/mailinglist");
	}
}
