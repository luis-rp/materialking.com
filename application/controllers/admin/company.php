<?php

class company extends CI_Controller {

    private $limit = 10;

    function company() {
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

    function index($offset = 0) {
        $uri_segment = 4;
        $offset = $this->uri->segment($uri_segment);
        $companys = $this->company_model->get_companys($this->limit, $offset);
        //print_r($companys);die;
        $this->load->library('pagination');
        $config ['base_url'] = site_url('admin/company/index');
        $config ['total_rows'] = $this->company_model->total_company();
        $config ['per_page'] = $this->limit;
        $config ['uri_segment'] = $uri_segment;

        $this->pagination->initialize($config);
        $data ['pagination'] = $this->pagination->create_links();
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('ID', 'Name', 'Email', 'Actions');
        $i = 0 + $offset;

        $count = count($companys);
        $items = array();
        if ($count >= 1) {
            foreach ($companys as $company) {
                $company->actions = anchor('admin/company/update/' . $company->id, '<span class="icon-2x icon-edit"></span>', array('class' => 'update'))
                        . ' ' .
                        anchor('admin/company/delete/' . $company->id, '<span class="icon-2x icon-trash"></span>', array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"))
                ;
                if ($company->poitems)
                    $company->actions .= ' ' . anchor('admin/company/poitems/' . $company->id, '<span class="icon-2x icon-search"></span>', array('class' => 'view'))
                    ;
                $items[] = $company;
            }

            $data['items'] = $items;
            $data['jsfile'] = 'companyjs.php';
        }
        else {
            $this->data['message'] = 'No Records';
        }
        $data ['addlink'] = '';
        $data ['heading'] = 'Company Management';
        $data ['table'] = $this->table->generate();
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/company/add">Add Company</a>';

        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}

        $this->load->view('admin/datagrid', $data);
    }

    function poitems($id) {
        $poitems = $this->company_model->getpoitems($id);
        //print_r($poitems);die;
        $count = count($poitems);
        $items = array();
        if ($count >= 1) {
            foreach ($poitems as $row) {
                $awarded = $this->quote_model->getawardedbid($row->quote);
                $row->totalamount = "$ " . $row->totalamount;
                $row->status = strtoupper($awarded->status);
                $row->actions = $row->status == 'COMPLETE' ? '' :
                        anchor('admin/quote/track/' . $row->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update'))
                ;
                $items[] = $row;
            }

            $data['items'] = $items;
        } else {
            $this->data['message'] = 'No Items';
        }
        $data['jsfile'] = 'companyitemjs.php';
        $data ['addlink'] = '';
        $data ['heading'] = "PO items";
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/company">&lt;&lt; Back</a>';

        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}

        $this->load->view('admin/datagrid', $data);
    }

    function add() {
        $this->_set_fields();
        $data ['heading'] = 'Add New Company';
        $data ['message'] = '';
        $data ['action'] = site_url('admin/company/add_company');
        $data['types'] = $this->db->get('type')->result();
        $data['states'] = $this->db->get('state')->result();
        $this->load->view('admin/company', $data);
    }

    function add_company() {
        $data ['heading'] = 'Add New Company';
        $data ['action'] = site_url('admin/company/add_company');

        $this->_set_fields();
        $this->_set_rules();

        if ($this->validation->run() == FALSE) {
            $data ['message'] = $this->validation->error_string;
            $data['types'] = $this->db->get('type')->result();
            $data['states'] = $this->db->get('state')->result();
            $this->load->view('admin/company', $data);
        } else {
            $key = md5(uniqid($_POST['title']) . '-' . date('YmdHisu'));
            $_POST['regkey'] = $key;
            $itemid = $this->company_model->SaveCompany();
            $this->sendRegistrationEmail($itemid, $key);
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Company Added Successfully</div></div>');
            redirect('admin/company');
        }
    }

    function update($id) {
        $this->_set_fields();
        $item = $this->company_model->get_companys_by_id($id);

        $this->validation->id = $id;
        $this->validation->title = $item->title;
        $this->validation->primaryemail = $item->primaryemail;
        $this->validation->username = $item->username;
        $this->validation->email = $item->email;
        $this->validation->contact = $item->contact;
        $this->validation->address = $item->address;
        $this->validation->street = $item->street;
        $this->validation->state = $item->state;
        $this->validation->city = $item->city;
        $this->validation->zip = $item->zip;
        $this->validation->pwd = $item->pwd;
        $types = $this->db->get('type')->result();
        $data['types'] = array();
        foreach ($types as $type) {
            $this->db->where('companyid', $id);
            $this->db->where('typeid', $type->id);
            if ($this->db->get('companytype')->result()) {
                $type->checked = true;
            } else {
                $type->checked = false;
            }
            $data['types'][] = $type;
        }
        $data ['heading'] = 'Update Company Item';
        $data ['message'] = '';
        $data ['action'] = site_url('admin/company/updatecompany');
        $query = $this->db->get_where('company', array('id' => $id));
        $data['company'] = $query->result();
        $data['states'] = $this->db->get('state')->result();
        $this->load->view('admin/company', $data);
    }

    function updatecompany()
    {
        $data ['heading'] = 'Update Company Item';
        $data ['action'] = site_url('message/updatecompany');
        $this->_set_fields();
        $this->_set_rules();

        $itemid = $this->input->post('id');

        if ($this->validation->run() == FALSE) {
            $data ['message'] = $this->validation->error_string;
            $data ['action'] = site_url('admin/company/updatecompany');
            $types = $this->db->get('type')->result();
            $data['types'] = array();
            foreach ($types as $type) {
                $this->db->where('companyid', $itemid);
                $this->db->where('typeid', $type->id);
                if ($this->db->get('companytype')->result()) {
                    $type->checked = true;
                } else {
                    $type->checked = false;
                }
                $data['types'][] = $type;
            }
            $query = $this->db->get_where('company', array('id' => $id));
            $data['company'] = $query->result();
            $data['states'] = $this->db->get('state')->result();
            $this->load->view('admin/company', $data);
        } else {
            $this->company_model->updateCompany($itemid);
            $data ['message'] = '<div class="success">Company has been updated.</div>';
            redirect('admin/company/update/' . $itemid);
            //redirect('admin/company/index');
        }
    }

    function delete($id) {
        $this->company_model->remove_company($id);
        redirect('admin/company', 'refresh');
    }

    function _set_fields() {
        $fields ['id'] = 'id';
        $fields ['title'] = 'title';
        $fields ['email'] = 'email';
        $fields ['contact'] = 'contact';
        $fields ['address'] = 'address';
        $fields ['street'] = 'street';
        $fields ['city'] = 'city';
        $fields ['state'] = 'state';
        $fields ['zip'] = 'zip';
        $fields ['password'] = 'password';
        $fields ['primaryemail'] = 'primaryemail';
        $this->validation->set_fields($fields);
    }

    function _set_rules() {
        $rules ['title'] = 'trim|required';
        $rules ['primaryemail'] = 'trim|required';
        $rules ['password']='trim|md5';
        $rules ['zip']='trim|integer|is_natural|required';
        $rules ['contact'] = 'trim|required';

        $this->validation->set_rules($rules);

        $this->validation->set_message('required', '* required');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }

    function sendRegistrationEmail($id, $key) {
        $c = $this->company_model->get_companys_by_id($id);

        $link = base_url() . 'company/complete/' . $key;
        $body = "Dear " . $c->title . ",<br><br>
	  	Please click following link to complete your registration:  <br><br>
	    <a href='$link' target='blank'>$link</a>";

        $settings = (array) $this->settings_model->get_current_settings();
        $this->load->library('email');
        $this->email->from($settings['adminemail'], "Administrator");

        $this->email->to($c->title . ',' . $c->email);

        $this->email->subject('Request to Join the Network.');
        $this->email->message($body);
        $this->email->set_mailtype("html");
        $this->email->send();
    }

}

?>