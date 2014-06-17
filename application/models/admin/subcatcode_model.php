<?php
class subcatcode_model extends Model
{
  function subcatcode_model()
	{
		parent::Model();
	}
  function get_subcatcodes($limit=0,$offset=0){
      if ($offset == 0) {
			$newoffset = 0;
		} else {
			$newoffset = $offset;
		}
                 $sql ="SELECT a.id,a.subcategory,a.category,b.catname
		FROM
		".$this->db->dbprefix('subcategory')." a left join
                     ".$this->db->dbprefix('category')." b on a.category=b.id ORDER BY a.id ASC";

		$query = $this->db->query ($sql);
                if ($query->result ())
		{
			$result = $query->result ();
                      //  var_dump($result);
			return $result;
                }else
		{
			return null;
		}
  }


  function listHeirarchicalCombo($parent_id = 0, $level = 0, $selected = ''){
		static $temp = '';
		# retrieve all children of $parent
		$sql = "SELECT * FROM ".$this->db->dbprefix('category')." WHERE 1=1 ORDER BY catname ASC";

  		$result = $this->db->query($sql)->result();
		# display each child
		if($result)
		foreach($result as $row)
		{
			$row = (array)$row;
//			if($row['parent'] == 0){
//				$opt_style = "style = \"BACKGROUND-COLOR: #EEEEEE;COLOR: #136C99;FONT-SIZE: 11px;FONT-WEIGHT: bold;\"";
//			}else{
//				$opt_style = "";
//			}
                        $opt_style = "";
                        //$opt_style = "style = \"BACKGROUND-COLOR: #EEEEEE;COLOR: #136C99;FONT-SIZE: 11px;FONT-WEIGHT: bold;\"";
                      //var_dump($selected);
                        //echo $row['id'].' =='. $selected;
			if($row['id'] == $selected){
				$is_selected = 'selected="selected"';
			}else{
				$is_selected = "";
			}
			//$separator = str_repeat("&raquo;&nbsp;", $level);
			$temp .= "<option value=\"{$row['id']}\" {$is_selected}>{$row['catname']}</option>";
			//$this->listHeirarchicalCombo($row['id'], $level + 1, $selected);
		}//exit;
            
		return $temp;
	}

  function SaveCategory()
	{
            //var_dump($this->input->post('subcategory')); exit;
		$options = array(
			'subcategory'=>$this->input->post('subcategory'),
                        'category'=>$this->input->post('category')
	);
		$this->db->insert('subcategory', $options);
		return $this->db->insert_id();
	}

  function checkDuplicateCat($subcategory, $edit_id = 0)
	{
		if($edit_id > 0)
		{
		    $this->db->where(array('id !='=> $edit_id,'subcategory'=>$subcategory));
		}
		else
		{
			$this->db->where('subcategory',$subcategory);
		}
		$query = $this->db->get('subcategory');
		$result = $query->result();

	    if($query->num_rows>0)
	    {
                 return true;
	    }
	    else
	    {
	         return false;
	    }
	}
  function get_subcatcodes_by_id($id){
      $sql ="SELECT *
		FROM
		".$this->db->dbprefix('subcategory')." where id =".$id." ORDER BY subcategory ASC";

		$query = $this->db->query ($sql);
                if ($query->result ())
		{
			$result = $query->result ();
                        //var_dump($result);
			return $result;
                }else
		{
			return null;
		}
  }
  function updateCategory(){
      $options = array('subcategory'=>$this->input->post('subcategory'),'category'=>$this->input->post('category'),);
      $this->db->where('id', $this->input->post('id'));
      $this->db->update('subcategory', $options);
  }
  function remove_category($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('subcategory');
        }

}
?>
