<?php
class settings_model extends Model
{
	function settings_model()
	{
		parent::Model();
	}

 	function get_setting_by_id($id)
 	{
		$sql ="SELECT *
		FROM
		".$this->db->dbprefix('settings')." WHERE id='$id'";

		$query = $this->db->query ($sql);
		if($query->num_rows>0)
		{
			$ret = $query->row();
			$ret->taxpercent = $ret->taxrate;
	        return $ret;
		}
		return NULL;
	}

 	function get_setting_by_admin($id)
 	{
		$sql ="SELECT *
		FROM
		".$this->db->dbprefix('settings')." WHERE purchasingadmin='$id'";

		$query = $this->db->query ($sql);
		if($query->num_rows>0)
		{
			$ret = $query->row();
			$ret->taxpercent = $ret->taxrate;
	        return $ret;
		}
		return NULL;
	}

	function getalldata($id)
	{
		$query=$this->db->get_where('settings', array('purchasingadmin' => $id));
		return $query->result();
	}

 	function get_current_settings()
 	{
 	    $pa = $this->session->userdata('purchasingadmin');
 	    if(!$pa)
 	        $pa = 1;
		$sql ="SELECT * FROM
		".$this->db->dbprefix('settings')."
		WHERE purchasingadmin='".$pa."' ";

		$query = $this->db->query ($sql);
		if($query->num_rows>0)
		{
			$ret = $query->row();
			$ret->taxpercent = $ret->taxrate;
	        return $ret;
		}
		return NULL;
	}

	// counting total
	function total_settings()
	{
		$query = $this->db->count_all_results('settings');
		return $query;
	}

	// updating
	function updatesettings($id)
	{
		$updatedata=$this->db->get_where('settings',array('purchasingadmin'=>$this->session->userdata('purchasingadmin')))->row();
		$miles="";
		if($this->input->post('miles')!="")
		{
		$miles=preg_replace("/[a-zA-Z]/", "", $this->input->post('miles'));
		$miles=explode(",",$miles);
		$miles=array_filter($miles);
		$miles=implode(",",$miles);
    	$miles = str_replace("\n",",",$miles);
		}
		$options = array(
			'taxrate'=>$this->input->post('taxrate'),
          	'adminemail'=>$this->input->post('adminemail'),
          	'pricedays'=>$this->input->post('pricedays'),
          	'pricepercent'=>"",
          	'tour'=>$this->input->post('tour'),
          	'pagetour'=>$this->input->post('pagetour'),
          	'timezone'=>$this->input->post('timezone'),
          	'logo'=>"",
          	'miles'=>$miles
		);

		$this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
		$this->db->update('settings', $options);
	}
}
?>