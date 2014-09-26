<?php

class manage_network extends CI_Controller {

    function manage_network() {
        parent::__construct();
        $this->load->library('session');
        if (!$this->session->userdata('id')) {
            redirect('admin/login/index', 'refresh');
        }
        if ($this->session->userdata('usertype_id') == 3) {
            redirect('admin/dashboard', 'refresh');
        }
        $this->load->dbforge();
        $this->load->library('form_validation');
        $this->load->library(array('table', 'validation', 'session'));
        $this->load->helper('form', 'url');
        $this->load->model('admin/company_model');
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
        $this->load->model('admin/quote_model');
        $data['pendingbids'] = $this->quote_model->getpendingbids();
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data ['title'] = "Administrator";
        $this->load = new My_Loader();
        $this->load->template('../../templates/admin/template', $data);
    }

    function index()
      {

      	$sql = "SELECT DISTINCT n.purchasingadmin,u.companyname, u.email FROM ".$this->db->dbprefix('network')." n join ".$this->db->dbprefix('users')." u on n.purchasingadmin = u.id";
      	//$sql = "SELECT DISTINCT n.company,u.companyname, u.email FROM ".$this->db->dbprefix('network')." n join ".$this->db->dbprefix('users')." u on n.company = u.id
				//WHERE purchasingadmin=".$this->session->userdata('id');
		$pc = $this->db->query($sql)->result();
		$data['pc'] = $pc;
		//echo "<pre>"; print_r($pc); die;
        $this->load->view('admin/manage_network', $data);
      }


    function delete($pid)
    {
    	$this->db->delete('network', array('purchasingadmin' => $pid));
    	$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Company Deleted Successfully From All Network.</div></div>');
    	redirect('admin/manage_network', 'refresh');
    }
}

?>