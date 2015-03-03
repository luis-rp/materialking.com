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
 						    AND STR_TO_DATE(receiveddate, '%Y-%m-%d') <= '$todate') ";
 			}
 			elseif(@$_POST['searchfrom'])
 			{
 				$fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
 				$search = " HAVING ( STR_TO_DATE(receiveddate, '%Y-%m-%d') >= '$fromdate' ) ";
 			}
 			elseif(@$_POST['searchto'])
 			{
 				$todate = date('Y-m-d', strtotime($_POST['searchto']));
 				$search = " HAVING ( STR_TO_DATE(receiveddate, '%Y-%m-%d') <= '$todate' ) ";
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
 		
 		$datesql = "SELECT distinct(receiveddate) receiveddate, invoicenum,  ai.company, ai.purchasingadmin, r.datedue, r.paymentstatus, r.paymentdate,
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
 		
 		
 		$contractsql = "SELECT distinct(receiveddate) receiveddate, invoicenum,  ai.company, ai.purchasingadmin,  r.datedue, r.paymentstatus, r.paymentdate, 
 						SUM(if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ) totalquantity,
 						ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity/100,if(r.invoice_type='alreadypay',0,1)) ),2) totalprice
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
						ai.itemname, ai.ea as ea, ai.unit, ai.daterequested, ai.costcode, ai.notes, q.id as quoteid, ai.quantity as aiquantity,i.url as itemurl,i.item_img,						if(pc.catname='My Item Codes',1,0) as IsMyItem 
					  FROM 
					  ".$this->db->dbprefix('received')." r, 
					  ".$this->db->dbprefix('awarditem')." ai,
					  ".$this->db->dbprefix('company')." c,
					  ".$this->db->dbprefix('award')." a,
					  ".$this->db->dbprefix('quote')." q,
					  ".$this->db->dbprefix('item')." i		
					  LEFT JOIN pms_category pc ON pc.id = i.category			 
					  WHERE r.awarditem=ai.id AND 
					  ai.company=c.id AND
					  ai.award=a.id AND
					  ai.itemid = i.id AND
					  a.quote=q.id AND q.potype <> 'Contract' $filter ";
			if(@$sepdate->receiveddate)
			$itemsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$itemsql .= " and r.invoicenum='".$sepdate->invoicenum."'";
			}			  		
			
			$itemcontractsql = "SELECT 
						r.*, ai.itemcode, c.companyname companyname, q.ponum, q.potype, a.awardedon,
						ai.itemname, (ai.ea * if(r.invoice_type='fullpaid',ai.quantity/100,if(r.invoice_type='alreadypay',0,1)) ) as ea, ai.unit, ai.daterequested, ai.costcode, ai.notes , q.id as quoteid, ai.quantity as aiquantity,i.url as itemurl,i.item_img,if(pc.catname='My Item Codes',1,0) as IsMyItem    
					  FROM 
					  ".$this->db->dbprefix('received')." r, 
					  ".$this->db->dbprefix('awarditem')." ai,
					  ".$this->db->dbprefix('users')." c,
					  ".$this->db->dbprefix('award')." a,
					  ".$this->db->dbprefix('quote')." q,
					  ".$this->db->dbprefix('item')." i	
					  LEFT JOIN pms_category pc ON pc.id = i.category				 		
					  WHERE r.awarditem=ai.id AND 
					  ai.company=c.id AND
					  ai.award=a.id AND
					  ai.itemid = i.id AND
					  a.quote=q.id AND q.potype = 'Contract' $filter ";
			if(@$sepdate->receiveddate)
			$itemcontractsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$itemcontractsql .= " and r.invoicenum='".$sepdate->invoicenum."'";		
			}
					
			$itemcombo = $itemsql." UNION ".$itemcontractsql;
			
			$itemquery = $this->db->query($itemcombo);
			$items = $itemquery->result();
			$sepdate->items = $items;
			
 		
			
			                // Code for getting discount/Penalty
                if(@$sepdate->company && @$sepdate->purchasingadmin){

                	$sql = "SELECT duedate, term, penalty_percent, discount_percent, discountdate FROM " .$this->db->dbprefix('invoice_cycle') . " where company='" . $sepdate->company . "'
				and purchasingadmin = '". $sepdate->purchasingadmin ."'";
                	//echo $sql;
                	$resultinvoicecycle = $this->db->query($sql)->row();

                	$sepdate->penalty_percent = 0;
                	$sepdate->penaltycount = 0;
                	$sepdate->discount_percent =0;

                	if($resultinvoicecycle){

                		if((@$resultinvoicecycle->penalty_percent || @$resultinvoicecycle->discount_percent) ){

                			if(@$sepdate->datedue){
		
                				if(@$sepdate->paymentstatus == "Paid" && @$sepdate->paymentdate){
                					$oDate = $sepdate->paymentdate;
                					$now = strtotime($sepdate->paymentdate);
                				}else {
                					$oDate = date('Y-m-d');
                					$now = time();
                				}

                				$d1 = strtotime($sepdate->datedue);
                				$d2 = strtotime($oDate);
                				$datediff =  (date('Y', $d2) - date('Y', $d1))*12 + (date('m', $d2) - date('m', $d1));
                				if(is_int($datediff) && $datediff > 0) {

                					$sepdate->penalty_percent = $resultinvoicecycle->penalty_percent;
                					$sepdate->penaltycount = $datediff;

                				}else{

                					$discountdate = $resultinvoicecycle->discountdate;
                					if(@$discountdate){

                						if ($now < strtotime($discountdate)) {
                							$sepdate->discount_percent = $resultinvoicecycle->discount_percent;
                						}
                					}
                				}
                			}

                		}
                	}

                }
			
			
			
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
			$datepaidsql .= " and r.invoicenum='".$sepdate->invoicenum."'";				  
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
			$datecontractpaidsql .= " and r.invoicenum='".$sepdate->invoicenum."'";				  
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
 						ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity/100,if(r.invoice_type='alreadypay',0,1)) ),2) totalprice
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
						ai.itemname, ai.ea as ea, ai.unit, ai.daterequested, ai.costcode, ai.notes, ai.quantity aiquantity,i.url as itemurl,if(pc.catname='My Item Codes',1,0) as IsMyItem    
					  FROM 
					  ".$this->db->dbprefix('received')." r, 
					  ".$this->db->dbprefix('awarditem')." ai,
					  ".$this->db->dbprefix('company')." c,
					  ".$this->db->dbprefix('award')." a,
					  ".$this->db->dbprefix('quote')." q,
					  ".$this->db->dbprefix('item')." i
					  LEFT JOIN pms_category pc ON pc.id = i.category	
					  WHERE r.awarditem=ai.id AND 
					  ai.company=c.id AND
					  ai.award=a.id AND
					  ai.itemid=i.id AND
					  a.quote=q.id AND q.potype <> 'Contract' $filter ";					  
			if(@$sepdate->receiveddate)
			$itemsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$itemsql .= " and r.invoicenum='".$sepdate->invoicenum."'";
			}
					
			
			$itemcontractsql = "SELECT 
						r.*, ai.itemcode, c.companyname companyname, q.ponum, q.potype, a.awardedon,
						ai.itemname, (ai.ea * if(r.invoice_type='fullpaid',ai.quantity/100,if(r.invoice_type='alreadypay',0,1)) ) as ea, ai.unit, ai.daterequested, ai.costcode, ai.notes, ai.quantity aiquantity,i.url as itemurl,if(pc.catname='My Item Codes',1,0) as IsMyItem  
					  FROM 
					  ".$this->db->dbprefix('received')." r, 
					  ".$this->db->dbprefix('awarditem')." ai,
					  ".$this->db->dbprefix('users')." c,
					  ".$this->db->dbprefix('award')." a,
					  ".$this->db->dbprefix('quote')." q,
					  ".$this->db->dbprefix('item')." i
					  LEFT JOIN pms_category pc ON pc.id = i.category	
					  WHERE r.awarditem=ai.id AND 
					  ai.company=c.id AND
					  ai.award=a.id AND
					  ai.itemid=i.id AND
					  a.quote=q.id AND q.potype = 'Contract'  $filter ";
					  
			if(@$sepdate->receiveddate)
			$itemcontractsql .= " AND  r.receiveddate='{$sepdate->receiveddate}'";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$itemcontractsql .= " and r.invoicenum='".$sepdate->invoicenum."'";
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
			$datepaidsql .= " and r.invoicenum='".$sepdate->invoicenum."'";
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
			$datecontractpaidsql .= " and r.invoicenum='".$sepdate->invoicenum."'";
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