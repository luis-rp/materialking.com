<?php
class reportmodel extends Model
{
	function reportmodel()
	{
		parent::Model();
	}
	

 	function get_reports()
 	{
		$company = $this->session->userdata('company');
 		$search = '';
 		$filter = '';
 		$orderBy = '';
 		$projectSortBy = '';
 		
 		if(!@$_POST)
 		{
 			$_POST['searchfrom'] = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );;
 			$_POST['searchto'] = date('Y-m-d');
 		}
 		if(@$_POST)
 		{
 			if(@$_POST['searchfrom'] && @$_POST['searchto'])
 			{
 				$fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
 				$todate = date('Y-m-d', strtotime($_POST['searchto']));
 				$search = " HAVING STR_TO_DATE(receiveddate, '%Y-%m-%d') >= '$fromdate'
 						    AND STR_TO_DATE(receiveddate, '%Y-%m-%d') <= '$todate'";
 			}
 			elseif(@$_POST['searchfrom'])
 			{
 				$fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
 				$search = " HAVING STR_TO_DATE(receiveddate, '%Y-%m-%d') >= '$fromdate'";
 			}
 			elseif(@$_POST['searchto'])
 			{
 				$todate = date('Y-m-d', strtotime($_POST['searchto']));
 				$search = " HAVING STR_TO_DATE(receiveddate, '%Y-%m-%d') <= '$todate'";
 			}
 			if(@$_POST['purchasingadmin'])
 			{
 				$filter = " AND q.purchasingadmin='".$_POST['purchasingadmin']."' ";
 			}
 			if(@$_POST['searchproject'] && @$_POST['searchproject'])
 			{
 				$filter .= " AND p.id='".$_POST['searchproject']."' ";
 			}
 			if(@$_POST['searchpaymentstatus'] && @$_POST['searchpaymentstatus'])
 			{
 				$filter .= " AND r.paymentstatus='".$_POST['searchpaymentstatus']."' ";
 			}
 			if(@$_POST['verificationstatus'] && @$_POST['verificationstatus'])
 			{
 				$filter .= " AND r.status='".$_POST['verificationstatus']."' ";
 			} 
 		}
 		//print_r($_POST);die;
 		$datesql = "SELECT distinct(receiveddate) receiveddate, invoicenum,
 						SUM(r.quantity) totalquantity,
 						ROUND(SUM(ai.ea * r.quantity),2) totalprice
					   FROM 
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q,
					   ".$this->db->dbprefix('project')." p
					  WHERE r.awarditem=ai.id AND ai.company='".$company->id."'
					  AND ai.award=a.id AND a.quote=q.id AND
					  p.purchasingadmin=q.purchasingadmin AND
					  p.id=q.pid  
					  $filter
					  GROUP BY receiveddate
					  $search
					  ORDER BY receiveddate DESC
					  ";
		//echo $datesql;
		$datequery = $this->db->query($datesql);
		$sepdates = $datequery->result();
		
		$dates = array();
		foreach($sepdates as $sepdate)
		{
			$itemsql = "SELECT 
						r.*, ai.itemcode, u.companyname, q.ponum, a.awardedon, q.purchasingadmin,
						s.taxrate taxpercent,
						ai.itemname, ai.ea, ai.unit, ai.daterequested, ai.costcode, ai.notes,p.title,q.id as quote,ai.award  
					  FROM 
					  ".$this->db->dbprefix('received')." r, 
					  ".$this->db->dbprefix('awarditem')." ai,
					  ".$this->db->dbprefix('award')." a,
					  ".$this->db->dbprefix('quote')." q,
					  ".$this->db->dbprefix('settings')." s,
					  ".$this->db->dbprefix('users')." u,
					  ".$this->db->dbprefix('project')." p
					  WHERE r.awarditem=ai.id AND 
					  ai.company='".$company->id."' AND
					  ai.award=a.id AND
					  a.quote=q.id AND
					  u.id=q.purchasingadmin AND
					  s.purchasingadmin=q.purchasingadmin AND
					  p.purchasingadmin=q.purchasingadmin AND
					  p.id=q.pid AND 
					  r.receiveddate='{$sepdate->receiveddate}'
					  $filter
					  ";
			
			$itemquery = $this->db->query($itemsql);
			$items = $itemquery->result();
			$sepdate->items = $items;
			
 		
 		    $datepaidsql = "SELECT 
 						ROUND(SUM(ai.ea * r.quantity),2) totalpaid
					   FROM 
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q,
					   ".$this->db->dbprefix('project')." p
					  WHERE r.awarditem=ai.id AND ai.company='".$company->id."'
					  AND ai.award=a.id AND a.quote=q.id 
					  AND p.purchasingadmin=q.purchasingadmin 
					  AND r.paymentstatus='Paid'
					  AND r.receiveddate='{$sepdate->receiveddate}'
					  $filter
					  ";
 		    
 		    $sepdate->totalpaid = @$this->db->query($datepaidsql)->row()->totalpaid;
			
			$dates[]=$sepdate;
		}
		return $dates;
	}
	
}
?>