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
 		
 		$sql ="SELECT q.id,q.ponum,aw.itemid,aw.itemcode,aw.itemname, SUM(aw.received) as qtyonhand, SUM(aw.quantity - aw.received) as qtyonpo, SUM(aw.quantity) quantity, SUM(aw.ea) as ea, SUM(aw.ea*aw.received) as valueonhand, Min(IF(aw.quantity > aw.received, aw.daterequested, NULL )) daterequested, Max(DATE_FORMAT(a.awardedon,'%m/%d/%Y')) as lastaward,'' as manage
				FROM
				".$this->db->dbprefix('quote')." q
				JOIN ".$this->db->dbprefix('award')." a ON a.quote = q.id 
				LEFT JOIN ".$this->db->dbprefix('awarditem')." aw ON aw.award = a.id				
				WHERE 1=1 AND q.purchasingadmin='".$this->session->userdata('purchasingadmin')."'  {$where} GROUP by aw.itemid ";
 		
 		$qry = $this->db->query($sql);		
 		$qryresult = $qry->result();
 		//echo "<pre>",print_r($qryresult); die;
 		if($qryresult){ $i=0;
 		
 		$whereproject = '';
 		if($this->session->userdata('managedprojectdetails') != '')
 		{
 			$whereproject .= " AND i.project = ".$this->session->userdata('managedprojectdetails')->id;
 		}
 		
 		foreach($qryresult as $q){
 			
 			$inventorysql = "select minstock, maxstock,reorderqty from ".$this->db->dbprefix('inventory')." i WHERE 1=1 AND i.purchasingadmin='".$this->session->userdata('purchasingadmin')."' {$whereproject} AND i.itemid = ".$q->itemid; 
 			$sqlq = $this->db->query($inventorysql);		
 			$qryinvresult = $sqlq->row();
 			
 			if($qryinvresult){ 			 				
 				$qryresult[$i]->minstock = $qryinvresult->minstock;
 				$qryresult[$i]->maxstock = $qryinvresult->maxstock;
 				$qryresult[$i]->reorderqty = $qryinvresult->reorderqty;
 				$i++;
 			}
 		}
 		}
 		return $qryresult;
 	}
}	