<?php

class catcode extends CI_Controller {

    function catcode() 
    {
        parent::__construct();
        $this->load->library('session');
        if (!$this->session->userdata('id')) {
            redirect('admin/login/index', 'refresh');
        }
        if ($this->session->userdata('usertype_id') == 3) {
            redirect('admin/dashboard', 'refresh');
        }
        $this->load->dbforge();
        $this->load->library('form_validation');
        $this->load->library(array('table', 'validation', 'session'));
        $this->load->helper('form', 'url');
        $this->load->model('admin/catcode_model');
        $this->load->model('admin/settings_model');
        $this->load->model('admin/quote_model');
        $this->load->model('admin/itemcode_model');
        $data['pendingbids'] = $this->quote_model->getpendingbids();
        //$data['cats'] = $this->catcode_model->get_catcodes();
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data ['title'] = "Administrator";
        $this->load = new My_Loader();
        $this->load->template('../../templates/admin/template', $data);
    }

    private function list_categories(&$categories, $parent_id, $cats, $sub = '') 
    {

        foreach ($cats[$parent_id] as $cat) 
        {
            $cat->id = $cat->id;
            $cat->catname = $sub . $cat->catname;
            $cat->cattype = $cat->parent_id?'Sub Category':'Parent Category';
            $cat->actions = anchor('admin/catcode/update/' . $cat->id, '<span class="icon-2x icon-edit"></span>', array('class' => 'update'))
                    . ' ' .
                    anchor('admin/catcode/delete/' . $cat->id, '<span class="icon-2x icon-trash"></span>', array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"))
            ;
            $categories[] = $cat;
            if (isset($cats[$cat->id]) && sizeof($cats[$cat->id]) > 0) {
                $sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
                $sub2 .= '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
                $this->list_categories($categories, $cat->id, $cats, $sub2);
            }
        }
    }
    

    function index() 
    {

        $catcodes = $this->catcode_model->get_categories_tiered();

        $categories = array();
        if ($catcodes) 
        {

            if (isset($catcodes[0])) 
            {
                $this->list_categories($categories, 0, $catcodes);
            }

            $data['categories'] = $categories;
            $data['jsfile'] = 'catcodes.php';
        } 
        else 
        {
            $this->data['message'] = 'No Records';
        }

        $data ['heading'] = 'Category Management';
        $data ['table'] = $this->table->generate();
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/catcode/addcat">Add Category</a>';
        $data ['addsubcatlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/subcatcode/addcat">Add Sub Category</a>';
        $this->load->view('admin/catlist', $data);
    }

    function addcat() 
    {
        $catcodes = $this->catcode_model->get_categories_tiered();
        $categories = array();
        if ($catcodes) {
            if (isset($catcodes[0])) {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $data['id'] = false;
        $data['parent_id'] = false;
        $data['categories'] = $categories;
        $this->_set_catfields();
        $data ['heading'] = 'Add New Category';
        $data ['message'] = '';
        $data ['action'] = site_url('admin/catcode/add_catcode');
        $data['parentoptions'] = $this->catcode_model->getTreeOptions();
        $this->load->view('admin/catcode', $data);
    }
    function  do_upload($previous_image = "")
    {
        $config['upload_path'] = './uploads/category-banners/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['remove_spaces'] = TRUE;
       // $config['max_size']	= '330';
        //$config['max_width']  = '830';
        //$config['max_height']  = '330';
        
        $this->load->library('upload', $config);
        $field_name = "banner_image";
        if ( ! $this->upload->do_upload($field_name))
        {
                $error = array('error' => $this->upload->display_errors());
                return $error;
        }
        else
        {
                $data_image = array('upload_data' => $this->upload->data());
                
                $config['image_library'] = 'gd2';
                $config['source_image']	= './uploads/category-banners/'.$data_image['upload_data']['file_name'];
                $config['new_image'] =  './uploads/category-banners/thumbs/'.$data_image['upload_data']['file_name'];
                $config['create_thumb'] = TRUE;
                $config['thumb_marker'] = '';
                //$config['maintain_ratio'] = TRUE;
                $config['width']	 = 90;
                $config['height']	= 60;
                $this->load->library('image_lib', $config); 
                $this->image_lib->resize();
                
                //Unlink Previous images
                if($previous_image!= "")
                {
                        unlink('./uploads/category-banners/'.$previous_image);
                        unlink('./uploads/category-banners/thumbs/'.$previous_image);
                }   
                
        }
         return $data_image['upload_data']['file_name'];
    }
    function add_catcode() 
    {
        $image_name = "";
        if($_FILES['banner_image']['error'] == 0)
        {
             $image_name = $this->do_upload();
        }
        /*
        if(isset($image_name['error']) && $image_name['error'] != "")
        {
            $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Not a valid image file.</div></div>');
            redirect(base_url()."admin/catcode/addcat/");
            die;
        }
        */

        
        $data ['heading'] = 'Add New Category';
        $data ['action'] = site_url('admin/catcode/add_catcode');

        $this->_set_catfields();
        $this->_set_catrules();
        $data['parentoptions'] = $this->catcode_model->getTreeOptions();

        if ($this->validation->run() == FALSE) {
            $data ['message'] = $this->validation->error_string;
            $this->load->view('admin/catcode', $data);
        } elseif ($this->catcode_model->checkDuplicateCat($this->input->post('catname'), 0)) {
            $data ['message'] = 'Duplicate Category';
            $this->load->view('admin/catcode', $data);
        } else {
            $itemid = $this->catcode_model->SaveCategory($image_name);
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Category Added Successfully</div></div>');
            redirect('admin/catcode');
        }
    }

    function _set_catfields() {
        $fields ['id'] = 'id';
        $fields ['catname'] = 'catname';
        $this->validation->set_fields($fields);
    }

    function _set_catrules() {
        $rules ['catname'] = 'trim|required';

        $this->validation->set_rules($rules);
        $this->validation->set_message('required', '* required');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }

    function update($id) 
    {
        $catcodes = $this->catcode_model->get_categories_tiered();
        $categories = array();
        if ($catcodes) {
            if (isset($catcodes[0])) {
                build_category_tree($categories, 0, $catcodes);
            }
        }
        $data['categories'] = $categories;

        $this->_set_catfields();
        //$this->_set_catrules();

        $category_record = $this->catcode_model->get_category($id);

        $data['id'] = $category_record->id;
        $data['parent_id'] = $category_record->parent_id;
        $data['parentoptions'] = $this->catcode_model->getTreeOptions($category_record->parent_id);
        $cat = $this->catcode_model->get_catcodes_by_id($id);
        // var_dump($cat);
        $this->validation->id = $id;
        $this->validation->catname = $cat[0]->catname;
        $this->validation->parent_id = $cat[0]->parent_id;
        
      //  $data['banner_image'] = $cat[0]->banner_image;
        
        $data ['heading'] = 'Update Category';
        $data ['message'] = '';
        $data ['action'] = site_url('admin/catcode/updatecatcode');
        $this->load->view('admin/catcode', $data);
    }

    function updatecatcode() {
        $data ['heading'] = 'Update Category';
        //$data ['action'] = site_url ('admin/catcode/update/'.$catid);
        $this->_set_catfields();
        $this->_set_catrules();
        
        $image_name = $this->input->post('previous_image');
        
        if($_FILES['banner_image']['error'] == 0)
        {
            $image_name = $this->do_upload($this->input->post('previous_image'));
        }
        
//        if(isset($image_name['error']) && $image_name['error'] != "")
//        {
//            $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Not a valid image file.</div></div>');
//            redirect(base_url()."admin/catcode/update/".$this->input->post('id'),$data);
//            die;
//        }
        

        $catid = $this->input->post('id');
        // echo $this->validation->run (); exit;
        //$cat = $this->catcode_model->get_catcodes_by_id ($catid);
        if ($this->validation->run() == FALSE) {
            $data ['message'] = $this->validation->error_string;
            $data ['action'] = site_url('admin/catcode/update/' . $catid);
            $data['parentoptions'] = $this->catcode_model->getTreeOptions($this->input->post('parent_id'));
            $this->load->view('admin/catcode', $data);
        } elseif ($this->catcode_model->checkDuplicateCat($this->input->post('catname'), $catid)) {
            $data ['message'] = 'Duplicate Category';
            $data ['action'] = site_url('admin/catcode/update/' . $catid);
            $data['parentoptions'] = $this->catcode_model->getTreeOptions($this->input->post('parent_id'));
            $this->load->view('admin/catcode', $data);
        } else {
            
            $dataUpdate = array(
                            "catname"=>$this->input->post('catname'),
                            "parent_id"=>$this->input->post('parent_id'),
                            "banner_image"=>$image_name
            );
             $this->catcode_model->updateCategory($dataUpdate);
            
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Category Updaed Successfully</div></div>');
            redirect('admin/catcode');
            //redirect('admin/itemcode/index');
        }
    }

    function delete($id) {
        $this->catcode_model->remove_category($id);
        redirect('admin/catcode', 'refresh');
    }

}
?>