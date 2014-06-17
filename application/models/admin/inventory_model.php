<?php
class Inventory_model extends Model 
{
	
	function Inventory_model() 
	{
		parent::Model ();
	}
	
	function getconfigurations()
	{
	    $query = $this->db->get ('settings' );
	    $result = $query->result ();
	    return $result [0];
	}
	
	
	function getItems($company)
	{
		$items = $this->db->get('item')->result();
		$ret = array();
		if($items)
		foreach($items as $item)
		{
			$this->db->where('itemid',$item->id);
			$this->db->where('company',$company);
			$this->db->where('type','Purchasing');
			$item->companyitem = $this->db->get('companyitem')->row();
			
			if(!$item->companyitem)
			{
				$insert = array();
				$insert['itemid'] = $item->id;
				$insert['itemid'] = $item->id;
				$insert['itemid'] = $item->id;
				$insert['itemid'] = $item->id;
				
			}
			$ret[]=$item;
		}
		//print_r($ret);die;
		return $ret;
	}
	
	function getitemdetails($itemid)
	{
		$this->db->where('id',$itemid);
		$ret = $this->db->get('item')->row();
		
		$this->db->where('itemid',$itemid);
		$this->db->where('company',$this->session->userdata('purchasingadmin'));
		$this->db->where('type','Purchasing');
		$ret->companyitem = $this->db->get('companyitem')->row();
		
		
			$ret->minprices = $this->getminimumprices($ret->id);
			$ret->poitems = $this->getpoitems($ret->id);
			
			// LAST QUOTED DATE
			$lastsql = "SELECT b.submitdate lastdate 
				FROM ".$this->db->dbprefix('biditem')." bi, ".$this->db->dbprefix('bid')." b
				WHERE b.id=bi.bid AND bi.itemid='".$ret->id."' 
				AND bi.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";
			//echo $lastsql;die;
			$lastquery = $this->db->query($lastsql);
			if($lastquery->num_rows>0)
				$ret->lastquoted = $lastquery->row()->lastdate;
			else
				$ret->lastquoted = '';
				

			// TARGET PRICE
			$sqlmin = "SELECT MIN(ea) minprice FROM ".$this->db->dbprefix('biditem')." 
						WHERE itemid='".$ret->id."' AND purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
			$minprice = $this->db->query($sqlmin)->row()->minprice;
			$sqlconfig ="SELECT * FROM ".$this->db->dbprefix('settings')." WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
			$queryconfig = $this->db->query ($sqlconfig);
			$pricepercent = $queryconfig->row()->pricepercent;
			
			$ret->targetprice = $minprice - ($minprice * $pricepercent / 100);
		
		//echo '<pre>';print_r($ret);die;
		return $ret;
	}
	
	function getminimumprices($itemid)
	{
		$sql = "SELECT c.title companyname, m.*
			   	FROM
				".$this->db->dbprefix('minprice')." m, 
				".$this->db->dbprefix('company')." c
				WHERE
				m.company=c.id AND m.itemid='$itemid'
				AND m.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
				";
		//echo $sql; die;
		$query = $this->db->query($sql);
		if($query->num_rows>0)
		{
			$result = $query->result ();
			$ret = array();
			foreach($result as $item)
			{
				$ret[]=$item;
			}
			return $ret;
		}
		return NULL;
	}
	
	function getpoitems($itemid)
	{
		$sql = "SELECT ai.*, c.title companyname, q.ponum, a.awardedon, a.quote
			   	FROM
				".$this->db->dbprefix('awarditem')." ai, ".$this->db->dbprefix('award')." a, 
				".$this->db->dbprefix('quote')." q, ".$this->db->dbprefix('company')." c
				WHERE
				ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."' AND
				ai.award=a.id AND a.quote=q.id AND ai.company=c.id AND ai.itemid='$itemid'
				ORDER BY a.awardedon DESC
				";
		//echo $sql;
		$query = $this->db->query($sql);
		if($query->num_rows>0)
		{
			$result = $query->result ();
			$ret = array();
			foreach($result as $item)
			{
				$ret[]=$item;
			}
			return $ret;
		}
		return NULL;
	}
	
	function getlowestquoteprice($itemid)
	{
		$sql = "SELECT MIN(ea) minprice FROM ".$this->db->dbprefix('biditem')." WHERE itemcode='$itemid'
		AND purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
		$row = $this->db->query($sql)->row();
		return $row->minprice;
	}
	
	function getdaysmeanprice($itemid)
	{
		$this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
		$pricedays = $this->db->get('settings')->row()->pricedays;
		$sql = "SELECT AVG(ea) avgprice FROM ".$this->db->dbprefix('awarditem')." ai, ".$this->db->dbprefix('award')." a
			  WHERE ai.itemid='$itemid' AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."' 
			  AND ai.award=a.id AND DATEDIFF(NOW(),a.awardedon) <=$pricedays
		";
		//die($sql);
		$row = $this->db->query($sql)->row();
		return $row->avgprice;
	}
}
?>