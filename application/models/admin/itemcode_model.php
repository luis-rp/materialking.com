<?php

class itemcode_model extends Model {

    function itemcode_model() {
        parent::Model();
    }

    function get_count_all_itemcodes() {
    	   
        if(@$_POST['searchQuery']){
        		$wheres = "itemname LIKE '%{$_POST['searchQuery']}%' OR itemcode LIKE '%{$_POST['searchQuery']}%' ";
        		$this->db->where($wheres);         		
        }   
        if(@$_POST['searchitemname']){
			$wheres = " itemname LIKE '%{$_POST['searchitemname']}%' OR itemcode LIKE '%{$_POST['searchitemname']}%'";
        	$this->db->where($wheres);         
        }
            
        if(@$_POST['searchcategory'])
        	 $this->db->where('category' , $_POST['searchcategory']); 
          	 
        if(@$pa && @$pa!='1'){            	
            $wheres = " AND (purchasingadmin='{$pa}' OR purchasingadmin is NULL)  ";   
        	$this->db->where($wheres); 	
        }       
    	
        return $this->db->count_all_results('item');
    }    
    
    function get_itemcodes($limit = 100, $offset = 0,$category = '') {
        if ($offset == 0) {
            $newoffset = 0;
        } else {
            $newoffset = $offset;
        }
        $ti = $this->db->dbprefix('item');
        $ta = $this->db->dbprefix('award');
        $tai = $this->db->dbprefix('awarditem');

        $pa = $this->session->userdata('purchasingadmin');

        $where = " WHERE 1=1 ";
        if(@$_POST['searchQuery'])
            $where .= " AND (i.itemname LIKE '%{$_POST['searchQuery']}%' OR i.itemcode LIKE '%{$_POST['searchQuery']}%')";
            
        if(@$_POST['searchitemname'])
            $where .= " AND i.itemname LIKE '%{$_POST['searchitemname']}%' OR i.itemcode LIKE '%{$_POST['searchitemname']}%'";
        if(@$_POST['searchcategory'])
            $where .= " AND i.category = '{$_POST['searchcategory']}'";
       
        if($category)
            $where .= " AND i.category = '{$category}'";
                
        if($pa && $pa!='1')    
            $where .= " AND (i.purchasingadmin='{$pa}' OR i.purchasingadmin is NULL OR i.purchasingadmin = '1')  ";   
            
       if(@$_POST['isfavorite'])
            $where .= " AND fi.isfavorite = '{$_POST['isfavorite']}'";
            
        $sql = "SELECT i.*, IFNULL(IF(group_concat(distinct q.pid)='".@$this->session->userdata('managedprojectdetails')->id."',max(a.awardedon),''), IF(group_concat(distinct o.project)='".@$this->session->userdata('managedprojectdetails')->id."',max(o.purchasedate),'')) AS awardedon
, if(IFNULL(o.project,q.pid)='".@$this->session->userdata('managedprojectdetails')->id."',sum(ai.totalprice),'') totalpurchase,IFNULL( IF(o.project='".@$this->session->userdata('managedprojectdetails')->id."',group_concat(distinct o.project),group_concat(distinct q.pid)),IF(q.pid='".@$this->session->userdata('managedprojectdetails')->id."',group_concat(distinct q.pid),group_concat(distinct o.project))) AS project,fi.isfavorite
                FROM
                $ti i
                LEFT JOIN $tai ai ON i.id=ai.itemid
                LEFT JOIN $ta a ON ai.award=a.id AND ai.purchasingadmin='$pa'
                LEFT JOIN ".$this->db->dbprefix('quote') ." q ON q.id = a.quote
                LEFT JOIN ".$this->db->dbprefix('orderdetails') ." od ON i.id=od.itemid 
				LEFT JOIN ".$this->db->dbprefix('order') ." o ON od.orderid = o.id
				LEFT JOIN ".$this->db->dbprefix('favoriteitem') ." fi ON fi.itemid = i.id AND fi.purchasingadmin='$pa' 
                $where 
                GROUP BY i.id
                ORDER BY awardedon DESC LIMIT $newoffset, $limit ";
   // echo '<pre>',$sql;die;
  
        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
         //   echo '<pre>',print_r($result);die;
            $ret = array();
            $where1 = "";
            if(@$this->session->userdata('managedprojectdetails')->id)
            $where1 = " AND o.project='".$this->session->userdata('managedprojectdetails')->id."' ";
            
            foreach ($result as $item)
            {
                $item->poitems = $this->getpoitems($item->id);
                $item->minprices = $this->getminimumprices($item->id);
                $item->tierprices = $this->gettierprices($item->id);           
                
                $orderSql = "SELECT *,od.quantity as qty FROM pms_order o,
		        			 pms_orderdetails od
		        			 WHERE o.id=od.orderid
		        			 AND o.purchasingadmin='".$this->session->userdata('purchasingadmin')."' {$where1} 
		        			 AND od.itemid={$item->id} GROUP BY od.orderid";
                
                $orderRes = $this->db->query($orderSql)->result();
                //if($item->poitems && @$item->poitems[0])
                    //$item->awardedon = $item->poitems[0]->awardedon;
	//echo '<pre>',print_r($item)
			$orderQty=0;
				if(isset($orderRes))
				{
					foreach ($orderRes as $k=>$v)
					{
						$orderQty += $v->qty; 
					}	
				}
                $item->totalpoprice = 0;
                $item->qty = 0;
                
                if ($item->poitems)
                    foreach ($item->poitems as $po) {
                    	
                    	if($item->project == @$this->session->userdata('managedprojectdetails')->id)
                    	{
                        	$item->totalpoprice += $po->totalprice;
                    	}
                    	else 
                    	{
                    		$item->totalpoprice +=0;
                    	}
                        $item->qty += $po->quantity;
                    }
				$item->qty += $orderQty;
                /*$sql2 = "SELECT (SUM(od.quantity * od.price) + (SUM(od.quantity * od.price) * o.taxpercent / 100))
		    	 totalprice2 FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
                WHERE od.orderid=o.id AND od.itemid = ".$item->id." AND o.purchasingadmin='$pa'";*/
                
                $sql2 = "SELECT (SUM(od.quantity * od.price)) 
		    	 totalprice2, od.shipping  FROM ".$this->db->dbprefix('order')." o, ".$this->db->dbprefix('orderdetails')." od
                WHERE od.orderid=o.id AND od.itemid = ".$item->id." {$where1} AND o.purchasingadmin='$pa'";
                
                $query2 = $this->db->query($sql2);
                 if ($query2->result()) {
            		$result2 = $query2->result();
                	if(isset($result2[0]->totalprice2)){
                		if($item->project == @$this->session->userdata('managedprojectdetails')->id)
                    	{
	                		$item->totalpoprice += $result2[0]->totalprice2;
	                		$item->ordershipping = $result2[0]->shipping;
	                    }
	                    else 
	                    {	                    	
	                		$item->totalpoprice += 0;
	                		$item->ordershipping = 0;
	                    }
                	}
                 }

                //$item->minprices = $this->getminimumprices($item->itemcode);
                $ret[] = $item;
            }
            //print_r($ret);die;
            return $ret;
        } else {
            return null;
        }
    }

    function get_itemcodescatcode($subcatid)
    {
        if ($offset == 0) {
            $newoffset = 0;
        } else {
            $newoffset = $offset;
        }

        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('item') . " where subcategory=" . $subcatid . " ORDER BY itemname ASC";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item) {
                $item->poitems = $this->getpoitems($item->id);

                $item->minprices = $this->getminimumprices($item->id);

                $item->totalpoprice = 0;
                if ($item->poitems)
                    foreach ($item->poitems as $po) {
                        $item->totalpoprice += $po->totalprice;
                    }
                $item->minprices = $this->getminimumprices($item->itemcode);
                $ret[] = $item;
            }
            //print_r($ret);die;
            return $ret;
        } else {
            return null;
        }
    }

    // counting total itemcodes
    function total_itemcode() {
        $query = $this->db->count_all_results('item');
        return $query;
    }

    function getparentcategory()
    {
    	$sql = "SELECT * FROM " . $this->db->dbprefix('category') . "
        		WHERE parent_id=0";
        $leaves = $this->db->query($sql)->result();
        return $leaves;
    }
    
     function get_all_sub_cats($parent_cat_id, $level_string,$parent_cat_name,$categoryID='',$newLevel='')
	  {
	      $return_str='';
	      if(!$level_string)
	      {
	          $level_string='';
	          $parent_cat_name = '';
	      }
	      
	        $peis="";                   
	     	$qry =  $this->db->query("select * from ". $this->db->dbprefix('category') ." where parent_id IN('{$parent_cat_id}')");
	     	$res = $qry->result();
	        $selected = '';
	        
	          foreach($res as $key=>$val)
	          {
	          	if($val->parent_id == 0)
	          	{
	          		$level_string = ' ';
	          	}
	          	else 
	          	{
	          		$level_string = '->';
	          	}	
	          	if($this->session->userdata('usertype_id')!=1)
	            {
	            	$peis=$this->session->userdata('purchasingadmin');
	            	$sql1 = "SELECT * FROM " . $this->db->dbprefix('item') . " WHERE category IN('$val->id') AND purchasingadmin='$peis'";
	            }
	            else 
	            {
	            	$sql1 = "SELECT * FROM " . $this->db->dbprefix('item') . " WHERE category IN('$val->id')";
	            }
	            if($categoryID == $val->id)
	            {
	            	$selected = ' selected ';
	            }
	            else 
	            {
	            	$selected = '';
	            }
	            
	            	$item = $this->db->query($sql1)->result(); 
            		$count=number_format(count($item));
	          	
	              $return_str .="<option value=\"{$val->id}\" style=\"padding-left:10px\" {$selected}>{$newLevel} {$parent_cat_name}{$level_string}{$val->catname}({$count})</option>";
	              $return_str .= $this->get_all_sub_cats($val->id, $level_string.' &nbsp;&nbsp; ',$val->catname,$categoryID,$newLevel.' &nbsp;&nbsp; ');	             
	          }	    
	    
        return $return_str;
	  
	  }      
           
    function getcategories()
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('category') . "
        		WHERE id NOT IN (SELECT distinct(parent_id) FROM " . $this->db->dbprefix('category') . ")";
        $leaves = $this->db->query($sql)->result();
        $ret = array();

        foreach($leaves as $leaf)
        {
            $parent = $leaf->parent_id;
          
            while($parent)
            {
                $sql = "SELECT * FROM " . $this->db->dbprefix('category') . " WHERE id='$parent'  ";
                $pcat = $this->db->query($sql)->row();
                
                if($pcat)
                {
                    $parent = $pcat->parent_id;
                    $leaf->catname =  $pcat->catname . ' > ' . $leaf->catname;
                }
                else
                {
                    break 1;
                }
            }
         
            $peis="";
            if($this->session->userdata('usertype_id')!=1)
            {
            	$peis=$this->session->userdata('purchasingadmin');
            	$sql1 = "SELECT * FROM " . $this->db->dbprefix('item') . " WHERE category='$leaf->id' AND purchasingadmin='$peis'";
            }
            else 
            {
            	$sql1 = "SELECT * FROM " . $this->db->dbprefix('item') . " WHERE category='$leaf->id'  ";
            }
            
            $item = $this->db->query($sql1)->result(); 
            $count=number_format(count($item));
            $leaf->catname .="(".$count.")";
            
            $ret[] = $leaf;
        }
        
        $parentCategory =  $this->getparentcategory();
        $ret= array_merge($parentCategory,$ret);
        $this->aasort($ret, 'catname');
      //  echo '<pre>'; print_r($ret);die;

        return $ret;

    }
    
    function getdesigncategories()
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix('designcategory') . "
        		WHERE id NOT IN (SELECT distinct(parent_id) FROM " . $this->db->dbprefix('designcategory') . ")";
        $leaves = $this->db->query($sql)->result();
        $ret = array();

        foreach($leaves as $leaf)
        {
            $parent = $leaf->parent_id;
            while($parent)
            {
                $sql = "SELECT * FROM " . $this->db->dbprefix('designcategory') . " WHERE id='$parent'  ";
                $pcat = $this->db->query($sql)->row();
                if($pcat)
                {
                    $parent = $pcat->parent_id;
                    $leaf->catname = $pcat->catname . ' > ' . $leaf->catname;
                }
                else
                {
                    break 1;
                }
            }
            
            $sql1 = "SELECT * FROM " . $this->db->dbprefix('designbook_category') . " WHERE categoryid='$leaf->id'  ";
            $item = $this->db->query($sql1)->result(); 
            $count=number_format(count($item));
            $leaf->catname .="(".$count.")";
            
            $ret[] = $leaf;
        }      
        $this->aasort($ret, 'catname');
        return $ret;

    }
    
	function aasort (&$array, $key)
	{
	    $sorter=array();
	    $ret=array();
	    reset($array);
	    foreach ($array as $ii => $va)
	    {
	        $sorter[$ii]=$va->$key;
	    }
	    $sortflag = 14;//SORT_NATURAL ^ SORT_FLAG_CASE;

	    asort($sorter, $sortflag );
	    foreach ($sorter as $ii => $va)
	    {
	        $ret[$ii]=$array[$ii];
	    }
	    $array=$ret;
	}

    function getpoitems($itemid)
    {
        $projectwhere = '';
        $mp = $this->session->userdata('managedprojectdetails');
        if($mp)
            $projectwhere = " AND q.pid='".$mp->id."'";
        $sql = "SELECT ai.*, c.title companyname, q.ponum, a.awardedon, a.quote,IFNULL(ai.received,0) as  newreceived
			   	FROM
				" . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a,
				" . $this->db->dbprefix('quote') . " q, " . $this->db->dbprefix('company') . " c
				WHERE
				ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "' AND
				ai.award=a.id AND a.quote=q.id AND ai.company=c.id AND ai.itemid='$itemid'
				$projectwhere
				ORDER BY a.awardedon DESC
				";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item) {
                /*$this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
                $this->db->where('company', $item->company);
                if ($this->db->get('network')->result()) {*/
                	
                	$wherecode = "";	
                	if(@$this->session->userdata('managedprojectdetails')->id){
            			$wherecode = "AND q.pid=".$this->session->userdata('managedprojectdetails')->id;
				 	}
                		            	
            	 // Code for getting discount/Penalty per invoice
					$query = "SELECT invoicenum, ai.company, ai.purchasingadmin, ROUND(SUM(ai.ea * if(r.invoice_type='fullpaid',ai.quantity,if(r.invoice_type='alreadypay',0,r.quantity)) ),2) totalprice , r.paymentdate, r.datedue, r.paymentstatus 
			 FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,				   
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q WHERE r.awarditem=ai.id AND ai.award=a.id AND a.quote=q.id {$wherecode} AND ai.id='".$item->id."' GROUP by invoicenum";		
					
					$invoicequery = $this->db->query($query);
        			$itemsinv = $invoicequery->result();
                    
        			if($itemsinv){

        				foreach ($itemsinv as $invoice) {


        					
        					if(@$invoice->company && @$invoice->purchasingadmin){

        						$sql = "SELECT duedate, term, penalty_percent, discount_percent, discountdate FROM " .$this->db->dbprefix('invoice_cycle') . " where company='" . $invoice->company . "'
				and purchasingadmin = '". $invoice->purchasingadmin ."'";
        						//echo $sql;
        						$resultinvoicecycle = $this->db->query($sql)->row();

        						$penalty_percent = 0;
        						$penaltycount = 0;
        						$discount_percent =0;

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

        										$penalty_percent = $resultinvoicecycle->penalty_percent;
        										$penaltycount = $datediff;

        									}else{

        										$discountdate = $resultinvoicecycle->discountdate;
        										if(@$discountdate){

        											if ($now < strtotime($discountdate)) {
        												$discount_percent = $resultinvoicecycle->discount_percent;
        											}
        										}
        									}
        									
        									
        									if(@$discount_percent){

        										$item->totalprice = $item->totalprice - ($invoice->totalprice*$discount_percent/100);
        									}

        									if(@$penalty_percent){

        										$item->totalprice = $item->totalprice + (($invoice->totalprice*$penalty_percent/100)*@$penaltycount);
        									}
        									
        								}

        							}
        						}

        					}

        				}

        			}      			
        			// Code for getting discount/Penalty Ends
                	
                	
                    $ret[] = $item;
               // }
            }
            
            return $ret;
        }
        return NULL;
    }

    function getpoitems2($itemid)
    {
    	$projectwhere = '';
    	$mp = $this->session->userdata('managedprojectdetails');
    	if($mp)
    	$projectwhere = " AND q.pid='".$mp->id."'";
    	$sql = "SELECT sum(ai.totalprice) as totalprice, ai.company, ai.ea, ai.daterequested as daterequested, c.title companyname, GROUP_CONCAT(q.ponum,' : $',ai.totalprice) as ponum, a.awardedon, a.quote
			   	FROM
				" . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a,
				" . $this->db->dbprefix('quote') . " q, " . $this->db->dbprefix('company') . " c
				WHERE
				ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "' AND
				ai.award=a.id AND a.quote=q.id AND ai.company=c.id AND ai.itemid='$itemid'
				$projectwhere
        		group by ai.daterequested
				ORDER BY ai.daterequested DESC

				";
    	//echo $sql;
    	$query = $this->db->query($sql);
    	if ($query->num_rows > 0) {
    		$result = $query->result();
    		$ret = array();
    		foreach ($result as $item) {
    			$this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
    			$this->db->where('company', $item->company);
    			if ($this->db->get('network')->result()) {
    				$ret[] = $item;
    			}
    		}
    		return $ret;
    	}
    	return NULL;
    }

    function getminimumprices($itemid)
    {
        $sql = "SELECT c.title companyname,q.id,q.ponum, m.*
			   	FROM
				" . $this->db->dbprefix('minprice') . " m,
				" . $this->db->dbprefix('company') . " c,
				" . $this->db->dbprefix('quoteitem') . " qi,
				" . $this->db->dbprefix('quote') . " q,
				" . $this->db->dbprefix('awarditem') . " ai
				WHERE
				m.company=c.id AND m.itemid='$itemid' 
				AND qi.itemid = m.itemid AND qi.purchasingadmin = m.purchasingadmin
				AND q.id = qi.quote AND qi.purchasingadmin = m.purchasingadmin
				AND ai.company = c.id
				AND m.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
				AND q.pid='".$this->session->userdata('managedprojectdetails')->id."'
				GROUP By c.id";
      //  echo '<pre>', $sql; 
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item) {
               /* $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
                $this->db->where('company', $item->company);
                $this->db->where('status', 'Active');
                if ($this->db->get('network')->result()) {*/
                    $ret[] = $item;
               // }
            }
            return $ret;
        }
        return NULL;
    }

    function gettierprices($itemid)
    {
        $pa = $this->session->userdata('purchasingadmin');
        $sql = "select c.id companyid, c.title companyname, ci.ea listprice
				FROM " . $this->db->dbprefix('company') . " c,
				" . $this->db->dbprefix('companyitem') . " ci,
				" . $this->db->dbprefix('network') . " n
				WHERE c.id=ci.company AND c.id=n.company AND
				n.purchasingadmin='" . $pa . "'
				AND ci.itemid='" . $itemid . "' AND ci.ea != 0";
        //echo $sql;//die;
        $recs = $this->db->query($sql)->result();
        $ret = array();
        foreach ($recs as $rec)
        {
            $rec->price = $rec->listprice;
            $sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$pa' AND company='" . $rec->companyid . "'";

            $tier = $this->db->query($sql)->row();
           
            if (@$tier->tier)
            {
                $tier = $tier->tier;
                $this->db->where('company', $rec->companyid);
                $pt = $this->db->get('tierpricing')->row();
               
                if (@$pt)
                {
                    $deviation = $pt->$tier;
                    $price = $rec->listprice + ($rec->listprice * $deviation / 100);
                    $price = number_format($price, 2);
                    $rec->price = $price;
                }
            }
			
            $ret[] = $rec;
        }
        return $ret;
    }

    function gettierpriceswithqtydiscount($itemid,$quantity)
    {
        $pa = $this->session->userdata('purchasingadmin');
        $sql = "select c.id companyid, c.title companyname, ci.ea listprice
				FROM " . $this->db->dbprefix('company') . " c,
				" . $this->db->dbprefix('companyitem') . " ci,
				" . $this->db->dbprefix('network') . " n
				WHERE c.id=ci.company AND c.id=n.company AND
				n.purchasingadmin='" . $pa . "'
				AND ci.itemid='" . $itemid . "' AND c.isdeleted=0  AND n.status='Active' AND ci.ea != 0 GROUP BY c.id";
        //echo $sql;//die;
        $recs = $this->db->query($sql)->result();
        $ret = array();
        foreach ($recs as $rec)
        {
        	$discountedquantityprice = $this->getnewprice($itemid,$rec->companyid, $quantity);
        	if($discountedquantityprice>0){
        		$rec->price = $discountedquantityprice;
        		$rec->listprice = $discountedquantityprice;
        	}else
           	    $rec->price = $rec->listprice;

            $sql = "select tier from " . $this->db->dbprefix('purchasingtier') . "
				    where purchasingadmin='$pa' AND company='" . $rec->companyid . "'";

            $sqltier = "select tierprice from " . $this->db->dbprefix('companyitem') . "
				    where itemid='$itemid' AND company='" . $rec->companyid . "' AND type = 'Supplier'";

            $istierprice = $this->db->query($sqltier)->row();
            if($istierprice){
            	$istier = $istierprice->tierprice;
            }else
            	$istier = 0;

            $tier = $this->db->query($sql)->row();
            if ($tier && $istier)
            {
                $tier = $tier->tier;
                $this->db->where('company', $rec->companyid);
                $pt = $this->db->get('tierpricing')->row();
                if ($pt)
                {
                    $deviation = $pt->$tier;
                    $price = $rec->listprice + ($rec->listprice * $deviation / 100);
                    $price = number_format($price, 2);
                    $rec->price = $price;
                }
            }
            
            
            $purchasingadmin = $this->session->userdata('purchasingadmin');     
        $resultprice = $this->db->select('p.price')->from('purchasingtier_item p')->join('company c','p.company=c.id')->where('p.purchasingadmin', $purchasingadmin)->where('p.company', $rec->companyid)->where('p.itemid', $itemid)->where('c.isdeleted', 0)->get()->row();
        if($resultprice){
        	if($resultprice->price)
        		$rec->price = $resultprice->price;
        }

            $ret[] = $rec;
        }
        return $ret;
    }

    function getnewprice($itemid,$companyid, $quantity){

    	$sql1 = "SELECT * FROM ".$this->db->dbprefix('qtydiscount')." WHERE company = '{$companyid}' and itemid = '{$itemid}' and qty <= '{$quantity}' order by qty desc limit 1";
    	$result1 = $this->db->query($sql1)->row();
    	if($result1){
			return $result1->price;
    	}else{
    		return 0;
    	}die;

    }

    function SaveItemcode() {   	
    	if(@$_FILES['UploadFile']['name'])
    	$name=implode(",",$_FILES['UploadFile']['name']);
    	else 
    	$name = "";
    	
    	if(@$_POST['filename'])
    	$filename=implode(",",$_POST['filename']);   
    	else 
    	$filename = "";
    	
    	$tag = $this->input->post('tags');
    	$tag = str_replace("\n",",",$tag);
     
    	if(@$_POST['category'][0])
    	$primarycategory = $_POST['category'][0];
    	else 
    	$primarycategory = "";
    	
    	$fi;
    	if(@$_POST['fi'] && $_POST['fi']=='on')
    	$fi=1;   
    	else 
    	$fi =0;
    	
    	
	        $options = array(
            'itemcode' => $this->input->post('itemcode'),
            'itemname' => $this->input->post('itemname'),
            'description' => $this->input->post('description'),
            'details' => $this->input->post('details'),
            'unit' => $this->input->post('unit'),
            'ea' => $this->input->post('ea'),
            'notes' => $this->input->post('notes'),
            'keyword' => $this->input->post('keyword'),
            'category' => $primarycategory, //$this->input->post('catid'),
            'item_img' => $_FILES["userfile"]["name"],
            'external_url' => $this->input->post('external_url'),
        	'length' => $this->input->post('length'),
        	'width' => $this->input->post('width'),
        	'height' => $this->input->post('height'),
        	'weight' => $this->input->post('weight'),
            'featuredsupplier' => $this->input->post('featuredsupplier'),
            'instore' => 1,//$this->input->post('instore'),
            'wiki' => $this->input->post('wiki'),
            'url' => $this->input->post('url'),
            'listinfo' => $this->input->post('listinfo'),
	        'tags' => $tag,
	        'files' => $name,
	        'filename' => $filename,
	        'searchquery' => $this->input->post('searchquery'),
	        'increment' => $this->input->post('increment'),
	        'fi' => $fi
        );

        if(@$this->session->userdata('purchasingadmin'))
        $options['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        
        $this->db->insert('item', $options);
        $id = $this->db->insert_id();
		
        
         if(@$this->session->userdata('timstmp') && $id){
        	
        	$sql1 = "SELECT * FROM ".$this->db->dbprefix('masterdefaulttemp'.$this->session->userdata('timstmp'));
    		$result1 = $this->db->query($sql1)->result();
        	if($result1){
    		foreach($result1 as $res1){    		
    			
    			$insert['itemid'] = $id;
    			$insert['manufacturer'] = $res1->manufacturer;
    			$insert['partnum'] = $res1->partnum;
    			$insert['price'] = $res1->price;
    			$insert['itemname'] = mysql_real_escape_string($res1->itemname);
    			$insert['minqty'] = $res1->minqty;
    			$insert['itemcode'] = mysql_real_escape_string($res1->itemcode);
    			
	        	$this->db->insert('masterdefault', $insert);    				
    		} }
    		    		
        	$query = "DROP TABLE  IF EXISTS ".$this->db->dbprefix('masterdefaulttemp'.$this->session->userdata('timstmp'));
    		$returnval = $this->db->query($query);
        }
        
        
        if(@$_POST['category'][0]){    	
        foreach ($_POST['category'] as $category){
        	$options2 = array();
        	$options2['itemid'] = $id;
        	$options2['categoryid'] = $category;
        	$this->db->insert('item_category', $options2);
        }
        }
        
        $categories = $this->input->post('catid');
        if ($categories !== false) {
            if ($id) {
                $cats = $this->get_product_categories($id);
                $ids = array();
                foreach ($cats as $c) {
                    $ids[] = $c->id;
                }
                foreach ($ids as $c) {
                    if (!in_array($c, $categories)) {
                        $this->db->delete($this->db->dbprefix('category_products'), array('product_id' => $id, 'category_id' => $c));
                    }
                }

                //add products to new categories
                foreach ($categories as $c) {
                    if (!in_array($c, $ids)) {
                        $this->db->insert($this->db->dbprefix('category_products'), array('product_id' => $id, 'category_id' => $c));
                    }
                }
            }
        }
        return $id;
    }

    
    
   function SaveItemcode_user($useritem="") {   	    	
         	
	        $options = array(
            'itemcode' => $this->input->post('itemcode'),
            'itemname' => $this->input->post('itemname'),            
            'unit' => $this->input->post('unit'),            
        	'weight' => 1,           
	        'increment' => 1,
	        'category' => 248,
	        'item_img' =>  (isset($_FILES["userfile"]["name"]) && $_FILES["userfile"]["name"] != '') ? $_FILES["userfile"]["name"] : '' ,
        );

        if(@$this->session->userdata('purchasingadmin'))
        $options['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        
        $this->db->insert('item', $options);
        $id = $this->db->insert_id();      
        
        if($useritem==""){
        	if(@$this->session->userdata('timstmp') && $id){

        		$sql1 = "SELECT * FROM ".$this->db->dbprefix('masterdefaulttemp'.$this->session->userdata('timstmp'));
        		$result1 = $this->db->query($sql1)->result();
        		if($result1){
        			foreach($result1 as $res1){

        				$insert['itemid'] = $id;
        				$insert['manufacturer'] = $res1->manufacturer;
        				$insert['partnum'] = $res1->partnum;
        				$insert['price'] = $res1->price;
        				$insert['itemname'] = mysql_real_escape_string($res1->itemname);
        				$insert['minqty'] = $res1->minqty;
        				$insert['itemcode'] = mysql_real_escape_string($res1->itemcode);


        				$this->db->insert('masterdefault', $insert);
        			} }

        			$query = "DROP TABLE  IF EXISTS ".$this->db->dbprefix('masterdefaulttemp'.$this->session->userdata('timstmp'));
        			$returnval = $this->db->query($query);
        	}
        }
        return $id;
    }
    
    
    
    function get_product_categories($id) {
        return $this->db->where('product_id', $id)->join($this->db->dbprefix('category'), 'category_id = category.id')->get($this->db->dbprefix('category_products'))->result();
    }



// updating cost code
    function updateItemcode($id) {  	
    	
    	if(@$_FILES['UploadFile']['name'])
    	$name=implode(",",$_FILES['UploadFile']['name']);
    	else 
    	$name = "";
    	
    	if(@$_POST['filename'])
    	$filename=implode(",",$_POST['filename']);   
    	else 
    	$filename = "";
    	
    	$orgname=$this->db->get_where('item',array('id'=>$this->input->post('id')))->row();
    	if(@$name && $orgname->files!="")
    	{
    		$name=$orgname->files.",".$name;
    	}
    	
    	if(@$filename && $orgname->filename!="")
    	{
    		$filename=$orgname->filename.",".$filename;
    	}
    	
    	$zoom;
		if(@$this->input->post('zoom') == "on")
    		$zoom = 1;
    	else
    		$zoom = 0;
		$tag = $this->input->post('tags');
    	$tag = str_replace("\n",",",$tag);
    	
    	if(@$_POST['category'][0])
    	$primarycategory = $_POST['category'][0];
    	else 
    	$primarycategory = "";
    	
    	$fi;
    	if(@$_POST['fi'] && $_POST['fi']=='on')
    	$fi=1;   
    	else 
    	$fi =0;
    	
        $options = array(
            'itemcode' => $this->input->post('itemcode'),
            'itemname' => $this->input->post('itemname'),
            'description' => $this->input->post('description'),
            'details' => $this->input->post('details'),
            'unit' => $this->input->post('unit'),
            'ea' => $this->input->post('ea'),
            'notes' => $this->input->post('notes'),
            'keyword' => $this->input->post('keyword'),
            'category' => $primarycategory,
            'item_img' => $_FILES["userfile"]["name"],
            'external_url' => $this->input->post('external_url'),
        		'length' => $this->input->post('length'),
        		'width' => $this->input->post('width'),
        		'height' => $this->input->post('height'),
        		'weight' => $this->input->post('weight'),
            'featuredsupplier' => $this->input->post('featuredsupplier'),
            'instore' => 1,//$this->input->post('instore'),
            'zoom' => $zoom,
            'wiki' => $this->input->post('wiki'),
            'url' => $this->input->post('url'),
            'listinfo' => $this->input->post('listinfo'),
            'item_img_alt_text' => $this->input->post('item_img_alt_text'),
        	'tags' => $tag,
        	'files' => $name,
	        'filename' => $filename,
	        'searchquery' => $this->input->post('searchquery'),
	        'increment' => $this->input->post('increment'),
	        'fi' => $fi
        );
        
    	if($_FILES["userfile"]["name"]=="")
    	{
    		$updatedata=$this->db->get_where('item',array('id'=>$this->input->post('id')))->row();
    		$options['item_img'] = $updatedata->item_img;
    	}
    	else 
    	{
    		$options['item_img'] = $_FILES["userfile"]["name"];
    	}
        $oldcoderow = $this->get_itemcodes_by_id($this->input->post('id'));
        $oldcode = $oldcoderow->itemcode;
        $newcode = $this->input->post('itemcode');

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('item', $options);

        $this->db->where('itemid', $this->input->post('id'));        
        $this->db->delete('item_category');
		
        if(@$_POST['category'][0]){
        foreach ($_POST['category'] as $category){
        	$options2 = array();
        	$options2['itemid'] = $this->input->post('id');
        	$options2['categoryid'] = $category;
        	$this->db->insert('item_category', $options2);
        }
        }
        
        if(!$options['instore'])
        {
            $this->db->where('itemid', $this->input->post('id'));
            $this->db->where('type','Supplier');
            $this->db->delete('companyitem');
        }

        $update = array('itemcode' => $newcode);

        $this->db->where('itemcode', $oldcode);
        $this->db->update('awarditem', $update);

        $this->db->where('itemcode', $oldcode);
        $this->db->update('biditem', $update);

        $this->db->where('itemcode', $oldcode);
        $this->db->update('quoteitem', $update);

        $categories = $this->input->post('catid');
        if ($categories !== false) {
            if ($id = $this->input->post('id')) {
                $cats = $this->get_product_categories($id);
                $ids = array();
                foreach ($cats as $c) {
                    $ids[] = $c->id;
                }
                foreach ($ids as $c) {
                    if (!in_array($c, $categories)) {
                        $this->db->delete($this->db->dbprefix('category_products'), array('product_id' => $id, 'category_id' => $c));
                    }
                }

                //add products to new categories
                foreach ($categories as $c) {
                    if (!in_array($c, $ids)) {
                        $this->db->insert($this->db->dbprefix('category_products'), array('product_id' => $id, 'category_id' => $c));
                    }
                }
            }
        }
    }

    
    
    // updating user cost code
    function updateItemcodeUser($id) {  	
    	    	
        $options = array(
            'itemcode' => $this->input->post('itemcode'),
            'itemname' => $this->input->post('itemname'),            
            'unit' => $this->input->post('unit'),          
        	'weight' => 1,            
	        'increment' => 1,
	        'category' => 248
        );
        
    	
        $oldcoderow = $this->get_itemcodes_by_id($this->input->post('id'));
        $oldcode = $oldcoderow->itemcode;
        $newcode = $this->input->post('itemcode');

        if($_FILES["userfile"]["name"]=="")
    	{
    		$updatedata=$this->db->get_where('item',array('id'=>$this->input->post('id')))->row();
    		$options['item_img'] = $updatedata->item_img;
    	}
    	else 
    	{
    		$options['item_img'] = $_FILES["userfile"]["name"];
    	}
    	
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('item', $options);
        
        $update = array('itemcode' => $newcode);

        $this->db->where('itemcode', $oldcode);
        $this->db->update('awarditem', $update);

        $this->db->where('itemcode', $oldcode);
        $this->db->update('biditem', $update);

        $this->db->where('itemcode', $oldcode);
        $this->db->update('quoteitem', $update);
      
    }
    
    
    
    
    // removing cost code
    function remove_itemcode($id) {
        $item = $this->get_itemcodes_by_id($id);
        $this->db->where('id', $id);
        $this->db->delete('item');
        /*
          $this->db->where('itemcode', $item->itemcode);
          $this->db->delete('quoteitem');

          $this->db->where('itemcode', $item->itemcode);
          $this->db->delete('biditem');
         */
    }

    // retrieve cost code by their id
    //function get_itemcodes_by_id($id,$quantity) {
    function get_itemcodes_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('item');
        if ($query->num_rows > 0) {
            $ret = $query->row();
            $ret->minprices = $this->getminimumprices($id);
            $ret->poitems = $this->getpoitems($id);
            $ret->tierprices = $this->gettierprices($id);


            // LAST QUOTED DATE
            $lastsql = "SELECT a.awardedon lastdate
				FROM " . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a
				WHERE a.id=ai.award AND ai.itemcode='" . $ret->itemcode . "' AND ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastsql = "SELECT b.submitdate lastdate
				FROM " . $this->db->dbprefix('biditem') . " bi, " . $this->db->dbprefix('bid') . " b
				WHERE b.id=bi.bid AND bi.itemcode='" . $ret->itemcode . "' AND bi.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastquery = $this->db->query($lastsql);
            if ($lastquery->num_rows > 0)
                $ret->lastquoted = $lastquery->row()->lastdate;
            else
                $ret->lastquoted = '';


            // TARGET PRICE
            $sqlmin = "SELECT MIN(ea) minprice FROM " . $this->db->dbprefix('biditem') . "
						WHERE itemcode='" . $ret->itemcode . "' AND purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
            $minprice = $this->db->query($sqlmin)->row()->minprice;
            $sqlconfig = "SELECT * FROM " . $this->db->dbprefix('settings') . " WHERE id='1'";
            $queryconfig = $this->db->query($sqlconfig);
            $pricepercent = $queryconfig->row()->pricepercent;

            $ret->targetprice = $minprice - ($minprice * $pricepercent / 100);


            //$ret->product_categories = $this->get_product_categories($id);

            return $ret;
        }
        return NULL;
    }

	function getstoredorderitems($itemid)
	{
		/*$sql = "SELECT c.title companyname,o.id,o.ordernumber, m.*,DATE_FORMAT(o.purchasedate,'%m/%d/%Y') as purchasedate
			   	FROM
				" . $this->db->dbprefix('minprice') . " m,
				" . $this->db->dbprefix('company') . " c,
				" . $this->db->dbprefix('order') . " o,
				" . $this->db->dbprefix('orderdetails') . " od
				WHERE
				m.company=c.id AND m.itemid='$itemid' 
				AND od.itemid = m.itemid AND o.purchasingadmin = m.purchasingadmin
				AND m.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
				GROUP By c.id";*/
		
		$sql = "SELECT c.id as company, c.title companyname,o.id,o.ordernumber, DATE_FORMAT(o.purchasedate,'%m/%d/%Y') as purchasedate, od.itemid as itemid, od.price  
			   	FROM 								
				" . $this->db->dbprefix('order') . " o,
				" . $this->db->dbprefix('orderdetails') . " od,  
				" . $this->db->dbprefix('company') . " c 
				WHERE o.id = od.orderid AND 
				od.itemid='$itemid' AND od.company = c.id  AND o.purchasingadmin='".$this->session->userdata('purchasingadmin')."'
				GROUP By c.id"; 
		
        //echo '<pre>', $sql; 
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item) {
                $this->db->where('purchasingadmin', $this->session->userdata('purchasingadmin'));
                $this->db->where('company', $item->company); 
                $this->db->where('status', 'Active');
                if ($this->db->get('network')->result()) {
                    $ret[] = $item;
                }
            }
            return $ret;
        }
        return NULL;
	}
    
        function get_itemcodes_by_idandbidid($id,$bidid) {
        $this->db->where('id', $id);
        $query = $this->db->get('item');
        
        if($query->num_rows <= 0){
        $this->db->where('itemid', $id)->where('bid', $bidid);
        $query = $this->db->get('biditem');
        }
        
        if ($query->num_rows > 0) {
            $ret = $query->row();
            $ret->minprices = $this->getminimumprices($id);
            $ret->poitems = $this->getpoitems($id);
            $ret->tierprices = $this->gettierprices($id);
            $ret->soitems = $this->getstoredorderitems($id);

            $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
			$this->db->where('itemid',$id);
			$result = $this->db->get('favoriteitem')->row();
		
			if(count($result) > 0)
			{
				$ret->isfavorite = $result->isfavorite;
			}
			else 
			{
				$ret->isfavorite = 0;
			}
            // LAST QUOTED DATE
            $lastsql = "SELECT a.awardedon lastdate
				FROM " . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a
				WHERE a.id=ai.award AND ai.itemcode='" . $ret->itemcode . "' AND ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastsql = "SELECT b.submitdate lastdate
				FROM " . $this->db->dbprefix('biditem') . " bi, " . $this->db->dbprefix('bid') . " b
				WHERE b.id=bi.bid AND bi.itemcode='" . $ret->itemcode . "' AND bi.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastquery = $this->db->query($lastsql);
            if ($lastquery->num_rows > 0)
                $ret->lastquoted = $lastquery->row()->lastdate;
            else
                $ret->lastquoted = '';


            // TARGET PRICE
            $sqlmin = "SELECT MIN(ea) minprice FROM " . $this->db->dbprefix('biditem') . "
						WHERE itemcode='" . $ret->itemcode . "' AND purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
            $minprice = $this->db->query($sqlmin)->row()->minprice;
            $sqlconfig = "SELECT * FROM " . $this->db->dbprefix('settings') . " WHERE id='1'";
            $queryconfig = $this->db->query($sqlconfig);
            $pricepercent = $queryconfig->row()->pricepercent;

            $ret->targetprice = $minprice - ($minprice * $pricepercent / 100);


            //$ret->product_categories = $this->get_product_categories($id);

            return $ret;
        }
        return NULL;
    }
    

     function get_itemcodes_by_id2($id, $quantity) {
        $this->db->where('id', $id);
        $query = $this->db->get('item');
        if ($query->num_rows > 0) {
            $ret = $query->row();
            $ret->minprices = $this->getminimumprices($id);
            $ret->poitems = $this->getpoitems($id);
            $ret->tierprices = $this->gettierpriceswithqtydiscount($id, $quantity); // $quantity var not fount


            // LAST QUOTED DATE
            $lastsql = "SELECT a.awardedon lastdate
				FROM " . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a
				WHERE a.id=ai.award AND ai.itemcode='" . $ret->itemcode . "' AND ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastsql = "SELECT b.submitdate lastdate
				FROM " . $this->db->dbprefix('biditem') . " bi, " . $this->db->dbprefix('bid') . " b
				WHERE b.id=bi.bid AND bi.itemcode='" . $ret->itemcode . "' AND bi.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastquery = $this->db->query($lastsql);
            if ($lastquery->num_rows > 0)
                $ret->lastquoted = $lastquery->row()->lastdate;
            else
                $ret->lastquoted = '';


            // TARGET PRICE
            $sqlmin = "SELECT MIN(ea) minprice FROM " . $this->db->dbprefix('biditem') . "
						WHERE itemcode='" . $ret->itemcode . "' AND purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
            $minprice = $this->db->query($sqlmin)->row()->minprice;
            $sqlconfig = "SELECT * FROM " . $this->db->dbprefix('settings') . " WHERE id='1'";
            $queryconfig = $this->db->query($sqlconfig);
            $pricepercent = $queryconfig->row()->pricepercent;

            $ret->targetprice = $minprice - ($minprice * $pricepercent / 100);


            //$ret->product_categories = $this->get_product_categories($id);

            return $ret;
        }
        return NULL;
    }

    // retrieve cost code by their id
    function get_itemcodes_by_code($code) {
        $this->db->where('itemcode', $code);
        $query = $this->db->get('item');
        if ($query->num_rows > 0) {
            $ret = $query->row();
            $ret->minprices = $this->getminimumprices($ret->itemcode);
            $ret->poitems = $this->getpoitems($ret->itemcode);
            $ret->tierprices = $this->gettierprices($ret->id);

            // LAST QUOTED DATE
            $lastsql = "SELECT b.submitdate lastdate
				FROM " . $this->db->dbprefix('biditem') . " bi, " . $this->db->dbprefix('bid') . " b
				WHERE b.id=bi.bid AND bi.itemcode='" . $ret->itemcode . "' bi.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'
				ORDER BY b.submitdate DESC LIMIT 0,1
				";

            $lastquery = $this->db->query($lastsql);
            if ($lastquery->num_rows > 0)
                $ret->lastquoted = $lastquery->row()->lastdate;
            else
                $ret->lastquoted = '';

            return $ret;
        }
        return NULL;
    }

    function getlowestquoteprice($itemid, $potype = 'Bid') {
        //if($potype == 'Bid')
        $sql = "SELECT MIN(ea) minprice FROM " . $this->db->dbprefix('biditem') . " WHERE itemid='$itemid' AND purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";

        //if($potype == 'Direct')
        //$sql = "SELECT MIN(ea) minprice FROM ".$this->db->dbprefix('biditem')." WHERE itemcode='$itemid'";
        $row = $this->db->query($sql)->row();
        return $row->minprice;
    }

    function getdaysmeanprice($itemid, $potype = 'Bid') {
        $pricedays = $this->db->get('settings')->row()->pricedays;
        $sql = "SELECT AVG(ea) avgprice FROM " . $this->db->dbprefix('awarditem') . " ai, " . $this->db->dbprefix('award') . " a
			  WHERE ai.itemid='$itemid' AND ai.award=a.id AND DATEDIFF(NOW(),a.awardedon) <=$pricedays
              AND ai.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";

        $sql = "SELECT AVG(ea) avgprice FROM " . $this->db->dbprefix('biditem') . " bi,
        		" . $this->db->dbprefix('bid') . " b
			  WHERE bi.itemid='$itemid' AND bi.bid=b.id AND DATEDIFF(NOW(),b.submitdate) <=$pricedays
              AND bi.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
        
        //echo $sql.'<br>';
        $row = $this->db->query($sql)->row();
        
        if(@$row->avgprice<=0)
        {
        	$pa = $this->session->userdata('purchasingadmin');
	        $sql = "select c.id companyid, c.title companyname, avg(ci.ea) avgprice
					FROM " . $this->db->dbprefix('company') . " c,
					" . $this->db->dbprefix('companyitem') . " ci,
					" . $this->db->dbprefix('network') . " n
					WHERE c.id=ci.company AND c.id=n.company AND
					n.purchasingadmin='" . $pa . "'
					AND ci.itemid='" . $itemid . "' AND c.isdeleted=0  AND n.status='Active' AND ci.ea != 0";
	       
	        $row = $this->db->query($sql)->row();
        }
        return $row->avgprice;
    }

    function checkDuplicateCode($code, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'itemcode' => $code));
        } else {
            $this->db->where('itemcode', $code);
        }
        $query = $this->db->get('item');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function checkDuplicateUrl($url, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'url' => $url));
        } else {
            $this->db->where('url', $url);
        }
        $query = $this->db->get('item');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function checkDuplicateArticleUrl($url, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'url' => $url));
        } else {
            $this->db->where('url', $url);
        }
        $query = $this->db->get('itemarticle');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    
    function checkDuplicateuserCode($code, $purchasingadmin, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'itemcode' => $code));
        } else {
            $this->db->where('itemcode', $code);
        }
        
        /*if(@$purchasingadmin)
        $this->db->where('purchasingadmin', $purchasingadmin);*/
        
        $query = $this->db->get('item');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function checkDuplicateUserItemName($itemname, $purchasingadmin, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'itemname' => $itemname));
        } else {
            $this->db->where('itemname', $itemname);
        }
        
        /*if(@$purchasingadmin)
        $this->db->where('purchasingadmin', $purchasingadmin); */       
        
        $query = $this->db->get('item');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }  
    
    
    // Start By Dhruvisha On 11th Jan 2014
    function SaveCategory() {
        //var_dump($this->input->post('catname')); exit;
        $options = array(
            'catname' => $this->input->post('catname')
        );
        $this->db->insert('category', $options);
        return $this->db->insert_id();
    }

    function checkDuplicateCat($catname, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'catname' => $catname));
        } else {
            $this->db->where('catname', $catname);
        }
        $query = $this->db->get('category');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getCategoryList() {
        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('category') . " ORDER BY catname ASC";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            return $result;
        } else {
            return null;
        }
    }

    function getSubCategoryList($catid = 0) {
        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('subcategory') . "";
        if ($catid > 0) {
            $sql.=" where category=" . $catid;
        }
        $sql.=" ORDER BY subcategory ASC";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            return $result;
        } else {
            return null;
        }
    }

    function checkDuplicateSubCat($catname, $subcatname, $edit_id = 0) {
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id, 'subcategory' => $subcatname, 'category' => $catname));
        } else {
            $this->db->where(array('category' => $catname, 'subcategory' => $subcatname));
        }
        $query = $this->db->get('subcategory');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function SaveSubCategory() {

        $options = array(
            'category' => $this->input->post('catid'),
            'subcategory' => $this->input->post('subcat')
        );
        $this->db->insert('subcategory', $options);
        return $this->db->insert_id();
    }

    public function add_massitem($data_user)
    {	
        $this->load->database();

        $this->db->insert('item',$data_user);
        $id = $this->db->insert_id();
      
       if(isset($_POST['categories']) && @$_POST['categories']!= '')
       {
	        foreach ($_POST['categories'] as $category){
	        	$options2 = array();
	        	$options2['itemid'] = $id;
	        	$options2['categoryid'] = $category;
	        	$this->db->insert('item_category', $options2);
	        }
       }  
       return $id;
    }
    
    public function add_massitemmanufacturer($master_data)
    {	
        $this->load->database();
        $this->db->insert('masterdefault',$master_data);      
    }
    
    public function getManufacturerId($str)
    {
    	$this->db->where(array('title'=>$str,'category'=>'Manufacturer'));
    	$query = $this->db->get('type');
    	return  $query->result();
    }
    
    
    public function checkinventoryresult($itemid){
    	
    	$this->db->where('itemid',$itemid);
    	$this->db->where('project',@$this->session->userdata('managedprojectdetails')->id);
    	$this->db->where('purchasingadmin',$this->session->userdata('id'));
    	$query = $this->db->get('inventory');
    	return  $query->row();
    	    	
    }
    
//End By Dhruvisha
}

?>