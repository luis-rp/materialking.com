<?php
class manufacturer_model extends Model
{
	function manufacturer_model()
	{
		parent::Model();
	}
	

 	function get_manufacturers($limit=0,$offset=0)
 	{		
	    if ($offset == 0) {
			$newoffset = 0;
		} else {
			$newoffset = $offset;
		}
		
		$sql ="SELECT *
		FROM
		".$this->db->dbprefix('manufacturer')." WHERE 1=1 ";

		$query = $this->db->query ($sql);
		if ($query->result ()) 
		{
			$result = $query->result ();
			$ret = array();
			foreach($result as $item)
			{
				$ret[] = $item;
			}
			//print_r($ret);die;
			return $ret;
		} 
		else 
		{
			return null;
		}
	}
	
	// counting total manufacturers
	function total_manufacturer()
	{
		$query = $this->db->count_all_results('manufacturer');
		return $query;
	}
	
	function SaveManufacturer()
	{
		$options = array(
			'title'=>$this->input->post('title')
		);
		$this->db->insert('manufacturer', $options);
		return $this->db->insert_id();
	}
	
	// updating cost code 
	function updateManufacturer($id)
	{
		$options = array(
			'title'=>$this->input->post('title')
		);
		$this->db->where('id', $this->input->post('id'));
		$this->db->update('manufacturer', $options);
	}
	
	
	// removing cost code
	function remove_manufacturer($id)
	{
		$item = $this->get_manufacturers_by_id($id);
		$this->db->where('id', $id);
		$this->db->delete('manufacturer');
	}
	
	// retrieve cost code by their id
	function get_manufacturers_by_id($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('manufacturer');
		if($query->num_rows>0)
		{
			$ret = $query->row();
	        return $ret;
		}
		return NULL;
	}
	
	function checkDuplicateTitle($title, $edit_id = 0)
	{
		if($edit_id > 0)
		{
		    $this->db->where(array('id !='=> $edit_id,'title'=>$title));
		}
		else
		{
			$this->db->where('title', $title);
		}
		$query = $this->db->get ('manufacturer' );
		$result = $query->result ();
		
	}
}
?>