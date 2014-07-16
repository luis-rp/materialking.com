<?php
class project extends CI_Controller 
{
	private $limit = 10;
	
	function project() 
	{
		parent::__construct ();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh'); 
		}
		$this->load->library ('form_validation');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$this->load->model('admin/project_model');
		$this->load->model('admin/quote_model');
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$data ['title'] = "Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}
	
	function index($offset = 0) 
	{
		$uri_segment = 4;
		$offset = $this->uri->segment ($uri_segment);
		$projects = $this->project_model->get_projects ($this->limit, $offset);
		
		$this->load->library ('pagination');
		$config ['base_url'] = site_url ('admin/project/index');
		$config ['total_rows'] = $this->project_model->total_project ();
		$config ['per_page'] = $this->limit;
		$config ['uri_segment'] = $uri_segment;
		
		$this->pagination->initialize ($config);
		$data ['pagination'] = $this->pagination->create_links();
		$this->load->library ('table');
		$this->table->set_empty ("&nbsp;");
		$this->table->set_heading ('ID', 'Name',  'Actions');
		$i = 0 + $offset;
		
		$count = count ($projects);
		$data['counts'] = count($projects);
		
		$items = array();
		if ($count >= 1) 
		{
			foreach ($projects as $project) 
			{
				$project->actions=
				anchor ('admin/project/update/' . $project->id,'<span class="icon-2x icon-edit"></span>',array ('class' => 'update' ) )
				. ' ' . 
				anchor ( 'admin/project/delete/' . $project->id, '<span class="icon-2x icon-trash"></span>', array ('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')" ) ) 
				. ' ' . 
				anchor ( 'admin/quote/index/' . $project->id, '<span class="icon-2x icon-file"></span>', array ('class' => 'quote' ) ) 
				;
				$items[] = $project;
			}
				 
		    $data['items'] = $items;
		    $data['jsfile'] = 'projectjs.php';
		}
		else
		{
		    $this->data['message'] = 'No Records';
		}
		$data ['addlink'] = '';
		$data ['heading'] = 'Project Management';
		$data ['table'] = $this->table->generate ();
		$data ['addlink'] = '<a id="step5" class="btn btn-green" href="'.base_url().'admin/project/add">Add Project</a>';
		$data['viewname'] = 'projects';
		$this->load->view ('admin/projects', $data);
	}

	function add()
	{
		$this->_set_fields ();
		$data ['heading'] = 'Add New Project';
		$data ['message'] = '';
		$data ['action'] = site_url ('admin/project/add_project');
		$data['viewname'] = 'project';
		$this->load->view ('admin/project', $data);
	}
	
	function add_project() 
	{
		$_POST['startdate'] = date('Y-m-d', strtotime($_POST['startdate']));
		
		$data ['heading'] = 'Add New Project';
		$data ['action'] = site_url ('admin/project/add_project');
		
		$this->_set_fields ();
		$this->_set_rules ();
		
		if ($this->validation->run () == FALSE) 
		{
			$data ['message'] = $this->validation->error_string;
			$this->load->view ('admin/project', $data);
		} 
		elseif($this->project_model->checkDuplicateName($this->input->post('title'),0))
		{
			$data ['message'] = 'Duplicate Project Name';
			$this->load->view ('admin/project', $data);
		}
		else 
		{
			$itemid = $this->project_model->SaveProject ();
			$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox" style="display:inline;" id="step7">Project Added Successfully</div></div>');
			
			redirect('admin/project'); 
		}
	}
	
	function update($id)
	{
		$this->_set_fields ();
		$item = $this->project_model->get_projects_by_id ($id);
		$this->validation->id = $id;
		$this->validation->title = $item->title;
		$this->validation->description = $item->description;
		$this->validation->address = $item->address;
		$this->validation->startdate = $item->startdate;

		$data ['heading'] = 'Update Project Item';
		$data ['message'] = '';
		$data ['action'] = site_url ('admin/project/updateproject');
		$this->load->view ('admin/project', $data);
	}
	
	function updateproject()
	{
		$_POST['startdate'] = date('Y-m-d', strtotime($_POST['startdate']));
		$data ['heading'] = 'Update Project Item';
		$data ['action'] = site_url ('message/updateproject');
		$this->_set_fields ();
		$this->_set_rules ();
		
		$itemid = $this->input->post ('id');
		
		if ($this->validation->run () == FALSE) 
		{
			$data ['message'] = $this->validation->error_string;
		    $data ['action'] = site_url ('admin/project/updateproject');
			$this->load->view ('admin/project', $data);
		} 
		elseif($this->project_model->checkDuplicateName($this->input->post('title'),$itemid))
		{
			$data ['message'] = 'Duplicate Project Name';
			$this->load->view ('admin/project', $data);
		}
		else 
		{
			$this->project_model->updateProject ($itemid);
			$data ['message'] = '<div class="success">Project has been updated.</div>';
			$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Project Updated Successfully</div></div>');
			redirect('admin/project/update/'.$itemid); 
		}
	}
	
	function delete($id) 
	{
		$this->project_model->remove_project ($id);
		redirect ('admin/project', 'refresh');
	}
	
	function _set_fields() 
	{
		$fields ['id'] = 'id';
		$fields ['title'] = 'title';
		$fields ['description'] = 'description';
		$fields ['address'] = 'address';
		$fields ['startdate'] = 'startdate';
		$this->validation->set_fields ($fields);
	}
	
	function _set_rules() 
	{
		$rules ['title'] = 'trim|required';
		
		$this->validation->set_rules ( $rules );
		
		$this->validation->set_message ( 'required', '* required' );
		$this->validation->set_error_delimiters ( '<div class="error">', '</div>');
	}
}
?>