<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class My_Controller extends CI_Controller
{
	function My_Controller()
	{
		parent::__construct();
		
		//session_start();
		//language switch ------------
		  
		if($this->session->userdata('language'))
 		{
  			$this->config->set_item('language',$this->session->userdata('language'));
			$this->lang->load('admin_common', $this->config->item('language'));
            $this->lang->load('common', $this->config->item('language'));
  		}
		else
		{
			$this->lang->load('admin_common', $this->config->item('language'));
            $this->lang->load('common', $this->config->item('language'));
		}
		//---------------------------------	
	}
	
	// check for authenticated user[administrator]
	function check_userlogin()
	{
		if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh'); 
		}
	}

    // checking site setting
	function checkSiteStatus()
	{
		$this->load->model('Site_setting_model');
		$data['site_info'] = $this->Site_setting_model->get_site_info('1');
		if($data['site_info']->status=='0')
		{
			//echo "offline";
			redirect('offline', 'refresh');
		}
	}
	
	function offlineStatus()
	{
		$this->load->model('Site_setting_model');
		$data['site_info'] = $this->Site_setting_model->get_site_info('1');
		if($data['site_info']->status=='1')
		{
			redirect(base_url());
		}
	}
	
}