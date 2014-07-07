<?php
class adminmodel extends Model 
{
	
	function adminmodel() 
	{
		parent::Model ();
	}
	
	function getfields()
	{
		$sql ="SHOW FIELDS FROM ".$this->db->dbprefix('users');
		
		$query = $this->db->query ($sql);
		if ($query->result ()) {
			return $query->result ();
		} else {
			return null;
		}	
	}
	
	function getAdminuserName($alias) 
	{
		$this->db->select ( 'username' );
		$this->db->where ( 'username', $alias );
		$query = $this->db->get ('users' );
		if ($query->num_rows >= 1) {
			$result = $query->result ();
			return $result [0]->username;
		} else {
			return false;
		}
	}
	
	function get_paged_list($limit = 10, $offset = 0) 
	{
		if ($offset == 0) {
			$newoffset = 0;
		} else {
			$newoffset = $offset;
		}
		
		$userwhere = '';
		if($this->session->userdata('usertype_id')==2)
			$userwhere = " WHERE u.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
			
		
			$sql = "SELECT
			u.id,
			u.username,
			u.fullname,
			u.designation,
			u.phone,
			u.created_date,
			u.last_logged_date,
			u.status,
			ut.userType
			FROM
			".$this->db->dbprefix('usertype')." ut
			Inner Join ".$this->db->dbprefix('users')." u 
			ON u.usertype_id = ut.id
			$userwhere
			ORDER BY u.fullname asc  LIMIT $newoffset, $limit ";
		
		$query = $this->db->query ( $sql );
		if ($query->result ()) {
			return $query->result ();
		} else {
			return null;
		}
	}
	
	function getUserType() 
	{
		$userarrays = array ();
		$this->db->select ( 'id, userType' );
		$query = $this->db->get ( 'usertype' );
		foreach ( $query->result_array () as $userarray ) {
			array_push ( $userarrays, $userarray );
		}
		return $userarrays;
	}
	
	function count_all() 
	{
	    $userwhere = "";
		if($this->session->userdata('usertype_id')==2)
			$userwhere = " WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
	    $query = "SELECT * FROM ".$this->db->dbprefix('users'). $userwhere;
		$all = $this->db->query($query)->result();
		
		return count($all);
		//return $this->db->count_all ( 'users' );
	}
	
	function get_by_id($id) 
	{
		$this->db->where ( 'id', $id );
		return $this->db->get ( 'users' );
	}
	
	function save() 
	{
		$options = $this->input->post();
		$options['password'] = md5($options['password']);
		if($this->session->userdata('usertype_id') == 2)
			$options['purchasingadmin'] = $this->session->userdata('purchasingadmin');
		$options['created_date'] = date ( "Y-m-d h:i:s" );
		unset($options['_wysihtml5_mode']);
		if($this->session->userdata('usertype_id') == 2)
			$options['usertype_id'] = 3;
		$this->db->insert ( 'users', $options );
		$id = $this->db->insert_id ();
		
		if($options['usertype_id'] == 2)
		{
			$this->db->where('id',$id)->update('users',array('purchasingadmin'=>$id));
		}
		return $id;
	}	
	
	function savefb($request) 
	{
		$options = $request;
		//echo "<pre>",print_r($options['location']['name']); die;
		$options['address'] = $options['location']['name'];
		$options['fullname'] = $options['name'];
		$options['usertype_id'] = 3;
		unset($options['location']);
		unset($options['gender']);
		unset($options['birthday']);
		unset($options['name']);
		$options['password'] = md5($options['password']);
		if($this->session->userdata('usertype_id') == 2)
			$options['purchasingadmin'] = $this->session->userdata('purchasingadmin');
		$options['created_date'] = date ( "Y-m-d h:i:s" );
		unset($options['_wysihtml5_mode']);
		if($this->session->userdata('usertype_id') == 2)
			$options['usertype_id'] = 3;
		$this->db->insert ( 'users', $options );
		$id = $this->db->insert_id ();
		
		if($options['usertype_id'] == 2)
		{
			$this->db->where('id',$id)->update('users',array('purchasingadmin'=>$id));
		}
		return $id;
	}
	
	
	function update() 
	{
		$options = $this->input->post();
		if(@$options['password'])
			$options['password'] = md5($options['password']);
		unset($options['_wysihtml5_mode']);
		$id = $options['id'];
		//print_r($options);die;
		/*
		if($options['address'])
		{
    		$geocode = file_get_contents(
            "http://maps.google.com/maps/api/geocode/json?address=" . urlencode(str_replace("\n", ", ", $options['address'])) . "&sensor=false");
            $output = json_decode($geocode);
            
            $options['user_lat'] = @$output->results[0]->geometry->location->lat;
            $options['user_lng'] = @$output->results[0]->geometry->location->lng;
		}
		*/
		$this->db->where ( 'id', $id );
		$this->db->update ( 'users', $options );
	}
	
	function delete($id) 
	{
		$this->db->where ('id', $id );
		$this->db->delete ('users');
		
		//Delete the Setting data from the table
		
	}
	
	function activate($id)
	{
		$options = array(
			'status'=>'1'
		);
		
		$this->db->where('id',$id);
		$this->db->update('users', $options);
	}
	
	function deactivate($id)
	{
		$options = array(
			'status'=>'0'
		);
		
		$this->db->where('id',$id);
		$this->db->update('users', $options);
	}
	
	function checkExistingUserEmail($email)
	{
	    $this->db->where ( 'email', $email );
		$query = $this->db->get ('users' );
		if ($query->num_rows >= 1) 
		    return true;
		else
		    return false;
	}
}
?>