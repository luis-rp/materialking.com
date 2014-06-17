<?php
class subcatcode extends CI_Controller
{
function subcatcode()
	{
		parent::__construct ();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh');
		}
		$this->load->dbforge();
		$this->load->library ('form_validation');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$this->load->model('admin/subcatcode_model');
		$this->load->model('admin/settings_model');
		//$data['cats'] = $this->subcatcode_model->get_subcatcodes();
		$this->load->model('admin/quote_model');
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$data ['title'] = "Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}
        function index()
	{
            $subcatcodes = $this->subcatcode_model->get_subcatcodes ();
            $count = count ($subcatcodes);
	    $subcategories = array();
            if ($count >= 1)
	    {
                foreach ($subcatcodes as $cat)
                {
                    $cat->id= $cat->id;
                    $cat->subcategory = $cat->subcategory;
                    $cat->catname = $cat->catname;
                    $cat->actions=
				anchor ('admin/subcatcode/update/' . $cat->id,'<span class="icon-2x icon-edit"></span>',array ('class' => 'update' ) )
				. ' ' .
				anchor ( 'admin/subcatcode/delete/' . $cat->id, '<span class="icon-2x icon-trash"></span>', array ('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')" ) )
				;
                    $subcategories[] = $cat;
                }
                $data['subcategories'] = $subcategories;
                              //echo '<pre>'; var_dump($subcategories);exit;
                 $data['jsfile'] = 'subcatcodes.php';
            }else
		{
		    $this->data['message'] = 'No Records';
		}
                //$data ['addlink'] = '';
		$data ['heading'] = 'Sub Category Management';
		$data ['table'] = $this->table->generate ();
                $data ['addlink'] = '<a class="btn btn-green" href="'.base_url().'admin/subcatcode/addcat">Add Sub Category</a>';
                $this->load->view ('admin/subcatlist', $data);
        }
        function addcat()
	{
		$this->_set_catfields ();
		$data ['heading'] = 'Add New Sub Category';
		$data ['message'] = '';
		$data ['action'] = site_url ('admin/subcatcode/add_subcatcode');
                $data['parentcombooptions'] = $this->subcatcode_model->listHeirarchicalCombo();
		$this->load->view ('admin/subcatcode', $data);
	}
        function add_subcatcode()
	{
		$data ['heading'] = 'Add New Sub Category';
		$data ['action'] = site_url ('admin/subcatcode/add_subcatcode');

		$this->_set_catfields();
		$this->_set_catrules();
//echo $this->validation->run ();
		if ($this->validation->run () == FALSE)
		{
			$data ['message'] = $this->validation->error_string;
                         $data['parentcombooptions'] = $this->subcatcode_model->listHeirarchicalCombo();
			$this->load->view ('admin/subcatcode', $data);
		}
		elseif($this->subcatcode_model->checkDuplicateCat($this->input->post('subcategory'),0))
		{
			$data ['message'] = 'Duplicate Sub Category';
                         $data['parentcombooptions'] = $this->subcatcode_model->listHeirarchicalCombo('0',0,$this->input->post('category'));
			$this->load->view ('admin/subcatcode', $data);
			//$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Duplicate Itemcode</div></div>');
			//redirect('admin/itemcode/add');
		}
		else
		{
			$itemid = $this->subcatcode_model->SaveCategory();
			$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Category Added Successfully</div></div>');
			redirect('admin/subcatcode');
		}
	}
        function _set_catfields()
	{
		$fields ['id'] = 'id';
		$fields ['subcategory'] = 'subcategory';
                $fields ['category'] = 'category';
		$this->validation->set_fields ($fields);
	}
	function _set_catrules()
	{
		$rules ['subcategory'] = 'trim|required';
                $rules ['category'] = 'trim|required';

		$this->validation->set_rules ( $rules );
		$this->validation->set_message ( 'required', '* required' );
		$this->validation->set_error_delimiters ( '<div class="error">', '</div>');
	}
    
	function update($id)
	{
		$this->_set_catfields ();
                //$this->_set_catrules();
		$cat = $this->subcatcode_model->get_subcatcodes_by_id ($id);
               // var_dump($cat);
                $this->validation->id = $id;
		$this->validation->subcategory= $cat[0]->subcategory;
                $data['parentcombooptions'] = $this->subcatcode_model->listHeirarchicalCombo('0',0,$cat[0]->category);


                $data ['heading'] = 'Update Sub Category';
		$data ['message'] = '';
		$data ['action'] = site_url ('admin/subcatcode/updatesubcatcode');
		$this->load->view ('admin/subcatcode', $data);
    }
    
    function updatesubcatcode()
	{
		$data ['heading'] = 'Update Sub Category';
		
		$this->_set_catfields ();
		$this->_set_catrules ();

		$subcatid = $this->input->post ('id');
               // echo $this->validation->run (); exit;
		//$cat = $this->subcatcode_model->get_subcatcodes_by_id ($subcatid);
                if ($this->validation->run () == FALSE)
		{
			$data ['message'] = $this->validation->error_string;
                        $data['parentcombooptions'] = $this->subcatcode_model->listHeirarchicalCombo('0',0,$this->input->post('category'));
		        $data ['action'] = site_url ('admin/subcatcode/update/'.$subcatid);
			$this->load->view ('admin/subcatcode', $data);
		}
		elseif($this->subcatcode_model->checkDuplicateCat($this->input->post('subcategory'),$subcatid))
		{
			$data ['message'] = 'Duplicate  Category';
                        $data['parentcombooptions'] = $this->subcatcode_model->listHeirarchicalCombo('0',0,$this->input->post('category'));
                        $data ['action'] = site_url ('admin/subcatcode/update/'.$subcatid);
			$this->load->view ('admin/subcatcode', $data);
		}
		else
		{
			$this->subcatcode_model->updateCategory ();
			
			$data ['message'] = '<div class="success">Sub Category has been updated.</div>';
			redirect('admin/subcatcode');
			//redirect('admin/itemcode/index');
		}
        }
        function delete($id) 
	{
		$this->subcatcode_model->remove_category ($id);
		redirect ('admin/subcatcode', 'refresh');
	}

}
?>