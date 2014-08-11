<?php
class banner_model extends Model
{
	function banner_model()
	{
		parent::Model();
		$this->load->database();
        $this->load->helper('url');
	}


	public function display()
	{
		$this->db->select('*');
		$query=  $this->db->get('banner');
		return $query->result();
	}

	public function delete($id)
	{
		$this->db->delete('banner', array('id' => $id));
	}

}
?>