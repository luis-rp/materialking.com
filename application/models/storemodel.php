<?php

class Storemodel extends Model 
{

    function Storemodel() 
    {
        parent::Model();
    }
    
    public function get_items($company, $manufacturer) 
    {
        $limit = 10;
        $return = new stdClass();

        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;

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
        }
        if ($manufacturer)
        {
            $where []= "ci.manufacturer='".$manufacturer."'";
        }
        
        if ($where)
            $where = " WHERE ci.itemid=i.id  AND " . implode(' AND ', $where) . " ";
        else
            $where = ' WHERE ci.itemid=i.id ';

        $query = "SELECT ci.*, i.url FROM " . $this->db->dbprefix('companyitem') .' ci, 
        							'.$this->db->dbprefix('item').' i, 
        							'.$this->db->dbprefix('company').' c '. $where;
        $return->totalresult = $this->db->query($query)->num_rows();
        $query = $query." LIMIT $start, $limit";
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
    
}

?>