<?php
class report extends CI_Controller 
{
	private $limit = 10;
	
	function report() 
	{
	    parent::__construct ();
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$data ['title'] = 'Dashboard';
		$this->load->dbforge();
		$this->load->model ('homemodel', '', TRUE);
		$this->load->model ('reportmodel', '', TRUE);
		$this->load->model ('quotemodel', '', TRUE);
		$this->load->model ('companymodel', '', TRUE);
		$this->load->model ('messagemodel', '', TRUE);
		$this->load->model ('admin/settings_model', '', TRUE);
		if($this->session->userdata('company')) $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/front/template', $data);
	}
	
	function index($offset = 0) 
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$uri_segment = 4;
		$offset = $this->uri->segment ($uri_segment);
		$reports = $this->reportmodel->get_reports ();
		
		$count = count ($reports);
		$items = array();
		if ($count >= 1) 
		{
			foreach ($reports as $report) 
			{
			    //echo '<pre>';print_r($report);die;
				$items[] = $report;
			}
			//echo '<pre>';print_r($items);die;
		    $data['reports'] = $items;
		}
		if(!$items)
		{
		    $this->data['message'] = 'No Records';
		}
        $query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('network')." n
        		  WHERE u.id=n.purchasingadmin AND n.company='".$company->id."'";
        //$query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u WHERE usertype_id=2 AND username IS NOT NULL";
        $data['purchasingadmins'] = $this->db->query($query)->result();
        
		$data ['addlink'] = '';
		$data ['heading'] = 'Report';
		$this->load->view ('company/report', $data);
	}
}
?>