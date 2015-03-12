<?php
class Admin extends CI_Controller {
	private $limit = 10;

	private $adminuser;
	private $userarrays;

	function Admin()
	{
		parent::__construct();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login', 'refresh');
		}
		$this->load->library ( array ('table', 'validation') );
		$this->load->helper ('url');
		$this->load->model ('admin/adminmodel', '', TRUE);
		$this->load->model('admin/quote_model');
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
        $this->load->model('admin/company_model');
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$data ['title'] = "Site Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}

	function index($offset = 0)
	{
		if($this->session->userdata('usertype_id')==3)
		{
			redirect('admin/dashboard', 'refresh');
		}
		$uri_segment = 4;
		$offset = $this->uri->segment ( $uri_segment );
		$adminusers = $this->adminmodel->get_paged_list ($this->limit, $offset);

		$this->load->library ( 'pagination' );
		$config ['base_url'] = site_url ( 'admin/admin/index/' );
		$config ['total_rows'] = $this->adminmodel->count_all ();
		$config ['per_page'] = $this->limit;
		$config ['uri_segment'] = $uri_segment;
		$this->pagination->initialize ( $config );
		$data ['pagination'] = $this->pagination->create_links ();

		$this->load->library ( 'table' );
		$this->table->set_empty ( "&nbsp;" );
		$this->table->set_heading ( 'ID', 'Full Name', 'Login Type','Position', 'User Name','Email','Company Name', 'Created Date', 'Last Logged', 'Status', 'Actions' );
		$i = 0 + $offset;
		if(isset($adminusers)) {
		foreach ($adminusers as $adminuser)
		{
				$this->table->add_row ( ++ $i,
			    $adminuser->fullname,
			    $adminuser->userType,
			    $adminuser->position,
			    $adminuser->username,
			    $adminuser->email,
			    $adminuser->companyname,
				$adminuser->created_date?date("m/d/Y h:i A", strtotime($adminuser->created_date)):'',
				$adminuser->last_logged_date?date("m/d/Y h:i A", strtotime( $adminuser->last_logged_date)):'',
			    $adminuser->status == '1'
			    ?
			    'Active&nbsp;&nbsp;'
			    .anchor ('admin/admin/deactivate/' . $adminuser->id,'<span class="icon-2x icon-remove-sign"></span>',array ('class' => 'disapprove' ) )
			    :
			    'Inactive&nbsp;&nbsp;'
			    .anchor ( 'admin/admin/activate/' . $adminuser->id, '<span class="icon-2x icon-ok"></span>', array ('class' => 'approve' ) )
			    ,
			    anchor ('admin/admin/update/' . $adminuser->id, '<span class="icon-2x icon-edit"></span>', array ('class' => 'update' ) )
			    . ' ' .
			    anchor ('admin/admin/changepwd/' . $adminuser->id, '<span class="icon-2x icon-key"></span>', array ('class' => 'update' ) )
			    . ' ' .
			    anchor ('admin/admin/delete/' . $adminuser->id, '<span class="icon-2x icon-trash"></span>', array ('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')" ) ) );
		}
		}
		$data ['addlink'] = '<a class="btn btn-green" href="add">Add New User</a>';
		$data ['heading'] = 'User Overview';
		$data ['table'] = $this->table->generate ();

		$uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}

		$this->load->view ( 'admin/userlist', $data );
	}

	function activate($id)
	{
		if($this->session->userdata('usertype_id')==3)
		{
			redirect('admin/dashboard', 'refresh');
		}
		$this->adminmodel->activate ($id);
		redirect ('admin/admin', 'refresh');
	}

	function deactivate($id)
	{
		if($this->session->userdata('usertype_id')==3)
		{
			redirect('admin/dashboard', 'refresh');
		}
		$this->adminmodel->deactivate ($id);
		redirect ('admin/admin', 'refresh');
	}

	function add()
	{
		if($this->session->userdata('usertype_id')==3)
		{
			redirect('admin/dashboard', 'refresh');
		}
		$this->_set_fields ();

		$data ['title'] = 'Add New Employee Users';
		$data ['message'] = '';
		$data ['action'] = site_url ( 'admin/admin/addAdminuser' );
		$data ['link_back'] = anchor ( 'admin/admin/index/', 'Back To List', array ('class' => 'back' ) );
		$data ['userarrays'] = $this->adminmodel->getUserType();
		$this->db->order_by('catname', 'ASC');
		$parent=0;
        //$data['categories'] = $this->db->get_where('category',array('parent_id'=>$parent))->result();
        $data['categories'] = $this->db->get('contractcategory')->result();
        $data['adminusers'] = $this->adminmodel->get_paged_list();
		$this->load->view ( 'admin/adminEdit', $data );
	}

	function addAdminuser()
	{
		
		if($this->session->userdata('usertype_id')==3)
		{
			redirect('admin/dashboard', 'refresh');
		}
		$data ['message'] = '';
		$data ['userarrays'] = $this->adminmodel->getUserType ();
		$data ['title'] = 'Add New Admin User';
		$data ['action'] = site_url ('admin/admin/addAdminuser' );
		$data ['link_back'] = anchor ('admin/admin/index/', 'Back To List', array ('class' => 'back' ) );
		$this->_set_fields ();
		$this->_set_rules ();

		if ($this->validation->run () == FALSE) {
			$data ['message'] = '';
			$this->load->view ( 'admin/adminEdit', $data );
		} else {
			$extName = $this->adminmodel->getAdminuserName ( $this->input->post ('username') );

			if ($extName == $this->input->post ('username')) {
				$data ['message'] = '<div class="already">Username Already Exists.</div>';
				$this->load->view ('admin/adminEdit', $data);
			} else {
							
				$created_date = date ( "Y-m-d h:i:s" );
                //$geoloc = $this->company_model->getLatLong( $this->input->post ( 'address' ));

				$id = $this->adminmodel->save();
				$this->validation->id = $id;
			//	if($this->session->userdata('usertype_id')==2)			{
					$settings = (array)$this->settings_model->get_current_settings ();
				    $this->load->library('email');
				    $config['charset'] = 'utf-8';
				    $config['mailtype'] = 'html';
				    $this->email->initialize($config);
					//$this->email->clear(true);
			        $this->email->from($settings['adminemail'], "Administrator");
			        $this->email->to($_POST['email']);
			        $link = '<a href="'.site_url('admin').'">Login</a>';
			        $data['email_body_title'] = "Dear ".$_POST['fullname'];
/*$data['email_body_content'] = "Your account is created with following details <br/><br/>*/
$data['email_body_content'] = "(".$this->session->userdata('fullname').") Has created an account for you at materialking.com, you now have to access your account using your username and password below. <br/><br/>
Username: {$_POST['username']}<br/><br/>
Password: {$_POST['password']}<br/><br/>
You can login from:<br/><br/>
$link";

$loaderEmail = new My_Loader();
			$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		    $this->email->subject("EZPZ-P Account Created");
	        $this->email->message($send_body);
	        $this->email->set_mailtype("html");
	        //echo "<pre>"; print_r($this->email); die;
	        $this->email->send();
				//}
				$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">User Added Successfully</div></div>');
				redirect ( 'admin/admin/index/', 'refresh' );
			}
		}
	}

	function profile()
	{
	    $this->update($this->session->userdata('id'));
	}

	function update($id)
	{
		if($this->session->userdata('usertype_id')==3 && $this->session->userdata('id') != $id)
		{
			redirect('admin/dashboard', 'refresh');
		}
		$this->_set_fields ();
		$data ['userarrays'] = $this->adminmodel->getUserType ();
		//$data ['plantypes'] = $this->adminmodel->getplantypes ();

		$adminuser = $this->adminmodel->get_by_id ( $id )->row ();
		if($this->session->userdata('usertype_id')==2 && $adminuser->purchasingadmin != $this->session->userdata('id'))
		{
			redirect('admin/dashboard', 'refresh');
		}
		//print_r($adminuser);die;
		$columns = (array)$this->adminmodel->getfields();

		foreach($columns as $column)
		{
			$column = (array)$column;
			$this->validation->$column['Field'] = $adminuser->$column['Field'];
		}
		$_POST ['status'] = $this->validation->status = $adminuser->status;

		$data ['title'] = 'Update Admin User';
		$data ['message'] = '';
		$data ['action'] = site_url ('admin/admin/updateAdminuser' );
		//$this->db->select('category');
		//$cat = $this->db->get_where('users',array('id'=>$id));
		$this->db->order_by('catname', 'ASC');
		$parent=0;
        //$data['categories'] = $this->db->get_where('category',array('parent_id'=>$parent))->result();
        $data['categories'] = $this->db->get('contractcategory')->result();
		$data ['link_back'] = anchor ('admin/admin/index/', 'Back to list of User List', array ('class' => 'back' ) );	        
		$this->load->view ('admin/adminEdit', $data );
	}


	function updateAdminuser()
	{
		if($this->session->userdata('usertype_id')==3 && $this->session->userdata('id') != $this->input->post ( 'id' ))
		{
			redirect('admin/dashboard', 'refresh');
		}

		$adminuser = $this->adminmodel->get_by_id ( $this->input->post ( 'id' ) )->row ();
		
		if($this->session->userdata('usertype_id')==2 && $adminuser->purchasingadmin != $this->session->userdata('id'))
		{
			redirect('admin/dashboard', 'refresh');
		}
		$data ['userarrays'] = $this->adminmodel->getUserType ();
		$data ['title'] = 'Update Admin User Record';
		$data ['action'] = site_url ( 'admin/admin/updateAdminuser' );
		$data ['link_back'] = anchor ( 'admin/admin/index/', 'Back To List', array ('class' => 'back' ) );

		$this->_set_fields ();
		$this->_set_rules ();

		if ($this->validation->run () == FALSE) {
			$data ['message'] = '';
			$this->load->view ( 'admin/adminEdit', $data );

		} else {
			$created_date = date ( "Y-m-d h:i:s" );
            //$geoloc = $this->company_model->getLatLong( $this->input->post ( 'address' ));
			//$adminuser = array ('username' => $this->input->post ( 'username' ), 'usertype_id' => $this->input->post ( 'usertype_id' ), 'fullname' => $this->input->post ( 'fullname' ), 'email'=> $this->input->post('email'),'status' => $this->input->post ( 'status' ), 'created_date' => $created_date,'address'=> $this->input->post ( 'address' ),'user_lat'=>$geoloc['lat'],'user_lng'=>$geoloc['lng']);

			//$this->validation->id = $this->input->post('id');
						
			$this->adminmodel->update ();
			
			$settings = (array)$this->settings_model->get_current_settings ();
			$this->load->library('email');
			$config['charset'] = 'utf-8';
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->email->from($settings['adminemail'], "Administrator");
			$this->email->to($_POST['email']);
		    $link = '<a href="'.site_url('admin').'">Login</a>';
			$data['email_body_title'] = "Dear ".$_POST['fullname'];
			$data['email_body_content'] = "(".$this->session->userdata('fullname').") Has Updated an account for you at materialking.com, you now have to access your 
			account using your below information. <br/>
			Username: {$_POST['username']}<br/>
			position : {$_POST['position']}<br/>
			fullname : {$_POST['fullname']}<br/>
			email : {$_POST['email']}<br/>
			You can login from:<br/><br/>
			$link";
	
			$loaderEmail = new My_Loader();
			$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
			$this->email->subject("EZPZ-P Account Updated");
		    $this->email->message($send_body);
		    $this->email->set_mailtype("html");
		    
		    $this->email->send();
			
			$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">User Updated Successfully</div></div>');
			redirect('admin/admin/update/'.$this->input->post ( 'id' ));
			//redirect ( 'admin/admin/index/', 'refresh' );
		}
	}

	function changepwd($id='')
	{
		$this->_set_fields ();
		$data['id'] = $id;
		if(!$id)
			$id = $this->session->userdata ( 'id' );

		if($this->session->userdata('usertype_id')==3 && $this->session->userdata('id') != $id)
		{
			redirect('admin/dashboard', 'refresh');
		}
		$adminuser = $this->adminmodel->get_by_id ( $id )->row ();
		$this->validation->id = $id;
		$this->validation->username = $adminuser->username;
		$this->validation->fullname = $adminuser->fullname;
		$this->validation->position = $adminuser->position;
		$this->validation->password = $adminuser->password;
		$this->validation->newpassword = '';
		$this->validation->rnewpassword = '';
		//print_r($this->validation);die;
		$data ['action'] = site_url ('admin/admin/editownProfile' );
		$data ['message'] = '';
		$data ['heading'] = "Change Password - ".$adminuser->fullname." - ".$adminuser->username;
		$permissiondata=$this->db->get_where('quoteuser',array('userid'=>$id))->result();
		if(count($permissiondata) > 0)
		{
			$data['permissions']=array();		
			foreach ($permissiondata as $permission)
			{
				$quote=$this->db->get_where('quote',array('id'=>$permission->quote))->row();
				$project=$this->db->get_where('project',array('id'=>$quote->pid))->row();			
				$permission->quotename = $quote->ponum;
				$permission->projectname = $project->title;
				$data['permissions'][]=$permission;
			}		
		}
		$this->load->view ('admin/editProfile', $data );
	}

	function editownProfile()
	{
		$id = $_POST['id'];
		if(!$id)
			$id = $this->session->userdata ( 'id' );

		if($this->session->userdata('usertype_id')==3 && $this->session->userdata('id') != $id)
		{
			redirect('admin/dashboard', 'refresh');
		}
		$adminuser = $this->adminmodel->get_by_id ( $id )->row ();
		if($this->session->userdata('usertype_id')==2 && $adminuser->purchasingadmin != $this->session->userdata('id'))
		{
			redirect('admin/dashboard', 'refresh');
		}
		$data ['heading'] = 'Update Your Profile';
		$data ['action'] = site_url ('admin/admin/editownProfile');
		$this->validation->password = md5($adminuser->password);

		$this->load->library ('form_validation');

		$config_rules = array(
						'newpassword'	=> 'required',
						'rnewpassword'	=> 'required'
			);

		$this->validation->set_rules($config_rules);
		$adminuser = array ('password' => md5($this->input->post ( 'newpassword' )) );
		$this->_set_fields ();

		if ($this->input->post('newpassword') == "")
		{
			$data ['action'] = site_url ('admin/admin/editownProfile' );
			$data ['message'] = '<div class="already">New Password Could Not Blank.</div>';
			$this->load->view ('admin/editProfile', $data);

		}
		else if ($this->input->post('rnewpassword') == "")
		{
			$data ['action'] = site_url ('admin/admin/editownProfile' );
			$data ['message'] = '<div class="already">Confirm Password Can Not Blank.</div>';
			$this->load->view ('admin/editProfile', $data);

		}
		else if($this->input->post('newpassword') != $this->input->post('rnewpassword'))
		{
			$data ['action'] = site_url ('admin/admin/editownProfile');
			$data ['message'] = '<div class="already">Password Does Not Match.</div>';
			$this->load->view ('admin/editProfile', $data);
		}
		else
		{
			$data ['message'] = "";
			$this->db->where ('id', $id );
			$this->db->update ('users', $adminuser);
			$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Password has been changed Successfully</div></div>');
			if($this->session->userdata('usertype_id')<3)
				redirect ( 'admin/admin/index/', 'refresh');
			else
				redirect ('admin/admin/profile');
		}
	}

	function delete($id)
	{

		if($this->session->userdata('usertype_id')==3)
		{
			redirect('admin/dashboard', 'refresh');
		}
		$this->adminmodel->delete($id);
		$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">User Deleted.</div></div>');
		redirect ( 'admin/admin/index/', 'refresh');
	}

	function _set_fields()
	{
		$columns = (array)$this->adminmodel->getfields();
		$fields = array();
		foreach($columns as $column)
		{
			$column = (array)$column;
			$fields [$column['Field']] = $column['Field'];
		}
		$fileds['labels'] = 'Lables';
		$fileds['newpassword'] = 'newpassword';
		$fileds['rnewpassword'] = 'rnewpassword';
		//$fileds['category'] = 'category';
		$this->validation->set_fields ($fields);
	}

	function _set_rules()
	{
		$rules ['username'] = 'trim|required';
		$rules ['email'] = 'trim|required';
		$rules ['status'] = 'trim|required';
		$rules ['fullname'] = 'trim|required';
		$rules ['position'] = 'trim';


		$this->validation->set_rules ( $rules );

		$this->validation->set_message ( 'required', '* required' );
		$this->validation->set_message ( 'isset', '* required' );
		$this->validation->set_error_delimiters ( '<div class="error">', '</div>' );
	}

	function finish_user_tour(){
			$id = 	$this->session->userdata ( 'id' );
			$this->db->where('id',$id);
			$this->db->update('users',array('tour'=>'finished'));
			$this->db->where('purchasingadmin',$id);
			$this->db->update('settings',array('pagetour'=>'0'));
			$this->session->set_userdata('tour','finished');


	}
	function restart_tour(){
		$id = 	$this->session->userdata ( 'id' );
		$this->db->where('id',$id);
		$this->db->update('users',array('tour'=>'unfinished'));
		$this->session->set_userdata('tour','unfinished');
		redirect("admin");
	}
	
	function set_bank_purchaser()
	{
		$id = 	$this->session->userdata ( 'id' );
		$bankaccount = $this->db->where('purchasingadmin',$id)->get('purchaserbank')->row();
        if(!$bankaccount)
        {
            $this->db->insert('purchaserbank',array('purchasingadmin'=>$id));
            $bankaccount = $this->db->where('purchasingadmin',$id)->get('purchaserbank')->row();
        }
        $data['bankaccount'] = $bankaccount;
        $this->load->view('admin/purchaserbankaccount',$data);
       
		
	}
	
	 function savebankaccount()
    {   	
        $id = $this->session->userdata ( 'id' );
         if (!$id)
            redirect('admin/login');
          
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
            
        
        //$this->db->where('purchasingadmin',$id)->update('purchaserbank',$_POST);
         $this->db->where('purchasingadmin',$id)->update('purchaserbank',array('bankname'=>$_POST['bankname'],'accountnumber'=>$accountnumber,'routingnumber'=>$routingnumber));
        $message = 'Bank Account settings updated.';
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close">X</button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('admin/admin/set_bank_purchaser');
    }
    
    
    public function checkpwd()
	{
		$id = $this->session->userdata ( 'id' );
         if (!$id)
            redirect('admin/login');
		if(!@$_POST['pwd'])
		{
			die;
		}
		
        $_POST['pwd'] = md5($_POST['pwd']);
        $check = $this->db->get_where('users',array('id'=>$id))->row();
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

	
	
	
	
}
?>