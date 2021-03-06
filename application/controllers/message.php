<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Message extends CI_Controller 
{
	public function Message()
	{
	    parent::__construct ();
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$data ['title'] = 'Dashboard';
		$this->load->dbforge();
		$this->load->model ('homemodel', '', TRUE);
		$this->load->model ('messagemodel', '', TRUE);
		$this->load->model ('quotemodel', '', TRUE);
		$this->load->model ('companymodel', '', TRUE);
		if($this->session->userdata('company')) $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
		 if ($this->session->userdata('company')) {    
            $data['pagetour'] = $this->companymodel->getcompanybyid($this->session->userdata('company')->id); }
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/front/template', $data);
	}
	
	public function index($quote='')
	{
		$this->messages($quote);
	}
	
	
	function messages($quote='')
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$quotewhere = '';
		
		
		if($quote)
		{
			$sql = "SELECT m.quote FROM ".$this->db->dbprefix('message')." m WHERE m.messagekey='".$quote."'";
			$quoteid = $this->db->query($sql)->result(); 
			if($quoteid)
			$quotewhere = " AND m.quote='".$quoteid[0]->quote."'";
		}
		$pafilter = '';		
		if(@$_POST['searchpurchasingadmin'])
			$pafilter = " AND m.purchasingadmin='".$_POST['searchpurchasingadmin']."'";
		$messagesql = "SELECT m.*,q.id quoteid, q.ponum, u.email adminemail, c.email companyemail, b.complete FROM 
		".$this->db->dbprefix('message')." m, ".$this->db->dbprefix('quote')." q, ".$this->db->dbprefix('bid')." b, 
		".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('company')." c
		WHERE m.quote=q.id AND q.id = b.quote AND m.company=b.company AND m.company=c.id AND m.adminid=u.id AND 
		m.company='{$company->id}' $quotewhere $pafilter ORDER BY m.id DESC";
		
		$msgs = $this->db->query($messagesql)->result();
		$messages = array();
		foreach($msgs as $msg)
		{
			if(strpos($msg->from, '(Admin)') > 0)
				$msg->showemail = $msg->adminemail;
			else
				$msg->showemail = $msg->companyemail;
			$msg->showago = $this->tago(strtotime($msg->senton));
			$datetime = strtotime($msg->senton);
			$msg->showdate = date("M d, Y H:i A", $datetime);
			$messages[$msg->ponum]['messages'][]=$msg;
			$messages[$msg->ponum]['quote']['id']=$msg->quoteid;
			$messages[$msg->ponum]['quote']['ponum']=$msg->ponum;
			$messages[$msg->ponum]['quote']['messagekey']=$msg->messagekey;
			//$messages[$msg->ponum]['quote']['complete']=$msg->complete;	
			$messages[$msg->ponum]['quote']['complete']= "No";
			$this->db->where('quote',$msg->quoteid);
			$award = $this->db->get('award')->row();		
			$finalstatus = 0;	
			
			if($award)
			{
				$messages[$msg->ponum]['quote']['status'] = 'Awarded';
				$this->db->where('award',$award->id);
				$this->db->where('company',$company->id);
				$awarditems = $this->db->get('awarditem')->num_rows;
				$messages[$msg->ponum]['quote']['awarditems'] = $awarditems;
				/*--------------------------------------------------------------------------*/
					$awardeditems = $this->quotemodel->getawardeditems($award->id,$company->id);
					$complete = true;
					$noitemsgiven = true;
					$allawarded = true;
					$awarditemcount = count($awardeditems);
					foreach($awardeditems as $ai)
					{				
						if($ai->received < $ai->quantity)
							$complete = false;
						if($ai->company != $company->id)
							$allawarded = false;
						if($ai->received > 0)
							$noitemsgiven = false;
						$data['myawarditems'][] = $ai;
						$progress="";
						if(!$noitemsgiven)
						{
							
							if($complete)
							{
								$progress=100;
							}
							else
							{
								$progress=80;
							}
						}
						else
						{
							$progress=60;
						}
						$finalstatus+=$progress;
						
				 }
				if(($finalstatus/$awarditemcount) == 100)
					$messages[$msg->ponum]['quote']['complete']= 'Yes'; 	
			   /*--------------------------------------------------------------------------*/								
			}
			else
			{
				$messages[$msg->ponum]['quote']['status'] = 'Pending';
				$this->db->where('quote',$msg->quoteid);
				$inv = $this->db->get('invitation')->row();
				if($inv)
					$messages[$msg->ponum]['quote']['invitation'] = $inv->invitation;
				else
					$messages[$msg->ponum]['quote']['invitation'] = '';
			}			
		}
		$this->db->select($this->db->dbprefix('users.').'*');
		$this->db->where('usertype_id',2);
		$this->db->from('users')->join('network',"users.id=network.purchasingadmin")->where('network.company',$company->id);
		$data['purchasingadmins'] = $this->db->get()->result();
		$data['company'] = $company;
		$data['messages'] = $messages;
		
		$data['quote'] = $quote;
		
		
		if($quote)
			$this->load->view('message/list',$data);
		else
			$this->load->view('message/singlelist',$data);
	}

	function archivemessage($qid){

		$company = $this->session->userdata('company');
		if(!$company)
		redirect('company/login');

		$messagesql = "INSERT INTO ".$this->db->dbprefix('message_archive')." select * from ".$this->db->dbprefix('message')." WHERE quote='{$qid}'";
		$returnval = $this->db->query($messagesql);
		if($returnval) {
			$messagesql2 = "DELETE FROM ".$this->db->dbprefix('message')." WHERE quote='{$qid}'";
			$returnval2 = $this->db->query($messagesql2);
		}

		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">Message Archived Successfully</div></div></div>');
		redirect('message');
	}
	
	
	function viewmessage($id)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
		$messagesql = "SELECT * FROM ".$this->db->dbprefix('message')." WHERE id='{$id}'";
		$message = $this->db->query($messagesql)->row();
		if($message->company != $company->id)
		{
			$message = 'Invalid Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('message');
			die;
		}
		$data['company'] = $company;
		$data['message'] = $message;
		$this->load->view('message/message',$data);
	}
	
	function sendmessage($quote)
	{
		if(!$_POST)
			die;
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
	
		if($_POST['company'] != $company->id)
		{
			$message = 'Invalid Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('message');
			die;
		}
		
		$quote = $this->quotemodel->getquotebyid($quote);
		$ponum = $quote->ponum;
		$c = $this->companymodel->getcompanybyid ($_POST['company']);
		
		$key = md5($c->id.'-'.$quote->id.'-'.date('YmdHisu'));
		$_POST['messagekey'] = $key;
		$_POST['senton'] = date('Y-m-d H:i');
		$this->db->insert('message',$_POST);
		
		//$settings = (array)$this->quotemodel->getconfigurations ();
		$settings = (array)$this->quotemodel->getpurchaseremail($quote->purchasingadmin);
	    $this->load->library('email');
	    $config['charset'] = 'utf-8';
	    $config['mailtype'] = 'html';
	    
	    $this->email->initialize($config);
		$this->email->clear(true);
        $this->email->to($settings['adminemail'], "Administrator");
        $this->email->from($c->primaryemail, $c->title); 
			        
	    $link = base_url().'admin/message/messages/'.$quote->id;
            
	    $data['email_body_title']  = "Dear Administrator";
	    $data['email_body_content'] = "
		    You have got a new message from '".$c->title."' regarding PO# '$ponum'.<br><br>
		    '{$_POST['message']}'
		    <br><br>
	  		Please click following link to reply (PO# ".$this->input->post('ponum')."):  <br><br>		 
	    	<a href='$link' target='blank'>$link</a>";
	    $loaderEmail = new My_Loader();
	    $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);

	  
        $this->email->subject("New Message");
        $this->email->message($send_body);	
        //$this->email->set_mailtype("html");
        $this->email->send();
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">Message sent for PO#: '.$ponum.'</div></div></div>');
		redirect('message/index/'.$key);
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
}
