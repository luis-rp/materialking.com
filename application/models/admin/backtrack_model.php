<?php
class backtrack_model extends Model
{
	function backtrack_model()
	{
		parent::Model();
	}
	

 	function get_quotes()
 	{
 		$where = '';
 		if(@$_POST['searchponum'])
 		    $where = " AND ponum='{$_POST['searchponum']}'";
		$sql ="SELECT *
		FROM
		".$this->db->dbprefix('quote')." 
		WHERE 
		pid='".$this->session->userdata('managedprojectdetails')->id."' 
		ORDER BY podate DESC";
		
		if($this->session->userdata('usertype_id')>1)
		{
			$sql ="SELECT *
			FROM
			".$this->db->dbprefix('quote')." 
			WHERE pid='".$this->session->userdata('managedprojectdetails')->id."' AND 
			purchasingadmin='".$this->session->userdata('purchasingadmin')."' $where
			ORDER BY podate DESC";
		}
		
		$ret = array();
		$query = $this->db->query ($sql);
		if ($query->result ()) 
		{
			$ret = $query->result ();
			
		} 
		return $ret;	
	}
	

 	function get_backtracks()
 	{
		$sql ="SELECT *
		FROM
		".$this->db->dbprefix('backtrack')." ORDER BY senton DESC";
		
		if($this->session->userdata('usertype_id')>1)
		{
			$sql ="SELECT *
			FROM
			".$this->db->dbprefix('backtrack')." 
			WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."' ORDER BY senton DESC";
		}
		
		$ret = array();
		$query = $this->db->query ($sql);
		if ($query->result ()) 
		{
			$ret = $query->result ();
			
		} 
		return $ret;	
	}
	
	function checkReceivedPartially($awardid)
	{
		$sql ="SELECT *
		FROM
		".$this->db->dbprefix('awarditem')." WHERE award='$awardid'";
		
		$ret = array();
		$query = $this->db->query ($sql);
		if ($query->result ()) 
		{
			foreach($query->result () as $item)
				if($item->received)
					return true;
			
		}
		return false;
	}
	
}
?>