<?php
class report_model extends Model
{
	function report_model()
	{
		parent::Model();
	}
	

 	function get_reports()
 	{
 		$search = '';
 		$filter = '';
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
 				$search = " HAVING ( STR_TO_DATE(receiveddate, '%Y-%m-%d') >= '$fromdate'
 						    AND STR_TO_DATE(receiveddate, '%Y-%m-%d') <= '$todate' OR receiveddate IS NULL ) ";
 			}
 			elseif(@$_POST['searchfrom'])
 			{
 				$fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
 				$search = " HAVING ( STR_TO_DATE(receiveddate, '%Y-%m-%d') >= '$fromdate' OR receiveddate IS NULL ) ";
 			}
 			elseif(@$_POST['searchto'])
 			{
 				$todate = date('Y-m-d', strtotime($_POST['searchto']));
 				$search = " HAVING ( STR_TO_DATE(receiveddate, '%Y-%m-%d') <= '$todate' OR receiveddate IS NULL ) ";
 			}
 			if(@$_POST['searchcompany'])
 			{
 				$filter = " AND ai.company='".$_POST['searchcompany']."'";
 			}
 		}
 		
 		if($this->session->userdata('usertype_id')>1)
		{
			$filter .= " AND r.purchasingadmin='".$this->session->userdata('purchasingadmin')."' ";
		}
 		
 		$datesql = "SELECT distinct(receiveddate) receiveddate, invoicenum,
 						SUM(if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ) totalquantity,
 						ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice
					   FROM 
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id
					  AND ai.award=a.id AND a.quote=q.id AND q.potype <> 'Contract'  
					  AND q.pid='".$this->session->userdata('managedprojectdetails')->id."'
					  $filter
					  GROUP BY receiveddate
					  $search ";
 		
 		
 		$contractsql = "SELECT distinct(receiveddate) receiveddate, invoicenum,
 						SUM(if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ) totalquantity,
 						ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity/100,if(r.invoice_type='alreadypay',0,r.quantity/100)) ),2) totalprice
					   FROM 
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id
					  AND ai.award=a.id AND a.quote=q.id AND q.potype = 'Contract'  
					  AND q.pid='".$this->session->userdata('managedprojectdetails')->id."'
					  $filter
					  GROUP BY receiveddate
					  $search ";
 		
		//echo $datesql;
		$combsql = $datesql." union ".$contractsql." ORDER BY receiveddate DESC";
		$datequery = $this->db->query($combsql);
		$sepdates = $datequery->result();
		
		$dates = array();
		foreach($sepdates as $sepdate)
		{
			$itemsql = "SELECT 
						r.*, ai.itemcode, c.title companyname, q.ponum, q.potype, a.awardedon,
						ai.itemname, ai.ea as ea, ai.unit, ai.daterequested, ai.costcode, ai.notes, q.id as quoteid, ai.quantity as aiquantity    
					  FROM 
					  ".$this->db->dbprefix('received')." r, 
					  ".$this->db->dbprefix('awarditem')." ai,
					  ".$this->db->dbprefix('company')." c,
					  ".$this->db->dbprefix('award')." a,
					  ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id AND 
					  ai.company=c.id AND
					  ai.award=a.id AND
					  a.quote=q.id AND q.potype <> 'Contract' $filter ";
			if(@$sepdate->receiveddate)
			$itemsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$itemsql .= " AND  r.receiveddate is NULL and r.invoicenum='".$sepdate->invoicenum."'";
			}			  		
			
			$itemcontractsql = "SELECT 
						r.*, ai.itemcode, c.companyname companyname, q.ponum, q.potype, a.awardedon,
						ai.itemname, (ai.ea * if(r.invoice_type='fullpaid',ai.quantity/100,if(r.invoice_type='alreadypay',0,r.quantity/100)) ) as ea, ai.unit, ai.daterequested, ai.costcode, ai.notes , q.id as quoteid, ai.quantity as aiquantity    
					  FROM 
					  ".$this->db->dbprefix('received')." r, 
					  ".$this->db->dbprefix('awarditem')." ai,
					  ".$this->db->dbprefix('users')." c,
					  ".$this->db->dbprefix('award')." a,
					  ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id AND 
					  ai.company=c.id AND
					  ai.award=a.id AND
					  a.quote=q.id AND q.potype = 'Contract' $filter ";
			if(@$sepdate->receiveddate)
			$itemcontractsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$itemcontractsql .= " AND  r.receiveddate is NULL and r.invoicenum='".$sepdate->invoicenum."'";		
			}
					
			$itemcombo = $itemsql." UNION ".$itemcontractsql;
			
			$itemquery = $this->db->query($itemcombo);
			$items = $itemquery->result();
			$sepdate->items = $items;
			
 		
 		    $datepaidsql = "SELECT 
 						ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalpaid
					   FROM 
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id
					  AND ai.award=a.id AND a.quote=q.id 
					  AND q.pid='".$this->session->userdata('managedprojectdetails')->id."' AND q.potype <> 'Contract' 
					  AND r.paymentstatus='Paid'";
 		    if(@$sepdate->receiveddate)
			$datepaidsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$datepaidsql .= " AND  r.receiveddate is NULL and r.invoicenum='".$sepdate->invoicenum."'";				  
			}
			
 		    $datecontractpaidsql = "SELECT 
 						ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity/100,if(r.invoice_type='alreadypay',0,r.quantity/100)) ),2) totalpaid
					   FROM 
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id
					  AND ai.award=a.id AND a.quote=q.id 
					  AND q.pid='".$this->session->userdata('managedprojectdetails')->id."' AND q.potype = 'Contract' 
					  AND r.paymentstatus='Paid'";
 		     if(@$sepdate->receiveddate)
			$datecontractpaidsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$datecontractpaidsql .= " AND  r.receiveddate is NULL and r.invoicenum='".$sepdate->invoicenum."'";				  
			}
 		    //echo $datepaidsql.'<br/>';
 		    
 		    $paidcombo = $datepaidsql." UNION ".$datecontractpaidsql;
 		    
 		    $sepdate->totalpaid = @$this->db->query($paidcombo)->row()->totalpaid;
			
			$dates[]=$sepdate;
		}
		return $dates;
	}
	
	
	function get_reports1()
 	{
 		
 		$filter = '';
 		
 		if($this->session->userdata('usertype_id')>1)
		{
			$filter .= " AND r.purchasingadmin='".$this->session->userdata('purchasingadmin')."' ";
		}
 		
 		$datesql = "SELECT distinct(receiveddate) receiveddate, invoicenum, SUM(if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ) totalquantity,
 					ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice
					   FROM 
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id
					  AND ai.award=a.id AND a.quote=q.id AND q.potype <> 'Contract'  
					  AND q.pid='".$this->session->userdata('managedprojectdetails')->id."'
					  $filter
					  GROUP BY receiveddate";
 		
 		
 		$contractsql = "SELECT distinct(receiveddate) receiveddate, invoicenum,
 						SUM(if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ) totalquantity,
 						ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity/100,if(r.invoice_type='alreadypay',0,r.quantity/100)) ),2) totalprice
					   FROM 
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id
					  AND ai.award=a.id AND a.quote=q.id AND q.potype = 'Contract'  
					  AND q.pid='".$this->session->userdata('managedprojectdetails')->id."'
					  $filter
					  GROUP BY receiveddate";
 		
		//echo $datesql;
		$combsql = $datesql." union ".$contractsql." ORDER BY receiveddate DESC";
		$datequery = $this->db->query($combsql);
		$sepdates = $datequery->result();
		
		$dates = array();
		foreach($sepdates as $sepdate)
		{
			$itemsql = "SELECT 
						r.*, ai.itemcode, c.title companyname, q.ponum, q.potype, a.awardedon,
						ai.itemname, ai.ea as ea, ai.unit, ai.daterequested, ai.costcode, ai.notes, ai.quantity aiquantity 
					  FROM 
					  ".$this->db->dbprefix('received')." r, 
					  ".$this->db->dbprefix('awarditem')." ai,
					  ".$this->db->dbprefix('company')." c,
					  ".$this->db->dbprefix('award')." a,
					  ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id AND 
					  ai.company=c.id AND
					  ai.award=a.id AND
					  a.quote=q.id AND q.potype <> 'Contract' $filter ";					  
			if(@$sepdate->receiveddate)
			$itemsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$itemsql .= " AND  r.receiveddate is NULL and r.invoicenum='".$sepdate->invoicenum."'";
			}
					
			
			$itemcontractsql = "SELECT 
						r.*, ai.itemcode, c.companyname companyname, q.ponum, q.potype, a.awardedon,
						ai.itemname, (ai.ea * if(r.invoice_type='fullpaid',ai.quantity/100,if(r.invoice_type='alreadypay',0,r.quantity/100)) ) as ea, ai.unit, ai.daterequested, ai.costcode, ai.notes, ai.quantity aiquantity  
					  FROM 
					  ".$this->db->dbprefix('received')." r, 
					  ".$this->db->dbprefix('awarditem')." ai,
					  ".$this->db->dbprefix('users')." c,
					  ".$this->db->dbprefix('award')." a,
					  ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id AND 
					  ai.company=c.id AND
					  ai.award=a.id AND
					  a.quote=q.id AND q.potype = 'Contract'  $filter ";
					  
			if(@$sepdate->receiveddate)
			$itemcontractsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$itemcontractsql .= " AND  r.receiveddate is NULL and r.invoicenum='".$sepdate->invoicenum."'";
			}
			
			$itemcombo = $itemsql." UNION ".$itemcontractsql;
			
			$itemquery = $this->db->query($itemcombo);
			$items = $itemquery->result();
			$sepdate->items = $items;
			
 		
 		    $datepaidsql = "SELECT 
 						ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalpaid
					   FROM 
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id
					  AND ai.award=a.id AND a.quote=q.id 
					  AND q.pid='".$this->session->userdata('managedprojectdetails')->id."' AND q.potype <> 'Contract' 
					  AND r.paymentstatus='Paid'";
 		    if(@$sepdate->receiveddate)
			$datepaidsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$datepaidsql .= " AND  r.receiveddate is NULL and r.invoicenum='".$sepdate->invoicenum."'";
			}
					  
 		    
 		    $datecontractpaidsql = "SELECT 
 						ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity/100,if(r.invoice_type='alreadypay',0,r.quantity/100)) ),2) totalpaid
					   FROM 
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id
					  AND ai.award=a.id AND a.quote=q.id 
					  AND q.pid='".$this->session->userdata('managedprojectdetails')->id."' AND q.potype = 'Contract' 
					  AND r.paymentstatus='Paid'";
 		    if(@$sepdate->receiveddate)
			$datecontractpaidsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$datecontractpaidsql .= " AND  r.receiveddate is NULL and r.invoicenum='".$sepdate->invoicenum."'";
			}					  
 		    
 		    //echo $datepaidsql.'<br/>';
 		    
 		    $paidcombo = $datepaidsql." UNION ".$datecontractpaidsql;
 		    
 		    $sepdate->totalpaid = @$this->db->query($paidcombo)->row()->totalpaid;
			
			$dates[]=$sepdate;
		}
		return $dates;
	}
	
}
?>