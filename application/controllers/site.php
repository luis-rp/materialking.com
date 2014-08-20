<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');
class site extends CI_Controller
{
    public function site ()
    {
        ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 700);
        parent::__construct();
        $data['title'] = 'Home';
        $this->load->dbforge();
        $this->load->model('homemodel', '', TRUE);
        $this->load->model('admin/banner_model', '', TRUE);
        $this->load->model('admin/itemcode_model', '', TRUE);
        $this->load->model('admin/catcode_model', '', TRUE);
        $this->load->model('admin/quote_model', '', TRUE);
		$this->load->model ('items_model', '', TRUE);
		$data['banner']=$this->banner_model->display();
		$data['categorymenu'] = $this->items_model->getCategoryMenu (0) ;
        $this->load = new My_Loader();
        $this->load->template('../../templates/site/template', $data);
    }
    public function my_location ($lat, $long)
    {
        $this->session->set_userdata('navigator_lat', $lat);
        $this->session->set_userdata('navigator_lng', $long);
        redirect($_SERVER['HTTP_REFERER']);
        //redirect(base_url('site'));
    }
    
    public function index ()
    {

    	$details = get_my_address();
    	$center = $details->loc;
    	//$center = "56, 38";
    	//var_dump($details);die;
    	$data['my_location'] = get_my_location($details);
    	
    	$geo_coords = explode(",", $center);
    	$search = new stdClass();
    	$search->distance = 100000;
    	$search->current_lat = $geo_coords[0];
    	$search->current_lon = $geo_coords[1];
    	$search->earths_radius = 6371;
    	$data['norecords'] = '';
    	$use_supplier_position = false;
    	$this->homemodel->set_search_criteria($search);
    	
    	$location = $this->input->post('location');
    	
    	//$lat = $this->input->post('lat');
    	//$lng = $this->input->post('lng');
    	if ($location)
    	{
    		$return = get_geo_from_address($location);
    		if($return)
    		{
    			$center = "{$return->lat}, {$return->long}";
    			$search->current_lat = $return->lat;
    			$search->current_lon = $return->long;
    			$this->homemodel->set_search_criteria($search);
    		}
    	}
    	
    	
    	$suppliers_near_me = $this->homemodel->get_nearest_suppliers();
    	$data['suppliers_10_miles'] = false;
    	$nearest_10 = false;
    	if (! $suppliers_near_me)
    	{
    		$suppliers_near_me = $this->homemodel->get_nearest_suppliers($ignore_location = true);
    		$data['norecords'] = "Found " . $suppliers_near_me->totalresult . "  suppliers";
    	}
    	else
    	{
    		$data['norecords'] = "Found " . $suppliers_near_me->totalresult . " nearest suppliers";
    		//            $this->homemodel->set_distance(10);
    		//            $nearest_10 = $this->homemodel->get_nearest_suppliers();
    		$this->homemodel->set_distance(20);
    		$nearest_10 = $suppliers_near_me;
    		if ($nearest_10)
    		{
    			$nearest_10 = $nearest_10->suppliers;
    			foreach ($nearest_10 as $supplier)
    			{
    				$supplier->joinstatus = '';
    				if ($this->session->userdata('site_loggedin'))
    				{
    					$supplier->joinstatus = '<a onclick="joinnetwork(' . $supplier->id . ')" />Join</a>';
    					$currentpa = $this->session->userdata('site_loggedin')->id;
    					$this->db->where('purchasingadmin', $currentpa);
    					$this->db->where('company', $supplier->id);
    					if ($this->db->get('network')->result())
    						$supplier->joinstatus = 'Already in Network';
    					$this->db->where('fromid', $currentpa);
    					$this->db->where('toid', $supplier->id);
    					$this->db->where('fromtype', 'users');
    					$this->db->where('totype', 'company');
    					if ($this->db->get('joinrequest')->result())
    						$supplier->joinstatus = 'Already sent request';
    				}
    				$data['suppliers_10_miles'][] = $supplier;
    			}
    		}
    	}
    	//get suppliers on the map
    	if (! $suppliers = $nearest_10)
    	{
    		$suppliers = $this->homemodel->getSuppliers();
    	}
    	
    	$data['suppliers'] = array();
    	$latlongs = array();
    	$popups = array();
    	//log_message('debug',var_export($suppliers));
    	if ($suppliers)
    	{
    		foreach ($suppliers as $supplier)
    		{
    			if (! $supplier->com_lat || ! $supplier->com_lng)
    			{
    				$geocode = file_get_contents(
    						"http://maps.google.com/maps/api/geocode/json?address=" . urlencode(str_replace("\n", ", ", $supplier->address)) . "&sensor=false");
    				$output = json_decode($geocode);
    				if(@$output->results[0]->geometry->location->lat && @$output->results[0]->geometry->location->lng)
    				{
    					$lat = $output->results[0]->geometry->location->lat;
    					$long = $output->results[0]->geometry->location->lng;
    					$supplier->com_lat = $lat;
    					$supplier->com_lng = $long;
    					$this->db->where('id', $supplier->id);
    					$this->db->update('company', array('com_lat' => $lat, 'com_lng' => $long));
    				}
    			}
    			$supplier->joinmark = '';
    			if ($this->session->userdata('site_loggedin'))
    			{
    				$currentpa = $this->session->userdata('site_loggedin')->id;
    				$supplier->joinmark = '<a  class="btn btn-primary arrow-right" onclick="joinnetwork(' . $supplier->id . ')" /> Join </a>';
    				$this->db->where('fromid', $currentpa);
    				$this->db->where('toid', $supplier->id);
    				$this->db->where('fromtype', 'users');
    				$this->db->where('totype', 'company');
    				if ($this->db->get('joinrequest')->result())
    					$supplier->joinmark ='<div class="star"><div class="content">Already sent Request</div></div>';
    				 
    				$this->db->where('purchasingadmin', $currentpa);
    				$this->db->where('company', $supplier->id);
    				if ($this->db->get('network')->result())
    				{
    					$supplier->joinmark = '<div class="star"><div class="content">Already in Network</div></div>';
    				}
    			}
    			$latlongs[] = "[$supplier->com_lat, $supplier->com_lng]";
    			if (! $supplier->logo)
    			{
    				$supplier->logo = 'big.png';
    			}
    			log_message('debug',"lat:".$supplier->com_lat.":lng:".$supplier->com_lng);
    			$popups["$supplier->com_lat, $supplier->com_lng"] = '<div class="infobox"><div class="image"><img src="' . base_url() . 'uploads/logo/thumbs/' . $supplier->logo .
    			'" alt="" width="100"></div><div class="title"><a href="' . site_url('site/supplier/' . $supplier->username) . '">' . $supplier->title .
    			'</a></div><div class="area"><div class="price">&nbsp;</div><span class="key">'.$supplier->contact .'<br/>' . $supplier->city.',&nbsp;'.$supplier->state . '</span><span class="value">' . '' .
    			'</span></div><div style="align:left;overflow:hidden;"><p>' . $supplier->joinmark . '</p><p><div class="btn btn-primary arrow-right"><a href="' . site_url('site/supplier/' . $supplier->username) . '">View Profile</a></div></p><p><div class="btn btn-primary arrow-right "><a href="' . site_url('store/items/' . $supplier->username) . '">Go to Store</a></div></p></div></div>';
    			$data['suppliers'][] = $supplier;
    		}
    	}
    	//var_dump($popups);die;
    	$my_coords = "$search->current_lat, $search->current_lon";
    	$data['latlongs'] = $latlongs?implode(',', $latlongs):'0,0';
    	$data['mapcenter'] = $center;
    	$data['popups'] = $popups;
    	$data['my_coords'] = $my_coords;
    	$sql = "SELECT DISTINCT(CONCAT(city,', ',state)) citystate FROM " . $this->db->dbprefix('company');
    	$data['citystates'] = $this->db->query($sql)->result();
    	$data['states'] = $this->db->get('state')->result();
    	$data['types'] = $this->db->get('type')->result();
    	$featured = $this->homemodel->get_featured_suppliers();
    	$data['featured'] = array();
    	foreach ($featured as $fet)
    	{
    		$fet->joinstatus = '';
    		if ($this->session->userdata('site_loggedin'))
    		{
    			$currentpa = $this->session->userdata('site_loggedin')->id;
    			$this->db->where('purchasingadmin', $currentpa);
    			$this->db->where('company', $fet->id);
    			if ($this->db->get('network')->result())
    				$fet->joinstatus = '<div class="star"><div class="content">&nbsp;</div></div>';
    		}
    		$data['featured'][] = $fet;
    	}
    	$recents = $this->homemodel->get_recent_suppliers();
    	$recents = $this->db->where('username !=','')->order_by('regdate','DESC')->limit(3)->get('company')->result();
    	$data['recentcompanies'] = array();
    	foreach ($recents as $recent)
    	{
    		$recent->joinstatus = '';
    		if ($this->session->userdata('site_loggedin'))
    		{
    			$currentpa = $this->session->userdata('site_loggedin')->id;
    			$this->db->where('purchasingadmin', $currentpa);
    			$this->db->where('company', $recent->id);
    			if ($this->db->get('network')->result())
    				$recent->joinstatus = '<div class="star"><div class="content">&nbsp;</div></div>';
    		}
    		$data['recentcompanies'][] = $recent;
    	}
    	$this->load->view('site/index', $data);
    }
    
    public function search_supplier ($keyword)
    {
        $details = get_my_address();
        $center = $details->loc;
        $this->data['my_location'] = get_my_location($details);
        $geo_coords = explode(",", $center);
        $search = new stdClass();
        $search->distance = 100000;
        $search->current_lat = $geo_coords[0];
        $search->current_lon = $geo_coords[1];
        $search->earths_radius = 6371;
        $use_supplier_position = false;
        $this->homemodel->set_search_criteria($search);
        $this->homemodel->set_keyword($keyword);
        $items = $this->homemodel->find_item();
        return $items;
    }
    
    public function suppliers ($keyword = false)
    {
        //print_r($_POST);
        if ($this->input->post('keyword') || $keyword)
        {
            if (! $keyword)
            {
                $keyword = $this->input->post('keyword');
            }
            
            $query_suppliers = $this->search_supplier($keyword);
            $this->data['found_records'] = "Found " . $query_suppliers->totalresult . " suppliers";
            $this->data['submiturl'] = 'site/suppliers/' . $keyword;
            $this->data['keyword'] = $keyword;
            if ($keyword)
            {
                $this->data['page_titile'] = "Search for \"$keyword\"";
            }
            else
            {
                $this->data['page_titile'] = "Suppliers List";
            }
        }
        else
        {
            $details = get_my_address();
            $center = $details->loc;
            $this->data['my_location'] = get_my_location($details);
            $geo_coords = explode(",", $center);
            $search = new stdClass();
            $search->distance = 100000;
            $search->current_lat = $geo_coords[0];
            $search->current_lon = $geo_coords[1];
            $search->earths_radius = 6371;
            $use_supplier_position = false;
            $this->homemodel->set_search_criteria($search);
            
            $location = $this->input->post('location');
            
            //$lat = $this->input->post('lat');
            //$lng = $this->input->post('lng');
            if ($location)
            {
                $return = get_geo_from_address($location);
                if($return)
                {
                    $center = "{$return->lat}, {$return->long}";
                    $search->current_lat = $return->lat;
                    $search->current_lon = $return->long;
                    $this->homemodel->set_search_criteria($search);
                }
            }
            $this->homemodel->set_distance(20);
            $query_suppliers = $this->homemodel->get_nearest_suppliers();
            if (! $query_suppliers->totalresult)
            {
                $this->homemodel->set_distance(15000);
                $query_suppliers = $this->homemodel->get_nearest_suppliers($ignore_location = true);
                $this->homemodel->set_distance(20);
                $this->data['found_records'] = "Found " . $query_suppliers->totalresult . " suppliers";
            }
            else
            {
                $this->data['found_records'] = "Found " . $query_suppliers->totalresult . " nearest suppliers";
            }
            $this->data['submiturl'] = 'site/suppliers';
        }
        $this->data['norecords'] = false;
        if ($query_suppliers->totalresult == 0)
        {
            $this->data['norecords'] = 'No Records found for the search.';
        }
        $limit = 6;
        $this->data['totalcount'] = $query_suppliers->totalresult;
        $this->data['currentpage'] = $_POST['pagenum'] + 1;
        $this->data['totalpages'] = ceil($this->data['totalcount'] / $limit);
        $this->data['submitmethod'] = 'POST';
        $this->data['pagingfields'] = $_POST;
        $this->data['suppliers'] = array();
        $suppliers = $query_suppliers->suppliers;
        $this->load->helper('text');
        foreach ($suppliers as $supplier)
        {
            $supplier->joinstatus = '';
            if ($this->session->userdata('site_loggedin'))
            {
                $supplier->joinstatus = '<a  class="btn btn-primary arrow-right" onclick="joinnetwork(' . $supplier->id . ')"/>Join</a>';
                $currentpa = $this->session->userdata('site_loggedin')->id;
                $this->db->where('fromid', $currentpa);
                $this->db->where('toid', $supplier->id);
                $this->db->where('fromtype', 'users');
                $this->db->where('totype', 'company');
                if ($this->db->get('joinrequest')->result())
                    $supplier->joinstatus = '<input type="button" value="Already sent request" class="btn btn-primary arrow-right"/>';
                $this->db->where('purchasingadmin', $currentpa);
                $this->db->where('company', $supplier->id);
                if ($this->db->get('network')->result())
                    $supplier->joinstatus = '<input type="button" value="Already in Network" class="btn btn-primary arrow-right"/>';
            }
            if ($keyword)
            {
                $supplier->contact = highlight_phrase($supplier->contact, $keyword, '<span style="color:#990000">', '</span>');
                $supplier->address = highlight_phrase($supplier->address, $keyword, '<span style="color:#990000">', '</span>');
                $supplier->title = highlight_phrase($supplier->title, $keyword, '<span style="color:#990000">', '</span>');
            }
            $this->data['suppliers'][] = $supplier;
        }
        $sql = "SELECT DISTINCT(CONCAT(city,', ',state)) citystate FROM " . $this->db->dbprefix('company');
        $this->data['citystates'] = $this->db->query($sql)->result();
        $this->data['states'] = $this->db->get('state')->result();
        $this->data['types'] = $this->db->get('type')->result();
        $this->load->view('site/suppliers', $this->data);
    }
    
    public function send_supplier_email ()
    {
        $id = $this->input->post('supplier_id');
        $this->db->where('id', $id);
        $supplier = $this->db->get('company')->row();
        if ($supplier)
        {
            if (! $send_email_to = $supplier->primaryemail)
            {
                $send_email_to = $supplier->email;
            }
            if ($send_email_to)
            {
                $contact_email = $this->input->post('contact_email');
                $contact_message = $this->input->post('contact_message');
                $contact_name = $this->input->post('contact_name');
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: EZPZP <noreply@EZPZP.com>' . "\r\n";
                $headers .= 'Bcc:  378470@gmail.com' . "\r\n";
                $message = "Contact name: " . $contact_name . "<br>" . "Contact email: " . $contact_email . "<br>" . "Message: " . $contact_message . "<br>";
                $status = mail($send_email_to, "Contact request from EZPZP.com", $message, $headers);
                if ($status)
                {
                    $this->session->set_flashdata('message', 'Email was sent.');
                }
                else
                {
                    $this->session->set_flashdata('message', 'Email wasn`t sent. Please try again later');
                }
            }
            else
            {
                $this->session->set_flashdata('message', 'Email wasn`t sent. Supplier`s email not found');
            }
        }
        else
        {
            $this->session->set_flashdata('message', 'Email wasn`t sent');
        }
        redirect(base_url('site/supplier/' . $id));
    }
    
    public function supplier ($username)
    {
        $username = urldecode($username);
        $data['message'] = $this->session->flashdata('message');
        $data['supplier'] = $this->supplier_model->get_supplier($username);
        if(!$data['supplier'])
        	redirect('');
        if(!@$data['supplier']->username || !@$data['supplier']->id)
        {
        	sleep(5);
        	redirect('site/supplier/'.$username);
        }
        $id = $data['supplier']->id;
        if ($this->session->userdata('site_loggedin') && $data['supplier'])
        {
            $data['supplier']->joinstatus = '<input type="button" value="Join Network" onclick="joinnetwork(' . $data['supplier']->id . ')" class="btn btn-primary arrow-right"/>';
            
            $currentpa = $this->session->userdata('site_loggedin')->id;
            
            $this->db->where('fromid', $currentpa);
            $this->db->where('toid', $data['supplier']->id);
            $this->db->where('fromtype', 'users');
            $this->db->where('totype', 'company');
            $checkrequest = $this->db->get('joinrequest')->result();
            //print_r($checkrequest);die;
            if ($checkrequest)
                $data['supplier']->joinstatus = 'Already sent request';
                
            $this->db->where('purchasingadmin', $currentpa);
            $this->db->where('company', $data['supplier']->id);
            if ($this->db->get('network')->result())
                $data['supplier']->joinstatus = 'Already in Network';
            
        }
        //print_r($data['supplier']);die;
        $data['nextid'] = $this->nextsupplier($id);
        $data['previd'] = $this->prevsupplier($id);
        $sql = "SELECT t.* FROM " . $this->db->dbprefix('type') . " t, " . $this->db->dbprefix('companytype') . " ct
			    WHERE ct.typeid=t.id AND ct.companyid=" . $id;
        $data['types'] = $this->db->query($sql)->result();
        if (!trim(@$data['supplier']->com_lat) && @$data['supplier']->address)
        {
            $geoloc = get_geo_from_address($data['supplier']->address);
            if(@$geoloc->lat && @$geoloc->lat)
            {
                $update_supplier['com_lat'] = $geoloc->lat;
                $update_supplier['com_lng'] = $geoloc->long;
                $this->supplier_model->update_supplier($id, $update_supplier);
                $data['supplier'] = $this->supplier_model->get_supplier($id);
            }
        }
        
        $rating = $this->db->where('company',$id)->select_avg('rating')->get('quotefeedback')->row();
		
        if(@$rating->rating)
        {
            $data['ratingvalue'] = $rating->rating;
            $data['rating'] = $rating = '<div class="fixedrating" data-average="'.@$rating->rating.'" data-id="1"></div>';
        }
        else
        {
            $data['ratingvalue'] = $rating->rating;
            $data['rating'] = '';
        }
        $data['feedbacks'] = $this->db->select('quotefeedback.*, users.companyname')
                            ->order_by('ratedate','DESC')
                            ->from('quotefeedback')->join('users','quotefeedback.purchasingadmin=users.id')->limit(3)
                            ->where('company',$id)->get()->result();
        
        $inventory = $this->db->where('type','Supplier')
                    ->where('company',$id)
                    ->where('isfeature','1')
                    ->get('companyitem')
                    ->result();
        log_message('debug',"id:".var_export($id,true));
         $data['inventory'] = array();
         
         foreach($inventory as $initem)
         {
         	log_message('debug',var_export($initem,true));
         	
            $this->db->where('id',$initem->manufacturer);
            $initem->manufacturername = @$this->db->get('type')->row()->title;
           
            $this->db->where('id',$initem->itemid);
            $orgitem = $this->db->get('item')->row();
            if(!is_object($orgitem)){
            	continue;
            }
            if(!$initem->itemname)
            	$initem->itemname = $orgitem->itemname;
            
            if(!$initem->image)
            	$initem->image = $orgitem->item_img;
            
            $initem->url = $orgitem->url;
            
            if($this->session->userdata('site_loggedin'))
            {
                $this->db->where('company', $id);
                $tiers = $this->db->get('tierpricing')->row();
                if ($tiers)
                {
                    $currentpa = $this->session->userdata('site_loggedin')->id;
                    $this->db->where('company', $initem->company);
                    $this->db->where('purchasingadmin', $currentpa);
                    $tier = @$this->db->get('purchasingtier')->row()->tier;
                    if ($tier)
                    {
                        $tv = $tiers->$tier;
                        $initem->ea = $initem->ea + ($initem->ea * $tv / 100);
                        $initem->ea = number_format($initem->ea, 2);
                    }
                }
                
                
                /*
                $dealitem = $this->db->where('company',$id)
                    ->where('itemid',$initem->itemid)
                    ->where('dealactive','1')
                    ->where('qtyavailable >=','qtyreqd')
                    ->where('qtyavailable >','0')
                    ->where('dealdate >=',date('Y-m-d'))
                    ->get('dealitem')
                    ->row();
                $initem->dealprice = '';
                $initem->qtyreqd = 0;
                if($dealitem)
                {
                
                    if(isset($tv))
                    {                    
                        $dealitem->dealprice = $dealitem->dealprice + ($dealitem->dealprice * $tv / 100);
                        $dealitem->dealprice = number_format($dealitem->dealprice, 2);
                    }
                    $initem->dealprice = $dealitem->dealprice;
                    $initem->qtyreqd = $dealitem->qtyreqd;
                }
                */
            }
             
            $data['inventory'][] = $initem;
         }
         
        $dealitems = $this->db->where('company',$id)
                    ->where('dealactive','1')
                    ->where('qtyavailable >=','qtyreqd')
                    ->where('qtyavailable >','0')
                    ->where('dealdate >=',date('Y-m-d'))
                    ->get('dealitem')
                    ->result();
        //print_r($dealitems);die;
        $data['dealfeed'] = array();
        foreach($dealitems as $di)
        {
            if($di->dealactive)
            {
               /* if(!$di->image)
                    $di->image="big.png";*/
                    
                $orgitem = $this->db->where('id',$di->itemid)->get('item')->row();
                $cmpitem = $this->db->where('itemid',$di->itemid)->where('company',$id)->where('type','Supplier')->get('companyitem')->row();
                $di->itemname = @$cmpitem->itemname?$cmpitem->itemname:$orgitem->itemname;
                $di->url = $orgitem->url;
                $di->image = $orgitem->item_img;
                $di->itemcode = $cmpitem->itemcode;
                if($di->memberonly)
                {
                    if($this->session->userdata('site_loggedin'))
                        $data['dealfeed'][] = $di;
                }
                else
                {
                    $data['dealfeed'][] = $di;
                }
            }
        }
        //print_r($data['dealfeed']);die;
        $similarsuppliers = $this->supplier_model->getrelatedsupplier($id);
        $data['similarsuppliers'] = $similarsuppliers;
        //print_r($data['dealfeed']);die;
        
        $this->load->view('site/supplier', $data);
    }
    public function prevsupplier ($id)
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('company') . " c ORDER BY title ASC";
        $result = $this->db->query($sql)->result();
        for ($i = 0; $i < count($result); $i ++)
        {
            if ($result[$i]->id == $id && isset($result[$i - 1]))
            {
                return $result[$i - 1]->id;
            }
        }
        return null;
    }
    public function nextsupplier ($id)
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('company') . " c ORDER BY title ASC";
        $result = $this->db->query($sql)->result();
        for ($i = 0; $i < count($result); $i ++)
        {
            if ($result[$i]->id == $id && isset($result[$i + 1]))
            {
                return $result[$i + 1]->id;
            }
        }
        return null;
    }
    public function purchasers ()
    {
        $limit = 6;
        if (! isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;
        $where = array();
        $where['usertype_id'] = 2;
        $this->db->where($where);
        if (@$_POST['search'])
            $this->db->like('companyname', $_POST['search']);
        $totalresult = $this->db->get('users')->result();
        $this->data['totalcount'] = count($totalresult);
        $this->data['currentpage'] = $_POST['pagenum'] + 1;
        $this->data['totalpages'] = ceil($this->data['totalcount'] / $limit);
        $this->data['submiturl'] = 'site/purchasers';
        $this->data['submitmethod'] = 'POST';
        $this->data['pagingfields'] = $_POST;
        $this->db->limit($limit, $start);
        $this->db->where($where);
        $this->db->order_by('companyname');
        if (@$_POST['search'])
            $this->db->like('companyname', $_POST['search']);
        $this->data['purchasers'] = $this->db->get('users')->result();
        $this->load->view('site/purchasers', $this->data);
    }
    public function purchaser ($id)
    {
        $this->db->where('usertype_id', 2);
        $this->db->where('id', $id);
        $data['purchaser'] = $this->db->get('users')->row();
        $data['nextid'] = $this->nextpurchaser($id);
        $data['previd'] = $this->prevpurchaser($id);
        $this->load->view('site/purchaser', $data);
    }
    public function prevpurchaser ($id)
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('users') . " WHERE usertype_id=2 ORDER BY companyname ASC";
        $result = $this->db->query($sql)->result();
        for ($i = 0; $i < count($result); $i ++)
        {
            if ($result[$i]->id == $id && isset($result[$i - 1]))
            {
                return $result[$i - 1]->id;
            }
        }
        return null;
    }
    public function nextpurchaser ($id)
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('users') . " WHERE usertype_id=2 ORDER BY companyname ASC";
        $result = $this->db->query($sql)->result();
        for ($i = 0; $i < count($result); $i ++)
        {
            if ($result[$i]->id == $id && isset($result[$i + 1]))
            {
                return $result[$i + 1]->id;
            }
        }
        return null;
    }
    
    public function search ($keyword = false)
    {
        if (! $keyword)
        {
            $keyword = $this->input->post('keyword');
        }
        $this->items_model->set_keyword($keyword);
        $items = $this->items_model->find_item();
        $limit = 6;
        $this->data['totalcount'] = $items->totalresult;
        $this->data['currentpage'] = $_POST['pagenum'] + 1;
        $this->data['totalpages'] = ceil($this->data['totalcount'] / $limit);
        $this->data['submiturl'] = 'site/search/' . $keyword;
        $this->data['submitmethod'] = 'POST';
        $this->data['keyword'] = $keyword;
        if ($keyword)
        {
            $this->data['page_titile'] = "Search for \"$keyword\"";
        }
        else
        {
            $this->data['page_titile'] = "Items List";
        }
        $this->data['pagingfields'] = $_POST;
        $this->data['items'] = array();
        $items = $items->items;
        foreach ($items as $item)
        {
            $query = "SELECT MIN(ea) minea, MAX(ea) maxea FROM ".$this->db->dbprefix('companyitem')." where itemid='".$item->id."'";
            $minmax = $this->db->query($query)->row();
            $item->minprice = $minmax->minea;
            $item->maxprice = $minmax->maxea;
            
            $cquery = "SELECT count(ci.company) countitem FROM ".$this->db->dbprefix('companyitem')." ci join ".$this->db->dbprefix('item')." i on ci.itemid=i.id WHERE ci.itemid = ".$item->id." and ci.type='Supplier' group by ci.itemid";
            $countofitems = $this->db->query($cquery)->row();
            //echo "<pre>",print_r($countofitems->countitem); die;
            if(isset($countofitems->countitem) && $countofitems->countitem!="")
            	$item->offercount = $countofitems->countitem;
            else
            	$item->offercount = 0;
            
            $item->articles = $this->db->where('itemid',$item->id)->order_by('postedon','DESC')->limit(3)->get('itemarticle')->result();
            
            $item->hasdeal = $this->db->where('itemid',$item->id)->get('dealitem')->result()?true:false;
            $item->hasdeal = $this->db
                            ->where('itemid',$item->id)
                            ->where('dealactive','1')
                            ->where('qtyavailable >=','qtyreqd')
                            ->where('qtyavailable >','0')
                            ->where('dealdate >=',date('Y-m-d'))
                            ->get('dealitem')
                            ->result()
                            ?
                            true
                            :false
                            ;
            
            $this->data['items'][] = $item;
        }
        $this->data['norecords'] = '';
        if (! $this->data['items'])
        {
            $this->data['norecords'] = 'No Records found in items for the search.';
        }
        $this->data['categories'] = $this->itemcode_model->getcategories();
        $this->data['categoriesoptions'] = $this->items_model->getTreeOptions(@$_POST['category']);
        $this->data['categorymenu'] = $this->items_model->getCategoryMenu();
        $currentcategory = '';
        if(@$_POST['category'])
        {
            $this->db->where('id',$_POST['category']);
            $cat = $this->db->get('category')->row();
            if($cat)
                $currentcategory = $cat->catname;
        
            $catcodes = $this->catcode_model->get_categories_tiered();
            $categories = $this->itemcode_model->getcategories();
            /*
            foreach($categories as $cat)
                if($cat->id == $_POST['category'])
                    $this->data['breadcrumb'] = 'Current Category: '.$cat->catname;
            */
        }
        
        $this->data['userquotes'] = array();
        $this->data['projects'] = array();
        $this->data['costcodes'] = array();
        if ($this->session->userdata('site_loggedin'))
        {
            $pa = $this->session->userdata('site_loggedin')->purchasingadmin;
            $userquotes = $this->db->where('purchasingadmin',$pa)->where('potype','Bid')->get('quote')->result();
            foreach($userquotes as $uq)
            {
                if(!$this->db->where('quote',$uq->id)->get('invitation')->row())
                    $this->data['userquotes'][]=$uq;                
            }
            $this->data['projects'] = $this->db->where('purchasingadmin',$pa)->get('project')->result();
            $this->data['costcodes'] = $this->db->where('purchasingadmin',$pa)->get('costcode')->result();
        }
        
         $this->data['breadcrumb'] = $this->items_model->getParents(@$_POST['category']);
        $this->data['breadcrumb2'] = $this->items_model->getsubcategorynames(@$_POST['category']);        
        $this->data['currentcategory'] = $currentcategory;          
        //echo "<pre>",print_r($this->data); die;
        if($keyword){
        	      	
        	if(isset($_POST['searchfor']) && $_POST['searchfor'] == "suppliers"){
        		$this->data2 = $this->suppliers2($keyword);     
        		$this->data['data2'] = $this->data2;
        	}
        	if(isset($_POST['searchfor']) && $_POST['searchfor'] == "itemandtags")
        	$this->data['datatags'] = $this->items_model->find_tags($keyword);
        }
        if(isset($_POST['searchfor']))
        $this->data['searchfor'] = $_POST['searchfor'];
        $this->load->view('site/items', $this->data);
    }
    
    
        public function suppliers2 ($keyword = false)
    {
        //print_r($_POST);
        if ($this->input->post('keyword') || $keyword)
        {
            if (! $keyword)
            {
                $keyword = $this->input->post('keyword');
            }

            $query_suppliers = $this->search_supplier($keyword);
            $this->data2['found_records'] = "Found " . $query_suppliers->totalresult . " suppliers";
            $this->data2['submiturl'] = 'site/items/' . $keyword;
            $this->data2['keyword'] = $keyword;
            if ($keyword)
            {
                $this->data2['page_titile'] = "Search for \"$keyword\"";
            }
            else
            {
                $this->data2['page_titile'] = "Suppliers List";
            }
        }
        else
        {
            $details = get_my_address();
            $center = $details->loc;
            $center = "33.956419, -118.442232";
            $this->data2['my_location'] = get_my_location($details);
            $geo_coords = explode(",", $center);
            $search = new stdClass();
            $search->distance = 100000;
            $search->current_lat = $geo_coords[0];
            $search->current_lon = $geo_coords[1];
            $search->earths_radius = 6371;
            $use_supplier_position = false;
            $this->homemodel->set_search_criteria($search);

            $location = $this->input->post('location');

            //$lat = $this->input->post('lat');
            //$lng = $this->input->post('lng');
            if ($location)
            {
                $return = get_geo_from_address($location);
                if($return)
                {
                    $center = "{$return->lat}, {$return->long}";
                    $center = "33.956419, -118.442232";
                    $search->current_lat = $return->lat;
                    $search->current_lon = $return->long;
                    $this->homemodel->set_search_criteria($search);
                }
            }
            $this->homemodel->set_distance(20);
            $query_suppliers = $this->homemodel->get_nearest_suppliers();
            if (! $query_suppliers->totalresult)
            {
                $this->homemodel->set_distance(15000);
                $query_suppliers = $this->homemodel->get_nearest_suppliers($ignore_location = true);
                $this->homemodel->set_distance(20);
                $this->data2['found_records'] = "Found " . $query_suppliers->totalresult . " suppliers";
            }
            else
            {
                $this->data2['found_records'] = "Found " . $query_suppliers->totalresult . " nearest suppliers";
            }
            $this->data2['submiturl'] = 'site/items';
        }
        $this->data2['norecords'] = false;
        if ($query_suppliers->totalresult == 0)
        {
            $this->data2['norecords'] = 'No Records found in Suppliers for the search.';
        }
        $limit = 6;
        $this->data2['totalcount'] = $query_suppliers->totalresult;
        $this->data2['currentpage'] = $_POST['pagenum'] + 1;
        $this->data2['totalpages'] = ceil($this->data2['totalcount'] / $limit);
        $this->data2['submitmethod'] = 'POST';
        $this->data2['pagingfields'] = $_POST;
        $this->data2['suppliers'] = array();
        $suppliers = $query_suppliers->suppliers;
        $this->load->helper('text');
        foreach ($suppliers as $supplier)
        {
            $supplier->joinstatus = '';
            if ($this->session->userdata('site_loggedin'))
            {
                $supplier->joinstatus = '<a  class="btn btn-primary arrow-right" onclick="joinnetwork(' . $supplier->id . ')"/>Join</a>';
                $currentpa = $this->session->userdata('site_loggedin')->id;
                $this->db->where('fromid', $currentpa);
                $this->db->where('toid', $supplier->id);
                $this->db->where('fromtype', 'users');
                $this->db->where('totype', 'company');
                if ($this->db->get('joinrequest')->result())
                    $supplier->joinstatus = '<input type="button" value="Already sent request" class="btn btn-primary arrow-right"/>';
                $this->db->where('purchasingadmin', $currentpa);
                $this->db->where('company', $supplier->id);
                if ($this->db->get('network')->result())
                    $supplier->joinstatus = '<input type="button" value="Already in Network" class="btn btn-primary arrow-right"/>';
            }
            if ($keyword)
            {
                $supplier->contact = highlight_phrase($supplier->contact, $keyword, '<span style="color:#990000">', '</span>');
                $supplier->address = highlight_phrase($supplier->address, $keyword, '<span style="color:#990000">', '</span>');
                $supplier->title = highlight_phrase($supplier->title, $keyword, '<span style="color:#990000">', '</span>');
            }
            $this->data2['suppliers'][] = $supplier;
        }
        $sql = "SELECT DISTINCT(CONCAT(city,', ',state)) citystate FROM " . $this->db->dbprefix('company');
        $this->data2['citystates'] = $this->db->query($sql)->result();
        $this->data2['states'] = $this->db->get('state')->result();
        $this->data2['types'] = $this->db->get('type')->result();
        return $this->data2;
    }
    
    public function items ()
    {
        $limit = 18;
        $this->items_model->set_keyword(false);
        $items = $this->items_model->find_item();
        $this->data['totalcount'] = $items->totalresult;
        $this->data['currentpage'] = $_POST['pagenum'] + 1;
        $this->data['totalpages'] = ceil($this->data['totalcount'] / $limit);
        $this->data['submiturl'] = 'site/items';
        $this->data['submitmethod'] = 'POST';
        $this->data['pagingfields'] = $_POST;
        $this->data['page_titile'] = "Items List";
        $this->data['items'] = array();
        $items = $items->items;
        foreach ($items as $item)
        {
            $query = "SELECT MIN(ea) minea, MAX(ea) maxea FROM ".$this->db->dbprefix('companyitem')." where itemid='".$item->id."'";
            $minmax = $this->db->query($query)->row();
            $item->minprice = $minmax->minea;
            $item->maxprice = $minmax->maxea;
            
            $cquery = "SELECT count(ci.company) countitem FROM ".$this->db->dbprefix('companyitem')." ci join ".$this->db->dbprefix('item')." i on ci.itemid=i.id WHERE ci.itemid = ".$item->id." and ci.type='Supplier' group by ci.itemid";            
        	$countofitems = $this->db->query($cquery)->row();
        	//echo "<pre>",print_r($countofitems->countitem); die;
        	if(isset($countofitems->countitem) && $countofitems->countitem!="")
        	$item->offercount = $countofitems->countitem;
            else 
            $item->offercount = 0;
            
            $item->articles = $this->db->where('itemid',$item->id)->order_by('postedon','DESC')->limit(3)->get('itemarticle')->result();
            
            //$item->hasdeal = $this->db->where('itemid',$item->id)->get('dealitem')->result()?true:false;
            $item->hasdeal = $this->db
                            ->where('itemid',$item->id)
                            ->where('dealactive','1')
                            ->where('qtyavailable >=','qtyreqd')
                            ->where('qtyavailable >','0')
                            ->where('dealdate >=',date('Y-m-d'))
                            ->get('dealitem')
                            ->result()
                            ?
                            true
                            :false
                            ;
            
            $this->data['items'][] = $item;
        }
        $this->data['norecords'] = '';
        if (! $this->data['items'])
        {
            $this->data['norecords'] = 'No Records found for the search.';
        }
        $this->data['categories'] = $this->itemcode_model->getcategories();
        
         if(isset($_POST['category']))
        $category = $_POST['category'];
        else 
        $category = "";
        
        $this->data['categoriesoptions'] = $this->items_model->getTreeOptions($category);
        
        if($category){
        	
        	$sql1 = "SELECT * FROM ".$this->db->dbprefix('category')." WHERE id = '{$_POST['category']}' ORDER BY catname ASC";

        	$result1 = $this->db->query($sql1)->result();
        	if($result1)
        	$this->data['catname'] = $result1[0]->catname;
  	    }
        //if (@$_POST['category'])
            //$this->db->where('category', $_POST['category']);
        $this->data['subcategories'] = array();//$this->db->get('subcategory')->result();
        $this->data['categorymenu'] = $this->items_model->getCategoryMenu();
        $this->data['breadcrumb'] = @$_POST['breadcrumb'];
        
        $this->data['userquotes'] = array();
        $this->data['projects'] = array();
        $this->data['costcodes'] = array();
        if ($this->session->userdata('site_loggedin'))
        {
            $pa = $this->session->userdata('site_loggedin')->purchasingadmin;
            $userquotes = $this->db->where('purchasingadmin',$pa)->where('potype','Bid')->get('quote')->result();
            foreach($userquotes as $uq)
            {
                if(!$this->db->where('quote',$uq->id)->get('invitation')->row())
                    $this->data['userquotes'][]=$uq;                
            }
            $this->data['projects'] = $this->db->where('purchasingadmin',$pa)->get('project')->result();
            $this->data['costcodes'] = $this->db->where('purchasingadmin',$pa)->get('costcode')->result();
        }
        
        $this->data['breadcrumb'] = $this->items_model->getParents(@$_POST['category']);
        $this->data['breadcrumb2'] = $this->items_model->getsubcategorynames(@$_POST['category']);        
        //echo '<pre>';print_r($data['categorymenu']);die;
        $this->load->view('site/items', $this->data);
    }
    public function getCategoryImage($catID)
    {
        if($catID !=0)
        {
    
        $cat_data = $this->db->where('id',$catID)->get('category')->result();
       
       if($cat_data['0']->banner_image =='' || $cat_data['0']->banner_image =='0')
        {
            $cat_dataImage = $this->getCategoryImage($cat_data['0']->parent_id);
            return $cat_dataImage;
        }
        else {
            return $cat_data['0']->banner_image;
        }
    }
        else 
        {
        return "";
        }
    }
    public function item ($url)
    {
        $url = urldecode($url);
        //echo $url;die;
        $this->db->where('url', $url);
        $item = $this->db->get('item')->row();
     
        if(!$item)
            redirect('site');
            
        
        $id = $item->id;
        $item->articles = $this->db->where('itemid',$item->id)->order_by('postedon','DESC')->get('itemarticle')->result();
        $item->images = $this->db->where('itemid',$item->id)->get('itemimage')->result();
        $cat_data = $this->db->where('id',$item->category)->get('category')->result();
        $data['cat_image'] = $this->getCategoryImage($cat_data['0']->id);
        $mainimg = new stdClass();
        if($item->item_img)
        {
            $mainimg->filename = $item->item_img;
            $data['filetype'] = "image";
        }
        else
        {
            $mainimg->filename = 'big.png';
            $data['filetype'] = "image";
        }
        array_unshift($item->images, $mainimg);
        $data['item'] = $item; 
        $this->db->where('itemid', $id);
        $this->db->where('type', 'Supplier');
        $inventory = $this->db->get('companyitem')->result();
        $data['amazon'] = $this->items_model->get_amazon($id);
        
        if($item->featuredsupplier)
        {
            $this->db->where('itemid', $id);
            $this->db->where('company', $item->featuredsupplier);
            $this->db->where('type', 'Supplier');
            $item->featureditem = $this->db->get('companyitem')->row();
            if($item->featureditem)
            {
                $this->db->where('id',$item->featuredsupplier);
                $item->featuredsupplierdetails = $this->db->get('company')->row();
                
                if($item->featureditem->manufacturer)
                {
                    $this->db->where('id',$item->featureditem->manufacturer);
                    $item->featureditem->manufacturername = @$this->db->get('type')->row()->title;
                }
                
                if ($this->session->userdata('site_loggedin'))
                {
                    $item->orgea = $item->ea;
                    $currentpa = $this->session->userdata('site_loggedin')->id;
                    $this->db->where('company', $item->featuredsupplier);
                    $tiers = $this->db->get('tierpricing')->row();
                    if ($tiers)
                    {
                        $this->db->where('company', $item->featuredsupplier);
                        $this->db->where('purchasingadmin', $currentpa);
                        $tier = @$this->db->get('purchasingtier')->row()->tier;
                        if ($tier)
                        {
                            $item->featureditem->orgea = $item->featureditem->ea;
                            $tv = $tiers->$tier;
                            $item->featureditem->ea = $item->featureditem->ea + ($item->featureditem->ea * $tv / 100);
                            $item->featureditem->ea = number_format($item->featureditem->ea, 2);
                        }
                    }
                }
            }
        }
        //echo '<pre>';print_r($inventory);die;
        $inventorydata = array();
        foreach ($inventory as $initem)
        {
            $this->db->where('id',$initem->itemid);
            $orgitem = $this->db->get('item')->row();
            if(!$initem->itemname)
                $initem->itemname = $orgitem->itemname;
            $this->db->where('id',$initem->manufacturer);
            $initem->manufacturername = @$this->db->get('type')->row()->title;
            $initem->listprice = $initem->ea;
            $initem->joinstatus = '';
            $initem->dist = '';
            $this->db->where('id', $initem->company);
            $initem->companydetails = $this->db->get('company')->row();
            $this->db->where('id', $initem->company);
            $company = $this->db->get('company')->row();
            if(!$company)
                continue;
            if (! @$_POST['address'] && ! $this->session->userdata('site_loggedin'))
            {
                $details = get_my_address();
                $center = $details->loc;
                $geo_coords = explode(",", $center);
                $lat1 = $geo_coords[0];
                $lon1 = $geo_coords[1];                
                
                if ((! $company->com_lat || ! $company->com_lng) && $company->address)
                {
                    $geocode = file_get_contents(
                    "http://maps.google.com/maps/api/geocode/json?address=" . urlencode(str_replace("\n", ", ", $company->address)) . "&sensor=false");
                    $output = json_decode($geocode);
                    //print_r($output);
                    $update = array();
                    $lat2 = $update['com_lat'] = $output->results[0]->geometry->location->lat;
                    $lon2 = $update['com_lng'] = $output->results[0]->geometry->location->lng;
                    $this->db->where('id', $company->id);
                    $this->db->update('company', $update);
                }
                else
                {
                    $lat2 = $company->com_lat;
                    $lon2 = $company->com_lng;
                }
                //echo ($lat1 .'--'. $lon1 .'--'. $lat2 .'--'. $lon2.'<br/>');
                if ($lat1 && $lon1 && $lat2 && $lon2)
                {
                    //$dist = $this->haversineGreatCircleDistance($lat1,$lon1,$lat2,$lon2);
                    $theta = $lon1 - $lon2;
                    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                    $dist = acos($dist);
                    $dist = rad2deg($dist);
                    $miles = $dist * 60 * 1.1515;
                    $initem->dist = $miles;
                    if (! @$_POST['miles'])
                        $measure = 20;
                    else
                        $measure = $_POST['miles'];
                    //echo ($miles .'<'. $measure.'<br/>');
                    if($miles < $measure)
                        $inventorydata[] = $initem;
                }
            }
            elseif ($this->session->userdata('site_loggedin'))
            {
                $currentpa = $this->session->userdata('site_loggedin')->id;
                $this->db->where('company', $initem->company);
                $tiers = $this->db->get('tierpricing')->row();
                if ($tiers)
                {
                    $this->db->where('company', $initem->company);
                    $this->db->where('purchasingadmin', $currentpa);
                    $tier = @$this->db->get('purchasingtier')->row()->tier;
                    if ($tier)
                    {
                        $tv = $tiers->$tier;
                        $initem->ea = $initem->ea + ($initem->ea * $tv / 100);
                        $initem->ea = number_format($initem->ea, 2);
                    }
                }
                if (! @$_POST['address'])
                {
                    $lat1 = $this->session->userdata('site_loggedin')->user_lat;
                    $lon1 = $this->session->userdata('site_loggedin')->user_lng;
                }
                else
                {
                    $geocode = file_get_contents(
                    "http://maps.google.com/maps/api/geocode/json?address=" . urlencode(str_replace("\n", ", ", $_POST['address'])) . "&sensor=false");
                    $output = json_decode($geocode);
                    $update = array();
                    $lat1 = $update['com_lat'] = $output->results[0]->geometry->location->lat;
                    $lon1 = $update['com_lng'] = $output->results[0]->geometry->location->lng;
                }
                
                if ((! $company->com_lat || ! $company->com_lng) && $company->address)
                {
                    $geocode = file_get_contents(
                    "http://maps.google.com/maps/api/geocode/json?address=" . urlencode(str_replace("\n", ", ", $company->address)) . "&sensor=false");
                    $output = json_decode($geocode);
                    $update = array();
                    $lat2 = $update['com_lat'] = $output->results[0]->geometry->location->lat;
                    $lon2 = $update['com_lng'] = $output->results[0]->geometry->location->lng;
                    $this->db->where('id', $company->id);
                    $this->db->update('company', $update);
                }
                else
                {
                    $lat2 = $company->com_lat;
                    $lon2 = $company->com_lng;
                }
                //echo ($lat1 .' - '. $lon1 .' - '. $lat2 .' - '. $lon2);die;
                if ($lat1 && $lon1 && $lat2 && $lon2)
                {
                    //$dist = $this->haversineGreatCircleDistance($lat1,$lon1,$lat2,$lon2);
                    $theta = $lon1 - $lon2;
                    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                    $dist = acos($dist);
                    $dist = rad2deg($dist);
                    $miles = $dist * 60 * 1.1515;
                    $initem->dist = $miles;
                    //echo '<br/>'.$miles;
                    $this->db->where('purchasingadmin', $currentpa);
                    $this->db->where('company', $initem->company);
                    if ($this->db->get('network')->result())
                        $initem->joinstatus = ' (In Network)';
                    if (! @$_POST['miles'])
                        $measure = 20;
                    else
                        $measure = $_POST['miles'];
                    $_POST['miles'] = $measure;
                    $checknetwork = true;
                    if (@$_POST['innetwork'])
                        $checknetwork = @$_POST['innetwork'];
                    $checknetwork = true;
                    if (@$_POST['innetwork'])
                    {
                        $checknetwork = $initem->joinstatus == ' (In Network)' ? true : false;
                    }
                    $checkaddress = true;
                    //if(@$_POST['address'])
                    //$checkaddress = strpos($initem->companydetails->address, $_POST['address'])>=0?true:false;
                    //echo $company->title.'-'.$checknetwork.'-'.$checkaddress.'-'.$miles .'<'. $measure.'<br/>';
                    if ($miles < $measure && $checknetwork && $checkaddress)
                    {
                        /*
                        $dealitem = $this->db
                            ->where('itemid',$initem->itemid)
                            ->where('company',$initem->company)
                            ->where('dealactive','1')
                            ->where('qtyavailable >=','qtyreqd')
                            ->where('qtyavailable >','0')
                            ->where('dealdate >=',date('Y-m-d'))
                            ->get('dealitem')
                            ->row();
                        $initem->dealprice = '';
                        $initem->qtyreqd = 0;
                        if($dealitem)
                        {
                            $initem->dealprice = $dealitem->dealprice;
                            $initem->qtyreqd = $dealitem->qtyreqd;
                        }
                        */
                        $inventorydata[] = $initem;
                    }
                }
            }
            elseif (@$_POST['address'])
            {
                $geocode = file_get_contents(
                "http://maps.google.com/maps/api/geocode/json?address=" . urlencode(str_replace("\n", ", ", $_POST['address'])) . "&sensor=false");
                $output = json_decode($geocode);
                $update = array();
                $lat1 = $update['com_lat'] = $output->results[0]->geometry->location->lat;
                $lon1 = $update['com_lng'] = $output->results[0]->geometry->location->lng;
                if ((! @$company->com_lat || ! @$company->com_lng) && @$company->address)
                {
                    $geocode = file_get_contents(
                    "http://maps.google.com/maps/api/geocode/json?address=" . urlencode(str_replace("\n", ", ", $company->address)) . "&sensor=false");
                    $output = json_decode($geocode);
                    $update = array();
                    $lat2 = $update['com_lat'] = $output->results[0]->geometry->location->lat;
                    $lon2 = $update['com_lng'] = $output->results[0]->geometry->location->lng;
                    $this->db->where('id', $company->id);
                    $this->db->update('company', $update);
                }
                else
                {
                    $lat2 = $company->com_lat;
                    $lon2 = $company->com_lng;
                }
                //echo ($lat1 .' - '. $lon1 .' = '. $lat2 .' - '. $lon2.'--');
                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $initem->dist = $miles;
                if (! @$_POST['miles'])
                    $measure = 20;
                else
                    $measure = $_POST['miles'];
                $_POST['miles'] = $measure;
                //echo $company->title.'-'.$miles .'<'. $measure.'<br/>';
                if ($miles <= $measure)
                    $inventorydata[] = $initem;
            }
        }
        //echo '<pre>';print_r($inventorydata);die;
        $data['inventory'] = array();
        $data['filtermanufacturer'] = array();
        foreach($inventorydata as $initem)
        {
            if(!isset($data['filtermanufacturer'][$initem->manufacturer]))
                $data['filtermanufacturer'][$initem->manufacturer] = $this->db->where('id',$initem->manufacturer)->get('type')->row();
            
            if(!@$_POST['manufacturer'])
            {
                $data['inventory'][] = $initem;
            }
            elseif(@$_POST['manufacturer'] == $initem->manufacturer)
            {
                $data['inventory'][] = $initem;
            }
        }
        $ti = $this->db->dbprefix('item');
        $ri = $this->db->dbprefix('relateditem');
        $query = "SELECT * FROM $ti WHERE id IN (SELECT relateditem FROM $ri WHERE item='$id')";
        $data['relateditems'] = $this->db->query($query)->result();
         
        $dealitems = $this->db
                    ->select('dealitem.*, item.*')
                    ->from('dealitem')
                    ->join('companyitem',"dealitem.itemid=companyitem.id AND pms_dealitem.company=pms_companyitem.company AND pms_companyitem.type='Supplier'")
                    ->join('item',"companyitem.itemid=item.id")
                    ->where('dealitem.itemid',$id)
                    ->where('dealitem.dealactive','1')
                    ->where('dealitem.qtyavailable >',0)
                    ->where('dealitem.qtyavailable >=','dealitem.qtyreqd')
                    ->where('dealitem.dealdate >=',date('Y-m-d'))
                    ->get()
                    ->result();
         
        $dealitems = $this->db
                    ->select('dealitem.*')
                    ->from('dealitem')
                    ->where('dealitem.itemid',$id)
                    ->where('dealitem.dealactive','1')
                    ->where('dealitem.qtyavailable >',0)
                    ->where('dealitem.qtyavailable >=','dealitem.qtyreqd')
                    ->where('dealitem.dealdate >=',date('Y-m-d'))
                    ->get()
                    ->result();
        //echo '<pre>';print_r($dealitems);//die;
        $data['dealfeed'] = array();
        foreach($dealitems as $di)
        {
            if($di->dealactive)
            {
                if(!$di->image)
                    $di->image="big.png";
                $di->companyusername = $this->db->where('id',$di->company)->get('company')->row()->username;
                $di->companyname = $this->db->where('id',$di->company)->get('company')->row()->title;
                $orgitem = $this->db->where('id',$di->itemid)->get('item')->row();
                $di->url = $orgitem->url;
                $di->itemcode = $orgitem->itemcode;
                $di->itemname = $orgitem->itemname;
                if(isset($tv))
                {
                    $di->dealprice = $di->dealprice + ($di->dealprice * $tv / 100);
                    $di->dealprice = number_format($di->dealprice, 2);
                }
                if($di->memberonly)
                {
                    if($this->session->userdata('site_loggedin'))
                        $data['dealfeed'][] = $di;
                }
                else
                {
                    $data['dealfeed'][] = $di;
                }
            }
        }
        
        $data['userquotes'] = array();
        $data['projects'] = array();
        $data['costcodes'] = array();
        if ($this->session->userdata('site_loggedin'))
        {
            $pa = $this->session->userdata('site_loggedin')->purchasingadmin;
            $userquotes = $this->db->where('purchasingadmin',$pa)->where('potype','Bid')->get('quote')->result();
            foreach($userquotes as $uq)
            {
                if(!$this->db->where('quote',$uq->id)->get('invitation')->row())
                    $data['userquotes'][]=$uq;                
            }
           // $this->data['projects'] = $this->db->where('purchasingadmin',$pa)->get('project')->result();
            $data['projects'] = $this->db->where('purchasingadmin',$pa)->get('project')->result();
            $data['costcodes'] = $this->db->where('purchasingadmin',$pa)->get('costcode')->result();

        }
        
        $totalQuote = $this->db->where('itemid',$item->id)->join("pms_quoteitem",'pms_quoteitem.quote = quote.id')->from('quote')->count_all_results();
        $data['totalQuote'] = $totalQuote;
        //print_r($data['dealfeed']);die;
        /*
        $categories = $this->itemcode_model->getcategories();
        foreach($categories as $cat)
        {
            if($cat->id == $item->category)
                $data['breadcrumb'] = '<b>Category:</b> '.$cat->catname;
        }
        */
        $data['breadcrumb'] = $this->items_model->getParents($item->category);
        //echo '<pre>'; print_r($data['relateditems']);die;
        $this->load->view('site/item', $data);
    }
    public function tag($tag){
    	$tag = str_replace('%7C', '/', $tag);
    
    	$tag=urldecode($tag);
    	$tag=urldecode($tag);
    	$limit = 18;
    	$this->items_model->set_keyword(false);
    	$items = $this->items_model->find_item_byTag($tag);
    	$this->data['totalcount'] = $items->totalresult;
    	$this->data['currentpage'] = $_POST['pagenum'] + 1;
    	$this->data['totalpages'] = ceil($this->data['totalcount'] / $limit);
    	$this->data['submiturl'] = 'site/items';
    	$this->data['submitmethod'] = 'POST';
    	$this->data['pagingfields'] = $_POST;
    	$this->data['page_title'] = "Items tagged \"".$tag."\"";
    	$this->data['items'] = array();
    	$items = $items->items;
    	foreach ($items as $item)
    	{
    		$query = "SELECT MIN(ea) minea, MAX(ea) maxea FROM ".$this->db->dbprefix('companyitem')." where itemid='".$item->id."'";
    		$minmax = $this->db->query($query)->row();
    		$item->minprice = $minmax->minea;
    		$item->maxprice = $minmax->maxea;
    	
    		$cquery = "SELECT count(ci.company) countitem FROM ".$this->db->dbprefix('companyitem')." ci join ".$this->db->dbprefix('item')." i on ci.itemid=i.id WHERE ci.itemid = ".$item->id." and ci.type='Supplier' group by ci.itemid";
    		$countofitems = $this->db->query($cquery)->row();
    		//echo "<pre>",print_r($countofitems->countitem); die;
    		if(isset($countofitems->countitem) && $countofitems->countitem!="")
    			$item->offercount = $countofitems->countitem;
    		else
    			$item->offercount = 0;
    	
    		$item->articles = $this->db->where('itemid',$item->id)->order_by('postedon','DESC')->limit(3)->get('itemarticle')->result();
    	
    		//$item->hasdeal = $this->db->where('itemid',$item->id)->get('dealitem')->result()?true:false;
    		$item->hasdeal = $this->db
    		->where('itemid',$item->id)
    		->where('dealactive','1')
    		->where('qtyavailable >=','qtyreqd')
    		->where('qtyavailable >','0')
    		->where('dealdate >=',date('Y-m-d'))
    		->get('dealitem')
    		->result()
    		?
    		true
    		:false
    		;
    	
    		$this->data['items'][] = $item;
    	}
    	$this->data['norecords'] = '';
    	if (! $this->data['items'])
    	{
    		$this->data['norecords'] = 'No Records found for the search.';
    	}
    	$this->data['categories'] = $this->itemcode_model->getcategories();
    	
    	if(isset($_POST['category']))
    		$category = $_POST['category'];
    	else
    		$category = "";
    	
    	$this->data['categoriesoptions'] = $this->items_model->getTreeOptions($category);
    	
    	if($category){
    		 
    		$sql1 = "SELECT * FROM ".$this->db->dbprefix('category')." WHERE id = '{$_POST['category']}' ORDER BY catname ASC";
    	
    		$result1 = $this->db->query($sql1)->result();
    		if($result1)
    			$this->data['catname'] = $result1[0]->catname;
    	}
    	//if (@$_POST['category'])
    	//$this->db->where('category', $_POST['category']);
    	$this->data['subcategories'] = array();//$this->db->get('subcategory')->result();
    	$this->data['categorymenu'] = $this->items_model->getCategoryMenu();
    	$this->data['breadcrumb'] = @$_POST['breadcrumb'];
    	
    	$this->data['userquotes'] = array();
    	$this->data['projects'] = array();
    	$this->data['costcodes'] = array();
    	if ($this->session->userdata('site_loggedin'))
    	{
    		$pa = $this->session->userdata('site_loggedin')->purchasingadmin;
    		$userquotes = $this->db->where('purchasingadmin',$pa)->where('potype','Bid')->get('quote')->result();
    		foreach($userquotes as $uq)
    		{
    			if(!$this->db->where('quote',$uq->id)->get('invitation')->row())
    				$this->data['userquotes'][]=$uq;
    		}
    		$this->data['projects'] = $this->db->where('purchasingadmin',$pa)->get('project')->result();
    		$this->data['costcodes'] = $this->db->where('purchasingadmin',$pa)->get('costcode')->result();
    	}
    	
    	$this->data['breadcrumb'] = $this->items_model->getParents(@$_POST['category']);
    	
    	//echo '<pre>';print_r($data['categorymenu']);die;
    	$this->load->view('site/tag', $this->data);
    }
    public function previtem ($id)
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('item');
        $result = $this->db->query($sql)->result();
        for ($i = 0; $i < count($result); $i ++)
        {
            if ($result[$i]->id == $id && isset($result[$i - 1]))
            {
                return $result[$i - 1]->id;
            }
        }
        return null;
    }
    public function nextitem ($id)
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('item');
        $result = $this->db->query($sql)->result();
        for ($i = 0; $i < count($result); $i ++)
        {
            if ($result[$i]->id == $id && isset($result[$i + 1]))
            {
                return $result[$i + 1]->id;
            }
        }
        return null;
    }
    
    public function article($url)
    {
        $url = urldecode($url);
        $article = $this->db->where('url',$url)->get('itemarticle')->row();
        $data['article'] = $article;
        $data['links'] = $this->db->where('article',$article->id)->get('articlelink')->result();
        $data['articleitems'] = $this->db->select('item.*')->where('article',$article->id)->from('articleitem')->join('item','item=item.id')->get()->result();
        $data['item'] = $this->db->where('id',$article->itemid)->get('item')->row();
        //print_r($data);die;
        $this->load->view('site/article',$data);
    }
    
    public function sendrequest($id)
    {
        if(!$_POST)
            die;
        $body = '';
	    $settings = (array)$this->homemodel->getconfigurations ();
	    if(strpos(@$_POST['redirect'], 'supplier') === 0)
	    {
	        $supplier = $this->db->where('id',$id)->get('company')->row();
	        $to = $supplier->primaryemail;
		    $body .= 'You have a new request for assistance.';
	    }
	    else
	    {
	        $to = $settings['adminemail'];
		    $body .= 'You have a new request for assistance regarding '.site_url($_POST['redirect']).'.';
	    }
		$body .= ' Details are:<br/><br/>';
		$body .= "Type: ".$_POST['type']."<br/>";
		$body .= "Name: ".$_POST['name']."<br/>";
		$body .= "Email: ".$_POST['email']."<br/>";
		$body .= "Phone: ".$_POST['phone']."<br/>";
		
		if($_POST['type'] == 'Request Phone Assistance')
		{
    		$body .= "Best day to call: ".$_POST['day']."<br/>";
    		$body .= "Best time to call: ".$_POST['time']."<br/>";
		}
		else 
		{
    		$body .= "Appointment date: ".$_POST['day']."<br/>";
    		$body .= "Appointment time: ".$_POST['time']."<br/>";
		}
		
		$body .= "Regarding: ".$_POST['regarding']."<br/>";

		$this->load->library('email');
		
		$this->email->from($settings['adminemail']);
		$this->email->to($to);
		
		$this->email->subject('Request for assistance');
		$this->email->message($body);	
		$this->email->set_mailtype("html");
		$this->email->send();
		
        $this->session->set_flashdata('message', 'Email was sent.');
		
	    redirect('site/'.$_POST['redirect']);
    }
    
    public function additemtoquote()
    {
        $pa = $this->session->userdata('site_loggedin');
        if (!$pa)
            die('Login expired');
        if(!@$_POST['quote'])
        	die('No PO specified.');
        $itemid = $_POST['itemid'];
        
        if($this->db->where('quote',$_POST['quote'])->where('itemid',$_POST['itemid'])->get('quoteitem')->row())
        {
            die('Item already exists in the PO.');
        }
        $orgitem = $this->db->where('id',$itemid)->get('item')->row();
        //print_r($pa);die;
        $cmpitem = $this->db->where('itemid',$itemid)
                        ->where('company',$pa->id)
                        ->where('type','Purchasing')
                        ->get('companyitem')->row();
        //print_r($cmpitem);die;
        $_POST['purchasingadmin'] = $pa->id;
        $_POST['itemcode'] = @$cmpitem->itemcode?@$cmpitem->itemcode:$orgitem->itemcode;
        $_POST['itemname'] = @$cmpitem->itemname?@$cmpitem->itemname:$orgitem->itemname;
        $_POST['unit'] = @$cmpitem->unit?@$cmpitem->unit:$orgitem->unit;
        $_POST['ea'] = @$cmpitem->ea?@$cmpitem->ea:$orgitem->ea;
        $_POST['notes'] = @$cmpitem->notes?@$cmpitem->notes:$orgitem->notes;
        $_POST['totalprice'] = $_POST['quantity'] * $_POST['ea'];
        //print_r($_POST);die;
        $this->db->insert('quoteitem',$_POST);
        echo 'Success';
    }

    function getquotes() 
    {
        $pa = $this->session->userdata('site_loggedin');
        if (!$pa)
            die;
        $pid = $_POST['pid'];
        $sql = "SELECT * FROM " . $this->db->dbprefix('quote')
                . " WHERE pid='$pid' AND potype='Bid'";
        $sql .= " AND purchasingadmin='" . $pa->id . "'";
        //echo $sql;die;
        $items = $this->db->query($sql)->result();
        $ret = '<select name="quote" required>';
        foreach($items as $item)
        {
            if(!$this->db->where('quote',$item->id)->get('invitation')->row())
                $ret .= '<option value="'.$item->id.'">'.$item->ponum.'</option>'."\r\n";
        }
        $ret .= '</select>';
        echo $ret;
    }

    function getcostcodes() 
    {
        $pa = $this->session->userdata('site_loggedin');
        if (!$pa)
            die;
        $pid = $_POST['pid'];
        $sql = "SELECT * FROM " . $this->db->dbprefix('costcode')
                . " WHERE project='$pid'";
        $sql .= " AND purchasingadmin='" . $pa->id . "'";
        //echo $sql;die;
        $items = $this->db->query($sql)->result();
        $ret = '<select name="costcode" required>';
        foreach($items as $item)
        {
            $ret .= '<option value="'.$item->code.'">'.$item->code.'</option>'."\r\n";
        }
        $ret .= '</select>';
        echo $ret;
    }
    
    public function about ()
    {
        $this->load->view('site/about');
    }
    public function pricing ()
    {
        $this->load->view('site/pricing');
    }
    public function contact ()
    {
        $this->load->view('site/contact');
    }
    function haversineGreatCircleDistance ($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
