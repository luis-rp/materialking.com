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
 				$search = " HAVING (STR_TO_DATE(receiveddate, '%Y-%m-%d') >= '$fromdate'
 						    AND STR_TO_DATE(receiveddate, '%Y-%m-%d') <= '$todate'  OR receiveddate IS NULL )";
 			}
 			elseif(@$_POST['searchfrom'])
 			{
 				$fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
 				$search = "  HAVING (STR_TO_DATE(receiveddate, '%Y-%m-%d') >= '$fromdate'  OR receiveddate IS NULL ) ";
 			}
 			elseif(@$_POST['searchto'])
 			{
 				$todate = date('Y-m-d', strtotime($_POST['searchto']));
 				$search = "  HAVING (STR_TO_DATE(receiveddate, '%Y-%m-%d') <= '$todate'  OR receiveddate IS NULL ) ";
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
 			
 			if(@$_POST['datebymonth'] && @$_POST['datebymonth'])
 			{	
 				  if(!@$_POST['checkunpaid'])
 					{
 						$filter .= "AND r.paymentstatus='Paid'";				
 					}
 				
 				if(@$_POST['datebymonth']=="alltime")
 			     {					
 				    $search = "";
 			     }
 			     else 
 			     {
	 			    $fromdate = date('Y-m-d', strtotime($_POST['datebymonth']));
	 				$search = " HAVING (STR_TO_DATE(receiveddate, '%Y-%m-%d') >= '$fromdate'  OR receiveddate IS NULL ) "; 			     	
 			     } 				
 			} 			
 		}
 		//print_r($_POST);die;
 	 $datesql = "SELECT distinct(receiveddate) receiveddate, invoicenum,  ai.company, ai.purchasingadmin, r.datedue, r.paymentstatus, r.paymentdate, 	SUM(if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ) totalquantity,
 						ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice
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
						ai.itemname, ai.ea, ai.unit, ai.daterequested, ai.costcode, ai.notes,p.title,q.id as quote,ai.award, ai.quantity as aiquantity,i.item_img   
					  FROM 
					  ".$this->db->dbprefix('received')." r, 
					  ".$this->db->dbprefix('awarditem')." ai,
					  ".$this->db->dbprefix('award')." a,
					  ".$this->db->dbprefix('quote')." q,
					  ".$this->db->dbprefix('settings')." s,
					  ".$this->db->dbprefix('users')." u,
					  ".$this->db->dbprefix('project')." p,					  
					   ".$this->db->dbprefix('item')." i
					  WHERE r.awarditem=ai.id AND 
					  ai.company='".$company->id."' AND
					  ai.award=a.id AND
					  a.quote=q.id AND
					  u.id=q.purchasingadmin AND
					  s.purchasingadmin=q.purchasingadmin AND
					  p.purchasingadmin=q.purchasingadmin AND
					  i.id=ai.itemid AND
					  p.id=q.pid $filter ";
			if($sepdate->receiveddate != null)			 
			$itemsql .= "AND r.receiveddate='{$sepdate->receiveddate}' ";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$itemsql .= " AND  r.receiveddate is NULL and r.invoicenum='".$sepdate->invoicenum."'";
			}
			
			$itemquery = $this->db->query($itemsql);
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
					   ".$this->db->dbprefix('quote')." q,
					   ".$this->db->dbprefix('project')." p
					  WHERE r.awarditem=ai.id AND ai.company='".$company->id."'
					  AND ai.award=a.id AND a.quote=q.id 
					  AND p.purchasingadmin=q.purchasingadmin 
					  AND r.paymentstatus='Paid'  $filter ";
 		    
 		    if($sepdate->receiveddate != null)			 
			$datepaidsql .= "AND r.receiveddate='{$sepdate->receiveddate}' ";
			elseif (strpos(@$sepdate->invoicenum,'paid-in-full-already') !== false) { 
			$datepaidsql .= " AND  r.receiveddate is NULL and r.invoicenum='".$sepdate->invoicenum."'";
			} 
			
 		    $sepdate->totalpaid = @$this->db->query($datepaidsql)->row()->totalpaid;
			
			$dates[]=$sepdate;
		}
		return $dates;
	}
	
}
?>