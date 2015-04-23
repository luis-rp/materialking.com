<?php

class quote extends CI_Controller
{
    private $limit = 10;

    function quote()
    {
        parent::__construct();
        ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 1500);
        ini_set("set_time_limit", 200);
        $this->load->library('session');
        if (!$this->session->userdata('id')) {
            redirect('admin/login/index', 'refresh');
        }

        $this->load->dbforge();
        $this->load->library('form_validation');

        $this->load->library(array('table', 'validation', 'session'));
        $this->load->helper('form', 'url');
        $this->load->model('admin/quote_model');
        $this->load->model('quotemodel');
        $this->load->model('homemodel');
        $this->load->model('admin/report_model');
        $data['pendingbids'] = $this->quote_model->getpendingbids();
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data ['title'] = "Administrator";

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
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $this->load->model('admin/project_model');
        $this->load->model('admin/company_model');
        $this->load->model('admin/itemcode_model');
        $this->load = new My_Loader();
        
        $receiveqty = $this->quote_model->gettotalreceivedshipqty();
		$this->session->set_userdata('receiveqty',$receiveqty);  
        
        $this->load->template('../../templates/admin/template', $data);
    }

    function calendar()
    {
        $data = array();
        $this->load->view('admin/calendar', $data);
    }

    function jsonlist()
    {
        $mp = $this->session->userdata('managedprojectdetails');
        $quotes = $this->quote_model->get_quotes('',$mp ? $mp->id : '' );
      
        $quotelist = array();
        $added_date = array();
        foreach ($quotes as $quote) {
            $quote->awardedbid = $this->quote_model->getawardedbid($quote->id);
           // echo '<pre>##',print_r($quote->awardedbid);
            if ($quote->awardedbid) {
                if (@$quote->awardedbid->items) {
                    //echo $quote->id;
                    $added_date[$quote->id] = array();
                    $quote->url = site_url('admin/quote/track/' . $quote->id);
                    
                    $newDate = date("m/d/Y", strtotime($_GET['start']));
                    $itemcode = "Following Items are due from Quote : ".$quote->ponum;
                     if(isset($quote->deliverydate) && $quote->deliverydate != '')
                    {
                        $SQL = "SELECT q.id,ai.*
								FROM ".$this->db->dbprefix('quote')."  q
								LEFT JOIN ".$this->db->dbprefix('award')." a ON a.quote=q.id
								LEFT JOIN ".$this->db->dbprefix('awarditem')." ai ON ai.award = a.id
								where q.id={$quote->id} AND ai.daterequested='{$quote->deliverydate}'";
                    
                     
                     	$res = $this->db->query($SQL)->result();	 
                     
                     	$i = 1;
                     	if(isset($res) && count($res) > 0)
                     	{
	                     	foreach ($res as $key=>$val)
	                     	{                        	
	                     		$itemcode .= '             '. $i.' )  '. $val->itemcode. '   '. '('.$val->quantity.')';
	                     		$i++;
	                     	}
                     	}
                     	else 
                     	{
                     		$itemcode = 'No record Found';
                     	}
                    }  
                     
                    if ($this->session->userdata('usertype_id') == 3)
                        $quote->url = "javascript:void(0)";
                    $quote->title = $quote->ponum;
                    foreach ($quote->awardedbid->items as $item) {
                        if (!in_array($item->daterequested, $added_date[$quote->id])) {
                            $added_date[$quote->id][] = $item->daterequested;
                            $date = date("Y-m-d", strtotime($item->daterequested));
                            
                             
                            $quote->start = $date;
                            $quote->end = $date;

                            $obj = array();
                            $obj['url'] = $quote->url;
                            $obj['title'] = $quote->title;
                            $obj['start'] = $date;
                            $obj['end'] = $date;
                            $obj['itemcode'] = $itemcode;
                            if ($this->session->userdata('usertype_id') == 3) {
                                $checkauth = array('quote' => $quote->id, 'userid' => $this->session->userdata('id'));
                                $this->db->where($checkauth);

                                $checkauth = $this->db->get('quoteuser')->num_rows;
                                if ($checkauth)
                                    $quotelist[] = $obj;
                            }
                            else {
                                $quotelist[] = $obj;
                            }
                        }
                    }
                }
            }
        }
       
        //fwrite(fopen('test.txt',"w+"), print_r($quotelist,true));
        echo json_encode($quotelist);
    }

    function index($pid)
    {    	
        $temp['managedproject'] = $data['pid'] = $pid;
        //$this->load->model('admin/project_model');
        $temp['managedprojectdetails'] = $this->project_model->get_projects_by_id($pid);
        if ($this->session->userdata('usertype_id') == 2 && $temp['managedprojectdetails']->purchasingadmin != $this->session->userdata('id')) {
            //redirect('admin/dashboard', 'refresh');
        }
        $this->session->set_userdata($temp);

        $quotes = $this->quote_model->get_quotes('',$pid);
       // echo "<pre>"; print_r($quotes); die;
        $config ['total_rows'] = $this->quote_model->total_quote();
		$usersettings = (array)$this->settings_model->get_current_settings();
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('ID', 'Name', 'Actions');
		// Save rating into award table
		/*if(isset($_POST['idBox'])){
			$pricerank = "";
			if($_POST['rate'] <=1.4)
			$pricerank = 'poor';
			if($_POST['rate'] >=1.5 && $_POST['rate'] <=2.4)
			$pricerank = 'fair';
			if($_POST['rate'] >=2.5 && $_POST['rate'] <=3.4)
			$pricerank = 'good';
			if($_POST['rate'] >=3.5 && $_POST['rate'] <=5)
			$pricerank = 'great';
			$updatearray = array();
			$updatearray['pricerank'] = $pricerank;
			$this->quote_model->db->where('quote', $_POST['idBox']);
			$this->quote_model->db->update('award', $updatearray);
		}*/
		
        $data['counts'] = count($quotes);

        $count = count($quotes);
        $items = array();
        $companyarr = array();
        if ($count >= 1) {
			
            foreach ($quotes as $quote) { //echo $quod = $this->quote_model->getbidsjag($quote->id);exit;
            	$quote->subtotal = 0;
            	$quote->total = 0;
                $quote->invitations = $this->quote_model->getInvitedquote($quote->id);
                $quote->pendingbids = $this->quote_model->getbidsquote($quote->id);
                $quote->awardedbid = $this->quote_model->getawardedbidquote($quote->id);
                //print_r($quote->awardedbid);
                $quoteponum = $quote->ponum;
                $quote->pricerank = '-';
                if (!$quote->awardedbid)
                    $quote->pricerank = '-';
                elseif (!@$quote->awardedbid->items)
                    $quote->pricerank = '-';
                /*elseif (@$quote->awardedbid->quotedetails->potype == "Contract")
                    $quote->pricerank = '-';*/
                else {
                	
                	if(@$quote->awardedbid->quotedetails->potype == "Contract")
                    	$quote->ponum = '<a href="javascript:void(0)" onclick="viewcontractitems(\'' . $quote->id . '\')">' . $quote->ponum . '</a>';
                    else 
                    	$quote->ponum = '<a href="javascript:void(0)" onclick="viewitems(\'' . $quote->id . '\')">' . $quote->ponum . '</a>';
					
                    	$totalcount = count($quote->awardedbid->items);
                    	$lowcount = 0;
                    	foreach ($quote->awardedbid->items as $ai) {
                    		$itemlowest = $this->itemcode_model->getlowestquoteprice($ai->itemid);

                    		if ($ai->ea <= $itemlowest)
                    		$lowcount++;
                    	}
						if($quote->awardedbid->pricerank==""){	                    
								                    	
                    	if ($lowcount >= ($totalcount * 0.8))
                    	$quote->awardedbid->pricerank = 'great';
                    	elseif ($lowcount >= ($totalcount * 0.7))
                    	$quote->awardedbid->pricerank = 'good';
                    	elseif ($lowcount >= ($totalcount * 0.5))
                    	$quote->awardedbid->pricerank = 'fair';
                    	else
                    	$quote->awardedbid->pricerank = 'poor';
						
                    	$updatearray = array();
                    	$updatearray['pricerank'] = $quote->awardedbid->pricerank;
                    	$this->quote_model->db->where('quote', $quote->id);
                    	$this->quote_model->db->update('award', $updatearray);
                    	
						}
						
						
                    	if($quote->awardedbid->pricerank && (@$quote->awardedbid->quotedetails->potype != "Contract"))
                    	{
                    		if ($quote->awardedbid->pricerank == 'great')
                    		$quote->pricerank = 5;
                    		elseif ($quote->awardedbid->pricerank == 'good')
                    		$quote->pricerank = 4;
                    		elseif ($quote->awardedbid->pricerank == 'fair')
                    		$quote->pricerank = 3;
                    		elseif ($quote->awardedbid->pricerank == 'poor')
                    		$quote->pricerank = 2;
                    		else
                    		$quote->pricerank = 1;

                    		$quote->pricerank = '<div class="fixedrating" data-average="'.$quote->pricerank.'" data-id="'.$quote->id.'"></div>';
                    		//$quote->pricerank = '<img src="'.site_url('templates/admin/images/rank'.$quote->pricerank.'.png').'"/>';
                    	}
                    	
                    	foreach($quote->awardedbid->items as $awditems){
                    		$quote->subtotal += @$awditems->totalprice;
                    	}
                    	if(@$usersettings['taxpercent'])
                    	$quote->total = round($quote->subtotal + (@$usersettings['taxpercent']*$quote->subtotal/100),2);
                }
                //$quote->awardedcompany = $quote->awardedbid?$quote->awardedbid->companyname:'-';
                $quote->podate = $quote->podate ? $quote->podate : '';
                $quote->status = $quote->awardedbid ? 'AWARDED' : ($quote->pendingbids ? 'PENDING AWARD' : ($quote->invitations ? 'NO BIDS' : ($quote->potype == 'Direct' ? '-' : 'NO INVITATIONS')));
                //echo '<pre>';print_r($quote->awardedbid);die;
                if ($quote->status == 'AWARDED') {
					
                	$quote->status = $quote->status . ' - ' . strtoupper($quote->awardedbid->status);
                	
                	$shipmentsquery = "SELECT s.quantity FROM " . $this->db->dbprefix('shipment') . " s left join ".$this->db->dbprefix('item')." i on s.itemid=i.id WHERE quote='{$quote->id}' and s.accepted = 0";
        			$shipment = $this->db->query($shipmentsquery)->result();
                	if($shipment)
                	  {  
                		if(@$quote->awardedbid->quotedetails->potype == "Contract") 
                		   {               	
                            $quote->status = $quote->status .'<br> *Billing(s) Pending Acceptance'; 
                	       }
                        else 
                           {
                    	    $quote->status = $quote->status .'<br> <a href="'.site_url('admin/quote/receive/'.$this->session->userdata('managedprojectdetails')->id).'" > *Shipment(s) Pending Acceptance </a>';
                           }
                	  }
                }
                $quote->actions = $quote->awardedbid?'':
                anchor('admin/quote/items/' . $quote->id, '<span class="icon-2x icon-search"></span>', array('class' => 'view', 'title' => 'view quote items'))
                ;
                if (empty($quote->awardedbid)  && empty($quote->invitations) ) {
                    $quote->actions .=

                            anchor('admin/quote/update/' . $quote->id, '<span class="icon-2x icon-edit"></span>', array('class' => 'update'))
                            . ' ' .
                            anchor('admin/quote/delete/' . $quote->id, '<span class="icon-2x icon-trash"></span>', array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"))
                    ;
                } else {  if (!$quote->awardedbid) {
                    $quote->actions .= anchor('admin/quote/delete/' . $quote->id, '<span class="icon-2x icon-trash"></span>', array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"))
                    ;}
                     if($quote->potype !='Direct' && empty($quote->awardedbid))
                    $quote->actions .= anchor ('admin/quote/update/' . $quote->id,'<span class="icon-2x icon-edit"></span>',array ('class' => 'update' ) );
                }
                //$quote->sent ='';
                //if($quote->invitations && !$quote->awardedbid)	{
                $quote->sent = '<div class="badgepos"><span class="badge badge-blue">' . count($quote->invitations) . '</span></div>'
                ;
                //}
                if ($quote->awardedbid) {
                    //$quote->actions.= ' ' .
                    //anchor ( 'admin/quote/bids/' . $quote->id, '<span class="icon-2x icon-search"></span> ', array ('class' => 'view','alt' => 'awarded bid','title' => 'awarded bid' ) )
                    //;
                    
                    if($quote->potype=='Contract'){
                		$quote->actions.= ' ' .
                            anchor('admin/quote/contracttrack/' . $quote->id, '<span class="label label-pink">Track</span> ', array('class' => 'view', 'alt' => 'awarded bid', 'title' => 'awarded bid'))
                    ;
                	}else {
                    
                    $quote->actions.= ' ' .
                            anchor('admin/quote/track/' . $quote->id, '<span class="label label-pink">Track</span> ', array('class' => 'view', 'alt' => 'awarded bid', 'title' => 'awarded bid'));
                	} 
                }
                //echo "<pre>id-"; print_r($quote->id); die;
                $quote->recived = '';
                if ($quote->pendingbids) {
                	if($quote->potype=='Contract'){
                		 $quote->recived = anchor('admin/quote/conbids/' . $quote->id, '<div class="badgepos"><span class="badge badge-red">' . count($quote->pendingbids) . '</span></div>', array('class' => 'view'));
                	}
                	else {
                    $quote->recived = anchor('admin/quote/bids/' . $quote->id, '<div class="badgepos"><span class="badge badge-red">' . count($quote->pendingbids) . '</span></div>', array('class' => 'view'))
                    ;
                }}
                $quote->actions .=
                        '<a href="javascript:void(0)" onclick="duplicate(\'' . $quote->id . '\')" ><span class="icon-2x icon-copy"></span></a>'
                ;
                if ($this->session->userdata('usertype_id') == 2) {
                    $quote->actions .=
                            ' <a href="javascript: void(0)" onclick="quotepermission(' . $quote->id . ',\'' . $quoteponum . '\')"><span class="icon-2x icon-key"></span></a>';
                    ;
                }

                if(isset($quote->awardedbid->items)) {
                	foreach ($quote->awardedbid->items as $item) {

                		if($item->company){
                			$companyarr[] = $item->company;
                		}
                	}
                }

                if (@$_POST['searchcompany']) {

					if(count($companyarr)>0){
						if(!in_array($_POST['searchcompany'],$companyarr)){
							continue;
						}
					}else
					continue;

                }
                if (@$_POST['postatus']) {
                    if ($quote->status == $_POST['postatus']) {
                        $items[] = $quote;
                    }
                } else {
                    $items[] = $quote;
                }
            }
            $data['items'] = $items;
            $data['jsfile'] = 'quotejs.php';
        } else {
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

        $data ['addlink'] = '';
        $data ['heading'] = "Quote &amp; Purchase Order Management - " . $this->session->userdata('managedprojectdetails')->title;
        $data ['table'] = $this->table->generate();
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/quote/add/' . $pid . '">Add Quote</a>&nbsp;';
        $data ['addlink'].= '<a class="btn btn-green" href="' . base_url() . 'admin/quote/add/' . $pid . '/Direct">Add Purchase Order</a>&nbsp;';
        $data ['addlink'] .= '<a class="btn btn-green" href="' . base_url() . 'admin/quote/add/' . $pid . '/Contract">Add Contract Quote</a>&nbsp;';
        $mess= $this->session->flashdata('message');
        if(isset($mess) && $this->session->flashdata('message')!=""){
        	$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Permissions assigned.</div></div>');
        }
        
        $query1 = "SELECT code FROM ".$this->db->dbprefix('costcode')."
                  WHERE project='".$this->session->userdata('managedprojectdetails')->id."'";
        $data['costcodedata'] = $this->db->query($query1)->result();
        $sqlquery = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$this->session->userdata('managedprojectdetails')->id."' AND forcontract=1";
 		$data['contractcostcodes'] = $this->db->query($sqlquery)->result(); 
        
        
        $this->load->view('admin/quotelist', $data);
    }

    function items($id)
    {
        $quote = $this->quote_model->get_quotes_by_id($id);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre>';print_r($quote);die;
        if (!$quote) {
            die;
        }
        $quoteitems = $this->quote_model->getitems($id);
        $data['quote'] = $quote;
        $data['quoteitems'] = $quoteitems;
        //$this->load->model('admin/project_model');
        $data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $data['heading'] = "Original quote items: ".$quote->ponum;
       
        $this->load->view('admin/quotedetails', $data);
    }
    
    
    /*function contractitems($id)
    {	
        $quote = $this->quote_model->get_quotes_by_id($id);
       
        if (!$quote) {
            die;
        }
        $quoteitems = $this->quote_model->getitems($id);
        $data['quote'] = $quote;
        $data['quoteitems'] = $quoteitems;
        
        $data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $data['heading'] = "Original quote items: ".$quote->ponum;
       
        $this->load->view('admin/quotedetails', $data);
    }*/
    
    function contractitems($quoteid)
	{
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
		
		$quote = $this->quotemodel->getquotebyid($quoteid);
		$bid = $this->db->where('quote',$quoteid)->where('company',$company)->get('bid')->row();
		$award = $this->quote_model->getawardedcontractbid($quoteid);
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
			$this->db->where('company',$company);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
			    if($companyitem->itemcode)
				    $ai->itemcode = $companyitem->itemcode;
				if($companyitem->itemname)
				    $ai->itemname = $companyitem->itemname;
			}
			$data['allawardeditems'][] = $ai;
			if($ai->company == $company)
				$itemswon++;
			else
				$itemslost++;
		}
		//print_r($allawardeditems);die;
		$data['itemswon'] = $itemswon;
		$data['itemslost'] = $itemslost;
		$data['quote'] = $quote;
		$data['bid'] = $bid;
		if(isset($biditems)){
		$data['biditems'] = $biditems;}
		$data['award'] = $award;
		$data['quoteid'] = $quoteid;
		$purchaser = $this->quote_model->getpurchaseuserbyid($company);
		$data['company'] = $purchaser;
		
		$messagesql = "SELECT * FROM ".$this->db->dbprefix('message')." WHERE quote='{$quoteid}'";
		$message = $this->db->query($messagesql)->row();		
		if($message){
			$data['messagekey'] = $message->messagekey;
		}
		
		$this->load->view('admin/contractitems',$data);
	}

    function getitemsajax()
    {
        $id = $_POST['quote'];
        $quote = $this->quote_model->get_quotes_by_id($id);
        //echo '<pre>';print_r($quote);die;
        if (!$quote) {
            die;
        }
        $quoteitems = $this->quote_model->getitems($id);
        if (!$quoteitems) {
            die('No items');
        }
        //$this->load->model('admin/project_model');
        $awarded = $this->quote_model->getawardedbid($id);
        $awardeditems = array();
        if ($awarded) {
            if ($awarded->items) {
                $awardeditems = $awarded->items;
            }
        }
        $ret = '<h5>PO#:' . $quote->ponum . '&nbsp; &nbsp;' . anchor('admin/quote/track/' . $quote->id, '<span class="label label-pink">Track</span> ', array('class' => 'view')) . '</h5>';
        $ret .= '<table class="table table-bordered">';
        $ret .= '<tr><th>Itemcode</th><th>Item Image</th><th>Price Ea</th><th>Price Status</th><th>Qty.</th><th>Qty. received</th><th>Qty. due</th><th>Status</th></tr>';
        foreach ($quoteitems as $item)
        {
            $awarded = false;
            $status = '-';
            $paidprice = '';
            foreach ($awardeditems as $ai)
            {
                if ($ai->itemid == $item->itemid)
                {
                    $awarded = true;
                    $paidprice = $ai->ea;
                    $status = 'Not Complete';
                    if ($ai->quantity == $ai->received)
                    {
                        $status = 'Complete';
                    }
                    $item->quantity = $ai->quantity;
                    $item->received = $ai->received;
					$item->ea = $ai->ea;
					$item->companyname = $ai->companyname;
                }
            }
            if ($item->item_img && file_exists('./uploads/item/' . $item->item_img)) 
			 { 
			 	 $imgName = site_url('uploads/item/'.$item->item_img); 
			 } 
			 else 
			 { 
			 	 $imgName = site_url('uploads/item/big.png'); 
             }
            //$avgprice = $this->itemcode_model->getdaysmeanprice($item->itemcode);
            $lowestprice = $this->itemcode_model->getlowestquoteprice($item->itemid);
            if ($lowestprice < $paidprice)
                $ps = 'high';
            elseif ($lowestprice > $paidprice)
                $ps = 'good1';
            else
                $ps = 'equal';
            //$ps = 'paid-'.$paidprice. $ps.' avg for 120 days-'. $avgprice;
             if($awarded)
             {
             	if($item->received == '')
             		$received = 0;
             	else
             		$received = $item->received;
                $ret .= '<tr><td><a href="javascript:void(0)" onclick="viewitems2(\''.$item->itemid.'\')">'.$item->itemcode.'</a> <br> ('.$item->companyname.' )</td><td><img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:right;" src='.$imgName.'></td><td>'.$item->ea.'</td><td width="64"><img src="' . site_url('templates/admin/images/'.$ps.'.png') . '" width="64"/></td><td>' . $item->quantity . '</td><td>' . $received. '</td><td>' . ($item->quantity - $item->received) . '</td><td>' . $status . '</td></tr>';
             }
        }
        $ret .= '</table>';
        echo $ret;
    }

    
    
    function getcontractitemsajax()
    {
        $id = $_POST['quote'];
        $quote = $this->quote_model->get_quotes_by_id($id);
        //echo '<pre>';print_r($quote);die;
        if (!$quote) {
            die;
        }
        $quoteitems = $this->quote_model->getitems($id);
        if (!$quoteitems) {
            die('No items');
        }
        //$this->load->model('admin/project_model');
        $awarded = $this->quote_model->getawardedbid($id);
        $awardeditems = array();
        if ($awarded) {
            if ($awarded->items) {
                $awardeditems = $awarded->items;
            }
        }
        $ret = '<h5>Title:' . $quote->ponum . '&nbsp; &nbsp;' . anchor('admin/quote/contracttrack/' . $quote->id, '<span class="label label-pink">Track</span> ', array('class' => 'view')) . '</h5>';
        $ret .= '<table class="table table-bordered">';
        $ret .= '<tr><th>Filename</th><th>Price Ea</th><th>% Complete</th><th>% Due</th><th>Status</th></tr>';
        foreach ($quoteitems as $item)
        {
            $awarded = false;
            $status = '-';
            $paidprice = '';
            //echo "<pre>",print_r($awardeditems); die;
            foreach ($awardeditems as $ai)
            {
                if ($ai->itemid == $item->id)
                {
                    $awarded = true;
                    $paidprice = $ai->ea;
                    $status = 'Not Complete';
                    if (100 == $ai->received)
                    {
                        $status = 'Complete';
                    }
                    $item->quantity = 100;
                    $item->received = $ai->received;
					$item->ea = $ai->ea;
                }
            }
            //$avgprice = $this->itemcode_model->getdaysmeanprice($item->itemcode);
            $lowestprice = $this->itemcode_model->getlowestquoteprice($item->itemid);
            if ($lowestprice < $paidprice)
                $ps = 'high';
            elseif ($lowestprice > $paidprice)
                $ps = 'good1';
            else
                $ps = 'equal';
            //$ps = 'paid-'.$paidprice. $ps.' avg for 120 days-'. $avgprice;
             if($awarded)
             {
             	if($item->received == '')
             		$received = 0;
             	else
             		$received = $item->received;
                $ret .= '<tr><td><!-- <a href="javascript:void(0)" onclick="viewitems2(\''.$item->itemid.'\')">-->'.$item->attach.'</a></td><td>'.$item->ea.'</td><td>' . $received. '</td><td>'.(100 - $received).'</td><td>' . $status . '</td></tr>';
             }
        }
        $ret .= '</table>';
        echo $ret;
    }
    
    
    
    function duplicate()
    {
        $post = $this->input->post();
      
        if (!$post)
            die;
        if (!$post['id'])
            die;
        if (!$post['ponum'])
            die;
        $quote = (array) $this->quote_model->get_quotes_by_id($post['id']);

        if ($this->session->userdata('usertype_id') == 2 && $quote['purchasingadmin'] != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $quoteitems = $this->quote_model->getitems($post['id']);
       
        $quote['ponum'] = $post['ponum'];
        //$quote['potype'] = $post['potype'];
        unset($quote['id']);
        unset($quote['project']);
        $this->quote_model->db->insert('quote', $quote);
        $quoteid = $this->quote_model->db->insert_id();
        if ($quoteitems)
            foreach ($quoteitems as $item) {
                $item = (array) $item;
                $item['quote'] = $quoteid;
                unset($item['id']);                
                unset($item['increment']);            
                unset($item['title']);
                unset($item['item_img']); 
                unset($item['category']);          
                $this->quote_model->db->insert('quoteitem', $item);
            }
        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Purchase Order Duplicated Successfully</div></div>');
        redirect('admin/quote/update/' . $quoteid);
    }

    function checkpo($ponum,$pid="")
    {
        if ($this->quote_model->checkDuplicatePonum($ponum, 0,$pid))
            echo 'Duplicate';
        else
            echo 'Allow';
        die;
    }

    function add($pid, $potype = "Bid")
    {
    	$this->load->model('admin/costcode_model');
    	
        $this->_set_fields();
        $this->_set_fields1();
        $data['pid'] = $pid;
        $data['potype'] = $potype;
        $data ['heading'] = $potype == "Bid" ? 'Add New Quote' : ($potype == "Direct"?"Add New Purchase Order":"Add New Contract Quote");
        $data ['message'] = '';
        $data['companylist'] = $this->quote_model->getcompanylist();
        $data['quoteitems'] = array();
        $data ['action'] = site_url('admin/quote/add_quote/' . $pid . '/' . $potype);
        $sqlquery = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$pid."' AND forcontract=1";
 		$data['contractcostcodes'] = $this->db->query($sqlquery)->result();  

 		$costcodesql = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$pid."' ";
 		$data['costcodesresult'] = $this->db->query($costcodesql)->result(); 
 		 
 		if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        $data['projects'] = $this->db->get('project')->result();
        
        if(isset($_POST['parentfilter']) && $_POST['parentfilter']=="")
        {
        	$_POST['parentfilter']=0;
        }
        $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo('0', 0, 0,0);
        
 		$data['iscostcodeprefix'] = 0 ;
 		//echo '<pre>',print_r($data);die;
        if ($this->session->userdata('defaultdeliverydate'))
            $this->validation->deliverydate = $this->session->userdata('defaultdeliverydate');
        $this->validation->potype = $potype;
        if ($potype == 'Bid'){
            $this->load->view('admin/quotebid', $data);
        }elseif($potype == 'Direct'){
            $this->load->view('admin/direct', $data);
        }else 
        	$this->load->view('admin/contract', $data);    
    }

    function add_quote($pid, $potype = "Bid")
    {
    	$this->load->model('admin/costcode_model');
        if (!@$data)
            $data = array();
        $data = array_merge($data, $_POST);
        $data ['heading'] = $potype == "Bid" ? 'Add New Quote' : ($potype == "Direct"?"Add New Purchase Order":"Add New Contract Quote");
        $data ['action'] = site_url('admin/quote/add_quote/' . $pid . '/' . $potype);
        $data['companylist'] = $this->quote_model->getcompanylist();
        $data['quoteitems'] = array();
        $data['pid'] = $pid;
        $data['potype'] = $potype;
        $this->validation->potype = $potype;       
        $sqlquery = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$pid."' AND forcontract=1";
 		$data['contractcostcodes'] = $this->db->query($sqlquery)->result();  
 		
 		$costcodesql = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$pid."' ";
 		$data['costcodesresult'] = $this->db->query($costcodesql)->result(); 
 		
 		if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        $data['projects'] = $this->db->get('project')->result();
        
        if(isset($_POST['parentfilter']) && $_POST['parentfilter']=="")
        {
        	$_POST['parentfilter']=0;
        }
        $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo('0', 0, 0,0);
        
		$data['iscostcodeprefix'] = 0 ;
        $this->_set_fields();
        $this->_set_rules();
        if ($this->validation->run() == FALSE) {
            $this->load->view('admin/quotebid', $data);
        } elseif ($this->quote_model->checkDuplicatePonum($this->input->post('ponum'), 0,$pid)) {
            $data ['message'] = 'Duplicate PO#';
            //$this->load->view ('admin/quotebid', $data);
            if ($potype == 'Bid')
                $this->load->view('admin/quotebid', $data);
            elseif($potype == 'Direct')
                $this->load->view('admin/direct', $data);
            else 
              $this->load->view('admin/contract', $data);     
        }
        else {
            if ($this->input->post('makedefaultdeliverydate') == '1') {
                $temp['defaultdeliverydate'] = $this->input->post('deliverydate');
                $this->session->set_userdata($temp);
            }
            $itemid = $this->quote_model->SaveQuote();
            $itemtype = $potype == "Bid" ? 'Quote' : ($potype == "Direct"?'Purchase Order':'Contract Quote');
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">' . $itemtype . ' Added Successfully , Please add items below</div></div>');
            redirect('admin/quote/update/' . $itemid);
        }
    }

    function update($id)
    {
        $this->_set_fields();
        $config = (array) $this->settings_model->get_current_settings();

        $item = $this->quote_model->get_quotes_by_id($id);
        if ($this->session->userdata('usertype_id') == 2 && $item->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }

        $pid = $this->session->userdata('managedprojectdetails')->id;
        $costcodesql = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$pid."' ";
 		$data['costcodesresult'] = $this->db->query($costcodesql)->result();  
 		$data['iscostcodeprefix'] = 1 ;
        $data['quote'] = $item;
        $this->validation->id = $id;
        $this->validation->pid = $data['pid'] = $item->pid;
        $this->validation->potype = $data['potype'] = $item->potype;
        $this->validation->ponum = $item->ponum;
        $this->validation->podate = $item->podate;
        $this->validation->duedate = $item->duedate;
        $this->validation->startdate = $item->startdate;
        $this->validation->subject = $item->subject;
        $this->validation->company = $item->company;
        $this->validation->quoteattachment = $item->quoteattachment;
        $this->validation->itemchk = $item->itemchk;
        $this->validation->deliverydate = $item->deliverydate;
        $this->validation->subtotal = $this->quote_model->getsubtotal($id);
		$this->validation->contracttype = $item->contracttype;
        $this->validation->taxtotal = $this->validation->subtotal * $config['taxpercent'] / 100;
        $this->validation->total = $this->validation->subtotal + $this->validation->taxtotal;

        //echo($this->validation->company);die;

        $data['quoteitems'] = $this->quote_model->getitems($id);
        $data['companylist'] = $this->quote_model->getcompanylist();
        $data['company_for_reminder'] = $this->quote_model->getcompanylistforreminder();
        $data['invited'] = $this->quote_model->getInvited($id);
        $data['reminder'] = $this->quote_model->getInvitedButNotBid($id);
        $data['costcodes'] = $this->db->where('project',$item->pid)->get('costcode')->result();        
       	$sqlquery = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$item->pid."' AND forcontract=1";
 		$data['contractcostcodes'] = $this->db->query($sqlquery)->result();   
		
        $data['purchasercategories'] = $this->quote_model->getallCategories();		
        $data['purchasercategories1'] = $this->db->get('contractcategory')->result();	
        $this->db->where('quote', $id);
        $invitations = $this->db->get('invitation')->result();

        $data['invitations'] = array();
        foreach ($invitations as $inv) {
            $data['invitations'][$inv->company] = $inv;
        }
         /*
         $non = "SELECT c.title FROM " . $this->db->dbprefix('company') . " c left join ".$this->db->dbprefix('invitation')." i on c.id=i.company  WHERE c.company_type='3' AND i.quote='{$id}' AND i.purchasingadmin ='{$this->session->userdata('purchasingadmin')}'";*/
         
       /*  $non = "SELECT c.id,c.title FROM " . $this->db->dbprefix('company') . " c, ".$this->db->dbprefix('network')." n where c.isdeleted=0 AND c.id not in(select company from ".$this->db->dbprefix('network')." where purchasingadmin='{$this->session->userdata('purchasingadmin')}') AND n.purchasingadmin='{$this->session->userdata('purchasingadmin')}' group by c.id";*/
         
       
         $this->db->select('company');
         $nu=$this->db->get_where('network',array('purchasingadmin'=>$this->session->userdata('purchasingadmin')))->result();
        
         if($nu!="")
         {
         	 $dd="";
         	foreach ($nu as $n)
         	{
         		
         		$dd .=$n->company.",";
         	}
         }
         $stmt="AND 1=1";
         if($dd!="")
         {
         	$dd=trim($dd,",");
         	$stmt="AND c.id not in(".$dd.") AND n.purchasingadmin='{$this->session->userdata('purchasingadmin')}'";
         }    
         
      $non = "SELECT c.id,c.title FROM " . $this->db->dbprefix('company') . " c, ".$this->db->dbprefix('network')." n  where c.isdeleted=0 {$stmt} group by c.id";           
        $data['nonnetuser'] = $this->db->query($non)->result();
        $data['awarded'] = $this->quote_model->getawardedbid($id);
        $data['bids'] = $this->quote_model->getbids($id);

        $data ['heading'] = $data['potype'] == "Bid" ? 'Update Quote Item' : ($data['potype'] == "Direct" ?'Update Purchase Order Item':'Update Contract Item');
        $data['categorymenu'] = $this->items_model->getCategoryMenu();
        $data['categorymenuitems'] = $this->items_model->getCategoryMenuItems();
        $data ['message'] = '';
        $data ['action'] = site_url('admin/quote/updatequote');
        //$this->load->view ('admin/quotebid', $data);
               
         $gusttotal=0; $message = "";
         $minprices = array();
         foreach($data['quoteitems'] as $items){          
         	
         $minpriceresult = $this->itemcode_model->getminimumprices($items->itemid);	
         if($minpriceresult)
         $minprices[$items->itemid] = $minpriceresult;
         
         $totalSuppliers=0;
		 $invcnt = 0; $total=0; $totalSuppliers=0;
		 
		 $inventory = $this->db->where('type','Supplier')
                    ->where('itemid',$items->itemid)                  
                    ->get('companyitem')
                    ->result();
		 if($inventory){
         foreach($inventory as $initem)
         {
            $this->db->where('id',$initem->itemid);
            $orgitem = $this->db->get('item')->row();
            if(!is_object($orgitem)){
            	continue;
            }                        	
            
            if($this->session->userdata('site_loggedin'))
            {
                $this->db->where('company', $this->session->userdata('purchasingadmin'));
                $tiers = $this->db->get('tierpricing')->row();
                if ($tiers)
                {
                    $currentpa = $this->session->userdata('site_loggedin')->id;
                    $this->db->where('company', $initem->company);
                    $this->db->where('purchasingadmin', $currentpa);
                    $tier = @$this->db->get('purchasingtier')->row()->tier;
                    if ($tier)
                    {
                        $tv = $tiers->$tier;
                        $initem->ea = $initem->ea + ($initem->ea * $tv / 100);
                        $initem->ea = number_format($initem->ea, 2);
                    }
                }
            }
            
            if ($initem->ea && $initem->ea>0)
            {
            	$invcnt++;
            	$price = $initem->ea;
            	$total = $total + $price;
            }
            
         }
         
         	$totalSuppliers =$invcnt;
         	$total = $total*($items->quantity);
		 }else{
		 	$totalSuppliers = 0;		 	
		 	$message="*Some items without price data are not included in the estimate.";
		 }
         
           
			if($totalSuppliers>0)
			$gusttotal += $total/$totalSuppliers;
			
			if($data['potype'] == "Direct"){
				 $noncomp = "SELECT qc.* FROM " . $this->db->dbprefix('quoteitem_companies') . " qc where qc.companyemail not in (select primaryemail from ".$this->db->dbprefix('company')." c  where c.isdeleted=0) and qc.quoteitemid = ".$items->id;           
        $nonnetcompanies = $this->db->query($noncomp)->result();
			/*$this->db->where('quoteitemid',$items->id);
            $nonnetcompanies = $this->db->get('quoteitem_companies')->result();*/
            if($nonnetcompanies){
            	$data['nonnetcompanies'][$items->id] = $nonnetcompanies;
            }
			}                        	
         
         }
        
		$data['minprices'] = $minprices;
        
        $data['guesttotal'] = $gusttotal;
        $data['guesttotalmessage'] = $message;
        
	    if ($data['potype'] == 'Bid')
            $this->load->view('admin/quotebid', $data);
        elseif($data['potype'] == 'Direct')
            $this->load->view('admin/direct', $data);
        else{
        	$data ['action'] = site_url('admin/quote/updatecontractquote');
            $this->load->view('admin/contract', $data);    
        }
    }

    function getRandomPassword()
{
    $acceptablePasswordChars ="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $randomPassword = "";

    for($i = 0; $i < 8; $i++)
    {
        $randomPassword .= substr($acceptablePasswordChars, rand(0, strlen($acceptablePasswordChars) - 1), 1);  
    }
    return $randomPassword; 
}
    
    function updatequote()
    {
        $data ['heading'] = 'Update Quote Item';
        $data ['action'] = site_url('message/updatequote');
        $this->_set_fields();
        $this->_set_rules();

        $itemid = $this->input->post('id');
        $pid = $this->input->post('pid');
        $data['costcodes'] = $this->db->where('project',$pid)->get('costcode')->result();
        $sqlquery = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$pid."' AND forcontract=1";
 		$data['contractcostcodes'] = $this->db->query($sqlquery)->result();   
		$data['iscostcodeprefix'] = 1 ;
        if ($this->validation->run() == FALSE) {
            $data['quoteitems'] = $this->quote_model->getitems($itemid);
            $data['companylist'] = $this->quote_model->getcompanylist();
            $data['pid'] = $this->input->post('pid');
            $data ['action'] = site_url('admin/quote/updatequote');
            $this->load->view('admin/quotebid', $data);

            if ($this->input->post('potype') == 'Bid')
                $this->load->view('admin/quotebid', $data);
            else
                $this->load->view('admin/direct', $data);
        }
        else
        {
            if ($this->input->post('makedefaultdeliverydate') == '1')
            {
                $temp['defaultdeliverydate'] = $this->input->post('deliverydate');
                $this->session->set_userdata($temp);

                $this->quote_model->changeDateRequested($itemid, $this->input->post('deliverydate'));
            }
            $pid = $this->input->post('pid');
            $this->quote_model->updateQuote($itemid);
            $data ['message'] = '<div class="success">Quote has been updated.</div>';

            $itemtype = $this->input->post('potype') == "Bid" ? 'Quote' : ($this->input->post('potype') == "Direct"?'Purchase Order':'Contract');
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">' . $itemtype . ' Saved</div></div>');

            $quoteitems = $this->quote_model->getitems($itemid);
    		$emailitems = '<table BORDER CELLPADDING="12">';
    		$emailitems.= '<tr>';
    		$emailitems.= '<th>Item Image</th>';
    		$emailitems.= '<th> Itemcode  </th>';
    		$emailitems.= '<th>Itemname</th>';
    		$emailitems.= '<th>Qty</th>';
    		$emailitems.= '<th>Unit</th>';
    		$emailitems.= '<th>Price</th>';
    		$emailitems.= '<th>Notes</th>';
    		$emailitems.= '</tr>';
    		$priceea="";  		
    		foreach($quoteitems as $q)
    		{
    			if($q->ea > 0){ $priceea=$q->ea; } else { $priceea='RFQ';}
    			
    			if ($q->item_img && file_exists('./uploads/item/' . $q->item_img)) 
				{ 
				 	 $imgName = site_url('uploads/item/'.$q->item_img); 
				} 
				else 
				{ 
				 	 $imgName = site_url('uploads/item/big.png'); 
	            }
		                                     
    		    $emailitems.= '<tr>';
        		$emailitems.= '<td style="padding-left:5;"><img src="'.$imgName.'" width="80" height="80"></td>';
        		$emailitems.= '<td style="padding-left:5;">'.$q->itemcode.'</td>';
        		$emailitems.= '<td style="padding-left:5;">'.$q->itemname.'</td>';
        		$emailitems.= '<td style="padding-left:5;">'.$q->quantity.'</td>';
        		$emailitems.= '<td style="padding-left:5;">'.$q->unit.'</td>';
        		$emailitems.= '<td style="padding-left:5;">'.$priceea.'</td>';
        		$emailitems.= '<td style="padding-left:5;">'.$q->notes.'</td>';
        		$emailitems.= '</tr>';
    		}   		
    		$emailitems .= '</table>';
    		
            $infolist="";
            $invitees = $this->input->post('invitees');
            $nonnetuser=$this->input->post('nonnetworkuser');          
                 
            if(isset($nonnetuser) && $nonnetuser!="")
            {         		
            	$nonarray=explode(",",$nonnetuser);           		
            	foreach ($nonarray as $non)
            	{
            		$insert = array();
            		$insert['company'] = $non;
	            	$insert['purchasingadmin'] = $this->session->userdata('id');            		
	            	$insert['acceptedon'] = date('Y-m-d H:i:s');
	            	$insert['status'] = 'Active';
	            	$this->db->insert('network',$insert); 
	            	
	            	$pinsert = array();
            		$pinsert['company'] = $non;
            		$pinsert['purchasingadmin'] = $this->session->userdata('id');            		
            		$pinsert['creditonly'] = '1';
            		$this->db->insert('purchasingtier',$pinsert);        			
            	}          		
            }
            
           
            if(isset($nonnetuser))
            {
            	$invitees .=",".$nonnetuser;
            }
            $invitees=trim($invitees,",");
            
             if(isset($_POST['suem']))
             {
             	$trimemail=rtrim($_POST['suem'],',');            	
             }
             else 
             {
             	$trimemail="";
             }
             
              if(isset($_POST['suna']))
             {
             	 $trimname=rtrim($_POST['suna'],',');            	 
             }
             
             if(isset($_POST['una']))
             {
             	 $trimcontactname=rtrim($_POST['una'],',');            	 
             }
                             
             if(isset($trimemail) && $trimemail!="")
             {
               $supplyemailarr=explode(',',$trimemail);
               $i=0;
               $newarr[$i]=array();
             	    if(isset($trimemail) && $trimemail!="")
                    {
             	     $supplynamearr=explode(',',$trimname); 
             	     $supplycontactarr=explode(',',$trimcontactname); 
             	      
             	       foreach ($supplyemailarr as $c)
             	        { 
             	          if($c !="undefined"){	
             	        	$a=0;
             	        	$b=0;
             	        	$newarr[$i]['email']=$c;
             	        	foreach ($supplynamearr as $n)
             	        	{
             	        		$newarr[$a]['name']=$n;
             	        		$a++;
             	        	}
             	        	foreach ($supplycontactarr as $cn)
             	        	{
             	        		$newarr[$b]['cname']=$cn;
             	        		$b++;
             	        	}
             	        	$i++;
             	          }	
             	        }
                     }
             }  
           
            if(isset($newarr[0]['name']) && @$newarr[0]['name']!='')  
            {   $limitcompany = array();
               foreach ($newarr as $eachsup)
                {      
                	$this->db->insert('systemusers', array('parent_id'=>''));
					$lastid = $this->db->insert_id();		
                	$password = $this->getRandomPassword();
                	
                	$username = str_replace(' ', '-', strtolower($eachsup['name']));                	
                	
            		$limitedcompany = array(
            		   'id'=>$lastid,
            		   'primaryemail' => $eachsup['email'],  	
            		   'title' => $eachsup['name'],
            		   'contact' => @$eachsup['cname'],
                       'regkey' => '',
                       'username' => $username,
                       'pwd' => md5($password),
                       'password' => md5($password),
                       'company_type' => '3',
                       'regdate' => date('Y-m-d')                       
                    );
                    $this->db->insert('company', $limitedcompany);
            		
            		if($lastid){
            		if($invitees!=""){	
            		$invitees = $invitees.",".$lastid;
            		}else{ 
            		$invitees = $lastid;
            		
            		}
            		$limitcompany[$lastid]['username'] = $username;
            		$limitcompany[$lastid]['password'] = $password;
            		
            		$insert = array();
            		$insert['company'] = $lastid;
            		$insert['purchasingadmin'] = $this->session->userdata('id');            		
            		$insert['acceptedon'] = date('Y-m-d H:i:s');
            		$insert['status'] = 'Active';
            		$this->db->insert('network',$insert);
            		
            		$pinsert = array();
            		$pinsert['company'] = $lastid;
            		$pinsert['purchasingadmin'] = $this->session->userdata('id');            		
            		$pinsert['creditonly'] = '1';
            		$this->db->insert('purchasingtier',$pinsert);
            		
            		}
                } 

            }
			$sumofpurchase="SELECT SUM(totalprice) AS Total FROM ".$this->db->dbprefix('awarditem')." WHERE purchasingadmin='".$this->session->userdata('id')."'";
			$tot=$this->db->query($sumofpurchase)->row();
			if(isset($tot) && $tot!="")
			{
				$totalaccountspend='$'.number_format($tot->Total,2);
			}
			else {
				$totalaccountspend='$0';
			}
                       
            if ($invitees)
            {
                $companies = $this->quote_model->getcompanylistbyids($invitees);
                $companynames = array();
                foreach ($companies as $c)
                {
                    $companynames[] = $c->title;
                    $key = md5($c->id . '-' . $itemid . '-' . date('YmdHisu'));
                    $insertarray = array(
                        'quote' => $itemid,
                        'company' => $c->id,
                        'senton' => date('Y-m-d'),
                        'invitation' => $key,
                        'purchasingadmin' => $this->session->userdata('purchasingadmin'),
                        'itemcheck' => $this->input->post('itemchk')
                    );

                    $this->quote_model->db->insert('invitation', $insertarray);
                    $link = base_url() . 'home/quote/' . $key;
                    $loginlink = "";
                    $data['email_body_title']= "Dear, " .$c->title;					
                    $data['email_body_content'] = "";
                   
                    if(isset($limitcompany))
                    {
                    	if(count($limitcompany)>0 && @$limitcompany[$c->id]['username'] && @$limitcompany[$c->id]['password'])
                    	{ 
                    		$loginlink = base_url() . 'company/login/' . $this->session->userdata('id');                 		
                    		$data['email_body_content'] .= " You have received an invitation to join ({$this->session->userdata('companyname')}) procurement network.<br />Your Invitation is from user '{$this->session->userdata('fullname')}'.<br /><br />The Company '{$this->session->userdata('companyname')}' invites you to join their e-procurement network today.<br /><br />The Company '{$this->session->userdata('companyname')}' Details : <br /><strong>Contact Name : {$this->session->userdata('fullname')} <br> Address : {$this->session->userdata('address')} <br />Total Account Spend : {$totalaccountspend}</strong><br /><br />Your login details are as Follow<br /> <strong>Username : {$limitcompany[$c->id]['username']}  <br> Password : {$limitcompany[$c->id]['password']} </strong><br><br><a href='$loginlink' target='blank'>click here</a> to login.<br /><br />After Login, you can click the below link for direct access to quote #". $this->input->post('ponum') . "<br /> <a href='$link' target='blank'>$link</a> ";
                    	}
                    } 
                    else 
                    {                  
				  	$data['email_body_content'] .= "Please click following link for the quote PO# " . $this->input->post('ponum') . "<br /> <a href='$link' target='blank'>$link</a> ";
                    }				  	
				  	$data['email_body_content'] .= "<br><br/>Please find the details below:<br/><br/>
		  	        $emailitems
				   <br><br>Thank You,<br>(".$this->session->userdata('companyname').")<br>";
				  	$loaderEmail = new My_Loader();
                    $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
                 
                    $settings = (array) $this->settings_model->get_current_settings();
                    $this->load->library('email');
                    $config['charset'] = 'utf-8';
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);
                    $this->email->from($settings['adminemail'], "Administrator");
                    $this->email->to($settings['adminemail'] . ',' . $c->primaryemail);
                    $this->email->subject('Request for Quote Proposal PO# ' . $this->input->post('ponum').' From '.$this->session->userdata('companyname'));
                    $this->email->message($send_body);
                    $this->email->set_mailtype("html");
                    $this->email->send();

                    $notification = array(
                        'quote' => $itemid,
                        'company' => $c->id,
                        'ponum' => $this->input->post('ponum'),
                        'category' => 'Invitation',
                        'senton' => date('Y-m-d H:i'),
                        'isread' => '0',
                        'purchasingadmin' => $this->session->userdata('purchasingadmin')
                    );
                    $this->db->insert('notification', $notification);
                }
                $infolist=implode(', ', $companynames)."&nbsp;"; 
                $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Quote Sent to Companies: ' . $infolist . '</div></div>');

            }
            
            
            $reminders = $this->input->post('reminders');
            if ($reminders)
            {
                $companies = $this->quote_model->getcompanylistbyids($reminders);
                $companynames = array();
                foreach ($companies as $c)
                {
                    $companynames[] = $c->title;

                    $key = $this->quote_model->getInvitationKey($itemid, $c->id);

                    $link = base_url() . 'home/quote/' . $key;
                    $data['email_body_title'] = "Dear " . $c->title;
				    $data['email_body_content'] = "This is a reminder email for earlier invitation for the quote.<br><br>
				  	Please click following link for the quote PO# " . $this->input->post('ponum') . ":  <br><br>
				    <a href='$link' target='blank'>$link</a>
				    ";
				    $loaderEmail = new My_Loader();
                    $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
                    //$this->load->model('admin/settings_model');
                    $settings = (array) $this->settings_model->get_current_settings();
                    $this->load->library('email');
                    $config['charset'] = 'utf-8';
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);
                    $this->email->clear(true);
                    $this->email->from($settings['adminemail'], "Administrator");

                    $this->email->to( $c->title . ',' . $c->primaryemail);

                    $this->email->subject('Request for Quote Proposal PO# ' . $this->input->post('ponum'));
                    $this->email->message($send_body);
                    $this->email->set_mailtype("html");
                    $this->email->send();

                    $this->db->where('quote', $itemid);
                    $this->db->where('company', $c->id);
                    $this->db->update('invitation', array('remindedon' => date('Y-m-d'),'itemcheck' => $this->input->post('itemchk')));

                    $notification = array(
                        'quote' => $itemid,
                        'company' => $c->id,
                        'ponum' => $this->input->post('ponum'),
                        'category' => 'Invitation Reminder',
                        'senton' => date('Y-m-d H:i'),
                        'isread' => '0',
                        'purchasingadmin' => $this->session->userdata('purchasingadmin')
                    );
                    $this->db->insert('notification', $notification);
                }
                $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Quote Sent to Companies: ' . implode(', ', $companynames) . '</div></div>');
            }

            $revisions = $this->input->post('revisions');
            if ($revisions) {
                $companies = $this->quote_model->getcompanylistbyids($revisions);
                $companynames = array();
                foreach ($companies as $c) {
                    $companynames[] = $c->title;

                    $key = $this->quote_model->getInvitationKey($itemid, $c->id);

                    $link = base_url() . 'home/quote/' . $key;
                    $data['email_body_title'] = "Dear " . $c->title;
				    $data['email_body_content'] = "This is to notify you that there is a revision about the quote.<br><br>
				  	Please click following link for the quote PO# " . $this->input->post('ponum') . "):  <br><br>
				    <a href='$link' target='blank'>$link</a>
				    ";
				    $loaderEmail = new My_Loader();
                    $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
                    //$this->load->model('admin/settings_model');
                    $settings = (array) $this->settings_model->get_current_settings();
                    $this->load->library('email');
                    $config['charset'] = 'utf-8';
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);
                    $this->email->clear(true);
                    $this->email->from($settings['adminemail'], "Administrator");

                    $this->email->to($settings['adminemail'] . ',' . $c->primaryemail);

                    $this->email->subject('Request for Quote Proposal PO# ' . $this->input->post('ponum'));
                    $this->email->message($send_body);
                    $this->email->set_mailtype("html");
                    $this->email->send();

                    $this->db->where('quote', $itemid);
                    $this->db->where('company', $c->id);
                    $this->db->update('invitation', array('revisionsenton' => date('Y-m-d')));

                    $notification = array(
                        'quote' => $itemid,
                        'company' => $c->id,
                        'ponum' => $this->input->post('ponum'),
                        'category' => 'Invitation Revision',
                        'senton' => date('Y-m-d H:i'),
                        'isread' => '0',
                        'purchasingadmin' => $this->session->userdata('purchasingadmin')
                    );
                    $this->db->insert('notification', $notification);
                }
                $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Quote Sent to Companies: ' . implode(', ', $companynames) . '</div></div>');
            }

            redirect('admin/quote/update/' . $itemid);
            //redirect('admin/quote/index/'.$pid);
        }
    }

    
    function updatecontractquote()
    {   
        $data ['heading'] = 'Update Contract Item';
        $data ['action'] = site_url('message/updatecontractquote');
        $this->_set_fields();
        $this->_set_rules();

        $itemid = $this->input->post('id');
        $pid = $this->input->post('pid');
        $data['costcodes'] = $this->db->where('project',$pid)->get('costcode')->result();
        $sqlquery = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$pid."' AND forcontract=1";
 		$data['contractcostcodes'] = $this->db->query($sqlquery)->result();               

        if ($this->validation->run() == FALSE) {
            $data['quoteitems'] = $this->quote_model->getitems($itemid);
            $data['companylist'] = $this->quote_model->getcompanylist();
            $data['pid'] = $this->input->post('pid');
            $data ['action'] = site_url('admin/quote/updatecontractquote');
            $this->load->view('admin/contract', $data);
        }
        else
        {             
            $pid = $this->input->post('pid');
            $this->quote_model->updateQuote($itemid);
            $data ['message'] = '<div class="success">Contract has been updated.</div>';

            $itemtype = $this->input->post('potype') == "Bid" ? 'Quote' : ($this->input->post('potype') == "Direct"?'Purchase Order':'Contract');
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">' . $itemtype . ' Saved</div></div>');

            $quoteitems = $this->quote_model->getitems($itemid);
            $emailattachments[] = array();
            $emailitems = '<table CELLPADDING="12" style="text-align:center;">';
    		$emailitems.= '<tr>';    		
            $emailitems.= '<th>Contract Title</th> <th> Bid Due Date </th> <th> Contract Award Date </th></tr>';
            $emailitems.= '<tr><td>'.@$_POST['ponum'].'</td><td>'.date('m/d/Y', strtotime(@$_POST['duedate'])).'</td><td>'.date('m/d/Y', strtotime(@$_POST['podate'])).'</td></tr> </table> <br><br>';
    		$emailitems .= '<table BORDER CELLPADDING="12">';
    		$emailitems.= '<tr>';    		
    		$emailitems.= '<th>Description</th>';    		
    		$emailitems.= '<th>File Name</th>';
    		$emailitems.= '</tr>';
    		
    		$af=""; 		
    		foreach($quoteitems as $q)
    		{
    		    $emailitems.= '<tr>';        		
        		$emailitems.= '<td style="padding-left:5;">'.$q->itemname.'</td>';        		
        		$emailitems.= '<td style="padding-left:5;">'.$q->attach.'</td>';
        		$emailitems.= '</tr>';
        		$af.=$q->attach.","; 
        		//if(@$q->attach && file_exists("./uploads/quote/".$q->attach))
        		//$emailattachments[] = site_url('uploads/quote').'/'.$q->attach;
    		}   		    		
    		 $emailitems .= '</table>';
    	    
    		    if (@$_POST['categoryinvitees']) {
    		    $this->session->set_userdata('forcat', $_POST['categoryinvitees']);
                //$companies = $this->quote_model->getpurchaserlistbycategory($_POST['categoryinvitees']);
                
                $companies = $this->get_contract_company_in_miles(@$_POST['locradiushidden'],@$_POST['categoryinvitees']);
                //$companies = $this->db->get_where('users',array('purchasingadmin'=>'115'))->result();
               
                $companynames = array();
                //echo "<pre>",print_r($companies); die;
                foreach ($companies as $c)
                {	
                    $companynames[] = (@$c->companyname)?$c->companyname:$c->username;
                    $key = md5($c->id . '-' . $itemid . '-' . date('YmdHisu'));
                    $insertarray = array(
                        'quote' => $itemid,
                        'company' => $c->id,
                        'senton' => date('Y-m-d'),
                        'invitation' => $key,
                        'purchasingadmin' => $this->session->userdata('purchasingadmin'),
                        'invite_type' => 'contract'                        
                    );

                    $this->quote_model->db->insert('invitation', $insertarray);

                    $link = base_url() . 'admin/quote/invitation/' . $key;
                    $data['email_body_title']= "Dear " . (@$c->companyname)?$c->companyname:$c->username;

				  	$data['email_body_content'] = "Please click following link for the Contract quote " . $this->input->post('ponum') . " :  <br><br>
				    <a href='$link' target='blank'>$link</a>.<br><br/>
				    Please find the details below:<br/><br/>
		  	        $emailitems
				   <br><br>Thank You,<br>(".$this->session->userdata('companyname').")<br>";
				  	$loaderEmail = new My_Loader();
                    $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
                   
                    $settings = (array) $this->settings_model->get_current_settings();
                    $this->load->library('email');
                    $config['charset'] = 'utf-8';
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);
                    $this->email->clear(TRUE);
                    $this->email->from($settings['adminemail'], "Administrator");
                    $this->email->to($settings['adminemail'] . ',' . $c->email);                
                    $this->email->subject('Request for Contract Quote Proposal ' . $this->input->post('ponum'));
                    $this->email->message($send_body);
                    $this->email->set_mailtype("html");                   
                    $aff=""; 
                    $aff=rtrim($af, ",");
                    $aaff=""; 
		    		$aaff=explode(',',$aff);
		    		    		
                    if(isset($aaff) && $aaff!=""){
						$config = $this->config->config;
						$attachfile="";	
			            foreach($aaff as $file){
			    			if(@$file && file_exists("./uploads/quote/".$file)){
			    				$attachfile = $config['base_dir'] . 'uploads/quote/'.$file;
			    			    $this->email->attach($attachfile);			    			   		    				
			    			}			    			
			    		}
                    }
                    
                    /*foreach($emailattachments as $eattach)
                    $this->email->attach($eattach);*/               
                    $this->email->send();

                    $notification = array(
                        'quote' => $itemid,
                        'company' => $c->id,
                        'ponum' => $this->input->post('ponum'),
                        'category' => 'Invitation',
                        'senton' => date('Y-m-d H:i'),
                        'isread' => '0',
                        'purchasingadmin' => $this->session->userdata('purchasingadmin'),
                        'notify_type' => 'contract'      
                    );
                    $this->db->insert('notification', $notification);
                }              
                $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Contract Quote Sent to Companies: ' . implode(', ', $companynames) . '</div></div>');
            }
    		
    			
            redirect('admin/quote/update/' . $itemid);            
        }
    }

    
    
    public function invitation($key,$print='')
	{
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
		$invitation = $this->quotemodel->getinvitation($key);
		if(!$invitation)
		{
			$message = 'Quote Already Submitted for Review, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('admin/quote/contractbids');
		}
		if($company != $invitation->company)
		{
			$message = 'Wrong Access.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('admin/quote/contractbids');
		}
		
		$quote = $this->quotemodel->getquotebyid($invitation->quote);
		if($this->quotemodel->checkbidcomplete($quote->id))
		{
			$message = 'Bid Already Completed, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('admin/quote/contractbids');
		}
				
		
		$quoteitems = $this->quotemodel->getquoteitems($quote->id);
		//print_r($quoteitems);die;
		$originalitems1 = $this->quotemodel->getquoteitems($quote->id);
		$purchaser = $this->quote_model->getpurchaseuserbyid($invitation->company);
		$draftitems = $this->quotemodel->getdraftitems($quote->id,$invitation->company);
					
		foreach($originalitems1 as $q)
		{
			$originalitems[$q->itemid] = $q;
		}
		$data['originalitems'] = $originalitems;
		//echo '<pre>'; print_r($originalitems);
		
		$data['invitation'] = $key;
		$data['quote'] = $quote;
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company);
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
				WHERE company='".$company."' AND purchasingadmin='".$quote->purchasingadmin."' AND invitation='".$key."'
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
			$this->db->where('company',$company);
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
				if($orgitem){
					if(!$item->itemcode){
						$item->itemcode = $orgitem->itemcode;
					}
					if(!$item->itemname)
					$item->itemname = $orgitem->itemname;
					$item->showinventorylink = true;
				}
			}
			$price = $item->ea;
						
			if(!$draftitems){
			    $item->ea = number_format($item->ea,2);
			}
			$item->totalprice = $item->ea;					
			
			$data['quoteitems'][]=$item;
		}
		//echo '<pre>';print_r($data['quoteitems']);die;
		
		$data['draft'] = $draftitems?1:0;
		
		$data['company'] = $purchaser; 
		
		//for export link
		$data['invitekey'] = $key;
		
		$this->db->where('id',$invitation->purchasingadmin);
		$pa = $this->db->get('users')->row();
		if($pa)
		$data['purchasingadmin'] = $pa;
		if($print)
		{
			$this->load->template ( '../../templates/front/blank', $data);
			$this->load->view('quote/printquote',$data);
		}
		else {

			$sql = "SELECT i.*,q.ponum FROM
		".$this->db->dbprefix('invitation')." i, ".$this->db->dbprefix('quote')." q
		WHERE i.quote=q.id AND i.company='{$company}' AND i.invite_type='contract' and i.quote = {$quote->id} ORDER BY i.senton DESC";

			$invs = $this->db->query($sql)->result();

			$invitations = array();
			foreach($invs as $inv)
			{
				$this->db->where('id',$inv->quote);
				$inv->quotedetails = $this->db->get('quote')->row();
				$this->db->where('quote',$inv->quote);
				$this->db->where('company',$company);
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
					$this->db->where('company',$company);
					$items = $this->db->get('awarditem')->result();
					foreach($items as $i)
					{
						if($i->received < $i->quantity)
						$complete = false;
						if($i->company != $company)
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
						$awardeditems = $this->quotemodel->getawardeditems($awarded->id,$company);

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

					if($this->quotemodel->getawardeditems($awarded->id,$company))
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

			$data['invitations'] = $invitations;

			$this->load->view('admin/contractbid',$data);
		}
	}
    
    
    function contractbids(){
    	
    	$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
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
		WHERE i.quote=q.id AND i.company='{$company}' AND i.invite_type='contract' $pafilter ORDER BY i.senton DESC";
		$count = $this->db->query($sql)->num_rows;
		
		//echo $sql;
		
		$invs = $this->db->query($sql)->result();
		
		$invitations = array();
		foreach($invs as $inv)
		{
    		$this->db->where('id',$inv->quote);
    		$inv->quotedetails = $this->db->get('quote')->row();
    		$this->db->where('quote',$inv->quote);
    		$this->db->where('company',$company);
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
				$this->db->where('company',$company);
				$items = $this->db->get('awarditem')->result();
				foreach($items as $i)
				{
					if($i->received < 100)
						$complete = false;
					if($i->company != $company)
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
					$awardeditems = $this->quotemodel->getawardeditems($awarded->id,$company);
					
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
				
				if($this->quotemodel->getawardeditems($awarded->id,$company))
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
		/*$this->db->select($this->db->dbprefix('users.').'*');
		$this->db->where('usertype_id',2);
		$this->db->from('users')->join('network',"users.id=network.purchasingadmin")->where('network.company',$company->id);
		$data['purchasingadmins'] = $this->db->get()->result();*/
		$data['company'] = $company;
		$data['invitations'] = $invitations;
		$data['newcontractnotifications'] = $this->quote_model->getnewcontractnotifications();
		$this->load->view('admin/invitations',$data);
    	
    }
    
    function assignpo()
    {
        $post = $this->input->post();
        if (!$post)
            die;
        $quote = $this->quote_model->get_quotes_by_id($post['id']);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $project = $this->project_model->get_projects_by_id($quote->pid);
        if ($this->session->userdata('usertype_id') == 2 && $project->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }


        $items = $this->quote_model->getitems($quote->id);
        $invitees = array();
        $companyrows = array();
        foreach($items as $item)
        {
        	if($item->company)
            $invitees[$item->company] = $item->company;

            $this->db->where('quoteitemid',$item->id);
            $nonnetcompanies = $this->db->get('quoteitem_companies')->result();
            if($nonnetcompanies){
            	foreach($nonnetcompanies as $noncomp){
            		$companyexisits=0;
            		$resultnoncomp = "SELECT c.id as companyid FROM " . $this->db->dbprefix('company') . " c where c.isdeleted=0 and c.primaryemail like '".$noncomp->companyemail."'";     
            		
            		 $nonnetcompanies = $this->db->query($resultnoncomp)->row();
			
            		 if($nonnetcompanies){
            			$companyexisits = 1;
            		 	$lastid = $nonnetcompanies->companyid;	
            		 }else {	
            		 	$this->db->insert('systemusers', array('parent_id'=>''));
						$lastid = $this->db->insert_id();
            		 	$password = $this->getRandomPassword();

            		 	$username = str_replace(' ', '-', strtolower($noncomp->companyname));

            		 	$limitedcompany = array(
            		 	'id'=>$lastid,
            		 	'primaryemail' => $noncomp->companyemail,
            		 	'title' => $noncomp->companyname,
            		 	'contact' => @$noncomp->contact,
            		 	'regkey' => '',
            		 	'username' => $username,
            		 	'pwd' => md5($password),
            		 	'password' => md5($password),
            		 	'company_type' => '3',
            		 	'regdate' => date('Y-m-d')
            		 	);
            		 	$this->db->insert('company', $limitedcompany);            		 
            		 }
            		
            		 if($lastid){
            		 	
            		 	$updateitem = array(
            		 	'company' => $lastid,
            		 	);
            		 	
            		 	$this->quote_model->db->where('id', $item->id);
            			$this->quote_model->db->update('quoteitem', $updateitem);
            		 	
            		 	if($companyexisits==0){
            		 		$invitees[$lastid] = $lastid;
            		 		$limitcompany[$lastid]['username'] = $username;
            		 		$limitcompany[$lastid]['password'] = $password;

            		 		$insert = array();
            		 		$insert['company'] = $lastid;
            		 		$insert['purchasingadmin'] = $this->session->userdata('id');
            		 		$insert['acceptedon'] = date('Y-m-d H:i:s');
            		 		$insert['status'] = 'Active';
            		 		$this->db->insert('network',$insert);
            		 		
            		 		$pinsert = array();
		            		$pinsert['company'] = $lastid;
		            		$pinsert['purchasingadmin'] = $this->session->userdata('id');            		
		            		$pinsert['creditonly'] = '1';
		            		$this->db->insert('purchasingtier',$pinsert);
            		 	}

            		 	if(!isset($companyrows[$lastid]))
            		 	{
            		 		$companyrows[$lastid] = array();
            		 	}
            		 	
            		 	if ($item->item_img && file_exists('./uploads/item/' . $item->item_img)) 
						 { 
						 	 $imgName = site_url('uploads/item/'.$item->item_img); 
						 } 
						 else 
						 { 
						 	 $imgName = site_url('uploads/item/big.png'); 
		                 }
                 
            		 	$companyrow = '<tr>';
            		 	$companyrow.= '<td><img width="75" height="75" src="'.$imgName.'"></td>';
            		 	$companyrow.= '<td>'.$item->itemcode.'</td>';
            		 	$companyrow.= '<td>'.$item->itemname.'</td>';
            		 	$companyrow.= '<td>'.$item->quantity.'</td>';
            		 	$companyrow.= '<td>'.$item->ea.'</td>';
            		 	$companyrow.= '<td>'.$item->unit.'</td>';
            		 	$companyrow.= '<td>'.$item->notes.'</td>';
            		 	$companyrow.= '</tr>';
					
            		 	$companyrows[$lastid][] = $companyrow;


            		 }


            	}
            }
            if($item->company)
            {
            	if(!isset($companyrows[$item->company]))
            	{
            		$companyrows[$item->company] = array();
            	}
            	
            	 if ($item->item_img && file_exists('./uploads/item/' . $item->item_img)) 
				 { 
				 	 $imgName = site_url('uploads/item/'.$item->item_img); 
				 } 
				 else 
				 { 
				 	 $imgName = site_url('uploads/item/big.png'); 
                 }
		                                     
            	$companyrow = '<tr>';
            	$companyrow.= '<td><img width="75" height="75" src="'.$imgName.'"></td>';
            	$companyrow.= '<td>'.$item->itemcode.'</td>';
            	$companyrow.= '<td>'.$item->itemname.'</td>';
            	$companyrow.= '<td>'.$item->quantity.'</td>';
            	$companyrow.= '<td>'.$item->ea.'</td>';
            	$companyrow.= '<td>'.$item->unit.'</td>';
            	$companyrow.= '<td>'.$item->notes.'</td>';
            	$companyrow.= '</tr>';

            	$companyrows[$item->company][] = $companyrow;
            }
        }
        if($invitees){
        $companies = $this->quote_model->getcompanylistbyids(implode(',',$invitees));
        
        $companynames = array();
        foreach ($companies as $c)
        {
    		$emailitems = '<table border="1" width="100%">';
    		$emailitems.= '<tr>';
    		$emailitems.= '<th>Item Image</th>';
    		$emailitems.= '<th>Item Code</th>';
    		$emailitems.= '<th>Item Name</th>';
    		$emailitems.= '<th>Qty</th>';
    		$emailitems.= '<th>Price</th>';
    		$emailitems.= '<th>Unit</th>';
    		$emailitems.= '<th>Notes</th>';
    		$emailitems.= '</tr>';
    		$emailitems.= implode('',$companyrows[$c->id]);
    		$emailitems.= '</table>';

            $companynames[] = $c->title;
            $key = md5($c->id . '-' . $quote->ponum . '-' . date('YmdHisu'));
            $insertarray = array(
                'quote' => $quote->id,
                'company' => $c->id,
                'senton' => date('Y-m-d'),
                'invitation' => $key,
                'purchasingadmin' => $this->session->userdata('purchasingadmin')
            );

            $this->quote_model->db->insert('invitation', $insertarray);
            $link = base_url() . 'quote/direct/' . $key;
            $data['email_body_title'] = "Dear " . $c->title."," ;            
            $data['email_body_content'] = "";
            $loginlink="";           
            if(isset($limitcompany))
            {
            	if(count($limitcompany)>0 && @$limitcompany[$c->id]['username'] && @$limitcompany[$c->id]['password'])
            	{
            		$loginlink = base_url() . 'company/login/' . $this->session->userdata('id');
            		$data['email_body_content'] .= "The <i>{$this->session->userdata('companyname')}</i>  sent you a Purchase Order. <br />Please View the Details Below : <br /> <strong>Username :{$limitcompany[$c->id]['username']}  <br> Password :{$limitcompany[$c->id]['password']}</strong><br /><br /> Please <a href='$loginlink' target='blank'>Click Here</a> to login with your login Details provided to approve the Purchase order and join <i>{$this->session->userdata('companyname')}</i> e-procurement network as a vendor.<br /><br />  ";           		
            	}
            } 
            else 
            {
            	$data['email_body_content'] .= "You have been sent a Purchase Order from <strong>{$this->session->userdata('fullname')}</strong> of <strong>{$this->session->userdata('companyname')}</strong>.<br />Please click on following link to review the purchase order <strong>PO# " . $quote->ponum . "</strong>.";
            	 $data['email_body_content'] .= "<br /><br /><a href='$link' target='blank'>$link</a><br /><br />";
            }
            
            $data['email_body_content'] .= "The PO Details are:<br><br>	$emailitems";
            
		  	$loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->from($settings['adminemail'], "Administrator");
            $this->email->to($settings['adminemail'] . ',' . $c->primaryemail);
            $this->email->subject('Request for Quote Proposal (PO# ' . $quote->ponum.')');        
            $this->email->message($send_body);
            $this->email->set_mailtype("html");
            $this->email->send();

            $notification = array(
                'quote' => $quote->id,
                'company' => $c->id,
                'ponum' => $quote->ponum,
                'category' => 'Invitation(Direct)',
                'senton' => date('Y-m-d H:i'),
                'isread' => '0',
                'purchasingadmin' => $this->session->userdata('purchasingadmin')
            );
            $this->db->insert('notification', $notification);
        }
        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
        	<div class="msgBox">Purchase Order Sent to Companies: ' . implode(', ', $companynames) . '</div></div>');
    }        

        redirect('admin/quote/index/' . $project->id);

        /*
        return 1;
        $awardarray = array(
            'quote' => $quote->id,
            'awardedon' => date('Y-m-d'),
            'shipto' => $post['shipto'],
            'purchasingadmin' => $this->session->userdata('purchasingadmin')
        );
        $this->quote_model->db->insert('award', $awardarray);
        $awardedid = $this->quote_model->db->insert_id();

        $companies = array();
        foreach ($items as $item)
        {
            $item = (array) $item;
            $insertarray = array();
            $insertarray['award'] = $awardedid;
            while (list($k, $v) = each($item))
            {
                if ($k != 'id' && $k != 'quote') {
                    $insertarray[$k] = $item[$k];
                }
            }
            $insertarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->quote_model->db->insert('awarditem', $insertarray);

            if (!isset($companies[$item['company']])) {
                $bidarray = array('quote' => $quote->id, 'submitdate' => date('Y-m-d'), 'company' => $item['company'], 'complete' => 'Yes', 'draft' => 'No');
                $bidarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                $this->quote_model->db->insert('bid', $bidarray);
                $companies[$item['company']] = $this->quote_model->db->insert_id();
            }

            $biditem = $item;
            unset($biditem['id']);
            unset($biditem['quote']);
            unset($biditem['company']);
            $biditem['bid'] = $companies[$item['company']];
            $biditem['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->quote_model->db->insert('biditem', $biditem);
        }
        $this->sendawardemail($quote->id,'nonpaid');
        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
													<div class="msgBox">Purcnase Order Assigned Sucessfully.</div></div>');
		*/
        redirect('admin/quote/index/' . $project->id);
    }

    function updateitems($qid)
    {    	
        $items = $this->quote_model->getitems($qid);
        $quote = $this->quote_model->get_quotes_by_id($qid);
        //echo '<pre>';print_r($_POST);die;
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id'))
        {
        	redirect('admin/dashboard', 'refresh');
        }
        foreach ($items as $item)
        {
        	$itemcode = @$_POST['itemcode' . $item->id];
            if ( $itemcode && !$this->db->where('itemcode',$itemcode)->get('item')->row() )
            {die('asdf');
                $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a>
    			<div class="msgBox">Itemcode "'.$itemcode.'" does not exist.</div></div>');
                redirect('admin/quote/update/' . $qid);
            }
        }
      //  die;
        foreach ($items as $item)
        {
            if($quote->potype=='Direct')
            if(!@$_POST['ea' . $item->id] || !@$_POST['ea' . $item->id]=='0.00')
            {
                $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a>
    			<div class="msgBox">Item price cannot be 0</div></div>');
                continue;
            }
            
            $suppliername = "";
            $supplierusername = "";
            $supplieremail ="";
            if(isset($_POST['addsupplyname'.$item->id])){
            	$suppliername = $_POST['addsupplyname'.$item->id];
            	unset($_POST['addsupplyname'.$item->id]);
            }
            
             if(isset($_POST['addsupplyusername'.$item->id])){
            	$supplierusername = $_POST['addsupplyusername'.$item->id];
            	unset($_POST['addsupplyusername'.$item->id]);
            }

            if(isset($_POST['addsupplyemail'.$item->id])){
            	$supplieremail = $_POST['addsupplyemail'.$item->id];
            	unset($_POST['addsupplyemail'.$item->id]);
            }
            
          
            $updatearray = array();
            $key = $item->id;
            while (list($k, $v) = each($item))
            {
                if ($k != 'id' && $k != 'quote' && $k != 'purchasingadmin')
                {
                    $updatearray[$k] = @$_POST[$k . $key];
                }   
                if ($k == 'ea' || $k == 'totalprice')
                {
                    $updatearray[$k] = str_replace('$ ', '', $updatearray[$k]);
                }
            }
            //print_r($updatearray);die;
            $updatearray['totalprice'] = $updatearray['quantity'] * $updatearray['ea'];
            if(isset($updatearray['increment']) || $updatearray['increment'] == "" )
            unset($updatearray['increment']);
            unset($updatearray['title']);
            unset($updatearray['item_img']);
            unset($updatearray['category']);
           
            $this->quote_model->db->where('id', $key);
            $this->quote_model->db->update('quoteitem', $updatearray);
            if (!$this->quote_model->finditembycode($updatearray['itemcode'])) {
                $itemcode = array(
                    'itemcode' => $updatearray['itemcode'],
                    'itemname' => $updatearray['itemname'],
                    'unit' => $updatearray['unit'],
                    'ea' => $updatearray['ea'],
                    'notes' => $updatearray['notes']
                );
                $this->quote_model->db->insert('item', $itemcode);
            }
            
            
            if($quote->potype=='Direct' && $suppliername!="" && $supplieremail!=""){


            	$this->db->where('quoteitemid',$key);
            	$nonnetcompanies = $this->db->get('quoteitem_companies')->result();
            	
            	if($nonnetcompanies){

            		$tempcompanies = array(
            		'companyname' => $suppliername,
            		'companyemail' => $supplieremail,
            		'contact' => $supplierusername
            		);
            		$this->quote_model->db->where('quoteitemid', $key);
            		$this->quote_model->db->update('quoteitem_companies', $tempcompanies);

            	}else{
            		
            		$tempcompanies = array(
            		'quoteitemid' => $key,
            		'companyname' => $suppliername,
            		'companyemail' => $supplieremail,
            		'contact' => $supplierusername
            		);
            		$this->quote_model->db->insert('quoteitem_companies', $tempcompanies);
            	}
             }
			
             if(isset($_FILES['ownitemcodefile']))
             {
             	ini_set("upload_max_filesize","128M");
		        $target='uploads/item/';
		            	
             	foreach ($_FILES['ownitemcodefile']['name'] as $key=>$val)
             	{
             		if($val != '')
             		{             			
	            		$temp=$target;
	            		$tmp=$_FILES['ownitemcodefile']['tmp_name'][$key];
	            		$origionalFile=$_FILES['ownitemcodefile']['name'][$key];	            
	            		move_uploaded_file($tmp, $temp.$origionalFile);
	            		$temp='';
	            		$tmp='';
	            		
	            		$this->quote_model->db->where('id', $key);
            			$this->quote_model->db->update('item', array('item_img'=>$val));
             		}
             	}
             }
          }
        redirect('admin/quote/update/' . $qid);
    }
    
    
    
    function updatecontractitems($qid)
    {

        $items = $this->quote_model->getitems($qid);        
        $quote = $this->quote_model->get_quotes_by_id($qid);
       //echo '<pre>';print_r($_POST);die;
        /*if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id'))
        {
        	redirect('admin/dashboard', 'refresh');
        }*/
        
       foreach ($items as $item)
        {
        	 $updatearray = array();
                        	
           /* if(is_uploaded_file($_FILES['attach'.$item->id]['tmp_name']))
        	{        	
        	if(move_uploaded_file($_FILES['attach'.$item->id]['tmp_name'], "uploads/quote/".$_FILES['attach'.$item->id]['name']))
        	{        		
		        $updatearray['attach'] = $_FILES['attach'.$item->id]['name'];
        	}
        	}*/
           
          if(isset( $item->attach))
          {
          $updatearray['attach']=$item->attach;
          }
          else 
          {
          	 $updatearray['attach']="";
          }
            
            $updatearray['itemname'] = $_POST['itemname'.$item->id];
            $updatearray['costcode'] = $_POST['costcode'.$item->id];
           			
            $this->quote_model->db->where('id', $item->id);
            $this->quote_model->db->update('quoteitem', $updatearray);            
            
        }

        redirect('admin/quote/update/' . $qid);
    }
    

    
    public function placecontractbid()
	{
		$revisionid=1;
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
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
				redirect('admin/quote/invitation/'.$_POST['invitation']);
				die;
			}
			if($zeroerror)
			{
				$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">You Cannot bid with price 0.</div></div></div>');
				redirect('admin/quote/invitation/'.$_POST['invitation']);
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
					/*$quotearr = explode(".",$_POST['quotenum']);
					if(count($quotearr)>1){
					$number = sprintf('%03d',$quotearr[1]+1);
					$bidarray['quotenum'] = $quotearr[0].".".$number;*/
					$bidarray['quotenum'] = $_POST['quotenum'];
					/*}else {
						$bidarray['quotenum'] = "";
					}*/
				}else
					$bidarray['quotenum'] = "";
			}
			else 
				$bidarray['quotenum'] = $_POST['quotenum'];
					
				
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
						if(@$_POST[$postkey])	
						$updatearray[$k] = @$_POST[$postkey];
					}
				}
				$item = (array)$item;
				$updatearray['totalprice'] = $_POST['totalprice'.$key];
				$updatearray['substitute'] = @$_POST['substitute'.$key]?@$_POST['substitute'.$key]:0;				
				
				$this->quotemodel->db->where('id',$key);
				if(@$_POST['nobid'.$key])
				{
					$this->quotemodel->db->delete('biditem');
				}
				else
				{
					$updatearray['notes'] = @$_POST['s_notes'.$key]?@$_POST['s_notes'.$key]:'';
					$this->quotemodel->db->update('biditem',$updatearray);
					/*$this->quotemodel->saveminimum($invitation->company,$invitation->purchasingadmin,$updatearray['itemid'],$updatearray['itemcode'],$updatearray['itemname'],$updatearray['ea'],$updatearray['substitute']);*/
					
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
			//echo '<pre>'; print_r($items);
			//echo '<pre>',print_r($_POST); die;
			$zeroerror = false;
			$nobids = true;
			foreach($items as $item)
			{
				$key = $item->id;
				$postkey = 'ea'.$key;				
				if(@$_POST['nobid'.$key] != 1 && @$_POST[$postkey] == 0)
					$zeroerror = true;
				if(@$_POST['nobid'.$key] != 1)
					$nobids = false;
			}
			if($nobids)
			{
				$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">You Cannot place a bid without any items.</div></div></div>');
				redirect('admin/quote/invitation/'.$_POST['invitation']);
				die;
			}
			if($zeroerror)
			{
				$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-error"><button data-dismiss="alert" class="close"></button><div class="msgBox">You Cannot bid with price 0.</div></div></div>');
				redirect('admin/quote/invitation/'.$_POST['invitation']);
				die;
			}
			$bidarray = array('quote'=>$invitation->quote,'company'=>$invitation->company,'submitdate'=>date('Y-m-d'));
			
			$bidarray['quotenum'] = $_POST['quotenum'];			
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
						if(@$_POST[$postkey])
						$insertarray[$k] = $_POST[$postkey];
					}
				}
				$item = (array)$item;
				$insertarray['substitute'] = @$_POST['substitute'.$key]?@$_POST['substitute'.$key]:0;
				$insertarray['totalprice'] = $_POST['totalprice'.$key];
				$insertarray['purchasingadmin'] = $invitation->purchasingadmin;
				$insertarray['ismanual'] = @$_POST['ismanual'.$key]?@$_POST['ismanual'.$key]:0;
				$insertarray['notes'] = @$_POST['s_notes'.$key]?@$_POST['s_notes'.$key]:'';
				$insertarray['itemid'] = -$key;
				//$insertarray['itemid'] = "";
				
				if(!@$_POST['nobid'.$key])
				{
				    
					//print_r($insertarray);//die;
					$this->quotemodel->db->insert('biditem',$insertarray);
					
					//if(!$insertarray['substitute'])
					//{
					// commented below line as this fuction requires unique itemid for each row, which is not available in case of contract bid 
						//$this->quote_model->saveminimum($invitation->company,$invitation->purchasingadmin,$insertarray['itemname'],$insertarray['ea'],$insertarray['substitute']);
					//}
					$insertarray['daterequested'] = date('m/d/Y');	
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
    	        redirect('admin/quote/contractbids');
    	    if($bid->company != $company)
    	        redirect('admin/quote/contractbids');
            if($bid->quotefile !="" && (file_exists('./uploads/quotefile/'.$bid->quotefile) && !is_dir('./uploads/quotefile/'.$bid->quotefile)))
            {
                $attachment = "uploads/quotefile/".$bid->quotefile;
            }
            
    	    $quote = $this->quotemodel->getquotebyid($bid->quote);
    	    $biditems = $this->quotemodel->getdraftitems($bid->quote, $company);
    	    
    	    $sqlq = "SELECT revisionid FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bidid."' AND purchasingadmin='".$quote->purchasingadmin."' order by id desc limit 1";
			$revisionquote = $this->db->query($sqlq)->row();
			if($revisionquote)
			$revisionid = $revisionquote->revisionid+1;
			else
			$revisionid = 1;
    	    
    	    $biditems2 = $this->quotemodel->getrevisiondraftitems($bid->quote, $company,$revisionid);
    	    
    	    if(@$biditems2){
    	    	$bid->submitdate = $biditems2[0]->daterequested;
    	    }
    	    
    	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
    	    $taxpercent = $settings->taxpercent;
    	    
    	    $purchaser = $this->quote_model->getpurchaseuserbyid($company);
    	    
    		ob_start();
    	   	include $this->config->config['base_dir'].'application/views/admin/quotehtml.php';
    	   	$html = ob_get_clean();
		    
    		$settings = (array)$this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
    				
    	    $this->load->library('email');

    	    $config['charset'] = 'utf-8';
    	    $config['mailtype'] = 'html';
    	    $this->email->initialize($config);
    		//$this->email->clear(true);
            $to = array();
            $this->email->from($purchaser->email);
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
    		$data['email_body_content'] = "This is a notification of bid details by ".$purchaser->companyname." for Contract ".$quote->ponum.".<br/><br/>
    		  	Please find the details below:<br/><br/>
    		  	$html
    		    ";
    		$loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    		//echo($to.'<br/>');
    		//echo $body;
           	$this->email->subject('Bid Notification for Contract '.$quote->ponum. " by ".$purchaser->companyname);
            $this->email->message($send_body);	
            if(isset($attachment)) { 
                $this->email->attach($attachment);
            }
            $this->email->set_mailtype("html");
            $this->email->send();
		}
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">Quote Submitted to Company. Pending Award. You can return at any time before winner is awarded to edit your quote.</div></div></div>');
		redirect('admin/quote/contractbids','refresh');
	}
	
	
	function viewbids($bidid,$revisionid)
	{
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
		redirect('admin/login');
		//echo $bidid;die;
		$bid = $this->db->where('id',$bidid)->get('bid')->row();
		//print_r($bid);print_r($company);die;

		if(!$bid)
		redirect('admin/quote/contractbids');
		if($bid->company != $company)
		redirect('admin/quote/contractbids');
		$quote = $this->quotemodel->getquotebyid($bid->quote);
		$biditems = $this->quotemodel->getrevisiondraftitems($bid->quote, $company,$revisionid);
		if(@$biditems){
			$bid->submitdate = $biditems[0]->daterequested;
		}
		$settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
		$taxpercent = $settings->taxpercent;
		$purchaser = $this->quote_model->getpurchaseuserbyid($company);
		ob_start();
		include $this->config->config['base_dir'].'application/views/admin/quotehtml.php';
		$html = ob_get_clean();

		header('Content-Description: File Transfer');
		header('Content-type: application/html');
		header('Content-Disposition: attachment; filename="quote.html"');
		echo $html;die;
	}
    
    
	function viewbid($bidid)
	{		
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
		//echo $bidid;die;
	    $bid = $this->db->where('id',$bidid)->get('bid')->row();
	    //print_r($bid);print_r($company);die;
	    
	    if(!$bid)
	        redirect('admin/quote/contractbids');
	    if($bid->company != $company)
	        redirect('admin/quote/contractbids');
	    $quote = $this->quotemodel->getquotebyid($bid->quote);
	    $biditems = $this->quotemodel->getdraftitems($bid->quote, $company);
	    $purchaser = $this->quote_model->getpurchaseuserbyid($company);
	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
	    $taxpercent = $settings->taxpercent;
	    
		ob_start();
	   	include $this->config->config['base_dir'].'application/views/admin/quotehtml.php';
	   	$html = ob_get_clean();
	   	
	   	header('Content-Description: File Transfer');
        header('Content-type: application/html');
        header('Content-Disposition: attachment; filename="quote.html"');
	   	echo $html;die;
	}
	
	
    function do_upload1($qid)
    {
        //$this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $config['upload_path'] = './uploads/quote/';
        $config['allowed_types'] = '*';
        $this->upload->initialize($config);

        if (!$this->upload->do_upload()) {
            $error = array('error' => $this->upload->display_errors());
        } else {
            $error = array('upload_data' => $this->upload->data());
        }
        return $error;
    }

    function updateattach()
    {
      //$items = $this->quote_model->getitems($qid);
        $qid = $_POST['quoteid'];
        //var_dump($qid); exit;
        //$err = $this->do_upload1($qid);
        //echo $qid;
		//print_r($_FILES);
        if(is_uploaded_file($_FILES['userfile']['tmp_name']))
        {//echo 'uploaded';
        	$ext = end(explode('.', $_FILES['userfile']['name']));
        	$nfn = md5(uniqid().date('YmdHi')).'.'.$ext;
        	if(move_uploaded_file($_FILES['userfile']['tmp_name'], "uploads/quote/".$nfn))
        	{//echo $nfn;
        		$updatearray = array();
		        $updatearray['quoteattachment'] = $nfn;
		        $this->quote_model->db->where('id', $qid);
		        $this->quote_model->db->update('quote', $updatearray);
		        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
			<div class="msgBox">File uploaded successfully.</div></div>');
        	}
        }
        else
        {

        	$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
			<div class="msgBox">File could not uploaded.</div></div>');

        }
		//die;
        redirect('admin/quote/update/' . $qid);
    }

    function additem($qid)
    {  
    	$itemcode = @$_POST['itemcode'];
    	if ( $itemcode && !$this->db->where('itemcode',$itemcode)->get('item')->row() )
    	{
    		$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a>
    			<div class="msgBox">Itemcode "'.$itemcode.'" does not exist.</div></div>');
    		redirect('admin/quote/update/' . $qid);
    	}
        $_POST['totalprice'] = $_POST['quantity'] * $_POST['ea'];
        $_POST['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $quote = $this->quote_model->get_quotes_by_id($qid);
        if(isset($_POST['itemincrement']))
        unset($_POST['itemincrement']);
        
        $suppliername = "";
        $supplieusername ="";
        $supplieremail ="";
        if(isset($_POST['addsupplyname'])){
        	$suppliername = $_POST['addsupplyname'];
        	unset($_POST['addsupplyname']);
        }
        
         if(isset($_POST['addsupplyusername'])){
        	$supplieusername = $_POST['addsupplyusername'];
        	unset($_POST['addsupplyusername']);
        }
        
        if(isset($_POST['addsupplyemail'])){
        	$supplieremail = $_POST['addsupplyemail'];
        	unset($_POST['addsupplyemail']);
        }
       
        if($_POST['itemcode'] !='')
        {
	        if($quote->potype=='Direct')
	        if(!$_POST['ea'] || $_POST['ea']=='0.00')
	        {
	            $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a>
	    			<div class="msgBox">Item price cannot be 0</div></div>');
	
	            redirect('admin/quote/update/' . $qid);
	
	        }
        }
         
        if(isset($_FILES['userdefineitemfile']['name']) && $_FILES['userdefineitemfile']['name'] != '')
        {        
	        if (is_uploaded_file($_FILES['userdefineitemfile']['tmp_name'])) 
	        {
                $nfn = $_FILES['userdefineitemfile']['name'];
                $ext = end(explode('.', $nfn));
                if (!in_array(strtolower($ext), array('jpg', 'gif', 'jpeg', 'png'))) 
                {
                    $errormessage = '* Invalid file type, upload logo file.';
                } 
                else
                {
                   move_uploaded_file($_FILES['userdefineitemfile']['tmp_name'], "uploads/item/" . $nfn);
                }
	        }
         
	        $this->quote_model->db->update('item',array('item_img'=>$_FILES["userdefineitemfile"]["name"]),array('id'=>$_POST['itemid']));
        }   
        
        if($_POST['itemcode'] !='')
        {
	        $this->quote_model->db->insert('quoteitem', $_POST);
	        $lastquoteitem = $this->quote_model->db->insert_id();
	        if (!$this->quote_model->finditembycode($_POST['itemcode']))
	        {
	            $itemcode = array(
	                'itemcode' => $_POST['itemcode'],
	                'itemname' => $_POST['itemname'],
	                'unit' => $_POST['unit'],
	                'ea' => $_POST['ea'],
	                'notes' => $_POST['notes']
	            );
	            $this->quote_model->db->insert('item', $itemcode);
	        }
        
	        if($quote->potype=='Direct' && $suppliername!="" && $supplieremail!=""){
	        
	        	$tempcompanies = array(
	                'quoteitemid' => $lastquoteitem,
	                'companyname' => $suppliername,
	                'companyemail' => $supplieremail,
	                'contact' => @$supplieusername
	            );	
	        	$this->quote_model->db->insert('quoteitem_companies', $tempcompanies);
	        	
	        }
       }  
        redirect('admin/quote/update/' . $qid);
    }
           
    function addcontractitem($qid)
    {
        if(isset($_FILES['attach']['name']) && $_FILES['attach']['name']!="")
            {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/quote/';
            	$count=0;
            	$_POST['attach'] = "";
            	foreach ($_FILES['attach']['name'] as $filename)
            	{
            		$temp=$target;
            		$tmp=$_FILES['attach']['tmp_name'][$count];
            		$origionalFile=$_FILES['attach']['name'][$count];
            		$count=$count + 1;
            		$temp=$temp.basename($filename);
            		move_uploaded_file($tmp,$temp);
            		$temp='';
            		$tmp='';
            		if(isset($filename) && $filename!=""){
                    $_POST['attach'].=$filename.",";
                    }

            	}
            	 $_POST['attach'] = rtrim($_POST['attach'], ',');
            	
            }
            
            if(isset($_POST['itemincrement']))
        		unset($_POST['itemincrement']);
       
        $_POST['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $quote = $this->quote_model->get_quotes_by_id($qid);
        
        $this->quote_model->db->insert('quoteitem', $_POST);        
        
        /*if(is_uploaded_file($_FILES['attach']['tmp_name']))
        {//echo 'uploaded';        	
        	if(move_uploaded_file($_FILES['attach']['tmp_name'], "uploads/quote/".$_FILES['attach']['name']))
        	{        		
		        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
			<div class="msgBox">File uploaded successfully.</div></div>');
        	}
        }
        else
        {

        	$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
			<div class="msgBox">File could not uploaded.</div></div>');

        }*/
        
        redirect('admin/quote/update/' . $qid);
    }

    function deleteitem($itemid, $qid) {
        $this->quote_model->deleteitem($itemid, $qid);

        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
			<div class="msgBox">Item Deleted Sucessfully.</div></div>');

        redirect('admin/quote/update/' . $qid);
    }    

     function bids_export($qid)
    {
    	if ($this->session->userdata('usertype_id') == 3)
    		redirect('admin/purchasingadmin/bids/' . $qid);


    	$bids       = $this->quote_model->getbids($qid);
    	$quoteitems = $this->quote_model->getitems($qid);
    	$awarded    = $this->quote_model->getawardedbid($qid);
    	$quote      = $this->quote_model->get_quotes_by_id($qid);

    	if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
    		redirect('admin/dashboard', 'refresh');
    	}

    	if (!$bids) {
    		$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Bids Yet Placed.</div></div>');
    		redirect('admin/quote/update/' . $qid);
    	}

    	if (!$awarded)
    		$data['isawarded'] = 'No';
    	else {
    		$data['isawarded'] = 'Yes';
    		$data['awarded']   = $awarded;
    	}

    	$minimum  = array();
    	$maximum  = array();
    	$viewbids = array();


    	foreach ($bids as $bid)
    	{
    		$totalprice = 0;
    		foreach ($bid->items as $item) {
    			foreach ($quoteitems as $qi) {
    				if ($qi->itemcode == $item->itemcode) {
    					$item->originaldate = $qi->daterequested;
    				}
    			}
    			$totalprice += $item->totalprice;
    			$key = $item->itemcode;
    			if (!isset($minimum[$key])) {
    				$minimum[$key] = $item->ea;
    				$maximum[$key] = $item->totalprice;
    			} elseif ($minimum[$key] > $item->ea) {
    				$minimum[$key] = $item->ea;
    			} else if ($maximum[$key] < $item->totalprice) {
    				$maximum[$key] = $item->totalprice;
    			}
    		}
    		if (!isset($minimum['totalprice']))
    			$minimum['totalprice'] = $totalprice;
    		elseif ($minimum['totalprice'] > $totalprice)
    		$minimum['totalprice'] = $totalprice;

    		$revisionquote = $this->quote_model->getrevisionno($bid->id,$quote->purchasingadmin);
    		if(isset($revisionquote->revisionid))
    			$bid->revisionno = $revisionquote->revisionid;
    		else
    			$bid->revisionno = 1;

    	}

    	$data['quote']      = $this->quote_model->get_quotes_by_id($qid);
    	$data['quoteitems'] = $quoteitems;
    	$data['project']    = $this->project_model->get_projects_by_id($data['quote']->pid);
    	$data['config']     = (array) $this->settings_model->get_current_settings();
    	$data['bids']       = $bids;
    	$data['minimum']    = $minimum;
    	$data['maximum']    = $maximum;
    	$data['costcodes']  = $this->db->where('project',$quote->pid)->get('costcode')->result();
    	$data['heading']    = $data['quote']->potype == 'Bid' ? "Bids Placed" : "PO Review";


    	//=========================================================================================

    	if(isset($data['isawarded']))
    		$isawarded   = $data['isawarded'];


    	$quote       = $data['quote'];
    	$quoteitems  = $data['quoteitems'];
    	$project     = $data['project'];
    	$config      = $data['config'];
    	$bids        = $data['bids'];
    	$minimum     = $data['minimum'];
    	$maximum     = $data['maximum'];
    	$costcodes   = $data['costcodes'];
    	$heading     = $data['heading'];

    	if(isset($data['awarded']))
    		$awarded     = $data['awarded'];




    	$header[] = array('Report type','Bid Review' , '' , '' , '' , '' , '','','', '','', '' );

		if($this->session->userdata('managedprojectdetails'))
		{
			$header[] = array('Project Title',$this->session->userdata('managedprojectdetails')->title , '' , '' , '' , '' , '','','', '','', '' );
			$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );
		}



    	$alltotal=0;


    	foreach($bids as $bid)
    	{

    		$alltotal = '';
			$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );

			$header[] = array('PO #:',$quote->ponum , 'Company:' , $bid->companyname , 'Submitted:' , date('m/d/Y', strtotime($bid->submitdate)) , '','','', '','', '' );

    		$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );
    		$header[] = array('Item Code','Item Name' , 'Qty.' , 'Unit' , '60 day Low. Price' , 'Price EA' , 'Price Requested','Total Price','Date Available', 'Cost Code','Notes', 'Compare' );


    		if($bid->items)
    		{

    			foreach($bid->items as $q)
    			{
    				if($q->itemcode)
    				{
    					$alltotal += $q->quantity * $q->ea;
    					$key  = $q->itemcode;
    					$diff = $q->ea - $minimum[$key];
    					$diff = number_format($diff,2);

    					$k_compare = '';

    					$k_compare =  ($diff==0?$diff==0?'Lowest Unit Price':$diff:($diff<0?'- $':'+ $'.$diff));

    					$low_price = '$ '.$q->ea;
    					if($diff=='0')
    					{
    						$low_price = '$ '. $q->ea;
    					}

    					if($q->minprice >= $q->ea)
    					{
    						$low_price = '$ '.$q->ea;
    						//$low_price = '*New Low Price';
    					}

    					$pr_requested = $q->reqprice;
    					if($q->reqprice > 0)
    					{
    						//--------
    					}
    					else
    					{
    						$pr_requested = $pr_requested.' (RFQ)';
    					}

    					$k_costcode = '';

    					if($isawarded )
    					{
    						$k_costcode = $q->costcode;
    					}
    					else
    					{
    						$k_costcode = '-';
    					}

    					$k_total_price = round($q->quantity * $q->ea,2);


    					$header[] = array($q->itemcode, $q->itemname , $q->quantity , $q->unit , '$ '.$q->minprice.chr(160) , $low_price.chr(160) , $pr_requested,'$ '.$k_total_price.chr(160) , $q->daterequested, $k_costcode,$q->notes, $k_compare );

    				}



    			}
    			$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );
    			$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );

    			$alltotal   = round($alltotal,2);
    			$taxtotal   = $alltotal * $config['taxpercent'] / 100;
    			$taxtotal   = round($taxtotal,2);
    			$grandtotal = $alltotal + $taxtotal;
    			$grandtotal = round($grandtotal,2);
    			$diff       = $alltotal - $minimum['totalprice'];

    			$header[] = array('Subtotal','$ '.number_format($alltotal,2).chr(160) , '' , '' , '' , '' , '','','', '','', '' );
    			$header[] = array('Tax','$ '.$taxtotal.chr(160), '' , '' , '' , '' , '','','', '','', '' );
    			$header[] = array('Total','$ '.$grandtotal.chr(160) , '' , '' , '' , '' , '','','', '','', '' );




    		}

    	}
    	createXls('bids_export_'.$qid, $header);
    	die();

    	//===============================================================================


    }

	//BID PDF
	function bids_pdf($qid)
    {
    	if ($this->session->userdata('usertype_id') == 3)
    		redirect('admin/purchasingadmin/bids/' . $qid);


    	$bids       = $this->quote_model->getbids($qid);
    	$quoteitems = $this->quote_model->getitems($qid);
    	$awarded    = $this->quote_model->getawardedbid($qid);
    	$quote      = $this->quote_model->get_quotes_by_id($qid);

    	if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
    		redirect('admin/dashboard', 'refresh');
    	}

    	if (!$bids) {
    		$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Bids Yet Placed.</div></div>');
    		redirect('admin/quote/update/' . $qid);
    	}

    	if (!$awarded)
    		$data['isawarded'] = 'No';
    	else {
    		$data['isawarded'] = 'Yes';
    		$data['awarded']   = $awarded;
    	}

    	$minimum  = array();
    	$maximum  = array();
    	$viewbids = array();


    	foreach ($bids as $bid)
    	{
    		$totalprice = 0;
    		foreach ($bid->items as $item) {
    			foreach ($quoteitems as $qi) {
    				if ($qi->itemcode == $item->itemcode) {
    					$item->originaldate = $qi->daterequested;
    				}
    			}
    			$totalprice += $item->totalprice;
    			$key = $item->itemcode;
    			if (!isset($minimum[$key])) {
    				$minimum[$key] = $item->ea;
    				$maximum[$key] = $item->totalprice;
    			} elseif ($minimum[$key] > $item->ea) {
    				$minimum[$key] = $item->ea;
    			} else if ($maximum[$key] < $item->totalprice) {
    				$maximum[$key] = $item->totalprice;
    			}
    		}
    		if (!isset($minimum['totalprice']))
    			$minimum['totalprice'] = $totalprice;
    		elseif ($minimum['totalprice'] > $totalprice)
    		$minimum['totalprice'] = $totalprice;

    		$revisionquote = $this->quote_model->getrevisionno($bid->id,$quote->purchasingadmin);
    		if(isset($revisionquote->revisionid))
    			$bid->revisionno = $revisionquote->revisionid;
    		else
    			$bid->revisionno = 1;

    	}

    	$data['quote']      = $this->quote_model->get_quotes_by_id($qid);
    	$data['quoteitems'] = $quoteitems;
    	$data['project']    = $this->project_model->get_projects_by_id($data['quote']->pid);
    	$data['config']     = (array) $this->settings_model->get_current_settings();
    	$data['bids']       = $bids;
    	$data['minimum']    = $minimum;
    	$data['maximum']    = $maximum;
    	$data['costcodes']  = $this->db->where('project',$quote->pid)->get('costcode')->result();
    	$data['heading']    = $data['quote']->potype == 'Bid' ? "Bids Placed" : "PO Review";


    	//=========================================================================================

    	if(isset($data['isawarded']))
    		$isawarded   = $data['isawarded'];


    	$quote       = $data['quote'];
    	$quoteitems  = $data['quoteitems'];
    	$project     = $data['project'];
    	$config      = $data['config'];
    	$bids        = $data['bids'];
    	$minimum     = $data['minimum'];
    	$maximum     = $data['maximum'];
    	$costcodes   = $data['costcodes'];
    	$heading     = $data['heading'];

    	if(isset($data['awarded']))
    		$awarded     = $data['awarded'];




    	$header[] = array('Report type:','Bid Review' , '' , '' , '' , '' , '','','', '','', '' );

		if($this->session->userdata('managedprojectdetails'))
		{
			$header[] = array('Project Title',$this->session->userdata('managedprojectdetails')->title , '' , '' , '' , '' , '','','', '','', '' );
			//$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );
		}



    	$alltotal=0;


    	foreach($bids as $bid)
    	{

    		$alltotal = '';
		//	$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );

			$header[] = array('<b>PO #:</b>',$quote->ponum , '<b>Company:</b>' , $bid->companyname , '<b>Submitted:</b>' , date('m/d/Y', strtotime($bid->submitdate)) , '','','', '','', '' );

    		$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );
    		$header[] = array('<b>Item Code</b>','<b>Item Name</b>' , '<b>Qty.</b>' , '<b>Unit</b>' , '<b>60 day Low. Price</b>' , '<b>Price EA</b>' , '<b>Price Requested</b>','<b>Total Price</b>','<b>Date Available</b>', '<b>Cost Code</b>','<b>Notes</b>', '<b>Compare</b>' );


    		if($bid->items)
    		{

    			foreach($bid->items as $q)
    			{
    				if($q->itemcode)
    				{
    					$alltotal += $q->quantity * $q->ea;
    					$key  = $q->itemcode;
    					$diff = $q->ea - $minimum[$key];
    					$diff = number_format($diff,2);

    					$k_compare = '';

    					$k_compare =  ($diff==0?$diff==0?'Lowest Unit Price':$diff:($diff<0?'- $':'+ $'.$diff));

    					$low_price = '$ '.$q->ea;
    					if($diff=='0')
    					{
    						$low_price = '$ '. $q->ea;
    					}

    					if($q->minprice >= $q->ea)
    					{
    						$low_price = '$ '.$q->ea;
    						//$low_price = '*New Low Price';
    					}

    					$pr_requested = $q->reqprice;
    					if($q->reqprice > 0)
    					{
    						//--------
    					}
    					else
    					{
    						$pr_requested = $pr_requested.' (RFQ)';
    					}

    					$k_costcode = '';

    					if($isawarded )
    					{
    						$k_costcode = $q->costcode;
    					}
    					else
    					{
    						$k_costcode = '-';
    					}

    					$k_total_price = round($q->quantity * $q->ea,2);


    					$header[] = array($q->itemcode, $q->itemname , $q->quantity , $q->unit , '$ '.$q->minprice.chr(160) , $low_price.chr(160) , $pr_requested,'$ '.$k_total_price.chr(160) , $q->daterequested, $k_costcode,$q->notes, $k_compare );

    				}



    			}
    			$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );
    			$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );

    			$alltotal   = round($alltotal,2);
    			$taxtotal   = $alltotal * $config['taxpercent'] / 100;
    			$taxtotal   = round($taxtotal,2);
    			$grandtotal = $alltotal + $taxtotal;
    			$grandtotal = round($grandtotal,2);
    			$diff       = $alltotal - $minimum['totalprice'];

    			$header[] = array('<b>Subtotal</b>','$ '.number_format($alltotal,2).chr(160) , '' , '' , '' , '' , '','','', '','', '' );
    			$header[] = array('<b>Tax</b>','$ '.$taxtotal.chr(160), '' , '' , '' , '' , '','','', '','', '' );
    			$header[] = array('<b>Total</b>','$ '.$grandtotal.chr(160) , '' , '' , '' , '' , '','','', '','', '' );




    		}

    	}

		$headername = "BID PLACED";
    	createOtherPDF('bids_pdf_'.$qid, $header,$headername);
    	die();

    	//===============================================================================


    }



    function bids($qid)
    {
        if ($this->session->userdata('usertype_id') == 3)
            redirect('admin/purchasingadmin/bids/' . $qid);
        $bids = $this->quote_model->getbids($qid);
        $quoteitems = $this->quote_model->getitems($qid);
        $awarded = $this->quote_model->getawardedbid($qid);
        $quote = $this->quote_model->get_quotes_by_id($qid);

        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre> bids ';print_r($awarded);echo '</pre>';//die;
        if (!$bids) {
            $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Bids Yet Placed.</div></div>');
            redirect('admin/quote/update/' . $qid);
        }

        if (!$awarded)
            $data['isawarded'] = 'No';
        else {
            $data['isawarded'] = 'Yes';
            $data['awarded'] = $awarded;
        }
        $minimum = array();
        $maximum = array();
        $viewbids = array();
       
        $bankaccarray = array();
        $creditaccarray = array();
        foreach ($bids as $bid) {

            $totalprice = 0;
            foreach ($bid->items as $item) {
                foreach ($quoteitems as $qi) {
                    if ($qi->itemcode == $item->itemcode) {
                        $item->originaldate = $qi->daterequested;
                    }
                }
                $totalprice += $item->totalprice;
                $key = $item->itemcode;
                if (!isset($minimum[$key])) {
                    $minimum[$key] = $item->ea;
                    $maximum[$key] = $item->totalprice;
                } elseif ($minimum[$key] > $item->ea) {
                    $minimum[$key] = $item->ea;
                } else if ($maximum[$key] < $item->totalprice) {
                    $maximum[$key] = $item->totalprice;
                }
            }
            if (!isset($minimum['totalprice']))
                $minimum['totalprice'] = $totalprice;
            elseif ($minimum['totalprice'] > $totalprice)
                $minimum['totalprice'] = $totalprice;

                $revisionquote = $this->quote_model->getrevisionno($bid->id,$quote->purchasingadmin);
	    	if(isset($revisionquote->revisionid))
	    		$bid->revisionno = $revisionquote->revisionid;
	    	else
	    		$bid->revisionno = 1;
	    		
	    		
	    	$bankaccount = $this->db->where('company',$bid->company)->get('bankaccount')->row();
	    	$creditresult = $this->db->where('company',$bid->company)->where('purchasingadmin',$quote->purchasingadmin)->get('purchasingtier')->row();
			if((!$bankaccount || !@$bankaccount->routingnumber || !@$bankaccount->accountnumber) && (@$creditresult->creditonly==1) )
			{
				$bankaccarray[$bid->company] = $bid->companyname;
			}	
			
			if(@$creditresult->creditonly==1)
			$creditaccarray[$bid->company] = $bid->companyname;

        }
        $data['quote'] = $this->quote_model->get_quotes_by_id($qid);
        $data['quoteitems'] = $quoteitems;
        //$this->load->model('admin/project_model');
        $data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $data['bids'] = $bids;
        $data['minimum'] = $minimum;
        $data['maximum'] = $maximum;
        $data['bankaccarray'] = $bankaccarray; 
        $data['creditaccarray'] = $creditaccarray; 
        $data['costcodes'] = $this->db->where('project',$quote->pid)->get('costcode')->result();
        $sqlquery = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$quote->pid."' AND forcontract=1";
 		$data['contractcostcodes'] = $this->db->query($sqlquery)->result();           
        $data['heading'] = $data['quote']->potype == 'Bid' ? "Bids Placed" : "PO Review";
        
        $data['invitations'] = $this->quote_model->getInvitedquote($quote->id);
        
        if($data['quote']->potype == 'Bid')
            $this->load->view('admin/bids', $data);
        else
            $this->load->view('admin/directbids', $data);
    }
    
    
    

    function conbids($qid)
    {
    	//echo "<pre>"; print_r($qid); die;
        if ($this->session->userdata('usertype_id') == 3)
            redirect('admin/purchasingadmin/bids/' . $qid);
        $bids = $this->quote_model->getcontractbids($qid);
        $quoteitems = $this->quote_model->getitems($qid);
        $awarded = $this->quote_model->getawardedbid($qid);
        $quote = $this->quote_model->get_quotes_by_id($qid);

       /* if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        if (!$bids) {
            $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Bids Yet Placed.</div></div>');
            redirect('admin/quote/update/' . $qid);
        }*/

        if (!$awarded)
            $data['isawarded'] = 'No';
        else {
            $data['isawarded'] = 'Yes';
            $data['awarded'] = $awarded;
        }
        $minimum = array();
        $maximum = array();
        $viewbids = array();
        foreach ($bids as $bid) {

            $totalprice = 0;
            foreach ($bid->items as $item) {
                foreach ($quoteitems as $qi) {
                    if ($qi->id == $item->itemid) {
                        $item->originaldate = $qi->daterequested;
                    }
                }
                $totalprice += $item->totalprice;
                $key = $item->itemid;
                if (!isset($minimum[$key])) {
                    $minimum[$key] = $item->ea;
                    $maximum[$key] = $item->totalprice;
                } elseif ($minimum[$key] > $item->ea) {
                    $minimum[$key] = $item->ea;
                } else if ($maximum[$key] < $item->totalprice) {
                    $maximum[$key] = $item->totalprice;
                }
            }
            if (!isset($minimum['totalprice']))
                $minimum['totalprice'] = $totalprice;
            elseif ($minimum['totalprice'] > $totalprice)
                $minimum['totalprice'] = $totalprice;

                $revisionquote = $this->quote_model->getrevisionno($bid->id,$quote->purchasingadmin);
	    	if(isset($revisionquote->revisionid))
	    		$bid->revisionno = $revisionquote->revisionid;
	    	else
	    		$bid->revisionno = 1;

        }
       

        $data['quote'] = $this->quote_model->get_quotes_by_id($qid);
        $data['quoteitems'] = $quoteitems;
       
        $data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $data['bids'] = $bids;
        $data['minimum'] = $minimum;
        $data['maximum'] = $maximum;
        $data['costcodes'] = $this->db->where('project',$quote->pid)->get('costcode')->result();
        $sqlquery = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$quote->pid."' AND forcontract=1";
 		$data['contractcostcodes'] = $this->db->query($sqlquery)->result();               

        $data['heading'] = $data['quote']->potype == 'Contract' ? "Bids Placed" : "PO Review";
        
       // echo "<pre>"; print_r($data); die;
        if($data['quote']->potype == 'Contract')
            $this->load->view('admin/conbids', $data);      
    }

    function confirmdirect()
    {
        $qid = $_POST['quote'];
        if(!$qid)
            die;
        if ($this->session->userdata('usertype_id') == 3)
            redirect('admin/purchasingadmin/bids/' . $qid);
        $bids = $this->quote_model->getbids($qid);

        $quote = $this->quote_model->get_quotes_by_id($qid);
        //echo '<pre>';print_r($qutoe);//die;
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {die;
            redirect('admin/dashboard1', 'refresh');
        }
        //echo '<pre> bids ';print_r($awarded);echo '</pre>';//die;
        if (!$bids) {
            $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Bids Yet Placed.</div></div>');
            redirect('admin/quote/update/' . $qid);
        }


        $awardarray = array();
        $awardarray['quote'] = $qid;
        $awardarray['shipto'] = $_POST['shipto'];
        $awardarray['awardedon'] = date('Y-m-d H:i:s');
        $awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $this->quote_model->db->insert('award', $awardarray);
        $awardid = $this->quote_model->db->insert_id();

        foreach($bids as $bid)
        {
            foreach($bid->items as $item)
            {
                if($item->postatus=='Accepted')
                {
                    $item = (array) $item;
                    $itemarray = array();
                    $itemarray['award'] = $awardid;
                    $itemarray['company'] = $bid->company;
                    $itemarray['itemid'] = $item['itemid'];
                    $itemarray['itemcode'] = $item['itemcode'];
                    $itemarray['itemname'] = $item['itemname'];
                    $itemarray['quantity'] = $item['quantity'];
                    $itemarray['unit'] = $item['unit'];
                    $itemarray['ea'] = $item['ea'];
                    $itemarray['totalprice'] = $item['quantity'] * $item['ea'];
                    $itemarray['daterequested'] = $item['daterequested'];
                    $itemarray['costcode'] = $item['costcode'];
                    $itemarray['notes'] = $item['notes'];
                    $itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');

                    $this->quote_model->db->insert('awarditem', $itemarray);
                }
                else
                {
                    $this->db->where('id',$item->id);
                    $this->db->delete('biditem');
                }
            }
        }

        $this->quote_model->db->where('quote', $bid->quote);
        $this->quote_model->db->update('bid', array('complete' => 'Yes'));
        $this->sendawardemail($bid->quote,'nonpaid');
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bid awarded to the company.</div></div>');
        redirect('admin/quote/index/' . $quote->pid);
    }

    function getquoteitem()
    {
        $quote = $_POST['quote'];
        $itemcode = $_POST['itemcode'];

        $this->quote_model->db->where(array('quote' => $quote, 'itemcode' => $itemcode));
        $item = $this->db->get('quoteitem')->row();
        $ret = '<table class="table table-bordered">';
        $ret.= '<tr><td>Item Code</td><td>' . $item->itemcode . '</td></tr>';
        $ret.= '<tr><td>Item Name</td><td>' . $item->itemname . '</td></tr>';
        $ret.= '<tr><td>Quantity</td><td>' . $item->quantity . '</td></tr>';
        $ret.= '<tr><td>Unit</td><td>' . $item->unit . '</td></tr>';
        $ret.= '<tr><td>Price</td><td>' . $item->ea . '</td></tr>';
        $ret.= '<tr><td>Date Requested</td><td>' . $item->daterequested . '</td></tr>';
        $ret.= '<tr><td>Costcode</td><td>' . $item->costcode . '</td></tr>';
        $ret.= '<tr><td>Notes</td><td>' . $item->notes . '</td></tr>';
        $ret.= '<table>';

        echo $ret;
    }

    function delbiditem($id, $quoteid) {
        $quote = $this->quote_model->getbidbyid($quoteid);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $this->quote_model->db->where('id', $id);
        $this->quote_model->db->delete('biditem');
        redirect('admin/quote/bids/' . $quoteid);
    }

    function editbiditemqty($id, $qty, $total) {
        $this->quote_model->db->where('id', $id);
        $this->quote_model->db->update('biditem', array('quantity' => $qty, 'totalprice' => $total));
        echo 1;
    }

    function editbiditemcostcode($id, $costcode) {
        $this->quote_model->db->where('id', $id);
        $this->quote_model->db->update('biditem', array('costcode' => $costcode));
        echo 1;
    }

    function awardbid()
    {
        if ($_POST['bid'])
        {
        	$this->awardbidbyid();
            $bid = $this->quote_model->getbidbyid($_POST['bid']);
            $quote = $this->quote_model->get_quotes_by_id($bid->quote);
        }
        elseif ($_POST['itemids'])
        {
            $this->awardbidbyitems();
            $quote = $this->quote_model->get_quotes_by_id($_POST['quote']);
        }
        if($quote)
        {
        	$quote->awardedbid = $this->quote_model->getawardedbid($quote->id);

        	if(@$quote->awardedbid->items)
        	{
		        $totalcount = count($quote->awardedbid->items);
		        $lowcount = 0;
		        foreach ($quote->awardedbid->items as $ai)
		        {
		        	$itemlowest = $this->itemcode_model->getlowestquoteprice($ai->itemid);

		        	if ($ai->ea <= $itemlowest)
		        		$lowcount++;
		        }

		        if ($lowcount >= ($totalcount * 0.8))
		        	$quote->pricerank = 'great';
		        elseif ($lowcount >= ($totalcount * 0.7))
		        	$quote->pricerank = 'good';
		        elseif ($lowcount >= ($totalcount * 0.5))
		        	$quote->pricerank = 'fair';
		        else
		        	$quote->pricerank = 'poor';
		        $this->db->where('id',$quote->awardedbid->id)->update('award',array('pricerank'=>$quote->pricerank));

        	}
        }
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bid awarded to the selected supplier(s).</div></div>');
        redirect('admin/quote/index/' . $quote->pid);
    }

    function awardbidbyid()
    {
        $bid = $this->quote_model->getbidbyid($_POST['bid']);
        if ($this->session->userdata('usertype_id') == 2 && $bid->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre>';print_r($bid);die;
        if (!$bid) {
            die;
        }
        $awardarray = array();
        $awardarray['quote'] = $bid->quote;
        $awardarray['shipto'] = $_POST['shipto'];
        $awardarray['awardedon'] = date('Y-m-d H:i:s');
        $awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $this->quote_model->db->insert('award', $awardarray);
        $awardid = $this->quote_model->db->insert_id();

        foreach ($bid->items as $item) {
            $item = (array) $item;
            $itemarray = array();
            $itemarray['award'] = $awardid;
            $itemarray['company'] = $bid->company;
            $itemarray['itemid'] = $item['itemid'];
            $itemarray['itemcode'] = $item['itemcode'];
            $itemarray['itemname'] = $item['itemname'];
            $itemarray['quantity'] = $item['quantity'];
            $itemarray['unit'] = $item['unit'];
            $itemarray['ea'] = $item['ea'];
            $itemarray['totalprice'] = $item['quantity'] * $item['ea'];
            $itemarray['daterequested'] = $item['daterequested'];
            $itemarray['costcode'] = $item['costcode'];
            $itemarray['notes'] = $item['notes'];
            $itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');

            $awarditemid = $this->quote_model->db->insert('awarditem', $itemarray);

             $this->db->where('itemid',$item['itemid']);
            $this->db->where('company',$bid->company);
            $this->db->where('type', 'Supplier');
            $companyitem = $this->db->get('companyitem')->row();
            if($companyitem){
            	$bd['qtyavailable'] = $companyitem->qtyavailable-$item['quantity'];
            	$this->db->where('id',$companyitem->id);
            	$this->db->update('companyitem',$bd);
            }
        }
        $this->quote_model->db->where('quote', $bid->quote);
        $this->quote_model->db->update('bid', array('complete' => 'Yes'));
        $this->sendawardemail($bid->quote,'nonpaid');
    }

    function awardbidbyitems()
    {
        $quote = $this->quote_model->get_quotes_by_id($_POST['quote']);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $itemids = $_POST['itemids'];
        //echo '<pre>';print_r($bid);die;
        if (!$itemids) {
            die;
        }

        $items = $this->quote_model->getbiditemsbyids($itemids);
        //print_r($items);die;
        if (!$items)
            die;
        $awardarray = array();
        $awardarray['quote'] = $_POST['quote'];
        $awardarray['shipto'] = $_POST['shipto'];
        $awardarray['awardedon'] = date('Y-m-d H:i:s');
        $awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $this->quote_model->db->insert('award', $awardarray);
        $awardid = $this->quote_model->db->insert_id();

        foreach ($items as $item) {
            $item = (array) $item;
            $itemarray = array();
            $itemarray['award'] = $awardid;
            $itemarray['company'] = $item['company'];
            $itemarray['itemid'] = $item['itemid'];
            $itemarray['itemcode'] = $item['itemcode'];
            $itemarray['itemname'] = $item['itemname'];
            $itemarray['quantity'] = $item['quantity'];
            $itemarray['unit'] = $item['unit'];
            $itemarray['ea'] = $item['ea'];
            $itemarray['totalprice'] = $item['quantity'] * $item['ea'];
            $itemarray['daterequested'] = $item['daterequested'];
            $itemarray['costcode'] = $item['costcode'];
            $itemarray['notes'] = $item['notes'];
            $itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');

            $awarditemid = $this->quote_model->db->insert('awarditem', $itemarray);

            $this->db->where('itemid',$item['itemid']);
            $this->db->where('company',$item['company']);
            $this->db->where('type', 'Supplier');
            $companyitem = $this->db->get('companyitem')->row();
            if($companyitem){
            	$bd['qtyavailable'] = $companyitem->qtyavailable-$item['quantity'];
            	$this->db->where('id',$companyitem->id);
            	$this->db->update('companyitem',$bd);
            }
        }
        $this->quote_model->db->where('quote', $_POST['quote']);
        $this->quote_model->db->update('bid', array('complete' => 'Yes'));
        $this->sendawardemail($_POST['quote'],'nonpaid');
    }

    
    
    function awardcontractbid()
    {
        if ($_POST['bid'])
        {
        	$this->awardbidbycontractid();
            $bid = $this->quote_model->getcontractbidbyid($_POST['bid']);
            $quote = $this->quote_model->get_quotes_by_id($bid->quote);
        }
        elseif ($_POST['itemids'])
        {
            $this->awardbidbycontractitems();
            $quote = $this->quote_model->get_quotes_by_id($_POST['quote']);
        }
        if($quote)
        {
        	$quote->awardedbid = $this->quote_model->getawardedbid($quote->id);

        	if(@$quote->awardedbid->items)
        	{
		        $totalcount = count($quote->awardedbid->items);
		        $lowcount = 0;
		        foreach ($quote->awardedbid->items as $ai)
		        {
		        	$itemlowest = $this->itemcode_model->getlowestquoteprice($ai->itemid);

		        	if ($ai->ea <= $itemlowest)
		        		$lowcount++;
		        }

		        if ($lowcount >= ($totalcount * 0.8))
		        	$quote->pricerank = 'great';
		        elseif ($lowcount >= ($totalcount * 0.7))
		        	$quote->pricerank = 'good';
		        elseif ($lowcount >= ($totalcount * 0.5))
		        	$quote->pricerank = 'fair';
		        else
		        	$quote->pricerank = 'poor';
		        $this->db->where('id',$quote->awardedbid->id)->update('award',array('pricerank'=>$quote->pricerank));

        	}
        }
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bid awarded to the selected supplier(s).</div></div>');
        redirect('admin/quote/index/' . $quote->pid);
    }

    function awardbidbycontractid()
    {
        $bid = $this->quote_model->getcontractbidbyid($_POST['bid']);
        if ($this->session->userdata('usertype_id') == 2 && $bid->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre>';print_r($bid);die;
        if (!$bid) {
            die;
        }
        $awardarray = array();
        $awardarray['quote'] = $bid->quote;
        $awardarray['shipto'] = $_POST['shipto'];
        $awardarray['awardedon'] = date('Y-m-d H:i:s');
        $awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $this->quote_model->db->insert('award', $awardarray);
        $awardid = $this->quote_model->db->insert_id();

        foreach ($bid->items as $item) {
            $item = (array) $item;
            $itemarray = array();
            $itemarray['award'] = $awardid;
            $itemarray['company'] = $bid->company;           
            $itemarray['itemid'] = $item['itemid'];
            $itemarray['itemname'] = $item['itemname'];
            $itemarray['quantity'] = $item['quantity'];            
            $itemarray['ea'] = $item['ea'];
            $itemarray['totalprice'] = $item['totalprice'];
            $itemarray['daterequested'] = $item['daterequested'];
            $itemarray['costcode'] = $item['costcode'];
            $itemarray['attach'] = $item['attach'];
            $itemarray['notes'] = $item['notes'];
            $itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');

            $awarditemid = $this->quote_model->db->insert('awarditem', $itemarray);            
        }
        $this->quote_model->db->where('quote', $bid->quote);
        $this->quote_model->db->update('bid', array('complete' => 'Yes'));
        $this->sendcontractawardemail($bid->quote,'contract');
    }

    function awardbidbycontractitems()
    {
        $quote = $this->quote_model->get_quotes_by_id($_POST['quote']);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $itemids = $_POST['itemids'];
        //echo '<pre>';print_r($bid);die;
        if (!$itemids) {
            die;
        }

        $items = $this->quote_model->getbiditemsbyids($itemids);
        //print_r($items);die;
        if (!$items)
            die;
        $awardarray = array();
        $awardarray['quote'] = $_POST['quote'];
        $awardarray['shipto'] = $_POST['shipto'];
        $awardarray['awardedon'] = date('Y-m-d H:i:s');
        $awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $this->quote_model->db->insert('award', $awardarray);
        $awardid = $this->quote_model->db->insert_id();

        foreach ($items as $item) {
            $item = (array) $item;
            $itemarray = array();
            $itemarray['award'] = $awardid;
            $itemarray['company'] = $item['company'];           
            $itemarray['itemid'] = $item['itemid'];
            $itemarray['itemname'] = $item['itemname'];           
            $itemarray['ea'] = $item['ea'];
            $itemarray['totalprice'] = $item['totalprice'];            
            $itemarray['daterequested'] = $item['daterequested'];
            $itemarray['costcode'] = $item['costcode'];
            $itemarray['attach'] = $item['attach'];
            $itemarray['notes'] = $item['notes'];
            $itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');

            $awarditemid = $this->quote_model->db->insert('awarditem', $itemarray);

            $this->db->where('itemid',$item['itemid']);
            $this->db->where('company',$item['company']);
            $this->db->where('type', 'Supplier');
            $companyitem = $this->db->get('companyitem')->row();
            if($companyitem){
            	$bd['qtyavailable'] = $companyitem->qtyavailable-$item['quantity'];
            	$this->db->where('id',$companyitem->id);
            	$this->db->update('companyitem',$bd);
            }
        }
        $this->quote_model->db->where('quote', $_POST['quote']);
        $this->quote_model->db->update('bid', array('complete' => 'Yes'));
        $this->sendcontractawardemail($_POST['quote'],'contract');
    }
    
    function getminprice($companyid)
    {
        //print_r($_POST);die;
        $itemid = $_POST['itemid'];
        $this->quote_model->db->where(array('itemid' => $itemid, 'company' => $companyid, 'purchasingadmin'=>$this->session->userdata('purchasingadmin')));
        $query = $this->quote_model->db->get('minprice');
        if ($query->result())
            echo $query->row('price');
        else
            echo -1;
        die;
    }

    public function update_invoice_status() {
        $update['status'] = ucfirst($this->input->post('status'));
        $invoice = $this->quote_model->update_invoice_by_number($this->input->post('id'), $update);
        echo '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice Status Changed.</div></div>';
    }

    public function update_invoice_payment_status()
    {
        //print_r($_POST);die;
        $_POST['paymentstatus'] = 'Paid';
        $_POST['status'] = 'Pending';
        $this->db->where('invoicenum', $_POST['invoicenum']);
        $amount = $_POST['amount'];
        unset($_POST['amount']);
        $_POST['paymentdate'] = date('Y-m-d');
        $this->db->update('received', $_POST);


        if($_POST['paymentstatus'] == 'Paid')
        {
    		$company = $this->db->select('company.*')
    		            ->from('received')
    		            ->join('awarditem','received.awarditem=awarditem.id')
    		            ->join('company','awarditem.company=company.id')
    		            ->where('invoicenum',$_POST['invoicenum'])
    		            ->get()->row();
    		$quote = $this->db->select('quote.*')
    		            ->from('received')
    		            ->join('awarditem','received.awarditem=awarditem.id')
    		            ->join('award','awarditem.award=award.id')
    		            ->join('quote','award.quote=quote.id')
    		            ->where('invoicenum',$_POST['invoicenum'])
    		            ->get()->row();

    		$pa = $this->db->where('id',$this->session->userdata('id'))->get('users')->row();

    		$data['email_body_title']  = "Dear " . @$company->title ;
    		$data['email_body_content'] =  $pa->companyname." sent payment for the Invoice#: ".$_POST['invoicenum'].";
    		The following information sent:
    		<br/>
    		PO# : ".$quote->ponum."
    		<br/>
    		Payment By : ".$pa->companyname."
    		<br/>
    		Payment Type : ".$_POST['paymenttype']."
    		<br/>
    		Payment Amount : ".$amount."
    		<br/>
    		Ref# : ".$_POST['refnum']."
    		<br/>
    		Payment Date: ".date('m/d/Y')."
    		<br><br>";
    		$loaderEmail = new My_Loader();
    		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    		$this->load->library('email');
    		$config['charset'] = 'utf-8';
    		$config['mailtype'] = 'html';
    		$this->email->initialize($config);
    		$this->email->from($pa->email, $pa->companyname);
    		$this->email->to(@$company->title . ',' . @$company->primaryemail);
    		$this->email->subject('Payment made for the invoice: '.$_POST['invoicenum']);
    		$this->email->message($send_body);
    		$this->email->set_mailtype("html");
    		$this->email->send();
        }

        echo '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice Payment Status Changed.</div></div>';
    }


 function export()
    {
    	$invoices = $this->quote_model->getinvoices();

		$count = count($invoices);
        $items = array();
		 if ($count >= 1)
        {
            $settings = $this->settings_model->get_current_settings();
            $available_statuses = array('pending', 'verified', 'error');
            $data['available_statuses'] = $available_statuses;
            foreach ($invoices as $invoice)
            if($invoice->invoicenum && $invoice->quote->purchasingadmin == $this->session->userdata('purchasingadmin') )
            {
                $invoice->ponum = $invoice->quote->ponum;

                if($invoice->quote->potype=='Contract'){
                $company = $this->db->select('users.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('users','awarditem.company=users.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;
                }else{
                $company = $this->db->select('company.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('company','awarditem.company=company.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;                      
                }
                           
                $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
                $invoice->bankaccount = $bankaccount;

                $invoice->companydetails = $company;
                $invoice->totalprice = $invoice->totalprice + ($invoice->totalprice*$settings->taxpercent/100);
                //$invoice->status = $invoice->quote->status;
                
                if($invoice->quote->potype=='Contract')
                $invoice->actions = '<a href="javascript:void(0)" onclick="showContractInvoice(\'' . $invoice->invoicenum . '\',\''.$invoice->quote->id.'\')"><span class="icon-2x icon-search"></span></a>';
                else 
                 $invoice->actions = '<a href="javascript:void(0)" onclick="showInvoice(\'' . $invoice->invoicenum . '\',\''.$invoice->quote->id.'\')"><span class="icon-2x icon-search"></span></a>';
                 
                $options = false;
                foreach ($available_statuses as $status_key => $status_text)
                {

                    if (strtolower($invoice->status) == $status_text) {
                        $selected = " selected=\"selected\"";
                    } else {
                        $selected = '';
                    }
                    $options[] = "<option value=\"$status_text\" $selected>$status_text</option>";
                }
                $options_payment = array();
                $options_paymenttype = array();
                $options_payment[]="<option value=\"Paid\" ".($invoice->paymentstatus=='Paid'?" selected=\"selected\"":'').">Paid</option>";;
                //$options_payment[]="<option value=\"Requested Payment\" ".($invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Requested Paid</option>";;
                $options_payment[]="<option value=\"Unpaid\" ".($invoice->paymentstatus=='Unpaid'||$invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Unpaid</option>";;

                $options_paymenttype[]="<option value=\"\">Select Payment Type</option>";
                if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
                $options_paymenttype[]="<option value=\"Credit Card\" ".($invoice->paymenttype=='Credit Card'?" selected=\"selected\"":'').">Credit Card</option>";;
                $options_paymenttype[]="<option value=\"Cash\" ".($invoice->paymenttype=='Cash'?" selected=\"selected\"":'').">Cash</option>";;
                $options_paymenttype[]="<option value=\"Check\" ".($invoice->paymenttype=='Check'?" selected=\"selected\"":'').">Check</option>";;

                $txtrefnum = "<input type=\"text\" id=\"refnum_$invoice->invoicenum\" name=\"refnum\" value=\"$invoice->refnum\"/>";

                $update_button = "<button onclick=\"update_invoice_status('$invoice->invoicenum')\">update</button>";
                $update_payment_button = "<button onclick=\"update_invoice_payment_status('$invoice->invoicenum')\">update</button>";

                $status_html = "<select id=\"invoice_$invoice->invoicenum\" name=\"status_element\">" . implode("", $options) . "</select>" . $update_button;

                $payment_status_html = "<select id=\"invoice_payment_$invoice->invoicenum\" name=\"payment_status_element\">" . implode("", $options_payment) . "</select>";
                $payment_status_html .= "<select id=\"invoice_paymenttype_$invoice->invoicenum\" name=\"paymenttype_status_element\" onchange=\"paycc(this.value,'".$invoice->invoicenum."','".$invoice->totalprice."');\">" . implode("", $options_paymenttype) . "</select>";
                $payment_status_html .= $txtrefnum;
                $payment_status_html .= $update_payment_button;
                if($invoice->paymentstatus=='Requested Payment')
                {
                    $payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Supplier</i>';
                }

                $invoice->status_selectbox = $status_html;
                $invoice->payment_status_selectbox = $payment_status_html;              

                $items[] = $invoice;
            }

            $data['items'] = $items;
            $data['jsfile'] = 'invoicejs.php';
        } else {
        	$data['items'] = array();
            $data['message'] = 'No Records';
        }



    	//===============================================================================

		$header[] = array('Report type' , 'Invoices' , '' , '' , '' , '' , '' );

		if($this->session->userdata('managedprojectdetails'))
		{
			$header[] = array('Project Title', $this->session->userdata('managedprojectdetails')->title , '' , '' , '' , '' , '' );			$header[] = array('' , '' , '' , '' , '' , '' , '' );
		}

    	$header[] = array('PO Number' , 'Invoice' , 'Received On' , 'Total Cost' , 'Payment' , 'Verification' , 'Date Due' );
    	foreach($invoices as $i)
    	{
    		$dddate = '';
    		if($i->quote->duedate)
    		{ $dddate = date("m/d/Y", strtotime($i->quote->duedate)); }

    		$total_price = '';

    		if($i->totalprice > 0)
    		{
    			
    			if(@$i->discount_percent){                	
                	       	                	
                	$i->totalprice = $i->totalprice - ($i->totalprice*$i->discount_percent/100);                	
                }
                
                if(@$invoice->penalty_percent){                	
                	
                	$i->totalprice = $i->totalprice + (($i->totalprice*$i->penalty_percent/100)*$i->penaltycount);
                }
    			
                $i->totalprice = number_format($i->totalprice,2);
    			
    			$total_price = '$ '.$i->totalprice;
    		}


			//----------------------------------------------------------
			$p_status = $i->paymentstatus;

			if($i->status == 'Verified')
			{
				$p_status.= '/'.$i->paymenttype.'/'.$i->refnum;
			}
			if($i->paymentstatus=='Requested Payment' && isset($i->companydetails))
			{
			   if($i->quote->potype=='Contract')
               $p_status.= '/Payment Requested by/'.$i->companydetails->companyname.'on'.$i->refnum;
               else 
               $p_status.= '/Payment Requested by/'.$i->companydetails->title.'on'.$i->refnum;
			}
			//-----------------------------------------------------------

    		$header[] = array($i->quote->ponum,  $i->invoicenum,  $i->receiveddate , $total_price.chr(160) , $p_status ,$i->quote->status ,$dddate );
    	}

    	createXls('invoices' , $header);
    	die();

    }

	// Invoices PDF
	 function invoicepdf()
    {
    	$invoices = $this->quote_model->getinvoices();

		$count = count($invoices);
        $items = array();
		 if ($count >= 1)
        {
            $settings = $this->settings_model->get_current_settings();
            $available_statuses = array('pending', 'verified', 'error');
            $data['available_statuses'] = $available_statuses;
            foreach ($invoices as $invoice)
            if($invoice->invoicenum && $invoice->quote->purchasingadmin == $this->session->userdata('purchasingadmin') )
            {
                $invoice->ponum = $invoice->quote->ponum;

                if($invoice->quote->potype=='Contract'){
                $company = $this->db->select('users.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('users','awarditem.company=users.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;
                }else{
                $company = $this->db->select('company.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('company','awarditem.company=company.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;
                }
                           
                $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
                $invoice->bankaccount = $bankaccount;

                $invoice->companydetails = $company;
                $invoice->totalprice = $invoice->totalprice + ($invoice->totalprice*$settings->taxpercent/100);
                //$invoice->status = $invoice->quote->status;
                
                if($invoice->quote->potype=='Contract')
                $invoice->actions = '<a href="javascript:void(0)" onclick="showContractInvoice(\'' . $invoice->invoicenum . '\')"><span class="icon-2x icon-search"></span></a>';
                else                 
                $invoice->actions = '<a href="javascript:void(0)" onclick="showInvoice(\'' . $invoice->invoicenum . '\')"><span class="icon-2x icon-search"></span></a>';
                $options = false;
                foreach ($available_statuses as $status_key => $status_text)
                {

                    if (strtolower($invoice->status) == $status_text) {
                        $selected = " selected=\"selected\"";
                    } else {
                        $selected = '';
                    }
                    $options[] = "<option value=\"$status_text\" $selected>$status_text</option>";
                }
                $options_payment = array();
                $options_paymenttype = array();
                $options_payment[]="<option value=\"Paid\" ".($invoice->paymentstatus=='Paid'?" selected=\"selected\"":'').">Paid</option>";;
                //$options_payment[]="<option value=\"Requested Payment\" ".($invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Requested Paid</option>";;
                $options_payment[]="<option value=\"Unpaid\" ".($invoice->paymentstatus=='Unpaid'||$invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Unpaid</option>";;

                $options_paymenttype[]="<option value=\"\">Select Payment Type</option>";
                if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
                $options_paymenttype[]="<option value=\"Credit Card\" ".($invoice->paymenttype=='Credit Card'?" selected=\"selected\"":'').">Credit Card</option>";;
                $options_paymenttype[]="<option value=\"Cash\" ".($invoice->paymenttype=='Cash'?" selected=\"selected\"":'').">Cash</option>";;
                $options_paymenttype[]="<option value=\"Check\" ".($invoice->paymenttype=='Check'?" selected=\"selected\"":'').">Check</option>";;

                $txtrefnum = "<input type=\"text\" id=\"refnum_$invoice->invoicenum\" name=\"refnum\" value=\"$invoice->refnum\"/>";

                $update_button = "<button onclick=\"update_invoice_status('$invoice->invoicenum')\">update</button>";
                $update_payment_button = "<button onclick=\"update_invoice_payment_status('$invoice->invoicenum')\">update</button>";

                $status_html = "<select id=\"invoice_$invoice->invoicenum\" name=\"status_element\">" . implode("", $options) . "</select>" . $update_button;

                $payment_status_html = "<select id=\"invoice_payment_$invoice->invoicenum\" name=\"payment_status_element\">" . implode("", $options_payment) . "</select>";
                $payment_status_html .= "<select id=\"invoice_paymenttype_$invoice->invoicenum\" name=\"paymenttype_status_element\" onchange=\"paycc(this.value,'".$invoice->invoicenum."','".$invoice->totalprice."');\">" . implode("", $options_paymenttype) . "</select>";
                $payment_status_html .= $txtrefnum;
                $payment_status_html .= $update_payment_button;
                if($invoice->paymentstatus=='Requested Payment')
                {
                    $payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Supplier</i>';
                }

                $invoice->status_selectbox = $status_html;
                $invoice->payment_status_selectbox = $payment_status_html;

                $invoice->totalprice = $invoice->totalprice;

                $items[] = $invoice;
            }

            $data['items'] = $items;
            $data['jsfile'] = 'invoicejs.php';
        } else {
        	$data['items'] = array();
            $data['message'] = 'No Records';
        }



    	//===============================================================================

		$header[] = array('Report type:' , 'Invoices' , '' , '' , '' , '' , '' );

		if($this->session->userdata('managedprojectdetails'))
		{
			$header[] = array('<b>Project Title</b>', $this->session->userdata('managedprojectdetails')->title , '' , '' , '' , '' , '' );
			//$header[] = array('' , '' , '' , '' , '' , '' , '' );
		}

    	$header[] = array('<b>PO Number</b>' , '<b>Invoice</b>' , '<b>Received On</b>' , '<b>Total Cost</b>' , '<b>Payment</b>' , '<b>Verification</b>' , '<b>Date Due</b>' );
    	foreach($invoices as $i)
    	{
    		$dddate = '';
    		if($i->quote->duedate)
    		{ $dddate = date("m/d/Y", strtotime($i->quote->duedate)); }

    		$total_price = '';

    		if($i->totalprice > 0)
    		{              
                if(@$i->discount_percent){                	
                	       	                	
                	$i->totalprice = $i->totalprice - ($i->totalprice*$i->discount_percent/100);                	
                }
                
                if(@$invoice->penalty_percent){                	
                	
                	$i->totalprice = $i->totalprice + (($i->totalprice*$i->penalty_percent/100)*$i->penaltycount);
                }
    			
                $i->totalprice = number_format($i->totalprice,2);
    			
    			$total_price = '$ '.$i->totalprice;
    		}


			//----------------------------------------------------------
			$p_status = $i->paymentstatus;

			if($i->status == 'Verified')
			{
				$p_status.= '/'.$i->paymenttype.'/'.$i->refnum;
			}
			if($i->paymentstatus=='Requested Payment' && isset($i->companydetails))
			{  
			   if($i->quote->potype=='Contract')
               $p_status.= '/Payment Requested by/'.$i->companydetails->companyname.'on'.$i->refnum;
               else 
               $p_status.= '/Payment Requested by/'.$i->companydetails->title.'on'.$i->refnum;
			}
			//-----------------------------------------------------------

    		$header[] = array($i->quote->ponum,  $i->invoicenum,  $i->receiveddate , $total_price.chr(160) , $p_status ,$i->quote->status ,$dddate );
    	}

		$headername = "INVOICES";
    	createOtherPDF('invoices', $header,$headername);
    	die();

    }





    function invoices()
    {
        $invoices = $this->quote_model->getinvoices();
        //echo "<pre>",print_r($invoices);die;
        $aginginvoices = $this->quote_model->getinvoicesforagingtable();
        $count = count($invoices);
        $items = array();
        $companylist=array();
        if ($count >= 1)
        {
            $settings = $this->settings_model->get_current_settings();
            $available_statuses = array('pending', 'verified', 'error');
            $data['available_statuses'] = $available_statuses;
            foreach ($invoices as $invoice)
            if($invoice->invoicenum && $invoice->quote->purchasingadmin == $this->session->userdata('purchasingadmin') )
            {
            	
            	$sql = "SELECT c.* FROM " . $this->db->dbprefix('company') . " c," . $this->db->dbprefix('awarditem') . " ai
					  WHERE c.id=ai.company AND ai.id='{$invoice->quote->awarditemid}'";           
            	$result= $this->db->query($sql)->row();
            	    	
                $invoice->ponum = $invoice->quote->ponum;

                if($invoice->quote->potype=='Contract'){
                $company = $this->db->select('users.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('users','awarditem.company=users.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;
                }else{                
                $company = $this->db->select('company.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('company','awarditem.company=company.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;
                }           
                           
                $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
                $invoice->bankaccount = $bankaccount;

                $invoice->companydetails = $company;
                //$invoice->totalprice = $invoice->totalprice + ($invoice->totalprice*$settings->taxpercent/100);
                //$invoice->status = $invoice->quote->status;
                if($invoice->quote->potype=='Contract')
                $invoice->actions = '<a href="javascript:void(0)" onclick="showContractInvoice(\'' . $invoice->invoicenum . '\',\''.$invoice->quote->id.'\')"><span class="icon-2x icon-search"></span></a>';
                else 
                $invoice->actions = '<a href="javascript:void(0)" onclick="showInvoice(\'' . $invoice->invoicenum . '\',\''.$invoice->quote->id.'\')"><span class="icon-2x icon-search"></span></a>';
                
                $options = false;
                foreach ($available_statuses as $status_key => $status_text)
                {

                    if (strtolower($invoice->status) == $status_text) {
                        $selected = " selected=\"selected\"";
                    } else {
                        $selected = '';
                    }
                    $options[] = "<option value=\"$status_text\" $selected>$status_text</option>";
                }
                $options_payment = array();
                $options_paymenttype = array();
                $options_payment[]="<option value=\"Paid\" ".($invoice->paymentstatus=='Paid'?" selected=\"selected\"":'').">Paid</option>";;
                //$options_payment[]="<option value=\"Requested Payment\" ".($invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Requested Paid</option>";;
                $options_payment[]="<option value=\"Unpaid\" ".($invoice->paymentstatus=='Unpaid'||$invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Unpaid</option>";;

                $options_paymenttype[]="<option value=\"\">Select Payment Type</option>";
                if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
                $options_paymenttype[]="<option value=\"Credit Card\" ".($invoice->paymenttype=='Credit Card'?" selected=\"selected\"":'').">Credit Card</option>";;
                $options_paymenttype[]="<option value=\"Cash\" ".($invoice->paymenttype=='Cash'?" selected=\"selected\"":'').">Cash</option>";;
                $options_paymenttype[]="<option value=\"Check\" ".($invoice->paymenttype=='Check'?" selected=\"selected\"":'').">Check</option>";;

                $txtrefnum = "<input type=\"text\" id=\"refnum_$invoice->invoicenum\" name=\"refnum\" value=\"$invoice->refnum\"/>";

                $update_button = "<button onclick=\"update_invoice_status('$invoice->invoicenum')\">update</button>";
                $update_payment_button = "<button onclick=\"update_invoice_payment_status('$invoice->invoicenum')\">update</button>";

                $status_html = "<select id=\"invoice_$invoice->invoicenum\" name=\"status_element\">" . implode("", $options) . "</select>" . $update_button;

                $payment_status_html = "<select id=\"invoice_payment_$invoice->invoicenum\" name=\"payment_status_element\">" . implode("", $options_payment) . "</select>";
                $payment_status_html .= "<select id=\"invoice_paymenttype_$invoice->invoicenum\" name=\"paymenttype_status_element\" onchange=\"paycc(this.value,'".$invoice->invoicenum."','".$invoice->totalprice."');\">" . implode("", $options_paymenttype) . "</select>";
                $payment_status_html .= $txtrefnum;
                $payment_status_html .= $update_payment_button;
                if($invoice->paymentstatus=='Requested Payment')
                {
                	if($invoice->quote->potype=='Contract')
               			$payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Company</i>';
               		else 
                    	$payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Supplier</i>';
                }

                $invoice->status_selectbox = $status_html;
                $invoice->payment_status_selectbox = $payment_status_html;

                //$invoice->totalprice = number_format($invoice->totalprice,2);

                $items[] = $invoice;
                $companylist[]=$result;
                
            } $data['companylist1']=$companylist; 

            $data['items'] = $items;
            $data['jsfile'] = 'invoicejs.php';
        } else {
        	$data['items'] = array();
            $data['message'] = 'No Records';
        }
        
        $data['aginginvoices'] = $aginginvoices;
        $settings = $this->settings_model->get_current_settings();
        $data['taxpercent'] = $settings->taxpercent;
        //print_r($items);die;
        $data ['addlink'] = '';
        $data ['heading'] = 'Invoices';
        $query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
        $data['companies'] = $this->db->query($query)->result();

        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}
		
		/*Following code from Report controller.*/
		//------------------
		$reports = $this->report_model->get_reports1();	
		$count = count ($reports);
		$items = array();
		if ($count >= 1) 
		{
			foreach ($reports as $report) 
			{
				$items[] = $report;
			}
		    $data['reports'] = $items;
		    //$data['taxdata']=$settings->taxpercent;
		}
		if(!$items)
		{
		    $this->data['message'] = 'No Records';
		}
		//-------------
		
		
        $this->load->view('admin/invoices', $data);
    }

   function invoice($invid='',$quotid='')
    {
       if($invid=='')
    	{
        $invoicenum = @$_POST['invoicenum'];
    	}
    	else 
    	{
    	$invoicenum = $invid;	
    	}
    	
    	if($quotid=='')
    	{
        $invoicequote = $_POST['invoicequote'];
    	}
    	else 
    	{
    	$invoicequote =$quotid;	
    	}
    	
    	
    	/*if(isset($_POST['invoicequote']) && $_POST['invoicequote']!="")	
			$invoicequote = $_POST['invoicequote'];
		else 
			$invoicequote = "";	*/
    	
        if (!$invoicenum)
            redirect('quote/invoices');
        $invoice = $this->quote_model->getinvoicebynum($invoicenum,$invoicequote);
        $invoice->error = 0;
        foreach ($invoice->items as $invoiceitem) {
        	
        	 if(@$invoiceitem->invoice_type == "alreadypay"){ 
                  $invoice->alreadypay = 1;
                   $invoice->paidinvoicenum = $this->db->from('received')->where('purchasingadmin',$invoiceitem->purchasingadmin)->where('awarditem',$invoiceitem->awarditem)->get()->row()->invoicenum;        
        	 }     
        	 
        	 if(@$invoiceitem->invoice_type == "fullpaid"){
        	  	$invoice->fullpaid = 1;	  
        	  	$invoice->relatedinvoices = $this->db->select('invoicenum')->from('received')->where('purchasingadmin',$invoiceitem->purchasingadmin)->where('awarditem',$invoiceitem->awarditem)->where('invoice_type',"alreadypay")->get()->result();   
        	 } 	
        	 
        	 if(@$invoiceitem->invoice_type == "error"){ 
        	 	 $invoice->error = 1;
        	 }	
        }
        
        $awarded = $this->quote_model->getawardedbid($invoice->quote);
        //print_r($invoice); echo $this->session->userdata('purchasingadmin');die;
        if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('purchasingadmin')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre>';print_r($invoice);die;

        $this->db->where('id', $this->session->userdata('purchasingadmin'));
        $pa = $this->db->get('users')->row();

        $quote = $awarded->quotedetails;
        $project = $this->project_model->get_projects_by_id($quote->pid);
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);

        if(@$invoice->items[0]->companyid){
        $company = $this->db->from('received')
                    ->join('awarditem','received.awarditem=awarditem.id')
                    ->join('company','company.id=awarditem.company')->where('company',$invoice->items[0]->companyid)
                    ->get()->row();
        $data['company'] = $company;            
        }	
                    
        $data['quote'] = $quote;
        $data['awarded'] = $awarded;
        $data['config'] = $config;
        $data['project'] = $project;
        $data['invoice'] = $invoice;        
        $data['heading'] = "Invoice Details";
        $data['purchasingadmin'] = $pa;
        
        $invoices = $this->quote_model->getinvoicesforpayment($invoicenum);
        $invoice = $invoices[0];
        //echo "<pre>",print_r($invoice); die;
        $items = array();
        if($invoice){
        $settings = $this->settings_model->get_current_settings();
        $available_statuses = array('pending', 'verified', 'error');
        $data['available_statuses'] = $available_statuses;        
            if($invoice->invoicenum && $invoice->quote->purchasingadmin == $this->session->userdata('purchasingadmin') )
            {
                $invoice->ponum = $invoice->quote->ponum;

                if($invoice->quote->potype=='Contract'){
                $company = $this->db->select('users.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('users','awarditem.company=users.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;
                }else{                
                $company = $this->db->select('company.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('company','awarditem.company=company.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;
                }           
                           
                $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
                $invoice->bankaccount = $bankaccount;

                $invoice->companydetails = $company;
                $invoice->totalprice = $invoice->totalprice + ($invoice->totalprice*$settings->taxpercent/100);
                //$invoice->status = $invoice->quote->status;
                if($invoice->quote->potype=='Contract')
                $invoice->actions = '<a href="javascript:void(0)" onclick="showContractInvoice(\'' . $invoice->invoicenum . '\',\''.$invoice->quote->id.'\')"><span class="icon-2x icon-search"></span></a>';
                else 
                $invoice->actions = '<a href="javascript:void(0)" onclick="showInvoice(\'' . $invoice->invoicenum . '\',\''.$invoice->quote->id.'\')"><span class="icon-2x icon-search"></span></a>';
                
                $options = false;
                foreach ($available_statuses as $status_key => $status_text)
                {

                    if (strtolower($invoice->status) == $status_text) {
                        $selected = " selected=\"selected\"";
                    } else {
                        $selected = '';
                    }
                    $options[] = "<option value=\"$status_text\" $selected>$status_text</option>";
                }
                $options_payment = array();
                $options_paymenttype = array();
                $options_payment[]="<option value=\"Paid\" ".($invoice->paymentstatus=='Paid'?" selected=\"selected\"":'').">Paid</option>";;
                //$options_payment[]="<option value=\"Requested Payment\" ".($invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Requested Paid</option>";;
                $options_payment[]="<option value=\"Unpaid\" ".($invoice->paymentstatus=='Unpaid'||$invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Unpaid</option>";;

                $options_paymenttype[]="<option value=\"\">Select Payment Type</option>";
                if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
                $options_paymenttype[]="<option value=\"Credit Card\" ".($invoice->paymenttype=='Credit Card'?" selected=\"selected\"":'').">Credit Card</option>";;
                $options_paymenttype[]="<option value=\"Cash\" ".($invoice->paymenttype=='Cash'?" selected=\"selected\"":'').">Cash</option>";;
                $options_paymenttype[]="<option value=\"Check\" ".($invoice->paymenttype=='Check'?" selected=\"selected\"":'').">Check</option>";;

                $txtrefnum = "<input type=\"text\" id=\"refnum_$invoice->invoicenum\" name=\"refnum\" value=\"$invoice->refnum\"/>";

                $update_button = "<button onclick=\"update_invoice_status('$invoice->invoicenum')\">update</button>";
                $update_payment_button = "<button onclick=\"update_invoice_payment_status('$invoice->invoicenum')\">update</button>";

                $status_html = "<select id=\"invoice_$invoice->invoicenum\" name=\"status_element\">" . implode("", $options) . "</select>" . $update_button;

                $payment_status_html = "<select id=\"invoice_payment_$invoice->invoicenum\" name=\"payment_status_element\">" . implode("", $options_payment) . "</select>";
                $payment_status_html .= "<select id=\"invoice_paymenttype_$invoice->invoicenum\" name=\"paymenttype_status_element\" onchange=\"paycc(this.value,'".$invoice->invoicenum."','".$invoice->totalprice."');\">" . implode("", $options_paymenttype) . "</select>";
                $payment_status_html .= $txtrefnum;
                $payment_status_html .= $update_payment_button;
                if($invoice->paymentstatus=='Requested Payment')
                {
                	if($invoice->quote->potype=='Contract')
               			$payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Company</i>';
               		else 
                    	$payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Supplier</i>';
                }

                $invoice->status_selectbox = $status_html;
                $invoice->payment_status_selectbox = $payment_status_html;

                //$invoice->totalprice = number_format($invoice->totalprice,2);

                $items[] = $invoice;
            }

            $data['items'] = $items;
            $data['jsfile'] = 'invoicejs.php';
        } else {
        	$data['items'] = array();
            $data['message'] = 'No Records';
        }
        //print_r($items);die;
        $data ['addlink'] = '';
        $data ['heading'] = 'Invoices';
        $query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
        $data['companies'] = $this->db->query($query)->result();

        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}     
        
        $this->load->view('admin/invoice', $data);
    }


    
   function contract_invoice($invid='',$quotid='')
    {
    	if($invid=='')
    	{
        $invoicenum = @$_POST['invoicenum'];
    	}
    	else 
    	{
    	$invoicenum = $invid;	
    	}
    	
    	if($quotid=='')
    	{
        $invoicequote = $_POST['invoicequote'];
    	}
    	else 
    	{
    	$invoicequote =$quotid;	
    	}
    	
        if (!$invoicenum)
            redirect('admin/quote/invoices');
        $invoice = $this->quote_model->geticontractnvoicebynum($invoicenum, $invoicequote);
        $awarded = $this->quote_model->getawardedcontractbid($invoice->quote);
        //echo "<pre>",print_r($invoice); echo $this->session->userdata('purchasingadmin');die;
        /*if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('purchasingadmin')) {
            redirect('admin/dashboard', 'refresh');
        }*/
        //echo '<pre>';print_r($invoice);die;

        $this->db->where('id', $this->session->userdata('purchasingadmin'));
        $pa = $this->db->get('users')->row();

        $quote = $awarded->quotedetails;
        $project = $this->project_model->get_projects_by_id($quote->pid);
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
		
        if(@$invoice->items[0]->companyid){
        $company = $this->db->from('received')
                    ->join('awarditem','received.awarditem=awarditem.id')
                    ->join('company','company.id=awarditem.company')->where('company',$invoice->items[0]->companyid)
                    ->get()->row();
        $data['company'] = $company;            
        }            
                    
        $data['quote'] = $quote;
        $data['awarded'] = $awarded;
        $data['config'] = $config;
        $data['project'] = $project;
        $data['invoice'] = $invoice;
        $data['company'] = $company;
        $data['heading'] = "Invoice Details";
        $data['purchasingadmin'] = $pa;
        
        
        
        $invoices = $this->quote_model->getinvoicesforpayment($invoicenum);
        if (array_key_exists(0, $invoices)) {
        $invoice2 = $invoices[0];
        }
        //echo "<pre>",print_r($invoice2); die;
        $items = array();
        if (array_key_exists(0, $invoices)) {
        $settings = $this->settings_model->get_current_settings();
        $available_statuses = array('pending', 'verified', 'error');
        $data['available_statuses'] = $available_statuses;        
            if($invoice2->invoicenum && $invoice2->quote->purchasingadmin == $this->session->userdata('purchasingadmin') )
            {
                $invoice2->ponum = $invoice2->quote->ponum;

                if($invoice2->quote->potype=='Contract'){
                $company = $this->db->select('users.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('users','awarditem.company=users.id')
                           ->where('received.invoicenum',$invoice2->invoicenum)
                           ->get()->row()
                           ;
                }else{                
                $company = $this->db->select('company.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('company','awarditem.company=company.id')
                           ->where('received.invoicenum',$invoice2->invoicenum)
                           ->get()->row()
                           ;
                }           
                           
                $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
                $invoice2->bankaccount = $bankaccount;

                $invoice2->companydetails = $company;
                $invoice2->totalprice = $invoice2->totalprice + ($invoice2->totalprice*$settings->taxpercent/100);
                //$invoice2->status = $invoice2->quote->status;
                if($invoice2->quote->potype=='Contract')
                $invoice2->actions = '<a href="javascript:void(0)" onclick="showContractInvoice(\'' . $invoice2->invoicenum . '\',\''.$invoice2->quote->id.'\')"><span class="icon-2x icon-search"></span></a>';
                else 
                $invoice2->actions = '<a href="javascript:void(0)" onclick="showInvoice(\'' . $invoice2->invoicenum . '\',\''.$invoice2->quote->id.'\')"><span class="icon-2x icon-search"></span></a>';
                
                $options = false;
                foreach ($available_statuses as $status_key => $status_text)
                {

                    if (strtolower($invoice2->status) == $status_text) {
                        $selected = " selected=\"selected\"";
                    } else {
                        $selected = '';
                    }
                    $options[] = "<option value=\"$status_text\" $selected>$status_text</option>";
                }
                $options_payment = array();
                $options_paymenttype = array();
                $options_payment[]="<option value=\"Paid\" ".($invoice2->paymentstatus=='Paid'?" selected=\"selected\"":'').">Paid</option>";;
                //$options_payment[]="<option value=\"Requested Payment\" ".($invoice2->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Requested Paid</option>";;
                $options_payment[]="<option value=\"Unpaid\" ".($invoice2->paymentstatus=='Unpaid'||$invoice2->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Unpaid</option>";;

                $options_paymenttype[]="<option value=\"\">Select Payment Type</option>";
                if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
                $options_paymenttype[]="<option value=\"Credit Card\" ".($invoice2->paymenttype=='Credit Card'?" selected=\"selected\"":'').">Credit Card</option>";;
                $options_paymenttype[]="<option value=\"Cash\" ".($invoice2->paymenttype=='Cash'?" selected=\"selected\"":'').">Cash</option>";;
                $options_paymenttype[]="<option value=\"Check\" ".($invoice2->paymenttype=='Check'?" selected=\"selected\"":'').">Check</option>";;

                $txtrefnum = "<input type=\"text\" id=\"refnum_$invoice2->invoicenum\" name=\"refnum\" value=\"$invoice2->refnum\"/>";

                $update_button = "<button onclick=\"update_invoice_status('$invoice2->invoicenum')\">update</button>";
                $update_payment_button = "<button onclick=\"update_invoice_payment_status('$invoice2->invoicenum')\">update</button>";

                $status_html = "<select id=\"invoice_$invoice2->invoicenum\" name=\"status_element\">" . implode("", $options) . "</select>" . $update_button;

                $payment_status_html = "<select id=\"invoice_payment_$invoice2->invoicenum\" name=\"payment_status_element\">" . implode("", $options_payment) . "</select>";
                $payment_status_html .= "<select id=\"invoice_paymenttype_$invoice2->invoicenum\" name=\"paymenttype_status_element\" onchange=\"paycc(this.value,'".$invoice2->invoicenum."','".$invoice2->totalprice."');\">" . implode("", $options_paymenttype) . "</select>";
                $payment_status_html .= $txtrefnum;
                $payment_status_html .= $update_payment_button;
                if($invoice2->paymentstatus=='Requested Payment')
                {
                	if($invoice2->quote->potype=='Contract')
               			$payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Company</i>';
               		else 
                    	$payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Supplier</i>';
                }

                $invoice->status_selectbox = $status_html;
                $invoice->payment_status_selectbox = $payment_status_html;

                $invoice->totalprice = number_format($invoice->totalprice,2);

                $items[] = $invoice;
            }

            $data['items'] = $items;
            $data['jsfile'] = 'invoicejs.php';
        } else {
        	$data['items'] = array();
            $data['message'] = 'No Records';
        }
        //print_r($items);die;
        $data ['addlink'] = '';
        $data ['heading'] = 'Invoices';
        $query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
        $data['companies'] = $this->db->query($query)->result();

        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}     
        
        
        $this->load->view('admin/contract_invoice', $data);
    }
    
    
    
    function requestpayment($quoteid = '',$award='')
	{
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
		$invoicenum = $_POST['invoicenum'];
		
		if(!$invoicenum)
		{
			$message = 'Invalid Link.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('admin/quote/invoices');
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
		$purchaser = $this->quote_model->getpurchaseuserbyid($company);
		$this->email->from($purchaser->email);
		if($pa){
		$this->email->to($pa->email);
		$companyadminname = $pa->companyname;
		}else{
			$companyadminname = "";			
		}
		$subject = 'Payment requested by supplier';
		$data['email_body_title'] = "";
		$data['email_body_content'] = "Dear {$companyadminname}, <br> <br> Supplier {$purchaser->companyname} has sent payment request for
		Invoice# {$invoicenum}
		for PO# {$quote->ponum} on ".date('m/d/Y').".  <br> <br> Thank You. <br> {$purchaser->companyname}";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		
		$this->email->subject($subject);
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->reply_to($purchaser->email);
		$this->email->send();
		
		$message = 'Payment Requested for the invoice# '.$_POST['invoicenum'];
		$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
		
		if($quoteid)
		{
		    redirect('admin/quote/trackpurchaser/'.$quoteid.'/'.$award);
		}
		else
		{
		    redirect('admin/quote/invoices');
		    //$this->invoice();
		}
	}


  function trackexport($qid)
    {
    	if ($this->session->userdata('usertype_id') == 3)
    		redirect('admin/purchasinguser/bids/' . $qid);
    	$awarded = $this->quote_model->getawardedbid($qid);
    	if (!$awarded)
    		redirect('admin/quote/bids/' . $qid);
    	if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('id')) {
    		redirect('admin/dashboard', 'refresh');
    	}

    	$query = "SELECT s.*, c.title companyname FROM ".$this->db->dbprefix('shippingdoc')." s,
					 ".$this->db->dbprefix('company')." c WHERE s.company=c.id AND s.quote='$qid' ORDER BY uploadon DESC";

    	$docs = $this->db->query($query)->result();
    	$data['shippingdocs'] = $docs;
    	$messagesql = "SELECT * FROM " . $this->db->dbprefix('message') . " WHERE quote='{$qid}' ORDER BY senton ASC";
    	$msgresult = $this->db->query($messagesql)->result();
    	$messages = array();
    	foreach ($msgresult as $msg) {
    		$messages[$msg->company]['companydetails'] = $this->company_model->get_companys_by_id($msg->company);
    		$messages[$msg->company]['messages'][] = $msg;
    	}

    	if($awarded->status == 'complete')
    	{
    		$this->db->where('quote',$qid);
    		$feedbacks = $this->db->get('quotefeedback')->result();

    		$data['feedbacks'] = array();
    		foreach($feedbacks as $feedback)
    		{
    			if($feedback->company)
    				$data['feedbacks'][$feedback->company] = $feedback;
    		}
    	}

    	$shipments = $this->db->select('shipment.*, item.itemname')
    	->from('shipment')->join('item','shipment.itemid=item.id','left')
    	->where('quote',$qid)->get()->result();

    	$data['errorLog'] = $this->quote_model->get_quotes_error_log($awarded->quote);
    	$data['quote'] = $this->quote_model->get_quotes_by_id($awarded->quote);
    	$data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
    	$data['config'] = (array) $this->settings_model->get_current_settings();
    	$data['messages'] = $messages;
    	$data['awarded'] = $awarded;
    	$data['shipments'] = $shipments;
    	$data['heading'] = "TRACK Items";

    	$data['adquoteid'] = $qid;


    	//$this->load->view('admin/track', $data);

    	//=========================================================================================
    	$quote = $data['quote'];

		$header[] = array('Report type' , 'Order Tracking', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	if($this->session->userdata('managedprojectdetails'))
		{
			$header[] = array('Project Title' , $this->session->userdata('managedprojectdetails')->title, '' , '' , '' , '' , '' , '' , '' , '' , '' );
		}

		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
		$header[] = array('PO #' , $quote->ponum, '' , '' , '' , '' , '' , '' , '' , '' , '' );
		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	$header[] = array('Company' , 'Item Code' , 'Item Name' , 'Qty.' , 'Unit' , 'Price EA' , 'Total Price' , 'Date Requested' , 'Cost Code' , 'Notes' , 'Still Due' );

    	//-------------------------------------------

    	$config   = $data['config'];
    	$project  = $data['project'];
    	$quote    = $data['quote'];
    	$errorLog = $data['errorLog'];
    	$shippingdocs = $data['shippingdocs'] ;

    	if(isset($data['feedbacks']))
    	{
    		$feedbacks    = $data['feedbacks'];
    	}


    	$combocompanies = array();
    	$messagecompanies = array();
    	$recsum = 0;
    	$qntsum = 0;
    	foreach ($awarded->items as $q) {
    		$recsum = $recsum + $q->received;
    		$qntsum = $qntsum + $q->quantity;
    		if ($q->received < $q->quantity) {
    			if (isset($combocompanies[$q->company])) {
    				$combocompanies[$q->company]['value'][] = $q->id;
    			} else {
    				$combocompanies[$q->company] = array();
    				$combocompanies[$q->company]['value'] = array($q->id);
    				$combocompanies[$q->company]['id'] = $q->company;
    				$combocompanies[$q->company]['label'] = $q->companyname;
    			}
    		}

    		if (isset($messagecompanies[$q->company])) {
    			$messagecompanies[$q->company]['value'][] = $q->id;
    		} else {
    			$messagecompanies[$q->company] = array();
    			$messagecompanies[$q->company]['value'] = array($q->id);
    			$messagecompanies[$q->company]['id'] = $q->company;
    			$messagecompanies[$q->company]['label'] = $q->companyname;
    		}
    	}

    	if ($qntsum) {
    		$per = number_format(($recsum / $qntsum) * 100, 2);
    	}else{
    		$per = 0;
    	}
    	$per .='%';


    	//-------------------------------------------

    	$alltotal = 0;
    	foreach ($awarded->items as $q)
    	{
    		$alltotal+=$q->totalprice;

    		$still_due = $q->quantity - $q->received;

			$green_icon_value = '';
			if($q->received != '0.00' && $q->received != '')
			{
               $green_icon_value = '( received:'.$q->received.')';
			}

			$header[] = array(@$q->companydetails->title , $q->itemcode , $q->itemname , $q->quantity.$green_icon_value , $q->unit, '$ '.formatPriceNew($q->ea) , '$ '.formatPriceNew($q->totalprice) , $q->daterequested , $q->costcode , $q->notes , $still_due );

    	}

    	$taxtotal = $alltotal * $config['taxpercent'] / 100;
    	$grandtotal = $alltotal + $taxtotal;


    	$header[] = array('Subtotal:' , '$ '.formatPriceNew(round($alltotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('Tax:' ,  '$ '.formatPriceNew(round($taxtotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '', ''  );
    	$header[] = array('Total:' , '$ '.formatPriceNew(round($grandtotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	$header[] = array('Received:' , $per.chr(160) , '' , '' , '' , '' , '' , '' , '' , '' , '' );


    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );



    	//---------shipments-----------------------------------------------

    	if (@$shipments)
    	{
    		$header[] = array('Shipments:' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    		$canacceptall = false;
    		$shipitemids = array();
    		foreach($shipments as $cs)
    		{
    			if($cs->accepted == 0)
    			{
    				$canacceptall = true;
    			}
    		}
    		foreach($shipments as $cs)
    		{
    			if(isset($shipitemids[$cs->awarditem]))
    			{
    				$canacceptall = false;
    				break;
    			}
    			$shipitemids[$cs->awarditem] = 1;
    		}

    		$header[] = array('Item' , 'Quantity', 'Reference #' , '' , '' , '' , '' , '' , '' , '' , '' );

    		foreach($shipments as $s)
    		{
    			$header[] = array($s->itemname , $s->quantity , $s->invoicenum , '' , '' , '' , '' , '' , '' , '' , '' );
    		}
    	}

    	//------------messages---------------------------------------


    	if (@$messages)
    	{
    		foreach ($messages as $c)
    		{
    			if (@$c['messages'])
    			{
    				$message_for = 'Messages for '. $c['companydetails']->title.' regarding PO# '. $quote->ponum;

    				$header[] = array($message_for , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    				$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    				$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );


    				$header[] = array('From' , 'To', 'Message' , 'Date/Time' , '' , '' , '' , '' , '' , '' , '' );

    				foreach ($c['messages'] as $msg)
    				{
    					$header[] = array($msg->from , $msg->to, $msg->message, date("m/d/Y h:i A", strtotime($msg->senton)) , '' , '' , '' , '' , '' , '' , '' );

    				}

    			}
    		}
    	}
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );


    	//------------shipmentdoc---------------------------------------

    	if($shippingdocs)
    	{
    		$header[] = array('Shipping Documents' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Company' , 'Date', 'Reference#' , '' , '' , '' , '' , '' , '' , '' , '' );

    		foreach($shippingdocs as $sd)
    		{
    			$header[] = array($sd->companyname, date("m/d/Y", strtotime($sd->uploadon)), $sd->invoicenum , '' , '' , '' , '' , '' , '' , '' , '' );
    		}
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	}


    	//-------------------invoices-----------------------------------------------------------------

    	if ($awarded->invoices)
    	{
    		$header[] = array('Existing Invoices ' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Invoice #' , 'Total Cost', 'Tax' , 'Payment' ,  'Status' , '' , '' , '' , '' , '','' );

    		foreach ($awarded->invoices as $invoice)
    		{
    			$header[] = array($invoice->invoicenum , '$ '.formatPriceNew($invoice->totalprice), number_format($invoice->totalprice * $config['taxpercent'] / 100, 2) , $invoice->paymentstatus ,   $invoice->status , '' , '' , '' , '' , '','' );
    		}
    	}



    	//--------------Time Line---------------------------------------------------------------



    	if ($awarded->invoices)
    	{
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Time Line ' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('PO #' , 'Date', '' , '' ,  '' , '' , '' , '' , '' , '','' );
    		$header[]  = array($quote->ponum , date('m/d/Y', strtotime($awarded->awardedon)) ,'' , '',  '' , '' , '' , '' , '' , '','' );
    		//-----------------------------------------

    		foreach ($awarded->invoices as $invoice)
    		{

    			$header[] = array($invoice->invoicenum , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    			foreach ($invoice->items as $item)
    			{
    				$header[] = array($item->receiveddate, $item->itemname, $item->quantity.' Received' , '' , '' , '' , '' , '' , '' , '' , '' );
    			}

    		}
    	}


    	//--------------------Feedbacks------------------------------------------------------------

    	if ($awarded->status == 'complete')
    	{
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Feedbacks' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Company' , 'Rating', 'Feedback' , '' ,  '' , '' , '' , '' , '' , '','' );

    		foreach($messagecompanies as $combocompany)
    		{
    			if(isset($feedbacks[$combocompany['id']]))
    				$rating = '<div class="fixedrating" data-average="'.$feedbacks[$combocompany['id']]->rating.'" data-id="1"></div>';
    			else
    				$rating = '';
    			$feedback = isset($feedbacks[$combocompany['id']]) ? $feedbacks[$combocompany['id']]->feedback : '';
    			$header[] = array($combocompany['label'] , $rating, $feedback , '' ,  '' , '' , '' , '' , '' , '','' );
    		}
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	}


    	//--------------------errorLog------------------------------------------------------------

    	if(!empty($errorLog))
    	{

    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('ERROR LOG' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('company' , 'Error', 'Item' , 'Qty' ,  'Invoice#' , 'Date' , '' , '' , '' , '','' );

    		foreach($errorLog as $error)
    		{
    			$inv_date = (isset($error->date) && $error->date!="" && $error->date!="0000-00-00" && $error->date!="1969-12-31")?date("m/d/Y",  strtotime($error->date)):"";
    			$header[] = array($error->title , $error->error, $error->itemcode , $error->quantity ,  $error->invoicenum , $inv_date , '' , '' , '' , '','' );
    		}

    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	}


    	//--------------------errorLog------------------------------------------------------------

    	createXls('track_items', $header);
    	die();

    	//===============================================================================


    }

	// TRACK PDF

 function trackpdf($qid)
    {
    	if ($this->session->userdata('usertype_id') == 3)
    		redirect('admin/purchasinguser/bids/' . $qid);
    	$awarded = $this->quote_model->getawardedbid($qid);
    	if (!$awarded)
    		redirect('admin/quote/bids/' . $qid);
    	if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('id')) {
    		redirect('admin/dashboard', 'refresh');
    	}

    	$query = "SELECT s.*, c.title companyname FROM ".$this->db->dbprefix('shippingdoc')." s,
					 ".$this->db->dbprefix('company')." c WHERE s.company=c.id AND s.quote='$qid' ORDER BY uploadon DESC";

    	$docs = $this->db->query($query)->result();
    	$data['shippingdocs'] = $docs;
    	$messagesql = "SELECT * FROM " . $this->db->dbprefix('message') . " WHERE quote='{$qid}' ORDER BY senton ASC";
    	$msgresult = $this->db->query($messagesql)->result();
    	$messages = array();
    	foreach ($msgresult as $msg) {
    		$messages[$msg->company]['companydetails'] = $this->company_model->get_companys_by_id($msg->company);
    		$messages[$msg->company]['messages'][] = $msg;
    	}

    	if($awarded->status == 'complete')
    	{
    		$this->db->where('quote',$qid);
    		$feedbacks = $this->db->get('quotefeedback')->result();

    		$data['feedbacks'] = array();
    		foreach($feedbacks as $feedback)
    		{
    			if($feedback->company)
    				$data['feedbacks'][$feedback->company] = $feedback;
    		}
    	}

    	$shipments = $this->db->select('shipment.*, item.itemname')
    	->from('shipment')->join('item','shipment.itemid=item.id','left')
    	->where('quote',$qid)->get()->result();

    	$data['errorLog'] = $this->quote_model->get_quotes_error_log($awarded->quote);
    	$data['quote'] = $this->quote_model->get_quotes_by_id($awarded->quote);
    	$data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
    	$data['config'] = (array) $this->settings_model->get_current_settings();
    	$data['messages'] = $messages;
    	$data['awarded'] = $awarded;
    	$data['shipments'] = $shipments;
    	$data['heading'] = "TRACK Items";

    	$data['adquoteid'] = $qid;


    	//$this->load->view('admin/track', $data);

    	//=========================================================================================
    	$quote = $data['quote'];

		$header[] = array('Report type:' , 'Order Tracking', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	if($this->session->userdata('managedprojectdetails'))
		{
			$header[] = array('<b>Project Title</b>' , $this->session->userdata('managedprojectdetails')->title, '' , '' , '' , '' , '' , '' , '' , '' , '' );
		}

		//$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
		$header[] = array('<b>PO #</b>' , $quote->ponum, '' , '' , '' , '' , '' , '' , '' , '' , '' );
		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	$header[] = array('<b>Company</b>' , '<b>Item Code</b>' , '<b>Item Name</b>' , '<b>Qty.</b>' , '<b>Unit</b>' , '<b>Price EA</b>' , '<b>Total Price</b>' , '<b>Date Requested</b>' , '<b>Cost Code</b>' , '<b>Notes</b>' , '<b>Still Due</b>' );

    	//-------------------------------------------

    	$config   = $data['config'];
    	$project  = $data['project'];
    	$quote    = $data['quote'];
    	$errorLog = $data['errorLog'];
    	$shippingdocs = $data['shippingdocs'] ;

    	if(isset($data['feedbacks']))
    	{
    		$feedbacks    = $data['feedbacks'];
    	}


    	$combocompanies = array();
    	$messagecompanies = array();
    	$recsum = 0;
    	$qntsum = 0;
    	foreach ($awarded->items as $q) {
    		$recsum = $recsum + $q->received;
    		$qntsum = $qntsum + $q->quantity;
    		if ($q->received < $q->quantity) {
    			if (isset($combocompanies[$q->company])) {
    				$combocompanies[$q->company]['value'][] = $q->id;
    			} else {
    				$combocompanies[$q->company] = array();
    				$combocompanies[$q->company]['value'] = array($q->id);
    				$combocompanies[$q->company]['id'] = $q->company;
    				$combocompanies[$q->company]['label'] = $q->companyname;
    			}
    		}

    		if (isset($messagecompanies[$q->company])) {
    			$messagecompanies[$q->company]['value'][] = $q->id;
    		} else {
    			$messagecompanies[$q->company] = array();
    			$messagecompanies[$q->company]['value'] = array($q->id);
    			$messagecompanies[$q->company]['id'] = $q->company;
    			$messagecompanies[$q->company]['label'] = $q->companyname;
    		}
    	}

    	if ($qntsum) {
    		$per = number_format(($recsum / $qntsum) * 100, 2);
    	}else{
    		$per = 0;
    	}
    	$per .='%';


    	//-------------------------------------------

    	$alltotal = 0;
    	foreach ($awarded->items as $q)
    	{
    		$alltotal+=$q->totalprice;

    		$still_due = $q->quantity - $q->received;

			$green_icon_value = '';
			if($q->received != '0.00' && $q->received != '')
			{
               $green_icon_value = '( received:'.$q->received.')';
			}

			$header[] = array(@$q->companydetails->title , $q->itemcode , $q->itemname , $q->quantity.$green_icon_value , $q->unit, '$ '.formatPriceNew($q->ea) , '$ '.formatPriceNew($q->totalprice) , $q->daterequested , $q->costcode , $q->notes , $still_due );

    	}

    	$taxtotal = $alltotal * $config['taxpercent'] / 100;
    	$grandtotal = $alltotal + $taxtotal;


    	$header[] = array('<b>Subtotal:</b>' , '$ '.formatPriceNew(round($alltotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('<b>Tax:</b>' ,  '$ '.formatPriceNew(round($taxtotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '', ''  );
    	$header[] = array('<b>Total:</b>' , '$ '.formatPriceNew(round($grandtotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	$header[] = array('<b>Received:</b>' , $per.chr(160) , '' , '' , '' , '' , '' , '' , '' , '' , '' );


    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );



    	//---------shipments-----------------------------------------------

    	if (@$shipments)
    	{
    		$header[] = array('<b>Shipments:</b>' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    		$canacceptall = false;
    		$shipitemids = array();
    		foreach($shipments as $cs)
    		{
    			if($cs->accepted == 0)
    			{
    				$canacceptall = true;
    			}
    		}
    		foreach($shipments as $cs)
    		{
    			if(isset($shipitemids[$cs->awarditem]))
    			{
    				$canacceptall = false;
    				break;
    			}
    			$shipitemids[$cs->awarditem] = 1;
    		}

    		$header[] = array('<b>Item</b>' , '<b>Quantity</b>', '<b>Reference #</b>' , '' , '' , '' , '' , '' , '' , '' , '' );

    		foreach($shipments as $s)
    		{
    			$header[] = array($s->itemname , $s->quantity , $s->invoicenum , '' , '' , '' , '' , '' , '' , '' , '' );
    		}
    	}

    	//------------messages---------------------------------------


    	if (@$messages)
    	{
    		foreach ($messages as $c)
    		{
    			if (@$c['messages'])
    			{
    				$message_for = 'Messages for <b>'. $c['companydetails']->title.'</b> regarding PO# <b>'. $quote->ponum.'</b>';

    				$header[] = array($message_for , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    				$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    				$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );


    				$header[] = array('<b>From</b>' , '<b>To</b>', '<b>Message</b>' , '<b>Date/Time</b>' , '' , '' , '' , '' , '' , '' , '' );

    				foreach ($c['messages'] as $msg)
    				{
    					$header[] = array($msg->from , $msg->to, $msg->message, date("m/d/Y h:i A", strtotime($msg->senton)) , '' , '' , '' , '' , '' , '' , '' );

    				}

    			}
    		}
    	}
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );


    	//------------shipmentdoc---------------------------------------

    	if($shippingdocs)
    	{
    		$header[] = array('<b>Shipping Documents</b>' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('<b>Company</b>' , '<b>Date</b>', '<b>Reference#</b>' , '' , '' , '' , '' , '' , '' , '' , '' );

    		foreach($shippingdocs as $sd)
    		{
    			$header[] = array($sd->companyname, date("m/d/Y", strtotime($sd->uploadon)), $sd->invoicenum , '' , '' , '' , '' , '' , '' , '' , '' );
    		}
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	}


    	//-------------------invoices-----------------------------------------------------------------

    	if ($awarded->invoices)
    	{
    		$header[] = array('<b>Existing Invoices </b>' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('<b>Invoice #</b>' , '<b>Total Cost</b>', '<b>Tax</b>' , '<b>Payment</b>' ,  '<b>Status</b>' , '' , '' , '' , '' , '','' );

    		foreach ($awarded->invoices as $invoice)
    		{
    			$header[] = array($invoice->invoicenum , '$ '.formatPriceNew($invoice->totalprice), number_format($invoice->totalprice * $config['taxpercent'] / 100, 2) , $invoice->paymentstatus ,   $invoice->status , '' , '' , '' , '' , '','' );
    		}
    	}



    	//--------------Time Line---------------------------------------------------------------



    	if ($awarded->invoices)
    	{
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('<b>Time Line</b> ' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('<b>PO #</b>' , '<b>Date</b>', '' , '' ,  '' , '' , '' , '' , '' , '','' );
    		$header[]  = array($quote->ponum , date('m/d/Y', strtotime($awarded->awardedon)) ,'' , '',  '' , '' , '' , '' , '' , '','' );
    		//-----------------------------------------

    		foreach ($awarded->invoices as $invoice)
    		{

    			$header[] = array($invoice->invoicenum , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    			foreach ($invoice->items as $item)
    			{
    				$header[] = array($item->receiveddate, $item->itemname, $item->quantity.' Received' , '' , '' , '' , '' , '' , '' , '' , '' );
    			}

    		}
    	}


    	//--------------------Feedbacks------------------------------------------------------------

    	if ($awarded->status == 'complete')
    	{
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('<b>Feedbacks</b>' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('<b>Company</b>' , '<b>Rating</b>', '<b>Feedback</b>' , '' ,  '' , '' , '' , '' , '' , '','' );

    		foreach($messagecompanies as $combocompany)
    		{
    			if(isset($feedbacks[$combocompany['id']]))
    				$rating = '<div class="fixedrating" data-average="'.$feedbacks[$combocompany['id']]->rating.'" data-id="1"></div>';
    			else
    				$rating = '';
    			$feedback = isset($feedbacks[$combocompany['id']]) ? $feedbacks[$combocompany['id']]->feedback : '';
    			$header[] = array($combocompany['label'] , $rating, $feedback , '' ,  '' , '' , '' , '' , '' , '','' );
    		}
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	}


    	//--------------------errorLog------------------------------------------------------------

    	if(!empty($errorLog))
    	{

    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('<b>ERROR LOG</b>' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('<b>company</b>' , '<b>Error</b>', '<b>Item</b>' , '<b>Qty</b>' ,  '<b>Invoice#</b>' , '<b>Date</b>' , '' , '' , '' , '','' );

    		foreach($errorLog as $error)
    		{
    			$inv_date = (isset($error->date) && $error->date!="" && $error->date!="0000-00-00" && $error->date!="1969-12-31")?date("m/d/Y",  strtotime($error->date)):"";
    			$header[] = array($error->title , $error->error, $error->itemcode , $error->quantity ,  $error->invoicenum , $inv_date , '' , '' , '' , '','' );
    		}

    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	}


    	//--------------------errorLog------------------------------------------------------------


			$headername = "TRACK ITEMS";
    	createOtherPDF('track_items', $header,$headername);
    	die();

    	//===============================================================================


    }





    function track($qid)
    {
        if ($this->session->userdata('usertype_id') == 3)
            redirect('admin/purchasinguser/bids/' . $qid);
        $awarded = $this->quote_model->getawardedbid($qid);
        if (!$awarded)
            redirect('admin/quote/bids/' . $qid);
        if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre>';print_r($awarded);die;
        /*
        $this->db->select('shippingdoc.*, company.title');
        $this->db->from('shippingdoc')->join('company','shippingdoc.company=company.id');
		$this->db->where('quote',$qid);
		$this->db->order_by('uploadon','DESC');
		*/
		$query = "SELECT s.*, c.title companyname FROM ".$this->db->dbprefix('shippingdoc')." s,
				 ".$this->db->dbprefix('company')." c WHERE s.company=c.id AND s.quote='$qid' ORDER BY uploadon DESC";
	    //echo $query;
		$docs = $this->db->query($query)->result();
		$data['shippingdocs'] = $docs;
		//echo '<pre>';print_r($docs);die;

        $messagesql = "SELECT * FROM " . $this->db->dbprefix('message') . " WHERE quote='{$qid}' ORDER BY senton ASC";
        $msgresult = $this->db->query($messagesql)->result();
        $messages = array();
        foreach ($msgresult as $msg) {
            $messages[$msg->company]['companydetails'] = $this->company_model->get_companys_by_id($msg->company);
            $messages[$msg->company]['messages'][] = $msg;
        }

		if($awarded->status == 'complete')
		{
		    $this->db->where('quote',$qid);
		    $feedbacks = $this->db->get('quotefeedback')->result();

		    $data['feedbacks'] = array();
		    foreach($feedbacks as $feedback)
		    {
		        if($feedback->company)
		            $data['feedbacks'][$feedback->company] = $feedback;
		    }
		}

		/*$shipments = $this->db->select('shipment.*, item.itemname')
		             ->from('shipment')->join('item','shipment.itemid=item.id','left')
		             ->where('quote',$qid)->get()->result();*/
		             
		             $shipments = $this->db->select('shipment.*, item.itemname,awarditem.itemname as iii,item_img')
		             ->from('shipment')->join('item','shipment.itemid=item.id','left')->join('awarditem','awarditem.itemid=shipment.itemid','left')
		             ->where('quote',$qid)->where('accepted',0)->group_by("shipment.itemid")->get()->result();

		$shipmentsquery = "SELECT sum(s.quantity) as quantity, GROUP_CONCAT(s.invoicenum) as invoicenum, s.awarditem, i.itemname FROM " . $this->db->dbprefix('shipment') . " s, ".$this->db->dbprefix('item')." i WHERE s.itemid=i.id and quote='{$qid}' and s.accepted = 0 GROUP BY s.company";
        $shipments2 = $this->db->query($shipmentsquery)->result();

		if($awarded){
			foreach($awarded->items as $item) {
				
				$item->shipreceiveddate = "";
				
				$awarditemid = $this->db->select('awarditem.id')
			                        ->from('awarditem')
			                        ->where('award',$item->award)->where('company',$item->company)
			                        ->where('itemid',$item->itemid)
			                        ->get()->row();		
				
				if($awarditemid){
					
					$shipreceiveddateresult = $this->db->select('receiveddate')
			                        ->from('received')
			                        ->where('awarditem',$awarditemid->id)
			                        ->get()->row();	
			         
					if($shipreceiveddateresult){
						$item->shipreceiveddate = $shipreceiveddateresult->receiveddate;
					}
			                        
			                        
				}
				
				$itemshipmentdate = $this->db->select('shipment.shipdate')
		             ->from('shipment')->join('item','shipment.itemid=item.id','left')
		             ->where('quote',$qid)->where('shipment.itemid',$item->itemid)->get()->result();
				if(@$itemshipmentdate[0]->shipdate)
				$item->datereceived = $itemshipmentdate[0]->shipdate;
				else 
				$item->datereceived = "";
				
				if($item->company){
				$item->etalog = $this->db->where('company',$item->company)
				->where('quote',$qid)
				->where('itemid',$item->itemid)
				->get('etalog')->result();

				$item->pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$qid)->where('company',$item->company)
			                        ->where('itemid',$item->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;				
				}

				$item->quotedaterequested = $this->db->select('daterequested')
				->where('purchasingadmin',$item->purchasingadmin)
				->where('quote',$qid)
				->where('itemid',$item->itemid)
				->get('quoteitem')->row();
				
			}
		}

        $data['errorLog'] = $this->quote_model->get_quotes_error_log($awarded->quote);
        $data['quote'] = $this->quote_model->get_quotes_by_id($awarded->quote);
        $data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $data['messages'] = $messages;
        $data['awarded'] = $awarded;
        $data['shipments'] = $shipments;
        $data['shipments2'] = $shipments2;
        $data['heading'] = "TRACK Items";
        $data['adquoteid'] = $qid;
        $dbills = $this->db->select('sum(totalprice) as total, bill.*')
		             ->from('bill')->join('billitem','bill.id=billitem.bill','left')
		             ->where('bill.quote',$qid)->group_by('billitem.bill')->get()->result();
		
		$serviceItems = 0;
		$totPrice = 0;
		             
		foreach($dbills as $dbill){   
			$serviceItems = 0;
                $serviceitemRes = $this->db->where('billid',$dbill->id)->get('bill_servicelaboritems')->result_array();
              
                if(@$serviceitemRes)
                {
                	foreach ($serviceitemRes as $k=>$v)
                	{   
                		($v['quantity'] == '' || $v['quantity'] == 0) ? $qty = 1 : $qty =  $v['quantity'];           
                		$totPrice = $v['price'] * $qty;      	                		
                		$serviceItems += $totPrice + ($totPrice * $v['tax']/100);                   		           	
                	}	          	
                } 
                $dbill->serviceItems = $serviceItems;          
			
           $resamountpaid = $this->db->select('sum(amountpaid) as amountpaid')
		             ->from('bill')->join('pms_bill_payment_history','bill.id=pms_bill_payment_history.bill','left')
		             ->where('bill.id',$dbill->id)->group_by('pms_bill_payment_history.bill')->get()->row();      
			if($resamountpaid)             
		     	$dbill->amountpaid =  $resamountpaid->amountpaid;             
		    else 
		    	$dbill->amountpaid = 0; 
		}
				
		$data['bills'] = $dbills;
		
		$billeditems = $this->db->select('billitem.*')
		             ->from('bill')->join('billitem','bill.id=billitem.bill')
		             ->where('bill.quote',$qid)->get()->result();		             
		
		$billitemdata = array();
		if($billeditems){
		foreach($billeditems as $itemdata){
			$billitemdata[$itemdata->company][$itemdata->award][] = $itemdata->itemid;
		}
		}

		$data['billitemdata'] = $billitemdata;
		$data['servicebillitems'] = $this->db->where(array('isdeleted'=>0,'purchasingadmin' => $this->session->userdata('purchasingadmin')))->get('servicelaboritems')->result();
		$data['customerdata'] = $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'))				
				->get('customer')->result();             
		             
		//echo "<pre>",print_r($data['bills']); die;            
        $this->load->view('admin/track', $data);
    }
    
    
    
    function bill($invid='',$quotid='')
    {
       if($invid=='')
    	{
        $invoicenum = @$_POST['billid'];
    	}
    	else 
    	{
    	$invoicenum = $invid;	
    	}
    	
    	if($quotid=='')
    	{
        $invoicequote = $_POST['billquote'];
    	}
    	else 
    	{
    	$invoicequote =$quotid;	
    	}	
    	
    	
        if (!$invoicenum)
            redirect('quote/billings');
        $invoice = $this->quote_model->getinvoicebybillnum($invoicenum,$invoicequote);
        //echo "<pre>",print_r($invoice); die;
        $awarded = $this->quote_model->getawardedbid($invoice->quote);
        //print_r($invoice); echo $this->session->userdata('purchasingadmin');die;
        /*if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('purchasingadmin')) {
            redirect('admin/dashboard', 'refresh');
        }*/
        //echo '<pre>';print_r($invoice);die;

        $this->db->where('id', $this->session->userdata('purchasingadmin'));
        $pa = $this->db->get('users')->row();

        $quote = $awarded->quotedetails;
        $project = $this->project_model->get_projects_by_id($quote->pid);
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);

        /*$company = $this->db->from('received')
                    ->join('awarditem','received.awarditem=awarditem.id')
                    ->join('company','company.id=awarditem.company')
                    ->get()->row();*/

        $data['quote'] = $quote;
        $data['awarded'] = $awarded;
        $data['config'] = $config;
        $data['project'] = $project;
        $data['invoice'] = $invoice;
        //$data['company'] = $company;
        $data['heading'] = "Bill Details";
        $data['purchasingadmin'] = $pa;
        
       /* $invoices = $this->quote_model->getinvoicesforpayment($invoicenum);
        $invoice = $invoices[0];*/
        //echo "<pre>",print_r($invoice); die;
        
        
        /*$items = array();
        if($invoice){
        $settings = $this->settings_model->get_current_settings();
        $available_statuses = array('pending', 'verified', 'error');
        $data['available_statuses'] = $available_statuses;        
            if($invoice->invoicenum && $invoice->quote->purchasingadmin == $this->session->userdata('purchasingadmin') )
            {
                $invoice->ponum = $invoice->quote->ponum;

                if($invoice->quote->potype=='Contract'){
                $company = $this->db->select('users.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('users','awarditem.company=users.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;
                }else{                
                $company = $this->db->select('company.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('company','awarditem.company=company.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;
                }           
                           
                $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
                $invoice->bankaccount = $bankaccount;

                $invoice->companydetails = $company;
                $invoice->totalprice = $invoice->totalprice + ($invoice->totalprice*$settings->taxpercent/100);
                //$invoice->status = $invoice->quote->status;
                if($invoice->quote->potype=='Contract')
                $invoice->actions = '<a href="javascript:void(0)" onclick="showContractInvoice(\'' . $invoice->invoicenum . '\',\''.$invoice->quote->id.'\')"><span class="icon-2x icon-search"></span></a>';
                else 
                $invoice->actions = '<a href="javascript:void(0)" onclick="showInvoice(\'' . $invoice->invoicenum . '\',\''.$invoice->quote->id.'\')"><span class="icon-2x icon-search"></span></a>';
                
                $options = false;
                foreach ($available_statuses as $status_key => $status_text)
                {

                    if (strtolower($invoice->status) == $status_text) {
                        $selected = " selected=\"selected\"";
                    } else {
                        $selected = '';
                    }
                    $options[] = "<option value=\"$status_text\" $selected>$status_text</option>";
                }
                $options_payment = array();
                $options_paymenttype = array();
                $options_payment[]="<option value=\"Paid\" ".($invoice->paymentstatus=='Paid'?" selected=\"selected\"":'').">Paid</option>";;
                //$options_payment[]="<option value=\"Requested Payment\" ".($invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Requested Paid</option>";;
                $options_payment[]="<option value=\"Unpaid\" ".($invoice->paymentstatus=='Unpaid'||$invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Unpaid</option>";;

                $options_paymenttype[]="<option value=\"\">Select Payment Type</option>";
                if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
                $options_paymenttype[]="<option value=\"Credit Card\" ".($invoice->paymenttype=='Credit Card'?" selected=\"selected\"":'').">Credit Card</option>";;
                $options_paymenttype[]="<option value=\"Cash\" ".($invoice->paymenttype=='Cash'?" selected=\"selected\"":'').">Cash</option>";;
                $options_paymenttype[]="<option value=\"Check\" ".($invoice->paymenttype=='Check'?" selected=\"selected\"":'').">Check</option>";;

                $txtrefnum = "<input type=\"text\" id=\"refnum_$invoice->invoicenum\" name=\"refnum\" value=\"$invoice->refnum\"/>";

                $update_button = "<button onclick=\"update_invoice_status('$invoice->invoicenum')\">update</button>";
                $update_payment_button = "<button onclick=\"update_invoice_payment_status('$invoice->invoicenum')\">update</button>";

                $status_html = "<select id=\"invoice_$invoice->invoicenum\" name=\"status_element\">" . implode("", $options) . "</select>" . $update_button;

                $payment_status_html = "<select id=\"invoice_payment_$invoice->invoicenum\" name=\"payment_status_element\">" . implode("", $options_payment) . "</select>";
                $payment_status_html .= "<select id=\"invoice_paymenttype_$invoice->invoicenum\" name=\"paymenttype_status_element\" onchange=\"paycc(this.value,'".$invoice->invoicenum."','".$invoice->totalprice."');\">" . implode("", $options_paymenttype) . "</select>";
                $payment_status_html .= $txtrefnum;
                $payment_status_html .= $update_payment_button;
                if($invoice->paymentstatus=='Requested Payment')
                {
                	if($invoice->quote->potype=='Contract')
               			$payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Company</i>';
               		else 
                    	$payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Supplier</i>';
                }

                $invoice->status_selectbox = $status_html;
                $invoice->payment_status_selectbox = $payment_status_html;

                $invoice->totalprice = number_format($invoice->totalprice,2);

                $items[] = $invoice;
            }

            $data['items'] = $items;
            $data['jsfile'] = 'invoicejs.php';
        } else {
        	$data['items'] = array();
            $data['message'] = 'No Records';
        } */
        //print_r($items);die;
        $data ['addlink'] = '';
        $data ['heading'] = 'Bills';
        $query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
        $data['companies'] = $this->db->query($query)->result();

        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}     
        
		$sql3 = "SELECT bs.* FROM ".$this->db->dbprefix('bill'). " b 
        			JOIN ".$this->db->dbprefix('bill_servicelaboritems'). " bs ON bs.billid = b.id 
        			WHERE b.id = ".$invoicenum;
       	$data['billservicedetails'] = $this->db->query($sql3)->result_array();        	
		
        $this->load->view('admin/bill', $data);
    }
    
    
    
    function contracttrack($qid)
    {
    	
    	$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
			
        if ($this->session->userdata('usertype_id') == 3)
            redirect('admin/purchasinguser/bids/' . $qid);
        $awarded = $this->quote_model->getawardedcontractbid($qid);
        // echo "<pre>",print_r($awarded); die;
        if (!$awarded)
            redirect('admin/quote/conbids/' . $qid);
        /*if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }*/
        //echo '<pre>';print_r($awarded);die;
        /*
        $this->db->select('shippingdoc.*, company.title');
        $this->db->from('shippingdoc')->join('company','shippingdoc.company=company.id');
		$this->db->where('quote',$qid);
		$this->db->order_by('uploadon','DESC');
		*/
		$query = "SELECT s.*, u.companyname companyname FROM ".$this->db->dbprefix('shippingdoc')." s,
				 ".$this->db->dbprefix('users')." u WHERE s.company=u.id AND s.quote='$qid' ORDER BY uploadon DESC";
	    //echo $query;
		$docs = $this->db->query($query)->result();
		$data['shippingdocs'] = $docs;
		//echo '<pre>';print_r($docs);die;

        $messagesql = "SELECT * FROM " . $this->db->dbprefix('message') . " WHERE quote='{$qid}' ORDER BY senton ASC";
        $msgresult = $this->db->query($messagesql)->result();
        $messages = array();
        foreach ($msgresult as $msg) {
            $messages[$msg->company]['companydetails'] = $this->company_model->get_purchasecompanys_by_id($msg->company);
            $messages[$msg->company]['messages'][] = $msg;
        }

		if($awarded->status == 'complete')
		{
		    $this->db->where('quote',$qid);
		    $feedbacks = $this->db->get('quotefeedback')->result();

		    $data['feedbacks'] = array();
		    foreach($feedbacks as $feedback)
		    {
		        if($feedback->company)
		            $data['feedbacks'][$feedback->company] = $feedback;
		    }
		}

		$shipments = $this->db->select('shipment.*, quoteitem.itemname')
		             ->from('shipment')->join('quoteitem','shipment.quote=quoteitem.quote')
		             ->where('shipment.quote',$qid)
		             ->group_by('shipment.id')
		             ->get()->result();

		$shipmentsquery = "SELECT sum(s.quantity) as quantity, GROUP_CONCAT(s.invoicenum) as invoicenum, s.awarditem, i.itemname FROM " . $this->db->dbprefix('shipment') . " s, ".$this->db->dbprefix('quoteitem')." i WHERE s.itemid=-i.id and s.quote='{$qid}' and s.accepted = 0 GROUP BY s.company";
        $shipments2 = $this->db->query($shipmentsquery)->result();

		if($awarded){
			foreach($awarded->items as $item) {
				if($item->company){
				$item->etalog = $this->db->where('company',$item->company)
				->where('quote',$qid)
				->where('itemid',$item->itemid)
				->get('etalog')->result();

				}

				$item->quotedaterequested = $this->db->select('daterequested')
				->where('purchasingadmin',$item->purchasingadmin)
				->where('quote',$qid)
				->where('itemid',$item->itemid)
				->get('quoteitem')->row();

			}
		}

        $data['errorLog'] = $this->quote_model->get_quotes_error_log($awarded->quote);
        $data['quote'] = $this->quote_model->get_quotes_by_id($awarded->quote);
        $data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $data['messages'] = $messages;
        $data['awarded'] = $awarded;
        $data['shipments'] = $shipments;
        $data['shipments2'] = $shipments2;
        $data['heading'] = "TRACK Items";
        $data['adquoteid'] = $qid;
        $this->load->view('admin/contracttrack', $data);
    }

    
    
    
  function contracttrackexport($qid)
    {
    	$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
			
    	/*if ($this->session->userdata('usertype_id') == 3)
    		redirect('admin/purchasinguser/bids/' . $qid);*/
    	$awarded = $this->quote_model->getawardedcontractbid($qid);
    	if (!$awarded)
    		redirect('admin/quote/bids/' . $qid);
    	/*if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('id')) {
    		redirect('admin/dashboard', 'refresh');
    	}*/

    	$query = "SELECT u.companyname companyname FROM ".$this->db->dbprefix('users')." u";

    	$docs = $this->db->query($query)->result();
    	$data['shippingdocs'] = $docs;
    	$messagesql = "SELECT * FROM " . $this->db->dbprefix('message') . " WHERE quote='{$qid}' ORDER BY senton ASC";
    	$msgresult = $this->db->query($messagesql)->result();
    	$messages = array();
    	foreach ($msgresult as $msg) {
    		$messages[$msg->company]['companydetails'] = $this->company_model->get_purchasecompanys_by_id($msg->company);
    		$messages[$msg->company]['messages'][] = $msg;
    	}

    	if($awarded->status == 'complete')
    	{
    		$this->db->where('quote',$qid);
    		$feedbacks = $this->db->get('quotefeedback')->result();

    		$data['feedbacks'] = array();
    		foreach($feedbacks as $feedback)
    		{
    			if($feedback->company)
    				$data['feedbacks'][$feedback->company] = $feedback;
    		}
    	}

    	$shipments = $this->db->select('shipment.*, quoteitem.itemname')
		             ->from('shipment')->join('quoteitem','shipment.itemid=quoteitem.id')
		             ->where('shipment.quote',$qid)->where('shipment.company',$company)
		             ->get()->result();

    	$data['errorLog'] = $this->quote_model->get_quotes_error_log($awarded->quote);
    	$data['quote'] = $this->quote_model->get_quotes_by_id($awarded->quote);
    	$data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
    	$data['config'] = (array) $this->settings_model->get_current_settings();
    	$data['messages'] = $messages;
    	$data['awarded'] = $awarded;
    	$data['shipments'] = $shipments;
    	$data['heading'] = "TRACK Items";

    	$data['adquoteid'] = $qid;


    	//$this->load->view('admin/track', $data);

    	//=========================================================================================
    	$quote = $data['quote'];

		$header[] = array('Report type' , 'Order Tracking', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	if($this->session->userdata('managedprojectdetails'))
		{
			$header[] = array('Project Title' , $this->session->userdata('managedprojectdetails')->title, '' , '' , '' , '' , '' , '' , '' , '' , '' );
		}

		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
		$header[] = array('PO #' , $quote->ponum, '' , '' , '' , '' , '' , '' , '' , '' , '' );
		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	$header[] = array('Company' , 'Item Name' , 'Price EA' , 'Total Price' , 'Date Requested' , 'Cost Code' , 'Notes' , 'Still Due' );

    	//-------------------------------------------

    	$config   = $data['config'];
    	$project  = $data['project'];
    	$quote    = $data['quote'];
    	$errorLog = $data['errorLog'];
    	$shippingdocs = $data['shippingdocs'] ;

    	if(isset($data['feedbacks']))
    	{
    		$feedbacks    = $data['feedbacks'];
    	}


    	$combocompanies = array();
    	$messagecompanies = array();
    	$recsum = 0;
    	$qntsum = 0;
    	foreach ($awarded->items as $q) {
    		$recsum = $recsum + $q->received;
    		$qntsum = $qntsum + $q->quantity;
    		if ($q->received < $q->quantity) {
    			if (isset($combocompanies[$q->company])) {
    				$combocompanies[$q->company]['value'][] = $q->id;
    			} else {
    				$combocompanies[$q->company] = array();
    				$combocompanies[$q->company]['value'] = array($q->id);
    				$combocompanies[$q->company]['id'] = $q->company;
    				$combocompanies[$q->company]['label'] = $q->companyname;
    			}
    		}

    		if (isset($messagecompanies[$q->company])) {
    			$messagecompanies[$q->company]['value'][] = $q->id;
    		} else {
    			$messagecompanies[$q->company] = array();
    			$messagecompanies[$q->company]['value'] = array($q->id);
    			$messagecompanies[$q->company]['id'] = $q->company;
    			$messagecompanies[$q->company]['label'] = $q->companyname;
    		}
    	}

    	if ($qntsum) {
    		$per = number_format(($recsum / $qntsum) * 100, 2);
    	}else{
    		$per = 0;
    	}
    	$per .='%';


    	//-------------------------------------------

    	$alltotal = 0;
    	foreach ($awarded->items as $q)
    	{
    		$alltotal+=$q->totalprice;

    		$still_due = $q->quantity - $q->received;

			$green_icon_value = '';
			if($q->received != '0.00' && $q->received != '')
			{
               $green_icon_value = '( received:'.$q->received.')';
			}

			$header[] = array(@$q->companydetails->companyname , $q->itemname , '$ '.formatPriceNew($q->ea) , '$ '.formatPriceNew($q->totalprice) , $q->daterequested , $q->costcode , $q->notes , $still_due );

    	}

    	$taxtotal = $alltotal * $config['taxpercent'] / 100;
    	$grandtotal = $alltotal + $taxtotal;


    	$header[] = array('Subtotal:' , '$ '.formatPriceNew(round($alltotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('Tax:' ,  '$ '.formatPriceNew(round($taxtotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '', ''  );
    	$header[] = array('Total:' , '$ '.formatPriceNew(round($grandtotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	$header[] = array('Received:' , $per.chr(160) , '' , '' , '' , '' , '' , '' , '' , '' , '' );


    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );



    	//---------shipments-----------------------------------------------

    	if (@$shipments)
    	{
    		$header[] = array('Shipments:' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    		$canacceptall = false;
    		$shipitemids = array();
    		foreach($shipments as $cs)
    		{
    			if($cs->accepted == 0)
    			{
    				$canacceptall = true;
    			}
    		}
    		foreach($shipments as $cs)
    		{
    			if(isset($shipitemids[$cs->awarditem]))
    			{
    				$canacceptall = false;
    				break;
    			}
    			$shipitemids[$cs->awarditem] = 1;
    		}

    		$header[] = array('Item' , 'Quantity', 'Reference #' , '' , '' , '' , '' , '' , '' , '' , '' );

    		foreach($shipments as $s)
    		{
    			$header[] = array($s->itemname , $s->quantity , $s->invoicenum , '' , '' , '' , '' , '' , '' , '' , '' );
    		}
    	}

    	//------------messages---------------------------------------


    	if (@$messages)
    	{
    		foreach ($messages as $c)
    		{
    			if (@$c['messages'])
    			{
    				$message_for = 'Messages for '. $c['companydetails']->companyname.' regarding PO# '. $quote->ponum;

    				$header[] = array($message_for , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    				$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    				$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );


    				$header[] = array('From' , 'To', 'Message' , 'Date/Time' , '' , '' , '' , '' , '' , '' , '' );

    				foreach ($c['messages'] as $msg)
    				{
    					$header[] = array($msg->from , $msg->to, $msg->message, date("m/d/Y h:i A", strtotime($msg->senton)) , '' , '' , '' , '' , '' , '' , '' );

    				}

    			}
    		}
    	}
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );


    	//------------shipmentdoc---------------------------------------

    	if($shippingdocs)
    	{
    		$header[] = array('Shipping Documents' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Company' , 'Date', 'Reference#' , '' , '' , '' , '' , '' , '' , '' , '' );

    		foreach($shippingdocs as $sd)
    		{
    			if(@$sd->uploadon && @$sd->invoicenum)
    			$header[] = array($sd->companyname, date("m/d/Y", strtotime($sd->uploadon)), $sd->invoicenum , '' , '' , '' , '' , '' , '' , '' , '' );
    		}
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	}


    	//-------------------invoices-----------------------------------------------------------------

    	if ($awarded->invoices)
    	{
    		$header[] = array('Existing Invoices ' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Invoice #' , 'Total Cost', 'Tax' , 'Payment' ,  'Status' , '' , '' , '' , '' , '','' );

    		foreach ($awarded->invoices as $invoice)
    		{
    			$header[] = array($invoice->invoicenum , '$ '.formatPriceNew($invoice->totalprice), number_format($invoice->totalprice * $config['taxpercent'] / 100, 2) , $invoice->paymentstatus ,   $invoice->status , '' , '' , '' , '' , '','' );
    		}
    	}



    	//--------------Time Line---------------------------------------------------------------



    	if ($awarded->invoices)
    	{
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Time Line ' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('PO #' , 'Date', '' , '' ,  '' , '' , '' , '' , '' , '','' );
    		$header[]  = array($quote->ponum , date('m/d/Y', strtotime($awarded->awardedon)) ,'' , '',  '' , '' , '' , '' , '' , '','' );
    		//-----------------------------------------

    		foreach ($awarded->invoices as $invoice)
    		{

    			$header[] = array($invoice->invoicenum , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    			foreach ($invoice->items as $item)
    			{
    				$header[] = array($item->receiveddate, $item->itemname, $item->quantity.' Received' , '' , '' , '' , '' , '' , '' , '' , '' );
    			}

    		}
    	}


    	//--------------------Feedbacks------------------------------------------------------------

    	if ($awarded->status == 'complete')
    	{
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Feedbacks' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Company' , 'Rating', 'Feedback' , '' ,  '' , '' , '' , '' , '' , '','' );

    		foreach($messagecompanies as $combocompany)
    		{
    			if(isset($feedbacks[$combocompany['id']]))
    				$rating = '<div class="fixedrating" data-average="'.$feedbacks[$combocompany['id']]->rating.'" data-id="1"></div>';
    			else
    				$rating = '';
    			$feedback = isset($feedbacks[$combocompany['id']]) ? $feedbacks[$combocompany['id']]->feedback : '';
    			$header[] = array($combocompany['label'] , $rating, $feedback , '' ,  '' , '' , '' , '' , '' , '','' );
    		}
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );

    	}


    	//--------------------errorLog------------------------------------------------------------

    	if(!empty($errorLog))
    	{

    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('ERROR LOG' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('company' , 'Error', 'Item' , 'Qty' ,  'Invoice#' , 'Date' , '' , '' , '' , '','' );

    		foreach($errorLog as $error)
    		{
    			$inv_date = (isset($error->date) && $error->date!="" && $error->date!="0000-00-00" && $error->date!="1969-12-31")?date("m/d/Y",  strtotime($error->date)):"";
    			$header[] = array($error->title , $error->error, $error->itemcode , $error->quantity ,  $error->invoicenum , $inv_date , '' , '' , '' , '','' );
    		}

    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	}


    	//--------------------errorLog------------------------------------------------------------

    	createXls('contract_track_items', $header);
    	die();

    	//===============================================================================


    }   
    
    
    function sendautolateemail($id){
    	$quotearr = $this->quote_model->getallawardedqtyduebids();
    	//echo "<pre>",print_r($quotearr); die;
    	$this->load->library('email');
    	$config['charset'] = 'utf-8';
    	$config['mailtype'] = 'html';
    	$this->email->initialize($config);
    	foreach($quotearr as $quote){
    		if($quote->items){
    			foreach($quote->items as $items){

    				// echo "<pre>",print_r($items); die;
    				$data['email_body_title'] = "Dear {$items->companydetails->title}";
					 $data['email_body_content'] = "Quote '{$quote->quotedetails->ponum}' still has items left to be delivered and the item due has been past. These items are now past due. <br/><br/>
					 Below are the Details:<br/><br/>
					 Company: {$quote->quotedetails->companyname}<br>
					 PO: {$quote->quotedetails->ponum}<br>
					 Itemcode: {$items->itemcode}<br>
					 Date Requested : {$items->daterequested}
					 Quantity Left: {$items->quantityleft}";
					 $loaderEmail = new My_Loader();
    				$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    				/*$settings = (array)$this->settings_model->get_current_settings ();*/

    				$this->email->clear();
    				//$this->email->from($settings['adminemail'], "Administrator");
    				$this->email->from('email.jonrubin@gmail.com',"Administrator");
    				$this->email->to($items->companydetails->primaryemail);
    				$this->email->subject('Quantity Due Alert');
    				$this->email->message($send_body);
    				$this->email->set_mailtype("html");
    				$this->email->send($this->email);

    				$data['email_body_title'] = "Dear {$quote->quotedetails->companyname}";
 $data['email_body_content'] = "Quote: {$quote->quotedetails->ponum} still has items left to be delivered and the item due has been past. These items are now past due. <br/><br/>
 Below are the Details:<br/><br/>
 Company: {$items->companydetails->title}<br>
 PO: {$quote->quotedetails->ponum}<br>
 Itemcode: {$items->itemcode}<br>
 Date Requested : {$items->daterequested}
 Quantity Left: {$items->quantityleft}";
 $loaderEmail = new My_Loader();
 $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
 					$this->email->clear();
    				$settings = (array)$this->settings_model->get_setting_by_admin($quote->purchasingadmin);
    				//$this->email->from($settings['adminemail'], "Administrator");
    				$this->email->from('email.jonrubin@gmail.com',"Administrator");
    				$this->email->to($settings['adminemail']);
    				$this->email->subject('Quantity Due Alert');
    				$this->email->message($send_body);
    				$this->email->set_mailtype("html");
    				$this->email->send();

    			}
    		}
    	}
    	$this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Email Sent Successfully.</div></div>');
    	redirect('admin/quote/track/' . $id);
    }


    function sendautolateemailviacron(){
    	$quotearr = $this->quote_model->getallawardedqtyduebids();
    	//echo "<pre>",print_r($quotearr); die;
    	$this->load->library('email');
    	$config['charset'] = 'utf-8';
    	$config['mailtype'] = 'html';
    	$this->email->initialize($config);
    	foreach($quotearr as $quote){
    		if($quote->items){
    			foreach($quote->items as $items){

    				// echo "<pre>",print_r($items); die;
    				$data['email_body_title']  = "Dear {$items->companydetails->title}";
 $data['email_body_content'] = "Quote '{$quote->quotedetails->ponum}' still has items left to be delivered and the item due has been past. These items are now past due. <br/><br/>
 Below are the Details:<br/><br/>
 Company: {$quote->quotedetails->companyname}<br>
 PO: {$quote->quotedetails->ponum}<br>
 Itemcode: {$items->itemcode}<br>
 Date Requested : {$items->daterequested}
 Quantity Left: {$items->quantityleft}";
 $loaderEmail = new My_Loader();
    				$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    				/*$settings = (array)$this->settings_model->get_current_settings ();*/
    				$this->email->clear();
    				//$this->email->from($settings['adminemail'], "Administrator");
    				$this->email->from('email.jonrubin@gmail.com',"Administrator");
    				$this->email->to($items->companydetails->primaryemail);
    				$this->email->subject('Quantity Due Alert');
    				$this->email->message($send_body);
    				$this->email->set_mailtype("html");
    				$this->email->send($this->email);

    				$data['email_body_title']  = "Dear {$quote->quotedetails->companyname}";
 $data['email_body_content'] = "Quote: {$quote->quotedetails->ponum} still has items left to be delivered and the item due has been past. These items are now past due. <br/><br/>
 Below are the Details:<br/><br/>
 Company: {$items->companydetails->title}<br>
 PO: {$quote->quotedetails->ponum}<br>
 Itemcode: {$items->itemcode}<br>
 Date Requested : {$items->daterequested}
 Quantity Left: {$items->quantityleft}";
    				$this->email->clear();
    				$loaderEmail = new My_Loader();
    				$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    				$settings = (array)$this->settings_model->get_setting_by_admin($quote->purchasingadmin);
    				//$this->email->from($settings['adminemail'], "Administrator");
    				$this->email->from('email.jonrubin@gmail.com',"Administrator");
    				$this->email->to($settings['adminemail']);
    				$this->email->subject('Quantity Due Alert');
    				$this->email->message($send_body);
    				$this->email->set_mailtype("html");
    				$this->email->send();

    			}
    		}
    	}
    	$this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Email Sent Successfully.</div></div>');
    	redirect('admin/quote/track/' . $id);
    }

    function acceptshipment()
    {
        $id = $_POST['id'];
        $this->db->where('id',$id)->update('shipment',array('accepted'=>1));
    }

    function acceptall()
    {
        $quoteid = $_POST['quote'];
        $this->db->where('quote',$quoteid)->update('shipment',array('accepted'=>1));
    }

    function savefeedback()
    {
        $_POST['ratedate'] = date('Y-m-d');
        $_POST['purchasingadmin'] = $this->session->userdata('purchasingadmin');
		//print_r($_POST);die;
        $this->db->insert('quotefeedback',$_POST);
        redirect('admin/quote/track/'.$_POST['quote']);
    }


    function changestatus($id) {
        $this->db->where('id', $id);
        $this->db->update('quote', $_POST);
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Quote Status Changed.</div></div>');
        redirect('admin/quote/track/' . $id);
    }

    function invoicestatus($id) {
        $this->db->where('invoicenum', $_POST['invoicenum']);
        $this->db->update('received', $_POST);
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice Status Changed.</div></div>');
        redirect('admin/quote/track/' . $id);
    }

    function invoicepaystatus($id)
    {
        if($_POST['paymentstatus'] == 'Paid')
        {
            $_POST['status'] = 'Pending';
            $_POST['paymentdate'] = date('Y-m-d');
        }
        $this->db->where('invoicenum', $_POST['invoicenum']);
        $this->db->update('received', $_POST);

        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice Status Changed.</div></div>');
        redirect('admin/quote/track/' . $id);
    }


    function payinvoicebycc()
    {
        $company = $this->db->select('company.*')->from('received')
                   ->join('awarditem','received.awarditem=awarditem.id')
                   ->join('company','awarditem.company=company.id')
                   ->where('received.invoicenum',$_POST['invoicenum'])
                   ->get()->row()
                   ;
        $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
        if(!$bankaccount || !@$bankaccount->routingnumber || !@$bankaccount->accountnumber)
		{
		    $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bank account missing for credit card payment.</div></div>');
		    redirect('admin/quote/invoices');
		}
        //print_r($company);die;
		ini_set('max_execution_time', 300);
		$config = (array)$this->settings_model->get_current_settings();
		$config = array_merge($config, $this->config->config);

		require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
		Stripe::setApiKey($config['STRIPE_API_KEY']);
		//$myCard = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2015);
		$myCard = array('number' => $_POST['card'], 'exp_month' => $_POST['month'], 'exp_year' => $_POST['year']);
		$charge = Stripe_Charge::create(array('card' => $myCard, 'amount' => round($_POST['amount'],2) * 100, 'currency' => 'usd' ));
		//echo $charge;
		$chargeobj = json_decode($charge);
		if(@$chargeobj->paid)
		{
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
              $update = array(
                          'paymentstatus'=>'Paid',
                          'status'=>'Pending',
                          'paymenttype'=>'Credit Card',
                          'refnum'=>$chargeobj->balance_transaction
                          );
              //echo $_POST['invoicenum'];
              //print_r($update);die;
              $query = "UPDATE ".$this->db->dbprefix('received')." SET
              			paymentstatus='Paid',
              			status='Verified',
              			paymentdate='".date('Y-m-d')."',
              			paymenttype='Credit Card',
              			refnum='".$chargeobj->id."',
              			transfernum ='".$tobj->id."' 
              			WHERE invoicenum='".$_POST['invoicenum']."'";
              //echo $query;die;
              $this->db->query($query);
    		  $quote = $this->db->select('quote.*')
    		            ->from('received')
    		            ->join('awarditem','received.awarditem=awarditem.id')
    		            ->join('award','awarditem.award=award.id')
    		            ->join('quote','award.quote=quote.id')
    		            ->where('invoicenum',$_POST['invoicenum'])
    		            ->get()->row();

    		  $pa = $this->db->where('id',$this->session->userdata('id'))->get('users')->row();


              $data['email_body_title']  = "Dear {$company->title}";
$data['email_body_content'] = "$ {$_POST['amount']} has been transfered to your bank account for invoice#{$_POST['invoicenum']},
with the transfer# {$tobj->id}.
<br>Payment by: ".$pa->companyname."
<br>PO#: ".$quote->ponum."
";
$loaderEmail = new My_Loader();
              $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
              $settings = (array)$this->settings_model->get_current_settings ();
    	      $this->load->library('email');
    	      $config['charset'] = 'utf-8';
    	      $config['mailtype'] = 'html';
    	      $this->email->initialize($config);
              $this->email->from($settings['adminemail'], "Administrator");
              $this->email->to($company->primaryemail);
              $this->email->subject('Bank transfer notification from ezpzp');
              $this->email->message($send_body);
              $this->email->set_mailtype("html");
              $this->email->send();

              $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice paid successfully.</div></div>');
        	}
		}
		redirect('admin/quote/invoices');
    }

    public function mysql_date($date)
	{
		if(isset($date) && !empty($date))
		{
			$time = date('H:i:s');
			$parts = explode('/', $date);
			$newDate  = "$parts[2]-$parts[0]-$parts[1] $time";
			return $newDate;
		}
	}

    function savetrack($quoteid,$ajax=0)
    {
        $awarded = $this->quote_model->getawardedbid($quoteid);
        $data['email_body_title'] = "";
        $data['email_body_content'] = "";
        //echo '<pre>';print_r($awarded);die;
        $quote = $awarded->quotedetails;
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $project = $this->project_model->get_projects_by_id($quote->pid);
        $shipto = $awarded->shipto;
        $received = array();
        $invoices = array();
        $credits = array();
        
        $invoiceDetailStr = '<table class="table table-bordered" style="" border="1">
					    	<tr>
					    		<th width="10%">Company</th>					    	
					    		<th width="10%">Item Code</th>
					    		<th width="10%">Item Name</th>
					    		<th width="9%">Item Image</th>
					    		<th width="4%">Unit</th>
					    		<th width="4%">Qty.</th>
					    		<th width="4%">EA</th>
					    		<th width="5%">Total Price</th>
					    		<th width="8%">Payment</th>
					    		<th width="8%">Verification</th>
					    		<th width="10%">Cost Code</th>
					    	</tr>';
       
        foreach ($awarded->items as $item)
        {
            $received[$item->id] = (array) $item;
            $updatearray = array();
            $key = $item->id;
            $updatearray['received'] = $_POST['received' . $key] + $received[$item->id]['received'];
            if ($_POST['received' . $key] > $item->quantity - $item->received) {
                $this->session->set_flashdata('message', '<div class="alert alert-failure"><a data-dismiss="alert" class="close" href="#">X</a>
<div class="msgBox">Received quantity Cannot be more than due.</div></div>');

                redirect('admin/quote/track/' . $quote->id);
            }
            $received[$item->id]['received'] = $_POST['received' . $key];
            //print_r($updatearray);die;
            $this->quote_model->db->where('id', $key);
            $this->quote_model->db->update('awarditem', $updatearray);
            if ($received[$item->id]['received'] > 0)
            {
                if ($this->input->post('makedefaultinvoicenum') == '1') {
                    $temp['defaultinvoicenum'] = $_POST['invoicenum' . $key];
                    $this->session->set_userdata($temp);
                }
                if ($this->input->post('makedefaultreceiveddate') == '1') {
                    $temp['defaultreceiveddate'] = $this->mysql_date($_POST['receiveddate' . $key]);
                    $this->session->set_userdata($temp);
                }

                                $invoicearr = array();
                $invoicearr = explode(",",$_POST['invoicenum' . $key]);
				//echo "<pre>",print_r($invoicearr); echo "<pre>",print_r($_POST); die;		
				
				$sql = "SELECT duedate, term FROM " .$this->db->dbprefix('invoice_cycle') . " where company='" . $item->company . "'
				and purchasingadmin = '". $item->purchasingadmin ."'";
        //echo $sql;
        $resultinvoicecycle = $this->db->query($sql)->row();
						
                if(count($invoicearr)>1) {
                	foreach($invoicearr as $inv) {

                		$ship = $this->db->select('shipment.*')
                		->from('shipment')
                		->where('quote',$quoteid)
                		->where('awarditem',$item->id)
                		->where('invoicenum',$inv)
                		->get()->row();

						
                		$insertarray = array('awarditem' => $item->id, 'quantity' => $ship->quantity, 'invoicenum' => $inv, 'receiveddate' => $this->mysql_date($_POST['receiveddate' . $key]));
                		$insertarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');        		            		
                		
                		if($resultinvoicecycle){
                			
                			if(@$resultinvoicecycle->duedate && @$resultinvoicecycle->term){

                				if($resultinvoicecycle->term ==30)
                				$monthcount=1;
                				if($resultinvoicecycle->term ==60)
                				$monthcount=2;
                				if($resultinvoicecycle->term ==90)
                				$monthcount=3;
								$invoicereceiveddate = $_POST['receiveddate' . $key];	
                				$next_term = date("Y-m-d", strtotime("$invoicereceiveddate +".$monthcount." month"));

                				$exploded = explode("-",$resultinvoicecycle->duedate);

                				$explode = explode("-",$next_term);
                				$explode[2] = $exploded[2];
                				$next_term = implode("-",$explode);               				
                				$insertarray['datedue'] = $next_term;
                			}
                		}
                		
                		 /*if (strpos(@$inv,'paid-in-full-already') !== false) {                		 	
                		 	 $this->db->where('invoicenum', $inv);
                		 	 $this->db->where('awarditem', $item->id);
            				 $this->quote_model->db->update('received', $insertarray);                		 	
                		 }else*/                		
                			$this->quote_model->db->insert('received', $insertarray);
                			
                			
                			// Shipment entry for directly accepted quantities

                			$shiparr = array();
                			$shiparr['quantity'] = @$received[$item->id]['received'];
                			$shiparr['invoicenum'] = trim(@$_POST['invoicenum' . @$key]);
                			$shiparr['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                			$shiparr['quote'] = $quoteid;
                			$shiparr['company'] = $item->company;
                			$shiparr['awarditem'] = $item->id;
                			$shiparr['itemid'] = $item->itemid;
                			$shiparr['shipdate'] = date('Y-m-d');
                			$shiparr['accepted'] = 1;
                			//print_r($arr);
                			$this->db->insert('shipment',$shiparr);
                			
                			
                			 // Stock Inventory Entry for items
                			$stockarray = array();
                			$stockarray['quantity'] = (@$ship->quantity)?$ship->quantity:0;            			
                			
                			$this->db->where('itemid',$item->itemid);
                			$this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
                			if($quote->pid)
                			{
                				$this->db->where('project',$quote->pid);
                			}
                			$existing = $this->db->get('inventory')->row();
                			if($existing)
                			{
                				$stockarray['quantity'] += $existing->quantity; 
                				$this->db->where('itemid',$item->itemid);
                				$this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
                				if($quote->pid)
                				{
                					$this->db->where('project',$quote->pid);
                				}
                				$this->db->update('inventory',$stockarray);
                			}
                			else
                			{
                				$stockarray['itemid'] = $item->itemid;
                				$stockarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                				if($quote->pid)
                				{
                					$stockarray['project'] = $quote->pid;
                				}
                				$this->db->insert('inventory',$stockarray);
                			}
                			

                		$insertarray['id'] = $item->id;
                		$insertarray['itemname'] = $item->itemname;
                		$insertarray['companyname'] = $item->companyname;
                		$insertarray['daterequested'] = $item->daterequested;
                		$insertarray['unit'] = $item->unit;
                		$insertarray['ea'] = $item->ea;
                		$insertarray['item_img'] = $item->item_img;

                		if (!isset($invoices[$inv])) {
                			$invoices[$inv] = array();
                			$invoices[$inv]['invoicenum'] = $inv;
                			$invoices[$inv]['items'] = array($insertarray);
                			$invoices[$inv]['invoicenotes'] = $item->companydetails->invoicenote;
                		} else {
                			$invoices[$inv]['items'][] = $insertarray;
                		}
                		if(isset($credits[$item->company]))
                		{
                			$credits[$item->company]['amount'] += $insertarray['quantity'] * $insertarray['ea'];
                			$credits[$item->company]['items'][]=$insertarray;
                			$credits[$item->company]['item_img'] = $item->item_img;
                		}
                		else
                		{
                			$credits[$item->company] = array();
                			$credits[$item->company]['amount'] = $insertarray['quantity'] * $insertarray['ea'];
                			$credits[$item->company]['items'] = array($insertarray);
                			$credits[$item->company]['item_img'] = $item->item_img;
                		}

                	}
                }else {


                $insertarray = array('awarditem' => $item->id, 'quantity' => $received[$item->id]['received'], 'invoicenum' => trim($_POST['invoicenum' . $key]), 'receiveddate' => $this->mysql_date($_POST['receiveddate' . $key]));
                $insertarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                
                /*if (strpos(@trim($_POST['invoicenum' . $key]),'paid-in-full-already') !== false) {                		 	
                		 	 $this->db->where('invoicenum', trim($_POST['invoicenum' . $key]));
                		 	 $this->db->where('awarditem', $item->id);
            				 $this->quote_model->db->update('received', $insertarray);                		 	
                }else*/           
               
                
                if($resultinvoicecycle){

                	if(@$resultinvoicecycle->duedate && @$resultinvoicecycle->term){

                		if($resultinvoicecycle->term ==30)
                		$monthcount=1;
                		if($resultinvoicecycle->term ==60)
                		$monthcount=2;
                		if($resultinvoicecycle->term ==90)
                		$monthcount=3;
                		$invoicereceiveddate = $_POST['receiveddate' . $key];
                		$next_term = date("Y-m-d", strtotime("$invoicereceiveddate +".$monthcount." month"));

                		$exploded = explode("-",$resultinvoicecycle->duedate);

                		$explode = explode("-",$next_term);
                		$explode[2] = $exploded[2];
                		$next_term = implode("-",$explode);
                		$insertarray['datedue'] = $next_term;
                	}
                }/*else 
				$insertarray['datedue'] = $_POST['datedue' . $key];*/
                
                $paymentstatus = 'Unpaid';
                $status = 'Pending';
                if(@$_POST['invoicetype' . $key] == "fullpaid"){
                $insertarray['invoice_type'] = "alreadypay";
                $insertarray['paymentstatus']='Paid';
                $insertarray['paymentdate'] = $_POST['paymentdate' . $key];
                $insertarray['paymenttype'] = 'Credit Card';
                $insertarray['refnum'] = $_POST['refnum' . $key];
                $insertarray['status'] = 'Verified'; 
                
                $status = 'Verified'; 
                $paymentstatus =  "Paid";              
                }
                
                             
                $this->quote_model->db->insert('received', $insertarray);              

                
                // Shipment entry for directly accepted quantities
                
                $shiparr = array();
	            $shiparr['quantity'] = @$received[$item->id]['received'];
	            $shiparr['invoicenum'] = trim(@$_POST['invoicenum' . @$key]);
	            $shiparr['purchasingadmin'] = $this->session->userdata('purchasingadmin');
	            $shiparr['quote'] = $quoteid;
	            $shiparr['company'] = $item->company;
	            $shiparr['awarditem'] = $item->id;
	            $shiparr['itemid'] = $item->itemid;
	            $shiparr['shipdate'] = date('Y-m-d');
	            $shiparr['accepted'] = 1;
	            //print_r($arr);
	            $this->db->insert('shipment',$shiparr);
                
                // Stock Inventory Entry for items
                
                $stockarray = array();
                $stockarray['quantity'] = ($received[$item->id]['received'])?$received[$item->id]['received']:0;

                $this->db->where('itemid',$item->itemid);
                $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
                if($quote->pid)
                {
                	$this->db->where('project',$quote->pid);
                }
                $existing = $this->db->get('inventory')->row();
                if($existing)
                {
                	$stockarray['quantity'] += $existing->quantity; 
                	$this->db->where('itemid',$item->itemid);
                	$this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
                	if($quote->pid)
                	{
                		$this->db->where('project',$quote->pid);
                	}
                	$this->db->update('inventory',$stockarray);
                }
                else
                {
                	$stockarray['itemid'] = $item->itemid;
                	$stockarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                	if($quote->pid)
                	{
                		$stockarray['project'] = $quote->pid;
                	}
                	$this->db->insert('inventory',$stockarray);
                }
                
              
                $insertarray['id'] = $item->id;
                $insertarray['itemname'] = $item->itemname;
                $insertarray['companyname'] = $item->companyname;
                $insertarray['daterequested'] = $item->daterequested;
                $insertarray['unit'] = $item->unit;
                $insertarray['ea'] = $item->ea;
                $insertarray['item_img'] = $item->item_img;

                if (!isset($invoices[$_POST['invoicenum' . $key]])) {
                    $invoices[$_POST['invoicenum' . $key]] = array();
                    $invoices[$_POST['invoicenum' . $key]]['invoicenum'] = $_POST['invoicenum' . $key];
                    $invoices[$_POST['invoicenum' . $key]]['items'] = array($insertarray);
                    $invoices[$_POST['invoicenum' . $key]]['invoicenotes'] = $item->companydetails->invoicenote;
                } else {
                    $invoices[$_POST['invoicenum' . $key]]['items'][] = $insertarray;
                }
               
                
                if(isset($credits[$item->company]))
                {
                    $credits[$item->company]['amount'] += $insertarray['quantity'] * $insertarray['ea'];
                    $credits[$item->company]['items'][]=$insertarray;
                    $credits[$item->company]['item_img'] = $item->item_img;
                }
                else
                {
                    $credits[$item->company] = array();
                    $credits[$item->company]['amount'] = $insertarray['quantity'] * $insertarray['ea'];
                    $credits[$item->company]['items'] = array($insertarray);
                    $credits[$item->company]['item_img'] = $item->item_img;
                }

              }
            }
            
            if(isset($item->item_img) && $item->item_img!= "" && file_exists("./uploads/item/".$item->item_img)) 
    		 { 
             	$img_name = "<img style='max-height: 120px;max-width: 100px; padding: 5px;' height='75' width='75' src='". site_url('uploads/item/'.$item->item_img)."' alt='".$item->item_img."'>";
             } 
             else 
             { 
             	$img_name = "<img style='max-height: 120px;max-width: 100px;  padding: 5px;' height='75' width='75' src='".site_url('uploads/item/big.png')."'>";
             } 		
         
            $invoiceDetailStr .='<tr>
            						<td>'.$item->companyname.'</td>            						
            						<td>'.$item->itemcode.'</td>
            						<td>'.$item->itemname.'</td>
            						<td>'.$img_name.'</td>
            						<td>'.$item->unit.'</td>
            						<td>'.$item->quantity.'</td>
            						<td>'.$item->ea.'</td>
            						<td>'.$item->totalprice.'</td>
            						<td>'.@$paymentstatus.'</td>
            						<td>'.@$status.'</td>
            						<td>'.$item->costcode.'</td>
            					</tr>';   
            
        }
        
        	$invoiceDetailStr .= '</table>';
        //print_r($invoices);die;
        //echo '<pre>';print_r($invoiceDetailStr);die;
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        $this->db->where('id',$this->session->userdata('purchasingadmin'));
        $cpa = $this->db->get('users')->row();

       // $company = $this->company_model->get_companys_by_id($cid);
      // echo '<pre>',print_r($awarded);die;
        foreach ($invoices as $invoice)
        { 
            $pdfhtml = '
				<strong>Invoice #: ' . $invoice['invoicenum'] . '</strong><br/>
				<table width="100%" cellspacing="2" cellpadding="2">
				  <tr>
				    <td width="33%" align="left" valign="top">
				    <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></th>
				        </tr>
				      <tr>
				        <td width="33%" valign="top">Project Title</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $project->title . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Address</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $project->address . '</td>
				      </tr>
				    </table>
				    </td>
				    <td width="10" align="left" valign="top">&nbsp;</td>
				    <td width="65%" align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Purchase Order Information</strong></font></th>
			          </tr>
				      <tr>
				        <td width="33%" valign="top">PO#</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $quote->ponum . '</td>
				      </tr>				      
				      <tr>
				        <td valign="top">PO# Date</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->podate . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Invoice Date</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . date('m/d/Y') . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Invoice Due Date</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . @$invoice->datedue . '</td>
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
				        <td bgcolor="#000033"><font color="#FFFFFF"><strong>Ship to</strong></font></td>
				      </tr>
				      <tr>
				        <td>' . nl2br($shipto) . '</td>
				      </tr>
				    </table></td>
				  </tr>

			</table>

				<table width="100%" cellspacing="0" cellpadding="4">
				  <tr>
	              <td>Items:</td>
	              </tr>
	             </table>

	             <br/>

				<table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				  <thead>
				  <tr>
				    <th bgcolor="#000033"><font color="#FFFFFF">Item No</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Item Image</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Company</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Date Requested</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Date Received</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Quantity</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit Price</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Total Price</font></th>
				  </tr>
				  </thead>
				  ';
            $totalprice = 0;
            $i = 0;
            foreach ($invoice['items'] as $invoiceitem) {
                $totalprice += $invoiceitem['ea'] * $invoiceitem['quantity'];   
               if(isset($invoiceitem['item_img']) && $invoiceitem['item_img']!= "" && file_exists("./uploads/item/".$invoiceitem['item_img'])) { 
             		$img_name = '<img style="max-height: 120px;max-width: 100px; padding: 5px;" height="75" width="75" src="'. site_url('uploads/item/'.$invoiceitem['item_img']).'" alt="'.$invoiceitem['item_img'].'">';
             } else { 
             		$img_name = '<img style="max-height: 120px;max-width: 100px;  padding: 5px;" height="75" width="75" src="'.site_url('uploads/item/big.png').'">';
             } 	
                
                $pdfhtml.='<tr nobr="true">
				    <td style="border: 1px solid #000000;">' . ++$i . '</td>
				    <td style="border: 1px solid #000000;">' . @$img_name . '</td>
				    <td style="border: 1px solid #000000;">' . htmlentities($invoiceitem['itemname']) . '</td>
				    <td style="border: 1px solid #000000;">' . htmlentities($invoiceitem['companyname']) . '</td>
				    <td style="border: 1px solid #000000;">' . $invoiceitem['daterequested'] . '</td>
				    <td style="border: 1px solid #000000;">' . $this->mysql_date($_POST['receiveddate' . $invoiceitem['id']]) . '</td>
				    <td style="border: 1px solid #000000;">' . $received[$invoiceitem['id']]['received'] . '</td>
				    <td style="border: 1px solid #000000;">' . $invoiceitem['unit'] . '</td>
				    <td align="right" style="border: 1px solid #000000;">$ ' . $invoiceitem['ea'] . '</td>
				    <td align="right" style="border: 1px solid #000000;">$ ' . $invoiceitem['ea'] * $received[$invoiceitem['id']]['received'] . '</td>
				  </tr>
				  ';
            } 
            $taxtotal = $totalprice * $config['taxpercent'] / 100;
            $grandtotal = $totalprice + $taxtotal;

            $pdfhtml.='<tr>
            <td colspan="6" rowspan="3">
            <div style="width:70%">
            <br/>
            <h4 class="semi-bold">Terms and Conditions</h4>
            <p>'.$invoice['invoicenotes'].'</p>
            <h5 class="text-right semi-bold">Thank you for your business</h5>
            </div>
            </td>
            <td align="right">Subtotal</td>
            <td align="right">$ ' . number_format($totalprice, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Tax</td>
            <td align="right">$ ' . number_format($taxtotal, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Total</td>
            <td align="right">$ ' . number_format($grandtotal, 2) . '</td>
            </tr></table>
            ';

          
            if (!class_exists('TCPDF')) {
            	require_once($config['base_dir'] . 'application/libraries/tcpdf/config/lang/eng.php');
            	require_once($config['base_dir'] . 'application/libraries/tcpdf/tcpdf.php');
			}

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('');
            $pdf->SetTitle('');
            $pdf->SetSubject('');

            $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));

            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(true);

            $pdf->SetHeaderData('', '', $cpa->companyname . '', 'Invoice');

            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->AddPage('L', 'LETTER');

            $pdf->SetFont('helvetica', '', 8, '', true);
            $pdf->writeHTML($pdfhtml, true, 0, true, true);

            $pdf->lastPage();
            $pdfname = $config['base_dir'] . 'uploads/pdf/' . $quote->ponum . '_invoice_' . $invoice['invoicenum'] . '_' . date('YmdHis') . '.pdf';
            $pdf->Output($pdfname, 'f');


            $data['email_body_content'] = "Please find the attachment invoice for PO#: " . $quote->ponum . ".<br/><br/>";
            $data['email_body_content'] .= "The following items have been marked as received by (" . $cpa->companyname . ")  from PO#: (" . $quote->ponum . ")<br/><br/>".$invoiceDetailStr;

            $settings = (array) $this->settings_model->get_current_settings();

            $toemail = $settings['adminemail'];
            $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
	        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
            $purchaseusers = $this->db->query($sql)->result();
            foreach ($purchaseusers as $pu) {
                $toemail = $toemail . ',' . $pu->email;
            }
            $loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->clear(true);
            $this->email->from($settings['adminemail'], "Administrator");
            $this->email->to($toemail);
            //$this->email->to('tushar1717@gmail.com');
            $this->email->subject('Invoice for PO#:' . $quote->ponum);           
            $this->email->message($send_body);
            $this->email->set_mailtype("html");
            $this->email->attach($pdfname);
            $this->email->send();
        }

        if($credits)
        {
            $this->notifycredits($credits, $quote->ponum);
        }
        //echo '<pre>';print_r($received);die;
        //$this->sendbacktrack($quoteid);/// SENDING BACKTRACK IS NOW MANUAL
        if(!$ajax)
            redirect('admin/quote/track/' . $quoteid);
    }

    
    function savecontracttrack($quoteid,$ajax=0)
    {
        $awarded = $this->quote_model->getawardedcontractbid($quoteid);
        $data['email_body_title'] = "";
        $data['email_body_content'] = "";
        //echo '<pre>';print_r($awarded);die;
        $quote = $awarded->quotedetails;
        /*if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }*/
        $project = $this->project_model->get_projects_by_id($quote->pid);
        $shipto = $awarded->shipto;
        $received = array();
        $invoices = array();
        $credits = array();
        /*echo "<pre>",print_r($_POST); 
        echo "<pre>",print_r($awarded->items); die;*/
        foreach ($awarded->items as $item)
        {
            $received[$item->id] = (array) $item;
            $updatearray = array();
            $key = $item->id;
            $updatearray['received'] = $_POST['received' . $key] + $received[$item->id]['received'];
            if ($_POST['received' . $key] > 100 - $item->received) {
                $this->session->set_flashdata('message', '<div class="alert alert-failure"><a data-dismiss="alert" class="close" href="#">X</a>
<div class="msgBox">Received quantity Cannot be more than due.</div></div>');

                redirect('admin/quote/contracttrack/' . $quote->id);
            }
            $received[$item->id]['received'] = $_POST['received' . $key];
            //print_r($updatearray);die;
            $this->quote_model->db->where('id', $key);
            $this->quote_model->db->update('awarditem', $updatearray);
            //echo "<pre>",print_r($received); die;
            if ($received[$item->id]['received'] > 0)
            {
                if ($this->input->post('makedefaultinvoicenum') == '1') {
                    $temp['defaultinvoicenum'] = $_POST['invoicenum' . $key];
                    $this->session->set_userdata($temp);
                }
                if ($this->input->post('makedefaultreceiveddate') == '1') {
                    $temp['defaultreceiveddate'] = $this->mysql_date($_POST['receiveddate' . $key]);
                    $this->session->set_userdata($temp);
                }

                $invoicearr = array();
                $invoicearr = explode(",",$_POST['invoicenum' . $key]);

                if(count($invoicearr)>1) {
                	foreach($invoicearr as $inv) {

                		$ship = $this->db->select('shipment.*')
                		->from('shipment')
                		->where('quote',$quoteid)
                		->where('awarditem',$item->id)
                		->where('invoicenum',$inv)
                		->get()->row();


                		$insertarray = array();
                		if(@$item->id)
                		$insertarray['awarditem'] = $item->id;
                		if(@$ship->quantity)
                		$insertarray['quantity'] = $ship->quantity; 
                		if(@$inv)
                		$insertarray['invoicenum'] = $inv; 
                		if(@$_POST['receiveddate' . $key])
                		$insertarray['receiveddate'] = $this->mysql_date($_POST['receiveddate' . $key]);                		
                		
                		$insertarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                		$this->quote_model->db->insert('received', $insertarray);

                		$insertarray['id'] = $item->id;
                		$insertarray['itemname'] = $item->itemname;
                		$insertarray['companyname'] = $item->companyname;
                		$insertarray['daterequested'] = $item->daterequested;
                		$insertarray['unit'] = $item->unit;
                		$insertarray['ea'] = $item->ea;

                		if (!isset($invoices[$inv])) {
                			$invoices[$inv] = array();
                			$invoices[$inv]['invoicenum'] = $inv;
                			$invoices[$inv]['items'] = array($insertarray);
                			//$invoices[$inv]['invoicenotes'] = $item->companydetails->invoicenote;
                		} else {
                			$invoices[$inv]['items'][] = $insertarray;
                		}
                		if(isset($credits[$item->company]))
                		{
                			$credits[$item->company]['amount'] += $insertarray['quantity'] * $insertarray['ea'];
                			$credits[$item->company]['items'][]=$insertarray;
                		}
                		else
                		{
                			$credits[$item->company] = array();
                			$credits[$item->company]['amount'] = $insertarray['quantity'] * $insertarray['ea'];
                			$credits[$item->company]['items'] = array($insertarray);
                		}

                	}
                }else {


                $insertarray = array('awarditem' => $item->id, 'quantity' => $received[$item->id]['received'], 'invoicenum' => trim($_POST['invoicenum' . $key]), 'receiveddate' => $this->mysql_date($_POST['receiveddate' . $key]));
                $insertarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                $this->quote_model->db->insert('received', $insertarray);


                $insertarray['id'] = $item->id;
                $insertarray['itemname'] = $item->itemname;
                $insertarray['companyname'] = $item->companyname;
                $insertarray['daterequested'] = $item->daterequested;
                $insertarray['unit'] = $item->unit;
                $insertarray['ea'] = $item->ea;

                if (!isset($invoices[$_POST['invoicenum' . $key]])) {
                    $invoices[$_POST['invoicenum' . $key]] = array();
                    $invoices[$_POST['invoicenum' . $key]]['invoicenum'] = $_POST['invoicenum' . $key];
                    $invoices[$_POST['invoicenum' . $key]]['items'] = array($insertarray);
                    //$invoices[$_POST['invoicenum' . $key]]['invoicenotes'] = $item->companydetails->invoicenote;
                } else {
                    $invoices[$_POST['invoicenum' . $key]]['items'][] = $insertarray;
                }
                if(isset($credits[$item->company]))
                {
                    $credits[$item->company]['amount'] += $insertarray['quantity'] * $insertarray['ea'];
                    $credits[$item->company]['items'][]=$insertarray;
                }
                else
                {
                    $credits[$item->company] = array();
                    $credits[$item->company]['amount'] = $insertarray['quantity'] * $insertarray['ea'];
                    $credits[$item->company]['items'] = array($insertarray);
                }

              }
            }
        }
        //print_r($invoices);die;
        //echo '<pre>';print_r($credits);die;
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        $this->db->where('id',$this->session->userdata('purchasingadmin'));
        $cpa = $this->db->get('users')->row();

       // $company = $this->company_model->get_companys_by_id($cid);
        foreach ($invoices as $invoice)
        {
            $pdfhtml = '
				<strong>Invoice #: ' . $invoice['invoicenum'] . '</strong><br/>
				<table width="100%" cellspacing="2" cellpadding="2">
				  <tr>
				    <td width="33%" align="left" valign="top">
				    <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></th>
				        </tr>
				      <tr>
				        <td width="33%" valign="top">Project Title</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $project->title . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Address</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $project->address . '</td>
				      </tr>
				    </table>
				    </td>
				    <td width="10" align="left" valign="top">&nbsp;</td>
				    <td width="65%" align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Billing Information</strong></font></th>
			          </tr>
				      <tr>
				        <td width="33%" valign="top">Contract</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $quote->ponum . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Subject</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->subject . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Contract Date</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->podate . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Invoice Date</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . date('m/d/Y') . '</td>
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
				        <td bgcolor="#000033"><font color="#FFFFFF"><strong>Project Address</strong></font></td>
				      </tr>
				      <tr>
				        <td>' . nl2br($shipto) . '</td>
				      </tr>
				    </table></td>
				  </tr>

			</table>

				<table width="100%" cellspacing="0" cellpadding="4">
				  <tr>
	              <td>Items:</td>
	              </tr>
	             </table>

	             <br/>

				<table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				  <thead>
				  <tr>
				    <th bgcolor="#000033"><font color="#FFFFFF">Item No</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Company</font></th>
				    <!-- <th bgcolor="#000033"><font color="#FFFFFF">Date Requested</font></th> -->
				    <th bgcolor="#000033"><font color="#FFFFFF">Date Completed</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Update Progress</font></th>
				    <!-- <th bgcolor="#000033"><font color="#FFFFFF">Unit</font></th> -->
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit Price</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Total Price</font></th>
				  </tr>
				  </thead>
				  ';
            $totalprice = 0;
            $i = 0;
            foreach ($invoice['items'] as $invoiceitem) {
                $totalprice += $invoiceitem['ea'] * ($invoiceitem['quantity']/100);
                $pdfhtml.='<tr nobr="true">
				    <td style="border: 1px solid #000000;">' . ++$i . '</td>
				    <td style="border: 1px solid #000000;">' . htmlentities($invoiceitem['itemname']) . '</td>
				    <td style="border: 1px solid #000000;">' . htmlentities($invoiceitem['companyname']) . '</td>
				    <!-- <td style="border: 1px solid #000000;">' . $invoiceitem['daterequested'] . '</td> -->
				    <td style="border: 1px solid #000000;">' . $this->mysql_date($_POST['receiveddate' . $invoiceitem['id']]) . '</td>
				    <td style="border: 1px solid #000000;">' . $received[$invoiceitem['id']]['received'] . '%</td>
				    <!-- <td style="border: 1px solid #000000;">' . $invoiceitem['unit'] . '</td> -->
				    <td align="right" style="border: 1px solid #000000;">$ ' . $invoiceitem['ea'] . '</td>
				    <td align="right" style="border: 1px solid #000000;">$ ' . $invoiceitem['ea'] * ($received[$invoiceitem['id']]['received']/100) . '</td>
				  </tr>
				  ';
            }
            $taxtotal = $totalprice * $config['taxpercent'] / 100;
            $grandtotal = $totalprice + $taxtotal;

            $pdfhtml.='<tr>
            <td colspan="5" rowspan="3">
            <div style="width:70%">
            <br/>
            <h4 class="semi-bold">Terms and Conditions</h4>            
            <h5 class="text-right semi-bold">Thank you for your business</h5>
            </div>
            </td>
            <td align="right">Subtotal</td>
            <td align="right">$ ' . number_format($totalprice, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Tax</td>
            <td align="right">$ ' . number_format($taxtotal, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Total</td>
            <td align="right">$ ' . number_format($grandtotal, 2) . '</td>
            </tr></table>
            ';


            if (!class_exists('TCPDF')) {
            	require_once($config['base_dir'] . 'application/libraries/tcpdf/config/lang/eng.php');
            	require_once($config['base_dir'] . 'application/libraries/tcpdf/tcpdf.php');
			}

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('');
            $pdf->SetTitle('');
            $pdf->SetSubject('');

            $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));

            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(true);

            $pdf->SetHeaderData('', '', $cpa->companyname . '', 'Invoice');

            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->AddPage('L', 'LETTER');

            $pdf->SetFont('helvetica', '', 8, '', true);
            $pdf->writeHTML($pdfhtml, true, 0, true, true);

            $pdf->lastPage();
            $pdfname = $config['base_dir'] . 'uploads/pdf/' . $quote->ponum . '_invoice_' . $invoice['invoicenum'] . '_' . date('YmdHis') . '.pdf';
            $pdf->Output($pdfname, 'f');

            $data['email_body_content'] = "Please find the attachment invoice for PO#: " . $quote->ponum . ".<br/><br/>";
            $data['email_body_content'] .= "You have been awarded by " . $cpa->companyname . ".  for PO#: " . $quote->ponum . ".<br/><br/>";

            $settings = (array) $this->settings_model->get_current_settings();

            $toemail = $settings['adminemail'];
            $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
	        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
            $purchaseusers = $this->db->query($sql)->result();
            foreach ($purchaseusers as $pu) {
                $toemail = $toemail . ',' . $pu->email;
            }
            $loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->clear(true);
            $this->email->from($settings['adminemail'], "Administrator");
            $this->email->to($toemail);

            $this->email->subject('Invoice for PO#:' . $quote->ponum);
            $this->email->message($send_body);
            $this->email->set_mailtype("html");
            $this->email->attach($pdfname);
            $this->email->send();
        }

        if($credits)
        {
            $this->notifycredits($credits, $quote->ponum);
        }
        //echo '<pre>';print_r($received);die;
        //$this->sendbacktrack($quoteid);/// SENDING BACKTRACK IS NOW MANUAL
        if(!$ajax)
            redirect('admin/quote/contracttrack/' . $quoteid);
    }
    
    
    
    function notifycredits($credits, $ponum)
    {

        $this->db->where('id',$this->session->userdata('purchasingadmin'));
        $cpa = $this->db->get('users')->row();

        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        $data['email_body_title'] = "";
        $data['email_body_content'] = "";
      
        foreach($credits as $cid=>$credit)
        {
            $amount = $credit['amount'];
            $tax = $amount * $config['taxpercent'] / 100;
            $items = $credit['items'];
            $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
            $this->db->where('company',$cid);
            $tier = $this->db->get('purchasingtier')->row();
            //if($tier && $tier->creditlimit - $amount > 0 && $tier->creditfrom <= date('Y-m-d') && $tier->creditto >= date('Y-m-d') )
            if($tier && $tier->creditlimit - $amount > 0 )
            {
                $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
                $this->db->where('company',$cid);
                $this->db->update('purchasingtier',array('creditlimit'=>$tier->creditlimit-$amount));
                $company = $this->company_model->get_companys_by_id($cid);
                $tamount="";
                $ramount="";
                $tamount=$amount+$tax;
                $ramount=$tier->creditlimit - $amount;
                $data['email_body_title'] = "Credit amount of ".$cpa->fullname.",".$cpa->companyname." has been deducted by $".$tamount."<br>";
                $data['email_body_content'] = "Remaining available credit for ".$cpa->companyname."is $".$ramount."<br><br>";
                $data['email_body_content'] .= "Find the details below:<br/><br/>";
                $data['email_body_content'] .= "<table border='1' width='100%'>";
                $data['email_body_content'] .= "<tr><td><strong> Item Image</strong></td><td><strong>Name</strong></td><td><strong>Price</strong></td><td><strong>Quantity</strong></td><td><strong>Total</strong></td></tr>";
                $totalamount = 0;
                foreach($items as $item)
                {
                	if(isset($item->item_img) && $item->item_img!= "" && file_exists("./uploads/item/".$item->item_img)) 
		    		 { 
		             	$img_name = "<img style='max-height: 120px;max-width: 100px; padding: 5px;' height='65' width='65' src='". site_url('uploads/item/'.$item->item_img)."' alt='".$item->item_img."'>";
		             } 
		             else 
		             { 
		             	$img_name = "<img style='max-height: 120px;max-width: 100px;  padding: 5px;' height='65' width='65' src='".site_url('uploads/item/big.png')."'>";
		             } 		
                    $amount = $item['quantity'] * $item['ea'];
                    $totalamount += $amount;
                    $data['email_body_content'] .= "<tr>
                    				<td>{$img_name}</td>
                    				<td>{$item['itemname']}</td>
                    				<td>{$item['ea']}</td>
                    				<td>{$item['quantity']}</td>
                    				<td> $".round($amount,2)."</td>
                    			</tr>";
                }
                $tax = $totalamount * $config['taxpercent'] / 100;
                $totalamountwithtax = $totalamount + $tax;
                $totalamountwithtax = round($totalamountwithtax,2);
                $data['email_body_content'] .= "<tr>
                				<td colspan='4'>Total</td>                				
                				<td style='text-align:right'>$ ".round($totalamount,2)."</td>
                			</tr>";
                $data['email_body_content'] .= "<tr>
                				<td colspan='4'>Tax</td>
                				<td style='text-align:right'>$ ".round($tax,2)."</td>
                			</tr>";
                $data['email_body_content'] .= "<tr>
                				<td colspan='4'>Total</td>
                				<td style='text-align:right'>$ ".$totalamountwithtax."</td>
                			</tr>";

                $data['email_body_content'] .= "</table>";
                $loaderEmail = new My_Loader();
                $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
                $this->load->library('email');
                $config['charset'] = 'utf-8';
                $config['mailtype'] = 'html';
                $this->email->initialize($config);
                $this->email->clear(true);
                $this->email->from($config['adminemail'], "Administrator");
                $this->email->to($company->primaryemail.','.$cpa->email);

                $this->email->subject('Credit Alert');
                $this->email->message($send_body);
                $this->email->set_mailtype("html");
                $this->email->send();
            }
        }
    }

    function sendbacktrack($quoteid)
    {
        $quote = $this->quote_model->get_quotes_by_id($quoteid);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $awarded = $this->quote_model->getawardedbid($quoteid);
        $backtracks = array();
        foreach ($awarded->items as $item) {
            if ($item->quantity > $item->received) {
                if (!isset($backtracks[$item->company])) {
                    $backtracks[$item->company] = array();
                    $backtracks[$item->company]['company'] = $item->company;
                    $backtracks[$item->company]['items'] = array();
                }
                $backtracks[$item->company]['items'][] = $item;
            }
        }
        //print_r($backtracks);die;
        foreach ($backtracks as $backtrack) {
            $c = $this->company_model->get_companys_by_id($company); //$backtrack['company']);


            $this->quote_model->db->where(array(
                'quote' => $awarded->quote,
                'company' => $c->id
            ));
            $this->quote_model->db->delete('backtrack');
            $key = md5($c->id . '--' . date('YmdHisu'));
            $insertarray = array(
                'quote' => $awarded->quote,
                'company' => $c->id,
                'senton' => date('Y-m-d'),
                'invitation' => $key
            );
            $insertarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->quote_model->db->insert('backtrack', $insertarray);

            $link = base_url() . 'home/backtrack/' . $key;
            $data['email_body_title'] = "Dear " . $c->title ;

		  	$data['email_body_content'] = "Please update us on the estimated delivery dates for the following still due items off of PO# " . $quote->ponum . ":  <br><br>
		    <a href='$link' target='blank'>$link</a>
		    And let us know the delivery date of remaining items.
		    ";
		  	$loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
            //$this->load->model('admin/settings_model');
            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);

            $this->email->from($settings['adminemail'], "Administrator");

            $toemail = $settings['adminemail'] . ',' . $c->primaryemail;
            $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
	        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
            $purchaseusers = $this->db->query($sql)->result();
            foreach ($purchaseusers as $pu) {
                $toemail = $toemail . ',' . $pu->email;
            }
            $this->email->to($toemail);
            //$this->email->to($settings['adminemail'] . ',' . $c->email);

            $this->email->subject('Backorder update for PO# ' . $quote->ponum);
            $this->email->message($send_body);
            $this->email->set_mailtype("html");
            $this->email->send();

            $notification = array(
                'quote' => $quote->id,
                'company' => $c->id,
                'ponum' => $quote->ponum,
                'category' => 'Backorder',
                'senton' => date('Y-m-d H:i'),
                'isread' => '0'
            );
            $notification['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->db->insert('notification', $notification);
        }
    }
    
    
    
    function sendawardemail($quoteid,$paystatus="")
    {
        $awarded = $this->quote_model->getawardedbid($quoteid);
        $quote = $awarded->quotedetails;
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $project = $this->project_model->get_projects_by_id($quote->pid);

       // notification to purchasing user
        
        $toemail = array();
        $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu WHERE qu.userid=u.id 
        AND qu.quote=" . $quote->id;
        $purchaseusers = $this->db->query($sql)->result();
        foreach ($purchaseusers as $pu) {
            $toemail[] = $pu->email;
        }
        
        if(count($toemail) > 0){
        $data['email_body_title']  = "Dear Admin";
		$data['email_body_content'] ="This email is to notify PO# {$quote->ponum} that is assigned to you is awarded.";
		$loaderEmail = new My_Loader();
        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
        $settings = (array) $this->settings_model->get_current_settings();
        $this->load->library('email');
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from($settings['adminemail'], "Administrator");      
        $this->email->to(implode(',' , $toemail));
        $this->email->subject('Award PO notification for PO# ' . $quote->ponum);
        $this->email->message($send_body);
        $this->email->set_mailtype("html");
        $this->email->send();
        }

        //print_r($awarded);die;
        $companies = array();
        foreach ($awarded->items as $item) {
            if (!isset($companies[$item->companydetails->id])) {
                $companies[$item->companydetails->id] = array();
                $companies[$item->companydetails->id]['id'] = $item->companydetails->id;
                $companies[$item->companydetails->id]['title'] = $item->companydetails->title;
                $companies[$item->companydetails->id]['primaryemail'] = $item->companydetails->primaryemail;
                $companies[$item->companydetails->id]['contact'] = $item->companydetails->contact;
                $companies[$item->companydetails->id]['invoicenote'] = $item->companydetails->invoicenote;
                $companies[$item->companydetails->id]['items'] = array($item);
            } else {
                $companies[$item->companydetails->id]['items'][] = $item;
            }
        }
        //print_r($companies);die;
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        if (!class_exists('TCPDF')) {
        	require_once($config['base_dir'] . 'application/libraries/tcpdf/config/lang/eng.php');
        	require_once($config['base_dir'] . 'application/libraries/tcpdf/tcpdf.php');
        }
        $this->db->where('id',$this->session->userdata('purchasingadmin'));
        $cpa = $this->db->get('users')->row();
        foreach ($companies as $company) {
			$showpaystatus = "";
			if($paystatus=="fullpaid")
				$showpaystatus = " PAID IN FULL ";
            
			$pdfhtml = '
				<table width="100%" cellspacing="2" cellpadding="2">
				  <tr>
				    <td width="33%" align="left" valign="top">
				    <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></th>
				        </tr>
				      <tr>
				        <td width="33%" valign="top">Project Title</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $project->title . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Address</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $project->address . '</td>
				      </tr>
				    </table>
				    </td>
				    <td width="10" align="left" valign="top">&nbsp;</td>
				    <td width="65%" align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Purchase Order Information</strong></font></th>
			          </tr>
				      <tr>
				        <td width="33%" valign="top">PO#</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $quote->ponum . '</td>
				      </tr>				      
				      <tr>
				        <td valign="top">PO# Date</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->podate . '</td>
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
				        <td width="65%" valign="top">' . $company['contact'] . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Company</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $company['title'] . '</td>
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
				        <td>' . $awarded->shipto . '</td>
				      </tr>
				    </table></td>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">
				    
				    <table width="100%" cellspacing="0" cellpadding="4">				     
				      <tr>
				        <td>' . $showpaystatus. '</td>
				      </tr>
				    </table>   
				    
				    </td>
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
				    <th bgcolor="#000033"><font color="#FFFFFF">Item Image</font></th>
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
            
            foreach ($company['items'] as $item) {
            	
            	$totalprice += $item->ea * ((@$item->invoice_type != "fullpaid")? ((@$item->invoice_type == "alreadypay")?0:$item->quantity):$item->aiquantity);
                    
                $quantity = (@$item->invoice_type != "fullpaid")? ((@$item->invoice_type == "alreadypay")?0:$item->quantity):$item->aiquantity;
            	
                if(@$item->item_img && file_exists("./uploads/item/".$item->item_img))
                { 
            		$imgName = site_url('uploads/item/'.$item->item_img); 
                }	
            	else 
            	{
            		$imgName = site_url('uploads/item/big.png'); 
            	}
            	
                $pdfhtml.='<tr nobr="true">
					    <td style="border: 1px solid #000000;">' . ++$i . '</td>
					    <td style="border: 1px solid #000000;"><img src="'.$imgName.'" width="80" height="80"></td>
					    <td style="border: 1px solid #000000;">' . htmlentities($item->itemname) . '</td>
					    <td style="border: 1px solid #000000;">' . ($item->willcall?'For Pickup/Will Call':$item->daterequested) . '</td>
					    <td style="border: 1px solid #000000;">' . $quantity . '</td>
					    <td style="border: 1px solid #000000;">' . $item->unit . '</td>
					    <td align="right" style="border: 1px solid #000000;">$ ' . $item->ea . '</td>
					    <td align="right" style="border: 1px solid #000000;">$ ' . $totalprice . '</td>
					  </tr>
					  ';
                //$totalprice += $item->totalprice;
            }
            $config = (array) $this->settings_model->get_current_settings();
            $config = array_merge($config, $this->config->config);
            $taxtotal = $totalprice * $config['taxpercent'] / 100;
            $grandtotal = $totalprice + $taxtotal;

            $arradditionalcal = array();
            if($awarded->invoices){ 
        			
            	foreach ($awarded->invoices as $awinv){
            		
            		
            		$disocunt = 0;
            		if(@$awinv->discount_percent){

            			$arradditionalcal[] = ' Discount Expires on: '.@$awinv->discount_date;
            			$arradditionalcal[] = 'Discount('.$awinv->discount_percent.' %)';
            			$disocunt = round(($grandtotal*$awinv->discount_percent/100),2);
            			$arradditionalcal[] = - $disocunt;

            			$grandtotal = $grandtotal - ($grandtotal*$awinv->discount_percent/100);
            		}

            		if(@$invoice->penalty_percent){

            			$arradditionalcal[] = "";
            			$arradditionalcal[] = 'Penalty('.$awinv->penalty_percent.' %)';
            			$arradditionalcal[] = + (($grandtotal*$awinv->penalty_percent/100)*$awinv->penaltycount);
            			$grandtotal = $grandtotal + (($grandtotal*$awinv->penalty_percent/100)*$awinv->penaltycount);
            		}

            	}
        	
        	}
            
            
            $pdfhtml.='<tr>
            <td colspan="4" rowspan="4">
            <div style="width:70%">
            <br/>
            <h4 class="semi-bold">Terms and Conditions</h4>
             <p>'.$companies[$item->companydetails->id]['invoicenote'].'</p>
            <h5 class="text-right semi-bold">Thank you for your business</h5>
            </div>
            </td>
            <td>&nbsp;</td>
            <td align="right">Subtotal</td>
            <td align="right">$ ' . number_format($totalprice, 2) . '</td>
            </tr>
            <tr>
            <td>&nbsp;</td>
            <td align="right">Tax</td>
            <td align="right">$ ' . number_format($taxtotal, 2) . '</td>
            </tr>';
            
            if(count(@$arradditionalcal)>0){
            	$pdfhtml .='<tr>
                		<td>'.$arradditionalcal[0].'</td>
					    <td align="right">'.$arradditionalcal[1].'</td>
					    <td align="right">$ ' . $arradditionalcal[2] . '</td>
					  </tr>
					';
            }
            
            
            $pdfhtml .='<tr>
            <td>&nbsp;</td>
            <td align="right">Total</td>
            <td align="right">$ ' . number_format($grandtotal, 2) . '</td>
            </tr></table>
            ';

            
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('');
            $pdf->SetTitle('');
            $pdf->SetSubject('');

            $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));

            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(true);

            $pdf->SetHeaderData('', '', $cpa->companyname . '', 'Purchase Order');

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
            $pdfname = $config['base_dir'] . 'uploads/pdf/' . $quote->ponum . '_' . $company['id'] . '_accept.pdf';
            $pdf->Output($pdfname, 'f');
            $link = '<a href="' . site_url('quote/track/' . $quote->id) . '"></a>';
            $data['email_body_title']  = "Please find the attachment for your Purchase order (PO#: " . $quote->ponum . ").<br/><br/>";
            $data['email_body_content'] = "You have been awarded by " . $cpa->companyname . ".  for PO#: " . $quote->ponum . ".<br/>";
            
            if($paystatus=="fullpaid")
            $data['email_body_content'] .= "<br> This Order has been Paid in Full <br/>";
            
            $loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->clear(true);
            $this->email->from($settings['adminemail'], "Administrator");

            $toemail = $settings['adminemail'] . ',' . $company['primaryemail'];
            $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
	        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
            $purchaseusers = $this->db->query($sql)->result();
            foreach ($purchaseusers as $pu) {
                $toemail = $toemail . ',' . $pu->email;
            }
            $this->email->to($toemail);
		
           
            $this->email->subject('Your Purchase order for PO#:' . $quote->ponum);
            $this->email->message($send_body);
            $this->email->set_mailtype("html");
            $this->email->attach($pdfname);
            $this->email->send();

            $notification = array(
                'quote' => $quote->id,
                'company' => $company['id'],
                'ponum' => $quote->ponum,
                'category' => 'Award',
                'senton' => date('Y-m-d H:i'),
                'isread' => '0'
            );
            $notification['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->db->insert('notification', $notification);
        }
    }
    
    

    function sendcontractawardemail($quoteid,$contract='')
    {
    	if($contract=='contract')
    	$awarded = $this->quote_model->getawardedcontractbid($quoteid);
    	else 
        $awarded = $this->quote_model->getawardedbid($quoteid);
        $quote = $awarded->quotedetails;
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $project = $this->project_model->get_projects_by_id($quote->pid);

        // notification to purchasing user
        $data['email_body_title']  = "Dear Admin";

		$data['email_body_content'] ="This email is to notify PO# {$quote->ponum} that is assigned to you is awarded.
		    ";
		$loaderEmail = new My_Loader();
        $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
        $settings = (array) $this->settings_model->get_current_settings();
        $this->load->library('email');
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from($settings['adminemail'], "Administrator");

        $toemail = array();
        $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
        $purchaseusers = $this->db->query($sql)->result();
        foreach ($purchaseusers as $pu) {
            $toemail[] = $pu->email;
        }
        $this->email->to(implode(',' , $toemail));
        //$this->email->to($settings['adminemail'] . ',' . $c->email);
        $this->email->subject("You've been Awarded Contract:" . $quote->ponum);
        $this->email->message($send_body);
        $this->email->set_mailtype("html");
        $this->email->send();

        //echo "<pre>",print_r($awarded);die;
        $companies = array();
        foreach ($awarded->items as $item) {
        	if (!isset($companies[$item->companydetails->id])) {
        		$companies[$item->companydetails->id] = array();
        		if($contract =='contract'){
        			
        			$companies[$item->companydetails->id]['id'] = $item->companydetails->id;
        			$companies[$item->companydetails->id]['title'] = $item->companydetails->companyname;
        			$companies[$item->companydetails->id]['email'] = $item->companydetails->email;
        			$companies[$item->companydetails->id]['contact'] = $item->companydetails->username;        			        			
        			
        		}else {
        			
        			$companies[$item->companydetails->id]['id'] = $item->companydetails->id;
        			$companies[$item->companydetails->id]['title'] = $item->companydetails->title;
        			$companies[$item->companydetails->id]['primaryemail'] = $item->companydetails->primaryemail;
        			$companies[$item->companydetails->id]['contact'] = $item->companydetails->contact;
        			$companies[$item->companydetails->id]['invoicenote'] = $item->companydetails->invoicenote;
        		}
        		$companies[$item->companydetails->id]['items'] = array($item);
        	} else {
        		$companies[$item->companydetails->id]['items'][] = $item;
        	}
        }
        // echo "<pre>",print_r($companies); echo "conractval=".$contract;
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        if (!class_exists('TCPDF')) {
        	require_once($config['base_dir'] . 'application/libraries/tcpdf/config/lang/eng.php');
        	require_once($config['base_dir'] . 'application/libraries/tcpdf/tcpdf.php');
        }
        $this->db->where('id',$this->session->userdata('purchasingadmin'));
        $cpa = $this->db->get('users')->row();
        foreach ($companies as $company) {
            $pdfhtml = '
				<table width="100%" cellspacing="2" cellpadding="2">
				  <tr>
				    <td width="33%" align="left" valign="top">
				    <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></th>
				        </tr>
				      <tr>
				        <td width="33%" valign="top">Project Title</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $project->title . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Address</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $project->address . '</td>
				      </tr>
				    </table>
				    </td>
				    <td width="10" align="left" valign="top">&nbsp;</td>
				    <td width="65%" align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>';
            		   if($contract=="contract")
				       $pdfhtml .= '<th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Contract Information</strong></font></th>';
				       else
				       $pdfhtml .= '<th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Purchase Order Information</strong></font></th>';
				       
			          $pdfhtml .= '</tr>
				      <tr>';

			           if($contract=="contract")
				       $pdfhtml .= '<td width="33%" valign="top">Title</td>';
				       else 
				       $pdfhtml .= '<td width="33%" valign="top">PO#</td>';
				       
				       $pdfhtml .= '<td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $quote->ponum . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Subject</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->subject . '</td>
				      </tr>
				      <tr>';
				       
				      if($contract=="contract")
				      $pdfhtml .= ' <td valign="top">Award Date</td>';
				      else 
				      $pdfhtml .= ' <td valign="top">PO# Date</td>';
				      
				      $pdfhtml .= '<td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->podate . '</td>
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
				      <tr>';
				      
				    if($contract=="contract")
				      $pdfhtml .= '<td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Contractor</strong></font></td>';
				    else 
				      $pdfhtml .= '<td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Supplier</strong></font></td>';
				      
				      $pdfhtml .= '</tr>
				      <tr>
				        <td width="33%" valign="top">Contact</td>
				        <td width="2%" valign="top">&nbsp;</td>
				        <td width="65%" valign="top">' . $company['contact'] . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Company</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $company['title'] . '</td>
				      </tr>
				    </table></td>
				  </tr>
				  <tr>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				  </tr>
				  <tr>
				    <td align="left" valign="top">';
				      if($contract!="contract") {
				      	$pdfhtml .= '<table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr><td bgcolor="#000033"><font color="#FFFFFF"><strong>Ship to</strong></font></td>
				      </tr>
				      <tr>
				        <td>' . $awarded->shipto . '</td>
				      </tr>
				    </table>';
				      }
	                $pdfhtml .= '&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				  </tr>

			</table>';
				
				if($contract=="contract") {
					$pdfhtml .='<table width="100%" cellspacing="0" cellpadding="4">
				  <tr>
	              <td>Contract Details:</td>
	              </tr>
	             </table>';
				} else {
					$pdfhtml .='<table width="100%" cellspacing="0" cellpadding="4">
				  <tr>
	              <td>PO Details:</td>
	              </tr>
	             </table>';
				}
	             $pdfhtml .='<br/>';
				if($contract=="contract") {
				
					$pdfhtml .='<table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				  <thead>
				  <tr>
				    <th bgcolor="#000033"><font color="#FFFFFF">Filename</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>				   
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit Price</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Total Price</font></th>
				  </tr>
				  </thead>';						
				
				}else {
					
					$pdfhtml .='<table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				  <thead>
				  <tr>
				    <th bgcolor="#000033"><font color="#FFFFFF">Item No</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Item Image</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Date Requested</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Quantity</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit Price</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Total Price</font></th>
				  </tr>
				  </thead>
				  ';
					
				}
            $i = 0;
            $totalprice = 0;
            
            if($contract=="contract") {
            
            	foreach ($company['items'] as $item) {
                $pdfhtml.='<tr nobr="true">
					    <td style="border: 1px solid #000000;">' . $item->attach . '</td>
					    <td style="border: 1px solid #000000;">' . htmlentities($item->itemname) . '</td>					    
					    <td align="right" style="border: 1px solid #000000;">$ ' . $item->ea . '</td>
					    <td align="right" style="border: 1px solid #000000;">$ ' . $item->totalprice . '</td>
					  </tr>
					  ';
                	$totalprice += $item->totalprice;
            	}            
            
            }else{
            	
            	foreach ($company['items'] as $item) 
            	{
            		if(@$item->item_img && file_exists("./uploads/item/".$item->item_img))
	                { 
	            		$imgName = site_url('uploads/item/'.$item->item_img); 
	                }	
	            	else 
	            	{
	            		$imgName = site_url('uploads/item/big.png'); 
	            	}
            		$pdfhtml.='<tr nobr="true">
					    <td style="border: 1px solid #000000;">' . ++$i . '</td>
					    <td style="border: 1px solid #000000;"><img src="'.$imgName.'" width="80" height="80"></td>
					    <td style="border: 1px solid #000000;">' . htmlentities($item->itemname) . '</td>
					    <td style="border: 1px solid #000000;">' . (@$item->willcall)?'For Pickup/Will Call':@$item->daterequested . '</td>
					    <td style="border: 1px solid #000000;">' . (@$item->quantity?$item->quantity:"") . '</td>
					    <td style="border: 1px solid #000000;">' . (@$item->unit?$item->unit:"") . '</td>
					    <td align="right" style="border: 1px solid #000000;">$ ' . $item->ea . '</td>
					    <td align="right" style="border: 1px solid #000000;">$ ' . $item->totalprice . '</td>
					  </tr>
					  ';
            		$totalprice += $item->totalprice;
            	}
            }
            
            $config = (array) $this->settings_model->get_current_settings();
            $config = array_merge($config, $this->config->config);
            $taxtotal = $totalprice * $config['taxpercent'] / 100;
            $grandtotal = $totalprice + $taxtotal;

            $pdfhtml.='<tr>
            <td colspan="2" rowspan="3">
            <div style="width:70%">
            <br/>
            <h4 class="semi-bold">Terms and Conditions</h4>';
            
            if($contract !='contract')
            	$pdfhtml.='<p>'.$companies[$item->companydetails->id]['invoicenote'].'</p>';
            
            $pdfhtml.='<h5 class="text-right semi-bold">Thank you for your business</h5>
            </div>
            </td>
            <td align="right">Subtotal</td>
            <td align="right">$ ' . number_format($totalprice, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Tax</td>
            <td align="right">$ ' . number_format($taxtotal, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Total</td>
            <td align="right">$ ' . number_format($grandtotal, 2) . '</td>
            </tr></table>
            ';

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('');
            $pdf->SetTitle('');
            $pdf->SetSubject('');

            $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));

            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(true);
			if($contract=='contract')
            $pdf->SetHeaderData('', '', $cpa->companyname . '', 'Contract Award');
            else 
            $pdf->SetHeaderData('', '', $cpa->companyname . '', 'Purchase Order');

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
            $pdfname = $config['base_dir'] . 'uploads/pdf/' . $quote->ponum . '_' . $company['id'] . '_accept.pdf';
            $pdf->Output($pdfname, 'f');
            $link = '<a href="' . site_url('quote/track/' . $quote->id) . '"></a>';
            $data['email_body_title']  = "Please find the attachment for your Awarded Contract (Contract: " . $quote->ponum . ").<br/><br/>";
            $data['email_body_content'] = "You have been awarded Contract:" . $quote->ponum  . ".  by " . $cpa->companyname . ".<br/>";
            $loaderEmail = new My_Loader();
            $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->email->clear(true);
            $this->email->from($settings['adminemail'], "Administrator");
			if(isset($company['email']))
				$emailid = $company['email'];
			else 
				$emailid = $company['primaryemail'];
            $toemail = $settings['adminemail'] . ',' . $emailid;
            $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
	        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
            $purchaseusers = $this->db->query($sql)->result();
            foreach ($purchaseusers as $pu) {
                $toemail = $toemail . ',' . $pu->email;
            }
            $this->email->to($toemail);

            if($contract=="contract") {
            	$this->email->subject('You were Awarded Contract:' . $quote->ponum); 
            }else{
              $this->email->subject('Your Purchase order for PO#:' . $quote->ponum); 
            } 
            $this->email->message($send_body);
            $this->email->set_mailtype("html");
            $this->email->attach($pdfname);
            $this->email->send();

            $notification = array(
                'quote' => $quote->id,
                'company' => $company['id'],
                'ponum' => $quote->ponum,
                'category' => 'Award',
                'senton' => date('Y-m-d H:i'),
                'isread' => '0'
            );
            $notification['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->db->insert('notification', $notification);
        }
    }

    function findcostcode()
    {
        $codes = $this->quote_model->findcostcode($_GET['term']);
        $items = array();
        foreach ($codes as $code) {
            $items[] = $code->code;
        }
        echo json_encode($items);
    }

    function finditemcode()
    {
    	  //log_message('debug',"texxxxxxxxxto");
        $codes = $this->quote_model->finditemcode($_GET['term']);        
        $items = array();
        foreach ($codes as $code) {
            $item = array();
            if(@$code->item_img && file_exists("./uploads/item/".$code->item_img)) 
            $imgName = site_url('uploads/item/'.$code->item_img); 
            else 
            $imgName = site_url('uploads/item/big.png'); 
            $item['value'] = $code->itemcode;
            $item['label'] = '<span onmouseover=\'showspanimage("'.$imgName.'",'.$code->id.');\' onkeypress=\'showspanimage("'.$imgName.'",'.$code->id.');\'><!--<font color="#990000">-->'.$code->itemcode.'<!--</font>--> - '.$code->itemname.'</span> <span class="imgspcls" id="imgsp'.$code->id.'"></span>';
            $item['desc'] = $code->itemname;
            $items[] = $item;
            //$items[]= $code->itemcode.'<br/>'.$code->itemname;
        }
        echo json_encode($items);
    }
    function getitembycode()
    {
        $code = $_POST['code'];
        if(isset($_POST['projectid']))
        $projectid = $_POST['projectid'];
        else 
        $projectid = "";
        //fwrite(fopen("sql.txt","a+"),print_r($code,true));
        $item = $this->quote_model->finditembycode($code);
		if(@$item->itemid){
		$this->db->where('itemid',$item->itemid);
		$this->db->where('type','Purchasing');
		$this->db->where('company',$this->session->userdata('purchasingadmin'));
		$companyitem = $this->db->get('companyitem')->row();
        //print_r($companyitem);
		}
		
		if(@$companyitem)
		{
			if($companyitem->projectid){
				$arrproj = explode(",",$companyitem->projectid);

				if($companyitem->projectid != -1 && in_array($projectid,$arrproj)){
					$this->db->where('companyitemid',$companyitem->id);
					$companyprojectitem = $this->db->get('company_projectitem_notes')->row();
					if($companyprojectitem)
					$item->notes = $companyprojectitem->companynotes;
					else
					$item->notes = $companyitem->companynotes;
				}else
				$item->notes = $companyitem->companynotes;
			}else
				$item->notes = $companyitem->companynotes;

			//$item->notes = $companyitem->companynotes;
			$item->item_img = $companyitem->filename;
		}

        //fwrite(fopen("sql.txt","a+"),print_r($code,true));
        echo json_encode($item); // die;
    }

    function makedefaultcostcode() {
        if (!$_POST)
            die;
        if (!$_POST['defaultcostcode'])
            die;
        $temp = $_POST;
        $this->session->set_userdata($temp);
    }

    function updateitemnamewithcode()
    {
        if (!$_POST)
            die;
        if (!$_POST['itemid'] || !$_POST['itemname'])
            die;
        if ($this->session->userdata('usertype_id') == 2) {
            $this->quote_model->db->where('type', 'Purchasing');
            $this->quote_model->db->where('id', $_POST['itemid']);
            $this->quote_model->db->where('company', $this->session->userdata('id'));
            $this->quote_model->db->update('item', $_POST);
        } elseif ($this->session->userdata('usertype_id') == 1) {
            $this->quote_model->db->where('id', $_POST['itemid']);
            unset($_POST['itemid']);
            $this->quote_model->db->update('item', $_POST);
        }
    }

    function delete($id) {
        $quote = $this->quote_model->get_quotes_by_id($id);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $this->quote_model->remove_quote($id);
        redirect('admin/quote/index/' . $this->session->userdata('managedproject'), 'refresh');
    }

    function _set_fields() {
        $fields ['id'] = 'id';
        $fields ['pid'] = 'pid';
        $fields ['potype'] = 'potype';
        $fields ['ponum'] = 'ponum';
        $fields ['podate'] = 'PO Date';
        $fields ['duedate'] = 'Due Date';
        $fields ['startdate'] = 'startdate';
        $fields ['company'] = 'company';
        $fields ['subject'] = 'subject';
        $fields ['deliverydate'] = 'delivery date';
        $fields ['subtotal'] = 'sub total';
        $fields ['taxtotal'] = 'tax total';
        $fields ['total'] = 'total';
        $this->validation->set_fields($fields);
    }

    function _set_rules() {
        $rules ['ponum'] = 'trim|required';
        $rules ['potype'] = 'trim|required';
        $this->validation->set_rules($rules);
        $this->validation->set_message('required', '* required');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }

    // Start ON 21st jan 2014
    function getcompany_ajax() 
    {
        $localresult = isset($_POST['localresult']) ? $_POST['localresult'] : '';
        $radiusval = isset($_POST['radiusval']) ? $_POST['radiusval'] : '';
        $id = isset($_POST['id']) ? $_POST['id'] : '';

        $arr = array();
        $sql = "SELECT * FROM " . $this->db->dbprefix('company') . " WHERE 1=1 AND isdeleted=0";
        if ($localresult == 1) 
        {
            $lat = $this->quote_model->getcomplat($this->session->userdata('id'));
            $lng = $this->quote_model->getcomplong($this->session->userdata('id'));
            $sql_radius = "SELECT  *,(3963.191 * acos( cos( radians({$lat}) ) * cos( radians( `com_lat` ) ) * cos( radians( `com_lng` ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( `com_lat` ) ) ) ) AS distance FROM " . $this->db->dbprefix('company') . " HAVING distance <= {$radiusval} ORDER BY distance ASC";

            $sql_radius = $this->db->query($sql_radius);
            $dist = $sql_radius->result();
            
            foreach ($dist as $ret) 
            {
                array_push($arr, $ret->id, true);
            }
                       
            $network_arr = array();
            $this->db->select('company');
            $this->db->group_by("company");
         	$network_user=$this->db->get_where('network',array('purchasingadmin'=>$this->session->userdata('purchasingadmin')))->result();       
         	if($network_user!="")
         	{
         		foreach ($network_user as $n)
         		{        		
         		 array_push($network_arr, $n->company, true);
         		}
         	}
         	
         	if(!empty($network_arr))
         	{
            $result=array_diff($arr,$network_arr);
         	}
         	else 
         	{
         	$result = $arr;	
         	}
                     
            if (!empty($result)) {
                $arr1 = implode(',', $result);
                $sql .= " and id IN ($arr1)";
            } else {
                return '';
            }
                       
        }
     
        $str = '';

        $query = $this->db->query($sql);
        if ($query->result()) {
            $invited = $this->quote_model->getInvited($id);

            $companylist = $query->result();
            $i = 0;
            foreach ($companylist as $c) {
                if (!in_array($c->id, $invited)) {
                    $i++;
                    $str.= '<input type="checkbox" class="nonexist" value="' . $c->id . '" />&nbsp;&nbsp;' . $c->title . '<br/>';
                }
            }
            echo $str;
        } else {
            echo $str;
            exit;
            ;
        }
    }
    
    
    function getcompany_ajax1() 
    {
        $supplyresult = $_POST['supplyresult'];
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $arr = array();
        
        //echo "<pre>"; print_r($supplyresult); die;
        if($supplyresult==1)
        {
          $sql = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        	  WHERE c.id=n.company AND c.isdeleted='0' AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
        }
        else 
        {
          //$sql = "SELECT * FROM " . $this->db->dbprefix('company') . " WHERE 1=1 AND isdeleted=0";                 
          	$this->db->select('company');
         	$nu=$this->db->get_where('network',array('purchasingadmin'=>$this->session->userdata('purchasingadmin')))->result();       
         	if($nu!="")
         	{
         	 $dd="";
         		foreach ($nu as $n)
         		{        		
         		$dd .=$n->company.",";
         		}
         	}
         	$stmt="AND 1=1";
            if($dd!="")
         	{
         		$dd=trim($dd,",");
         		$stmt="AND c.id not in(".$dd.") AND n.purchasingadmin='{$this->session->userdata('purchasingadmin')}'";
         	}    
         
      		$sql = "SELECT c.id,c.title FROM " . $this->db->dbprefix('company') . " c, ".$this->db->dbprefix('network')." n  where c.isdeleted=0 {$stmt} group by c.id";
          
          
        }
     
        $str = '';
        $query = $this->db->query($sql);
        if ($query->result()) {
            $invited = $this->quote_model->getInvited($id);

            $companylist = $query->result();
            $i = 0;
            foreach ($companylist as $c) {
                if (!in_array($c->id, $invited)) {
                    $i++;
                    $str.= '<input type="checkbox" class="invite" value="' . $c->id . '" />&nbsp;&nbsp;' . $c->title . '<br/>';
                }
            }
            echo $str;
        } else {
            echo $str;
            exit;
            ;
        }
    }

    function sendduedatealert()
    {
    	$SQL = "SELECT c.* FROM " . $this->db->dbprefix('company') . " c WHERE c.id=".$_POST['companyid'];
    	$qry = $this->db->query($SQL);
    	$result = $qry->result_array();

    	$settings = (array)$this->settings_model->get_current_settings ();
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->clear(true);
	    $this->email->from($settings['adminemail'], "Administrator");

        $toemail = isset($result[0]['primaryemail']) ? $result[0]['primaryemail'] : '';

        $this->email->to($toemail);

		$data['email_body_title'] = "Dear ".$result[0]['title'];
		$data['email_body_content'] = "Please set due date for PO#  {$_POST['ponum']}<br><br><br>";

   		$data['email_body_content'].= site_url('quote/track/'.$_POST['quote'].'/'.$_POST['award']);
   		$loaderEmail = new My_Loader();
   		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$this->email->subject("Request to Set Due Date");
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->send();


		$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Request due date alert sent via email.</div></div>');

      die;
    }
    
    
    
    function sendcontractduedatealert()
    {
    	$SQL = "SELECT u.* FROM " . $this->db->dbprefix('users') . " u WHERE u.id=".$_POST['companyid'];
    	$qry = $this->db->query($SQL);
    	$result = $qry->result_array();

    	$settings = (array)$this->settings_model->get_current_settings ();
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->clear(true);
	    $this->email->from($settings['adminemail'], "Administrator");

        $toemail = isset($result[0]['email']) ? $result[0]['email'] : '';

        $this->email->to($toemail);

		$data['email_body_title'] = "Dear ".$result[0]['companyname'];
		$data['email_body_content'] = "Please set due date for Contract#  {$_POST['ponum']}<br><br><br>";

   		$data['email_body_content'].= site_url('admin/quote/trackpurchaser/'.$_POST['quote'].'/'.$_POST['award']);
   		$loaderEmail = new My_Loader();
   		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$this->email->subject("Request to Set Due Date");
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->send();


		$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Request due date alert sent via email.</div></div>');

      die;
    }
    
    // ITEM PDF
	/*function items_pdf($quoteid)
	{
		$company =  $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
	
		$quote = $this->quotemodel->getquotebyid($quoteid);
		$bid = $this->db->where('quote',$quoteid)->where('company',$company)->get('bid')->row();
		$award = $this->quote_model->getawardedcontractbid($quoteid);
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
			$this->db->where('company',$company);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
				if($companyitem->itemcode)
					$ai->itemcode = $companyitem->itemcode;
				if($companyitem->itemname)
					$ai->itemname = $companyitem->itemname;
			}
			$data['allawardeditems'][] = $ai;
			if($ai->company == $company)
				$itemswon++;
			else
				$itemslost++;
		}
		
		$purchaser = $this->quote_model->getpurchaseuserbyid($company);
		$data['itemswon'] = $itemswon;
		$data['itemslost'] = $itemslost;
		$data['quote'] = $quote;
		$data['bid'] = $bid;
		$data['award'] = $award;
		$data['company'] = $purchaser;
			
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
	
		$header[] = array('<b>Files</b>' , '<b>Description</b>','<b>Price</b>' , '<b>Total</b>' , '<b>Requested</b>');
			
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
			$header[] = array($ai->attach , $ai->itemname,'$ '. $ai->ea.chr(160) , '$ '.round($ai->totalprice,2).chr(160),$ai->daterequested);
		}
		$headername = "BID PERFORMANCE";
    	createOtherPDF('Contract_Quote_items_', $header,$headername);
    	die();
	 
	
		//===============================================================================
	
	}*/
	
	function items_pdf($quoteid)
	{
	
	
    	$awarded = $this->quote_model->getawardedcontractbid($quoteid);
    	
        $quote = $awarded->quotedetails;
        
        $project = $this->project_model->get_projects_by_id($quote->pid);

        //echo "<pre>",print_r($awarded);die;
        $companies = array();
        foreach ($awarded->items as $item) {
        	if (!isset($companies[$item->companydetails->id])) {
        		$companies[$item->companydetails->id] = array();

        		$companies[$item->companydetails->id]['id'] = $item->companydetails->id;
        		$companies[$item->companydetails->id]['title'] = $item->companydetails->companyname;
        		$companies[$item->companydetails->id]['email'] = $item->companydetails->email;
        		$companies[$item->companydetails->id]['contact'] = $item->companydetails->username;


        		$companies[$item->companydetails->id]['items'] = array($item);
        	} else {
        		$companies[$item->companydetails->id]['items'][] = $item;
        	}
        }
        // echo "<pre>",print_r($companies); echo "conractval=".$contract;
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        if (!class_exists('TCPDF')) {
        	require_once($config['base_dir'] . 'application/libraries/tcpdf/config/lang/eng.php');
        	require_once($config['base_dir'] . 'application/libraries/tcpdf/tcpdf.php');
        }
        $this->db->where('id',$this->session->userdata('purchasingadmin'));
        $cpa = $this->db->get('users')->row();
        foreach ($companies as $company) {
            $pdfhtml2 = '
				<table width="100%" cellspacing="2" cellpadding="2">
				  <tr>
				    <td width="33%" align="left" valign="top">
				    <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></th>
				        </tr>
				      <tr>
				        <td width="33%" valign="top">Project Title</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $project->title . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Address</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $project->address . '</td>
				      </tr>
				    </table>
				    </td>
				    <td width="10" align="left" valign="top">&nbsp;</td>
				    <td width="65%" align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Contract Information</strong></font></th>
			          </tr>
				      <tr>
				        <td width="33%" valign="top">Title</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $quote->ponum . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Subject</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->subject . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Award Date</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->podate . '</td>
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
				        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Contractor</strong></font></td>
				      </tr>
				      <tr>
				        <td width="33%" valign="top">Contact</td>
				        <td width="2%" valign="top">&nbsp;</td>
				        <td width="65%" valign="top">' . $company['title'] . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Company</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $company['contact'] . '</td>
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
				        <td bgcolor="#000033"><font color="#FFFFFF"><strong>Contractor</strong></font></td>
				      </tr>
				      <tr>
				        <td>' . $awarded->shipto . '</td>
				      </tr>
				    </table></td>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				  </tr>

			</table>

				<table width="100%" cellspacing="0" cellpadding="4">
				  <tr>
	              <td>Contract Details:</td>
	              </tr>
	             </table>

	             <br/>

				<table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				  <thead>
				  <tr>
				    <th bgcolor="#000033"><font color="#FFFFFF">Filename</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>				   
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit Price</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Total Price</font></th>
				  </tr>
				  </thead>
				  ';
            $pdfhtml = "";
            $i = 0;
            $totalprice = 0;
            foreach ($company['items'] as $item) {
                $pdfhtml2 .='<tr nobr="true">
					    <td style="border: 1px solid #000000;">' . $item->attach . '</td>
					    <td style="border: 1px solid #000000;">' . htmlentities($item->itemname) . '</td>					    
					    <td align="right" style="border: 1px solid #000000;">$ ' . $item->ea . '</td>
					    <td align="right" style="border: 1px solid #000000;">$ ' . $item->totalprice . '</td>
					  </tr>
					  ';
                $totalprice += $item->totalprice;
            }
            $config = (array) $this->settings_model->get_current_settings();
            $config = array_merge($config, $this->config->config);
            $taxtotal = $totalprice * $config['taxpercent'] / 100;
            $grandtotal = $totalprice + $taxtotal;

            $pdfhtml2.='<tr>
            <td colspan="2" rowspan="3">
            <div style="width:70%">
            <br/>
            <h4 class="semi-bold">Terms and Conditions</h4>';
                    
            $pdfhtml2.='<h5 class="text-right semi-bold">Thank you for your business</h5>
            </div>
            </td>
            <td align="right">Subtotal</td>
            <td align="right">$ ' . number_format($totalprice, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Tax</td>
            <td align="right">$ ' . number_format($taxtotal, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Total</td>
            <td align="right">$ ' . number_format($grandtotal, 2) . '</td>
            </tr></table>
            ';

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('');
            $pdf->SetTitle('');
            $pdf->SetSubject('');

            $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));

            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(true);

            $pdf->SetHeaderData('', '', $cpa->companyname . '', 'Contract Award');

            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->AddPage('L', 'LETTER');

            $pdf->SetFont('helvetica', '', 8, '', true);
            $pdf->writeHTML($pdfhtml2, true, 0, true, true);
            //$pdf->AddPage();

            $pdf->lastPage();
            $pdfname = $config['base_dir'] . 'uploads/pdf/' . $quote->ponum . '_' . $company['id'] . '_BID PERFORMANCE.pdf';
            $pdf->Output($pdfname, 'I');
            //$pdfname = "BID PERFORMANCE";
            //createOtherPDF('Contract_Quote_items_', $pdfhtml,$pdfname);
        }
	}
	
	
	function items_export($quoteid)
	{
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
	
		$quote = $this->quotemodel->getquotebyid($quoteid);
		$bid = $this->db->where('quote',$quoteid)->where('company',$company)->get('bid')->row();
		$award = $this->quote_model->getawardedcontractbid($quoteid);
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
			$this->db->where('company',$company);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
				if($companyitem->itemcode)
					$ai->itemcode = $companyitem->itemcode;
				if($companyitem->itemname)
					$ai->itemname = $companyitem->itemname;
			}
			$data['allawardeditems'][] = $ai;
			if($ai->company == $company)
				$itemswon++;
			else
				$itemslost++;
		}
		$purchaser = $this->quote_model->getpurchaseuserbyid($company);
		$data['itemswon'] = $itemswon;
		$data['itemslost'] = $itemslost;
		$data['quote'] = $quote;
		$data['bid'] = $bid;
		$data['award'] = $award;
		$data['company'] = $purchaser;
			
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
		
		$header[] = array('Report Type' , 'BID Performance','' , '' , '' , '', '');
		
		$header[] = array('' , '','' , '' , '' , '', '');	
		$header[] = array('BID Performance :' , $quote->ponum,'' , '' , '' , '', '');
		$header[] = array('' , '','' , '' , '' , '', '');
		$header[] = array('Items Won :' , $itemswon,'' , '' , '' , '', '');
		$header[] = array('Items Lost :' , $itemslost,'' , '' , '' , '', '');
		$header[] = array('' , '','' , '' , '' , '', '');
		$header[] = array('' , '','' , '' , '' , '', '');
	
		$header[] = array('File Name' , 'Description','Price' , 'Total');
			
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
			$header[] = array($ai->attach , $ai->itemname, '$ '. $ai->ea.chr(160) , '$ '.round($ai->totalprice,2).chr(160));
		}
		createXls('Contract_Quote_items_'.$quoteid, $header);
		die();
	
		//===============================================================================
	
	}
	
	
	
	
	function trackpurchaser($quoteid,$award='')
	{
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
		
		$quote = $this->quotemodel->getquotebyid($quoteid);
		
		$awardeditems = $this->quotemodel->getawardeditems($award,$company);
		if(!$awardeditems)
			redirect('admin/quote/items/'.$quoteid);
		$data['awarditems'] = array();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company);
		$bid = $this->db->get('bid')->row();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company);
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
			$this->db->where('company',$company);
			$companyitem = $this->db->get('companyitem')->row();
			if($companyitem)
			{
			    if($companyitem->itemcode)
				    $ai->itemcode = $companyitem->itemcode;
				if($companyitem->itemname)
				    $ai->itemname = $companyitem->itemname;
			}
			
			if($ai->received < 100)
				$complete = false;
			if($ai->company != $company)
				$allawarded = false;
			if($ai->received > 0)
				$noitemsgiven = false;
			
			$ai->pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company)
			                        ->where('itemid',$ai->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;

			$ai->manualprogress = $this->db->select('SUM(quantity) manualprogress')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company)
			                        ->where('itemid',$ai->itemid)
			                        ->get()->row()->manualprogress;			                        
			            
		    $ai->manualprogress = $ai->manualprogress ? $ai->manualprogress : 0;             
            $ai->manualprogressbar = '<input id="progress' . $ai->id . '"  class="slider" style="width:200px;"
											 data-slider-id="progress' . $ai->id . '" type="text"
											 data-slider-min="0" value="' . $ai->manualprogress . '"
											 data-slider-max="100" data-slider-step="1"
											 data-slider-value="' . $ai->manualprogress . '"/>&nbsp;&nbsp;
											 <span id="progresslabel' . $ai->id . '">' . $ai->manualprogress . '%</span>';
           			
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
		
		$shipments = $this->db->select('shipment.*, quoteitem.itemname')
		             ->from('shipment')->join('quoteitem','shipment.itemid=quoteitem.id')
		             ->where('shipment.quote',$quoteid)->where('shipment.company',$company)
		             ->get()->result();
		
	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
		
		$invs = $this->quote_model->getcontractinvoices($company);
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
								
		$this->load->view('admin/trackpurchaser',$data);
	}
	
	
	
	function shipitems($quoteid,$awardid)
	{
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
			
		$quote = $this->quotemodel->getquotebyid($quoteid);
	    $awardeditems = $this->quotemodel->getawardeditems($awardid,$company);
	    
	    //first check if any item is trying to ship with quantity more than due.
	    foreach($awardeditems as $ai)
	    {
	        
		    $pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company)
			                        ->where('itemid',$ai->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;
			                        
			$acceptedshipments = $this->db->select('SUM(quantity) acceptedshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company)
			                        ->where('itemid',$ai->itemid)->where('accepted',1)
			                        ->get()->row()->acceptedshipments;			                        
			                        
			if(isset($_POST['quantity'.$ai->id]))                            
	        $quantity = ($_POST['quantity'.$ai->id] - $acceptedshipments - $pendingshipments);
	        else 
	        $quantity = 0;
	        //echo $quantity."-".$pendingshipments."-".$ai->received; die;
	        $invoicenum = $_POST['invoicenum'.$ai->id];
	        if( $quantity && $invoicenum && $quantity + $pendingshipments > (100 - $ai->received) )
	        {
	            //you cannot ship more than due quantity.
	            $this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">
You cannot ship more than due quantity, including pending shipments.</div></div></div>');
		        redirect('admin/quote/trackpurchaser/'.$quoteid.'/'.$awardid,'refresh');
	        }
	    }
	    $shipitems = '';
            $shippingDocInvouceNum = $_POST['invoicenum'.$awardeditems[0]->id];
	    foreach($awardeditems as $ai)
	    {
            $pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company)
			                        ->where('itemid',$ai->itemid)->where('accepted',0)
			                        ->get()->row()->pendingshipments;
			                        
			                        
			$acceptedshipments = $this->db->select('SUM(quantity) acceptedshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company)
			                        ->where('itemid',$ai->itemid)->where('accepted',1)
			                        ->get()->row()->acceptedshipments;			                        
			                        
			if(isset($_POST['quantity'.$ai->id]))                            
	        $quantity = ($_POST['quantity'.$ai->id] - $acceptedshipments  - $pendingshipments);
	        else 
	        $quantity = 0;
	        
	        $invoicenum = $_POST['invoicenum'.$ai->id];
	        if( $quantity && $invoicenum && $quantity <= 100 - $ai->received )
	        {
	            $arr = array();
	            $arr['quantity'] = $quantity;
	            $arr['invoicenum'] = $invoicenum;
	            $arr['purchasingadmin'] = $quote->purchasingadmin;
	            $arr['quote'] = $quote->id;
	            $arr['company'] = $company;
	            $arr['awarditem'] = $ai->id;
	            $arr['itemid'] = $ai->itemid;
	            $arr['shipdate'] = date('Y-m-d');
	            $arr['accepted'] = 0;
	            //print_r($arr);
	            $this->db->insert('shipment',$arr);
	            
	            if($pendingshipments)
	            $Pendingitemacceptance = $pendingshipments+$quantity;
	            else 
	            $Pendingitemacceptance = $quantity;
	            
	            $shipitems .= "<tr><td>{$ai->attach}</td><td>{$quantity}%</td><td>{$ai->itemname}</td><td>".(100 - $ai->received - $quantity)." ( ".$Pendingitemacceptance." Pending Acknowledgement )</td></tr>";
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
				$insert['company'] = $company;
				$insert['invoicenum'] = $shippingDocInvouceNum;
				$insert['uploadon'] = date('Y-m-d');
				
				$this->db->insert('shippingdoc',$insert);
			}
		}
		if($shipitems)
		{
			$pa = $this->db->where('id',$quote->purchasingadmin)->get('users')->row();
		    $shipitems = "<table cellpadding='5' cellspacing='5' border='1'><tr><th>File</th><th>Progress Update</th><th>Description</th><th>Quantity Remaining</th></tr>$shipitems</table>";
    	    $settings = (array)$this->homemodel->getconfigurations ();
    		$this->load->library('email');
    		$config['charset'] = 'utf-8';
    		$config['mailtype'] = 'html';
    		$this->email->initialize($config);
    		$purchaser = $this->quote_model->getpurchaseuserbyid($company);
    		$this->email->from($purchaser->email);
    		if($pa)
    		$this->email->to($pa->email);
    		$subject = 'Billing Submitted By Company';
    		
    		$data['email_body_title']  = "Company {$purchaser->companyname} has submitted a billing for Contract: {$quote->ponum} on ".date('m/d/Y');
    		$data['email_body_content'] = "<br><br>Details:".$shipitems;
    		$loaderEmail = new My_Loader();
    		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    		$this->email->subject($subject);
    		$this->email->message($send_body);
    		$this->email->set_mailtype("html");
    		$this->email->reply_to($purchaser->email);
    		$this->email->send();
		}
		redirect('admin/quote/trackpurchaser/'.$quoteid.'/'.$awardid);
	}
	
	
	
	function track_purchase_export($quoteid,$award='')
	{
				
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
		
		$quote = $this->quotemodel->getquotebyid($quoteid);
		
		$awardeditems = $this->quotemodel->getawardeditems($award,$company);
		if(!$awardeditems)
			redirect('admin/quote/contractitems/'.$quoteid);
		$data['awarditems'] = array();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company);
		$bid = $this->db->get('bid')->row();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company);
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
			$this->db->where('company',$company);
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
			if($ai->company != $company)
				$allawarded = false;
			if($ai->received > 0)
				$noitemsgiven = false;
			
			$ai->pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company)
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
		
		$shipments = $this->db->select('shipment.*, quoteitem.itemname')
		             ->from('shipment')->join('quoteitem','shipment.itemid=quoteitem.id')
		             ->where('shipment.quote',$quoteid)->where('shipment.company',$company)
		             ->get()->result();
		
	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
		
		$invs = $this->quotemodel->getinvoices($company);
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
			$header[] = array('Award Date',$order_date ,'','','','','','','');
		}
			
		if(isset($purchasingadmin->companyname))
		{
			$companyname_name =  $purchasingadmin->companyname;
			$header[] = array('Company',$companyname_name ,'','','','','','','');
		}
				
		$header[] = array('','' ,'','','','','','','');
		$header[] = array('Contract Progress',$quote->progress.'%'.chr(160) ,'','','','','','','');
		
		$header[] = array('','' ,'','','','','','','');
		
		
		
		
		
		
		
		$header[] = array('File','Description','Price','Total','Requested','Notes','Shipped','Due');
		
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
			
			//$due = $ai->quantity - $ai->received;
			$due = '100%';
			
			if($ai->pendingshipments)
			{
                 $due.=  $ai->pendingshipments.'(Pending Acknowledgement)';
            }
						
			$header[] = array($ai->attach,$ai->itemname, '$'.$ai->ea.chr(160) ,'$'.round($ai->totalprice,2).chr(160),$ai->daterequested,$ai->notes,$ai->received,$due);							
										
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
										
		createXls('Contract_performance', $header);  			
		die();	
		
		//===============================================================================
		
	}
	
	// TRACK PDF
	function track_purchase_pdf($quoteid,$award='')
	{
				
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
		
		$quote = $this->quotemodel->getquotebyid($quoteid);
		
		$awardeditems = $this->quotemodel->getawardeditems($award,$company);
		if(!$awardeditems)
			redirect('admin/quote/contractitems/'.$quoteid);
		$data['awarditems'] = array();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company);
		$bid = $this->db->get('bid')->row();
		
		$this->db->where('quote',$quote->id);
		$this->db->where('company',$company);
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
			$this->db->where('company',$company);
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
			if($ai->company != $company)
				$allawarded = false;
			if($ai->received > 0)
				$noitemsgiven = false;
			
			$ai->pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
			                        ->from('shipment')
			                        ->where('quote',$quoteid)->where('company',$company)
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
		
		$shipments = $this->db->select('shipment.*, quoteitem.itemname')
		             ->from('shipment')->join('quoteitem','shipment.itemid=quoteitem.id')
		             ->where('shipment.quote',$quoteid)->where('shipment.company',$company)
		             ->get()->result();
		
	    $settings = $this->settings_model->get_setting_by_admin ($quote->purchasingadmin);
		
		$invs = $this->quotemodel->getinvoices($company);
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
			$header[] = array('<b>Award Date</b>',$order_date ,'','','','','','','');
		}
			
		if(isset($purchasingadmin->companyname))
		{
			$companyname_name =  $purchasingadmin->companyname;
			$header[] = array('<b>Company</b>',$companyname_name ,'','','','','','','');
		}
				
		$header[] = array('','' ,'','','','','','','');
		$header[] = array('<b>Contract Progress</b>',$quote->progress.'%'.chr(160) ,'','','','','','','');
		
		$header[] = array('','' ,'','','','','','','');
		
		
		
		
		
		
		
		$header[] = array('<b>File</b>','<b>Description</b>','<b>Price</b>','<b>Total</b>','<b>Requested</b>','<b>Notes</b>','<b>Shipped</b>','<b>Due</b>');
		
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
			
			//$due = $ai->quantity - $ai->received;
			$due = '100%';
			
			if($ai->pendingshipments)
			{
                 $due.=  $ai->pendingshipments.'(Pending Acknowledgement)';
            }
						
			$header[] = array($ai->attach,$ai->itemname, '$'.$ai->ea.chr(160) ,'$'.round($ai->totalprice,2).chr(160), $ai->daterequested, $ai->notes, $ai->received,$due);								
										
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
	
	function savecontractfeedback()
    {
        $_POST['ratedate'] = date('Y-m-d');
        $_POST['purchasingadmin'] = $this->session->userdata('purchasingadmin');
		//print_r($_POST);die;
        $this->db->insert('quotefeedback',$_POST);
        redirect('admin/quote/contracttrack/'.$_POST['quote']);
    }
	
    
    function invoicedatedue()
	{
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
		    die;
		$_POST['datedue'] = date('Y-m-d', strtotime($_POST['datedue']));
		$this->db->where('invoicenum',$_POST['invoicenum'])->update('received',$_POST);
		
		$company = $this->session->userdata('purchasingadmin');
		if(!$company)
			redirect('admin/login');
		
		$invs = $this->quote_model->getinvoicesdetailsformail($company,$_POST['invoicenum']);
		$purchaser = $this->quote_model->getpurchaseuserbyid($company);
		$subject = "Due Date Set For Invoice ".$_POST['invoicenum'];
		$data['email_body_title']  = "";
		$data['email_body_content']  = "";
		$gtotal = 0;
		foreach ($invs as $invoice)
		{     		
			$olddate=strtotime($invoice->awardedon); $awarddate = date('m/d/Y', $olddate);
			$data['email_body_title'] .= 'Dear '.$invoice->username.' ,<br><br>';
			$data['email_body_content'] .= $invoice->supplierusername.' has set Due Date for Contract '.$_POST['invoicenum'].' from Contract# '.$invoice->ponum.', Awarded on '.$awarddate.' to Due on  '.$invoice->DueDate.'<br><br>';
			$data['email_body_content'] .= 'Please see Billing details below :<br>';
			$data['email_body_content'] .= '
					<table class="table table-bordered span12" border="1">
		            	<tr>
		            		<th>Billing</th>
		            		<th>Received On</th>
		            		<th>Company Name</th>
		            		<th>Company Address</th>
		            		<!--<th>Supplier Phone</th>
		            	    <th>Order Number</th> -->
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
            		<!--<td>'.$invoice->phone.'</td>
            		<td>'.$invoice->ponum.'</td> -->
            		<td>'.$invoice->itemname.'</td>
            		<td>'.$invoice->quantity.'</td>
            		<td>'.$invoice->paymentstatus.'</td>
            		<td>'.$invoice->status.'</td>
            		<td>'.$invoice->DueDate.'</td>
            		<td align="right">'.number_format($invoice->price,2).'</td>
	            	  </tr>';
	        $total = ($invoice->price);
            $gtotal+=$total;
	        $tax = $gtotal * $invoice->taxrate / 100;
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
		$this->email->from($purchaser->email,$purchaser->companyname);
		
		$this->email->subject($subject);
		$this->email->message($send_body);	
		$this->email->set_mailtype("html");
		$this->email->send();
		// echo "<pre>",print_r($data); die;
	}
	
	
	function receive($pid)
    {    	
        $temp['managedproject'] = $data['pid'] = $pid;
        //$this->load->model('admin/project_model');
        $temp['managedprojectdetails'] = $this->project_model->get_projects_by_id($pid);
        if ($this->session->userdata('usertype_id') == 2 && $temp['managedprojectdetails']->purchasingadmin != $this->session->userdata('id')) {
            //redirect('admin/dashboard', 'refresh');
        }
        $this->session->set_userdata($temp);

        $quotes = $this->quote_model->get_pendingshipment_quotes('',$pid);
       // echo "<pre>"; print_r($quotes); die;
        //$config ['total_rows'] = $this->quote_model->total_quote();

        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('ID', 'Name', 'Actions');
		// Save rating into award table
		if(isset($_POST['idBox'])){
			$pricerank = "";
			if($_POST['rate'] <=1.4)
			$pricerank = 'poor';
			if($_POST['rate'] >=1.5 && $_POST['rate'] <=2.4)
			$pricerank = 'fair';
			if($_POST['rate'] >=2.5 && $_POST['rate'] <=3.4)
			$pricerank = 'good';
			if($_POST['rate'] >=3.5 && $_POST['rate'] <=5)
			$pricerank = 'great';
			$updatearray = array();
			$updatearray['pricerank'] = $pricerank;
			$this->quote_model->db->where('quote', $_POST['idBox']);
			$this->quote_model->db->update('award', $updatearray);
		}
		
        $data['counts'] = count($quotes);

        $count = count($quotes);
        $items = array();
        $companyarr = array();
        $shipmentarray = array();
        if ($count >= 1) {
			
            foreach ($quotes as $quote) { //echo $quod = $this->quote_model->getbidsjag($quote->id);exit;
            	$shipments = array();
                $quote->invitations = $this->quote_model->getInvitedquote($quote->id);
                $quote->pendingbids = $this->quote_model->getbidsquote($quote->id);
                $quote->awardedbid = $this->quote_model->getawardedbidquote($quote->id);
                //print_r($quote->awardedbid);
                $quoteponum = $quote->ponum;
                $quote->pricerank = '-';
                if (!$quote->awardedbid)
                    $quote->pricerank = '-';
                elseif (!@$quote->awardedbid->items)
                    $quote->pricerank = '-';
                /*elseif (@$quote->awardedbid->quotedetails->potype == "Contract")
                    $quote->pricerank = '-';*/
                else {
                	
                	if(@$quote->awardedbid->quotedetails->potype == "Contract")
                    	$quote->ponum = '<a href="javascript:void(0)" onclick="viewcontractitems(\'' . $quote->id . '\')">' . $quote->ponum . '</a>';
                    else 
                    	$quote->ponum = '<a href="javascript:void(0)" onclick="viewitems(\'' . $quote->id . '\')">' . $quote->ponum . '</a>';
					/*
                    $totalcount = count($quote->awardedbid->items);
                    $lowcount = 0;
                    foreach ($quote->awardedbid->items as $ai) {
                        $itemlowest = $this->itemcode_model->getlowestquoteprice($ai->itemid);

                        if ($ai->ea <= $itemlowest)
                            $lowcount++;
                    }

                    if ($lowcount >= ($totalcount * 0.8))
                        $quote->pricerank = 'great';
                    elseif ($lowcount >= ($totalcount * 0.7))
                        $quote->pricerank = 'good';
                    elseif ($lowcount >= ($totalcount * 0.5))
                        $quote->pricerank = 'fair';
                    else
                        $quote->pricerank = 'poor';
                    */
                    if($quote->awardedbid->pricerank && (@$quote->awardedbid->quotedetails->potype != "Contract"))
                    {
                    	if ($quote->awardedbid->pricerank == 'great')
                    	$quote->pricerank = 4;
                    	elseif ($quote->awardedbid->pricerank == 'good')
                    	$quote->pricerank = 3;
                    	elseif ($quote->awardedbid->pricerank == 'fair')
                    	$quote->pricerank = 2;
                    	else
                    	$quote->pricerank = 1;
                    	
	                    $quote->pricerank = '<div class="fixedrating" data-average="'.$quote->pricerank.'" data-id="'.$quote->id.'"></div>';
	                    //$quote->pricerank = '<img src="'.site_url('templates/admin/images/rank'.$quote->pricerank.'.png').'"/>';
                	}                	
                }
                //$quote->awardedcompany = $quote->awardedbid?$quote->awardedbid->companyname:'-';
                $quote->podate = $quote->podate ? $quote->podate : '';
                $quote->status = $quote->awardedbid ? 'AWARDED' : ($quote->pendingbids ? 'PENDING AWARD' : ($quote->invitations ? 'NO BIDS' : ($quote->potype == 'Direct' ? '-' : 'NO INVITATIONS')));
                //echo '<pre>';print_r($quote->awardedbid);die;
                if ($quote->status == 'AWARDED') {

                	$shipmentsquery = "SELECT s.*, qi.itemname,i.item_img FROM " . $this->db->dbprefix('shipment') . " s left join ".$this->db->dbprefix('quoteitem')." qi on (s.itemid=qi.itemid and s.quote=qi.quote) LEFT JOIN ".$this->db->dbprefix('item')." i ON i.id=qi.itemid  where s.quote='{$quote->id}' and s.accepted = 0";
        			$shipments = $this->db->query($shipmentsquery)->result();
        			/*$shipments = $this->db->select('shipment.*, item.itemname')
		             ->from('shipment')->join('item','shipment.itemid=item.id','left')
		             ->where('quote',$quote->id)->where('shipment.accepted',0)->get()->result();*/

                	if($shipments)
                	  {  
                		if(@$quote->awardedbid->quotedetails->potype == "Contract") 
                		   {               	
                            $quote->status = $quote->status . ' - ' . strtoupper($quote->awardedbid->status).'<br> *Billing(s) Pending Acceptance'; 
                	       }
                        else 
                           {
                    	    $quote->status = $quote->status . ' - ' . strtoupper($quote->awardedbid->status).'<br> *Shipment(s) Pending Acceptance <a id="hrefa_'.$quote->id.'" href="javascript:void(0)" onclick="previewshipment('.$quote->id.');">Preview Shipment</a><img height="15px;" width="15px;" id="imageholder_'.$quote->id.'" src="'.site_url('templates/admin/css/icons/plus.gif').'" >';
                           }
                	  }
                }
                /*$quote->actions = $quote->awardedbid?'':
                anchor('admin/quote/items/' . $quote->id, '<span class="icon-2x icon-search"></span>', array('class' => 'view', 'title' => 'view quote items'))
                ;
                 if (empty($quote->awardedbid)) {
                    $quote->actions .=

                            anchor('admin/quote/update/' . $quote->id, '<span class="icon-2x icon-edit"></span>', array('class' => 'update'))
                            . ' ' .
                            anchor('admin/quote/delete/' . $quote->id, '<span class="icon-2x icon-trash"></span>', array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"))
                    ;
                } else {
                    $quote->actions .= anchor('admin/quote/delete/' . $quote->id, '<span class="icon-2x icon-trash"></span>', array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"))
                    ;
                    
                }*/
                //$quote->sent ='';
                //if($quote->invitations && !$quote->awardedbid)	{
               // $quote->sent = '<div class="badgepos"><span class="badge badge-blue">' . count($quote->invitations) . '</span></div>';
                //}
                if ($quote->awardedbid) {
                    //$quote->actions.= ' ' .
                    //anchor ( 'admin/quote/bids/' . $quote->id, '<span class="icon-2x icon-search"></span> ', array ('class' => 'view','alt' => 'awarded bid','title' => 'awarded bid' ) )
                    //;
                    
                    if($quote->potype=='Contract'){
                		$quote->actions.= ' ' .
                            anchor('admin/quote/contracttrack/' . $quote->id, '<span class="label label-pink">Track</span> ', array('class' => 'view', 'alt' => 'awarded bid', 'title' => 'awarded bid'))
                    ;
                	}else {
                    
                    $quote->actions.= ' ' .
                            anchor('admin/quote/track/' . $quote->id, '<span class="label label-pink">Track</span> ', array('class' => 'view', 'alt' => 'awarded bid', 'title' => 'awarded bid'));
                	} 
                }
                //echo "<pre>id-"; print_r($quote->id); die;
                /*$quote->recived = '';
                if ($quote->pendingbids) {
                	if($quote->potype=='Contract'){
                		 $quote->recived = anchor('admin/quote/conbids/' . $quote->id, '<div class="badgepos"><span class="badge badge-red">' . count($quote->pendingbids) . '</span></div>', array('class' => 'view'));
                	}
                	else {
                    $quote->recived = anchor('admin/quote/bids/' . $quote->id, '<div class="badgepos"><span class="badge badge-red">' . count($quote->pendingbids) . '</span></div>', array('class' => 'view'))
                    ;
                }}
                $quote->actions .=
                        '<a href="javascript:void(0)" onclick="duplicate(\'' . $quote->id . '\')" ><span class="icon-2x icon-copy"></span></a>'
                ;
                if ($this->session->userdata('usertype_id') == 2) {
                    $quote->actions .=
                            ' <a href="javascript: void(0)" onclick="quotepermission(' . $quote->id . ',\'' . $quoteponum . '\')"><span class="icon-2x icon-key"></span></a>';
                    ;
                }*/

                if(isset($quote->awardedbid->items)) {
                	foreach ($quote->awardedbid->items as $item) {

                		if($item->company){
                			$companyarr[] = $item->company;
                		}
                	}
                }

                if (@$_POST['searchcompany']) {

					if(count($companyarr)>0){
						if(!in_array($_POST['searchcompany'],$companyarr)){
							continue;
						}
					}else
					continue;

                }
                if (@$_POST['postatus']) {
                    if ($quote->status == $_POST['postatus']) {
                        $items[] = $quote;
                    }
                } else {
                    $items[] = $quote;
                }
                if(count($shipments)>0)                
                $shipmentarray[] = $shipments;
            }
            $data['items'] = $items;
            $data['jsfile'] = 'quotereceivejs.php';
        } else {
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

        $data ['addlink'] = '';
        $data ['heading'] = "Shipments Pending Acknowledgement - " . $this->session->userdata('managedprojectdetails')->title;
        $data ['table'] = $this->table->generate();
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/quote/add/' . $pid . '">Add Quote</a>&nbsp;';
        $data ['addlink'].= '<a class="btn btn-green" href="' . base_url() . 'admin/quote/add/' . $pid . '/Direct">Add Purchase Order</a>&nbsp;';
        $data ['addlink'] .= '<a class="btn btn-green" href="' . base_url() . 'admin/quote/add/' . $pid . '/Contract">Add Contract Quote</a>&nbsp;';
        $mess= $this->session->flashdata('message');
        if(isset($mess) && $this->session->flashdata('message')!=""){
        	$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Permissions assigned.</div></div>');
        }
        
        $query1 = "SELECT code FROM ".$this->db->dbprefix('costcode')."
                  WHERE project='".$this->session->userdata('managedprojectdetails')->id."'";
        $data['costcodedata'] = $this->db->query($query1)->result();
        $sqlquery = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE project='".$this->session->userdata('managedprojectdetails')->id."' AND forcontract=1";
 		$data['contractcostcodes'] = $this->db->query($sqlquery)->result(); 
        $data['shipmentarray'] = $shipmentarray;
        
        $this->load->view('admin/receivelist', $data);
    }
    
    
        function get_contract_company_in_miles($miles="",$category) {
        
        $radiusval = $miles;
        $id = isset($_POST['id']) ? $_POST['id'] : '';

        $arr = array();
        $sql = "SELECT * FROM " . $this->db->dbprefix('users') . " WHERE 1=1 AND isdeleted='0'";
			
        	if($miles!="")
  			$having = "HAVING distance <= {$radiusval}";
  			else 
  			$having = "";
  			
            $lat = $this->quote_model->getcomplat($this->session->userdata('id'));
            $lng = $this->quote_model->getcomplong($this->session->userdata('id'));

            $sql_radius = "SELECT  *,(3963.191 * acos( cos( radians({$lat}) ) * cos( radians( `user_lat` ) ) * cos( radians( `user_lng` ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( `user_lat` ) ) ) ) AS distance 
                    FROM " . $this->db->dbprefix('users') . " 
                    {$having}                    
                    ORDER BY distance ASC";

            $sql_radius = $this->db->query($sql_radius);
            $dist = $sql_radius->result();

            foreach ($dist as $ret) {

                array_push($arr, $ret->id, true);
            }
            if (!empty($arr)) {
                $arr1 = implode(',', $arr);
                $sql .= " and id IN ($arr1)";
            } else {
                return '';
            }
           
           if($category) 
           $sql .= " and category=". $category;      
		   $query = $this->db->query($sql);
           $ret = $query->result();
           return $ret;	
    }
    
    
    function createbill() {
    	
    	//echo '<pre>',print_r($_POST);die;
    	
    	if(!$_POST)
            die;

	    $settings = (array)$this->homemodel->getconfigurations ();
	    $password = $this->getRandomPassword();                	
    	$username = str_replace(' ', '-', strtolower($_POST['customername']));                	
    	
	    $data['email_body_title'] = "";
	    $data['email_body_content'] = "";
	    $data['email_body_title'] = 'Bill';	 
	    if(@$_POST['customername'])   
		$email_body_content = " Dear ".$_POST['customername'].", Your bill details are as follows:<br/><br/>";
		else 
		$email_body_content = " Dear Customer, Your bill details are as follows:<br/><br/>";
		//$body .= "Type: ".$_POST['type']."<br/>";
		if(@$_POST['billname'])
		$data['email_body_content'] .= "Bill #Name: ".$_POST['billname']."<br/>";		
		if(@$_POST['customername'])
		$data['email_body_content'] .= "Name: ".$_POST['customername']."<br/>";		
		if(@$_POST['customeremail'])
		$data['email_body_content'] .= "Email: ".$_POST['customeremail']."<br/>";
		if(@$_POST['customeraddress'])
		$data['email_body_content'] .= "Address: ".$_POST['customeraddress']."<br/>";
		if(@$_POST['customerduedate'])
		$data['email_body_content'] .= "Due Date: ".date('m/d/Y', strtotime($_POST['customerduedate']))."<br/>";
		if(@$_POST['customerbillnote'])
		$data['email_body_content'] .= "Note: ".$_POST['customerbillnote']."<br/>";
		/*if(@$_POST['customerpaymenttype'])
		$data['email_body_content'] .= "Payment Type: ".$_POST['customerpaymenttype']."<br/>";
		if(@$_POST['customerpaypalemail'])
		$data['email_body_content'] .= "Paypal Email: ".$_POST['customerpaypalemail']."<br/>";*/
		if(@$_POST['markuptotalpercent'])
		$data['email_body_content'] .= "Mark up total %: ".$_POST['markuptotalpercent']."<br/>";
		/*if(@$_POST['markupitempercent'])
		$data['email_body_content'] .= "Mark up each item %: ".$_POST['markupitempercent']."<br/>";*/
		if(@$_POST['customerpayableto'])
		$data['email_body_content'] .= "Payable To: ".$_POST['customerpayableto']."<br/><br/>";

		if(@$_POST['customername'] && @$_POST['customerid'] == '')
		{
			$data['email_body_content'] .= "Invoice Portal: <br/>";		
			$data['email_body_content'] .= "User Name: ".$username."<br/>";		
			$data['email_body_content'] .= "Password: ".$password."<br/><br/>";					
		}
		else 
		{
			$customerRes = $this->db->where('id',@$_POST['customerid'])->get('customer')->result();
			$data['email_body_content'] .= "Invoice Portal: <br/>";		
			$data['email_body_content'] .= "User Name: ".$customerRes[0]->username."<br/>";		
			$data['email_body_content'] .= "Password: ".$customerRes[0]->plainpwd."<br/><br/>";	
		}
		
		$link1 = base_url() . 'company/customerlogin';
		
		$data['email_body_content'] .= "<a href='$link1' target='blank'>$link1</a>";
		
		$totalprice = 0;
		$subtotal = 0;
		$finaltotal = 0;
		$emailitems = '<table BORDER CELLPADDING="12">';
		$emailitems.= '<tr>';
		$emailitems.= '<th> Item Image  </th>';
		$emailitems.= '<th> Itemcode  </th>';
		$emailitems.= '<th>Itemname</th>';
		$emailitems.= '<th>Qty</th>';
		$emailitems.= '<th>Unit</th>';
		$emailitems.= '<th>Price</th>';
		$emailitems.= '<th>Total Price</th>';
		$emailitems.= '<th>Date Requested</th>';
		$emailitems.= '<th>Cost Code</th>';
		$emailitems.= '</tr>';		

		$emailitems1 = '';   
	
    	$awardedbid = $this->quote_model->getawardedbidquote($_POST['customerquoteid']);
		// echo "<pre>",print_r($awardedbid->items); die;
		
					if(!@$_POST['customerid'] || @$_POST['customerid']=="")
					{
						$custarray = array();
						
						$custarray['name'] = @$_POST['customername'];
						$custarray['email'] = @$_POST['customeremail'];
						$custarray['address'] = @$_POST['customeraddress'];
						$custarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
						$custarray['username'] = $username;
           				$custarray['password'] = md5($password);
           				$custarray['plainpwd'] = $password;
						$this->quote_model->db->insert('customer', $custarray);
						$custid = $this->quote_model->db->insert_id();	
					}else 
						$custid = $_POST['customerid'];
						
					$billarray = array();    		
					$billarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');		
                    $billarray['quote'] = $awardedbid->quote;                
                    $billarray['billname'] = @$_POST['billname'];    
                    $billarray['customerid'] = $custid;                    
                    $billarray['customerduedate'] = (@$_POST['customerduedate'])?date("Y-m-d", strtotime($_POST['customerduedate'])):"";
                    $billarray['customerbillnote'] = @$_POST['customerbillnote'];
                //    $billarray['customerpaymenttype'] = @$_POST['customerpaymenttype'];
                //    $billarray['customerpaypalemail'] = @$_POST['customerpaypalemail'];
                    $billarray['markuptotalpercent'] = @$_POST['markuptotalpercent'];
                    //$billarray['markupitempercent'] = @$_POST['markupitempercent'];     
                    $billarray['customerpayableto'] = @$_POST['customerpayableto'];                                   
                    $billarray['customerlogo'] = @$_POST['customerlogo'];       
					$billarray['billedon'] = date('Y-m-d H:i:s');
                    $billarray['project']=$this->session->userdata('managedprojectdetails')->id;
					
                    $this->quote_model->db->insert('bill', $billarray);
					$billid = $this->quote_model->db->insert_id();	
					
					$totPrice = 0;
					$serviceItemTax = 0;
					$serviceItemTaxTotal = 0;
					$str = '';
					$newstr = '';
					
					if(isset($_POST['servicelaboritem'])  && $billid != '')
					{
						foreach ($_POST['servicelaboritem'] as $k=>$val)
						{
							$insertArr = array('billid'=>$billid,
											   'servicelaboritems'=>(@$_POST['servicelaboritemname'][$k]) ? $_POST['servicelaboritemname'][$k] : '',
											   'price'=>(@$_POST['servicelaboritemprice'][$k]) ? $_POST['servicelaboritemprice'][$k] : '',
											   'tax'=>(@$_POST['servicelaboritemtax'][$k]) ? $_POST['servicelaboritemtax'][$k] : '',
											   'quantity'=>(@$_POST['servicelaboritemqty'][$k]) ? $_POST['servicelaboritemqty'][$k] : 1,
											   'purchasingadmin'=>$this->session->userdata('purchasingadmin')
											   
											   );
												
							$this->quote_model->db->insert('bill_servicelaboritems', $insertArr);			
								(@$_POST['servicelaboritemqty'][$k] == '') ? $qty = 1 : $qty = $_POST['servicelaboritemqty'][$k];
								$totPrice = @$_POST['servicelaboritemprice'][$k] * $qty;
								
								$emailitems1 .= '<tr>';
								$emailitems1 .= '<td colspan="6" style="padding-left:5; text-align:right;">'.@$_POST['servicelaboritemname'][$k].'</td>';
								$emailitems1 .= '<td style="padding-left:5;text-align:right;">$'.@$_POST['servicelaboritemprice'][$k].'</td><td>&nbsp;</td><td>&nbsp;</td>';
								$emailitems1 .= '</tr><tr>';
								$emailitems1 .= '<td colspan="6" style="padding-left:5; text-align:right;">Qty</td>';
								$emailitems1 .= '<td style="padding-left:5;text-align:right;">'.$qty.'</td><td>&nbsp;</td><td>&nbsp;</td>';
								$emailitems1 .= '</tr><tr><td colspan="6" style="padding-left:5; text-align:right;">Tax ('.@$_POST['servicelaboritemtax'][$k].' % )</td>';
								$emailitems1 .= '<td style="padding-left:5; text-align:right;">$'.($totPrice * (@$_POST['servicelaboritemtax'][$k]/100)).'</td><td>&nbsp;</td><td>&nbsp;</td></tr>';		
								$serviceItemTax += @$totPrice + (@$totPrice * (@$_POST['servicelaboritemtax'][$k]/100)) ;									
						}
						
					}
					
					$awarditemsarr = array();
					if(@$_POST['billawarditems']){
    				
    					$awarditemsarr = explode(",",$_POST['billawarditems']);	
    				
    				}
					
    	if($awardedbid->items && $billid){
    		
    		foreach($awardedbid->items as $item){
    			//echo "<pre>",print_r($items);
    			if($_POST['billingtype']=="all" || (count($awarditemsarr>0) && in_array($item->id,$awarditemsarr)) ){
    				$item = (array) $item;
    				$itemarray = array();
    				$itemarray['bill'] = $billid;
    				$itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                    $itemarray['award'] = $item['award'];
                    $itemarray['company'] = $item['company'];
                    $itemarray['itemid'] = $item['itemid'];
                    $itemarray['itemcode'] = $item['itemcode'];
                    $itemarray['itemname'] = $item['itemname'];
                    $itemarray['quantity'] = $item['quantity'];
                    $itemarray['unit'] = $item['unit'];
                    $itemarray['ea'] = $item['ea'];
                    $itemarray['totalprice'] = $item['quantity'] * $item['ea'];
                    $itemarray['daterequested'] = $item['daterequested'];
                    $itemarray['costcode'] = $item['costcode'];                    
                    
                    $this->quote_model->db->insert('billitem', $itemarray);
                    
                    if ($item['item_img'] && file_exists('./uploads/item/' . $item['item_img'])) 
					 { 
					 	 $imgName = site_url('uploads/item/'.$item['item_img']); 
					 } 
					 else 
					 { 
					 	 $imgName = site_url('uploads/item/big.png'); 
                     }
		                                     
                    $emailitems.= '<tr>';
                    $emailitems.= '<td style="padding-left:5;"><img src="'.$imgName.'"  width="80" height="80"></td>';
                    $emailitems.= '<td style="padding-left:5;">'.$item['itemcode'].'</td>';
                    $emailitems.= '<td style="padding-left:5;">'.$item['itemname'].'</td>';
                    $emailitems.= '<td style="padding-left:5;">'.$item['quantity'].'</td>';
                    $emailitems.= '<td style="padding-left:5;">'.$item['unit'].'</td>';
                    $emailitems.= '<td style="padding-left:5;">'.$item['ea'].'</td>';
                    $emailitems.= '<td style="padding-left:5;">'.$item['quantity'] * $item['ea'].'</td>';
                    $emailitems.= '<td style="padding-left:5;">'.date('m/d/Y', strtotime($item['daterequested'])).'</td>';
                    $emailitems.= '<td style="padding-left:5;">'.$item['costcode'].'</td>';
                    
                    $emailitems.= '</tr>';
                    $totalprice += $item['quantity'] * $item['ea'];
                    
    			}
    		}
    		
    	}
    	$settings = $this->settings_model->get_current_settings();
    	$emailitems.= '<tr>';    	
    	$emailitems.= '<td colspan="6" style="padding-left:5; text-align:right;">Markup Total ('.@$_POST['markuptotalpercent'].'%)</td>';
    	$emailitems.= '<td style="padding-left:5;text-align:right;">$'.number_format((@$totalprice*@$_POST['markuptotalpercent']/100),2).'</td>';
		$emailitems.= '<td style="padding-left:5;">&nbsp;</td>';
        $emailitems.= '<td style="padding-left:5;">&nbsp;</td>';
    	$emailitems.= '</tr>';
    	
    	$subtotal = @$totalprice + (@$totalprice*@$_POST['markuptotalpercent']/100);
    	
    	$emailitems.= '<tr>';    	
    	$emailitems.= '<td colspan="6" style="padding-left:5; text-align:right;">Subtotal</td>';
    	$emailitems.= '<td style="padding-left:5;text-align:right;">$'.number_format(@$subtotal,2).'</td>';    	
		$emailitems.= '<td style="padding-left:5;">&nbsp;</td>';
        $emailitems.= '<td style="padding-left:5;">&nbsp;</td>';
    	$emailitems.= '</tr>';
    	
    	
    	$emailitems.= '<tr>';    	
    	$emailitems.= '<td colspan="6" style="padding-left:5; text-align:right;">Tax</td>';
    	$emailitems.= '<td style="padding-left:5;text-align:right;">$'.number_format((@$totalprice*@$settings->taxrate/100),2).'</td>';
		$emailitems.= '<td style="padding-left:5;">&nbsp;</td>';
        $emailitems.= '<td style="padding-left:5;">&nbsp;</td>';
    	$emailitems.= '</tr>';
    	
    	$emailitems.= $emailitems1;
    	
    	$finaltotal = $serviceItemTax + $subtotal + (@$totalprice*@$settings->taxrate/100);
    	$emailitems.= '<tr>';    	
    	$emailitems.= '<td colspan="6" style="padding-left:5; text-align:right;">Total</td>';
    	$emailitems.= '<td style="padding-left:5;text-align:right;">'.number_format(@$finaltotal,2).'</td>';
		$emailitems.= '<td style="padding-left:5;">&nbsp;</td>';
        $emailitems.= '<td style="padding-left:5;">&nbsp;</td>';
    	$emailitems.= '</tr>';
  
    	$emailitems .= '</table>';
    
   
    	$data['email_body_content'] .= "<br><br>{$emailitems}";
    	$data['email_body_content2'] = $email_body_content." <br>".$data['email_body_content'];
    	$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
			
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
//		echo '<pre>',print_r($send_body);die;
	
		$this->email->initialize($config);
		$this->email->from($settings->adminemail);
		$this->email->to(@$_POST['customeremail']);
		$this->email->subject('Bill');
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->send();      	
		
		/*-------------------------------------------------------------------*/
		
		$config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        if (!class_exists('TCPDF')) {
        	require_once($config['base_dir'] . 'application/libraries/tcpdf/config/lang/eng.php');
        	require_once($config['base_dir'] . 'application/libraries/tcpdf/tcpdf.php');
        } 
         //$pdfhtml2 =$emailitems;
         $pdfhtml2 = $data['email_body_content'];
         $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('');
            $pdf->SetTitle('');
            $pdf->SetSubject('');

            $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));

            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(true);

            $pdf->SetHeaderData('', '', '', 'CUSTOMER BILL');

            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->AddPage('L', 'LETTER');

            $pdf->SetFont('helvetica', '', 8, '', true);
            $pdf->writeHTML($pdfhtml2, true, 0, true, true);
            $pdf->lastPage();
            $pdfname = $config['base_dir'] . 'uploads/pdf/bill_' .$billid .'.pdf';
            $pdf->Output($pdfname, 'f');
        /*-------------------------------------------------------------------*/
		
		
    	echo $billid;
    	
    }
    
    
    function billings()
    {
        /*$invoices = $this->quote_model->getinvoices();
        $count = count($invoices);*/
        $search = "";
        $searches = array();        
        if(!@$_POST)
 		{
 			$fromdate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );;
 			$todate = date('Y-m-d');
 			$searches[] = " (DATE(billedon) >= '$fromdate'
						AND DATE(billedon) <= '$todate')";
 		}
        
        if (@$_POST['searchcustomer']) {
            $searches[] = " b.customerid = '{$_POST['searchcustomer']}' ";
        }
        if (@$_POST['searchstatus']) {
            $searches[] = " b.status = '{$_POST['searchstatus']}' ";
        }
        if (@$_POST['searchpaymentstatus']) {
            $searches[] = " b.paymentstatus = '{$_POST['searchpaymentstatus']}' ";
        }
        if (@$_POST['searchinvoicenum']) {
            $searches[] = " b.billname LIKE '%{$_POST['searchinvoicenum']}%' ";
        }

        if (@$_POST['searchfrom'] && @$_POST['searchto']) {
            $fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
            $todate = date('Y-m-d', strtotime($_POST['searchto']));
            $searches[] = " (DATE(billedon) >= '$fromdate'
						AND DATE(billedon) <= '$todate')";
        } elseif (@$_POST['searchfrom']) {
            $fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
            $searches[] = " DATE(billedon) >= '$fromdate'";
        } elseif (@$_POST['searchto']) {
            $todate = date('Y-m-d', strtotime($_POST['searchto']));
            $searches[] = " DATE(billedon) <= '$todate'";
        }
        if ($this->session->userdata('usertype_id') > 1) {
            $searches[] = " b.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "' ";
        }
        if ($searches) {
            $search = "  AND (" . implode(" AND ", $searches) . " )";
        }
        
        $billquery = "select sum(bi.totalprice) as total, b.*, c.address, c.email, c.name as customername,ph.paymenttype,ph.refnum,ph.id as billhistoryid
        from ". $this->db->dbprefix('bill') ." b 
        left join ". $this->db->dbprefix('billitem') ." bi on b.id=bi.bill 
        left join ". $this->db->dbprefix('bill_payment_history') ." ph on b.id=ph.bill 
        left join ". $this->db->dbprefix('customer') ." c on b.customerid = c.id 
        where 1=1 AND project = {$this->session->userdata('managedprojectdetails')->id} {$search} group by bi.bill";
        
     
        $billqryeres = $this->db->query($billquery);
    
        $bills = $billqryeres->result();
        $count = count($bills);
        $items = array();
        if ($count >= 1)
        {
            $settings = $this->settings_model->get_current_settings();
            $available_statuses = array('pending', 'verified', 'error');
            $data['available_statuses'] = $available_statuses;
            foreach ($bills as $bill){
            	if($bill->id && $bill->purchasingadmin == $this->session->userdata('purchasingadmin') ){
           
                           
                $company = $this->db->select('company.*')->from('bill')
                           ->join('billitem','bill.id=billitem.bill')
                           ->join('company','billitem.company=company.id')
                           ->where('bill.id',$bill->id)
                           ->get()->row()
                           ;
                
                               
                           
                $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
                $bill->bankaccount = $bankaccount;

                if(@$bill->markuptotalpercent!="")
                $markuptotal = ($bill->total*$bill->markuptotalpercent/100);     
               
                $bill->companydetails = $company;
                $bill->total = $bill->total + ($bill->total*$settings->taxpercent/100);
                
                $serviceItems = 0;
                $serviceitemRes = $this->db->where('billid',$bill->id)->get('bill_servicelaboritems')->result_array();
                if(@$serviceitemRes)
                {
                	foreach ($serviceitemRes as $k=>$v)
                	{                     		
                		($v['quantity'] == '' || $v['quantity'] == 0) ? $qty = 1 : $qty =  $v['quantity'];           
                		$totPrice = $v['price'] * $qty;      	                		
                		$serviceItems += $totPrice + ($totPrice * $v['tax']/100);                    	
                	}	          	
                } 
                $bill->total = $bill->total + $serviceItems + $markuptotal;
                          
               	$payh = $this->db->select('sum(amountpaid) as amountpaid')->where('bill',$bill->id)->get('bill_payment_history')->row(); 
                
               	if($payh)
               	$bill->totaldue = number_format($bill->total - $payh->amountpaid,2);
               	else 
               	$bill->totaldue = $bill->total;
               	
               	if($payh)
               	$bill->totalpaid = number_format($payh->amountpaid,2);
               	else 
               	$bill->totalpaid = 0;
               	
               	
                $bill->actions = '<a href="javascript:void(0)" onclick="showBill(\'' . $bill->id . '\',\''.$bill->quote.'\')"><span class="icon-2x icon-search"></span></a>';
                
                /*$options = false;
                foreach ($available_statuses as $status_key => $status_text)
                {

                    if (strtolower($bill->status) == $status_text) {
                        $selected = " selected=\"selected\"";
                    } else {
                        $selected = '';
                    }
                    $options[] = "<option value=\"$status_text\" $selected>$status_text</option>";
                }
                $options_payment = array();
                $options_paymenttype = array();
                $options_payment[]="<option value=\"Paid\" ".($bill->paymentstatus=='Paid'?" selected=\"selected\"":'').">Paid</option>";;
               
                $options_payment[]="<option value=\"Unpaid\" ".($bill->paymentstatus=='Unpaid'||$bill->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Unpaid</option>";;

                $options_paymenttype[]="<option value=\"\">Select Payment Type</option>";
                if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
                //$options_paymenttype[]="<option value=\"Credit Card\" ".($bill->paymenttype=='Credit Card'?" selected=\"selected\"":'').">Credit Card</option>";
                $options_paymenttype[]="<option value=\"Cash\" ".($bill->paymenttype=='Cash'?" selected=\"selected\"":'').">Cash</option>";;
                $options_paymenttype[]="<option value=\"Check\" ".($bill->paymenttype=='Check'?" selected=\"selected\"":'').">Check</option>";;

                $txtrefnum = "<input type=\"text\" id=\"refnum_$bill->id\" name=\"refnum\" value=\"$bill->refnum\"/>";

                $txtamountpaid = "<input placeholder='Amount Paid' type=\"text\" id=\"amountpaid_$bill->id\" name=\"amountpaid\" value=\"$bill->amountpaid\"/>";
                
                $chkispaid = "<input type=\"text\" id=\"ispaid_$bill->id\" name=\"ispaid\" ".($bill->ispaid == '1'?" checked='checked'":'')." />";                
                $update_button = "<button onclick=\"update_invoice_status('$bill->id')\">update</button>";
                $update_payment_button = "<button onclick=\"update_bill_payment_status('$bill->id')\">update</button>";

                $status_html = "<select id=\"invoice_$bill->id\" name=\"status_element\">" . implode("", $options) . "</select>" . $update_button;

                $payment_status_html = "<select id=\"invoice_payment_$bill->id\" name=\"payment_status_element\">" . implode("", $options_payment) . "</select>";
                $payment_status_html .= "<select id=\"invoice_paymenttype_$bill->id\" name=\"paymenttype_status_element\" onchange=\"paycc(this.value,'".$bill->id."','".$bill->total."');\">" . implode("", $options_paymenttype) . "</select>";
                $payment_status_html .= $txtrefnum;
                $payment_status_html .= $txtamountpaid;
                $payment_status_html .= $chkispaid;
                $payment_status_html .= $update_payment_button;
                if($bill->paymentstatus=='Requested Payment')
                {                	
                   	$payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Supplier</i>';
                }*/

                //$bill->status_selectbox = $status_html;
                //$bill->payment_status_selectbox = $payment_status_html;

                $bill->total = number_format($bill->total,2);

                $items[] = $bill;
            }
          }

            $data['items'] = $items;
            $data['jsfile'] = 'invoicejs.php';
        } else {
        	$data['items'] = array();
            $data['message'] = 'No Records';
        } 
       
       // $data ['addlink'] = '';
        $data ['heading'] = 'Customer Bills';
               
        $query = "SELECT c.* 
        		  FROM ".$this->db->dbprefix('customer')." c 
        		  JOIN ".$this->db->dbprefix('bill')."   b ON c.id = b.customerid AND c.purchasingadmin=b.purchasingadmin
				  JOIN ".$this->db->dbprefix('project')."   p ON p.id = b.project AND c.purchasingadmin=p.purchasingadmin	
        		  WHERE c.purchasingadmin='".$this->session->userdata('purchasingadmin')."' AND p.id='".$this->session->userdata('managedprojectdetails')->id."'";
       
        $data['customers'] = $this->db->query($query)->result();
        /*
        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}
		
		/*Following code from Report controller.*/
		/*
		$reports = $this->report_model->get_reports1();	
		$count = count ($reports);
		$items = array();
		if ($count >= 1) 
		{
			foreach ($reports as $report) 
			{
				$items[] = $report;
			}
		    $data['reports'] = $items;
		    //$data['taxdata']=$settings->taxpercent;
		}
		if(!$items)
		{
		    $this->data['message'] = 'No Records';
		} */
		
		/*$payment_history = $this->db->select('bill_payment_history.*')->get('bill_payment_history')->result(); 
		$data['payment_history'] = $payment_history;*/
		$payment_history = $this->db->select('bill_payment_history.*')
									->from('bill_payment_history')
									->join('bill','bill.id=bill_payment_history.bill')
									->where('bill.project',$this->session->userdata('managedprojectdetails')->id)
									->get()->result(); 
		//echo "<pre>",print_r($payment_history);
		$data['payment_history'] = $payment_history;
		
		//echo "<pre>",print_r($_POST); die;
		if(@$_POST['message_hidden_div'])
		$data['message_hidden_div'] = $_POST['message_hidden_div'];
		
        $this->load->view('admin/billing', $data);
    }
    
    
    public function update_bill_payment_status()
    {
        //echo "<pre>",print_r($_POST);die;
        if($_POST['total_due_amount_value'] - $_POST['amountpaid'] == 0)
        $_POST['paymentstatus'] = 'Paid';
        else 
        $_POST['paymentstatus'] = 'Partial';
                
        $billarray = array();    		
		$billarray['status'] = "Pending";
		$billarray['paymentstatus'] = $_POST['paymentstatus'];
        $billarray['ispaid'] = $_POST['ispaid'];
        $this->db->where('id', $_POST['invoicenum']);
        $this->db->update('bill', $billarray);
      
        
        $billhistory = array();    		
		$billhistory['bill'] = $_POST['invoicenum'];
		$billhistory['paymenttype'] = $_POST['paymenttype'];
        $billhistory['paymentdate'] = date('Y-m-d');
        $billhistory['refnum'] = $_POST['refnum'];
        $billhistory['amountpaid'] = $_POST['amountpaid'];        
        $this->db->insert('pms_bill_payment_history', $billhistory);
        
        /*if($_POST['paymentstatus'] == 'Paid')
        {
    		$company = $this->db->select('company.*')
    		            ->from('received')
    		            ->join('awarditem','received.awarditem=awarditem.id')
    		            ->join('company','awarditem.company=company.id')
    		            ->where('invoicenum',$_POST['invoicenum'])
    		            ->get()->row();
    		$quote = $this->db->select('quote.*')
    		            ->from('received')
    		            ->join('awarditem','received.awarditem=awarditem.id')
    		            ->join('award','awarditem.award=award.id')
    		            ->join('quote','award.quote=quote.id')
    		            ->where('invoicenum',$_POST['invoicenum'])
    		            ->get()->row();

    		$pa = $this->db->where('id',$this->session->userdata('id'))->get('users')->row();

    		$data['email_body_title']  = "Dear " . @$company->title ;
    		$data['email_body_content'] =  $pa->companyname." sent payment for the Invoice#: ".$_POST['invoicenum'].";
    		The following information sent:
    		<br/>
    		PO# : ".$quote->ponum."
    		<br/>
    		Payment By : ".$pa->companyname."
    		<br/>
    		Payment Type : ".$_POST['paymenttype']."
    		<br/>
    		Payment Amount : ".$amount."
    		<br/>
    		Ref# : ".$_POST['refnum']."
    		<br/>
    		Payment Date: ".date('m/d/Y')."
    		<br><br>";
    		$loaderEmail = new My_Loader();
    		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
    		$this->load->library('email');
    		$config['charset'] = 'utf-8';
    		$config['mailtype'] = 'html';
    		$this->email->initialize($config);
    		$this->email->from($pa->email, $pa->companyname);
    		$this->email->to(@$company->title . ',' . @$company->primaryemail);
    		$this->email->subject('Payment made for the invoice: '.$_POST['invoicenum']);
    		$this->email->message($send_body);
    		$this->email->set_mailtype("html");
    		$this->email->send();
        }*/

        echo '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Billing Payment Status Changed.</div></div>';
    }
    
    
    function getcustomerdata(){
    	if(!@$_POST)
    	die;
    	
    	$id = $_POST['id'];
        $sql = "SELECT c.* FROM ".$this->db->dbprefix('customer')." c WHERE c.id=".$id;
        $query = $this->db->query($sql);
        $cust = $query->row();
        echo json_encode($cust);
    }
    
    
	function billdatedue()
	{
		if (!$this->session->userdata('id')) {
            die;
        }
		$company = $this->session->userdata('id');
		
		$_POST['customerduedate'] = date('Y-m-d', strtotime($_POST['customerduedate']));
		$this->db->where('id',$_POST['id'])->update('bill',$_POST);
				
		$invs = $this->quote_model->getbillsdetailsformail($_POST['id']);
		//echo "<pre>",print_r($invs); die;
		$subject = "Due Date Set For Invoice ".@$invs[0]->billname;
		$data['email_body_title']  = "";
		$data['email_body_content']  = "";
		$gtotal = 0;
				
		foreach ($invs as $invoice)
		{ 
		    $config = (array)$this->settings_model->get_setting_by_admin ($invoice->purchasingadmin);
		    $config = array_merge($config, $this->config->config); 		
			$olddate=strtotime($invoice->billedon); $awarddate = date('m/d/Y', $olddate);
			$data['email_body_title'] .= 'Dear '.$invoice->name.' ,<br><br>';
			$data['email_body_content'] .= @$this->session->userdata('companyname').' has set Due Date for Bill '.$invoice->billname.' Billed on '.$awarddate.' to Due on  '.$invoice->customerduedate.'<br><br>';
			$data['email_body_content'] .= 'Please see order details below :<br>';
			$data['email_body_content'] .= '
					<table class="table table-bordered span12" border="1">
		            	<tr>
		            		<th>Bill #</th>
		            		<th>Received On</th>
		            		<th>Company Name</th>
		            		<th>Company Address</th>
		            		<th>Company Phone</th>		            		
		            		<th>Item</th>
		            		<th>Quantity</th>
		            		<th>Payment Status</th>
		            		<th>Verification</th>
		            		<th>Due Date</th>
		            		<th>Price</th>
		            	</tr>';
			
	        $data['email_body_content'] .= '<td>'.$invoice->billname.'</td>
            		<td>'.$invoice->billedon.'</td>
            		<td>'.@$this->session->userdata('companyname').'</td>
            		<td>'.@$this->session->userdata('address').'</td>
            		<td>'.@$this->session->userdata('phone').'</td>            		
            		<td>'.$invoice->itemname.'</td>
            		<td>'.$invoice->quantity.'</td>
            		<td>'.$invoice->paymentstatus.'</td>
            		<td>'.$invoice->status.'</td>
            		<td>'.$invoice->customerduedate.'</td>
            		<td align="right">'.number_format($invoice->ea,2).'</td>
	            	  </tr>';
	        $total = $invoice->ea*$invoice->quantity;
            $gtotal+=$total;
	        $tax = $gtotal * $config['taxpercent'] / 100;
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
		$this->email->to(@$invs[0]->email);
		$this->email->from($this->session->userdata('companyname'),$this->session->userdata('companyname'));
		
		$this->email->subject($subject);
		$this->email->message($send_body);	
		$this->email->set_mailtype("html");
		$this->email->send();
	}
	
	
	function previewbill(){
		if(!$_POST)
		die;
		
		$awardedbid = $this->quote_model->getawardedbidquote($_POST['quote']);
		echo json_encode($awardedbid->items);
	}
	
	
	function checkuserexist(){
		
		if(!$_POST)
		die;
		
		$username = $_POST['username'];
		$username = str_replace(' ', '-', strtolower($username));  
        $sql = "SELECT c.id FROM ".$this->db->dbprefix('company')." c WHERE  c.isdeleted=0 and c.username='".$username."'";
        $query = $this->db->query($sql);
        $cust = $query->row();
        if($cust)
        echo 1;
        else 
        echo 0; die;
		
	}
		
	
	function checkemailexist(){
		
		if(!$_POST)
		die;
		
		$email = $_POST['email'];
		
        $sql = "SELECT c.id FROM ".$this->db->dbprefix('company')." c WHERE c.isdeleted=0 and c.primaryemail='".$email."'";
        $query = $this->db->query($sql);
        $cust = $query->row();
        if($cust)
        echo 1;
        else 
        echo 0; die;
		
	}
	
	
	
	function payquotebycc()
    {        
        //print_r($company);die;
        $qid = $_POST['invoicenum'];
        $bids = $this->quote_model->getbids($qid);

        $quote = $this->quote_model->get_quotes_by_id($qid);
        //echo '<pre>';print_r($qutoe);//die;
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {die;
        redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre> bids ';print_r($awarded);echo '</pre>';//die;
        if (!$bids) {
        	$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Bids Yet Placed.</div></div>');
        	redirect('admin/quote/update/' . $qid);
        }
        
		ini_set('max_execution_time', 300);
		$config = (array)$this->settings_model->get_current_settings();
		$config = array_merge($config, $this->config->config);
		$totalprice = 0;
		$currentpa = $this->session->userdata('site_loggedin')->id;
		
		foreach($bids as $bid)
        {        	
        	$this->db->where('company', $bid->company);
        	$this->db->where('purchasingadmin', $currentpa);
        	$creditonly = @$this->db->get('purchasingtier')->row()->creditonly;
        	
            foreach($bid->items as $item)
            {
            	if($item->postatus=='Accepted' && (@$creditonly==1)){                
            	$totalprice+= $item->quantity * $item->ea;
            	}
            	
            }
        }    
		$tax = number_format($totalprice*$config['taxpercent']/100,2);
		$totalprice = $totalprice+$tax;
		$totalprice = number_format($totalprice,2);
		
		require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
		Stripe::setApiKey($config['STRIPE_API_KEY']);
		//$myCard = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2015);
		$myCard = array('number' => $_POST['card'], 'exp_month' => $_POST['month'], 'exp_year' => $_POST['year']);
		$charge = Stripe_Charge::create(array('card' => $myCard, 'amount' => round($totalprice,2) * 100, 'currency' => 'usd' ));
		//echo $charge;
		$chargeobj = json_decode($charge);
		
		if(@$chargeobj->paid)
		{			
			$awardid = 0;	
      	$companiesamount = array();
		$ararditemarr = array();
        foreach($bids as $bid)
        {           	
        	$i=0;
            foreach($bid->items as $item)
            {
                if($item->postatus=='Accepted')
                {
                	
                	if($awardid==0){
                		
                		$awardarray = array();
                		$awardarray['quote'] = $qid;
                		$awardarray['shipto'] = @$_POST['shiptocopy'];
                		$awardarray['awardedon'] = date('Y-m-d H:i:s');
                		$awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                		$this->quote_model->db->insert('award', $awardarray);
                		$awardid = $this->quote_model->db->insert_id();
                		
                	}
                	
                    $item = (array) $item;                    
                    $itemarray['award'] = $awardid;
                    $itemarray['company'] = $bid->company;
                    $itemarray['itemid'] = $item['itemid'];
                    $itemarray['itemcode'] = $item['itemcode'];
                    $itemarray['itemname'] = $item['itemname'];
                    $itemarray['quantity'] = $item['quantity'];
                    $itemarray['unit'] = $item['unit'];
                    $itemarray['ea'] = $item['ea'];
                    $itemarray['totalprice'] = $item['quantity'] * $item['ea'];
                    $itemarray['daterequested'] = $item['daterequested'];
                    $itemarray['costcode'] = $item['costcode'];
                    $itemarray['notes'] = $item['notes'];
                    $itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');                   
                    
                    $this->quote_model->db->insert('awarditem', $itemarray);
					$awarditemid = $this->quote_model->db->insert_id();
					
					if(!isset($companiesamount[$bid->company]))
				    $companiesamount[$bid->company] = 0;
					$companiesamount[$bid->company] += $item['quantity'] * $item['ea'];
       				$ararditemarr[$bid->company][] = $awarditemid;
					
                }
                else
                {
                    $this->db->where('id',$item->id);
                    $this->db->delete('biditem');
                }
            }
        }
        
        foreach($companiesamount as $caid=>$amount){     
             
             $company = $this->db->select('company.*')->from('company')			
			->where('company.id',$caid)
			->get()->row();
					
			$amount = $amount + $amount*$config['taxpercent']/100;
			$amount=$amount-.55-($amount*2.9/100);
			$amount = round($amount,2);							
			
			$currentpa = $this->session->userdata('site_loggedin')->id;
            $this->db->where('company', $caid);
            $this->db->where('purchasingadmin', $currentpa);
            $creditonly = @$this->db->get('purchasingtier')->row()->creditonly;
			
			$bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
			if(!$bankaccount || !@$bankaccount->routingnumber || !@$bankaccount->accountnumber)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bank account missing for credit card payment.</div></div>');
				redirect('admin/quote/bids/'.$_POST['invoicenum']);
			}
			
			if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
			{
			  if(@$creditonly==1){	
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
              
              $transferObj = Stripe_Transfer::create(array(
                  "amount" => $amount * 100,
                  "currency" => "usd",
                  "recipient" => $obj->id,
                  "description" => "Transfer for ".$company->primaryemail )
              );
              $tobj = json_decode($transferObj);
                           
              
              foreach($ararditemarr[$caid] as $awarditemid){
              $update = array(
                          'purchasingadmin' => $this->session->userdata('purchasingadmin'),
              			  'quoteid' => $_POST['invoicenum'],	
              			  'company' => $caid,	
              			  'awardid' => $awardid,	
              			  'awarditem' => $awarditemid,	
                          'paymentstatus'=>'Paid',
                          'paymentdate' => date('Y-m-d'),
                          'paymenttype' =>'Credit Card',
                          'refnum'=>$chargeobj->balance_transaction,
                          'transfernum' => $tobj->id, 
                          'amount' => $amount
                          );
              
              $this->quote_model->db->insert('quote_payment', $update);                              
                          	
              $insertarray = array(
                          'purchasingadmin' => $this->session->userdata('purchasingadmin'),          			 
              			  'awarditem' => $awarditemid,	
                          'paymentstatus'=>'Paid',
                          'paymentdate' => date('Y-m-d'),
                          'paymenttype' =>'Credit Card',
                          'refnum'=>$chargeobj->id,
                          'invoicenum' => 'paid-in-full-already'.$awardid,
                          'quantity' => 0,
                          'status' => 'Verified',
                          'datedue' => date('Y-m-d'),
                          'invoice_type' => 'fullpaid',
                          'receiveddate' => date('Y-m-d H:i:s')
                          );                    
                          
              $this->quote_model->db->insert('received', $insertarray);        	  
                 
              }                           
              
    		  $quote = $this->db->select('quote.*')
    		            ->from('quote')    		            
    		            ->where('id',$_POST['invoicenum'])
    		            ->get()->row();

    		  $pa = $this->db->where('id',$this->session->userdata('id'))->get('users')->row();


              $data['email_body_title']  = "Dear {$company->title}";
$data['email_body_content'] = "$ {$amount} has been transfered to your bank account for invoice#paid-in-full-already{$awardid},
with the transfer# {$tobj->id}.
<br>Payment by: ".$pa->companyname."
<br>PO#: ".$quote->ponum."
";
$loaderEmail = new My_Loader();
              $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
              $settings = (array)$this->settings_model->get_current_settings ();
    	      $this->load->library('email');
    	      $config['charset'] = 'utf-8';
    	      $config['mailtype'] = 'html';
    	      $this->email->initialize($config);
              $this->email->from($settings['adminemail'], "Administrator");
              $this->email->to($company->primaryemail);
              $this->email->subject('Bank transfer notification from ezpzp');
              $this->email->message($send_body);
              $this->email->set_mailtype("html");
              $this->email->send();

              $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Payment done successfully & Bid awarded to the company</div></div>');
        	}
             
		  } 
        }      
		
        	$this->quote_model->db->where('quote', $_POST['invoicenum']);
            $this->quote_model->db->update('bid', array('complete' => 'Yes'));
            $this->sendawardemail($_POST['invoicenum'],'fullpaid');
        	
		}
		redirect('admin/quote/bids/'.$_POST['invoicenum']);
    }

    
    
    function payquotebidbycc(){    	
        //echo "<pre>",print_r($_POST); die;        
        if ($_POST['bidcopy'])
        {        	
            $bid = $this->quote_model->getbidbyid($_POST['bidcopy']);       
            
        if ($this->session->userdata('usertype_id') == 2 && $bid->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre>';print_r($bid);die;
        if (!$bid) {
            die;
        }
        
        
        ini_set('max_execution_time', 300);
		$config = (array)$this->settings_model->get_current_settings();
		$config = array_merge($config, $this->config->config);
	
		$currentpa = $this->session->userdata('site_loggedin')->id;
		$this->db->where('company', $bid->company);
		$this->db->where('purchasingadmin', $currentpa);
		$creditonly = @$this->db->get('purchasingtier')->row()->creditonly;
		
		$totalprice = 0;
		foreach ($bid->items as $item) {
			if(@$creditonly==1)
			$totalprice+= $item->quantity * $item->ea;
		} 		
		
		 $tax = number_format($totalprice*$config['taxpercent']/100,2);
 		 $totalprice = $totalprice+$tax;
 		 $totalprice = number_format($totalprice,2);
		
		require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
		Stripe::setApiKey($config['STRIPE_API_KEY']);
		//$myCard = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2015);
		$myCard = array('number' => $_POST['card'], 'exp_month' => $_POST['month'], 'exp_year' => $_POST['year']);
		$charge = Stripe_Charge::create(array('card' => $myCard, 'amount' => round($totalprice,2) * 100, 'currency' => 'usd' ));
		//echo $charge;
		$chargeobj = json_decode($charge);
		$qid = $_POST['invoicenum'];
		if(@$chargeobj->paid)
		{
        
        
        $awardarray = array();
        $awardarray['quote'] = $bid->quote;
        $awardarray['shipto'] = $_POST['shiptocopy'];
        $awardarray['awardedon'] = date('Y-m-d H:i:s');
        $awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $this->quote_model->db->insert('award', $awardarray);
        $awardid = $this->quote_model->db->insert_id();
		
		$companiesamount = array();
		$ararditemarr = array();
        foreach ($bid->items as $item) {
            $item = (array) $item;
            $itemarray = array();
            $itemarray['award'] = $awardid;
            $itemarray['company'] = $bid->company;
            $itemarray['itemid'] = $item['itemid'];
            $itemarray['itemcode'] = $item['itemcode'];
            $itemarray['itemname'] = $item['itemname'];
            $itemarray['quantity'] = $item['quantity'];
            $itemarray['unit'] = $item['unit'];
            $itemarray['ea'] = $item['ea'];
            $itemarray['totalprice'] = $item['quantity'] * $item['ea'];
            $itemarray['daterequested'] = $item['daterequested'];
            $itemarray['costcode'] = $item['costcode'];
            $itemarray['notes'] = $item['notes'];
            $itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');

            $this->quote_model->db->insert('awarditem', $itemarray);
			$awarditemid = $this->quote_model->db->insert_id();
            
            $this->db->where('itemid',$item['itemid']);
            $this->db->where('company',$bid->company);
            $this->db->where('type', 'Supplier');
            $companyitem = $this->db->get('companyitem')->row();
            if($companyitem){
            	$bd['qtyavailable'] = $companyitem->qtyavailable-$item['quantity'];
            	$this->db->where('id',$companyitem->id);
            	$this->db->update('companyitem',$bd);
            }
            
            if(!isset($companiesamount[$bid->company]))
            $companiesamount[$bid->company] = 0;
            $companiesamount[$bid->company] += $item['quantity'] * $item['ea'];
            
            /*if(!isset($ararditemarr[$bid->company]))
            $ararditemarr[$bid->company] = 0;

            if($ararditemarr[$bid->company] == 0)
            $ararditemarr[$bid->company] =   $awarditemid;
            else
            $ararditemarr[$bid->company] =   $ararditemarr[$bid->company].",".$awarditemid;*/
            
            $ararditemarr[$bid->company][] = $awarditemid;
            
        }
           foreach($companiesamount as $caid=>$amount){ 
            $company = $this->db->select('company.*')->from('company')			
			->where('company.id',$caid)
			->get()->row();
			
			$amount = $amount + $amount*$config['taxpercent']/100;
			$amount=$amount-.55-($amount*2.9/100);
			$amount = round($amount,2);
						
			$bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
			
			$currentpa = $this->session->userdata('site_loggedin')->id;
            $this->db->where('company', $caid);
            $this->db->where('purchasingadmin', $currentpa);
            $creditonly = @$this->db->get('purchasingtier')->row()->creditonly;
			
			if(!$bankaccount || !@$bankaccount->routingnumber || !@$bankaccount->accountnumber)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bank account missing for credit card payment.</div></div>');
				redirect('admin/quote/bids/'.$_POST['invoicenum']);
			}
			
			if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
			{	
			  if(@$creditonly==1){			  						
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
             
              $transferObj = Stripe_Transfer::create(array(
                  "amount" => $amount * 100,
                  "currency" => "usd",
                  "recipient" => $obj->id,
                  "description" => "Transfer for ".$company->primaryemail )
              );
              $tobj = json_decode($transferObj);
                           
              //echo $_POST['invoicenum'];
              //print_r($update);die;            
              foreach($ararditemarr[$caid] as $awarditemid){
              $update = array(
                          'purchasingadmin' => $this->session->userdata('purchasingadmin'),
              			  'quoteid' => $_POST['invoicenum'],	
              			  'company' => $bid->company,	
              			  'awardid' => $awardid,	
              			  'awarditem' => $awarditemid,	
                          'paymentstatus'=>'Paid',
                          'paymentdate' => date('Y-m-d'),
                          'paymenttype' =>'Credit Card',
                          'refnum'=>$chargeobj->balance_transaction,
                          'amount' => $amount
                          );
              
              $this->quote_model->db->insert('quote_payment', $update);                              
                          	
              $insertarray = array(
                          'purchasingadmin' => $this->session->userdata('purchasingadmin'),          			 
              			  'awarditem' => $awarditemid,	
                          'paymentstatus'=>'Paid',
                          'paymentdate' => date('Y-m-d'),
                          'paymenttype' =>'Credit Card',
                          'refnum'=>$chargeobj->id,
                          'transfernum' => $tobj->id, 
                          'invoicenum' => 'paid-in-full-already'.$awardid,
                          'quantity' => 0,
                          'status' => 'Verified',
                          'datedue' => date('Y-m-d'),
                          'invoice_type' => 'fullpaid',
                          'receiveddate' => date('Y-m-d H:i:s')
                          );                    
                          
              $this->quote_model->db->insert('received', $insertarray);        	  
                 
              }       
              
    		  $quote = $this->db->select('quote.*')
    		            ->from('quote')    		            
    		            ->where('id',$_POST['invoicenum'])
    		            ->get()->row();

    		  $pa = $this->db->where('id',$this->session->userdata('id'))->get('users')->row();


              $data['email_body_title']  = "Dear {$company->title}";
$data['email_body_content'] = "$ {$amount} has been transfered to your bank account for invoice#paid-in-full-already{$awardid},
with the transfer# {$tobj->id}.
<br>Payment by: ".$pa->companyname."
<br>PO#: ".$quote->ponum."
";
$loaderEmail = new My_Loader();
              $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
              $settings = (array)$this->settings_model->get_current_settings ();
    	      $this->load->library('email');
    	      $config['charset'] = 'utf-8';
    	      $config['mailtype'] = 'html';
    	      $this->email->initialize($config);
              $this->email->from($settings['adminemail'], "Administrator");
              $this->email->to($company->primaryemail);
              $this->email->subject('Bank transfer notification from ezpzp');
              $this->email->message($send_body);
              $this->email->set_mailtype("html");
              $this->email->send();

              $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Payment done successfully & Bid awarded to the company</div></div>');
			}
        	}
            
            
            
        }
        $this->quote_model->db->where('quote', $_POST['invoicenum']);
        $this->quote_model->db->update('bid', array('complete' => 'Yes'));
        $this->sendawardemail($_POST['invoicenum'],'fullpaid');
		}
        $quote = $this->quote_model->get_quotes_by_id($_POST['invoicenum']);     
       
       }elseif ($_POST['itemidscopy'])
       {
       	$quote = $this->quote_model->get_quotes_by_id($_POST['invoicenum']);
       	if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
       		redirect('admin/dashboard', 'refresh');
       	}
       	$itemids = $_POST['itemidscopy'];
       	//echo '<pre>';print_r($bid);die;
       	if (!$itemids) {
       		die;
       	}

       	$items = $this->quote_model->getbiditemsbyids($itemids);
       	//print_r($items);die;
       	if (!$items)
       	die;
       	
       	ini_set('max_execution_time', 300);
		$config = (array)$this->settings_model->get_current_settings();
		$config = array_merge($config, $this->config->config);

		$totalprice = 0;
		$currentpa = $this->session->userdata('site_loggedin')->id;
		
		foreach ($items as $item) {			
			
			$this->db->where('company', $item->company);
			$this->db->where('purchasingadmin', $currentpa);
			$creditonly = @$this->db->get('purchasingtier')->row()->creditonly;
			if(@$creditonly==1)
			$totalprice+= $item->quantity * $item->ea;
		} 		
		
		 $tax = number_format($totalprice*$config['taxpercent']/100,2);
 		 $totalprice = $totalprice+$tax;
 		 $totalprice = number_format($totalprice,2);
		
		
		require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
		Stripe::setApiKey($config['STRIPE_API_KEY']);
		//$myCard = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2015);
		$myCard = array('number' => $_POST['card'], 'exp_month' => $_POST['month'], 'exp_year' => $_POST['year']);
		$charge = Stripe_Charge::create(array('card' => $myCard, 'amount' => round($totalprice,2) * 100, 'currency' => 'usd' ));
		//echo $charge;
		$chargeobj = json_decode($charge);
		$qid = $_POST['invoicenum'];
		if(@$chargeobj->paid)
		{       	      	
       	
       	$awardarray = array();
       	$awardarray['quote'] = $_POST['invoicenum'];
       	$awardarray['shipto'] = $_POST['shiptocopy'];
       	$awardarray['awardedon'] = date('Y-m-d H:i:s');
       	$awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
       	$this->quote_model->db->insert('award', $awardarray);
       	$awardid = $this->quote_model->db->insert_id();		
		$companiesamount = array();
		$ararditemarr = array();
       	foreach ($items as $item) {
       		$item = (array) $item;
       		$itemarray = array();
       		$itemarray['award'] = $awardid;
       		$itemarray['company'] = $item['company'];
       		$itemarray['itemid'] = $item['itemid'];
       		$itemarray['itemcode'] = $item['itemcode'];
       		$itemarray['itemname'] = $item['itemname'];
       		$itemarray['quantity'] = $item['quantity'];
       		$itemarray['unit'] = $item['unit'];
       		$itemarray['ea'] = $item['ea'];
       		$itemarray['totalprice'] = $item['quantity'] * $item['ea'];
       		$itemarray['daterequested'] = $item['daterequested'];
       		$itemarray['costcode'] = $item['costcode'];
       		$itemarray['notes'] = $item['notes'];
       		$itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');

       		$this->quote_model->db->insert('awarditem', $itemarray);
			$awarditemid = $this->quote_model->db->insert_id();
       		
       		$this->db->where('itemid',$item['itemid']);
       		$this->db->where('company',$item['company']);
       		$this->db->where('type', 'Supplier');
       		$companyitem = $this->db->get('companyitem')->row();
       		if($companyitem){
       			$bd['qtyavailable'] = $companyitem->qtyavailable-$item['quantity'];
       			$this->db->where('id',$companyitem->id);
       			$this->db->update('companyitem',$bd);
       		}
       		
       		if(!isset($companiesamount[$item['company']]))
				    $companiesamount[$item['company']] = 0;
				$companiesamount[$item['company']] += $item['quantity'] * $item['ea'];
       			
				/*if(!isset($ararditemarr[$item['company']]))
       				$ararditemarr[$item['company']] = 0;
       			
       			if($ararditemarr[$item['company']] == 0)	
				$ararditemarr[$item['company']] =   $awarditemid;
				else 
				$ararditemarr[$item['company']] =   $ararditemarr[$item['company']].",".$awarditemid;*/
				
				$ararditemarr[$item['company']][] = $awarditemid;
       		}		
       		foreach($companiesamount as $caid=>$amount){
       		$company = $this->db->select('company.*')->from('company')			
			->where('company.id',$caid)
			->get()->row();
			
			$amount = $amount + $amount*$config['taxpercent']/100;
			$amount=$amount-.55-($amount*2.9/100);
			$amount = round($amount,2);
			
			$currentpa = $this->session->userdata('site_loggedin')->id;
            $this->db->where('company', $caid);
            $this->db->where('purchasingadmin', $currentpa);
            $creditonly = @$this->db->get('purchasingtier')->row()->creditonly;
			
			$bankaccount = $this->db->where('company',$caid)->get('bankaccount')->row();
			if( (!$bankaccount || !@$bankaccount->routingnumber || !@$bankaccount->accountnumber) && (@$creditonly==1) )
			{
				$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bank account missing for credit card payment.</div></div>');
				redirect('admin/quote/bids/'.$_POST['invoicenum']);
			}
			
			if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
			{
			  if(@$creditonly==1){							
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
              
              $transferObj = Stripe_Transfer::create(array(
                  "amount" => $amount * 100,
                  "currency" => "usd",
                  "recipient" => $obj->id,
                  "description" => "Transfer for ".$company->primaryemail )
              );
              $tobj = json_decode($transferObj);
                           
              //echo $_POST['invoicenum'];
              //print_r($update);die;            
              
              foreach($ararditemarr[$caid] as $awarditemid){
              $update = array(
                          'purchasingadmin' => $this->session->userdata('purchasingadmin'),
              			  'quoteid' => $_POST['invoicenum'],	
              			  'company' => $caid,	
              			  'awardid' => $awardid,	
              			  'awarditem' => $awarditemid,	
                          'paymentstatus'=>'Paid',
                          'paymentdate' => date('Y-m-d'),
                          'paymenttype' =>'Credit Card',
                          'refnum'=>$chargeobj->balance_transaction,
                          'amount' => $amount
                          );
              	
              $this->quote_model->db->insert('quote_payment', $update);    
              	
              $insertarray = array(
                          'purchasingadmin' => $this->session->userdata('purchasingadmin'),          			 
              			  'awarditem' => $awarditemid,	
                          'paymentstatus'=>'Paid',
                          'paymentdate' => date('Y-m-d'),
                          'paymenttype' =>'Credit Card',
                          'refnum'=>$chargeobj->id,
                          'transfernum' => $tobj->id, 
                          'invoicenum' => 'paid-in-full-already'.$awardid,
                          'quantity' => 0,
                          'status' => 'Verified',
                          'datedue' => date('Y-m-d'),
                          'invoice_type' => 'fullpaid',
                          'receiveddate' => date('Y-m-d H:i:s')
                          );                    
                          
              $this->quote_model->db->insert('received', $insertarray);        	  
                 
              }         
              
    		  $quote = $this->db->select('quote.*')
    		            ->from('quote')    		            
    		            ->where('id',$_POST['invoicenum'])
    		            ->get()->row();

    		  $pa = $this->db->where('id',$this->session->userdata('id'))->get('users')->row();


              $data['email_body_title']  = "Dear {$company->title}";
$data['email_body_content'] = "$ {$amount} has been transfered to your bank account for invoice#paid-in-full-already{$awardid},
with the transfer# {$tobj->id}.
<br>Payment by: ".$pa->companyname."
<br>PO#: ".$quote->ponum."
";
$loaderEmail = new My_Loader();
              $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
              $settings = (array)$this->settings_model->get_current_settings ();
    	      $this->load->library('email');
    	      $config['charset'] = 'utf-8';
    	      $config['mailtype'] = 'html';
    	      $this->email->initialize($config);
              $this->email->from($settings['adminemail'], "Administrator");
              $this->email->to($company->primaryemail);
              $this->email->subject('Bank transfer notification from ezpzp');
              $this->email->message($send_body);
              $this->email->set_mailtype("html");
              $this->email->send();

              $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Payment done successfully & Bid awarded to the company</div></div>');
        	} 		
       		
			}	
       	}
       	
       	$this->quote_model->db->where('quote', $_POST['invoicenum']);
       	$this->quote_model->db->update('bid', array('complete' => 'Yes'));
       	$this->sendawardemail($_POST['invoicenum'],'fullpaid');
       	
       	
       	
		}

       	$quote = $this->quote_model->get_quotes_by_id($_POST['invoicenum']);
       }
        if($quote)
        {
        	$quote->awardedbid = $this->quote_model->getawardedbid($quote->id);

        	if(@$quote->awardedbid->items)
        	{
		        $totalcount = count($quote->awardedbid->items);
		        $lowcount = 0;
		        foreach ($quote->awardedbid->items as $ai)
		        {
		        	$itemlowest = $this->itemcode_model->getlowestquoteprice($ai->itemid);

		        	if ($ai->ea <= $itemlowest)
		        		$lowcount++;
		        }

		        if ($lowcount >= ($totalcount * 0.8))
		        	$quote->pricerank = 'great';
		        elseif ($lowcount >= ($totalcount * 0.7))
		        	$quote->pricerank = 'good';
		        elseif ($lowcount >= ($totalcount * 0.5))
		        	$quote->pricerank = 'fair';
		        else
		        	$quote->pricerank = 'poor';
		        $this->db->where('id',$quote->awardedbid->id)->update('award',array('pricerank'=>$quote->pricerank));

        	}
        }
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Payment done successfully & Bid awarded to the selected supplier(s).</div></div>');
        redirect('admin/quote/index/' . $quote->pid);
    
    	
    }
    
    function uploadPaymentAttachment()
    {    	
    	$receivedid = $_POST['hidreceivedid'];
    	$invoiceNum = $_POST['hidinvoicenum'];
    	
    	if(@$receivedid && @$invoiceNum)
    	{
    		if(isset($_FILES['UploadFile']['name'][$receivedid]))
    		{
    			if(is_uploaded_file($_FILES['UploadFile']['tmp_name'][$receivedid]))
	    		{
	    			$ext = end(explode('.', $_FILES['UploadFile']['name'][$receivedid]));
	    			$nfn = md5(date('u').uniqid()).'.'.$ext;
	    			if(move_uploaded_file($_FILES['UploadFile']['tmp_name'][$receivedid], "uploads/invoiceattachments/".$nfn))
	    			{
	    				$updateArr = array('attachmentname'=>$nfn ,
	    								   'attachment'=> $_FILES['UploadFile']['name'][$receivedid],	    				
	    								   'sharewithsupplier'=>(@$_POST['sharewithsupplier_'.$receivedid] && $_POST['sharewithsupplier_'.$receivedid] == 'on') ? 1 : 0);
    					$where = array('id'=>$receivedid,'invoicenum'=>$invoiceNum);
	    				$this->db->update('received',$updateArr,$where);
	    			}
	    		}
    		}
    	}
    	$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">File Uploaded Successfully.</div></div>');
    	redirect('admin/quote/invoices');
    }
    
    
    function addNewUserItem()
    {
        if (!$_POST)
            die;
            
            $this->db->where('itemcode',$_POST['itemcode']);			
			$item = $this->db->get('item')->row();
            if($item){
            echo "itemcode(".$item->itemcode.") already exists"; die; 
            }
            $this->db->where('itemname',$_POST['itemname']);			
			$itemname = $this->db->get('item')->row();
            if($itemname){
            echo "itemname(".$itemname->itemname.") already exists"; die; 
            }
            
			$itemid = $this->itemcode_model->SaveItemcode_user('useritem');
			if($itemid)
			echo $itemid; die;            
    }
    
    
	
    function updatesharesuplliercheck()
    {    	
    	$receivedid = $_POST['receivedid'];
    	$invoiceNum = $_POST['invoicenum'];
    	
    	if(@$receivedid && @$invoiceNum)
    	{
    		$updateArr = array('sharewithsupplier'=>$_POST['sharewithsupplier']);
    		$where = array('id'=>$receivedid,'invoicenum'=>$invoiceNum);
	    	$this->db->update('received',$updateArr,$where);
    	}
    }
    // End
    
    
    function showrecentcompanies(){

    	if (!$_POST)
    	die;

    	$this->db->where('quote',$_POST['quote']);
    	$quoteitems = $this->db->get('quoteitem')->result();

    	if($quoteitems){
    		$itemarray = array();
    		foreach ($quoteitems as $quotei)
    		$itemarray[] = $quotei->id;
    		
    		$companyitemimplode = implode(",",$itemarray);
    		/*$this->db->where_in('quoteitemid',$itemarray);
    		$this->db->group_by("companyemail");
    		$nonnetcompanies = $this->db->get('quoteitem_companies')->result();*/
    		
    		$noncomp = "SELECT qc.* FROM " . $this->db->dbprefix('quoteitem_companies') . " qc where qc.companyemail not in (select primaryemail from ".$this->db->dbprefix('company')." c  where c.isdeleted=0) and qc.quoteitemid in (".$companyitemimplode.") group by qc.companyemail";           
    		$nonnetcompanies = $this->db->query($noncomp)->result();
    		
    		if($nonnetcompanies){
				$html = '<table>';
    			foreach ($nonnetcompanies as $nonnetcomp){
    						
	        	$html .='<tr>
		        	<td colspan="2">
						<strong>'.$nonnetcomp->companyname.'</strong>&nbsp;&nbsp;&nbsp;<input type="checkbox" onclick="setnewcompany(\''.$nonnetcomp->companyname.'\',\''.$nonnetcomp->companyemail.'\',\''.$nonnetcomp->contact.'\')">
		        	</td>
	        	</tr>';    	
    				
    			}
    			$html .= '</table>';
				echo $html;
    		}
    	}
    }
    
    
    function orderclose($quote){
    	
    	if (!$_POST)
    	die;
    	
    	if(@$quote)
    	{
    		$updateArr = array('orderclose'=>$_POST['orderclose']);
    		$where = array('id'=>$quote);
	    	$this->db->update('quote',$updateArr,$where);
    	}
    	
    }
    
    function addnewcostcode()
    {
    	if(isset($_POST) && $_POST != '')
    	{
	    	$options = array(
				'code'=>$this->input->post('code'),
				'cost'=>$this->input->post('cost'),
				'parent'=>$this->input->post('parent'),
				'project'=>$this->input->post('project'),
				'creation_date' => date('Y-m-d')
			);
			$options['purchasingadmin'] = $this->session->userdata('purchasingadmin');
			$this->db->insert('costcode', $options);
    	}		
    	
    	redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }
    
     function _set_fields1() {
        $fields ['id'] = 'id';
        $fields ['code'] = 'code';
        $fields ['cost'] = 'cost';       
        $fields ['parent'] = 'Parent';
        $fields ['project'] = 'Project';
        $this->validation->set_fields($fields);
    }

    function _set_rules1() {
        $rules ['code'] = 'trim|required';
        $rules ['cost'] = 'trim|required|numeric';

        $this->validation->set_rules($rules);
        $this->validation->set_message('required', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Please fill all mandatory fields.</div></div>');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }
}

?>