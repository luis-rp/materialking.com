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
        $this->load->model('quotemodel', '', TRUE);
        $this->load->model('admin/banner_model', '', TRUE);
        $this->load->model('admin/itemcode_model', '', TRUE);
        $this->load->model('admin/catcode_model', '', TRUE);
        $this->load->model('admin/quote_model', '', TRUE);
        $this->load->model('event_model', '', TRUE);
		$this->load->model ('items_model', '', TRUE);
		$this->load->model('form_model', '', TRUE);
		$data['banner']=$this->banner_model->display();
		//$data['categorymenu'] = $this->items_model->getCategoryMenu (0) ;
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
    public function insertuserip(){
    	if(isset($_POST['ipaddress'])){
    		$datatoadd = array('ip' => $_POST['ipaddress']);
    		$this->db->insert('ipaddress',$datatoadd);
    	}
    }
    public function index ()
    {
    	if(isset($_SERVER['REMOTE_ADDR'])){
			$this->db->where('ip', $_SERVER['REMOTE_ADDR']);
    					if ($this->db->get('ipaddress')->result()){
    						$data['videocount'] = 1;
    					}else{
							$data['videocount'] = 0;
    					}
		}else
		$data['videocount'] = 0;
		if(isset($_SERVER['REMOTE_ADDR'])){
			$data['ipaddress'] = $_SERVER['REMOTE_ADDR'];
		}else{
			$data['ipaddress'] = "";
		}
    	
    	$details = get_my_address();   	
    	$center = $details->loc;
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
    	 
    	if ($location)
    	{
    		$return = get_geo_from_address($location);
    		if($return)
    		{
    			$center = "{$return->lat}, {$return->long}";
    			$search->current_lat = $return->lat;
    			$search->current_lon = $return->long;
    			if($this->input->post('radius')!=0)
    			{
    				$search->distance = $this->input->post('radius');
    			}
    			$this->homemodel->set_search_criteria($search);
    		}
    	}
    	
    	$suppliers_near_me = $this->homemodel->get_nearest_suppliers();
    	$data['suppliers_10_miles'] = false;
    	$nearest_10 = false;
    	if (!$suppliers_near_me)
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
    	
    	if (!$suppliers = $nearest_10)
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
    			//$supplier->joinmark = '';
    			$supplier->joinmark = '<a  class="btn btn-primary arrow-right" onclick="joinnetwork(' . $supplier->id . ')" /> Join </a>';
    			if ($this->session->userdata('site_loggedin'))
    			{
    				$currentpa = $this->session->userdata('site_loggedin')->id;
    				//$supplier->joinmark = '<a  class="btn btn-primary arrow-right" onclick="joinnetwork(' . $supplier->id . ')" /> Join </a>';
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
    			//log_message('debug',"lat:".$supplier->com_lat.":lng:".$supplier->com_lng);
    			if($supplier->com_lat!="" && $supplier->com_lng!=""){
    				if($supplier->company_type == 1)
    				{
    					$img = '<img style="margin-top:-10px ; float: right;  width:105px; height:80px;" src="'.site_url('uploads/logo/thumbs/premium.png').'" alt="">';
    				}
    				else 
    				{
    					$img = '';
    				}
    			$popups["$supplier->com_lat, $supplier->com_lng"] = '<div class="infobox"><div class="image"><img src="' . base_url() . 'uploads/logo/thumbs/' . $supplier->logo .
    			'" alt="" width="100"></div><div class="title"><a href="' . site_url('site/supplier/' . $supplier->username) . '">' . $supplier->title .
    			'</a></div><div class="area"><div class="price">&nbsp;</div><span class="key" style="word-break:break-all;">'.$supplier->contact .'<br/>' . $supplier->city.'<br>'.$supplier->state . '</span><span class="value">' . '' .
    			'</span></div><div style="align:left;overflow:hidden;"><p>' . $supplier->joinmark . '</p><p><div class="btn btn-primary arrow-right"><a href="' . site_url('site/supplier/' . $supplier->username) . '">View Profile</a></div></p><p><div class="btn btn-primary arrow-right "><a href="' . site_url('store/items/' . $supplier->username) . '">Go to Store</a></div></p>'.$img.'</div></div>';
    			}	
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
    	$this->db->order_by("title", "asc"); 
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
    	$data['page_title']="The B2B Network for Contractors & Supply Houses";
    	$data['page_description']="The B2B Network for Contractors & Supply Houses";
    	$query="SELECT s.miles from ".$this->db->dbprefix('settings')." s JOIN ".$this->db->dbprefix('users')." u ON s.purchasingadmin=u.id WHERE u.usertype_id=1 AND u.isdeleted=0";
    	$allmiles=$this->db->query($query)->row()->miles;
    	if(isset($allmiles) && $allmiles!="")
    	{
    		$milesradius=explode(",",$allmiles);
    		$data['milesradius']=$milesradius;
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
            if ($location)
            {
                $return = get_geo_from_address($location);
                if($return)
                {
                    $center = "{$return->lat}, {$return->long}";
                    $search->current_lat = $return->lat;
                    $search->current_lon = $return->long;
                    if($this->input->post('radius')!=0)
    				{
    				$search->distance = $this->input->post('radius');
    				}
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
        $sql = "SELECT DISTINCT(CONCAT(city,', ',state)) citystate FROM " . $this->db->dbprefix('company') ." WHERE isdeleted = 0";
        $this->data['citystates'] = $this->db->query($sql)->result();
        $this->data['states'] = $this->db->get('state')->result();
        $this->data['types'] = $this->db->get('type')->result();
       	$this->data['page_title'] = "Construction & Building Supply House Search Engine, Directory, Reviews & Business Info.";
       	$this->data['page_description'] = "Construction & Building Supply House Search Engine, Directory, Reviews & Business Info.";
       	
       	$query="SELECT s.miles from ".$this->db->dbprefix('settings')." s JOIN ".$this->db->dbprefix('users')." u ON s.purchasingadmin=u.id WHERE u.usertype_id=1 AND u.isdeleted=0";
    	$allmiles=$this->db->query($query)->row()->miles;
    	if(isset($allmiles) && $allmiles!="")
    	{
    		$milesradius=explode(",",$allmiles);
    		$this->data['milesradius']=$milesradius;
    	}
       	
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
    public function supplier ($username=NULL)
    {
    	$this->load->helper('text');
    	$this->load->library('image_lib');
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
         $data['inventory'] = array();
         foreach($inventory as $initem)
         {
         	
         	 $hasdiscount = $this->db
                            ->where('itemid',$initem->itemid)
                            ->get('qtydiscount')
                            ->result();
            if($hasdiscount){
              $initem->hasdiscount = true;}
            else{
             $initem->hasdiscount = false;}
            $this->db->where('id',$initem->manufacturer);
            $initem->manufacturername = @$this->db->get('type')->row()->title;
            $this->db->where('id',$initem->itemid);
            $orgitem = $this->db->get('item')->row();
            if(!is_object($orgitem)){
            	continue;
            }
            if(!$initem->itemname)
            	$initem->itemname = $orgitem->itemname;
            if(!$initem->itemcode)
            	$initem->itemcode = $orgitem->itemcode;
            if(!$initem->image)
            	$initem->image = $orgitem->item_img;
            	
            $initem->increment = $orgitem->increment;	
            $initem->unit = $orgitem->unit;
            $initem->url = $orgitem->url;
            $this->db->where('id', $initem->company);
            $res2 = $this->db->get('company')->row();
            if($res2){
            	$initem->phone = $res2->phone;
            }else
            $initem->phone = "";
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
                $di->increment = $orgitem->increment;
                $di->price = @$cmpitem->price?$cmpitem->price:'';
                if(@$cmpitem->company){
                $this->db->where('id', $cmpitem->company);
            	$res = $this->db->get('company')->row();
            	if($res){
            		$di->phone = $res->phone;
            	}else
            	$di->phone = "";
                }else
                $di->phone = "";
                $di->unit = $orgitem->unit;
                $di->url = $orgitem->url;
                if(!$di->image)
                $di->image = $orgitem->item_img;
                $di->itemcode = @$cmpitem->itemcode?$cmpitem->itemcode:$orgitem->itemcode;
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
        $this->db->where("user_id",$data['supplier']->id);
        $ads = $this->db->get("ads")->result();
       
        $data['adforsupplier']=array(); 
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
	     		$config['maintain_ratio'] = FALSE;
	     		$config['width']     = 190;
	     		$config['height']   = 194;
	     		$this->image_lib->clear();
	     		$this->image_lib->initialize($config);
	     		$this->image_lib->resize();
	     		$data['adforsupplier'][] = $ad;
	     	} 
        }
     	//$data['adforsupplier']=$ads;
     	$membersDB = $this->db->where("cid", $data['supplier']->id)->get("companyteam")->result();
     	foreach($membersDB as $mdb){
     		$config['image_library'] = 'gd2';
     		$config['source_image'] = './uploads/companyMembers/'.$mdb->picture;
     		$config['create_thumb'] = TRUE;
     		$config['maintain_ratio'] = FALSE;
     		$config['width']     = 190;
     		$config['height']   = 194;
     		$this->image_lib->clear();
     		$this->image_lib->initialize($config);
     		$this->image_lib->resize();
     		$pathinfo = pathinfo($mdb->picture);
     		$mdb->picture = $pathinfo["filename"]."_thumb.".$pathinfo["extension"];
     		$data["members"][] = $mdb;
     	}
        //print_r($data['dealfeed']);die;
        $similarsuppliers = $this->supplier_model->getrelatedsupplier($id);
        $data['similarsuppliers'] = $similarsuppliers;
        //print_r($data['dealfeed']);die;
        $this->db->where("CompanyID",$data['supplier']->id);
        $data["fields"] = $this->db->get("formsubscription")->result();
        $data['image']=$this->db->get_where('companyattachment',array('company'=>$data['supplier']->id))->result();
       	$data['page_title'] = $data["supplier"]->title;
       	$data['page_description'] = character_limiter($data["supplier"]->shortdetail,150);
       	$upcomingevents = $this->event_model->get_upcoming_items($data['supplier']->id);
       	$data['upcomingevents'] = $upcomingevents;
       	if ($this->session->userdata('site_loggedin'))
       	 {
        	 $data['filesprivate']=$this->db->get_where('company_files',array('company'=>$data['supplier']->id,'private'=>1))->result();
       	 }
        $data['filespublic']=$this->db->get_where('company_files',array('company'=>$data['supplier']->id,'private'=>0))->result();
        $data['gallery']=$this->db->get_where('gallery',array('company'=>$data['supplier']->id))->result();
        $data['designbook']=$this->db->get_where('designbook',array('company'=>$data['supplier']->id))->result();
        
        $bhrs = $this->db->get_where('company_business_hours',array('company'=>$data['supplier']->id))->result();
        $data['businesshrs'] = $bhrs;
        
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
            $data['projects'] = $this->db->where('purchasingadmin',$pa)->get('project')->result();
            $data['costcodes'] = $this->db->where('purchasingadmin',$pa)->get('costcode')->result();
        }
        
        
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
    public function search($keyword = false)
    {
        if ($this->input->post('keyword') != '')
        {
            $keyword = $this->input->post('keyword');
        }
        $this->items_model->set_keyword($keyword);
        $items = $this->items_model->find_item();
        $limit = 18;
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
        	
        	$query6 = "SELECT ci.ea as minea , ci.price 
        			 	FROM ".$this->db->dbprefix('companyitem')." ci,".$this->db->dbprefix('company')." c
        			 	where c.id=ci.company AND c.isdeleted=0 AND ci.itemid='".$item->id."' and ci.ea = (SELECT MIN(ea) ea  FROM ".$this->db->dbprefix('companyitem')." where itemid='".$item->id."')";
            $min = $this->db->query($query6)->row();
            if($min){
            	$item->callminprice = $min->price;
            }
            $query7 = "SELECT ci.ea as maxea , ci.price 
            		   FROM ".$this->db->dbprefix('companyitem')." ci,".$this->db->dbprefix('company')." c 
            		   where  c.id=ci.company AND c.isdeleted=0 AND ci.itemid='".$item->id."' and ci.ea = (SELECT MAX(ea) ea  FROM ".$this->db->dbprefix('companyitem')." where itemid='".$item->id."')";
            $max = $this->db->query($query7)->row();
            if($max){           	
            	$item->callmaxprice = $max->price;
            }
        	
            $query = "SELECT MIN(ea) minea, MAX(ea) maxea, price FROM ".$this->db->dbprefix('companyitem')." ci ,".$this->db->dbprefix('company')." c  where c.id=ci.company AND c.isdeleted=0 AND itemid='".$item->id."'";
            $minmax = $this->db->query($query)->row();
            $item->minprice = $minmax->minea;
            $item->maxprice = $minmax->maxea;
            $item->price = $minmax->price;
            $cquery = "SELECT count(ci.company) countitem 
            			FROM ".$this->db->dbprefix('companyitem')." ci 
            			join ".$this->db->dbprefix('item')." i on ci.itemid=i.id 
            			join ".$this->db->dbprefix('company')." c on c.id=ci.company
            			WHERE c.isdeleted=0 AND ci.itemid = ".$item->id." and ci.type='Supplier' 
            			group by ci.itemid";
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
			$hasdiscount = $this->db
                            ->where('itemid',$item->id)
                            ->get('qtydiscount')
                            ->result();
            if($hasdiscount)
              $item->hasdiscount = true;
            else
             $item->hasdiscount = false;
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
        
        if($keyword)
        {
        	if(isset($_POST['searchfor']) && $_POST['searchfor'] == "suppliers")
        	{
        		$this->data2 = $this->suppliers2($keyword);
        		$this->data['data2'] = $this->data2;
        	}
        	
        	/*if(isset($_POST['searchfor']) && $_POST['searchfor'] == "contractors")
        	{
        		$data['contractors'] = $this->contractors($keyword);
        	}*/
        	
        	if(isset($_POST['searchfor']) && $_POST['searchfor'] == "itemandtags")
        	$tagarray = $this->items_model->find_tags($keyword);
        	
        	if(@$tagarray)
        	{
        		$tagarray2 = array();
        		foreach($tagarray as $tag)
        		$tagarray2[] = trim(strtolower($tag));
        		$tagarray2 = array_unique($tagarray2);
        		$this->data['datatags'] = $tagarray2;
        	}
        }
        
        if(isset($_POST['searchfor']))
        $this->data['searchfor'] = $_POST['searchfor'];
        
       /* if(isset($_POST['searchfor']) && $_POST['searchfor'] == "suppliers")
        {
           $this->load->view('site/suppliers', $this->data);
        }
        if(isset($_POST['searchfor']) && $_POST['searchfor'] == "contractors")
        {
           $this->load->view('site/contractors', $data);
        }
        if(isset($_POST['searchfor']) && $_POST['searchfor'] == "itemandtags")
        {
           $this->load->view('site/items', $this->data);
        }*/
        	
       
        
        
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
        $sql = "SELECT DISTINCT(CONCAT(city,', ',state)) citystate FROM " . $this->db->dbprefix('company') . " WHERE isdeleted = 0";
        $this->data2['citystates'] = $this->db->query($sql)->result();
        $this->data2['states'] = $this->db->get('state')->result();
        $this->data2['types'] = $this->db->get('type')->result();
        return $this->data2;
    }
    public function items ($categroyurl="",$curpage="")
    {
    	
    	if(!@$_POST['category']){
    		 $catid = $this->items_model->getcategoryid_fromurl(urldecode($categroyurl));
    		 if(@$catid->id)
    		 $_POST['category'] = $catid->id;
    	}
    	/*if($this->session->userdata('currentpage') != '')
    	{
    		$_POST['pagenum'] = $this->session->userdata('currentpage');
    	}*/
        $limit = 18;
        
        if($curpage!='')
        $_POST['pagenum'] = $curpage-1;
        
        $this->items_model->set_keyword(false);
        $items = $this->items_model->find_item();
        $this->data['totalcount'] = $items->totalresult;       
        $this->data['currentpage'] = $_POST['pagenum'] + 1;
        $this->data['totalpages'] = ceil($this->data['totalcount'] / $limit);
        $this->data['submiturl'] = 'site/items';
        $this->data['submitmethod'] = 'POST';
        $this->data['pagingfields'] = $_POST;
        $this->data['page_titile'] = "Items List";
               
        /*if($this->session->userdata('currentpage') != $_POST['pagenum'])
        {
        	$this->session->set_userdata('currentpage',$_POST['pagenum']);
        }*/	
     
        $this->data['items'] = array();
        $items = $items->items;
        
        foreach ($items as $item)
        {
            $query = "SELECT ci.ea as minea , ci.price 
            		  FROM ".$this->db->dbprefix('companyitem')." ci, ".$this->db->dbprefix('company')." c 
            		  where c.id = ci.company AND c.isdeleted = 0 AND ci.itemid='".$item->id."' 
            		  and ci.ea = (SELECT MIN(ea) ea  FROM ".$this->db->dbprefix('companyitem')." where itemid='".$item->id."' AND ea > '0')";
            $min = $this->db->query($query)->row();
            if($min){
            	$item->minprice = $min->minea;
            	$item->callminprice = $min->price;
            }
            $query2 = "SELECT ci.ea as maxea , ci.price 
            		   FROM ".$this->db->dbprefix('companyitem')."  ci, ".$this->db->dbprefix('company')." c 
            		   where c.id = ci.company AND c.isdeleted = 0 AND ci.itemid='".$item->id."' 
            		   and ci.ea = (SELECT MAX(ea) ea  FROM ".$this->db->dbprefix('companyitem')." where itemid='".$item->id."')";
            $max = $this->db->query($query2)->row();
            if($max){
            	$item->maxprice = $max->maxea;
            	$item->callmaxprice = $max->price;
            }
			$cquery = "SELECT count(ci.company) countitem 
						FROM ".$this->db->dbprefix('companyitem')." ci 
					    join ".$this->db->dbprefix('item')." i on ci.itemid=i.id 
					    join ".$this->db->dbprefix('company')." c on ci.company=c.id
					    WHERE c.isdeleted = 0 AND ci.itemid = ".$item->id." and ci.type='Supplier' and ci.ea > 0 group by ci.itemid";
        	$countofitems = $this->db->query($cquery)->row();
        	//echo "<pre>",print_r($countofitems->countitem); die;
        	if(isset($countofitems->countitem) && $countofitems->countitem!="")
        	$item->offercount = $countofitems->countitem;
            else
            $item->offercount = 0;
            $item->articles = $this->db->where('itemid',$item->id)->order_by('postedon','DESC')->limit(3)->get('itemarticle')->result();
            //$item->hasdeal = $this->db->where('itemid',$item->id)->get('dealitem')->result()?true:false;
             $hasdeal = $this->db
                            ->where('itemid',$item->id)
                            ->where('dealactive','1')
                            ->where('qtyavailable >=','qtyreqd')
                            ->where('qtyavailable >','0')
                            ->where('dealdate >=',date('Y-m-d'))
                            ->get('dealitem')
                            ->result();
            if($hasdeal)
              $item->hasdeal = true;
            else
             $item->hasdeal = false;
           /* $hasdiscount = $this->db
                            ->where('itemid',$item->id)
                            ->get('qtydiscount')
                            ->result();*/
                            
             $sql="SELECT * FROM ".$this->db->dbprefix('qtydiscount')." qd JOIN ".$this->db->dbprefix('company')." c ON qd.company=c.id AND c.isdeleted='0' AND qd.itemid='".$item->id."'";
             $hasdiscount=$this->db->query($sql)->result();                      
            if($hasdiscount)
              $item->hasdiscount = true;
            else
             $item->hasdiscount = false;
            $this->data['items'][] = $item;
        }
        $this->data['norecords'] = '';
        if (! $this->data['items'])
        {
            $this->data['norecords'] = 'No Records found for the search.';
        }
        $this->data['categories'] = $this->itemcode_model->getcategories();
        //echo "<pre>"; print_r($_POST['category']); die;
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
        $this->data['manufacturermenu'] = $this->items_model->getManufacturerMenu();
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
        
        if(@$_POST['manufacturer']!="")
        $this->data['breadcrumb'] = $this->items_model->getManufacturername(@$_POST['manufacturer']); 
        
        $this->data['breadcrumb2'] = $this->items_model->getsubcategorynames(@$_POST['category']);
        //echo '<pre>';print_r($data['categorymenu']);die;
        $this->data['page_title'] = "The Building & Construction Supply House Marketplace";
        $this->data['page_description'] = "The Building & Construction Supply House Marketplace";
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
    public function item ($url = '')
    {
    	if($url != '')
    	{
        	$url = urldecode($url);
    	}	
        //echo $url;die;
        $this->db->where('url', $url);
        $item = $this->db->get('item')->row();
        
        if(!$item)
            redirect('site');
        $id = $item->id;
        $item->articles = $this->db->where('itemid',$item->id)->order_by('postedon','DESC')->get('itemarticle')->result();
        $item->images = $this->db->where('itemid',$item->id)->get('itemimage')->result();
        $cat_data = $this->db->where('id',$item->category)->get('category')->result();
        if($cat_data)        
        $data['cat_image'] = $this->getCategoryImage($cat_data['0']->id);
        else{
        	$cat_data = $this->db->where('catname','Unassigned')->get('category')->result();
        	if($cat_data)
        	$data['cat_image'] = $this->getCategoryImage($cat_data['0']->id);
        }
        if(isset($cat_data['0']->title) && $cat_data['0']->title!=""){
        	$data['cat_title'] = $cat_data['0']->title;
        	$data['cat_text'] = $cat_data['0']->text;
        }else {
        	$parentcategories = $this->items_model->getParentids($item->category);
        	if($parentcategories){
				$where = "";
        		$str = explode(',', $parentcategories);
        		for($i=count($str)-1;$i>=0;$i--){
        			$cat_data2 = $this->db->where('id',$str[$i])->get('category')->result();
        			if($cat_data2['0']->title!=""){
        				$data['cat_title'] = $cat_data2['0']->title;
        				$data['cat_text'] = $cat_data2['0']->text;
        				break;
        			}
        		}
        	}else {
        		$data['cat_title'] = "";
        		$data['cat_text'] = "";
        	}
        }
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
        /*$this->db->where('itemid', $id);
        $this->db->where('type', 'Supplier');
        $this->db->where('ea >', 0);
        $inventory = $this->db->get('companyitem')->result();
        */
        $sql="SELECT ci.* from ".$this->db->dbprefix('companyitem')." ci LEFT JOIN  ".$this->db->dbprefix('company')." c ON ci.company=c.id where ci.itemid='{$id}' AND ci.type='Supplier' AND (ci.ea > '0' OR ci.price=1) AND c.company_type='1' AND isdeleted=0";
        $inventory = $this->db->query($sql)->result();
        $data['amazon'] = $this->items_model->get_amazon($id);
        if($item->featuredsupplier)
        {
            $this->db->where('itemid', $id);
            $this->db->where('company', $item->featuredsupplier);
            $this->db->where('type', 'Supplier');
            //$this->db->where('ea >', 0);
            $wherecnd = '(ea > 0 OR price=1)';
       		$this->db->where($wherecnd);
            $item->featureditem = $this->db->get('companyitem')->row();
            if($item->featureditem)
            {
                $this->db->where('id',$item->featuredsupplier);
                $item->featuredsupplierdetails = $this->db->get('company')->row();
                                
                $ans = $this->db->get_where('dealitem',array('itemid'=>$id,'company'=>$item->featuredsupplier))->row();
                if(@$ans && $ans != '')
                {
                	$dealitems1 = $this->db->get_where('dealitem',array('itemid'=>$id,'company'=>$item->featuredsupplier))->row()->qtyreqd;             
                
               		if(@$dealitems1 && $dealitems1!="")
	                {
	                	$item->featureditem->deal=$dealitems1;
	                }
	            }	    
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
             $initem->hasdiscount = $this->db
                            ->where('company',$initem->company)
                            ->where('itemid',$initem->itemid)
                            ->get('qtydiscount')
                            ->result();
                                
              $dealitems2 = $this->db->get_where('dealitem',array('itemid'=>$initem->itemid,'company'=>$initem->company))->row('qtyreqd');       
                 
                if(@$dealitems2 && @$dealitems2!="")
	                {
	                	$initem->deal=$dealitems2;
	                }
	                
          /*  if($hasdiscount){
              $initem->hasdiscount = true;}
            else{
             $initem->hasdiscount = false;}*/
            $this->db->where('id',$initem->itemid);
            $orgitem = $this->db->get('item')->row();
            if(!$initem->itemname)
                $initem->itemname = $orgitem->itemname;
            if(!$initem->itemcode)
                $initem->itemcode = $orgitem->itemcode;
                $initem->increment = $orgitem->increment;         
                
            $initem->unit = $orgitem->unit;
            $this->db->where('id',$initem->manufacturer);
            $initem->manufacturername = @$this->db->get('type')->row()->title;
            $initem->listprice = $initem->ea;
            $initem->joinstatus = '';
            $initem->dist = '';
            $this->db->where('id', $initem->company);
            $this->db->where('company_type', '1');
           // $this->db->where('isdeleted', '0');
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
                
                $resultprice = $this->db->select('p.price')->from('purchasingtier_item p')->join('company c','p.company=c.id')->where('p.purchasingadmin', $currentpa)->where('p.itemid', $initem->itemid)->where('c.isdeleted', 0)->where('p.company', $initem->company)->get()->row();
                if($resultprice){
						
                	$initem->ea = number_format($resultprice->price, 2);	
                	
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
        /*$dealitems = $this->db
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
                    ->result();*/
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
                $dealitems3 = $this->db->get_where('dealitem',array('itemid'=>$di->itemid,'company'=>$di->company))->row()->qtyreqd;               
                if(@$dealitems3 && $dealitems3!="")
	                {
	                	$di->deal=$dealitems3;
	                }
                $di->url = $orgitem->url;
                $di->itemcode = $orgitem->itemcode;
                $di->itemname = $orgitem->itemname;
                $di->increment = $orgitem->increment;
                $di->unit = $orgitem->unit;
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
        //Add for current Item
        $this->db->where("itemid",$item->id);
        $data['adforitem'] = $this->db->get("ads")->result();
        $data['breadcrumb'] = $this->items_model->getParents($item->category);
        $data['categorymenu'] = $this->data['categorymenu'] = $this->items_model->getCategoryMenu();
		$data['manufacturermenu'] = $this->items_model->getManufacturerMenu();		
        if(@$_POST['manufacturer']!="")
        $data['breadcrumb'] = $this->items_model->getManufacturername(@$_POST['manufacturer']); 
		
	    //echo '<pre>'; print_r($data['relateditems']);die;
	    $data['page_title'] = $data["item"]->itemname;
	    
	   
	    if($item->searchquery!="")
	    {
	    $data['searchquery'] = 'http://supplyspy.net/'.$item->searchquery;
	    }
	    else
	     {
	    $data['searchquery'] = 'http://supplyspy.net';
	     }
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
    		$hasdiscount = $this->db
                            ->where('itemid',$item->id)
                            ->get('qtydiscount')
                            ->result();
            if($hasdiscount)
              $item->hasdiscount = true;
            else
             $item->hasdiscount = false;
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
        $data['page_title'] = "Item Articles";
        $this->load->view('site/article',$data);
    }
    public function sendrequest($id)
    {
        if(!$_POST)
            die;
	    $settings = (array)$this->homemodel->getconfigurations ();
	    $data['email_body_title'] = "";
	    $data['email_body_content'] = "";
	    if(strpos(@$_POST['redirect'], 'supplier') === 0)
	    {
	        $supplier = $this->db->where('id',$id)->get('company')->row();
	        $to = $supplier->primaryemail;
		    $data['email_body_title'] = 'You have a new request for assistance.';
	    }
	    else
	    {
	        $to = $settings['adminemail'];
	        if(@$_POST['redirect'])
		    $data['email_body_title'] = 'You have a new request for assistance regarding '.site_url($_POST['redirect']).'.';
		    else
		    $data['email_body_title'] = 'You have a new request for assistance.';
	    }
		$data['email_body_content'] .= ' Details are:<br/><br/>';
		//$body .= "Type: ".$_POST['type']."<br/>";
		if(@$_POST['contactName'])
		$data['email_body_content'] .= "Name: ".$_POST['contactName']."<br/>";
		if(@$_POST['name'])
		$data['email_body_content'] .= "Name: ".$_POST['name']."<br/>";
		if(@$_POST['email'])
		$data['email_body_content'] .= "Email: ".$_POST['email']."<br/>";
		if(@$_POST['subject'])
		$data['email_body_content'] .= "Subject: ".$_POST['subject']."<br/>";
		if(@$_POST['comments'])
		$data['email_body_content'] .= "Details: ".$_POST['comments']."<br/>";
		if(@$_POST['hiddenad'])
		$data['email_body_content'] .= "Ad Url: ".site_url('site/ad/' .$_POST['hiddenad'])."<br/>";
		if(@$_POST['phone'])
		$data['email_body_content'] .= "Phone: ".$_POST['phone']."<br/>";
		//$body .= "Phone: ".$_POST['phone']."<br/>";
		if(@$_POST['type']) {
			if($_POST['type'] == 'Request Phone Assistance')
			{
				if(@$_POST['daytd'])
				$data['email_body_content'] .= "Best day to call: ".$_POST['daytd']."<br/>";
				if(@$_POST['timetd'])
				$data['email_body_content'] .= "Best time to call: ".$_POST['timetd']."<br/>";
			}else {
				if(@$_POST['daytd'])
				$data['email_body_content'] .= "Appointment date: ".$_POST['daytd']."<br/>";
				if(@$_POST['timetd'])
				$data['email_body_content'] .= "Appointment time: ".$_POST['timetd']."<br/>";
			}
		}
		if(@$_POST['regarding'])
		$data['email_body_content'] .= "Regarding: ".$_POST['regarding']."<br/>";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($settings['adminemail']);
		$this->email->to($to);
		$this->email->subject('Request for assistance');
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->send();
        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Email was sent successfully</div></div>');
        if(isset($_POST['hiddenad']))
        $adid = $_POST['hiddenad'];
		if(@$_POST['redirect'])
	    redirect('site/'.$_POST['redirect']);
	    else
	    redirect('site/ad/'.$adid);
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
        
        if(isset($_POST['incrementqty']))
        	unset($_POST['incrementqty']);
        
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
        public function classified()
    {
    	$data['a_title'] = "Classified area";
    	$sql_cat = "SELECT * FROM ".$this->db->dbprefix('category')." WHERE id IN (SELECt category FROM ".$this->db->dbprefix('ads')." a JOIN ".$this->db->dbprefix('company')." c ON c.id=a.user_id GROUP BY category)";
    	$categories = $this->db->query($sql_cat)->result_array();
    	
    	$res = array();
    	foreach($categories as $cat){
    		  $sql_ad = "SELECT * FROM ".$this->db->dbprefix('ads')." WHERE category=".$cat['id'];
    		  $res[$cat['catname']] = $this->db->query($sql_ad)->result_array();
    		 
    	} 
    	$data['ads'] = $res;
     
    	
		/*====*/
		$catcodes = $this->catcode_model->get_categories_tiered();
     	$itemcodes = $this->itemcode_model->get_itemcodes();
        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $data['categories'] = $categories;
        $data['items'] = $itemcodes;
		$data['viewallads'] = 0;
		
		foreach($data['ads'] as $keycat=>$ad) 
		   {
			  foreach($ad as $keyad=>$ad_item)
			    { 	      
		   	     $data['images']=$this->db->get_where('AdImage',array('adid'=>$ad_item['id']))->row();
		   	     if(@$data['images']->image) {
		   		 $data['ads'][$keycat][$keyad]['image']=$data['images']->image;}
			    		      
			  	   if($ad_item['latitude']!="" && $ad_item['longitude']!="") 
			  	     { 
			  	     	$adi=0;
						foreach($data['ads'] as $ad2) 
						{
						   foreach($ad2 as $ad_item2)
						     {					     	
								if( ($ad_item['latitude'] == $ad_item2['latitude']))
								 {
									$adi++;
								 }
								if($adi>1)
								 {
									$a = 360.0 / 100;
									$data['ads'][$keycat][$keyad]['latitude'] =  $ad_item['latitude'] + -.00004 * cos((+$a) / 180 * PI());  //x
									$data['ads'][$keycat][$keyad]['longitude'] = $ad_item['longitude'] + -.00004 * sin((+$a) / 180 * PI());  //Y
								  } 
						     } 
						}
			  	      } 
			    }
		    }
    	$this->load->view('site/classified', $data);
    }
        public function viewallads($catid){
    	$cat = array();
    	$cat['catname'] = "";
    	$data['a_title'] = "Classified area";
    	$str = "";
    	$where = "";
    	$subcategories = $this->items_model->getSubCategores($catid);
    	if($subcategories){
    		$str .= implode(',', $subcategories);
    		$where .= " AND category in (".$str.")";
    	}else
    	$where .= "AND category = {$catid}";
    	//$sql_ad = "SELECT * FROM ".$this->db->dbprefix('ads')." WHERE 1=1 {$where}";
    	$sql_ad = "SELECT a.* FROM ".$this->db->dbprefix('ads')." a JOIN ".$this->db->dbprefix('company')." c ON c.id=a.user_id WHERE 1=1 {$where}";
    	$res[$cat['catname']] = $this->db->query($sql_ad)->result_array();
    	$data['ads'] = $res;
		/*====*/
		$catcodes = $this->catcode_model->get_categories_tiered();
     	$itemcodes = $this->itemcode_model->get_itemcodes();
        $categories = array();
        if ($catcodes)
        {
            if (isset($catcodes[0]))
            {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        if(isset($catid) && $catid!="")
		$data['category'] = $catid;
        $data['categories'] = $categories;
        $data['items'] = $itemcodes;
		$data['viewallads'] = 1;
		/*===============*/
    	$this->load->view('site/classified', $data);
    }
    
    public function searchads()
    {
		 
    	$data['a_title'] = "Classified area";
    	$where = "";
    	$wherecat = "";
    	$str = "";
    	$cat = array();
    	$cat['catname'] = "";
    	if(isset($_POST['category']) && $_POST['category']!="") {
    		if(isset($_POST['items']) && $_POST['items']!=""){
    			$wherecat .= "AND ID = {$_POST['category']}";
    		}else {
    			$subcategories = $this->items_model->getSubCategores($_POST['category']);
    			if($subcategories){
    				$str .= implode(',', $subcategories);
    				$wherecat .= " AND ID in (".$str.")";
    			}else
    			$wherecat .= "AND ID = {$_POST['category']}";
    		}
    	}
    	$sql_cat = "SELECT * FROM ".$this->db->dbprefix('category')." WHERE 1=1 {$wherecat}";
    	$categories = $this->db->query($sql_cat)->result_array();
    	if(isset($_POST['s']) && $_POST['s']!="")
		$where .= " AND title like '%{$_POST['s']}%'";
    	if(isset($_POST['items']) && $_POST['items']!="")
    	$where .= " AND itemid = {$_POST['items']}";
    	$res = array();
    	foreach($categories as $cat)
    	{
    		$sql_ad = "SELECT a.* FROM ".$this->db->dbprefix('ads')." a JOIN 
    		".$this->db->dbprefix('company')." c ON c.id=a.user_id 
    		where a.category=".$cat['id']."";
    		$result = $this->db->query($sql_ad)->result_array();
    		 
    		if($result)
    		{
    			$res[$cat['catname']] =$result;
    			$i=0;
    			foreach ($result as $re)
    			{
    				$image=$this->db->get_where('AdImage',array('adid'=>$re['id']))->row()->image;
    				if($image!="")
    				{
    				$res[$cat['catname']][$i]['image'] = $image;
    				}
    				else 
    				{
    				$res[$cat['catname']][$i]['image'] = "";	
    				}
    				
    				$i++;				
   			    }   			
    		}  		
    	} 
		if(count($res)>0)
    	$data['ads'] = $res;
    	if(isset($_POST['category']) && $_POST['category']!="")
		$data['category'] = $_POST['category'];
		if(isset($_POST['items']) && $_POST['items']!="")
        $data['itemids'] = $_POST['items'];
        if(isset($_POST['geo-radius']) && $_POST['geo-radius']!="")
        $data['georadius'] = $_POST['geo-radius'];
        $catcodes = $this->catcode_model->get_categories_tiered();
        $itemcodes = $this->itemcode_model->get_itemcodes();
        $categories = array();
        if ($catcodes)
        {
        	if (isset($catcodes[0]))
        	{
        		build_category_tree($categories, 0, $catcodes);
        	}
        }
        $data['categories'] = $categories;
        $data['items'] = $itemcodes;
		$data['viewallads'] = 0;
		/*===============*/
    	$this->load->view('site/classified', $data);
    }
    
    
    public function ad($id){
    	$sql = "SELECT c.id c_id,c.title c_title,c.address c_address,c.logo c_logo,c.username c_username,a.id a_id,a.title a_title,a.description a_description,a.price a_price,a.priceunit a_priceunit,a.address a_address,a.latitude a_latitude,a.longitude a_longitude,a.published a_published, a.image a_image,a.views a_views,a.tags a_tags,c.phone c_phone,c.primaryemail c_primaryemail,a.category a_category, cat.catname FROM ".$this->db->dbprefix('company')." c left join ".$this->db->dbprefix('ads')." a on a.user_id=c.id left join ".$this->db->dbprefix('category')." cat on a.category = cat.id WHERE a.id=".$id." ";
    	$data = $this->db->query($sql)->row_array();
    	$data['images']=$this->db->get_where('AdImage',array('adid'=>$id))->result(); 
    	 	
    	if(isset($data["a_views"])){
    	$view = $data['a_views']+1;
    	}else 
    	$view = 0;
    	
    	/*if(isset($data["a_image"])){
    	$images = explode("|",$data["a_image"]);
    	foreach($images as $image){
    		$data['images'][]=$image;
    	}
    	}*/
    	if(isset($data['images'][0]->image))
    	$data['featured_image'] = $data['images'][0]->image;
    	else
    	$data['featured_image'] = '';
    	$sql_rel ="SELECT * FROM ".$this->db->dbprefix('ads')." WHERE category=(SELECT category FROM ".$this->db->dbprefix('ads')." WHERE id=".$id.") AND id<>".$id;
    	$rel= $this->db->query($sql_rel)->result_array();
    	
    	$data['related']=array(); 	
    	foreach ($rel as $value) {
    		
    		$image=$this->db->get_where('AdImage',array('adid'=>$value['id']))->row()->image;
     		$value['image']="";
     		if($image!="")
     		{
     			$value['image']=$image;
     		}
     		$data['related'][]=$value;
    	} 
        
    	//$sql_popular = "SELECT * FROM ".$this->db->dbprefix('ads')." ORDER BY views ASC LIMIT 3";
    	$sql_popular = "SELECT a.* FROM ".$this->db->dbprefix('ads')." a JOIN ".$this->db->dbprefix('company')." c ON c.id=a.user_id 
    	WHERE c.isdeleted='0' ORDER BY views ASC LIMIT 3";
    	$pop = $this->db->query($sql_popular)->result_array();
    	$data['popular']=array(); 	
    	foreach ($pop as $value) {
    		
    		$image=$this->db->get_where('AdImage',array('adid'=>$value['id']))->row()->image;
     		$value['image']="";
     		if($image!="")
     		{
     			$value['image']=$image;
     		}
     		$data['popular'][]=$value;
    	} 
    	
    	$this->db->where('id', $id);
    	$this->db->update('ads', array("views"=>$view));
    	$this->load->view('site/ad',$data);
    }
    	// List Items of the selected Categories
  	 function get_items($categoryId){
		// $this->load->model('items_model');
		 header('Content-Type: application/x-json; charset=utf-8');
		 echo(json_encode($this->items_model->get_items2($categoryId)));
	}
    function formview($id)
    {
    	$data['result'] = $this->form_model->view_field($id);
        $formdata="";
    	foreach($data['result'] as $key=>$fields)
    	 {
    	 	$name_id=trim($fields->Label);
    	    $formdata.='<h4>'.$fields->Label.'</h4>';
    	     if($fields->FieldType == 'text' || $fields->FieldType == 'email' || $fields->FieldType == 'password')
    	      {
    	      $formdata.='<input type="'.$fields->FieldType.'" class="required input-block-level" id="'.$name_id.'" name="formfields['.$fields->Id.']" placeholder="'.$fields->Label.'" required value="'.$fields->Value.'">';
      		  }
      		if($fields->FieldType == 'dropdown')
      		 {
      		   $dropdownValues = explode(",",$fields->FieldValue); $k= array_search($fields->Value,$dropdownValues);
      		   $formdata.='<select id="'.$name_id.'" name="formfields['.$fields->Id.']">';
      		     if(count($dropdownValues) > 0)
      		      {
      		   	    for($i=0;$i<count($dropdownValues); $i++)
      		   	    {
      		   	      $formdata.='<option value="'.$dropdownValues[$i].'"';
      		   	        if($dropdownValues[$i]==$fields->Value)
      		   	          {"selected";}
      		   	        else
      		   	          { ""; }
      		   	     $formdata.='>'.$dropdownValues[$i].'</option>';
      		   	     }
      		      }
      		   	 $formdata.='</select>';
             }
		     if($fields->FieldType == 'radio')
		      {
		      	$dropdownValues = explode(",",$fields->FieldValue);
		      	  if(count($dropdownValues) > 0)
		      	    {
		      	       for($i=0;$i<count($dropdownValues); $i++)
		      	        {
		     				$formdata.='<input type="radio" name="formfields['.$fields->Id.']" id="'.$dropdownValues[$i].'" value="'.$dropdownValues[$i].'"';
		                    if($fields->Value ==$dropdownValues[$i])  'checked';
		                    $formdata.='>&nbsp;'.$dropdownValues[$i].'<br>';
		                 }
		      	    }
		      }
 			  if($fields->FieldType == 'checkbox')
 			    {
 			      $dropdownValues = explode(",",$fields->FieldValue);
 			       if(count($dropdownValues) > 0)
 			        {
 			          for($i=0;$i<count($dropdownValues); $i++)
 			           {
 			  	        $formdata.='<input type="checkbox" name="formfields['.$fields->Id.'][]" id="'.$name_id.'"  value="'.$dropdownValues[$i].'"';
 			  	        if($fields->Value ==$dropdownValues[$i]) 'checked';
 			  	        $formdata.='>&nbsp;'.$dropdownValues[$i].'&nbsp;&nbsp;';
 			  	       }
 			        }
 			     }
 			 if($fields->FieldType == 'textarea')
 			  {
 				$formdata.='<textarea id="'.$name_id.'" name="formfields['.$fields->Id.']">'.$fields->Value.'</textarea>';
 			  }
    	 }
    	 print_r($formdata);
    }
                function getpriceqtydetails(){
    	if(!@$_POST)
    	{
    		die;
    	}
    	if(!@$_POST['itemid'])
    	{
    		die;
    	}
		$purchasingadmin = @$this->session->userdata('site_loggedin')->id;
    	$this->db->where('company',$_POST['companyid']);
    	$this->db->where('itemid',$_POST['itemid']);
    	$qtyresult = $this->db->get('qtydiscount')->result();
    	if($qtyresult){
    		$strput = "";
    		$i=0;
    		foreach($qtyresult as $qtyres){
    			if($purchasingadmin){
    				$sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$purchasingadmin' AND company='" . $_POST['companyid'] . "'";
    				$sqltier = "select tierprice from " . $this->db->dbprefix('companyitem') . "
				    where itemid='".$_POST['itemid']."' AND company='" . $_POST['companyid'] . "' AND type = 'Supplier'";
    				$istierprice = $this->db->query($sqltier)->row();
    				if($istierprice){
    					$istier = $istierprice->tierprice;
    				}else
    				$istier = 0;
    				$tier = $this->db->query($sql)->row();
    				if ($tier && $istier)
    				{
    					$tier = $tier->tier;
    					$this->db->where('company', $_POST['companyid']);
    					$pt = $this->db->get('tierpricing')->row();
    					if ($pt)
    					{
    						$deviation = $pt->$tier;
    						$qtyres->price = $qtyres->price + ($qtyres->price * $deviation / 100);
    						$qtyres->price = number_format($qtyres->price, 2);
    					}
    				}
    			}
    			if($i==0){
    				$strput .= '<div >
							 <div style="padding-bottom:9px;" class="col-md-8">1 - '.($qtyres->qty-1).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: $'.$_POST['price'].'</div>
          				  </div>';
    			}
    			$strput .= '<div >
							 <div style="padding-bottom:9px;" class="col-md-8">'.$qtyres->qty.' or more: $'.$qtyres->price.'</div>
          				  </div>';
    			$i++;
    		}
    		echo $strput;
    	}
    }
    function getpriceperqtydetails(){
    	if(!@$_POST)
    	{
    		die;
    	}
    	if(!@$_POST['itemid'])
    	{
    		die;
    	}
    	$sql1 = "SELECT * FROM ".$this->db->dbprefix('qtydiscount')." WHERE company = '{$_POST['companyid']}' and itemid = '{$_POST['itemid']}' and qty <= '{$_POST['qty']}' order by qty desc limit 1";
    	$result1 = $this->db->query($sql1)->row();
    	if($result1){
    		$purchasingadmin = @$this->session->userdata('site_loggedin')->id;
    		if($purchasingadmin){
    			$sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$purchasingadmin' AND company='" . $_POST['companyid'] . "'";
    			$sqltier = "select tierprice from " . $this->db->dbprefix('companyitem') . "
				    where itemid='".$_POST['itemid']."' AND company='" . $_POST['companyid'] . "' AND type = 'Supplier'";
    			$istierprice = $this->db->query($sqltier)->row();
    			if($istierprice){
    				$istier = $istierprice->tierprice;
    			}else
    			$istier = 0;
    			$tier = $this->db->query($sql)->row();
    			if ($tier && $istier)
    			{
    				$tier = $tier->tier;
    				$this->db->where('company', $_POST['companyid']);
    				$pt = $this->db->get('tierpricing')->row();
    				if ($pt)
    				{
    					$deviation = $pt->$tier;
    					$result1->price = $result1->price + ($result1->price * $deviation / 100);
    					$result1->price = number_format($result1->price, 2);
    				}
    			}
    			
    			$resultprice = $this->db->select('p.price')->from('purchasingtier_item p')->join('company c','p.company=c.id')->where('p.purchasingadmin', $purchasingadmin)->where('p.itemid', $_POST['itemid'])->where('c.isdeleted', 0)->where('p.company', $_POST['companyid'])->get()->row();
                if($resultprice){
						
                	$result1->price = number_format($resultprice->price, 2);	
                	
                }
    			
    		}
    		$strput = "";
    		$strput .= '<div>
							 <div style="padding-bottom:9px;" class="col-md-8">Total Price Estimation:&nbsp; $'.($_POST['qty']*$result1->price).'</div>
							 <div class="col-md-4"><span>You Save: &nbsp; $'.( ($_POST['qty']*$_POST['price']) - ($_POST['qty']*$result1->price)).'</span></div>
          				  </div>';
    		echo $strput;
    	}else{
    		$strput = "";
    		$strput .= '<div >
							 <div style="padding-bottom:9px;" class="col-md-8">Total Price Estimation:&nbsp; $'.($_POST['qty']*$_POST['price']).'</div>
							 <!-- <div class="col-md-4"><span>You Save: &nbsp; $'.( ($_POST['qty']*$_POST['price']) - ($_POST['qty']*$_POST['price'])).'</span></div> -->
          				  </div>';
    		echo $strput;
    	}
    }
    function getnewprice(){
    	if(!@$_POST)
    	{
    		die;
    	}
    	if(!@$_POST['itemid'])
    	{
    		die;
    	}
    	$sql1 = "SELECT * FROM ".$this->db->dbprefix('qtydiscount')." WHERE company = '{$_POST['companyid']}' and itemid = '{$_POST['itemid']}' and qty <= '{$_POST['qty']}' order by qty desc limit 1";
    	$result1 = $this->db->query($sql1)->row();
    	if($result1){
    		$purchasingadmin = @$this->session->userdata('site_loggedin')->id;
    		if($purchasingadmin){
    			$sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$purchasingadmin' AND company='" . $_POST['companyid'] . "'";
    			$sqltier = "select tierprice from " . $this->db->dbprefix('companyitem') . "
				    where itemid='".$_POST['itemid']."' AND company='" . $_POST['companyid'] . "' AND type = 'Supplier'";
    			$istierprice = $this->db->query($sqltier)->row();
    			if($istierprice){
    				$istier = $istierprice->tierprice;
    			}else
    			$istier = 0;
    			$tier = $this->db->query($sql)->row();
    			if ($tier && $istier)
    			{
    				$tier = $tier->tier;
    				$this->db->where('company', $_POST['companyid']);
    				$pt = $this->db->get('tierpricing')->row();
    				if ($pt)
    				{
    					$deviation = $pt->$tier;
    					$result1->price = $result1->price + ($result1->price * $deviation / 100);
    					$result1->price = number_format($result1->price, 2);
    				}
    			}
    			
    			$resultprice = $this->db->select('p.price')->from('purchasingtier_item p')->join('company c','p.company=c.id')->where('p.purchasingadmin', $purchasingadmin)->where('p.itemid', $_POST['itemid'])->where('c.isdeleted', 0)->where('p.company', $_POST['companyid'])->get()->row();
                if($resultprice){
						
                	$result1->price = number_format($resultprice->price, 2);	
                	
                }
    			
    			
    		}
			echo $result1->price;
    	}else{
    		echo "norecord";
    	}die;
    }
    function sayhello()
	{
		$_POST['inputName'];
		$_POST['inputEmail'];
		$_POST['inputMessage'];
		if (empty($_POST["inputName"]))
		{
			$Err = "Name is required";
		}
		if (empty($_POST["inputEmail"]))
		{
			$Err = "Email is required";
		}
		else
		{
			$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
			if (!preg_match($regex,$_POST["inputEmail"]))
			{
				$Err ="Invalid email. Please try again.";
			}
		}
		if (empty($_POST["inputMessage"]))
		{
			$Err = "Message is required";
		}
		if(isset($Err))
		{
			$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">'.$Err.'</div></div>');
			redirect('site');
		}
		if(isset($_POST["inputName"]) && isset($_POST["inputEmail"]) && isset($_POST["inputMessage"]))
		{
			$mail="info@MaterialKing.com";
			$this->load->library('email');
			$this->email->clear(true);
			$this->email->from($_POST["inputEmail"], $_POST["inputName"]);
			$this->email->to($mail);
			$body = "Dear Administrator,<br><br> Following User Has Send You Message.<br><p><strong>Name :</strong> ".$_POST["inputName"]."</p><p><strong>Email : </strong>".$_POST["inputEmail"]."</p><p><strong>Message : </strong></p>". $_POST["inputMessage"] . "<br>";
			$this->email->subject("Say Hello!");
			$this->email->message($body);
			$this->email->set_mailtype("html");
			$this->email->send();
			$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Email was sent successfully</div></div>');
			redirect('site');
		}
		redirect('site');
	}
	 public function savetag(){
	 	$id = $_POST['pic_id'];
	 	$name = $_POST['name'];
	 	$description = $_POST['description'];
	 	$pic_x = $_POST['pic_x'];
	 	$pic_y = $_POST['pic_y'];
    	$datatoadd = array('pic_id' => $_POST['pic_id'],'name' => $_POST['name'], 'description' => $description, 'pic_x' => $_POST['pic_x'], 'pic_y' => $_POST['pic_y'],  'pic_xactual' => $_POST['pic_xactual'],);
    	$this->db->insert('image_tag',$datatoadd);
    	echo $lastid = $this->db->insert_id();
    }
    function taglist()
    {
    	$checkauth = array('pic_id' => $_POST[ 'pic_id' ]);
    	$this->db->where($checkauth);
    	$result = $this->db->get('image_tag')->result();
    	$data['boxes'] = '';
    	$data['lists'] = '';
		$margin = 0;
    	if ($result){
    		foreach ($result as $rs){
    			$data['boxes'] .= '<div class="tagview tp_circle" style="left:' . $rs->pic_x . 'px;top:' . $rs->pic_y . 'px;" id="view_'.$rs->id.'">';
    			$data['boxes'] .= '<div class="square" id="view_'.$rs->id.'_" style="opacity:0;width:100px;height:10px;">';
    			$data['boxes'] .= '<div style="margin-top:20px;" class="person">' . $rs->name . '</div></div></div>';
    			//$data['boxes'] .= '<img src="' . base_url() . 'uploads/logo/thumbs/big.png"></div>';
    			$itemdata = "";
    			if($rs->itemid!=""){
    				$this->db->where('id',$rs->itemid);
        			$item = $this->db->get('item')->row();
    				$this->db->where('itemid',$rs->itemid);
    				$this->db->where('type','Supplier');
    				$this->db->where('company',$this->session->userdata('company')->id);
    				$companyitem = $this->db->get('companyitem')->row();
    				//print_r($companyitem);
    				if($companyitem)
    				{
    					$item->ea = $companyitem->ea;
    					if($companyitem->itemname!="")
    					$item->itemname = $companyitem->itemname;
    					if($companyitem->itemcode!="")
    					$item->itemcode = $companyitem->itemcode;
    				}
    				$itemdata .= '<table  class="table table-bordered" id="tableinnerid" style="background-color:white;"><tr><td id="'.$rs->id.'" ><strong><a>'.$rs->name.'</a></strong>(<a class="remove">Remove</a>)</td></tr><tr><td>Item Code:&nbsp;'.$item->itemcode.'</td></tr><tr><td>Item Name:&nbsp;'.$item->itemname.'</td></tr><tr><td>Price:&nbsp;$'.$item->ea.'</td></tr><tr><td><a target="blank" href="'.base_url().'site/item/'.$item->url.'">View Item</a></td></tr>';
    				
    				if($_POST['view']=='profile'){
    				
    				if ($this->session->userdata('site_loggedin')){
                            $itemdata .= '<tr><td><a class="btn btn-primary btn-sm"  href="javascript:void(0)" onclick="addtopo('.$item->id.','.$item->increment.')"> <i class="icon icon-plus"></i> <br/>Add to RFQ</a></td></tr>';
                    }else{
                            $itemdata .= '<tr><td><a class="btn btn-primary btn-sm"  href="javascript:void(0)" onclick="openrfqpopup();"> <i class="icon icon-plus"></i> <br/>Add to RFQ</a></td></tr>'; }
    				}
    				
                    $itemdata .='</table><br>';
    				
    				//$data['lists'] .= '<div  style="margin-left: '.$margin.'px;" id="'.$rs->id.'"><a>' . $rs->name . '</a> (<a class="remove">Remove</a>)'.$itemdata.'</div>';
    				$data['lists'] .= '<div>'.$itemdata.'</div>';
			
    			}else
    			$data['lists'] .= '<div style="margin-left: '.$margin.'px;position: absolute;" id="'.$rs->id.'"><a>' . $rs->name . '</a> (<a class="remove">Remove</a>)'.$itemdata.'</div>';
				$margin +=150;
    		}
    	}
    	echo json_encode( $data );
    }
    function taglistsupplier()
    {
    	$checkauth = array('pic_id' => $_POST[ 'pic_id' ]);
    	$this->db->where($checkauth);
    	$result = $this->db->get('image_tag')->result();
    	$data['boxes'] = '';
    	$data['lists'] = '';
		$margin = 0;
    	if ($result){
    		foreach ($result as $rs){
    			$data['boxes'] .= '<div class="tagview tp_circle" style="left:' . $rs->pic_x . 'px;top:' . $rs->pic_y . 'px;" id="view_'.$rs->id.'" > <div onmouseover="viewtagdescription(\'view_'.$rs->id.'\');">&nbsp;</div>';
    			$data['boxes'] .= '<div class="square" id="view_'.$rs->id.'_" style="opacity:0;width:100px;height:10px;">';
    			
    			
    			if($rs->itemid!=""){
    				$this->db->where('id',$rs->itemid);
        			$item = $this->db->get('item')->row();
    				$this->db->where('itemid',$rs->itemid);
    				$this->db->where('type','Supplier');
    				$this->db->where('company',$_POST[ 'company' ]);
    				$companyitem = $this->db->get('companyitem')->row();
    				
    				if($companyitem)
    				{
    					$item->ea = $companyitem->ea;
    					if($companyitem->itemname!="")
    					$item->itemname = $companyitem->itemname;
    					if($companyitem->itemcode!="")
    					$item->itemcode = $companyitem->itemcode;    					
    				}
                    if(isset($item->item_img) && $item->item_img!="" && file_exists("uploads/item/thumbs/".$item->item_img))
                    {
                    	$src=base_url().'uploads/item/thumbs/'.$item->item_img;
                    }
                    else 
                    {
                    	$src=site_url('uploads/item/big.png');
                    }
                                       
    				$itemdata = '<div class="table-responsive"><table class="table" style="background-color:white;width:100%;"><tr><td><img src="'.base_url().'templates/site/assets/img/icons/cross.png" style="position:relative;cursor:pointer;" onclick="hidetagdescription(\'view_'.$rs->id.'\');"/>&nbsp;<strong>'.$rs->name.'</strong></td><td><img src="'.$src.'" height="100px" width="100px"></td></tr><tr><td colspan="2">'.$item->itemcode.'</td></tr><tr><td colspan="2">'.$item->itemname.'</td></tr><tr><td colspan="2">'.$rs->description.'</td></tr><tr><td>$'.$item->ea.'</td><td><a class = "btn btn-green" target="blank" href="'.base_url().'site/item/'.$item->url.'">More Info</a></td></tr></table></div></div></div>';
    				//$itemdata = '<div class="table-responsive"><table class="table" style="background-color:white;"><tr><td style="margin-top:1opx;text-align:left;"><h2>'.$rs->name.'</h2></td><td><img style="width:100px;height:100px;"text-align:right;" src="'.base_url().'uploads/item/thumbs/'.$item->item_img.'"></td></tr><tr><td style="text-align:left;" colspan="2">'.$item->itemcode.'</td></tr><tr><td style="text-align:left;" colspan="2">'.$item->itemname.'</td></tr><tr><td colspan="2" style="text-align:left;">'.$rs->description.'</td></tr><tr><td>&nbsp;</td><td style="text-align:right;">$'.$item->ea.'&nbsp;&nbsp;&nbsp;<a class = "btn btn-green" target="blank" href="'.base_url().'site/item/'.$item->url.'">More Info</a></td></tr></table></div>';
    				//$data['boxes'] .= '<div style="margin-top:20px;width:-moz-fit-content;height:auto;color:black;" class="person">'.$itemdata.'</div></div>';
    				
    				
    				$data['boxes'] .= $itemdata;
    			}else 
    			$data['boxes'] .= '<div style="margin-top:20px;" class="person">' . $rs->name . '</div></div></div>';
    			
    			//$data['boxes'] .= '<img src="' . base_url() . 'uploads/logo/thumbs/big.png"></div>';
    			$itemdata = "";
    			if($rs->itemid!=""){
    				$this->db->where('id',$rs->itemid);
        			$item = $this->db->get('item')->row();
    				$this->db->where('itemid',$rs->itemid);
    				$this->db->where('type','Supplier');
    				$this->db->where('company',$_POST['company']);
    				$companyitem = $this->db->get('companyitem')->row();
    				//print_r($companyitem);
    				if($companyitem)
    				{
    					$item->ea = $companyitem->ea;
    					if($companyitem->itemname!="")
    					$item->itemname = $companyitem->itemname;
    					if($companyitem->itemcode!="")
    					$item->itemcode = $companyitem->itemcode;
    				}
    				if(isset($item->item_img) && $item->item_img!="" && file_exists("uploads/item/thumbs/".$item->item_img))
                    {
                    	$src=base_url().'uploads/item/thumbs/'.$item->item_img;
                    }
                    else 
                    {
                    	$src=site_url('uploads/item/big.png');
                    }
                    
    				 $itemdata = '<div class="table-responsive"><table class="table table-striped"><tr><td rowspan="2"><img src="'.$src.'" height="100px" width="100px"></td><td><strong>'. $rs->name .'</strong></td></tr><tr><td>Item Code:&nbsp;'.$item->itemcode.'</td></tr><tr><td>&nbsp;</td><td>Item Name:&nbsp;'.$item->itemname.'</td></tr><tr><td>&nbsp;</td><td>Price:&nbsp;$'.$item->ea.'</td></tr><tr><td>&nbsp;</td><td><a target="blank" href="'.base_url().'site/item/'.$item->url.'">View Item</a></td></tr>';
    				    				
    				if ($this->session->userdata('site_loggedin')){
                            $itemdata .= '<tr><td><a style="width:100px" class="btn btn-primary btn-xs"  href="javascript:void(0)" onclick="addtopo('.$item->id.','.$item->increment.')"> <i class="icon icon-plus"></i> Add to RFQ</a></td></tr>';
                    }else{
                            $itemdata .= '<tr><td><a style="width:100px" class="btn btn-primary btn-xs"  href="javascript:void(0)" onclick="openrfqpopup();"> <i class="icon icon-plus"></i>Add to RFQ</a></td></tr>'; }
    				
    				
                    $itemdata .='</table></div>';
    				
    				//$data['lists'] .= '<div  style="margin-left: '.$margin.'px;position: absolute;" id="'.$rs->id.'"><a>' . $rs->name . '</a>'.$itemdata.'</div>';
    				$data['lists'] .= '<div>'.$itemdata.'</div>';
    			}else
    			$data['lists'] .= '<div style="margin-left: '.$margin.'px;position: absolute;" id="'.$rs->id.'"><a>' . $rs->name . '</a>'.$itemdata.'</div>';
				$margin +=150;
    		}
    	}
    	echo json_encode( $data );
    }
    
    
    
    function taglistofdetail()
    {
    	$checkauth = array('pic_id' => $_POST[ 'pic_id' ]);
    	$this->db->where($checkauth);
    	$result = $this->db->get('image_tag')->result();
    	$data['boxes'] = '';
    	$data['lists'] = '';
		$margin = 0;
    	if ($result){
    		foreach ($result as $rs){
    			$data['boxes'] .= '<div class="tagview tp_circle" style="left:' . $rs->pic_xactual . 'px;top:' . $rs->pic_y . 'px;" id="view_'.$rs->id.'" >
    			 <div onmouseover="viewtagdescription(\'view_'.$rs->id.'\');">&nbsp;</div>';
    			$data['boxes'] .= '<div class="square" id="view_'.$rs->id.'_" style="opacity:0;width:100px;height:10px;">';
    			
    			
    			if($rs->itemid!="")
    			{
    				$this->db->where('id',$rs->itemid);
        			$item = $this->db->get('item')->row();
    				$this->db->where('itemid',$rs->itemid);
    				$this->db->where('type','Supplier');
    				$this->db->where('company',$_POST[ 'company' ]);
    				$companyitem = $this->db->get('companyitem')->row();
    				
    				if($companyitem)
    				{
    					$item->ea = $companyitem->ea;
    					if($companyitem->itemname!="")
    					$item->itemname = $companyitem->itemname;
    					if($companyitem->itemcode!="")
    					$item->itemcode = $companyitem->itemcode;    					
    				}
    				
                    if(isset($item->item_img) && $item->item_img!="" && file_exists("uploads/item/thumbs/".$item->item_img))
                    {
                    	$src=base_url().'uploads/item/thumbs/'.$item->item_img;
                    }
                    else 
                    {
                    	$src=site_url('uploads/item/big.png');
                    }
                                       
    				$itemdata = '<div class="table-responsive"><table class="table" style="background-color:white;width:100%;"><tr><td><img src="'.base_url().'templates/site/assets/img/icons/cross.png" style="position:relative;cursor:pointer;" onclick="hidetagdescription(\'view_'.$rs->id.'\');"/>&nbsp;<strong>'.$rs->name.'</strong></td><td><img src="'.$src.'" height="100px" width="100px"></td></tr><tr><td colspan="2">'.$item->itemcode.'</td></tr><tr><td colspan="2">'.$item->itemname.'</td></tr><tr><td colspan="2">'.$rs->description.'</td></tr><tr><td>$'.$item->ea.'</td><td><a class = "btn btn-green" target="blank" href="'.base_url().'site/item/'.$item->url.'">More Info</a></td></tr></table></div></div></div>';
    				//$itemdata = '<div class="table-responsive"><table class="table" style="background-color:white;"><tr><td style="margin-top:1opx;text-align:left;"><h2>'.$rs->name.'</h2></td><td><img style="width:100px;height:100px;"text-align:right;" src="'.base_url().'uploads/item/thumbs/'.$item->item_img.'"></td></tr><tr><td style="text-align:left;" colspan="2">'.$item->itemcode.'</td></tr><tr><td style="text-align:left;" colspan="2">'.$item->itemname.'</td></tr><tr><td colspan="2" style="text-align:left;">'.$rs->description.'</td></tr><tr><td>&nbsp;</td><td style="text-align:right;">$'.$item->ea.'&nbsp;&nbsp;&nbsp;<a class = "btn btn-green" target="blank" href="'.base_url().'site/item/'.$item->url.'">More Info</a></td></tr></table></div>';
    				//$data['boxes'] .= '<div style="margin-top:20px;width:-moz-fit-content;height:auto;color:black;" class="person">'.$itemdata.'</div></div>';
    				
    				
    				$data['boxes'] .= $itemdata;
    			}
    			else
    			{ 
    			$data['boxes'] .= '<div style="margin-top:20px;" class="person"><strong>' . $rs->name . '</strong></div></div></div>';
    			}
    			$itemdata = "";
    			if($rs->itemid!="")
    			{
    				$this->db->where('id',$rs->itemid);
        			$item = $this->db->get('item')->row();
    				$this->db->where('itemid',$rs->itemid);
    				$this->db->where('type','Supplier');
    				$this->db->where('company',$_POST['company']);
    				$companyitem = $this->db->get('companyitem')->row();
    				
    				if($companyitem)
    				{
    					$item->ea = $companyitem->ea;
    					if($companyitem->itemname!="")
    					$item->itemname = $companyitem->itemname;
    					if($companyitem->itemcode!="")
    					$item->itemcode = $companyitem->itemcode;
    				}
    				if(isset($item->item_img) && $item->item_img!="" && file_exists("uploads/item/thumbs/".$item->item_img))
                    {
                    	$src=base_url().'uploads/item/thumbs/'.$item->item_img;
                    }
                    else 
                    {
                    	$src=site_url('uploads/item/big.png');
                    }
                    
    				$itemdata = '<table class="table table-bordered;" style="background-color:white;width:98%;"><tr><td><strong>'. $rs->name.'</strong><td><td rowspan="6"><img src="'.$src.'" height="100px" width="120px"></td><tr><tr><td>
    				<strong>Item Code:&nbsp;</strong>'.$item->itemcode.'</td></tr><tr><td><strong>Item Name:&nbsp;</strong>'.$item->itemname.'</td></tr>
    				<tr><td><strong>Price:&nbsp;</strong>$'.$item->ea.'</td></tr><tr><td><a target="blank" href="'.base_url().'site/item/'.$item->url.'">
    				<strong>View Item</strong></a></td></tr>';
    				
    				if ($this->session->userdata('site_loggedin')){
                            $itemdata .= '<tr><td><a class="btn btn-primary btn-xs"  href="javascript:void(0)" onclick="addtopo('.$item->id.','.$item->increment.')"> <i class="icon icon-plus"></i> Add to RFQ</a></td></tr>';
                    }else{
                            $itemdata .= '<tr><td><a class="btn btn-primary btn-xs"  href="javascript:void(0)" onclick="openrfqpopup();"> <i class="icon icon-plus"></i>Add to RFQ</a></td></tr>'; }
    				
    				
                    $itemdata .='</table>';
    				
    				$data['lists'] .= '<div style="float:left;">'.$itemdata.'</div>&nbsp;';
    				//$data['lists'] .= '<div  style="margin-left: '.$margin.'px;position: absolute;" id="'.$rs->id.'"><a>' . $rs->name . '</a>'.$itemdata.'</div>';
    			}else
    			//$data['lists'] .= '<div style="float:left;margin-left: '.$margin.'px;position: absolute;" id="'.$rs->id.'"><a>' . $rs->name . '</a>'.$itemdata.'</div>';
    			$data['lists'] .= '<div style="float:left;padding-left:5px;" id="'.$rs->id.'"><a>' . $rs->name . '</a>'.$itemdata.'</div>';
				$margin +=150;
    		}
    	}
    	echo json_encode( $data );
    }
    
    
    function removetag(){
    	$id = $_POST['id'];
    	$where = array('id'=>$id);
    	$query = $this->db->delete('image_tag',$where);
    }
    function designbook()
    {
		$this->items_model->set_keyword(false);
        $items = $this->items_model->find_design_item();
        $this->data['totalcount'] = $items->totalresult;
    	$limit = 18;
    	if(@$_POST['pagenum'])
        $this->data['currentpage'] = $_POST['pagenum'] + 1;
        else
        $this->data['currentpage'] = 1;
        $this->data['totalpages'] = ceil($this->data['totalcount'] / $limit);
        $this->data['submiturl'] = 'site/designbook';
        $this->data['submitmethod'] = 'POST';
        $this->data['pagingfields'] = $_POST;
        $this->data['page_titile'] = "Design Book";
        
        $this->data['items'] = array();
        $items = $items->items;
       
        foreach ($items as $item)
        {
           $company=$this->db->get_where('company',array('id'=>$item->company,'isdeleted'=>'0'))->row();
           $item->supplier=$company->title;
           $catnamefor=$this->db->get_where('designcategory',array('id'=>$item->categoryid))->row();
           $item->catnamefor=@$catnamefor->catname;
           $this->data['items'][] = $item;
        }      
        $this->data['items'] =$items;
        $this->data['norecords'] = '';
        
        
        
        if (! $this->data['items'])
        {
            $this->data['norecords'] = 'No Records found for the search.';
        }
           
        $this->data['categories'] = $this->itemcode_model->getdesigncategories();
      
        if(isset($_POST['category']))
        $category = $_POST['category'];
        else
        $category = "";
         
        $this->data['categoriesoptions'] = $this->items_model->getDesignTreeOptions($category);
        if($category){
        	$sql1 = "SELECT * FROM ".$this->db->dbprefix('designcategory')." WHERE id = '{$_POST['category']}' ORDER BY catname ASC";
        	$result1 = $this->db->query($sql1)->result();
        	if($result1)
        	$this->data['catname'] = $result1[0]->catname;
  	    }
        $this->data['subcategories'] = array();
        $this->data['categorymenu'] = $this->items_model->getDesignCategoryMenu();
        $this->data['breadcrumb'] = @$_POST['breadcrumb'];
        $this->data['breadcrumb'] = $this->items_model->getDesignParents(@$_POST['category']);
        $this->data['breadcrumb2'] = $this->items_model->getdesignsubcategorynames(@$_POST['category']);
    	$this->load->view('site/designbook',$this->data);
    }
    
    function saveitemtotag(){
    	$id = $_POST['id'];
	 	$itemid = $_POST['itemid'];
    	$datatoadd = array('itemid' => $_POST['itemid']);
    	$this->db->where('id', $_POST['id']);
    	$this->db->update('image_tag',$datatoadd);
    }
    function populatetags()
	{
		$checkauth = array('pic_id' => $_POST[ 'pic_id' ]);
    	$this->db->where($checkauth);
    	$result = $this->db->get('image_tag')->result();
		echo json_encode($result);
	}
	function designbookdetail($id)
    {
    	$sql = "SELECT  d.*  FROM ".$this->db->dbprefix('designbook')." d  where d.id=".$id."";
    	
		$data['details'] = $this->db->query($sql)->row();
		$cid=$data['details']->company;
	
		$sql11 = "SELECT  *  FROM ".$this->db->dbprefix('company')." where isdeleted = 0 AND  id=".$cid."";		
		$data['supplier']=$this->db->query($sql11)->row();
		
		$sql = "SELECT  d.id  FROM ".$this->db->dbprefix('designbook')." d LEFT JOIN ".$this->db->dbprefix('company')." c ON c.id = d.company  where d.id <".$id." AND c.isdeleted = 0 order by d.id desc";
		$data['previd'] = $this->db->query($sql)->row();
    	
		$sql = "SELECT  d.id  FROM ".$this->db->dbprefix('designbook')." d LEFT JOIN ".$this->db->dbprefix('company')." c ON c.id = d.company  where d.id >".$id." AND c.isdeleted = 0 ";
		$data['nextid'] = $this->db->query($sql)->row();
		
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
            $data['projects'] = $this->db->where('purchasingadmin',$pa)->get('project')->result();
            $data['costcodes'] = $this->db->where('purchasingadmin',$pa)->get('costcode')->result();
        }
		$data['page_title']=$data['details']->name;
    	$data['page_description']=$data['details']->name;
    	$this->load->view('site/designbookdetail',$data);
    }
	public function price_check ()
    {
       $data = ""; 
       $this->load->view('site/priceCheck', $data);
    }
    public function submitlist ()
    {
    	$data = "";
    	$this->load->view('site/submitlist', $data);
    }   
    
	public function unknown_user($key)
	{		
		$invitation = $this->quotemodel->getinvitation($key);
		if(!$invitation)
		{
			$message = 'Wrong Access.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('site');
		}
		
		$quote = $this->quotemodel->getquotebyid($invitation->quote);		
		if($quote)
		{
		$data['project']=$this->db->get_where('project',array('id'=>$quote->pid))->row();
		}
		if($this->quotemodel->checkbidcomplete($quote->id))
		{
			$message = 'Bid Already Completed, Thank You.';
			$this->session->set_flashdata('message', '<div class="errordiv"><div class="alert alert-info"><button data-dismiss="alert" class="close"></button><div class="msgBox">'.$message.'</div></div></div>');
			redirect('site');
		}
				
		$quoteitems = $this->quotemodel->getquoteitems($quote->id);		
		$originalitems1 = $this->quotemodel->getquoteitems($quote->id);	
		foreach($originalitems1 as $q)
		{
			$originalitems[$q->itemid] = $q;
		}
		$data['originalitems'] = $originalitems;	
		$data['invitation'] = $key;
		$data['quote'] = $quote;    
		$items = $quoteitems;
		$data['quoteitems'] = array();
		foreach($items as $item)
		{					
			$orgitem = $this->db->where('id',$item->itemid)->get('item')->row();			
			$item->itemcode = $orgitem->itemcode;
		    $item->itemname = $orgitem->itemname;			
			$price = $orgitem->ea;			
			$data['quoteitems'][]=$item;
		}		
		$data['invitekey'] = $key;
		$data['invid']=$invitation->id;
		$this->db->where('id',$invitation->purchasingadmin);
		$pa = $this->db->get('users')->row();
		if($pa)
		$data['purchasingadmin'] = $pa;	
		$this->load->view('site/unknownuser',$data);
	}
	function customerbill()
	{		
		$this->load->model('admin/settings_model');
		
		$customer = $this->session->userdata('customer');
		$data['settings'] = $this->settings_model->get_current_settings();
		
		$billid = '';
	
		if(!$customer)
			redirect('company/customerlogin');
			
		$this->db->where('customerid',$this->session->userdata('customer')->id);
		$this->db->get('bill')->row();
		$sql1 = "SELECT b.*,c.*,b.id as billid 
				FROM ".$this->db->dbprefix('bill'). " b 
				JOIN ".$this->db->dbprefix('customer'). " c ON c.id = b.customerid
				WHERE c.id = '{$this->session->userdata('customer')->id}' AND c.purchasingadmin = '{$this->session->userdata('customer')->purchasingadmin}'";
		
        $billdetails = $this->db->query($sql1)->result_array();
       
        $data['billdetails'] = $billdetails;
       
        if(isset($_POST['billid']) && $_POST['billid'] != '')	
        {
        	$sql = "SELECT b.*,c.*,b.id as billid 
				FROM ".$this->db->dbprefix('bill'). " b 
				JOIN ".$this->db->dbprefix('customer'). " c ON c.id = b.customerid
				WHERE c.id = '{$this->session->userdata('customer')->id}' AND c.purchasingadmin = '{$this->session->userdata('customer')->purchasingadmin}' AND b.id = ".$_POST['billid'];
		
        	$data['billinfo'] = $this->db->query($sql)->result_array();
        	        	
         	$data['selectedbill'] = $_POST['billid'];
         	
			$sql2 = "SELECT b.id as billid ,b.*,bi.*,bph.*,bi.id as billitemid, pb.bankname, pb.accountnumber, pb.routingnumber,i.item_img 
					 FROM ".$this->db->dbprefix('billitem')." bi 
					 LEFT JOIN ".$this->db->dbprefix('bill')." b ON b.id=bi.bill 
					 LEFT JOIN ".$this->db->dbprefix('item')." i ON i.id=bi.itemid 
					 LEFT JOIN ".$this->db->dbprefix('bill_payment_history')." bph ON bph.bill = b.id  
					 LEFT JOIN ".$this->db->dbprefix('purchaserbank')." pb ON b.purchasingadmin = pb.purchasingadmin 
					 WHERE b.id = ".$_POST['billid'] ." GROUP BY bi.itemid";	
			
        	$data['billItemdetails'] = $this->db->query($sql2)->result_array();
        	
        	$sql3 = "SELECT bs.* FROM ".$this->db->dbprefix('bill'). " b 
        			JOIN ".$this->db->dbprefix('bill_servicelaboritems'). " bs ON bs.billid = b.id 
        			WHERE b.id = ". $_POST['billid'];
        	$data['billservicedetails'] = $this->db->query($sql3)->result_array();        	
        }
        
        if(isset($_POST['billid']) && $_POST['billid'] != '')
        {
        	 $billid = $_POST['billid'];	
        }
        elseif(isset($_POST['billnumber']) && $_POST['billnumber'] != '')
        {
        	 $billid = $_POST['billnumber'];	
        }
        else 
        {
        	$billid = '';
        }
       
		if(isset($_POST['btnSave']) && $_POST['btnSave'] == 'Save')
		{
			$insertArr['bill'] = $billid;
			$insertArr['refnum'] = @$_POST['refnum'];
			$insertArr['amountpaid'] = @$_POST['amountpaid'];
			$insertArr['paymenttype'] = @$_POST['paymenttype'];
			$insertArr['paymentdate'] = date('Y-m-d');
			$data['selectedbill'] = $billid;
			$this->db->insert('bill_payment_history',$insertArr);
		}
        	
        $this->session->set_flashdata('billid',$billid); 
        $this->load->template('../../templates/front/register');      
		$this->load->view('site/customerbill',$data);
	}
	
	function getmanufacturers(){
		
		if(@$_POST[ 'industryid' ]==0){
			
			$result = $this->db->get_where('type', array('category'=>'Manufacturer'))->result();
		}else{			
		
		$checkauth = array('parent_id' => $_POST[ 'industryid' ]);
    	$this->db->where($checkauth);
    	$result = $this->db->get('type')->result();
		}
		echo json_encode($result);
	}
	
	
	function paybillbycc()
    {
    	//echo "<pre>",print_r($_POST); die;
        $user = $this->db->select('users.*')->from('bill')                   
                   ->join('users','users.id=bill.purchasingadmin')
                   ->where('bill.id',$_POST['invoicenum'])
                   ->get()->row();
                   
        $bankaccount = $this->db->where('purchasingadmin',$user->id)->get('purchaserbank')->row();
        
        if(!$bankaccount || !@$bankaccount->routingnumber || !@$bankaccount->accountnumber)
		{
		    $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bank account missing for credit card payment.</div></div>');
		    redirect('site/customerbill');
		}
        //print_r($user);die;
		ini_set('max_execution_time', 300);
		$this->load->model('admin/settings_model');
		$config = (array)$this->settings_model->get_setting_by_admin($user->id);
		$config = array_merge($config, $this->config->config);
		require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
		Stripe::setApiKey($config['STRIPE_API_KEY']);
		//$myCard = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2015);
		$myCard = array('number' => $_POST['card'], 'exp_month' => $_POST['month'], 'exp_year' => $_POST['year']);
		$charge = Stripe_Charge::create(array('card' => $myCard, 'amount' => round($_POST['amount'] * 100), 'currency' => 'usd' ));
		//echo "<pre>",print_r($charge); die;
		$chargeobj = json_decode($charge);
		if(@$chargeobj->paid)
		{
			
			/*$amount = $_POST['amount'] + $_POST['amount']*$config['taxpercent']/100;
			$amount=$amount-.55-($amount*2.9/100);
			$amount = round($amount,2);*/
			
			if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
			{
	          $recbankInfo = array(
	          			'country' =>'US',
	          			'routing_number' => $bankaccount->routingnumber,
	          			'account_number' => $bankaccount->accountnumber
	          );
              $recObj = Stripe_Recipient::create(array(
              "name" => $user->fullname,
              "type" => "individual",
              "email" => $user->email,
              "bank_account" => $recbankInfo)
              );
              $obj = json_decode($recObj);
              $_POST['amount'] = round($_POST['amount'],2);
              $transferObj = Stripe_Transfer::create(array(
                  "amount" => $_POST['amount'] * 100,
                  "currency" => "usd",
                  "recipient" => $obj->id,
                  "description" => "Transfer for ".$user->email )
              );
              $tobj = json_decode($transferObj);
              
              
              $billarray = array();
              $billarray['status'] = "Verified";
              $billarray['paymentstatus'] = "Paid";
              $billarray['ispaid'] = 1;
              $this->db->where('id', $_POST['invoicenum']);
              $this->db->update('bill', $billarray);
              $billhistory = array();
              $billhistory['bill'] = $_POST['invoicenum'];
              $billhistory['paymenttype'] = "Credit Card";
              $billhistory['paymentdate'] = date('Y-m-d');
              $billhistory['refnum'] = $chargeobj->balance_transaction;
              $billhistory['amountpaid'] = $_POST['amount'];
              $this->db->insert('pms_bill_payment_history', $billhistory);
              
                            
    		  $bill = $this->db->select('bill.*')
    		            ->from('bill')    		           
    		            ->where('id',$_POST['invoicenum'])
    		            ->get()->row();
              $data['email_body_title']  = "Dear {$user->username}";
$data['email_body_content'] = "$ {$_POST['amount']} has been transfered to your bank account for bill#{$bill->billname},
with the transfer# {$tobj->id}.
<br>Payment by: ".$this->session->userdata('customer')->name."
<br>Bill#: ".$bill->billname."
";
$loaderEmail = new My_Loader();
              $send_body = $loaderEmail->view("email_templates/template",$data,TRUE);              
    	      $this->load->library('email');
    	      $config['charset'] = 'utf-8';
    	      $config['mailtype'] = 'html';
    	      $this->email->initialize($config);
              $this->email->from($this->session->userdata('customer')->email);
              $this->email->to($user->email);
              $this->email->subject('Bank transfer notification');
              $this->email->message($send_body);
              $this->email->set_mailtype("html");
              $this->email->send();
              $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bill paid successfully.</div></div>');
        	}
		}
		redirect('site/customerbill');
    }
    
    function getpodate()
    {
        $pa = $this->session->userdata('site_loggedin');
        if (!$pa)
            die;
        $pid = $_POST['pid'];
        if(isset($pid))
        {
        	$sql = "SELECT * FROM " . $this->db->dbprefix('quote')
            	    . " WHERE pid='$pid' AND potype='Bid'";
        	$sql .= " AND purchasingadmin='" . $pa->id . "'";
        	
        	$items = $this->db->query($sql)->result();
        }	      
        $ret = '';
        foreach($items as $item)
        {
            if(!$this->db->where('quote',$item->id)->get('invitation')->row())
                $ret .= $item->podate;
        }
        	
        echo $ret;
    }
    
    
    function addpoquote(){
    	
    	if(!$_POST)
    	die;
    	
    	$message = "";
    	$pid = $_POST['pid'];
    	$potype = "Bid";
    	//echo "<pre>",print_r($_POST); die;
    	       
        if ($this->quote_model->checkDuplicatePonum($this->input->post('ponum'), 0,$pid)) {
            	$message = 'Duplicate PO#';   
            	echo json_encode($message);        
        }
        else {            
        	
        	$options = array(
        	'pid' => $pid,
        	'potype' => $potype,
        	'ponum' => $this->input->post('ponum'),
        	'podate' => $this->input->post('podate'),        	
        	'company' => $this->input->post('company'),
        	'deliverydate' => $this->input->post('deliverydate'),
        	'duedate' => $this->input->post('duedate'),        	        	
        	'creation_date' => date('Y-m-d')        	
        	);
        	if(@$this->session->userdata('purchasingadmin'))
        	$options['purchasingadmin'] = $this->session->userdata('purchasingadmin');        	
        	$this->db->insert('quote', $options);        	
        	$result = $this->db->insert_id();
			if($result){
				 $data['poid'] = $result;
				 $data['ponum'] = $this->input->post('ponum');
            	 echo json_encode($data);         
			}	 
        }        
    	
    }
    
    
     public function contractors($keyword = false)
      {
      	
        if ($this->input->post('keyword') || $keyword)
        {
            if (!$keyword)
            {
                $keyword = $this->input->post('keyword');
            }
           
            $query_contractors = $this->search_contractor($keyword); 
            $this->data['found_records'] = "Found " . $query_contractors->totalresult . "Contractors";
            $this->data['submiturl'] = 'site/contractors/' . $keyword;
            $this->data['keyword'] = $keyword;
            if ($keyword)
            {
                $this->data['page_titile'] = "Search for \"$keyword\"";
            }
            else
            {
                $this->data['page_titile'] = "Contractors List";
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
            $use_contractor_position = false;
            $this->homemodel->set_search_criteria($search);
            $location = $this->input->post('location');
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
            $query_contractors = $this->homemodel->get_nearest_contractors();
           
            if (!$query_contractors->totalresult)
            {       
                $this->homemodel->set_distance(15000);
                $query_contractors = $this->homemodel->get_nearest_contractors($ignore_location = true);
                $this->homemodel->set_distance(20);
                $this->data['found_records'] = "Found " . $query_contractors->totalresult . " contractors";
            }
            else
            {
                $this->data['found_records'] = "Found " . $query_contractors->totalresult . " nearest contractors";
            }
            $this->data['submiturl'] = 'site/contractors';
        }
        
        
        $this->data['norecords'] = false;
        if ($query_contractors->totalresult == 0)
        {
            $this->data['norecords'] = 'No Records found for the search.';
        }
        
        $limit = 6;
        $this->data['totalcount'] = $query_contractors->totalresult;
        $this->data['currentpage'] = $_POST['pagenum'] + 1;
        $this->data['totalpages'] = ceil($this->data['totalcount'] / $limit);
        $this->data['submitmethod'] = 'POST';
        $this->data['pagingfields'] = $_POST;
        $this->data['contractors'] = array();
        $contractors = $query_contractors->contractors;
         
        $this->load->helper('text');
        foreach ($contractors as $contractor)
        {
            if ($keyword)
            {
                $contractor->fullname = highlight_phrase($contractor->fullname, $keyword, '<span style="color:#990000">', '</span>');
                $contractor->address = highlight_phrase($contractor->address, $keyword, '<span style="color:#990000">', '</span>');
                $contractor->companyname = highlight_phrase($contractor->companyname, $keyword, '<span style="color:#990000">', '</span>');
            }
            $this->data['contractors'][] = $contractor;
            
        }
         
        $sql = "SELECT DISTINCT(CONCAT(city,', ',state)) citystate FROM " . $this->db->dbprefix('users') ." WHERE isdeleted = 0";
        $this->data['citystates'] = $this->db->query($sql)->result();
        $this->data['states'] = $this->db->get('state')->result();
        $this->data['contractcategory'] = $this->db->get('contractcategory')->result();
       	$this->data['page_title'] = "Contractor Info.";
       	$this->data['page_description'] = "Contractor";       	
       	$this->load->view('site/contractors', $this->data);	
       	
    }
    
    public function search_contractor($keyword)
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
        $use_contractor_position = false;
        $this->homemodel->set_search_criteria($search);
        $this->homemodel->set_keyword($keyword);
        $con = $this->homemodel->FindContractor();       
        return $con;
    }
    
   public function contractor($username=NULL)
    {
    	$this->load->helper('text');
    	$this->load->library('image_lib');
        $username = urldecode($username);
        $data['message'] = $this->session->flashdata('message'); 
        $data['contractor'] = $this->supplier_model->get_contractor($username);              
        if(!$data['contractor'])
        	redirect('');
        if(!@$data['contractor']->username || !@$data['contractor']->id)
        {
        	sleep(5);
        	redirect('site/contractor/'.$username);
        }
        $id = $data['contractor']->id;
        $data['nextid'] = $this->nextcontractor($id);
        $data['previd'] = $this->prevcontractor($id);
        if (!trim(@$data['contractor']->user_lat) && @$data['contractor']->address)
        {
            $geoloc = get_geo_from_address($data['contractor']->address);
            if(@$geoloc->lat && @$geoloc->lat)
            {
                $update_contractor['user_lat'] = $geoloc->lat;
                $update_contractor['user_lng'] = $geoloc->long; 
                
                $this->supplier_model->update_contractor($id, $update_contractor);
                $data['contractor'] = $this->supplier_model->get_contractor($id);
            }
        }
         	
        $similarcontractors = $this->supplier_model->getrelatedcontractor($id);
        $data['similarcontractors'] = $similarcontractors;     
        $data['image']=$this->db->get_where('Contractor_Images',array('contractor'=>$data['contractor']->id))->result();
       	$data['page_title'] = $data["contractor"]->companyname;
       	$data['page_description'] = character_limiter($data["contractor"]->shortdetail,150);
       
       	if ($this->session->userdata('site_loggedin'))
       	 {
        	 $data['filesprivate']=$this->db->get_where('Contractor_Files',array('contractor'=>$data['contractor']->id,'private'=>1))->result();
       	 }
        $data['filespublic']=$this->db->get_where('Contractor_Files',array('contractor'=>$data['contractor']->id,'private'=>0))->result();
        $data['gallery']=$this->db->get_where('Contractor_Gallery',array('contractor'=>$data['contractor']->id))->result();
        
        $membersDB = $this->db->where("contractorid", $data['contractor']->id)->get("Contractor_Team")->result();
     	foreach($membersDB as $mdb){
     		$config['image_library'] = 'gd2';
     		$config['source_image'] = './uploads/ContractorTeam/'.$mdb->picture;
     		$config['create_thumb'] = TRUE;
     		$config['maintain_ratio'] = FALSE;
     		$config['width']     = 190;
     		$config['height']   = 194;
     		$this->image_lib->clear();
     		$this->image_lib->initialize($config);
     		$this->image_lib->resize();
     		$pathinfo = pathinfo($mdb->picture);
     		$mdb->picture = $pathinfo["filename"]."_thumb.".$pathinfo["extension"];
     		$data["members"][] = $mdb;
     	}
        
        $bhrs = $this->db->get_where('Contractor_Business_Hour',array('contractor'=>$data['contractor']->id))->result();
        $data['businesshrs'] = $bhrs;       
        $this->load->view('site/contractor', $data);
    }
    
    public function nextcontractor($id)
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('users') . "  ORDER BY companyname ASC";
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
    
    public function prevcontractor($id)
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('users') . "  ORDER BY companyname ASC";
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
    
     public function sendcontractrequest($id)
    	{
        if(!$_POST)
            die;
	    $settings = (array)$this->homemodel->getconfigurations ();
	    $data['email_body_title'] = "";
	    $data['email_body_content'] = "";
	    if(strpos(@$_POST['redirect'], 'contractor') === 0)
	    {
	        $contractor = $this->db->where('id',$id)->get('users')->row();
	        $to = $contractor->email;
		    $data['email_body_title'] = 'You have a new request for assistance.';
	    }
	    else
	    {
	        $to = $settings['adminemail'];
	        if(@$_POST['redirect'])
		    $data['email_body_title'] = 'You have a new request for assistance regarding '.site_url($_POST['redirect']).'.';
		    else
		    $data['email_body_title'] = 'You have a new request for assistance.';
	    }
		$data['email_body_content'] .= ' Details are:<br/><br/>';
		if(@$_POST['contactName'])
		$data['email_body_content'] .= "Name: ".$_POST['contactName']."<br/>";
		if(@$_POST['name'])
		$data['email_body_content'] .= "Name: ".$_POST['name']."<br/>";
		if(@$_POST['email'])
		$data['email_body_content'] .= "Email: ".$_POST['email']."<br/>";
		if(@$_POST['subject'])
		$data['email_body_content'] .= "Subject: ".$_POST['subject']."<br/>";
		if(@$_POST['comments'])
		$data['email_body_content'] .= "Details: ".$_POST['comments']."<br/>";
		if(@$_POST['phone'])
		$data['email_body_content'] .= "Phone: ".$_POST['phone']."<br/>";
		
		if(@$_POST['type']) {
			if($_POST['type'] == 'Request Phone Assistance')
			{
				if(@$_POST['daytd'])
				$data['email_body_content'] .= "Best day to call: ".$_POST['daytd']."<br/>";
				if(@$_POST['timetd'])
				$data['email_body_content'] .= "Best time to call: ".$_POST['timetd']."<br/>";
			}else {
				if(@$_POST['daytd'])
				$data['email_body_content'] .= "Appointment date: ".$_POST['daytd']."<br/>";
				if(@$_POST['timetd'])
				$data['email_body_content'] .= "Appointment time: ".$_POST['timetd']."<br/>";
			}
		}
		if(@$_POST['regarding'])
		$data['email_body_content'] .= "Regarding: ".$_POST['regarding']."<br/>";
		$loaderEmail = new My_Loader();
		$send_body = $loaderEmail->view("email_templates/template",$data,TRUE);
		$this->load->library('email');
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($settings['adminemail']);
		$this->email->to($to);
		$this->email->subject('Request for assistance');
		$this->email->message($send_body);
		$this->email->set_mailtype("html");
		$this->email->send();
        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Email was sent successfully</div></div>');      
	    redirect('site/'.$_POST['redirect']);
	    
    }
    
   
	
}    