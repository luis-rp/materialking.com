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
        $this->load->model('admin/banner_model', '', TRUE);
		$this->load->model ('storemodel', '', TRUE);
		$data['banner']=$this->banner_model->display();
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
            $temp['site_loggedin']->comet_user_id = $row->id;
            $temp['site_loggedin']->comet_user_email = $row->email;
            $this->session->set_userdata($temp);
			
			/*$this->load->helper('cookie');
        	$this->input->set_cookie("comet_user_id", $row->id,time()+3600);
			$this->input->set_cookie("comet_user_email", $row->email,time()+3600);
			$this->input->set_cookie("userid", $row->id,time()+3600);
			$this->input->set_cookie("logintype", '',time()+3600);*/
			
			@session_start();
			$_SESSION['comet_user_id']=$row->id;
			$_SESSION['comet_user_email']=$row->email;
			$_SESSION['userid']=$row->id;
			$_SESSION['logintype']='';
            
            $this->session->set_userdata($row);
          	$this->session->set_userdata(array("userid"=>$row->id));
            $data_tour = array('usertype_id'=>$row->usertype_id,
            'tour'=>$row->tour);
            $this->session->set_userdata($data_tour);
            
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
		@session_start();
		$_SESSION['comet_user_id']='';
		$_SESSION['comet_user_email']='';
		$_SESSION['userid']='';
		$_SESSION['logintype']='';
		/*$this->load->helper('cookie');
		$this->input->set_cookie("comet_user_id",'',time()+3600);
		$this->input->set_cookie("comet_user_email", '',time()+3600);
		$this->input->set_cookie("userid",'',time()+3600);
		$this->input->set_cookie("logintype", '',time()+3600);*/
		
        redirect('site', 'refresh');
    }

    public function join() {
        if (!$_POST)
            die('Error');

        if(isset($_POST['formfields']))
        {
   	        $formField = $_POST['formfields'];
        	unset($_POST['formfields']);
        }
        $_POST['fromtype'] = 'users';
        $_POST['fromid'] = $this->session->userdata('site_loggedin')->id;
        $this->db->where($_POST);

        if(!$this->db->get('joinrequest')->row())
          {
          	if(@$_POST['creditcardonly'] && $_POST['creditcardonly'] == 'on')
          	{
          		$_POST['creditcardonly'] = 1;
          		
          		$arr = array('purchasingadmin' => $this->session->userdata('site_loggedin')->id, 'company' => $_POST['toid']);
	            $this->db->where($arr);
	            $this->db->delete('purchasingtier');
          		$arr['tier'] = 'tier0';
	            $arr['creditlimit'] = 0;
	            $arr['totalcredit'] = 0;//this is not a mistake, same value is fed to both fields.
	            $arr['creditfrom'] = '';
	            $arr['creditto'] = ''; 
	            $arr['creditonly'] = '1';  	 
	           	           
	            $this->db->insert('purchasingtier', $arr); 
          	}
          	else 
          	{
          		$_POST['creditcardonly'] = 0;
          		
          		
          		$arr = array('purchasingadmin' => $this->session->userdata('site_loggedin')->id, 'company' => $_POST['toid']);
	            $this->db->where($arr);
	            $this->db->delete('purchasingtier');
          		$arr['tier'] = 'tier0';
	            $arr['creditlimit'] = 0;
	            $arr['totalcredit'] = 0;//this is not a mistake, same value is fed to both fields.
	            $arr['creditfrom'] = '';
	            $arr['creditto'] = ''; 
	            $arr['creditonly'] = '1'; 
	            
	             $this->db->insert('purchasingtier', $arr); 
          	}
            $_POST['requeston'] = date('Y-m-d H:i:s');
		
            $this->db->insert('joinrequest', $_POST);
			$joinrequestID = $this->db->insert_id();

			$checkBoxValues = "";
            if(isset($formField) && isset($joinrequestID))
             {
               foreach ($formField as $key => $val)
    		    {
    			  	if($val != '')
    			    {
    			    	if(is_array($val)==1)
	    		    	{
							foreach ($val as $k1=>$newValue)
							{
								$checkBoxValues .= $newValue. " ,";
							}
							$formValue = substr($checkBoxValues, 0, -1);
	    		    	}
	    		    	else
	    		    	{
	    		    		$formValue = $val;
	    		    	}
					    $savedata = array('joinrequestid'=>$joinrequestID,'formfieldid'=>$key,'value' =>$formValue);
					    $this->db->insert('joinrequestform', $savedata);
    			    }
			     }
             }

            $supplier = $this->db->where('id',$_POST['toid'])->get('company')->row();
            $company = $this->db->where('id',$_POST['fromid'])->get('users')->row();

            $sql = "SELECT fb.*,jrf.Value as formValue FROM ".$this->db->dbprefix('formbuilder')." fb LEFT JOIN ".$this->db->dbprefix('joinrequestform') ." jrf ON jrf.formfieldid = fb.Id
            		WHERE jrf.joinrequestid=".$joinrequestID;

            $qry = $this->db->query($sql);
            $formresult = $qry->result_array();

            $body1= "";
            if(isset($formresult) && count($formresult)>0)
            {
				foreach ($formresult as $k=>$value)
				{
					$body1 .= "<br/>".$value['Label']." : ".$value['formValue']." <br/>";
				}
            }

            if($_POST['accountnumber']=="")
            	$_POST['accountnumber'] = "none";

            if(isset($_POST['wishtoapply']) && $_POST['wishtoapply'] ==1)
            	$wish = "Yes";
            else
            	$wish = "No";
            
            if(isset($_POST['creditcardonly']) && $_POST['creditcardonly'] == 1)
            	$creditcardonly = "Yes";
            else
            	$creditcardonly = "No";
            			
            if(isset($_POST['message']) && $_POST['message'] !="")
            	$msg = $_POST['message'];
            else
            	$msg = "none";

           $data['email_body_title']  = "Dear " . $supplier->title ;
           $data['email_body_content'] = $company->companyname." just sent you a request to join in your Newtork.
            The following information was sent:
            Account Number: ".$_POST['accountnumber']."
            <br/>
            Application Sent: ".$wish."
            <br/>
            Credit Card Only Account : ".$creditcardonly."
            <br/>
            Message: ".$_POST['message']."
            <br/>
            ".$body1."
            You can login on your dasboard and accept or deny request.
            <br><br>";
          
           $loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            	
            $this->email->initialize($config);
            $this->email->from($company->email, $company->companyname);
            $this->email->to($supplier->title . ',' . $supplier->primaryemail);
            $this->email->subject('Request to Join Network.');
            $this->email->message($send_body);
            $this->email->set_mailtype("html");
            $this->email->send();

        }
        die('Success');
    }

}
