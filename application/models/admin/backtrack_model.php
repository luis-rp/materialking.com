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
	
	
	
	function get_quoteswithoutprj($pid="")
	{
		$ret = array();
		
		$where = "";
		if($this->session->userdata('usertype_id')>1)
		$where = " and purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
		
		$sql2 ="SELECT *
		FROM
		".$this->db->dbprefix('project')." where 1=1 {$where}";

		if($pid){
			
			$sql2 ="SELECT *
		FROM
		".$this->db->dbprefix('project')." 
		WHERE 1=1 {$where} and id=".$pid;
		}
		
		$query2 = $this->db->query ($sql2);
		$result = $query2->result ();
		if ($result)
		{
			foreach($result as $res){
				$where = '';

				$sql ="SELECT *
		FROM
		".$this->db->dbprefix('quote')." 
		WHERE 
		pid='".$res->id."' 
		ORDER BY podate DESC";

				if($this->session->userdata('usertype_id')>1)
				{
					$sql ="SELECT *
			FROM
			".$this->db->dbprefix('quote')." 
			WHERE pid='".$res->id."' AND 
			purchasingadmin='".$this->session->userdata('purchasingadmin')."' 
			ORDER BY podate DESC";
				}
				
				$query = $this->db->query ($sql);
				if ($query->result ())
				{
					$ret[] = $query->result ();

				}

			}
		}
		
		if(!$ret)
			return array();				
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
				/*if($item->received>0)
					return true;*/
				if( (date('Y-m-d H:i:s', strtotime( @$item->daterequested."23:59:59")) < date('Y-m-d H:i:s')) && @($item->quantity-$item->received)>0 )
					return true;	
			
		}
		return false;
	}
	
}
?>