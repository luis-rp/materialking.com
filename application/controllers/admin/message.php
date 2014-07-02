<?php
class message extends CI_Controller 
{
	private $limit = 10;
	
	function message() 
	{
		parent::__construct ();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh'); 
		}
		$this->load->library ('form_validation');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$this->load->dbforge();
		$this->load->model('admin/company_model');
		$this->load->model('admin/settings_model');
		$this->load->model('admin/quote_model');
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$data ['title'] = "Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}


    function do_upload1($qid)
	{
                $this->load->helper(array('form', 'url'));
                $this->load->library('upload');
				$config['upload_path'] = './uploads/messages/';
				$config['allowed_types'] = '*';
                $config['file_name'] = $qid;
                $config['overwrite'] = false;

                $this->upload->initialize($config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
		
		}
		else
		{
        		$error = array('upload_data' => $this->upload->data());
        		$ext =   substr(strrchr(strtolower($_FILES['userfile']['name']), '.'), 1);
        		$updatearray['user_attachment'] = $qid.'.'.$ext;
		        $this->db->where('messagekey',$qid);
		        $this->db->update('message',$updatearray);
		}
				//var_dump($error); exit;
                return $error;
	}

	function sendmessage($quote,$return='bids',$filterquote='')
	{
		if(!$_POST)
			die;
		//print_r($_POST);die;
		$ponum = $_POST['ponum'];
		unset($_POST['ponum']);
		$c = $this->company_model->get_companys_by_id ($_POST['company']);
		//print_r($c);die;
		$key = md5($c->id.'-'.$quote.'-'.date('YmdHisu'));
		$_POST['messagekey'] = $key;
		$_POST['senton'] = date('Y-m-d H:i');
		$_POST['adminid'] = $this->session->userdata('id');
		$_POST['purchasingadmin'] = $this->session->userdata('purchasingadmin');
		if($this->session->userdata('usertype_id')==3)
		{
			str_replace('(Admin)', '(User)', $_POST['from']);
		}
		$this->db->insert('message',$_POST);
       
        $settings = (array)$this->settings_model->get_current_settings ();
         
	    $this->load->library('email');
		$this->email->clear(true);
        $this->email->from($settings['adminemail'], "Administrator");
        
        $this->email->to($c->primaryemail); 
			        
		$link = base_url().'message/messages/'.$key;
		
	    $body = "
    	Dear ".$c->title.",<br><br>
    	You have got a new message regarding PO# $ponum.<br><br>
    	'{$_POST['message']}'
    	<br><br>
  		Please click following link to reply (PO# ".$this->input->post('ponum')."):  <br><br>		 
    	<a href='$link' target='blank'>$link</a>
	    ";
	  
        $this->email->subject("New Message");
        $this->email->message($body);	
        $this->email->set_mailtype("html");
        $this->email->send();
        
        $err=  $this->do_upload1($key);
        
		$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Message sent to '.$_POST['to'].' successfully</div></div>');
		if($return=='backtrack')
		{
			redirect('admin/backtrack');
		}
		elseif($return=='track')
		{
		    if($this->session->userdata('usertype_id')==3)
		        redirect('admin/purchaseuser/track/'.$quote);
		    else
			    redirect('admin/quote/track/'.$quote);
		}
		elseif($return=='messages')
		{
			//redirect('admin/purchaseuser/messages/'.$filterquote);
			redirect('admin/message/messages/'.$filterquote);
		}
		else
		{
		    if($this->session->userdata('usertype_id')==3)
		        redirect('admin/purchaseuser/'.$return.'/'.$quote); 
		    else
			    redirect('admin/quote/'.$return.'/'.$quote); 
		}
	}
	
	function senderror($quote)
	{
		if(!$_POST)
			die;
		//print_r($_POST);die;
		$quote = $this->quote_model->get_quotes_by_id($quote);
		$companies = array();
		$items = array();
		$errors = array();
		
		$quantities = explode(',',$_POST['quantities']);
		$invoicenums = explode(',',$_POST['invoicenums']);
		$dates = explode(',',$_POST['dates']);
		
		$i=0;
		foreach(explode(',',$_POST['errors']) as $error)
		{
			$error = explode('-',$error);
			$errors[$error[0]] = $error[1];
			$this->db->where('id',$error[0]);
			$items[]=$this->db->get('awarditem')->row();
		}
		//print_r($errors);die;
		foreach($items as $item)
		{
			$companies[$item->company]['companydetails'] = $this->company_model->get_companys_by_id ($item->company);
			$companies[$item->company]['items'][$errors[$item->id]][]=$item;
			$companies[$item->company]['quantities'][$errors[$item->id]][]=$quantities[$i];
			$companies[$item->company]['invoicenums'][$errors[$item->id]][]=$invoicenums[$i];
			$companies[$item->company]['dates'][$errors[$item->id]][]=$dates[$i];
			$i++;
		}
		//print_r($companies);die;
		$i = 0;
		foreach($companies as $company)
		{
			$c = $company['companydetails'];
			$settings = (array)$this->settings_model->get_current_settings ();
		    $this->load->library('email');
			$this->email->clear(true);
	        $this->email->from($settings['adminemail'], "Administrator");
	        
	        
            $toemail = $c->primaryemail;
            $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
	        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
            $purchaseusers = $this->db->query($sql)->result();
            foreach ($purchaseusers as $pu) {
                $toemail = $toemail . ',' . $pu->email;
            }
            $this->email->to($toemail); 
			
		    $body = "
		    Dear ".$c->title.",<br><br>
		    There are errors on items you sent for PO# {$quote->ponum}:
		  	<dl>
		  	";
		    
			while(list($e,$items)=each($company['items']))
			{
		  		$body.="
		  		<dt>".$e." (".count($items)." items):</dt>";
		  		$body.="
		  		<dd>
		  			<table cellspacing=5 cellpadding=25 border=1 clsss='table table-bordered col-md-4' style='border-radius: 3px;border-style: solid solid solid solid;border-width: 1px 1px 1px 1px;'>
		  				<tr><th>Item</th><th>Qty</th><th>Invoice#</th><th>Date</th></tr>
		  			";
		  			foreach($items as $item)
		  			{
	  					$body .= "
	  					<tr>
	  						<td>".$item->itemcode."</td>
	  						<td>".$company['quantities'][$e][$i]."</td>
	  						<td>".$company['invoicenums'][$e][$i]."</td>
	  						<td>".$company['dates'][$e][$i]."</td>
	  					</tr>
	  					";
		  			}
		  		$body.="
		  			</table>
		  		</dd>";
			}
		  	$body.="
		  	</dl>
		    ";
		  	//echo($body);
		    //die;
	        $this->email->subject("Error in items sent");
	        $this->email->message($body);	
	        $this->email->set_mailtype("html");
	        $this->email->send();
		}
		$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Error notifications sent via email.</div></div>');
		$this->session->set_userdata(array("messageError"=>$body));
		die;
	}
	
	function messages($quote='')
	{
		$whrMessage = '';
		$orderBy = '';
	
		if(isset($_POST['searchmsg']) && $_POST['searchmsg'] != '')
		{
			$whrMessage .= " AND m.message LIKE '%".trim($_POST['searchmsg'])."%'";
		}
		
		if(isset($_POST['ponumsearch']) && $_POST['ponumsearch'] != '')
		{
			$whrMessage .= " AND q.ponum LIKE '%".trim($_POST['ponumsearch'])."%'";
		}
		if(isset($_POST['sortby']) && $_POST['sortby'] != '')
		{
			if($_POST['sortby'] == 'date')
			{
				$orderBy .= " ORDER BY m.senton ASC" ;
			}
			if($_POST['sortby'] == 'ponumber')	
			{
				$orderBy .= "  ORDER BY q.ponum ASC" ;
			}
			if($_POST['sortby'] == 'company')	
			{
				$orderBy .= "  ORDER BY c.title ASC" ;
			}
		}
		else 
		{
			$orderBy .= " ORDER BY m.senton ASC" ;
		}
		
		if($this->session->userdata('usertype_id')!=2)
		{
			$message = 'You dont have the permission to access the page.';
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$message.'</div></div>');
			redirect('admin/dashboard'); 
		}
		$quotewhere = '';
		$con = '';
		if($quote)
			$quotewhere = ' AND m.quote="'.$quote.'"';
		
		if ($this->session->userdata('managedprojectdetails'))
		{
			$con = " AND q.pid='".$this->session->userdata('managedprojectdetails')->id."'";
		}
		
		
		$messagesql = "SELECT m.*,q.id quoteid, q.ponum, c.title companyname, c.email companyemail, u.email adminemail FROM 
		".$this->db->dbprefix('message')." m, ".$this->db->dbprefix('quote')." q,
		".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('users')." u
		WHERE m.quote=q.id AND m.company=c.id AND m.adminid=u.id		
		AND m.purchasingadmin='{$this->session->userdata('id')}' $whrMessage $quotewhere $con $orderBy";
		
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
		$data['searchmsg'] = (isset($_POST['searchmsg']) && $_POST['searchmsg'] != '') ? $_POST['searchmsg'] : " ";
		$data['sortbyoption']  = (isset($_POST['sortby']) && $_POST['sortby'] != '') ? $_POST['sortby'] : " ";
		$data['ponumsearch']  = (isset($_POST['ponumsearch']) && $_POST['ponumsearch'] != '') ? $_POST['ponumsearch'] : " ";
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
