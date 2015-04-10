<?php

class inventorymanagement extends CI_Controller 
{

    function inventorymanagement() 
    {
    	parent::__construct();
        $this->load->library('session');
        $this->load->helper(array('form', 'url'));
        if (! $this->session->userdata('id'))
        {
            redirect('admin/login/index', 'refresh');
        }
        if ($this->session->userdata('usertype_id') == 3)
        {
            redirect('admin/dashboard', 'refresh');
        }
        $this->load->dbforge();
        $this->load->library('form_validation');
        $this->load->library(array('table', 'validation', 'session'));

        $this->load->helper('form', 'url');
      //  $this->load->model('admin/itemcode_model');
        $this->load->model('admin/quote_model');
        $this->load->model('admin/settings_model');
        $this->load->model('admin/inventorymanagement_model');
        $this->load->model('admin/statmodel');
        $id = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($id);
		if(empty($setting)){
		$data['settingtour']=$setting;
		$data['timezone']='America/Los_Angeles';
		}else{
		$data['timezone']=$setting[0]->tour;
		$data['timezone']=$setting[0]->timezone;
		}
        $this->load->model('admin/catcode_model');
        $data['pendingbids'] = $this->quote_model->getpendingbids();
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data['title'] = "Administrator";
        $this->load = new My_Loader();
        $this->load->template('../../templates/admin/template', $data);
    }

    function index()
    {    	
    	$inventory = array();
    	$inventoryRes = $this->inventorymanagement_model->get_inventorydetails();
    	
    	if(count($inventoryRes) > 0)
    	{
    		if($this->session->userdata('managedprojectdetails') != '')
 			{
 				$readonly = '';
 			}else 
 				$readonly = 'readonly';
    		
    		
	    	foreach ($inventoryRes as $row)
	        {
	        	$criticallevel= "";
	        	$mistock = $row->minstock+($row->minstock*25/100);
	        	if($row->qtyonhand<$mistock)
	        	$criticallevel = '<font color="red">*Critical Stock Level</font> <a class="view" target="blank" href="' . base_url() . 'admin/inventorymanagement/qtyreorder/' . @$row->itemid . '/'.$row->reorderqty.'">Reorder Quantity</span></a>';
	        	
	        	
	        	$row->itemcode = '<a href="javascript:void(0)" onclick="viewitems2(\''.$row->itemid.'\')">'.$row->itemcode.'</a><br>'.$criticallevel;
	        	
	        	$row->minstock = '<input style="width:50px;" '.$readonly.' type="text" name="minstock" id="minstock" value="'.@$row->minstock.'" onchange="updateminstock('.$row->itemid.',this.value);" >';
	        	$row->maxstock = '<input style="width:50px;"  '.$readonly.' type="text" name="maxstock" id="maxstock" value="'.@$row->maxstock.'"  onchange="updatemaxstock('.$row->itemid.',this.value);" >';
	        	$row->reorderqty = '<input style="width:50px;"  '.$readonly.' type="text" name="reorderqty" id="reorderqty" value="'.@$row->reorderqty.'" onchange="updatereorderqty('.$row->itemid.',this.value);"  >';
	        	$clickfunc = "";
	        	if($readonly =="")
	        	$clickfunc = ' onclick="reduceval('.$row->itemid.');"';      	
	        	$row->manage = '<input style="width:40px;" readonly type="text" name="adjustqty" id="adjustqty'.@$row->itemid.'" value="'.@$row->qtyonhand.'" > <img src="http://i.imgur.com/yOadS1c.png" style="width:25px;height:25px;" act="add" class="adjust'.@$row->itemid.'" '.$clickfunc.' width="12" height="12" /> 
				<a class="view" target="blank" href="' . base_url() . 'admin/inventorymanagement/qty_adjust/' . @$row->itemid . '"><span class="icon-2x icon-file"></span></a> &nbsp;
	        	<a class="view" target="blank" href="' . base_url() . 'admin/inventorymanagementmob/qty_adjust/' . @$row->itemid . '"><img style= "margin-top:-20px;" width="25px" height="25px" src="'.site_url('templates/admin/images/mobile_icon.jpg').'"/></a>
	        	
	        	<input type="button" style="display:none;text-align:center;" id="save'.@$row->itemid.'" onclick="updateadjustedqty('.$row->itemid.','.$row->ea.');" value="save"/> ';      	
	        	
	        	$row->qtyonhand = '<input style="width:50px;" type="text" name="qtyonhand" id="qtyonhand'.@$row->itemid.'" readonly value="'.@$row->qtyonhand.'" >';
	        	
	        	$row->valueonhand = round($row->valueonhand,2);
	        	
	        	$row->valueonhand = '<input style="width:50px;" type="text" name="valueonhand" id="valueonhand'.@$row->itemid.'" readonly value="'.@$row->valueonhand.'" >';
	        	
	        	
	        	$row->valuecomitted = round($row->valuecomitted,2);
	        	
	        	$pastdue = "";
	        	if(@$this->session->userdata('pastdueqtys')){
	        		
	        		if(in_array($row->itemid,$this->session->userdata('pastdueqtys')))
	        		$pastdue = " <a target='blank' href='".site_url('admin/backtrack')."'>PAST DUE</a>";
	        	}
	        	
	        	$row->daterequested = $row->daterequested." <br>".$pastdue;
	        	
	        	if (@$row->item_img && file_exists('./uploads/item/' . @$row->item_img))
	        	{
	        		$imgName = site_url('uploads/item/'.$row->item_img);
	        	}
	        	else
	        	{
	        		$imgName = site_url('uploads/item/big.png');
	        	}
	        	
	        	$row->item_img = '<img style="max-height: 120px; padding: 0px;width:100px; height:70px;float:left;" src="'.$imgName.'">';
	        	
	        	$inventory[] = $row;
	        }
    	}    
        else 
        {
            $data['items'] = array();
            $this->data['message'] = 'No Records';
        }
      
    	$data['items'] = $inventory;
        $data['jsfile'] = 'inventorymanagementjs.php';  
    	
    	$data['heading'] = 'Inventory Management';
    	$this->load->view('admin/inventorymanagement', $data);
    }
    
    
    
    public function updateminstock()
	{
		$company = $this->session->userdata('id');
		if(!$company)
			redirect('admin/login');
			
		if(!@$_POST)
		{
			die;
		}
		
		if(!@$_POST['itemid'])
		{
			die;
		}
				
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('purchasingadmin',$company);		
		if($this->session->userdata('managedprojectdetails') != '')
 		{
 			$this->db->where('project',$this->session->userdata('managedprojectdetails')->id);
 		}		
		$existing = $this->db->get('inventory')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('purchasingadmin',$company);			
			if($this->session->userdata('managedprojectdetails') != '')
 			{
 			$this->db->where('project',$this->session->userdata('managedprojectdetails')->id);
 			}
			$this->db->update('inventory',$_POST);
		}
		else
		{
			$_POST['purchasingadmin'] = $company;
			if($this->session->userdata('managedprojectdetails') != '')
 			{
 			$_POST['project'] = $this->session->userdata('managedprojectdetails')->id;
 			}			
			$this->db->insert('inventory',$_POST);
		}
		//print_r($_POST);
	}
	
	
	
	public function updatemaxstock()
	{
		$company = $this->session->userdata('id');
		if(!$company)
			redirect('admin/login');
			
		if(!@$_POST)
		{
			die;
		}
		
		if(!@$_POST['itemid'])
		{
			die;
		}
				
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('purchasingadmin',$company);		
		if($this->session->userdata('managedprojectdetails') != '')
 		{
 			$this->db->where('project',$this->session->userdata('managedprojectdetails')->id);
 		}		
		$existing = $this->db->get('inventory')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('purchasingadmin',$company);			
			if($this->session->userdata('managedprojectdetails') != '')
 			{
 			$this->db->where('project',$this->session->userdata('managedprojectdetails')->id);
 			}
			$this->db->update('inventory',$_POST);
		}
		else
		{
			$_POST['purchasingadmin'] = $company;
			if($this->session->userdata('managedprojectdetails') != '')
 			{
 			$_POST['project'] = $this->session->userdata('managedprojectdetails')->id;
 			}			
			$this->db->insert('inventory',$_POST);
		}
		//print_r($_POST);
	}
	
	
	public function updatereorderqty()
	{
		$company = $this->session->userdata('id');
		if(!$company)
			redirect('admin/login');
			
		if(!@$_POST)
		{
			die;
		}
		
		if(!@$_POST['itemid'])
		{
			die;
		}
				
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('purchasingadmin',$company);		
		if($this->session->userdata('managedprojectdetails') != '')
 		{
 			$this->db->where('project',$this->session->userdata('managedprojectdetails')->id);
 		}		
		$existing = $this->db->get('inventory')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('purchasingadmin',$company);			
			if($this->session->userdata('managedprojectdetails') != '')
 			{
 			$this->db->where('project',$this->session->userdata('managedprojectdetails')->id);
 			}
			$this->db->update('inventory',$_POST);
		}
		else
		{
			$_POST['purchasingadmin'] = $company;
			if($this->session->userdata('managedprojectdetails') != '')
 			{
 			$_POST['project'] = $this->session->userdata('managedprojectdetails')->id;
 			}			
			$this->db->insert('inventory',$_POST);
		}
		//print_r($_POST);
	}
	
	
	
	public function updateadjustedqty()
	{
		$company = $this->session->userdata('id');
		if(!$company)
			redirect('admin/login');
			
		if(!@$_POST)
		{
			die;
		}
		
		if(!@$_POST['itemid'])
		{
			die;
		}
				
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('purchasingadmin',$company);		
		if($this->session->userdata('managedprojectdetails') != '')
 		{
 			$this->db->where('project',$this->session->userdata('managedprojectdetails')->id);
 		}		
		$existing = $this->db->get('inventory')->row();
		if($existing)
		{
			$_POST['quantity'] = $existing->quantity-$_POST['quantity']; 
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('purchasingadmin',$company);			
			if($this->session->userdata('managedprojectdetails') != '')
 			{
 			$this->db->where('project',$this->session->userdata('managedprojectdetails')->id);
 			}
			$this->db->update('inventory',$_POST);
		}
		else
		{
			$_POST['purchasingadmin'] = $company;
			if($this->session->userdata('managedprojectdetails') != '')
 			{
 			$_POST['project'] = $this->session->userdata('managedprojectdetails')->id;
 			}			
			$this->db->insert('inventory',$_POST);
		}
		//print_r($_POST);
	}
	
	
	public function checkmaxstock(){
		
		$company = $this->session->userdata('id');
		if(!$company)
			redirect('admin/login');
			
		if(!@$_POST)
		{
			die;
		}
		
		if(!@$_POST['itemid'])
		{
			die;
		}
				
		$this->db->select('inventory.*');
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('purchasingadmin',$company);		
		if($this->session->userdata('managedprojectdetails') != '')
 		{
 			$this->db->where('project',$this->session->userdata('managedprojectdetails')->id);
 		}		
		$existing = $this->db->get('inventory')->row();
		if($existing)
		{
			
			/*$where = '';
			if($this->session->userdata('managedprojectdetails') != '')
			{
				$where .= " AND q.pid = ".$this->session->userdata('managedprojectdetails')->id;
			}

			$sql ="SELECT q.id,q.ponum,aw.itemid,aw.itemcode,aw.itemname, SUM(aw.received) as qtyonhand, SUM(aw.quantity - aw.received) as qtyonpo, SUM(aw.quantity) quantity, SUM(aw.ea) as ea, SUM(aw.ea*aw.received) as valueonhand, Min(IF(aw.quantity > aw.received, aw.daterequested, NULL )) daterequested, Max(DATE_FORMAT(a.awardedon,'%m/%d/%Y')) as lastaward,'' as manage
				FROM
				".$this->db->dbprefix('quote')." q
				JOIN ".$this->db->dbprefix('award')." a ON a.quote = q.id 
				LEFT JOIN ".$this->db->dbprefix('awarditem')." aw ON aw.award = a.id				
				WHERE 1=1 AND q.purchasingadmin='".$this->session->userdata('purchasingadmin')."' and itemid = ".$_POST['itemid']." {$where} GROUP by aw.itemid ";
			
			$qry = $this->db->query($sql);		
 			$qryresult = $qry->row();*/
						
 				
			if($existing->quantity>=$existing->maxstock && $existing->maxstock!="")
			echo 1;
			else
			echo 0;
 			
		}else{ 
			echo 0;	
		}
		die;
	}
	
	
	public function qty_adjust($itemid){
		
		if(@!$itemid)
		die;
		
		$inventory = array();
    	$inventoryRes = $this->inventorymanagement_model->get_inventorydetails($itemid);
    	
    	if(count($inventoryRes) > 0)
    	{
    		if($this->session->userdata('managedprojectdetails') != '')
 			{
 				$readonly = '';
 			}else 
 				$readonly = 'readonly';
    		
    		
	    	foreach ($inventoryRes as $row)
	        {
	        	$row->minstock = '<input style="width:50px;" '.$readonly.' type="text" name="minstock" id="minstock" value="'.@$row->minstock.'" onchange="updateminstock('.$row->itemid.',this.value);" >';
	        	$row->maxstock = '<input style="width:50px;"  '.$readonly.' type="text" name="maxstock" id="maxstock" value="'.@$row->maxstock.'"  onchange="updatemaxstock('.$row->itemid.',this.value);" >';
	        	$row->reorderqty = '<input style="width:50px;"  '.$readonly.' type="text" name="reorderqty" id="reorderqty" value="'.@$row->reorderqty.'" onchange="updatereorderqty('.$row->itemid.',this.value);"  >';
	        	$clickfunc = "";
	        	if($readonly =="")
	        	$clickfunc = ' onclick="reduceval('.$row->itemid.');"';      	
	        	$row->manage = ' <input style="width:50px;" readonly type="text" name="adjustqty" id="adjustqty'.@$row->itemid.'" value="'.@$row->qtyonhand.'" > <img src="http://i.imgur.com/yOadS1c.png" act="add" style="width:25px;height:25px;" class="adjust'.@$row->itemid.'" '.$clickfunc.' width="15" height="15" /> 
				&nbsp; &nbsp; &nbsp;
	        	<input type="button" style="text-align:center;" id="save'.@$row->itemid.'" onclick="updateadjustedqty('.$row->itemid.','.$row->ea.');" value="save"/> ';
	        	
	        	$row->qtyonhand = '<input style="width:50px;" type="text" name="qtyonhand" id="qtyonhand'.@$row->itemid.'" readonly value="'.@$row->qtyonhand.'" >';
	        	
	        	$row->valueonhand = round($row->valueonhand,2);
	        	
	        	$row->valueonhand = '<input style="width:50px;" type="text" name="valueonhand" id="valueonhand'.@$row->itemid.'" readonly value="'.@$row->valueonhand.'" >';
	        	
	        	$row->valuecomitted = round($row->valuecomitted,2);
	        	
	        	$inventory[] = $row;
	        }
    	}    
        else 
        {
            $data['items'] = array();
            $data['message'] = 'No Records';
        }
      
    	$data['items'] = $inventory;       	
    	$this->load->view('admin/qtyadjust', $data);
		
	}
	
	
	public function qtyreorder($itemid,$reorderqty){
		
		$project = $this->session->userdata('managedproject');
		
		/*if(!$project)
			redirect('admin/dashboard');*/	
		
		if(@$this->session->flashdata('message'))
			$data['message'] = $this->session->flashdata('message');
									
			/*$this->db->where('id',$id);
			$order = $this->db->get('order')->row();
			if(!$order)
				redirect('admin/order');			
						
			$data['order'] = $order;				
			$data['projects']  =  $this->statmodel->getProjects();
			$data['orderid'] = $id;*/
			$data['projects']  =  $this->statmodel->getProjects();
			$data['itemid'] =  $itemid;
			$this->db->select('itemcode,itemname,item_img');
			$this->db->where('id',$itemid);
			$data['itemdetails'] = $this->db->get('item')->row();			
			$data['reorderqty'] = $reorderqty;
			$this->load->view('admin/qtyreorder', $data);
			
	}
	
	
	function get_quotes_by_project(){
		
		$pid = $this->input->post("projectfilter");
		$itemid = $this->input->post("itemid");    	
		$wherecomb = "(potype = 'Bid' OR potype = 'Direct') ";
		$this->db->where($wherecomb);
		/*$wherecomb2 = " id not in (select quote from ".$this->db->dbprefix('invitation')." where purchasingadmin = ".$this->session->userdata('id').") ";
		$this->db->where($wherecomb2);*/
		$this->db->where('`id` not IN (select quote from '.$this->db->dbprefix('invitation').' where purchasingadmin = '.$this->session->userdata('id').')', NULL, FALSE);
    	$this->db->where('pid', $pid);
    	$this->db->where('purchasingadmin', $this->session->userdata('id'));
    	//$this->db->where('itemid', $itemid);
        $quotes = $this->db->get('quote')->result();		
    	echo json_encode($quotes);
    }

    
    function additemtoquote(){

    	if(@$_POST['ccid']){
    		$this->db->select('code');
    		$this->db->where('id',$_POST['ccid']);
    		$costcode = $this->db->get('costcode')->row();
    	}
    	if(@$_POST['hiddenitemid'] && @$_POST['qid'] && @$_POST['hiddenreorderqty'] && @$costcode->code){

    		$this->db->where('itemid',$_POST['hiddenitemid']);
    		$this->db->where('purchasingadmin',$this->session->userdata('id'));
    		$this->db->where('quote',$_POST['qid']);
    		$quoteitemresult = $this->db->get('quoteitem')->row();

    		if($quoteitemresult){
				$updatearray = array();
				$newquantity = $quoteitemresult->quantity+$_POST['hiddenreorderqty'];
				$newtotalprice = $quoteitemresult->totalprice+($_POST['hiddenreorderqty']*$quoteitemresult->ea);
				$updatearray['quantity'] = $newquantity;
				$updatearray['totalprice'] = $newtotalprice;
				$updatearray['costcode'] = $costcode->code;
    			$this->db->where('id', $quoteitemresult->id);
            	$this->db->update('quoteitem', $updatearray);
            	$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Item Quantity Updated for the Quote Successfully. <a target="blank" href="' . base_url() . 'admin/quote/update/' . $_POST['qid'] . '">Click Here to view the Quote</a> </div></div>');
    				redirect('admin/inventorymanagement/qtyreorder/' . $_POST['hiddenitemid'].'/'.$_POST['hiddenreorderqty']);    			
    		}else{

    			$this->db->where('id',$_POST['hiddenitemid']);
    			$itemresult = $this->db->get('item')->row();
    			//echo "<pre>",print_r($itemresult); die;
    			if($itemresult){
    				$items = array();
    				$items['itemid'] = $itemresult->id;
    				$items['itemcode'] = $itemresult->itemcode;
    				$items['itemname'] = $itemresult->itemname;
    				$items['unit'] = $itemresult->unit;
    				$items['ea'] = $itemresult->ea;
    				$items['quantity'] = $_POST['hiddenreorderqty'];
    				$items['notes'] = $itemresult->notes;
    				$items['purchasingadmin'] = $this->session->userdata('id');
    				$items['quote'] = $_POST['qid'];    								
					$items['costcode'] = $costcode->code;
    				$items['totalprice'] = $itemresult->ea*$_POST['hiddenreorderqty'];
    				$this->quote_model->db->insert('quoteitem', $items);
    				$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Item Added to Quote Successfully. <a target="blank" href="' . base_url() . 'admin/quote/update/' . $_POST['qid'] . '">Click Here to view the Quote</a> </div></div>');
    				redirect('admin/inventorymanagement/qtyreorder/' . $_POST['hiddenitemid'].'/'.$_POST['hiddenreorderqty']);
    			}

    		}
    	}
    }
    
} 