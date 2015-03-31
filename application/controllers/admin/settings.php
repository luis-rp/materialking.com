<?php
class Settings extends CI_Controller
{
	function Settings() {
		parent::__construct ();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh');
		}
		$this->load->library ('form_validation');
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$data ['title'] = 'Site Settings';
		$this->load->model('admin/settings_model');
		$id = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($id);
		if(empty($setting)){
		$data['settingtour']=$setting;
		$data['pagetour']=$setting;
		$data['timezone']='America/Los_Angeles';
		}else{
		$data['settingtour']=$setting[0]->tour;
		$data['pagetour']=$setting[0]->pagetour;
		$data['timezone']=$setting[0]->timezone;
		}
		//$this->load->helper('timezone');
		//date_default_timezone_set(bd_time());
		$this->load->model('admin/quote_model');
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}

	function index()
	{
		$data = $this->settings_model->get_current_settings ();
		$this->validation->id = 1;
		$this->validation->taxrate = $data->taxrate;
		$this->validation->adminemail = $data->adminemail;
		$this->validation->pricedays = $data->pricedays;
		$this->validation->pricepercent = $data->pricepercent;
		$this->validation->tour = $data->tour;
		$this->validation->pagetour = $data->pagetour;
		$this->validation->timezone = $data->timezone;
		$var ['action'] = site_url ('admin/settings/update');
		$this->load->view ('admin/settings', $var);
	}

	function _set_fields()
	{
		$fields ['id'] = 'id';
		$fields ['taxrate'] = 'taxrate';
		$fields ['adminemail']= 'adminemail';
		$fields ['pricedays'] = 'pricedays';
		$fields ['pricepercent']= 'pricepercent';
		$fields ['tour']= 'tour';
		$fields ['pagetour']= 'pagetour';
		$fields ['timezone']= 'timezone';
		$this->validation->set_fields ($fields);
	}

	function _set_rules()
	{
		$rules ['taxrate'] = 'trim|required';
		$rules ['adminemail']= 'trim|required';
		$rules ['pricedays'] = 'trim|required';
		$rules ['pricepercent']= 'trim';
		$this->validation->set_rules ( $rules );
		$this->validation->set_message ( 'required', '* required' );
		$this->validation->set_error_delimiters ( '<div class="error">', '</div>');
	}

	function update()
	{
		$data ['heading'] = 'Update Settings';
		$this->_set_fields ();
		$this->_set_rules ();
		if(isset($_POST['tour']))
		{
			$_POST['tour']=1;
		}

		if(isset($_POST['pagetour']))
		{
			$_POST['pagetour']=1;
		}

		$id = $this->input->post ('id');
		if ($this->validation->run () == FALSE)
		{
			$data ['message'] = $this->validation->error_string;
		    $data ['action'] = site_url ('admin/settings/update');
			$this->load->view ('admin/settings', $data);
		}
		else
		{
			$this->settings_model->updatesettings ($id);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Settings has been Updated.</div></div>');;
			redirect('admin/settings', $data);

		}
	}

}

?>