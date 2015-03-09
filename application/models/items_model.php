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
    
    function getCategoryMenukkkk ($parentid=0) 
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
                 $ret .= "<li><a href='#' onclick='return filtercategory1(".$item->id.");' >" . $item->catname."</a>";
                $ret .= $this->getCategoryMenu($item->id); // here is the recursion
            }
            else
            {
                $ret .= "<li><a href='#' onclick='return filtercategory1(".$item->id.");'>" . $item->catname."</a>";
                //$ret .= "<li><input type='submit' name='category' value='" . $item->id."'/>";
            }
            $ret .= "</li>";
        }
        $ret .= "</ul>";
        return $ret;
    }
    
	
	 function getCategoryMenu ($parentid=0) 
    {
    	$this->db->order_by('catname','asc');
        $this->db->where('parent_id',$parentid);
        $menus = $this->db->get('category')->result();
       
	   $ret =  "";
	   if($parentid==0)
	   {
	   
	    $ret = "<ul class='topmenu' id='css3menu1' >";
        }
		else
		{
			$ret = "<ul >";
		}
	  
	  
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
            	$count="<font color='red'>".number_format(count($hasitems))."</font>";
                 //$ret .= "<li ><a href='#' onclick='return filtercategory1(".$item->id.");' >" . $item->catname."</a>";
                 $ret .= "<li><a href='#' onclick='return filtercategory1(".$item->id.");'>
                   <span style='white-space:pre-wrap;'><b>" . $item->catname."(".$count.")</b><span></a>";
			   	   
			    $ret .= $this->getCategoryMenu($item->id); // here is the recursion
            }
            else
          {
            	$count="<font color='red'>".number_format(count($hasitems))."</font>";
                $ret .= "<li><a href='#' onclick='return filtercategory1(".$item->id.");'>
                 <span style='white-space:pre-wrap;'>" . $item->catname."(".$count.")<span></a>";
                //$ret .= "<li><input type='submit' name='category' value='" . $item->id."'/>";
            }
            $ret .= "</li>";
        }
        
        $ret .= "</ul>";
             
       
        return $ret;
    }
    
    
    
    function getManufacturerMenu () 
    {
    	
        $cnt = 0;
        $manufacturers = $this->db->order_by('title')->where('category','Manufacturer')->get('type')->result();
        
        $ret = "";  
        $retsub = "";  
	    $ret .= "<ul class='topmenu'  style='margin-top:-1em;' id='css3menu2' >";        
	    
	    foreach ($manufacturers as $man) 
        {
                    
        		$cquery = "SELECT m.*  						
					    FROM ".$this->db->dbprefix('item')." i join 
					    ".$this->db->dbprefix('masterdefault')." m on i.id = m.itemid and m.manufacturer = '".$man->id."' 
					     group by m.itemid";
        		$hasitems = $this->db->query($cquery)->result();
        		
            	//$hasitems = $this->db->where('manufacturer',$man->id)->get('masterdefault')->result();
        	    if(!$hasitems)
                continue;
                
            	$count="<font color='red'>".number_format(count($hasitems))."</font>";
            	$cnt += count($hasitems);
                $retsub .= "<ul><li><a href='#' onclick='return filtermanufacturer(".$man->id.");'>
                 <span style='white-space:pre-wrap;'>" . $man->title."(".$count.")<span></a></li></ul>";
                //$ret .= "<li><input type='submit' name='category' value='" . $item->id."'/>";
                        
        }
        $cntfont="<font color='red'>".number_format($cnt)."</font>";
        $ret .= "<li><a href='#'>
                 <span style='white-space:pre-wrap;'>Browse By Manufacturer(".$cntfont.") <span></a>";
        
        $ret .= $retsub;
        
        $ret .= "</li></ul>";
             
       
        return $ret;
    }
    
    
    
    function getDesignCategoryMenu ($parentid=0) 
    {
    	$this->db->order_by('catname','asc');
        $this->db->where('parent_id',$parentid);
        $menus = $this->db->get('designcategory')->result();	   
	   if($parentid==0)
	   {
	   
	    $ret = "<ul class='topmenu' id='css3menu1' >";
        }
		else
		{
			$ret = "<ul >";
		}
	  
	  
	    foreach ($menus as $item) 
        {
           $subcategories = $this->getDesignSubCategores($item->id,true);
           $hasitems = $this->db->where_in('category',$subcategories)->where('publish','1')->get('designbook')->result();
            
            if(!$hasitems)
                continue;
            $this->db->where('parent_id',$item->id);
            $submenus = $this->db->get('designcategory')->result();
            if ($submenus) 
            {
            	$count="<font color='red'>".number_format(count($hasitems))."</font>";
                 //$ret .= "<li ><a href='#' onclick='return filtercategory1(".$item->id.");' >" . $item->catname."</a>";
                 $ret .= "<li><a href='#' onclick='return filtercategory1(".$item->id.");'>
                   <span style='white-space:pre-wrap;'><b>" . $item->catname."(".$count.")</b><span></a>";
			   	   
			    $ret .= $this->getDesignCategoryMenu($item->id); // here is the recursion
            }
            else
          {
            	$count="<font color='red'>".number_format(count($hasitems))."</font>";
                $ret .= "<li><a href='#' onclick='return filtercategory1(".$item->id.");'>
                 <span style='white-space:pre-wrap;'>" . $item->catname."(".$count.")<span></a>";
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
        //$ret = "<ul>";
       
	   
	    if($parentid==0)
	   {
	   
	    $ret = "<ul class='topmenu' id='css3menu1' >";
        }
		else
		{
			$ret = "<ul >";
		}
	   
	   
	   
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
                //$ret .= "<li><a href='javascript:void(0)'>" . $item->catname."</a>";
                $ret .= "<li  class='topmenu'><a href='javascript:void(0)' onclick='return filtercategoryitems(".$item->id.");'><b>" . $item->catname."</b></a>";
               			   
			    $ret .= $this->getCategoryMenuItems($item->id); // here is the recursion
            }
            else
            {
                $ret .= "<li><a href='javascript:void(0)' onclick='return filtercategoryitems(".$item->id.");'> <span style='white-space:pre-wrap;line-height:20px;'>" . $item->catname."<span></a>";
                
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
        //$ret = "<ul>";
        
		
		 if($parentid==0)
	   {
	   
	    $ret = "<ul class='topmenu' id='css3menu1' >";
        }
		else
		{
			$ret = "<ul  style='width:300px;'>";
		}
		
		
		
		
		
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
            	$count="<font color='red'>".number_format(count($hasitems))."</font>";
                $ret .= "<li style='width:300px;'><a href='#' onclick='return filtercategory1(".$item->id.");'><b>" . $item->catname."(".$count.")</b></a>";
                $ret .= $this->getStoreCategoryMenu($supplier,$item->id); // here is the recursion
            }
            else
            {
               $count="<font color='red'>".number_format(count($hasitems))."</font>";	
               $ret .= "<li style='width:300px;'><a href='#' onclick='return filtercategory1(".$item->id.");'><span style='white-space:pre-wrap;'>" . $item->catname."(".$count.")</span></a>";
            }
            $ret .= "</li>";
        }
        $ret .= "</ul>";
        //die;
        return $ret;
    }
    
    
    
    function getStoreManufacturerMenu ($supplier) 
    {
        $cnt = 0;
        $manufacturers = $this->db->order_by('title')->where('category','Manufacturer')->get('type')->result();
        
        $ret = "";  
        $retsub = "";  
	    $ret .= "<ul class='topmenu'  style='margin-top:-1em;' id='css3menu2' >";        
	    
	    foreach ($manufacturers as $man) 
        {
                    
        		$cquery = "SELECT m.*  						
					    FROM ".$this->db->dbprefix('companyitem')." ci join ".$this->db->dbprefix('item')." i on ci.itemid=i.id join 
					    ".$this->db->dbprefix('masterdefault')." m on i.id = m.itemid where ci.company=$supplier AND type='Supplier' AND ci.instore='1' and  m.manufacturer = '".$man->id."' group by m.itemid";     		
        		
        		$hasitems = $this->db->query($cquery)->result();
        		
            	//$hasitems = $this->db->where('manufacturer',$man->id)->get('masterdefault')->result();
        	    if(!$hasitems)
                continue;
                
            	$count="<font color='red'>".number_format(count($hasitems))."</font>";
            	$cnt += count($hasitems);
                $retsub .= "<ul><li><a href='#' onclick='return filtermanufacturer(".$man->id.");'>
                 <span style='white-space:pre-wrap;'>" . $man->title."(".$count.")<span></a></li></ul>";
                //$ret .= "<li><input type='submit' name='category' value='" . $item->id."'/>";
                        
        }
        $cntfont="<font color='red'>".number_format($cnt)."</font>";
        $ret .= "<li><a href='#'>
                 <span style='white-space:pre-wrap;'>Browse By Manufacturer(".$cntfont.") <span></a>";
        
        $ret .= $retsub;
        
        $ret .= "</li></ul>";
             
       
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
    
     function getDesignSubCategores($catid,$includetop=true) 
    {
        $ret = array();
        if($includetop)
            $ret[]=$catid;
        $sub = $this->getalldesignsubcategories($catid);
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
    
    function getalldesignsubcategories($parent)
    {
        $this->db->where('parent_id',$parent);
        $sub = $this->db->get('designcategory')->result();
        $ret = array();
        foreach($sub as $s)
        {
            $ret[]=$s->id;
        }
        foreach($ret as $r)
        {
            $rs = $this->getalldesignsubcategories($r);
            $ret = array_merge($ret,$rs);
        }
        return $ret;
    }
    
    
    function getManufacturername($manufacturer)
    {
    	$this->db->where('id',$manufacturer);
    	$this->db->where('category','manufacturer');
        $manufacturername = @$this->db->get('type')->row()->title;    	
        
        if(!$manufacturername)
        	return '';
        	
        $ret = '<li onclick="filtermanufacturer('.$manufacturer.')"><a href="#">'.$manufacturername.'</a></li>';
       
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
    
    function getDesignParents($catid)
    {
        $cat = $this->db->where('id',$catid)->get('designcategory')->row();
        if(!$cat)
        	return '';
        $ret = '<li onclick="filtercategory('.$cat->id.')"><a href="#">'.$cat->catname.'</a></li>';//array($cat);
        $parent = $this->db->where('id',$cat->parent_id)->get('designcategory')->result();
        if($parent)
        {
            $rs = $this->getDesignParents($cat->parent_id);
            $ret = $rs.$ret;//array_merge($ret,$rs);
        }
        return $ret;
    }
    
    
    
    function getParentids($catid)
    {
        $cat = $this->db->where('id',$catid)->get('category')->row();
        if(!$cat)
        	return '';
        $ret = $cat->id;
        $parent = $this->db->where('id',$cat->parent_id)->get('category')->result();
        if($parent)
        {
            $rs = $this->getParentids($cat->parent_id);
            $ret = $rs.",".$ret;//array_merge($ret,$rs);
        }
        
        //$ret = array_reverse($ret);
        return $ret;
    }
    
    function getsubcategorynames($parentid=0) 
    {
    	$this->db->order_by('catname','asc');
        $this->db->where('parent_id',$parentid);
        $menus = $this->db->get('category')->result();
        $ret = "";
        foreach ($menus as $item) 
        {
            $subcategories = $this->getSubCategores($item->id,true);
            $hasitems = $this->db->where_in('category',$subcategories)->where('instore','1')->get('item')->result();
            
            if(!$hasitems)
                continue;
               $ret .= '<ul><li onclick="filtercategory('.$item->id.')"><a href="#">'.$item->catname.' &nbsp;&nbsp;('.count($hasitems).')</a></li></ul>';
                //$ret .= "<li><input type='submit' name='category' value='" . $item->id."'/>";
           
            
        }
    //    $ret .= "</ul>";
        return $ret;
    }
    
    function getdesignsubcategorynames($parentid=0) 
    {
    	$this->db->order_by('catname','asc');
        $this->db->where('parent_id',$parentid);
        $menus = $this->db->get('designcategory')->result();
        $ret = "";
        foreach ($menus as $item) 
        {
            $subcategories = $this->getDesignSubCategores($item->id,true);
            $hasitems = $this->db->where_in('category',$subcategories)->where('publish','1')->get('designbook')->result();
            
            if(!$hasitems)
                continue;
               $ret .= '<ul><li onclick="filtercategory('.$item->id.')"><a href="#">'.$item->catname.'</a></li></ul>';          
        }
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
	
	function getDesignTreeOptions($selected = '', $parent_id = 0, $level = 0)
	{
		static $temp = '';
		# retrieve all children of $parent
		$sql = "SELECT * FROM ".$this->db->dbprefix('designcategory')." WHERE parent_id = '{$parent_id}' ORDER BY catname ASC";
		
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
			$this->getDesignTreeOptions($selected, $row['id'], $level + 1);			
		} 
		return $temp;
	}
    
    public function find_item() 
    {
        $limit = 18;
        $return = new stdClass();

        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;

        $where = array();
        $leftmasterdefault = "";
        $where[]=" instore='1' ";
        if (@$_POST['category']) 
        {
            $slist = $this->getSubCategores($_POST['category']);
            $inclause = implode(',', $slist);
            $where[]=" (category IN ($inclause) OR ic.categoryid IN ($inclause) )";
            //$where[] = " category='{$_POST['category']}'";
        }
        
        if (@$_POST['manufacturer']) 
        {            
            $where[] = " m.manufacturer='{$_POST['manufacturer']}'";
            $leftmasterdefault = "left join " . $this->db->dbprefix('masterdefault') ." m on i.id = m.itemid";
        }
        
        $orderlookup = "";
        if ($where)
            $where = " WHERE " . implode(' AND ', $where) . " ";
        else
            $where = '';
        
        if($this->keyword){
            $lookup = " (`itemcode` like '%$this->keyword%' OR `itemname` like '%$this->keyword%' or `keyword` like '%$this->keyword%')";
            $orderlookup = " (`itemcode` like '%$this->keyword%' OR `itemname` like '%$this->keyword%')";
            
            $arrkeyword = explode(" ",$this->keyword);
            if(count($arrkeyword>1)){
            $lookup = " (`itemcode` like '%$this->keyword%' OR `itemname` like '%$this->keyword%' or `keyword` like '%$this->keyword%' ";
            $orderlookup = " (`itemcode` like '%$this->keyword%' OR `itemname` like '%$this->keyword%' ";
            
            $keyword = str_replace(' ', '', $this->keyword);
            $lookup .= " or `itemcode` like '%$keyword%' OR `itemname` like '%$keyword%' or `keyword` like '%$keyword%' ";
            
            foreach($arrkeyword as $keyw){
            	$lookup .= " or `itemcode` like '%$keyw%' OR `itemname` like '%$keyw%' or `keyword` like '%$keyw%' ";
            	//$orderlookup .= " or `itemcode` like '%$keyw%' OR `itemname` like '%$keyw%' ";
            } }
            
            $lookup .= " ) ";
            $orderlookup .= " ) ";
            
            if(trim($where)){
                $where .= " AND ".$lookup;
            }else{
                $where .= " WHERE ".$lookup;
            }
        }

        $query = "SELECT * FROM " . $this->db->dbprefix('item') . " i left join " . $this->db->dbprefix('item_category') ." ic on i.id = ic.itemid {$leftmasterdefault} ".$where;
        
        $return->totalresult = $this->db->query($query)->num_rows();
        
        if(@$orderlookup!="")
        $orderlookup = "order by ".$orderlookup." desc";
        
        $query = "SELECT * FROM " . $this->db->dbprefix('item') . " i left join " . $this->db->dbprefix('item_category') ." ic on i.id = ic.itemid {$leftmasterdefault} $where $orderlookup LIMIT $start, $limit";
        //echo $query;//die;
        $return->items = $this->db->query($query)->result();
        return $return;
    }
    
    
       public function find_design_item() 
    {
        $limit = 18;
        $return = new stdClass();

        if (!isset($_POST['pagenum']))
            $_POST['pagenum'] = 0;
        $start = $_POST['pagenum'] * $limit;

        $where = array();
        $where[]="publish='1'";
       
        if (@$_POST['category']) 
        {
            $slist = $this->getDesignSubCategores($_POST['category']);
            $inclause = implode(',', $slist);
            $where[]=" (category IN ($inclause) OR ic.categoryid IN ($inclause) )";
        }
        
        if ($where)
            $where = " WHERE " . implode(' AND ', $where) . " ";
        else
            $where = '';
        
        if($this->keyword){
            $lookup = " (`name` like '%$this->keyword%')";
            if(trim($where)){
                $where .= " AND ".$lookup;
            }else{
                $where .= " WHERE ".$lookup;
            }
        }

     	$query = "SELECT i.*,ic.* FROM " . $this->db->dbprefix('designbook') . " i 
        		  left join " . $this->db->dbprefix('designbook_category') ." ic on i.id = ic.itemid         		  
        		  left join " . $this->db->dbprefix('company') ." c on c.id = i.company    		  
        		  ".$where. " AND c.isdeleted = 0 ";      
        $return->totalresult = $this->db->query($query)->num_rows();
   
        $query = "SELECT i.*,ic.* FROM " . $this->db->dbprefix('designbook') . " i 
        		  left join " . $this->db->dbprefix('designbook_category') ." ic on i.id = ic.itemid 
        		  left join " . $this->db->dbprefix('company') ." c on c.id = i.company  AND c.isdeleted = 0
        		  $where AND c.isdeleted = 0 LIMIT $start, $limit";
        $return->items = $this->db->query($query)->result();
        return $return;
    }
    
    public function find_tags($keyword) 
    {
        $where = array();
               
         if($keyword){
            $where = " where `tags` like '%$keyword%'";
         }

        $query = "SELECT * FROM " . $this->db->dbprefix('item') . $where;
        
        $tags = $this->db->query($query)->result();
        $taggs = array();
        foreach($tags as $tag){
        	$tagarr = explode(",",$tag->tags);
        	foreach($tagarr as $tag2){
        		if (stristr($tag2,$keyword))
        		$taggs[] = $tag2;
        	}
        	
        }        
        return $taggs;
    }
    
    public function find_item_byTag($tag){
    	$limit = 18;
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
    	
    	$query = "SELECT * FROM " . $this->db->dbprefix('item') . $where."  AND tags like '%$tag%'";
    	
    	$return->totalresult = $this->db->query($query)->num_rows();
    	
    	$query = "SELECT * FROM " . $this->db->dbprefix('item') . $where ."  AND tags like '%$tag%' LIMIT $start, $limit";
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
	
	
	// Get Items
	function get_items($categoryId = null){
		 $this->db->select('id, itemname');
		 
		 if($categoryId != NULL){
			 $this->db->where('category', $categoryId);
		 }
		 
		 $query = $this->db->get('pms_item');
		 
		 $items = array();
		 
		 if($query->result()){
			 foreach ($query->result() as $item) {
				$items[$item->id] = $item->itemname;
			 }
			 return $items;
		 }else{
		 	return FALSE;
		 }
	}
	
	
	public function get_items2($categoryId) 
    {
        
        $where = array();
        
        if (@$categoryId)
        {
        	$this->db->where('parent_id',$categoryId);
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
        		$where []= "category = {$categoryId}";
        	}
        }
        if ($where)
           $where = " WHERE " . implode(' AND ', $where) . " ";
        
            $query = "SELECT id, itemname FROM ".$this->db->dbprefix('item').' i '. $where;
        	$result = $this->db->query($query)->result();
        $items = array();
		//echo "<pre>",print_r($result);  die;
		if($result){
			 foreach ($result as $item) {
				$items[$item->id] = $item->itemname;
			 } // echo "<pre>",print_r($items); die;
			 return $items;
		}else{
		 	return FALSE;
		}
        
    }
    
    
        public function get_items3($categoryId) 
    {
        
        $where = array();
        
        if (@$categoryId)
        {
        	$this->db->where('parent_id',$categoryId);
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
        		$where []= "category = {$categoryId}";
        	}
        }
        if ($where)
           $where = " WHERE " . implode(' AND ', $where) . " ";
        
            $query = "SELECT itemcode, itemname FROM ".$this->db->dbprefix('item').' i '. $where;
        	$result = $this->db->query($query)->result();
        $items = array();
		//echo "<pre>",print_r($result);  die;
		if($result){
			 foreach ($result as $item) {
				$items[$item->itemcode] = $item->itemname;
			 } // echo "<pre>",print_r($items); die;
			 return $items;
		}else{
		 	return FALSE;
		}
        
    }
    
    public function get_items4($categoryId) 
    {
        
        $where = array();
        
        if (@$categoryId)
        {
        	$this->db->where('parent_id',$categoryId);
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
        		$where []= "category = {$categoryId}";
        	}
        }
        if ($where)
           $where = " WHERE " . implode(' AND ', $where) . " ";
        
            $query = "SELECT id,itemcode, itemname FROM ".$this->db->dbprefix('item').' i '. $where;
        	$result = $this->db->query($query)->result();
        $items = array();
		//echo "<pre>",print_r($result);  die;
		if($result){
			 foreach ($result as $item) {
				$items[$item->id] = $item->itemcode;
			 } // echo "<pre>",print_r($items); die;
			 return $items;
		}else{
		 	return FALSE;
		}
        
    }
    
}
