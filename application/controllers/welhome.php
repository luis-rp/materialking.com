<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class welhome extends CI_Controller {

    public function welhome()
    {
        parent::__construct();
        // Your own constructor code
        $CI = & get_instance();
        $CI->config->load("facebook",TRUE);
        $config = $CI->config->item('facebook');
        $this->load->library('facebook', $config);
    }

    public function index()
    {
    	$this->load->model ('admin/adminmodel', '', TRUE);
    	$this->load->library('session');
	   /* if(!$this->session->userdata('id'))
		{
			redirect('admin/login', 'refresh'); 
		}*/
		$this->load->library ( array ('table', 'validation') );
		$this->load->helper ('url');
		
		$this->load->model('admin/quote_model');
        $this->load->model('admin/settings_model');
        $this->load->model('admin/company_model');
		
        $user = $this->facebook->getUser();
        if($user) {
            try {
                $user_info = $this->facebook->api('/me');
               // echo '<pre>'.htmlspecialchars(print_r($user_info, true)).'</pre>';
                 $request = $this->facebook->getSignedRequest();
        	/* echo '<pre>';
    		print_r($request);
    		echo '</pre>';*/
        	
       	if($this->session->userdata('usertype_id')==3)
		{  
			redirect('admin/dashboard', 'refresh'); 
		}
		$data ['message'] = '';
		$data ['userarrays'] = $this->adminmodel->getUserType ();
		$data ['title'] = 'Add New Admin User';
		$data ['action'] = site_url ('admin/admin/addAdminuser' );
		$data ['link_back'] = anchor ('admin/admin/index/', 'Back To List', array ('class' => 'back' ) );
		
		//$this->_set_fields ();
		//$this->_set_rules ();
		
		/*if ($this->validation->run () == FALSE) { 
			$data ['message'] = '';
			$this->load->view ( 'admin/adminEdit', $data );
		} else { echo "in else";*/
			//echo "<pre>",print_r($request['registration']['name']); die;
			$extName = $this->adminmodel->getAdminuserName ( $request['registration']['username'] );
			
			if ($extName == $request['registration']['username']) {
				$data ['message'] = '<div class="already">Username Already Exists.</div>';
				$this->load->view ('admin/adminEdit', $data);
			} else {
				$created_date = date ( "Y-m-d h:i:s" );
                //$geoloc = $this->company_model->getLatLong( $this->input->post ( 'address' ));
                
				$id = $this->adminmodel->savefb ($request['registration']);
				//$this->validation->id = $id;
				if($this->session->userdata('usertype_id')==2)
				{  
					$settings = (array)$this->settings_model->get_current_settings ();
				    $this->load->library('email');
				    $config['charset'] = 'utf-8';
				    $config['mailtype'] = 'html';
				     
				    $this->email->initialize($config);
					//$this->email->clear(true);
			        $this->email->from($settings['adminemail'], "Administrator");
			        $this->email->to($request['registration']['email']);
			        $link = '<a href="'.site_url('admin').'">Login</a>';
			        $data['email_body_title'] = "Dear ".$request['registration']['name'];
$data['email_body_content'] = "Your account is created with following details <br/><br/>
Username: {$request['registration']['username']}<br/><br/>
Password: {$request['registration']['password']}<br/><br/>
You can login from:<br/><br/>
$link";

$send_body = $this->load->view("email_templates/template",$data,TRUE);
		    $this->email->subject("EZPZ-P Account Created");
	        $this->email->message($send_body);	
	        $this->email->set_mailtype("html");
	        $this->email->send();
				}
				//$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">User Added Successfully</div></div>');
				$this->session->set_userdata('id',$id);
				redirect ( 'admin/admin/index/', 'refresh' );
			}
		//}
        	
        	
            } catch(FacebookApiException $e) {
                echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
                $user = null;
            }
        } else {
            echo "<a href=\"{$this->facebook->getLoginUrl()}\">Login using Facebook</a>";
        }
    }
}