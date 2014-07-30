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
	
	public function direct($key)
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
	    $data['bid'] = $bid;
	    if($bid){
	    	$sqlq = "SELECT revisionid FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bid->id."' AND purchasingadmin='".$quote->purchasingadmin."' order by id desc limit 1";
	    	$revisionquote = $this->db->query($sqlq)->row();
	    	$data['revisionno'] = $revisionquote->revisionid;
	    }
	    
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
		if($print)
		{
			$this->load->template ( '../../templates/front/blank', $data);
			$this->load->view('quote/printquote',$data);
		}
		else
			$this->load->view('quote/quote',$data);
	}
	
	public function placebid()
	{
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
			
			$sqlq = "SELECT revisionid FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bidid."' AND purchasingadmin='".$invitation->purchasingadmin."' order by id desc limit 1"; 
             $revisionquote = $this->db->query($sqlq)->row(); 
             if($revisionquote) 
                 $revisionid = $revisionquote->revisionid+1; 
             else  
                 $revisionid = 1; 
			
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
            if($bid->quotefile !="")
            {
                $attachment = "uploads/quotefile/".$bid->quotefile;
            }
            
    	    $quote = $this->quotemodel->getquotebyid($bid->quote);
    	    $biditems = $this->quotemodel->getdraftitems($bid->quote, $company->id);
    	    
    	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
    	    $taxpercent = $settings->taxpercent;
    	    
    		ob_start();
    	   	include $this->config->config['base_dir'].'application/views/quote/quotehtml.php';
    	   	$html = ob_get_clean();
		    
    		$settings = (array)$this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
    	    $this->load->library('email');
    		$this->email->clear(true);
            $to = array();
            $this->email->from($settings['adminemail'], "Administrator");
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
            $body = "Dear Admin,<br><br>
    		  	This is a notification of bid details by ".$company->title." for PO# ".$quote->ponum.".<br/><br/>
    		  	Please find the details below:<br/><br/>
    		  	$html
    		    ";
    		//echo($to.'<br/>');
    		//echo $body;
           	$this->email->subject('Bid Notification for PO# '.$quote->ponum. " by ".$company->title);
            $this->email->message($body);	
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
        		$emailitems.= '<tr>';
        		$emailitems.= '<td>'.$q->itemname.'</td>';
        		$emailitems.= '<td>'.$_POST['daterequested'.$q->id].'</td>';
        		$emailitems.= '<td>'.$_POST['notes'.$q->id].'</td>';
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
		$this->email->clear(true);
        $this->email->from($settings['adminemail'], "Administrator");
        $this->email->to($settings['adminemail']); 
        $sql = "SELECT u.email FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('quoteuser')." qu
	        	WHERE qu.userid=u.id AND qu.quote=".$quote->id;
        $purchaseusers = $this->db->query($sql)->result();
        $pa = $this->db->where('id',$q->purchasingadmin)->get('users')->row();
        $this->email->cc($pa->email);
        foreach($purchaseusers as $pu)
        {
        	$this->email->cc($pu->email);
        }
        $body = "Dear Admin,<br><br>
		  	ETA has been updated by ".$company->title." for PO# ".$quote->ponum.".<br/><br/>
		  	Please find the details below:<br/><br/>
		  	$emailitems
		    ";
       	$this->email->subject('Backorder update for PO# '.$quote->ponum. " by ".$company->title);
        $this->email->message($body);	
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
		$body  = "Company has modified bid for following backorder:<br/><br/>";
		$body .= "PO#: ".$quote->ponum."<br/>";
		$body .= "Company: ".$company->title."<br/>";
		$settings = (array)$this->homemodel->getconfigurations ();
		$this->load->library('email');
		
		$this->email->from($settings['adminemail'], "Administrator");
		$this->email->to($settings['adminemail']);
		
		$pa = $this->db->where('id',$quote->purchasingadmin)->get('users')->row();
		$this->email->cc($pa->email);
		
		$this->email->subject('Backorder Update Notification');
		$this->email->message($body);	
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
		//print_r($allawardeditems);die;
		$data['itemswon'] = $itemswon;
		$data['itemslost'] = $itemslost;
		$data['quote'] = $quote;
		$data['bid'] = $bid;
		$data['award'] = $award;
		
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
		    if($i->quote->id == $quoteid)
			    $invoices[]=$i;
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
    		
    		$this->email->from($company->primaryemail);
    		$this->email->to($pa->email);
    		$subject = 'Shipment made by supplier';
    		
    		$body = "Supplier {$company->title} has made shipment for PO# {$quote->ponum} on ".date('m/d/Y').".
    				  <br><br>Details:$shipitems";
    		
    		$this->email->subject($subject);
    		$this->email->message($body);
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
		$body = "";
		$gtotal = 0;
		foreach ($invs as $invoice)
		{     		
			$body .= 'Dear '.$invoice->username.' ,<br><br>';
			$body .= $invoice->supplierusername.' has set Due Date for Invoice '.$_POST['invoicenum'].' to Due on  '.$invoice->DueDate.'<br><br>';
			$body .= 'Please see order details below :<br>';
			$body .= '
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
			
	        $body .= '<td>'.$invoice->invoicenum.'</td>
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
            	
            $body .= '<tr><td colspan="12">&nbsp;</td> <tr>
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
            $body .= '</table>';   
	    }            
		$this->load->library('email');
		$this->email->clear(true);
		$this->email->to($invs[0]->email);
		//$this->email->cc('pratiksha@esparkinfo.com');
		$this->email->from($this->session->userdata("company")->primaryemail,$this->session->userdata("company")->primaryemail);
		
		$this->email->subject($subject);
		$this->email->message($body);	
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
		
		$this->email->from($company->primaryemail);
		$this->email->to($pa->email);
		
		if(isset($_POST['status']) && $_POST['status']!="" && $_POST['status']=="Verified")
		$subject = 'Supplier Verified Payment'; 
		
		if(isset($_POST['status']) && $_POST['status']!="" && $_POST['status']=="Error")
		$subject = 'Supplier Disputes Payment'; 
		
		$body = "Supplier {$company->title} has set the status of 
				Invoice# {$_POST['invoicenum']} to {$_POST['status']} 
				for PO# {$quote->ponum} on ".date('m/d/Y').".";
		
		$this->email->subject($subject);
		$this->email->message($body);
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
		
		$this->email->from($company->primaryemail);
		$this->email->to($pa->email);
		$subject = 'Payment requested by supplier';
		
		$body = "Supplier {$company->title} has sent payment request for
		Invoice# {$invoicenum}
		for PO# {$quote->ponum} on ".date('m/d/Y').".";
		
		$this->email->subject($subject);
		$this->email->message($body);
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
			$query = "SELECT count(id) as awardcount
				  FROM ".$this->db->dbprefix('awarditem')." 
				  WHERE company={$company->id} AND itemid='".$item->itemid."'
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