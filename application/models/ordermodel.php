<?php
class Ordermodel extends Model 
{
	
	function Ordermodel() 
	{
		parent::Model ();
	}
	
	function getconfigurations()
	{
	    $query = $this->db->get ('settings' );
	    $result = $query->result ();
	    return $result [0];
	}
}
?>