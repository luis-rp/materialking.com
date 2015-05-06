<?php
class Quotemodel extends Model
{

	function Quotemodel()
	{
		parent::Model ();
	}

	function getconfigurations()
	{
	    $query = $this->db->get ('settings' );
	    $result = $query->result ();
	    return $result [0];
	}

	function getpurchaseremail($prchaserid){

		$this->db->where('purchasingadmin',$prchaserid);
		$query = $this->db->get ('settings' );
	    $result = $query->result ();
	    return $result [0];
	}

	/*function getnewinvitations($company)
	{
		$this->db->where('company',$company);
		$invitations = $this->db->get('invitation')->result();

		$ret = array();
		foreach($invitations as $i)
		{
			$new = true;
			if($this->getdraftitems($i->quote,$company))
				$new = false;
			$this->db->where('quote',$i->quote);
			if($this->db->get('award')->num_rows>0)
				$new = false;

			if($new)
			{
				$this->db->where('id',$i->quote);
				$i->quotedetails = $this->db->get('quote')->row();
				$ret[]=$i;
			}
		}
		return $ret;
	}*/


	function getnewinvitations($company){
		$searchstatus = 'New';
				$sql = "SELECT i.*,q.ponum FROM
		".$this->db->dbprefix('invitation')." i, ".$this->db->dbprefix('quote')." q
		WHERE i.quote=q.id AND i.company='{$company}' ORDER BY i.senton DESC";
		$count = $this->db->query($sql)->num_rows;

		//echo $sql;

		$invs = $this->db->query($sql)->result();

		$invitations = array();
		foreach($invs as $inv)
		{
    		$this->db->where('id',$inv->quote);
    		$inv->quotedetails = $this->db->get('quote')->row();
    		$this->db->where('quote',$inv->quote);
    		$this->db->where('company',$company);
    		$bid = $this->db->get('bid')->row();
    		$inv->quotenum = @$bid->quotenum;

			$awarded = $this->checkbidcomplete($inv->quote);
			$inv->awardedtothis = false;

			if($bid){
				$sqlq = "SELECT daterequested FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bid->id."' AND purchasingadmin='".$bid->purchasingadmin."' order by id desc limit 1";
				$revisionquote = $this->db->query($sqlq)->row();
				if($revisionquote)
				$inv->daterequested = $revisionquote->daterequested;
			}

			if($awarded)
			{
				$complete = true;
				$noitemsgiven = true;
				$allawarded = true;
				$this->db->where('award',$awarded->id);
				$this->db->where('company',$company);
				$items = $this->db->get('awarditem')->result();
				foreach($items as $i)
				{
					if($i->received < $i->quantity)
						$complete = false;
					if($i->company != $company)
						$allawarded = false;
					if($i->received > 0)
						$noitemsgiven = false;
				}

				if(!$noitemsgiven)
				{
					if($complete)
					{
						$inv->status = 'Completed';
						$inv->progress = 100;
						$inv->mark = "progress-bar-success";
					}
					else
					{
						$inv->status = 'Partially Completed';
						$inv->progress = 80;
						$inv->mark = "progress-bar-success";
					}
				}
				else
				{
					$awardeditems = $this->getawardeditems($awarded->id,$company);

					if($awardeditems && !$allawarded)
					{
						$inv->status = 'Partially Awarded';
						$inv->progress = 60;
						$inv->mark = "progress-bar-success";
					}
					else
					{
						$inv->status = 'Awarded';
						$inv->progress = 60;
						$inv->mark = "progress-bar-success";
					}
				}

				if($this->getawardeditems($awarded->id,$company))
				{
					$inv->awardedtothis = true;
					$inv->award = $awarded->id;
				}
				else
				{
					$inv->status = 'PO Closed - 0 items won';
					$inv->progress = 100;
					$inv->mark = "progress-bar-warning";
				}
			}
			elseif($this->getdraftitems($inv->quote,$inv->company))
			{
				$inv->status = 'Processing';
				$inv->progress = 40;
				$inv->mark = "progress-bar-warning";
			}
			else
			{
				$inv->status = 'New';
				$inv->progress = 20;
				$inv->mark = "progress-bar-danger";
			}

			if(!$searchstatus)
			{
				$invitations[]=$inv;
			}
			elseif(@$searchstatus == $inv->status)
			{
				$invitations[]=$inv;
			}

		}

		return $invitations;

	}

	function getinvitation($key)
	{
		$this->db->where('invitation',$key);
		$query = $this->db->get('invitation');
		if($query->num_rows>0)
			return $query->row();
		else
			return NULL;
	}

	function getcompanybyid($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('company');
		if($query->num_rows>0)
			return $query->row();
		else
			return NULL;
	}

	function getquotebyid($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('quote');
		if($query->num_rows>0)
		{
			$ret = $query->row();
	        return $ret;
		}
		return NULL;
	}

	function checkbidcomplete($qid)
	{
		//$this->db->reset();
		$this->db->where(array('quote'=>$qid));
		$query = $this->db->get('award');
		if($query->num_rows>0)
		{
			return $query->row();
		}
		return false;
	}

	function getawardeditems($award,$company)
	{
		//$this->db->reset();
		$query=$this->db->select('awarditem.*')->select('item.item_img')->from('awarditem')->where(array('award'=>$award, 'company'=>$company))->join('item','awarditem.itemid=item.id','LEFT')->get();
		/*$this->db->where(array('award'=>$award, 'company'=>$company));
		$query = $this->db->get('awarditem');*/
		if($query->num_rows>0)
		{
			return $query->result();
		}
		return false;
	}

	function clearinvitation($key)
	{
		$this->db->where('invitation',$key);
		$this->db->update('invitation',array('invitation'=>''));
	}

	function getquoteitems($id)
	{
		$this->db->where('quote',$id);
		$query = $this->db->get('quoteitem');

		$ret = $query->result();
		return $ret;
	}

	function getdraftitems($quote,$company)
	{
		$sql = "SELECT bi.*,b.quote,i.item_img FROM ".$this->db->dbprefix('biditem')." bi, ".$this->db->dbprefix('bid')." b, ".$this->db->dbprefix('item')." i
			   WHERE bi.bid=b.id AND i.id = bi.itemid AND b.quote='$quote' AND b.company='$company'
		 	   ";
		$query = $this->db->query ($sql);
		if($query->num_rows>0)
			return $query->result();
		else
			return array();
	}

	function getdraftitemswithdefaultitemcode($quote,$company)
	{
		$sql = "SELECT bi.*, i.itemcode as defaultitemcode, i.itemname as defaultitemname,i.item_img FROM ".$this->db->dbprefix('biditem')." bi, ".$this->db->dbprefix('bid')." b, ".$this->db->dbprefix('item')." i 
			   WHERE bi.bid=b.id AND bi.itemid = i.id AND b.quote='$quote' AND b.company='$company'
		 	   ";
		$query = $this->db->query ($sql);
		if($query->num_rows>0)
			return $query->result();
		else
			return array();
	}
	
	function getrevisiondraftitems($quote,$company,$rid="")
    {
    	$where = "";

    	if(isset($rid) && $rid!=""){
    		$where = "AND bi.revisionid=".$rid;
    	}

        $sql = "SELECT bi.* FROM ".$this->db->dbprefix('quoterevisions')." bi, ".$this->db->dbprefix('bid')." b
               WHERE bi.bid=b.id AND b.quote='$quote' AND b.company='$company' {$where} order by bi.revisionid desc" ;
        $query = $this->db->query ($sql);
        if($query->num_rows>0)
            return $query->result();
        else
            return array();
    }

    
    function getlowestbidprice($quote, $itemid) {
        
        $sql = "SELECT ea as price, bi.id FROM " . $this->db->dbprefix('bid') . " b join " . $this->db->dbprefix('biditem') . " bi on  bi.bid=b.id WHERE b.quote='$quote' AND itemid='$itemid'";        
        $result = $this->db->query($sql)->result();
        return $result;
    }
    
    
	function getquotesubtotal($id)
	{
		$sql ="SELECT SUM(totalprice) subtotal
		FROM
		".$this->db->dbprefix('quoteitem')." WHERE quote='$id'";

		$query = $this->db->query ($sql);
		if ($query->result ())
		{
			$row = $query->row ();
			return $row->subtotal;
		}
		else
		{
			return 0;
		}
	}

	function saveminimum($company,$pa,$itemid,$itemcode,$itemname,$price,$substitute=0)
	{
		$arr = array('company'=>$company,'itemid'=>$itemid,'purchasingadmin'=>$pa);
		//print_r($arr);die;
		$this->db->where($arr);
		$this->db->delete('minprice');
		
		$itemname = mysql_real_escape_string($itemname);
		$itemcode = mysql_real_escape_string($itemcode);
		
		$sql = "INSERT INTO ".$this->db->dbprefix('minprice')."
				SET company='$company', itemid='$itemid', purchasingadmin='$pa',
				itemcode='$itemcode', itemname='$itemname', price='$price',
				substitute='$substitute', quoteon='".date('m/d/Y')."'
				";
		//echo $sql;die;
		$this->db->query($sql);
	}

	///////////// backtrack

	function getBacktracks($company)
	{
		if(@$_POST['searchpurchasingadmin'])
			$this->db->where('purchasingadmin',$_POST['searchpurchasingadmin']);
		$this->db->order_by("podate", "asc");
		$quotes = $this->db->get('quote')->result();

		$totalDueQty = 0;
		$count = count ($quotes);
		$items = array();
		if ($count >= 1)
		{
			foreach ($quotes as $quote)
			{
				$this->db->where('quote',$quote->id);
				$awarded = $this->db->get('award')->row();
				if($awarded)
				{
					$this->db->where('award',$awarded->id);
					$awardeditems = $this->db->get('awarditem')->result();
					if($awardeditems && $this->checkReceivedPartially($awarded->id))
					{
						foreach($awardeditems as $item)
						{
							if($item->received < $item->quantity && $item->company==$company)
							{
								$items[$quote->ponum]['quote'] = $quote;
								$item->ponum = $quote->ponum;
								$item->duequantity = $item->quantity - $item->received;

								$totalDueQty += $item->quantity - $item->received;
								$item->etalog = $this->db->where('company',$company)
								->where('quote',$quote->id)
								->where('itemid',$item->itemid)
								->get('etalog')->result();

								$item->pendingshipments = $this->db->select('SUM(quantity) pendingshipments')
								->from('shipment')
								->where('quote',$quote->id)->where('company',$company)
								->where('itemid',$item->itemid)->where('accepted',0)
								->get()->row()->pendingshipments;

								$item->quotedaterequested = $this->db->select('daterequested')
								->where('purchasingadmin',$item->purchasingadmin)
								->where('quote',$quote->id)
								->where('itemid',$item->itemid)
								->get('quoteitem')->row();
								
								$item->itemimage = $this->db->select('item_img')
								->where('id',$item->itemid)
								->get('item')->row();
						
								if(!isset($items[$quote->ponum]['items']))
								$items[$quote->ponum]['items'] = array();
								$items[$quote->ponum]['items'][]=$item;
							}
						}

					}
				}
			}
			$this->session->set_userdata('backorderQtyDue',$totalDueQty);	
		}
		return $items;
	}

	function getBacktrackDetails($quote,$company)
	{
		$this->db->where('id',$quote);
		$quote = $this->db->get('quote')->row();

		$count = count ($quote);
		$items = array();
		if ($quote)
		{
			$this->db->where('quote',$quote->id);
			$awarded = $this->db->get('award')->row();
			if($awarded)
			{
				$this->db->where('award',$awarded->id);
				$awardeditems = $this->db->get('awarditem')->result();
				if($awardeditems && $this->checkReceivedPartially($awarded->id))
				{
					foreach($awardeditems as $item)
					{
						if($item->received < $item->quantity && $item->company==$company)
						{
							$item->ponum = $quote->ponum;
							$item->duequantity = $item->quantity - $item->received;

			                $item->etalog = $this->db->where('company',$company)
                                			->where('quote',$quote->id)
                                			->where('itemid',$item->itemid)
                                			->get('etalog')->result();

                            $item->quotedaterequested = $this->db->select('daterequested')
					        ->where('purchasingadmin',$item->purchasingadmin)
					        ->where('quote',$quote->id)
					        ->where('itemid',$item->itemid)
					        ->get('quoteitem')->row();

					         $itemRes = $this->db->select('item_img')
					         ->where('id',$item->itemid)
					         ->get('item')->row();					        
					        
					         if(isset($itemRes) && $itemRes != '')
					         {
					        	$item->item_img = $itemRes->item_img;
					         }
					         else 
					         {
					         	$item->item_img = '';
					         }
					        		
							$items[]=$item;
						}
					}

				}
			}
		}
		//echo '<pre>';print_r($items);die;
		return array('quote'=>$quote,'items'=>$items);
	}

	function checkReceivedPartially($awardid)
	{
		$sql ="SELECT *
		FROM
		".$this->db->dbprefix('awarditem')." WHERE award='$awardid'";

		$ret = array();
		$query = $this->db->query ($sql);
		if ($query->result ())
		{
			foreach($query->result () as $item)
				/*if($item->received>0)
					return true;*/
				if( (date('Y-m-d H:i:s', strtotime( @$item->daterequested."23:59:59")) < date('Y-m-d H:i:s')) && (@$item->received==0 || $item->received>0)  )
					return true;	
		}
		return false;
	}

	function getbacktrack($key)
	{
		$this->db->where('invitation',$key);
		$query = $this->db->get('backtrack');
		if($query->num_rows>0)
			return $query->row();
		else
			return NULL;
	}

	function getawardedbid($quote)
	{
		$this->db->where('quote',$quote);
		$query = $this->db->get('award');

		$item = $query->row();
		if(!$item)
			return false;
		//foreach($result as $item)
		//{

			$this->db->where('id',$item->quote);
			$query = $this->db->get('quote');
			if($query->result())
			{
				$item->quotedetails = $query->row();
				$item->award = $item->id;
				$this->db->where('award',$item->id);
				$query = $this->db->get('awarditem');
				$awarditems = array();
				foreach($query->result() as $awarditem)
				{
					$this->db->where('id',$awarditem->company);
					$query = $this->db->get('company');
					$awarditem->companyname = $query->row('title');
					$awarditem->companydetails = $query->row();
					$awarditems[] = $awarditem;
				}
				$item->items = $awarditems;

				$status = 'complete';
				foreach($item->items as $it)
				{
					if($it->quantity > $it->received)
					{
						$status = 'incomplete';
					}
				}
				$item->status = $status;
			}
		//}
		//echo '<pre>';print_r($ret);die;
		return $item;
	}

	function getbidbyid($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('bid');
		if($query->num_rows>0)
		{
			$ret = $query->row();
	        return $ret;
		}
		return NULL;
	}

	function getinvoices_export($company)
	{
		$search   = '';
		$searches = array();
		$pafilter = '';

		if($this->session->userdata("quote_search"))
		{
			$search = $this->session->userdata("quote_search");
		}

		if($this->session->userdata("pafilter"))
		{
			$pafilter = $this->session->userdata("pafilter");
		}

		//----------edit ends------------------	----------------------------------------------------

		$query = "SELECT invoicenum, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice,
					receiveddate, r.status, r.paymentstatus, r.paymenttype, r.refnum, r.datedue
				   FROM
				   ".$this->db->dbprefix('received')." r,
				   ".$this->db->dbprefix('awarditem')." ai
				  WHERE r.awarditem=ai.id AND ai.company=$company $search
				  $pafilter
				  GROUP BY invoicenum
                  ORDER BY STR_TO_DATE(r.receiveddate, '%m/%d/%Y') DESC
				  ";
		//echo $query;
		//exit;

		$invoicequery = $this->db->query($query);
		$items = $invoicequery->result();

		$invoices = array();
		foreach($items as $invoice)
		{
			$quotesql = "SELECT q.*
					   FROM
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q
					  WHERE r.awarditem=ai.id AND ai.award=a.id
					  AND a.quote=q.id AND invoicenum='{$invoice->invoicenum}'
					  ";
			$quotequery = $this->db->query($quotesql);
			$invoice->quote = $quotequery->row();

			$invoices[]=$invoice;
		}

		return $invoices;
	}





	function getinvoices($company)
	{
		$search='';
		$searches = array();
		
		
        if (@$_POST['searchpaymentstatus'])
        {
        	if($_POST['searchpaymentstatus'] == "Unpaid")
        	{
        	$searches[] = " (r.paymentstatus = 'Unpaid' || r.paymentstatus = 'Requested Payment') ";}
        	else {
            $searches[] = " r.paymentstatus = '{$_POST['searchpaymentstatus']}' ";}
          
        }
        
        
        if(@$_POST['searchstatus'] == "pastdue" || @$_POST['searchkeyword'])
        {  
        	$tempv = "";
        }
        else 
        {
        	      	
        	
        	if(@$_POST['searchfrom'])
			{
				$fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
				$searches[] = " ( date(datedue) >= '$fromdate' OR date(datedue) is NULL ) ";
				//$searches[] = " ( date(receiveddate) >= '$fromdate' ) ";
			}
			if(@$_POST['searchto'])
			{
				$todate = date('Y-m-d', strtotime($_POST['searchto']));
				$searches[] = " ( date(datedue) <= '$todate' OR date(datedue) is NULL ) ";
				//$searches[] = " ( date(receiveddate) <= '$todate' ) ";
			} 		 
        	
        }
		
	
		if(@$_POST['searchkeyword'])
		{
			
			$searches[] = " r.invoicenum LIKE '%{$_POST['searchkeyword']}%'";
		}
		
		
		if(@$_POST['searchstatus'])
		{
			  if($_POST['searchstatus'] == "pastdue")
               {
               	$toda = date('Y-m-d', strtotime(date('m/d/Y')));
            	$searches[] = " ( date(datedue) <= '$toda' OR date(datedue) is NULL ) ";
            	//echo "<pre>"; print_r($searches); die;
                } else 
                {          
				$searches[] = " r.status='{$_POST['searchstatus']}'";
                }
		}

		// ------- note: $_SESSION['quote_search'] and $_SESSION['pafilter'] are used for export function


		if($searches)
		{
			$search = " AND ".implode(" AND ", $searches);
			$this->session->set_userdata("quote_search",$search);
		}
		else
		{
			$this->session->unset_userdata("quote_search");
		}

		if($this->session->userdata("quote_search"))
		{
			$search = $this->session->userdata("quote_search");
		}
		//-----------------------
		$pafilter = '';
		if(@$_POST['searchpurchasingadmin'])
		{
			$pafilter = " AND r.purchasingadmin='".$_POST['searchpurchasingadmin']."'";

			$this->session->set_userdata("pafilter",$pafilter);
			$this->session->set_userdata("searchpurchasingadmin",$_POST['searchpurchasingadmin']);
		}
		else
		{
			$this->session->unset_userdata('pafilter');
		}

		if($this->session->userdata("pafilter"))
		{
			$pafilter = $this->session->userdata("pafilter");
		}

		//----------edit ends------------------	----------------------------------------------------



		 $query = "SELECT invoicenum,  ai.company, ai.purchasingadmin, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice,s.taxrate,
					receiveddate, r.status, r.paymentstatus, r.paymenttype, r.refnum, r.paymentdate, r.datedue, ai.award,r.attachmentname,r.sharewithsupplier, r.quantity, r.invoice_type   
				   FROM
				   ".$this->db->dbprefix('received')." r,
				   ".$this->db->dbprefix('awarditem')." ai,
				    ".$this->db->dbprefix('settings')." s
				  WHERE r.awarditem=ai.id AND ai.purchasingadmin=s.purchasingadmin AND ai.company=$company $search
				  $pafilter
				  GROUP BY invoicenum
                  ORDER BY STR_TO_DATE(r.receiveddate, '%m/%d/%Y') DESC
				  ";
		//echo $query;
		//exit;

		$invoicequery = $this->db->query($query);
		$items = $invoicequery->result();
        //echo "<pre>"; print_r($items); die;
		$invoices = array();
		foreach($items as $invoice)
		{
			$quotesql = "SELECT q.*,u.companyname
					   FROM
					   ".$this->db->dbprefix('received')." r,
					   ".$this->db->dbprefix('awarditem')." ai,
					   ".$this->db->dbprefix('award')." a,
					   ".$this->db->dbprefix('quote')." q,
					   ".$this->db->dbprefix('users')." u
					  WHERE r.awarditem=ai.id AND ai.award=a.id
					  AND a.quote=q.id AND q.purchasingadmin=u.id AND invoicenum='{$invoice->invoicenum}' AND a.id = '{$invoice->award}'
					  ";
			$quotequery = $this->db->query($quotesql);
			$invoice->quote = $quotequery->row();
			
			
			                    // Code for getting discount/Penalty
        if(@$invoice->company && @$invoice->purchasingadmin){

        	$sql = "SELECT duedate, term, penalty_percent, discount_percent, discountdate FROM " .$this->db->dbprefix('invoice_cycle') . " where company='" . $invoice->company . "'
				and purchasingadmin = '". $invoice->purchasingadmin ."'";
        	//echo $sql;
        	$resultinvoicecycle = $this->db->query($sql)->row();
        	
        	$invoice->penalty_percent = 0;
        	$invoice->penaltycount = 0;
        	$invoice->discount_percent =0;
        	
        	if($resultinvoicecycle){

        		if((@$resultinvoicecycle->penalty_percent || @$resultinvoicecycle->discount_percent) ){

        			if(@$invoice->datedue){
        				
        				if(@$invoice->paymentstatus == "Paid" && @$invoice->paymentdate){
        					$oDate = $invoice->paymentdate;
        					$now = strtotime($invoice->paymentdate);
        				}else {
        					$oDate = date('Y-m-d');
        					$now = time();
        				}
        				
        				$d1 = strtotime($invoice->datedue);
        				$d2 = strtotime($oDate);
        				$datediff =  (date('Y', $d2) - date('Y', $d1))*12 + (date('m', $d2) - date('m', $d1));
        				if(is_int($datediff) && $datediff > 0) {
							
        					$invoice->penalty_percent = $resultinvoicecycle->penalty_percent;
        					$invoice->penaltycount = $datediff;
        					
        				}else{

        					$discountdate = $resultinvoicecycle->discountdate;
        					if(@$discountdate){
        						$exploded = explode("-",@$invoice->datedue);
        						$exploded[2] = $discountdate;
        						$discountdt = implode("-",$exploded);
        						if ($now < strtotime($discountdt)) {        						
        							$invoice->discount_percent = $resultinvoicecycle->discount_percent;
        						}
        					}
        				}
        			}
        			
        		}
        	}       	
        	
        }
			
			
			$invoices[]=$invoice;
		}
        //echo "<pre>",print_r($invoices);  die;
		return $invoices;
	}

	function getinvoicesdetailsformail($company,$invoicenumber)
	{
		$search='';
		if($invoicenumber)
		{
			$search .= " AND r.invoicenum ='{$invoicenumber}'";
		}
		$pafilter = '';
		if(@$_POST['searchpurchasingadmin'])
			$pafilter = " AND r.purchasingadmin='".$_POST['searchpurchasingadmin']."'";
		/*$query = "SELECT r.invoicenum,q.ponum,od.quantity,o.taxpercent, ROUND(SUM(ai.ea * r.quantity),2) totalprice, receiveddate, r.status, r.paymentstatus, r.paymenttype, r.refnum, r.datedue,u.username,ai.company,c.title,c.username as supplierusername,ai.itemid,od.orderid,o.ordernumber,ai.itemcode,i.itemname,od.price,c.address,c.phone,date_format(datedue,'%m/%d/%Y') as DueDate,o.taxpercent,u.email,a.awardedon
				   FROM
				   ".$this->db->dbprefix('received')." r
				   LEFT JOIN  ".$this->db->dbprefix('awarditem')." ai ON r.awarditem =ai.id
				   LEFT JOIN  ".$this->db->dbprefix('users')." u ON u.purchasingadmin = r.purchasingadmin
				   LEFT JOIN  ".$this->db->dbprefix('company')." c ON ai.company = c.id
				   LEFT JOIN  ".$this->db->dbprefix('item')." i ON i.id = ai.itemid
				   LEFT JOIN ".$this->db->dbprefix('orderdetails')."  od ON od.itemid = ai.itemid
				   LEFT JOIN ".$this->db->dbprefix('order')."  o ON o.id = od.orderid
				   LEFT JOIN ".$this->db->dbprefix('award')."  a ON a.id = ai.award
				   LEFT JOIN ".$this->db->dbprefix('quote')."  q ON q.id = a.quote
				  WHERE r.awarditem=ai.id AND ai.company=$company $search
				  $pafilter
				  GROUP BY invoicenum
                  ORDER BY STR_TO_DATE(r.receiveddate, '%m/%d/%Y') DESC
				  ";*/
		
		$query = "SELECT r.invoicenum,q.ponum,r.quantity,o.taxpercent, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice, receiveddate, r.status, r.paymentstatus, r.paymenttype, r.refnum, r.datedue,u.username,ai.company,c.title,c.username as supplierusername,ai.itemid,od.orderid,o.ordernumber,ai.itemcode,i.itemname,od.price,c.address,c.phone,date_format(datedue,'%m/%d/%Y') as DueDate,u.email,a.awardedon,ai.ea,r.purchasingadmin
				   FROM
				   ".$this->db->dbprefix('received')." r
				   LEFT JOIN  ".$this->db->dbprefix('awarditem')." ai ON r.awarditem =ai.id
				   LEFT JOIN  ".$this->db->dbprefix('users')." u ON u.purchasingadmin = r.purchasingadmin
				   LEFT JOIN  ".$this->db->dbprefix('company')." c ON ai.company = c.id
				   LEFT JOIN  ".$this->db->dbprefix('item')." i ON i.id = ai.itemid
				   LEFT JOIN ".$this->db->dbprefix('orderdetails')."  od ON od.itemid = ai.itemid
				   LEFT JOIN ".$this->db->dbprefix('order')."  o ON o.id = od.orderid
				   LEFT JOIN ".$this->db->dbprefix('award')."  a ON a.id = ai.award
				   LEFT JOIN ".$this->db->dbprefix('quote')."  q ON q.id = a.quote
				  WHERE r.awarditem=ai.id AND ai.company=$company $search
				  $pafilter
				  GROUP BY invoicenum
                  ORDER BY STR_TO_DATE(r.receiveddate, '%m/%d/%Y') DESC
				  ";

		$invoicequery = $this->db->query($query);
		$items = $invoicequery->result();

		return $items;
	}
	
		
	function getpurchaserinvoices($company, $purchasingadmin){
		
		$pafilter = '';
		if(@$purchasingadmin)
			$pafilter = " AND r.purchasingadmin='".$purchasingadmin."'";
				
		$query = "SELECT r.invoicenum,r.awarditem as awarditem, q.ponum,r.quantity,o.taxpercent, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice, receiveddate, r.status, r.paymentstatus, r.paymenttype, r.refnum, r.datedue,u.username,ai.company,c.title,c.username as supplierusername,ai.itemid,od.orderid,o.ordernumber,ai.itemcode,i.itemname,od.price,c.address,c.phone,date_format(datedue,'%m/%d/%Y') as DueDate,u.email,a.awardedon,ai.ea,r.purchasingadmin
				   FROM
				   ".$this->db->dbprefix('received')." r
				   LEFT JOIN  ".$this->db->dbprefix('awarditem')." ai ON r.awarditem =ai.id
				   LEFT JOIN  ".$this->db->dbprefix('users')." u ON u.purchasingadmin = r.purchasingadmin
				   LEFT JOIN  ".$this->db->dbprefix('company')." c ON ai.company = c.id
				   LEFT JOIN  ".$this->db->dbprefix('item')." i ON i.id = ai.itemid
				   LEFT JOIN ".$this->db->dbprefix('orderdetails')."  od ON od.itemid = ai.itemid
				   LEFT JOIN ".$this->db->dbprefix('order')."  o ON o.id = od.orderid
				   LEFT JOIN ".$this->db->dbprefix('award')."  a ON a.id = ai.award
				   LEFT JOIN ".$this->db->dbprefix('quote')."  q ON q.id = a.quote
				  WHERE r.awarditem=ai.id AND ai.company=$company $pafilter
				  GROUP BY invoicenum
                  ORDER BY STR_TO_DATE(r.receiveddate, '%m/%d/%Y') DESC
				  ";

		$invoicequery = $this->db->query($query);
		$items = $invoicequery->result();

		return $items;
		
	}	
	
	
	function getinvoicebynum($invoicenum, $company,$invoicequote)
	{

		$invoicesql = "SELECT invoicenum, ai.company, ai.purchasingadmin, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice, r.status,
					 r.paymentstatus, r.paymenttype, r.refnum, r.transfernum, receiveddate, r.datedue, r.paymentdate, r.invoice_type ,r.attachmentname , r.sharewithsupplier, r.attachment
				   FROM
				   ".$this->db->dbprefix('received')." r,
				   ".$this->db->dbprefix('awarditem')." ai,
				   ".$this->db->dbprefix('award')." a 
				  WHERE r.awarditem=ai.id AND ai.award = a.id AND a.quote='{$invoicequote}' 
				  AND invoicenum='{$invoicenum}'
				  AND ai.company=$company
				  GROUP BY invoicenum
				  ";
		//echo $invoicesql;
		$invoicequery = $this->db->query($invoicesql);
		$invoice = $invoicequery->row();

		
		        // Code for getting discount/Penalty
        if(@$invoice->company && @$invoice->purchasingadmin){

        	$sql = "SELECT duedate, term, penalty_percent, discount_percent, discountdate FROM " .$this->db->dbprefix('invoice_cycle') . " where company='" . $invoice->company . "'
				and purchasingadmin = '". $invoice->purchasingadmin ."'";
        	//echo $sql;
        	$resultinvoicecycle = $this->db->query($sql)->row();
        	
        	$invoice->penalty_percent = 0;
        	$invoice->penaltycount = 0;
        	$invoice->discount_percent =0;
        	
        	if($resultinvoicecycle){

        		if((@$resultinvoicecycle->penalty_percent || @$resultinvoicecycle->discount_percent) ){

        			if(@$invoice->datedue){
        				
        				if(@$invoice->paymentstatus == "Paid" && @$invoice->paymentdate){
        					$oDate = $invoice->paymentdate;
        					$now = strtotime($invoice->paymentdate);
        				}else {
        					$oDate = date('Y-m-d');
        					$now = time();
        				}
        				$d1 = strtotime($invoice->datedue);
        				$d2 = strtotime($oDate);
        				$datediff =  (date('Y', $d2) - date('Y', $d1))*12 + (date('m', $d2) - date('m', $d1));
        				if(is_int($datediff) && $datediff > 0) {
							
        					$invoice->penalty_percent = $resultinvoicecycle->penalty_percent;
        					$invoice->penaltycount = $datediff;
        					
        				}else{

        					$discountdate = $resultinvoicecycle->discountdate;
        					if(@$discountdate){    
        						$exploded = explode("-",@$invoice->datedue);
        						$exploded[2] = $discountdate;
        						$discountdt = implode("-",$exploded);
        						if ($now < strtotime($discountdt)) {        						
        							$invoice->discount_percent = $resultinvoicecycle->discount_percent;
        							$invoice->discount_date = date('m/d/Y', strtotime($discountdt));
        						}
        					}
        				}
        			}
        			
        		}
        	}       	
        	
        }
		
		$quotesql = "SELECT quote
				   FROM
				   ".$this->db->dbprefix('received')." r,
				   ".$this->db->dbprefix('awarditem')." ai,
				   ".$this->db->dbprefix('award')." a
				  WHERE r.awarditem=ai.id AND ai.award=a.id AND a.quote='{$invoicequote}'
				  AND invoicenum='{$invoicenum}' AND ai.company=$company
				  ";
		$quotequery = $this->db->query($quotesql);
		$invoice->quote = $quotequery->row('quote');

		$itemsql = "SELECT
					r.*, ai.itemcode, c.title companyname,
					ai.itemname, ai.ea, ai.unit, ai.daterequested, ai.costcode, ai.notes, ai.quantity as aiquantity,i.item_img 
				  FROM
				  ".$this->db->dbprefix('received')." r,
				  ".$this->db->dbprefix('awarditem')." ai,
				  ".$this->db->dbprefix('award') . " a,
				  ".$this->db->dbprefix('company')." c,
				  ".$this->db->dbprefix('item')." i
				  WHERE r.awarditem=ai.id  AND ai.company=$company AND ai.award=a.id AND i.id = ai.itemid AND a.quote='{$invoicequote}'
				  AND invoicenum='{$invoicenum}'
				  GROUP BY awarditem
				  ";
		//echo $itemsql;
		$itemquery = $this->db->query($itemsql);
		$invoice->items = $itemquery->result();
		return $invoice;
	}

	function getpendinginvoices($company)
	{

		$invoicesql = "SELECT r.id, invoicenum, r.paymentstatus, r.paymenttype, r.refnum, r.datedue, r.purchasingadmin, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice, r.alertsentdate, q.ponum, q.id as quoteid 
				   FROM
				   ".$this->db->dbprefix('received')." r,
				   ".$this->db->dbprefix('awarditem')." ai,
				   ".$this->db->dbprefix('award')." a,
				   ".$this->db->dbprefix('quote')." q
				  WHERE r.awarditem=ai.id
				  AND ai.award = a.id
				  AND a.quote = q.id
				  AND ai.company=$company
				  AND r.paymentstatus <> 'Paid'
				  AND r.datedue <= CURDATE()
				  GROUP BY invoicenum
				  ";
		//echo $invoicesql;
		$invoicequery = $this->db->query($invoicesql);
		$invoice = $invoicequery->result();
		return $invoice;
	}

	function setalertdate($alertarray){

		$this->db->where('id',$alertarray['id']);
		$returnresult = $this->db->update('received',array('alertsentdate'=>$alertarray['alertsentdate']));
		return $returnresult;
	}
		
	
    function get_quotes_error_log($quote,$companyid)
    {
        $this->db->select("quote_errorlog.*,company.*");
        $this->db->where('quoteid', $quote);
        $this->db->where('quote_errorlog.companyid', $companyid);
        $this->db->join("company",'company.id = quote_errorlog.companyid');
        $query = $this->db->get('quote_errorlog');
          if ($query->num_rows > 0) {
            $ret = $query->result();
            return $ret;
          }
          return NULL;

    }
    
    
    function getforthcomings($company)
	{		
		$this->db->order_by("podate", "asc");
		$quotes = $this->db->get('quote')->result();
		$totalQtyDue = 0;
		$count = count ($quotes);
		$items = array();
		if ($count >= 1)
		{
			foreach ($quotes as $quote)
			{
				$this->db->where('quote',$quote->id);
				$awarded = $this->db->get('award')->row();
				if($awarded)
				{
					$this->db->where('award',$awarded->id);
					$this->db->where('company',$company);
					$awardeditems = $this->db->get('awarditem')->result();
					if($awardeditems  && $this->checkitemdue($awarded->id))
					{	$olddate="";
						$qtydue= 0;
						
						foreach($awardeditems as $item)
						{			
							if($item->daterequested){					
							$orgdate=date('Y-m-d', strtotime( $item->daterequested));
							$currentdate=date('Y-m-d');
							$date1 = date_create($currentdate);
							$date2 = date_create($orgdate);
							$diff12 = date_diff($date1, $date2);
							$diffindays = $diff12->days;							
							if($diffindays>=0 && $diffindays<=7 && ($diff12->invert==0) ){								
								$items[$quote->ponum]['quoteid'] = $quote->id;
								$items[$quote->ponum]['podate'] = $quote->podate;
								$items[$quote->ponum]['awardid'] = $awarded->id;	
								if($olddate==""){
								$items[$quote->ponum]['datereqested'] = $item->daterequested;									
								$olddate = $item->daterequested;
								}elseif(strtotime($item->daterequested)<strtotime($olddate)){
									$items[$quote->ponum]['datereqested'] = $item->daterequested;										
								}
								$qtydue += ($item->quantity-$item->received);
								$totalQtyDue += ($item->quantity-$item->received);
								$items[$quote->ponum]['quantitydue'] = $qtydue;
								
							}							
							}	
						}
					}
				}
			}
			$this->session->set_userdata('forthcomingQtyDue',$totalQtyDue);	
		}
		return $items;
	}
	
	
	
	function checkitemdue($awardid)
	{
		$sql ="SELECT received, quantity 
		FROM
		".$this->db->dbprefix('awarditem')." WHERE award='$awardid'";

		$ret = array();
		$query = $this->db->query ($sql);
		if ($query->result ())
		{
			foreach($query->result () as $item)
				if($item->received<$item->quantity)
					return true;				
		}
		return false;
	}

}
?>