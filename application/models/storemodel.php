<?php

class Storemodel extends Model 
{

    function Storemodel() 
    {
        parent::Model();
    }
    
    public function get_items($company, $manufacturer) 
    {
        $limit = 12;
        $return = new stdClass();
			
        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;

        $leftmasterdefault = "";
        $where = array();
        $where []= "ci.company=c.id";
        $where []= "c.username='$company'";
        $where []= "type='Supplier'";
        $where []= "ci.instore='1'";
        if (@$_POST['category'])
        {
        	$this->db->where('parent_id',$_POST['category']);
        	$menus = $this->db->get('category')->result();
        	$str= "";
        	if($menus) {
        	foreach ($menus as $item)
        	{
        		$subcategories = $this->getSubCategores($item->id,true);
				if($subcategories) {
        			
					$str .= implode(',', $subcategories);
					$str = $str.",";
				}
        	}
        	$str = substr($str,0,strlen($str)-1);
            $where []= "category in (".$str.")";
        	}else {
        		$where []= "category = {$_POST['category']}";
        	}
        }
        if ($manufacturer)
        {
            $where []= "ci.manufacturer='".$manufacturer."'";
        }
        
        if (@$_POST['manufacturer']) 
        {            
            $where[] = " m.manufacturer='{$_POST['manufacturer']}' and ci.itemid = m.itemid ";
            $leftmasterdefault = ", " . $this->db->dbprefix('masterdefault') ." m";
        }
        
        if ($where)
            $where = " WHERE ci.itemid=i.id  AND " . implode(' AND ', $where) . " ";
        else
            $where = ' WHERE ci.itemid=i.id ';

        $query = "SELECT ci.*, i.url FROM " . $this->db->dbprefix('companyitem') .' ci, 
        							'.$this->db->dbprefix('item').' i, 
        							'.$this->db->dbprefix('company').' c '.$leftmasterdefault.' '.$where;
        $return->totalresult = $this->db->query($query)->num_rows();
        $query = $query." AND ci.ea <>'' LIMIT $start, $limit";
        //echo $query;//die;
        $return->items = $this->db->query($query)->result();
        return $return;
    }
    
    
    function getSubCategores($catid,$includetop=true) 
    {
        $ret = array();
        if($includetop)
            $ret[]=$catid;
        $sub = $this->getallsubcategories($catid);
        $ret = array_merge($ret,$sub);
        return $ret;
    }
    
    
    function getallsubcategories($parent)
    {
        $this->db->where('parent_id',$parent);
        $sub = $this->db->get('category')->result();
        $ret = array();
        foreach($sub as $s)
        {
            $ret[]=$s->id;
        }
        foreach($ret as $r)
        {
            $rs = $this->getallsubcategories($r);
            $ret = array_merge($ret,$rs);
        }
        return $ret;
    }  
    
    function getsubcategorynames($parentid=0,$company) 
    {
    	$this->db->order_by('catname','asc');
        $this->db->where('parent_id',$parentid);
        $menus = $this->db->get('category')->result();
        $ret = "";
       
        $tci = $this->db->dbprefix('companyitem');
        $ti = $this->db->dbprefix('item');
        $tc = $this->db->dbprefix('company');
        foreach ($menus as $item) 
        {
            $subcategories = $this->getSubCategores($item->id,true);
            
            $query = "SELECT ci.*, i.url FROM $tci ci, $ti i
                        WHERE ci.itemid=i.id AND ci.company=$company AND type='Supplier' AND ci.instore='1' 
                        AND i.category IN (".implode(',', $subcategories).")";
           
            $hasitems = $this->db->query($query)->result();
           
            if(!$hasitems)
                continue;
            
            $ret .= '<ul><li onclick="filtercategorystore('.$item->id.')"><a href="#">'.$item->catname.'</a></li></ul>';    
        }
        
        
        
      //  $ret .= "</ul>";
        return $ret;
    }    
    
}

?>