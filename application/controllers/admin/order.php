<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends CI_Controller 
{
	public function Order()
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
		$this->load->model('admin/statmodel');
		$this->load->model('admin/company_model');
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
		$this->load->model('admin/quote_model');
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$data ['title'] = "Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}
	
	public function index()
	{
		$this->orders();
	}
	
function orders_export()
	{
		$search = '';
		$filter = '';
		
		if(!@$_POST)
		{
			$_POST['searchfrom'] = date("m/d/Y", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );;
			$_POST['searchto'] = date('m/d/Y');
		}
		
		
		$sql = "SELECT o.*,od.orderid as odorderid, sum(od.price*od.quantity) as totalprice  
                FROM ".$this->db->dbprefix('order')." o join ".$this->db->dbprefix('orderdetails')." od on o.id = od.orderid 
				WHERE purchasingadmin=".$this->session->userdata('id')."
				$search
				$filter		
				GROUP BY o.id 
				ORDER BY purchasedate DESC";
			
		$orders = $this->db->query($sql)->result();
		$data['orders'] = array();
		foreach($orders as $order)
		{
			
			if(!is_null($order->project))
			{
				$sql = "SELECT *
				FROM ".$this->db->dbprefix('project')." p
				WHERE id=".$order->project;
				$project = $this->db->query($sql)->result();
//<<<<<<< HEAD
				if($project)
				$order->prjName = "assigned to ".$project[0]->title;
				else
				{
					$order->prjName = "";
				}
			
///=======
		//		$order->prjName = "Assigned to ".$project[0]->title;
//>>>>>>> 281a9c3788222760bb00d9d7be97eb0142db72a3
			}else{
				$order->prjName = "Pending Project Assignment";
			}
			
			if(!is_null($order->costcode)){
				$sql = "SELECT *
				FROM ".$this->db->dbprefix('costcode')." p
				WHERE id=".$order->costcode;
				$project = $this->db->query($sql)->result();
				$order->codeName = "Cost Code ".$project[0]->code;
			}else{
				$order->codeName = "Pending Cost Code Assignment";
			}
			$data['orders'][]=$order;
			$query = "SELECT c.title company, sum(quantity*price) total , o.taxpercent, od.status, od.paymentstatus    
                      FROM ".$this->db->dbprefix('orderdetails')." od, ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('order')." o   
                      WHERE od.company=c.id AND od.orderid='".$order->id."' and  od.orderid= o.id GROUP BY c.id";  
            $order->details = $this->db->query($query)->result(); 
		}
		$data['title_orders'] = "Orders";
		
		
		//$this->load->view('admin/order/list',$data);
			
		//=========================================================================================
		
		$header[] = array('Report type' , 'Orders','' , '' , '' , '' , '','','','');	
		
		if($this->session->userdata('managedprojectdetails'))
		{	
			$header[] = array('Project Title', $this->session->userdata('managedprojectdetails')->title ,'' , '' , '' , '' , '','','','');	
			$header[] = array('' , '','' , '' , '' , '' , '','','','');	
		}	
		
		
		
		$header[] = array('Order#' , 'Ordered On','Project' , 'Type' , 'Txn ID' , 'Amount' , 'Company','Paid Status','Order Status','Total');				
			
		$total = 0; 
        $oldorderid = ""; 
		$i = 0;	
			
				
		foreach($data['orders'] as $order)
		{
			$i++;
			if($order->id != $oldorderid){ 
			$total = 0; 
			$total +=  ($order->totalprice) + ($order->totalprice)*$order->taxpercent/100; 
			}else{ 
				$total +=  ($order->totalprice) + ($order->totalprice)*$order->taxpercent/100; 
			} 
	
			$code_name = $order->prjName.','.$order->codeName;
			
			
			//---------------------
			$icounter              = 1;
			
			$detail_company        = '';
			$detail_paymentstatus  = '';
			$detail_status         = '';
			$detail_total          = '';
			
			
			foreach($order->details as $detail)
			{
				if($icounter == 1)
				{
					$detail_company        = $detail->company;
					$detail_paymentstatus  = $detail->paymentstatus;
					if($detail->status == "Void") 
						$detail_status =  "Declined"; 
					else
						$detail_status =  $detail->status;
					
					$detail_total   = round(($detail->total + ($detail->total*$detail->taxpercent)/100 ),2)	;
				}
				$icounter++; 		
			}
			
			$header[] = array($order->ordernumber , date('m/d/Y',strtotime($order->purchasedate)) , $code_name , $order->type , $order->txnid , "$ ".round($total,2).chr(160) , $detail_company, $detail_paymentstatus, $detail_status, "$ ".$detail_total.chr(160));	
		
			$oldorderid = $order->id;
		}
				
		createXls('orders', $header);  			
		die();	
		
		//===============================================================================
				
	}
	
	// ORDER PDF
	function orders_pdf()
	{
		$search = '';
		$filter = '';
		
		if(!@$_POST)
		{
			$_POST['searchfrom'] = date("m/d/Y", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );;
			$_POST['searchto'] = date('m/d/Y');
		}
		
		
		$sql = "SELECT o.*,od.orderid as odorderid, sum(od.price*od.quantity) as totalprice  
                FROM ".$this->db->dbprefix('order')." o join ".$this->db->dbprefix('orderdetails')." od on o.id = od.orderid 
				WHERE purchasingadmin=".$this->session->userdata('id')."
				$search
				$filter		
				GROUP BY o.id 
				ORDER BY purchasedate DESC";
			
		$orders = $this->db->query($sql)->result();
		$data['orders'] = array();
		foreach($orders as $order)
		{
			
			if(!is_null($order->project))
			{
				$sql = "SELECT *
				FROM ".$this->db->dbprefix('project')." p
				WHERE id=".$order->project;
				$project = $this->db->query($sql)->result();
//<<<<<<< HEAD
				if($project)
				$order->prjName = "assigned to ".$project[0]->title;
				else
				{
					$order->prjName = "";
				}
			
///=======
		//		$order->prjName = "Assigned to ".$project[0]->title;
//>>>>>>> 281a9c3788222760bb00d9d7be97eb0142db72a3
			}else{
				$order->prjName = "Pending Project Assignment";
			}
			
			if(!is_null($order->costcode)){
				$sql = "SELECT *
				FROM ".$this->db->dbprefix('costcode')." p
				WHERE id=".$order->costcode;
				$project = $this->db->query($sql)->result();
				$order->codeName = "Cost Code ".$project[0]->code;
			}else{
				$order->codeName = "Pending Cost Code Assignment";
			}
			$data['orders'][]=$order;
			$query = "SELECT c.title company, sum(quantity*price) total , o.taxpercent, od.status, od.paymentstatus    
                      FROM ".$this->db->dbprefix('orderdetails')." od, ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('order')." o   
                      WHERE od.company=c.id AND od.orderid='".$order->id."' and  od.orderid= o.id GROUP BY c.id";  
            $order->details = $this->db->query($query)->result(); 
		}
		$data['title_orders'] = "Orders";
		
		
		//$this->load->view('admin/order/list',$data);
			
		//=========================================================================================
		
		$header[] = array('Report type:' , 'Orders','' , '' , '' , '' , '','','','');	
		
		if($this->session->userdata('managedprojectdetails'))
		{	
			$header[] = array('<b>Project Title</b>', $this->session->userdata('managedprojectdetails')->title ,'' , '' , '' , '' , '','','','');	
			$header[] = array('' , '','' , '' , '' , '' , '','','','');	
		}	
		
		
		
		$header[] = array('<b>Order#</b>' , '<b>Ordered On</b>','<b>Project</b>' , '<b>Type</b>' , '<b>Txn ID</b>' , '<b>Amount</b>' , '<b>Company</b>','<b>Paid Status</b>','<b>Order Status</b>','<b>Total</b>');				
			
		$total = 0; 
        $oldorderid = ""; 
		$i = 0;	
			
				
		foreach($data['orders'] as $order)
		{
			$i++;
			if($order->id != $oldorderid){ 
			$total = 0; 
			$total +=  ($order->totalprice) + ($order->totalprice)*$order->taxpercent/100; 
			}else{ 
				$total +=  ($order->totalprice) + ($order->totalprice)*$order->taxpercent/100; 
			} 
	
			$code_name = $order->prjName.','.$order->codeName;
			
			
			//---------------------
			$icounter              = 1;
			
			$detail_company        = '';
			$detail_paymentstatus  = '';
			$detail_status         = '';
			$detail_total          = '';
			
			
			foreach($order->details as $detail)
			{
				if($icounter == 1)
				{
					$detail_company        = $detail->company;
					$detail_paymentstatus  = $detail->paymentstatus;
					if($detail->status == "Void") 
						$detail_status =  "Declined"; 
					else
						$detail_status =  $detail->status;
					
					$detail_total   = round(($detail->total + ($detail->total*$detail->taxpercent)/100 ),2)	;
				}
				$icounter++; 		
			}
			if($order->txnid != ''){ $ordertxn = $order->txnid ;} else { $ordertxn = '';}
			$header[] = array($order->ordernumber , date('m/d/Y',strtotime($order->purchasedate)) , $code_name , $order->type , $ordertxn , "$ ".round($total,2).chr(160) , $detail_company, $detail_paymentstatus, $detail_status, "$ ".$detail_total.chr(160));	
		
			$oldorderid = $order->id;
		}
		$headername = "MY PURCHASED ITEMS";
    	createOtherPDF('orders', $header,$headername);
    	die();		

		
		//===============================================================================
				
	}
	
	function orders()
	{
		$search = '';
		$filter = '';
		$filter2 = '';
		if(!@$_POST)
		{
			$_POST['searchfrom'] = date("m/d/Y", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );;
			$_POST['searchto'] = date('m/d/Y');
		}
		
		if(@$_POST)
		{
			if(@$_POST['searchfrom'] && @$_POST['searchto'])
			{
				$fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
				$todate = date('Y-m-d', strtotime($_POST['searchto']));
				$search = " AND STR_TO_DATE(purchasedate,'%Y-%m-%d') >= '$fromdate'
				AND STR_TO_DATE(purchasedate,'%Y-%m-%d') <= '$todate'";
			}
			elseif(@$_POST['searchfrom'])
			{
				$fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
				$search = " AND STR_TO_DATE(purchasedate,'%Y-%m-%d') >= '$fromdate'";
			}
			elseif(@$_POST['searchto'])
			{
				$todate = date('Y-m-d', strtotime($_POST['searchto']));
				$search = " AND STR_TO_DATE(purchasedate,'%Y-%m-%d') <= '$todate'";
			}
			if(@$_POST['ordernumber'])
			{
				$filter .= " AND o.ordernumber='".$_POST['ordernumber']."'";
			}
			if(@$_POST['searchcompany'])
			{
				$filter .= " AND od.company='".$_POST['searchcompany']."'";
			}
			if(@$_POST['searchpaymentstatus'])
			{
				$filter .= " AND od.paymentstatus='".$_POST['searchpaymentstatus']."'";
			}
			if(@$_POST['searchorderstatus'])
			{
				$filter .= " AND od.status='".$_POST['searchorderstatus']."'";
			}
			if(@$_POST['searchproject'])
			{
				$filter .= " AND o.project='".$_POST['searchproject']."'";
			}
			if(@$_POST['searchcostcode'])
			{
				$filter .= " AND o.costcode='".$_POST['searchcostcode']."'";
			}
		}
		$sql = "SELECT o.*,od.orderid as odorderid,od.paymentnote, sum(od.price*od.quantity) as totalprice  
                FROM ".$this->db->dbprefix('order')." o join ".$this->db->dbprefix('orderdetails')." od on o.id = od.orderid 
				WHERE purchasingadmin=".$this->session->userdata('id')."
				$search
				$filter		
				GROUP BY o.id 
				ORDER BY purchasedate DESC";
		//echo $sql;
	/*	$sql = "SELECT *
				FROM ".$this->db->dbprefix('order')." o,".$this->db->dbprefix('order')." p
				WHERE o.purchasingadmin=".$this->session->userdata('id')." AND o.project=p.id";*/
		$orders = $this->db->query($sql)->result();
		$data['orders'] = array();
		foreach($orders as $order)
		{
			if(!is_null($order->project)){
				$sql = "SELECT *
				FROM ".$this->db->dbprefix('project')." p
				WHERE id=".$order->project;
				$project = $this->db->query($sql)->result();
				if($project)
				$order->prjName = "Assigned to ".$project[0]->title;
			}else{
				$order->prjName = "Pending Project Assignment";
			}
			
			if(!is_null($order->costcode)){
				$sql = "SELECT *
				FROM ".$this->db->dbprefix('costcode')." p
				WHERE id=".$order->costcode;
				$project = $this->db->query($sql)->result();
				$order->codeName = "Cost Code ".$project[0]->code;
			}else{
				$order->codeName = "Pending Cost Code Assignment";
			}
			$data['orders'][]=$order;
			
			if(@$_POST['searchcompany'])
			{
				$filter2 .= " AND od.company='".$_POST['searchcompany']."'";
			}
			if(@$_POST['searchpaymentstatus'])
			{
				$filter2 .= " AND od.paymentstatus='".$_POST['searchpaymentstatus']."'";
			}
			if(@$_POST['searchorderstatus'])
			{
				$filter2 .= " AND od.status='".$_POST['searchorderstatus']."'";
			}
			if(@$_POST['searchproject'])
			{
				$filter2 .= " AND o.project='".$_POST['searchproject']."'";
			}
			if(@$_POST['searchcostcode'])
			{
				$filter2 .= " AND o.costcode='".$_POST['searchcostcode']."'";
			}
			$query = "SELECT c.title company, sum(quantity*price) total , o.taxpercent, od.status, od.paymentstatus ,od.shipping   
                      FROM ".$this->db->dbprefix('orderdetails')." od, ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('order')." o   
                      WHERE od.company=c.id AND od.orderid='".$order->id."' and  od.orderid= o.id {$filter2} GROUP BY c.id";  
            $order->details = $this->db->query($query)->result(); 
		}
		$data['title_orders'] = "Orders";
		
				$sql2 = "SELECT o.*,od.company as company   
                FROM ".$this->db->dbprefix('order')." o join ".$this->db->dbprefix('orderdetails')." od on o.id = od.orderid 
				WHERE purchasingadmin=".$this->session->userdata('id')." ORDER BY purchasedate DESC";
		
		$orders2 = $this->db->query($sql2)->result();
		$companyarr = array();
		$projectarr = array();
		$costcodearr = array();
		foreach($orders2 as $order2){
			if(isset($order2->company)){
				if($order2->company!="")
				$companyarr[] = $order2->company;
			}
			
			if(isset($order2->project)){
				if($order2->project!="")
				$projectarr[] = $order2->project;
			}
			
			if(isset($order2->costcode)){
				if($order2->costcode!="")
				$costcodearr[] = $order2->costcode;
			}		
		}
		
		
		if(count($companyarr)>1){
        	$companyimplode = implode(",",$companyarr);
        	$companystr = "AND c.id in (".$companyimplode.")";
        }else 
        	$companystr = "";
		
        
        if(count($projectarr)>1){
        	$projectimplode = implode(",",$projectarr);
        	$projectstr = "AND p.id in (".$projectimplode.")";
        }else 
        	$projectstr = "";
        	
        	
        if(count($costcodearr)>1){
        	$costimplode = implode(",",$costcodearr);
        	$costcodestr = "AND c.id in (".$costimplode.")";
        }else 
        	$costcodestr = "";	
        			
		
		$query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."' {$companystr}";
        $data['companies'] = $this->db->query($query)->result();
		
        $wherep = "";
        if ($this->session->userdata('usertype_id') > 1)
                $wherep = " AND purchasingadmin =".$this->session->userdata('purchasingadmin');               
        $queryp = "SELECT p.* FROM ".$this->db->dbprefix('project')." p WHERE 1=1 {$wherep} {$projectstr}";
        $data['projects'] = $this->db->query($queryp)->result();
                
        $wherecost = "";
        if ($this->session->userdata('usertype_id') > 1)
                $wherecost = " AND purchasingadmin =".$this->session->userdata('purchasingadmin');               
        $querycost = "SELECT c.* FROM ".$this->db->dbprefix('costcode')." c WHERE 1=1 {$wherecost} {$costcodestr}";
        $data['costcode'] = $this->db->query($querycost)->result();   
		
		$this->load->view('admin/order/list',$data);
	}
	
	
	function allorders()//super admin
	{
		$sql = "SELECT o.*, SUM(quantity*price) total
				FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
				WHERE od.orderid = o.id
				GROUP BY o.id
				ORDER BY purchasedate DESC";
		$orders = $this->db->query($sql)->result();
		$data['orders'] = array();
		foreach($orders as $order)
		{
			if($order->purchasingadmin)
			{
				$this->db->where('id',$order->purchasingadmin);
				$order->purchaser = $this->db->get('users')->row();
			}
			else
			{
				$order->purchaser = new stdClass();
				$order->purchaser->companyname = 'Guest';
			}
			$query = "SELECT c.title company, sum(quantity*price) total , o.taxpercent , od.shipping shipping 
                      FROM ".$this->db->dbprefix('orderdetails')." od, ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('order')." o  
                      WHERE od.company=c.id AND od.orderid='".$order->id."' and  od.orderid= o.id GROUP BY c.id"; 
			$order->details = $this->db->query($query)->result();
			$data['orders'][]=$order;
		}
		//echo '<pre>'; print_r($data['orders']);die;
		$this->load->view('admin/order/listall',$data);
	}
	
	function details_export($id)
	{
		$this->db->where('id',$id);
		$order = $this->db->get('order')->row();
		if(!$order)
			redirect('order');
	
		if($order->purchasingadmin)
		{
			$this->db->where('id',$order->purchasingadmin);
			$order->purchaser = $this->db->get('users')->row();
		}
		else
		{
			$order->purchaser->companyname = 'Guest';
		}
		$this->db->where('orderid',$id);
		$orderdetails = $this->db->get('orderdetails')->result();
	
		$transfers = $this->db->where('orderid',$id)->get('transfer')->result();
		foreach($transfers as $transfer)
		{
			$config = $this->config->config;
			require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
			Stripe::setApiKey($config['STRIPE_API_KEY']);
	
			$info = Stripe_Transfer::retrieve($transfer->transferid);
			$this->db->where('id',$transfer->id)->update('transfer',array('status'=>$info['status']));
		}
		$sql = "SELECT t.*, c.title companyname FROM
			   ".$this->db->dbprefix('transfer')." t, ".$this->db->dbprefix('company')." c
				   WHERE t.orderid='$id' AND t.company=c.id";
		$transfers = $this->db->query($sql)->result();
		$data['transfers'] = $transfers;
		if(!is_null($order->project)){
			$this->db->where('id',$order->project);
			$prj = $this->db->get('project')->row();
			$order->prjName = $prj->title;
		}
		$data['order'] = $order;
		if(!is_null($order->costcode)){
			$this->db->where('id',$order->costcode);
			$costcodes = $this->db->get('costcode')->result();
			$data['costcodes'] = $costcodes;
		}
		$data['orderitems'] = array();
	
		$companyamounts = array();
		$companies = array();
		foreach($orderdetails as $item)
		{
			$this->db->where('itemid',$item->itemid);
			$this->db->where('type','Supplier');
			$itemdetails = $this->db->get('companyitem')->row();
	
			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
				
			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
				
			$item->itemdetails = $itemdetails;
				
			$bankaccount = $this->db->where('company',$item->company)->get('bankaccount')->row();
			$item->bankaccount = $bankaccount;
				
			$data['orderitems'][]=$item;
				
			if(!isset($companyamounts[$item->company]))
			{
				$company = $this->db->where('id',$item->company)->get('company')->row();
				$companies[]=$company;
				$company->amount = $item->quantity * $item->price;
				$company->paymentstatus = $item->paymentstatus;
				$company->paymenttype = $item->paymenttype;
				$company->paymentnote = $item->paymentnote;
					
				$bankaccount = $this->db->where('company',$item->company)->get('bankaccount')->row();
				$item->bankaccount = $bankaccount;
					
				$company->bankaccount = $bankaccount;
				 
				$companyamounts[$item->company] = $company;
				$companyamounts[$item->company]->status = $item->status;
				$companyamounts[$item->company]->accepted = $item->accepted;
			}
			else
			{
				$companyamounts[$item->company]->amount += $item->quantity * $item->price;
			}
		}
		$data['companyamounts'] = $companyamounts;
		$data['companies'] = $companies;
		$data['orderid'] = $id;
		$pa = $order->purchasingadmin;
		$messages = $this->db->where('orderid',$id)->order_by('senton')->get('ordermessage')->result();
		$data['messages'] = array();
		foreach($messages as $msg)
		{
			if(($msg->fromtype=='users' && $msg->fromid==$pa)||($msg->totype=='users' && $msg->toid==$pa))
			{
				$from = $this->db->where('id',$msg->fromid)->get($msg->fromtype)->row();
				$msg->fromname = $msg->fromtype=='company'?$from->title:@$from->companyname;
				$to = $this->db->where('id',$msg->toid)->get($msg->totype)->row();
				$msg->toname = $msg->totype=='company'?$to->title:@$to->companyname;
				 
				$data['messages'][]=$msg;
			}
		}
		//echo '<pre>';print_r($companyamounts);die;
		$this->load->view('admin/order/details',$data);
	
	
	
		//=========================================================================================
	
		//-----------------------orderitems-----------------------------
			
		$order = 	$data['order'];
	
	
		$header[] = array('Order items for order#' , $order->ordernumber,'' , '' , '' , '' , '');
		$header[] = array('Item Code' , 'Quantity','Price' , 'Total' , 'Status' , '' , '');
	
		$orderitems = $data['orderitems'];
		if($orderitems)
		{
			$i = 0;
			$gtotal = 0;
			foreach($orderitems as $item)
			{
				$total = $item->quantity * $item->price;
				$gtotal+=$total;
				$i++;
	
				$o_status = '';
	
				if($item->status=="Void")
				{ $o_status =  "Declined";
				}else
				{$o_status =  $item->status;}
	
	
				$header[] = array($item->itemdetails->itemname , $item->quantity,'$ '.formatPriceNew($item->price) , '$ '.formatPriceNew(number_format($total,2)) , $o_status , ' ' , '');
			}
				
			$taxpercent   = $order->taxpercent;
			$tax          = $gtotal * $taxpercent/100;
			$totalwithtax = $tax+$gtotal;
	
			$header[] = array('' , '','Total' , '$ '.formatPriceNew(number_format($gtotal,2)) , '' , '','');
			$header[] = array('' , '','Tax' , '$ '.formatPriceNew(number_format($tax,2) , '' , '',''));
			$header[] = array('' , '','Total' , '$ '.formatPriceNew(number_format($totalwithtax,2)) , '' , '','');
		}
	
	
		//---------------------------companyamounts----------------------------------------------
		$order           = $data['order'];
		$companyamounts  = $data['companyamounts'];
	
	
		if($companyamounts && $order->type=='Manual')
		{
			$header[] = array('' , '','' , '' , '' , '' , '');
			$header[] = array('Payments for order' , '','' , '' , '' , '' , '');
			$header[] = array('Company' , 'Amount','Tax' , 'Payment' , 'Type' , 'Notes/Check No./Txn Id' , 'Status');
				
			$i = 0;
			foreach($companyamounts as $item)
			{
				$i++;
				$tax = $item->amount * $order->taxpercent / 100;
				$tax = number_format($tax,2);
				$c_status = '';
				if($item->status=="Void") $c_status =  "Declined"; else $c_status =  $item->status;
	
				$header[] = array($item->title , '$ '.formatPriceNew($item->amount) ,'$ '.formatPriceNew($tax) , $item->paymentstatus , $item->paymenttype, $item->paymentnote , $c_status);
			}
		}
	
		//-----------------messages-----------------------------------------
		$messages   = $data['messages'];
		if($messages)
		{
				
			$header[] = array('' , '','' , '' , '' , '' , '');
			$header[] = array('Messages'  , '','' , '' , '' , '' , '');
			$header[] = array('Date' , 'Subject','From' , 'To' , 'Message' , '' , '');
	
			foreach($messages as $message)
			{
				$header[] = array(date('m/d/Y',strtotime($message->senton)) , $message->subject,$message->fromname , $message->toname, $message->message , '' , '');
			}
		}
	
		//-----------------------transfers-----------------------------------------
	
		$transfers   = $data['transfers'];
	
		if($transfers)
		{
			$header[] = array('' , '','' , '' , '' , '' , '');
			$header[] = array('Transfers'  , '','' , '' , '' , '' , '');
			$header[] = array('Transferid' , 'Company','Amount' , 'Status' , '' , '' , '');
	
			$i = 0;
			foreach($transfers as $item)
			{
				$i++;
				//$item->amount = number_format($item->amount + ($item->amount * $order->taxpercent/100),2);
	
				$header[] = array($item->transferid , $item->companyname,'$ '.$item->amount.chr(160) , $item->status , '' , '' , '');
			}
		}
	
		//-----------------------costcodes-----------------------------------------
	
		if(isset($data['costcodes']))
		{
			$costcodes = $data['costcodes'];
				
			$header[] = array('' , '','' , '' , '' , '' , '');
			$header[] = array('Costcodes'  , '','' , '' , '' , '' , '');
			$header[] = array('Cost Code' , 'Cost','' , '' , '' , '' , '');
	
			foreach($costcodes as $cc)
			{
				$header[] = array($cc->code , $cc->cost ,'' , '' , '' , '' , '');
			}
		}
	
		//-----------------------------------------------------------------------
			
	
	
		createXls('Orderdetails', $header);
		die();
	
		//===============================================================================
			
	
	}
		// ORDER DETAILS PDF
	function details_pdf($id)
	{
		$this->db->where('id',$id);
		$order = $this->db->get('order')->row();
		if(!$order)
			redirect('order');
	
		if($order->purchasingadmin)
		{
			$this->db->where('id',$order->purchasingadmin);
			$order->purchaser = $this->db->get('users')->row();
		}
		else
		{
			$order->purchaser->companyname = 'Guest';
		}
		$this->db->where('orderid',$id);
		$orderdetails = $this->db->get('orderdetails')->result();
	
		$transfers = $this->db->where('orderid',$id)->get('transfer')->result();
		foreach($transfers as $transfer)
		{
			$config = $this->config->config;
			require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
			Stripe::setApiKey($config['STRIPE_API_KEY']);
	
			$info = Stripe_Transfer::retrieve($transfer->transferid);
			$this->db->where('id',$transfer->id)->update('transfer',array('status'=>$info['status']));
		}
		$sql = "SELECT t.*, c.title companyname FROM
			   ".$this->db->dbprefix('transfer')." t, ".$this->db->dbprefix('company')." c
				   WHERE t.orderid='$id' AND t.company=c.id";
		$transfers = $this->db->query($sql)->result();
		$data['transfers'] = $transfers;
		if(!is_null($order->project)){
			$this->db->where('id',$order->project);
			$prj = $this->db->get('project')->row();
			$order->prjName = $prj->title;
		}
		$data['order'] = $order;
		if(!is_null($order->costcode)){
			$this->db->where('id',$order->costcode);
			$costcodes = $this->db->get('costcode')->result();
			$data['costcodes'] = $costcodes;
		}
		$data['orderitems'] = array();
	
		$companyamounts = array();
		$companies = array();
		foreach($orderdetails as $item)
		{
			$this->db->where('itemid',$item->itemid);
			$this->db->where('type','Supplier');
			$itemdetails = $this->db->get('companyitem')->row();
	
			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
				
			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
				
			$item->itemdetails = $itemdetails;
				
			$bankaccount = $this->db->where('company',$item->company)->get('bankaccount')->row();
			$item->bankaccount = $bankaccount;
				
			$data['orderitems'][]=$item;
				
			if(!isset($companyamounts[$item->company]))
			{
				$company = $this->db->where('id',$item->company)->get('company')->row();
				$companies[]=$company;
				$company->amount = $item->quantity * $item->price;
				$company->paymentstatus = $item->paymentstatus;
				$company->paymenttype = $item->paymenttype;
				$company->paymentnote = $item->paymentnote;
					
				$bankaccount = $this->db->where('company',$item->company)->get('bankaccount')->row();
				$item->bankaccount = $bankaccount;
					
				$company->bankaccount = $bankaccount;
				 
				$companyamounts[$item->company] = $company;
				$companyamounts[$item->company]->status = $item->status;
				$companyamounts[$item->company]->accepted = $item->accepted;
			}
			else
			{
				$companyamounts[$item->company]->amount += $item->quantity * $item->price;
			}
		}
		$data['companyamounts'] = $companyamounts;
		$data['companies'] = $companies;
		$data['orderid'] = $id;
		$pa = $order->purchasingadmin;
		$messages = $this->db->where('orderid',$id)->order_by('senton')->get('ordermessage')->result();
		$data['messages'] = array();
		foreach($messages as $msg)
		{
			if(($msg->fromtype=='users' && $msg->fromid==$pa)||($msg->totype=='users' && $msg->toid==$pa))
			{
				$from = $this->db->where('id',$msg->fromid)->get($msg->fromtype)->row();
				$msg->fromname = $msg->fromtype=='company'?$from->title:@$from->companyname;
				$to = $this->db->where('id',$msg->toid)->get($msg->totype)->row();
				$msg->toname = $msg->totype=='company'?$to->title:@$to->companyname;
				 
				$data['messages'][]=$msg;
			}
		}
		//echo '<pre>';print_r($companyamounts);die;
		$this->load->view('admin/order/details',$data);
	
	
	
		//=========================================================================================
	
		//-----------------------orderitems-----------------------------
			
		$order = 	$data['order'];
	
	
		$header[] = array('<b>Order#</b>' , $order->ordernumber,'' , '' , '' , '' , '');
		$header[] = array('<b>Item Code</b>' , '<b>Quantity</b>','<b>Price</b>' , '<b>Total</b>' , '<b>Status</b>' , '' , '');
	
		$orderitems = $data['orderitems'];
		if($orderitems)
		{
			$i = 0;
			$gtotal = 0;
			foreach($orderitems as $item)
			{
				$total = $item->quantity * $item->price;
				$gtotal+=$total;
				$i++;
	
				$o_status = '';
	
				if($item->status=="Void")
				{ $o_status =  "Declined";
				}else
				{$o_status =  $item->status;}
	
	
				$header[] = array($item->itemdetails->itemname , $item->quantity,'$ '.formatPriceNew($item->price) , '$ '.formatPriceNew(number_format($total,2)) , $o_status , ' ' , '');
			}
				
			$taxpercent   = $order->taxpercent;
			$tax          = $gtotal * $taxpercent/100;
			$totalwithtax = $tax+$gtotal;
	
			$header[] = array('' , '','<b>Total</b>' , '$ '.formatPriceNew(number_format($gtotal,2)) , '' , '','');
			$header[] = array('' , '','<b>Tax</b>' , '$ '.formatPriceNew(number_format($tax,2)) , '' , '','');
			$header[] = array('' , '','<b>Total</b>' , '$ '.formatPriceNew(number_format($totalwithtax,2)) , '' , '','');
		}
	
	
		//---------------------------companyamounts----------------------------------------------
		$order           = $data['order'];
		$companyamounts  = $data['companyamounts'];
	
	
		if($companyamounts && $order->type=='Manual')
		{
			$header[] = array('' , '','' , '' , '' , '' , '');
			$header[] = array('<b>Payments for order</b>' , '','' , '' , '' , '' , '');
			$header[] = array('<b>Company</b>' , '<b>Amount</b>','<b>Tax</b>' , '<b>Payment</b>' , '<b>Type</b>' , '<b>Notes/Check No./Txn Id</b>' , '<b>Status</b>');
				
			$i = 0;
			foreach($companyamounts as $item)
			{
				$i++;
				$tax = $item->amount * $order->taxpercent / 100;
				$tax = number_format($tax,2);
				$c_status = '';
				if($item->status=="Void") $c_status =  "Declined"; else $c_status =  $item->status;
	
				$header[] = array($item->title , '$ '.formatPriceNew($item->amount) ,'$ '.formatPriceNew($tax) , $item->paymentstatus , $item->paymenttype.'', $item->paymentnote.'' , $c_status.'');
			}
		}
	
		//-----------------messages-----------------------------------------
		$messages   = $data['messages'];
		if($messages)
		{
				
			$header[] = array('' , '','' , '' , '' , '' , '');
			$header[] = array('<b>Messages</b>'  , '','' , '' , '' , '' , '');
			$header[] = array('<b>Date</b>' , '<b>Subject</b>','<b>From</b>' , '<b>To</b>' , '<b>Message</b>' , '' , '');
	
			foreach($messages as $message)
			{
				$header[] = array(date('m/d/Y',strtotime($message->senton)) , $message->subject,$message->fromname , $message->toname, $message->message , '' , '');
			}
		}
	
		//-----------------------transfers-----------------------------------------
	
		$transfers   = $data['transfers'];
	
		if($transfers)
		{
			$header[] = array('' , '','' , '' , '' , '' , '');
			$header[] = array('<b>Transfers</b>'  , '','' , '' , '' , '' , '');
			$header[] = array('<b>Transferid</b>' , '<b>Company</b>','<b>Amount</b>' , '<b>Status</b>' , '' , '' , '');
	
			$i = 0;
			foreach($transfers as $item)
			{
				$i++;
				//$item->amount = number_format($item->amount + ($item->amount * $order->taxpercent/100),2);
	
				$header[] = array($item->transferid , $item->companyname,'$ '.$item->amount.chr(160) , $item->status , '' , '' , '');
			}
		}
	
		//-----------------------costcodes-----------------------------------------
	
		if(isset($data['costcodes']))
		{
			$costcodes = $data['costcodes'];
				
			$header[] = array('' , '','' , '' , '' , '' , '');
			$header[] = array('<b>Costcodes</b>'  , '','' , '' , '' , '' , '');
			$header[] = array('<b>Cost Code</b>' , '<b>Cost</b>','' , '' , '' , '' , '');
	
			foreach($costcodes as $cc)
			{
				$header[] = array($cc->code , $cc->cost ,'' , '' , '' , '' , '');
			}
		}
	
		//-----------------------------------------------------------------------
			
	
	
		 
		$headername = "ORDER ITEMS";
    	createOtherPDF('Orderdetails', $header,$headername);
    	die();
	
		//===============================================================================
			
	
	}
	
	
	function details($id)
	{
		$this->db->where('id',$id);
		$order = $this->db->get('order')->row();
		if(!$order)
			redirect('order');
	
		if($order->purchasingadmin)
		{
			$this->db->where('id',$order->purchasingadmin);
			$order->purchaser = $this->db->get('users')->row();
		}
		else
		{
			$order->purchaser->companyname = 'Guest';
		}
		
		$this->db->where('orderid',$id);
		$orderdetails = $this->db->get('orderdetails')->result();
		
		$transfers = $this->db->where('orderid',$id)->get('transfer')->result();
		//print_r($transfers);die;
		foreach($transfers as $transfer)
		{
    	    $config = $this->config->config;
    		require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
    		Stripe::setApiKey($config['STRIPE_API_KEY']);
            
            $info = Stripe_Transfer::retrieve($transfer->transferid);
            $this->db->where('id',$transfer->id)->update('transfer',array('status'=>$info['status']));
		}
		//print_r($transfers);die;
		$sql = "SELECT t.*, c.title companyname FROM 
			   ".$this->db->dbprefix('transfer')." t, ".$this->db->dbprefix('company')." c
			   WHERE t.orderid='$id' AND t.company=c.id";
	    //echo $sql;
		$transfers = $this->db->query($sql)->result();
		//print_r($transfers);die;
		$data['transfers'] = $transfers;
		//print_r($transfers);
		
		if(!is_null($order->project)){
		$this->db->where('id',$order->project);
		$prj = $this->db->get('project')->row();
		$order->prjName = $prj->title;
		}
		$data['order'] = $order;
		
		if(!is_null($order->costcode)){
			$this->db->where('id',$order->costcode);
			$costcodes = $this->db->get('costcode')->result();
			$data['costcodes'] = $costcodes;
		}
		 
		$data['orderitems'] = array();
		
		$companyamounts = array();
		$companies = array();
		foreach($orderdetails as $item)
		{
			$this->db->where('itemid',$item->itemid);
			$this->db->where('type','Supplier');
			$itemdetails = $this->db->get('companyitem')->row();
		
			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
			
			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
			
			$item->itemdetails = $itemdetails;
			
			$bankaccount = $this->db->where('company',$item->company)->get('bankaccount')->row();
			$item->bankaccount = $bankaccount;
			
			$data['orderitems'][]=$item;
			
			if(!isset($companyamounts[$item->company]))
			{
			    $company = $this->db->where('id',$item->company)->get('company')->row();
			    $companies[]=$company;
			    $company->amount = $item->quantity * $item->price;
			    $company->paymentstatus = $item->paymentstatus;
			    $company->paymenttype = $item->paymenttype;
			    $company->paymentnote = $item->paymentnote;
			
				$bankaccount = $this->db->where('company',$item->company)->get('bankaccount')->row();
				$item->bankaccount = $bankaccount;
			
			    $company->bankaccount = $bankaccount;
			    
			    $companyamounts[$item->company] = $company;
			    $companyamounts[$item->company]->status = $item->status;
			    $companyamounts[$item->company]->accepted = $item->accepted;
			}
			else
			{
			    $companyamounts[$item->company]->amount += $item->quantity * $item->price;
			}
		}
		$data['companyamounts'] = $companyamounts;
		$data['companies'] = $companies;
		$data['orderid'] = $id;
		$pa = $order->purchasingadmin;
	    $messages = $this->db->where('orderid',$id)->order_by('senton')->get('ordermessage')->result();
	    $data['messages'] = array();
	    foreach($messages as $msg)
	    {
	        if(($msg->fromtype=='users' && $msg->fromid==$pa)||($msg->totype=='users' && $msg->toid==$pa))
	        {
	            $from = $this->db->where('id',$msg->fromid)->get($msg->fromtype)->row();
	            $msg->fromname = $msg->fromtype=='company'?$from->title:@$from->companyname;
	            $to = $this->db->where('id',$msg->toid)->get($msg->totype)->row();
    	        $msg->toname = $msg->totype=='company'?$to->title:@$to->companyname;
	            
	            $data['messages'][]=$msg;
	        }
	    }
		//echo '<pre>';print_r($companyamounts);die;
		$this->load->view('admin/order/details',$data);
	}
	
	function status()
	{
		$this->db->where('id',$_POST['id']);
		$this->db->update('order',$_POST);
		redirect('admin/order/details/'.$_POST['id']);
	}
	
	function pay()
	{
	    $amount = $_POST['amount'];
		unset($_POST['amount']);
		$this->db->where('orderid',$_POST['orderid']);
		$this->db->where('company',$_POST['company']);
		$_POST['status'] = 'Pending';
		$this->db->update('orderdetails',$_POST);
		
		$company = $this->db->where('id',$_POST['company'])->get('company')->row();
		$orders = $order = $this->db->where('id',$_POST['orderid'])->get('order')->row();
		
		$c = $this->db->where('id',$this->session->userdata('id'))->get('users')->row();
		
		$data['email_body_title']  = "Dear " . $company->title;
		$data['email_body_content'] = $c->companyname." send payment for the PO ".$orders->ordernumber.";
		The following information sent:
		Payment Type : ".$_POST['paymenttype']."
		<br/>
		Payment Amount : ".$amount."
		<br/>
		Payment Note : ".$_POST['paymentnote']."
		<br/>
		Payment Date: ".date('Y-m-d')."
		<br><br>";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($c->email, $c->companyname);
		$this->email->to($company->title . ',' . $company->primaryemail);
		$this->email->subject('Payment made for the order: '.$orders->ordernumber);
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->send();
		
		
		redirect('admin/order/details/'.$_POST['orderid']);
		
	}
	
    function paybycc()
    {
		ini_set('max_execution_time', 300);
		$config = (array)$this->settings_model->get_current_settings();
		$config = array_merge($config, $this->config->config);
		
		require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
		Stripe::setApiKey($config['STRIPE_API_KEY']);
		//$myCard = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2015);
		$myCard = array('number' => $_POST['card'], 'exp_month' => $_POST['month'], 'exp_year' => $_POST['year']);
		$charge = Stripe_Charge::create(array('card' => $myCard, 'amount' => $_POST['amount'] * 100, 'currency' => 'usd' ));
		//echo $charge;
		$chargeobj = json_decode($charge);
		if(@$chargeobj->paid)
		{
		    $bankaccount = $this->db->where('company',$_POST['company'])->get('bankaccount')->row();
			$company = $this->db->where('id',$_POST['company'])->get('company')->row();
			if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
			{
	          $recbankInfo = array(
	          			'country' =>'US', 
	          			'routing_number' => $bankaccount->routingnumber, 
	          			'account_number' => $bankaccount->accountnumber
	          );

              $recObj = Stripe_Recipient::create(array(
              "name" => $company->title, 
              "type" => "individual",
              "email" => $company->primaryemail,
              "bank_account" => $recbankInfo)
              );
              
              $obj = json_decode($recObj);
              $_POST['amount'] = round($_POST['amount'],2);
              $transferObj = Stripe_Transfer::create(array(
                  "amount" => $_POST['amount'] * 100, 
                  "currency" => "usd", 
                  "recipient" => $obj->id, 
                  "description" => "Transfer for ".$company->primaryemail )
              );
              $tobj = json_decode($transferObj);
                      
              $transfer = array();
              $transfer['orderid'] = $_POST['orderid'];
              $transfer['purchasingadmin'] = $this->session->userdata('purchasignadmin');
              $transfer['company'] = $company->id;
              $transfer['amount'] = $_POST['amount'];
              $transfer['transferid'] = $tobj->id;
              $transfer['transferdate'] = date('Y-m-d H:i');
              $transfer['status'] = '';
              $this->db->insert('transfer',$transfer);
                      
              $this->db->where('orderid', $_POST['orderid'])->where('company', $company->id);
              $this->db->update('orderdetails', array('status'=>'Pending','paymentstatus'=>'Paid','paymenttype'=>'Credit Card','paymentnote'=>$chargeobj->balance_transaction));
              
              $ordernumberobj = $this->db->where('id',$_POST['orderid'])->get('order')->row();
              if(isset($ordernumberobj->ordernumber))
              $ordernumber = $ordernumberobj->ordernumber;
              else 
              $ordernumber = $_POST['orderid'];
              
              $data['email_body_title']  = "Dear {$company->title}";
			  $data['email_body_content'] = "$ {$_POST['amount']} has been transfered to your bank account for order#{$ordernumber}, 
with the transfer#{$tobj->id}.
";
              $settings = (array)$this->settings_model->get_current_settings ();
              $loaderEmail = new My_Loader();
              $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    	      $this->load->library('email');
    	      $config['charset'] = 'utf-8';
    	      $config['mailtype'] = 'html';
    	      $this->email->initialize($config);
              $this->email->from($settings['adminemail'], "Administrator");
            
              $this->email->to($company->primaryemail); 
            
              $this->email->subject('Order details from ezpzp');
              $this->email->message($send_body);	
              $this->email->set_mailtype("html");
              $this->email->send();
              
              $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice paid successfully.</div></div>');
        	}
		}
		redirect('admin/order/details/' . $_POST['orderid']);
    }
    
    function add_to_project_export($id)
    {
    	$project = $this->session->userdata('managedproject');
    
    	if(!$project)
    		redirect('admin/dashboard');
    
    
    	if($this->input->post('pid') != 0)
    	{
    		$this->db->where('id',$id);
    		$this->db->update('order',array('project'=>$this->input->post('pid'),'costcode'=>$this->input->post('ccid')));
    		redirect('admin/order');
    	}
    	else
    	{
    		$this->db->where('id',$id);
    		$order = $this->db->get('order')->row();
    		if(!$order)
    			redirect('admin/order');
    			
    		if($order->purchasingadmin)
    		{
    			$this->db->where('id',$order->purchasingadmin);
    			$order->purchaser = $this->db->get('users')->row();
    		}
    		else
    		{
    			$order->purchaser->companyname = 'Guest';
    		}
    			
    		$this->db->where('orderid',$id);
    		$orderdetails = $this->db->get('orderdetails')->result();
    			
    		if(!is_null($order->costcode)){
    			$sql = "SELECT *
				FROM ".$this->db->dbprefix('costcode')." p
				WHERE id=".$order->costcode;
    			$project = $this->db->query($sql)->result();
    			$order->codeName = "Cost Code ".$project[0]->code;
    		}else{
    			$order->codeName = "Pending Cost Code Assignment";
    		}
    			
    		$data['order'] = $order;
    		$data['orderitems'] = array();
    			
    		foreach($orderdetails as $item)
    		{
    			$this->db->where('itemid',$item->itemid);
    			$this->db->where('type','Supplier');
    			$itemdetails = $this->db->get('companyitem')->row();
    
    			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
    			 
    			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
    			 
    			$item->itemdetails = $itemdetails;
    			 
    			$this->db->where('id',$item->company);
    			$companyname = $this->db->get('company')->row();
    			$item->companyname = $companyname->title;
    				
    			$data['orderitems'][]=$item;
    		}
    
    		$data['projects']  =  $this->statmodel->getProjects();
    		$data['orderid'] = $id;
    			
    		//$this->load->view('admin/order/list_projects',$data);
    			
    			
    		//=========================================================================================
    			
    		//$header[] = array('Item Code' , 'Company','Quantity' , 'Price' , 'Price' , 'Total' , 'Status'  );
    
    		$order      = $data['order'];
    		$orderitems = $data['orderitems'];
    			
    			
    			
    		$total;$i = 0;
    		$gtotal = 0;
    		foreach($orderitems as $item)
    		{
    			$total = number_format($item->quantity * $item->price,2);
    			$gtotal+=$total;
    			$i++;
    		}
    		$total = number_format($gtotal,2);
    			
    		$tax = number_format(($order->taxpercent * $total)/100,2);
    			
    			
    			
    			
    			
    			
    			
    			
    			
    			
    			
    			
    			
    		if(@$orderitems[0]->accepted == 1)
    		{
    				
    			$header[] = array('Order Items for Order#' , $order->ordernumber , '' , '' , '' , '' );
    			$header[] = array('ORDER #' , 'DATE', 'TYPE' , '' , '' , '' );
    				
    			$header[] = array($order->ordernumber , date('m/d/Y',strtotime($order->purchasedate)), $order->type , '' , '' , '' );
    				
    			$i      = 0;
    			$gtotal = 0;
    
    			foreach($orderitems as $item)
    			{
    				$total = number_format($item->quantity * $item->price,2);
    				$gtotal+=$total;
    				$i++;
    				//log_message('debug',var_export($item,true));
    			}
    			$gd_total =  number_format($gtotal,2);
    			$gd_tax   =  $tax + number_format($gtotal,2);
    
    
    			$header[] = array('SubTotal:' , '', '$ '.formatPriceNew($gd_total) , '' , '' , '' );
    			$header[] = array('Tax:' , '', '$ '.formatPriceNew($tax) , '' , '' , ''  );
    			$header[] = array('Total:' , '', '$ '.formatPriceNew($gd_tax) , '' , '' , ''  );
    
    
    			$header[] = array('' , '', '' , '' , '' , ''  );
    			$header[] = array('' , '', '' , '' , '' , ''  );
    
    
    				
    				
    		}
    		//--------------------------------------------------------------------------------
    			
    			
    		if($orderitems)
    		{
    
    			$header[] = array('Item Code' , 'Company','Quantity' , 'Price' ,  'Total' , 'Status'  );
    				
    			$i = 0;
    			$gtotal = 0;
    			foreach($orderitems as $item)
    			{
    				$total = number_format($item->quantity * $item->price,2);
    				$gtotal+=$total;
    				$i++;
    					
    				//------------------
    				$item_status = '';
    				if($item->status=="Void") $item_status =  "Declined"; else $item_status = $item->status;
    					
    				$header[] = array($item->itemdetails->itemname , $item->companyname ,$item->quantity , '$ '.formatPriceNew($item->price) , $total , $item_status );
    			}
    
    			//-------------------------------------
    
    			$gd_tatal_2 = $tax+number_format($gtotal,2);
    
    			$header[] = array('SubTotal' , '','' , '$ '.formatPriceNew(number_format($gtotal,2)) ,  '' , ''  );
    			$header[] = array('Tax' , '','' , '$ '.formatPriceNew(number_format($tax,2)) ,  '' , ''  );
    			$header[] = array('Total' , '','' , '$ '.formatPriceNew($gd_tatal_2)  ,  '' , ''  );
    
    		}
    			
    			
    		createXls('add_to_project', $header);
    		die();
    			
    			
    		//===============================================================================
    			
    		
    	}
    }
		
	// Add to project PDF
	
	function add_to_project_pdf($id)
    {
    	$project = $this->session->userdata('managedproject');
    
    	if(!$project)
    		redirect('admin/dashboard');   
    
    	if($this->input->post('pid') != 0)
    	{
    		$this->db->where('id',$id);
    		$this->db->update('order',array('project'=>$this->input->post('pid'),'costcode'=>$this->input->post('ccid')));
    		redirect('admin/order');
    	}
    	else
    	{
    		$this->db->where('id',$id);
    		$order = $this->db->get('order')->row();
    		if(!$order)
    			redirect('admin/order');
    			
    		if($order->purchasingadmin)
    		{
    			$this->db->where('id',$order->purchasingadmin);
    			$order->purchaser = $this->db->get('users')->row();
    		}
    		else
    		{
    			$order->purchaser->companyname = 'Guest';
    		}
    			
    		$this->db->where('orderid',$id);
    		$orderdetails = $this->db->get('orderdetails')->result();
    			
    		if(!is_null($order->costcode)){
    			$sql = "SELECT *
				FROM ".$this->db->dbprefix('costcode')." p
				WHERE id=".$order->costcode;
    			$project = $this->db->query($sql)->result();
    			$order->codeName = "Cost Code ".$project[0]->code;
    		}else{
    			$order->codeName = "Pending Cost Code Assignment";
    		}
    			
    		$data['order'] = $order;
    		$data['orderitems'] = array();
    			
    		foreach($orderdetails as $item)
    		{
    			$this->db->where('itemid',$item->itemid);
    			$this->db->where('type','Supplier');
    			$itemdetails = $this->db->get('companyitem')->row();
    
    			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
    			 
    			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
    			 
    			$item->itemdetails = $itemdetails;
    			 
    			$this->db->where('id',$item->company);
    			$companyname = $this->db->get('company')->row();
    			$item->companyname = $companyname->title;
    				
    			$data['orderitems'][]=$item;
    		}
    
    		$data['projects']  =  $this->statmodel->getProjects();
    		$data['orderid'] = $id;
    			
    		//$this->load->view('admin/order/list_projects',$data);
    			
    			
    		//=========================================================================================
    			
    		//$header[] = array('Item Code' , 'Company','Quantity' , 'Price' , 'Price' , 'Total' , 'Status'  );
    
    		$order      = $data['order'];
    		$orderitems = $data['orderitems'];
    			
    			
    			
    		$total;$i = 0;
    		$gtotal = 0;
    		foreach($orderitems as $item)
    		{
    			$total = number_format($item->quantity * $item->price,2);
    			$gtotal+=$total;
    			$i++;
    		}
    		$total = number_format($gtotal,2);
    			
    		$tax = number_format(($order->taxpercent * $total)/100,2);
    			
    			
    		if(@$orderitems[0]->accepted == 1)
    		{
    				
    			$header[] = array('Order Items for Order#' , $order->ordernumber , '' , '' , '' , '' );
    			$header[] = array('<b>ORDER #</b>' , '<b>DATE</b>', '<b>TYPE</b>' , '' , '' , '' );
    				
    			$header[] = array($order->ordernumber , date('m/d/Y',strtotime($order->purchasedate)), $order->type , '' , '' , '' );
    				
    			$i      = 0;
    			$gtotal = 0;
    
    			foreach($orderitems as $item)
    			{
    				$total = number_format($item->quantity * $item->price,2);
    				$gtotal+=$total;
    				$i++;
    				log_message('debug',var_export($item,true));
    			}
    			$gd_total =  number_format($gtotal,2);
    			$gd_tax   =  $tax + number_format($gtotal,2);
    
    
    			$header[] = array('<b>SubTotal:</b>' , '', '$ '.formatPriceNew($gd_total) , '' , '' , '' );
    			$header[] = array('<b>Tax:</b>' , '', '$ '.formatPriceNew($tax) , '' , '' , ''  );
    			$header[] = array('<b>Total:</b>' , '', '$ '.formatPriceNew($gd_tax) , '' , '' , ''  );
    
    
    			$header[] = array('' , '', '' , '' , '' , ''  );
    			$header[] = array('' , '', '' , '' , '' , ''  );
    
   		 $headername = "ASSIGN ORDER TO PROJECT";
    	createOtherPDF('add_to_project', $header,$headername);
    	die();
    				
    				
    		}
    		//--------------------------------------------------------------------------------
    			
    			
    		if($orderitems)
    		{
    
    			$header[] = array('<b>Item Code</b>' , '<b>Company</b>','<b>Quantity</b>' , '<b>Price</b>' ,  '<b>Total</b>' , '<b>Status</b>'  );
    				
    			$i = 0;
    			$gtotal = 0;
    			foreach($orderitems as $item)
    			{
    				$total = number_format($item->quantity * $item->price,2);
    				$gtotal+=$total;
    				$i++;
    					
    				//------------------
    				$item_status = '';
    				if($item->status=="Void") $item_status =  "Declined"; else $item_status = $item->status;
    					
    				$header[] = array($item->itemdetails->itemname , $item->companyname ,$item->quantity , '$ '.formatPriceNew($item->price) , $total , $item_status );
    			}
    
    			//-------------------------------------
    
    			$gd_tatal_2 = $tax+number_format($gtotal,2);
    
    			$header[] = array('<b>SubTotal</b>' , '','' , '$ '.formatPriceNew(number_format($gtotal,2)) ,  '' , ''  );
    			$header[] = array('<b>Tax</b>' , '','' , '$ '.formatPriceNew(number_format($tax,2)) ,  '' , ''  );
    			$header[] = array('<b>Total</b>' , '','' , '$ '.formatPriceNew($gd_tatal_2)  ,  '' , ''  );
    
    		 
    	$headername = "ASSIGN ORDER TO PROJECT";
    	createPDF('add_to_project', $header,$headername);
    	die();
			}
    		//===============================================================================
    			
    		
    	}
    }
	function add_to_project($id)
	{
		$project = $this->session->userdata('managedproject');
		
		if(!$project)
			redirect('admin/dashboard');
		
		
		if($this->input->post('pid') != 0){
			$this->db->where('id',$id);
			$this->db->update('order',array('project'=>$this->input->post('pid'),'costcode'=>$this->input->post('ccid')));
			
			
			redirect('admin/order');
		}else{
			/*$this->db->select('ordernumber');
			$this->db->where('id',$id);
			$q_order = $this->db->get('order')->row();
			$data['orders'] = $q_order;*/
			
			$this->db->where('id',$id);
			$order = $this->db->get('order')->row();
			if(!$order)
				redirect('admin/order');
			
			if($order->purchasingadmin)
			{
				$this->db->where('id',$order->purchasingadmin);
				$order->purchaser = $this->db->get('users')->row();
			}
			else
			{
				$order->purchaser->companyname = 'Guest';
			}
			
			$this->db->where('orderid',$id);
			$orderdetails = $this->db->get('orderdetails')->result();
			
			if(!is_null($order->costcode)){
				$sql = "SELECT *
				FROM ".$this->db->dbprefix('costcode')." p
				WHERE id=".$order->costcode;
				$project = $this->db->query($sql)->result();
				$order->codeName = $project[0]->code;
			}else{
				$order->codeName = "Pending Cost Code Assignment";
			}
			
			$data['order'] = $order;
			$data['orderitems'] = array();
			
			foreach($orderdetails as $item)
			{
				$this->db->where('itemid',$item->itemid);
				$this->db->where('type','Supplier');
    			$itemdetails = $this->db->get('companyitem')->row();
    		
    			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
    			
    			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
    			
    			$item->itemdetails = $itemdetails;
    			
    		     $this->db->where('id',$item->company); 
				 $companyname = $this->db->get('company')->row(); 
				 $item->companyname = $companyname->title; 
					
				$data['orderitems'][]=$item;
			}
				
			$data['projects']  =  $this->statmodel->getProjects();
			$data['orderid'] = $id;
			$this->load->view('admin/order/list_projects',$data);
		}
	}
	
	function sendemail($id)
	{
		$data['email_body_title'] = $_POST['message'];
		$data['email_body_content'] = "";
		$order = $this->db->where('id',$id)->get('order')->row();
		$company = $this->db->where('id',$_POST['company'])->get('company')->row();
		$orderdetails = $this->db->where('orderid',$id)->where('company',$company->id)->get('orderdetails')->result();
	    $orderitems = array();
		foreach($orderdetails as $item)
		{
			$this->db->where('itemid',$item->itemid);
			$this->db->where('type','Supplier');
			$itemdetails = $this->db->get('companyitem')->row();
		
			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
			
			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
			
			$item->itemdetails = $itemdetails;
			
			$orderitems[]=$item;
		}
		
		$data['email_body_content'] .= "<br><br><strong>Supplier Name:</strong> {$company->title} <br><br><strong>Supplier Address:</strong> {$company->address} <br><br><strong>Supplier Phone:</strong>  {$company->phone} <br><br><strong>Order details:</strong>"; 
		
		$data['email_body_content'] .= '
			<table class="table table-bordered span12" border="1">
				 <tr><td colspan ="4">Type :'.$order->type.'</td></tr> 
            	<tr>
            		<th>Item</th>
            		<th>Price</th>
            		<th>Quantity</th>
            		<th>Total</th>
            	</tr>';
            	
                	$gtotal=0; 
                	foreach ($orderitems as $item)
                	{
                	    $total = $item->quantity*$item->quantity;
                	    $gtotal+=$total;
                         $data['email_body_content'] .= '<tr>
                            		<td>'.$item->itemdetails->itemname.'</td>
                            		<td>'.$item->price.'</td>
                            		<td>'.$item->quantity.'</td>
                            		<td>'.number_format($total,2).'</td>
                            	</tr>';
            	    }
            	 
            	    $tax = $gtotal*$order->taxpercent/100;
            	    $totalwithtax = number_format($tax+$gtotal,2);
            	
            	$data['email_body_content'] .= '<tr>
            		<td colspan="3" align="right">Total</td>
            		<td>$'.number_format($gtotal,2).'</td>
            	</tr>
            	
            	<tr>
            		<td colspan="3" align="right">Tax</td>
            		<td>$'. number_format($tax,2).'</td>
            	</tr>
            	
            	<tr>
            		<td colspan="3" align="right">Total</td>
            		<td>$'. $totalwithtax.'</td>
            	</tr>
            	
            </table>';
            	
            	$data['email_body_content'] .='<br/><br/><p>order#'.$order->ordernumber.'<br/><a href="'.base_url().'/order/details/'.$order->id.'" >Manage Order</a>';	
            	$loaderEmail = new My_Loader();
            	$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->to($company->primaryemail);
		$this->email->from($this->session->userdata('email'));
		
		$this->email->subject($_POST['subject']);
		$this->email->message($send_body);	
		$this->email->set_mailtype("html");
		$this->email->send();
		
		$om = array();
		$om['orderid'] = $id;
		$om['fromtype'] = 'users';
		$om['fromid'] = $order->purchasingadmin;
		$om['totype'] = 'company';
		$om['toid'] = $company->id;
		$om['subject'] = $_POST['subject'];
		$om['message'] = $_POST['message'];
		$om['senton'] = date('Y-m-d');
		
		$this->db->insert('ordermessage',$om);
		
		$message = "Message sent successfully.";
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('admin/order/details/'.$id);
	}

}