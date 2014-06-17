<?php
class report extends CI_Controller 
{
	private $limit = 10;
	
	function report() 
	{
		parent::__construct ();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh'); 
		}
		if($this->session->userdata('usertype_id')==3)
		{
			redirect('admin/dashboard', 'refresh'); 
		}
		$this->load->library ('form_validation');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$this->load->model('admin/report_model');
		$this->load->model('admin/settings_model');
		$this->load->model('admin/company_model');
		$this->load->model('admin/quote_model');
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$data ['title'] = "Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}
	
	function index($offset = 0) 
	{
		$uri_segment = 4;
		$offset = $this->uri->segment ($uri_segment);
		$reports = $this->report_model->get_reports ();
		
		$count = count ($reports);
		$items = array();
		if ($count >= 1) 
		{
			foreach ($reports as $report) 
			{
				$items[] = $report;
			}
			//echo '<pre>';print_r($items);die;
		    $data['reports'] = $items;
		}
		if(!$items)
		{
		    $this->data['message'] = 'No Records';
		}
        $query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
        $data['companies'] = $this->db->query($query)->result();
        
        $data['settings'] = $this->settings_model->get_current_settings();
        
		$data ['addlink'] = '';
		$data ['heading'] = 'Report';
		$this->load->view ('admin/report', $data);
	}
	
	function payinvoice()
	{
	    $this->db->where($_POST)->update('received',array('paymentstatus'=>'Paid'));
	    redirect('admin/report');
	}
	
}
?>