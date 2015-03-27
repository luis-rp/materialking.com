<?php
class inventorymanagement_model extends Model
{
	function inventorymanagement_model()
	{
		parent::Model();
	}
	

 	function get_inventorydetails($itemid="")
 	{	
 		//echo '<pre>',print_r($this->session->all_userdata());die;
 		$where = '';
 		$whereo = '';
 		if($itemid!="") 		
 			$where .= " AND aw.itemid = ".$itemid;
 			
 		if($this->session->userdata('managedprojectdetails') != '')
 		{
 			$where .= " AND q.pid = ".$this->session->userdata('managedprojectdetails')->id;
 		}
 		
 		$sql ="SELECT aw.itemid as id,aw.itemid,i.itemcode,i.itemname, SUM(aw.received) as qtyonhand, SUM(aw.quantity - aw.received) as qtyonpo, SUM(aw.quantity) quantity, SUM(aw.ea) as ea, SUM(aw.ea*aw.received) as valueonhand, Min(IF(aw.quantity > aw.received, aw.daterequested, NULL )) daterequested, Max(DATE_FORMAT(a.awardedon,'%m/%d/%Y')) as lastaward,'' as manage, SUM(aw.ea*(aw.quantity - aw.received)) valuecomitted, i.item_img 
				FROM
				".$this->db->dbprefix('quote')." q
				JOIN ".$this->db->dbprefix('award')." a ON a.quote = q.id 
				LEFT JOIN ".$this->db->dbprefix('awarditem')." aw ON aw.award = a.id	
				LEFT JOIN ".$this->db->dbprefix('item')." i ON aw.itemid = i.id				
				WHERE 1=1 AND q.purchasingadmin='".$this->session->userdata('purchasingadmin')."'  {$where} GROUP by aw.itemid ";		
 		 		
 		if($this->session->userdata('managedprojectdetails') != '')
 		{
 			$whereo .= " AND o.project = ".$this->session->userdata('managedprojectdetails')->id;
 		}
 		
 		if($itemid!="") 		
 			$whereo .= " AND od.itemid = ".$itemid;
 		
 		$orderSql = "SELECT od.itemid as id,od.itemid, i.itemcode, i.itemname, SUM(IF(od.isreceived=1, od.quantity, 0)) as qtyonhand, SUM(IF(od.isreceived=0, od.quantity, 0)) as qtyonpo, SUM(od.quantity) quantity, SUM(od.price) as ea, SUM(od.price*IF(od.isreceived=1, od.quantity, 0)) as valueonhand, '' as daterequested, '' as lastaward ,'' as manage, SUM(od.price*IF(od.isreceived=0, od.quantity, 0)) as valuecomitted, i.item_img  FROM ".$this->db->dbprefix('order')." o left join
 		        			 ".$this->db->dbprefix('orderdetails')." od on o.id=od.orderid 
 		        			 JOIN ".$this->db->dbprefix('item')." i ON od.itemid = i.id				
		        			 AND o.purchasingadmin='".$this->session->userdata('purchasingadmin')."' {$whereo}  
		        			 GROUP BY od.itemid";
 		
 		$unionquery = "SELECT comb.itemid as id,comb.itemid, comb.itemcode, comb.itemname, SUM(comb.qtyonhand) qtyonhand, SUM(comb.qtyonpo) as qtyonpo, SUM(comb.quantity) as quantity, SUM(comb.ea) as ea, SUM(comb.valueonhand) as valueonhand, comb.daterequested as daterequested, comb.lastaward as lastaward , comb.manage as manage, SUM(comb.valuecomitted) as valuecomitted, comb.item_img as item_img from ( (".$sql.") union (".$orderSql.") ) comb group by comb.itemid ";
 		
 		$qry = $this->db->query($unionquery);		
 		$qryresult = $qry->result();
 		//echo "<pre>",print_r($qryresult); die; 		
 		
 		if($qryresult){ $i=0;
 		
 		$whereproject = '';
 		if($this->session->userdata('managedprojectdetails') != '')
 		{
 			$whereproject .= " AND i.project = ".$this->session->userdata('managedprojectdetails')->id;
 		}
 		
 		foreach($qryresult as $q){
 			
 			$inventorysql = "select minstock, maxstock,reorderqty, adjustedqty from ".$this->db->dbprefix('inventory')." i WHERE 1=1 AND i.purchasingadmin='".$this->session->userdata('purchasingadmin')."' {$whereproject} AND i.itemid = ".$q->itemid; 
 			$sqlq = $this->db->query($inventorysql);		
 			$qryinvresult = $sqlq->row();
 			
 			if($qryinvresult){ 			 				
 				$qryresult[$i]->minstock = $qryinvresult->minstock;
 				$qryresult[$i]->maxstock = $qryinvresult->maxstock;
 				$qryresult[$i]->reorderqty = $qryinvresult->reorderqty;
 				if($qryinvresult->adjustedqty>0){
 					$qryresult[$i]->qtyonhand = ($qryresult[$i]->qtyonhand - $qryinvresult->adjustedqty);
 					$qryresult[$i]->valueonhand = ($qryresult[$i]->valueonhand - ($qryinvresult->adjustedqty*$qryresult[$i]->ea));
 				}
 				
 			}else{
 				$qryresult[$i]->minstock = 0;
 				$qryresult[$i]->maxstock = 0;
 				$qryresult[$i]->reorderqty = 0;
 			}
 			$i++;
 		}
 		}
 		return $qryresult;
 	}
}	