<?php
class inventorymanagement_model extends Model
{
	function inventorymanagement_model()
	{
		parent::Model();
	}
	

 	function get_inventorydetails()
 	{	
 		//echo '<pre>',print_r($this->session->all_userdata());die;
 		$where = '';
 		if($this->session->userdata('managedprojectdetails') != '')
 		{
 			$where .= " AND q.pid = ".$this->session->userdata('managedprojectdetails')->id;
 		}
 		
 		$sql ="SELECT q.id,q.ponum,aw.itemcode,aw.itemname,r.quantity as qtyonhand,(aw.quantity - r.quantity) as qtyonpo,aw.quantity,aw.ea,aw.totalprice as valueonhand,aw.daterequested,DATE_FORMAT(a.awardedon,'%m/%d/%Y') as lastaward,'' as manage
				FROM
				".$this->db->dbprefix('quote')." q
				JOIN ".$this->db->dbprefix('award')." a ON a.quote = q.id 
				LEFT JOIN ".$this->db->dbprefix('awarditem')." aw ON aw.award = a.id
				LEFT JOIN ".$this->db->dbprefix('received')." r ON r.awarditem = aw.id
				WHERE 1=1 AND q.purchasingadmin='".$this->session->userdata('purchasingadmin')."'  {$where}";
 		
 		$qry = $this->db->query($sql);		
 		return $qry->result();
 	}
}	