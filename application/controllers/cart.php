<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cart extends CI_Controller 
{
	public function cart()
	{
		ini_set("memory_limit","512M");
		ini_set("max_execution_time", 700);
	    parent::__construct ();
	  
		$data ['title'] = 'Home';
		$this->load->dbforge();
		$this->load->model ('homemodel', '', TRUE);
		$this->load->model ('admin/settings_model', '', TRUE);
		$this->load->model ('items_model', '', TRUE);
		$this->load->model('admin/banner_model', '', TRUE);
		$data['banner']=$this->banner_model->display();
		$data['categorymenu'] = $this->items_model->getCategoryMenu (0) ;
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/site/template', $data);
		
	}

	public function index()
	{
		$cart = $this->session->userdata('pms_site_cart');
		if(!$cart)
			redirect('site');
		$this->data['cart'] = array();
		$canmanualorder = false;
		if($this->session->userdata('site_loggedin'))
		{
		    $canmanualorder = true;
		}
		//echo '<pre>';print_r($cart);
		foreach($cart as $item)
		{			
			
			$this->db->where('itemid',$item['itemid']);
			$this->db->where('company',$item['company']);
			$this->db->where('type','Supplier');
			$itemdetails = $this->db->get('companyitem')->row();
			
			$orgitem = $this->db->where('id',$item['itemid'])->get('item')->row();
			
			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
			
			$item['itemdetails'] = $itemdetails;
			
			
			$this->db->where('id',$item['company']);
			$item['companydetails'] = $this->db->get('company')->row();
			
			$dealitem = $this->db
            			->where('itemid',$item['itemid'])
            			->where('company',$item['company'])
			            ->where('dealactive','1')
                        ->where('qtyavailable >=','qtyreqd')
                        ->where('qtyavailable >','0')
                        ->where('dealdate >=',date('Y-m-d'))
                        ->get('dealitem')->row();
            if($dealitem)
            {
                if($item['quantity'] > $dealitem->qtyavailable)
                {
                    $item['quantity'] = $dealitem->qtyavailable;
                }
                if($dealitem->qtyreqd <= $item['quantity'] && $item['quantity'] <= $dealitem->qtyavailable)
                {
                    $item['price'] = $dealitem->dealprice;
                    $item['isdeal'] = '1';
                }
            }
			
			if($this->session->userdata('site_loggedin'))
			{
    			$this->db->where('company',$item['company']);
    			$this->db->where('purchasingadmin',$this->session->userdata('site_loggedin')->id);
    			$innw = $this->db->get('network')->row();
    			if(!$innw)
    			    $canmanualorder = false;
			}
			$this->data['cart'][$item['itemid'].':'.$item['company']]=$item;
		}
		//echo '<pre>';print_r($this->data['cart']);die;
		$temp['pms_site_cart'] = $this->data['cart'];
		$this->session->set_userdata($temp);
		
		
		$this->data['canmanualorder'] = $canmanualorder;
		$this->data['settings'] = $this->settings_model->get_current_settings();
		
		$this->load->view('site/cart', $this->data);
	}
	
	public function addtocart()
	{
		if(!$_POST)
		{
			die;
		}
		$itemid = $_POST['itemid'];
		$company = $_POST['company'];
		$price = $_POST['price'];
		$qty = @$_POST['qty']?$_POST['qty']:1;
		$isdeal = @$_POST['isdeal']?$_POST['isdeal']:1;
		//print_r($_POST);die;
		$cart = $this->session->userdata('pms_site_cart');
		$orderid = $this->session->userdata('pms_orderid');
		if(!$orderid)
		{
			$orderid = uniqid();
		}
		
		if(isset($cart[$itemid.':'.$company]))
		{
			$cart[$itemid.':'.$company]['quantity']+=$qty;
		}
		else
		{
			$cart[$itemid.':'.$company] = array();
			$cart[$itemid.':'.$company]['itemid'] = $itemid;
			$cart[$itemid.':'.$company]['company'] = $company;
			$cart[$itemid.':'.$company]['price'] = $price;
			$cart[$itemid.':'.$company]['quantity'] = $qty;
			$cart[$itemid.':'.$company]['isdeal'] = $isdeal;
		}
		
		$order['pms_orderid'] = $orderid;
		$this->session->set_userdata($order);
		$temp['pms_site_cart'] = $cart;
		$this->session->set_userdata($temp);
		
		die('Item added to cart.');
	}
	
	public function updatecartitem()
	{
		if(!$_POST)
		{
			die;
		}
		$itemid = $_POST['itemid'];
		$company = $_POST['company'];
		$quantity = $_POST['quantity'];
		//print_r($_POST);die;
		$cart = $this->session->userdata('pms_site_cart');    	
		
		if(isset($cart[$_POST['itemid'].':'.$_POST['company']]['itemdetails']->ea))
		$ea = $cart[$_POST['itemid'].':'.$_POST['company']]['itemdetails']->ea;
		else 
		$ea = 0;
		
    	$sql1 = "SELECT * FROM ".$this->db->dbprefix('qtydiscount')." WHERE company = '{$_POST['company']}' and itemid = '{$_POST['itemid']}' and qty <= '{$_POST['quantity']}' order by qty desc limit 1";
    	$result1 = $this->db->query($sql1)->row();
    	if($result1){
    		$price = $result1->price;
    		$purchasingadmin = @$this->session->userdata('site_loggedin')->id;
    		if($purchasingadmin){
    			$sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$purchasingadmin' AND company='" . $_POST['company'] . "'";


    			$sqltier = "select tierprice from " . $this->db->dbprefix('companyitem') . "
				    where itemid='".$_POST['itemid']."' AND company='" . $_POST['company'] . "' AND type = 'Supplier'";

    			$istierprice = $this->db->query($sqltier)->row();
    			if($istierprice){
    				$istier = $istierprice->tierprice;
    			}else
    			$istier = 0;

    			$tier = $this->db->query($sql)->row();
    			if ($tier && $istier)
    			{
    				$tier = $tier->tier;
    				$this->db->where('company', $_POST['company']);
    				$pt = $this->db->get('tierpricing')->row();
    				if ($pt)
    				{
    					$deviation = $pt->$tier;
    					$price = $result1->price + ($result1->price * $deviation / 100);
    					$price = number_format($price, 2);
    				}
    			}
    		} 		
			
    		$cart[$_POST['itemid'].':'.$_POST['company']]['price'] = $price;
    		
    	}else{
    		
    		$purchasingadmin = @$this->session->userdata('site_loggedin')->id;
    		if($purchasingadmin){
    			$sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$purchasingadmin' AND company='" . $_POST['company'] . "'";


    			$sqltier = "select tierprice from " . $this->db->dbprefix('companyitem') . "
				    where itemid='".$_POST['itemid']."' AND company='" . $_POST['company'] . "' AND type = 'Supplier'";

    			$istierprice = $this->db->query($sqltier)->row();
    			if($istierprice){
    				$istier = $istierprice->tierprice;
    			}else
    			$istier = 0;

    			$tier = $this->db->query($sql)->row();
    			if ($tier && $istier)
    			{
    				$tier = $tier->tier;
    				$this->db->where('company', $_POST['company']);
    				$pt = $this->db->get('tierpricing')->row();
    				if ($pt)
    				{
    					$deviation = $pt->$tier;
    					$price = $ea + ($ea * $deviation / 100);
    					$price = number_format($price, 2);
    					$cart[$_POST['itemid'].':'.$_POST['company']]['price'] = $price;
    				}
    			}
    		}else{
    			$cart[$_POST['itemid'].':'.$_POST['company']]['price'] = $ea;
    		}   		
    	}
		
		
		$cart[$itemid.':'.$company]['quantity'] = $quantity;
		$temp['pms_site_cart'] = $cart;
		$this->session->set_userdata($temp);
		die('Item updated successfully.');
	}
	
	public function removecartitem()
	{
		if(!$_POST)
		{
			die;
		}
		$itemid = $_POST['itemid'];
		$company = $_POST['company'];
		//print_r($_POST);die;
		$cart = $this->session->userdata('pms_site_cart');
		unset($cart[$itemid.':'.$company]);
		$temp['pms_site_cart'] = $cart;
		$this->session->set_userdata($temp);
		die('Item Removed Successfully.');
	}
	
	public function ccpayment()
	{
		include(APPPATH.'libraries/easypost/easypost.php');
		$cart = $this->session->userdata('pms_site_cart');
		$ordernumber = $this->session->userdata('pms_orderid');
		if(!$cart || !$ordernumber)
		{
			redirect('');
		}
		
		$data['cart']=array();
		\EasyPost\EasyPost::setApiKey('tcOjVdKjSCcDxpn14CSkjw');
		$dataitemshipping=0; $userinfoship = array();
		foreach($cart as $item)
		{
			//echo "<pre>"; print_r($item['quantity']);
			$this->db->where('itemid',$item['itemid']);
			$this->db->where('company',$item['company']);
			$this->db->where('type','Supplier');
			$itemdetails = $this->db->get('companyitem')->row();
			
			$orgitem = $this->db->where('id',$item['itemid'])->get('item')->row();
			
			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
			
			$item['itemdetails'] = $itemdetails;
				
			$this->db->where('id',$item['company']);
			$item['companydetails'] = $this->db->get('company')->row();
			
								
			///Easy Post
			
			
			$this->db->where('id',$item['itemid']);
			$current_item = $this->db->get('item')->row();
			// create addresses
			
			if($_POST){
			$vendorShipping['shippingStreet']=$_POST['shippingStreet'];
			$vendorShipping['shippingCity']=$_POST['shippingCity'];
			$vendorShipping['shippingState']=$_POST['shippingState'];
			$vendorShipping['shippingZip']=$_POST['shippingZip'];
			$vendorShipping['shippingName']=$_POST['shippingName'];
			$vendorShipping['shippingCountry']=$_POST['shippingCountry'];
			$tempshipping['pms_site_full_address'] = $vendorShipping;
			$this->session->set_userdata($tempshipping);
			}
			else
			{
				$vendorShipping = $this->session->userdata('pms_site_full_address');
			}
			
			//print_r($vendorShipping);
			$to_address_params = array("name"    => $vendorShipping['shippingName'],
					"street1" => $vendorShipping['shippingStreet'],
					"street2" => "",
					"city"    => $vendorShipping['shippingCity'],
					"state"   => $vendorShipping['shippingState'],
					"zip"     => $vendorShipping['shippingZip'],
			);
			
			try
			{
				$to_address = \EasyPost\Address::create($to_address_params);
					
				$from_address_params = array("name"    => $item['companydetails']->contact,
						"street1" => $item['companydetails']->address,
						"street2" => "",
						"city"    => $item['companydetails']->city,
						"state"   => $item['companydetails']->state,
						"zip"	  => $item['companydetails']->zip);
				
				$from_address = \EasyPost\Address::create($from_address_params);
				// create parcel
				if($current_item->weight>0)
				{
					$parcel_params = array(
							"predefined_package" => null,
							"weight"             => $current_item->weight*$item['quantity']
					);
					if($current_item->length !=  "0.0"){
						$parcel_params["length"] = $current_item->length;
					}
					if($current_item->width !=  "0.0"){
						$parcel_params["width"] = $current_item->width;
					}
					if($current_item->height !=  "0.0"){
						$parcel_params["height"] = $current_item->height;
					}
					$parcel = \EasyPost\Parcel::create($parcel_params);
		
					// create shipment
					$shipment_params = array("from_address" => $from_address,
							"to_address"   => $to_address,
							"parcel"       => $parcel
					);
					$shipment = \EasyPost\Shipment::create($shipment_params);
					if (count($shipment->rates) === 0) {
						$item['rate']  = 'No rates for your address';
					}else{
						$rate = \EasyPost\Rate::retrieve($shipment->lowest_rate());
						$item['rate'] = $rate;
						$shipment->buy($rate);
					
					    $shipment22 = \EasyPost\Shipment::retrieve($rate->shipment_id);
						
						if($shipment22->postage_label->label_url!='')
							$item['label']=$shipment22->postage_label->label_url;
						else
							$item['label']=$shipment->postage_label->label_url;
						}
				}
				else
				{
					$item['rate']=0;$item['label']='';
				}
				$data['cart'][]=$item;
				
				if(is_object($item['rate']))
					$userinfoship[$item['company'].'comp'.$item['itemid']]=$item['rate']->rate;
				else
					$userinfoship[$item['company'].'comp'.$item['itemid']]=$item['rate'];
					
					$userinfoship[$item['company'].'comp2'.$item['itemid']]=$item['label'];

				
				if(is_object($item['rate'])){
					$dataitemshipping +=$item['rate']->rate;		
				}
				else{
					$dataitemshipping +=$item['rate'];	
				}
				
 			}
			catch(Exception $e)
			{
  				$cadderr['cart_address_error'] = "<div style='padding-bottom:5px; color:red;'>Please enter correct address.</div>";
				$this->session->set_userdata($cadderr);
				redirect('cart');
			}			
		}
		$userinfoship=http_build_query($userinfoship,'',', ');
		$data['settings'] = $this->settings_model->get_current_settings();
		$data['name'] = $vendorShipping['shippingName'];
		$data['street'] = $vendorShipping['shippingStreet'];
		$data['city'] = $vendorShipping['shippingCity'];
		$data['state'] = $vendorShipping['shippingState'];
		$data['zip'] = $vendorShipping['shippingZip'];
		$data['country'] = $vendorShipping['shippingCountry'];
		$data['shippingforvendors'] = $userinfoship;
 		$data['itemshipping'] = $dataitemshipping;
		$this->load->view('site/payment', $data);
	}
	
	public function ccpost()
	{
		ini_set('max_execution_time', 300);
		include(APPPATH.'libraries/easypost/easypost.php');
		$data['settings'] = $this->settings_model->get_current_settings();
		\EasyPost\EasyPost::setApiKey('tcOjVdKjSCcDxpn14CSkjw');
		$cart = $this->session->userdata('pms_site_cart');
		$ordernumber = $this->session->userdata('pms_orderid');
 		
		if(!$cart || !$ordernumber)
		{
			redirect('');
		}
		
		$comparedate = $_POST['year'] .'-'. $_POST['month'] . '-01';
		$currentmonth = date('Y-m-01');
		if($comparedate < $currentmonth)
		{
			$this->session->set_flashdata('message', '<div style="font-size:14px; font-weight:bold;color:#FF0000;">Incorrect Expiry Date. Please try again.</div>');
			redirect('cart/ccpayment');
		}
		
		$totalprice = 0;
		foreach($cart as $ci)
		{
			$totalprice+= $ci['quantity'] * $ci['price'];
		}
 		$settings = $this->settings_model->get_current_settings();
		
		 $tax = number_format($totalprice*$settings->taxpercent/100,2);
 		 $gtotal = number_format($totalprice,2);
 		 $totalshipping = number_format($_POST['itemshipping'],2);
					
		$totalprice = $totalprice+ $totalshipping+ $tax;
	    $totalprice = number_format($totalprice,2);
		
		@session_start();
		$_SESSION['cart_shipping_vals']=$_POST['itemshipping'];
		
		if($this->session->userdata('site_loggedin'))
	    	$config = (array)$this->settings_model->get_setting_by_admin ($this->session->userdata('site_loggedin')->id);
	    else
	    	$config = array();
	    $config = array_merge($config, $this->config->config);
		require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
		Stripe::setApiKey($config['STRIPE_API_KEY']);
		//$myCard = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2015);
		$myCard = array('number' => $_POST['card'], 'exp_month' => $_POST['month'], 'exp_year' => $_POST['year']);
		$charge = Stripe_Charge::create(array('card' => $myCard, 'amount' => $totalprice * 100, 'currency' => 'usd' ));
		//echo $charge;
		$chargeobj = json_decode($charge);
		//echo '<pre>';print_r($chargeobj);

		if(@$chargeobj->paid)
		{
			if($this->session->userdata('site_loggedin'))
			{
				$pdftopurchasingadmin = $this->orderpdf('',true,'Credit Card');
				$subject = "Order Details from ezpzp";
				$this->sendEmail($pdftopurchasingadmin, $this->session->userdata('site_loggedin')->email, $subject);
			}
			else 
			{
				$pdftopurchasingadmin = $this->orderpdf('',true,'Credit Card');
				$subject = "Order Details from ezpzp";
				$this->sendEmail($pdftopurchasingadmin, $_POST['email'], $subject);
			}
			$companies = array();
			$companiesamount = array();
			$getvendorship=explode(', ',$_REQUEST['shippingforvendors']);
			foreach($cart as $ci)
			{
				if(!isset($companies[$ci['company']]))
				{
					$companies[$ci['company']] = $this->orderpdf($ci['company'],true,'Credit Card');
					$this->db->where('id',$ci['company']);
					$cd = $this->db->get('company')->row();
					
					//echo $ci['company'].$cd->primaryemail.'>'.$companies[$ci['company']].'<br/>';
					$subject = "Order Details from ezpzp";
					
					$labelforvendor='';$addemaillabel='';
 					foreach($getvendorship as $getvendorship2)
					{
						$getvendorship3=explode('=',trim($getvendorship2));
						//echo $getvendorship3[0].'---'.$ci['company'].'comp'.$ci['itemid'];
						if($getvendorship3[0]==$ci['company'].'comp2'.$ci['itemid'])
						{
							$labelforvendor=$getvendorship3[1];
						}
					}
					if($labelforvendor!='')
					$addemaillabel="<br><br>Label url: ".urldecode($labelforvendor);
						
					$this->sendEmail($companies[$ci['company']].$addemaillabel,$cd->primaryemail, $subject, $cd->title);
				}
				if(!isset($companiesamount[$ci['company']]))
				    $companiesamount[$ci['company']] = 0;
				$companiesamount[$ci['company']] += $ci['price'] * $ci['quantity'];
				
				
			}
			$order = array();
			$order['ordernumber'] = $ordernumber;
			$order['type'] = 'Credit Card';
			$order['purchasingadmin'] = @$this->session->userdata('site_loggedin')->id;
			$order['purchasedate'] = date('Y-m-d H:i:s');
			$order['txnid'] = $chargeobj->balance_transaction;
			$order['email'] = @$_POST['email'];
			$order['taxpercent'] = $data['settings']->taxpercent;
			$order['shipping'] = $totalshipping;
			
			$this->db->insert('order',$order);
			$oid = $this->db->insert_id();
			$data['order'] = $ordernumber;
			$notifications = array();
			
			
			foreach($cart as $ci)
			{
				$od = array();
				$od['orderid'] = $oid;
				$od['itemid'] = $ci['itemid'];
				$od['company'] = $ci['company'];
				$od['price'] = $ci['price'];
				$od['quantity'] = $ci['quantity'];
    			$od['accepted'] = '1';
    			$od['paymentstatus'] = 'Paid';
    			$od['paymenttype'] = 'Credit Card';
    			$od['paymentnote'] = $chargeobj->balance_transaction;
				$shipmentofvendor=0;
 					foreach($getvendorship as $getvendorship2)
					{
						$getvendorship3=explode('=',trim($getvendorship2));
						//echo $getvendorship3[0].'---'.$ci['company'].'comp'.$ci['itemid'];
						if($getvendorship3[0]==$ci['company'].'comp'.$ci['itemid'])
						{
							$shipmentofvendor=$getvendorship3[1];
						}
					}
 				$od['shipping'] = $shipmentofvendor;
				$this->db->insert('orderdetails',$od);
 			
	    		$notifications[$ci['company']]['ponum'] = $ordernumber;
	    		$notifications[$ci['company']]['category'] = 'Order';
	    		$notifications[$ci['company']]['company'] = $ci['company'];
	    		$notifications[$ci['company']]['quote'] = $oid;
	    		$notifications[$ci['company']]['senton'] = date('Y-m-d H:i:s');
		
			}
			
 			foreach($notifications as $notification)
			{
			    $this->db->insert('notification',$notification);
			}
			$this->removeallcart();
			//echo '<pre>';print_r($companiesamount);
			//divide payment to companies
			foreach($companiesamount as $caid=>$amount)
			{
			    $amount = $amount + $amount*$settings->taxpercent/100;
				$amount=$amount-.55-($amount*2.9/100);
			    $amount = round($amount,2);
			    $bankaccount = $this->db->where('company',$caid)->get('bankaccount')->row();
			    $company = $this->db->where('id',$caid)->get('company')->row();
			    //print_r($bankaccount);
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
                      //echo $amount;
                      //print_r($recObj);
                      $obj = json_decode($recObj);
                      
                      $transferObj = Stripe_Transfer::create(array(
                          "amount" => $amount * 100, 
                          "currency" => "usd", 
                          "recipient" => $obj->id, 
                          "description" => "Transfer for ".$company->primaryemail )
                      );
                      //print_r($transferObj);
                      $tobj = json_decode($transferObj);
                      
                      $insert = array();
                      $insert['orderid'] = $oid;
                      $insert['purchasingadmin'] = @$this->session->userdata('site_loggedin')->id;
                      $insert['company'] = $company->id;
                      $insert['amount'] = $amount;
                      $insert['transferid'] = $tobj->id;
                      $insert['transferdate'] = date('Y-m-d H:i');
                      $insert['status'] = '';
                      //print_r($insert);
                      $this->db->insert('transfer',$insert);
                      
                      $transferbody = "Dear {$company->title},<br/><br/>
$ {$amount} has been transfered to your bank account for order#{$ordernumber}, with the transfer#{$tobj->id}.
";
                      //echo $company->primaryemail.'<br>';
                      //echo $transferbody;
                      $subject = "Payment Details from ezpzp";
                      $this->sendEmail($transferbody,$company->primaryemail, $subject, $company->title);
			    }
			}
			
			$data['message'] = 'Order Placed Successfully, order#: '.$ordernumber.'<br/>Transaction id is: '.$order['txnid'];
			
			$data['status'] = 'Success';
			$data['cart']=array();
			foreach($cart as $item)
			{
				$this->db->where('itemid',$item['itemid']);
				$this->db->where('company',$item['company']);
				$this->db->where('type','Supplier');
				$itemdetails = $this->db->get('companyitem')->row();
			
    			$orgitem = $this->db->where('id',$item['itemid'])->get('item')->row();
    			
    			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
    			
				$item['itemdetails'] = $itemdetails;
				
				$this->db->where('id',$item['company']);
				$item['companydetails'] = $this->db->get('company')->row();
				
				
				///Easy Post
					
					
				$this->db->where('id',$item['itemid']);
				$current_item = $this->db->get('item')->row();
				// create addresses
				$to_address_params = array("name"    => $_POST['name'],
						"street1" => $_POST['street'],
						"street2" => "",
						"city"    => $_POST['city'],
						"state"   => $_POST['state'],
						"zip"     => $_POST['zip'],
				);
				
				//Commented by dev - 120914
				/*try{
					$to_address = \EasyPost\Address::create($to_address_params);
					
					$from_address_params = array("name"    => $item['companydetails']->contact,
							"street1" => $item['companydetails']->address,
							"street2" => "",
							"city"    => $item['companydetails']->city,
							"state"   => $item['companydetails']->state,
							"zip"	  => $item['companydetails']->zip);
						
					$from_address = \EasyPost\Address::create($from_address_params);
					// create parcel
					
					if($current_item->weight>0)
					{
						$parcel_params = array(
								"predefined_package" => null,
								"weight"             => $current_item->weight
						);
						if($current_item->length !=  "0.0"){
							$parcel_params["length"] = $current_item->length;
						}
						if($current_item->width !=  "0.0"){
							$parcel_params["width"] = $current_item->width;
						}
						if($current_item->height !=  "0.0"){
							$parcel_params["height"] = $current_item->height;
						}
						$parcel = \EasyPost\Parcel::create($parcel_params);
						
						// create shipment
						$shipment_params = array("from_address" => $from_address,
								"to_address"   => $to_address,
								"parcel"       => $parcel
						);
						$shipment = \EasyPost\Shipment::create($shipment_params);
						if (count($shipment->rates) === 0) {
							$item['rate']  = 'No rates for your address';
						}else{
							
							$rate = \EasyPost\Rate::retrieve($shipment->lowest_rate());
							$item['rate'] = $rate;
							$shipment->buy($rate);
						}
					}
					else
					{
						$item['rate']=0;
					}
				}
				catch(Exception $e)
				{
					$item['rate']=0;
				}*/
				//Commented by dev - 120914
				$item['rate']=0;
				$data['cart'][]=$item;
				///////////////////////////
			
				/*
				$this->db->where('id',$item['itemid']);
				$current_item = $this->db->get('item')->row();
			
		
				// create addresses
				$to_address_params = array("name"    => $item['companydetails']->contact,
						"street1" => $item['companydetails']->address,
						"street2" => "",
						"city"    => $item['companydetails']->city,
						"state"   => $item['companydetails']->state,
						"zip"     => "");
				$to_address = \EasyPost\Address::create($to_address_params);
				
				$from_address_params = array("name"    => $_POST['name'],
						"street1" => $_POST['street'],
						"street2" => "",
						"city"    => $_POST['city'],
						"state"   => $_POST['state'],
						"zip"     => $_POST['zip'],
						);
				$from_address = \EasyPost\Address::create($from_address_params);
				
				
				// create parcel
				$parcel_params = array("length"             => $current_item->length,
						"width"              => $current_item->width,
						"height"             => $current_item->height,
						"predefined_package" => null,
						"weight"             => $current_item->weight
				);
				$parcel = \EasyPost\Parcel::create($parcel_params);
				
				
				// create shipment
				$shipment_params = array("from_address" => $from_address,
						"to_address"   => $to_address,
						"parcel"       => $parcel
				);
				$shipment = \EasyPost\Shipment::create($shipment_params);
			
				if (count($shipment->rates) === 0) {
				    $shipment->get_rates();
				  $created_rates = \EasyPost\Rate::create($shipment);
				}
				$rate = \EasyPost\Rate::retrieve($shipment->lowest_rate());
				$shipment = \EasyPost\Shipment::retrieve(array('id' => $shipment->id));
				$shipment->buy($shipment->rates[1]);
*/
				
				
			}
                        $data['order'] = $oid;
						$data['totalordershipping'] = $totalshipping;
		}
		else
		{
			$data['status'] = 'Error';
			$data['message'] = 'Error in credit card processing.<br/>Please try later.';
		}
		$this->load->view('site/cartmessage', $data);
	}
	
	
	public function manualpayment()
	{
		include(APPPATH.'libraries/easypost/easypost.php');
		\EasyPost\EasyPost::setApiKey('tcOjVdKjSCcDxpn14CSkjw');
		if(!$this->session->userdata('site_loggedin'))
		{
			redirect('cart');
		}
		$data['settings'] = $this->settings_model->get_current_settings();
		$cart = $this->session->userdata('pms_site_cart');
	    //echo '<pre>';print_r($cart);die;
		$ordernumber = $this->session->userdata('pms_orderid');
		if(!$cart || !$ordernumber)
		{
			redirect('');
		}
		if($this->session->userdata('site_loggedin'))
		{
			$pdftopurchasingadmin = $this->orderpdf('',true,'Manual');
			$subject = "Order Details from ezpzp";
			$this->sendEmail($pdftopurchasingadmin, $this->session->userdata('site_loggedin')->email,$subject);
		}
		$companies = array();
		
		$data['cart']=array();
		$dataitemshipping=0; $userinfoship = array();
 		
 		foreach($cart as $item)
		{
			$this->db->where('itemid',$item['itemid']);
			$this->db->where('company',$item['company']);
			$this->db->where('type','Supplier');
			$itemdetails = $this->db->get('companyitem')->row();
 			$orgitem = $this->db->where('id',$item['itemid'])->get('item')->row();
 			$itemdetails->itemname = @$itemdetails->itemname?$itemdetails->itemname:$orgitem->itemname;
 			$item['itemdetails'] = $itemdetails;
 			$this->db->where('id',$item['company']);
			$item['companydetails'] = $this->db->get('company')->row();
 			
			///Easy Post
 				
			$this->db->where('id',$item['itemid']);
			$current_item = $this->db->get('item')->row();
			
			// create addresses
			$to_address_params = array("name"    => $_POST['shippingName'],
					"street1" => $_POST['shippingStreet'],
					"street2" => "",
					"city"    => $_POST['shippingCity'],
					"state"   => $_POST['shippingState'],
					"zip"     => $_POST['shippingZip'],
			);
 			
			try
			{
			$to_address = \EasyPost\Address::create($to_address_params);
			
			$from_address_params = array("name"    => $item['companydetails']->contact,
					"street1" => $item['companydetails']->address,
					"street2" => "",
					"city"    => $item['companydetails']->city,
					"state"   => $item['companydetails']->state,
					"zip"	  => $item['companydetails']->zip);
			//print_r($from_address_params);print_r($current_item);die;	
			$from_address = \EasyPost\Address::create($from_address_params);
			// create parcel
			if($current_item->weight>0)
			{ 
				$parcel_params = array(
						"predefined_package" => null,
						"weight"             => $current_item->weight*$item['quantity']
				);
				if($current_item->length !=  "0.0"){
					$parcel_params["length"] = $current_item->length;
				}
				if($current_item->width !=  "0.0"){
					$parcel_params["width"] = $current_item->width;
				}
				if($current_item->height !=  "0.0"){
					$parcel_params["height"] = $current_item->height;
				}
				$parcel = \EasyPost\Parcel::create($parcel_params);
				
				// create shipment
				$shipment_params = array("from_address" => $from_address,
						"to_address"   => $to_address,
						"parcel"       => $parcel
				);
				$shipment = \EasyPost\Shipment::create($shipment_params);
				if (count($shipment->rates) === 0) {
					$item['rate']  = 'No rates for your address';
					$item['label']  = '';
				}else{
					$rate = \EasyPost\Rate::retrieve($shipment->lowest_rate());
					$item['rate'] = $rate;
					$shipment->buy($rate);
					
					$shipment22 = \EasyPost\Shipment::retrieve($rate->shipment_id);
				  //echo "<pre>"; echo $shipment22->postage_label->label_url;  echo "cccc".$shipment->postage_label->label_url; print_r($shipment22); die;  
				  if($shipment22->postage_label->label_url!='')
				  	$item['label']=$shipment22->postage_label->label_url;
				  else
				  	$item['label']=$shipment->postage_label->label_url;
				}
			}
			else
			{
				$item['rate']=0; $item['label']='';
			}
				$data['cart'][]=$item;
				
				if(is_object($item['rate']))
					$userinfoship[$item['company'].'comp'.$item['itemid']]=$item['rate']->rate;
				else
					$userinfoship[$item['company'].'comp'.$item['itemid']]=$item['rate'];
				
				$userinfoship[$item['company'].'comp2'.$item['itemid']]=$item['label'];
				
				if(is_object($item['rate'])){
					$dataitemshipping +=$item['rate']->rate;		
				}
				else{
					$dataitemshipping +=$item['rate'];	
				}				
			}
			catch(Exception $e)
			{
 				$cadderr['cart_address_error'] = "<div style='padding-bottom:5px; color:red;'>Please enter correct address.</div>";
				$this->session->set_userdata($cadderr);
				redirect('cart');
			}
		}
 		$userinfoship=http_build_query($userinfoship,'',', ');
		$getvendorship=explode(', ',$userinfoship);
		foreach($cart as $ci)
		{
			if(!isset($companies[$ci['company']]))
			{
				$companies[$ci['company']] = $this->orderpdf($ci['company'],true,'Manual');
				
				$this->db->where('id',$ci['company']);
				$cd = $this->db->get('company')->row();
				
				$subject = "Order Details from ezpzp";
				
				$labelforvendor='';$addemaillabel='';
				foreach($getvendorship as $getvendorship2)
				{
					$getvendorship3=explode('=',trim($getvendorship2));
					//echo $getvendorship3[0].'---'.$ci['company'].'comp'.$ci['itemid'];
					if($getvendorship3[0]==$ci['company'].'comp2'.$ci['itemid'])
					{
						$labelforvendor=$getvendorship3[1];
					}
				}
				if($labelforvendor!='')
				$addemaillabel="<br><br>Label url: ".urldecode($labelforvendor);
				
				//echo $companies[$ci['company']].$addemaillabel; die;
				$this->sendEmail($companies[$ci['company']].$addemaillabel,$cd->primaryemail, $subject);
				
			}
		}
		$order = array();
		$order['ordernumber'] = $ordernumber;
		$order['type'] = 'Manual';
		$order['purchasingadmin'] = $this->session->userdata('site_loggedin')->id;
		$order['purchasedate'] = date('Y-m-d H:i:s');
		$order['email'] = $this->session->userdata('site_loggedin')->email;
		$order['taxpercent'] = $data['settings']->taxpercent;
		$order['shipping'] = $dataitemshipping;
		//print_r($order);die;
		$this->db->insert('order',$order);
		$oid = $this->db->insert_id();
		
		$notifications = array();
		
		
		foreach($cart as $ci)
		{
			$od = array();
			$od['orderid'] = $oid;
			$od['itemid'] = $ci['itemid'];
			$od['quantity'] = $ci['quantity'];
			$od['company'] = $ci['company'];
			$od['price'] = $ci['price'];
    		$od['accepted'] = '0';
			
			$shipmentofvendor=0;
				foreach($getvendorship as $getvendorship2)
				{
					$getvendorship3=explode('=',trim($getvendorship2));
					//echo $getvendorship3[0].'---'.$ci['company'].'comp'.$ci['itemid'];
					if($getvendorship3[0]==$ci['company'].'comp'.$ci['itemid'])
					{
						$shipmentofvendor=$getvendorship3[1];
					}
				}
			$od['shipping'] = $shipmentofvendor;			
			$this->db->insert('orderdetails',$od);
			
    		$notifications[$ci['company']]['ponum'] = $ordernumber;
    		$notifications[$ci['company']]['category'] = 'Order';
    		$notifications[$ci['company']]['company'] = $ci['company'];
    		$notifications[$ci['company']]['quote'] = $oid;
    		$notifications[$ci['company']]['senton'] = date('Y-m-d H:i:s');
		}
		
		foreach($notifications as $notification)
		{
		    $this->db->insert('notification',$notification);
		}
		$this->removeallcart();
		$data['message'] = 'Order Placed Successfully, order#: '.$ordernumber;
		$data['ordernumber'] = $ordernumber;
		$data['order'] = $oid;
		$data['status'] = 'Success';
		
		$data['totalordershipping'] = $dataitemshipping;
		$this->load->view('site/cartmessage', $data);
	}
	
	public function removeallcart()
	{
	    $cart = $this->session->userdata('pms_site_cart');
		foreach($cart as $item)
		if($item['isdeal']==1)
		{	
			$dealitem = $this->db
			->where('itemid',$item['itemid'])
			->where('company',$item['company'])
            ->where('dealactive','1')
            ->where('qtyavailable >=','qtyreqd')
            ->where('qtyavailable >','0')
            ->where('dealdate >=',date('Y-m-d'))
            ->get('dealitem')->row();
            if($dealitem && $item['price'] == $dealitem->dealprice)
            {
                $remaining = $dealitem->qtyavailable - $item['quantity'];
                
    			$this->db
    			->where('itemid',$item['itemid'])
    			->where('company',$item['company'])
                ->where('dealactive','1')
                ->where('qtyavailable >=','qtyreqd')
                ->where('qtyavailable >','0')
                ->where('dealdate >=',date('Y-m-d'))
                ->update('dealitem',array('qtyavailable'=>$remaining));
            }
            //echo $item['itemid'].'-'.$item['company'].'::'.$remaining.'<br>';
		}
		else 
		{
			$ci = $this->db
			->where('itemid',$item['itemid'])
			->where('company',$item['company'])
			->where('type','Supplier')
            ->get('companyitem')->row();
            if($ci)
            {
                $remaining = $ci->qtyavailable - $item['quantity'];
                $update = array('qtyavailable'=>$remaining);
                if($remaining == 0)
                    $update['instock'] = 0;
    			$this->db
    			->where('itemid',$item['itemid'])
    			->where('company',$item['company'])
    			->where('type','Supplier')
                ->update('companyitem',$update);
            }
		}
		$temp['pms_site_cart'] = null;
		$temp['pms_orderid'] = null;
		$this->session->set_userdata($temp);
	}
	
	public function orderpdf($company='',$htmlonly=false,$paymentType = '')
	{
		$sesscart = $this->session->userdata('pms_site_cart');
		$orderid = $this->session->userdata('pms_orderid');
		$cart = array();
		foreach($sesscart as $item)
		{
			$this->db->where('itemid',$item['itemid']);
			$this->db->where('company',$item['company']);
			$this->db->where('type','Supplier');
			$itemdetails = $this->db->get('companyitem')->row();
			if($itemdetails)
			{
			    $item['itemdetails'] = $itemdetails;
			}
			$this->db->where('id',$item['itemid']); 
			$orgitem = $this->db->get('item')->row();
			if(!$item['itemdetails']->itemname)
			    $item['itemdetails']->itemname = $orgitem->itemname;
			
			$this->db->where('id',$item['company']);
			$item['companydetails'] = $this->db->get('company')->row();
			if(!$company)
			{
				$cart[]=$item;
			}
			elseif($company == $item['company'])
			{
				$cart[]=$item;
			}
		}
		$settings = $this->settings_model->get_current_settings();
		ob_start();
	   	include $this->config->config['base_dir'].'application/views/site/orderpdfall.php';
	   	$pdfhtml = ob_get_clean();
	   	
		//if($company)
	        //echo $pdfhtml.'<br/>';
	        
	   	if($htmlonly)
	   		return $pdfhtml;
	   	
		//echo $pdfhtml;
		if($this->session->userdata('site_loggedin'))
	    	$config = (array)$this->settings_model->get_setting_by_admin ($this->session->userdata('site_loggedin')->id);
	    else
	    	$config = array();
	    $config = array_merge($config, $this->config->config);
		require_once($config['base_dir'].'application/libraries/tcpdf/config/lang/eng.php');
		require_once($config['base_dir'].'application/libraries/tcpdf/tcpdf.php');
	
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('EZPZP');
		$pdf->SetTitle('Invoice');
		$pdf->SetSubject('Order Invoice');
		$pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));
		$pdf->setCellPaddings(0,0,0,0);
		$pdf->setPrintFooter(false);
		$pdf->setPrintHeader(false);
	
		$pdf->SetHeaderData('', '', 'EZPZP'.'', 'Invoice');
	
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage('P', 'portrait');
	
		$pdf->SetFont('helvetica', '', 8, '', true);
		$pdf->writeHTML($pdfhtml, true, 0, true, true);
	
		$pdf->lastPage();
		$type = $company?'sup':'pad';
		if($company)
			$pdfname = $orderid.'-'.$type.'-'.$company.'.pdf';
		elseif(@$this->session->userdata('site_loggedin')->id)
			$pdfname = $orderid.'-'.$type.'-'.$this->session->userdata('site_loggedin')->id.'.pdf';
		else
			$pdfname = $orderid.'-'.$type.'.pdf';
		$pdf->Output($config['base_dir'].'uploads/orderpdf/'.$pdfname, 'f');
		return $pdfname;
	}
	
	function sendEmail($html,$email,$subject,$name='',$attachment='')
	{
		if($this->session->userdata('site_loggedin'))
			$settings = (array)$this->settings_model->get_setting_by_admin ($this->session->userdata('site_loggedin')->id);
		else
			$settings = (array)$this->settings_model->get_setting_by_admin (1);
		//echo $email.'-'.$html.'<br/>';
		$data['email_body_title']  = "";
		$data['email_body_content'] = $html;
		

		$loaderEmail = new My_Loader();
		
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		
	
	    $this->load->library('email');
	    $config['charset'] = 'utf-8';
	    $config['mailtype'] = 'html';
	    $this->email->initialize($config);
        $this->email->from($settings['adminemail'], "Administrator");
        
        $this->email->to($email); 
        
        $this->email->subject($subject);
        $this->email->message($send_body);	
        $this->email->set_mailtype("html");
        $this->email->send();
        
	}
}