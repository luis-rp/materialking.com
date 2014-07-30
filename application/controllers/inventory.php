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
		$this->load->model ('admin/itemcode_model', '', TRUE);
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		if($this->session->userdata('company')) $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
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
    
    
}
