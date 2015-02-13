<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends CI_Controller 
{
	private $limit = 20;
	public function Inventory()
	{
		ini_set("memory_limit","512M");
		ini_set("max_execution_time", 700);
	    parent::__construct ();
	    
		$data ['title'] = 'Inventory';
		$this->load->dbforge();
		$this->load->model ('inventorymodel', '', TRUE);
		$this->load->model ('quotemodel', '', TRUE);
		$this->load->model ('messagemodel', '', TRUE);
		$this->load->model('companymodel', '', TRUE);
		$this->load->model ('admin/itemcode_model', '', TRUE);
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if($this->session->userdata('company')) $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
		 if ($this->session->userdata('company')) {    
            $data['pagetour'] = $this->companymodel->getcompanybyid($this->session->userdata('company')->id); }
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/front/template', $data);
	}
    
	public function index()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
        $limit = 10;
        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;
		$items = $this->inventorymodel->getItems($company->id,$limit, $start);
		
        $data['totalcount'] = $this->inventorymodel->count_all($company->id);
        $data['currentpage'] = $_POST['pagenum'] + 1;
        $data['totalpages'] = ceil($data['totalcount'] / $limit);
        $data['submitmethod'] = 'POST';
        $data['submiturl'] = 'inventory';
        $data['pagingfields'] = $_POST;
        $data['suppliers'] = array();
		
		$this->db->where('company',$company->id);
		$tier = $this->db->get('tierpricing')->row();
		
		$data['tier'] = $tier;
		$data['items'] = $items;
		//$data['manufacturers'] = $this->db->order_by('title')->get('manufacturer')->result();
		$data['manufacturers'] = $this->db->order_by('title')->where('category','Manufacturer')->get('type')->result();
		$data['categories'] = $this->itemcode_model->getcategories();
		$this->db->where('id',$company->id);
		$data['company'] = $this->db->get('company')->row();
				
		$data['masterdefaults'] = $this->db->order_by('itemid')->select('md.*,p.title')->from('masterdefault md')->join('type p','md.manufacturer=p.id', 'left')->get()->result();
		$this->load->view('inventory/items',$data);
	}
	
	public function Inventory2(){

		ini_set("memory_limit","512M");
		ini_set("max_execution_time", 700);

		$this->load->view('inventory/itempoup');
	}
	
	public function updateitemcode()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
	}
	
	public function updateitemname()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$_POST['itemname'] = htmlentities($_POST['itemname']);
		//print_r($_POST);die;
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
	}
	
	public function updatepartnum()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
	}
	
	public function updatemanufacturer()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		//print_r($existing);die;
		if($existing)
		{
			$where = array();
			$where['itemid'] = $_POST['itemid'];
			$where['company'] = $company->id;
			$where['type'] = 'Supplier';
			$this->db->where($where);
			//print_r($where);print_r($_POST);
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
	}
	
	public function updateitemprice()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
	}
	
	public function updateminqty()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		if(!@$_POST['minqty'])
		    $_POST['minqty'] = 1;
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
	}
	
	public function updateqtyavailable()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		if(!@$_POST['minqty'])
		    $_POST['minqty'] = 1;
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
	}
	
	public function updateiteminstock()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
		//print_r($_POST);
	}
	
	public function updatebackorder()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
	}
	
	public function updateshipfrom()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
	}
	

	
	function additem($invitation)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$_POST['company'] = $company->id;
		$this->db->insert('companyitem',$_POST);
		//print_r($_POST);
		redirect('quote/invitation/'.$invitation);
	}
	
	

    
    ///////inventory

	
	public function updateitem($invitation='')
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
		    $this->db->where('type','Supplier');
			$this->db->where('company',$company->id);
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
		if($invitation)
		    redirect('quote/invitation/'.$invitation);
	}
	
	function export()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
	
		$limit = 10;
		if (!isset($_POST['pagenum']))
			$_POST['pagenum'] = 0;
		$start = $_POST['pagenum'] * $limit;
		$items = $this->inventorymodel->getItems($company->id,$limit, $start);
	
		$data['totalcount'] = $this->inventorymodel->count_all($company->id);
		$data['currentpage'] = $_POST['pagenum'] + 1;
		$data['totalpages'] = ceil($data['totalcount'] / $limit);
		$data['submitmethod'] = 'POST';
		$data['submiturl'] = 'inventory';
		$data['pagingfields'] = $_POST;
		$data['suppliers'] = array();
	
		$this->db->where('company',$company->id);
		$tier = $this->db->get('tierpricing')->row();
	
		$data['tier'] = $tier;
		$data['items'] = $items;
		//$data['manufacturers'] = $this->db->order_by('title')->get('manufacturer')->result();
		$data['manufacturers'] = $this->db->order_by('title')->where('category','Manufacturer')->get('type')->result();
		$data['categories'] = $this->itemcode_model->getcategories();
	
	
		//$this->load->view('inventory/items',$data);
	
		//=========================================================================================
	
		$header[] = array('Item Name' , 'Code', 'Name','Manufacturer' , 'Part#' , 'List Price' , 'Min. Qty.' ,'Stock');
			
		$i = 0;
	
	
		$manufacturers = $data['manufacturers'];
		foreach($items as $item)
		{
			$i++;
				
			$Item_code = @$item->companyitem->itemcode;
			$Item_name = @$item->companyitem->itemname;
				
			$mf_name = '';
			foreach($manufacturers as $mf)
			{
				if($mf->id == @$item->companyitem->manufacturer)
				{
					$mf_name = $mf->title;
				}
			}
			$list_partnum = @$item->companyitem->partnum;
			$item_price   = @$item->companyitem->ea;
			$item_minqty  = @$item->companyitem->minqty;
			$item_qtyavailable  = @$item->companyitem->qtyavailable;
				
			$i_price = '';
			if($item_price > 0)
			{
				$i_price = '$ '.$item_price ;
			}
				
				
			$header[] = array($item->itemname, $Item_code, $Item_name ,$mf_name, $list_partnum , formatPriceNew($i_price) , $item_minqty ,$item_qtyavailable);
				
		}
		createXls('inventory', $header);
		die();
	
		//===============================================================================

	}
	
	//Inventory PDF
	function inventoryPDF()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
	
		$limit = 10;
		if (!isset($_POST['pagenum']))
			$_POST['pagenum'] = 0;
		$start = $_POST['pagenum'] * $limit;
		$items = $this->inventorymodel->getItems($company->id,$limit, $start);
	
		$data['totalcount'] = $this->inventorymodel->count_all($company->id);
		$data['currentpage'] = $_POST['pagenum'] + 1;
		$data['totalpages'] = ceil($data['totalcount'] / $limit);
		$data['submitmethod'] = 'POST';
		$data['submiturl'] = 'inventory';
		$data['pagingfields'] = $_POST;
		$data['suppliers'] = array();
	
		$this->db->where('company',$company->id);
		$tier = $this->db->get('tierpricing')->row();
	
		$data['tier'] = $tier;
		$data['items'] = $items;
		//$data['manufacturers'] = $this->db->order_by('title')->get('manufacturer')->result();
		$data['manufacturers'] = $this->db->order_by('title')->where('category','Manufacturer')->get('type')->result();
		$data['categories'] = $this->itemcode_model->getcategories();
	
	
		//$this->load->view('inventory/items',$data);
	
		//=========================================================================================
	
		$header[] = array('Item Name' , 'Code', 'Name','Manufacturer' , 'Part#' , 'List Price' , 'Min. Qty.' ,'Stock');
			
		$i = 0;
	
	
		$manufacturers = $data['manufacturers'];
		foreach($items as $item)
		{
			$i++;
				
			$Item_code = @$item->companyitem->itemcode;
			$Item_name = @$item->companyitem->itemname;
				
			$mf_name = '';
			foreach($manufacturers as $mf)
			{
				if($mf->id == @$item->companyitem->manufacturer)
				{
					$mf_name = $mf->title;
				}
			}
			$list_partnum = @$item->companyitem->partnum;
			$item_price   = @$item->companyitem->ea;
			$item_minqty  = @$item->companyitem->minqty;
			$item_qtyavailable  = @$item->companyitem->qtyavailable;
				
			$i_price = '';
			if($item_price > 0)
			{
				$i_price = '$ '.$item_price ;
			}
				
				
			$header[] = array($item->itemname.'', $Item_code.'', $Item_name.'' ,$mf_name.'', $list_partnum.'' , formatPriceNew($i_price).'' , $item_minqty.'' ,$item_qtyavailable.'');
				
		}
		 
		$headername = "INVENTORY";
		createPDF('inventory', $header,$headername);
		die();
	
		//===============================================================================

	}
	
    function showeditform($itemid)
    {
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
        //$itemid = $_POST['itemid'];
        
        $query = "SELECT i.*, ci.companynotes, ci.filename, ci.image FROM ".$this->db->dbprefix('item')." i LEFT JOIN 
        		 ".$this->db->dbprefix('companyitem')." ci ON ci.itemid=i.id AND ci.type='Supplier' 
        		 AND ci.company='".$company->id."' WHERE i.id='$itemid'";
        //echo $query.'<pre>';//die;
        $item = $this->db->query($query)->row();
        //print_r($item);die;
        $data['item'] = $item;
        $this->load->template('../../templates/front/blank');
        $this->load->view('inventory/miniform', $data);
    }
    
    function saveinventory()
    {
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
        $itemid = $_POST['itemid'];
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
        
		if(isset($_FILES['image']['tmp_name']))
		if(is_uploaded_file($_FILES['image']['tmp_name']))
		{
			$ext = end(explode('.', $_FILES['image']['name']));
			$nfn = md5(uniqid().date('YmdHi')).'.'.$ext;
			if(move_uploaded_file($_FILES['image']['tmp_name'], "uploads/item/".$nfn))
			{
				$this->_createThumbnail($nfn,'item',81,80);
				unset($_POST['image']);
				$_POST['image'] = $nfn;
			}
		}
        
        $where = array();
        $where['company'] = $company->id;
        $where['itemid'] = $itemid;
        $where['type'] = 'Supplier';
        
        $this->db->where($where);
        $check = $this->db->get('companyitem')->row();
        //print_r($check);print_r($_POST);die;
        unset($_POST['_wysihtml5_mode']);
        if($check)
        {
            $this->db->where($where);
            $this->db->update('companyitem',$_POST);
        }
        else 
        {
            $_POST['type'] = 'Supplier';
            $_POST['company'] = $company->id;
            $this->db->insert('companyitem',$_POST);
        }
        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Item details saved successfully.</div>');
			
        redirect('inventory/Inventory2');
    }
	
    function showdealform($itemid)
    {
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
        //$itemid = $_POST['itemid'];
        
        $query = "SELECT * FROM ".$this->db->dbprefix('dealitem')." 
        		  WHERE company='".$company->id."' AND itemid='$itemid'";
        //echo $query.'<pre>';die;
        $item = $this->db->query($query)->row();
        if(!$item) $item = new stdClass();
        $orgitem = $this->db->where('id',$itemid)->get('item')->row();
        $item->itemname = $orgitem->itemname;
        $item->itemid = $orgitem->id;
        //print_r($item);die;
        $data['item'] = $item;
        $this->load->template('../../templates/front/blank');
        $this->load->view('inventory/dealform', $data);
    }
    
    function savedeal()
    {
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
        $itemid = $_POST['itemid'];
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
        
		if(isset($_FILES['image']['tmp_name']))
		if(is_uploaded_file($_FILES['image']['tmp_name']))
		{
			$ext = end(explode('.', $_FILES['image']['name']));
			$nfn = md5(uniqid().date('YmdHi')).'.'.$ext;
			if(move_uploaded_file($_FILES['image']['tmp_name'], "uploads/item/".$nfn))
			{
				$this->_createThumbnail($nfn,'item',81,80);
				unset($_POST['image']);
				$_POST['image'] = $nfn;
			}
		}
        
        $where = array();
        $where['company'] = $company->id;
        $where['itemid'] = $itemid;
        
        $this->db->where($where);
        $check = $this->db->get('dealitem')->row();
        //print_r($check);print_r($_POST);die;
        $_POST['memberonly'] = @$_POST['memberonly']?'1':'0';
        $_POST['dealactive'] = @$_POST['dealactive']?'1':'0';
        
        if($_POST['dealdate'])
        	$_POST['dealdate'] = date('Y-m-d', strtotime($_POST['dealdate']));
        else
        	$_POST['dealdate'] = '';
        //print_r($_POST);die;
        if($check)
        {
            $this->db->where($where);
            $this->db->update('dealitem',$_POST);
        }
        else 
        {
            $_POST['company'] = $company->id;
            $this->db->insert('dealitem',$_POST);
        }
        $this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Item details saved successfully.</div>');
			
        redirect('inventory/Inventory2');
    }
    
    function _createThumbnail($fileName, $foldername="", $width=81, $height=80)
    {
    	ini_set("memory_limit","512M");
    	ini_set("max_execution_time", 1500);
    	ini_set("set_time_limit", 200);
    
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
    
    
    function addqtydiscount(){
    	
    	$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		
		$_POST['company'] = $company->id;
		$result = $this->db->insert('qtydiscount',$_POST);		
		if($result){
		    $this->db->where('company',$company->id);
		    $this->db->where('itemid',$_POST['itemid']);
			$qtyresult = $this->db->get('qtydiscount')->result();
			if($qtyresult){
				$strput = "";
				foreach($qtyresult as $qtyres){
					
					$strput .= '<div class="row form-row">
							 <div class="col-md-8">'.$qtyres->qty.'+ Price:</div>
							  <div class="col-md-4"><span>'.$qtyres->price.'</span><span><a href="#"><img onclick="delqtydiscount('.$qtyres->id.','.$qtyres->itemid.')" src="'.base_url().'templates/front/assets/img/icon/delete.ico" /></a></span></div>
          				  </div>';
				}
				echo $strput;
			}else 
				echo "Record Added but fetching failed";
		}
		else
			echo "fail";
		die;	
    	
    }
    
    
    function viewqtydiscount(){

    	$company = $this->session->userdata('company');
    	if(!$company)
    	redirect('company/login');

    	if(!@$_POST)
    	{
    		die;
    	}
    	if(!@$_POST['itemid'])
    	{
    		die;
    	}

    	$this->db->where('company',$company->id);
    	$this->db->where('itemid',$_POST['itemid']);
    	$qtyresult = $this->db->get('qtydiscount')->result();
    	if($qtyresult){
    		$strput = "";
    		foreach($qtyresult as $qtyres){

    			$strput .= '<div class="row form-row">
							 <div class="col-md-8">'.$qtyres->qty.'+ Price:</div>
							<div class="col-md-4"><span>'.$qtyres->price.'</span><span><a href="#"><img style="margin-left:5px;width:14px;" onclick="delqtydiscount('.$qtyres->id.','.$qtyres->itemid.')" src="'.base_url().'templates/front/assets/img/icon/delete.ico" /></a></span></div>
          				  </div>';
    		}
    		echo $strput; die;
    	}


    }
    
    function deleteitemqtydiscount(){
    	
    	$company = $this->session->userdata('company');
    	if(!$company)
    	redirect('company/login');
    	$query = "DELETE FROM ".$this->db->dbprefix('qtydiscount')." WHERE `id` = ".$_POST['id'];
    	$returnval = $this->db->query($query);
    	if($returnval)
    	echo "success";
    	else 
    	echo "fail"; die;
    }
    
    public function updatecheckprice()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
		//print_r($_POST);
	}
	
	public function saleitem()
	{
		//echo "<pre>"; print_r($_POST); die;
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}

		$this->db->where('id',$company->id);
		$this->db->update('company',$_POST);

	}
	
	public function blockitem()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		$this->db->where('id',$company->id);
		$this->db->update('company',$_POST);

	}
	
	public function availprice()
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		$this->db->where('id',$company->id);
		$this->db->update('company',$_POST);

	}
	
	function updatetierprice(){
		
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$company->id);
		$this->db->where('type','Supplier');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$company->id);
			$this->db->where('type','Supplier');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $company->id;
			$_POST['type'] = 'Supplier';
			$this->db->insert('companyitem',$_POST);
		}
	}
	
	
	function getallcompanyprices(){
		
		if(!$_POST)
		die;
		
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		/*$this->db->where('company', $company->id);       
        $this->db->where('itemid', $_POST['itemid']);        
        $resultprice = $this->db->get('purchasingtier_item')->row()->price;*/
        
        $resultprice = $this->db->select('p.*,u.username')->from('purchasingtier_item p')->join('users u','p.purchasingadmin=u.id', 'left')->where('company', $company->id)->where('itemid', $_POST['itemid'])->get()->result();
        if($resultprice)
		echo json_encode($resultprice); 
		else 
		echo "";
		die;
	}
	
	
	function getcompanynames(){
		
		if(!$_POST)
		die;
		
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		
		$query = "SELECT u.id,u.username,u.fullname from ".$this->db->dbprefix('users')." u where u.id not in (select purchasingadmin from ".$this->db->dbprefix('purchasingtier_item')." where company = '".$company->id."' and itemid ='".$_POST['itemid']."') and username is not null";
        //echo $query.'<pre>';//die;
        $result = $this->db->query($query)->result();	
		if($result)
		echo json_encode($result); 
		else 
		echo "";
		die;
	}
	
		
	function setnewcompanyprice(){

		if(!$_POST)
		die;

		$company = $this->session->userdata('company');		
		if(!$company)
			redirect('company/login');
		
		
		/*$this->db->where('company', $_POST['companyid']);
		$this->db->where('purchasingadmin', $_POST['purchasingadmin']);
		$this->db->where('itemid', $_POST['itemid']);
		if($this->db->get('purchasingtier_item')->row())
		{
			if(isset($_POST['val'])){
				$update['price'] = $_POST['val'];
				$update['notes'] = "*Given Company Price";
			}

			$this->db->where('company', $_POST['companyid']);
			$this->db->where('purchasingadmin', $_POST['purchasingadmin']);
			$this->db->where('itemid', $_POST['itemid']);
			$this->db->update('purchasingtier_item', $update);
			echo "Item price Changed";
		}
		else
		{*/
			$insert = array();

			if(isset($_POST['val'])){
				$insert['price'] = $_POST['val'];
			}

			$insert['company'] = $company->id;
			$insert['itemid'] = $_POST['itemid'];			
			$insert['notes'] = "*Given Company Price";
			$insert['purchasingadmin'] = $_POST['purchasingadmin'];
			$newcompanyid = $this->db->insert('purchasingtier_item', $insert);
			
			if($newcompanyid){				
				
		 $resultprice = $this->db->select('p.price,u.username')->from('purchasingtier_item p')->join('users u','p.purchasingadmin=u.id', 'left')->where('company', $company->id)->where('p.itemid', $_POST['itemid'])->where('p.purchasingadmin', $_POST['purchasingadmin'])->get()->row();
        if($resultprice)
        				
				if($resultprice)
				{
					echo '<div class="row form-row"><div class="col-md-6"><strong>'.$resultprice->username.'</strong></div><div class="col-md-6"><strong><input type="text" onblur="setcompanypriceprompt(this.value,'.$company->id.','.$_POST['itemid'].','.$_POST['purchasingadmin'].');" value="'.$_POST['val'].'"/></strong> <span><a href="#"><img style="margin-left:5px;width:14px;" onclick="delcompanyprice('.$company->id.','.$_POST['itemid'].','.$_POST['purchasingadmin'].')" src="'.base_url().'templates/front/assets/img/icon/delete.ico" /></a></span>
                            </div>
                          </div>                          
                        </div>';
				}

			}
			
		//}

		die;
	}
	
	
	function compricedelete(){
    	
    	$company = $this->session->userdata('company');
    	if(!$company)
    	redirect('company/login');
    	
    	$query = "DELETE FROM ".$this->db->dbprefix('purchasingtier_item')." where company = '".$company->id."' and itemid ='".$_POST['itemid']."' and purchasingadmin ='".$_POST['purchasingadmin']."'";
    	$returnval = $this->db->query($query);
    	if($returnval)
    	echo "success";
    	else 
    	echo "fail"; die;
    }
    
}
