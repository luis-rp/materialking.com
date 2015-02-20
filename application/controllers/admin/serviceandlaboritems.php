<?php
class serviceandlaboritems extends CI_Controller
{
	function serviceandlaboritems ()
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
        $this->load->model('admin/itemcode_model');
        $this->load->model('admin/quote_model');
        $this->load->model('admin/settings_model');
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

	public function index()
	{		
		$this->load->view('admin/serviceandlaboritems');
	}
}