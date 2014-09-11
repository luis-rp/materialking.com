<?php
class backtrack extends CI_Controller 
{
	private $limit = 10;
	
	function backtrack() 
	{
		parent::__construct ();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh'); 
		}
		$this->load->dbforge();
		$this->load->library ('form_validation');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$this->load->model('admin/backtrack_model');
		$this->load->model('admin/company_model');
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
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$data ['title'] = "Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}
	
	function index($offset = 0) 
	{
		$uri_segment = 4;
		$offset = $this->uri->segment ($uri_segment);
		$quotes = $this->backtrack_model->get_quotes ();
		
		$count = count ($quotes);
		
		$items = array();
		$companyarr = array();
		if ($count >= 1) 
		{	
			foreach ($quotes as $quote) 
			{
				$awarded = $this->quote_model->getawardedbid($quote->id);
				$items[$quote->ponum]['quote'] = $quote;
				if($awarded)
				{
					if($awarded->items && $this->backtrack_model->checkReceivedPartially($awarded->id))
					{
						foreach($awarded->items as $item)
						{
						    $checkcompany = true;
						    $checkitemname = true;
						    
						    if(@$_POST['searchcompany'])
						    {
						        $checkcompany = $item->company == @$_POST['searchcompany'];
						    }
						    
						    if(@$_POST['searchitem'])
						    {
						        if(strpos($item->itemname, @$_POST['searchitem'])!== FALSE)
						        {
						            $checkitemname = true;
						        }
						        else
						        {
						            $checkitemname = false;
						        }
						    }
						    
					        if($item->company){
					        $companyarr[] = $item->company;	
						    $item->etalog = $this->db->where('company',$item->company)
                            			->where('quote',$quote->id)
                            			->where('itemid',$item->itemid)
                            			->get('etalog')->result();
					        }
					        
					        $pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quote->id)->where('company',$item->company)
			                        ->where('itemid',$item->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;
                             $item->pendingshipments=$pendingshipments;
					        
							if($item->received < $item->quantity && $checkcompany && $checkitemname)
							{
								$item->companyname = @$item->companydetails->title;
								if(!$item->companyname)
									$item->companyname = '&nbsp;';
								$item->ponum = $quote->ponum;
								$item->duequantity = $item->quantity - $item->received;
								if(!isset($items[$quote->ponum]['items']))
									$items[$quote->ponum]['items'] = array();
								$items[$quote->ponum]['items'][]=$item;
								
								$messagesql = "SELECT * FROM ".$this->db->dbprefix('message')." WHERE quote='{$quote->id}' AND company='{$item->company}' ORDER BY senton ASC";
								
								
								if(!isset($items[$quote->ponum]['messages'][$item->company]))
									$items[$quote->ponum]['messages'][$item->company] = array();
								$items[$quote->ponum]['messages'][$item->company]['companyname'] = $item->companyname;
								//$items[$quote->ponum]['messages'][$item->company]['sql'] = $messagesql;
								if($this->db->query($messagesql)->result())
								{
									$result = $this->db->query($messagesql)->result();
									//foreach($result as $msgrow)
									$items[$quote->ponum]['messages'][$item->company]['messages']=$result;
								}
							}
						}
						
					}
				}
			}
			//echo '<pre>';print_r($quotes);die;
		
    		if($this->session->userdata('usertype_id')==3)
    		{
    			$data['backtracks'] = array();
    			foreach($items as $item)
    			{
    			    $this->db->where('quote',$item['quote']->id);
    			    $this->db->where('userid',$this->session->userdata('id'));
    			    $check = $this->db->get('quoteuser')->row();
    			    if($check)
    			    {
    			        $data['backtracks'][]=$item;
    			    }
    			}
    		}
    		else
    		{
		        $data['backtracks'] = $items;
    		}
		}
	
		if(!$items)
		{
		    $this->data['message'] = 'No Records';
		}
		
		if(count($companyarr)>1){
        	$companyimplode = implode(",",$companyarr);
        	$companystr = "AND c.id in (".$companyimplode.")";
        }else 
        	$companystr = "";		
		
        $query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."' {$companystr}";
        $data['companies'] = $this->db->query($query)->result();
        
        $data['quotes'] = $quotes;
		$data ['addlink'] = '';
		$data ['heading'] = 'Backorder Items';
		
		$uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		$data['settingtour']=(isset($setting[0]->tour)) ? $setting[0]->tour : '';  
		
		$this->load->view ('admin/backtrack', $data);
	}
	
//=========================================================================	
	
	function export($offset = 0)
	{
		$uri_segment = 4;
		$offset = $this->uri->segment ($uri_segment);
		$quotes = $this->backtrack_model->get_quotes();
	
		$count = count ($quotes);
	
		$items = array();
		if ($count >= 1)
		{
			foreach ($quotes as $quote)
			{
				$awarded = $this->quote_model->getawardedbid($quote->id);
				$items[$quote->ponum]['quote'] = $quote;
				if($awarded)
				{
					if($awarded->items && $this->backtrack_model->checkReceivedPartially($awarded->id))
					{
						foreach($awarded->items as $item)
						{
							$checkcompany = true;
							$checkitemname = true;
	
							if($item->received < $item->quantity && $checkcompany && $checkitemname)
							{
								$item->companyname = @$item->companydetails->title;
								if(!$item->companyname)
									$item->companyname = '&nbsp;';
								$item->ponum = $quote->ponum;
								$item->duequantity = $item->quantity - $item->received;
								if(!isset($items[$quote->ponum]['items']))
									$items[$quote->ponum]['items'] = array();
								$items[$quote->ponum]['items'][]=$item;
	
								$messagesql = "SELECT * FROM ".$this->db->dbprefix('message')." WHERE quote='{$quote->id}' AND company='{$item->company}' ORDER BY senton ASC";
	
	
								if(!isset($items[$quote->ponum]['messages'][$item->company]))
									$items[$quote->ponum]['messages'][$item->company] = array();
								$items[$quote->ponum]['messages'][$item->company]['companyname'] = $item->companyname;
								//$items[$quote->ponum]['messages'][$item->company]['sql'] = $messagesql;
								if($this->db->query($messagesql)->result())
								{
									$result = $this->db->query($messagesql)->result();
									//foreach($result as $msgrow)
									$items[$quote->ponum]['messages'][$item->company]['messages']=$result;
								}
							}
						}
	
					}
				}
			}
			//echo '<pre>';print_r($quotes);die;
	
			if($this->session->userdata('usertype_id')==3)
			{
				$data['backtracks'] = array();
				foreach($items as $item)
				{
					$this->db->where('quote',$item['quote']->id);
					$this->db->where('userid',$this->session->userdata('id'));
					$check = $this->db->get('quoteuser')->row();
					if($check)
					{
						$data['backtracks'][]=$item;
					}
				}
			}
			else
			{
				$data['backtracks'] = $items;
			}
		}
		if(!$items)
		{
			$this->data['message'] = 'No Records';
		}
		$query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
		$data['companies'] = $this->db->query($query)->result();
	
		$data['quotes'] = $quotes;
		$data ['addlink'] = '';
		$data ['heading'] = 'Backorder Items';
			
		//=======================================================================
	
					
		$header[] = array('Report type', 'Backorders' , '' , '' , '' , '' , '' , '' , '');		
				
		if($this->session->userdata('managedprojectdetails'))
		{	
			$header[] = array('Project Title', $this->session->userdata('managedprojectdetails')->title , '' , '' , '' , '' , '' , '' , '');
			$header[] = array('', '' , '' , '' , '' , '' , '' , '' , '');
		}		
	
		foreach($data['backtracks'] as $backtrack)
		{
			if(@$backtrack['items'])
			{
	
				//     heading
					
					
				$header[] = array('PO#', $backtrack['quote']->ponum , '' , '' , '' , '' , '' , '' , '');
					
				$header[] = array('', '' , '' , '' , '' , '' , '' , '' , '');	
					
				$header[] = array('PO#', 'Item Code' , 'Item Name' , 'Company' , 'Due Qty.' , 'Unit' , 'ETA' ,'Cost Code', 'Notes');
				
			
				//$sheet_name = $backtrack['quote']->ponum;
				foreach($backtrack['items'] as $item)
				{
	
					$header[] = array($item->ponum, $item->itemcode,  $item->itemname ,  $item->companyname ,$item->duequantity, $item->unit ,$item->daterequested ,$item->costcode , $item->notes);
				}
										
				foreach($backtrack['messages'] as $cmp_messg)
				{
					if(isset($cmp_messg['messages']))
					{
							
						$header[] = array('' , '' , '' , '' , '' , '' , '');
						$header[] = array('' , '' , '' , '' , '' , '' , '');
						$header[] = array('' , '' , '' , '' , '' , '' , '');
						$header[] = array('Messages for ' , $backtrack['quote']->ponum , '' , '' , '' , '' , '');
						$header[] = array('From' , 'To' , 'Message' , 'Date/Time' , '' , '' , '');
	
						foreach($cmp_messg['messages'] as $c)
						{
							if(isset($c->from))
								$header[] = array($c->from , $c->to , $c->message , $c->senton , '' , '' , '');
						}
					}
				}
	
	
					
				$header[] = array('' , '' , '' , '' , '' , '' , '');
				$header[] = array('' , '' , '' , '' , '' , '' , '');
					
	
			}// outer if
		}// outer foreach
			
		createXls('backtrack', $header);
		die();
			
	
	}
	
//=========================================================================	

	
	function sendbacktrack($quoteid)
	{
		$company = $_POST['company'];
		
		$quote = $this->quote_model->get_quotes_by_id($quoteid);
		$awarded = $this->quote_model->getawardedbid($quoteid);
		$backtracks = array();
		$c = $this->company_model->get_companys_by_id($company);//$backtrack['company']);
		
		
		$key = md5($c->id.'--'.date('YmdHisu'));
		$insertarray = array(
					'quote'=>$awarded->quote,
					'company'=>$c->id,
					'senton'=>date('Y-m-d'),
					'invitation'=>$key,
					'purchasingadmin'=>$quote->purchasingadmin
					);
		//print_r($insertarray);die;
		$this->quote_model->db->where(array(
					'quote'=>$awarded->quote,
					'company'=>$c->id
					));
		$this->quote_model->db->delete('backtrack');
        $this->quote_model->db->insert('backtrack',$insertarray);
        
        $notification = array(
            'quote' => $quote->id,
            'company' => $c->id,
            'ponum' => $quote->ponum,
            'category' => 'Backorder',
            'senton' => date('Y-m-d H:i'),
            'isread' => '0',
            'purchasingadmin' => $this->session->userdata('purchasingadmin')
        );
        $this->db->insert('notification', $notification);
        
		$link = base_url().'home/backtrack/'.$key;
	    $body = "Dear ".$c->title.",<br><br>
	    		 
	  	Please update us on the estimated delivery dates for the following still due items off of PO# ".$quote->ponum.":  <br><br>		 
	    <a href='$link' target='blank'>$link</a>
	    And let us know the delivery date of remaining items.
	    ";
	    //$this->load->model('admin/settings_model');
	    $settings = (array)$this->settings_model->get_current_settings ();
	    $this->load->library('email');
		$this->email->clear(true);
        $this->email->from($settings['adminemail'], "Administrator");
        $this->email->to($settings['adminemail']); 
        $emails = explode(',',$c->email);
        if($emails)
        foreach($emails as $email)
        {
        	$this->email->cc($email); 
        }
        $sql = "SELECT u.email FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('quoteuser')." qu
	        	WHERE qu.userid=u.id AND qu.quote=".$quote->id;
        $purchaseusers = $this->db->query($sql)->result();
        foreach($purchaseusers as $pu)
        {
        	$this->email->cc($pu->email);
        }
        
        $this->email->subject('Backorder update for PO# '.$quote->ponum);
        $this->email->message($body);	
        $this->email->set_mailtype("html");
        $this->email->send();
        
        
        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">ETA Requested to the company "'.$c->title.'" for "'.$quote->ponum.'"</div></div>');
        redirect('admin/backtrack');
	}
	
}
?>