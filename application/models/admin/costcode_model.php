<?php
class costcode_model extends Model
{
	function costcode_model()
	{
		parent::Model();
	}
	

 	function get_costcodes($limit=0,$offset=0)
 	{		
	    if ($offset == 0) {
			$newoffset = 0;
		} else {
			$newoffset = $offset;
		}
		
		$sql ="SELECT *
		FROM
		".$this->db->dbprefix('costcode')." WHERE 1=1 ";
		
		if($this->session->userdata('usertype_id')>1)
		{
			$sql ="SELECT *
			FROM
			".$this->db->dbprefix('costcode')." 
			WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
		}
		if(@$_POST['parentfilter'])
			$sql .= " AND parent='{$_POST['parentfilter']}'";
		if(@$_POST['projectfilter'])
			$sql .= " AND project='{$_POST['projectfilter']}'";
		$query = $this->db->query ($sql);
		if ($query->result ()) 
		{
			$result = $query->result ();
			$ret = array();
			foreach($result as $item)
			{
				$sql ="SELECT SUM(ai.quantity*ai.ea) totalcost
				FROM
				".$this->db->dbprefix('awarditem')." ai, 
				".$this->db->dbprefix('award')." a
				WHERE
				ai.award=a.id AND ai.costcode='".$item->code."'";
				
				if($item->forcontract==1){
					
				$sql ="SELECT SUM(ai.ea) totalcost
				FROM
				".$this->db->dbprefix('awarditem')." ai, 
				".$this->db->dbprefix('award')." a
				WHERE
				ai.award=a.id AND ai.costcode='".$item->code."'";
					
				}
				
				if($this->session->userdata('usertype_id')>1)
					$sql .= " AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
				
				$query = $this->db->query ($sql);
				$item->totalspent = $query->row ('totalcost');
				/****/
				
						$sql2 = "SELECT SUM( od.price * od.quantity ) sumT, o.shipping 
					FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('costcode')." cc, ".$this->db->dbprefix('orderdetails')." od
					WHERE cc.id =  ".$item->id."
					AND o.costcode = cc.id
					AND o.id = od.orderid
					GROUP BY o.costcode";
						
					if($item->forcontract==1){
							
							$sql2 = "SELECT SUM( od.price) sumT, o.shipping 
					FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('costcode')." cc, ".$this->db->dbprefix('orderdetails')." od
					WHERE cc.id =  ".$item->id."
					AND o.costcode = cc.id
					AND o.id = od.orderid
					GROUP BY o.costcode";
							
					}
						
						$query2 = $this->db->query ($sql2);
						
						if($query2->result()){
								
								
							$totalOrder = $query2->row();
							$item->totalspent += $totalOrder->sumT;
							$item->shipping = $totalOrder->shipping;
						}
					
				/****/
				if($item->totalspent == null)
					$item->totalspent = '-';
				$ret[] = $item;
			}
			//print_r($ret);die;
			return $ret;
		} 
		else 
		{
			return null;
		}
	}
	
	function listHeirarchicalCombo($projectid='',$parent_id = 0, $level = 0, $selected = ''){
		static $temp = '';
		# retrieve all children of $parent
		$where = "";
		if($projectid!="")
		$where = 'and project = '.$projectid;
		
		$sql = "SELECT * FROM ".$this->db->dbprefix('costcode')." WHERE parent = '{$parent_id}' {$where} ORDER BY code ASC";
		
		if($this->session->userdata('usertype_id')>1)
		{
			$sql ="SELECT *
			FROM
			".$this->db->dbprefix('costcode')." 
			WHERE parent = '{$parent_id}' AND purchasingadmin='".$this->session->userdata('purchasingadmin')."' {$where} 
			ORDER BY code ASC";
		}
		
  		$result = $this->db->query($sql)->result();			
		# display each child
		if($result)	
		foreach($result as $row)
		{
			$row = (array)$row;
			if($row['parent'] == 0){				
				$opt_style = "style = \"BACKGROUND-COLOR: #EEEEEE;COLOR: #136C99;FONT-SIZE: 11px;FONT-WEIGHT: bold;\"";
			}else{
				$opt_style = "";
			}
			if($row['id'] == $selected){
				$is_selected = 'selected="selected"';
			}else{
				$is_selected = "";
			}
			$separator = str_repeat("&raquo;&nbsp;", $level);
			$temp .= "\t<option value=\"{$row['id']}\" {$opt_style} {$is_selected}> {$separator} {$row['code']}</option>\r\n";						if($projectid!="")
			$this->listHeirarchicalCombo($projectid,$row['id'], $level + 1, $selected);	
			else 
			$this->listHeirarchicalCombo('',$row['id'], $level + 1, $selected);		
		} 
		return $temp;
	}
	
	// counting total costcodes
	function total_costcode()
	{
		$query = $this->db->count_all_results('costcode');
		return $query;
	}
	
	function getcostcodeitems($costcode)
	{
		$sql ="SELECT ai.*, q.ponum, q.potype, a.quote,IFNULL(ai.received,0) as  newreceived
			FROM
			".$this->db->dbprefix('awarditem')." ai, ".$this->db->dbprefix('award')." a,
			 ".$this->db->dbprefix('quote')." q
			WHERE a.quote=q.id AND
			ai.award=a.id AND ai.costcode='$costcode' order by ai.daterequested";
		if($this->session->userdata('usertype_id')>1)
		{
			$sql ="SELECT ai.*, q.ponum, q.potype, a.quote,IFNULL(ai.received,0) as  newreceived
				FROM
				".$this->db->dbprefix('awarditem')." ai, ".$this->db->dbprefix('award')." a,
				 ".$this->db->dbprefix('quote')." q
				WHERE a.quote=q.id AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
				AND ai.award=a.id AND ai.costcode='$costcode' order by ai.daterequested";
		}
		//echo $sql;
		$query = $this->db->query ($sql);
		if ($query->result ()) {
			return $query->result ();
		} else {
			return null;
		}
		/*
		$this->db->where('costcode',$costcode);
		$query = $this->db->get('quoteitem');
		if($query->num_rows>0)
		{
			$ret = $query->result();
	        return $ret;
		}
		return NULL;
		*/
	}
	
		function getcostcodeitems2($costcode)
	{
		$sql ="SELECT sum(ai.totalprice) as totalprice, ai.company, ai.itemname, ai.ea, ai.daterequested as daterequested, GROUP_CONCAT(q.ponum,' : $',ai.totalprice) as ponum, q.potype, a.awardedon, a.quote,SUM(IFNULL(ai.received,0)) as  newreceived
			FROM
			".$this->db->dbprefix('awarditem')." ai, ".$this->db->dbprefix('award')." a,
			 ".$this->db->dbprefix('quote')." q
			WHERE a.quote=q.id AND
			ai.award=a.id AND ai.costcode='$costcode' group by ai.daterequested order by ai.daterequested";
		if($this->session->userdata('usertype_id')>1)
		{
			$sql ="SELECT sum(ai.totalprice) as totalprice, ai.company,  ai.itemname,  ai.ea, ai.daterequested as daterequested, GROUP_CONCAT(q.ponum,' : $',ai.totalprice) as ponum, q.potype, a.awardedon, a.quote,SUM(IFNULL(ai.received,0)) as  newreceived
				FROM
				".$this->db->dbprefix('awarditem')." ai, ".$this->db->dbprefix('award')." a,
				 ".$this->db->dbprefix('quote')." q
				WHERE a.quote=q.id AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
				AND ai.award=a.id AND ai.costcode='$costcode' group by ai.daterequested order by ai.daterequested";
		}
		//echo $sql;
		$query = $this->db->query ($sql);
		if ($query->result ()) {
			return $query->result ();
		} else {
			return null;
		}
		/*
		$this->db->where('costcode',$costcode);
		$query = $this->db->get('quoteitem');
		if($query->num_rows>0)
		{
			$ret = $query->result();
	        return $ret;
		}
		return NULL;
		*/
	}
	
	
	function SaveCostcode()
	{
		$forcontract="";
		if(@$this->input->post('forcontract') == "on")
    		$forcontract = 1;
    		else 
    		$forcontract = 0;
		$options = array(
			'code'=>$this->input->post('code'),
			'cost'=>$this->input->post('cost'),
			'cdetail'=>$this->input->post('cdetail'),
			'parent'=>$this->input->post('parent'),
			'project'=>$this->input->post('project'),
			'creation_date' => date('Y-m-d'),
			'forcontract' => $forcontract
		);
		$options['purchasingadmin'] = $this->session->userdata('purchasingadmin');
		$this->db->insert('costcode', $options);
		return $this->db->insert_id();
	}
	
	// updating cost code 
	function updateCostcode($id)
	{
		$forcontract="";
		if(@$this->input->post('forcontract') == "on")
    		$forcontract = 1;
    		else 
    		$forcontract = 0;
		$options = array(
			'code'=>$this->input->post('code'),
			'cost'=>$this->input->post('cost'),
			'cdetail'=>$this->input->post('cdetail'),
			'parent'=>$this->input->post('parent'),
			'project'=>$this->input->post('project'),
			'forcontract' => $forcontract
		);
		$oldcoderow = $this->get_costcodes_by_id($this->input->post('id'));
		$oldcode = $oldcoderow->code;
		$newcode = $this->input->post('code');
		
		$this->db->where('id', $this->input->post('id'));
		$this->db->update('costcode', $options);
		
		$update = array('costcode'=>$newcode);
		
		$this->db->where('costcode', $oldcode);
		$this->db->update('awarditem',$update);
		
		$this->db->where('costcode', $oldcode);
		$this->db->update('biditem',$update);
		
		$this->db->where('costcode', $oldcode);
		$this->db->update('quoteitem',$update);
	}
	
	
	// removing cost code
	function remove_costcode($id)
	{
		$item = $this->get_costcodes_by_id($id);
		$this->db->where('id', $id);
		$this->db->delete('costcode');
		
		$this->db->where('costcode', $item->code);
		$this->db->delete('quoteitem');
		
		$this->db->where('costcode', $item->code);
		$this->db->delete('biditem');
	}
	
	// retrieve cost code by their id
	function get_costcodes_by_id($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('costcode');
		if($query->num_rows>0)
		{
			$ret = $query->row();
	        return $ret;
		}
		return NULL;
	}
	
	function checkDuplicateCode($code, $edit_id = 0,$project)
	{
		if($edit_id > 0)
		{
			
		    $this->db->where(array('id !='=> $edit_id,'code'=>$code,'purchasingadmin'=> $this->session->userdata('purchasingadmin') ));
		}
		else
		{
			
			$this->db->where(array('purchasingadmin'=> $this->session->userdata('purchasingadmin'),'code'=>$code,'project'=>$project));
		}
		$query = $this->db->get ('costcode' );
		
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