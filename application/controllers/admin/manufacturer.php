<?php

class manufacturer extends CI_Controller {

    private $limit = 10;

    function manufacturer() {
        parent::__construct();
        $this->load->library('session');
        if (!$this->session->userdata('id')) {
            redirect('admin/login/index', 'refresh');
        }
        if ($this->session->userdata('usertype_id') == 3) {
            redirect('admin/dashboard', 'refresh');
        }
        $this->load->library('form_validation');
        $this->load->library(array('table', 'validation', 'session'));
        $this->load->helper('form', 'url');

        $this->load->dbforge();
        $this->load->model('admin/manufacturer_model');
        $this->load->model('admin/quote_model');
        $this->load->model('admin/order_model');
        $data['pendingbids'] = $this->quote_model->getpendingbids();
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data ['title'] = "Administrator";
        $this->load = new My_Loader();
        $this->load->template('../../templates/admin/template', $data);
    }

    function index($offset = 0) {
        $uri_segment = 4;
        $offset = $this->uri->segment($uri_segment);
        $manufacturers = $this->manufacturer_model->get_manufacturers($this->limit, $offset);

        $this->load->library('pagination');
        $config ['base_url'] = site_url('admin/manufacturer/index');
        $config ['total_rows'] = $this->manufacturer_model->total_manufacturer();
        $config ['per_page'] = $this->limit;
        $config ['uri_segment'] = $uri_segment;

        $this->pagination->initialize($config);
        $data ['pagination'] = $this->pagination->create_links();
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('ID', 'Name', 'Email', 'Actions');
        $i = 0 + $offset;

        $count = count($manufacturers);
        $items = array();
        if ($count >= 1) {
            foreach ($manufacturers as $manufacturer) {
                $manufacturer->actions = anchor('admin/manufacturer/update/' . $manufacturer->id, '<span class="icon-2x icon-edit"></span>', array('class' => 'update'))
                        . ' ' .
                        anchor('admin/manufacturer/delete/' . $manufacturer->id, '<span class="icon-2x icon-trash"></span>', array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"))
                ;
                $items[] = $manufacturer;
            }

            $data['items'] = $items;
            $data['jsfile'] = 'manufacturerjs.php';
        }
        else {
            $data['items'] = array();
            $this->data['message'] = 'No Records';
        }
        //print_r($data['projects']);die;

        $data ['addlink'] = '';
        $data ['heading'] = 'Manufacturer Management';
        $data ['table'] = $this->table->generate();
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/manufacturer/add">Add Manufacturer</a>';
        $this->load->view('admin/manufacturerlist', $data);
    }

    function add() {
        $this->_set_fields();
        $data ['heading'] = 'Add New Manufacturer';
        $data ['message'] = '';
        $data ['action'] = site_url('admin/manufacturer/add_manufacturer');
        $this->load->view('admin/manufacturer', $data);
    }

    function add_manufacturer() {
        $data ['heading'] = 'Add New Manufacturer';
        $data ['action'] = site_url('admin/manufacturer/add_manufacturer');

        $this->_set_fields();
        $this->_set_rules();

        if ($this->validation->run() == FALSE) {
            $data ['message'] = $this->validation->error_string;
			
            $this->load->view('admin/manufacturer', $data);
        }
        elseif ($this->manufacturer_model->checkDuplicateTitle($this->input->post('title'), 0)) {
            $data ['message'] = 'Duplicate Manufacturer';
            $this->load->view('admin/manufacturer', $data);
        } else {
            $itemid = $this->manufacturer_model->SaveManufacturer();
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Manufacturer Added Successfully</div></div>');
            redirect('admin/manufacturer');
        }
    }

    function update($id) {
        $this->_set_fields();
        $item = $this->manufacturer_model->get_manufacturers_by_id($id);
        $this->validation->id = $id;
        $this->validation->title = $item->title;
        $data ['heading'] = 'Update Manufacturer';
        $data ['message'] = '';
        $data ['action'] = site_url('admin/manufacturer/updatemanufacturer');
        $this->load->view('admin/manufacturer', $data);
    }

    function updatemanufacturer() {
        $data ['heading'] = 'Update Manufacturer';
        $data ['action'] = site_url('message/updatemanufacturer');
        $this->_set_fields();
        $this->_set_rules();

        $itemid = $this->input->post('id');

        if ($this->validation->run() == FALSE) {
            $data ['message'] = $this->validation->error_string;
            $data ['action'] = site_url('admin/manufacturer/updatemanufacturer');
            $this->load->view('admin/manufacturer', $data);
        }
        elseif ($this->manufacturer_model->checkDuplicateTitle($this->input->post('title'), $itemid)) {
            $data ['message'] = 'Duplicate Manufacturer';
            $this->db->where('id !=', $itemid);
            $this->load->view('admin/manufacturer', $data);
        } else {
            $this->manufacturer_model->updateManufacturer($itemid);
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Manufacturer Updated Successfully</div></div>');
            redirect('admin/manufacturer/update/' . $itemid);
            redirect('admin/manufacturer/index');
        }
    }

    function delete($id) {
        $this->manufacturer_model->remove_manufacturer($id);
        redirect('admin/manufacturer', 'refresh');
    }

    function _set_fields() {
        $fields ['id'] = 'id';
        $fields ['title'] = 'title';
        $fields ['cost'] = 'cost';
        $fields ['cdetail'] = 'cdetail';
        $fields ['parent'] = 'Parent';
        $fields ['project'] = 'Project';
        $this->validation->set_fields($fields);
    }

    function _set_rules() {
        $rules ['title'] = 'trim|required';
        $this->validation->set_rules($rules);
        $this->validation->set_message('required', '* required');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }
}

?>