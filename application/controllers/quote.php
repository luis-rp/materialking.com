<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Quote extends CI_Controller 
{
	public function Quote()
	{
		ini_set("memory_limit","512M");
		ini_set("max_execution_time", 700);
	    parent::__construct ();
		$data ['title'] = 'Dashboard';
		$this->load->dbforge();
		$this->load->model ('homemodel', '', TRUE);
		$this->load->model ('quotemodel', '', TRUE);
		$this->load->model ('messagemodel', '', TRUE);
		$this->load->model ('admin/project_model', '', TRUE);
		$this->load->model ('admin/settings_model', '', TRUE);
		if($this->session->userdata('company')) $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
		//print_r($data['newquotes']);die;
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/front/template', $data);
	}
	
	function index()
	{
		$this->invitations();
	}
	

	function performanceexport()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$query = "SELECT itemid, itemcode, count(bi.id) as bidcount
				  FROM ".$this->db->dbprefix('biditem')." bi, ".$this->db->dbprefix('bid')." b
					  WHERE bi.bid=b.id AND b.company={$company->id}
					  GROUP BY itemid HAVING itemid
					  ";
					  $data['items'] = array();
					  $items = $this->db->query($query)->result();
					  foreach($items as $item)
					  {
			$query = "SELECT count(id) as awardcount
					  FROM ".$this->db->dbprefix('awarditem')."
					  		WHERE company={$company->id} AND itemid='".$item->itemid."'
					  		";
					  			
					  		$item->awardcount = $this->db->query($query)->row()->awardcount;
					  		$item->performance = round(($item->awardcount/$item->bidcount) * 100,2);
					  		$data['items'][]= $item;
	}
		
	//=========================================================================================
	$header[] = array('Itemcode' , 'Bids','Awards' , 'Win Rate(%)');
	
	foreach($data['items'] as $item)
					  {
					  $header[] = array($item->itemcode , $item->bidcount,$item->awardcount , $item->performance);
	}
	createXls('performance', $header);
	die();
	//===============================================================================
	
	}
	
	//Performance PDF
	function performancePDF()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$query = "SELECT itemid, itemcode, count(bi.id) as bidcount
				  FROM ".$this->db->dbprefix('biditem')." bi, ".$this->db->dbprefix('bid')." b
					  WHERE bi.bid=b.id AND b.company={$company->id}
					  GROUP BY itemid HAVING itemid
					  ";
					  $data['items'] = array();
					  $items = $this->db->query($query)->result();
					  foreach($items as $item)
					  {
			$query = "SELECT count(id) as awardcount
					  FROM ".$this->db->dbprefix('awarditem')."
					  		WHERE company={$company->id} AND itemid='".$item->itemid."'
					  		";
					  			
					  		$item->awardcount = $this->db->query($query)->row()->awardcount;
					  		$item->performance = round(($item->awardcount/$item->bidcount) * 100,2);
					  		$data['items'][]= $item;
						}
		
			//=========================================================================================
			$header[] = array('Itemcode' , 'Bids','Awards' , 'Win Rate(%)');
			
			foreach($data['items'] as $item)
							  {
							  $header[] = array($item->itemcode , $item->bidcount,$item->awardcount , $item->performance);
			}
			 
					$headername = "PERFORMANCE BY ITEM";
				createPDF('performance', $header,$headername);
				die();
			 
			//===============================================================================
	
	}
	
	

	function invoices_export ()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$invs = $this->quotemodel->getinvoices_export($company->id);
		$invoices = array();
		foreach($invs as $i)
		{
			if(isset($i) && isset($i->quote) && isset($i->quote->ponum))
			$invoices[$i->quote->ponum][]=$i;
		}
		
		$data['invoices'] = $invoices;
		$this->db->select($this->db->dbprefix('users.').'*');
		$this->db->where('usertype_id',2);
		$this->db->from('users')->join('network',"users.id=network.purchasingadmin")->where('network.company',$company->id);
		$data['purchasingadmins'] = $this->db->get()->result();
		
		//=========================================================================================
				
		$header[] = array('Report type' , 'Invoices','' , '' , '' , '' , '');
		
		$fullname = 'All';		
		if($this->session->userdata("searchpurchasingadmin"))
		{
			$searchpurchasingadmin = $this->session->userdata("searchpurchasingadmin");		
			$this->db->select($this->db->dbprefix('users.').'fullname');			
			$this->db->from('users')->where('users.id',$searchpurchasingadmin);
			$fullname_arr = $this->db->get()->result();		
		    $fullname     = $fullname_arr[0]->fullname;
		}								
		$header[] = array('Company' , $fullname ,'' , '' , '' , '' , '');	
		$header[] = array('' , '','' , '' , '' , '' , '');
				
		$header[] = array('PO Number' , 'Invoice#','Received On' , 'Total Cost' , 'Payment Status' , 'Verification' , 'Date Due');				
					
		foreach($invoices as $ponum=>$invs)
		{
			foreach($invs as $i)
			{
				$due_date = '';
				if($i->datedue)
				{
				 $due_date =  date("m/d/Y", strtotime($i->datedue));
				} 
				$header[] = array($ponum , $i->invoicenum,$i->receiveddate , '$ '.formatPriceNew($i->totalprice) , $i->paymentstatus , $i->status , $due_date);		
			}
		}
		createXls('Invoice', $header);  			
		die();	
		
		//===============================================================================
		
	}
	
	//Invoices PDF
	function invoices_pdf ()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$invs = $this->quotemodel->getinvoices_export($company->id);
		$invoices = array();
		foreach($invs as $i)
		{
			if(isset($i) && isset($i->quote) && isset($i->quote->ponum))
			$invoices[$i->quote->ponum][]=$i;
		}
		
		$data['invoices'] = $invoices;
		$this->db->select($this->db->dbprefix('users.').'*');
		$this->db->where('usertype_id',2);
		$this->db->from('users')->join('network',"users.id=network.purchasingadmin")->where('network.company',$company->id);
		$data['purchasingadmins'] = $this->db->get()->result();
		
		//=========================================================================================
				
		$header[] = array('Report type:' , 'Invoices','' , '' , '' , '' , '');
		
		$fullname = 'All';		
		if($this->session->userdata("searchpurchasingadmin"))
		{
			$searchpurchasingadmin = $this->session->userdata("searchpurchasingadmin");		
			$this->db->select($this->db->dbprefix('users.').'fullname');			
			$this->db->from('users')->where('users.id',$searchpurchasingadmin);
			$fullname_arr = $this->db->get()->result();		
		    $fullname     = $fullname_arr[0]->fullname;
		}								
		$header[] = array('<b>Company</b>' , $fullname ,'' , '' , '' , '' , '');	
		$header[] = array('' , '','' , '' , '' , '' , '');
				
		$header[] = array('<b>PO Number</b>' , '<b>Invoice#</b>','<b>Received On</b>' , '<b>Total Cost</b>' , '<b>Payment Status</b>' , '<b>Verification</b>' , '<b>Date Due</b>');				
					
		foreach($invoices as $ponum=>$invs)
		{
			foreach($invs as $i)
			{
				$due_date = '';
				if($i->datedue)
				{
				 $due_date =  date("m/d/Y", strtotime($i->datedue));
				} 
				$header[] = array($ponum , $i->invoicenum,$i->receiveddate , '$ '.formatPriceNew($i->totalprice) , $i->paymentstatus , $i->status , $due_date);		
			}
		}
		 	
		 
		$headername = "INVOICES";
		createotherPDF('Invoice', $header,$headername);
		die();
			 
		
		//===============================================================================
		
	}

	
	
	function invitations()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$pafilter = '';		
		if(@$_POST['searchpurchasingadmin'])
		{
			$pafilter = " AND i.purchasingadmin='".$_POST['searchpurchasingadmin']."'";
			$this->db->where('purchasingadmin',$_POST['searchpurchasingadmin']);
			$projects = $this->db->get('project')->result();
			$data['projects'] = array();
			foreach($projects as $project)
			{
				$sql = "SELECT * FROM ".$this->db->dbprefix('quote')." q, ".$this->db->dbprefix('bid')." b
					WHERE b.quote=q.id AND q.pid=".$project->id;
				if($this->db->query($sql)->result())
				{
					$data['projects'][]=$project;
				}
			}
		}
		
		$sql = "SELECT i.*,q.ponum FROM 
		".$this->db->dbprefix('invitation')." i, ".$this->db->dbprefix('quote')." q
		WHERE i.quote=q.id AND i.company='{$company->id}' $pafilter ORDER BY i.senton DESC";
		$count = $this->db->query($sql)->num_rows;
		
		//echo $sql;
		
		$invs = $this->db->query($sql)->result();
		
		$invitations = array();
		foreach($invs as $inv)
		{
    		$this->db->where('id',$inv->quote);
    		$inv->quotedetails = $this->db->get('quote')->row();
    		$this->db->where('quote',$inv->quote);
    		$this->db->where('company',$company->id);
    		$bid = $this->db->get('bid')->row();
    		$inv->quotenum = @$bid->quotenum;
    		
			$awarded = $this->quotemodel->checkbidcomplete($inv->quote);
			$inv->awardedtothis = false;
			
			if($bid){
				$sqlq = "SELECT daterequested FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bid->id."' AND purchasingadmin='".$bid->purchasingadmin."' order by id desc limit 1";
				$revisionquote = $this->db->query($sqlq)->row();
				if($revisionquote)
				$inv->daterequested = $revisionquote->daterequested;
			}
			
			if($awarded)
			{
				$complete = true;
				$noitemsgiven = true;
				$allawarded = true;
				$this->db->where('award',$awarded->id);
				$this->db->where('company',$company->id);
				$items = $this->db->get('awarditem')->result();
				foreach($items as $i)
				{
					if($i->received < $i->quantity)
						$complete = false;
					if($i->company != $company->id)
						$allawarded = false;
					if($i->received > 0)
						$noitemsgiven = false;
				}
				
				if(!$noitemsgiven)
				{
					if($complete)
					{
						$inv->status = 'Completed';
						$inv->progress = 100;
						$inv->mark = "progress-bar-success";
					}
					else
					{
						$inv->status = 'Partially Completed';
						$inv->progress = 80;
						$inv->mark = "progress-bar-success";
					}
				}
				else
				{
					$awardeditems = $this->quotemodel->getawardeditems($awarded->id,$company->id);
					
					if($awardeditems && !$allawarded)
					{
						$inv->status = 'Partially Awarded';
						$inv->progress = 60;
						$inv->mark = "progress-bar-success";
					}
					else
					{
						$inv->status = 'Awarded';
						$inv->progress = 60;
						$inv->mark = "progress-bar-success";
					}
				}
				
				if($this->quotemodel->getawardeditems($awarded->id,$company->id))
				{
					$inv->awardedtothis = true;
					$inv->award = $awarded->id;
				}
				else
				{
					$inv->status = 'PO Closed - 0 items won';
					$inv->progress = 100;
					$inv->mark = "progress-bar-warning";
				}
			}
			elseif($this->quotemodel->getdraftitems($inv->quote,$inv->company))
			{
				$inv->status = 'Processing';
				$inv->progress = 40;
				$inv->mark = "progress-bar-warning";
			}
			else
			{
				$inv->status = 'New';
				$inv->progress = 20;
				$inv->mark = "progress-bar-danger";
			}
			
			if(!@$_POST['searchstatus'])
			{
				$invitations[]=$inv;
			}
			elseif(@$_POST['searchstatus'] == $inv->status)
			{
				$invitations[]=$inv;
			}
			
		}
		$this->db->select($this->db->dbprefix('users.').'*');
		$this->db->where('usertype_id',2);
		$this->db->from('users')->join('network',"users.id=network.purchasingadmin")->where('network.company',$company->id);
		$data['purchasingadmins'] = $this->db->get()->result();
		$data['company'] = $company;
		$data['invitations'] = $invitations;
		$this->load->view('quote/invitations',$data);
	}
	
	public function direct($key=null)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$invitation = $this->quotemodel->getinvitation($key);
		if(!$invitation)
		{
			$message = 'Purchase order already reviewed, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		if($company->id != $invitation->company)
		{
			$message = 'Wrong Access.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		
		$quote = $this->quotemodel->getquotebyid($invitation->quote);
		if($this->quotemodel->checkbidcomplete($quote->id))
		{
			$message = 'PO Already Completed, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		
		$this->db->where('company',$company->id);
		$this->db->where('quote',$quote->id);
		$quoteitems = $this->db->get('quoteitem')->result();
		
		$draftitems = $this->quotemodel->getdraftitems($quote->id,$invitation->company);
		
		$data['invitation'] = $key;
		$data['quote'] = $quote;
		
		$this->db->where('quote',$quote->id);
		$bid = $this->db->get('bid')->row();
	    $data['quotenum'] = $bid?$bid->quotenum:'';
	    $data['quotefile'] = $bid?$bid->quotefile:'';
		
		$items = $draftitems?$draftitems:$quoteitems;
		$data['quoteitems'] = $items;
		//echo '<pre>';print_r($items);//die;
		
		
        $this->db->where('company', $company->id);
        $tier = $this->db->get('tierpricing')->row();
        if ($tier) 
        {
            
    		$sql = "SELECT *
    				FROM ".$this->db->dbprefix('purchasingtier')." pt 
    				WHERE pt.company='".$company->id."' AND pt.purchasingadmin='".$quote->purchasingadmin."'
    			";
    		$ptier = $this->db->query($sql)->row();
    		$data['purchasingtier'] = $ptier;
    		$data['tier'] = $tier;
        }
		
		$data['draft'] = $draftitems?1:0;
		
		$data['company'] = $company;
		
		$this->db->where('id',$invitation->purchasingadmin);
		$pa = $this->db->get('users')->row();
		$data['purchasingadmin'] = $pa;
		$this->load->view('quote/review',$data);
	}
	
	public function reviewpo()
	{
	    $key = $_POST['invitation'];
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$invitation = $this->quotemodel->getinvitation($key);
		if(!$invitation)
		{
			$message = 'Purchase order already reviewed, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		if($company->id != $invitation->company)
		{
			$message = 'Wrong Access.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		
		$quote = $this->quotemodel->getquotebyid($invitation->quote);
		if($this->quotemodel->checkbidcomplete($quote->id))
		{
			$message = 'PO Already Completed, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		
		$this->db->where('company',$company->id);
		$this->db->where('quote',$quote->id);
		$quoteitems = $this->db->get('quoteitem')->result();
		
		$draftitems = $this->quotemodel->getdraftitems($quote->id,$invitation->company);
		//print_r($_POST);die;
		if(@$_POST['postatus'])
		{
	        if($draftitems)
	        {
        	    foreach($_POST['postatus'] as $k=>$v)
        	    {
    	            $this->db->where('id',$k);
    	            $this->db->update('biditem',array('postatus'=>$v));
        	    }
	        }
	        else
	        {
	            $bidarray = array('quote'=>$invitation->quote,'company'=>$company->id,'submitdate'=>date('Y-m-d'));
    			$bidarray['quotenum'] = $_POST['quotenum'];
    			$bidarray['purchasingadmin'] = $invitation->purchasingadmin;
        		if(is_uploaded_file($_FILES['quotefile']['tmp_name']))
        		{
        			$ext = end(explode('.', $_FILES['quotefile']['name']));
        			$nfn = md5(date('u').uniqid()).'.'.$ext;
        			if(move_uploaded_file($_FILES['quotefile']['tmp_name'], "uploads/quotefile/".$nfn))
        			{
        				$bidarray['quotefile'] = $nfn;
        			}
        		}
    			$this->db->insert('bid',$bidarray);
    			$bidid = $this->quotemodel->db->insert_id();
				//echo '<pre>';
				//print_r($quoteitems);
	            foreach($quoteitems as $item)
	            {
    				$insertarray = array();
    				$insertarray['bid'] = $bidid;
    				$key = $item->id;
    				while(list($k,$v) = each($item))
    				{
    					if($k != 'invitation' && $k != 'id' && $k != 'quote' && $k != 'company')
    					{
    						$insertarray[$k] = $v;
    					}
    				}
    				$item = (array)$item;
    				$insertarray['totalprice'] = $item['quantity'] * $item['ea'];
    				$insertarray['purchasingadmin'] = $invitation->purchasingadmin;
    				$insertarray['postatus'] = $_POST['postatus'][$item['id']];
    				//print_r($insertarray);//die;
					$this->quotemodel->db->insert('biditem',$insertarray);
					
					$this->quotemodel->saveminimum($invitation->company,$invitation->purchasingadmin,$insertarray['itemid'],$insertarray['itemcode'],$insertarray['itemname'],$insertarray['ea']);
						
	            }
	        }
	    }
	    
		$message = 'PO Review Saved, Thank You.';
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
		redirect('quote/invitations');
	    
	}
	
	public function invitation($key,$print='')
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$invitation = $this->quotemodel->getinvitation($key);
		if(!$invitation)
		{
			$message = 'Quote Already Submitted for Review, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		if($company->id != $invitation->company)
		{
			$message = 'Wrong Access.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		
		$quote = $this->quotemodel->getquotebyid($invitation->quote);
		if($this->quotemodel->checkbidcomplete($quote->id))
		{
			$message = 'Bid Already Completed, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		
		
        $sql = "SELECT tier FROM " . $this->db->dbprefix('purchasingtier') . " pt
        		WHERE pt.purchasingadmin='".$quote->purchasingadmin."' AND pt.company='" . $company->id . "'
			";
        $data['patier'] = @$this->db->query($sql)->row()->tier;
		
		
		$quoteitems = $this->quotemodel->getquoteitems($quote->id);
		//print_r($quoteitems);die;
		$originalitems1 = $this->quotemodel->getquoteitems($quote->id);
		$company = $this->quotemodel->getcompanybyid($invitation->company);
		$draftitems = $this->quotemodel->getdraftitems($quote->id,$invitation->company);
		
		$sql = "SELECT tier
				FROM ".$this->db->dbprefix('purchasingtier')." pt 
				WHERE pt.company='".$company->id."' AND pt.purchasingadmin='".$quote->purchasingadmin."'
			";
		$tier = $this->db->query($sql)->row();
		if($tier)
		{
			$tier = $tier->tier;
			$sql = "SELECT *
				FROM ".$this->db->dbprefix('tierpricing')." pt 
				WHERE pt.company='".$company->id."'
			";
			$tiers = $this->db->query($sql)->row();
			$tier = $tiers->$tier;
		}
		else
		{
			$tier = 0;
		}
		//die($tier);
		//echo $sql;
		$admins = $this->db->query($sql)->result();
		
		
		foreach($originalitems1 as $q)
		{
			$originalitems[$q->itemid] = $q;
		}
		$data['originalitems'] = $originalitems;
		//echo '<pre>'; print_r($originalitems);
		
		$this->db->where('company',$company->id);
		$tiers = $this->db->get('tierpricing')->row();
		//print_r($tiers);die;
		$data['tiers'] = $tiers;
		$data['invitation'] = $key;
		$data['quote'] = $quote;
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company->id);
		$bid = $this->db->get('bid')->row();
	    $data['quotenum'] = $bid?$bid->quotenum:'';
	    $data['quotefile'] = $bid?$bid->quotefile:'';
	    $data['expire_date'] = $bid?$bid->expire_date:'';
	     if($bid){
	    	$sqlq = "SELECT revisionid FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bid->id."' AND purchasingadmin='".$quote->purchasingadmin."' order by id desc limit 1";
	    	$revisionquote = $this->db->query($sqlq)->row();
	    	if($revisionquote)
	    	$data['revisionno'] = $revisionquote->revisionid;
	    	
	    	$sqlq = "SELECT revisionid, daterequested FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bid->id."' AND purchasingadmin='".$quote->purchasingadmin."' group by revisionid";
	    	$revisiondate = $this->db->query($sqlq)->result();
	    	foreach($revisiondate as $revisedate){	    		
	    		$revisionsid = $revisedate->revisionid;
	    		$bid->$revisionsid = $revisedate->daterequested;
	    	}
	    	
	    }
	   	$data['bid'] = $bid; 
	    
		$items = $draftitems?$draftitems:$quoteitems;
		$data['quoteitems'] = array();
		//echo '<pre>';print_r($items);//die;

		$sqlq = "SELECT itemcheck
				FROM ".$this->db->dbprefix('invitation')." iv 
				WHERE company='".$company->id."' AND purchasingadmin='".$quote->purchasingadmin."' AND invitation='".$key."'
			";
		$quoteinvite = $this->db->query($sqlq)->row();

		if($quoteinvite){
			$quoteitemck = $quoteinvite->itemcheck;
		}else
			$quoteitemck = 0;
		$quoteitemck = 1; // Assigned itemcheck value as 1 by default
		foreach($items as $item)
		{
			$this->db->where('itemid',$item->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			
			$item->companyitem = $companyitem;
			
			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
			
			$item->orgitem = $orgitem;
			
    	    //if($bid && $quoteitemck)
    	    if($quoteitemck)
    	    {
    			$this->db->where('itemid',$item->itemid);
    			$this->db->where('type','Purchasing');
    			$this->db->where('company',$quote->purchasingadmin);
    			$paitem = $this->db->get('companyitem')->row();
    			
    			if($paitem)
    			    $item->attachment = $paitem->filename;
    			else
    			    $item->attachment = '';
    	    }
			else
			{
			    $item->attachment = '';
			}
			//print_r($companyitem);
			if($companyitem)
			{
				$item->itemcode = $companyitem->itemcode;
				$item->itemname = $companyitem->itemname;
				if(!$draftitems) $item->ea = $companyitem->ea;
				$item->showinventorylink = false;
				
			}
			else
			{
			    if(!$item->itemcode)
			        $item->itemcode = $orgitem->itemcode;
			    if(!$item->itemname)
			        $item->itemname = $orgitem->itemname;
				$item->showinventorylink = true;
			}
			$price = $item->ea;
			if(!$draftitems)
			    $item->ea = number_format($item->ea + ($item->ea * $tier/100),2);
			
			$item->totalprice = $item->ea * $item->quantity;
			$item->tiers = array();
			$item->tiers['Tier0'] = number_format($price,2);
			$item->tiers['Tier1'] = number_format($price + ($price * $tiers->tier1/100),2);
			//echo $item->tiers['Tier1'];echo '<br/>';
			$item->tiers['Tier2'] = number_format($price + ($price * $tiers->tier2/100),2);
			//echo $item->tiers['Tier2'];echo '<br/>';
			$item->tiers['Tier3'] = number_format($price + ($price * $tiers->tier3/100),2);
			//echo $item->tiers['Tier3'];echo '<br/>';
			$item->tiers['Tier4'] = number_format($price + ($price * $tiers->tier4/100),2);
			//echo $item->tiers['Tier4'];echo '<br/>';echo '<br/>';echo '<br/>';
			$data['quoteitems'][]=$item;
		}
		//echo '<pre>';print_r($data['quoteitems']);die;
		
		$data['draft'] = $draftitems?1:0;
		
		$data['company'] = $company;
		
		//for export link
		$data['invitekey'] = $key;
		
		$this->db->where('id',$invitation->purchasingadmin);
		$pa = $this->db->get('users')->row();
		$data['purchasingadmin'] = $pa;
		if($print)
		{
			$this->load->template ( '../../templates/front/blank', $data);
			$this->load->view('quote/printquote',$data);
		}
		else
			$this->load->view('quote/quote',$data);
	}
	
	function getpriceqtydetails(){
    	
		$companyid = $_POST['companyid'];
		$itemid = $_POST['itemid'];
		$quantiid = $_POST['quantityid'];
		$priceid = $_POST['priceid'];		
		$purchasingadmin = $_POST['purchaser'];
    	$this->db->where('company',$companyid);
    	$this->db->where('itemid',$itemid);
    	$qtyresult = $this->db->get('qtydiscount')->result();
    	if($qtyresult){
    		$strput = "";
    		$selectbutton2 = "";
    		$istier = 0;
    		$strput .= "<table class='table table-bordered'>";
    		foreach($qtyresult as $qtyres){
    			
    			if(isset($purchasingadmin)){
    				$sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$purchasingadmin' AND company='" . $_POST['companyid'] . "'";


    				$sqltier = "select tierprice from " . $this->db->dbprefix('companyitem') . "
				    where itemid='".$_POST['itemid']."' AND company='" . $_POST['companyid'] . "' AND type = 'Supplier'";

    				$istierprice = $this->db->query($sqltier)->row();
    				if($istierprice){
    					$istier = $istierprice->tierprice;
    				}else
    				$istier = 0;

    				$tier = $this->db->query($sql)->row();
    				if ($tier && $istier)
    				{
    					$tier = $tier->tier;
    					$this->db->where('company', $_POST['companyid']);
    					$pt = $this->db->get('tierpricing')->row();
    					if ($pt)
    					{
    						$deviation = $pt->$tier;
    						$qtyres->price = $qtyres->price + ($qtyres->price * $deviation / 100);
    						$qtyres->price = number_format($qtyres->price, 2);
    					}
    				}
    			}
    			
				$selectbutton2 = "<input type='button' class='btn btn-small' onclick='selectquantity(\"$qtyres->qty\",\"{$quantiid}\",\"{$qtyres->price}\",\"{$priceid}\")' value='Select' data-dismiss='modal'>";
    			$strput .= '<tr >
							 <td style="padding-bottom:9px;" class="col-md-8">'.$qtyres->qty.' or more: </td><td>$'.$qtyres->price.'</td><td>'. $selectbutton2 . '</td></tr>';
    		}
    		if($istier)
    		$strput .= '<tr><td colspan="3" style="text-align:center;"><strong>Tier Price is applied on top of qty. discount</strong></td></tr>';
    		$strput .= "</table>";
    		echo $strput;
    	}else 
    	echo "No Quantity Discount Available";  	

    }
	
	
	public function invitation_export($key,$print='')
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$invitation = $this->quotemodel->getinvitation($key);
		if(!$invitation)
		{
			$message = 'Quote Already Submitted for Review, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		if($company->id != $invitation->company)
		{
			$message = 'Wrong Access.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		
		$quote = $this->quotemodel->getquotebyid($invitation->quote);
		if($this->quotemodel->checkbidcomplete($quote->id))
		{
			$message = 'Bid Already Completed, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		
		
        $sql = "SELECT tier FROM " . $this->db->dbprefix('purchasingtier') . " pt
        		WHERE pt.purchasingadmin='".$quote->purchasingadmin."' AND pt.company='" . $company->id . "'
			";
        $data['patier'] = @$this->db->query($sql)->row()->tier;
		
		
		$quoteitems = $this->quotemodel->getquoteitems($quote->id);
		//print_r($quoteitems);die;
		$originalitems1 = $this->quotemodel->getquoteitems($quote->id);
		$company = $this->quotemodel->getcompanybyid($invitation->company);
		$draftitems = $this->quotemodel->getdraftitems($quote->id,$invitation->company);
		
		$sql = "SELECT tier
				FROM ".$this->db->dbprefix('purchasingtier')." pt 
				WHERE pt.company='".$company->id."' AND pt.purchasingadmin='".$quote->purchasingadmin."'
			";
		$tier = $this->db->query($sql)->row();
		if($tier)
		{
			$tier = $tier->tier;
			$sql = "SELECT *
				FROM ".$this->db->dbprefix('tierpricing')." pt 
				WHERE pt.company='".$company->id."'
			";
			$tiers = $this->db->query($sql)->row();
			$tier = $tiers->$tier;
		}
		else
		{
			$tier = 0;
		}
		//die($tier);
		//echo $sql;
		$admins = $this->db->query($sql)->result();
		
		
		foreach($originalitems1 as $q)
		{
			$originalitems[$q->itemid] = $q;
		}
		$data['originalitems'] = $originalitems;
		//echo '<pre>'; print_r($originalitems);
		
		$this->db->where('company',$company->id);
		$tiers = $this->db->get('tierpricing')->row();
		//print_r($tiers);die;
		$data['tiers'] = $tiers;
		$data['invitation'] = $key;
		$data['quote'] = $quote;
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company->id);
		$bid = $this->db->get('bid')->row();
	    $data['quotenum'] = $bid?$bid->quotenum:'';
	    $data['quotefile'] = $bid?$bid->quotefile:'';
	    $data['expire_date'] = $bid?$bid->expire_date:'';
	     if($bid){
	    	$sqlq = "SELECT revisionid FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bid->id."' AND purchasingadmin='".$quote->purchasingadmin."' order by id desc limit 1";
	    	$revisionquote = $this->db->query($sqlq)->row();
	    	if($revisionquote)
	    	$data['revisionno'] = $revisionquote->revisionid;
	    	
	    	$sqlq = "SELECT revisionid, daterequested FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bid->id."' AND purchasingadmin='".$quote->purchasingadmin."' group by revisionid";
	    	$revisiondate = $this->db->query($sqlq)->result();
	    	foreach($revisiondate as $revisedate){	    		
	    		$revisionsid = $revisedate->revisionid;
	    		$bid->$revisionsid = $revisedate->daterequested;
	    	}
	    	
	    }
	   	$data['bid'] = $bid; 
	    
		$items = $draftitems?$draftitems:$quoteitems;
		$data['quoteitems'] = array();
		//echo '<pre>';print_r($items);//die;

		$sqlq = "SELECT itemcheck
				FROM ".$this->db->dbprefix('invitation')." iv 
				WHERE company='".$company->id."' AND purchasingadmin='".$quote->purchasingadmin."' AND invitation='".$key."'
			";
		$quoteinvite = $this->db->query($sqlq)->row();

		if($quoteinvite){
			$quoteitemck = $quoteinvite->itemcheck;
		}else
			$quoteitemck = 0;
		$quoteitemck = 1; // Assigned itemcheck value as 1 by default
		foreach($items as $item)
		{
			$this->db->where('itemid',$item->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			
			$item->companyitem = $companyitem;
			
			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
			
			$item->orgitem = $orgitem;
			
    	    //if($bid && $quoteitemck)
    	    if($quoteitemck)
    	    {
    			$this->db->where('itemid',$item->itemid);
    			$this->db->where('type','Purchasing');
    			$this->db->where('company',$quote->purchasingadmin);
    			$paitem = $this->db->get('companyitem')->row();
    			
    			if($paitem)
    			    $item->attachment = $paitem->filename;
    			else
    			    $item->attachment = '';
    	    }
			else
			{
			    $item->attachment = '';
			}
			//print_r($companyitem);
			if($companyitem)
			{
				$item->itemcode = $companyitem->itemcode;
				$item->itemname = $companyitem->itemname;
				if(!$draftitems) $item->ea = $companyitem->ea;
				$item->showinventorylink = false;
				
			}
			else
			{
			    if(!$item->itemcode)
			        $item->itemcode = $orgitem->itemcode;
			    if(!$item->itemname)
			        $item->itemname = $orgitem->itemname;
				$item->showinventorylink = true;
			}
			$price = $item->ea;
			if(!$draftitems)
			    $item->ea = number_format($item->ea + ($item->ea * $tier/100),2);
			
			$item->totalprice = $item->ea * $item->quantity;
			$item->tiers = array();
			$item->tiers['Tier0'] = number_format($price,2);
			$item->tiers['Tier1'] = number_format($price + ($price * $tiers->tier1/100),2);
			//echo $item->tiers['Tier1'];echo '<br/>';
			$item->tiers['Tier2'] = number_format($price + ($price * $tiers->tier2/100),2);
			//echo $item->tiers['Tier2'];echo '<br/>';
			$item->tiers['Tier3'] = number_format($price + ($price * $tiers->tier3/100),2);
			//echo $item->tiers['Tier3'];echo '<br/>';
			$item->tiers['Tier4'] = number_format($price + ($price * $tiers->tier4/100),2);
			//echo $item->tiers['Tier4'];echo '<br/>';echo '<br/>';echo '<br/>';
			$data['quoteitems'][]=$item;
		}
		//echo '<pre>';print_r($data['quoteitems']);die;
		
		$data['draft'] = $draftitems?1:0;
		
		$data['company'] = $company;
		
		$this->db->where('id',$invitation->purchasingadmin);
		$pa = $this->db->get('users')->row();
		$data['purchasingadmin'] = $pa;
		
		
		
		//--------------------------------------------------------------------------	
		$originalitems = $data['originalitems'];
		
		if(isset($data['revisionno']))
		{
			$revisionno = $data['revisionno'];	
		}
		
		
		$header[] = array('Reporting Type',	'Bid Invitations', 	'',	'',	'', '', '');			
		
		$header[] = array('','', '','',	'', '', '');	
		
		$header[] = array('PO#',$quote->ponum, '','',	'', '', '');	
		
		$header[] = array('Due',$quote->duedate, '','',	'', '', '');	
		
		
		$header[] = array('Company',$company->title, '','',	'', '', '');	
		$header[] = array('Contact',$company->contact, '','',	'', '', '');	
		
					
		$revision_no  = '';
		if(isset($revisionno))
		{
		 	$revision_no =  $revisionno-1;
		}
		
		$header[] = array('','', '','',	'', '', '');	
		$header[] = array('Number of Revisions',$revision_no, '','',	'', '', '');	
		
		$header[] = array('','', '','',	'', '', '');	
					
		if(isset($revisionno)) 
		{ 
			$quotearr = explode(".",$bid->quotenum);  
						
			$header[] = array('Quote #','Date', '','',	'', '', '');	
				
				
			if(isset($bid->id))
			{ 
			
				$quotearr  = explode(".",$bid->quotenum);  				 
				$rev_quote =  $quotearr[0].".000";
				$rev_date  = '';
				
				if(isset($bid->submitdate))
				{
					$rev_date  =  date("m/d/Y", strtotime($bid->submitdate)); 				
				} 
								
				$header[] = array($rev_quote , $rev_date, '','',	'', '', '');	
			}		
			
				for($i=2;$i<=$revisionno;$i++)
				{ 
				
					
					$rev_quote = $quotearr[0].".00".($i-1);
					
					$rev_date = '';
					
					if(isset($bid->$i))
					{
						 $rev_date = date("m/d/Y", strtotime($bid->$i));
					}
										
					$header[] = array($rev_quote , $rev_date, '','',	'', '', '');	
				}
				
		} 
		
		$header[] = array('','', '','',	'', '', '');	
		$header[] = array('','', '','',	'', '', '');	
			
		
		$patier   = $data['patier'];
		$header[] = array('Tier Level',$patier, '','',	'', '', '');	
		
		$header[] = array('','', '','',	'', '', '');	
		
		$header[] = array('Item Name','Qty', 'Unit','Price',	'Total', 'Date Avail', 'Note');	
		
		foreach($quoteitems as $q)
		{
		
			if(@$q->itemid)
			{
				if(@$originalitems[$q->itemid])
				{		
					$header[] = array($originalitems[$q->itemid]->itemname,$originalitems[$q->itemid]->quantity, $originalitems[$q->itemid]->unit,'$'.$originalitems[$q->itemid]->ea.chr(160), round($originalitems[$q->itemid]->ea * $originalitems[$q->itemid]->quantity,2), $originalitems[$q->itemid]->daterequested, $originalitems[$q->itemid]->notes);
			
				}				
				
				//$header[] = array(htmlspecialchars_decode($q->itemname, ENT_COMPAT), $q->quantity, $q->unit,'$'.$q->ea.chr(160),	$q->totalprice, $q->daterequested, $q->notes);
			}
		}	
				
		createXls('bid_invitations ', $header);  			
		die();	
		
		//===============================================================================	
						
	}
	
	// Inviations PDF	
	public function invitation_pdf($key,$print='')
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$invitation = $this->quotemodel->getinvitation($key);
		if(!$invitation)
		{
			$message = 'Quote Already Submitted for Review, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		if($company->id != $invitation->company)
		{
			$message = 'Wrong Access.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		
		$quote = $this->quotemodel->getquotebyid($invitation->quote);
		if($this->quotemodel->checkbidcomplete($quote->id))
		{
			$message = 'Bid Already Completed, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invitations');
		}
		
		
        $sql = "SELECT tier FROM " . $this->db->dbprefix('purchasingtier') . " pt
        		WHERE pt.purchasingadmin='".$quote->purchasingadmin."' AND pt.company='" . $company->id . "'
			";
        $data['patier'] = @$this->db->query($sql)->row()->tier;
		
		
		$quoteitems = $this->quotemodel->getquoteitems($quote->id);
		//print_r($quoteitems);die;
		$originalitems1 = $this->quotemodel->getquoteitems($quote->id);
		$company = $this->quotemodel->getcompanybyid($invitation->company);
		$draftitems = $this->quotemodel->getdraftitems($quote->id,$invitation->company);
		
		$sql = "SELECT tier
				FROM ".$this->db->dbprefix('purchasingtier')." pt 
				WHERE pt.company='".$company->id."' AND pt.purchasingadmin='".$quote->purchasingadmin."'
			";
		$tier = $this->db->query($sql)->row();
		if($tier)
		{
			$tier = $tier->tier;
			$sql = "SELECT *
				FROM ".$this->db->dbprefix('tierpricing')." pt 
				WHERE pt.company='".$company->id."'
			";
			$tiers = $this->db->query($sql)->row();
			$tier = $tiers->$tier;
		}
		else
		{
			$tier = 0;
		}
		//die($tier);
		//echo $sql;
		$admins = $this->db->query($sql)->result();
		
		
		foreach($originalitems1 as $q)
		{
			$originalitems[$q->itemid] = $q;
		}
		$data['originalitems'] = $originalitems;
		//echo '<pre>'; print_r($originalitems);
		
		$this->db->where('company',$company->id);
		$tiers = $this->db->get('tierpricing')->row();
		//print_r($tiers);die;
		$data['tiers'] = $tiers;
		$data['invitation'] = $key;
		$data['quote'] = $quote;
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company->id);
		$bid = $this->db->get('bid')->row();
	    $data['quotenum'] = $bid?$bid->quotenum:'';
	    $data['quotefile'] = $bid?$bid->quotefile:'';
	    $data['expire_date'] = $bid?$bid->expire_date:'';
	     if($bid){
	    	$sqlq = "SELECT revisionid FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bid->id."' AND purchasingadmin='".$quote->purchasingadmin."' order by id desc limit 1";
	    	$revisionquote = $this->db->query($sqlq)->row();
	    	if($revisionquote)
	    	$data['revisionno'] = $revisionquote->revisionid;
	    	
	    	$sqlq = "SELECT revisionid, daterequested FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bid->id."' AND purchasingadmin='".$quote->purchasingadmin."' group by revisionid";
	    	$revisiondate = $this->db->query($sqlq)->result();
	    	foreach($revisiondate as $revisedate){	    		
	    		$revisionsid = $revisedate->revisionid;
	    		$bid->$revisionsid = $revisedate->daterequested;
	    	}
	    	
	    }
	   	$data['bid'] = $bid; 
	    
		$items = $draftitems?$draftitems:$quoteitems;
		$data['quoteitems'] = array();
		//echo '<pre>';print_r($items);//die;

		$sqlq = "SELECT itemcheck
				FROM ".$this->db->dbprefix('invitation')." iv 
				WHERE company='".$company->id."' AND purchasingadmin='".$quote->purchasingadmin."' AND invitation='".$key."'
			";
		$quoteinvite = $this->db->query($sqlq)->row();

		if($quoteinvite){
			$quoteitemck = $quoteinvite->itemcheck;
		}else
			$quoteitemck = 0;
		$quoteitemck = 1; // Assigned itemcheck value as 1 by default
		foreach($items as $item)
		{
			$this->db->where('itemid',$item->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			
			$item->companyitem = $companyitem;
			
			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
			
			$item->orgitem = $orgitem;
			
    	    //if($bid && $quoteitemck)
    	    if($quoteitemck)
    	    {
    			$this->db->where('itemid',$item->itemid);
    			$this->db->where('type','Purchasing');
    			$this->db->where('company',$quote->purchasingadmin);
    			$paitem = $this->db->get('companyitem')->row();
    			
    			if($paitem)
    			    $item->attachment = $paitem->filename;
    			else
    			    $item->attachment = '';
    	    }
			else
			{
			    $item->attachment = '';
			}
			//print_r($companyitem);
			if($companyitem)
			{
				$item->itemcode = $companyitem->itemcode;
				$item->itemname = $companyitem->itemname;
				if(!$draftitems) $item->ea = $companyitem->ea;
				$item->showinventorylink = false;
				
			}
			else
			{
			    if(!$item->itemcode)
			        $item->itemcode = $orgitem->itemcode;
			    if(!$item->itemname)
			        $item->itemname = $orgitem->itemname;
				$item->showinventorylink = true;
			}
			$price = $item->ea;
			if(!$draftitems)
			    $item->ea = number_format($item->ea + ($item->ea * $tier/100),2);
			
			$item->totalprice = $item->ea * $item->quantity;
			$item->tiers = array();
			$item->tiers['Tier0'] = number_format($price,2);
			$item->tiers['Tier1'] = number_format($price + ($price * $tiers->tier1/100),2);
			//echo $item->tiers['Tier1'];echo '<br/>';
			$item->tiers['Tier2'] = number_format($price + ($price * $tiers->tier2/100),2);
			//echo $item->tiers['Tier2'];echo '<br/>';
			$item->tiers['Tier3'] = number_format($price + ($price * $tiers->tier3/100),2);
			//echo $item->tiers['Tier3'];echo '<br/>';
			$item->tiers['Tier4'] = number_format($price + ($price * $tiers->tier4/100),2);
			//echo $item->tiers['Tier4'];echo '<br/>';echo '<br/>';echo '<br/>';
			$data['quoteitems'][]=$item;
		}
		//echo '<pre>';print_r($data['quoteitems']);die;
		
		$data['draft'] = $draftitems?1:0;
		
		$data['company'] = $company;
		
		$this->db->where('id',$invitation->purchasingadmin);
		$pa = $this->db->get('users')->row();
		$data['purchasingadmin'] = $pa;
		
		
		
		//--------------------------------------------------------------------------	
		$originalitems = $data['originalitems'];
		
		if(isset($data['revisionno']))
		{
			$revisionno = $data['revisionno'];	
		}
		
		
		$header[] = array('Reporting Type',	'Bid Invitations', 	'',	'',	'', '', '');			
		
		$header[] = array('','', '','',	'', '', '');	
		
		$header[] = array('<b>PO#</b>',$quote->ponum, '','',	'', '', '');	
		
		$header[] = array('<b>Due</b>',$quote->duedate, '','',	'', '', '');	
		
		
		$header[] = array('<b>Company</b>',$company->title, '','',	'', '', '');	
		$header[] = array('<b>Contact</b>',$company->contact, '','',	'', '', '');	
		
					
		$revision_no  = '';
		if(isset($revisionno))
		{
		 	$revision_no =  $revisionno-1;
		}
		
		$header[] = array('','', '','',	'', '', '');	
		$header[] = array('<b>Number of Revisions</b>',$revision_no, '','',	'', '', '');	
		
		$header[] = array('','', '','',	'', '', '');	
					
		if(isset($revisionno)) 
		{ 
			$quotearr = explode(".",$bid->quotenum);  
						
			$header[] = array('<b>Quote #</b>','<b>Date</b>', '','',	'', '', '');	
				
				
			if(isset($bid->id))
			{ 
			
				$quotearr  = explode(".",$bid->quotenum);  				 
				$rev_quote =  $quotearr[0].".000";
				$rev_date  = '';
				
				if(isset($bid->submitdate))
				{
					$rev_date  =  date("m/d/Y", strtotime($bid->submitdate)); 				
				} 
								
				$header[] = array($rev_quote , $rev_date, '','',	'', '', '');	
			}		
			
				for($i=2;$i<=$revisionno;$i++)
				{ 
				
					
					$rev_quote = $quotearr[0].".00".($i-1);
					
					$rev_date = '';
					
					if(isset($bid->$i))
					{
						 $rev_date = date("m/d/Y", strtotime($bid->$i));
					}
										
					$header[] = array($rev_quote , $rev_date, '','',	'', '', '');	
				}
				
		} 
		
		$header[] = array('','', '','',	'', '', '');	
		$header[] = array('','', '','',	'', '', '');	
			
		
		$patier   = $data['patier'];
		$header[] = array('<b>Tier Level</b>',$patier, '','',	'', '', '');	
		
		$header[] = array('','', '','',	'', '', '');	
		
		$header[] = array('<b>Item Name</b>','<b>Qty</b>', '<b>Unit</b>','<b>Price</b>',	'<b>Total</b>', '<b>Date Avail</b>', '<b>Note</b>');	
		
		foreach($quoteitems as $q)
		{
		
			if(@$q->itemid)
			{
				if(@$originalitems[$q->itemid])
				{		
					$header[] = array($originalitems[$q->itemid]->itemname,$originalitems[$q->itemid]->quantity, $originalitems[$q->itemid]->unit,'$'.$originalitems[$q->itemid]->ea.chr(160), round($originalitems[$q->itemid]->ea * $originalitems[$q->itemid]->quantity,2), $originalitems[$q->itemid]->daterequested, $originalitems[$q->itemid]->notes);
			
				}				
				
				//$header[] = array(htmlspecialchars_decode($q->itemname, ENT_COMPAT), $q->quantity, $q->unit,'$'.$q->ea.chr(160),	$q->totalprice, $q->daterequested, $q->notes);
			}
		}	
				
		 
		$headername = "BID INVITATIONS";
    	createOtherPDF2('bid_invitations', $header,$headername);
    	die();
		
		//===============================================================================	
						
	}
	

	
	
	
	public function placebid()
	{
		$revisionid=1;
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!$_POST)
			die;
		//echo '<pre>'; print_r($_POST); print_r($_FILES);die;
		$invitation = $this->quotemodel->getinvitation($_POST['invitation']);
		
		if(!$invitation)
		{
			die('Quote Already Submitted for Review, Thank You.');
		}
		$quote = $this->quotemodel->getquotebyid($invitation->quote);
		$draftitems = $this->quotemodel->getdraftitems($quote->id,$invitation->company);
		if($draftitems)
		{
			$zeroerror = false;
			$nobids = true;
			foreach($draftitems as $item)
			{
			    $bidid = $item->bid;
				$key = $item->id;
				$postkey = 'ea'.$key;
				if(@$_POST['substitute'.$key] == 1)
					$postkey = 's_'.$postkey;
				if(@$_POST['nobid'.$key] != 1 && @$_POST[$postkey] == 0)
					$zeroerror = true;
				if(@$_POST['nobid'.$key] != 1)
					$nobids = false;
			}
			if($nobids)
			{	
				$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">You Cannot place a bid without any items</div></div></div>');	
				redirect('quote/invitation/'.$_POST['invitation']);
				die;
			}
			if($zeroerror)
			{
				$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">You Cannot bid with price 0.</div></div></div>');
				redirect('quote/invitation/'.$_POST['invitation']);
				die;
			}
			
			$sqlq = "SELECT revisionid FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bidid."' AND purchasingadmin='".$invitation->purchasingadmin."' order by id desc limit 1";
			$revisionquote = $this->db->query($sqlq)->row();
			if($revisionquote)
			$revisionid = $revisionquote->revisionid+1;
			else
			$revisionid = 1;
			
			if($revisionid > 1){			
				if(isset($_POST['quotenum'])){
					$quotearr = explode(".",$_POST['quotenum']);
					if(count($quotearr)>1){
					$number = sprintf('%03d',$quotearr[1]+1);
					$bidarray['quotenum'] = $quotearr[0].".".$number;
					}else {
						$bidarray['quotenum'] = "";
					}
				}else
					$bidarray['quotenum'] = "";
			}
			else 
				$bidarray['quotenum'] = $_POST['quotenum'];
		
				$bidarray['expire_date'] = date("Y-m-d",  strtotime($_POST['expire_date']));
				
    		if(is_uploaded_file($_FILES['quotefile']['tmp_name']))
    		{
    			$ext = end(explode('.', $_FILES['quotefile']['name']));
    			$nfn = md5(date('u').uniqid()).'.'.$ext;
    			if(move_uploaded_file($_FILES['quotefile']['tmp_name'], "uploads/quotefile/".$nfn))
    			{
    				$bidarray['quotefile'] = $nfn;
    			}
    		}
    		//echo $bidid.'-'.$quote->id.'<pre>'; print_r($bidarray);die;
			$this->db->where('id', $bidid);
			$this->db->update('bid',$bidarray);
						
			foreach($draftitems as $item)
			{
				$bidid = $item->bid;
				$updatearray = array();
				$key = $item->id;
				while(list($k,$v) = each($item))
				{
					if($k != 'invitation' && $k != 'id' && $k != 'bid' && $k != 'substitute' && $k != 'received' && $k != 'purchasingadmin')
					{
						$postkey = $k.$key;
						if(@$_POST['substitute'.$key] == 1 && $k != 'substitute')
							$postkey = 's_'.$postkey;
						$updatearray[$k] = @$_POST[$postkey];
					}
				}
				$item = (array)$item;
				$updatearray['totalprice'] = $updatearray['quantity'] * $updatearray['ea'];
				$updatearray['substitute'] = @$_POST['substitute'.$key]?@$_POST['substitute'.$key]:0;
				
				$this->quotemodel->db->where('id',$key);
				if(@$_POST['nobid'.$key])
				{
					$this->quotemodel->db->delete('biditem');
				}
				else
				{
					$this->quotemodel->db->update('biditem',$updatearray);
					$this->quotemodel->saveminimum($invitation->company,$invitation->purchasingadmin,$updatearray['itemid'],$updatearray['itemcode'],$updatearray['itemname'],$updatearray['ea'],$updatearray['substitute']);
					
					if($revisionquote){ 
						 $updatearray['daterequested'] = date('m/d/Y');	
                         $updatearray['purchasingadmin'] = $invitation->purchasingadmin; 
                         $updatearray['bid'] = $bidid; 
                         $updatearray['revisionid'] = $revisionid; 
                         $this->quotemodel->db->insert('quoterevisions',$updatearray); 
                          
                     } 
				}
			}
		}
		else
		{
			$items = $this->quotemodel->getquoteitems($quote->id);
			//echo '<pre>'; print_r($items);die;
			//echo '<pre>';
			$zeroerror = false;
			$nobids = true;
			foreach($items as $item)
			{
				$key = $item->id;
				$postkey = 'ea'.$key;
				if(@$_POST['substitute'.$key] == 1)
					$postkey = 's_'.$postkey;
				if(@$_POST['nobid'.$key] != 1 && @$_POST[$postkey] == 0)
					$zeroerror = true;
				if(@$_POST['nobid'.$key] != 1)
					$nobids = false;
			}
			if($nobids)
			{
				$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">You Cannot place a bid without any items.</div></div></div>');
				redirect('quote/invitation/'.$_POST['invitation']);
				die;
			}
			if($zeroerror)
			{
				$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">You Cannot bid with price 0.</div></div></div>');
				redirect('quote/invitation/'.$_POST['invitation']);
				die;
			}
			$bidarray = array('quote'=>$invitation->quote,'company'=>$invitation->company,'submitdate'=>date('Y-m-d'));
			
			$bidarray['quotenum'] = $_POST['quotenum'];
			$bidarray['expire_date'] = date('Y-m-d',  strtotime($_POST['expire_date']));
			$bidarray['draft'] = $_POST['draft'];
			$bidarray['purchasingadmin'] = $invitation->purchasingadmin;
    		if(is_uploaded_file($_FILES['quotefile']['tmp_name']))
    		{
    			$ext = end(explode('.', $_FILES['quotefile']['name']));
    			$nfn = md5(date('u').uniqid()).'.'.$ext;
    			if(move_uploaded_file($_FILES['quotefile']['tmp_name'], "uploads/quotefile/".$nfn))
    			{
    				$bidarray['quotefile'] = $nfn;
    			}
    		}
    		//echo '<pre>'; print_r($bidarray);
			$this->db->insert('bid',$bidarray);
			$bidid = $this->db->insert_id();
			
			foreach($items as $item)
			{
			
				$insertarray = array();
				$insertarray['bid'] = $bidid;
				
				$key = $item->id;
				while(list($k,$v) = each($item))
				{
					if($k != 'invitation' && $k != 'id' && $k != 'quote' && $k != 'company'&& $k != 'purchasingadmin')
					{
						$postkey = $k.$key;
						if(@$_POST['substitute'.$key] == 1 && $k != 'substitute')
							$postkey = 's_'.$postkey;
						
						$insertarray[$k] = $_POST[$postkey];
					}
				}
				$item = (array)$item;
				$insertarray['substitute'] = @$_POST['substitute'.$key]?@$_POST['substitute'.$key]:0;
				$insertarray['totalprice'] = $insertarray['quantity'] * $insertarray['ea'];
				$insertarray['purchasingadmin'] = $invitation->purchasingadmin;
				$insertarray['ismanual'] = @$_POST['ismanual'.$key]?@$_POST['ismanual'.$key]:0;
				if(!@$_POST['nobid'.$key])
				{
				    
					//print_r($insertarray);//die;
					$this->quotemodel->db->insert('biditem',$insertarray);
					
					//if(!$insertarray['substitute'])
					//{
						$this->quotemodel->saveminimum($invitation->company,$invitation->purchasingadmin,$insertarray['itemid'],$insertarray['itemcode'],$insertarray['itemname'],$insertarray['ea'],$insertarray['substitute']);
					//}
					$insertarray['revisionid']=1; 
                    $this->quotemodel->db->insert('quoterevisions',$insertarray); 
				}
			}
			//echo($bidid.'<br/>');
		}
		
		if($bidid)
		{
    	    $bid = $this->db->where('id',$bidid)->get('bid')->row();
    	    //print_r($bid);print_r($company);die;
    	    
    	    if(!$bid)
    	        redirect('quote');
    	    if($bid->company != $company->id)
    	        redirect('quote');
            if($bid->quotefile !="" && (file_exists('./uploads/quotefile/'.$bid->quotefile) && !is_dir('./uploads/quotefile/'.$bid->quotefile)))
            {
                $attachment = "uploads/quotefile/".$bid->quotefile;
            }
            
    	    $quote = $this->quotemodel->getquotebyid($bid->quote);
    	    $biditems = $this->quotemodel->getdraftitems($bid->quote, $company->id);
    	    
    	    $sqlq = "SELECT revisionid FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bidid."' AND purchasingadmin='".$quote->purchasingadmin."' order by id desc limit 1";
			$revisionquote = $this->db->query($sqlq)->row();
			if($revisionquote)
			$revisionid = $revisionquote->revisionid+1;
			else
			$revisionid = 1;
    	    
    	    $biditems2 = $this->quotemodel->getrevisiondraftitems($bid->quote, $company->id,$revisionid);
    	    
    	    if(@$biditems2){
    	    	$bid->submitdate = $biditems2[0]->daterequested;
    	    }
    	    
    	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
    	    $taxpercent = $settings->taxpercent;
    	    
    		ob_start();
    	   	include $this->config->config['base_dir'].'application/views/quote/quotehtml.php';
    	   	$html = ob_get_clean();
		    
    		$settings = (array)$this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
    	    $this->load->library('email');

    	    $config['charset'] = 'utf-8';
    	    $config['mailtype'] = 'html';
    	    $this->email->initialize($config);
    		//$this->email->clear(true);
            $to = array();
            $this->email->from($company->primaryemail);
    		$pa = $this->db->where('id',$quote->purchasingadmin)->get('users')->row();
            $to[] = $pa->email;
            $to[] = $settings['adminemail'];
            $sql = "SELECT u.email FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('quoteuser')." qu
    	        	WHERE qu.userid=u.id AND qu.quote=".$quote->id;
            $purchaseusers = $this->db->query($sql)->result();
            foreach($purchaseusers as $pu)
            {
            	$to[] = $pu->email;
            }
            $to = implode(',',$to);
            $this->email->to($to); 
            $data['email_body_title'] = "Dear Admin";
    		$data['email_body_content'] = "This is a notification of bid details by ".$company->title." for PO# ".$quote->ponum.".<br/><br/>
    		  	Please find the details below:<br/><br/>
    		  	$html
    		    ";
    		$loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    		//echo($to.'<br/>');
    		//echo $body;
           	$this->email->subject('Bid Notification for PO# '.$quote->ponum. " by ".$company->title);
            $this->email->message($send_body);	
            if(isset($attachment)) { 
                $this->email->attach($attachment);
            }
            $this->email->set_mailtype("html");
            $this->email->send();
		}
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">Quote Submitted to Company. Pending Award. You can return at any time before winner is awarded to edit your quote.</div></div></div>');
		redirect('quote/invitations','refresh');
	}
	
	function viewbid($bidid)
	{		
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		//echo $bidid;die;
	    $bid = $this->db->where('id',$bidid)->get('bid')->row();
	    //print_r($bid);print_r($company);die;
	    
	    if(!$bid)
	        redirect('quote');
	    if($bid->company != $company->id)
	        redirect('quote');
	    $quote = $this->quotemodel->getquotebyid($bid->quote);
	    $biditems = $this->quotemodel->getdraftitems($bid->quote, $company->id);
	    
	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
	    $taxpercent = $settings->taxpercent;
	    
		ob_start();
	   	include $this->config->config['base_dir'].'application/views/quote/quotehtml.php';
	   	$html = ob_get_clean();
	   	
	   	header('Content-Description: File Transfer');
        header('Content-type: application/html');
        header('Content-Disposition: attachment; filename="quote.html"');
	   	echo $html;die;
	}
	
	function viewquote($qid)
	{		
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			    
	    if(!$qid)
	        redirect('quote');
	    
	    $quote = $this->quotemodel->getquotebyid($qid);
	    $quoteitems = $this->quotemodel->getquoteitems($qid);
	    
	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
	    $taxpercent = $settings->taxpercent;
	    
		ob_start();
	   	include $this->config->config['base_dir'].'application/views/quote/originalquotehtml.php';
	   	$html = ob_get_clean();
	   	
	   	header('Content-Description: File Transfer');
        header('Content-type: application/html');
        header('Content-Disposition: attachment; filename="quote.html"');
	   	echo $html;die;
	}
	
	function viewbids($bidid,$revisionid)
	{
		$company = $this->session->userdata('company');
		if(!$company)
		redirect('company/login');
		//echo $bidid;die;
		$bid = $this->db->where('id',$bidid)->get('bid')->row();
		//print_r($bid);print_r($company);die;

		if(!$bid)
		redirect('quote');
		if($bid->company != $company->id)
		redirect('quote');
		$quote = $this->quotemodel->getquotebyid($bid->quote);
		$biditems = $this->quotemodel->getrevisiondraftitems($bid->quote, $company->id,$revisionid);
		if(@$biditems){
			$bid->submitdate = $biditems[0]->daterequested;
		}
		$settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
		$taxpercent = $settings->taxpercent;

		ob_start();
		include $this->config->config['base_dir'].'application/views/quote/quotehtml.php';
		$html = ob_get_clean();

		header('Content-Description: File Transfer');
		header('Content-type: application/html');
		header('Content-Disposition: attachment; filename="quote.html"');
		echo $html;die;
	}
	
	////////// BACKTRACK

	
	function backtracks()
	{		
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$bcks = $this->quotemodel->getBacktracks($company->id);
		$backtracks = array();
		foreach($bcks as $bck)
		{
			$backtracks[]=$bck;
		}
		//echo '<pre>';print_r($backtracks);die;
		$this->db->select($this->db->dbprefix('users.').'*');
		$this->db->where('usertype_id',2);
		$this->db->from('users')->join('network',"users.id=network.purchasingadmin")->where('network.company',$company->id);
		$data['purchasingadmins'] = $this->db->get()->result();
		$data['company'] = $company;
		$data['backtracks'] = $backtracks;
		$this->load->view('quote/backtracks',$data);
	}
	
	public function viewbacktrack_export($quote)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$backtrack = $this->quotemodel->getBacktrackDetails($quote,$company->id);
		
		$quote = $this->quotemodel->getquotebyid($quote);
		$award = $this->quotemodel->getawardedbid($quote->id);
		
		$awardeditems = $this->quotemodel->getawardeditems($award->id,$company->id);
		$data['awardeditems'] = array();
		foreach($awardeditems as $item)
		{
			$this->db->where('itemid',$item->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
				$item->itemcode = $companyitem->itemcode;
				$item->itemname = $companyitem->itemname;
			}
			
			$item->etalog = $this->db->where('company',$company->id)
                			->where('quote',$quote->id)
                			->where('itemid',$item->itemid)
                			->get('etalog')->result();
			
			$data['awardeditems'][] = $item;
		}
		//echo '<pre>';print_r($backtrack);die;
		$data['backtrack'] = $backtrack;
		$data['company'] = $company;
		$data['quote'] = $quote;
		
		$this->db->where('id',$quote->purchasingadmin);
		$data['pa'] = $this->db->get('users')->row();
		
		$this->load->view('quote/backtrack',$data);
		
		
		//=========================================================================================
								
		$header[] = array('Report type' , 'Bid Progress','' , '' , ' ' , ' ' , ' ','');
		
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		
		
		$recsum =0;
		$qntsum =0;
		foreach($backtrack['items'] as $ai)
		{
			$recsum = $recsum + $ai->received;
			$qntsum = $qntsum + $ai->quantity;
			//print_r($ai);die;
		}
		if($qntsum==0) $per=0;
		else $per = number_format(($recsum/$qntsum)*100,2);
		$per .='%';
			
					
		
		$header[] = array('Items received' , $per.chr(160),'' , '' , ' ' , ' ' , ' ','');
		
		
		$header[] = array('PO#' ,$quote->ponum,'' , '' , ' ' , ' ' , ' ','');
		
		$header[] = array('Company' ,$company->title,'' , '' , ' ' , ' ' , ' ','');
		
		$header[] = array('Contact' ,$company->contact,'' , '' , ' ' , ' ' , ' ','');
			
	
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		
		
		
		$header[] = array('Item Name' , 'Qty. Req','Qty. Due' , 'Unit' , 'Price EA' , 'Total Price' , 'Date Available','Notes');
		
		foreach($backtrack['items'] as $q)
		{
			$due_quantity = $q->quantity - $q->received;
				
			$total_quantity = round($q->ea * ($q->quantity - $q->received), 2);
				
			$header[] = array(htmlentities($q->itemname) , $q->quantity,$due_quantity , $q->unit , '$'.$q->ea.chr(160) , '$'.$total_quantity.chr(160) , $q->daterequested, $q->notes);
		
		}
		
		
		
		
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		$header[] = array('ETA Update History' , '','' , '' , ' ' , ' ' , ' ','');
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		
		$header[] = array('Date' , 'Notes','Updated' , '' , ' ' , ' ' , ' ','');
		
		
		$i=0;
		foreach($q->etalog as $l)		
		{
			if($q->etalog)
			{
				$date_1 = '';
				if ($i==0)
				{
					$date_1 =  $l->daterequested;
				}else{ 
					$date_1 =  "changed from ".$olddate." to ".$l->daterequested;
				}	
				$header[] = array($date_1 , $l->notes,date("m/d/Y", strtotime($l->updated)) , '' , ' ' , ' ' , ' ','');
				
				$i++; $olddate = $l->daterequested; 
			}		
		}
				
		
		createXls('backtrack_report', $header);  			
		die();	
		
		//===============================================================================
		
				
		
	}
	
	// Backtrack PDF
	public function viewbacktrack_pdf($quote)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$backtrack = $this->quotemodel->getBacktrackDetails($quote,$company->id);
		
		$quote = $this->quotemodel->getquotebyid($quote);
		$award = $this->quotemodel->getawardedbid($quote->id);
		
		$awardeditems = $this->quotemodel->getawardeditems($award->id,$company->id);
		$data['awardeditems'] = array();
		foreach($awardeditems as $item)
		{
			$this->db->where('itemid',$item->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
				$item->itemcode = $companyitem->itemcode;
				$item->itemname = $companyitem->itemname;
			}
			
			$item->etalog = $this->db->where('company',$company->id)
                			->where('quote',$quote->id)
                			->where('itemid',$item->itemid)
                			->get('etalog')->result();
			
			$data['awardeditems'][] = $item;
		}
		//echo '<pre>';print_r($backtrack);die;
		$data['backtrack'] = $backtrack;
		$data['company'] = $company;
		$data['quote'] = $quote;
		
		$this->db->where('id',$quote->purchasingadmin);
		$data['pa'] = $this->db->get('users')->row();
		
		$this->load->view('quote/backtrack',$data);
		
		
		//=========================================================================================
								
		$header[] = array('Report type' , 'Bid Progress','' , '' , ' ' , ' ' , ' ','');
		
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		
		
		$recsum =0;
		$qntsum =0;
		foreach($backtrack['items'] as $ai)
		{
			$recsum = $recsum + $ai->received;
			$qntsum = $qntsum + $ai->quantity;
			//print_r($ai);die;
		}
		if($qntsum==0) $per=0;
		else $per = number_format(($recsum/$qntsum)*100,2);
		$per .='%';
			
					
		
		$header[] = array('<b>Items received</b>' , $per.chr(160),'' , '' , ' ' , ' ' , ' ','');
		
		
		$header[] = array('<b>PO#</b>' ,$quote->ponum,'' , '' , ' ' , ' ' , ' ','');
		
		$header[] = array('<b>Company</b>' ,$company->title,'' , '' , ' ' , ' ' , ' ','');
		
		$header[] = array('<b>Contact</b>' ,$company->contact,'' , '' , ' ' , ' ' , ' ','');
			
	
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		
		
		
		$header[] = array('<b>Item Name</b>' , '<b>Qty. Req</b>','<b>Qty. Due</b>' , '<b>Unit</b>' , '<b>Price EA</b>' , '<b>Total Price</b>' , '<b>Date Available</b>','<b>Notes</b>');
		
		foreach($backtrack['items'] as $q)
		{
			$due_quantity = $q->quantity - $q->received;
				
			$total_quantity = round($q->ea * ($q->quantity - $q->received), 2);
				
			$header[] = array(htmlentities($q->itemname) , $q->quantity,$due_quantity , $q->unit , '$'.$q->ea.chr(160) , '$'.$total_quantity.chr(160) , $q->daterequested, $q->notes);
		
		}
		
		
		
		
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		$header[] = array('<b>ETA Update History</b>' , '','' , '' , ' ' , ' ' , ' ','');
		$header[] = array('' , '','' , '' , ' ' , ' ' , ' ','');
		
		$header[] = array('<b>Date</b>' , '<b>Notes</b>','<b>Updated</b>' , '' , ' ' , ' ' , ' ','');
		
		
		$i=0;
		foreach($q->etalog as $l)		
		{
			if($q->etalog)
			{
				$date_1 = '';
				if ($i==0)
				{
					$date_1 =  $l->daterequested;
				}else{ 
					$date_1 =  "changed from ".$olddate." to ".$l->daterequested;
				}	
				$header[] = array($date_1 , $l->notes,date("m/d/Y", strtotime($l->updated)) , '' , ' ' , ' ' , ' ','');
				
				$i++; $olddate = $l->daterequested; 
			}		
		}
				
		
		 
		$headername = "BACK TRACK ORDER";
    	createOtherPDF2('backtrack_report', $header,$headername);
    	die();
		//===============================================================================
		
				
		
	}
	
		
		
	public function viewbacktrack($quote)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$backtrack = $this->quotemodel->getBacktrackDetails($quote,$company->id);
		
		$quote = $this->quotemodel->getquotebyid($quote);
		$award = $this->quotemodel->getawardedbid($quote->id);
		
		$awardeditems = $this->quotemodel->getawardeditems($award->id,$company->id);
		$data['awardeditems'] = array();
		if($awardeditems){
			foreach($awardeditems as $item)
			{
				$this->db->where('itemid',$item->itemid);
				$this->db->where('type','Supplier');
				$this->db->where('company',$company->id);
				$companyitem = $this->db->get('companyitem')->row();
				if($companyitem)
				{
					$item->itemcode = $companyitem->itemcode;
					$item->itemname = $companyitem->itemname;
				}

				$item->etalog = $this->db->where('company',$company->id)
				->where('quote',$quote->id)
				->where('itemid',$item->itemid)
				->get('etalog')->result();

				$pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quote->id)->where('company',$company->id)
			                        ->where('itemid',$item->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;
				
				$data['awardeditems'][] = $item;
			}
		}
		$data['pendingshipments'] = $pendingshipments;
		$data['backtrack'] = $backtrack;
		$data['company'] = $company;
		$data['quote'] = $quote;
		$data['quoteid'] = $quote;
		$this->db->where('id',$quote->purchasingadmin);
		$data['pa'] = $this->db->get('users')->row();
		
		$this->load->view('quote/backtrack',$data);
	}
	
	public function updateeta($quote)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$backtrack = $this->quotemodel->getBacktrackDetails($quote,$company->id);
		$quote = $backtrack['quote'];
		$emailitems = '<table>';
		$emailitems.= '<tr>';
		$emailitems.= '<th>Item</th>';
		$emailitems.= '<th>Date</th>';
		$emailitems.= '<th>Notes</th>';
		$emailitems.= '</tr>';
		foreach($backtrack['items'] as $q)
		{
			if($q->company == $company->id)
			{
				$etacount = count($q->etalog);
				if(isset($q->etalog[$etacount-1]->daterequested) && $q->etalog[$etacount-1]->daterequested!="")
				$reqdate = $q->etalog[$etacount-1]->daterequested;
				else 
				$reqdate = $q->quotedaterequested->daterequested;
				
				if(isset($_POST['daterequested'.$q->id]))
				$postdate = $_POST['daterequested'.$q->id];
				else 
				$postdate = "";
				
				if(isset($_POST['notes'.$q->id]))
				$postnote = $_POST['notes'.$q->id];
				else 
				$postnote = "";
				
        		$emailitems.= '<tr>';
        		$emailitems.= '<td>'.$q->itemname.'</td>';
        		$emailitems.= '<td>changed from '.$reqdate.' to '.$postdate.'</td>';
        		$emailitems.= '<td>'.$postnote.'</td>';
        		$emailitems.= '</tr>';
        		
				$updatearray = array(
					'daterequested'=>$_POST['daterequested'.$q->id],
					'notes'=>$_POST['notes'.$q->id],
				);
				//print_r($updatearray);die;
				$this->model->db->where('id',$q->id);
				$this->model->db->update('awarditem',$updatearray);
				
				$log = array();
				$log['quote'] = $quote->id;
				$log['purchasingadmin'] = $quote->purchasingadmin;
				$log['company'] = $company->id;
				$log['itemid'] = $q->itemid;
				$log['daterequested'] = $_POST['daterequested'.$q->id];
				$log['notes'] = $_POST['notes'.$q->id];
				$log['updated'] = date('Y-m-d');
				$this->db->insert('etalog',$log);
			}
		}
		$emailitems .= '</table>';
		
		$settings = (array)$this->settings_model->get_setting_by_admin ($q->purchasingadmin);
	    $this->load->library('email');
	    $config['charset'] = 'utf-8';
	    $config['mailtype'] = 'html';
	    $this->email->initialize($config);
		//$this->email->clear(true);
        $this->email->from($settings['adminemail'], "Administrator");
        $this->email->to($settings['adminemail']); 
        $sql = "SELECT u.email FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('quoteuser')." qu
	        	WHERE qu.userid=u.id AND qu.quote=".$quote->id;
        $purchaseusers = $this->db->query($sql)->result();
        $pa = $this->db->where('id',$q->purchasingadmin)->get('users')->row();
        if($pa)
        $this->email->cc($pa->email);
        foreach($purchaseusers as $pu)
        {
        	$this->email->cc($pu->email);
        }
        $data['email_body_title'] = "Dear Admin";
		$data['email_body_content'] = "ETA has been updated by ".$company->title." for PO# ".$quote->ponum.".<br/><br/>
		  	Please find the details below:<br/><br/>
		  	$emailitems
		    ";
		$loaderEmail = new My_Loader();
        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
       	$this->email->subject('Backorder update for PO# '.$quote->ponum. " by ".$company->title);
        $this->email->message($send_body);	
        $this->email->set_mailtype("html");
        $this->email->send();
		
        $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">ETA Updated.</div></div></div>');
		redirect('quote/backtracks');
	}
	
	public function backtrack($key,$print='')
	{
		$invitation = $this->quotemodel->getbacktrack($key);
		if(!$invitation)
		{
			$message = 'Invalid Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/backtracks');
		}
		else 
		{
	        redirect('quote/viewbacktrack/'.$invitation->quote);
		}
		$quote = $this->quotemodel->getquotebyid($invitation->quote);
		$awarded = $this->quotemodel->getawardedbid($invitation->quote);
		if($awarded->status == 'complete')
		{
			$message = 'Outdated Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/backtracks');
		}
	
		if(!$awarded->items)
		{
			die;
		}
		
		$company = $this->quotemodel->getcompanybyid($invitation->company);
	
		$data['invitation'] = $key;
		$data['quote'] = $quote;
		$data['awarded'] = $awarded;		
		$data['company'] = $company;
		if($print)
			$this->load->view('quote/printbacktrack',$data);
		else
			$this->load->view('quote/backtrack',$data);
	}

	
	public function savebacktrack()
	{
		$key = $_POST['invitation'];
		$invitation = $this->quotemodel->getbacktrack($key);
		if(!$invitation)
		{
			$message = 'Invalid Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/backtracks');
		}
		$quote = $this->quotemodel->getquotebyid($invitation->quote);
		$company = $this->quotemodel->getcompanybyid($invitation->company);
		$awarded = $this->quotemodel->getawardedbid($invitation->quote);
		if($awarded->status == 'complete')
		{
			$message = 'Outdated Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/backtracks');
		}
		if(!$awarded->items)
		{
			$message = 'Invalid Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/backtracks');
		}
		
		foreach($awarded->items as $q)
		{
			if($q->company == $invitation->company)
			{
				$updatearray = array(
					'daterequested'=>$_POST['daterequested'.$q->id],
					'notes'=>$_POST['notes'.$q->id],
				);
				//print_r($updatearray);die;
				$this->model->db->where('id',$q->id);
				$this->model->db->update('awarditem',$updatearray);
			}
		}
		$data['email_body_title']   = "Company has modified bid for following backorder:";
		$data['email_body_content']  = "PO#: ".$quote->ponum."<br/>";
		$data['email_body_content']  .= "Company: ".$company->title."<br/>";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$settings = (array)$this->homemodel->getconfigurations ();
		$this->load->library('email');

		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($settings['adminemail'], "Administrator");
		$this->email->to($settings['adminemail']);
		
		$pa = $this->db->where('id',$quote->purchasingadmin)->get('users')->row();
		if($pa)
		$this->email->cc($pa->email);
		
		$this->email->subject('Backorder Update Notification');
		$this->email->message($send_body);	
		$this->email->set_mailtype("html");
		$this->email->send();
		
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">Draft Saved. <br/><br/>
Please click on the original update request link in your e-mail to return 
or edit your quote.</div></div></div>');
		redirect('quote/backtracks','refresh');
	}
	
	function items($quoteid)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$quote = $this->quotemodel->getquotebyid($quoteid);
		$bid = $this->db->where('quote',$quoteid)->where('company',$company->id)->get('bid')->row();
		$award = $this->quotemodel->getawardedbid($quoteid);
		if($bid)
		{
			$this->db->where('bid',$bid->id);			
			$biditems = $this->db->get('biditem')->result();
		}
		if($award)
		{
			$this->db->where('award',$award->id);
			$this->db->order_by('company');
			$allawardeditems = $this->db->get('awarditem')->result();
		}
		$itemswon = 0;
		$itemslost = 0;
		$data['biditems'] = array();
		$data['awarditems'] = array();
		foreach($allawardeditems as $ai)
		{
			$this->db->where('itemid',$ai->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
			    if($companyitem->itemcode)
				    $ai->itemcode = $companyitem->itemcode;
				if($companyitem->itemname)
				    $ai->itemname = $companyitem->itemname;
			}
			$data['allawardeditems'][] = $ai;
			if($ai->company == $company->id)
				$itemswon++;
			else
				$itemslost++;
		}
		//print_r($allawardeditems);die;
		$data['itemswon'] = $itemswon;
		$data['itemslost'] = $itemslost;
		$data['quote'] = $quote;
		$data['bid'] = $bid;
		$data['biditems'] = $biditems;
		$data['award'] = $award;
		$data['quoteid'] = $quoteid;
		
		$data['company'] = $company;
		
		$messagesql = "SELECT * FROM ".$this->db->dbprefix('message')." WHERE quote='{$quoteid}'";
		$message = $this->db->query($messagesql)->row();		
		if($message){
			$data['messagekey'] = $message->messagekey;
		}
		
		$this->load->view('quote/items',$data);
	}
	
	function getawardedpdf($quoteid)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$quote = $this->quotemodel->getquotebyid($quoteid);
		$awarded = $this->quotemodel->getawardedbid($quoteid);
		$items = $this->quotemodel->getawardeditems($awarded->id, $company->id);
		
		$this->db->where('id',$quote->pid);
		$project = $this->db->get('project')->row();
		
		$company = (array)$company;
		$this->db->where('id',$quote->purchasingadmin);
        $cpa = $this->db->get('users')->row();
		$pdfhtml = '<table width="100%" cellspacing="2" cellpadding="2">
			  <tr>
			    <td width="33%" align="left" valign="top">
			    <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
			      <tr>
			        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></td>
			        </tr>
			      <tr>
			        <td width="33%" valign="top">Project Title</td>
			        <td width="7%" valign="top">&nbsp;</td>
			        <td width="60%" valign="top">'.$project->title.'</td>
			      </tr>
			      <tr>
			        <td valign="top">Address</td>
			        <td valign="top">&nbsp;</td>
			        <td valign="top">'.$project->address.'</td>
			      </tr>
			    </table>
			    </td>
			    <td width="10" align="left" valign="top">&nbsp;</td>
			    <td width="65%" align="left" valign="top">
                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
			      <tr>
			        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Purchase Order Information</strong></font></td>
		          </tr>
			      <tr>
			        <td width="33%" valign="top">PO#</td>
			        <td width="7%" valign="top">&nbsp;</td>
			        <td width="60%" valign="top">'.$quote->ponum.'</td>
			      </tr>
			      <tr>
			        <td valign="top">Subject</td>
			        <td valign="top">&nbsp;</td>
			        <td valign="top">'.$quote->subject.'</td>
			      </tr>
			      <tr>
			        <td valign="top">PO# Date</td>
			        <td valign="top">&nbsp;</td>
			        <td valign="top">'.$quote->podate.'</td>
			      </tr>
			    </table></td>
			  </tr>
			  <tr>
			    <td align="left" valign="top">&nbsp;</td>
			    <td align="left" valign="top">&nbsp;</td>
			    <td align="left" valign="top">&nbsp;</td>
			  </tr>
			  <tr>
			    <td align="left" valign="top">
                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
			      <tr>
			        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>From</strong></font></td>
			      </tr>
				      <tr>
				        <td width="33%" valign="top">Contact</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">'.$cpa->fullname.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Company</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->companyname.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Address</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->address.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Phone</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->phone.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Fax</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->fax.'</td>
				      </tr>
			    </table></td>
			    <td align="left" valign="top">&nbsp;</td>
			    <td align="left" valign="top">
                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
			      <tr>
			        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Supplier</strong></font></td>
			      </tr>
			      <tr>
			        <td width="33%" valign="top">Contact</td>
			        <td width="2%" valign="top">&nbsp;</td>
			        <td width="65%" valign="top">'.$company['contact'].'</td>
			      </tr>
			      <tr>
			        <td valign="top">Company</td>
			        <td valign="top">&nbsp;</td>
			        <td valign="top">'.$company['title'].'</td>
			      </tr>
			    </table></td>
			  </tr>
			  <tr>
			    <td align="left" valign="top">&nbsp;</td>
			    <td align="left" valign="top">&nbsp;</td>
			    <td align="left" valign="top">&nbsp;</td>
			  </tr>
			  <tr>
			    <td align="left" valign="top">
                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
			      <tr>
			        <td bgcolor="#000033"><font color="#FFFFFF"><strong>Ship to</strong></font></td>
			      </tr>
			      <tr>
			        <td>'.$awarded->shipto.'</td>
			      </tr>
			    </table></td>
			    <td align="left" valign="top">&nbsp;</td>
			    <td align="left" valign="top">&nbsp;</td>
			  </tr>
			  
		</table>
			
			<table width="100%" cellspacing="0" cellpadding="4">
			  <tr>
              <td>PO Details:</td>
              </tr>
             </table>
             
             <br/>
             
			<table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
			  <thead>
			  <tr>
			    <th bgcolor="#000033"><font color="#FFFFFF">Item No</font></th>
			    <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>
			    <th bgcolor="#000033"><font color="#FFFFFF">Date Requested</font></th>
			    <th bgcolor="#000033"><font color="#FFFFFF">Quantity</font></th>
			    <th bgcolor="#000033"><font color="#FFFFFF">Unit</font></th>
			    <th bgcolor="#000033"><font color="#FFFFFF">Unit Price</font></th>
			    <th bgcolor="#000033"><font color="#FFFFFF">Total Price</font></th>
			  </tr>
			  </thead>
			  ';
			$i = 0;
			$totalprice = 0;
			foreach($items as $item)
			{
				$this->db->where('itemid',$item->itemid);
				$this->db->where('type','Supplier');
				$this->db->where('company',$company['id']);
				$companyitem = $this->db->get('companyitem')->row();
				if($companyitem)
				{
					$item->itemcode = $companyitem->itemcode;
					$item->itemname = $companyitem->itemname;
				}
				$pdfhtml.='<tr nobr="true">
				    <td style="border: 1px solid #000000;">'.++$i.'</td>
				    <td style="border: 1px solid #000000;">'.htmlentities($item->itemname).'</td>
				    <td style="border: 1px solid #000000;">'.($item->willcall?'For Pickup/Will Call':$item->daterequested).'</td>
				    <td style="border: 1px solid #000000;">'.$item->quantity.'</td>
				    <td style="border: 1px solid #000000;">'.$item->unit.'</td>
				    <td align="right" style="border: 1px solid #000000;">$ '.$item->ea.'</td>
				    <td align="right" style="border: 1px solid #000000;">$ '.$item->totalprice.'</td>
				  </tr>
				  ';
				$totalprice += $item->totalprice;
			}
			$config = (array)$this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
			//print_r($config);die;
			$config = array_merge($config, $this->config->config);
			$taxtotal = $totalprice * $config['taxpercent'] / 100;
			$grandtotal = $totalprice + $taxtotal;
			$pdfhtml.='<tr>
		    	<td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td align="right">Subtotal</td>
			    <td align="right">$ '. number_format($totalprice,2).'</td>
			  </tr>
			  <tr>
		    	<td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td align="right">Tax</td>
			    <td align="right">$ '. number_format($taxtotal,2).'</td>
			  </tr>
			  <tr>
		    	<td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td>&nbsp;</td>
			    <td align="right">Total</td>
			    <td align="right">$ '.number_format($grandtotal,2).'</td>
			  </tr>
			</table>';
		//die($pdfhtml);
    	
		require_once($config['base_dir'].'application/libraries/tcpdf/config/lang/eng.php');
    	require_once($config['base_dir'].'application/libraries/tcpdf/tcpdf.php');
    	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    	
    	$pdf->SetCreator(PDF_CREATOR);
    	$pdf->SetAuthor('');
    	$pdf->SetTitle('');
    	$pdf->SetSubject('');
    	
    	$pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));
    	
    	$pdf->setPrintFooter(false);
    	$pdf->setPrintHeader(true);
    	
    	$pdf->SetHeaderData('', '', $cpa->companyname.'', 'Purchase Order');
    	
    	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    	$pdf->AddPage('L', 'LETTER');
    	
    	$pdf->SetFont('helvetica', '', 8, '', true);
    	$pdf->writeHTML($pdfhtml, true, 0, true, true);
    	//$pdf->AddPage();
    	
    	$pdf->lastPage();
    	$pdfname = 'awarded.pdf';
    	$pdf->Output($pdfname, 'd');
	}
	
	function track($quoteid,$award='')
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$quote = $this->quotemodel->getquotebyid($quoteid);
		
		$awardeditems = $this->quotemodel->getawardeditems($award,$company->id);
		if(!$awardeditems)
			redirect('quote/items/'.$quoteid);
		$data['awarditems'] = array();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company->id);
		$bid = $this->db->get('bid')->row();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company->id);
		$this->db->order_by('uploadon','DESC');
		$docs = $this->db->get('shippingdoc')->result();
		
		$data['shippingdocs'] = $docs;
		
		$complete = true;
		$noitemsgiven = true;
		$allawarded = true;
		foreach($awardeditems as $ai)
		{
		
			$this->db->where('itemid',$ai->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
			    if($companyitem->itemcode)
				    $ai->itemcode = $companyitem->itemcode;
				if($companyitem->itemname)
				    $ai->itemname = $companyitem->itemname;
			}
			
			if($ai->received < $ai->quantity)
				$complete = false;
			if($ai->company != $company->id)
				$allawarded = false;
			if($ai->received > 0)
				$noitemsgiven = false;
			
			$ai->pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company->id)
			                        ->where('itemid',$ai->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;
			
			$data['awarditems'][] = $ai;
		}
		if(!$noitemsgiven)
		{
			if($complete)
			{
				$quote->status = 'Completed';
				$quote->progress = 100;
				$quote->mark = "progress-bar-success";
			}
			else
			{
				$quote->status = 'Partially Completed';
				$quote->progress = 80;
				$quote->mark = "progress-bar-success";
			}
		}
		else
		{
			$quote->status = 'Awarded';
			$quote->progress = 60;
			$quote->mark = "progress-bar-success";
		}
		
		$shipments = $this->db->select('shipment.*, item.itemname')
		             ->from('shipment')->join('item','shipment.itemid=item.id')
		             ->where('quote',$quoteid)->where('company',$company->id)
		             ->get()->result();
		
	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
		
		$invs = $this->quotemodel->getinvoices($company->id);
		$invoices = array();
		
		foreach($invs as $i)
		{		   
		    if(isset($i) && isset($i->quote) && isset($i->quote->id) && $i->quote->id == $quoteid)			
			    $invoices[]=$i;
		}
		//print_r($invoices);die;
		$data['quote'] = $quote;
		$data['award'] = $award;
		$data['invoices'] = $invoices;
		$data['settings'] = $settings;
		$data['shipments'] = $shipments;
		
		$data['purchasingadmin'] = $this->db->where('id',$quote->purchasingadmin)->get('users')->row();
		
		$messagesql = "SELECT * FROM ".$this->db->dbprefix('message')." WHERE quote='{$quoteid}'";
		$message = $this->db->query($messagesql)->row();		
		if($message){
			$data['messagekey'] = $message->messagekey;
		}
		
		//for export link
		$data['quoteid'] = $quoteid;
		$data['award']   = $award;
								
		$this->load->view('quote/track',$data);
	}
	
	
	
	
	
	 
	
	
	
	
	
	/* is merged with shipitems function
	function saveshippingdoc()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
	    $award = $_POST['award'];
	    unset($_POST['award']);
		if(is_uploaded_file($_FILES['filename']['tmp_name']))
		{
			$ext = end(explode('.', $_FILES['filename']['name']));
			$nfn = md5(date('u').uniqid()).'.'.$ext;
			if(move_uploaded_file($_FILES['filename']['tmp_name'], "uploads/shippingdoc/".$nfn))
			{
			    $insert = $_POST;
				$insert['filename'] = $nfn;
				$insert['company'] = $company->id;
				$insert['uploadon'] = date('Y-m-d');
				
				$this->db->insert('shippingdoc',$insert);
			}
		}
		redirect('quote/track/'.$_POST['quote'].'/'.$award);
	}
	*/
	
	function shipitems($quoteid,$awardid)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
		$quote = $this->quotemodel->getquotebyid($quoteid);
	    $awardeditems = $this->quotemodel->getawardeditems($awardid,$company->id);
	    
	    //first check if any item is trying to ship with quantity more than due.
	    foreach($awardeditems as $ai)
	    {
	        
		    $pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company->id)
			                        ->where('itemid',$ai->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;
	        $quantity = $_POST['quantity'.$ai->id];
	        $invoicenum = $_POST['invoicenum'.$ai->id];
	        if( $quantity && $invoicenum && $quantity + $pendingshipments > ($ai->quantity - $ai->received) )
	        {
	            //you cannot ship more than due quantity.
	            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">
You cannot ship more than due quantity, including pending shipments.</div></div></div>');
		        redirect('quote/track/'.$quoteid.'/'.$awardid,'refresh');
	        }
	    }
	    $shipitems = '';
            $shippingDocInvouceNum = $_POST['invoicenum'.$awardeditems[0]->id];
	    foreach($awardeditems as $ai)
	    {
                $pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company->id)
			                        ->where('itemid',$ai->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;
                
	        $quantity = $_POST['quantity'.$ai->id];
	        $invoicenum = $_POST['invoicenum'.$ai->id];
	        if( $quantity && $invoicenum && $quantity <= $ai->quantity - $ai->received )
	        {
	            $arr = array();
	            $arr['quantity'] = $quantity;
	            $arr['invoicenum'] = $invoicenum;
	            $arr['purchasingadmin'] = $quote->purchasingadmin;
	            $arr['quote'] = $quote->id;
	            $arr['company'] = $company->id;
	            $arr['awarditem'] = $ai->id;
	            $arr['itemid'] = $ai->itemid;
	            $arr['shipdate'] = date('Y-m-d');
	            $arr['accepted'] = 0;
	            //print_r($arr);
	            $this->db->insert('shipment',$arr);
	            
	            $shipitems .= "<tr><td>{$ai->itemcode}</td><td>{$quantity}</td><td>{$ai->quantity}</td><td>".($ai->quantity - $ai->received - $quantity)." ( ".$pendingshipments." Pending Acknowledgement )</td></tr>";
	        }
	    }
	    
		if(is_uploaded_file($_FILES['filename']['tmp_name']))
		{
			$ext = end(explode('.', $_FILES['filename']['name']));
			$nfn = md5(date('u').uniqid()).'.'.$ext;
			if(move_uploaded_file($_FILES['filename']['tmp_name'], "uploads/shippingdoc/".$nfn))
			{
			    $insert = array();
			    $insert['purchasingadmin'] = $_POST['purchasingadmin'];
			    $insert['quote'] = $_POST['quote'];
				$insert['filename'] = $nfn;
				$insert['company'] = $company->id;
				$insert['invoicenum'] = $shippingDocInvouceNum;
				$insert['uploadon'] = date('Y-m-d');
				
				$this->db->insert('shippingdoc',$insert);
			}
		}
		if($shipitems)
		{
			$pa = $this->db->where('id',$quote->purchasingadmin)->get('users')->row();
		    $shipitems = "<table cellpadding='5' cellspacing='5' border='1'><tr><th>Item</th><th>Quantity Shippped</th><th>Quantity Ordered</th><th>Quantity Remaining</th></tr>$shipitems</table>";
    	    $settings = (array)$this->homemodel->getconfigurations ();
    		$this->load->library('email');
    		$config['charset'] = 'utf-8';
    		$config['mailtype'] = 'html';
    		$this->email->initialize($config);
    		
    		$this->email->from($company->primaryemail);
    		if($pa)
    		$this->email->to($pa->email);
    		$subject = 'Shipment made by supplier';
    		
    		$data['email_body_title']  = "Supplier {$company->title} has made shipment for PO# {$quote->ponum} on ".date('m/d/Y');
    		$data['email_body_content'] = "<br><br>Details:".$shipitems;
    		$loaderEmail = new My_Loader();
    		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    		$this->email->subject($subject);
    		$this->email->message($send_body);
    		$this->email->set_mailtype("html");
    		$this->email->reply_to($company->primaryemail);
    		$this->email->send();
		}
		redirect('quote/track/'.$quoteid.'/'.$awardid);
	}
	
	function invoices ()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$invs = $this->quotemodel->getinvoices($company->id);
		$invoices = array();
		foreach($invs as $i)
		{
			if(isset($i) && isset($i->quote) && isset($i->quote->ponum))
			$invoices[$i->quote->ponum][]=$i;
		}
		
		$data['invoices'] = $invoices;
		$this->db->select($this->db->dbprefix('users.').'*');
		$this->db->where('usertype_id',2);
		$this->db->from('users')->join('network',"users.id=network.purchasingadmin")->where('network.company',$company->id);
		$data['purchasingadmins'] = $this->db->get()->result();
		$this->load->view('quote/invoices',$data);
	}
	
	function invoicedatedue()
	{
		$company = $this->session->userdata('company');
		if(!$company)
		    die;
		$_POST['datedue'] = date('Y-m-d', strtotime($_POST['datedue']));
		$this->db->where('invoicenum',$_POST['invoicenum'])->update('received',$_POST);
		
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$invs = $this->quotemodel->getinvoicesdetailsformail($company->id,$_POST['invoicenum']);
		
		$subject = "Due Date Set For Invoice ".$_POST['invoicenum'];
		$data['email_body_title']  = "";
		$data['email_body_content']  = "";
		$gtotal = 0;
		foreach ($invs as $invoice)
		{     		
			$data['email_body_title'] .= 'Dear '.$invoice->username.' ,<br><br>';
			$data['email_body_content'] .= $invoice->supplierusername.' has set Due Date for Invoice '.$_POST['invoicenum'].' to Due on  '.$invoice->DueDate.'<br><br>';
			$data['email_body_content'] .= 'Please see order details below :<br>';
			$data['email_body_content'] .= '
					<table class="table table-bordered span12" border="1">
		            	<tr>
		            		<th>Invoice</th>
		            		<th>Received On</th>
		            		<th>Supplier Name</th>
		            		<th>Supplier Address</th>
		            		<th>Supplier Phone</th>
		            		<th>Order Number</th>
		            		<th>Item</th>
		            		<th>Quantity</th>
		            		<th>Payment Status</th>
		            		<th>Verification</th>
		            		<th>Due Date</th>
		            		<th>Price</th>
		            	</tr>';
			
	        $data['email_body_content'] .= '<td>'.$invoice->invoicenum.'</td>
            		<td>'.$invoice->receiveddate.'</td>
            		<td>'.$invoice->supplierusername.'</td>
            		<td>'.$invoice->address.'</td>
            		<td>'.$invoice->phone.'</td>
            		<td>'.$invoice->ponum.'</td>
            		<td>'.$invoice->itemname.'</td>
            		<td>'.$invoice->quantity.'</td>
            		<td>'.$invoice->paymentstatus.'</td>
            		<td>'.$invoice->status.'</td>
            		<td>'.$invoice->DueDate.'</td>
            		<td align="right">'.number_format($invoice->price,2).'</td>
	            	  </tr>';
	        $total = $invoice->price*$invoice->quantity;
            $gtotal+=$total;
	        $tax = $gtotal * $invoice->taxpercent / 100;
            $totalwithtax = number_format($tax+$gtotal,2);
            	
            $data['email_body_content'] .= '<tr><td colspan="12">&nbsp;</td> <tr>
            		<td colspan="11" align="right">Total</td>
            		<td style="text-align:right;">$'.number_format($gtotal,2).'</td>
            	</tr>
            	
            	<tr>
            		<td colspan="11" align="right">Tax</td>
            		<td style="text-align:right;">$'. number_format($tax,2).'</td>
            	</tr>
            	
            	<tr>
            		<td colspan="11" align="right">Total</td>
            		<td style="text-align:right;">$'. $totalwithtax.'</td>
            	</tr>';
            $data['email_body_content'] .= '</table>';   
	    }      
	    $loaderEmail = new My_Loader();
	    $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		//$this->email->clear(true);
		$this->email->to($invs[0]->email);
		//$this->email->cc('pratiksha@esparkinfo.com');
		$this->email->from($this->session->userdata("company")->primaryemail,$this->session->userdata("company")->primaryemail);
		
		$this->email->subject($subject);
		$this->email->message($send_body);	
		$this->email->set_mailtype("html");
		$this->email->send();
	}
	
	function invoice()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
		if(isset($_POST['invoicenum']) && $_POST['invoicenum']!="")	
			$invoicenum = $_POST['invoicenum'];
		else 
			$invoicenum = "";
			
		if(!$invoicenum)
		{
			$message = 'Invalid Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invoices');
		}
		$invoice = $this->quotemodel->getinvoicebynum($invoicenum, $company->id);
		$awarded = $this->quotemodel->getawardedbid($invoice->quote, $company->id);
		//echo '<pre>';print_r($invoice);die;
		
		$quote = $awarded->quotedetails;
		$project = $this->project_model->get_projects_by_id($quote->pid);
		$config = (array)$this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
		$config = array_merge($config, $this->config->config);
		
		$this->db->where('id',$quote->purchasingadmin);
		$purchasingadmin = $this->db->get('users')->row();
		
		$data['purchasingadmin'] = $purchasingadmin;
		$company = $this->db->where('id',$company->id)->get('company')->row();
		$data['company'] = $company;
		$data['quote'] = $quote;
		$data['awarded'] = $awarded;
		$data['config'] = $config;
		$data['project'] = $project;
		$data['invoice'] = $invoice;
		$data['heading'] = "Invoice Details";
		$this->load->view ('quote/invoice', $data);
			
	}
	
	function invoicestatus()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
		if(isset($_POST['invoicenum']) && $_POST['invoicenum']!="")	
			$invoicenum = $_POST['invoicenum'];
		else 
			$invoicenum = "";
			
		if(!$invoicenum)
		{
			$message = 'Invalid Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invoices');
		}
		if($_POST['status'] == 'Error')
		{
		    $_POST['paymentstatus'] = 'Pending';
		    $_POST['paymenttype'] = '';
		    $_POST['refnum'] = '';
		}
		$this->db->where('invoicenum',$_POST['invoicenum']);
		$this->db->update('received',$_POST);
		
		
		$invoice = $this->quotemodel->getinvoicebynum($_POST['invoicenum'], $company->id);
		$awarded = $this->quotemodel->getawardedbid($invoice->quote, $company->id);
		//echo '<pre>';print_r($invoice);die;
		
		$quote = $awarded->quotedetails;
		
		$pa = $this->db->where('id',$quote->purchasingadmin)->get('users')->row();
		
		$settings = (array)$this->homemodel->getconfigurations ();
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($company->primaryemail);
		if($pa)
		$this->email->to($pa->email);
		
		if(isset($_POST['status']) && $_POST['status']!="" && $_POST['status']=="Verified")
		$subject = 'Supplier Verified Payment'; 
		
		if(isset($_POST['status']) && $_POST['status']!="" && $_POST['status']=="Error")
		$subject = 'Supplier Disputes Payment'; 
		$data['email_body_title'] = "";
		$data['email_body_content']= "Supplier {$company->title} has set the status of 
				Invoice# {$_POST['invoicenum']} to {$_POST['status']} 
				for PO# {$quote->ponum} on ".date('m/d/Y').".";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$this->email->subject($subject);
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->reply_to($company->primaryemail);
		$this->email->send();
		
		$this->invoice();
	}
	
	function requestpayment($quoteid = '',$award='')
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$invoicenum = $_POST['invoicenum'];
		
		if(!$invoicenum)
		{
			$message = 'Invalid Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('quote/invoices');
		}
		$update = array('paymentstatus'=>'Requested Payment');
		$update['paymenttype'] = '';
		$update['refnum'] = date('Y-m-d');//in this case, as the payment status is not paid, we use this field for date.
		$this->db->where('invoicenum',$invoicenum);
		$this->db->update('received',$update);
		
		$quote = $this->db->select('quote.*')
				 ->from('received')
				 ->join('awarditem','received.awarditem=awarditem.id')
				 ->join('award','awarditem.award=award.id')
				 ->join('quote','award.quote=quote.id')	 
				 ->where('invoicenum',$invoicenum)
				 ->get()->row();
		
		$pa = $this->db->where('id',$quote->purchasingadmin)->get('users')->row();
		
		$settings = (array)$this->homemodel->getconfigurations ();
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($company->primaryemail);
		if($pa){
		$this->email->to($pa->email);
		$companyadminname = $pa->companyname;
		}else{
			$companyadminname = "";			
		}
		$subject = 'Payment requested by supplier';
		$data['email_body_title'] = "";
		$data['email_body_content'] = "Dear {$companyadminname}, <br> <br> Supplier {$company->title} has sent payment request for
		Invoice# {$invoicenum}
		for PO# {$quote->ponum} on ".date('m/d/Y').".  <br> <br> Thank You. <br> {$company->title}";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		
		$this->email->subject($subject);
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->reply_to($company->primaryemail);
		$this->email->send();
		
		$message = 'Payment Requested for the invoice# '.$_POST['invoicenum'];
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
		
		if($quoteid)
		{
		    redirect('quote/track/'.$quoteid.'/'.$award);
		}
		else
		{
		    redirect('quote/invoices');
		    //$this->invoice();
		}
	}
	

	function track_export($quoteid,$award='')
	{
				
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$quote = $this->quotemodel->getquotebyid($quoteid);
		
		$awardeditems = $this->quotemodel->getawardeditems($award,$company->id);
		if(!$awardeditems)
			redirect('quote/items/'.$quoteid);
		$data['awarditems'] = array();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company->id);
		$bid = $this->db->get('bid')->row();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company->id);
		$this->db->order_by('uploadon','DESC');
		$docs = $this->db->get('shippingdoc')->result();
		
		$data['shippingdocs'] = $docs;
		
		$complete = true;
		$noitemsgiven = true;
		$allawarded = true;
		foreach($awardeditems as $ai)
		{
		
			$this->db->where('itemid',$ai->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
			    if($companyitem->itemcode)
				    $ai->itemcode = $companyitem->itemcode;
				if($companyitem->itemname)
				    $ai->itemname = $companyitem->itemname;
			}
			
			if($ai->received < $ai->quantity)
				$complete = false;
			if($ai->company != $company->id)
				$allawarded = false;
			if($ai->received > 0)
				$noitemsgiven = false;
			
			$ai->pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company->id)
			                        ->where('itemid',$ai->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;
			
			$data['awarditems'][] = $ai;
		}
		if(!$noitemsgiven)
		{
			if($complete)
			{
				$quote->status = 'Completed';
				$quote->progress = 100;
				$quote->mark = "progress-bar-success";
			}
			else
			{
				$quote->status = 'Partially Completed';
				$quote->progress = 80;
				$quote->mark = "progress-bar-success";
			}
		}
		else
		{
			$quote->status = 'Awarded';
			$quote->progress = 60;
			$quote->mark = "progress-bar-success";
		}
		
		$shipments = $this->db->select('shipment.*, item.itemname')
		             ->from('shipment')->join('item','shipment.itemid=item.id')
		             ->where('quote',$quoteid)->where('company',$company->id)
		             ->get()->result();
		
	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
		
		$invs = $this->quotemodel->getinvoices($company->id);
		$invoices = array();
		foreach($invs as $i)
		{		   
		    if(isset($i) && isset($i->quote) && isset($i->quote->id) && $i->quote->id == $quoteid)			
			    $invoices[]=$i;
		}
				
		//print_r($invoices);die;
		$data['quote'] = $quote;
		$data['award'] = $award;
		$data['invoices'] = $invoices;
		$data['settings'] = $settings;
		$data['shipments'] = $shipments;
		
		$data['purchasingadmin'] = $this->db->where('id',$quote->purchasingadmin)->get('users')->row();
		
		//  $this->load->view('quote/track',$data);
		
		//--------------------------------------------------------------------------
		
		$shippingdocs = $data['shippingdocs'];
		
		
		$purchasingadmin = $data['purchasingadmin'];
		
		$header[] = array('Report Type','Quote Performance','','','','','','','');
				
		if(isset($quote->podate))
		{ 
			$order_date = $quote->podate; 
			$header[] = array('Order Date',$order_date ,'','','','','','','');
		}
			
		if(isset($purchasingadmin->companyname))
		{
			$companyname_name =  $purchasingadmin->companyname;
			$header[] = array('Company',$companyname_name ,'','','','','','','');
		}
				
		$header[] = array('','' ,'','','','','','','');
		$header[] = array('PO Progress',$quote->progress.'%'.chr(160) ,'','','','','','','');
		
		$header[] = array('','' ,'','','','','','','');
		
		
		
		
		
		
		
		$header[] = array('ITEM Code/Name','Qty','Unit','Price','Total','Requested','Notes','Shipped','Due');
		
		$awarditems = $data['awarditems'];
			
		$i = 0;
		foreach($awarditems as $ai)
		{



			$i++;
										
			$itemname = '';
			if(trim($ai->itemname) != '')
			{			
				$itemname = '('.$ai->itemname.')';
			}
			
			$due = $ai->quantity - $ai->received;
			
			if($ai->pendingshipments)
			{
                 $due.=  $ai->pendingshipments.'(Pending Acknowledgement)';
            }
						
			$header[] = array($ai->itemcode.$itemname, $ai->quantity , $ai->unit , '$'.$ai->ea.chr(160) ,'$'.round($ai->quantity * $ai->ea,2).chr(160),$ai->daterequested,$ai->notes,$ai->received,$due);							
										
		}								
										
										
		if($shippingdocs)
		{		
			$header[] = array('','' ,'','','','','','','');							
			$header[] = array('','' ,'','','','','','','');	
			$header[] = array('Existing Documents','' ,'','','','','','','');	
			$header[] = array('','' ,'','','','','','','');								
		
			
			
			$header[] = array('Date','REF#' ,'','','','','','','');	
		
			foreach($shippingdocs as $sd)
			{
				$header[] = array(date("m/d/Y",  strtotime($sd->uploadon)),$sd->invoicenum ,'','','','','','','');		
			}
			$header[] = array('','' ,'','','','','','','');				
		}
		
		
		
		if($shipments)
		{
			$header[] = array('','' ,'','','','','','','');							
			$header[] = array('','' ,'','','','','','','');	
			$header[] = array('Shipments Made For PO#', $quote->ponum ,'','','','','','','');	
			$header[] = array('','' ,'','','','','','','');	
		
		
			$header[] = array('Ref#','Item' ,'Quantity','Sent On','Status','','','','');	
		
			foreach($shipments as $s)
			{
				$ship_status = $s->accepted?'Accepted':'Pending';
				$header[] = array($s->invoicenum,$s->itemname ,$s->quantity,date('m/d/Y',strtotime($s->shipdate)), $ship_status ,'','','','');
			}				
		}
		
		
		
		if($invoices)
		{
			$header[] = array('','' ,'','','','','','','');							
			$header[] = array('','' ,'','','','','','','');	
			$header[] = array('Existing Invoices For PO#',  $quote->ponum ,'','','','','','','');
			
			$header[] = array('Invoice#','Status' ,'Received On','Total Cost','Payment Status','Due Date','','','');	
			
			foreach($invoices as $i)
			{
				$amount = $i->totalprice;
				$amount = $amount + ($amount*$settings->taxpercent/100);
				$amount = number_format($amount,2);

				$verify_status = '';
				if($i->status=='Verified')
				{
	                 $verify_status = '('.$i->paymenttype.'/'.$i->refnum.')';
	            }

				$header[] = array($i->invoicenum,$i->status ,$i->receiveddate,'$'.$amount.chr(160),$i->paymentstatus.$verify_status,date('m/d/Y',strtotime($i->datedue)),'','','');				
			}
		}
										
		createXls('po_performance', $header);  			
		die();	
		
		//===============================================================================
		
	}
	
	// TRACK PDF
	function track_pdf($quoteid,$award='')
	{
				
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$quote = $this->quotemodel->getquotebyid($quoteid);
		
		$awardeditems = $this->quotemodel->getawardeditems($award,$company->id);
		if(!$awardeditems)
			redirect('quote/items/'.$quoteid);
		$data['awarditems'] = array();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company->id);
		$bid = $this->db->get('bid')->row();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company->id);
		$this->db->order_by('uploadon','DESC');
		$docs = $this->db->get('shippingdoc')->result();
		
		$data['shippingdocs'] = $docs;
		
		$complete = true;
		$noitemsgiven = true;
		$allawarded = true;
		foreach($awardeditems as $ai)
		{
		
			$this->db->where('itemid',$ai->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
			    if($companyitem->itemcode)
				    $ai->itemcode = $companyitem->itemcode;
				if($companyitem->itemname)
				    $ai->itemname = $companyitem->itemname;
			}
			
			if($ai->received < $ai->quantity)
				$complete = false;
			if($ai->company != $company->id)
				$allawarded = false;
			if($ai->received > 0)
				$noitemsgiven = false;
			
			$ai->pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company->id)
			                        ->where('itemid',$ai->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;
			
			$data['awarditems'][] = $ai;
		}
		if(!$noitemsgiven)
		{
			if($complete)
			{
				$quote->status = 'Completed';
				$quote->progress = 100;
				$quote->mark = "progress-bar-success";
			}
			else
			{
				$quote->status = 'Partially Completed';
				$quote->progress = 80;
				$quote->mark = "progress-bar-success";
			}
		}
		else
		{
			$quote->status = 'Awarded';
			$quote->progress = 60;
			$quote->mark = "progress-bar-success";
		}
		
		$shipments = $this->db->select('shipment.*, item.itemname')
		             ->from('shipment')->join('item','shipment.itemid=item.id')
		             ->where('quote',$quoteid)->where('company',$company->id)
		             ->get()->result();
		
	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
		
		$invs = $this->quotemodel->getinvoices($company->id);
		$invoices = array();
		foreach($invs as $i)
		{		   
		    if(isset($i) && isset($i->quote) && isset($i->quote->id) && $i->quote->id == $quoteid)			
			    $invoices[]=$i;
		}
				
		//print_r($invoices);die;
		$data['quote'] = $quote;
		$data['award'] = $award;
		$data['invoices'] = $invoices;
		$data['settings'] = $settings;
		$data['shipments'] = $shipments;
		
		$data['purchasingadmin'] = $this->db->where('id',$quote->purchasingadmin)->get('users')->row();
		
		//  $this->load->view('quote/track',$data);
		
		//--------------------------------------------------------------------------
		
		$shippingdocs = $data['shippingdocs'];
		
		
		$purchasingadmin = $data['purchasingadmin'];
		
		$header[] = array('Report Type:','Quote Performance','','','','','','','');
				
		if(isset($quote->podate))
		{ 
			$order_date = $quote->podate; 
			$header[] = array('<b>Order Date</b>',$order_date ,'','','','','','','');
		}
			
		if(isset($purchasingadmin->companyname))
		{
			$companyname_name =  $purchasingadmin->companyname;
			$header[] = array('<b>Company</b>',$companyname_name ,'','','','','','','');
		}
				
		$header[] = array('','' ,'','','','','','','');
		$header[] = array('<b>PO Progress</b>',$quote->progress.'%'.chr(160) ,'','','','','','','');
		
		$header[] = array('','' ,'','','','','','','');
		
		
		
		
		
		
		
		$header[] = array('<b>ITEM Code/Name</b>','<b>Qty</b>','<b>Unit</b>','<b>Price</b>','<b>Total</b>','<b>Requested</b>','<b>Notes</b>','<b>Shipped</b>','<b>Due</b>');
		
		$awarditems = $data['awarditems'];
			
		$i = 0;
		foreach($awarditems as $ai)
		{



			$i++;
										
			$itemname = '';
			if(trim($ai->itemname) != '')
			{			
				$itemname = '('.$ai->itemname.')';
			}
			
			$due = $ai->quantity - $ai->received;
			
			if($ai->pendingshipments)
			{
                 $due.=  $ai->pendingshipments.'(Pending Acknowledgement)';
            }
						
			$header[] = array($ai->itemcode.$itemname, $ai->quantity , $ai->unit , '$'.$ai->ea.chr(160) ,'$'.round($ai->quantity * $ai->ea,2).chr(160),$ai->daterequested,$ai->notes,$ai->received,$due);							
										
		}								
										
										
		if($shippingdocs)
		{		
			$header[] = array('','' ,'','','','','','','');							
			$header[] = array('','' ,'','','','','','','');	
			$header[] = array('<b>Existing Documents</b>','' ,'','','','','','','');	
			$header[] = array('','' ,'','','','','','','');								
		
			
			
			$header[] = array('<b>Date</b>','<b>REF#</b>' ,'','','','','','','');	
		
			foreach($shippingdocs as $sd)
			{
				$header[] = array(date("m/d/Y",  strtotime($sd->uploadon)),$sd->invoicenum ,'','','','','','','');		
			}
			$header[] = array('','' ,'','','','','','','');				
		}
		
		
		
		if($shipments)
		{
			$header[] = array('','' ,'','','','','','','');							
			$header[] = array('','' ,'','','','','','','');	
			$header[] = array('<b>Shipments Made For PO#</b>', $quote->ponum ,'','','','','','','');	
			$header[] = array('','' ,'','','','','','','');	
		
		
			$header[] = array('<b>Ref#</b>','<b>Item</b>' ,'<b>Quantity</b>','<b>Sent On</b>','<b>Status</b>','','','','');	
		
			foreach($shipments as $s)
			{
				$ship_status = $s->accepted?'Accepted':'Pending';
				$header[] = array($s->invoicenum,$s->itemname ,$s->quantity,date('m/d/Y',strtotime($s->shipdate)), $ship_status ,'','','','');
			}				
		}
		
		
		
		if($invoices)
		{
			$header[] = array('','' ,'','','','','','','');							
			$header[] = array('','' ,'','','','','','','');	
			$header[] = array('<b>Existing Invoices For PO#</b>',  $quote->ponum ,'','','','','','','');
			
			$header[] = array('<b>Invoice#</b>','<b>Status</b>' ,'<b>Received On</b>','<b>Total Cost</b>','<b>Payment Status</b>','<b>Due Date</b>','','','');	
			
			foreach($invoices as $i)
			{
				$amount = $i->totalprice;
				$amount = $amount + ($amount*$settings->taxpercent/100);
				$amount = number_format($amount,2);

				$verify_status = '';
				if($i->status=='Verified')
				{
	                 $verify_status = '('.$i->paymenttype.'/'.$i->refnum.')';
	            }

				$header[] = array($i->invoicenum,$i->status ,$i->receiveddate,'$'.$amount.chr(160),$i->paymentstatus.$verify_status,date('m/d/Y',strtotime($i->datedue)),'','','');				
			}
		}
										
		 	
		$headername = "TRACK";
    	createOtherPDF('po_performance', $header,$headername);
    	die();
		//===============================================================================
		
	}
	
	
	
	
	
	
	
	function items_export($quoteid)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
	
		$quote = $this->quotemodel->getquotebyid($quoteid);
		$bid = $this->db->where('quote',$quoteid)->where('company',$company->id)->get('bid')->row();
		$award = $this->quotemodel->getawardedbid($quoteid);
		if($award)
		{
			$this->db->where('award',$award->id);
			$this->db->order_by('company');
			$allawardeditems = $this->db->get('awarditem')->result();
		}
		$itemswon = 0;
		$itemslost = 0;
		$data['awarditems'] = array();
		foreach($allawardeditems as $ai)
		{
			$this->db->where('itemid',$ai->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
				if($companyitem->itemcode)
					$ai->itemcode = $companyitem->itemcode;
				if($companyitem->itemname)
					$ai->itemname = $companyitem->itemname;
			}
			$data['allawardeditems'][] = $ai;
			if($ai->company == $company->id)
				$itemswon++;
			else
				$itemslost++;
		}
		
		$data['itemswon'] = $itemswon;
		$data['itemslost'] = $itemslost;
		$data['quote'] = $quote;
		$data['bid'] = $bid;
		$data['award'] = $award;
		$data['company'] = $company;
			
		$quote = $data['quote'];
	
		//=========================================================================================
		$customer_name = '';
		
		$allawardeditems_for_c = $allawardeditems;
		foreach($allawardeditems_for_c as $ai)
		{
						
			$customer = $this->db->select('users.*')
				 ->from('users')				
				 ->where('id',$ai->purchasingadmin)
				 ->get()->row();
			$customer_name = $customer->companyname;
			break;
		}		
				
		$header[] = array('Customer' , $customer_name ,'' , '' , '' , '', '');
		
		$header[] = array('Report Type' , 'PO Performance','' , '' , '' , '', '');
		
		$header[] = array('' , '','' , '' , '' , '', '');	
		$header[] = array('PO Performance :' , $quote->ponum,'' , '' , '' , '', '');
		$header[] = array('' , '','' , '' , '' , '', '');
		$header[] = array('Items Won :' , $itemswon,'' , '' , '' , '', '');
		$header[] = array('Items Lost :' , $itemslost,'' , '' , '' , '', '');
		$header[] = array('' , '','' , '' , '' , '', '');
		$header[] = array('' , '','' , '' , '' , '', '');
	
		$header[] = array('Item Code' , 'Item Name','QTY.' , 'Unit', 'Price' , 'Total' , 'Requested');
			
		$i = 0;
		
		foreach($allawardeditems as $ai)
		{
						
			$customer = $this->db->select('users.*')
				 ->from('users')				
				 ->where('id',$ai->purchasingadmin)
				 ->get()->row();
			$customer_name = $customer->companyname;
			
			
			
			
			
			//--------------------------------------------------------------
			$i++;
			$header[] = array($ai->itemcode , $ai->itemname,$ai->quantity , $ai->unit , '$ '. $ai->ea.chr(160) , '$ '.round($ai->quantity * $ai->ea,2).chr(160),$ai->daterequested);
		}
		createXls('Quote_items_'.$quoteid, $header);
		die();
	
		//===============================================================================
	
	}

	// ITEM PDF
	function items_pdf($quoteid)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
	
		$quote = $this->quotemodel->getquotebyid($quoteid);
		$bid = $this->db->where('quote',$quoteid)->where('company',$company->id)->get('bid')->row();
		$award = $this->quotemodel->getawardedbid($quoteid);
		if($award)
		{
			$this->db->where('award',$award->id);
			$this->db->order_by('company');
			$allawardeditems = $this->db->get('awarditem')->result();
		}
		$itemswon = 0;
		$itemslost = 0;
		$data['awarditems'] = array();
		foreach($allawardeditems as $ai)
		{
			$this->db->where('itemid',$ai->itemid);
			$this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
				if($companyitem->itemcode)
					$ai->itemcode = $companyitem->itemcode;
				if($companyitem->itemname)
					$ai->itemname = $companyitem->itemname;
			}
			$data['allawardeditems'][] = $ai;
			if($ai->company == $company->id)
				$itemswon++;
			else
				$itemslost++;
		}
		
		$data['itemswon'] = $itemswon;
		$data['itemslost'] = $itemslost;
		$data['quote'] = $quote;
		$data['bid'] = $bid;
		$data['award'] = $award;
		$data['company'] = $company;
			
		$quote = $data['quote'];
	
		//=========================================================================================
		$customer_name = '';
		
		$allawardeditems_for_c = $allawardeditems;
		foreach($allawardeditems_for_c as $ai)
		{
						
			$customer = $this->db->select('users.*')
				 ->from('users')				
				 ->where('id',$ai->purchasingadmin)
				 ->get()->row();
			$customer_name = $customer->companyname;
			break;
		}		
				
		$header[] = array('<b>Customer</b>' , $customer_name ,'' , '' , '' , '', '');
		
		$header[] = array('<b>Report Type:</b>' , 'PO Performance','' , '' , '' , '', '');
		
		$header[] = array('' , '','' , '' , '' , '', '');	
		$header[] = array('<b>PO Performance :</b>' , $quote->ponum,'' , '' , '' , '', '');
		$header[] = array('' , '','' , '' , '' , '', '');
		$header[] = array('<b>Items Won :</b>' , $itemswon,'' , '' , '' , '', '');
		$header[] = array('<b>Items Lost :</b>' , $itemslost,'' , '' , '' , '', '');
		$header[] = array('' , '','' , '' , '' , '', '');
		$header[] = array('' , '','' , '' , '' , '', '');
	
		$header[] = array('<b>Item Code</b>' , '<b>Item Name</b>','<b>QTY.</b>' , '<b>Unit</b>', '<b>Price</b>' , '<b>Total</b>' , '<b>Requested</b>');
			
		$i = 0;
		
		foreach($allawardeditems as $ai)
		{
						
			$customer = $this->db->select('users.*')
				 ->from('users')				
				 ->where('id',$ai->purchasingadmin)
				 ->get()->row();
			$customer_name = $customer->companyname;
			
			
			
			
			
			//--------------------------------------------------------------
			$i++;
			$header[] = array($ai->itemcode , $ai->itemname,$ai->quantity , $ai->unit , '$ '. $ai->ea.chr(160) , '$ '.round($ai->quantity * $ai->ea,2).chr(160),$ai->daterequested);
		}
		$headername = "PO PERFORMANCE";
    	createOtherPDF('Quote_items_', $header,$headername);
    	die();
	 
	
		//===============================================================================
	
	}

	
	
	
	function performance()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$query = "SELECT itemid, itemcode, count(bi.id) as bidcount
				  FROM ".$this->db->dbprefix('biditem')." bi, ".$this->db->dbprefix('bid')." b 
				  WHERE bi.bid=b.id AND b.company={$company->id}
				  GROUP BY itemid HAVING itemid
				  ";
		//echo $query.'<br>';
		$data['items'] = array();
		$items = $this->db->query($query)->result();
		foreach($items as $item)
		{
			$query = "SELECT count(ai.id) as awardcount
				  FROM ".$this->db->dbprefix('awarditem')." ai, ".$this->db->dbprefix('award')." a, ".$this->db->dbprefix('quote')." q WHERE ai.award = a.id AND a.quote = q.id 
				  AND ai.company={$company->id} AND itemid='".$item->itemid."'
				  ";
			//echo $query.'<br>'.'<br>';
			$item->awardcount = $this->db->query($query)->row()->awardcount;
			$item->performance = round(($item->awardcount/$item->bidcount) * 100,2);
			$data['items'][]= $item;
		}
		$this->load->view ('quote/performance', $data);
	}
	//
	function directs()
	{
	    
	}
}