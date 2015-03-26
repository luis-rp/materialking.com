<?php
class contractor_profile extends CI_Controller 
{
    function contractor_profile() 
    {
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
     	$contractorid = $this->session->userdata('id');
        if (!$contractorid)
            redirect("admin/login/index");
        $contractor = $this->db->where('id',$contractorid)->get('users')->row();
        $data['contractor']=$contractor;
        $data['states'] = $this->db->get('state')->result();
		$data['contractorimages']=$this->db->get_where('Contractor_Images',array('contractor'=>$contractor->id))->result();
		$data['contractorgallery']=$this->db->get_where('Contractor_Gallery',array('contractor'=>$contractor->id))->result();
		$data['contractorfiles']=$this->db->get_where('Contractor_Files',array('contractor'=>$contractor->id))->result();
		$data['contractorteam']=$this->db->where('contractorid',$contractor->id)->get("Contractor_Team")->result();
		
		$bhrs = $this->db->get_where('Contractor_Business_Hour',array('contractor'=>$contractor->id))->result();
        if($bhrs){
        $businesshrs = array();
        foreach($bhrs as $dbh){

        	$businesshrs[$dbh->day.'start'] = $dbh->start;
        	$businesshrs[$dbh->day.'end'] = $dbh->end;
        	$businesshrs[$dbh->day.'closed'] = $dbh->isclosed;

        }
        $data['businesshrs'] = $businesshrs;

        }
		
        $this->load->view('admin/contractor_view_profile', $data);
      
     } 
     
    function saveprofile() 
     { 
     	$contractorid = $this->session->userdata('id');
        if (!$contractorid)
           redirect("admin/login/index");	
        if (!$_POST)
            die('Wrong Access.');

        $errormessage = '';
        $this->db->where('id !=',$contractorid);
        $this->db->where('isdeleted',0);
        $this->db->where('email', $_POST['email']);
        if ($this->db->get('users')->num_rows > 0) 
        {
            $errormessage = "Email '{$_POST['email']}' already exists.";
        }
        
		$orgpwd="";
		if(isset($_POST['password']) && $_POST['password']!="")
		{
			$orgpwd=$_POST['password'];
			$_POST['password']=md5($_POST['password']);
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
        
        if($this->input->post('address'))
        {
            $geoloc = get_geo_from_address($this->input->post('address'));
            if($geoloc && @$geoloc->lat && @$geoloc->long)
            {
                $_POST['user_lat'] = $geoloc->lat;
                $_POST['user_lng'] = $geoloc->long;              
            }
        }

		
        if (isset($_FILES['logo']['tmp_name']) && $_FILES['logo']['tmp_name']!="")
        {
            if (is_uploaded_file($_FILES['logo']['tmp_name'])) 
            {
                $nfn = $_FILES['logo']['name'];
                $ext = end(explode('.', $nfn));
                if (!in_array(strtolower($ext), array('jpg', 'gif', 'jpeg', 'png')))
                 {
                    $errormessage = '* Invalid file type, upload logo file.';
                 } 
                elseif (move_uploaded_file($_FILES['logo']['tmp_name'], "uploads/logo/" . $nfn)) 
                {
                    $this->_createThumbnail($nfn, 'logo', 270, 200);
                    $_POST['logo'] = $nfn;
                }
            }
        }
  
       if(isset($_FILES['UploadFile']['name']))
        {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/ContractorImages/';
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

					if(isset($filename) && $filename!=''){
            		$this->db->insert('Contractor_Images', array('contractor' => $contractorid, 'image' => $filename));}          		
            	}
           }


      if(isset($_FILES['UploadFile1']['name']) && $_FILES['UploadFile1']['name']!="")
       {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/ContractorFiles/';
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
            		$this->db->insert('Contractor_Files', array('contractor' => $contractorid, 'file' => $filename));
                    }
            	}         		
        }
            
        $file1 = 0;
        if(isset($_POST['checkid']))
        {
            foreach($_POST['checkid'] as $check)
            {
            	$this->db->where('id', $check);
            		if(isset($_POST['file1'][$check]))
            		{
            			$file1 = 1;
            		}
            		else
            		{
            			$file1 = 0;
            		}
            		$this->db->update('Contractor_Files', array('private' => $file1));
            }
        }

	   if(isset($_FILES['UploadFile2']['name']) && $_FILES['UploadFile2']['name']!="")
         {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/ContractorGallery/';
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
            		$this->db->insert('Contractor_Gallery', array('contractor' => $contractorid, 'image' => $filename));}
            	}
         }
       
        if ($errormessage) {
            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close">X</button><div class="msgBox">' . $errormessage . '</div></div></div>');
            redirect('admin/contractor_profile');
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

        		$this->db->where('contractor =', $contractorid);
        		$this->db->where('day', $day);
        		if ($this->db->get('Contractor_Business_Hour')->num_rows > 0) {
        			$this->db->where('contractor =', $contractorid);
        			$this->db->where('day', $day);
        			$this->db->update('Contractor_Business_Hour', array('start' => $start,'end' => $end,'isclosed' => $closed));
        		}else{
        			$this->db->insert('Contractor_Business_Hour', array('contractor' => $contractorid, 'day' => $day, 'start' => $start,'end' => $end,'isclosed' => $closed));
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
        }  
        $this->db->where('id', $contractorid);       
        $this->db->update('users', $_POST);
        $sms="";
        	$link = site_url('admin/dashboard'); 	  	
        	$data['email_body_title']  = "Dear " .@$_POST['companyname'];
		  	$data['email_body_content']  = "You have updated Contractor Company Information as Follow:  <br><br>
		  	Username : ".@$_POST['username']."<br/>
		  	Password : ".@$orgpwd."<br/>
		  	Company Name : ".@$_POST['companyname']."<br/>
		  	Email : ".@$_POST['Email']."<br/>
		  	Full Name : ".@$_POST['fullname']."<br/>
		  	City : ".@$_POST['city']."<br/>
		  	Zip : ".@$_POST['zip']."<br/>
		  	Street : ".@$_POST['street']."<br/>
		  	Phone : ".@$_POST['phone']."<br/>
		  	Fax : ".@$_POST['fax']."<br/>
		    Your Profile URL :  <a href='$link' target='blank'>$link</a>";
		  	$loaderEmail = new My_Loader();
	        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
	        $settings = (array)$this->settings_model->get_current_settings ();
	        $this->load->library('email');
	        $config['charset'] = 'utf-8';
	        $config['mailtype'] = 'html';
	        $this->email->initialize($config);
	        $this->email->from($settings['adminemail'], "Administrator");
	        $this->email->to(@$_POST['companyname'] . ',' . @$_POST['email']);
	        $this->email->subject('Updated Contactor Company Information');
	        $this->email->message($send_body);
	        $this->email->set_mailtype("html");
	        $this->email->send();
	        $sms="Email Sent Successfully.";
         
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X
        </a><div class="msgBox">Your profile has been saved.'.$sms.'</div></div></div>');
       redirect("admin/contractor_profile");
    }
    
     function _createThumbnail($fileName, $foldername = "", $width = 170, $height = 150) 
     {
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
     
    function deletecontractorimage($id)
	 {
	 	$contractorid = $this->session->userdata('id');
        if (!$contractorid)
            redirect("admin/login/index");
		$rows['contractorimages']=$this->db->get_where('Contractor_Images',array('id'=>$id))->row();
		$name=$rows['contractorimages']->image;
		if(file_exists('./uploads/ContractorImages/'.$name) && !is_dir('./uploads/ContractorImages/'.$name))
		{
		unlink('./uploads/ContractorImages/'.$name);
		}

		$this->db->delete('Contractor_Images',array('id'=>$id));
		$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close">X</button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
	    $res['message'] = $message;
		$this->session->set_flashdata('message', $message);
		redirect("admin/contractor_profile");
	 }	
	 
    function deletecontractorgalleryimage($id)
	 {
	 	$contractorid = $this->session->userdata('id');
        if (!$contractorid)
            redirect("admin/login/index");
		$rows['contractorgallery']=$this->db->get_where('Contractor_Gallery',array('id'=>$id))->row();
		$name=$rows['contractorgallery']->image;

		if(file_exists('./uploads/ContractorGallery/'.$name) && !is_dir('./uploads/ContractorGallery/'.$name))
		{
		unlink('./uploads/ContractorGallery/'.$name);
		}

		$this->db->delete('Contractor_Gallery',array('id'=>$id));
		$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close">X</button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
	    $res['message'] = $message;
		$this->session->set_flashdata('message', $message);
		redirect("admin/contractor_profile");

	 }
	 
    function deletecontractfile($id)
	 {
	 	$contractorid = $this->session->userdata('id');
        if (!$contractorid)
           redirect("admin/login/index");
		$rows['contractorfiles']=$this->db->get_where('Contractor_Files',array('id'=>$id))->row();
		$name=$rows['contractorfiles']->file;

		if(file_exists('./uploads/ContractorFiles/'.$name) && !is_dir('./uploads/ContractorFiles/'.$name))
		{
		unlink('./uploads/ContractorFiles/'.$name);
		}
		$this->db->delete('Contractor_Files',array('id'=>$id));
		$message ='<div class="errordiv"><div class="alert alert-success"><button data-dismiss="alert" class="close">X</button><div class="msgBox">Data Deleted Successfully.</div></div></div>';
	    $res['message'] = $message;
		$this->session->set_flashdata('message', $message);
		redirect("admin/contractor_profile");

	 }
   
     function addcontractember(){
    	$id = $this->session->userdata('id');
    	if (!$id)
    		redirect("admin/login/index");
    	$contractor = $this->db->where('id',$id)->get('users')->row();
    	if(!$_POST)
    	    die;
    	 $picture="";  	
    	if (isset($_FILES['memberPicture']['tmp_name']) && $_FILES['memberPicture']['tmp_name']!="")
        {
            if (is_uploaded_file($_FILES['memberPicture']['tmp_name'])) 
            {
                $nfn = $_FILES['memberPicture']['name'];
                $ext = end(explode('.', $nfn));
                if (!in_array(strtolower($ext), array('jpg', 'gif', 'jpeg', 'png')))
                 {
                    $errormessage = '* Invalid file type, upload logo file.';
                 } 
                elseif (move_uploaded_file($_FILES['memberPicture']['tmp_name'], "uploads/ContractorTeam/" . $nfn)) 
                {
                    $picture = $nfn;
                }
            }
        }
        
             $insertarray=array("contractorid"=>$contractor->id,
    	    "name"=>$this->input->post("memberName"),
    	    "email"=>$this->input->post("memberEmail"),
    	    "title"=>$this->input->post("memberTitle"),
    	    "phone"=>$this->input->post("memberPhone"),
    	    "linkedin"=>$this->input->post("memberLinkedin"),
    	    "picture"=>$picture);
    	    
    	    if($_POST!="")
    	    {
    	    	$this->db->insert("Contractor_Team",$insertarray);
    	    }  	    
        redirect("admin/contractor_profile");
    }
    
    function getMemberInfo($id){
    	$cid = $this->session->userdata('id');
    	if (!$cid)
    		 redirect("admin/login/index");

    	$this->db->where("id",$id);
    	$this->db->where("contractorid",$cid);
    	$row = $this->db->get("Contractor_Team")->row();
    	echo json_encode($row);
    }
    
     function editcontractmember()
     {
    	$id = $this->session->userdata('id');
    	if (!$id)
    		redirect("admin/login/index");
    	$contractor = $this->db->where('id',$id)->get('users')->row();

    	 $picture="";  	
    	if (isset($_FILES['memberPicture']['tmp_name']) && $_FILES['memberPicture']['tmp_name']!="")
         {
            if (is_uploaded_file($_FILES['memberPicture']['tmp_name'])) 
            {
                $nfn = $_FILES['memberPicture']['name'];
                $ext = end(explode('.', $nfn));
                if (!in_array(strtolower($ext), array('jpg', 'gif', 'jpeg', 'png')))
                 {
                    $errormessage = '* Invalid file type, upload logo file.';
                 } 
                elseif (move_uploaded_file($_FILES['memberPicture']['tmp_name'], "uploads/ContractorTeam/" . $nfn)) 
                {
                    $picture = $nfn;
                }
            }
         }
        
            $insertarray=array("contractorid"=>$contractor->id,
    	    "name"=>$this->input->post("memberName"),
    	    "email"=>$this->input->post("memberEmail"),
    	    "title"=>$this->input->post("memberTitle"),
    	    "phone"=>$this->input->post("memberPhone"),
    	    "linkedin"=>$this->input->post("memberLinkedin"),
    	    "picture"=>$picture);
    	    
    	    if($_POST!="")
    	    {
    	    	$this->db->where("id",$this->input->post("idMember"));
    	    	$this->db->update("Contractor_Team",$insertarray);
    	    }  	    
	    	redirect("admin/contractor_profile");
       }
       
     function deletecontractmember($id)
	 {
	 	$contractorid = $this->session->userdata('id');
        if (!$contractorid)
            redirect("admin/login/index");
    	$this->db->where('id', $id);
    	$this->db->delete('Contractor_Team');
    	redirect("admin/contractor_profile");
    }

}
?>