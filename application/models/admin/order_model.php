<?php
class order_model extends Model
{
	function costcode_model()
	{
		parent::Model();
	}
	
	function get_order_by_costcode($costcode){
		
	$sql = "SELECT o.* FROM ".
						$this->db->dbprefix('order')." o,".
						$this->db->dbprefix('orderdetails')." od, ".
						$this->db->dbprefix('costcode')." cc".
						" WHERE o.costcode=cc.id AND o.id=od.id".
	 					" AND	 cc.code='".$costcode."'";
				$query = $this->db->query ($sql);
				return $query->result();
		
	}
}
?>