<?php

class form_model extends Model
	{
    	function form_model()
    	{
        	parent::Model();
        	$this->load->database();
        	$this->load->helper('url');
        	$this->load->library('session');
    	}

    	public function create_field($data,$companyId)
    	{
    		$savedata = array('CompanyID'=>$companyId,
    						'FieldType'=>$_POST['type'],
    						'FieldValue'=>$_POST['frm_option'],
    						'Label'=>$_POST['label'],
    						);
			return $this->db->insert('pms_formbuilder', $savedata);

    	}

    	public function view_field($id)
    	{
			$query = $this->db->get_where('pms_formbuilder', array('CompanyID' => $id));
			return $query->result();
    	}

    	public function save_fields($data,$companyId)
    	{
    		foreach ($data['formfields'] as $key => $val)
    		{
    			if($val != '')
    			{
					$savedata = array('Value' =>$val);
					$where = array('CompanyID'=>$companyId,'Id'=>$key);
					$query = $this->db->update('pms_formbuilder', $savedata,$where);
    			}
			}
			return 1;
    	}
    }

?>