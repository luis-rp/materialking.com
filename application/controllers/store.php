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
		$this->load->model('admin/banner_model', '', TRUE);
		$this->load->model ('storemodel', '', TRUE);
		$data['banner']=$this->banner_model->display();
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/site/template', $data);
	}

	public function index($id)
	{
	}
	
	public function items($username, $manufacturer='')
	{
		$this->load->library('image_lib');
		if(isset($_POST['searchbreadcrumbcategory']))
		$_POST['category'] = $_POST['searchbreadcrumbcategory'];
	    $username = urldecode($username);
	    $company = $this->db->where('username',$username)->get('company')->row()->id;
        $limit = 12;
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
        
        $this->data['companybanner']=$this->db->get_where('companybanner',array('companyid'=>$company,'isdeleted'=>0))->result();
        $this->data['supplier'] = $this->supplier_model->get_supplier($username);
        
        $id = $this->data['supplier']->id;
        $this->data['status']="";
        
        if ($this->session->userdata('site_loggedin') &&  $this->data['supplier'])
        {
            $this->data['supplier']->joinstatus = '<input type="button" value="Join Network" onclick="joinnetwork(' . $this->data['supplier']->id . ')" class="btn btn-primary arrow-right"/>';

            $currentpa = $this->session->userdata('site_loggedin')->id;

            $this->db->where('fromid', $currentpa);
            $this->db->where('toid', $this->data['supplier']->id);
            $this->db->where('fromtype', 'users');
            $this->db->where('totype', 'company');
            $checkrequest = $this->db->get('joinrequest')->result();
            if($checkrequest)
                $this->data['supplier']->joinstatus = 'Already sent request';

            $this->db->where('purchasingadmin', $currentpa);
            $this->db->where('company', $this->data['supplier']->id);
            if ($this->db->get('network')->result())
                $this->data['supplier']->joinstatus = 'Already in Network';
                
               
                if($this->data['supplier']->joinstatus=='Already in Network'){
                	$this->data['status']=1;
                }
                elseif(($this->data['supplier']->joinstatus=='Already sent request' || $this->data['supplier']->joinstatus == '<input type="button" value="Join Network" onclick="joinnetwork(' . $this->data['supplier']->id . ')" class="btn btn-primary arrow-right"/>') && $this->data['supplier']->blockitemdata==0){
                	$this->data['status']=1;
                }
               elseif(($this->data['supplier']->joinstatus=='Already sent request' || $this->data['supplier']->joinstatus == '<input type="button" value="Join Network" onclick="joinnetwork(' . $this->data['supplier']->id . ')" class="btn btn-primary arrow-right"/>') && $this->data['supplier']->blockitemdata==1){
                	$this->data['status']=0;
                }
        }
        elseif($this->data['status']=="" && $this->data['supplier']->blockitemdata==0)
        {
        	$this->data['status']=1;
        }
        
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
            
            if(!$item->itemcode)
                $item->itemcode = $orgitem->itemcode;        
                
            $item->unit = $orgitem->unit;
            $item->increment = $orgitem->increment;
            
            $this->db->where('id',$item->manufacturer);
            $item->manufacturername = @$this->db->get('type')->row()->title;
            
            if(!$item->image)
                $item->image = $orgitem->item_img;
                
            $item->unit = $orgitem->unit;
                
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
                
                $resultprice = $this->db->select('p.price')->from('purchasingtier_item p')->join('company c','p.company=c.id')->where('p.purchasingadmin', $currentpa)->where('p.itemid', $item->itemid)->where('c.isdeleted', 0)->where('p.company', $item->company)->get()->row();
                if($resultprice){
						
                	$item->ea = number_format($resultprice->price, 2);	
                	
                }
                
            }
            $hasdiscount = $this->db
                            ->where(array('itemid'=>$item->itemid,'company'=>$item->company))
                            ->get('qtydiscount')
                            ->result();

            if($hasdiscount)
              $item->hasdiscount = true;
            else
             $item->hasdiscount = false;            
            $this->data['inventory'][] = $item;
        }
        
        /***
         * Deals by Supplier
         */
       
         
        $dealitems = $this->db
        ->select('dealitem.*,item.item_img')
        ->from('dealitem')
        ->join('item','item.id=dealitem.itemid')
        ->where('dealitem.company',$company)
        ->where('dealitem.dealactive','1')
        ->where('dealitem.qtyavailable >',0)
        ->where('dealitem.qtyavailable >=','dealitem.qtyreqd')
        ->where('dealitem.dealdate >=',date('Y-m-d'))
        ->get()
        ->result();
        //echo '<pre>';print_r($dealitems);//die;
        $this->data['dealfeed'] = array();
        foreach($dealitems as $di)
        {
        	if($di->dealactive)
        	{
        		if(!$di->image)
        			$di->image="big.png";
        		$di->companyusername = $this->db->where('id',$company)->get('company')->row()->username;
        		$di->companyname = $this->db->where('id',$company)->get('company')->row()->title;
        		$orgitem = $this->db->where('id',$di->itemid)->get('item')->row();
        		$di->url = $orgitem->url;
        		$di->itemcode = $orgitem->itemcode;
        		$di->itemname = $orgitem->itemname;
        		$di->unit = $orgitem->unit;
        		$di->increment = $orgitem->increment;
        		if(isset($tv))
        		{
        			$di->dealprice = $di->dealprice + ($di->dealprice * $tv / 100);
        			$di->dealprice = number_format($di->dealprice, 2);
        		}
        		if($di->memberonly)
        		{
        			if($this->session->userdata('site_loggedin'))
        				$this->data['dealfeed'][] = $di;
        		}
        		else
        		{
        			$this->data['dealfeed'][] = $di;
        		}
        	}
        }
        /****End deal by supplier**/
        $this->data['norecords'] = '';
        if (! $this->data['inventory'])
        {
            $this->data['norecords'] = 'No Records found.';
        }
        $this->db->where('id',$company);
        $this->data['company'] = $this->db->get('company')->row();
        $this->data['categorymenu'] = $this->items_model->getStoreCategoryMenu($company);
        $this->data['manufacturermenu'] = $this->items_model->getStoreManufacturerMenu($company);
        $this->data['breadcrumb'] = @$_POST['breadcrumb'];
        
        if(@$_POST['manufacturer']!="")
        $this->data['breadcrumb'] = $this->items_model->getManufacturername(@$_POST['manufacturer']); 
        
        //echo '<pre>';print_r($data['categorymenu']);die;
        $this->data['breadcrumb2'] = $this->storemodel->getsubcategorynames(@$_POST['category'],$company);
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
        $data['company'] = $company;
        
        $this->db->where("user_id",$company);
        $ads = $this->db->get("ads")->result();
       
        $this->data['adforsupplier']=array();    
        if(count($ads) > 0)
        {  
	        foreach($ads as $ad)
	        {
	        	$image=$this->db->get_where('AdImage',array('adid'=>$ad->id))->row();
	     		$ad->image="";
	     		if(@$image->image)
	     		{
	     			$ad->image=$image->image;
	     		} 
	        	$config['image_library'] = 'gd2';
	        	$config['source_image'] = './uploads/ads/'.$ad->image;
	        	$config['create_thumb'] = TRUE;
	        	$config['width']     = 190;
	        	$config['height']   = 194;
	        	 
	        	$this->image_lib->clear();
	        	$this->image_lib->initialize($config);
	        	$this->image_lib->resize();
	        	$this->data['adforsupplier'][]=$ad;	        	 
	        }
        }
        
          
        $this->data['page_title'] = $this->data["company"]->title." Online Store";
        
        $bhrs = $this->db->get_where('company_business_hours',array('company'=>$id))->result();      
        $this->data['businesshrs'] = $bhrs;
        
        $this->load->view('store/items', $this->data);
	}
}
