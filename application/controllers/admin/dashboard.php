<?php
class Dashboard extends CI_Controller 
{
	function Dashboard() {
		parent::__construct ();

		$this->load->helper('url');
		$this->load->library('session');
		if(!$this->session->userdata('id'))
		{
			redirect ( 'admin/login/index');
		}
		$data ['title'] = 'Statistics';
		$this->load->dbforge();
		$this->load = new My_Loader();
		$this->load->library ( array ('table', 'session'));
		$this->load->model('admin/statmodel');
		$this->load->model('admin/quote_model');
		$this->load->model('admin/project_model');
		$this->load->model('admin/settings_model');
		//$this->load->helper('timezone');
		//date_default_timezone_set(bd_time());		
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
		$this->load->template ( '../../templates/admin/template', $data);
	}
	
		
	function export()
	{
	
		$id = $this->session->userdata('id');
		if(!$id)
		{
			redirect ( 'admin/login/index');
			die;
		}
		$mp = $this->session->userdata('managedprojectdetails');
		$data['projects']  = $this->statmodel->getProjects();
		$data['companies'] = $this->db->get('company')->result();
		if($this->session->userdata('usertype_id')>1)
			$this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
		if($mp)
			$this->db->where('project',$mp->id);
		$data['costcodes'] = $this->db->get('costcode')->result();
	
		$data['itemcodes'] = $this->db->get('item')->result();
	
		$data['quotes'] = $this->quote_model->get_quotes('dashboard',$mp?$mp->id:'');
		$data['directquotes'] = $this->quote_model->get_Direct_Quotes('dashboard',$mp?$mp->id:'');
		$invited = 0;
		$pending = 0;
		$awarded = 0;
		if($data['quotes'])
		foreach($data['quotes'] as $quote)
		{
			if($this->quote_model->getinvited($quote->id))
				$invited++;
			if($this->quote_model->getpendingbids($quote->id))
				$pending++;
			if($this->quote_model->getawardedbid($quote->id))
				$awarded++;
		}
	
		$data['invited'] = $invited;
		$data['pending'] = $pending;
		$data['awarded'] = $awarded;
			
		$data['networkjoinedcompanies'] = array();
		$sql = "SELECT c.*, acceptedon FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
			WHERE c.id=n.company AND n.status='Active' AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
		$query = $this->db->query($sql);
		$netcomps = $query->result();
		$settings = $this->settings_model->get_current_settings();
		$id = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($id);

		 
		foreach($netcomps as $nc)
		{
			$pa = $this->session->userdata('purchasingadmin');
			$this->db->where('purchasingadmin',$pa);
			$this->db->where('company',$nc->id);
			$tier = $this->db->get('purchasingtier')->row();
			if($tier)
			{
				$nc->credit = $tier->creditlimit;
				$nc->totalcredit = $tier->totalcredit;
			}
			else
			{
				$nc->credit = '';
				$nc->totalcredit = '';
			}
			$query = "SELECT
		    			(SUM(r.quantity*ai.ea) + (SUM(r.quantity*ai.ea) * ".$settings->taxpercent." / 100))
		    			totalunpaid FROM
		    			".$this->db->dbprefix('received')." r, ".$this->db->dbprefix('awarditem')." ai
						WHERE r.awarditem=ai.id AND r.paymentstatus!='Paid' AND ai.company='".$nc->id."'
							AND ai.purchasingadmin='$pa'";
			//echo $query.'<br>';
			$nc->due = $this->db->query($query)->row()->totalunpaid;
			$nc->due = round($nc->due,2);
			//echo $nc->due.' - ';
			$query = "SELECT (SUM(od.quantity * od.price) + (SUM(od.quantity * od.price) * o.taxpercent / 100))
		    	orderdue
                FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
	                WHERE od.orderid=o.id AND o.type='Manual' AND od.paymentstatus!='Paid' AND od.accepted!=-1
	                AND o.purchasingadmin='$pa' AND od.company='".$nc->id."'";
			$manualdue = $this->db->query($query)->row()->orderdue;
			$manualdue = round($manualdue,2);
			//echo $manualdue.' <br/> ';
			$nc->due += $manualdue;
	
			$data['networkjoinedcompanies'][] = $nc;
		}
	
		if($this->session->userdata('managedprojectdetails'))
		{
				
			$query = "SELECT ai.costcode label, sum(ai.quantity*ai.ea) data FROM pms_awarditem ai, pms_award a, pms_quote q
            	WHERE ai.award=a.id AND a.quote=q.id AND q.pid=".$this->session->userdata('managedprojectdetails')->id."
            	GROUP by label";
			$codes = $this->db->query($query)->result();
			$costcodesjson = array();
			foreach($codes as $c)
			{
				if($c->data)
				{
					/**************
					 * Luis
					*/
					if ($this->session->userdata('usertype_id') > 1)
						$where = " and s.purchasingadmin = ".$this->session->userdata('purchasingadmin');
					else
						$where = "";
	
					$cquery = "SELECT taxrate FROM ".$this->db->dbprefix('settings')." s WHERE 1=1".$where." ";
					$taxrate = $this->db->query($cquery)->row();
	
					$sqlOrders ="SELECT SUM( od.price * od.quantity ) sumT
					FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('costcode')." cc,
							".$this->db->dbprefix('orderdetails')." od
					WHERE cc.code =  '".$c->label."'
					AND o.costcode = cc.id
					AND o.id = od.orderid
					GROUP BY o.costcode";
					$queryOrder = $this->db->query($sqlOrders);
					if($queryOrder->result()){
							
							
						$totalOrder = $queryOrder->row();
						$c->data += $totalOrder->sumT;
					}
					$c->data = round( ($c->data + ($c->data*($taxrate->taxrate/100) ) ),2);
					/*********/
					$c->label = $c->label . ' - $'.$c->data;
					$costcodesjson[]=$c;
				}
			}
	
			$data['costcodesjson'] = $costcodesjson;
		}
	
	
		//===============================================================================
	
		
		
		
		
		
		
		$report_title = 'Project Statistics';		
		$header[] = array('Report type' , $report_title , '' , '' , '' , '' , '' , '' );
		
		if($this->session->userdata('managedprojectdetails'))
		{			
			$header[] = array('Project Title' , $this->session->userdata('managedprojectdetails')->title , '' , '' , '' , '' , '' , '' );		
			
		}
		else
		{
		
			$projects_arr = array();
			foreach($data['projects'] as $prj)
			{			
				
				$projects_arr[] =  $prj->title;
			}
			
			
			
			$projects_string = implode(", ",$projects_arr);	
			
			$header[] = array('Project Title' , 'All projects('.$projects_string.')' , '' , '' , '' , '' , '' , '' );	
		
		}	
		
		
		
		
		
		
		
		
		
				
		$header[] = array('' , '' , '' , '' , '' , '' , '' , '' );
	
			
		//---------------------------------------------------------------------------
			
	
		$header[] = array('Number of Project' , 'Number of Cost Code' , 'Number of Item Codes' , 'Total Number of Direct Orders' , 'Total Number of Quotes' , 'Total Number of Quotes Requested' , 'Total Number of Quotes Pending' , 'Total Number of Awarded Quotes' );
			
	
		$companies = '';
		if($this->session->userdata('usertype_id') == 1)
		{
			//$companies = count($data['companies']);
		}
	
		$header[] = array(count($data['projects']),  count($data['costcodes']) ,  count($data['itemcodes']) , count($data['directquotes']) ,count($data['quotes']) ,$data['invited'] ,$data['pending'] ,$data['awarded']);
	
	
		//--------------------------
	
		if($this->session->userdata('usertype_id') == 2 && isset($data['networkjoinedcompanies']) && $data['networkjoinedcompanies'] != '')
		{
			$header[] = array('' , '' , '' , '' , '' , '' , '' , '' );
			$header[] = array('' , '' , '' , '' , '' , '' , '' , '' , '');
	
			$header[] = array('Company' , 'Credit Limit' , 'Credit Remaining' , 'Amount Due' , '' , '' , '' , '');
	
			foreach($data['networkjoinedcompanies'] as $njc)
			{
				$header[] = array($njc->title , $njc->totalcredit ,  $njc->credit, $njc->due , '' , '' , '' , '' );
			}
		}
			
		createXls('Statistics', $header);
		die();
	
		//===============================================================================
	
	}	
	
		
	function index() 
	{
		//echo '<pre>';print_r($data);die;
		$id = $this->session->userdata('id');
		if(!$id)
		{
			redirect ( 'admin/login/index');
			die;
		}
		$mp = $this->session->userdata('managedprojectdetails');
		$data['projects']  = $this->statmodel->getProjects();
		$data['companies'] = $this->db->get('company')->result();
		if($this->session->userdata('usertype_id')>1)
			$this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
	    if($mp)
	        $this->db->where('project',$mp->id);
		$data['costcodes'] = $this->db->get('costcode')->result();
		
		$data['itemcodes'] = $this->db->get('item')->result();
		
		$data['quotes'] = $this->quote_model->get_quotes('dashboard',$mp?$mp->id:'');
		$data['directquotes'] = $this->quote_model->get_Direct_Quotes('dashboard',$mp?$mp->id:'');
		$invited = 0;
		$pending = 0;
		$awarded = 0;
		if($data['quotes'])
		foreach($data['quotes'] as $quote)
		{
			if($this->quote_model->getinvited($quote->id))
				$invited++;
			if($this->quote_model->getpendingbids($quote->id))
				$pending++;
			if($this->quote_model->getawardedbid($quote->id))
				$awarded++;
		}
		
		$data['invited'] = $invited;
		$data['pending'] = $pending;
		$data['awarded'] = $awarded;
		
		//$data['allcompanies'] = $this->db->where('username !=', '')->get('company')->result();
		
		
		$data['networkjoinedcompanies'] = array();
		$sql = "SELECT c.*, acceptedon FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
			WHERE c.id=n.company AND n.status='Active' AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
		$query = $this->db->query($sql);
		$netcomps = $query->result();
	    $settings = $this->settings_model->get_current_settings();
	    $id = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($id);
	    
		foreach($netcomps as $nc)
		{
		    $pa = $this->session->userdata('purchasingadmin');
		    $this->db->where('purchasingadmin',$pa);
		    $this->db->where('company',$nc->id);
		    $tier = $this->db->get('purchasingtier')->row();
		    if($tier)
		    {
		        $nc->credit = $tier->creditlimit;
		        $nc->totalcredit = $tier->totalcredit;
		    }
		    else
		    {
		        $nc->credit = '';
		        $nc->totalcredit = '';
		    }
		    $query = "SELECT
		    			(SUM(r.quantity*ai.ea) + (SUM(r.quantity*ai.ea) * ".$settings->taxpercent." / 100)) 
		    			totalunpaid FROM 
		    			".$this->db->dbprefix('received')." r, ".$this->db->dbprefix('awarditem')." ai
						WHERE r.awarditem=ai.id AND r.paymentstatus!='Paid' AND ai.company='".$nc->id."' 
						AND ai.purchasingadmin='$pa'";
		    //echo $query.'<br>';
		    $nc->due = $this->db->query($query)->row()->totalunpaid;
		    $nc->due = round($nc->due,2);
		    //echo $nc->due.' - ';
		    $query = "SELECT (SUM(od.quantity * od.price) + (SUM(od.quantity * od.price) * o.taxpercent / 100))
		    	orderdue
                FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
                WHERE od.orderid=o.id AND o.type='Manual' AND od.paymentstatus!='Paid' AND od.accepted!=-1
                AND o.purchasingadmin='$pa' AND od.company='".$nc->id."'";
		    $manualdue = $this->db->query($query)->row()->orderdue;
		    $manualdue = round($manualdue,2);
		    //echo $manualdue.' <br/> ';
		    $nc->due += $manualdue;
		    
		    $data['networkjoinedcompanies'][] = $nc;
		}
		
		if($this->session->userdata('managedprojectdetails'))
		{
			
			$query = "SELECT ai.costcode label, sum(ai.quantity*ai.ea) data FROM pms_awarditem ai, pms_award a, pms_quote q
            	WHERE ai.award=a.id AND a.quote=q.id AND q.pid=".$this->session->userdata('managedprojectdetails')->id."
            	GROUP by label";
			$codes = $this->db->query($query)->result();
			$codearr = array();			
			foreach($codes as $code2)
			$codearr[] = $code2->label;
						
			$where2 = "";
			
			if(count($codearr)>0){
				$codestr = "'" .implode("', '", $codearr) . "'";
				$where2 = "AND code not in ({$codestr})";
			}
			
			$sql ="SELECT *	FROM ".$this->db->dbprefix('costcode')." WHERE 1=1 {$where2} AND project=".$this->session->userdata('managedprojectdetails')->id;
		
			if($this->session->userdata('usertype_id')>1)
			{
				$sql ="SELECT *
			FROM
			".$this->db->dbprefix('costcode')." 
			WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."' {$where2} AND project=".$this->session->userdata('managedprojectdetails')->id;
			}
		
		$query = $this->db->query ($sql);
		$cnt = count($codearr);
		if ($query->result ()) 
		{
			$result = $query->result();
			$ret = array();
			foreach($result as $item)
			{
			
			$query2 = "SELECT cc.code label, SUM( od.price * od.quantity ) data, o.shipping 
					FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('costcode')." cc, ".$this->db->dbprefix('orderdetails')." od WHERE cc.id =  ".$item->id."
					AND o.costcode = cc.id
					AND o.id = od.orderid
					GROUP BY o.costcode";
			$result = $this->db->query($query2)->row();
			//echo "<pre>",print_r($result); die;

			if($result){

			  if (!isset($codes[$cnt])) 
    			$codes[$cnt] = new stdClass();	
			$codes[$cnt]->label = $result->label;
			$codes[$cnt]->data = $result->data;
			$codes[$cnt]->type = "new";
			$codes[$cnt]->shipping = $result->shipping;
			$cnt++;
			}
			
			}
		}		
			
			$costcodesjson = array();
			foreach($codes as $c)
			{
				if($c->data)
				{
					/**************
					 * Luis
					*/
					if ($this->session->userdata('usertype_id') > 1)
					$where = " and s.purchasingadmin = ".$this->session->userdata('purchasingadmin');
					else
					$where = "";

					$cquery = "SELECT taxrate FROM ".$this->db->dbprefix('settings')." s WHERE 1=1".$where." ";
					$taxrate = $this->db->query($cquery)->row();
						
					$sqlOrders ="SELECT SUM( od.price * od.quantity ) sumT, o.shipping 
					FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('costcode')." cc, 
							".$this->db->dbprefix('orderdetails')." od
					WHERE cc.code =  '".$c->label."'
					AND o.costcode = cc.id
					AND o.id = od.orderid
					GROUP BY o.costcode";
					$queryOrder = $this->db->query($sqlOrders);
					if($queryOrder->result() && (!isset($c->type))){
					
							$totalOrder = $queryOrder->row();
							$c->data += $totalOrder->sumT;
							$c->shipping = $totalOrder->shipping;
					}
					$c->data = round( ($c->data + ($c->data*($taxrate->taxrate/100) ) ),2);
					if(isset($c->shipping))
						$c->data = $c->data + $c->shipping;
						
					/*********/
					$c->label = $c->label . ' - $'.$c->data;
					$costcodesjson[]=$c;
				}
			}
			/*
			$costcodesjson = json_encode($costcodesjson);
			$costcodesjson = str_replace('"label"', 'label', $costcodesjson);
			$costcodesjson = str_replace('"data"', 'data', $costcodesjson);
			$costcodesjson = str_replace('data:"', 'data:', $costcodesjson);
			$costcodesjson = str_replace('"}', '}', $costcodesjson);
			*/
			//print_r($costcodesjson);die;
			$data['costcodesjson'] = $costcodesjson;
		}
		
		$invoices = $this->quote_model->getinvoices();
		//$invoicespay = $this->quote_model->getpaymentrequestedorders($this->session->userdata('purchasingadmin'));
		
		/*$bcks = $this->quote_model->getBacktracks($this->session->userdata('purchasingadmin'));
		$backtracks = array();
		foreach($bcks as $bck)
		{
			$backtracks[]=$bck;
		}*/
		$this->load->model('admin/backtrack_model');
		$quotes = $this->backtrack_model->get_quoteswithoutprj ();
		//echo "<pre>",print_r($quotes);
		if(isset($quotes[0]))
		$count = count ($quotes[0]);		
		else 
		$count = 0;	
		$items = array();
		$companyarr = array();
		if ($count >= 1) 
		{	
			foreach ($quotes[0] as $quote) 
			{
				$awarded = $this->quote_model->getawardedbid($quote->id);
				$items[$quote->ponum]['quote'] = $quote;
				if($awarded)
				{
					if($awarded->items && $this->backtrack_model->checkReceivedPartially($awarded->id))
					{
						foreach($awarded->items as $item)
						{
							if(date('Y-m-d', strtotime( $item->daterequested)) < date('Y-m-d')) {
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
									
							}
						  }	
						}
						
					}
				}
			}
			
		
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
		
		//echo "<pre>",print_r($data['backtracks']); die;
		
		$messagesql = "SELECT m.* FROM 
		".$this->db->dbprefix('message')." m WHERE m.purchasingadmin='{$this->session->userdata('purchasingadmin')}' and m.senton between DATE_SUB(now(), INTERVAL 1 WEEK) AND now();  ";
		
		$msgs = $this->db->query($messagesql)->result();
		
		
		$wherequote = "";
		$wherequote = "and STR_TO_DATE(q.podate,'%m/%d/%Y') between DATE_SUB(curdate(), INTERVAL 1 WEEK) AND curdate()";
					
		$quotesql = "SELECT q.* FROM 
		".$this->db->dbprefix('quote')." q WHERE q.purchasingadmin='{$this->session->userdata('purchasingadmin')}' {$wherequote} ";
		
		$newquotes = $this->db->query($quotesql)->result();
		
		
		$whereawardquote = "";
		$whereawardquote = "and a.awardedon between DATE_SUB(now(), INTERVAL 1 WEEK) AND now()";
					
		$awarquotesql = "SELECT q.*,a.awardedon FROM 
		".$this->db->dbprefix('quote')." q join ".$this->db->dbprefix('award')." a on q.id = a.quote and q.purchasingadmin = a.purchasingadmin WHERE q.purchasingadmin='{$this->session->userdata('purchasingadmin')}' {$whereawardquote} ";
		
		$awardquotes = $this->db->query($awarquotesql)->result();
				
		
		
		$wherecostcode = "";
		$wherecostcode .= "and c.creation_date between DATE_SUB(curdate(), INTERVAL 1 WEEK) AND curdate()";
					
		$costcodesql = "SELECT c.*, p.title FROM 
		".$this->db->dbprefix('costcode')." c left join ".$this->db->dbprefix('project')." p on c.project = p.id  WHERE c.purchasingadmin='{$this->session->userdata('purchasingadmin')}' {$wherecostcode} ";
		
		$newcostcodes = $this->db->query($costcodesql)->result();
		
		
		
		$whereproject = "";
		$whereproject = "and p.creation_date between DATE_SUB(curdate(), INTERVAL 1 WEEK) AND curdate()";
					
		$projectsql = "SELECT p.title, p.creation_date FROM 
		".$this->db->dbprefix('project')." p WHERE p.purchasingadmin='{$this->session->userdata('purchasingadmin')}' {$whereproject} ";
		
		$newprojects = $this->db->query($projectsql)->result();
		
		
		$whereusers = "";
		$whereusers = "and created_date between DATE_SUB(now(), INTERVAL 1 WEEK) AND now() and  purchasingadmin='{$this->session->userdata('purchasingadmin')}'";
					
		$userssql = "SELECT username, created_date FROM 
		".$this->db->dbprefix('users')." u WHERE 1=1 {$whereusers} ";
		
		$users = $this->db->query($userssql)->result();
			
		
		$wherenetwork = "";
		$wherenetwork = "and n.acceptedon between DATE_SUB(now(), INTERVAL 1 WEEK) AND now()";
					
		$networksql = "SELECT n.*, c.title FROM 
		".$this->db->dbprefix('network')." n left join ".$this->db->dbprefix('company')." c on n.company = c.id  WHERE n.purchasingadmin='{$this->session->userdata('purchasingadmin')}' {$wherenetwork} ";
	
		$networks = $this->db->query($networksql)->result();		
		
		if($invoices)
		$data['invoices'] = $invoices;
		/*if($invoicespay)
		$data['invoicespay'] = $invoicespay;
		if($backtracks)
		$data['backorders'] = $backtracks;*/	
		if($msgs)
		$data['msgs'] = $msgs;		
		if($newquotes)
		$data['newquotes'] = $newquotes;
		if($awardquotes)
		$data['awardquotes'] = $awardquotes;
		if($newcostcodes)
		$data['newcostcodes'] = $newcostcodes;
		if($newprojects)
		$data['newprojects'] = $newprojects;
		if($users)
		$data['users'] = $users;
		if($networks)
		$data['networks'] = $networks;
		if(empty($setting))
		$data['settingtour']=$setting;
		else
		$data['settingtour']=$setting[0]->tour;
		
		$data['viewname'] = 'dashboard';
		
		$this->load->view ('admin/dashboard', $data);
	}
	
	function acceptreq($id)
	{
		$userid = $this->session->userdata('id');
		if(!$userid)
		{
			redirect ( 'admin/login/index');
			die;
		}
		$this->db->where('id',$id);
		$this->db->where('totype','users');
		$this->db->where('toid',$this->session->userdata('id'));
		$this->db->where('status','Pending');
		$row = $this->db->get('joinrequest')->row();
		if($row)
		{
			$this->db->where('id',$id);
			$this->db->where('totype','users');
			$this->db->where('toid',$this->session->userdata('id'));
			$this->db->where('status','Pending');
			$this->db->update('joinrequest',array('status'=>'Accepted'));
			
			$insert = array();
			$insert['company'] = $row->fromid;
			$insert['purchasingadmin'] = $this->session->userdata('id');
			$insert['request'] = $row->id;
			$insert['acceptedon'] = date('Y-m-d H:i:s');
			$insert['status'] = 'Active';
			$this->db->insert('network',$insert);
		}
		redirect('admin/dashboard');
	}
	
	function rejectreq($id)
	{
		$userid = $this->session->userdata('id');
		if(!$userid)
		{
			redirect ( 'admin/login/index');
			die;
		}
		$this->db->where('id',$id);
		$this->db->where('totype','users');
		$this->db->where('toid',$this->session->userdata('id'));
		$this->db->where('status','Pending');
		$row = $this->db->get('joinrequest')->row();
		if($row)
		{
			$this->db->where('id',$id);
			$this->db->where('totype','users');
			$this->db->where('toid',$this->session->userdata('id'));
			$this->db->where('status','Pending');
			$this->db->update('joinrequest',array('status'=>'Rejected'));
		}
		redirect('admin/dashboard');
	}
	
	function payall()
	{
		$userid = $this->session->userdata('id');
		if(!$userid)
		{
			redirect ( 'admin/login/index');
			die;
		}
		if(!@$_POST['company'])
		{
			redirect ( 'admin/dashboard');
			die;
		}
		$company = $_POST['company'];
		$pa = $this->session->userdata('purchasingadmin');
		$query = "UPDATE ".$this->db->dbprefix('received')." r SET paymentstatus='Paid' 
				  WHERE r.awarditem IN (SELECT id FROM ".$this->db->dbprefix('awarditem')." WHERE company='$company')
				";
		//echo $query;
		$this->db->query($query);
	    $this->session->set_flashdata('message', 'Company due paid successfully');
		redirect('admin/dashboard');
		
	}
	
	function project()
	{
		$userid = $this->session->userdata('id');
		if(!$userid)
		{
			redirect ( 'admin/login/index');
			die;
		}
		if($this->input->post('pid') != 0)
		{
		    
			$pid = $this->input->post('pid');
			$temp['managedproject'] = $pid;
			$temp['managedprojectdetails'] = $this->project_model->get_projects_by_id($pid);
			$this->session->set_userdata($temp);
		    redirect('admin/dashboard');
		    die;
			if($this->session->userdata('usertype_id')<3)
			{
				redirect('admin/quote/index/'.$this->input->post('pid'));
			}
			else
			{
				redirect('admin/purchaseuser/quotes');
			}
		}
		else
		{
			$this->session->unset_userdata("managedproject");
			$this->session->unset_userdata("managedprojectdetails");
			redirect('admin/dashboard');
		}
	}
	
	function application()
	{
		$userid = $this->session->userdata('id');
		if(!$userid)
		{
			redirect ( 'admin/login/index');
			die;
		}
	    $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
	    $appl = $this->db->get('application')->row();
	    if(!$appl)
	    {
	        $this->db->insert('application',array('purchasingadmin'=>$this->session->userdata('purchasingadmin')));
    	    $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
    	    $appl = $this->db->get('application')->row();
	    }
	    $sql = "SELECT * FROM ".$this->db->dbprefix('applicationattachment')." WHERE purchasingadmin=".$this->session->userdata('purchasingadmin');
	    $qry = $this->db->query($sql);
	    $attachmentData = $qry->result_array();	    
	    $data['attachmentdata'] = $attachmentData;
	    $data['appl'] = $appl;
	    $this->load->view ('admin/application', $data);
	}
	
	function saveappl()
	{
		$userid = $this->session->userdata('id');
		if(!$userid)
		{
			redirect ( 'admin/login/index');
			die;
		}
				
		if(isset($_FILES['UploadFile']['name']))
		{        
	        $count=0;
	        foreach ($_FILES['UploadFile']['name'] as $filename) 
	        {
	            if(isset($_FILES['UploadFile']['tmp_name'][$count]))
				if(is_uploaded_file($_FILES['UploadFile']['tmp_name'][$count]))
				{
					$ext = end(explode('.', $_FILES['UploadFile']['name'][$count]));
					$nfn = md5(uniqid().date('YmdHi')).'.'.$ext;
					if(move_uploaded_file($_FILES['UploadFile']['tmp_name'][$count], "uploads/attachments/".$nfn))
					{
						$savedata = array('purchasingadmin'=>$this->session->userdata('purchasingadmin'),
										  'attachmentname'=>$nfn,
										  'attachmentpath'=> "uploads/attachments/"
										 );
										
						$this->db->insert('applicationattachment',$savedata);
					}
					 $count=$count + 1;
				}
	        }
		}
	    $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
	    $_POST['aboutyourcompany'] = @$_POST['aboutyourcompany']?implode(', ', $_POST['aboutyourcompany']):'';
	    $this->db->update('application', $_POST);
	    $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Form saved successfully.</div>');
		redirect('admin/dashboard/application');
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
