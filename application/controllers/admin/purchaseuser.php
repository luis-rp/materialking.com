<?php
class purchaseuser extends CI_Controller 
{
	private $limit = 10;
	
	
	function purchaseuser() 
	{
		parent::__construct();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login', 'refresh'); 
		}
		$this->load->dbforge();
		$this->load->library ( array ('table', 'validation') );
		$this->load->helper ('url');
		$this->load->model ('admin/purchaseusermodel', '', TRUE);
		$this->load->model('admin/project_model', '', TRUE);
		$this->load->model('admin/quote_model', '', TRUE);
		$this->load->model('admin/settings_model', '', TRUE);
		$id = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($id);
		if(empty($setting)){
		$data['settingtour']=$setting;
		$data['timezone']='America/Los_Angeles';
		}else{
		$data['timezone']=$setting[0]->tour;
		$data['timezone']=$setting[0]->timezone;
		}
		$this->load->model('admin/company_model', '', TRUE);
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$data ['title'] = "Site Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}
	
	function index()
	{
		
	}
	
	function quotepermissions()
	{
		if($this->session->userdata('usertype_id')!=2)
			die('You dont have the access right.');
		$quote = $_POST['quote'];
		$quotedetails = $this->quote_model->get_quotes_by_id ($quote);
		$query = "SELECT *
				  FROM ".$this->db->dbprefix('users')."
				  WHERE purchasingadmin=".$this->session->userdata('id')." 
				  AND id!=".$this->session->userdata('id');
		//echo $query;die;
		$users = $this->db->query($query)->result();
		//print_r($users);die;
		if(!$users)
		{
			die('No users created.');
		}
		$ret = '';
		$ret .= '<form action="'.site_url('admin/purchaseuser/savequotepermission').'" method="POST">';
		$ret .= '<input type="hidden" value="'.$quote.'" name="quote"/>';
		$ret .= '<input type="hidden" value="'.$quotedetails->pid.'" name="project"/>';
		$ret .= '<table class="table table-bordered">';
		$ret .= '<tr>';
		$ret .= '<th>Name</th>';
		$ret .= '<th>Username</th>';
		$ret .= '<th>Email</th>';
		$ret .= '<th>Select</th>';
		$ret .= '</tr>';
		foreach($users as $user)
		{
			$query = "SELECT * FROM ".$this->db->dbprefix('quoteuser')." 
					  WHERE userid=".$user->id." AND quote=".$quote;
			//die($query);
			$check = $this->db->query($query)->num_rows;
			
			$ret .= '<tr>';
			$ret .= '<td>'.$user->fullname.'</td>';
			$ret .= '<td>'.$user->username.'</td>';
			$ret .= '<td>'.$user->email.'</td>';
			$ret .= '<td><input name="users[]" type="checkbox" '.($check?'checked="CHECKED"':'').' value="'.$user->id.'" /></td>';
			$ret .= '</tr>';
		}
		$ret .= '</table>';
		$ret .= '<input type="submit" value="Save" class="btn btn-primary"/>';
		$ret .= '</form>';
		die($ret);
	}
	
	function savequotepermission()
	{
		if($this->session->userdata('usertype_id')!=2)
			die('You dont have the access right.');
		$quoteid = $_POST['quote'];
		//die($quote);
		$query = "SELECT *
				  FROM ".$this->db->dbprefix('users')."
				  WHERE purchasingadmin=".$this->session->userdata('id');
		//echo $query;die;
		$users = $this->db->query($query)->result();
		foreach($users as $user)
		{
			$this->db->where('userid',$user->id);
			$this->db->where('quote',$quoteid);
			$this->db->delete('quoteuser');
		}
		$settings = (array)$this->settings_model->get_current_settings ();
		$users = $_POST['users'];
		if($users)
		foreach($users as $user)
		{
			$perm = array();
			$perm['quote'] = $quoteid;
			$perm['userid'] = $user;
			$this->db->insert('quoteuser',$perm);
			
			$this->db->where('id',$quoteid);
			$quote = $this->db->get('quote')->row();
			
			$this->db->where('id',$user);
			$user = $this->db->get('users')->row();
			
			$this->load->library('email');
			$config['charset'] = 'utf-8';
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			//$this->email->clear(true);
	        $this->email->from($settings['adminemail'], "Administrator");
	        
	        $this->email->to($user->email); 
			$link = '<a href="'.site_url('admin/purchaseuser/quoteitems/'.$quote->id).'"></a>';
		    $data['email_body_title'] = "Dear ".$user->fullname;
		    $data['email_body_content'] = "Your are assigned to the PO# $quote->ponum <br/><br/>
You will receive alerts regarding action items taken on this PO.<br/><br/>
$link 
	    	";
		    $loaderEmail = new My_Loader();
		    $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		    $this->email->subject("PO assigned");
	        $this->email->message($send_body);	
	        $this->email->set_mailtype("html");
	        $this->email->send();
		}
		$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Permissions assigned.</div></div>');
		redirect('admin/quote/index/'.$_POST['project']); 
	}
	
	function quotes($offset = 0)
	{
		if($this->session->userdata('usertype_id')!=3)
		{
			$message = 'You dont have the permission to access the page.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/dashboard'); 
		}
		$query = "SELECT q.* FROM ".$this->db->dbprefix('quoteuser')." qu, ".$this->db->dbprefix('quote')." q
				  WHERE q.id=qu.quote AND qu.userid=".$this->session->userdata('id')." 
				  AND q.pid='".$this->session->userdata('managedprojectdetails')->id."'
				  ORDER BY podate DESC
				   ";
		//echo $query;
		$quotes = $this->db->query($query)->result();
		$uri_segment = 4;
		$offset = $this->uri->segment ($uri_segment);
		if(!$offset)
			$offset = 0;
		
		$this->load->library ('pagination');
		$config ['base_url'] = site_url ('admin/purchaseuser/quotes');
		$config ['total_rows'] = count($quotes);
		$config ['per_page'] = $this->limit;
		$config ['uri_segment'] = $uri_segment;
		
		$this->pagination->initialize ($config);
		$data ['pagination'] = $this->pagination->create_links();
		
		$quotes = $this->db->query($query." LIMIT $offset, $this->limit")->result();
		//print_r($quotes);die;
		$items = array();
		if($quotes)
		{
			foreach ($quotes as $quote) 
			{
				$quote->invitations = $this->quote_model->getInvitedquote($quote->id);
				$quote->pendingbids = $this->quote_model->getbidsquote($quote->id);
				$quote->awardedbid = $this->quote_model->getawardedbidquote($quote->id);
				
				$quote->podate = $quote->podate?$quote->podate:'';
				$quote->status = $quote->awardedbid?'AWARDED':($quote->pendingbids?'PENDING AWARD':($quote->invitations?'NO BIDS':($quote->potype=='Direct'?'-':'NO INVITATIONS')));
				
				if($quote->status == 'AWARDED')
				{
					$quote->status = $quote->status.' - '.strtoupper($quote->awardedbid->status);
				}
				$quote->actions=anchor ('admin/purchaseuser/quoteitems/' . $quote->id,'<span class="icon-2x icon-search"></span>',array ('class' => 'view','title' => 'view quote items' ) )
				. ' ' .
				anchor ('admin/purchaseuser/messages/' . $quote->id,'<span class="icon-2x icon-envelope"></span>',array ('class' => 'view','title' => 'view messages' ) )
				;
				$quote->sent = '<div class="badgepos"><span class="badge badge-blue">' . count($quote->invitations) .'</span></div>'
				;
				
				if($quote->awardedbid)
				{
					$quote->actions.= ' ' . 
					anchor ( 'admin/purchaseuser/track/' . $quote->id, '<span class="label label-pink">Track</span> ', array ('class' => 'view','alt' => 'awarded bid','title' => 'awarded bid' ) ) 
					;
				}
				$quote->recived='';
				if($quote->pendingbids)
				{
					$quote->recived = anchor ( 'admin/purchaseuser/bids/' . $quote->id, '<div class="badgepos"><span class="badge badge-red">' . count($quote->pendingbids) .'</span></div>', array ('class' => 'view' ) ) 
					;
				}
				if(@$_POST['postatus'])
				{
					if($quote->status == $_POST['postatus'])
					{
						$items[] = $quote;
					}
				}
				else
				{
					$items[] = $quote;
				}
			}
		    $data['jsfile'] = 'quotejs.php';
		}
		else
		{
			$this->data['message'] = 'No Records';
		}
		$data['items'] = $items;
		$data ['heading'] = 'Quote &amp; Purchase Order Management';
		$this->load->view ('admin/purchaseuser/quotelist', $data);
	}

	
	function quoteitems($id)
	{
		if($this->session->userdata('usertype_id')!=3)
		{
			$message = 'You dont have the permission to access the page.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/dashboard'); 
		}
		$this->db->where(array('userid'=>$this->session->userdata('id'),'quote'=>$id));
		if($this->db->get('quoteuser')->num_rows == 0)
		{
			$message = 'You dont have the permission to view the PO.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/purchaseuser/quotes');
		}
		$quote = $this->quote_model->get_quotes_by_id ($id);
		//echo '<pre>';print_r($quote);die;
		if(!$quote)
		{
			$message = 'Wrong link.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/purchaseuser/quotes');
		}
		$quoteitems = $this->quote_model->getitems($id);
		$data['quote'] = $quote;
		$data['quoteitems'] = $quoteitems;
		//$this->load->model('admin/project_model');
		$data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
		$data['config'] = (array)$this->settings_model->get_current_settings ();
		$data['heading'] = "Purchase Order Details";
		$this->load->view ('admin/quotedetails', $data);
	}
	
	function bids($qid)
	{
		if($this->session->userdata('usertype_id')!=3)
		{
			$message = 'You dont have the permission to access the page.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/dashboard'); 
		}
		$this->db->where(array('userid'=>$this->session->userdata('id'),'quote'=>$qid));
		if($this->db->get('quoteuser')->num_rows == 0)
		{
			$message = 'You dont have the permission to view the PO.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/purchaseuser/quotes');
		}
		$quote = $this->quote_model->get_quotes_by_id ($qid);
		//echo '<pre>';print_r($quote);die;
		if(!$quote)
		{
			$message = 'Wrong link.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/purchaseuser/quotes');
		}
		$bids = $this->quote_model->getbids($qid);
		$quoteitems = $this->quote_model->getitems($qid);
		$awarded = $this->quote_model->getawardedbid($qid);
		//echo '<pre> bids ';print_r($awarded);echo '</pre>';//die;
		if(!$bids)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Bids Yet Placed.</div></div>');
			redirect('admin/purchaseuser/quotes/'); 
		}
		
		if(!$awarded)
			$data['isawarded'] = 'No';
		else
		{
			$data['isawarded'] = 'Yes';
			$data['awarded'] = $awarded;
		}
		$minimum = array();
		$maximum = array();
		foreach($bids as $bid)
		{
			$totalprice = 0;
			foreach($bid->items as $item)
			{
				foreach($quoteitems as $qi)
				{
					if($qi->itemcode == $item->itemcode)
					{
						$item->originaldate = $qi->daterequested;
					}
				}
				$totalprice += $item->totalprice;
				$key = $item->itemcode;
				if(!isset($minimum[$key])){
					$minimum[$key] = $item->ea;
					$maximum[$key] = $item->totalprice;
					   
				}
				elseif($minimum[$key] > $item->ea){
					$minimum[$key] = $item->ea;
				} else if($maximum[$key] < $item->totalprice) {
					$maximum[$key] = $item->totalprice;
					
				}
					
			}
			if(!isset($minimum['totalprice']))
				$minimum['totalprice'] = $totalprice;
			elseif($minimum['totalprice'] > $totalprice)
				$minimum['totalprice'] = $totalprice;
		}
		
		//echo '<pre>';print_r(array_sum($maximum));echo '</pre>';//die;
		
		$data['quote'] = $this->quote_model->get_quotes_by_id ($qid);
		$data['quoteitems'] = $quoteitems;
		//$this->load->model('admin/project_model');
		$data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
		$data['config'] = (array)$this->settings_model->get_current_settings ();
		$data['bids'] = $bids;
		$data['minimum'] = $minimum;
				$data['maximum'] = $maximum;

		$data['heading'] = $data['quote']->potype=='Bid'?"Bids Placed":"Bid Details";
		$this->load->view ('admin/purchaseuser/bids', $data);
	}
	
	function track($qid)
	{
		if($this->session->userdata('usertype_id')!=3)
		{
			$message = 'You dont have the permission to access the page.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/dashboard'); 
		}
		$this->db->where(array('userid'=>$this->session->userdata('id'),'quote'=>$qid));
		if($this->db->get('quoteuser')->num_rows == 0)
		{
			$message = 'You dont have the permission to view the PO.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/purchaseuser/quotes');
		}
		$quote = $this->quote_model->get_quotes_by_id ($qid);
		//echo '<pre>';print_r($quote);die;
		if(!$quote)
		{
			$message = 'Wrong link.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/purchaseuser/quotes');
		}
		$awarded = $this->quote_model->getawardedbid($qid);
		//echo '<pre>';print_r($awarded);die;
		if(!$awarded)
			die;
		
		$messagesql = "SELECT * FROM ".$this->db->dbprefix('message')." WHERE quote='{$qid}' ORDER BY senton ASC";
		$msgresult = $this->db->query($messagesql)->result();
		$messages = array();
		foreach ($msgresult as $msg)
		{
			$messages[$msg->company]['companydetails'] = $this->company_model->get_companys_by_id($msg->company);
			$messages[$msg->company]['messages'][]=$msg;
		}
		//print_r($messages);die;
		
		$data['quote'] = $this->quote_model->get_quotes_by_id ($awarded->quote);
		$data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
		$data['config'] = (array)$this->settings_model->get_current_settings ();
		$data['messages'] = $messages;
		$data['awarded'] = $awarded;
		$data['heading'] = "TRACK Items";
		$this->load->view ('admin/purchaseuser/track', $data);
	}
	
	function messages($quote='')
	{
		if($this->session->userdata('usertype_id')!=3)
		{
			$message = 'You dont have the permission to access the page.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/dashboard'); 
		}
		$quotewhere = '';
		if($quote)
			$quotewhere = ' AND m.quote='.$quote;
		$messagesql = "SELECT m.*,q.id quoteid, q.ponum, c.title companyname, c.email companyemail, u.email adminemail FROM 
		".$this->db->dbprefix('message')." m, ".$this->db->dbprefix('quote')." q,
		".$this->db->dbprefix('quoteuser')." qu, ".$this->db->dbprefix('company')." c,
		".$this->db->dbprefix('users')." u
		WHERE m.quote=q.id AND m.company=c.id AND q.id=qu.quote AND m.adminid=u.id
		AND qu.userid='{$this->session->userdata('id')}' 
		AND q.pid='".$this->session->userdata('managedprojectdetails')->id."'
		$quotewhere ORDER BY m.senton DESC";
		
		$quotesql = "SELECT q.* FROM ".$this->db->dbprefix('quote')." q, ".$this->db->dbprefix('quoteuser')." qu
					 WHERE q.id=qu.quote AND qu.userid='{$this->session->userdata('id')}' 
		";
		$data['availablequotes'] = $this->db->query($quotesql)->result();
		//echo $quotesql;
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
		}
		//echo '<pre>';print_r($messages);die;
		$data['messages'] = $messages;
		$data['filterquote'] = $quote;
		$uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}
		$this->load->view ('admin/purchaseuser/messages', $data);
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
?>