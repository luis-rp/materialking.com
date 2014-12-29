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
		$this->load->model('companymodel', '', TRUE);
		if($this->session->userdata('company')) $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
		$company = $this->session->userdata('company');
		 if ($this->session->userdata('company')) {    
            $data['pagetour'] = $this->companymodel->getcompanybyid($this->session->userdata('company')->id); }
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
		
		$this->db->where('CompanyID',$company->id);		
		$formsubmissionresult = $this->db->get('formbuilder')->result();
		$data['formdata'] = $formsubmissionresult;
		
		$data['newrequests'] = array();
		//echo "<pre>"; print_r($reqs); die;
		foreach($reqs as $rq)
		{
			$rq->tago = $this->messagemodel->tago(strtotime($rq->requeston));
			$this->db->where('id',$rq->fromid);
			$rq->from = $this->db->get($rq->fromtype)->row();
			
			$rq->quoteitems=$this->db->get_where('quoteitem',array('purchasingadmin'=>$rq->fromid))->result();
			$rq->awarditems=$this->db->get_where('awarditem',array('purchasingadmin'=>$rq->fromid))->result();
			
			$sql = "SELECT itemid FROM " . $this->db->dbprefix('quoteitem') ." where itemid 
		   NOT IN (SELECT itemid FROM " . $this->db->dbprefix('awarditem')." where purchasingadmin='{$rq->fromid}') AND  purchasingadmin='{$rq->fromid}'GROUP BY itemid"; 			
			$data['quoting']=$this->db->query($sql)->result();
				
			$data['newrequests'][]=$rq;
		} 

		if(isset($_POST['allcompany']))
		{
		$sql = "SELECT u.fullname, u.companyname, u.address, acceptedon, accountnumber, wishtoapply, n.purchasingadmin FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('network')." n
			WHERE u.id=n.purchasingadmin AND n.status='Active' AND n.company=".$company->id." order by n.purchasingadmin desc";
		}
		else
		{
		$sql = "SELECT u.fullname, u.companyname, u.address, acceptedon, accountnumber, wishtoapply, n.purchasingadmin FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('network')." n
			WHERE u.id=n.purchasingadmin AND n.status='Active' AND n.company=".$company->id." order by n.purchasingadmin desc limit 5";
		}


		$query = $this->db->query($sql);

		$data['networkjoinedpurchasers'] = $query->result();
		$invoices = $this->quotemodel->getpendinginvoices($company->id);
		//echo "<pre>",print_r($invoices);	die;
		$data['invoices'] = $invoices;

		$errorLogSql = "SELECT qe.*,q.ponum,ai.award
						FROM ".$this->db->dbprefix('quote_errorlog')." qe
						JOIN ".$this->db->dbprefix('quote')." q ON q.id = qe.quoteid
						JOIN ".$this->db->dbprefix('award')." a ON a.quote = qe.quoteid
						JOIN ".$this->db->dbprefix('awarditem')." ai ON a.id = ai.award
						AND qe.companyid= ai.company
						WHERE qe.companyid=".$company->id;

		$logQry = $this->db->query($errorLogSql);
		$logDetails = $logQry->result_array();
		$tago = '';
		foreach ($logDetails as $key=>$val)
		{
			$tago[] = $this->messagemodel->tago(strtotime($val['created']));
		}
		//log_message("debug",var_export($data['newrequests'],true));
		$data['logDetails'] = $logDetails;
		$data['tago'] = $tago;

		$this->load->view('dashboard/index',$data);
	}

	function creditapplication($pa)
	{
	    $this->db->where('purchasingadmin',$pa);
	    $appl = $this->db->get('application')->row();
	    $data['appl'] = $appl;

	    $sql = "SELECT * FROM ".$this->db->dbprefix('applicationattachment')." WHERE purchasingadmin=".$pa;
	    $qry = $this->db->query($sql);
	    $attachmentData = $qry->result_array();
	    $data['attachmentdata'] = $attachmentData;
	    $this->load->view('dashboard/application',$data);
	}

	function readnotification()
	{
		$this->db->where($_POST);
		$this->db->update('notification',array('isread'=>1));
	}

		function sendemailalert(){

		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');

		$settings = (array)$this->quotemodel->getpurchaseremail($_POST['admin']);

		$totalprice = round( (($_POST['price']*$settings['taxrate']/100) + $_POST['price']),2);

		$duedate = $_POST['datedue'];

		if($duedate >= date('Y-m-d')){
			$datestr =  "due from PO# (".$_POST['ponum'].") on".$duedate;
		}else
			$datestr =  "overdue from PO# (".$_POST['ponum'].") since ".$duedate;

		$data['email_body_title'] = "Dear Administrator ";
		$data['email_body_content'] =	"Your Payment of $ ".$totalprice." against invoice '". $_POST['invoice']."' is ".$datestr." , Please Pay immediately.
			<br/><br/><br/>
			Thanks<br>(".$company->title.")
			<br><br>";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		//echo "<pre>",print_r($_POST); die;
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from( $company->primaryemail, $company->title);
		$this->email->to($settings['adminemail'], "Administrator");
		$this->email->subject('Pending Payment.');
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		if($this->email->send())
		echo "success";
		else
		echo "fail";
		//echo "<pre>",print_r($this->email->send()); die;
	}

	function alertsentdate(){

		$alertdatearray = array();
		$alertdatearray['alertsentdate'] = date('Y-m-d');
		$alertdatearray['id'] = $_POST['invoiceid'];
		$alertdata = $this->quotemodel->setalertdate($alertdatearray);
		if($alertdata==1){
		$return = "Alert Sent ".date('m/d/Y');
		echo $return;
		}else
		echo "*Error in sending Alert";
		die;
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

			$data['email_body_title'] = "Dear " . $company->companyname ;
			$data['email_body_content'] = "Congratulation! ". $supplier->title." has just accepted your request to join in the network.
			<br/>
			Thanks,<br>
			Materialking.com
			<br><br>";
			$loaderEmail = new My_Loader();
			$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
			$this->load->library('email');
			$config['charset'] = 'utf-8';
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->email->from($supplier->primaryemail, $supplier->title);
			$this->email->to($company->companyname . ',' . $company->email);
			$this->email->subject('Request Accepted.');
			$this->email->message($send_body);
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

	function networkdelete($pid)
	{
      $cid=$this->session->userdata('company')->id;
      $this->db->delete('network', array('purchasingadmin' => $pid,'company'=>$cid));
      $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Company Deleted Successfully From Your Network.</div></div>');
      redirect('dashboard');
	}
}
