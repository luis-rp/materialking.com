<?php

class form_subscription_model extends Model
	{
    	function form_subscription_model()
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
    						'Name'=>$_POST['name'],
    						);
			return $this->db->insert('pms_formsubscription', $savedata);

    	}

    	public function view_field($id)
    	{
			$query = $this->db->get_where('pms_formsubscription', array('CompanyID' => $id));
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
					$query = $this->db->update('pms_formsubscription', $savedata,$where);
    			}
			}
			return 1;
    	}
    	
    	public function delete_field($id)
    	{
    		$where = array('Id'=>$id);
			$query = $this->db->delete('pms_formsubscription',$where);
			return 1;
    	}

    	public function delete_allfield($id)
    	{
    		$where = array('CompanyID'=>$id);
			$query = $this->db->delete('pms_formsubscription',$where);
			return 1;
    	}
    }

?>