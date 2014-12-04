<?php
class itemcode extends CI_Controller
{
    private $limit = 10;


    function itemcode ()
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
        $this->load->model('admin/itemcode_model');
        $this->load->model('admin/quote_model');
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
        $this->load->model('admin/catcode_model');
        $data['pendingbids'] = $this->quote_model->getpendingbids();
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data['title'] = "Administrator";
        $this->load = new My_Loader();
        $this->load->template('../../templates/admin/template', $data);
    }

  
     function export()
    {
    	$itemcodes = $this->itemcode_model->get_itemcodes();
    	$count = count($itemcodes);
    	$items = array();
    	if ($count >= 1)
    	{
    		foreach ($itemcodes as $itemcode)
    		{
    			if($itemcode->awardedon)
    				$itemcode->awardedon = date("m/d/Y", strtotime($itemcode->awardedon));

    			$itemcode->ea = "$ " . $itemcode->ea;
    			$itemcode->totalpoprice = "$ " . $itemcode->totalpoprice;

    			$itemcode->awardedon = $itemcode->awardedon?$itemcode->awardedon:'';

				$specs="";

                $query = "SELECT companynotes,filename FROM ".$this->db->dbprefix('companyitem')." ci
        		 WHERE itemid = ".$itemcode->id." AND ci.type='Purchasing' AND ci.company='".$this->session->userdata('purchasingadmin')."'" ;

                $dataspecs = $this->db->query($query)->row();

                if($dataspecs) {
                	if($dataspecs->filename!="")
                	$specs = "Yes";
                	else if($dataspecs->companynotes!="")
                	$specs = "Yes";
                	else
                	$specs = "No";
                }else {
                	$specs = "No";
                }

                $itemcode->specs = $specs;



    			$items[] = $itemcode;
    		}

    		$data['items'] = $items;
    		$data['jsfile'] = 'itemcodejs.php';
    	}
    	else
    	{
    		$this->data['message'] = 'No Records';
    	}
    	$data['addlink'] = '';
    	$data['heading'] = 'Item Code Management';
    	$data['table'] = $this->table->generate();
    	$data['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/itemcode/add">Add Item Code</a>';
    	$data['addcatlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/catcode/addcat">Add Category</a>';
    	$data['addsubcatlink'] = false;

    	//uksort($array, 'strcasecmp');

    	$data['categories'] = $this->itemcode_model->getcategories(); ;

    	if ($this->session->userdata('usertype_id') == 2)
    	{
    		$data['addlink'] = '';
    		$data['addcatlink'] = '';
    		$data['addsubcatlink'] = '';
    	}
    	
		//===============================================================================
		
		$header[] = array('Report type','Item Code History','','','','','','');
				
		if($this->session->userdata('managedprojectdetails'))
		{
			$header[] = array('Project Title',$this->session->userdata('managedprojectdetails')->title,'','','','','','');
			
			$header[] = array('','','','','','','','');
			
		}	

		//------------------------------------------------------

    	$header[] = array('ID','Code','Item Name','Unit','Specs','Total purchased amount','Last awarded date','');

    			
		foreach($items  as  $enq_row)
    	{
  			
    		$item_price = $enq_row->totalpoprice;
					    				
    		$header[] = array($enq_row->id  , $enq_row->itemcode  ,  $enq_row->itemname ,  $enq_row->unit , $enq_row->specs, $item_price.chr(160)  , $enq_row->awardedon);
    	}
    	createXls('itemcode',$header);
    	die();

    }
	//PDF
	function itempdf()
    {
    	$itemcodes = $this->itemcode_model->get_itemcodes();
    	$count = count($itemcodes);
    	$items = array();
    	if ($count >= 1)
    	{
    		foreach ($itemcodes as $itemcode)
    		{
    			if($itemcode->awardedon)
    				$itemcode->awardedon = date("m/d/Y", strtotime($itemcode->awardedon));

    			$itemcode->ea = "$ " . $itemcode->ea;
    			$itemcode->totalpoprice = "$ " . $itemcode->totalpoprice;

    			$itemcode->awardedon = $itemcode->awardedon?$itemcode->awardedon:'';

				$specs="";

                $query = "SELECT companynotes,filename FROM ".$this->db->dbprefix('companyitem')." ci
        		 WHERE itemid = ".$itemcode->id." AND ci.type='Purchasing' AND ci.company='".$this->session->userdata('purchasingadmin')."'" ;

                $dataspecs = $this->db->query($query)->row();

                if($dataspecs) {
                	if($dataspecs->filename!="")
                	$specs = "Yes";
                	else if($dataspecs->companynotes!="")
                	$specs = "Yes";
                	else
                	$specs = "No";
                }else {
                	$specs = "No";
                }

                $itemcode->specs = $specs;



    			$items[] = $itemcode;
    		}

    		$data['items'] = $items;
    		$data['jsfile'] = 'itemcodejs.php';
    	}
    	else
    	{
    		$this->data['message'] = 'No Records';
    	}
    	$data['addlink'] = '';
    	$data['heading'] = 'Item Code Management';
    	$data['table'] = $this->table->generate();
    	$data['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/itemcode/add">Add Item Code</a>';
    	$data['addcatlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/catcode/addcat">Add Category</a>';
    	$data['addsubcatlink'] = false;

    	//uksort($array, 'strcasecmp');

    	$data['categories'] = $this->itemcode_model->getcategories(); ;

    	if ($this->session->userdata('usertype_id') == 2)
    	{
    		$data['addlink'] = '';
    		$data['addcatlink'] = '';
    		$data['addsubcatlink'] = '';
    	}
    	
		//===============================================================================
		
		$header[] = array('Report type:','Item Code History','','','','','');
				
		if($this->session->userdata('managedprojectdetails'))
		{
			$header[] = array('<b>Project Title</b>',$this->session->userdata('managedprojectdetails')->title,'','','','','');
			
			//$header[] = array('','','','','','','');
			
		}	

		//------------------------------------------------------

    	$header[] = array('<b>ID</b>','<b>Code</b>','<b>Item Name</b>','<b>Unit</b>','<b>Specs</b>','<b>Total purchased amount</b>','<b>Last awarded date</b>');

    			
		foreach($items  as  $enq_row)
    	{
  			
    		$item_price = $enq_row->totalpoprice;
					    				
    		$header[] = array($enq_row->id  , $enq_row->itemcode  ,  $enq_row->itemname ,  $enq_row->unit , $enq_row->specs, $item_price.chr(160)  , $enq_row->awardedon);
    	}
		$headername = "ITEM CODE MANAGEMENT";
    	createOtherPDF('itemcode', $header,$headername);
    	die();

    }
	
		
    //	function do_upload()
    //	{
    //		$config['upload_path'] = './uploads/';
    //		$config['allowed_types'] = 'gif|jpg|png';
    //		$config['max_size']	= '100';
    //		$config['max_width']  = '1024';
    //		$config['max_height']  = '768';
    //
    //		$this->load->library('upload', $config);
    //
    //		if ( ! $this->upload->do_upload())
    //		{
    //			$error = array('error' => $this->upload->display_errors());
    //
    //			$this->load->view('upload_form', $error);
    //		}
    //		else
    //		{
    //			$data = array('upload_data' => $this->upload->data());
    //
    //			$this->load->view('upload_success', $data);
    //		}
    //	}

    function delete_multiple(){

    	$myArray = $_POST['items'];
    	foreach($myArray as $myValue){
    		$sql="";
    		$sql = "DELETE i,a,ai FROM ". $this->db->dbprefix('awarditem')." ai INNER JOIN ".$this->db->dbprefix('award')." a ON  ai.award=a.id INNER JOIN ".$this->db->dbprefix('item')." i ON  i.id=ai.itemid ";
    		$sql .= "WHERE i.id=".$myValue;
    		//log_message('debug',$sql);
    		 $this->db->query($sql);
    	}
    }
    function index ()
    {
        $itemcodes = $this->itemcode_model->get_itemcodes();
      
        $count = count($itemcodes);
        $items = array();
        if ($count >= 1)
        {
            foreach ($itemcodes as $itemcode)
            {
            	if($itemcode->awardedon)
            	$itemcode->awardedon = date("m/d/Y", strtotime($itemcode->awardedon));

                $itemcode->ea = "$ " . $itemcode->ea;
                
                $itemcode->actions = "<input type='checkbox' name='del_group' class='del_group' value='".$itemcode->id."' />";
                $itemcode->actions .= anchor('admin/itemcode/update/' . $itemcode->id, '<span class="icon-2x icon-edit"></span>', array('class' => 'update')) . ' ' . anchor(
                'admin/itemcode/delete/' . $itemcode->id, '<span class="icon-2x icon-trash"></span>',
                array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"));
                if ($this->session->userdata('usertype_id') == 2)
                {
                    $itemcode->actions = '<a href="javascript:void(0)" onclick="updateitem('.$itemcode->id.')"><span class="icon-2x icon-edit"></span></a>';
                    //echo $itemcode->actions;die;
                }
                if ($itemcode->poitems || $itemcode->totalpoprice!=0)
                    $itemcode->actions .= ' ' . anchor('admin/itemcode/poitems/' . $itemcode->id, '<span class="icon-2x icon-search"></span>', array('class' => 'view'));
                if ($itemcode->minprices)
                    $itemcode->actions .= ' ' . anchor('admin/itemcode/companyprices/' . $itemcode->id, '<span class="icon-2x icon-file"></span>', array('class' => 'view'));

                $itemcode->totalpoprice = "$ " . $itemcode->totalpoprice;    
                    
                $itemcode->awardedon = $itemcode->awardedon?$itemcode->awardedon:'';

                $specs="";

                $query = "SELECT companynotes,filename FROM ".$this->db->dbprefix('companyitem')." ci
        		 WHERE itemid = ".$itemcode->id." AND ci.type='Purchasing' AND ci.company='".$this->session->userdata('purchasingadmin')."'" ;

                $dataspecs = $this->db->query($query)->row();

                if($dataspecs) {
                	if($dataspecs->filename!="")
                	$specs = "Yes";
                	else if($dataspecs->companynotes!="")
                	$specs = "Yes";
                	else
                	$specs = "No";
                }else {
                	$specs = "No";
                }

                $itemcode->specs = $specs;

                $items[] = $itemcode;
            }

            $data['items'] = $items;
            $data['jsfile'] = 'itemcodejs.php';
        }
        else
        {
            $this->data['message'] = 'No Records';
        }
        $data['addlink'] = '';
        $data['heading'] = 'Item Code Management';
        $data['table'] = $this->table->generate();
        $data['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/itemcode/add">Add Item Code</a>';
        $data['addcatlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/catcode/addcat">Add Category</a>';
        $data['addsubcatlink'] = false;

        //uksort($array, 'strcasecmp');

        $data['categories'] = $this->itemcode_model->getcategories(); ;

        if ($this->session->userdata('usertype_id') == 2)
        {
            $data['addlink'] = '';
            $data['addcatlink'] = '';
            $data['addsubcatlink'] = '';
        }

        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}

        $this->load->view('admin/itemlist', $data);
    }



   /* function poitems ($id)
    {
        $item = $this->itemcode_model->get_itemcodes_by_id($id);
        if (! $item)
            die();
        $poitems = $this->itemcode_model->getpoitems($item->id);
        $poitems2 = $this->itemcode_model->getpoitems2($item->id);
        //echo '<pre>';print_r($poitems);die;
        $count = count($poitems);
        $items = array();
        $items2 = array();
        if ($count >= 1)
        {
            foreach ($poitems as $row)
            {
                $awarded = $this->quote_model->getawardedbid($row->quote);
                $row->awardedon = date("m/d/Y", strtotime($row->awardedon));
                $row->ea = "$ " . $row->ea;
                $row->totalprice = "$ " . $row->totalprice;
                $row->status = strtoupper($awarded->status);
                $row->actions = //$row->status=='COMPLETE'?'':
anchor('admin/quote/track/' . $row->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update')); //.
                //anchor ('admin/quote/update/' . $row->bid,'<span class="icon-2x icon-search"></span>',array ('class' => 'update' ) )

                $items[] = $row;
            }
            $data['items'] = $items;

            foreach ($poitems2 as $row2)
            {
                $awarded = $this->quote_model->getawardedbid($row2->quote);
                $row2->awardedon = date("m/d/Y", strtotime($row2->awardedon));
                $row2->ea = "$ " . $row2->ea;
                $row2->totalprice = "$ " . $row2->totalprice;
                $row2->status = strtoupper($awarded->status);
                $row2->actions = //$row->status=='COMPLETE'?'':
anchor('admin/quote/track/' . $row->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update')); //.
                //anchor ('admin/quote/update/' . $row->bid,'<span class="icon-2x icon-search"></span>',array ('class' => 'update' ) )

                $items2[] = $row2;
            }
            $data['items2'] = $items2;

        }
        else
        {
            $this->data['message'] = 'No Items';
        }
        $sqlOrders = "SELECT * FROM " . $this->db->dbprefix('order') . " o,
        			 " . $this->db->dbprefix('orderdetails') . " od
        			 WHERE o.id=od.orderid
        			 AND o.purchasingadmin='" . $this->session->userdata('purchasingadmin')."'
        			 AND od.itemid=" . $id . " GROUP BY od.orderid";
        $resOrders = $this->db->query($sqlOrders)->result();
        $i = 0;
        foreach ($resOrders as $order)
        {
            $i++;
            $order->sno = $i;
            if (! is_null($order->project))
            {
                $sql = "SELECT *
					FROM " . $this->db->dbprefix('project') . " p
					WHERE id=" . $order->project;
                $project = $this->db->query($sql)->result();
                if($project)
                $order->prjName = "Assigned to " . $project[0]->title;
            }
            else
            {
                $order->prjName = "Pending Project Assignment";
            }
            $order->purchasedate = date("m/d/Y", strtotime($order->purchasedate));
            $data['orders'][] = $order;
        }
        $data['title_orders'] = "Orders with the current Item";
        $data['jsfile'] = 'itemcodeitemjs.php';
        $data['addlink'] = '';
        $data['heading'] = "PO items for '$item->itemcode'";
        $data['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/itemcode">&lt;&lt; Back</a>';
        $this->load->view('admin/datagrid', $data);
    }*/

    function poitems ($id)
    {
    	$item = $this->itemcode_model->get_itemcodes_by_id($id);
    	if (! $item)
    		die();
    	$poitems = $this->itemcode_model->getpoitems($item->id);
    	//echo '<pre>';print_r($poitems);die;
    	$count = count($poitems);
    	$items = array();
    	if ($count >= 1)
    	{
    		foreach ($poitems as $row)
    		{
    			$awarded = $this->quote_model->getawardedbid($row->quote);
    			$row->awardedon = date("m/d/Y", strtotime($row->awardedon));
    			$row->ea = "$ " . $row->ea;
    			$row->totalprice = "$ " . $row->totalprice;
    			$row->status = strtoupper($awarded->status);
    			$row->actions = //$row->status=='COMPLETE'?'':
    			anchor('admin/quote/track/' . $row->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update')); //.
    			//anchor ('admin/quote/update/' . $row->bid,'<span class="icon-2x icon-search"></span>',array ('class' => 'update' ) )

    			$items[] = $row;
    		}
    		$data['items'] = $items;
    	}
    	else
    	{
    		$this->data['message'] = 'No Items';
    	}
    	$sqlOrders = "SELECT * FROM " . $this->db->dbprefix('order') . " o,
        			 " . $this->db->dbprefix('orderdetails') . " od
        			 WHERE o.id=od.orderid
        			 AND o.purchasingadmin='" . $this->session->userdata('purchasingadmin')."'
        			 AND od.itemid=" . $id . " GROUP BY od.orderid";
    	log_message('debug',var_export($sqlOrders,true));
    	$resOrders = $this->db->query($sqlOrders)->result();
    	$i = 0;
    	foreach ($resOrders as $order)
    	{
    		$i++;
    		$order->sno = $i;
    		if (! is_null($order->project))
    		{
    			$sql = "SELECT *
					FROM " . $this->db->dbprefix('project') . " p
					WHERE id=" . $order->project;
    			$project = $this->db->query($sql)->result();
    			if(isset($project) && !(empty($project)))
    			{
    				$order->prjName = "Assigned to " . $project[0]->title."<br/>";
    			}
    		}
    		else
    		{
    			$order->prjName = "Pending Project Assignment"."<br/>";
    		}
    		
    		if (! is_null($order->costcode))
    		{
    			$sql = "SELECT *
					FROM " . $this->db->dbprefix('costcode') . " cc
					WHERE id=" . $order->costcode;
    			$costcode = $this->db->query($sql)->row();
    			
    			if(isset($costcode) && !(empty($costcode)))
    			{
    				$order->prjName .= "Assigned to Cost Code  '" . $costcode->code."'";
    			}
    			
    		}else{
    			$order->prjName .= "Pending Cost Code Assignment";
    		}
    		$order->purchasedate = date("m/d/Y", strtotime($order->purchasedate));
    		$data['orders'][] = $order;
    	}
    	$data['title_orders'] = "Orders with the current Item";
    	$data['jsfile'] = 'itemcodeitemjs.php';
    	$data['addlink'] = '';
    	$data['heading'] = "PO items for '$item->itemcode'  <a href='".site_url('admin/itemcode/poitems_export')."/".$id."' class='btn btn-green'>Export</a> &nbsp;<a href='".site_url('admin/itemcode/poitems_pdf')."/".$id."' class='btn btn-green'>View PDF</a>";
    	$data ['bottomheading'] = "Store Orders With Itemcode '$item->itemcode'";
    	$data['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/itemcode">&lt;&lt; Back</a>';

    	$uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}

    	$this->load->view('admin/datagrid', $data);
    }

    function poitems_export ($id)
    {
    	$item = $this->itemcode_model->get_itemcodes_by_id($id);
    	if (! $item)
    		die();
    	$poitems = $this->itemcode_model->getpoitems($item->id);
    	//echo '<pre>';print_r($poitems);die;
    	$count = count($poitems);
    	$items = array();
    	if ($count >= 1)
    	{
    		foreach ($poitems as $row)
    		{
    			$awarded = $this->quote_model->getawardedbid($row->quote);
    			$row->awardedon = date("m/d/Y", strtotime($row->awardedon));
    			$row->ea = "$ " . $row->ea;
    			$row->totalprice = "$ " . $row->totalprice;
    			$row->status = strtoupper($awarded->status);
    			$row->actions = //$row->status=='COMPLETE'?'':
    			anchor('admin/quote/track/' . $row->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update')); //.
    			//anchor ('admin/quote/update/' . $row->bid,'<span class="icon-2x icon-search"></span>',array ('class' => 'update' ) )

    			$items[] = $row;
    		}
    		$data['items'] = $items;
    	}
    	else
    	{
    		$this->data['message'] = 'No Items';
    	}
    	$sqlOrders = "SELECT * FROM " . $this->db->dbprefix('order') . " o,
        			 " . $this->db->dbprefix('orderdetails') . " od
        			 WHERE o.id=od.orderid
        			 AND o.purchasingadmin='" . $this->session->userdata('purchasingadmin')."'
        			 AND od.itemid=" . $id . " GROUP BY od.orderid";
    	$resOrders = $this->db->query($sqlOrders)->result();
    	$i = 0;
    	foreach ($resOrders as $order)
    	{
    		$i++;
    		$order->sno = $i;
    		if (! is_null($order->project))
    		{
    			$sql = "SELECT *
					FROM " . $this->db->dbprefix('project') . " p
					WHERE id=" . $order->project;
    			log_message('debug',$sql);
    			$project = $this->db->query($sql)->row();
    			if(!$project)
    			$order->prjName = "Project Deleted";
    			else     			
    			$order->prjName = "Assigned to " . $project->title;
    		}
    		else
    		{
    			$order->prjName = "Pending Project Assignment";
    		}
    		$order->purchasedate = date("m/d/Y", strtotime($order->purchasedate));
    		$data['orders'][] = $order;
    	}
    	$data['title_orders'] = "Orders with the current Item";
    	$data['jsfile'] = 'itemcodeitemjs.php';
    	$data['addlink'] = '';
    	$data['heading'] = "PO items for '$item->itemcode'";
    	$data['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/itemcode">&lt;&lt; Back</a>';


    	//  $this->load->view('admin/datagrid', $data);

    	//-----------------------------------------------------------


    	//===============================================================================

    	$header[] = array('PO#','Company','Date','Price EA','Quantity','Total price','');


    	if(isset($data['items']))
    	{
    		$items = $data['items'];


    		foreach($items as $item)
    		{
    		
				$header[] = array($item->ponum,$item->companyname,$item->daterequested ,formatPriceNew($item->ea),$item->quantity,formatPriceNew($item->totalprice),'');
    		}

    	}
    	createXls('poitems_export',$header);
    	die();
    }
	
	// PDF
	function poitems_pdf ($id)
    {
    	$item = $this->itemcode_model->get_itemcodes_by_id($id);
    	if (! $item)
    		die();
    	$poitems = $this->itemcode_model->getpoitems($item->id);
    	//echo '<pre>';print_r($poitems);die;
    	$count = count($poitems);
    	$items = array();
    	if ($count >= 1)
    	{
    		foreach ($poitems as $row)
    		{
    			$awarded = $this->quote_model->getawardedbid($row->quote);
    			$row->awardedon = date("m/d/Y", strtotime($row->awardedon));
    			$row->ea = "$ " . $row->ea;
    			$row->totalprice = "$ " . $row->totalprice;
    			$row->status = strtoupper($awarded->status);
    			$row->actions = //$row->status=='COMPLETE'?'':
    			anchor('admin/quote/track/' . $row->quote, '<span class="icon-2x icon-search"></span>', array('class' => 'update')); //.
    			//anchor ('admin/quote/update/' . $row->bid,'<span class="icon-2x icon-search"></span>',array ('class' => 'update' ) )

    			$items[] = $row;
    		}
    		$data['items'] = $items;
    	}
    	else
    	{
    		$this->data['message'] = 'No Items';
    	}
    	$sqlOrders = "SELECT * FROM " . $this->db->dbprefix('order') . " o,
        			 " . $this->db->dbprefix('orderdetails') . " od
        			 WHERE o.id=od.orderid
        			 AND o.purchasingadmin='" . $this->session->userdata('purchasingadmin')."'
        			 AND od.itemid=" . $id . " GROUP BY od.orderid";
    	$resOrders = $this->db->query($sqlOrders)->result();
    	$i = 0;
    	foreach ($resOrders as $order)
    	{
    		$i++;
    		$order->sno = $i;
    		if (! is_null($order->project))
    		{
    			$sql = "SELECT *
					FROM " . $this->db->dbprefix('project') . " p
					WHERE id=" . $order->project;
    			log_message('debug',$sql);
    			$project = $this->db->query($sql)->row();
    			if(!$project)
    			$order->prjName = "Project Deleted";
    			else     			
    			$order->prjName = "Assigned to " . $project->title;
    		}
    		else
    		{
    			$order->prjName = "Pending Project Assignment";
    		}
    		$order->purchasedate = date("m/d/Y", strtotime($order->purchasedate));
    		$data['orders'][] = $order;
    	}
    	$data['title_orders'] = "Orders with the current Item";
    	$data['jsfile'] = 'itemcodeitemjs.php';
    	$data['addlink'] = '';
    	$data['heading'] = "PO items for '$item->itemcode'";
    	$data['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/itemcode">&lt;&lt; Back</a>';


    	//  $this->load->view('admin/datagrid', $data);

    	//-----------------------------------------------------------


    	//===============================================================================

    	$header[] = array('PO#','Company','Date','Price EA','Quantity','Total price');


    	if(isset($data['items']))
    	{
    		$items = $data['items'];


    		foreach($items as $item)
    		{
    		
				$header[] = array($item->ponum,$item->companyname,$item->daterequested ,formatPriceNew($item->ea),$item->quantity,formatPriceNew($item->totalprice));
    		}

    	}
		$headername = "PO ITEMS";
    	createPDF('poitems_export', $header,$headername);
    	die();    	
    }

	
    function companyprices ($id)
    {
        $item = $this->itemcode_model->get_itemcodes_by_id($id);
        if (! $item)
            die();

     //$item = $this->itemcode_model->get_itemcodes_by_code ($item->itemcode);
        if (! $item)
        {
            $this->data['message'] = 'Wrong Itemcode';
        }
        else
        {
            //echo '<pre>';print_r($item);die;
            $count = count($item->minprices);
            $items = array();
            if ($count >= 1)
            {
                foreach ($item->minprices as $row)
                {
                    $row->substitute = $row->substitute ? 'Substitute' : '';
                    $row->price = '$' . $row->price;
                    $items[] = $row;
                }
                $data['items'] = $items;
            }
            else
            {
                $this->data['message'] = 'No Items';
            }
        }
        $data['jsfile'] = 'itemcodepricesjs.php';
        $data['addlink'] = '';
        $data['heading'] = "Company prices for '$item->itemcode'";
        $data['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/itemcode">&lt;&lt; Back</a>';

        $uid = $this->session->userdata('id');
		$setting=$this->settings_model->getalldata($uid);
		if($setting){
			$data['settingtour']=$setting[0]->tour;
		}

        $this->load->view('admin/datagrid', $data);
    }


    //Change By Dhruvisha On 11th Saturday 2014
    function addcat ()
    {
        $this->_set_catfields();
        $data['heading'] = 'Add New Category';
        $data['message'] = '';
        $data['action'] = site_url('admin/itemcode/add_catcode');

        $this->load->view('admin/category', $data);
    }


    function add_catcode ()
    {
        $data['heading'] = 'Add New Category';
        $data['action'] = site_url('admin/itemcode/add_catcode');
        $this->_set_catfields();
        $this->_set_catrules();
        //echo $this->validation->run ();
        if ($this->validation->run() == FALSE)
        {
            $data['message'] = $this->validation->error_string;
            $this->load->view('admin/category', $data);
        }
        elseif ($this->itemcode_model->checkDuplicateCat($this->input->post('catname'), 0))
        {
            $data['message'] = 'Duplicate Category';
            $this->load->view('admin/category', $data);

     //$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Duplicate Itemcode</div></div>');
        //redirect('admin/itemcode/add');
        }
        else
        {
            $itemid = $this->itemcode_model->SaveCategory();
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Category Added Successfully</div></div>');
            redirect('admin/itemcode');
        }
    }

    function _set_catfields ()
    {
        $fields['id'] = 'id';
        $fields['catname'] = 'category';
        $this->validation->set_fields($fields);
    }

    function _set_catrules ()
    {
        $rules['catname'] = 'trim|required';
        $this->validation->set_rules($rules);
        $this->validation->set_message('required', '* required');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }

    function addsubcat ()
    {
        $this->_set_subcatfields();
        $data['heading'] = 'Add New Sub Category';
        $data['message'] = '';
        $data['action'] = site_url('admin/itemcode/add_subcatcode');
        $data['category'] = $this->itemcode_model->getCategoryList();
        $this->load->view('admin/subcategory', $data);
    }

    function add_subcatcode ()
    {
        $data['heading'] = 'Add New Sub Category';
        $data['action'] = site_url('admin/itemcode/add_subcatcode');
        $data['category'] = $this->itemcode_model->getCategoryList();
        $this->_set_subcatfields();
        $this->_set_subcatrules();
        // echo $this->input->post('catid'); exit;
        //echo $this->validation->run ();
        if ($this->validation->run() == FALSE)
        {
            $data['message'] = $this->validation->error_string;
            $this->load->view('admin/subcategory', $data);
        }
        elseif ($this->itemcode_model->checkDuplicateSubCat($this->input->post('catid'), $this->input->post('subcat'), 0))
        {
            $data['message'] = 'Duplicate Sub Category';
            $this->load->view('admin/subcategory', $data);

     //$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Duplicate Itemcode</div></div>');
        //redirect('admin/itemcode/add');
        }
        else
        {
            $itemid = $this->itemcode_model->SaveSubCategory();
            $this->session->set_flashdata('message',
            '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Sub Category Added Successfully</div></div>');
            redirect('admin/itemcode');
        }
    }


    function _set_subcatfields ()
    {
        $fields['id'] = 'id';
        //$fields ['category'] = 'category';
        $fields['subcat'] = 'subcategory';
        $this->validation->set_fields($fields);
    }


    function _set_subcatrules ()
    {
        $rules['subcat'] = 'trim|required';
        $this->validation->set_rules($rules);
        $this->validation->set_message('required', '* required');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }

    function getcatitem ()
    {
        //  $catid = $_POST['catid'];
        $catid = $_POST['catid'];
        $this->db->where('category',$catid);
        $itemcodes = $this->db->get('item')->result();
        $str = '';
        if ($itemcodes != null)
        {
            foreach ($itemcodes as $sub)
            {
                $str .= '<option value="' . $sub->itemcode . '">' . $sub->itemcode . '</option>';
            }
        }
        else
        {
            $str .= '<option>No Item Code</option>';
        }
        echo $str;
    }

	function gatcatitem3(){
    	
    	header('Content-Type: application/x-json; charset=utf-8');
		echo(json_encode($this->items_model->get_items3($_POST['catid'])));
    	
    }
    
    function add ()
    {
        $catcodes = $this->catcode_model->get_categories_tiered();
        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $this->_set_fields();
        $data['heading'] = 'Add New Itemcode';
        $data['message'] = '';
        $data['action'] = site_url('admin/itemcode/add_itemcode');
        $data['category'] = $categories;
        $data['product_categories'] = false;
        $data['categories'] = $this->itemcode_model->getcategories();
        $data['companies'] = $this->db->get('company')->result();
        $this->validation->featuredsupplier = 38;
        $this->load->view('admin/itemcode', $data);
    }


    function add_itemcode ()
    {
        $data['heading'] = 'Add New Itemcode';
        $data['action'] = site_url('admin/itemcode/add_itemcode');
        //$data['category'] = $this->itemcode_model->getCategoryList();
        //$data['subcategory'] = $this->itemcode_model->getSubCategoryList();
        $this->_set_fields();
        $this->_set_rules();
        $catcodes = $this->catcode_model->get_categories_tiered();
        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $data['category'] = $categories;
        if ($this->validation->run() == FALSE)
        {
            $data['message'] = $this->validation->error_string;
            $data['categories'] = $this->itemcode_model->getcategories();
            $data['companies'] = $this->db->get('company')->result();
            $this->load->view('admin/itemcode', $data);
        }
        elseif ($this->itemcode_model->checkDuplicateCode($this->input->post('itemcode'), 0))
        {
            $data['message'] = 'Duplicate Itemcode';
            $data['categories'] = $this->itemcode_model->getcategories();
            $data['companies'] = $this->db->get('company')->result();
            $this->load->view('admin/itemcode', $data);
        }
        elseif ($this->itemcode_model->checkDuplicateUrl($this->input->post('url'), 0))
        {
            $data['message'] = 'Duplicate Itemcode';
            $data['categories'] = $this->itemcode_model->getcategories();
            $data['companies'] = $this->db->get('company')->result();
            $this->load->view('admin/itemcode', $data);
        }
        else
        {
        	 if(isset($_FILES['UploadFile']['name']))
                {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/item/';
            	$count=0;           	
            	foreach ($_FILES['UploadFile']['name'] as $filename)
            	{
            		$temp=$target;
            		$tmp=$_FILES['UploadFile']['tmp_name'][$count];
            		$origionalFile=$_FILES['UploadFile']['name'][$count];
            		$count=$count + 1;
            		$temp=$temp.basename($filename);
            		move_uploaded_file($tmp,$temp);
            		$temp='';
            		$tmp='';
            	}

            }
            $this->do_upload();
            $itemid = $this->itemcode_model->SaveItemcode();
            $this->session->set_flashdata('message',
            '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Item Code Added Successfully</div></div>');
            redirect('admin/itemcode');
        }
    }


    function update($id)
    {
        $this->_set_fields();
        $item = $this->itemcode_model->get_itemcodes_by_id($id);
        
        $this->db->where('id',$id);
        $itemcategoriesresult = $this->db->get('vw_item_category')->row();     
        $itemcategories = explode(",",$itemcategoriesresult->categories);         
        $this->validation->id = $id;
        $this->validation->itemcode = $item->itemcode;
        $this->validation->itemname = $item->itemname;
        $this->validation->description = $item->description;
        $this->validation->details = $item->details;
        $this->validation->unit = $item->unit;
        $this->validation->ea = $item->ea;
        $this->validation->notes = $item->notes;
        $this->validation->keyword = $item->keyword;
        $this->validation->lastquoted = $item->lastquoted;
        $this->validation->targetprice = $item->targetprice;
        //$this->validation->category = $item->category;
        $this->validation->category = $itemcategories;
        $this->validation->item_img = $item->item_img;
        $this->validation->external_url = $item->external_url;
        $this->validation->featuredsupplier = $item->featuredsupplier;
        $this->validation->instore = $item->instore;
        $this->validation->zoom = $item->zoom;
        $this->validation->wiki = $item->wiki;
        $this->validation->listinfo = $item->listinfo;
        $this->validation->url = $item->url;
        $this->validation->item_img_alt_text = $item->item_img_alt_text;
        $this->validation->length = $item->length;
        $this->validation->width = $item->width;
        $this->validation->height = $item->height;
        $this->validation->weight = $item->weight;
        $this->validation->tags = $item->tags;
        $this->validation->files = $item->files;
        $this->validation->filename = $item->filename;
        $this->validation->searchquery = $item->searchquery;
        $this->validation->increment = $item->increment;
        $data['minprices'] = $item->minprices;
        $data['poitems'] = $item->poitems;
        $catcodes = $this->catcode_model->get_categories_tiered();


        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $data['category'] = $categories;
        $totalminprice = $this->itemcode_model->getlowestquoteprice($item->itemcode);
        $daysavgprice = $this->itemcode_model->getdaysmeanprice($item->itemcode);
        if ($daysavgprice > $totalminprice)
            $trend = 'HIGH';
        elseif ($daysavgprice < $totalminprice)
            $trend = 'LOW';
        else
            $trend = 'EQUAL';
        if ($daysavgprice == null)
            $trend = 'NO DATA';
        $data['itempricetrend'] = $trend;
        $data['heading'] = 'Update Item Code';
        $data['message'] = '';
        $data['action'] = site_url('admin/itemcode/updateitemcode');
        $data['categories'] = $this->itemcode_model->getcategories();

        $query = "SELECT * FROM ".$this->db->dbprefix('company')."
        		 WHERE id IN (SELECT company FROM ".$this->db->dbprefix('companyitem')." WHERE type='Supplier' AND itemid='$id')";

        $data['companies'] = $this->db->query($query)->result();

        $this->db->where('itemid',$id);
        $this->db->order_by('postedon','DESC');
        $data['articles'] = $this->db->get('itemarticle')->result();

        $this->db->where('itemid',$id);
        $data['images'] = $this->db->where('is_video',0)->get('itemimage')->result();
        $data['videos'] = $this->db->where('is_video',1)->get('itemimage')->result();


        $relateditems = $this->db->select('r.relateditem')->where('item',$id)->from('relateditem r')->join('item i','r.item=i.id')->get()->result();
        $data['relateditems'] = array();
        foreach($relateditems as $ri)
        {
            $data['relateditems'][]=$ri->relateditem;
        }
        //print_r($relateditems);
        $data['items'] = $this->db->get('item')->result();

        $this->load->view('admin/itemcode', $data);
    }


    //relateditems
    function saverelateditem($itemid)
    {
        $this->db->where('item',$itemid)->delete('relateditem');
        if(@$_POST['item'])
        foreach($_POST['item'] as $item)
        {
            $insert = array('item'=>$itemid,'relateditem'=>$item);

            $this->db->insert('relateditem',$insert);
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Items saved successfully.</div>');

        redirect('admin/itemcode/update/'.$itemid);
    }


    function do_upload ()
    {
        $config['upload_path'] = './uploads/item/';
        $config['allowed_types'] = '*';
        //$config['max_size']	= '9000';
        //	$config['max_width']  = '1024';
        //	$config['max_height']  = '768';
        $this->load->library('upload', $config);
        if (! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

             //$this->load->view('upload_form', $error);
        }
        else
        {
            //var_dump($this->upload->data()); exit;
            $error = array('upload_data' => $this->upload->data());
            $this->_createThumbnail($_FILES["userfile"]["name"],'item',200,200);
             //$this->load->view('upload_success', $data);
        } //var_dump($error); exit;
        return $error;
    }


    function updateitemcode ()
    {
        $data['heading'] = 'Update Item Code';
        $data['action'] = site_url('message/updateitemcode');
        $this->_set_fields();
        $this->_set_rules();
        $this->do_upload();
        $itemid = $this->input->post('id');
        $item = $this->itemcode_model->get_itemcodes_by_id($itemid);
        $catcodes = $this->catcode_model->get_categories_tiered();
        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $data['category'] = $categories;
        $data['items'] = $this->db->get('item')->result();
        $relateditems = $this->db->select('r.relateditem')->where('item',$itemid)->from('relateditem r')->join('item i','r.item=i.id')->get()->result();
        $data['relateditems'] = array();
        foreach($relateditems as $ri)
        {
            $data['relateditems'][]=$ri->relateditem;
        }
        if ($this->validation->run() == FALSE)
        {
            $data['message'] = $this->validation->error_string;
            $data['action'] = site_url('admin/itemcode/updateitemcode');
            $data['categories'] = $this->itemcode_model->getcategories();
            $data['companies'] = $this->db->get('company')->result();
            $this->load->view('admin/itemcode', $data);
        }
        elseif ($this->itemcode_model->checkDuplicateUrl($this->input->post('url'), $itemid))
        {
            $data['message'] = 'Duplicate URL';
            $data['categories'] = $this->itemcode_model->getcategories();
            $data['companies'] = $this->db->get('company')->result();
            $this->load->view('admin/itemcode', $data);
        }
        elseif ($this->itemcode_model->checkDuplicateCode($this->input->post('itemcode'), $itemid))
        {
            $data['message'] = 'Duplicate Itemcode';
            $data['categories'] = $this->itemcode_model->getcategories();
            $data['companies'] = $this->db->get('company')->result();
            $this->load->view('admin/itemcode', $data);
        }
        else
        {
            $url = trim($this->input->post('external_url'));
            //die($url);
            if ($url)
            {
                $amazon_info = $this->get_amazon_details($url);
                //print_r($amazon_info);die;
                if ($amazon_info)
                {
                    $set_amazon['amazon_price'] = $amazon_info->amazon_price;
                    $set_amazon['amazon_name'] = $amazon_info->title;
                    $set_amazon['amazon_url'] = $amazon_info->DetailPageURL;
                    $set_amazon['item_id'] = $itemid;
                    //die($itemid);
                    $this->items_model->save_amazon($set_amazon);
                }
            }

            $this->do_upload();
            
             if(isset($_FILES['UploadFile']['name']))
                {
            	ini_set("upload_max_filesize","128M");
            	$target='uploads/item/';
            	$count=0;           	
            	foreach ($_FILES['UploadFile']['name'] as $filename)
            	{
            		$temp=$target;
            		$tmp=$_FILES['UploadFile']['tmp_name'][$count];
            		$origionalFile=$_FILES['UploadFile']['name'][$count];
            		$count=$count + 1;
            		$temp=$temp.basename($filename);
            		move_uploaded_file($tmp,$temp);
            		$temp='';
            		$tmp='';
            	}

            }
                      
            $this->itemcode_model->updateItemcode($itemid);
            if ($item->minprices)
                foreach ($item->minprices as $mp)
                {
                    $updatearray = array();
                    $updatearray['itemcode'] = $this->input->post('itemcode');
                    $updatearray['price'] = $this->input->post('price' . $mp->company);
                    $this->itemcode_model->db->where(array('company' => $mp->company, 'itemcode' => $mp->itemcode));
                    $this->itemcode_model->db->update('minprice', $updatearray);
                }
            $data['message'] = '<div class="success">Item Code has been updated.</div>';
            redirect('admin/itemcode/update/' . $itemid);

     //redirect('admin/itemcode/index');
        }
    }


    function delete ($id)
    {
        $this->itemcode_model->remove_itemcode($id);
        redirect('admin/itemcode', 'refresh');
    }


    function _set_fields ()
    {
        $fields['id'] = 'id';
        $fields['itemcode'] = 'itemcode';
        $fields['itemname'] = 'itemname';
        $fields['unit'] = 'unit';
        $fields['ea'] = 'ea';
        $fields['notes'] = 'notes';
        $fields['keyword'] = 'keyword';
        $fields['lastquoted'] = 'lastquoted';
        $fields['targetprice'] = 'targetprice';
        $fields['description'] = 'description';
        $fields['details'] = 'details';
        $fields['category'] = 'category';
        $fields['subcategory'] = 'subcategory';
        $fields['featuredsupplier'] = 'featuredsupplier';
        $fields['instore'] = 'instore';
        $fields['zoom'] = 'zoom';
        $fields['wiki'] = 'wiki';
        $fields['url'] = 'url';
        $fields['length'] = 'length';
        $fields['width'] = 'width';
        $fields['height'] = 'height';
        $fields['weight'] = 'weight';
        $fields['files'] = 'files';
        $fields['filename'] = 'filename';
        $fields['searchquery'] = 'searchquery';
        $fields['increment'] = 'increment';
        $this->validation->set_fields($fields);
    }


    function _set_rules ()
    {

        $rules['itemcode'] = 'trim|required';
        $rules['itemname'] = 'trim|required';
        $rules['weight'] = 'trim|required';
        $rules['url'] = 'trim|required';
        $this->validation->set_rules($rules);
        $this->validation->set_message('required', '* required');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }

    function getminpricecompanies()
    {
        $itemid = $_POST['itemid']; //urldecode($itemcode);
        $quantity = $_POST['quantity'];
        $item = $this->itemcode_model->get_itemcodes_by_id2($itemid, $quantity);

        //echo '<pre>';print_r($item);die;
        $ret = array();
        if(@$item->tierprices)
        {
            foreach ($item->tierprices as $tp)
            {
                $ret[] = array('id'=>$tp->companyid, 'title'=>$tp->companyname, 'price'=>$tp->price);
            }
        }
        //echo print_r($ret);die;
        echo json_encode($ret);
    }

    function getcompanypricetable ()
    {
    	$table = '';
    	$table2 = '';
        //print_r($_POST);
        $codeid = $_POST['codeid'];
        $itemid = $_POST['id']; //urldecode($itemcode);
        $quantity = $_POST['quantity'];
        $quantiid = $_POST['quantid'];
        $priceid = $_POST['priceid'];
        $item = $this->itemcode_model->get_itemcodes_by_id2($itemid, $quantity);
        
        //echo '<pre>'.$itemid;print_r($item->tierprices);die;
        echo '<div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h3>Company prices : <span id="minpriceitemcode">' . $item->itemcode . '</span></h3>
        	</div>
        	<div class="modal-body" id="minprices">
        	';
        //print_r($item);die;
        if (! $item)
            echo ('No Item found');
        elseif (! @$item->tierprices)
        {
            //echo 'New purchasing admin set up guide (project, cost code, companies)';//('No company prices for ' . $item->itemcode);
            echo 'No Company Prices Exist, Please issue a quote for this item';//('No company prices for ' . $item->itemcode);
        }
        else
        {         
            $seconds = time() - strtotime($item->lastquoted);
            $days = $seconds / (3600 * 24);
            //if ($days > 30)
                //$table .= "<b><font color='red'>Item has not been requited within 30 days.</font></b>";
            $table .= "<table class='table table-bordered'><tr><th>Company</th><th>Price</th><th width='30'></th></tr>";
            $table2 .= "<table class='table table-bordered'><tr><th colspan='3'>Quantity Discounts</th></tr>";
            foreach ($item->tierprices as $mp)
            {
            	$priceqtyresult = $this->getpriceqtydetails($mp->companyid, $itemid,$quantiid,$priceid);
            	if($priceqtyresult){
            		$table2 .= "<tr><td colspan='3'>" . $mp->companyname . "</td></tr>";
            		$table2 .= "<tr><td colspan='3'>" . $priceqtyresult . "</td></tr>";            	
            	}
                $selectbutton = "<input type='button' class='btn btn-small' onclick='selectcompany(\"$codeid\",\"{$mp->companyid}\",\"{$mp->price}\")' value='Select' data-dismiss='modal'>";
                $table .= "<tr><td>" . $mp->companyname . "</td><td>" . $mp->price . "</td><td>" . $selectbutton . "</td></tr>";
            }
            $table .= "</table>";
            $table2 .= "</table>";
            
            echo $table2;            
        }
        echo '</div>';
        
        echo '<div class="modal-body" id="minprices2">'.$table.'</div>';
    }


	function getpriceqtydetails($companyid, $itemid,$quantiid,$priceid){
    	
    	$this->db->where('company',$companyid);
    	$this->db->where('itemid',$itemid);
    	$qtyresult = $this->db->get('qtydiscount')->result();
    	if($qtyresult){
    		$strput = "";
    		$selectbutton2 = "";
			$purchasingadmin = $this->session->userdata('purchasingadmin');
    		$strput .= "<table class='table table-bordered'>";
    		foreach($qtyresult as $qtyres){
    			
    			if(isset($purchasingadmin)){
    				$sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$purchasingadmin' AND company='" . $companyid . "'";


    				$sqltier = "select tierprice from " . $this->db->dbprefix('companyitem') . "
				    where itemid='".$itemid."' AND company='" . $companyid . "' AND type = 'Supplier'";

    				$istierprice = $this->db->query($sqltier)->row();
    				if($istierprice){
    					$istier = $istierprice->tierprice;
    				}else
    				$istier = 0;

    				$tier = $this->db->query($sql)->row();
    				if ($tier && $istier)
    				{
    					$tier = $tier->tier;
    					$this->db->where('company', $companyid);
    					$pt = $this->db->get('tierpricing')->row();
    					if ($pt)
    					{
    						$deviation = $pt->$tier;
    						$qtyres->price = $qtyres->price + ($qtyres->price * $deviation / 100);
    						$qtyres->price = number_format($qtyres->price, 2);
    					}
    				}
    			}
    			
				$selectbutton2 = "<input type='button' class='btn btn-small' onclick='selectquantity(\"$qtyres->qty\",\"{$quantiid}\",\"{$qtyres->price}\",\"{$priceid}\")' value='Select' data-dismiss='modal'>";
    			$strput .= '<tr >
							 <td style="padding-bottom:9px;" class="col-md-8">'.$qtyres->qty.' or more: </td><td>$'.$qtyres->price.'</td><td>'. $selectbutton2 . '</td></tr>';
    		}
    		if($istier)
    		$strput .= '<tr><td colspan="3" style="text-align:center;"><strong>Tier Price is applied on top of qty. discount</strong></td></tr>';
    		$strput .= "</table>";
    		return $strput;
    	}

    }
    
    function getcompanypricetablebrow ()
    {
        $val = 'onblur=savclose()';
        $str = '';
        $category = $this->itemcode_model->getcategories();
        if ($category != null)
        {
            $sel = '';
            $str .= '<select name="catid" id="catid" onchange="getcatitem($(catid).val()); savclose();" ' . $val . '>';
            $str .= '<option value="-1">Select Category</option>';
            foreach ($category as $categorymain)
            {
                $str .= '<option value="' . $categorymain->id . '" ' . $sel . ' >' . $categorymain->catname . '</option>';
            }
        }
        else
        {
            $str .= '<option>No category</option>';
        }
        $str .= '</select>';
        $table = '';
        $table .= "<h2><b><font color='red'>Store</font></b><h2>";
        $table .= "<table class='table table-bordered'>";
        $table .= "<tr><td ><div align='right'><b>Category<b></div></td><td>" . $str . "</td></tr>";

        $val123 = 'onchange="javascript:savclose()"';
        $table .= "<tr><td ><div align='right'><b>Item</b></div></td><td>";
        $table .= '<select name="catiditem" id="catiditem" ' . $val123 . ' >';
        $table .= '</select>';
        $table .= " </td></tr>";
        $table .= "<tr><td colspan='100%' >";
        $table .= '<div align="center">
                    <button aria-hidden="true" data-dismiss="modal" class="btn btn-primary" type="button">Save</button></div>';
        $table .= " </td></tr>";
        $table .= "</table>";
        die($table);
    }


    function gethistory ()
    {
        $company = $_POST['companyid'];
        $itemid = $_POST['itemid'];

        $sql1 = "SELECT ai.quantity, ai.ea, q.ponum, a.quote, a.submitdate `date`, 'quoted',ai.itemcode
			   	FROM
				" . $this->db->dbprefix('biditem') . " ai, " . $this->db->dbprefix('bid') . " a,
				" . $this->db->dbprefix('quote') . " q
				WHERE
				ai.bid=a.id AND a.quote=q.id AND ai.itemid='$itemid'
				AND a.company='$company' AND ai.itemid='$itemid'
				AND a.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
				";
        $sql2 = "SELECT ai.quantity, ai.ea, q.ponum, a.quote, a.awardedon `date`, 'awarded',ai.itemcode
			   	FROM
				" . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a,
				" . $this->db->dbprefix('quote') . " q
				WHERE
				ai.award=a.id AND a.quote=q.id AND ai.itemid='$itemid'
				AND ai.company='$company' AND ai.itemid='$itemid'
				AND a.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
				";
        $sql = $sql1 . " UNION " . $sql2;

        $itemnamesql = "SELECT * FROM " . $this->db->dbprefix('item') . " i WHERE i.id='$itemid'";
        $itemqry = $this->db->query($itemnamesql);
        $itemnameResult = $itemqry->result_array();

        $query = $this->db->query($sql);
        if ($query->num_rows > 0)
        {
            $result = $query->result();

            $avgforpricedays = $this->itemcode_model->getdaysmeanprice($itemid);
            $avgforpricedays = number_format($avgforpricedays, 2);
            $sqlavg = "SELECT AVG(ea) avgprice FROM " . $this->db->dbprefix('awarditem') . " ai,
            		" . $this->db->dbprefix('award') . " a
				  WHERE ai.itemid='$itemid' AND ai.award=a.id AND ai.company='$company'
				  AND a.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
			";
            $sqlavg = "SELECT AVG(ea) avgprice FROM " . $this->db->dbprefix('biditem') . " bi,
            		" . $this->db->dbprefix('bid') . " b
				  WHERE bi.itemid='$itemid' AND bi.bid=b.id AND b.company='$company'
				  AND b.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
			";
            //die($sqlavg);
            $itemname = 'Itemcode :'.isset($itemnameResult[0]['itemcode']) ? $itemnameResult[0]['itemcode'] : '' ;
            $companyavgpricefordays = $this->db->query($sqlavg)->row()->avgprice;
            $companyavgpricefordays = number_format($companyavgpricefordays, 2);
            if ($companyavgpricefordays > $avgforpricedays)
                $overalltrend = 'HIGH';
            elseif ($companyavgpricefordays < $avgforpricedays)
                $overalltrend = 'LOW';
            else
                $overalltrend = 'GOOD';
            $overalltrend = "<b><font color='red'>$overalltrend</font></b>";
            $pricedays = $this->settings_model->get_current_settings()->pricedays;
            $trendstring = 'Price Trend: ' . $overalltrend .
            				"(item avg for $pricedays days: $avgforpricedays,
            				company avg price: $companyavgpricefordays.)<br/>";
            if ($avgforpricedays == 0)
                $trendstring .= 'Item not awarded for set days.';
            if ($companyavgpricefordays == null)
                $trendstring .= 'Item not awarded to this company.';


            $ret = $trendstring;
            $ret .= '<table class="table table-bordered">';
            $ret .= '<tr><th>Date</th><th>Status</th><th>PO#</th><th>Trend</th><th>Qty.</th><th>Price</th></tr>';
            foreach ($result as $item)
            {
                if ($item->ea > $avgforpricedays)
                    $trend = 'high';
                elseif ($item->ea < $avgforpricedays)
                    $trend = 'low';
                else
                    $trend = 'good';
                if ($avgforpricedays == null)
                    $trend = 'NO DATA';
                $ret .= '<tr><td>' . date('m/d/Y',strtotime($item->date)) . '</td><td>' . $item->quoted . '</td><td>' . $item->ponum . '</td><td width="64"><img src="' . site_url('templates/admin/images/'.$trend.'.png') . '" width="64"/></td><td>' . $item->quantity . '</td><td>' . $item->ea .
                 '</td></tr>';
            }
            $ret .= '</table>';
            echo $ret.'*#*#$'.$itemname;
        }
        die();
    }


    public function get_amazon_details ($url = false)
    {
        $do_return = false;
        if (! $url)
        {
            $url = $this->input->post('url');
        }
        else
        {
            $do_return = true;
        }
        $reg = '#(?:http://(?:www\.){0,1}amazon\.com(?:/.*){0,1}(?:/dp/|/gp/product/))(.*?)(?:/.*|$)#';
        $matches = array();
        preg_match($reg, $url, $matches);
        //if(isset($matches[1]))
            $asin = $matches[1];
        //else
            //return;
        //die;
        require_once 'application/libraries/aws_signed_request.php';
        $public_key = $this->config->config['public_key']; //'AKIAJ7PBPYZLPRSQFUPQ';
        $private_key = $this->config->config['private_key']; //'oxteCJsIqntN5hB/2hgKc8u7MX29HxaCbqM6Kk/a';
        $associate_tag = $this->config->config['associate_tag']; //'optimaitsolut-20';
        $request = aws_signed_request('com', array('Operation' => 'ItemLookup', 'ItemId' => $asin, 'ResponseGroup' => 'Large'), $public_key, $private_key, $associate_tag);
        $response = @file_get_contents($request);

        if ($response === FALSE)
        {
            echo "Request failed.\n";
        }
        else
        {
            $pxml = simplexml_load_string($response);
            if ($pxml === FALSE)
            {
                echo "Response could not be parsed.\n";
            }
            else
            {
                $return = new stdClass();
                foreach ($pxml->Items->Item as $Item)
                {
                    $return->ASIN = (string) $Item->ASIN;
                    $return->DetailPageURL = (string) $Item->DetailPageURL;
                    $return->title = (string) $Item->ItemAttributes->Title;
                    $return->amazon_price = $Item->OfferSummary->LowestNewPrice->Amount / 100;
                    $return->description = (string) $Item->EditorialReviews->EditorialReview->Content;
                    $return->details = implode("<br>", (array) $Item->ItemAttributes->Feature);
                    //echo 'asdf';die;//json_encode($return);die;
                    if ($do_return)
                    {
                        return $return;
                    }
                    else
                    {
                        echo json_encode($return);
                    }
                }
            }
        }
        if ($do_return)
        {
            return false;
        }
    }


    function amazon ()
    {
        require_once 'application/libraries/aws_signed_request.php';
        $public_key = $this->config->config['public_key']; //'AKIAJ7PBPYZLPRSQFUPQ';
        $private_key = $this->config->config['private_key']; //'oxteCJsIqntN5hB/2hgKc8u7MX29HxaCbqM6Kk/a';
        $associate_tag = $this->config->config['associate_tag']; //'optimaitsolut-20';
        $request = aws_signed_request('com', array('Operation' => 'ItemSearch', 'Keywords' => $_POST['keyword'], 'SearchIndex' => 'Industrial', 'Sort' => 'price', 'ResponseGroup' => 'Large'),
        $public_key, $private_key, $associate_tag);
        $response = @file_get_contents($request);
        if ($response === FALSE)
        {
            echo "Request failed.\n";
        }
        else
        {
            $pxml = simplexml_load_string($response);
            if ($pxml === FALSE)
            {
                echo "Response could not be parsed.\n";
            }
            else
            {
                echo '<table class="table table-bordered">';
                echo '<tr><th>ASIN</th><th>Title</th><th>Price</th></tr>';
                foreach ($pxml->Items->Item as $Item)
                {
                    echo "<tr><td>{$Item->ASIN}</td><td>{$Item->ItemAttributes->Title}</td><td>{$Item->OfferSummary->LowestNewPrice->FormattedPrice}</td></tr>";
                }
                echo '</table>';
            }
        }
    }


    function ajaxdetail ($id = 0)
    {
        $this->_set_fields();
        $item = $this->itemcode_model->get_itemcodes_by_id($id);
      //  echo '<pre>';print_r($item);die;
        $data['item'] = $item;
        $this->validation->id = $id;
        $data['minprices'] = array();
        $data['poitems'] = '';
        $trend = '';
        if (! empty($item))
        {
            $data['minprices'] = $item->minprices;
            $data['poitems'] = $item->poitems;
            $totalminprice = $this->itemcode_model->getlowestquoteprice($item->id);
            $daysavgprice = $this->itemcode_model->getdaysmeanprice($item->id);
            if ($daysavgprice > $totalminprice)
                $trend = 'HIGH';
            elseif ($daysavgprice < $totalminprice)
                $trend = 'LOW';
            else
                $trend = 'EQUAL';
            if ($daysavgprice == null)
                $trend = 'NO DATA';
        }
        $data['itempricetrend'] = $trend;
        $data['heading'] = 'Item Code Detail for '.$item->itemcode;
        $data['message'] = '';
        $data['action'] = site_url('admin/itemcode/updateitemcode');
        $this->load->template('../../templates/admin/blank', $data);
        $this->load->view('admin/itemcode-ajaxdetail', $data);
    }

    ///////inventory
    function showeditform()
    {
        $itemid = $_POST['itemid'];
        $query = "SELECT i.*, ci.companynotes, ci.filename, ci.projectid, ci.id as cid FROM ".$this->db->dbprefix('item')." i LEFT JOIN
        		 ".$this->db->dbprefix('companyitem')." ci ON ci.itemid=i.id AND ci.type='Purchasing'
        		 AND ci.company='".$this->session->userdata('purchasingadmin')."' WHERE i.id='$itemid'";

        $item = $this->db->query($query)->row();
        //print_r($item);die;

        if($item->projectid){
				$arrproj = explode(",",$item->projectid);

				if($item->projectid != -1){
					$this->db->where('companyitemid',$item->cid);
					$companyprojectitem = $this->db->get('company_projectitem_notes')->row();
					if($companyprojectitem)
					$item->companynotes = $companyprojectitem->companynotes;
					else
					$item->companynotes = $item->companynotes;
				}else
				$item->companynotes = $item->companynotes;
		}


        $data['item'] = $item;

        $query2 = "SELECT title,id FROM ".$this->db->dbprefix('project')." where purchasingadmin =".$this->session->userdata('purchasingadmin');
        $projectdata = $this->db->query($query2)->result();
        $data['projectdata'] = $projectdata;

        $this->load->template('../../templates/admin/blank');
        $this->load->view('admin/inventory/miniform', $data);
    }

    function saveinventory()
    {
        $itemid = $_POST['itemid'];
        $pa = $this->session->userdata('purchasingadmin');


		if(isset($_FILES['filename']['tmp_name']))
		if(is_uploaded_file($_FILES['filename']['tmp_name']))
		{
			$ext = end(explode('.', $_FILES['filename']['name']));
			$nfn = md5(uniqid().date('YmdHi')).'.'.$ext;
			if(move_uploaded_file($_FILES['filename']['tmp_name'], "uploads/item/".$nfn))
			{
				$_POST['filename'] = $nfn;
			}
		}

        $where = array();
        $where['company'] = $pa;
        $where['itemid'] = $itemid;
        $where['type'] = 'Purchasing';

        $i=1;
        $projectids = "";
        $iscompnotes = "";
        if(isset($_POST['projectid'])){
        	foreach($_POST['projectid'] as $projectid){

        		if($projectid != -1)
        		$iscompnotes = 1;

        		if($i<count($_POST['projectid']))
        		$projectids .= $projectid.",";
        		else
        		$projectids .= $projectid;
        		$i++;
        	}
        }
        $_POST['projectid'] = $projectids;

        $this->db->where($where);
        $check = $this->db->get('companyitem')->row();
        //echo "<pre>",print_r($check->id); die;
        //print_r($check);print_r($_POST);die;


        if($check)
        {
        	 if($iscompnotes==1){
        	 	$where1 = "";

        	 	$compnotedat['companynotes'] = $_POST['companynotes'];
        	 	$where1['companyitemid'] = $check->id;
        	 	$this->db->where($where1);
        		$check1 = $this->db->get('company_projectitem_notes')->row();
        		if($check1){
        			$this->db->where($where1);
            		$this->db->update('company_projectitem_notes',$compnotedat);
        		}else{

            		$compnotedat['companyitemid'] = $check->id;
            		$this->db->insert('company_projectitem_notes',$compnotedat);
        		}
        		unset($_POST['companynotes']);
        	 }

            $this->db->where($where);
            $this->db->update('companyitem',$_POST);
        }
        else
        {
        	$_POST['type'] = 'Purchasing';
            $_POST['company'] = $pa;
            if($iscompnotes==1){
            	$compnotedat['companynotes'] = $_POST['companynotes'];
            	unset($_POST['companynotes']);
            }
            $this->db->insert('companyitem',$_POST);
            $companyitemlastid = $this->db->insert_id();

            if($iscompnotes==1){
        	 	$where1 = "";

        	 	$compnotedat['companyitemid'] = $companyitemlastid;
            	$this->db->insert('company_projectitem_notes',$compnotedat);

        	 }
        }
        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Item details saved successfully.</div>');

        redirect('admin/itemcode');
    }

    //////// articles
    function addarticle($itemid)
    {
        if(!$itemid)
            die;
        $data['itemid'] = $itemid;
        $this->load->view('admin/article', $data);
    }

    function editarticle($id)
    {
        if(!$id)
            die;
        $data['article'] = $this->db->where('id',$id)->get('itemarticle')->row();
        $data['links'] = $this->db->where('article',$id)->get('articlelink')->result();
        $articleitems = $this->db->select('item.id')->where('article',$id)->from('articleitem')->join('item','item=item.id')->get()->result();
        $data['articleitems'] = array();
        foreach($articleitems as $ai)
        {
            $data['articleitems'][]=$ai->id;
        }
        $data['items'] = $this->db->get('item')->result();
        $data['itemid'] = $data['article']->itemid;
        $this->load->view('admin/article', $data);
    }

    function savearticle($itemid)
    {
        unset($_POST['_wysihtml5_mode']);
		$_POST['title'] = mysql_real_escape_string($_POST['title']);
		if($this->itemcode_model->checkDuplicateArticleUrl($_POST['url'],$_POST['id']))
		{
            $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Duplicate URL.</div>');
    		if($_POST['id'])
            redirect('admin/itemcode/editarticle/'.$_POST['id']);
            else
            redirect('admin/itemcode/addarticle/'.$itemid);
		}
        if($_POST['id'])
        {
            $this->db->where('id',$_POST['id'])->update('itemarticle',$_POST);
        }
        else
        {
            $_POST['postedon'] = date('Y-m-d');
            $this->db->insert('itemarticle',$_POST);
        }
        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Article saved successfully.</div>');
		redirect('admin/itemcode/update/'.$itemid);
    }

    function deletearticle($id,$itemid)
    {
        $this->db->where('id',$id)->delete('itemarticle');

        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Article deleted successfully.</div>');

        redirect('admin/itemcode/update/'.$itemid);
    }

    //images
    function saveimage($itemid)
    {
    	error_reporting(0);
		if(is_uploaded_file($_FILES['filename']['tmp_name']))
		{
			$ext = end(explode('.', $_FILES['filename']['name']));
			$nfn = md5(uniqid().date('YmdHi')).'.'.$ext;
			if(move_uploaded_file($_FILES['filename']['tmp_name'], "uploads/item/".$nfn))
			{
			    $this->_createThumbnail($nfn,'item',200,200);
			    //$this->convertToFlv( "uploads/item/".$nfn, "uploads/item/thumbs/".$nfn );
			    $insert = array();
			    $insert['itemid'] = $itemid;
				$insert['filename'] = $nfn;
				//print_r($insert);
				$this->db->insert('itemimage',$insert);
			}
		}
		 else{
				echo $this->upload->display_errors();	die;
			}
        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Image saved successfully.</div>');
		redirect('admin/itemcode/update/'.$itemid);
    }

    function savevideoid($itemid){
    	if(isset($_POST['videoid']) && $_POST['videoid']!=""){
    		$videoid = $_POST['videoid'];
    		$insert = array();
    		$insert['itemid'] = $itemid;
    		$insert['filename'] = $videoid;
    		$insert['is_video'] = 1;
    		$this->db->insert('itemimage',$insert);
    	}

    	 $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Video ID saved successfully.</div>');
		redirect('admin/itemcode/update/'.$itemid);
    }

    function convertToFlv( $input, $output ) {
    	//echo "Converting $input to $output<br />";
    	$command = "ffmpeg -v 0 -y -i $input -vframes 1 -ss 5 -vcodec mjpeg -f rawvideo -s 286x160 -aspect 16:9 $output ";
    	// echo "$command<br />";
    	if(shell_exec( $command )){
    		echo "succes";
    	}else
    	echo "failed";
    	echo "Converted<br />";
    }

    function deleteimage($id,$itemid)
    {
        $this->db->where('id',$id)->delete('itemimage');

        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Image deleted successfully.</div>');

        redirect('admin/itemcode/update/'.$itemid);
    }

    //links
    function savearticlelink($articleid)
    {
		$insert = array();
		$insert['article'] = $articleid;
		$insert['title'] = $_POST['title'];
		$insert['link'] = $_POST['link'];
		if(is_uploaded_file($_FILES['filename']['tmp_name']))
		{
			$ext = end(explode('.', $_FILES['filename']['name']));
			$nfn = md5(uniqid().date('YmdHi')).'.'.$ext;
			if(move_uploaded_file($_FILES['filename']['tmp_name'], "uploads/item/".$nfn))
			{
			    $this->_createThumbnail($nfn,'item',200,200);
				$insert['filename'] = $nfn;
				//print_r($insert);
			}
		}
		$this->db->insert('articlelink',$insert);
        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Link saved successfully.</div>');
		redirect('admin/itemcode/editarticle/'.$articleid);
    }

    function deletearticlelink($id,$articleid)
    {
        $this->db->where('id',$id)->delete('articlelink');

        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Link deleted successfully.</div>');

        redirect('admin/itemcode/editarticle/'.$articleid);
    }

    //articleitems
    function savearticleitem($articleid)
    {
        $this->db->where('article',$articleid)->delete('articleitem');
        if(@$_POST['item'])
        foreach($_POST['item'] as $item)
        {
            $insert = array('article'=>$articleid,'item'=>$item);

            $this->db->insert('articleitem',$insert);
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Items saved successfully.</div>');

        redirect('admin/itemcode/editarticle/'.$articleid);
    }


	function _createThumbnail($fileName, $foldername="", $width=170, $height=150)
	{
		$config['image_library'] = 'gd2';
		$config['source_image'] = 'uploads/'.($foldername?$foldername.'/':'') . $fileName;
		$config['new_image'] = 'uploads/'.($foldername?$foldername.'/':'').'thumbs/' . $fileName;
		$config['create_thumb'] = FALSE;
		$config['maintain_ratio'] = TRUE;
		$image_config['x_axis'] = '0';
		$image_config['y_axis'] = '0';
		$config['width'] = $width;
		$config['height'] = $height;

		$this->load->library('image_lib', $config);
		if(!$this->image_lib->resize()) echo $this->image_lib->display_errors();
	}
}
?>