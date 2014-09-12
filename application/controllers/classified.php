<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');
class classified extends CI_Controller
{
    public function classified()
    {
    	ini_set("memory_limit", "512M");
    	ini_set("max_execution_time", 700);
    	parent::__construct();
    	$data['title'] = 'Home';
    	$this->load->model('homemodel', '', TRUE);
		 $this->load->model('admin/catcode_model');
        $this->load->model('admin/itemcode_model');
		
    	$this->load = new My_Loader();
    	$this->load->template('../../templates/classified/template', $data);
    }
    
    public function index(){
		//echo "===="; exit;
    	$data['title'] = "Classified area";
    	$sql_cat = "SELECT * FROM ".$this->db->dbprefix('category')." WHERE id IN (SELECt category FROM ".$this->db->dbprefix('ads')." GROUP BY category)";
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
		
		/*===============*/
    	$this->load->view('classified/index', $data);
    }
    
    public function viewallads($catid){
    	
    	$cat = array();
    	$cat['catname'] = "";
    	    	
    	$data['title'] = "Classified area";
    		$sql_ad = "SELECT * FROM ".$this->db->dbprefix('ads')." WHERE category=".$catid; 
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
		
		/*===============*/
    	$this->load->view('classified/index', $data);
    }
    
    
        public function searchads(){
		//echo "===="; exit;
    	$data['title'] = "Classified area";
    	$where = "";
    	$str = "";
    	$cat = array();
    	$cat['catname'] = "";
    	if(isset($_POST['category']) && $_POST['category']!="") {
    		
    		if(isset($_POST['items']) && $_POST['items']!=""){
    			$where .= "AND category = {$_POST['category']}";
    		}else {
    			$subcategories = $this->items_model->getSubCategores($_POST['category']);
    			if($subcategories){

    				$str .= implode(',', $subcategories);
    				$where .= " AND category in (".$str.")";
    			}else
    			$where .= "AND category = {$_POST['category']}";
    		}
		
    	}
    	
    	if(isset($_POST['s']) && $_POST['s']!="") 
		$where .= " AND title like '%{$_POST['s']}%'";
    	
    	if(isset($_POST['items']) && $_POST['items']!="") 
    	$where .= " AND itemid = {$_POST['items']}";    	
    	  	
    	$sql_ad = "SELECT * FROM ".$this->db->dbprefix('ads')." WHERE 1=1 {$where}";
    	$res[$cat['catname']] = $this->db->query($sql_ad)->result_array();
    	
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

		/*===============*/
    	$this->load->view('classified/index', $data);
    }
    
    public function ad($id){
    	 
    	$sql = "SELECT c.id c_id,c.title c_title,c.address c_address,c.logo c_logo,c.username c_username,a.id a_id,a.title a_title,a.description a_description,a.price a_price,a.address a_address,a.latitude a_latitude,a.longitude a_longitude,a.published a_published, a.image a_image,a.views a_views,a.tags a_tags,c.phone c_phone,c.primaryemail c_primaryemail,a.category a_category, cat.catname FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('ads')." a, ".$this->db->dbprefix('category')." cat WHERE a.id=".$id." AND a.user_id=c.id AND a.category = cat.id ";
    	$data = $this->db->query($sql)->row_array();
    	$view = $data['a_views']+1;
    	$images = explode("|",$data["a_image"]);
    	foreach($images as $image){
    		$data['images'][]=$image;
    	}
    	$data['featured_image'] = $data['images'][0];
    	
    	$sql_rel =  "SELECT * FROM ".$this->db->dbprefix('ads')." WHERE category=(SELECT category FROM ".$this->db->dbprefix('ads')." WHERE id=".$id.") AND id<>".$id;
    	$data['related'] = $this->db->query($sql_rel)->result_array();
    	
    	$sql_popular = "SELECT * FROM ".$this->db->dbprefix('ads')." ORDER BY views ASC LIMIT 3";
    	$data['popular'] = $this->db->query($sql_popular)->result_array();
    	
    	$this->db->where('id', $id);
    	$this->db->update('ads', array("views"=>$view));
    	
    	$this->load->view('classified/ad',$data);
    }
    public function sendrequest($id)
    {
    	if(!$_POST)
    		die;
    	
    	$settings = (array)$this->homemodel->getconfigurations ();
    	
    		$supplier = $this->db->where('id',$id)->get('company')->row();
    		$to = $supplier->primaryemail;
    		$data['email_body_title'] = 'You have a new request for assistance.';
   
    	$data['email_body_title'] = ' Details are:<br/><br/>';
    	$data['email_body_title'] .= "Name: ".$_POST['contactName']."<br/>";
    	$data['email_body_title'] .= "Email: ".$_POST['email']."<br/>";
    	$data['email_body_title'] .= "Subject: ".$_POST['subject']."<br/>";
    
    	$data['email_body_title'] .= "Regarding: ".$_POST['comments']."<br/>";
    	$send_body = $this->load->view("email_templates/template",$data,TRUE);
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
    
    	$this->session->set_flashdata('message', 'Email was sent.');
    
    	redirect('classified/ad/'.$a_id);
    }
	
		// List Items of the selected Categories
  	 function get_items($categoryId){

		// $this->load->model('items_model');
		 header('Content-Type: application/x-json; charset=utf-8');
		 echo(json_encode($this->items_model->get_items2($categoryId)));
	}
	
	function search(){
		
		echo "==>> This is that!!"; exit;	
		
		
	}
}