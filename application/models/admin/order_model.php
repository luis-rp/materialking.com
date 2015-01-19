<?php
class order_model extends Model
{
	function costcode_model()
	{
		parent::Model();
	}
	
	function get_order_by_costcode($costcode){

	$projectwhere = ""	;
	$mp = $this->session->userdata('managedprojectdetails');       
    if(@$mp->id){	
        	
			$projectwhere = " AND o.project='{$mp->id}'";
    }	
    
	$sql = "SELECT o.*, od.paymentnote FROM ".
						$this->db->dbprefix('order')." o,".
						$this->db->dbprefix('orderdetails')." od, ".
						$this->db->dbprefix('costcode')." cc".
						" WHERE o.costcode=cc.id AND o.id=od.orderid".
	 					" AND	 cc.code='".$costcode."' {$projectwhere}".
	 					" GROUP BY o.id";
	
				$query = $this->db->query ($sql);
				return $query->result();
		
	}
}
?>