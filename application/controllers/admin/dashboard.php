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
	
		$header[] = array('Number of Project' , 'Number of Cost Code' , 'Number of Item Codes' , 'Total Number of Direct Orders' , 'Total Number of Quotes' , 'Total Number of Quotes Requested' , 'Total Number of Quotes Pending' , 'Total Number of Awarded Quotes' , 'Number of Companies');
			
	
		$companies = '';
		if($this->session->userdata('usertype_id') == 1)
		{
			//$companies = count($data['companies']);
		}
	
		$header[] = array(count($data['projects']),  count($data['costcodes']) ,  count($data['itemcodes']) , count($data['directquotes']) ,count($data['quotes']) ,$data['invited'] ,$data['pending'] ,$data['awarded'] ,$companies );
	
	
		//--------------------------
	
		if($this->session->userdata('usertype_id') == 2 && isset($data['networkjoinedcompanies']) && $data['networkjoinedcompanies'] != '')
		{
			$header[] = array('' , '' , '' , '' , '' , '' , '' , '' , '');
			$header[] = array('' , '' , '' , '' , '' , '' , '' , '' , '');
	
			$header[] = array('Company' , 'Credit Limit' , 'Credit Remaining' , 'Amount Due' , '' , '' , '' , '' , '');
	
			foreach($data['networkjoinedcompanies'] as $njc)
			{
				$header[] = array($njc->title , $njc->totalcredit ,  $njc->credit, $njc->due , '' , '' , '' , '' , '');
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
		
		$invoices = $this->quote_model->getpendinginvoices($this->session->userdata('purchasingadmin'));
		$invoicespay = $this->quote_model->getpaymentrequestedorders($this->session->userdata('purchasingadmin'));
		
		$bcks = $this->quote_model->getBacktracks($this->session->userdata('purchasingadmin'));
		$backtracks = array();
		foreach($bcks as $bck)
		{
			$backtracks[]=$bck;
		}
		
		$events = $this->quote_model->get_items();
		
		$sql = "SELECT last_logged_date FROM ".$this->db->dbprefix('users')." WHERE purchasingadmin ='".$this->session->userdata('purchasingadmin')."' and usertype_id = '".$this->session->userdata('usertype_id')."'";
		$resultid = $this->db->query($sql)->result(); 
		$whereres = "";
		if(@$resultid){
			$whereres = "and m.senton > '".$resultid[0]->last_logged_date."'";
			$purchaserloggeddate = $resultid[0]->last_logged_date;
		}else {
			$whereres = "";
			$purchaserloggeddate = "";
		}
		
		$messagesql = "SELECT m.* FROM 
		".$this->db->dbprefix('message')." m WHERE m.purchasingadmin='{$this->session->userdata('purchasingadmin')}' {$whereres} ";
		
		$msgs = $this->db->query($messagesql)->result();		
		
		
		$wherequote = "";
		$wherequote = "and STR_TO_DATE(q.podate,'%m/%d/%Y') between DATE_SUB(curdate(), INTERVAL 1 WEEK) AND curdate()";
					
		$quotesql = "SELECT q.* FROM 
		".$this->db->dbprefix('quote')." q WHERE q.purchasingadmin='{$this->session->userdata('purchasingadmin')}' {$wherequote} ";
		
		$newquotes = $this->db->query($quotesql)->result();
		
		
		$whereawardquote = "";
		$whereawardquote = "and a.awardedon between DATE_SUB(curdate(), INTERVAL 1 WEEK) AND curdate()";
					
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
		$whereusers = "and regdate between DATE_SUB(curdate(), INTERVAL 1 WEEK) AND curdate()";
					
		$userssql = "SELECT companyname, regdate FROM 
		".$this->db->dbprefix('users')." u WHERE 1=1 and u.purchasingadmin='{$this->session->userdata('purchasingadmin')}' {$whereusers} ";
		
		$users = $this->db->query($userssql)->result();
			
		
		$wherenetwork = "";
		$wherenetwork = "and n.acceptedon between DATE_SUB(now(), INTERVAL 1 WEEK) AND now()";
					
		$networksql = "SELECT n.*, c.title FROM 
		".$this->db->dbprefix('network')." n left join ".$this->db->dbprefix('company')." c on n.company = c.id  WHERE n.purchasingadmin='{$this->session->userdata('purchasingadmin')}' {$wherenetwork} ";
	
		$networks = $this->db->query($networksql)->result();		
		
		if($invoices)
		$data['invoices'] = $invoices;
		if($invoicespay)
		$data['invoicespay'] = $invoicespay;
		if($backtracks)
		$data['backorders'] = $backtracks;
		if($events)
		$data['events'] = $events;
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