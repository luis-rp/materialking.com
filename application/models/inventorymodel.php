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
		if ($offset == 0) {
			$newoffset = 0;
		} else {
			$newoffset = $offset;
		}
		$ti = $this->db->dbprefix('item');
		$tci = $this->db->dbprefix('companyitem');
		$where = " WHERE  1=1 ";
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
		
		if(@$_POST['serachmyitem'])
		{
		    $where .= " AND ($tci.itemcode!='' OR $tci.itemname!='') ";
		    //$this->db->where('manufacturer', $_POST['manufacturer']);
		}
		
		$sql = "SELECT $ti.* FROM $ti 
				LEFT JOIN $tci ON $tci.itemid=$ti.id AND $tci.company=$company AND $tci.type='Supplier'
		        $where 
		        LIMIT $offset, $limit";
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
		$where = " WHERE  1=1 ";
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
		
		if(@$_POST['serachmyitem'])
		{
		    $where .= " AND ($tci.itemcode!='' OR $tci.itemname!='') ";
		    //$this->db->where('manufacturer', $_POST['manufacturer']);
		}
		
		$sql = "SELECT $ti.* FROM $ti 
				LEFT JOIN $tci ON $tci.itemid=$ti.id AND $tci.company=$company AND $tci.type='Supplier'
		        $where";
		$items = $this->db->query($sql)->result();
		$total = count($items);
		return $total;
	}
}
?>