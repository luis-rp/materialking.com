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
    	
    	if(count($inventoryRes) > 1)
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
	        	$row->manage = '<input style="width:50px;" readonly type="text" name="adjustqty" id="adjustqty'.@$row->itemid.'" value="'.@$row->qtyonhand.'" > <img src="http://i.imgur.com/yOadS1c.png" act="add" class="adjust'.@$row->itemid.'" '.$clickfunc.' width="15" height="15" />  <input type="button" style="display:none;" id="save'.@$row->itemid.'" onclick="updateadjustedqty('.$row->itemid.','.$row->qtyonhand.');" value="save"/> ';
	        	
	        	$row->qtyonhand = '<input style="width:50px;" type="text" name="qtyonhand" id="qtyonhand'.@$row->itemid.'" readonly value="'.@$row->qtyonhand.'" >';
	        	
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
			$_POST['adjustedqty'] += $existing->adjustedqty; 
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
			
			$where = '';
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
 			$qryresult = $qry->row();
			
 			if($qryresult){
 				
 				if(($qryresult->quantity - $existing->adjustedqty)>=$existing->maxstock)
 				echo 1; 
 				else 
 				echo 0; 				
 			}else 
 			echo 0;
 			
		}else{ 
			echo 0;	
		}
		die;
	}
    
} 