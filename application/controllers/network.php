<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class network extends CI_Controller {

    public function network() {
        ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 700);
        parent::__construct();

        $data ['title'] = 'Home';
        $this->load->dbforge();
        $this->load->model('homemodel', '', TRUE);
        $this->load = new My_Loader();
        $this->load->template('../../templates/site/template', $data);
    }

    public function index() {
        $this->load->view('site/index');
    }

    public function login($type) {
        if ($type != 'users') {
            die('Error');
        }
        if (!$_POST)
            die('Error');
        $_POST['usertype_id'] = 2;
        $_POST['password'] = md5($_POST['password']);
        $_POST['status'] = 1;
        $this->db->where($_POST);
        $row = $this->db->get('users')->row();
        //print_r($_POST);
        if ($row) {

            if ((!$row->user_lat || !$row->user_lng) && $row->address) {
                $geocode = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=" . urlencode(str_replace("\n", ", ", $row->address)) . "&sensor=false");
                $output = json_decode($geocode);
                $update = array();
                $row->user_lat = $update['user_lat'] = $output->results[0]->geometry->location->lat;
                $row->user_lng = $update['user_lng'] = $output->results[0]->geometry->location->lng;

                $this->db->where('id', $row->id);
                $this->db->update('users', $update);
            }
            $temp = array();
            $temp['site_loggedin'] = $row;
            $this->session->set_userdata($temp);
            
            $this->session->set_userdata($row);
            
            die('Success');
        } else {
            die('Error');
        }
    }

    public function logout() 
    {
        $temp['site_loggedin'] = null;
        $this->session->set_userdata($temp);
	    $this->session->sess_destroy();
        redirect('site', 'refresh');
    }

    public function join() {
        if (!$_POST)
            die('Error');
		
       
        $_POST['fromtype'] = 'users';
        $_POST['fromid'] = $this->session->userdata('site_loggedin')->id;
        $this->db->where($_POST);
        if(!$this->db->get('joinrequest')->row())
        {
            //print_r($_POST);die;
            $_POST['requeston'] = date('Y-m-d H:i:s');
            $this->db->insert('joinrequest', $_POST);
            
         
            $supplier = $this->db->where('id',$_POST['toid'])->get('company')->row();
            $company = $this->db->where('id',$_POST['fromid'])->get('users')->row();
            
            if($_POST['accountnumber']=="")
            	$_POST['accountnumber'] = "none";
            
            if($_POST['wishtoapply'] ==1)
            	$wish = "Yes";
            else
            	$wish = "No";		
            	
            $body = "Dear " . $supplier->title . ",<br><br>
            ". $company->companyname." just sent you a request to join in your newtork. 
            The following information was sent:   
            Account Number: ".$_POST['accountnumber']."
            <br/>
            Application Sent: ".$wish."
            <br/>
            Message: ".$_POST['message']."
            <br/>
            You can login on your dasboard and accept or deny request.
            <br><br>";
            
            //echo $body;
            
            $this->load->library('email');
            $this->email->from($company->email, $company->companyname);
            $this->email->to($supplier->title . ',' . $supplier->primaryemail);
            $this->email->subject('Request to Join Network.');
            $this->email->message($body);
            $this->email->set_mailtype("html");
            $this->email->send();
            
        }
        die('Success');
    }

}
