<?php
class Login extends CI_Controller {

	function Login()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->load->library('session');
		$this->load->library ( 'table' );
		$this->load = new My_Loader();
		$data['title'] = 'Login System';
	    $data['username'] = array('id' => 'username', 'name' => 'username');
	    $data['password'] = array('id' => 'password', 'name' => 'password');
	    $data['AdminTitle']="Web Administrator";
		$this->load->template ( '../../templates/admin/register', $data);
	}

	function index()
	{
		$this->load->helper ( array ('form', 'url' ) );
	    if ($this->session->userdata('logged_in') == TRUE)
	    {
	        redirect('admin/dashboard');
	    }
	    
	    $data['title'] = 'Login System';
	    $data['username'] = array('id' => 'username', 'name' => 'username');
	    $data['password'] = array('id' => 'password', 'name' => 'password');
	    $data['AdminTitle']="Web Administrator";
	    $this->load->view('admin/login', $data);
	}

	function process_login()
	{
	    $username = $this->input->post('username');
	    $password  = $this->input->post('password');
	  
	    if($username == '' OR $password == '') {
            $this->session->set_flashdata('message', '<div class="alert"><div id="alertlogin alert-errorlogin">UserName or Password can not blank.</div></div>');
	       redirect('admin/login/index'); 
        }
        
	 	$this->db->where('username', $this->input->post('username'));
       	$this->db->where('password', md5($this->input->post('password')));
	 	$this->db->where('status', 1);
        
        $query = $this->db->get('users');
        $row = $query->row(); 
         
        if ($query->num_rows == 1)
        {
        	  $data = array(
        	  		'id'=> $row->id,
                   	'username'  => $username,
        	  		'last_logged_date' => $row->last_logged_date,
        	   		'fullname'  => $row->fullname,
        	  		'usertype_id'  => $row->usertype_id,
        	  		'purchasingadmin'  => $row->purchasingadmin,
                    'logged_in'  => TRUE,
        	  		'logintype'=>'users',
        	  		'user_type'=>$row->usertype_id,
        	  		'tour'=>$row->tour
                );
            $data = array_merge($data, (array)$row);
        	$this->session->set_userdata($data);
        	
            $temp['site_loggedin'] = $row;
            $this->session->set_userdata($temp);
             
             // Update the status of users on tbl_users table
            $last_logged_date = date('Y-m-d H:i:s');
			$login = array ('last_logged_date' => $last_logged_date);
			$this->db->where('id', $row->id);
			$this->db->update('users', $login);
		
            $this->id = $this->session->userdata('id');
            $this->fullname = $this->session->userdata('fullname');
            $this->last_logged_date = $this->session->userdata('last_logged_date');
        	$this->usertype_id = $this->session->userdata('usertype_id'); 
        	$this->username = $this->session->userdata('username');
        	
        	redirect('admin/dashboard', $data);
        }
        else
        {
            $this->session->set_flashdata('message', '<div class="alert"><div id="alertlogin alert-errorlogin">Access Denied.</div></div>');
	        redirect('admin/login/index'); 
        }
	}
	
	function logout()
	{
	    $this->session->sess_destroy();
	    redirect('admin/login/index', 'refresh'); 
	}
}
?>