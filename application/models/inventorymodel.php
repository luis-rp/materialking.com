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
		$td = $this->db->dbprefix('dealitem');
		$where = " WHERE  1=1 ";
		$joinqty="";
		$dealqty="";
		
		if(@$_POST['activdeal'])
		{
		//$dealqty="JOIN (select * from $tq  where $tq.company=1  group by company,itemid) qty2 ON $tci.itemid=qty2.itemid";	
		$dealqty="JOIN ".$td." ON ($tci.company=$td.company AND $tci.itemid=$td.itemid)";		
		$where .= " AND dealactive='1' ";		   		
		}
		else 
		{
		$dealqty="";
		
		}
		
		//$this->db->limit($limit, $newoffset);
		if(@$_POST['searchitem'])
		{
		    $where .= " AND ($ti.itemname LIKE '%".$_POST['searchitem']."%' OR $ti.itemcode LIKE '%".$_POST['searchitem']."%' OR  $tci.itemname LIKE '%".$_POST['searchitem']."%' OR $tci.itemcode LIKE '%".$_POST['searchitem']."%') ";
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
		{$noleftjoin} JOIN $tci ON $tci.itemid=$ti.id $joinqty $dealqty AND $tci.company=$company AND $tci.type='Supplier'
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
		$td = $this->db->dbprefix('dealitem');
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
		
		$dealqty="";
		
		if(@$_POST['activdeal'])
		{
		//$dealqty="JOIN (select * from $tq  where $tq.company=1  group by company,itemid) qty2 ON $tci.itemid=qty2.itemid";	
		$dealqty= "JOIN ".$td." ON ($tci.company=$td.company AND $tci.itemid=$td.itemid)";		
		$where .= " AND dealactive='1' ";		   		
		}
		else 
		{
		$dealqty="";
		
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
		{$noleftjoin} JOIN $tci ON $tci.itemid=$ti.id $joinqty $dealqty AND $tci.company=$company AND $tci.type='Supplier'
		        $where";		
		
		$items = $this->db->query($sql)->result();		
		$total = count($items);
		//echo "<pre>"; print_r($total); die;
		return $total;
	}
	
	function getcategories()
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('category') . "
        		WHERE id NOT IN (SELECT distinct(parent_id) FROM " . $this->db->dbprefix('category') . ")";
        $leaves = $this->db->query($sql)->result();
        $ret = array();

        foreach($leaves as $leaf)
        {
            $parent = $leaf->parent_id;
            while($parent)
            {
                $sql = "SELECT * FROM " . $this->db->dbprefix('category') . " WHERE id='$parent'  ";
                $pcat = $this->db->query($sql)->row();
                if($pcat)
                {
                    $parent = $pcat->parent_id;
                    $leaf->catname = $pcat->catname . ' > ' . $leaf->catname;
                }
                else
                {
                    break 1;
                }
            }
         
           
            $sql1 = "SELECT * FROM " . $this->db->dbprefix('item') . " WHERE category='$leaf->id'  ";
           
            
            $item = $this->db->query($sql1)->result(); 
            $count=number_format(count($item));
            $leaf->catname .="(".$count.")";
            
            $ret[] = $leaf;
        }
        //echo '<pre>'; print_r($ret);//die;
        $this->aasort($ret, 'catname');
        //echo '<pre>'; print_r($ret);die;

        return $ret;

    }
     
	function aasort (&$array, $key)
	{
	    $sorter=array();
	    $ret=array();
	    reset($array);
	    foreach ($array as $ii => $va)
	    {
	        $sorter[$ii]=$va->$key;
	    }
	    $sortflag = 14;//SORT_NATURAL ^ SORT_FLAG_CASE;

	    asort($sorter, $sortflag );
	    foreach ($sorter as $ii => $va)
	    {
	        $ret[$ii]=$array[$ii];
	    }
	    $array=$ret;
	}
}
?>