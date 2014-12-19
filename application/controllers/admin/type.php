<?php
class type extends CI_Controller
{
	private $limit = 10;
	private $pageid = 6;

	function type()
	{
		parent::__construct ();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh');
		}
		if($this->session->userdata('usertype_id')==3)
		{
			redirect('admin/dashboard', 'refresh');
		}
		$this->load->dbforge();
		$this->load->library ('form_validation');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$this->load->model('admin/type_model');
		$this->load->model('admin/adminmodel');
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
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$data ['title'] = "Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}

	function index($offset = 0)
	{
		$uri_segment = 4;
		$offset = $this->uri->segment ( $uri_segment );

		$this->load->library ( 'pagination' );
		$config ['base_url'] = site_url ('admin/type/index/');
		$config ['total_rows'] = $this->type_model->count_all ();
		$config ['per_page'] = $this->limit;
		$config ['uri_segment'] = $uri_segment;
		$this->pagination->initialize ( $config );
		$data ['pagination'] = $this->pagination->create_links ();

		$type = $this->type_model->get_items ($this->limit, $offset);

		$jsitems = array();
		if (count($type) >= 1)
		{
			$sn = $offset + 0;
			foreach ($type as $item)
			{
				$item->sn = ++$sn;
				$jsitems[] = $item;
			}
		    $data['items'] = $jsitems;
		}
		else
		{
			$data['items'] = array();
		    $this->data['message'] = 'No Records';
		}
		//print_r($jsitems);die;
		$this->_set_fields ();

		$data ['addlink'] = '';
		$data ['heading'] = 'type';
		$data ['table'] = $this->table->generate ();
		$data ['addlink'] = '<a class="btn btn-primary" href="'.base_url().'admin/type/add"><i class="icon-plus-sign"></i> Add New type</a>';
		$this->load->view ('admin/type/list', $data);
	}

	function add()
	{
		$this->_set_fields ();
		$data ['heading'] = 'Add New type';
		$data ['message'] = '';
		$data ['action'] = site_url ('admin/type/additem');
		$data['types'] = $this->db->get_where('type', array('category'=>'Industry'))->result();
		$this->load->view ('admin/type/form', $data);
	}

	function additem()
	{
		$data ['heading'] = 'Add New type';
		$data ['action'] = site_url ('admin/type/additem');

		$this->_set_fields ();
		$this->_set_rules ();

		if(isset($_FILES['image']['tmp_name']))
		if(is_uploaded_file($_FILES['image']['tmp_name']))
		{
			$ext = end(explode('.', $_FILES['image']['name']));
			$nfn = $_FILES['image']['name'];
			if(!in_array(strtolower($ext), array('jpg','jpeg','png')))
			{
				$this->validation->image_error = '* Invalid File Type, Upload image.';
				$error = true;
			}
			elseif(move_uploaded_file($_FILES['image']['tmp_name'], "uploads/type/".$nfn))
			{
			    $this->_createThumbnail($nfn,'type',95,34);
				unset($_POST['image']);
				$_POST['image'] = $nfn;
			}
		}

		if ($this->validation->run () == FALSE)
		{
			$this->load->view ('admin/type/form', $data);
		}
		else
		{
			$itemid = $this->type_model->add ();
			$this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>type Added Successfully.</div>');
			redirect('admin/type');
		}
	}

	function update($id)
	{
		$this->_set_fields ();
		$item = $this->type_model->get_item($id);
		$columns = (array)$this->type_model->getfields();

		foreach($columns as $column)
		{
			$column = (array)$column;
			$this->validation->$column['Field'] = $item->$column['Field'];
		}
		//print_r($item);die;
		$data ['heading'] = 'Update type';
		$data ['message'] = '';
		$data['types'] = $this->db->get_where('type', array('category'=>'Industry'))->result();
		$data ['action'] = site_url ('admin/type/updateitem');
		$this->load->view ('admin/type/form', $data);
	}

	function updateitem()
	{
		$data ['heading'] = 'Update type';
		$data ['action'] = site_url ('admin/type/updateitem');
		$this->_set_fields ();
		$this->_set_rules ();

		if(isset($_FILES['image']['tmp_name']))
		if(is_uploaded_file($_FILES['image']['tmp_name']))
		{
			$ext = end(explode('.', $_FILES['image']['name']));
			$nfn = $_FILES['image']['name'];
			if(!in_array(strtolower($ext), array('jpg','jpeg','png')))
			{
				$this->validation->image_error = '* Invalid File Type, Upload image.';
				$error = true;
			}
			elseif(move_uploaded_file($_FILES['image']['tmp_name'], "uploads/type/".$nfn))
			{
			    $this->_createThumbnail($nfn,'type',95,34);
				unset($_POST['image']);
				$_POST['image'] = $nfn;
			}
		}

		if ($this->validation->run () == FALSE)
		{
		    $data ['action'] = site_url ('admin/type/updateitem');
			$this->load->view ('admin/type/form', $data);
		}
		else
		{
			$this->type_model->update ();
			$this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>type has been Updated.</div>');
			redirect('admin/type');
		}
	}

	function delete($id)
	{
		$this->type_model->remove($id);
		$this->session->set_flashdata('message', '<div class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Type Deleted.</div>');
		redirect ('admin/type', 'refresh');
	}

	function _set_fields()
	{
		$columns = (array)$this->type_model->getfields();
		$fields = array();
		foreach($columns as $column)
		{
			$column = (array)$column;
			$fields [$column['Field']] = $column['Field'];
		}
		$fileds['labels'] = 'Lables';
		$fileds['types'] = 'Types';
		$this->validation->set_fields ($fields);
	}

	function _set_rules()
	{
		$rules = array();
		$rules ['title'] = 'trim|required';
		$this->validation->set_rules ($rules);
		$this->validation->set_message ('required', '* Field Required');
		$this->validation->set_error_delimiters ( '<div class="frmerror">', '</div>');
	}

	function _createThumbnail($fileName, $foldername="", $width=170, $height=150)
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
}
?>