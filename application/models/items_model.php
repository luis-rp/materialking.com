<?php

class items_model extends Model {

    private $search;
    private $keyword;

    function items_model() {
        parent::Model();
    }

    public function set_keyword($keyword) {
        $this->keyword = $keyword;
    }
    
    function getCategoryMenu ($parentid=0) 
    {
    	$this->db->order_by('catname','asc');
        $this->db->where('parent_id',$parentid);
        $menus = $this->db->get('category')->result();
        $ret = "<ul>";
        foreach ($menus as $item) 
        {
            $subcategories = $this->getSubCategores($item->id,true);
            $hasitems = $this->db->where_in('category',$subcategories)->where('instore','1')->get('item')->result();
            
            if(!$hasitems)
                continue;
            $this->db->where('parent_id',$item->id);
            $submenus = $this->db->get('category')->result();
            if ($submenus) 
            {
                 $ret .= "<li><a href='javascript:void(0)' ondblclick='return filtercategory1(".$item->id.");'>" . $item->catname."</a>";
                $ret .= $this->getCategoryMenu($item->id); // here is the recursion
            }
            else
            {
                $ret .= "<li><a href='javascript:void(0)' ondblclick='return filtercategory1(".$item->id.");'>" . $item->catname."</a>";
                //$ret .= "<li><input type='submit' name='category' value='" . $item->id."'/>";
            }
            $ret .= "</li>";
        }
        $ret .= "</ul>";
        return $ret;
    }
    
	function getCategoryMenuItems ($parentid=0) 
    {
    	$this->db->order_by('catname','asc');
        $this->db->where('parent_id',$parentid);
        $menus = $this->db->get('category')->result();
        $ret = "<ul>";
        foreach ($menus as $item) 
        {
            $subcategories = $this->getSubCategores($item->id,true);
            $hasitems = $this->db->where_in('category',$subcategories)->where('instore','1')->get('item')->result();
            
            if(!$hasitems)
                continue;
            $this->db->where('parent_id',$item->id);
            $submenus = $this->db->get('category')->result();
            if ($submenus) 
            {
                $ret .= "<li><a href='javascript:void(0)'>" . $item->catname."</a>";
                $ret .= $this->getCategoryMenuItems($item->id); // here is the recursion
            }
            else
            {
                $ret .= "<li><a href='javascript:void(0)' onclick='return filtercategoryitems(".$item->id.");'>" . $item->catname."</a>";
                //$ret .= "<li><input type='submit' name='category' value='" . $item->id."'/>";
            }
            $ret .= "</li>";
        }
        $ret .= "</ul>";
        return $ret;
    }
    
    function getStoreCategoryMenu ($supplier,$parentid=0) 
    {
        $this->db->where('parent_id',$parentid);
        $menus = $this->db->get('category')->result();
        $ret = "<ul>";
        
        $tci = $this->db->dbprefix('companyitem');
        $ti = $this->db->dbprefix('item');
        $tc = $this->db->dbprefix('company');
        foreach ($menus as $item) 
        {
            $subcategories = $this->getSubCategores($item->id,true);
            
            $query = "SELECT ci.*, i.url FROM $tci ci, $ti i
                        WHERE ci.itemid=i.id AND ci.company=$supplier AND type='Supplier' AND ci.instore='1' 
                        AND i.category IN (".implode(',', $subcategories).")";
            /*$hasitems = $this->db->from('item')->join('companyitem',"item.id=companyitem.id")
                ->where_in('category',$subcategories)->where('companyitem.instore','1')
                ->where('companyitem.type','Supplier')
                ->get()->result();*/
            $hasitems = $this->db->query($query)->result();
            //echo '<pre>';print_r($item);print_r($subcategories);print_r($query);echo '<br>';print_r($hasitems);
            if(!$hasitems)
                continue;
            $this->db->where('parent_id',$item->id);
            $submenus = $this->db->get('category')->result();
            if ($submenus) 
            {
                $ret .= "<li><a href='javascript:void(0)'>" . $item->catname."</a>";
                $ret .= $this->getStoreCategoryMenu($supplier,$item->id); // here is the recursion
            }
            else
            {
                $ret .= "<li><a href='javascript:void(0)' onclick='filtercategory1(".$item->id.");'>" . $item->catname."</a>";
            }
            $ret .= "</li>";
        }
        $ret .= "</ul>";
        //die;
        return $ret;
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
    
    function getParents($catid)
    {
        $cat = $this->db->where('id',$catid)->get('category')->row();
        if(!$cat)
        	return '';
        $ret = '<li onclick="filtercategory('.$cat->id.')"><a href="#">'.$cat->catname.'</a></li>';//array($cat);
        $parent = $this->db->where('id',$cat->parent_id)->get('category')->result();
        if($parent)
        {
            $rs = $this->getParents($cat->parent_id);
            $ret = $rs.$ret;//array_merge($ret,$rs);
        }
        
        //$ret = array_reverse($ret);
        return $ret;
    }
    
	function getTreeOptions($selected = '', $parent_id = 0, $level = 0)
	{
		static $temp = '';
		# retrieve all children of $parent
		$sql = "SELECT * FROM ".$this->db->dbprefix('category')." WHERE parent_id = '{$parent_id}' ORDER BY catname ASC";
		
  		$result = $this->db->query($sql)->result();			
		# display each child		
		foreach($result as $row)
		{
		    $row = (array)$row;
			if($row['parent_id'] == 0)
			{				
				$opt_style = "";//"style = \"BACKGROUND-COLOR: #EEEEEE;COLOR: #136C99;FONT-SIZE: 11px;FONT-WEIGHT: bold;\"";
			}
			else
			{
				$opt_style = "";
			}
			if($row['id'] == $selected)
			{
				$is_selected = 'selected="selected"';
			}
			else
			{
				$is_selected = "";
			}
			$separator = str_repeat("&raquo;&nbsp;", $level);
			$temp .= "\t<option value=\"{$row['id']}\" {$opt_style} {$is_selected}> {$separator} {$row['catname']}</option>\r\n";				
			$this->getTreeOptions($selected, $row['id'], $level + 1);			
		} 
		return $temp;
	}
    
    public function find_item() 
    {
        $limit = 6;
        $return = new stdClass();

        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;

        $where = array();
        $where[]=" instore='1' ";
        if (@$_POST['category']) 
        {
            $slist = $this->getSubCategores($_POST['category']);
            $inclause = implode(',', $slist);
            $where[]=" category IN ($inclause)";
            //$where[] = " category='{$_POST['category']}'";
        }
        
        if ($where)
            $where = " WHERE " . implode(' AND ', $where) . " ";
        else
            $where = '';
        
        if($this->keyword){
            $lookup = " (`itemcode` like '%$this->keyword%' OR `itemname` like '%$this->keyword%' or `keyword` like '%$this->keyword%')";
            if(trim($where)){
                $where .= " AND ".$lookup;
            }else{
                $where .= " WHERE ".$lookup;
            }
        }

        $query = "SELECT * FROM " . $this->db->dbprefix('item') . $where;
        
        $return->totalresult = $this->db->query($query)->num_rows();
        
        $query = "SELECT * FROM " . $this->db->dbprefix('item') . " $where LIMIT $start, $limit";
        //echo $query;//die;
        $return->items = $this->db->query($query)->result();
        return $return;
    }

     function save_amazon($args) {
        
        $this->db->where('item_id', $args['item_id']);
        $this->db->delete('amazon_products');

        //foreach ($data as $key => $value) {
            //$this->db->set($key, $value);
        //}
        //echo '<pre>';
        //print_r($args);
        $this->db->insert('amazon_products',$args);
        //print_r($arr);
        //die;
    }

    function get_amazon($item_id) {
        $this->db->where('item_id', $item_id);
        $result = $this->db->get($this->db->dbprefix('amazon_products'));
        if($result->num_rows()){
            return $result->row();
        }else{
            return false;
        }
    }
    
}
