<?php

class contractcatcode_model extends Model {

    function contractcatcode_model() {
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
		$sql = "SELECT * FROM ".$this->db->dbprefix('contractcategory')." WHERE parent_id = '{$parent_id}' ORDER BY catname ASC";
		
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

    function SaveCategory() 
    {
        $options = array(
            'parent_id' => $this->input->post('parent_id'),
            'catname' => $this->input->post('catname'),
            //'banner_image'=>$image_name,
            'title'=>$this->input->post('catTitle'),
            'text'=>$this->input->post('catText')
        );
        $this->db->insert('contractcategory', $options);
        return $this->db->insert_id();
    }

    function checkDuplicateCat($catname, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'catname' => $catname));
        } else {
            $this->db->where('catname', $catname);
        }
        $query = $this->db->get('contractcategory');
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
		" . $this->db->dbprefix('contractcategory') . " where id =" . $id . " ORDER BY catname ASC";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            //var_dump($result);
            return $result;
        } else {
            return null;
        }
    }

    function updateCategory($data) {
        
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('contractcategory', $data);
    }

    function remove_category($id) 
    {
        $subcategories = $this->getSubCategores($id);
        $this->db->where_in('id', $subcategories);
        $this->db->delete('contractcategory');
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
    	$sub = $this->db->get('contractcategory')->result();
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

    function get_categories_tiered() {
        $this->db->order_by('catname', 'ASC');
        $categories = $this->db->get($this->db->dbprefix('contractcategory'))->result();
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
        $result = $this->db->get($this->db->dbprefix('contractcategory'));

        $categories = array();
        foreach ($result->result() as $cat) {
            $categories[] = $this->get_category($cat->id);
        }

        return $categories;
    }

    function get_category($id) {
        return $this->db->get_where($this->db->dbprefix('contractcategory'), array('id' => $id))->row();
    }

}

?>
