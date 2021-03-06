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
        $this->load->model('admin/settings_model');
        $id = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($id);
		if(empty($setting)){
		$data['settingtour']=$setting;
		$data['pagetour']=$setting;
		$data['timezone']='America/Los_Angeles';
		}else{
		$data['settingtour']=$setting[0]->tour;
		$data['pagetour']=$setting[0]->pagetour;
		$data['timezone']=$setting[0]->timezone;
		}
        $this->load->model('admin/costcode_model');
        $this->load->model('admin/quote_model');
        $this->load->model('admin/order_model');
        $this->load->model('admin/project_model');
        $data['pendingbids'] = $this->quote_model->getpendingbids();
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data ['title'] = "Administrator";
        
        $receiveqty = $this->quote_model->gettotalreceivedshipqty();
		$this->session->set_userdata('receiveqty',$receiveqty);  
        
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

	// PDF

    function costcodepdf($mpid= 0)
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
		$headername = "COST CODE MANAGEMENT";
    	createPDF('costcodes', $header,$headername);
    	die();

    	//===============================================================================

    }

    function index($offset = 0)
    {
        $uri_segment = 4;
        $appendStr = '';
        $parentId = 0;
        
        $offset = $this->uri->segment($uri_segment);
        $mp = $this->session->userdata('managedprojectdetails');
        if(!@$_POST['projectfilter'] && @$mp->id)
        {
        	@$_POST['projectfilter'] = $mp->id;
        }
        
        if(@$_POST['projectfilter'] == "viewall")
        $_POST['projectfilter'] = "";
        
        $parent = '';
        
        $costcodesforgraph = $this->costcode_model->get_costcodesforgraph($this->limit, $offset,$parent);
        
        $parent = 0;
        
        $costcodes = $this->costcode_model->get_costcodes($this->limit, $offset,$parent);

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
        if(isset($taxrate) && $taxrate!="")
        {
        $data['taxrate'] = $taxrate;
        }
        else 
        {
         $data['taxrate'] = "";	
        }

        $count = count($costcodes);
        $items = array();
        $itemsofgraph = array();
        if ($count >= 1) {
        //	$level = $this->costcode_model->listcategorylevel('0', 0, 0,@$_POST['parentfilter']);
            foreach ($costcodesforgraph as $costcode) {
            	
            	$wherecode = "";
            	if(@$this->session->userdata('managedprojectdetails')->id){
            		$wherecode = "AND q.pid=".$this->session->userdata('managedprojectdetails')->id;
				}
            	
				if($this->session->userdata('usertype_id')>1)
						$wherecode .= " AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."' ";
				
            	// Code for getting discount/Penalty per invoice
					$query = "SELECT invoicenum, ai.company, ai.purchasingadmin, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice , r.paymentdate, r.datedue, r.paymentstatus 
			 FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,				   
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q WHERE r.awarditem=ai.id AND ai.award=a.id AND a.quote=q.id {$wherecode} AND ai.costcode='".$costcode->code."' GROUP by invoicenum";		
					
					$invoicequery = $this->db->query($query);
        			$itemsinv = $invoicequery->result();
                    
        			if($itemsinv){

        				foreach ($itemsinv as $invoice) {


        					
        					if(@$invoice->company && @$invoice->purchasingadmin){

        						$sql = "SELECT duedate, term, penalty_percent, discount_percent, discountdate FROM " .$this->db->dbprefix('invoice_cycle') . " where company='" . $invoice->company . "'
				and purchasingadmin = '". $invoice->purchasingadmin ."'";
        						//echo $sql;
        						$resultinvoicecycle = $this->db->query($sql)->row();

        						$penalty_percent = 0;
        						$penaltycount = 0;
        						$discount_percent =0;

        						if($resultinvoicecycle){

        							if((@$resultinvoicecycle->penalty_percent || @$resultinvoicecycle->discount_percent) ){

        								if(@$invoice->datedue){

        									if(@$invoice->paymentstatus == "Paid" && @$invoice->paymentdate){
        										$oDate = $invoice->paymentdate;
        										$now = strtotime($invoice->paymentdate);
        									}else {
        										$oDate = date('Y-m-d');
        										$now = time();
        									}

        									$d1 = strtotime($invoice->datedue);
        									$d2 = strtotime($oDate);
        									$datediff =  (date('Y', $d2) - date('Y', $d1))*12 + (date('m', $d2) - date('m', $d1));
        									if(is_int($datediff) && $datediff > 0) {

        										$penalty_percent = $resultinvoicecycle->penalty_percent;
        										$penaltycount = $datediff;

        									}else{

        										$discountdate = $resultinvoicecycle->discountdate;
        										if(@$discountdate){
													$exploded = explode("-",@$invoice->datedue);
        											$exploded[2] = $discountdate;
        											$discountdt = implode("-",$exploded);
        											if ($now < strtotime($discountdt)) {         											
        												$discount_percent = $resultinvoicecycle->discount_percent;
        											}
        										}
        									}
        									
        									
        									if(@$discount_percent){

        										$costcode->totalspent = $costcode->totalspent - ($invoice->totalprice*$discount_percent/100);
        									}

        									if(@$penalty_percent){

        										$costcode->totalspent = $costcode->totalspent + (($invoice->totalprice*$penalty_percent/100)*@$penaltycount);
        									}
        									
        								}

        							}
        						}

        					}

        				}

        			}      			
        			// Code for getting discount/Penalty Ends
        			 
            	
            	
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
                	$costcode->code = trim($costcode->code);
                    $costcode->totalspent = $costcode->totalspent;
                    $costcode->actions .= ' ' .
                            anchor('admin/costcode/items/' . str_replace('%2F', '/', urlencode(urlencode($costcode->code))).'/'. str_replace('%2F', '/', urlencode(urlencode($costcode->project))), '<span class="icon-2x icon-search"></span>', array('class' => 'view'))
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
                if ($per <= $costcode->manualprogress || $costcode->estimate==1)
                {
                	
                    $costcode->status = 'Good';
                    $costcode->status = "<img src='".site_url('templates/admin/images/ok.gif')."'/>";
                	
                }
                else
                {
                    $costcode->status = 'Bad';
                    $costcode->status = "<img src='".site_url('templates/admin/images/bad.png')."'/>";
                }
               
               $csql ="SELECT code,id,parent
						FROM
						".$this->db->dbprefix('costcode')." 
						WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."'  AND parent = '{$costcode->id}'";
               
               $qry = $this->db->query($csql);
               $res = $qry->result();
              
               if(count($res) > 0)
               {
               		$costcode->codeforgraph = $costcode->code;
               	 	$costcode->code = '<a href="#" onclick="filterdata('.$res[0]->parent.')">'. $costcode->code .'</a>&nbsp;&nbsp;&nbsp;&nbsp;<a  id="isexpand_'.$costcode->id.'" href="javascript:void(0)"  onclick="getchildcostcode(this,'.$res[0]->parent.','.$costcode->id.')">Expand</a>';
               }
               else 
               {
               		$costcode->codeforgraph = $costcode->code;
	            	$costcode->code = $costcode->code;	            	
               }
              //  $costcode->level = $level[$costcode->id];
              
              foreach($costcodes as $ccodes){

              	if($costcode->id == $ccodes->id)
              	$items[] = $costcode;

              }
              
              $itemsofgraph[] = $costcode;
              
            } 
            $data['items'] = $items;
            $data['itemsofgraph'] = $itemsofgraph;
            $data['jsfile'] = 'costcodejs.php';
        } 
        else {
            $data['items'] = array();
            $data['itemsofgraph'] = array();
            $this->data['message'] = 'No Records';
        }
       
        if(isset($_POST['parentfilter']) && $_POST['parentfilter']=="")
        {
        	$_POST['parentfilter']=0;
        }
        $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo('0', 0, 0, @$_POST['parentfilter']);

        if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        $data['projects'] = $this->db->get('project')->result();
        
        $data ['addlink'] = '';
        $data ['heading'] = 'Cost Code Management';
        $data ['table'] = $this->table->generate();
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/costcode/add" id="step10">Add Cost Code</a>';
        $data['viewname'] = 'costcodelist';

        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}

		
        $this->load->view('admin/costcodelist', $data);
    }

    function getchildcostcode()
    {
    	$str = '';
    	$isprogressBar = '';
    	$isestimate = '';
    	$totalspend = '';
    	$imgName = '';
    	$uri_segment = 4;
        $offset = $this->uri->segment($uri_segment);
    	
    	$costcodes = $this->costcode_model->get_costcodes($this->limit, $offset,$_POST['parent']);
    	
    	if ($this->session->userdata('usertype_id') > 1)
        $where = " and s.purchasingadmin = ".$this->session->userdata('purchasingadmin');
        else
        $where = "";

        $cquery = "SELECT taxrate FROM ".$this->db->dbprefix('settings')." s WHERE 1=1".$where." ";
        $taxrate = $this->db->query($cquery)->row();
        if(isset($taxrate) && $taxrate!="")
        {
        $data['taxrate'] = $taxrate;
        }
        else 
        {
         $data['taxrate'] = "";	
        }

        $count = count($costcodes);
        $items = array();
        if ($count >= 1) {
        //	$level = $this->costcode_model->listcategorylevel('0', 0, 0,@$_POST['parentfilter']);
            foreach ($costcodes as $costcode) {
            	
            	$wherecode = "";
            	if(@$this->session->userdata('managedprojectdetails')->id){
            		$wherecode = "AND q.pid=".$this->session->userdata('managedprojectdetails')->id;
				}
            	
				if($this->session->userdata('usertype_id')>1)
						$wherecode .= " AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."' ";
				
            	// Code for getting discount/Penalty per invoice
					$query = "SELECT invoicenum, ai.company, ai.purchasingadmin, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice , r.paymentdate, r.datedue, r.paymentstatus 
			 FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,				   
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q WHERE r.awarditem=ai.id AND ai.award=a.id AND a.quote=q.id {$wherecode} AND ai.costcode='".$costcode->code."' GROUP by invoicenum";		
					
					$invoicequery = $this->db->query($query);
        			$itemsinv = $invoicequery->result();
                    
        			if($itemsinv){

        				foreach ($itemsinv as $invoice) {


        					
        					if(@$invoice->company && @$invoice->purchasingadmin){

        						$sql = "SELECT duedate, term, penalty_percent, discount_percent, discountdate FROM " .$this->db->dbprefix('invoice_cycle') . " where company='" . $invoice->company . "'
				and purchasingadmin = '". $invoice->purchasingadmin ."'";
        						//echo $sql;
        						$resultinvoicecycle = $this->db->query($sql)->row();

        						$penalty_percent = 0;
        						$penaltycount = 0;
        						$discount_percent =0;

        						if($resultinvoicecycle){

        							if((@$resultinvoicecycle->penalty_percent || @$resultinvoicecycle->discount_percent) ){

        								if(@$invoice->datedue){

        									if(@$invoice->paymentstatus == "Paid" && @$invoice->paymentdate){
        										$oDate = $invoice->paymentdate;
        										$now = strtotime($invoice->paymentdate);
        									}else {
        										$oDate = date('Y-m-d');
        										$now = time();
        									}

        									$d1 = strtotime($invoice->datedue);
        									$d2 = strtotime($oDate);
        									$datediff =  (date('Y', $d2) - date('Y', $d1))*12 + (date('m', $d2) - date('m', $d1));
        									if(is_int($datediff) && $datediff > 0) {

        										$penalty_percent = $resultinvoicecycle->penalty_percent;
        										$penaltycount = $datediff;

        									}else{

        										$discountdate = $resultinvoicecycle->discountdate;
        										if(@$discountdate){
													$exploded = explode("-",@$invoice->datedue);
        											$exploded[2] = $discountdate;
        											$discountdt = implode("-",$exploded);
        											if ($now < strtotime($discountdt)) {         											
        												$discount_percent = $resultinvoicecycle->discount_percent;
        											}
        										}
        									}
        									
        									
        									if(@$discount_percent){

        										$costcode->totalspent = $costcode->totalspent - ($invoice->totalprice*$discount_percent/100);
        									}

        									if(@$penalty_percent){

        										$costcode->totalspent = $costcode->totalspent + (($invoice->totalprice*$penalty_percent/100)*@$penaltycount);
        									}
        									
        								}

        							}
        						}

        					}

        				}

        			}      			
        			// Code for getting discount/Penalty Ends
        			 
            	
            	
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
                	$costcode->code = trim($costcode->code);
                    $costcode->totalspent = $costcode->totalspent;
                    $costcode->actions .= ' ' .
                            anchor('admin/costcode/items/' . str_replace('%2F', '/', urlencode(urlencode($costcode->code))).'/'. str_replace('%2F', '/', urlencode(urlencode($costcode->project))), '<span class="icon-2x icon-search"></span>', array('class' => 'view'))
                    ;
                }
                $costcode->manualprogress = $costcode->manualprogress ? $costcode->manualprogress : 0;
                //$costcode->manualprogress = number_format($costcode->manualprogress,2);
                $costcode->manualprogressbar = "<input id='progress" . $costcode->id . "'  class='slider1' style='width:200px;'
											 data-slider-id='progress" . $costcode->id . "' type='text'
											 data-slider-min='0' value='" . $costcode->manualprogress . "'
											 data-slider-max='100' data-slider-step='1'
											 data-slider-value='" . $costcode->manualprogress . "'/>&nbsp;&nbsp;
											 <span id='progresslabel" . $costcode->id . "'>" . $costcode->manualprogress . "%</span>";
                $per = str_replace('%', '', $per);
                if ($per <= $costcode->manualprogress || $costcode->estimate==1)
                {
                	
                    $costcode->status = 'Good';
                    $costcode->status = "<img src='".site_url('templates/admin/images/ok.gif')."'/>";
                	
                }
                else
                {
                    $costcode->status = 'Bad';
                    $costcode->status = "<img src='".site_url('templates/admin/images/bad.png')."'/>";
                }
               
               $csql ="SELECT code,id,parent
						FROM
						".$this->db->dbprefix('costcode')." 
						WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."'  AND parent = '{$costcode->id}'";
               
               $qry = $this->db->query($csql);
               $res = $qry->result();
              
               if(count($res) > 0)
               {
               		$costcode->codeforgraph = '&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $costcode->code .'&nbsp;&nbsp;&nbsp;&nbsp;';
               	 	$costcode->code = '&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $costcode->code .'&nbsp;&nbsp;&nbsp;&nbsp;<a id="isexpand_'.$costcode->id.'"  href="#" onclick="getchildcostcode(this,'.$res[0]->parent.','.$costcode->id.')">Expand</a>';
               }
               else 
               {
	            	$costcode->code = '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $costcode->code;
	            	$costcode->codeforgraph = '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $costcode->code;
               }               
          
          	 if (isset($costcode->costcode_image) && $costcode->costcode_image != '' && file_exists('./uploads/costcodeimages/' . $costcode->costcode_image)) 
			 { 
			 	 $imgName = '<img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src="'.site_url('uploads/costcodeimages/'.$costcode->costcode_image).'">'; 
			 } 
			$shipping = 0;
			if(isset($costcode->shipping)) 
			{
				$shipping = $costcode->shipping; 
				$totalspend =  "$ ".round( ($costcode->totalspent + $costcode->totalspent*($taxrate->taxrate/100) + $shipping),2 );
			}
			if(@$costcode->estimate ==1) { $isestimate = "yes"; } else { $isestimate = "no"; }
			if(@$costcode->estimate!=1) { $isprogressBar =  $costcode->manualprogressbar; } else { $isprogressBar = ""; }
			
				$str .='<tr id="costcode_'.$costcode->id.'" class="clscostcode_'.$costcode->parent.'">
						<input type="hidden" id="budget'.$costcode->id.'" value="'.$costcode->budgetper.'"/>
						<td>'.$costcode->code.'<span class="cost-code" style="display:none;">"'. $costcode->codeforgraph.'"</span></td>
						<td>'.$imgName.'</td>
						<td>'.$costcode->cost.'</td>
						<td><span class="total-spent">'.$totalspend.'</span></td>
		              	<td id="lastpbar">'.$costcode->budget.'</td>
		              	<td id="progress'.$costcode->id.'">              	 	
			              	<span class="task-progress" style="display: none;">            	
			              	'.$costcode->manualprogress.'
			              	</span>
			              	<span class="turnoff-estcost" style="display: none;">            	
			              	'.$isestimate.'
			              	</span>
			              	'.$isprogressBar.'
			            </td>
		              	<td id="status'.$costcode->id.'">'.$costcode->status.'</td>
		              	<td>'.$costcode->actions.'</td>
						</tr>';		 
            } 
        } 
        else
        {
            $str = '';
        }
       
    	echo $str; die;
    }
    
	function aasort (&$array, $key)
	{
	    $sorter=array();
	    $ret=array();
	    reset($array);
	    foreach ($array as $ii => $va)
	    {
	        $sorter[$ii]=$va->$key;
	    }
	    $sortflag = 14;//SORT_NATURAL ^ SORT_FLAG_CASE;

	    asort($sorter, $sortflag );
	    foreach ($sorter as $ii => $va)
	    {
	        $ret[$ii]=$array[$ii];
	    }
	    $array=$ret;
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


		$poitem_title = 'Items with costcode '.$costcode;

		$header[] = array('Report type' , $poitem_title , '' , '' , '' , '' , '' , '' , '' , '');


		if($this->session->userdata('managedprojectdetails'))
		{

			$header[] = array('Project Title',$this->session->userdata('managedprojectdetails')->title  , '' , '' , '' , '' , '' , '' , '' , '');
			$header[] = array('' , '' , '' , '' , '' , '' , '' , '' , '' , '');
		}


		$header[] = array('ID' , 'PO#' , 'Code' , 'Item Name' , 'Unit' , 'Quantity' , 'Price EA' , 'Total Price' , 'Date Requested' , 'Status');

    	foreach($items  as  $enq_row)
    	{
    		$header[] = array($enq_row->id,  $enq_row->ponum ,  $enq_row->itemcode , $enq_row->itemname ,$enq_row->unit ,$enq_row->quantity , formatPriceNew($enq_row->ea) , formatPriceNew($enq_row->totalprice) ,$enq_row->daterequested , $enq_row->status);
    	}
    	createXls('costcode'.$costcode , $header);
    	die();

    }

//CUST PDF
    function custPDF($costcode,$project)
    {
    	$costcode = urldecode($costcode);
    	$costcodeitems = $this->costcode_model->getcostcodeitems($costcode,$project);

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


		$poitem_title = 'Items with costcode '.$costcode;

		$header[] = array('Report type:' , $poitem_title , '' , '' , '' , '' , '' , '' , '' , '');


		if($this->session->userdata('managedprojectdetails'))
		{

			$header[] = array('Project Title',$this->session->userdata('managedprojectdetails')->title  , '' , '' , '' , '' , '' , '' , '' , '');
			$header[] = array('' , '' , '' , '' , '' , '' , '' , '' , '' , '');
		}


		$header[] = array('ID' , 'PO#' , 'Code' , 'Item Name' , 'Unit' , 'Quantity' , 'Price EA' , 'Total Price' , 'Date Requested' , 'Status');

    	foreach($items  as  $enq_row)
    	{
    		$header[] = array($enq_row->id,  $enq_row->ponum ,  $enq_row->itemcode , $enq_row->itemname ,$enq_row->unit ,$enq_row->quantity , formatPriceNew($enq_row->ea) , formatPriceNew($enq_row->totalprice) ,$enq_row->daterequested , $enq_row->status);
    	}
		$headername = "ITEMS WITH COSTCODE";
    	createOtherPDF('costcode'.$costcode, $header,$headername);
    	die();


    }

    function items($costcode,$project = '') {
    	$costcode = str_replace('%7C', '/', $costcode);
    	$costcode=urldecode($costcode);
        $costcode = urldecode($costcode);
       
        $project = str_replace('%7C', '/', $project);
    	$project=urldecode($project);
        $project = urldecode($project);

        $costcodeitems = $this->costcode_model->getcostcodeitems($costcode,$project);
        $costcodeitems2 = $this->costcode_model->getcostcodeitems2($costcode,$project);
		
        if ($this->session->userdata('usertype_id') > 1)
        $wheretax = " and s.purchasingadmin = ".$this->session->userdata('purchasingadmin');
        else
        $wheretax = "";

        $cquery = "SELECT taxrate FROM ".$this->db->dbprefix('settings')." s WHERE 1=1".$wheretax." ";
        $taxrate = $this->db->query($cquery)->row();
                
      	$count = count($costcodeitems);
        
        $postatus = "incomplete";
        $totalquantity = 0;
        $totalreceived = 0;
        $newTotal = 0;
        if ($count >= 1) 
        {
	        foreach ($costcodeitems2 as $row) 
	        {	  
	        	$totalquantity = 0;
        		$totalreceived = 0;      	
        		
	        	$totalquantity +=  $row->newquantity;
	        	$totalreceived += $row->newreceived;
	        	
	        	$newTotal = $totalquantity-$totalreceived;
	        	
	        	$newQuote[$row->quote] = $newTotal;
	        }
        }
       
        $items = array();
        if ($count >= 1) {
            foreach ($costcodeitems as $row) {
            	$status = "incomplete";
            	if($row->quantity - $row->received == 0)
            	{
                	$status = "complete";
            	}	
                else 
                {
                	$status = "incomplete";
                }
               
                if(array_key_exists($row->quote,$newQuote))
                {
	                if($newQuote[$row->quote] == 0 || $newQuote[$row->quote] == '')
	                {
	                	$postatus = "complete";
	                }
	                else 
	                {
	                	$postatus = "incomplete";
	                }
                }    
                if ($row->item_img && file_exists('./uploads/item/' . $row->item_img)) 
				 { 
				 	 $imgName = '<img style="max-height: 120px;max-width: 100px; padding: 5px;" height="80" width="100" src="'.site_url('uploads/item/'.$row->item_img).'" alt="'. $row->item_img.'">';
				 } 
				 else 
				 { 
				 	 $imgName = '<img style="max-height: 120px;max-width: 100px;  padding: 5px;"height="80" width="100" src="'. site_url('uploads/item/big.png').'" alt="">'; 
                 }
                $wherecode = ""; 
                if(@$this->session->userdata('managedprojectdetails')->id){
            		$wherecode = "AND q.pid=".$this->session->userdata('managedprojectdetails')->id;
				}
                 
				if($this->session->userdata('usertype_id')>1)
						$wherecode .= " AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."' ";
				
                 // Code for getting discount/Penalty per invoice
					$query = "SELECT invoicenum, ai.company, ai.purchasingadmin, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice , r.paymentdate, r.datedue, r.paymentstatus 
			 FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,				   
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q WHERE r.awarditem=ai.id AND ai.award=a.id AND a.quote=q.id {$wherecode} AND ai.award='" . $row->award."' GROUP by invoicenum";		
					
					$invoicequery = $this->db->query($query);
        			$itemsinv = $invoicequery->result();
                    
        			if($itemsinv){

        				foreach ($itemsinv as $invoice) {


        					
        					if(@$invoice->company && @$invoice->purchasingadmin){

        						$sql = "SELECT duedate, term, penalty_percent, discount_percent, discountdate FROM " .$this->db->dbprefix('invoice_cycle') . " where company='" . $invoice->company . "'
				and purchasingadmin = '". $invoice->purchasingadmin ."'";
        						//echo $sql;
        						$resultinvoicecycle = $this->db->query($sql)->row();

        						$penalty_percent = 0;
        						$penaltycount = 0;
        						$discount_percent =0;

        						if($resultinvoicecycle){

        							if((@$resultinvoicecycle->penalty_percent || @$resultinvoicecycle->discount_percent) ){

        								if(@$invoice->datedue){

        									if(@$invoice->paymentstatus == "Paid" && @$invoice->paymentdate){
        										$oDate = $invoice->paymentdate;
        										$now = strtotime($invoice->paymentdate);
        									}else {
        										$oDate = date('Y-m-d');
        										$now = time();
        									}

        									$d1 = strtotime($invoice->datedue);
        									$d2 = strtotime($oDate);
        									$datediff =  (date('Y', $d2) - date('Y', $d1))*12 + (date('m', $d2) - date('m', $d1));
        									if(is_int($datediff) && $datediff > 0) {

        										$penalty_percent = $resultinvoicecycle->penalty_percent;
        										$penaltycount = $datediff;

        									}else{

        										$discountdate = $resultinvoicecycle->discountdate;
        										if(@$discountdate){
													$exploded = explode("-",@$invoice->datedue);
        											$exploded[2] = $discountdate;
        											$discountdt = implode("-",$exploded);
        											if ($now < strtotime($discountdt)) {         											
        												$discount_percent = $resultinvoicecycle->discount_percent;
        											}
        										}
        									}
        									
        									
        									if(@$discount_percent){

        										$row->totalprice = $row->totalprice - ($invoice->totalprice*$discount_percent/100);
        									}

        									if(@$penalty_percent){

        										$row->totalprice = $row->totalprice + (($invoice->totalprice*$penalty_percent/100)*@$penaltycount);
        									}
        									
        								}

        							}
        						}

        					}

        				}

        			}      			
        			// Code for getting discount/Penalty Ends
                 
                 
            	if($row->potype=="Contract"){
                $awarded = $this->quote_model->getawardedcontractbid($row->quote);
                $row->ea = "$ " . $row->ea;
                $row->quantity = '100%';
                $row->unit = 'N/A';
                $row->itemcode = 'N/A';
                $row->newreceived = $row->newreceived."%";
                $row->daterequested = 'N/A';
                $row->totalprice = $row->totalprice + $row->totalprice*(@$taxrate->taxrate/100);
                $row->totalprice = "$ " . round($row->totalprice,2);
                $row->itemname = htmlentities($row->itemname);
                $row->status = strtoupper($awarded->status);
                $row->actions = //$row->status=='COMPLETE'?'':
                        anchor('admin/quote/contracttrack/' . $row->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update'))
                ;
                $items[] = $row;
            	}else{
            		
            		 $awarded = $this->quote_model->getawardedbid($row->quote);
            	//echo '<pre>',print_r($row);die;
            	
            	if($row->IsMyItem == 0) 
                 {
                    $link =	'<a href="'.site_url("site/item/".$row->url).'">'.htmlentities($row->itemname).'</a>';
                 } 
                 else 
                 { 
                 	$link = htmlentities($row->itemname); 
                 }
              //  $row->ponum = '<a href="'.site_url('admin/quote/track').'/'.@$row->quote.'">'.$row->ponum.'</a>'; 
                $row->ponum = '<a href="javascript:void(0)" onclick="viewitems(\'' . @$row->quote . '\')">'.$row->ponum.'</a>'; 
                $row->itemcode = '<a href="javascript:void(0)" onclick="viewitems2(\'' . @$row->itemid . '\')">'.$row->itemcode.'</a>'; ; 	 
                $row->ea = "$ " . $row->ea;
                $row->totalprice = $row->totalprice + $row->totalprice*(@$taxrate->taxrate/100);
                $row->totalprice = "$ " . round($row->totalprice,2);
                $row->itemname = $link;
                $row->itemstatus = strtoupper($status);
                $row->status = strtoupper($postatus);
                $row->item_img = $imgName;
                $row->actions = //$row->status=='COMPLETE'?'':
                        anchor('admin/quote/track/' . $row->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update'))
                ;
                $items[] = $row;
            	}
            }
         
            foreach ($costcodeitems2 as $row2) {
            	
            	/*$status2 = "incomplete";
            	if($row2->quantity - $row2->received == 0)
                $status2 = "complete";
                else 
                $status2 = "incomplete";*/            	
            	
            	 $wherecode = "";
            	 if(@$this->session->userdata('managedprojectdetails')->id){
            		$wherecode = "AND q.pid=".$this->session->userdata('managedprojectdetails')->id;
				 } 
            	
				 if($this->session->userdata('usertype_id')>1)
						$wherecode .= " AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."' ";
				 
            	 // Code for getting discount/Penalty per invoice
					$query = "SELECT invoicenum, ai.company, ai.purchasingadmin, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice , r.paymentdate, r.datedue, r.paymentstatus 
			 FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,				   
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q WHERE r.awarditem=ai.id AND ai.award=a.id AND a.quote=q.id {$wherecode} AND ai.award='" . $row2->award."' GROUP by invoicenum";		
					
					$invoicequery = $this->db->query($query);
        			$itemsinv = $invoicequery->result();
                    
        			if($itemsinv){

        				foreach ($itemsinv as $invoice) {


        					
        					if(@$invoice->company && @$invoice->purchasingadmin){

        						$sql = "SELECT duedate, term, penalty_percent, discount_percent, discountdate FROM " .$this->db->dbprefix('invoice_cycle') . " where company='" . $invoice->company . "'
				and purchasingadmin = '". $invoice->purchasingadmin ."'";
        						//echo $sql;
        						$resultinvoicecycle = $this->db->query($sql)->row();

        						$penalty_percent = 0;
        						$penaltycount = 0;
        						$discount_percent =0;

        						if($resultinvoicecycle){

        							if((@$resultinvoicecycle->penalty_percent || @$resultinvoicecycle->discount_percent) ){

        								if(@$invoice->datedue){

        									if(@$invoice->paymentstatus == "Paid" && @$invoice->paymentdate){
        										$oDate = $invoice->paymentdate;
        										$now = strtotime($invoice->paymentdate);
        									}else {
        										$oDate = date('Y-m-d');
        										$now = time();
        									}

        									$d1 = strtotime($invoice->datedue);
        									$d2 = strtotime($oDate);
        									$datediff =  (date('Y', $d2) - date('Y', $d1))*12 + (date('m', $d2) - date('m', $d1));
        									if(is_int($datediff) && $datediff > 0) {

        										$penalty_percent = $resultinvoicecycle->penalty_percent;
        										$penaltycount = $datediff;

        									}else{

        										$discountdate = $resultinvoicecycle->discountdate;
        										if(@$discountdate){
													$exploded = explode("-",@$invoice->datedue);
        											$exploded[2] = $discountdate;
        											$discountdt = implode("-",$exploded);
        											if ($now < strtotime($discountdt)) { 	        											
        												$discount_percent = $resultinvoicecycle->discount_percent;
        											}
        										}
        									}
        									
        									
        									if(@$discount_percent){

        										$row2->totalprice = $row2->totalprice - ($invoice->totalprice*$discount_percent/100);
        									}

        									if(@$penalty_percent){

        										$row2->totalprice = $row2->totalprice + (($invoice->totalprice*$penalty_percent/100)*@$penaltycount);
        									}
        									
        								}

        							}
        						}

        					}

        				}

        			}      			
        			// Code for getting discount/Penalty Ends
            	
            	
            	
            	if($row2->potype=="Contract"){
            		
            	$awarded = $this->quote_model->getawardedcontractbid($row2->quote);
                $row2->ea = "$ " . $row2->ea;
                $row2->quantity = '100%';
                $row2->unit = 'Contract';
                $row2->itemcode = 'Contract';
                $row2->newreceived = $row2->newreceived;
                $row2->daterequested = isset($awarded->invoices[0]->items[0]->receiveddate)?$awarded->invoices[0]->items[0]->receiveddate:'N/A';
                $row2->totalprice = $row2->totalprice + $row2->totalprice*(@$taxrate->taxrate/100);
                $row2->totalprice = "$ " . round($row2->totalprice,2);
                
                $newarr = explode("$",$row2->ponum);
                if(count($newarr>1))
                $row2->ponum = $newarr[0]."".$row2->totalprice;
                
                $row2->itemname = htmlentities($row2->itemname);
                $row2->status = strtoupper($awarded->status);
                //$row2->newreceived = $row2->newreceived;
                $row2->actions = //$row->status=='COMPLETE'?'':
                        anchor('admin/quote/contracttrack/' . $row2->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update'))
                ;
                $items2[] = $row2;
            	}else{
                $awarded = $this->quote_model->getawardedbid($row2->quote);
                $row2->ea = "$ " . $row2->ea;
                $row2->totalprice = $row2->totalprice + $row2->totalprice*(@$taxrate->taxrate/100);
                $row2->totalprice = "$ " . round($row2->totalprice,2);
                
                $newarr = explode("$",$row2->ponum);
                if(count($newarr>1))
                $row2->ponum = $newarr[0]."".$row2->totalprice;
                
                $row2->itemname = htmlentities($row2->itemname);
                //$row2->itemstatus = strtoupper($status2);
                $row2->status = strtoupper($awarded->status);
                //$row2->newreceived = $row2->newreceived;
                $row2->actions = //$row->status=='COMPLETE'?'':
                        anchor('admin/quote/track/' . $row2->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update'))
                ;
                $items2[] = $row2;
            	}
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
        		
        		$project1 = $this->db->query($sql)->row();
        		$order->prjName = "Assigned to ".$project1->title;
        		$order->prjName .= "<br>";
        		$order->prjName .= "Assigned to '$costcode' costcode";
        	}else{
        		$order->prjName = "Pending Project Assignment";
        	}
        	$data['orders'][]=$order;
        }

		/****************/
        $data['jsfile'] = 'costcodeitemjs.php';
        $data ['addlink'] = '';
        $data ['heading'] = "Items with Costcode '$costcode'";
        $data ['bottomheading'] = "Store Orders With Costcode '$costcode'";

     
        
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/costcode">&lt;&lt; Back</a> &nbsp;<a class="btn btn-green" href="'.site_url('admin/costcode/custPDF')."/".$costcode."/".$project.'">View PDF</a>';

        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}

        $this->load->view('admin/datagrid', $data);
    }

    function add() {
        $this->_set_fields();
        $data ['heading'] = 'Add New Costcode';
        $data ['message'] = '';
        $data ['action'] = site_url('admin/costcode/add_costcode');
        if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
       
        $mp = $this->session->userdata('managedprojectdetails');
       
        if(@$mp->id){
        	
        	$data['parents'] = $mp->id;
        	
        }else {
	        $projectresult = $this->db->get('costcode')->result();
        	if($projectresult)
        	$data['parents'] = $projectresult[0]->project;
        }
        		
        if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        $data['projects'] = $this->db->get('project')->result();


        $mp = $this->session->userdata('managedprojectdetails');
        if(@$mp->id)
        {
        	$this->validation->project = $mp->id;
        }
        $id = $this->session->userdata('id');
        $setting=$this->settings_model->getalldata($id);
        if(empty($setting))
        	$data['settingtour']=$setting;
        else
        	$data['settingtour']=$setting[0]->tour;
        $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo();
        $data['viewname'] = 'costcode';
        $data['costcodes'] = $this->costcode_model->get_costcodes();
        
        if($this->session->userdata('managedprojectdetails')) 
        {
        $sql ="SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."' AND project='".$mp->id."' ORDER BY code";         }
        else 
        {
        $sql ="SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."' ORDER BY code";
        }
        $data['costcodesdata'] = $this->db->query ($sql)->result();
        
        $this->load->view('admin/costcode', $data);
    }

    function getcostcodefromproject(){

        $resultcostcode = $this->costcode_model->listHeirarchicalCombo($_POST['projectid']);

    	echo $resultcostcode; die;
    }
    
    function getprojectfromcostcode()
     { 
     	
     	 $resultpro = $this->costcode_model->listHeirarchicalComboPro($_POST['catid']);
     	 //echo "<pre>"; print_r($resultpro); die;
    	 echo $resultpro; die; 	
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
        elseif ($this->costcode_model->checkDuplicateCode($this->input->post('code'), 0,$this->input->post('project'))) {       	
            $data ['message'] = 'Duplicate Costcode';
            $data['parents'] = $this->db->get('costcode')->result();
            $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo();
            $this->load->view('admin/costcode', $data);
            //$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Duplicate Costcode</div></div>');
            //redirect('admin/costcode/add');
        } else {
            $itemid = $this->costcode_model->SaveCostcode();
            if(empty($this->session->userdata['managedprojectdetails']))
            {
            	$pid = $_POST['project'];
				$temp['managedproject'] = $pid;
				$temp['managedprojectdetails'] = $this->project_model->get_projects_by_id($pid);
				$this->session->set_userdata($temp);
            }
            else 
            {
            	$this->session->userdata['managedprojectdetails']->id = $_POST['project'];	
            }            
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox" style="display:inline;" id="step12" >Cost Code Added Successfully</div> &nbsp;&nbsp;&nbsp;&nbsp;<a href='.site_url('admin/dashboard').'>Go To Project Dashboard</a></div>');
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
        $this->validation->forcontract = $item->forcontract;
        $this->validation->estimate = $item->estimate;
        $this->validation->costcode_image = $item->costcode_image;
        
        $this->db->where('id', $id);
        if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        //$data['parents'] = $this->db->get('costcode')->result();
        $projectresult = $this->db->get('costcode')->result();
       
        if($projectresult)
        $data['parents'] = $projectresult[0]->parent;

        $this->db->where('id', $projectresult[0]->project);
        if ($this->session->userdata('usertype_id') > 1)
            $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
        $data['projects'] = $this->db->get('project')->result();

        $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo(0, 0, 0, $item->parent);
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
		$item = $this->costcode_model->get_costcodes_by_id($itemid);
        if ($this->validation->run() == FALSE) {
            $data ['message'] = $this->validation->error_string;
            $this->db->where('id !=', $itemid);
            if ($this->session->userdata('usertype_id') > 1)
                $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
            $data['parents'] = $this->db->get('costcode')->result();

            if ($this->session->userdata('usertype_id') > 1)
                $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
            $data['projects'] = $this->db->get('project')->result();

            $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo('0', 0, 0, $item->parent);
            $data ['action'] = site_url('admin/costcode/updatecostcode');
            $this->load->view('admin/costcode', $data);
        }
        elseif ($this->costcode_model->checkDuplicateCode($this->input->post('code'), $itemid, $this->input->post('project'))) {
            $data ['message'] = 'Duplicate Costcode';
            $this->db->where('id !=', $itemid);
            $data['parents'] = $this->db->get('costcode')->result();
            $data['parentcombooptions'] = $this->costcode_model->listHeirarchicalCombo('0', 0, 0, $item->parent);
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
        $fields ['forcontract'] = 'forcontract';
        $fields ['estimate'] = 'estimate';
        $fields ['cdetail'] = 'cdetail';
        $fields ['parent'] = 'Parent';
        $fields ['project'] = 'Project';
        $this->validation->set_fields($fields);
    }

    function _set_rules() {
        $rules ['code'] = 'trim|required';
        $rules ['cost'] = 'trim|required|numeric';

        $this->validation->set_rules($rules);
        $this->validation->set_message('required', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Please fill all mandatory fields.</div></div>');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }

    function get_cc_by_project(){
    	$pid = $this->input->get("projectId");
    	$uri_segment = 4;
    	$offset = $this->uri->segment($uri_segment);
    	$costcodes = $this->costcode_model->get_costcodes($this->limit, $offset);

    	echo json_encode($costcodes);
    }
    
    function DefaultImport()
    {
    	$data ['message'] = '';
        $data ['heading'] = 'Default Costcode List';
        $prodata=$this->db->get_where('project',array('id'=>$this->session->userdata('managedprojectdetails')->id))->row();
        if(@$prodata->defaultadded == 0){
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/costcode/AddDefaultImport/'.$this->session->userdata('managedprojectdetails')->id.'" id="step10">Apply These Default Cost Code For '.$this->session->userdata('managedprojectdetails')->title.' project.</a>'; }
        $sql ="SELECT * FROM ".$this->db->dbprefix('DefaultCostcode')." ORDER BY code";       
        $data['defaultcostcodesdata'] = $this->db->query ($sql)->result();        
        $this->load->view('admin/defaultcostcode', $data);
    	
    }
    
    function AddDefaultImport($id)
    {
    	$data ['message'] = '';
    	
    	$parentcombooptions = $this->costcode_model->rearrangedefaultcostcode($id, 0, 0);
    	//echo "<pre>",print_r($parentcombooptions); die;    	
    	$parent=array();
    	foreach($parentcombooptions as $rowupper){
    		//echo "<pre>",print_r($rowupper); die;
    		foreach($rowupper as $key=>$row){
    		
    		$this->db->where('id',$row);
    		$DefaultCostcode=$this->db->get('costcode')->row();
    		//echo "<pre>",print_r($DefaultCostcode); die;
    		if($key==0){
    			
    		$this->db->insert('costcode',array('project'=>$id,'purchasingadmin'=>$this->session->userdata('id'),'code'=>$DefaultCostcode->code,'cost'=>'500','cdetail'=>$DefaultCostcode->cdetail,'parent'=>0,'costcode_image'=>$DefaultCostcode->costcode_image,'creation_date'=>date('Y-m-d'),'estimate'=>'1'));
    		$parent=array();
    		$parent[0] = $this->db->insert_id();
    		}else{    				
    			$this->db->insert('costcode',array('project'=>$id,'purchasingadmin'=>$this->session->userdata('id'),'code'=>$DefaultCostcode->code,'cost'=>'500','cdetail'=>$DefaultCostcode->cdetail,'parent'=>$parent[$key-1],'costcode_image'=>$DefaultCostcode->costcode_image,'creation_date'=>date('Y-m-d'),'estimate'=>'1'));
    		$parent[$key] = $this->db->insert_id();	
    		}    		    		
    	 }	
    	}
    	
    	/*$DefaultCostcode=$this->db->get('DefaultCostcode')->result();   		    	    		
    	foreach ($DefaultCostcode as $DC) { 
        		 $this->db->insert('costcode',array('project'=>$id,'purchasingadmin'=>$this->session->userdata('id'),'code'=>$DC->code,'cost'=>'500','cdetail'=>$DC->cdetail,'parent'=>'0','costcode_image'=>$DC->costcode_image,'creation_date'=>date('Y-m-d'),'estimate'=>'1'));
        	} */
        $data ['message']="These Default Cost Codes are set For this project."; 
        $this->db->where('id',$id);
        $this->db->update('project',array('defaultadded'=>'1'));		
    	
        $data ['heading'] = 'Default Costcode List';
        $prodata=$this->db->get_where('project',array('id'=>$id))->row();
        $sql ="SELECT * FROM ".$this->db->dbprefix('DefaultCostcode')." ORDER BY code";       
        $data['defaultcostcodesdata'] = $this->db->query ($sql)->result();        
        $this->load->view('admin/defaultcostcode', $data);    	
    }

}

?>
