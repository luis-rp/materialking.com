<?php
class project_model extends Model
{
	function project_model()
	{
		parent::Model();
	}
	

 	function get_projects($limit=0,$offset=0)
 	{		
	    if ($offset == 0) {
			$newoffset = 0;
		} else {
			$newoffset = $offset;
		}
		
		$sql ="SELECT id, purchasingadmin, title, description, address, date_format(`startdate`, '%m/%d/%Y')  as startdate  
		FROM ".$this->db->dbprefix('project')." ORDER BY title";
		
		if($this->session->userdata('usertype_id')>1)
		{
			$sql ="SELECT id, purchasingadmin, title, description, address, date_format(`startdate`, '%m/%d/%Y')  as startdate 
			FROM ".$this->db->dbprefix('project')." WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."' ORDER BY title";
		}
		
		$query = $this->db->query ($sql);
		if ($query->result ()) {
			return $query->result ();
		} else {
			return null;
		}	
	}
	
	// counting total projects
	function total_project()
	{
		$query = $this->db->count_all_results('project');
		return $query;
	}
	
	function SaveProject()
	{
		$options = array(
			'title'=>$this->input->post('title'),
			'description'=>$this->input->post('description'),
			'address'=>$this->input->post('address'),
			'startdate'=>$this->input->post('startdate')
		);
		$options['purchasingadmin'] = $this->session->userdata('purchasingadmin');
		$this->db->insert('project', $options);
		return $this->db->insert_id();
	}
	
	// updating pricing column
	function updateProject($id)
	{
		$options = array(
			'title'=>$this->input->post('title'),
			'description'=>$this->input->post('description'),
			'address'=>$this->input->post('address'),
			'startdate'=>$this->input->post('startdate')
		);
		
		$this->db->where('id', $this->input->post('id'));
		$this->db->update('project', $options);
	}
	
	
	// removing product
	function remove_project($id)
	{
		$delsql = array();
		$delsql[] = "DELETE FROM ".$this->db->dbprefix('project')." WHERE id='$id'";
		$delsql[] = "DELETE FROM ".$this->db->dbprefix('quote')." WHERE pid='$id'";
		
		$quotesql = "SELECT * FROM ".$this->db->dbprefix('quote')." WHERE pid='$id'";
		$quotequery = $this->db->query ($quotesql);
		$quotes = $quotequery->result();
		foreach($quotes as $quote)
		{
			$delsql[] = "DELETE FROM ".$this->db->dbprefix('quoteitem')." WHERE quote='".$quote->id."'";
			$delsql[] = "DELETE FROM ".$this->db->dbprefix('bid')." WHERE quote='".$quote->id."'";
			$delsql[] = "DELETE FROM ".$this->db->dbprefix('invitation')." WHERE quote='".$quote->id."'";
			
			$bidsql = "SELECT * FROM ".$this->db->dbprefix('bid')." WHERE quote='".$quote->id."'";
			$bidquery = $this->db->query ($bidsql);
			$bids = $bidquery->result();
			foreach($bids as $bid)
			{
				$delsql[] = "DELETE FROM ".$this->db->dbprefix('biditem')." WHERE bid='".$bid->id."'";
				$delsql[] = "DELETE FROM ".$this->db->dbprefix('award')." WHERE bid='".$bid->id."'";
			}
		}
		foreach($delsql as $sql)
		{
			$this->db->query($sql);
		}
		
	}
	
	// retrieve product by their id
	function get_projects_by_id($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('project');
		if($query->num_rows>0)
		{
			$ret = $query->row();
	        return $ret;
		}
		return NULL;
	}
	
	function checkDuplicateName($name, $edit_id = 0)
	{
		if($edit_id > 0)
		{
		    $this->db->where(array('id !='=> $edit_id,'title'=>$name,'purchasingadmin'=> $this->session->userdata('purchasingadmin') ));
		}
		else
		{
			$this->db->where(array('title'=>$name,'purchasingadmin'=> $this->session->userdata('purchasingadmin') ));
		}
		$query = $this->db->get ('project' );
		$result = $query->result ();
		
	    if($query->num_rows>0)
	    {
    	    return true;
	    }
	    else
	    {
	         return false;
	    }
	}
}
?>