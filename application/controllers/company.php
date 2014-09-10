<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Company extends CI_Controller {

    public function Company() {
        ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 700);
        parent::__construct();
        $data ['title'] = 'Dashboard';
        $this->load->dbforge();
        $this->load->model('quotemodel', '', TRUE);
        $this->load->model('companymodel', '', TRUE);
        $this->load->model('messagemodel', '', TRUE);
        $this->load->model('admodel', '', TRUE);
        $this->load->model('admin/settings_model');
        $this->load->model('admin/catcode_model');
        $this->load->model('admin/itemcode_model');
        $this->load->model('form_model');
        $this->load->model('form_subscription_model');
        $this->load->library("validation");
        $this->load->helper('url','form');
        $this->load->library('form_validation');
        $this->load->library('session');
        if ($this->session->userdata('company'))
            $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
        $data['newnotifications'] = $this->messagemodel->getnewnotifications();
        $this->load = new My_Loader();
        $this->load->template('../../templates/front/template', $data);
    }

    public function index() {
		
        $this->login();
    }

    public function register() 
    {
        $this->load->template('../../templates/front/register');
        $data['states'] = $this->db->get('state')->result();
        
        $this->load->view('company/register',$data);
    }

    public function saveregister() {
        if (!@$_POST)
            die;
        $errormessage = '';

        if (!@$_POST['title'] || !@$_POST['primaryemail']) {
            $errormessage = 'Please Fill up all the fields.';
        } else {
            $this->db->where('primaryemail', $_POST['primaryemail']);
            if ($this->db->get('company')->num_rows > 0) {
                $errormessage = "Email '{$_POST['primaryemail']}' already exists.";
            }
        }

        $completeaddress="";
            if($_POST['street'])
            {
            	$completeaddress.=$_POST['street'].",";
            }
            if($_POST['city'])
            {
            	$completeaddress.=$_POST['city'].",";
            }
            if($_POST['state'])
            {
            	$completeaddress.=$_POST['state'].",";
            }
            if($_POST['zip'])
            {
            	$completeaddress.=$_POST['zip'];
            }

        $_POST['address'] = $completeaddress;        
        
        if ($errormessage) {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $errormessage . '</div></div></div>');
            redirect('company/register');
        }

        $key = md5(uniqid($_POST['title']) . '-' . date('YmdHisu'));
        $_POST['regkey'] = $key;
        $this->db->insert('company', $_POST);
        $itemid = $this->db->insert_id();
        $this->sendRegistrationEmail($itemid, $key);
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Account Created Successfully.<br/>Please check your email for activation link.</div></div><div class="errordiv">');
        redirect('company/register');
    }

    function sendRegistrationEmail($id, $key) 
    {
        $c = $this->companymodel->getcompanybyid($id);

        $link = base_url() . 'company/complete/' . $key;
        $body = "Dear " . $c->title . ",<br><br> 
	  	Please click following link to complete your registration:  <br><br>		 
	    <a href='$link' target='blank'>$link</a>";

        $settings = (array) $this->companymodel->getconfigurations(1);
        $this->load->library('email');
        $this->email->from($settings['adminemail'], "Administrator");

        $this->email->to($c->title . ',' . $c->primaryemail);

        $this->email->subject('Activate your account.');
        $this->email->message($body);
        $this->email->set_mailtype("html");
        $this->email->send();
    }

    public function complete($regkey = '') {
        if (!$regkey)
            redirect('admin', 'refresh');

        $this->db->where('regkey', $regkey);
        $c = $this->db->get('company')->row();
        if (!$c) {
            $message = 'Invalid key.';
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
            redirect('company/register');
            die;
        }

        $data['company'] = $c;
        $this->load->template('../../templates/front/register', $data);
        $this->load->view('company/complete', $data);
    }

    public function savecomplete() 
    {
        if (!@$_POST)
            die;
        if (!@$_POST['regkey']) {
            $message = 'Invalid key.';
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
            redirect('company/register');
            die;
        }
        $errormessage = '';
        if (!@$_POST['username'] || !@$_POST['password'] || !@$_POST['repassword']) {
            $errormessage = 'Please Fill up all the fields.';
        } elseif ($_POST['password'] != $_POST['repassword']) {
            $errormessage = 'Password and Confirm Password does not matched. Please try again.';
        }

        $this->db->where('username', $_POST['username']);
        if ($this->db->get('company')->num_rows > 0) {
            $errormessage = "Username '{$_POST['username']}' already exists";
        }

        if ($errormessage) {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $errormessage . '</div></div></div>');
            redirect('company/complete/' . $_POST['regkey']);
        }

        $regkey = $_POST['regkey'];
        $this->db->where('regkey', $regkey);
        $c = $this->db->get('company')->row();
        if (!$c) {
            $message = 'Invalid key.';
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
            redirect('company/register');
            die;
        }

        unset($_POST['repassword']);
        $_POST['regkey'] = '';
        $rawpassword = $_POST['password'];
        $_POST['password'] = md5($_POST['password']);
        $_POST['pwd'] = $_POST['password'];
        $_POST['regdate'] = date('Y-m-d');
        $this->db->where('regkey', $regkey);
        $this->db->update('company', $_POST);

        $tierprice = array();
        $tierprice['company'] = $c->id;
        $tierprice['tier1'] = -2;
        $tierprice['tier2'] = -4;
        $tierprice['tier3'] = -6;
        $tierprice['tier4'] = -10;
        $this->db->insert('tierpricing', $tierprice);
        
        $c->username = $_POST['username'];

        $data['company'] = $c;
        $this->session->set_userdata($data);
        
        $body = "Dear " . $c->username . ",<br><br>
        Congratulations! <br><br> Thanks for registration, Your registration is complete. You can login in Dashboard.
        <br/><br/>
        Your Login Profile is as follows:<br/>
        Login User Name : ". $c->username ." <br/>
        Login Password :  ". $rawpassword ." <br/>
        Company Name: ". $c->title ." <br/>
        Email Address: ". $c->primaryemail ." <br/>
        Contact Name: ". $c->contact ." <br/> <br/>";
        
        $settings = (array) $this->companymodel->getconfigurations(1);
        $this->load->library('email');
        $this->email->from($settings['adminemail'], "Administrator");
        $this->email->to($c->primaryemail);
        $this->email->subject('Registration Completed');
        $this->email->message($body);
        $this->email->set_mailtype("html");
        $this->email->send();
        
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">You Have Now Successfully Completed Your Registration.</div></div></div>');
        redirect('dashboard');
    }

    function login() {
        $data['message'] = '';
        $this->load->template('../../templates/front/register', $data);
        $this->load->view('company/login', $data);
    }
	
    function checklogin() {
        if (!@$_POST)
            die('Wrong access');
        $errormessage = '';
        if (!@$_POST['username'])
            $errormessage = 'Please Provide Username';
        if (!@$_POST['password'])
            $errormessage = 'Please Provide Password';

        if ($errormessage) {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $errormessage . '</div></div></div>');
            redirect('company/login/');
        }

        $_POST['password'] = md5($_POST['password']);
        $this->db->where($_POST);
        $check = $this->db->get('company')->row();
//echo "<pre>";print_r($check);exit;
        if ($check) {
            $data['company'] = $check;
            $data['logintype'] = 'company';

            $data['comet_user_id'] = $check->id;
            $data['comet_user_email'] = $check->primaryemail;
			
            $this->session->set_userdata($data);
			
			@session_start();
			$_SESSION['comet_user_id']=$check->id;
			$_SESSION['comet_user_email']=$check->primaryemail;
			$_SESSION['userid']=$check->id;
			$_SESSION['logintype']='company';
			
			/*$this->load->helper('cookie');
        	$this->input->set_cookie("comet_user_id", $check->id,time()+3600);
			$this->input->set_cookie("comet_user_email", $check->primaryemail,time()+3600);
			$this->input->set_cookie("userid", $check->id,time()+3600);
			$this->input->set_cookie("logintype", 'company',time()+3600);*/
 			
            redirect('dashboard');
        } else {
            $data['message'] = 'Invalid Login';
            $this->load->template('../../templates/front/register', $data);
            $this->load->view('company/login', $data);
        }
    }

    function logout() {
        $this->session->sess_destroy();
		/*$this->load->helper('cookie');
		$this->input->set_cookie("comet_user_id",'',time()+3600);
		$this->input->set_cookie("comet_user_email", '',time()+3600);
		$this->input->set_cookie("userid", '',time()+3600);
		$this->input->set_cookie("logintype", '',time()+3600);*/
		@session_start();
		$_SESSION['comet_user_id']='';
		$_SESSION['comet_user_email']='';
		$_SESSION['userid']='';
		$_SESSION['logintype']='';
			
        redirect('company/login', 'refresh');
    }

    function profile() 
    {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        $company = $this->db->where('id',$company->id)->get('company')->row();
        
        if (!trim(@$company->com_lat) && @$c->address) 
        {
            $geoloc = get_geo_from_address($c->address);
            if(@$geoloc->lat && @$geoloc->long)
            {
                $update_supplier['com_lat'] = $geoloc->lat;
                $update_supplier['com_lng'] = $geoloc->long;
                $this->supplier_model->update_supplier($company->id, $update_supplier);
                $company = $this->supplier_model->get_supplier($company->id);
            }
        }


        $data['states'] = $this->db->get('state')->result();
        
        $data['types'] = array();
        $types = $this->db->order_by('title')->get('type')->result();        
        foreach ($types as $t) {
            $this->db->where(array('companyid' => $company->id, 'typeid' => $t->id));
            
            if ($this->db->get('companytype')->num_rows > 0)
                $t->checked = 'checked="CHECKED"';
            else
                $t->checked = '';
            $data['types'][] = $t;
        }

        //print_r($states);
		$data['image']=$this->db->get_where('companyattachment',array('company'=>$company->id))->result();
		$data['members']=$this->db->where('cid',$company->id)->get("companyteam")->result();
        $this->db->where('company', $company->id);
        $emails = $this->db->get('companyemail')->result();
        $data['company'] = $company;
        $data['emails'] = $emails;
        $this->load->view('company/profile', $data);
    }
    
    function addMember(){
    	$company = $this->session->userdata('company');
    	if (!$company)
    		redirect('company/login');
    	$company = $this->db->where('id',$company->id)->get('company')->row();

    	$config['upload_path'] = './uploads/companyMembers/';
		$config['allowed_types'] = 'gif|jpg|png';
		/*$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';*/

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload("memberPicture"))
		{
			$error = $this->upload->display_errors();
			$this->session->set_flashdata('message', $error);
			
		}
		else
		{
			$data = $this->upload->data();
			$this->session->set_flashdata('message', "");
			$this->db->insert("companyteam",array("cid"=>$company->id,"name"=>$this->input->post("memberName"),"email"=>$this->input->post("memberEmail"),"title"=>$this->input->post("memberTitle"),"phone"=>$this->input->post("memberPhone"),"linkedin"=>$this->input->post("memberLinkedin"),"picture"=>$data['file_name']));
	    	
    	}
    	redirect("company/profile");
    	
    }
    function editMember(){
    	$company = $this->session->userdata('company');
    	if (!$company)
    		redirect('company/login');
    	$company = $this->db->where('id',$company->id)->get('company')->row();
    	
    	
    	$config['upload_path'] = './uploads/companyMembers/';
    	$config['allowed_types'] = 'gif|jpg|png';
    	/*$config['max_size']	= '100';
    	 $config['max_width']  = '1024';
    	$config['max_height']  = '768';*/
    	
    	$this->load->library('upload', $config);
    	
    	if ( ! $this->upload->do_upload("memberPicture"))
    	{
    		$error = $this->upload->display_errors();
    		$this->session->set_flashdata('message', $error);
    			
    	}
    	else
    	{
    		$data = $this->upload->data();
    		$this->session->set_flashdata('message', "");
    	}
    	$this->db->where("id",$this->input->post("idMember"));
    	$this->db->update("companyteam",array("name"=>$this->input->post("memberName"),"email"=>$this->input->post("memberEmail"),"title"=>$this->input->post("memberTitle"),"phone"=>$this->input->post("memberPhone"),"linkedin"=>$this->input->post("memberLinkedin"),"picture"=>$data['file_name']));
    	redirect("company/profile");
    }
    
    function getMemberInfo($id){
    	$company = $this->session->userdata('company');
    	if (!$company)
    		redirect('company/login');
    	
    	$this->db->where("id",$id);
    	$this->db->where("cid",$company->id);
    	$row = $this->db->get("companyteam")->row();
    	echo json_encode($row);
    }
    function saveprofile() {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');

        if (!$_POST)
            die('Wrong Access.');

        $errormessage = '';
        $this->db->where('id !=', $company->id);
        $this->db->where('primaryemail', $_POST['primaryemail']);
        if ($this->db->get('company')->num_rows > 0) {
            $data['types'] = $this->db->get('type')->result();
            $errormessage = "Email '{$_POST['primaryemail']}' already exists.";
        }

        if (isset($_FILES['logo']['tmp_name']))
            if (is_uploaded_file($_FILES['logo']['tmp_name'])) {
                $nfn = $_FILES['logo']['name'];
                $ext = end(explode('.', $nfn));
                if (!in_array(strtolower($ext), array('jpg', 'gif', 'jpeg', 'png'))) {
                    $errormessage = '* Invalid file type, upload logo file.';
                } elseif (move_uploaded_file($_FILES['logo']['tmp_name'], "uploads/logo/" . $nfn)) {
                    $this->_createThumbnail($nfn, 'logo', 270, 200);
                    $_POST['logo'] = $nfn;
                }
            }
			
            if($_FILES['UploadFile']['name'])
            {
            	ini_set("upload_max_filesize","128M");
            	//$target=substr($_SERVER["DOCUMENT_ROOT"], 0).'/uploads/gallery';
            	$target='uploads/gallery/';
            	$count=0;
            	foreach ($_FILES['UploadFile']['name'] as $filename)
            	{
            		$temp=$target;
            		$tmp=$_FILES['UploadFile']['tmp_name'][$count];
            		$origionalFile=$_FILES['UploadFile']['name'][$count];
            		$count=$count + 1;
            		$temp=$temp.basename($filename);
            		move_uploaded_file($tmp,$temp);
            		$temp='';
            		$tmp='';

            		$AttachmentName= $_FILES['UploadFile']['name'];
            		//$AttachmentPath = substr($_SERVER["DOCUMENT_ROOT"], 0).'/uploads/gallery';
            		//$AttachmentPath = site_url('uploads/gallery/');
            		//echo "<pre>"; print_r($AttachmentPath); die;

            		//$imagename=implode(",",$AttachmentName);


            		$this->db->insert('companyattachment', array('company' => $company->id, 'imagename' => $filename));


            		//echo "<pre>"; print_r($imagename); die;
            	}
            	//$_POST['lightbox'] = $imagename;
            }
            
         $completeaddress="";
            if($_POST['street'])
            {
            	$completeaddress.=$_POST['street'].",";
            }
            if($_POST['city'])
            {
            	$completeaddress.=$_POST['city'].",";
            }
            if($_POST['state'])
            {
            	$completeaddress.=$_POST['state'].",";
            }
            if($_POST['zip'])
            {
            	$completeaddress.=$_POST['zip'];
            }

        $_POST['address'] = $completeaddress;    
            
            
        if ($errormessage) {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $errormessage . '</div></div></div>');
            redirect('company/profile');
        }

        $this->db->where('companyid', $company->id);
        $this->db->delete('companytype');

        if (isset($_POST['types'])) {
            $types = $_POST['types'];
            unset($_POST['types']);
            foreach ($types as $type) {
                $this->db->insert('companytype', array('companyid' => $company->id, 'typeid' => $type));
            }
        }

        $this->db->where('company', $company->id);
        $this->db->delete('companyemail');
        if (isset($_POST['emails'])) {
            $emails = $_POST['emails'];
            unset($_POST['emails']);
            foreach ($emails as $email) {
                $this->db->insert('companyemail', array('company' => $company->id, 'email' => $email));
            }
        }

        unset($_POST['_wysihtml5_mode']);
        $this->db->where('id', $company->id);
        $this->db->update('company', $_POST);
        
        
        if($this->input->post('address'))
        {
            $geoloc = get_geo_from_address($this->input->post('address'));
            if($geoloc && @$geoloc->lat && @$geoloc->long)
            {
                $update_supplier = array();
                
                $update_supplier['com_lat'] = $geoloc->lat;
                $update_supplier['com_lng'] = $geoloc->long;
                
                $this->supplier_model->update_supplier($company->id, $update_supplier);
            }
        }
        

        $company = $this->supplier_model->get_supplier($company->id);
       

        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Your profile has been saved.</div></div></div>');
        redirect('company/profile');
    }

    function password() {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        $company = $this->companymodel->getcompanybyid($company->id);
        $data['company'] = $company;
        $this->load->view('company/password', $data);
    }

    function savepassword() {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        $company = $this->companymodel->getcompanybyid($company->id);
        if (!$_POST)
            die('Wrong Access.');


        $errormessage = '';

        if ($company->password != md5($_POST['epassword'])) {
            $errormessage = "Wrong existing password";
        } elseif ($_POST['password'] != $_POST['cpassword']) {
            $errormessage = 'Two passwords do not match';
        }

        if ($errormessage) {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $errormessage . '</div></div></div>');
            redirect('company/password');
        }

        $this->db->where('id', $company->id);
        $update = array('password' => md5($_POST['password']),'pwd' => $_POST['password']);
        $this->db->update('company', $update);
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Password changed successfully.</div></div></div>');
        redirect('company/password');
    }
    
    ////////////////////////
    function forgot() {
        $this->load->template('../../templates/front/register');
        $this->load->view('company/forgot');
    }

    function sendforgot() {
        if (!@$_POST)
            die('Wrong access');
        $errormessage = '';
        if (!@$_POST['email']) {
            $errormessage = 'Please Provide Email';
        } else {
            //print_r($_POST);die;
            $this->db->where('primaryemail', $_POST['email']);
            $check = $this->db->get('company');
            
            if ($check->num_rows == 0) {
                $errormessage = "Invalid email";
            }
        }


        if ($errormessage) {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $errormessage . '</div></div></div>');
            redirect('company/forgot');
        }

        $c = $check->row();
        
        if($_POST['type'] == 'username')
        {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success">Your username is - '.$c->username.'</div></div>');
            redirect('company/forgot');
        }
        
        
        $key = md5(uniqid($c->title) . '-' . date('YmdHisu'));
        $this->db->where('id', $c->id);
        $this->db->update('company', array('passkey' => $key));

        $link = base_url() . 'company/change/' . $key;
        $body = "Dear " . $c->title . ",<br><br> 
	  	Please click following link to change your password:  <br><br>		 
	    <a href='$link' target='blank'>$link</a>";

        $settings = (array) $this->companymodel->getconfigurations(1);
        $this->load->library('email');
        $this->email->from($settings['adminemail'], "Administrator");

        $this->email->to($c->title . ',' . $c->primaryemail);

        $this->email->subject('Password change link.');
        $this->email->message($body);
        $this->email->set_mailtype("html");
        $this->email->send();

        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Password change link sent to your email successfully.</div></div></div>');
        redirect('company/forgot');
    }

    public function change($passkey = '') {
        if (!$passkey)
            redirect('admin', 'refresh');

        $this->db->where('passkey', $passkey);
        $c = $this->db->get('company')->row();
        if (!$c) {
            $message = 'Invalid key.';
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
            redirect('company/login');
            die;
        }

        $data['company'] = $c;
        $this->load->template('../../templates/front/register', $data);
        $this->load->view('company/change', $data);
    }

    public function savechange() {
        if (!@$_POST)
            die;
        if (!@$_POST['passkey'])
            die('Wrong access');
        $errormessage = '';
        if (!@$_POST['password'] || !@$_POST['repassword']) {
            $errormessage = 'Please Fill up all the fields.';
        } elseif ($_POST['password'] != $_POST['repassword']) {
            $errormessage = 'Password and Confirm Password does not matched. Please try again.';
        }

        if ($errormessage) {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $errormessage . '</div></div></div>');
            redirect('company/change/' . $_POST['passkey']);
        }

        $passkey = $_POST['passkey'];
        $this->db->where('passkey', $passkey);
        $c = $this->db->get('company')->row();
        if (!$c) {
            $message = 'Invalid key.';
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
            redirect('company/login');
            die;
        }

        unset($_POST['repassword']);
        $_POST['passkey'] = '';
        $_POST['password'] = md5($_POST['password']);
        $_POST['pwd'] = $_POST['password'];

        $this->db->where('passkey', $passkey);
        $this->db->update('company', $_POST);
        //$data['company'] = $c;
        //$this->session->set_userdata($data);
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Password changed successfully.</div></div></div>');
        redirect('company/login');
    }
    //////////////////////////////////
    
    
    ////////////////////////
    function resend() 
    {
        $this->load->template('../../templates/front/register');
        $this->load->view('company/resend');
    }

    function sendkeyagain() 
    {
        if (!@$_POST)
            die('Wrong access');
        $errormessage = '';
        if (!@$_POST['email']) {
            $errormessage = 'Please Provide Email';
        } else {
            $this->db->where('primaryemail', $_POST['email']);
            $check = $this->db->get('company');
            if ($check->num_rows == 0) {
                $errormessage = "Invalid email";
            }
        }

        $c = $check->row();
        $key = $c->regkey;
        if(!$key)
        {
            $errormessage = "Account already activated.";
        }


        if ($errormessage) {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $errormessage . '</div></div></div>');
            redirect('company/resend');
        }

        $link = base_url() . 'company/complete/' . $key;
        $body = "Dear " . $c->title . ",<br><br> 
	  	Please click following link to complete your registration:  <br><br>		 
	    <a href='$link' target='blank'>$link</a>";

        $settings = (array) $this->companymodel->getconfigurations(1);
        $this->load->library('email');
        $this->email->from($settings['adminemail'], "Administrator");

        $this->email->to($c->title . ',' . $c->primaryemail);

        $this->email->subject('Activate your account.');
        $this->email->message($body);
        $this->email->set_mailtype("html");
        $this->email->send();

        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Activation link sent to your email successfully.</div></div></div>');
        redirect('company/resend');
    }
    //////////////////////////////////
    
    
    function tier() {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        $this->db->where('company', $company->id);
        $tier = $this->db->get('tierpricing')->row();
        if (!$tier) {
            $tierprice = array();
            $tierprice['company'] = $company->id;
            $tierprice['tier1'] = -2;
            $tierprice['tier2'] = -4;
            $tierprice['tier3'] = -6;
            $tierprice['tier4'] = -10;
            $this->db->insert('tierpricing', $tierprice);

            $this->db->where('company', $company->id);
            $tier = $this->db->get('tierpricing')->row();
        }

        $sql = "SELECT u.id purchasingadmin, u.companyname purchasingcompany, u.fullname purchasingfullname, 
        			   tier, creditlimit, totalcredit, creditfrom, creditto
				FROM " . $this->db->dbprefix('users') . " u
				INNER JOIN pms_network n ON u.id=n.purchasingadmin AND n.company='" . $company->id . "'
				LEFT JOIN " . $this->db->dbprefix('purchasingtier') . " pt ON pt.purchasingadmin=u.id AND pt.company='" . $company->id . "'
				WHERE u.usertype_id=2
			";
        //echo $sql;
        $admins = $this->db->query($sql)->result();
        $data['admins'] = array();
        foreach($admins as $admin)
        {
            $pa = $admin->purchasingadmin;
		    $settings = $this->settings_model->get_setting_by_admin($pa);
		    $query = "SELECT
		    			(SUM(r.quantity*ai.ea) + (SUM(r.quantity*ai.ea) * ".$settings->taxpercent." / 100)) 
		    			totalunpaid FROM 
		    			".$this->db->dbprefix('received')." r, ".$this->db->dbprefix('awarditem')." ai
						WHERE r.awarditem=ai.id AND r.paymentstatus!='Paid' AND ai.company='".$company->id."' 
						AND ai.purchasingadmin='$pa'";
		    //echo $query.'<br/>';
		    $due = $this->db->query($query)->row()->totalunpaid;
		    $due = round($due,2);
		    //echo $nc->due.' - ';
		    $query = "SELECT (SUM(od.quantity * od.price) + (SUM(od.quantity * od.price) * o.taxpercent / 100))
		    	orderdue
                FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
                WHERE od.orderid=o.id AND o.type='Manual' AND od.paymentstatus!='Paid' AND od.accepted!=-1
                AND o.purchasingadmin='$pa' AND od.company='".$company->id."'";
		    //echo $query.'<br/>';
		    $manualdue = $this->db->query($query)->row()->orderdue;
		    $manualdue = round($manualdue,2);
		    //echo $manualdue.' <br/> ';
		    $due += $manualdue;
		    $admin->amountdue = $due;
            $data['admins'][]=$admin;
        }
        //print_r($admins);die;
        $data['tier'] = $tier;
        $this->load->view('company/tier', $data);
    }

    function savetier() 
    {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        //print_r($_POST);die;
        $this->db->where('company', $company->id);
        $this->db->update('tierpricing', $_POST);
        $message = 'Tier price settings updated.';
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('company/tier');
    }
    
    function changetier($invitation='')
    {
        $company = $this->session->userdata('company');
        if (!$company)
            die;
        
        if(!@$_POST['purchasingadmin'])
            die;
        
        if(!@$_POST['tier'])
            die;
        
        $update = array('tier'=>strtolower($_POST['tier']));
        //print_r($company);die;
        $this->db->where('company', $company->id);
        $this->db->where('purchasingadmin', $_POST['purchasingadmin']);
        $this->db->update('purchasingtier', $update);
        if($invitation)
        {
            redirect('quote/invitation/'.$invitation);
            die;
        }
        die(1);
    }
    
    function changeitemprice()
    {
        $company = $this->session->userdata('company');
        if (!$company)
            die;
        if(!@$_POST['itemid'])
            die;
        if(!@$_POST['price'])
            die;
        
        $this->db->where('type', 'Supplier');
        $this->db->where('company', $company->id);
        $this->db->where('itemid', $_POST['itemid']);
        if($this->db->get('companyitem')->row())
        {
            $update = array('ea'=>strtolower($_POST['price']));
            
            $this->db->where('type', 'Supplier');
            $this->db->where('company', $company->id);
            $this->db->where('itemid', $_POST['itemid']);
            $this->db->update('companyitem', $update);
        }
        else
        {
            $insert = array();
            $insert['type'] = 'Supplier';
            $insert['company'] = $company->id;
            $insert['itemid'] = $_POST['itemid'];
            $insert['ea'] = $_POST['price'];
            $this->db->insert('companyitem', $insert);
        }
        echo 1; die;
    }
    
    function price()
    {
        $company = $this->session->userdata('company');
        if (!$company)
            die;
        if(!@$_POST['itemid'])
            die;
        if(!@$_POST['price'])
            die;
        $update = array('ea'=>$_POST['price']);
        //print_r($company);die;
        $this->db->where('type', 'Supplier');
        $this->db->where('company', $company->id);
        $this->db->where('itemid', $_POST['itemid']);
        $this->db->update('purchasingtier', $update);
        echo 1; die;
    }

    function savepurchasingtier() 
    {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        //print_r($_POST);die;

        foreach ($_POST['tier'] as $admin => $tier) {
            //echo $admin.'-'.$tier.'<br/>';
            $arr = array('purchasingadmin' => $admin, 'company' => $company->id);
            $this->db->where($arr);
            $this->db->delete('purchasingtier');
            $arr['tier'] = $tier;
            $arr['creditlimit'] = $_POST['creditlimit'][$admin];
            $arr['totalcredit'] = $_POST['creditlimit'][$admin];//this is not a mistake, same value is fed to both fields.
            if($_POST['creditfrom'][$admin])
            	$arr['creditfrom'] = date('Y-m-d', strtotime($_POST['creditfrom'][$admin]));
            if($_POST['creditto'][$admin])
            	$arr['creditto'] = date('Y-m-d', strtotime($_POST['creditto'][$admin]));
            
            //print_r($arr);die;
            $this->db->insert('purchasingtier', $arr);
        }

        $message = 'Tier price settings updated for purchasing companies.';
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('company/tier');
    }
    
    ///bankaccount
    function bankaccount()
    {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
        if(!$bankaccount)
        {
            $this->db->insert('bankaccount',array('company'=>$company->id));
            $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
        }
        $data['bankaccount'] = $bankaccount;
        $this->load->view('company/bankaccount', $data);
    }
    
    function savebankaccount()
    {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        //////
        $this->db->where('company',$company->id)->update('bankaccount',$_POST);

        $message = 'Bank Account settings updated.';
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('company/bankaccount');
    }

    function _createThumbnail($fileName, $foldername = "", $width = 170, $height = 150) {
        $config['image_library'] = 'gd2';
        $config['source_image'] = 'uploads/' . ($foldername ? $foldername . '/' : '') . $fileName;
        $config['new_image'] = 'uploads/' . ($foldername ? $foldername . '/' : '') . 'thumbs/' . $fileName;
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        $image_config['x_axis'] = '0';
        $image_config['y_axis'] = '0';
        $config['width'] = $width;
        $config['height'] = $height;

        $this->load->library('image_lib', $config);
        if (!$this->image_lib->resize())
            echo $this->image_lib->display_errors();
    }
 
    function ads(){
    	$company = $this->session->userdata('company');
    	if (!$company)
    		redirect('company/login');
    	
    	$this->db->where("user_id",$company->id);
    	$res['ads'] = $this->db->get("ads")->result();
    	$this->load->view('company/ads', $res);
    }
    function addAd(){
    	$company = $this->session->userdata('company');
    	if (!$company)
    		redirect('company/login');
    	
     $catcodes = $this->catcode_model->get_categories_tiered();
     
        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $itemcodes = $this->items_model->get_items4($categories[0]->id);
        $data['categories'] = $categories;
        $data['items'] = $itemcodes;
        $data['company'] = $company;
    	$this->load->view('company/addAd',$data);
    }
    function saveAd(){
    	$res = $this->do_upload();
	    
    	//$res = $this->do_upload();
    	if(isset($res['error'])){
    		$this->session->set_flashdata('message',$res['error']);
    	
    	}
    	else {
    		$this->admodel->saveAd($res);
    	}
    	redirect("company/ads");
    }
    public function do_upload ()
    {
    $this->load->library('upload');

    	
    	$config['upload_path'] = './uploads/ads/';
    	$config['allowed_types'] = '*';
    	//$config['max_size']	= '9000';
    	//	$config['max_width']  = '1024';
    	//	$config['max_height']  = '768';
    	$this->upload->initialize($config);
    	if (! $this->upload->do_multi_upload("adfile"))
    	{
    		$error = array('error' => $this->upload->display_errors());
    
    		//$this->load->view('upload_form', $error);
    	}
    	else
    	{
    		//var_dump($this->upload->data()); exit;
    		$error = array('upload_data' => $this->upload->get_multi_upload_data());
    		//$this->_createThumbnail($_FILES["adfile"]["name"],'item',200,200);
    		//$this->load->view('upload_success', $data);
    	} //var_dump($error); exit;
    	return $error;
    }
  
  	// List Items of the selected Categories
  	 function get_items($categoryId){

		// $this->load->model('items_model');
		 header('Content-Type: application/x-json; charset=utf-8');
		 echo(json_encode($this->items_model->get_items2($categoryId)));
	}

	public function createformfields()
    {
    	$company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');

        $companyId = $this->session->userdata('company')->id;
        $this->load->view('company/formbuilderselector');
    
    }
    public function createformnetwork()
    {
    	$company = $this->session->userdata('company');
    	if (!$company)
    		redirect('company/login');
    
    	$companyId = $this->session->userdata('company')->id;
    	$this->load->view('company/formnetworkbuilder');
    
    }

    public function createformdata()
    {
    	$companyId = $this->session->userdata('company')->id;
		$result = $this->form_model->create_field($_POST,$companyId);
		//$data['result'] = $this->form_model->view_field($companyId);

		$path=base_url() . 'company/formview';

		$message =  '<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Form fields are created Successfully.<a href='.$path.' target="_self">View Form</a></div></div></div>';
		$data['message'] = $message;
		$this->load->view('company/formnetworkbuilder',$data);
    }
    
    public function createformsubscriptions(){
    	$company = $this->session->userdata('company');
    	if (!$company)
    		redirect('company/login');

    	$this->load->view('company/formsubscriptionsbuilder');
    }
    
    public function insertformsubscriptions(){
    	$companyId = $this->session->userdata('company')->id;
    	$result = $this->form_subscription_model->create_field($_POST,$companyId);
    	//$data['result'] = $this->form_model->view_field($companyId);
    	
    	$path=base_url() . 'company/formview';
    	
    	$message =  '<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Form fields are created Successfully.<a href='.$path.' target="_self">View Form</a></div></div></div>';
    	$data['message'] = $message;
    	 	$this->load->view('company/formsubscriptionsbuilder');
    }
     public function formview()
    {
    	$companyId = $this->session->userdata('company')->id;
		$data['result'] = $this->form_model->view_field($companyId);

		$this->load->view('company/formview',$data);
    }
    
     public function formsubscriptionsview(){
     	$companyId = $this->session->userdata('company')->id;
     	$data['result'] = $this->form_subscription_model->view_field($companyId);
     	
     	$this->load->view('company/formsubscriptionsview',$data);
     }

     public function saveformdata()
    {
    	$companyId = $this->session->userdata('company')->id;
		$result = $this->form_model->save_fields($_POST,$companyId);
		$data['result'] = $this->form_model->view_field($companyId);
		$message =  '<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Saved Successfully.</div></div></div>';
		$data['message'] = $message;
		$this->session->set_flashdata('message', $message);
		$this->load->view('company/formview',$data);
    }
    
    public function deleteformdata($id)
    {
		$data['result'] = $this->form_model->delete_field($id);
		$this->load->view('company/formnetworkbuilder',$data);
    }
    
    public function deleteformsubscriptionsdata($id)
    {
    	$data['result'] = $this->form_subscription_model->delete_field($id);
    	$this->load->view('company/formsubscriptionsbuilder',$data);
    }

    public function deleteallformdata()
    {
    	$companyId = $this->session->userdata('company')->id;
		$data['result'] = $this->form_model->delete_allfield($companyId);
		$this->load->view('company/formnetworkbuilder',$data);
    }
    
    
    function updatead($id)
	{		
		$this->db->where("id",$id);
    	$res['ads'] = $this->db->get("ads")->result();		
    	$catcodes = $this->catcode_model->get_categories_tiered();
     	$itemcodes = $this->itemcode_model->get_itemcodes();
        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $res['categories'] = $categories;
        $res['items'] = $itemcodes;
        $res['adsid'] = $id;
        
        if(isset($_POST['add']))
        {
			$res = $_POST;
	    	$res = $this->do_upload();
		    
	    	//$res = $this->do_upload();
	    
	    	if(isset($res['error'])){
	    		$this->session->set_flashdata('message',$res['error']);
	    		redirect("company/ads");
	    	}
	    	
	    		$this->admodel->updateAd($res);
	    		
	    	$message =  '<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Saved Successfully.</div></div></div>';
			$res['message'] = $message;
			$this->session->set_flashdata('message', $message);
			redirect("company/ads");
        }
		$this->load->view('company/updatead',$res);
		
	}
	
	function deletead($id)
	{
            $result=$this->admodel->deleteAd($id);
		    $message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
			$res['message'] = $message;
			$this->session->set_flashdata('message', $message);
			redirect("company/ads");

	}
	
	function formsubmission()
	{
	    $companyId = $this->session->userdata('company')->id;

	    $where="";

	    if(isset($_POST['companyname']) && $_POST['companyname']!="")
	    {
	    $where = "AND fromid = ".$_POST['companyname'];
	    }

        $sql = "SELECT fb.*,jrf.Value as formValue,jr.message,jr.accountnumber,jr.fromid, u.companyname FROM ".$this->db->dbprefix('formbuilder')." fb LEFT JOIN ".$this->db->dbprefix('joinrequestform') ." jrf ON jrf.formfieldid = fb.Id LEFT JOIN ".$this->db->dbprefix('joinrequest') ." jr ON jrf.joinrequestid = jr.id left join pms_users u on jr.fromid = u.id WHERE fb.CompanyID=".$companyId." {$where} order by fromid";

            $qry = $this->db->query($sql);
            $data['formresult'] = $qry->result_array();
            if(isset($data['formresult']) && count($data['formresult'])>0)
            {
            	$this->load->view('company/formsubmission',$data);
            }
	}
	
	function mailinglist(){
		$this->load->view('company/mailinglist');
		
	}
	
	function listsubscribers(){
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$this->db->where("cid",$company->id);
		$subscribers = $this->db->get("newsletter_subscribers")->result();
		
		$data = array();
		foreach( $subscribers as $subscriptor){
			
			$this->db->where("subscriber_id",$subscriptor->id);
			
			$data['subscribers'][$subscriptor->id] = $this->db->get("newsletter_subscribers_data")->result_array();
			
		}
		$this->load->view('company/listsubscribers',$data);
	}
	
	function listtemplates(){
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		/*$this->db->where("cid",$company->id);
		$data['templates']  = $this->db->get("newsletter_template")->result();*/
		
		$this->db->select("*");
		$this->db->from("newsletter_template");
		$this->db->join("newsletter_analytics","newsletter_template.id=newsletter_analytics.tid");
		$this->db->where("cid",$company->id);
		$data['templates']  = $this->db->get()->result();
		
		$this->load->view('company/listtemplates',$data);
		
	}
	
	function listpretemplates(){
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
	
		
		$data['templates']  = $this->db->get("newsletter_predefined_template")->result();
	
		$this->load->view('company/listpretemplates',$data);
	
	}
	
	function newtemplate(){
		
		$data['action'] = "new";
		$data["cid"] = $this->session->userdata('company')->id;
		$this->db->where("CompanyID",$this->session->userdata('company')->id);
		$data['fields'] = $this->db->get("formsubscription")->result();
		$this->load->view('company/newnewslettertemplate',$data);
	}
	
	function edittemplate($id){
		
		$data['action'] = "update";
		$this->db->where("id",$id);
		$this->db->where("cid",$this->session->userdata('company')->id);
		$res = $this->db->get("newsletter_template")->row();
		if(!empty($res)){
			$data['title'] = $res->title;
			$data['body'] = $res->body;
			$data['id'] = $this->session->userdata('company')->id;

			$this->db->where("CompanyID",$this->session->userdata('company')->id);
			$data['fields'] = $this->db->get("formsubscription")->result();
			
			$this->load->view('company/newnewslettertemplate',$data);
		}else{
			$this->session->set_flashdata('message', 'The template doesnt exist');
			redirect("company/mailinglist");
		}
	}
	
	function editpretemplate($id){
	
		$data['action'] = "update";
		$this->db->where("id",$id);
		$res = $this->db->get("newsletter_predefined_template")->row();
		if(!empty($res)){
			$data['title'] = $res->title;
			$data['body'] = $res->body;
			
			$this->load->view('company/newnewsletterpretemplate',$data);
		}else{
			$this->session->set_flashdata('message', 'The template doesnt exist');
			redirect("company/mailinglist");
		}
	}
	
	function addtemplate(){
		
		$title = $this->input->post("title");
		$body = $this->input->post("body");
		$cid  = $this->input->post("cid");
		
		$this->db->insert("newsletter_template",array("cid"=>$cid, "title"=>$title,"body"=>$body));
		$this->db->insert("newsletter_analytics",array("tid"=>$this->db->insert_id(),"numSent"=>0,"numErrors"=>0));;
		$this->session->set_flashdata('message', 'The template was created');
			redirect("company/mailinglist");
	}
	function addpretemplate(){
	
		$title = $this->input->post("title");
		$body = $this->input->post("body");
		$cid  = $this->session->userdata('company')->id;
	
		$this->db->insert("newsletter_template",array("cid"=>$cid, "title"=>$title,"body"=>$body));
	
		$this->session->set_flashdata('message', 'The template was created');
		redirect("company/mailinglist");
	}
	
	function updatetemplate($id){
	
		$title = $this->input->post("title");
		$body = $this->input->post("body");
		
		$this->db->where("id",$id);
		$this->db->update("newsletter_template",array("title"=>$title,"body"=>$body));
		
		$this->session->set_flashdata('message', 'The template was updated');
		redirect("company/mailinglist");
	}
	
	function deleteimage($id)
	{
		$rows['image']=$this->db->get_where('companyattachment',array('id'=>$id))->row();
		$name=$rows['image']->imagename;
				
		if(file_exists('./uploads/gallery/'.$name) && !is_dir('./uploads/gallery/'.$name))
		{
		unlink('./uploads/gallery/'.$name);
		}
		
		$this->db->delete('companyattachment',array('id'=>$id));
		$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
	    $res['message'] = $message;
		$this->session->set_flashdata('message', $message);
		redirect("company/profile");

	}
    
}