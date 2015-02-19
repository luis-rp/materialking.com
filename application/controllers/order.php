<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends CI_Controller 
{
	public function Order()
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
		$this->load->model ('admin/settings_model', '', TRUE);
		if($this->session->userdata('company')) $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
		 if ($this->session->userdata('company')) {    
            $data['pagetour'] = $this->companymodel->getcompanybyid($this->session->userdata('company')->id); }
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/front/template', $data);
	}
	
	public function index()
	{
		$this->orders();
	}
	
	function export()
	{
		$company = $this->session->userdata('company');
		$search = '';
		$filter = '';
			
		$sql = "SELECT DISTINCT(o.id), o.ordernumber, o.purchasedate, o.purchasingadmin, o.type, o.txnid, o.email, od.accepted, od.paymentstatus, sum(od.price*od.quantity) amount, o.taxpercent, od.status
				FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
				WHERE o.id=od.orderid AND od.company=".$company->id."
					$search $filter
					GROUP BY od.orderid
					ORDER BY o.purchasedate DESC";
					$orders = $this->db->query($sql)->result();
					$data['orders'] = array();
					foreach($orders as $order)
					{
						log_message('debug',$sql);
						if($order->purchasingadmin)
						{
							$this->db->where('id',$order->purchasingadmin);
							$order->purchaser = $this->db->get('users')->row();
						}
						else
						{
							$order->purchaser = new stdClass();
							$order->purchaser->companyname = 'Guest ('.$order->email.')';
						}
	
						$order->amount = round(($order->amount + ($order->amount*$order->taxpercent/100) ),2);
							
						$data['orders'][]=$order;
					}
					$data['company'] = $company;
					$query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('network')." n
        		  WHERE u.id=n.purchasingadmin AND n.company='".$company->id."'";
					//$query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u WHERE usertype_id=2 AND username IS NOT NULL";
					$data['purchasingadmins'] = $this->db->query($query)->result();
					$this->load->view('order/list',$data);
	
					//=========================================================================================
	
					$header[] = array('Order#' , 'Ordered by','Payment Status' , 'Order Status' , 'Ordered On' , 'Amount' , 'Type' , 'Txn Id' );
						
					foreach($data['orders'] as $order)
					{
						$order_status = '';
						if($order->status =="Void")
							$order_status =  "Declined";
						elseif($order->status =="Accepted")
						$order_status =  "Approved";
						else
							$order_status = $order->status;
	
	
						$order_date =  date("d F Y", strtotime($order->purchasedate));
	
						$header[] = array(isset($order->ordernumber) ? $order->ordernumber :'',  isset($order->purchaser->companyname) ? $order->purchaser->companyname : '' ,  isset($order->paymentstatus) ? $order->paymentstatus : '' , $order_status ,isset($order_date) ? $order_date : '' ,'$ '.formatPriceNew($order->amount) ,$order->type ,$order->txnid );
					}
	
	
					createXls('order', $header);
					die();
	
					//===============================================================================
	}
	
	//ORDER PDF
	function order_pdf()
	{
		$company = $this->session->userdata('company');
		$search = '';
		$filter = '';
			
		$sql = "SELECT DISTINCT(o.id), o.ordernumber, o.purchasedate, o.purchasingadmin, o.type, o.txnid, o.email, od.accepted, od.paymentstatus, sum(od.price*od.quantity) amount, o.taxpercent, od.status
				FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
				WHERE o.id=od.orderid AND od.company=".$company->id."
					$search $filter
					GROUP BY od.orderid
					ORDER BY o.purchasedate DESC";
					$orders = $this->db->query($sql)->result();
					$data['orders'] = array();
					foreach($orders as $order)
					{
						log_message('debug',$sql);
						if($order->purchasingadmin)
						{
							$this->db->where('id',$order->purchasingadmin);
							$order->purchaser = $this->db->get('users')->row();
						}
						else
						{
							$order->purchaser = new stdClass();
							$order->purchaser->companyname = 'Guest ('.$order->email.')';
						}
	
						$order->amount = round(($order->amount + ($order->amount*$order->taxpercent/100) ),2);
							
						$data['orders'][]=$order;
					}
					$data['company'] = $company;
					$query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('network')." n
        		  WHERE u.id=n.purchasingadmin AND n.company='".$company->id."'";
					//$query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u WHERE usertype_id=2 AND username IS NOT NULL";
					$data['purchasingadmins'] = $this->db->query($query)->result();
					$this->load->view('order/list',$data);
	
					//=========================================================================================
	
					$header[] = array('Order#' , 'Ordered by','Payment Status' , 'Order Status' , 'Ordered On' , 'Amount' , 'Type' , 'Txn Id' );
						
					foreach($data['orders'] as $order)
					{
						$order_status = '';
						if($order->status =="Void")
							$order_status =  "Declined";
						elseif($order->status =="Accepted")
						$order_status =  "Approved";
						else
							$order_status = $order->status;
	
	
						$order_date =  date("d F Y", strtotime($order->purchasedate));
	
						$header[] = array(isset($order->ordernumber) ? $order->ordernumber :'',  isset($order->purchaser->companyname) ? $order->purchaser->companyname : '' ,  isset($order->paymentstatus) ? $order->paymentstatus : '' , $order_status ,isset($order_date) ? $order_date : '' ,'$ '.formatPriceNew($order->amount) ,$order->type ,$order->txnid.'' );
					}
	
	
					 
					$headername = "PROJECT STATISTICS";
					createPDF('order', $header,$headername);
					die(); 
	
					//===============================================================================
	}
	
	function orders()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$search = '';
		$filter = '';
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
			if(@$_POST['purchasingadmin'])
			{
				if(@$_POST['purchasingadmin']=='guest')
				{
				 $filter = " AND o.purchasingadmin IS NULL ";	
				}
				else 
				{
				$filter = " AND o.purchasingadmin='".$_POST['purchasingadmin']."'";
				}
			}
			
			if(@$_POST['searchpaymentstatus'] && @$_POST['searchpaymentstatus'])
 			{
 				if(@$_POST['searchpaymentstatus']=='Unpaid')
 				{
 				$filter .= " AND (od.paymentstatus='Unpaid' OR od.paymentstatus='Requested Payment') ";
 				}
 				else 
 				{
 				$filter .= " AND od.paymentstatus='".$_POST['searchpaymentstatus']."' ";
 				}
 			}
 			if(@$_POST['searchorderstatus'] && @$_POST['searchorderstatus'])
 			{
 				$filter .= " AND od.status='".$_POST['searchorderstatus']."' ";
 			}
 			
 			if(@$_POST['ordertype'] && @$_POST['ordertype'])
 			{
 				$filter .= " AND o.type='".$_POST['ordertype']."' ";
 			}
 			
 			if(@$_POST['searchbyguest'] && @$_POST['searchbyguest'])
 			{
 				$filter .=  " AND o.email LIKE '%".trim($_POST['searchbyguest'])."%'";
 			}
 			
 			if(@$_POST['searchbykeyword'] && @$_POST['searchbykeyword'])
 			{
 				$filter .=  " AND (o.ordernumber LIKE '%".trim($_POST['searchbykeyword'])."%' OR c.title LIKE '%".trim($_POST['searchbykeyword'])."%')" ;
 			}
		}
 		
		$sql = "SELECT DISTINCT(o.id), o.ordernumber, o.purchasedate, o.purchasingadmin, o.type, o.txnid, o.email, od.accepted, od.paymentstatus, sum(od.price*od.quantity) amount, o.taxpercent, od.status  
				FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
				JOIN ".$this->db->dbprefix('company')." c ON c.id = od.company
				WHERE o.id=od.orderid AND od.company=".$company->id." 
				$search $filter 
				GROUP BY od.orderid 		
				ORDER BY o.purchasedate DESC";
		
		$orders = $this->db->query($sql)->result();
		$data['orders'] = array();
		foreach($orders as $order)
		{
			log_message('debug',$sql);
			if($order->purchasingadmin)
			{
				$this->db->where('id',$order->purchasingadmin);
				$order->purchaser = $this->db->get('users')->row();
				$order->purchaser->companyname .= '('.$order->email.')';
				
			}
			else
			{
				$order->purchaser = new stdClass();
				$order->purchaser->companyname = 'Guest ('.$order->email.')';
			}
						
			$order->amount = round(($order->amount + ($order->amount*$order->taxpercent/100) ),2);
			
			$data['orders'][]=$order;
		}
		$data['company'] = $company;
        $query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('network')." n
        		  WHERE u.id=n.purchasingadmin AND n.company='".$company->id."'";
        //$query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u WHERE usertype_id=2 AND username IS NOT NULL";
        $data['purchasingadmins'] = $this->db->query($query)->result();
		$this->load->view('order/list',$data);
	}
	
	function details($id)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
		$this->db->where('id',$id);
		$order = $this->db->get('order')->row();
		if(!$order)
			redirect('order');
	
		if($order->purchasingadmin)
		{
			$this->db->where('id',$order->purchasingadmin);
			$order->purchaser = $this->db->get('users')->row();
			//print_r($order->purchaser);die;
		}
		else
		{
		    $order->purchaser = new stdClass();
			$order->purchaser->companyname = 'Guest';
		}
		
		$this->db->where('orderid',$id);
		$this->db->where('company',$company->id);
		$orderdetails = $this->db->get('orderdetails')->result();
		//echo '<pre>';print_r($orderdetails);die;
		$data['order'] = $order;
		$data['orderitems'] = array();
		//echo '<pre>';print_r($order);die;
		foreach($orderdetails as $item)
		{
			if($item->company == $company->id)
			{
				$this->db->where('itemid',$item->itemid);
				$this->db->where('company',$item->company);
				$this->db->where('type','Supplier');
				$itemdetails = $this->db->get('companyitem')->row();
			
    			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
    			
    			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
    			
				$item->itemdetails = $itemdetails;
				
				$data['orderitems'][]=$item;
			}
		}
		if(!$data['orderitems'])
			redirect('order');
	    $messages = $this->db->where('orderid',$id)->order_by('senton')->get('ordermessage')->result();
	    $data['messages'] = array();
	    foreach($messages as $msg)
	    {
	        if(($msg->fromtype=='company' && $msg->fromid==$company->id)||($msg->totype=='company' && $msg->toid==$company->id))
	        {
	            if($msg->fromtype == 'guest')
	            {
	                $msg->fromname = $order->email;
	            }
	            else
	            {
    	            $from = $this->db->where('id',$msg->fromid)->get($msg->fromtype)->row();
	                $msg->fromname = $msg->fromtype=='company'?$from->title:@$from->companyname;
	            }
	            if($msg->totype == 'guest')
	            {
	                $msg->toname = $order->email;
	            }
	            else
	            {
    	            $to = $this->db->where('id',$msg->toid)->get($msg->totype)->row();
    	            $msg->toname = $msg->totype=='company'?$to->title:@$to->companyname;
	            }
	            
	            $data['messages'][]=$msg;
	        }
	    }
	    $data['address']= $company->address;
		$this->load->view('order/details',$data);
	}
	
	function status($id,$status)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
		$this->db->where('id',$id);
		$order = $this->db->get('order')->row();
		$orderdetails = $this->db->get_where('orderdetails',array('orderid'=>$order->id))->row();
		if(!$order)
			redirect('order');
		if($status != 'Accepted' && $status != 'Void')
		{
			redirect('order/details/'.$id);
		}
		$update = array('status'=>$status);
		if($status == 'Void')
		{ 
			if(isset($orderdetails)) {
				if($orderdetails->paymentstatus=='Paid' && $orderdetails->paymenttype=='Credit Card') {
					$data="";
					if(!$order->email)
		    		{
			    	$order->email = @$this->db->where('id',$order->purchasingadmin)->get('users')->row()->email;
		    		}
		    		
					if($order->email)
					{
						$settings = (array) $this->settings_model->get_current_settings();						
	        			$this->load->library('email');
		        		$config['charset'] = 'utf-8';
		        		$config['mailtype'] = 'html';	        			
		        		$this->email->initialize($config);
		        		$this->email->from($company->primaryemail);
		        		$this->email->to($order->email);
		        		$this->email->cc($settings['adminemail']);
		        		$subject = 'Payment verified by supplier';		        		
		        		$data['email_body_title'] = "Information Regarding Payment";
	        		$data['email_body_content'] = "Payment verified for Transaction id is {$order->txnid} for order# {$order->ordernumber} <br>Order Details<br>";
	        			$data['email_body_content'] .= $this->getorderdetails($order->id);		
			      		$loaderEmail = new My_Loader();
			        	$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);				     
		        		$this->email->subject($subject);
		        		$this->email->message($send_body);	
		        		$this->email->set_mailtype("html");
		        		$this->email->reply_to($company->primaryemail);
		        		$this->email->send();
					} } }
		    $update['paymentstatus'] = 'Unpaid';
		    $update['paymenttype'] = '';
		    $update['paymentnote'] = '';
		}
		elseif($status == 'Accepted')
		{
		    if(!$order->email)
		    {
			    $order->email = @$this->db->where('id',$order->purchasingadmin)->get('users')->row()->email;
		    }
			if($order->email)
					{
	        		$this->load->library('email');
	        		$config['charset'] = 'utf-8';
	        		$config['mailtype'] = 'html';        			
	        		$this->email->initialize($config);
	        		$this->email->from($company->primaryemail);
	        		$this->email->to($order->email);
	        		$subject = 'Payment verified by supplier';	        		
	        		$data['email_body_title'] = "Payment verified for order# {$order->ordernumber}";
	        		$data['email_body_content'] = "<strong>Supplier Name</strong>: {$company->title}<br><br><strong>Supplier Address</strong>: {$company->address} <br><br><strong>Supplier Phone:</strong>  {$company->phone} <br><br><strong>Order details:</strong>";
			        $data['email_body_content'] .= $this->getorderdetails($order->id);	
			      	$loaderEmail = new My_Loader();
			        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
	        		$this->email->subject($subject);
	        		$this->email->message($send_body);	
	        		$this->email->set_mailtype("html");
	        		$this->email->reply_to($company->primaryemail);
	        		$this->email->send();
					}			
		}
		$this->db->where('orderid',$id);
		$this->db->where('company',$company->id);
		$this->db->update('orderdetails',$update);
		redirect('order/details/'.$id);
	}
	
	
		
	function export_order($id)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
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
			$order->purchaser = new stdClass();
			$order->purchaser->companyname = 'Guest';
		}
	
		$this->db->where('orderid',$id);
		$this->db->where('company',$company->id);
		$orderdetails = $this->db->get('orderdetails')->result();
		
		$data['order'] = $order;
		$data['orderitems'] = array();
	
		foreach($orderdetails as $item)
		{
			if($item->company == $company->id)
			{
				$this->db->where('itemid',$item->itemid);
				$this->db->where('type','Supplier');
				$itemdetails = $this->db->get('companyitem')->row();
					
				$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
				 
				$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
				 
				$item->itemdetails = $itemdetails;
	
				$data['orderitems'][]=$item;
			}
		}
		if(!$data['orderitems'])
			redirect('order');
		$messages = $this->db->where('orderid',$id)->order_by('senton')->get('ordermessage')->result();
		$data['messages'] = array();
		foreach($messages as $msg)
		{
			if(($msg->fromtype=='company' && $msg->fromid==$company->id)||($msg->totype=='company' && $msg->toid==$company->id))
			{
				if($msg->fromtype == 'guest')
				{
					$msg->fromname = $order->email;
				}
				else
				{
					$from = $this->db->where('id',$msg->fromid)->get($msg->fromtype)->row();
					$msg->fromname = $msg->fromtype=='company'?$from->title:@$from->companyname;
				}
				if($msg->totype == 'guest')
				{
					$msg->toname = $order->email;
				}
				else
				{
					$to = $this->db->where('id',$msg->toid)->get($msg->totype)->row();
					$msg->toname = $msg->totype=='company'?$to->title:@$to->companyname;
				}
				 
				$data['messages'][]=$msg;
			}
		}
		//$this->load->view('order/details',$data);
	
	
	
		//=========================================================================================
	
		$order = $data['order'];
	
		$header[] = array('Order#' , 'Transaction ID' ,'Buyer Email','Buyer Company' , 'Item' , 'Quantity' , 'Price' , 'Total' );
	
		$trans_id = '';
	
		if($order->txnid)
		{
			$trans_id = $order->txnid;
		}
	
	
	
		$header[] = array($order->ordernumber, $trans_id , $order->email ,  $order->purchaser->companyname , '' ,'' ,'' ,''  );
	
		$header[] = array('' ,'' , '','' , '' , '' , '' , '' );
	
		$header[] = array('' ,'' , '','' , '' , '' , '' , '' );
	
		$orderitems = $data['orderitems'];
		$i = 0;
		$gtotal = 0;
		foreach($orderitems as $item)
		{
			$i++;
			$total = $item->quantity * $item->price;
			$gtotal += $total;
			$header[] = array('' ,'' , '','' , $item->itemdetails->itemname ,$item->quantity , '$ '.$item->price.chr(160) , '$ '.number_format($total,2).chr(160) );
		}
		 
		$taxpercent   = $order->taxpercent;
		$tax          = $gtotal*$taxpercent/100;
		$totalwithtax = $tax+$gtotal;
	
		$header[] = array('' ,'','' , '' , '' , '' , 'Tax' , '$ '.$tax.chr(160));
		$header[] = array('' , '','' , '' , '' , '' , 'Total' ,'$ '.$gtotal.chr(160));
		$header[] = array('' , '','' , '' , '' , '' , 'Total' ,'$ '.$totalwithtax.chr(160));
	
		createXls('order_detail_'.$id, $header);
		die();
	
		//===============================================================================
	
	}

	
	//ORDER DETAILS PDF
	function pdf_order($id)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
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
			$order->purchaser = new stdClass();
			$order->purchaser->companyname = 'Guest';
		}
	
		$this->db->where('orderid',$id);
		$this->db->where('company',$company->id);
		$orderdetails = $this->db->get('orderdetails')->result();
		
		$data['order'] = $order;
		$data['orderitems'] = array();
	
		foreach($orderdetails as $item)
		{
			if($item->company == $company->id)
			{
				$this->db->where('itemid',$item->itemid);
				$this->db->where('type','Supplier');
				$itemdetails = $this->db->get('companyitem')->row();
					
				$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();
				 
				$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
				 
				$item->itemdetails = $itemdetails;
	
				$data['orderitems'][]=$item;
			}
		}
		if(!$data['orderitems'])
			redirect('order');
		$messages = $this->db->where('orderid',$id)->order_by('senton')->get('ordermessage')->result();
		$data['messages'] = array();
		foreach($messages as $msg)
		{
			if(($msg->fromtype=='company' && $msg->fromid==$company->id)||($msg->totype=='company' && $msg->toid==$company->id))
			{
				if($msg->fromtype == 'guest')
				{
					$msg->fromname = $order->email;
				}
				else
				{
					$from = $this->db->where('id',$msg->fromid)->get($msg->fromtype)->row();
					$msg->fromname = $msg->fromtype=='company'?$from->title:@$from->companyname;
				}
				if($msg->totype == 'guest')
				{
					$msg->toname = $order->email;
				}
				else
				{
					$to = $this->db->where('id',$msg->toid)->get($msg->totype)->row();
					$msg->toname = $msg->totype=='company'?$to->title:@$to->companyname;
				}
				 
				$data['messages'][]=$msg;
			}
		}
		//$this->load->view('order/details',$data);
	
	
	
		//=========================================================================================
	
		$order = $data['order'];
	
		$header[] = array('Order#' , 'Transaction ID' ,'Buyer Email','Buyer Company' , 'Item' , 'Quantity' , 'Price' , 'Total' );
	
		$trans_id = '';
	
		if($order->txnid)
		{
			$trans_id = $order->txnid;
		}
	
	
	
		$header[] = array($order->ordernumber, $trans_id , $order->email ,  $order->purchaser->companyname , '' ,'' ,'' ,''  );
	
		$header[] = array('' ,'' , '','' , '' , '' , '' , '' );
	
		$header[] = array('' ,'' , '','' , '' , '' , '' , '' );
	
		$orderitems = $data['orderitems'];
		$i = 0;
		$gtotal = 0;
		foreach($orderitems as $item)
		{
			$i++;
			$total = $item->quantity * $item->price;
			$gtotal += $total;
			$header[] = array('' ,'' , '','' , $item->itemdetails->itemname ,$item->quantity , '$ '.$item->price.chr(160) , '$ '.number_format($total,2).chr(160) );
		}
		 
		$taxpercent   = $order->taxpercent;
		$tax          = $gtotal*$taxpercent/100;
		$totalwithtax = $tax+$gtotal;
	
		$header[] = array('' ,'','' , '' , '' , '' , '<b>Tax</b>' , '$ '.$tax.chr(160));
		$header[] = array('' , '','' , '' , '' , '' , '<b>Total</b>' ,'$ '.$gtotal.chr(160));
		$header[] = array('' , '','' , '' , '' , '' , '<b>Total</b>' ,'$ '.$totalwithtax.chr(160));
	
	 
		$headername = "ORDER DETAILS";
    	createPDF('order_detail_', $header,$headername);
    	die();	
		//===============================================================================
	
	}

	
	
	
	
	function accept($id,$status)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
		$this->db->where('id',$id);
		$order = $this->db->get('order')->row();
		if(!$order)
			redirect('order');
		if($status != '1' && $status != '-1')
		{
			redirect('order/details/'.$id);
		}
		$update = array('accepted'=>$status);
		if($status==1)
		$update['status'] = 'Accepted';
		if($status==-1)
		$update['status'] = 'Void';
		$this->db->where('orderid',$id);
		$this->db->where('company',$company->id);
		$this->db->update('orderdetails',$update);
		
		$this->db->where('orderid',$id);
		$orderdetail = $this->db->get('orderdetails')->result();
		if($orderdetail){

			foreach($orderdetail as $orderone){

				$this->db->where('itemid',$orderone->itemid);
				$this->db->where('company',$orderone->company);
				$this->db->where('type', 'Supplier');
				$companyitem = $this->db->get('companyitem')->row();
				//echo "<pre>",print_r($companyitem); die;
				if($companyitem){
					$bd['qtyavailable'] = $companyitem->qtyavailable-$orderone->quantity;
					$this->db->where('id',$companyitem->id);
					$this->db->update('companyitem',$bd);
				}

			}

		}
		
		//echo $id.'-'.$company->id.'-'.$status;die;
	
		$data['email_body_title'] = "";
		$order = $this->db->where('id',$id)->get('order')->row();
		$data['email_body_content'] = "Order:".$order->ordernumber;
		$data['email_body_title'] .= "<br>The following Order is : ". ($status=='1'?'Approved':'Declined');
		$data['email_body_content'] .= "<br><br><strong>Supplier Name</strong>: {$company->title}<br><br><strong>Supplier Address</strong>: {$company->address} <br><br><strong>Supplier Phone:</strong>  {$company->phone} <br><br><strong>Order details:</strong>";
		$data['email_body_content'] .= $this->getorderdetails($id);
		$data['email_body_content'] .= "<br><br><a href='".site_url('admin/order/details/'.$id)."' target='_blank'>Click here to manage/view the Order</a>";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$this->db->where('id',$order->purchasingadmin);
		$admin = $this->db->get('users')->row();
		
		$this->load->library('email');
		$config['charset'] = 'utf-8';	
		$config['mailtype'] = 'html';
			
		$this->email->initialize($config);
		$this->email->from($company->primaryemail);
		$this->email->to($admin->email);
		$subject = 'Order Status';
		$this->email->subject($subject);
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->reply_to($company->primaryemail);
		$this->email->send();
		
		redirect('order/details/'.$id);
	}
	
	function requestpayment($id)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
		$this->db->where('id',$id);
		$order = $this->db->get('order')->row();
		if(!$order)
			redirect('order');
		
		$update = array('paymentstatus'=>'Requested Payment');
		$update['paymenttype'] = '';
		$update['paymentnote'] = date('Y-m-d');
		$this->db->where('orderid',$id);
		$this->db->where('company',$company->id);
		$this->db->update('orderdetails',$update);
		
		
		$subject = "Payment requested by ".$company->title." for order# ".$order->ordernumber;
	
		$data['email_body_title'] = $company->title." has requested payment of order #".$order->ordernumber." on ".date('m/d/Y');
		$data['email_body_content'] = "<br><br>Order Details:";
		
		$data['email_body_content'] .= $this->getorderdetails($id);
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$admin = $this->db->where('id',$order->purchasingadmin)->get('users')->row();
		
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
			
		$this->email->initialize($config);
		$this->email->from($company->primaryemail);
		$this->email->to($admin->email);
		
		$this->email->subject($subject);
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->reply_to($company->primaryemail);
		$this->email->send();
		
		
		//echo $id.'-'.$company->id.'-'.$status;die;
		redirect('order/details/'.$id);
	}
	
	function sendemail($id)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
			
			if(isset($_POST['pickupaddress']))
			{
				$add=$_POST['pickupaddress'];
			}
			else {
				$add="";
			}
			
			if(isset($_POST['pickup']) && $_POST['pickup']=='on')
			{
				$data['email_body_content'] = "Pickup Address:".$add."<br>";
			}
			else 
			{
				$data['email_body_content'] = "";
			}

		$data['email_body_title'] = $_POST['message'];
		$order = $this->db->where('id',$id)->get('order')->row();
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
		$data['email_body_content'] .= "<br/>Order Status:" . (@$orderdetails[0]->accepted==1?'Approved':(@$orderdetails[0]->accepted==-1?'Declined':'Pending'));
		$data['email_body_content'] .= "<br/>Payment Status:" . @$orderdetails[0]->paymentstatus;
		$data['email_body_content'] .= "<br/><br>Order details:";
		
		$data['email_body_content'] .= $this->getorderdetails($id);	
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
			
	    $settings = (array)$this->homemodel->getconfigurations ();
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
			
		$this->email->initialize($config);
		$this->email->from($company->primaryemail);
		$this->email->to($_POST['to']);
		$subject = '';
		if(@$_POST['paymentrequest'])
		    $subject = 'PAYMENT REQUEST ';
		$this->email->subject($subject.$_POST['subject']);
		$this->email->message($send_body);	
		$this->email->set_mailtype("html");
		$this->email->reply_to($company->primaryemail);
		
		$this->email->send();
		
		$om = array();
		$om['orderid'] = $id;
		$om['fromtype'] = 'company';
		$om['fromid'] = $company->id;
		if($order->purchasingadmin)
		{
		    $om['totype'] = 'users';
		    $om['toid'] = $order->purchasingadmin;
		}
		else
		{
		    $om['totype'] = 'guest';
		}
		$om['paymentrequest'] = @$_POST['paymentrequest'];
		$om['subject'] = $_POST['subject'];
		$om['message'] = $_POST['message'];
		$om['senton'] = date('Y-m-d');
		
		$this->db->insert('ordermessage',$om);
		
		$message = "Message sent successfully.";
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">' . $message . '</div></div></div>');
        redirect('order/details/'.$id);
	}
	
	function getorderdetails($id)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
	    $order = $this->db->where('id',$id)->get('order')->row();
	    //print_r($order);die;
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
		$body = '
			<table class="table table-bordered span12" border="1" width="50%">
            	<tr>
            		<th>Item</th>
            		<th>Price</th>
            		<th>Quantity</th>
            		<th>Total</th>
            	</tr>';
            	
                	$gtotal=0; 
                	foreach ($orderitems as $item)
                	{
                	    $total = $item->price*$item->quantity;
                	    $gtotal+=$total;
                         $body .= '<tr>
                            		<td>'.$item->itemdetails->itemname.'</td>
                            		<td style="text-align:right;">'.$item->price.'</td>
                            		<td style="text-align:center;">'.$item->quantity.'</td>
                            		<td style="text-align:right;">'.number_format($total,2).'</td>
                            	</tr>';
            	    }
            	 
            	    $tax = $gtotal * $order->taxpercent / 100;
            	    $totalwithtax = number_format($tax+$gtotal+$order->shipping,2);
            	
            	$body .= '<tr><td colspan="5">&nbsp;</td> <tr>
            		<td colspan="3" align="right">Total</td>
            		<td style="text-align:right;">$'.number_format($gtotal,2).'</td>
            	</tr>
            	
            	<tr>
            		<td colspan="3" align="right">Tax</td>
            		<td style="text-align:right;">$'. number_format($tax,2).'</td>
            	</tr>
				
				<tr>
            		<td colspan="3" align="right">Shipping</td>
            		<td style="text-align:right;">$'. number_format($order->shipping,2).'</td>
            	</tr>
            	
            	<tr>
            		<td colspan="3" align="right">Total</td>
            		<td style="text-align:right;">$'. $totalwithtax.'</td>
            	</tr>
            	
            </table>';
        return $body;
	}
}
