<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Banner extends CI_Controller {

    public function Banner() {
        ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 700);
        parent::__construct();
        $this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh');
		}

        $this->load->dbforge();
        if ($this->session->userdata('company'))
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$this->load->model('admin/banner_model');
		$data ['title'] = 'Banner Settings';
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
    }

function index()
	{
	    $data['banner']=$this->banner_model->display();
        $this->load->view ('admin/banner',$data);
	}

    function do_upload()
    {
        if (isset($_FILES['banner']['tmp_name']))
            if (is_uploaded_file($_FILES['banner']['tmp_name'])) {
                $nfn = $_FILES['banner']['name'];
                $ext = end(explode('.', $nfn));
                if (!in_array(strtolower($ext), array('jpg', 'gif', 'jpeg', 'png'))) {
                    $errormessage = '* Invalid file type, upload logo file.';
                } elseif (move_uploaded_file($_FILES['banner']['tmp_name'], "uploads/banners/" . $nfn)) {

                    $_POST['banner'] = $nfn;
                    $data = array('banner' => $nfn);
		            $this->db->insert('banner', $data);
                }
            }
            $data['banner']=$this->banner_model->display();
        $this->load->view ('admin/banner',$data);
    }


     function del()
     {
        $id=$_GET['id'];
        $this->banner_model->delete($id);
        $data['banner'] = $this->index();
     }


}
    ?>