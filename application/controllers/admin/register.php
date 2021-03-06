<?php
class Register extends CI_Controller 
{
	function Register() {
		parent::__construct ();
		$this->load->dbforge();
		$this->load->model('admin/settings_model');
		$this->load->model('admin/costcode_model');
		$this->load->model('companymodel', '', TRUE);
		$this->load->library ('form_validation');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$data ['title'] = 'Site Register';
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/register', $data);
	}
	
	function index()
	{
		$this->form();
	}
	
	function form() 
	{
		$this->load->view ('admin/register/form');
	}
	
	function saveregister()
	{
		if(!@$_POST)
			die;
		$errormessage = '';
		$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
		if(!@$_POST['companyname']||!@$_POST['email'])
		{
			$errormessage = 'Please Fill up all the fields.';
		}
		elseif(!preg_match($regex, $_POST['email']))
		{
			$errormessage = 'Please Enter Valid Email Address.';
		}
		else 
		{
			$this->db->where('email',$_POST['email']);
			$this->db->where('isdeleted', 0);
			if($this->db->get('users')->num_rows > 0)
			{
				$errormessage = "Email '{$_POST['email']}' already exists.";
			}
		}
		
		if($errormessage)
		{
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$errormessage.'</div></div></div>');
			redirect('admin/register'); 
		}
		
		$this->db->insert('systemusers', array('parent_id'=>''));
		$itemid = $this->db->insert_id();
		$key =  md5(uniqid($_POST['companyname']).'-'.date('YmdHisu'));
		$_POST['regkey'] = $key;
		$_POST['id']= $itemid;
		
		$this->db->insert('users',$_POST);
		
		$this->sendRegistrationEmail($itemid, $key);
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Account Created Successfully.<br/>Please check your email for activation link.</div></div><div class="errordiv">');
		$this->session->set_userdata('companysname', $_POST['companyname']);
		redirect('admin/register'); 
	}
	
	function saveregisterfb()
	{
		$CI = & get_instance();
        $CI->config->load("facebook",TRUE);
        $config = $CI->config->item('facebook');
        $this->load->library('facebook', $config);
		
        $user = $this->facebook->getUser();
        if($user) {
        	try {
        		$user_info = $this->facebook->api('/me');
        		// echo '<pre>'.htmlspecialchars(print_r($user_info, true)).'</pre>';
        		$request = $this->facebook->getSignedRequest();
				//echo "<pre>",print_r($request['registration']); die;
				$request2 = $request['registration'];
				//echo "<pre>",print_r($request2); die;
				//$request2['address'] = $request2['location']['name'];
				
				if($request2['location']['name']!=""){
					
					$addressfields = explode(",",$request2['location']['name']);
					if(isset($addressfields[0]))
					$request2['city'] = trim($addressfields[0]);
					
					if(isset($addressfields[1]))
					$request2['state'] = trim($addressfields[1]);
				}
				
				$request2['fullname'] = $request2['name'];
				unset($request2['location']);
				unset($request2['gender']);
				unset($request2['birthday']);
				unset($request2['name']);
				
        	} catch(FacebookApiException $e) {
        		echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
        		$user = null;
        	}
        }
		if(!@$request2)
			die;
		$errormessage = '';
		$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
		if(!@$request2['companyname']||!@$request2['email'])
		{
			$errormessage = 'Please Fill up all the fields.';
		}
		elseif(!preg_match($regex, $request2['email']))
		{
			$errormessage = 'Please Enter Valid Email Address.';
		}
		else 
		{
			$this->db->where('email',$request2['email']);
			$this->db->where('isdeleted', 0);
			if($this->db->get('users')->num_rows > 0)
			{
				$errormessage = "Email '{$request2['email']}' already exists.";
			}
		}
		
		if($errormessage)
		{
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$errormessage.'</div></div></div>');
			redirect('admin/register'); 
		}
		
		$this->db->insert('systemusers', array('parent_id'=>''));
		$itemid = $this->db->insert_id();
		
		$key =  md5(uniqid($request2['companyname']).'-'.date('YmdHisu'));
		$request2['regkey'] = $key;
		$request2['id'] = $itemid;
		
		$this->db->insert('users',$request2);
		
		$this->sendRegistrationEmail($itemid, $key);
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Account Created Successfully.<br/>Please check your email for activation link.</div></div><div class="errordiv">');
		$this->session->set_userdata('companysname', $request2['companyname']);
		redirect('admin/register'); 
	}
	
	function sendRegistrationEmail($id, $key)
	{
		$this->db->where('id',$id);
		$user = $this->db->get('users')->row();
		
		$link = site_url('admin/register/complete/'.$key);
	    $body = "Dear ".$user->companyname.",<br><br> 
	  	Please click following link to complete your registration:  <br><br>		 
	    <a href='$link' target='blank'>$link</a>";
	    
	    $settings = (array)$this->settings_model->get_setting_by_id (1);
	    $this->load->library('email');
	    $this->load->helper('file');
	    
	    $data['email_body_title'] ="Dear ".$user->companyname;
	    $data['email_body_content'] = "Please click the following activation link to complete your registration.  <br><br>		 
	    <a href='$link' target='blank'>$link</a>";
	    	
	   /* $image =  "/home/materialking/public_html/templates/site/assets/img/logo.png";
	    $data['logoExt'] = get_mime_by_extension($image);
	    $data['logo'] = base64_encode(file_get_contents($image));*/
	    $loaderEmail = new My_Loader();
	    $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
	    
	   	$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
	    	
	    $this->email->initialize($config);
	    
        $this->email->from($settings['adminemail'], "Administrator");
        
        $this->email->to($user->fullname . ',' . $user->email); 
        
        $this->email->subject('Activate your account.');
        $this->email->message($send_body);
  
        $this->email->send();
	}
    
	function testemail(){
	
	
		
		$this->load->library('email');
		$this->load->helper('file');
		
		
	/*	$image =  "/home/luis/git/materialking.com/templates/site/assets/img/logo.png";
		$data['logoExt'] = get_mime_by_extension($image);
		$data['logo'] = base64_encode(file_get_contents($image));*/
		$data['email_body_title'] ="Dear Username";
		$data['email_body_content'] = "Please click the following activation link to complete your registration.  <br><br>
	    <a href='#' target='blank'>Link</a>";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);

		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
			
		$this->email->from("admin@godaddy.com");
	
		$this->email->to("syd.sozo@gmail.com");
	
		$this->email->subject('Activate your account.');
		$this->email->message($send_body);
		$this->email->send();
		
		echo $this->email->print_debugger();
	}
    ////////////////////////
    function resend()
    {
    	$this->load->view('admin/register/resend');
    }

    function sendkeyagain()
    {
    	if (!@$_POST)
    	die('Wrong access');
    	$errormessage = '';
    	if(!@$_POST['email'])
    	{
    		$errormessage = 'Please Provide Email';
    	}
    	else
    	{
    		$this->db->where('email', $_POST['email']);
    		$check = $this->db->get('users');
    		if ($check->num_rows == 0)
    		{
    			$errormessage = "Invalid email";
    		}
    		else
    		{
    			$user = $this->db->get_where('users',array('email'=> $_POST['email']))->row();
    			if(isset($user->regkey))
    			$key = $user->regkey;
    			else
    			$key = "";

    			if(!$key)
    			{
    				$errormessage = "Account already activated.";
    			}
    		}
    	}

    	if ($errormessage) {
    		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button>
    		<div class="msgBox">' . $errormessage . '</div></div></div>');
    		redirect('admin/register/resend');
    	}

    	$link = site_url('admin/register/complete/'.$key);
    	 $data['email_body_title'] = "Dear ".$user->companyname;
	  	 $data['email_body_content'] = "Please click the following activation link to complete your registration.  <br><br>
	    <a href='$link' target='blank'>$link</a>";
	  	 $loaderEmail = new My_Loader();
	  	 $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    	$settings = (array)$this->settings_model->get_setting_by_id (1);
    	$this->load->library('email');
    	$config['charset'] = 'utf-8';
    	$config['mailtype'] = 'html';
    	$this->email->initialize($config);
    	$this->email->from($settings['adminemail'], "Administrator");

    	$this->email->to($user->fullname . ',' . $user->email);

    	$this->email->subject('Activate your account.');
    	$this->email->message($send_body);
    	$this->email->set_mailtype("html");
    	$this->email->send();

    	$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">
    	</a><div class="msgBox">Activation link sent to your email successfully.</div></div></div>');
    	redirect('admin/register/resend');
    }
    //////////////////////////////////

	public function complete($regkey='')
	{
		if(!$regkey)
			redirect ('admin', 'refresh' );
		
		$this->db->where('regkey',$regkey);
		$u = $this->db->get('users')->row();
		
		if(!$u)
			die('Invalid Key');
		
		$data['user'] = $u;
		$this->db->order_by('catname', 'ASC');
        $data['categories'] = $this->db->get('contractcategory')->result();
		$this->load->template ( '../../templates/admin/register', $data);
		$this->load->view('admin/register/complete',$data);
	}
	
	public function savecomplete()
	{
		if(!@$_POST)
			die;
		if(!@$_POST['regkey'])
			die('Wrong access');
		$errormessage = '';    
        $_POST['category'] = $_POST['category'];		
		$regkey = $_POST['regkey'];
		$this->db->where('regkey',$regkey);
		$u = $this->db->get('users')->row();
		
		
		$coreemail = $u->email;
		$coreemailids = $u->id;
		if(!$u)
		{
			$errormessage = 'Invalid Key.';
		}
		//if(!@$_POST['username']||!@$_POST['password']||!@$_POST['repassword']||!@$_POST['city'] || !@$_POST['state'])
		if(!@$_POST['username']||!@$_POST['password']||!@$_POST['repassword'])
		{
			$errormessage = 'Please Fill up all the fields.';
		}
		elseif($_POST['password'] != $_POST['repassword'])
		{
			$errormessage = 'Password and Confirm Password does not match. Please try again.';
		}		
		$this->db->where('username',$_POST['username']);
		if($rowcount=$this->db->get('users')->num_rows > 0)
		{
			if(@$_POST['hiddenuserid']){
				
				$this->db->where('username',$_POST['username']);
				$usersdata = $this->db->get('users')->row();
				if($usersdata->id!=$_POST['hiddenuserid'] || $rowcount>1)
				$errormessage = "Username '{$_POST['username']}' already exists";
			}else
			$errormessage = "Username '{$_POST['username']}' already exists";
		}
		
		if($errormessage)
		{
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$errormessage.'</div></div></div>');
			redirect('admin/register/complete/'.$_POST['regkey']); 
		}
		
		unset($_POST['repassword']);
		unset($_POST['hiddenuserid']);
		$_POST['regkey'] = '';
		
		$rawpassword = $_POST['password'];
		
		$_POST['password'] = md5($_POST['password']);
		$_POST['regdate'] = date('Y-m-d');
		$_POST['usertype_id'] = '2';
		$_POST['purchasingadmin'] = $u->id;
		$_POST['created_date'] = date('Y-m-d H:i:s');
		$_POST['last_logged_date'] = date('Y-m-d H:i:s');
	
		if($_POST['address'])
		{
    		$geocode = file_get_contents(
            "http://maps.google.com/maps/api/geocode/json?address=" . urlencode(str_replace("\n", ", ", $_POST['address'])) . "&sensor=false");
            $output = json_decode($geocode);
            $_POST['street']="";
            $_POST['city']="";
            $_POST['state']="";
            $_POST['zip']="";
            
            if(isset($output->results[0]->address_components[0]->long_name) && $output->results[0]->address_components[0]->long_name)           
            {
            	$_POST['street']=$output->results[0]->address_components[0]->long_name;
            }
            if(isset($output->results[0]->address_components[1]->long_name) && $output->results[0]->address_components[1]->long_name)           
            {
            	$_POST['street'] .=" ".$output->results[0]->address_components[1]->long_name;
            }
            if(isset($output->results[0]->address_components[3]->long_name) && $output->results[0]->address_components[3]->long_name)           
            {
            	$_POST['city'] =$output->results[0]->address_components[3]->long_name;
            }
            if(isset($output->results[0]->address_components[5]->long_name) && $output->results[0]->address_components[5]->long_name)           
            {
            	$_POST['state'] =$output->results[0]->address_components[5]->long_name;
            }
            if(isset($output->results[0]->address_components[7]->long_name) && $output->results[0]->address_components[7]->long_name)           
            {
            	$_POST['zip'] =$output->results[0]->address_components[7]->long_name;
            }
            
            $_POST['user_lat'] = @$output->results[0]->geometry->location->lat;
            $_POST['user_lng'] = @$output->results[0]->geometry->location->lng;
		}
		
		$this->db->where('regkey',$regkey);
		$this->db->update('users',$_POST);
		
		$insert = array();
        $insert['purchasingadmin'] = $u->id;            		
        $insert['title'] = 'Shop-Inventory';
        $insert['description'] = 'Shop-Inventory';
        $insert['address'] = $_POST['address'];
        $insert['startdate'] = date('Y-m-d');
        $insert['creation_date'] = date('Y-m-d');
        $insert['defaultadded'] ='1';
        $this->db->insert('project',$insert);
        $lastid = $this->db->insert_id();
            		           	
        //$DefaultCostcode=$this->db->get('DefaultCostcode')->result();
       
       /* $sqlquery="SELECT * FROM ".$this->db->dbprefix('costcode')." ORDER BY id DESC LIMIT 1";
        $tc=$this->db->query($sqlquery)->row();
        $costid=($tc->id+1);*/
        /*$i=0;
        $costid=0;
        $ll=222;
        if(count($DefaultCostcode) > 0) {
        	foreach ($DefaultCostcode as $DC) { 
        		$ll=$DC->parent-$ll;
        		if($DC->parent!=0){ $D= $ll;} else { $D=$DC->parent;}
        		 $this->db->insert('costcode',array('project'=>$lastid,'purchasingadmin'=>$u->id,'code'=>$DC->code,'cost'=>'500','cdetail'=>$DC->cdetail,'parent'=>$D,'costcode_image'=>$DC->costcode_image,'creation_date'=>date('Y-m-d'),'estimate'=>'1'));
        		 if($i==0){
        		 $lastcostcodeid = $this->db->insert_id();
        		 $newlastcostcodeid=$lastcostcodeid+$ll;
        		 $this->db->where('id',$lastcostcodeid);
        		 $this->db->update('costcode',array('parent'=>$newlastcostcodeid));}
        		 $ll=$lastcostcodeid;
        		 $i++;
        	}
        }*/
        
        /*foreach ($DefaultCostcode as $DC) { 
        		 $this->db->insert('costcode',array('project'=>$lastid,'purchasingadmin'=>$u->id,'code'=>$DC->code,'cost'=>'500','cdetail'=>$DC->cdetail,'parent'=>'0','costcode_image'=>$DC->costcode_image,'creation_date'=>date('Y-m-d'),'estimate'=>'1'));
        	} */
        	
        $parentcombooptions = $this->costcode_model->rearrangedefaultcostcode($lastid, 0, 0);
    	//echo "<pre>",print_r($parentcombooptions); die;    	
    	$parent=array();
    	foreach($parentcombooptions as $rowupper){
    		//echo "<pre>",print_r($rowupper); die;
    		foreach($rowupper as $key=>$row){
    		
    		$this->db->where('id',$row);
    		$DefaultCostcode=$this->db->get('costcode')->row();
    		//echo "<pre>",print_r($DefaultCostcode); die;
    		if($key==0){
    			
    		$this->db->insert('costcode',array('project'=>$lastid,'purchasingadmin'=>$u->id,'code'=>$DefaultCostcode->code,'cost'=>'500','cdetail'=>$DefaultCostcode->cdetail,'parent'=>0,'costcode_image'=>$DefaultCostcode->costcode_image,'creation_date'=>date('Y-m-d'),'estimate'=>'1'));
    		$parent=array();
    		$parent[0] = $this->db->insert_id();
    		}else{    				
    			$this->db->insert('costcode',array('project'=>$lastid,'purchasingadmin'=>$u->id,'code'=>$DefaultCostcode->code,'cost'=>'500','cdetail'=>$DefaultCostcode->cdetail,'parent'=>$parent[$key-1],'costcode_image'=>$DefaultCostcode->costcode_image,'creation_date'=>date('Y-m-d'),'estimate'=>'1'));
    		$parent[$key] = $this->db->insert_id();	
    		}    		    		
    	 }	
    	}
        	
        								
		$settings = (array)$this->settings_model->get_setting_by_id (1);
		$settings['purchasingadmin'] = $u->id;
		$settings['adminemail'] = $u->email;
				
		$data = array(
				'purchasingadmin' => $u->id ,
				'adminemail' => $u->email ,
				'taxrate' => '9.00',
				'pricedays' =>  '120',
				'pricepercent' =>  '2.00',
		);		
		$this->db->insert('settings', $data);
		
		$this->db->where('id',$u->id);
		$row = $this->db->get('users')->row();
		$u = (array)$row;
		$u['logged_in'] = true;
		$u['logintype'] = 'users';
		$this->session->set_userdata($u);
		
		// for store
		$temp['site_loggedin'] = $row;
		$this->session->set_userdata($temp);
		
		@session_start();
		$_SESSION['comet_user_id']=$coreemailids;
		$_SESSION['comet_user_email']=$coreemail;
		$_SESSION['userid']=$coreemailids;
		$_SESSION['logintype']='';
		
		$link = base_url() . 'admin';
		$data['email_body_title'] = "Dear " . $_POST['username'];
		$data['email_body_content'] = "Congratulations! <br><br> Thank You for registering with us, Below are your account details, You can click or copy/paste the link below to access your login screen.
		<br/><br/>
		Your Login Profile is as follows:<br/>
		Login User Name : ". $_POST['username'] ." <br/>
		Login Password :  ". $rawpassword ." <br/>
		Company Name: ". $this->session->userdata('companyname')." <br/>
		Contact Name: ". $_POST['fullname'] ." <br/>
		Email Address: ". $coreemail ." <br/> <br/>
		
		<a href='$link' target='blank'>Login: $link</a>";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$settings = (array) $this->companymodel->getconfigurations(1);
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($settings['adminemail'], "Administrator");
		$this->email->to($_POST['username'] . ',' . $coreemail);
		$this->email->subject('Registration Completed');
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->send();
		
		redirect('admin/dashboard'); 
	}
	
	
	function forgot()
	{
		$this->load->template ( '../../templates/admin/register');
		$this->load->view('admin/register/forgot');
	}
	
	function sendforgot()
	{
		if(!@$_POST)
			die('Wrong access');
		$errormessage = '';
		if(!@$_POST['email'])
		{
			$errormessage = 'Please Provide Email';
		}
		else
		{	
			$this->db->where('email',$_POST['email']);
			$this->db->where('isdeleted', 0);
			$check = $this->db->get('users');
			if($check->num_rows == 0)
			{
				$errormessage = "Invalid email";
			}
		}
		if($errormessage)
		{
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$errormessage.'</div></div></div>');
			redirect('admin/register/forgot'); 
		}
		
		$user = $check->row();		
		
        if($_POST['reqtype'] == 'Get Username')
        {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success">Your username is - '.$user->username.'</div></div>');
            redirect('admin/register/forgot');
        }
		
		$key =  md5(uniqid($user->fullname).'-'.date('YmdHisu'));
		$this->db->where('id',$user->id);
		$this->db->update('users',array('passkey'=>$key));
		
		
		$link = base_url().'admin/register/change/'.$key;
	      $data['email_body_title'] = "Dear ".$user->fullname; 
	  	  $data['email_body_content'] = "Please click the following link to change your password:  <br><br>		 
	    <a href='$link' target='blank'>$link</a>";
	  	  $loaderEmail = new My_Loader();
	    $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
	    $settings = (array)$this->settings_model->get_setting_by_id (1);
	    $this->load->library('email');
	    $config['charset'] = 'utf-8';
	    $config['mailtype'] = 'html';
	    $this->email->initialize($config);
        $this->email->from($settings['adminemail'], "Administrator");
        
        $this->email->to($user->fullname . ',' . $user->email); 
        
        $this->email->subject('Password change link.');
        $this->email->message($send_body);	
        $this->email->set_mailtype("html");
        $this->email->send();
		
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Password change link sent to your email successfully.</div></div></div>');
		redirect('admin/register/forgot'); 
	}
	
	public function change($passkey='')
	{
		if(!$passkey)
			redirect ('admin', 'refresh' );
		
		$this->db->where('passkey',$passkey);
		$user = $this->db->get('users')->row();
		if(!$user)
			die('Invalid Key');
		
		$data['user'] = $user;
		$this->load->template ( '../../templates/admin/register', $data);
		$this->load->view('admin/register/change',$data);
	}
	
	public function savechange()
	{
		if(!@$_POST)
			die;
		if(!@$_POST['passkey'])
			die('Wrong access');
		$errormessage = '';
		
		$passkey = $_POST['passkey'];
		$this->db->where('passkey',$passkey);
		$user = $this->db->get('users')->row();
		if(!$user)
		{
			$errormessage = 'Invalid Key.';
		}
		if(!@$_POST['password']||!@$_POST['repassword'])
		{
			$errormessage = 'Please Fill up all the fields.';
		}
		elseif($_POST['password'] != $_POST['repassword'])
		{
			$errormessage = 'Password and Confirm Password does not match. Please try again.';
		}
		
		if($errormessage)
		{
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$errormessage.'</div></div></div>');
			redirect('admin/register/change/'.$_POST['passkey']); 
		}
		
		unset($_POST['repassword']);
		$_POST['passkey'] = '';
		$_POST['password'] = md5($_POST['password']);
		
		$this->db->where('passkey',$passkey);
		$this->db->update('users',$_POST);
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">Password changed successfully.</div></div></div>');
		redirect('admin/login'); 
	}
	
}

?>
