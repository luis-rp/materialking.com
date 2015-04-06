<?php

class catcode_model extends Model {

    function catcode_model() {
        parent::Model();
    }

    function get_catcodes($limit = 0, $offset = 0) 
    {
        return $this->get_categories_tiered();
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
				$opt_style = "style = \"BACKGROUND-COLOR: #EEEEEE;COLOR: #136C99;FONT-SIZE: 11px;FONT-WEIGHT: bold;\"";
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
				$opt_style = "style = \"BACKGROUND-COLOR: #EEEEEE;COLOR: #136C99;FONT-SIZE: 11px;FONT-WEIGHT: bold;\"";
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

    function SaveCategory($image_name="") 
    {
        $options = array(
            'parent_id' => $this->input->post('parent_id'),
            'catname' => $this->input->post('catname'),
            'banner_image'=>$image_name,
            'title'=>$this->input->post('catTitle'),
            'categoryurl'=>$this->input->post('categoryurl'),
            'text'=>$this->input->post('catText')
        );
        $this->db->insert('category', $options);
        return $this->db->insert_id();
    }

    function SaveDesignCategory() 
    {
        $options = array(
            'parent_id' => $this->input->post('parent_id'),
            'catname' => $this->input->post('catname')         
        );
        $this->db->insert('designcategory', $options);
        return $this->db->insert_id();
    }
    
    function checkDuplicateCat($catname, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'catname' => $catname));
        } else {
            $this->db->where('catname', $catname);
        }
        $query = $this->db->get('category');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    
    function checkDuplicateCaturl($categoryurl, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'categoryurl' => $categoryurl));
        } else {
            $this->db->where('categoryurl', $categoryurl);
        }
        $query = $this->db->get('category');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    
    function checkDesignDuplicateCat($catname, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'catname' => $catname));
        } else {
            $this->db->where('catname', $catname);
        }
        $query = $this->db->get('designcategory');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function get_catcodes_by_id($id) {
        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('category') . " where id =" . $id . " ORDER BY catname ASC";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            //var_dump($result);
            return $result;
        } else {
            return null;
        }
    }
    
     function get_designcatcodes_by_id($id) {
        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('designcategory') . " where id =" . $id . " ORDER BY catname ASC";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            return $result;
        } else {
            return null;
        }
    }

    function updateCategory($data) {
        
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('category', $data);
    }
    
     function updateDesignCategory($data) {
        
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('designcategory', $data);
    }

    function remove_category($id) 
    {
        $subcategories = $this->getSubCategores($id);
        $this->db->where_in('id', $subcategories);
        $this->db->delete('category');
    }
    
     function remove_designcategory($id) 
    {
        $subcategories = $this->getSubDesignCategores($id);
        $this->db->where_in('id', $subcategories);
        $this->db->delete('designcategory');
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
    
    function getSubDesignCategores($catid,$includetop=true)
    {
    	$ret = array();
    	if($includetop)
    		$ret[]=$catid;
    	$sub = $this->getallsubdesigncategories($catid);
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
    
    function getallsubdesigncategories($parent)
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
    		$rs = $this->getallsubdesigncategories($r);
    		$ret = array_merge($ret,$rs);
    	}
    	return $ret;
    }

    function get_categories_tiered() {
        $this->db->order_by('catname', 'ASC');
        $categories = $this->db->get($this->db->dbprefix('category'))->result();
        $results = array();
        foreach ($categories as $category) {
            $results[$category->parent_id][$category->id] = $category;
        }

        return $results;
    }
    
    function get_designcategories_tiered() {
        $this->db->order_by('catname', 'ASC');
        $categories = $this->db->get($this->db->dbprefix('designcategory'))->result();
        $results = array();
        foreach ($categories as $category) {
            $results[$category->parent_id][$category->id] = $category;
        }

        return $results;
    }

    function get_categories($parent = false) {
        if ($parent !== false) {
            $this->db->where('parent_id', $parent);
        }
        $this->db->select('id');

        $this->db->order_by('catname', 'ASC');
        $result = $this->db->get($this->db->dbprefix('category'));

        $categories = array();
        foreach ($result->result() as $cat) {
            $categories[] = $this->get_category($cat->id);
        }

        return $categories;
    }

    function get_category($id) {
        return $this->db->get_where($this->db->dbprefix('category'), array('id' => $id))->row();
    }
    
     function get_designcategory($id) {
        return $this->db->get_where($this->db->dbprefix('designcategory'), array('id' => $id))->row();
    }

}

?>
