<?php

class inventorymanagement extends CI_Controller 
{

    function inventorymanagement() 
    {
    	parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('form', 'url'));
        if (! $this->session->userdata('id'))
        {
            redirect('admin/login/index', 'refresh');
        }
        if ($this->session->userdata('usertype_id') == 3)
        {
            redirect('admin/dashboard', 'refresh');
        }
        $this->load->dbforge();
        $this->load->library('form_validation');
        $this->load->library(array('table', 'validation', 'session'));

        $this->load->helper('form', 'url');
      //  $this->load->model('admin/itemcode_model');
        $this->load->model('admin/quote_model');
        $this->load->model('admin/settings_model');
        $this->load->model('admin/inventorymanagement_model');
        $id = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($id);
		if(empty($setting)){
		$data['settingtour']=$setting;
		$data['timezone']='America/Los_Angeles';
		}else{
		$data['timezone']=$setting[0]->tour;
		$data['timezone']=$setting[0]->timezone;
		}
        $this->load->model('admin/catcode_model');
        $data['pendingbids'] = $this->quote_model->getpendingbids();
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data['title'] = "Administrator";
        $this->load = new My_Loader();
        $this->load->template('../../templates/admin/template', $data);
    }

    function index()
    {    	
    	$inventory = array();
    	$inventoryRes = $this->inventorymanagement_model->get_inventorydetails();
    	
    	if(count($inventoryRes) > 1)
    	{
	    	foreach ($inventoryRes as $row)
	        {
	        	$row->minstock = '<input type="text" name="minstock" id="minstock">';
	        	$row->maxstock = '<input type="text" name="maxstock" id="maxstock">';
	        	$row->reorderqty = '<input type="text" name="reorderqty" id="reorderqty">';
	        	$inventory[] = $row;
	        }
    	}    
        else 
        {
            $data['items'] = array();
            $this->data['message'] = 'No Records';
        }
      
    	$data['items'] = $inventory;
        $data['jsfile'] = 'inventorymanagementjs.php';  
    	
    	$data['heading'] = 'Inventory Management';
    	$this->load->view('admin/inventorymanagement', $data);
    }
} 