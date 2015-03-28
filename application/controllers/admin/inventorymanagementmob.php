<?php

class inventorymanagementmob extends CI_Controller 
{

    function inventorymanagementmob() 
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
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data['title'] = "Administrator";
        //$this->load = new My_Loader();
        //$this->load->template('../../templates/admin/template', $data);
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
	        	$row->manage = ' <input style="width:50px;" readonly type="text" name="adjustqty" id="adjustqty'.@$row->itemid.'" value="'.@$row->qtyonhand.'" > <br> <img src="http://i.imgur.com/yOadS1c.png" act="add" style="width:25px;height:25px;" class="adjust'.@$row->itemid.'" '.$clickfunc.' width="15" height="15" /> 
				<br><br>
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
    	$this->load->view('admin/qtyadjustmobile', $data);
		
	}

} 