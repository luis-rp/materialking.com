<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller 
{
	public function Dashboard()
	{
		ini_set("memory_limit","512M");
		ini_set("max_execution_time", 700);
	    parent::__construct ();
		$data ['title'] = 'Dashboard';
		$this->load->dbforge();
		$this->load->model ('quotemodel', '', TRUE);
		$this->load->model ('messagemodel', '', TRUE);
		if($this->session->userdata('company')) $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
		$company = $this->session->userdata('company');
		//print_r($company);
		if(!$company)
			redirect('company/login');
			
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/front/template', $data);
	}

	public function index()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
		$this->db->where('totype','company');
		$this->db->where('toid',$company->id);
		$this->db->where('status','Pending');
		$reqs = $this->db->get('joinrequest')->result();
		
		$data['newrequests'] = array();
		foreach($reqs as $rq)
		{
			$rq->tago = $this->messagemodel->tago(strtotime($rq->requeston));
			$this->db->where('id',$rq->fromid);
			$rq->from = $this->db->get($rq->fromtype)->row();
			$data['newrequests'][]=$rq;
		}
		
		$sql = "SELECT u.fullname, u.companyname, u.address, acceptedon, accountnumber, wishtoapply, n.purchasingadmin FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('network')." n
			WHERE u.id=n.purchasingadmin AND n.status='Active' AND n.company=".$company->id;
		$query = $this->db->query($sql);
		$data['networkjoinedpurchasers'] = $query->result();
		
		$this->load->view('dashboard/index',$data);
	}
	
	function creditapplication($pa)
	{
	    $this->db->where('purchasingadmin',$pa);
	    $appl = $this->db->get('application')->row();
	    $data['appl'] = $appl;
	    $this->load->view('dashboard/application',$data);
	}
	
	function readnotification()
	{
		$this->db->where($_POST);
		$this->db->update('notification',array('isread'=>1));
	}
	
	function close($id)
	{
		$company = $this->session->userdata('company');
		$this->db->where('id',$id);
		$this->db->where('company',$company->id);
		$this->db->update('notification',array('isread'=>1));
		redirect('dashboard');
	}
	
	function acceptreq($id)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$this->db->where('id',$id);
		$this->db->where('totype','company');
		$this->db->where('toid',$company->id);
		$this->db->where('status','Pending');
		$row = $this->db->get('joinrequest')->row();
		if($row)
		{
			$this->db->where('id',$id);
			$this->db->where('totype','company');
			$this->db->where('toid',$company->id);
			$this->db->where('status','Pending');
			$this->db->update('joinrequest',array('status'=>'Accepted'));
			
			$insert = array();
			$insert['company'] = $company->id;
			$insert['purchasingadmin'] = $row->fromid;
			$insert['request'] = $row->id;
			$insert['accountnumber'] = $row->accountnumber;
			$insert['wishtoapply'] = $row->wishtoapply;
			$insert['acceptedon'] = date('Y-m-d H:i:s');
			$insert['status'] = 'Active';
			//print_r($insert);die;
			$this->db->insert('network',$insert);
			
			$supplier = $this->db->where('id', $company->id)->get('company')->row();
			$company = $this->db->where('id',$row->fromid)->get('users')->row();
			
			$body = "Dear " . $company->companyname . ",<br><br>
			Congratulation! ". $supplier->title." has just accept your request to join in the network.
			<br/>
			Thanks
			<br><br>";
			
			$this->load->library('email');
			$this->email->from($supplier->primaryemail, $supplier->title);
			$this->email->to($company->companyname . ',' . $company->email);
			$this->email->subject('Request Accepted.');
			$this->email->message($body);
			$this->email->set_mailtype("html");
			$this->email->send();
			
		}
		redirect('dashboard');
	}
	
	function rejectreq($id)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$this->db->where('id',$id);
		$this->db->where('totype','company');
		$this->db->where('toid',$company->id);
		$this->db->where('status','Pending');
		$row = $this->db->get('joinrequest');
		if($row)
		{
			$this->db->where('id',$id);
			$this->db->where('totype','company');
			$this->db->where('toid',$company->id);
			$this->db->where('status','Pending');
			$this->db->update('joinrequest',array('status'=>'Rejected'));
		}
		redirect('dashboard');
	}
}
