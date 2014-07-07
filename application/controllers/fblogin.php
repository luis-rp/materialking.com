<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class fblogin extends CI_Controller 
{
	public function fblogin()
	{
		ini_set("memory_limit","512M");
		ini_set("max_execution_time", 700);
	    parent::__construct ();
	    
		/*$data ['title'] = 'Home';
		$this->load->dbforge();
		$this->load->model ('homemodel', '', TRUE);
		$this->load->model ('messagemodel', '', TRUE);
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/front/template', $data);*/
	}

	public function index()
	{
		$this->load->view('fblogin');
	}		
}
