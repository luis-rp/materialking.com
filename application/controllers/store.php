<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store extends CI_Controller 
{
	public function Store()
	{
		ini_set("memory_limit","512M");
		ini_set("max_execution_time", 700);
	    parent::__construct ();
	    
		$data ['title'] = 'Home';
		$this->load->dbforge();
		$this->load->model ('storemodel', '', TRUE);
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/site/template', $data);
	}

	public function index($id)
	{
	}
	
	public function items($username, $manufacturer='')
	{
	    $username = urldecode($username);
	    $company = $this->db->where('username',$username)->get('company')->row()->id;
        $limit = 10;
        $this->items_model->set_keyword(false);
        $items = $this->storemodel->get_items($username, $manufacturer);
        
        $this->data['totalcount'] = $items->totalresult;
        $this->data['currentpage'] = $_POST['pagenum'] + 1;
        $this->data['totalpages'] = ceil($this->data['totalcount'] / $limit);
        $this->data['submiturl'] = "store/items/$username";
        $this->data['submitmethod'] = 'POST';
        $this->data['pagingfields'] = $_POST;
        $this->data['page_titile'] = "Items List";
        $items = $items->items;
        
        
        $this->data['supplier'] = $this->supplier_model->get_supplier($username);
        
        $id = $this->data['supplier']->id;
        
        $sql = "SELECT t.* FROM " . $this->db->dbprefix('type') . " t, " . $this->db->dbprefix('companytype') . " ct
			    WHERE ct.typeid=t.id AND ct.companyid=" . $id;
        $this->data['types'] = $this->db->query($sql)->result();
        
        
        $rating = $this->db->where('company',$id)->select_avg('rating')->get('quotefeedback')->row();
        
        if(@$rating->rating)
        {
        	$this->data['ratingvalue'] = $rating->rating;
        	$this->data['rating'] = $rating = '<div class="fixedrating" data-average="'.@$rating->rating.'" data-id="1"></div>';
        }
        else
        {
        	$this->data['ratingvalue'] = $rating->rating;
        	$this->data['rating'] = '';
        }
        
        $this->data['inventory'] = array();
        foreach ($items as $item)
        {
            $this->db->where('id',$item->itemid);
            $orgitem = $this->db->get('item')->row();
            if(!$item->itemname)
                $item->itemname = $orgitem->itemname;
            $item->unit = $orgitem->unit;
            
            $this->db->where('id',$item->manufacturer);
            $item->manufacturername = @$this->db->get('type')->row()->title;
            
            if(!$item->image)
                $item->image = $orgitem->item_img;
            
            if($this->session->userdata('site_loggedin'))
            {
                $this->db->where('company', $company);
                $tiers = $this->db->get('tierpricing')->row();
                if ($tiers)
                {
                    $currentpa = $this->session->userdata('site_loggedin')->id;
                    $this->db->where('company', $company);
                    $this->db->where('purchasingadmin', $currentpa);
                    $tier = @$this->db->get('purchasingtier')->row()->tier;
                    if ($tier)
                    {
                        $tv = $tiers->$tier;
                        $item->ea = $item->ea + ($item->ea * $tv / 100);
                        $item->ea = number_format($item->ea, 2);
                    }
                }
            }
            
            $this->data['inventory'][] = $item;
        }
        
        
        $this->data['norecords'] = '';
        if (! $this->data['inventory'])
        {
            $this->data['norecords'] = 'No Records found.';
        }
        $this->db->where('id',$company);
        $this->data['company'] = $this->db->get('company')->row();
        $this->data['categorymenu'] = $this->items_model->getStoreCategoryMenu($company);
        $this->data['breadcrumb'] = @$_POST['breadcrumb'];
        //echo '<pre>';print_r($data['categorymenu']);die;
        $this->data['breadcrumb2'] = $this->items_model->getsubcategorynames(@$_POST['category']); 
        if(isset($_POST['category']))
        $category = $_POST['category'];
        else
        $category = "";


        if($category){

        	$sql1 = "SELECT * FROM ".$this->db->dbprefix('category')." WHERE id = '{$_POST['category']}' ORDER BY catname ASC";

        	$result1 = $this->db->query($sql1)->result();
        	if($result1)
        	$this->data['catname'] = $result1[0]->catname;
        }
        
        $this->load->view('store/items', $this->data);
	}
}
