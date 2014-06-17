<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller 
{
	public function Home()
	{
		ini_set("memory_limit","512M");
		ini_set("max_execution_time", 700);
	    parent::__construct ();
	    
		$data ['title'] = 'Home';
		$this->load->dbforge();
		$this->load->model ('homemodel', '', TRUE);
		$this->load->model ('messagemodel', '', TRUE);
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/front/template', $data);
	}

	public function index()
	{
		redirect ('site', 'refresh' );
	}
	
	public function quote($key)
	{
		redirect('quote/invitation/'.$key);
	}
	
	public function backtrack($key,$print='')
	{
		redirect('quote/backtrack/'.$key);
	}
		
	public function message($key)
	{
		$message = $this->messagemodel->getmessage($key);
		if(!$message)
		{
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">Invalid Key.</div></div></div>');
			redirect('message/messages');
		}
		else 
		{
			redirect('message/viewmessage/'.$message->id);
		}
	}
}
