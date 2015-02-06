<?php
class Inventorymodel extends Model 
{
	
	function Inventorymodel() 
	{
		parent::Model ();
	}
	
	function getconfigurations()
	{
	    $query = $this->db->get ('settings' );
	    $result = $query->result ();
	    return $result [0];
	}
	
	function getItems($company, $limit = 10, $offset = 0)
	{
		//echo "<pre>"; print_r($_POST['filteroption']); die;
		if ($offset == 0) {
			$newoffset = 0;
		} else {
			$newoffset = $offset;
		}
		$ti = $this->db->dbprefix('item');
		$tci = $this->db->dbprefix('companyitem');
		$tq = $this->db->dbprefix('qtydiscount');
		$where = " WHERE  1=1 ";
		$joinqty="";
		//$this->db->limit($limit, $newoffset);
		if(@$_POST['searchitem'])
		{
		    $where .= " AND ($ti.itemname LIKE '%".$_POST['searchitem']."%' OR $ti.itemcode LIKE '%".$_POST['searchitem']."%') ";
		    //$this->db->like('itemname', $_POST['searchitem']);
		    //$this->db->or_like('itemcode', $_POST['searchitem']);
		}
		if(@$_POST['category'])
		{
		    $where .= " AND category='".$_POST['category']."' ";
		    //$this->db->where('category', $_POST['category']);
		}
		if(@$_POST['manufacturer'])
		{
		    $where .= " AND manufacturer='".$_POST['manufacturer']."' ";
		    //$this->db->where('manufacturer', $_POST['manufacturer']);
		}
		$noleftjoin = "LEFT";
		if(@$_POST['serachmyitem'])
		{
		   // $where .= " AND ($tci.itemcode!='' OR $tci.itemname!='') ";
		   $noleftjoin = ""; 
		}
		
		if(@$_POST['filteroption'])
		{
		    	if(@$_POST['filteroption']=='backorder')
				{
		   		$where .= " AND $tci.backorder='1' ";
				}
				
				if(@$_POST['filteroption']=='shipfrom')
				{
		   		$where .= " AND $tci.shipfrom='1' ";
				}
				
				if(@$_POST['filteroption']=='qtydiscount')
				{
				$joinqty="JOIN (select * from $tq  where $tq.company=1  group by company,itemid) qty2 ON $tci.itemid=qty2.itemid";		   		
				}
				else 
				{
				$joinqty="";
				}
				
				if(@$_POST['filteroption']=='serachmyitem')
				{
		   		  $where .= " AND $tci.instore='1'";
				}
				
				if(@$_POST['filteroption']=='isfeature')
				{
		   		$where .= " AND $tci.isfeature='1' ";
				}
		}
		
		 $sql = "SELECT $ti.* FROM $ti 
		{$noleftjoin} JOIN $tci ON $tci.itemid=$ti.id $joinqty AND $tci.company=$company AND $tci.type='Supplier'
		        $where 
		        LIMIT $offset, $limit";
		
		/*echo $sql = "SELECT $ti.* FROM $ti 
		{$noleftjoin} JOIN $tci ON $tci.itemid=$ti.id AND $tci.company=$company AND $tci.type='Supplier'
		        $where 
		        LIMIT $offset, $limit";*/
			
		//$this->db->where('item.instore',1);
		//$items = $this->db->from('item')->join('companyitem', 'item.id=companyitem.itemid', 'left')->get()->result();
		//echo $sql;
		$items = $this->db->query($sql)->result();
		
		$ret = array();
		if($items)
		foreach($items as $item)
		{
			$this->db->where('itemid',$item->id);
			$this->db->where('company',$company);
			$this->db->where('type','Supplier');
			$item->companyitem = $this->db->get('companyitem')->row();
			$ret[]=$item;
		}
		//print_r($ret);die;
		return $ret;
	}

	
	function count_all($company) 
	{
		$ti = $this->db->dbprefix('item');
		$tci = $this->db->dbprefix('companyitem');
		$tq = $this->db->dbprefix('qtydiscount');
		$where = " WHERE  1=1 ";
		//$noleftjoin = "";
		$joinqty="";
		//$this->db->limit($limit, $newoffset);
			
		if(@$_POST['searchitem'])
		{
		    $where .= " AND ($ti.itemname LIKE '%".$_POST['searchitem']."%' OR $ti.itemcode LIKE '%".$_POST['searchitem']."%') ";
		    //$this->db->like('itemname', $_POST['searchitem']);
		    //$this->db->or_like('itemcode', $_POST['searchitem']);
		}
		if(@$_POST['category'])
		{
		    $where .= " AND category='".$_POST['category']."' ";
		    //$this->db->where('category', $_POST['category']);
		}
		if(@$_POST['manufacturer'])
		{
		    $where .= " AND manufacturer='".$_POST['manufacturer']."' ";
		    //$this->db->where('manufacturer', $_POST['manufacturer']);
		}
		
		$noleftjoin = "LEFT";
		//$noleftjoin = "";
		if(@$_POST['serachmyitem'])
		{
		    //$where .= " AND ($tci.itemcode!='' OR $tci.itemname!='') ";
		    //$this->db->where('manufacturer', $_POST['manufacturer']);
		    $noleftjoin = ""; 
		}		
		
		if(@$_POST['filteroption'])
		{
		    	if(@$_POST['filteroption']=='backorder')
				{
		   		$where .= " AND $tci.backorder='1' ";
				}
				
				if(@$_POST['filteroption']=='shipfrom')
				{
		   		$where .= " AND $tci.shipfrom='1' ";
				}
				
				if(@$_POST['filteroption']=='qtydiscount')
				{
				$joinqty="JOIN (select * from $tq  where $tq.company=1  group by company,itemid) qty2 ON $tci.itemid=qty2.itemid";		   		
				}
				else 
				{
				$joinqty="";
				}
				
				if(@$_POST['filteroption']=='serachmyitem')
				{
		   		  $where .= " AND $tci.instore='1'";
				}
				
				if(@$_POST['filteroption']=='isfeature')
				{
		   		$where .= " AND $tci.isfeature='1' ";
				}
		}	
		$sql = "SELECT $ti.* FROM $ti 
		{$noleftjoin} JOIN $tci ON $tci.itemid=$ti.id $joinqty AND $tci.company=$company AND $tci.type='Supplier'
		        $where";		
		
		$items = $this->db->query($sql)->result();		
		$total = count($items);
		//echo "<pre>"; print_r($total); die;
		return $total;
	}
}
?>