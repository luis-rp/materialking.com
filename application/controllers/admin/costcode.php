<?php

class costcode extends CI_Controller {

    private $limit = 10;

    function costcode() {
        parent::__construct();
        $this->load->library('session');
        if (!$this->session->userdata('id')) {
            redirect('admin/login/index', 'refresh');
        }
        if ($this->session->userdata('usertype_id') == 3) {
            redirect('admin/dashboard', 'refresh');
        }
        $this->load->library('form_validation');
        $this->load->library(array('table', 'validation', 'session'));
        $this->load->helper('form', 'url');

        $this->load->dbforge();
        $this->load->model('admin/costcode_model');
        $this->load->model('admin/quote_model');
        $this->load->model('admin/order_model');
        $data['pendingbids'] = $this->quote_model->getpendingbids();
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data ['title'] = "Administrator";
        $this->load = new My_Loader();
        $this->load->template('../../templates/admin/template', $data);
    }
    
    function costcodeexport($mpid= 0)
    {
    	$offset = 0;
    	$uri_segment = 4;
    	$mpid = $this->uri->segment($uri_segment);
    	$mp = $this->session->userdata('managedprojectdetails');
    	 
    	 
    
    	if(!@$_POST && @$mp->id)
    	{
    		@$_POST['projectfilter'] = $mp->id;
    	}
    
    
    	if($mpid > 0)
    	{
    		@$_POST['projectfilter'] = $mpid;
    	}
    
    
    	$costcodes = $this->costcode_model->get_costcodes(1000, 0);
    
    	$this->load->library('pagination');
    	$config ['base_url'] = site_url('admin/costcode/index');
    	$config ['total_rows'] = $this->costcode_model->total_costcode();
    	$config ['per_page'] = $this->limit;
    	$config ['uri_segment'] = $uri_segment;
    
    	$this->pagination->initialize($config);
    	$data ['pagination'] = $this->pagination->create_links();
    	$this->load->library('table');
    	$this->table->set_empty("&nbsp;");
    	$this->table->set_heading('ID', 'Name', 'Email', 'Actions');
    	$i = 0 + $offset;
    
    	if ($this->session->userdata('usertype_id') > 1)
    		$where = " and s.purchasingadmin = ".$this->session->userdata('purchasingadmin');
    	else
    		$where = "";
    
    	$cquery = "SELECT taxrate FROM ".$this->db->dbprefix('settings')." s WHERE 1=1".$where." ";
    	$taxrate = $this->db->query($cquery)->row();
    	$data['taxrate'] = $taxrate->taxrate;
    
    	$count = count($costcodes);
    	$items = array();
    	if ($count >= 1)
    	{
    		foreach ($costcodes as $costcode)
    		{
    			if ($costcode->totalspent != '-' && $costcode->cost > 0)
    			{
    				if ($costcode->totalspent / $costcode->cost > 1)
    				{
    					$per = number_format(( ($costcode->totalspent + $costcode->totalspent*($taxrate->taxrate/100)) / $costcode->cost) * 100, 2) . '%';
    					$costcode->budget =  $per ;
    				}
    				else
    				{
    					$per = number_format(( ($costcode->totalspent + $costcode->totalspent*($taxrate->taxrate/100)) / $costcode->cost) * 100, 2) . '%';
    					$costcode->budget =  $per;
    				}
    			}
    			else
    			{
    				$per = 0;
    				$costcode->budget = '';
    			}
    			$costcode->budgetper = $per;
    			$costcode->cost = "$ " . $costcode->cost;
    			if ($costcode->totalspent != '-') {
    				$costcode->totalspent = $costcode->totalspent;
    			}
    			$costcode->manualprogress = $costcode->manualprogress ? $costcode->manualprogress : 0;
    			$costcode->manualprogressbar = $costcode->manualprogress ;
    			$per = str_replace('%', '', $per);
    			if ($per <= $costcode->manualprogress)
    			{
    				$costcode->status = 'Good';
    			}
    			else
    			{
    				$costcode->status = 'Bad';
    			}
    			$items[] = $costcode;
    		}
    
    		$data['items'] = $items;
    	}
    	else {
    		$data['items'] = array();
    	}
    
    	//=========================================================================================
    		
    	$header[] = array('Code' , 'Budget','$ Spent' , 'Budget % Allocated' , 'Task Progress % Complete' , 'Status' );
    		
    	$taxrate = 	$data['taxrate'];
    
    	foreach( $data['items'] as $item)
    	{
    			
    		$spent = "$ ".round( ($item->totalspent + $item->totalspent*($taxrate/100)),2 );
    			
    		$header[] = array($item->code , formatPriceNew($item->cost) , formatPriceNew($spent) , $item->budget.chr(160) , $item->manualprogressbar.'%'.chr(160) , $item->status );
    	}
    
    
    	createXls('costcodes', $header);
    	die();
    
    	//===============================================================================
    		
    }

    function index($offset = 0) 
    {
        $uri_segment = 4;
        $offset = $this->uri->segment($uri_segment);
        $mp = $this->session->userdata('managedprojectdetails');
        if(!@$_POST && @$mp->id)
        {
        	@$_POST['projectfilter'] = $mp->id;
        }
        $costcodes = $this->costcode_model->get_costcodes($this->limit, $offset);

        $this->load->library('pagination');
        $config ['base_url'] = site_url('admin/costcode/index');
        $config ['total_rows'] = $this->costcode_model->total_costcode();
        $config ['per_page'] = $this->limit;
        $config ['uri_segment'] = $uri_segment;

        $this->pagination->initialize($config);
        $data ['pagination'] = $this->pagination->create_links();
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('ID', 'Name', 'Email', 'Actions');
        $i = 0 + $offset;

        if ($this->session->userdata('usertype_id') > 1)
        $where = " and s.purchasingadmin = ".$this->session->userdata('purchasingadmin');
        else
        $where = "";

        $cquery = "SELECT taxrate FROM ".$this->db->dbprefix('settings')." s WHERE 1=1".$where." ";
        $taxrate = $this->db->query($cquery)->row();
        $data['taxrate'] = $taxrate->taxrate;
        
        $count = count($costcodes);
        $items = array();
        if ($count >= 1) {
            foreach ($costcodes as $costcode) {
                $costcode->actions = anchor('admin/costcode/update/' . $costcode->id, '<span class="icon-2x icon-edit"></span>', array('class' => 'update'))
                        . ' ' .
                        anchor('admin/costcode/delete/' . $costcode->id, '<span class="icon-2x icon-trash"></span>', array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"))
                ;
                if ($costcode->totalspent != '-' && $costcode->cost > 0) 
                {
                    if ($costcode->totalspent / $costcode->cost > 1) 
                    {
                        //$per = '100%';
                        $per = number_format(( ($costcode->totalspent + $costcode->totalspent*($taxrate->taxrate/100)) / $costcode->cost) * 100, 2) . '%';
                        $costcode->budget = '<div class="progress progress-red progress-striped active">
									      <div style="width: 100%;" class="bar">' . $per . '</div>
									     </div>';
                    } 
                    else 
                    {
                        $per = number_format(( ($costcode->totalspent + $costcode->totalspent*($taxrate->taxrate/100)) / $costcode->cost) * 100, 2) . '%';
                        $costcode->budget = '<div class="progress progress-blue progress-striped active">
									      <div style="width: ' . $per . ';" class="bar">' . $per . '</div>
									     </div>';
                    }
                } 
                else 
                {
                    $per = 0;
                    $costcode->budget = '';
                }
                $costcode->budgetper = $per;
                $costcode->cost = "$ " . $costcode->cost;
                if ($costcode->totalspent != '-') {
                    $costcode->totalspent = $costcode->totalspent;
                    $costcode->actions .= ' ' .
                            anchor('admin/costcode/items/' . $costcode->code, '<span class="icon-2x icon-search"></span>', array('class' => 'view'))
                    ;
                }
                $costcode->manualprogress = $costcode->manualprogress ? $costcode->manualprogress : 0;
                //$costcode->manualprogress = number_format($costcode->manualprogress,2);
                $costcode->manualprogressbar = '<input id="progress' . $costcode->id . '"  class="slider" style="width:200px;"
											 data-slider-id="progress' . $costcode->id . '" type="text" 
											 data-slider-min="0" value="' . $costcode->manualprogress . '"
											 data-slider-max="100" data-slider-step="1" 
											 data-slider-value="' . $costcode->manualprogress . '"/>&nbsp;&nbsp;
											 <span id="progresslabel' . $costcode->id . '">' . $costcode->manualprogress . '%</span>';
                $per = str_replace('%', '', $per);
                if ($per <= $costcode->manualprogress)
                {
                    $costcode->status = 'Good';
                    $costcode->status = "<img src='".site_url('templates/admin/images/ok.gif')."'/>";
                }
                else
                {
                    $costcode->status = 'Bad';
                    $costcode->status = "<img src='".site_url('templates/admin/images/bad.png')."'/>";
                }
                $items[] = $costcode;
            }

            $data['items'] = $items;
            $data['jsfile'] = 'costcodejs.php';
        }
        else {
            $data['items'] = array();
            $this->data['message'] = 'No Records';
        }

        $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo('0', 0, @$_POST['parentfilter']);

        if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        $data['projects'] = $this->db->get('project')->result();
        //print_r($data['projects']);die;
        
        $data ['addlink'] = '';
        $data ['heading'] = 'Cost Code Management';
        $data ['table'] = $this->table->generate();
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/costcode/add" id="step10">Add Cost Code</a>';
        $data['viewname'] = 'costcodelist';
        $this->load->view('admin/costcodelist', $data);
    }
    function export($costcode)
    {
    	$costcode = urldecode($costcode);
    	$costcodeitems = $this->costcode_model->getcostcodeitems($costcode);
    
    	$count = count($costcodeitems);
    	$items = array();
    	if ($count >= 1) {
    		foreach ($costcodeitems as $row) {
    			$awarded = $this->quote_model->getawardedbid($row->quote);
    			$row->ea = "$ " . $row->ea;
    			$row->totalprice = "$ " . $row->totalprice;
    			$row->itemname = htmlentities($row->itemname);
    			$row->status = strtoupper($awarded->status);
    			$row->actions = //$row->status=='COMPLETE'?'':
    			anchor('admin/quote/track/' . $row->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update'))
    			;
    			$items[] = $row;
    		}
    
    		$data['items'] = $items;
    	} else {
    		$this->data['message'] = 'No Items';
    	}
    
    	$orders = $this->order_model->get_order_by_costcode($costcode);
    
    	$data['orders'] = array();
    	$i = 0;
    	foreach($orders as $order)
    	{
    		$i++;
    		$order->sno = $i;
    		if(!is_null($order->project)){
    			$sql = "SELECT *
				FROM ".$this->db->dbprefix('project')." p
				WHERE id=".$order->project;
    			$project = $this->db->query($sql)->result();
    			$order->prjName = "assigned to ".$project[0]->title;
    		}else{
    			$order->prjName = "Pending Assignment";
    		}
    		$data['orders'][]=$order;
    	}
    
    	//===============================================================================
    
    	$header[] = array('ID' , 'PO#' , 'Code' , 'Item Name' , 'Unit' , 'Quantity' , 'Price EA' , 'Total Price' , 'Date Requested' , 'Status');
    		
    	foreach($items  as  $enq_row)
    	{
    		$header[] = array($enq_row->id,  $enq_row->ponum ,  $enq_row->itemcode , $enq_row->itemname ,$enq_row->unit ,$enq_row->quantity , formatPriceNew($enq_row->ea) , formatPriceNew($enq_row->totalprice) ,$enq_row->daterequested , $enq_row->status);
    	}
    	createXls('costcode'.$costcode , $header);
    	die();
    
    }
    function items($costcode) {
        $costcode = urldecode($costcode);
        $costcodeitems = $this->costcode_model->getcostcodeitems($costcode);
        $costcodeitems2 = $this->costcode_model->getcostcodeitems2($costcode);

        $count = count($costcodeitems);
        $items = array();
        if ($count >= 1) {
            foreach ($costcodeitems as $row) {
                $awarded = $this->quote_model->getawardedbid($row->quote);
                $row->ea = "$ " . $row->ea;
                $row->totalprice = "$ " . $row->totalprice;
                $row->itemname = htmlentities($row->itemname);
                $row->status = strtoupper($awarded->status);
                $row->actions = //$row->status=='COMPLETE'?'':
                        anchor('admin/quote/track/' . $row->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update'))
                ;
                $items[] = $row;
            }
            
            foreach ($costcodeitems2 as $row2) {
                $awarded = $this->quote_model->getawardedbid($row2->quote);
                $row2->ea = "$ " . $row2->ea;
                $row2->totalprice = "$ " . $row2->totalprice;
                $row->itemname = htmlentities($row2->itemname);
                $row2->status = strtoupper($awarded->status);
                $row2->received = $row2->received;
                $row2->actions = //$row->status=='COMPLETE'?'':
                        anchor('admin/quote/track/' . $row2->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update'))
                ;
                $items2[] = $row2;
            }

            $data['items'] = $items;
            $data['items2'] = $items2;
        } else {
            $this->data['message'] = 'No Items';
        }
		/****************/
        $orders = $this->order_model->get_order_by_costcode($costcode);
        //print_r($orders);die;
        $data['orders'] = array();
        $i = 0;
        foreach($orders as $order)
        {
            $i++;
            $order->sno = $i;
        	if(!is_null($order->project)){
        		$sql = "SELECT *
				FROM ".$this->db->dbprefix('project')." p
				WHERE id=".$order->project;
        		$project = $this->db->query($sql)->result();
        		$order->prjName = "assigned to ".$project[0]->title;
        	}else{
        		$order->prjName = "Pending Assignment";
        	}
        	$data['orders'][]=$order;
        }
      
		/****************/
        $data['jsfile'] = 'costcodeitemjs.php';
        $data ['addlink'] = '';
        $data ['heading'] = "Store Orders with Costcode '$costcode'";
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/costcode">&lt;&lt; Back</a>';
        $this->load->view('admin/datagrid', $data);
    }

    function add() {
        $this->_set_fields();
        $data ['heading'] = 'Add New Costcode';
        $data ['message'] = '';
        $data ['action'] = site_url('admin/costcode/add_costcode');

        if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        $data['parents'] = $this->db->get('costcode')->result();

        if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        $data['projects'] = $this->db->get('project')->result();
        

        $mp = $this->session->userdata('managedprojectdetails');
        if(@$mp->id)
        {
        	$this->validation->project = $mp->id;
        }

        $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo();
        $data['viewname'] = 'costcode';
        $this->load->view('admin/costcode', $data);
    }

    function add_costcode() {
        $data ['heading'] = 'Add New Costcode';
        $data ['action'] = site_url('admin/costcode/add_costcode');

        $this->_set_fields();
        $this->_set_rules();

        if ($this->validation->run() == FALSE) {
            $data ['message'] = $this->validation->error_string;
            if ($this->session->userdata('usertype_id') > 1)
                $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
            $data['parents'] = $this->db->get('costcode')->result();

            if ($this->session->userdata('usertype_id') > 1)
                $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
            $data['projects'] = $this->db->get('project')->result();

            $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo();
            $this->load->view('admin/costcode', $data);
        }
        elseif ($this->costcode_model->checkDuplicateCode($this->input->post('code'), 0)) {
            $data ['message'] = 'Duplicate Costcode';
            $data['parents'] = $this->db->get('costcode')->result();
            $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo();
            $this->load->view('admin/costcode', $data);
            //$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Duplicate Costcode</div></div>');
            //redirect('admin/costcode/add'); 
        } else {
            $itemid = $this->costcode_model->SaveCostcode();
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox" style="display:inline;" id="step12" >Cost Code Added Successfully</div></div>');
            redirect('admin/costcode');
        }
    }

    function update($id) {
        $this->_set_fields();
        $item = $this->costcode_model->get_costcodes_by_id($id);
        $this->validation->id = $id;
        $this->validation->code = $item->code;
        $this->validation->cost = $item->cost;
        $this->validation->cdetail = $item->cdetail;
        $this->validation->parent = $item->parent;

        $this->db->where('id !=', $id);
        if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        $data['parents'] = $this->db->get('costcode')->result();

        if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        $data['projects'] = $this->db->get('project')->result();

        $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo('0', 0, $item->parent);

        $data ['heading'] = 'Update Cost Code';
        $data ['message'] = '';
        $data ['action'] = site_url('admin/costcode/updatecostcode');
        $this->load->view('admin/costcode', $data);
    }

    function updatecostcode() {
        $data ['heading'] = 'Update Cost Code';
        $data ['action'] = site_url('message/updatecostcode');
        $this->_set_fields();
        $this->_set_rules();

        $itemid = $this->input->post('id');

        if ($this->validation->run() == FALSE) {
            $data ['message'] = $this->validation->error_string;
            $this->db->where('id !=', $itemid);
            if ($this->session->userdata('usertype_id') > 1)
                $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
            $data['parents'] = $this->db->get('costcode')->result();

            if ($this->session->userdata('usertype_id') > 1)
                $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
            $data['projects'] = $this->db->get('project')->result();

            $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo('0', 0, $item->parent);
            $data ['action'] = site_url('admin/costcode/updatecostcode');
            $this->load->view('admin/costcode', $data);
        }
        elseif ($this->costcode_model->checkDuplicateCode($this->input->post('code'), $itemid)) {
            $data ['message'] = 'Duplicate Costcode';
            $this->db->where('id !=', $itemid);
            $data['parents'] = $this->db->get('costcode')->result();
            $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo('0', 0, $item->parent);
            $this->load->view('admin/costcode', $data);
        } else {
            $this->costcode_model->updateCostcode($itemid);
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Cost Code Updated Successfully</div></div>');
            redirect('admin/costcode/update/' . $itemid);
            redirect('admin/costcode/index');
        }
    }

    function updateprogress() {
        $this->db->where('id', $_POST['id']);
        $this->db->update('costcode', $_POST);
    }

    function delete($id) {
        $this->costcode_model->remove_costcode($id);
        redirect('admin/costcode', 'refresh');
    }

    function _set_fields() {
        $fields ['id'] = 'id';
        $fields ['code'] = 'code';
        $fields ['cost'] = 'cost';
        $fields ['cdetail'] = 'cdetail';
        $fields ['parent'] = 'Parent';
        $fields ['project'] = 'Project';
        $this->validation->set_fields($fields);
    }

    function _set_rules() {
        $rules ['code'] = 'trim|required';
        $rules ['cost'] = 'trim|required|numeric';

        $this->validation->set_rules($rules);
        $this->validation->set_message('required', '* required');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }
    
    function get_cc_by_project(){
    	$pid = $this->input->get("projectId");
    	$uri_segment = 4;
    	$offset = $this->uri->segment($uri_segment);
    	$costcodes = $this->costcode_model->get_costcodes($this->limit, $offset);
    	
    	echo json_encode($costcodes);
    }

}

?>
