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
        $this->load->model('admin/quote_model');
        $this->load->model('form_model');
        $this->load->model('form_subscription_model');
        $this->load->library("validation");
        $this->load->helper('url','form');
        $this->load->library('form_validation');
        $this->load->library('session');
        if ($this->session->userdata('company'))
            $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
          if ($this->session->userdata('company')) {    
            $data['pagetour'] = $this->companymodel->getcompanybyid($this->session->userdata('company')->id); }
                
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
        
        $this->db->insert('systemusers', array('parent_id'=>''));
		$itemid = $this->db->insert_id();
        $key = md5(uniqid($_POST['title']) . '-' . date('YmdHisu'));
        $_POST['regkey'] = $key;
        $_POST['id']= $itemid;
      //  echo '<pre>',print_r($_POST);die;
        $this->db->insert('company', $_POST);
       
        $this->sendRegistrationEmail($itemid, $key);
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Account Created Successfully.<br/>Please check your email for activation link.</div></div><div class="errordiv">');
        redirect('company/register');
    }

    function sendRegistrationEmail($id, $key)
    {
        $c = $this->companymodel->getcompanybyid($id);

        $link = base_url() . 'company/complete/' . $key;
        $data['email_body_title'] = "Dear " . $c->title ;
	  	$data['email_body_content'] = "Please click following link to complete your registration:  <br><br>
	    <a href='$link' target='blank'>$link</a>";
	  	$loaderEmail = new My_Loader();
        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
        $settings = (array) $this->companymodel->getconfigurations(1);
        $this->load->library('email');
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from($settings['adminemail'], "Administrator");

        $this->email->to($c->title . ',' . $c->primaryemail);

        $this->email->subject('Activate your account.');
        $this->email->message($send_body);
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
            $errormessage = 'Password and Confirm Password does not match. Please try again.';
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

        $data['email_body_title']  = "Dear " . $c->username;
        $data['email_body_content']  = "Congratulations! <br><br> Thanks for registration, Your registration is complete. You can login in Dashboard.
        <br/><br/>
        Your Login Profile is as follows:<br/>
        Login User Name : ". $c->username ." <br/>
        Login Password :  ". $rawpassword ." <br/>
        Company Name: ". $c->title ." <br/>
        Email Address: ". $c->primaryemail ." <br/>
        Contact Name: ". $c->contact ." <br/> <br/>";
        $loaderEmail = new My_Loader();
        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
        $settings = (array) $this->companymodel->getconfigurations(1);
        $this->load->library('email');
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from($settings['adminemail'], "Administrator");
        $this->email->to($c->primaryemail);
        $this->email->subject('Registration Completed');
        $this->email->message($send_body);
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
        $_POST['isdeleted']= 0;
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
		$data['files']=$this->db->get_where('company_files',array('company'=>$company->id))->result();
		$data['gallery']=$this->db->get_where('gallery',array('company'=>$company->id))->result();
		$data['companybanner']=$this->db->get_where('companybanner',array('companyid'=>$company->id,'isdeleted'=>0))->result();
		$data['members']=$this->db->where('cid',$company->id)->get("companyteam")->result();
        $this->db->where('company', $company->id);
        $emails = $this->db->get('companyemail')->result();
        $data['company'] = $company;
        $data['emails'] = $emails;

        $bhrs = $this->db->get_where('company_business_hours',array('company'=>$company->id))->result();
        if($bhrs){
        $businesshrs = array();
        foreach($bhrs as $dbh){

        	$businesshrs[$dbh->day.'start'] = $dbh->start;
        	$businesshrs[$dbh->day.'end'] = $dbh->end;
        	$businesshrs[$dbh->day.'closed'] = $dbh->isclosed;

        }
        $data['businesshrs'] = $businesshrs;

        }

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

    function deleteMember($id){
    	$company = $this->session->userdata('company');
    	if (!$company)
    		redirect('company/login');
    	$this->db->where('id', $id);
    	$this->db->where('cid', $company->id);
    	$this->db->delete('companyteam');

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
        $this->db->where('isdeleted',0);
        $this->db->where('primaryemail', $_POST['primaryemail']);
        if ($this->db->get('company')->num_rows > 0) {
            $data['types'] = $this->db->get('type')->result();
            $errormessage = "Email '{$_POST['primaryemail']}' already exists.";
        }
        if(isset($_POST['pagetour']))
        {
			$_POST['pagetour']=1;
		}
		else 
		{
			$_POST['pagetour']=0;
		}
		
		$orgpwd="";
		if(isset($_POST['password']) && $_POST['password']!="")
		{
			$orgpwd=$_POST['password'];
			$_POST['password']=md5($_POST['password']);
			$_POST['pwd']=$_POST['password'];
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

	       /* if (isset($_FILES['banner']['tmp_name']))
	        {
	            if (is_uploaded_file($_FILES['banner']['tmp_name'])) {
	                $nfn = $_FILES['banner']['name'];
	                $ext = end(explode('.', $nfn));
	                if (!in_array(strtolower($ext), array('jpg', 'gif', 'jpeg', 'png'))) {
	                    $errormessage = '* Invalid file type, upload banner file.';
	                } elseif (move_uploaded_file($_FILES['banner']['tmp_name'], "uploads/logo/" . $nfn)) {
	                  //  $this->_createThumbnail($nfn, 'banner', 270, 200);
	                    $_POST['banner'] = $nfn;
	                }
	            }    
	        }*/      
	        
            if(isset($_FILES['UploadFile']['name']))
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

					if(isset($filename) && $filename!=''){
            		$this->db->insert('companyattachment', array('company' => $company->id, 'imagename' => $filename));}


            		//echo "<pre>"; print_r($imagename); die;
            	}
            	//$_POST['lightbox'] = $imagename;
            }


         	if(isset($_FILES['UploadFile1']['name']))
        	{
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/filegallery/';
            	$count=0;
            	foreach ($_FILES['UploadFile1']['name'] as $filename)
            	{

            		$temp=$target;
            		$tmp=$_FILES['UploadFile1']['tmp_name'][$count];
            		$origionalFile=$_FILES['UploadFile1']['name'][$count];
            		$count=$count + 1;
            		$temp=$temp.basename($filename);
            		move_uploaded_file($tmp,$temp);
            		$temp='';
            		$tmp='';
                    if(isset($filename) && $filename!='')
                    {
            		$this->db->insert('company_files', array('company' => $company->id, 'filename' => $filename));
                    }

            		}

            			$file1 = 0;
            			if(isset($_POST['checkid'])){
            				foreach($_POST['checkid'] as $check){
            					$this->db->where('id', $check);
            					if(isset($_POST['file1'][$check]))
            					$file1 = 1;
            					else
            					$file1 = 0;
            					$this->db->update('company_files', array('private' => $file1));
            				}
            			}
            }



			  if(isset($_FILES['UploadFile2']['name']))
                {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/imagegallery/';
            	$count=0;
            	foreach ($_FILES['UploadFile2']['name'] as $filename)
            	{
            		$temp=$target;
            		$tmp=$_FILES['UploadFile2']['tmp_name'][$count];
            		$origionalFile=$_FILES['UploadFile2']['name'][$count];
            		$count=$count + 1;
            		$temp=$temp.basename($filename);
            		move_uploaded_file($tmp,$temp);
            		$temp='';
            		$tmp='';
                    if(isset($filename) && $filename!=''){
            		$this->db->insert('gallery', array('company' => $company->id, 'imagename' => $filename));}
            	}

            }
       
            if(isset($_FILES['UploadFile3']['name']))
                {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/logo/';
            	$count=0;
            	foreach ($_FILES['UploadFile3']['name'] as $filename)
            	{
            		$temp=$target;
            		$tmp=$_FILES['UploadFile3']['tmp_name'][$count];
            		$origionalFile=$_FILES['UploadFile3']['name'][$count];
            		
            		$temp=$temp.basename($filename);
            		move_uploaded_file($tmp,$temp);
            		$temp='';
            		$tmp='';
            		
            		
            		if (isset($_POST['bannerurl'][$count]) && $_POST['bannerurl'][$count] != '' && !preg_match("~^(?:f|ht)tps?://~i", $_POST['bannerurl'][$count])) 
			    	{
			        	$_POST['bannerurl'][$count] = "http://" .$_POST['bannerurl'][$count];
			        }
			        
            		$bannerUrl = (isset($_POST['bannerurl'][$count]) && $_POST['bannerurl'][$count] != '') ? $_POST['bannerurl'][$count] : '';
            		
                    if(isset($filename) && $filename!='')
                    {
            		 	$this->db->insert('companybanner', array('companyid' => $company->id, 'banner' => $filename,'bannerurl'=> $bannerUrl,'isdeleted'=>0));
                    }
                    $count=$count + 1;
            	}

            }
           
            if(isset($_POST['bannerurl']) && $_POST['bannerurl']!='')
            {
            	foreach ($_POST['bannerurl'] as $key=>$val)
            	{            		
            		$check = $this->db->get_where('companybanner',array('id'=>$key))->row();
            		
            		if (isset($val) && $val != '' && !preg_match("~^(?:f|ht)tps?://~i", $val)) 
			    	{
			        	$val = "http://" .$val;
			        }
			        
            		if(isset($check))
            		{
            			$this->db->update('companybanner',array('bannerurl'=> $val),array('id'=>$key));
            		}
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


        $dayarray = array('mon','tue','wed','thu','fri','sat','sun');

        foreach($dayarray as $day){
        	if(isset($_POST[$day."start"]) || isset($_POST[$day."end"]) || isset($_POST[$day."closed"]) ) {

        		if(isset($_POST[$day."start"]))
        		$start = $_POST[$day."start"];
        		else
        		$start = '';

        		if(isset($_POST[$day."end"]))
        		$end = $_POST[$day."end"];
        		else
        		$end = '';

        		if(isset($_POST[$day."closed"]))
        		$closed = 1;
        		else
        		$closed = 0;

        		$this->db->where('company =', $company->id);
        		$this->db->where('day', $day);
        		if ($this->db->get('company_business_hours')->num_rows > 0) {
        			$this->db->where('company =', $company->id);
        			$this->db->where('day', $day);
        			$this->db->update('company_business_hours', array('start' => $start,'end' => $end,'isclosed' => $closed));
        		}else{
        			$this->db->insert('company_business_hours', array('company' => $company->id, 'day' => $day, 'start' => $start,'end' => $end,'isclosed' => $closed));
        		}
        	  if(isset($_POST[$day."start"]))
			  	unset($_POST[$day."start"]);
			  if(isset($_POST[$day."end"]))
			  	unset($_POST[$day."end"]);
			  if(isset($_POST[$day."closed"]))
        		unset($_POST[$day."closed"]);
        	}
        }

 
        unset($_POST['_wysihtml5_mode']);
        if(isset($_POST['checkid']))
        unset($_POST['checkid']);
        if(isset($_POST['file1']))
        unset($_POST['file1']);
        if($_POST['password']=="")
        {
        unset($_POST['password']);      
        unset($_POST['pwd']);
        }  
        unset($_POST['bannerurl']);
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

        $sms="";
        if($this->session->userdata('company')->company_type=='3')
        {  
        	//$link = '<a href="'.site_url('company/login').'">Login</a>'; 
        	$link = site_url('site/supplier/'.@$_POST['username']); 	  	
        	$data['email_body_title']  = "Dear " .@$_POST['title'];
		  	$data['email_body_content']  = "You have updated Company Information as Follow:  <br><br>
		  	Username : ".@$_POST['username']."<br/>
		  	Password : ".@$orgpwd."<br/>
		  	Title : ".@$_POST['title']."<br/>
		  	Primaryemail : ".@$_POST['primaryemail']."<br/>
		  	Contact : ".@$_POST['contact']."<br/>
		  	City : ".@$_POST['city']."<br/>
		  	Zip : ".@$_POST['zip']."<br/>
		  	Street : ".@$_POST['street']."<br/>
		  	Phone : ".@$_POST['phone']."<br/>
		  	Fax : ".@$_POST['fax']."<br/>
		  	Invoice Note : ".@$_POST['invoicenote']."<br/>
		    Your Profile URL :  <a href='$link' target='blank'>$link</a>";
		  	$loaderEmail = new My_Loader();
	        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
	        $settings = (array) $this->companymodel->getconfigurations(1);
	        $this->load->library('email');
	        $config['charset'] = 'utf-8';
	        $config['mailtype'] = 'html';
	        $this->email->initialize($config);
	        $this->email->from($settings['adminemail'], "Administrator");
	        $this->email->to(@$_POST['title'] . ',' . @$_POST['primaryemail']);
	        $this->email->subject('Updated Company Information');
	        $this->email->message($send_body);
	        $this->email->set_mailtype("html");
	        $this->email->send();
	        $sms="Email Sent Successfully.";
        }

        $company = $this->supplier_model->get_supplier($company->id);  
        $anch= base_url() . 'dashboard';  
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">
        </a><div class="msgBox">Your profile has been saved.'.$sms.'<a href='.$anch.' target=blank>Click here to go to your dashboard.</a></div></div></div>');
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
        $data['email_body_title']  = "Dear " . $c->title ;
	  	$data['email_body_content']  = "Please click following link to change your password:  <br><br>
	    <a href='$link' target='blank'>$link</a>";
	  	$loaderEmail = new My_Loader();
        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
        $settings = (array) $this->companymodel->getconfigurations(1);
        $this->load->library('email');
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from($settings['adminemail'], "Administrator");

        $this->email->to($c->title . ',' . $c->primaryemail);

        $this->email->subject('Password change link.');
        $this->email->message($send_body);
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
            $errormessage = 'Password and Confirm Password does not match. Please try again.';
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
        $data['email_body_title'] = "Dear " . $c->title ;
	  	$data['email_body_content'] = "Please click following link to complete your registration:  <br><br>
	    <a href='$link' target='blank'>$link</a>";
	  	$loaderEmail = new My_Loader();
        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
        $settings = (array) $this->companymodel->getconfigurations(1);
        $this->load->library('email');
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from($settings['adminemail'], "Administrator");

        $this->email->to($c->title . ',' . $c->primaryemail);

        $this->email->subject('Activate your account.');
        $this->email->message($send_body);
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
        			   tier, creditlimit, totalcredit, creditfrom, creditto, creditonly
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
                WHERE od.orderid=o.id AND o.type='Manual' AND od.paymentstatus!='Paid' AND od.status!='Void' AND od.accepted!=-1
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


    function changeitemtier()
    {
        $company = $this->session->userdata('company');
        if (!$company)
            die;

        if(!@$_POST['purchasingadmin'])
            die;

        if(!@$_POST['tier'])
            die;

        if(!@$_POST['itemid'])
            die;

        if(!@$_POST['quote'])
            die;

        $this->db->where('company', $company->id);
        $this->db->where('purchasingadmin', $_POST['purchasingadmin']);
        $this->db->where('itemid', $_POST['itemid']);
        $this->db->where('quote', $_POST['quote']);
        if($this->db->get('purchasingtier_item')->row())
        {
        	if(isset($_POST['tier']))
            $update = array('tier'=>strtolower($_POST['tier']));

            if(isset($_POST['notes']))
            $update['notes'] = $_POST['notes'];

            if(isset($_POST['qty']))
            $update['qty'] = $_POST['qty'];
            else
            $update['qty'] = 0;

            $this->db->where('company', $company->id);
        	$this->db->where('purchasingadmin', $_POST['purchasingadmin']);
        	$this->db->where('itemid', $_POST['itemid']);
        	$this->db->where('quote', $_POST['quote']);
            $this->db->update('purchasingtier_item', $update);
            echo "Item price Changed";
        }
        else
        {
            $insert = array();
            if(isset($_POST['tier']))
            $insert['tier'] = $_POST['tier'];
            if(isset($_POST['notes']))
            $insert['notes'] = $_POST['notes'];

            if(isset($_POST['qty']))
            $insert['qty'] = $_POST['qty'];
            else
            $update['qty'] = 0;

            $insert['company'] = $company->id;
            $insert['itemid'] = $_POST['itemid'];
            $insert['quote'] = $_POST['quote'];
            $insert['purchasingadmin'] = $_POST['purchasingadmin'];
            $this->db->insert('purchasingtier_item', $insert);
            echo "Item price Set";
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
               
		if(@$_POST['tier'])
		{
	        foreach ($_POST['tier'] as $admin => $tier)
	         { 
	            $arr = array('purchasingadmin' => $admin, 'company' => $company->id);
	            $this->db->where($arr);
	            $this->db->delete('purchasingtier');
	            $arr['tier'] = $tier;
	            $arr['creditlimit'] = $_POST['creditlimit'][$admin];
	            $arr['totalcredit'] = $_POST['creditlimit'][$admin];//this is not a mistake, same value is fed to both fields.
	            $arr['creditfrom']="";
	            $arr['creditto']="";
	           /* if($_POST['creditfrom'][$admin])
	            	$arr['creditfrom'] = date('Y-m-d', strtotime($_POST['creditfrom'][$admin]));
	            if($_POST['creditto'][$admin])
	            	$arr['creditto'] = date('Y-m-d', strtotime($_POST['creditto'][$admin])); */
	             if(@$_POST['creditonly'][$admin]=='on')
	            	$arr['creditonly'] = '1';  	 
	            $this->db->insert('purchasingtier', $arr);
	        }
       
		}

        $message = 'Network connection settings updated for purchasing companies.';
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('company/networkconnections');
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
        
        if(isset($_POST['disableaccountnumber']) && $_POST['disableaccountnumber']!=""){
        	$accountnumber=$_POST['disableaccountnumber'];
        	
        }
              
         if(isset($_POST['enableaccountnumber']) && $_POST['enableaccountnumber']!=""){
        	$accountnumber=$_POST['enableaccountnumber'];
        	
        }
        
         if(isset($_POST['disableroutingnumber']) && $_POST['disableroutingnumber']!=""){
        	$routingnumber=$_POST['disableroutingnumber'];
        	
        }
        
         if(isset($_POST['enableroutingnumber']) && $_POST['enableroutingnumber']!=""){
        	$routingnumber=$_POST['enableroutingnumber'];
        	
        }    
        //$this->db->where('company',$company->id)->update('bankaccount',$_POST);
        $this->db->where('company',$company->id)->update('bankaccount',array('bankname'=>$_POST['bankname'],'accountnumber'=>$accountnumber,'routingnumber'=>$routingnumber));

        $message = 'Bank Account settings updated.';
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('company/bankaccount');
    }
    
    public function checkpwd()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');	
		if(!@$_POST['pwd'])
		{
			die;
		}
		
        $_POST['pwd'] = md5($_POST['pwd']);
        $check = $this->db->get_where('company',array('id'=>$company->id))->row();
        if ($check) 
        {
        	if($check->password==$_POST['pwd'])
        	{ 
        		echo 1;
        		
        	}
        	else 
        	{   
        		echo 0;
        	}          
        }
        die;
      	
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
    	//$res = $this->do_upload();

    	//$res = $this->do_upload();
    	/*if(isset($res['error'])){
    		$this->session->set_flashdata('message',$res['error']);

    	}
    	else {*/
    	$result = $this->admodel->saveAd();
    	//}
    	if($result){
    		$message =  '<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Saved Successfully.</div></div></div>';
    		$res['message'] = $message;
    		$this->session->set_flashdata('message', $message);
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
    	if(isset($res['ads']) && $res['ads']!="")
    	{
    		$res['image']=$this->db->get_where('AdImage',array('adid'=>$res['ads'][0]->id,'company'=>$res['ads'][0]->user_id))->result();
    	}
    	
    	$catcodes = $this->catcode_model->get_categories_tiered();
     	$itemcodes = $this->itemcode_model->get_itemcodes(100,0,$res['ads'][0]->category);
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
			//$res = $_POST;
	    	//$res = $this->do_upload();

	    	//$res = $this->do_upload();

	    	/*if(isset($res['error'])){
	    		$this->session->set_flashdata('message',$res['error']);
	    		redirect("company/ads");
	    	}*/

	    		$this->admodel->updateAd($id);

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

	function formsubmission($id)
	{
	    $companyId = $this->session->userdata('company')->id;

	    $where="";

	    if(isset($_POST['companyname']) && $_POST['companyname']!="")
	    {
	    $where = "AND fromid = ".$_POST['companyname'];
	    }
	    elseif(isset($id) && $id!="") 
	    {
	    $where = "AND fromid = ".$id;
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
		
		 $company = $this->session->userdata('company');
         if (!$company)
            redirect('company/login');
		
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
			$data['id'] = $id;//$this->session->userdata('company')->id;

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

	function deletepurchasingtier($id)
    {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        $this->db->delete('network',array('purchasingadmin'=>$id,'company'=>$company->id));
        $message = 'Purchasing company Deleted Successfully.';
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('company/networkconnections');
    }

    function deletefile($id)
	{
		$rows['file']=$this->db->get_where('company_files',array('id'=>$id))->row();
		$name=$rows['file']->filename;

		if(file_exists('./uploads/gallery/'.$name) && !is_dir('./uploads/gallery/'.$name))
		{
		unlink('./uploads/gallery/'.$name);
		}
		$this->db->delete('company_files',array('id'=>$id));
		$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
	    $res['message'] = $message;
		$this->session->set_flashdata('message', $message);
		redirect("company/profile");

	}

	function deletegalleryimage($id)
	{
		$rows['gallery']=$this->db->get_where('gallery',array('id'=>$id))->row();
		$name=$rows['gallery']->imagename;

		if(file_exists('./uploads/imagegallery/'.$name) && !is_dir('./uploads/imagegallery/'.$name))
		{
		unlink('./uploads/imagegallery/'.$name);
		}

		$this->db->delete('gallery',array('id'=>$id));
		$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
	    $res['message'] = $message;
		$this->session->set_flashdata('message', $message);
		redirect("company/profile");

	}

	function designbook()
	{
		/*---------------------------------------------------------------------------*/
		$catcodes = $this->catcode_model->get_designcategories_tiered();
        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $this->_set_fields();
        $data['category'] = $categories;
        $data['product_categories'] = false;
        $data['categories'] = $this->itemcode_model->getdesigncategories();
        /*---------------------------------------------------------------------------*/
        
		$company = $this->session->userdata('company');
        $design=$this->db->get_where('designbook',array('company'=>$company->id))->result();
        
        if(isset($design) && count($design) > 0)
        {
        	foreach ($design as $d)
        	{
        		$designcat = $this->db->get_where('designbook_category',array('itemid'=>$d->id))->result();
        		if(isset($designcat) && count($designcat) > 0)
        		{
	        		$ccid=array();
	        		foreach ($designcat as $value) 
	        		{
	        		 array_push($ccid,$value->categoryid);        			
	        		}	        		
	        		$d->catid = $ccid;
        		}
        	} 
        }
        $data['design']=$design;
        //echo "<pre>"; print_r($data['design']); die;
        
       	$codes = $this->db->get('item')->result();        
        $items = array();
        foreach ($codes as $code) {
            $item = array();
            $item['value'] = $code->itemcode;
            $item['label'] = '<!--<font color="#990000">-->'.$code->itemcode.'<!--</font>--> - '.$code->itemname;           
            $itemids[] = $item;           
        }        
    	$data['itemids'] = $itemids;
        
        $this->load->view('company/designbook',$data);
	}


	function designbook1()
	{
		//echo "<pre>"; print_r($_POST); die;
       $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
		$this->load->library('image_lib');
			
        $errormessage = '';
        $message='';
		   if(isset($_FILES['UploadFile']['name']))
            {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/designbook/';
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
                    if(isset($filename) && $filename!='')
                      {
                      	if(isset($_POST['selectresize']) && $_POST['selectresize']!=""){
                      		$config['image_library'] = 'gd2';
                      		$config['source_image'] = 'uploads/designbook/'.$filename;
                      		//$config['create_thumb'] = TRUE;
                      		$config['maintain_ratio'] = FALSE;
                      		$sizes = explode("*",$_POST['selectresize']);
                      		$config['width']     = $sizes[0];
                      		$config['height']   = $sizes[1];

                      		$this->image_lib->clear();
                      		$this->image_lib->initialize($config);
                      		$this->image_lib->resize();
                      	}
            		$this->db->insert('designbook', array('company' => $company->id, 'imagename' => $filename));
            		 $id = $this->db->insert_id();
                      }
            	 }
            	$message='Images Uploaded Succesfully.';
             }
		   $file = 0;
           if(isset($_POST['publishid']))
           {
              foreach($_POST['publishid'] as $check)
              {

            	 if(isset($_POST['file'][$check]))
            	   {
            		 $file = 1;
            	   }
            	 else
            	   {
            		$file = 0;
            	   }
            		$this->db->where('id', $check);
            		$this->db->update('designbook', array('publish' => $file));
               }
           }

           if(isset($_POST['nameid']))
           {
              foreach($_POST['nameid'] as $check1)
              {
            	 if(isset($_POST['designname'][$check1]))
            	 {
            	    $name = $_POST['designname'][$check1];
            	    $this->db->where('id', $check1);
            		$this->db->update('designbook', array('name' => $name));
            	 }
               }
           }
           
           else
            {
             $errormessage = 'Please Enter Name for Image.';
            }
             
              /*---------------------My Code-------------------------------*/
              
			$this->_set_fields();		
			$catcodes = $this->catcode_model->get_designcategories_tiered();
			$primarycategory="";
	        $categories = array();
	        if ($catcodes)
	        {
	            if (isset($catcodes[0]))
	            {
	                build_category_tree($categories, 0, $catcodes);
	            }
	        }
	          $data['category'] = $categories;  
	        if(isset($_POST['designcatid']))
           {
              foreach($_POST['designcatid'] as $check1)
              {
              	
            	if(isset($_POST['category'][$check1]))
            	 {
            	    $catid = $_POST['category'][$check1][0];
            	    $this->db->where('id', $check1);
            		$this->db->update('designbook', array('category' => $catid));
            		
            		$options2 = array();
            		$this->db->delete('designbook_category', array('itemid' => $check1));
            		foreach ($_POST['category'][$check1] as $value) 
            		{
            		$options2['itemid'] = $check1;
				    $options2['categoryid'] = $value;
				    $this->db->insert('designbook_category', $options2);          			
            		}				  
            	 }
               }  
           }
	        
	        
	     
			/*---------------------End My Code-------------------------------*/
             
            
           $message="Data Updated successfully.";
			if ($errormessage)
			 {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $errormessage . '</div></div></div>');
              }


            if ($message)
			 {
               $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">' . $message . '</div></div></div>');
			 }

       redirect('company/designbook');
	}

	 function deletedesignfile($id)
	{
		$rows['design']=$this->db->get_where('designbook',array('id'=>$id))->row();
		$name=$rows['design']->imagename;
		if(file_exists('./uploads/designbook/'.$name) && !is_dir('./uploads/designbook/'.$name))
		{
		unlink('./uploads/designbook/'.$name);
		}
		$this->db->delete('designbook',array('id'=>$id));
		$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
		$this->session->set_flashdata('message', $message);
		redirect("company/designbook");

	}

	function getitembycode()
    {
        $code = $_POST['code'];
        
        $item = $this->quote_model->finditembycode($code);		
        
        $this->db->where('itemid',$item->itemid);
		$this->db->where('type','Supplier');
		$this->db->where('company',$this->session->userdata('company')->id);
		$companyitem = $this->db->get('companyitem')->row();
        //print_r($companyitem);

		if($companyitem)
		{			
			$item->ea = $companyitem->ea;
			if($companyitem->itemname!="")
			$item->itemname = $companyitem->itemname;
			if($companyitem->itemcode!="")
			$item->itemcode = $companyitem->itemcode;
		}
        
        echo json_encode($item); // die;
    }    
    
    
    function savefbwall()
    {
    	//echo "<pre>ctrl"; print_r($_POST); die;
    	$insertarray = array();
    	
    	if(@$_POST['reply']){
    		
    		if(@$_POST['replysection'.$_POST['reply']]){
    			$insertarray['replyto'] = $_POST['reply'];
    			$_POST['commentsection'] = $_POST['replysection'.$_POST['reply']];
    		}
    		
    	}
    	
    	
    	if(@$_POST['senderid']){
    		$insertarray['from_type'] = $_POST['logintype'];
    		$insertarray['from'] = $_POST['senderid'];
    	}    	
    	
    	if(@$_POST['receiverid']){
    		$insertarray['to_type'] = $_POST['messageto'];
    		$insertarray['to'] = $_POST['receiverid'];
    	}  
    	$name="";
    	if(@$_POST['senderid']!='guest'){
    		$udata=$this->db->get_where('users',array('id'=>$_POST['senderid']))->row();
    		$name=$udata->companyname;
    	} 
    	else 
    	{
    		$name=$_POST['senderid'];
    	}
    	//echo "<pre>"; print_r($name); die;
    	$insertarray['company'] = $_POST['companyid'];
    	$insertarray['message'] = $_POST['commentsection'];    	
    	$insertarray['senton'] = date('Y-m-d H:i');
    	$insertarray['threadsenton'] = date('Y-m-d H:i');   	
    	$cdata=$this->db->get_where('company',array('id'=>$_POST['receiverid']))->row(); 
    	$this->db->insert('fb_comment', $insertarray);
    	$data['email_body_title'] = "Dear " . $cdata->title ;
	  	$data['email_body_content'] = "You have a new comment from ".$name."  <br><br>
	 <strong>Comment:</strong>".$_POST['commentsection']."";
	  	$settings = (array) $this->companymodel->getconfigurations(1);
	  	$loaderEmail = new My_Loader();
        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
        $this->load->library('email');
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from($settings['adminemail'], $name);
        $this->email->to($cdata->primaryemail);
        $this->email->subject('Materialking Profile Wall Comment.');
        $this->email->message($send_body);
        $this->email->set_mailtype("html");
        $this->email->send();
        
        echo "<pre>"; print_r($this->email); die;   	
    	//echo $_POST['about']; die;
    	
    	if(@$_POST['reply']){
    		$updatearray = array();
    		$updatearray['threadsenton'] = date('Y-m-d H:i');    		
    		$this->db->where('id', $_POST['reply']);    		
    		$result = $this->db->update('fb_comment',$updatearray);
    	}
    	
    }
    
    function getcompanycomments(){
    	
    	$this->db->where('company',$_POST['companyid']);    	
    	$this->db->where('replyto IS NULL', null, false);
    	$this->db->order_by('threadsenton', 'DESC');
    	$result = $this->db->get('fb_comment')->result();
    	$messagebody = array();
    	foreach ($result as $res){
    		$res->from_types = $res->from_type."-".$res->id;
    		$messagebody[$res->from_types]['id'] = $res->id;
    		$messagebody[$res->from_types]['message'] = $res->message;
    		$messagebody[$res->from_types]['from'] = $res->from;
    		$messagebody[$res->from_types]['from_type'] = $res->from_type;
    		
    		if($res->from_type=='users'){
    			$this->db->select("companyname");
    			$this->db->where('id',$res->from);
				$nameres = $this->db->get('users')->row();
				
				$this->db->select("logo");
    			$this->db->where('purchasingadmin',$res->from);
				$logres = $this->db->get('settings')->row();
				if($logres){
				if($logres->logo && file_exists("./uploads/logo/".$logres->logo))
				$messagebody[$res->from_types]['logosrc'] = site_url('uploads/logo/'.$logres->logo);
				else 
				$messagebody[$res->from_types]['logosrc'] =  site_url('uploads/logo/noavatar.png');
				}else 
				$messagebody[$res->from_types]['logosrc'] =  site_url('uploads/logo/noavatar.png');
				$messagebody[$res->from_types]['name'] = $nameres->companyname;
    		}elseif($res->from_type=='company'){
    			$this->db->select("logo, title");
    			$this->db->where('id',$res->from);
				$logres = $this->db->get('company')->row();
				if($logres->logo && file_exists("./uploads/logo/".$logres->logo))
				$messagebody[$res->from_types]['logosrc'] = site_url('uploads/logo/'.$logres->logo);
				else 
				$messagebody[$res->from_types]['logosrc'] = site_url('templates/site/assets/img/logo.png');
				$messagebody[$res->from_types]['name'] = $logres->title;
    		}else {
    			$messagebody[$res->from_types]['logosrc'] = site_url('uploads/logo/noavatar.png');
    			$messagebody[$res->from_types]['name'] = "Guest";
    		}
    		
    		
    		$messagebody[$res->from_types]['showago'] = $this->tago(strtotime($res->senton));
    		$datetime = strtotime($res->senton);
			$messagebody[$res->from_types]['showdate'] = date("M d, Y H:i A", $datetime);
			
			$this->db->where('company',$_POST['companyid']);
			$this->db->where('replyto',$res->id);
			$this->db->order_by('threadsenton', 'DESC');
    		$result2 = $this->db->get('fb_comment')->result();
    		
    		if($result2){
				$messagebody2 = array();
    			foreach ($result2 as $res2){
    				$res2->from_types = $res2->from_type."-".$res2->id;
    				$messagebody2[$res2->from_types]['id'] = $res2->id;
    				$messagebody2[$res2->from_types]['message'] = $res2->message;
    				$messagebody2[$res2->from_types]['from'] = $res2->from;
    				$messagebody2[$res2->from_types]['from_type'] = $res2->from_type;

    				if($res2->from_type=='users'){
    					$this->db->select("companyname");
    					$this->db->where('id',$res2->from);
    					$nameres = $this->db->get('users')->row();

    					$this->db->select("logo");
    					$this->db->where('purchasingadmin',$res2->from);
    					$logres = $this->db->get('settings')->row();

    					if($logres->logo && file_exists("./uploads/logo/".$logres->logo))
    					$messagebody2[$res2->from_types]['logosrc'] = site_url('uploads/logo/'.$logres->logo);
    					else
    					$messagebody2[$res2->from_types]['logosrc'] =  site_url('uploads/logo/noavatar.png');
    					$messagebody2[$res2->from_types]['name'] = $nameres->companyname;
    				}elseif($res2->from_type=='company'){
    					$this->db->select("logo, title");
    					$this->db->where('id',$res2->from);
    					$logres = $this->db->get('company')->row();
    					if($logres->logo && file_exists("./uploads/logo/".$logres->logo))
    					$messagebody2[$res2->from_types]['logosrc'] = site_url('uploads/logo/'.$logres->logo);
    					else
    					$messagebody2[$res2->from_types]['logosrc'] = site_url('templates/site/assets/img/logo.png');
    					$messagebody2[$res2->from_types]['name'] = $logres->title;
    				}else {
    					$messagebody2[$res2->from_types]['logosrc'] = site_url('uploads/logo/noavatar.png');
    					$messagebody2[$res2->from_types]['name'] = "Guest";
    				}

    				$messagebody2[$res2->from_types]['showago'] = $this->tago(strtotime($res2->senton));
    				$datetime = strtotime($res2->senton);
    				$messagebody2[$res2->from_types]['showdate'] = date("M d, Y H:i A", $datetime);
    			}
				$messagebody[$res->from_types]['innercomment'] = $messagebody2;
    		}
			
    	}
    	
    	echo json_encode($messagebody);
    }
    
    
    function tago($time)
    {
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");
        
        $now = time();
        $difference     = $now - $time;
        $tense         = "ago";
        
        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
         $difference /= $lengths[$j];
        }
        $difference = round($difference);
        
        if($difference != 1) {
         $periods[$j].= "s";
        }
        return "$difference $periods[$j] ago ";
    }
    
    /*function add ()
    {
        $catcodes = $this->catcode_model->get_categories_tiered();
        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $this->_set_fields();
        $data['heading'] = 'Add New Itemcode';
        $data['message'] = '';
        $data['action'] = site_url('admin/itemcode/add_itemcode');
        $data['category'] = $categories;
        $data['product_categories'] = false;
        $data['categories'] = $this->itemcode_model->getcategories();
        $data['companies'] = $this->db->get('company')->result();
        $this->validation->featuredsupplier = 38;
        $this->load->view('admin/itemcode', $data);
    }
    
     function add_itemcode ()
    {
        $data['heading'] = 'Add New Itemcode';
        $data['action'] = site_url('admin/itemcode/add_itemcode');
        //$data['category'] = $this->itemcode_model->getCategoryList();
        //$data['subcategory'] = $this->itemcode_model->getSubCategoryList();
        $this->_set_fields();
        $this->_set_rules();
        $catcodes = $this->catcode_model->get_categories_tiered();
        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $data['category'] = $categories;
        if ($this->validation->run() == FALSE)
        {
            $data['message'] = $this->validation->error_string;
            $data['categories'] = $this->itemcode_model->getcategories();
            $data['companies'] = $this->db->get('company')->result();
            $this->load->view('admin/itemcode', $data);
        }
        elseif ($this->itemcode_model->checkDuplicateCode($this->input->post('itemcode'), 0))
        {
            $data['message'] = 'Duplicate Itemcode';
            $data['categories'] = $this->itemcode_model->getcategories();
            $data['companies'] = $this->db->get('company')->result();
            $this->load->view('admin/itemcode', $data);
        }
        elseif ($this->itemcode_model->checkDuplicateUrl($this->input->post('url'), 0))
        {
            $data['message'] = 'Duplicate Itemcode';
            $data['categories'] = $this->itemcode_model->getcategories();
            $data['companies'] = $this->db->get('company')->result();
            $this->load->view('admin/itemcode', $data);
        }
        else
        {
        	 if(isset($_FILES['UploadFile']['name']))
                {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/item/';
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
            	}

            }
            $this->do_upload();
            $itemid = $this->itemcode_model->SaveItemcode();
            $this->session->set_flashdata('message',
            '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Item Code Added Successfully</div></div>');
            redirect('admin/itemcode');
        }
    }
    
    */
    
    function _set_fields ()
    {
        $fields['category'] = 'category';    
        $this->validation->set_fields($fields);
    }
    
    function customerlogin()
    {
    	$data['message'] = '';
        $this->load->template('../../templates/front/register', $data);
        $this->load->view('company/customerlogin', $data);
    }
    
    function checkcustomerlogin() {
    	
        if (!@$_POST)
            die('Wrong access');
        $errormessage = '';
        if (!@$_POST['username'])
            $errormessage = 'Please Provide Username';
        if (!@$_POST['password'])
            $errormessage = 'Please Provide Password';

        if ($errormessage) {
        	
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $errormessage . '</div></div></div>');
            redirect('company/customerlogin/');
        }

        $_POST['password'] = md5($_POST['password']);
        $this->db->where($_POST);
        $check = $this->db->get('customer')->row();

        if ($check) {
            $data['customer'] = $check;
            $data['logintype'] = 'customer';

            $data['comet_user_id'] = $check->id;
            $data['comet_user_email'] = $check->email;

            $this->session->set_userdata($data);

			@session_start();
			$_SESSION['comet_user_id']=$check->id;
			$_SESSION['comet_user_email']=$check->email;
			$_SESSION['userid']=$check->id;
			$_SESSION['logintype']='customer';

            redirect('site/customerbill');
        } else {
            $data['message'] = 'Invalid Login';
            $this->load->template('../../templates/front/register', $data);
            $this->load->view('company/customerlogin', $data);
        }
    }
    
    function networkconnections()
    {
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

        $sql = "SELECT u.id purchasingadmin, u.companyname purchasingcompany, u.fullname purchasingfullname,u.address purchasingaddress,u.regdate purchasingregdate,
        			   tier, creditlimit, totalcredit, creditfrom, creditto, creditonly
				FROM " . $this->db->dbprefix('users') . " u
				INNER JOIN pms_network n ON u.id=n.purchasingadmin AND n.company='" . $company->id . "'
				LEFT JOIN " . $this->db->dbprefix('purchasingtier') . " pt ON pt.purchasingadmin=u.id AND pt.company='" . $company->id . "'
				WHERE u.usertype_id=2
			";
  
        $admins = $this->db->query($sql)->result();
        $data['admins'] = array();
        foreach($admins as $admin)
        {
        	 
            $pa = $admin->purchasingadmin;
		    $settings = $this->settings_model->get_setting_by_admin($pa);
		   		    
		    $awarded=0;
			$admin->pro=$this->db->get_where('project',array('purchasingadmin'=>$pa))->result();
			$admin->quo=$this->db->get_where('quote',array('purchasingadmin'=>$pa,'potype'=>'Bid'))->result();				  
			$admin->directquo=$this->db->get_where('quote',array('purchasingadmin'=>$pa,'potype'=>'Direct'))->result();
			     	
			if($admin->quo)
			{
			   foreach($admin->quo as $quot)
				{							
				   if($this->quote_model->getawardedbid($quot->id))
				   $awarded++;							
				}
			    $admin->awar = $awarded;
			}
			    		    
		    $query = "SELECT
		    			(SUM(r.quantity*ai.ea) + (SUM(r.quantity*ai.ea) * ".$settings->taxpercent." / 100))
		    			totalunpaid FROM
		    			".$this->db->dbprefix('received')." r, ".$this->db->dbprefix('awarditem')." ai
						WHERE r.awarditem=ai.id AND r.paymentstatus!='Paid' AND ai.company='".$company->id."'
						AND ai.purchasingadmin='$pa'";
		   
		    $due = $this->db->query($query)->row()->totalunpaid;
		    $due = round($due,2);
		   
		    $query = "SELECT (SUM(od.quantity * od.price) + (SUM(od.quantity * od.price) * o.taxpercent / 100))
		    	orderdue
                FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
                WHERE od.orderid=o.id AND o.type='Manual' AND od.paymentstatus!='Paid' AND od.status!='Void' AND od.accepted!=-1
                AND o.purchasingadmin='$pa' AND od.company='".$company->id."'";
		    
		    $manualdue = $this->db->query($query)->row()->orderdue;
		    $manualdue = round($manualdue,2);
		    $due += $manualdue;
		    $admin->amountdue = $due;
            $data['admins'][]=$admin;
            
        } 
        $data['tier'] = $tier;
        $this->load->view('company/networkconnections', $data);       	
    }
    
    function savelistsubscribers()
    {
		if(isset($_POST['subscribersID']) && $_POST['subscribersID'] != '' && isset($_POST['fieldName']) && $_POST['fieldName'] != '')
		{
			$subscriberId = $_POST['subscribersID'];
			
			foreach ($_POST['fieldName'][$subscriberId] as $key=>$val)
			{
				$updateArr = array(
									'name'=>$_POST['fieldName'][$subscriberId][$key],
									'value'=>$_POST['fieldValue'][$subscriberId][$key]
								  );
				$where = array(
								'id'=>$key,
								'subscriber_id'=>$subscriberId
							  );
				
			    $this->db->update('newsletter_subscribers_data',$updateArr,$where);				  
			}
			
			$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Record Saved Successfully.</div></div></div>';
			$this->session->set_flashdata('message', $message);
		
		}
			redirect("company/listsubscribers");	
    }

    function deletesubscribersdata()
    {
    	if(isset($_POST['subscribersID']) && $_POST['subscribersID'] != '')
		{
	    	$subscriberId = $_POST['subscribersID'];
	    	$this->db->delete('newsletter_subscribers',array('id'=>$subscriberId));
	    	$this->db->delete('newsletter_subscribers_data',array('subscriber_id'=>$subscriberId));
			$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
			$this->session->set_flashdata('message', $message);
		}	
		redirect("company/listsubscribers");	
    }
    
    
    
    function invoicecycle()
    {
    	$company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        $this->db->where('company', $company->id);
        
        $sql = "SELECT u.id purchasingadmin, u.companyname purchasingcompany, u.fullname purchasingfullname,
        			   discount_percent, penalty_percent, duedate, term, discountdate  
				FROM " . $this->db->dbprefix('users') . " u
				INNER JOIN pms_network n ON u.id=n.purchasingadmin AND n.company='" . $company->id . "'
				LEFT JOIN " . $this->db->dbprefix('invoice_cycle') . " ic ON ic.purchasingadmin=u.id AND ic.company='" . $company->id . "'
				WHERE u.usertype_id=2
			";
        //echo $sql;
        $data['admins'] = $this->db->query($sql)->result();
      
		/*---------------------------------------------------*/
        
        $this->load->view('company/invoicecycle', $data);     
        	
    }
    
    
    function saveinvoicecycle()
    {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login'); 
        // echo "<pre>",print_r($_POST);       
		if(@$_POST['term'])
		{
	        foreach ($_POST['term'] as $admin => $discount_percent)
	         { 
	            $arr = array('purchasingadmin' => $admin, 'company' => $company->id);
	            $this->db->where($arr);
	            $this->db->delete('invoice_cycle');
	            
	            if($_POST['discount_percent'][$admin])
	            $arr['discount_percent'] = $_POST['discount_percent'][$admin];
	            
	            $arr['penalty_percent'] = $_POST['penalty_percent'][$admin];	           
	            
	            if($_POST['duedate'][$admin])
	            	$arr['duedate'] = date('Y-m-d', strtotime($_POST['duedate'][$admin]));
	            
	            if($_POST['term'][$admin])
	            	$arr['term'] = $_POST['term'][$admin]; 	    
	            //echo $_POST['discountdate'][$admin]; die;	 
	            if($_POST['discountdate'][$admin])
	            	$arr['discountdate'] = date('Y-m-d', strtotime($_POST['discountdate'][$admin])); 	     
				
	           // if($_POST['discount_percent'][$admin] == '21.00')
				//echo "<pre>",print_r($arr); die;
		            	           	
	            $this->db->insert('invoice_cycle', $arr);
	            
	            if($_POST['duedate'][$admin]!="")
	            $this->setallinvoiceduedate($admin,$_POST['duedate'][$admin],$_POST['term'][$admin]);
	            
	        }
       
		}

        $message = 'Invoice Cycle updated for purchasing companies.';
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('company/invoicecycle');
    }
    
    
    function deleteinvoicecycle($id)
    {
        $company = $this->session->userdata('company');
        if (!$company)
            redirect('company/login');
        $this->db->delete('network',array('purchasingadmin'=>$id,'company'=>$company->id));
        $message = 'Purchasing company Deleted Successfully.';
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('company/invoicecycle');
    }

    
    
    function setallinvoiceduedate($purchasingadmin, $datedue, $term)
	{
		$company = $this->session->userdata('company');
		if(!$company)
		    die;
		$datedue = date('Y-m-d', strtotime($datedue));		
		
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$invs = $this->quotemodel->getpurchaserinvoices($company->id, $purchasingadmin);
		
		
		$data['email_body_title']  = "";
		$data['email_body_content']  = "";
		$gtotal = 0;
				
		foreach ($invs as $invoice)
		{ 
			if($term ==30)
			$monthcount=1;
			if($term ==60)
			$monthcount=2;
			if($term ==90)
			$monthcount=3;
			
			$next_term = date("Y-m-d", strtotime("$invoice->receiveddate +".$monthcount." month")); 
			
			$exploded = explode("-",$datedue);
								
			$explode = explode("-",$next_term);
			$explode[2] = $exploded[2];
			$next_term = implode("-",$explode);
			
			$arr = array('datedue' => $next_term);
			$this->db->where('invoicenum',$invoice->invoicenum)->where('awarditem',$invoice->awarditem)->update('received',$arr);
			
			$subject = "Due Date Set For Invoice ".$invoice->invoicenum;
			
		    $config = (array)$this->settings_model->get_setting_by_admin ($invoice->purchasingadmin);
		    $config = array_merge($config, $this->config->config); 		
			$olddate=strtotime($invoice->awardedon); $awarddate = date('m/d/Y', $olddate);
			$data['email_body_title'] .= 'Dear '.$invoice->username.' ,<br><br>';
			$data['email_body_content'] .= $invoice->supplierusername.' has set Due Date for Invoice '.$invoice->invoicenum.' from PO# '.$invoice->ponum.', Ordered on '.$awarddate.' to Due on  '.$next_term.'<br><br>';
			$data['email_body_content'] .= 'Please see order details below :<br>';
			$data['email_body_content'] .= '
					<table class="table table-bordered span12" border="1">
		            	<tr>
		            		<th>Invoice</th>
		            		<th>Received On</th>
		            		<th>Supplier Name</th>
		            		<th>Supplier Address</th>
		            		<th>Supplier Phone</th>
		            		<th>Order Number</th>
		            		<th>Item</th>
		            		<th>Quantity</th>
		            		<th>Payment Status</th>
		            		<th>Verification</th>
		            		<th>Due Date</th>
		            		<th>Price</th>
		            	</tr>';
			
	        $data['email_body_content'] .= '<td>'.$invoice->invoicenum.'</td>
            		<td>'.$invoice->receiveddate.'</td>
            		<td>'.$invoice->supplierusername.'</td>
            		<td>'.$invoice->address.'</td>
            		<td>'.$invoice->phone.'</td>
            		<td>'.$invoice->ponum.'</td>
            		<td>'.$invoice->itemname.'</td>
            		<td>'.$invoice->quantity.'</td>
            		<td>'.$invoice->paymentstatus.'</td>
            		<td>'.$invoice->status.'</td>
            		<td>'.$next_term.'</td>
            		<td align="right">'.number_format($invoice->ea,2).'</td>
	            	  </tr>';
	        $total = $invoice->ea*$invoice->quantity;
            $gtotal+=$total;
	        $tax = $gtotal * $config['taxpercent'] / 100;
            $totalwithtax = number_format($tax+$gtotal,2);
            $data['email_body_content'] .= '<tr><td colspan="12">&nbsp;</td> <tr>
            		<td colspan="11" align="right">Total</td>
            		<td style="text-align:right;">$'.number_format($gtotal,2).'</td>
            	</tr>
            	
            	<tr>
            		<td colspan="11" align="right">Tax</td>
            		<td style="text-align:right;">$'. number_format($tax,2).'</td>
            	</tr>
            	
            	<tr>
            		<td colspan="11" align="right">Total</td>
            		<td style="text-align:right;">$'. $totalwithtax.'</td>
            	</tr>';
            $data['email_body_content'] .= '</table>';   
	    }
	    
	    if(@$invs[0]->email && @$subject){
	      
	    $loaderEmail = new My_Loader();
	    $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->to(@$invs[0]->email);
		$this->email->from($this->session->userdata("company")->primaryemail,$this->session->userdata("company")->primaryemail);
		
		$this->email->subject(@$subject);
		$this->email->message($send_body);	
		$this->email->set_mailtype("html");
		$this->email->send();
	    }
	}

	function deletebannerimage($id)
	{
		$rows['companybanner']=$this->db->get_where('companybanner',array('id'=>$id))->row();
		$name=$rows['companybanner']->banner;

		if(file_exists('./uploads/logo/'.$name) && !is_dir('./uploads/logo/'.$name))
		{
			unlink('./uploads/logo/'.$name);
		}

		$this->db->update('companybanner',array('isdeleted'=>1) ,array('id'=>$id));
		$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
	    $res['message'] = $message;
		$this->session->set_flashdata('message', $message);
		redirect("company/profile");

	}
    
	
	function deleteadimage($id)
	{
		$rows['image']=$this->db->get_where('AdImage',array('id'=>$id))->row();
		$name=$rows['image']->image;

		if(file_exists('./uploads/ads/'.$name) && !is_dir('./uploads/ads/'.$name))
		{
		unlink('./uploads/ads/'.$name);
		}

		$this->db->delete('AdImage',array('id'=>$id));
		$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close"></button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
	    $res['message'] = $message;
		$this->session->set_flashdata('message', $message);
		redirect("company/ads");

	}
}