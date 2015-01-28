<?php

class quote_model extends Model {

    function quote_model() {
        parent::Model();
    }

    function get_quotes($path,$pid = '') {
        $sql = "SELECT * FROM " . $this->db->dbprefix('quote') . " WHERE 1=1 ";
        if ($pid)
            $sql .= " AND pid='$pid'";
        if (@$_POST['potype'] != 'All' && @$_POST['potype']) {
            $sql .= " AND potype='" . $_POST['potype'] . "'";
        }
        if (@$_POST['searchponum']) {
            $sql .= " AND ponum LIKE '%" . $_POST['searchponum'] . "%'";
        }
        if (@$_POST['searchdatefrom']) {
            $sql .= " AND str_to_date(podate,'%m/%d/%Y') >= str_to_date('" . $_POST['searchdatefrom'] . "','%m/%d/%Y')";
        }
        if (@$_POST['searchdateto']) {
            $sql .= " AND str_to_date(podate,'%m/%d/%Y') <= str_to_date('" . $_POST['searchdateto'] . "','%m/%d/%Y')";
        }

        if ($this->session->userdata('usertype_id') > 1) {
            $sql .= " AND purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
        }
	
        if($path == 'dashboard')
        {
        	$sql .= " AND potype='Bid' ";
        }
        
        $sql .= " ORDER BY str_to_date(podate,'%m/%d/%Y') DESC";
        $query = $this->db->query($sql);
  
        if ($query->result()) {
            return $query->result();
        } else {
            return null;
        }
    }
		
		function getcompletedbids($quote = '') {
		$where = " b.quote=q.id AND `b`.complete='Yes' ";
		if ($quote)
			$where .= " AND b.quote=$quote ";
		if ($this->session->userdata('usertype_id') > 1)
			$where .= " AND q.purchasingadmin=" . $this->session->userdata('purchasingadmin');
		$mp = $this->session->userdata('managedprojectdetails');
		if ($mp)
			$where .= " AND q.pid=" . $mp->id;
		$query = "SELECT b.* FROM " . $this->db->dbprefix('quote') . " q, " . $this->db->dbprefix('bid') . " b 
				WHERE $where";
		//echo $query.'<br>';

		$query = $this->db->query($query);

		$ret = array();
		$result = $query->result();

		foreach ($result as $item) {
			$this->db->where('id', $item->quote);
			$query = $this->db->get('quote');
			if ($query->result()) {
				$item->quotedetails = $query->row();

				$this->db->where('bid', $item->id);
				$query = $this->db->get('biditem');
				$item->items = $query->result();

				$this->db->where('id', $item->company);
				$query = $this->db->get('company');
				$item->companyname = $query->row('title');

				$ret[] = $item;
			}
		}
		//echo '<pre>';print_r($ret);die;
		return $ret;
	}

	
	        function get_pendingshipment_quotes($path,$pid = '') {
        $sql = "SELECT q.* FROM " . $this->db->dbprefix('quote') . " q join ". $this->db->dbprefix('shipment')." s ON q.id = s.quote where s.accepted = 0 ";
        if ($pid)
            $sql .= " AND q.pid='$pid'";
        /*if (@$_POST['potype'] != 'All' && @$_POST['potype']) {
            $sql .= " AND potype='" . $_POST['potype'] . "'";
        }
        if (@$_POST['searchponum']) {
            $sql .= " AND ponum LIKE '%" . $_POST['searchponum'] . "%'";
        }
        if (@$_POST['searchdatefrom']) {
            $sql .= " AND str_to_date(podate,'%m/%d/%Y') >= str_to_date('" . $_POST['searchdatefrom'] . "','%m/%d/%Y')";
        }
        if (@$_POST['searchdateto']) {
            $sql .= " AND str_to_date(podate,'%m/%d/%Y') <= str_to_date('" . $_POST['searchdateto'] . "','%m/%d/%Y')";
        }*/

        if ($this->session->userdata('usertype_id') > 1) {
            $sql .= " AND q.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
        }
	
        if($path == 'dashboard')
        {
        	$sql .= " AND q.potype='Bid' ";
        }
        
        $sql .= " group by q.id ORDER BY str_to_date(podate,'%m/%d/%Y') DESC";
        $query = $this->db->query($sql);
  
        if ($query->result()) {
            return $query->result();
        } else {
            return null;
        }
    }
	
     function get_Direct_Quotes($path,$pid = '') {
        $sql = "SELECT * FROM " . $this->db->dbprefix('quote') . " WHERE 1=1 ";
        if ($pid)
            $sql .= " AND pid='$pid'";
        if (@$_POST['potype'] != 'All' && @$_POST['potype']) {
            $sql .= " AND potype='" . $_POST['potype'] . "'";
        }
        if (@$_POST['searchponum']) {
            $sql .= " AND ponum LIKE '%" . $_POST['searchponum'] . "%'";
        }
        if (@$_POST['searchdatefrom']) {
            $sql .= " AND str_to_date(podate,'%m/%d/%Y') >= str_to_date('" . $_POST['searchdatefrom'] . "','%m/%d/%Y')";
        }
        if (@$_POST['searchdateto']) {
            $sql .= " AND str_to_date(podate,'%m/%d/%Y') <= str_to_date('" . $_POST['searchdateto'] . "','%m/%d/%Y')";
        }

        if ($this->session->userdata('usertype_id') > 1) {
            $sql .= " AND purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
        }
	
        if($path == 'dashboard')
        {
        	$sql .= " AND potype='direct' ";
        }
        
        $sql .= " ORDER BY str_to_date(podate,'%m/%d/%Y') DESC";
        $query = $this->db->query($sql);
  
        if ($query->result()) {
            return $query->result();
        } else {
            return null;
        }
    }

    function getQuoteRank($awardedbid) {
        if (!$awardedbid->items)
            return '-';
    }

    // counting total quotes
    function total_quote() {
        $query = $this->db->count_all_results('quote');
        return $query;
    }

    function getcompanylist() {
        $sql = "SELECT c.* FROM " . $this->db->dbprefix('company') . " c, " . $this->db->dbprefix('network') . " n
			WHERE c.id=n.company AND n.status='Active' AND n.purchasingadmin=" . $this->session->userdata('id');
        $query = $this->db->query($sql);
        $ret = $query->result();
        return $ret;
    }
    
    function getcompanylistforreminder() {
        $sql = "SELECT c.* FROM " . $this->db->dbprefix('company') . " c, " . $this->db->dbprefix('network') . " n
			WHERE c.id=n.company AND c.company_type='1' AND n.status='Active' AND n.purchasingadmin=" . $this->session->userdata('id');
        $query = $this->db->query($sql);
        $ret = $query->result();
         //echo "<pre>"; print_r($ret); die;
        return $ret;
    }

    function getallCategories () 
    {
    	$this->db->order_by('catname','asc');
        $this->db->where('parent_id',0);
        $menus = $this->db->get('category')->result();
        return $menus;
    }    
    
    function getcompanylistbyids($ids) {
        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('company') . " WHERE id IN ($ids)";

        $query = $this->db->query($sql);
        $ret = $query->result();
        return $ret;
    }
    
    function getpurchaserlistbycategory($category) {
        $sql = "SELECT u.* FROM " . $this->db->dbprefix('users') . " u where u.category=" . $category;
        $query = $this->db->query($sql);
        $ret = $query->result();
        return $ret;
    }
    
    
    function getnewcontractnotifications()
	{
		$company = $this->session->userdata('purchasingadmin');
		log_message("debug",var_export($company,true));
		if(!$company)
			return array();
		/*$this->db->where('isread',0);
		$this->db->where('notify_type','contract');
		$this->db->where('company',$company);
		$this->db->order_by('senton','desc');
		$this->db->limit(5,0);
		$nots = $this->db->get('notification1')->result();*/
		$sql = "SELECT n.* FROM " . $this->db->dbprefix('notification') . " n join " . $this->db->dbprefix('quote') . " q on n.quote = q.id WHERE n.isread = 0 AND n.notify_type = 'contract' AND n.company = '{$company}' ORDER BY `senton` desc LIMIT 5 ";
		$query = $this->db->query($sql);
        $nots = $query->result();
		log_message("debug",var_export($nots,true));
		$ret = array();
		foreach($nots as $not)
		{
			$not->tago = $this->tago(strtotime($not->senton));
			$this->db->where('id',$not->purchasingadmin);
			$purchasingadmin = $this->db->get('users')->row();
			if($purchasingadmin) {
			
			if($not->category=='Invitation')
			{
				$this->db->where('invite_type','contract');
				$this->db->where('company',$company);
				$this->db->where('quote',$not->quote);
				$invitation = @$this->db->get('invitation')->row()->invitation;
				$not->class='info';
				$not->message =  "You have received a new bid request from $purchasingadmin->companyname, $purchasingadmin->fullname, for the Contract# $not->ponum";
				$this->db->where('quote',$not->quote);
				$not->submessage = $this->db->count_all_results('quoteitem') . " bid items requested.";
				
				$not->link = site_url('admin/quote/invitation/'.$invitation);
			}
			
			
			$ret[]=$not;
		  }	
		}
		if(!$ret)
			return array();
		return $ret;
	}
	
	
	function tago($time)
    {
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");
        
        $now = time();
        $difference     = $now - $time;
        $tense         = "ago";
        
        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
         $difference /= $lengths[$j];
        }
        $difference = round($difference);
        
        if($difference != 1) {
         $periods[$j].= "s";
        }
        return "$difference $periods[$j] ago ";
    }
    
    function getpurchaseuserbyid($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('users');
		if($query->num_rows>0)
			return $query->row();
		else
			return NULL;
	}

    function getInvited($id) {
        $this->db->where('quote', $id);
        $query = $this->db->get('invitation');

        $ret = array();
        $result = $query->result();
        foreach ($result as $item) {
            $ret[] = $item->company;
        }
        return $ret;
    }

    function getInvitedquote($id) {
        $sql = "SELECT invitation FROM " . $this->db->dbprefix('invitation') . " WHERE quote = $id";
        //$this->db->where('quote',$id);
        //	$query = $this->db->get('invitation');

        $query = $this->db->query($sql);
        $ret = $query->result();
        return $ret;
    }

    function getInvitationKey($quote, $company) 
    {
        $this->db->where('quote', $quote);
        $this->db->where('company', $company);
        $query = $this->db->get('invitation');

        if ($query->result()) {
            $row = $query->row();
            return $row->invitation;
        }
        return '';
    }

    function getInvitedButNotBid($id) {
        $this->db->where('quote', $id);
        $query = $this->db->get('invitation');

        $ret = array();
        $result = $query->result();
        foreach ($result as $item) {
            $checksql = "SELECT COUNT(*) totalcount from " . $this->db->dbprefix('bid') . " WHERE quote='$id' AND company='{$item->company}'";
            $check = $this->db->query($checksql)->row()->totalcount;
            if ($check == 0)
                $ret[] = $item->company;
        }
        return $ret;
    }

    function getitems($id) {
        
        $sql = "SELECT qi.*, i.increment FROM ".$this->db->dbprefix('quoteitem'). " qi left join ". $this->db->dbprefix('item') ." i on qi.itemid = i.id WHERE quote='$id' ";
		$query = $this->db->query($sql);
        $ret = $query->result();
        return $ret;
    }

    function getsubtotal($id) {
        $sql = "SELECT SUM(totalprice) subtotal
		FROM
		" . $this->db->dbprefix('quoteitem') . " WHERE quote='$id'";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $row = $query->row();
            return $row->subtotal;
        } else {
            return 0;
        }
    }

    function getpendingbids($quote = '') {
        $where = " b.quote=q.id AND `b`.complete='No' ";
        if ($quote)
            $where .= " AND b.quote=$quote ";
        if ($this->session->userdata('usertype_id') > 1)
            $where .= " AND q.purchasingadmin=" . $this->session->userdata('purchasingadmin');
        $mp = $this->session->userdata('managedprojectdetails');
        if ($mp)
            $where .= " AND q.pid=" . $mp->id;
        $query = "SELECT b.* FROM " . $this->db->dbprefix('quote') . " q, " . $this->db->dbprefix('bid') . " b 
				WHERE $where";
        //echo $query.'<br>';

        $query = $this->db->query($query);

        $ret = array();
        $result = $query->result();

        foreach ($result as $item) {
            $this->db->where('id', $item->quote);
            $query = $this->db->get('quote');
            if ($query->result()) {
                $item->quotedetails = $query->row();

                $this->db->where('bid', $item->id);
                $query = $this->db->get('biditem');
                $item->items = $query->result();

                $this->db->where('id', $item->company);
                $query = $this->db->get('company');
                $item->companyname = $query->row('title');

                $ret[] = $item;
            }
        }
        //echo '<pre>';print_r($ret);die;
        return $ret;
    }

    function getawardedbid($quote) {
        $this->db->where('quote', $quote);
        $query = $this->db->get('award');

        $item = $query->row();
        if (!$item)
            return false;
        //foreach($result as $item)
        //{

        $this->db->where('id', $item->quote);
        $query = $this->db->get('quote');
        if ($query->result()) {
            $item->quotedetails = $query->row();

            $this->db->where('award', $item->id);
            $query = $this->db->get('awarditem');
            $awarditems = array();
            foreach ($query->result() as $awarditem) {
                $this->db->where('id', $awarditem->company);
                $query = $this->db->get('company');
                $awarditem->companyname = $query->row('title');
                $awarditem->companydetails = $query->row();


                $this->db->where('id', $awarditem->itemid);
                $companyitem = $this->db->get('item')->row();

                if ($companyitem) {
                    $awarditem->itemcode = $companyitem->itemcode;
                    $awarditem->itemname = $companyitem->itemname;
                }
                //print_r($companyitem);die;
                $awarditems[] = $awarditem;
            }
            //log_message('debug',var_export($awarditems,true));
            $item->items = $awarditems;
            /*
              $invoicesql = "SELECT r.*, ai.itemname
              FROM
              ".$this->db->dbprefix('received')." r,
              ".$this->db->dbprefix('awarditem')." ai
              WHERE r.awarditem=ai.id AND ai.award='".$item->id."' ORDER BY id DESC";
              $invoicequery = $this->db->query($invoicesql);
              $invoices = $invoicequery->result();
             */
            $invoicesql = "SELECT distinct(invoicenum) invoicenum, 
            				   r.status, r.paymentstatus, r.paymenttype, r.refnum, r.datedue,  
            				  ROUND(SUM(ai.ea * if(r.quantity=0,ai.quantity,r.quantity) ),2) totalprice
							   FROM 
							   " . $this->db->dbprefix('received') . " r,
							   " . $this->db->dbprefix('awarditem') . " ai
							  WHERE r.awarditem=ai.id AND ai.award='" . $item->id . "'
							  GROUP BY invoicenum
							  ";
            //echo $invoicesql;
            $invoicequery = $this->db->query($invoicesql);
            $invoicenums = $invoicequery->result();
            $invoices = array();
            foreach ($invoicenums as $invoicenum) {
                $itemsql = "SELECT r.*, ai.itemid, ai.itemname, ai.ea 
							  FROM 
							  " . $this->db->dbprefix('received') . " r, 
							  " . $this->db->dbprefix('awarditem') . " ai,
							  " . $this->db->dbprefix('award') . " a
							  WHERE r.awarditem=ai.id AND ai.award = a.id AND a.quote='{$quote}' AND r.invoicenum='{$invoicenum->invoicenum}'";
                $itemquery = $this->db->query($itemsql);
                $invoiceitems = $itemquery->result();
                $invoicenum->items = array();
                foreach ($invoiceitems as $invoiceitem) {
                    $this->db->where('id', $invoiceitem->itemid);
                    $companyitem = $this->db->get('item')->row();

                    if ($companyitem) {
                        $invoiceitem->itemcode = $companyitem->itemcode;
                        $invoiceitem->itemname = $companyitem->itemname;
                    }
                    $invoicenum->items[] = $invoiceitem;
                }
                $invoices[] = $invoicenum;
            }
            $item->invoices = $invoices;

            $status = 'complete';
            foreach ($item->items as $it) {
                if ($it->quantity > $it->received) {
                    $status = 'incomplete';
                }
            }
            $item->status = $status;
        }
        //}
        //echo '<pre>';print_r($item);die;
        return $item;
    }
    
    
        function getawardedcontractbid($quote) {
        $this->db->where('quote', $quote);
        $query = $this->db->get('award');

        $item = $query->row();
        if (!$item)
            return false;
        //foreach($result as $item)
        //{

        $this->db->where('id', $item->quote);
        $query = $this->db->get('quote');
        if ($query->result()) {
            $item->quotedetails = $query->row();

            $this->db->where('award', $item->id);
            $query = $this->db->get('awarditem');
            $awarditems = array();
            foreach ($query->result() as $awarditem) {
                $this->db->where('id', $awarditem->company);
                $query = $this->db->get('users');
                $awarditem->companyname = $query->row('companyname');
                $awarditem->companydetails = $query->row();


                /*$this->db->where('id', $awarditem->itemid);
                $companyitem = $this->db->get('item')->row();

                if ($companyitem) {
                    $awarditem->itemcode = $companyitem->itemcode;
                    $awarditem->itemname = $companyitem->itemname;
                }*/
                //print_r($companyitem);die;
                $awarditems[] = $awarditem;
            }
            //log_message('debug',var_export($awarditems,true));
            $item->items = $awarditems;
            /*
              $invoicesql = "SELECT r.*, ai.itemname
              FROM
              ".$this->db->dbprefix('received')." r,
              ".$this->db->dbprefix('awarditem')." ai
              WHERE r.awarditem=ai.id AND ai.award='".$item->id."' ORDER BY id DESC";
              $invoicequery = $this->db->query($invoicesql);
              $invoices = $invoicequery->result();
             */
            $invoicesql = "SELECT distinct(invoicenum) invoicenum, 
            				   r.status, r.paymentstatus, r.paymenttype, r.refnum, r.datedue,  
            				   ROUND(SUM(totalprice * r.quantity/100),2) totalprice
							   FROM 
							   " . $this->db->dbprefix('received') . " r,
							   " . $this->db->dbprefix('awarditem') . " ai
							  WHERE r.awarditem=ai.id AND ai.award='" . $item->id . "'
							  GROUP BY invoicenum
							  ";
            //echo $invoicesql;
            $invoicequery = $this->db->query($invoicesql);
            $invoicenums = $invoicequery->result();
            $invoices = array();
            foreach ($invoicenums as $invoicenum) {
                $itemsql = "SELECT r.*, ai.itemid, ai.itemname, ai.ea 
							  FROM 
							  " . $this->db->dbprefix('received') . " r, 
							  " . $this->db->dbprefix('awarditem') . " ai
							  WHERE r.awarditem=ai.id AND r.invoicenum='{$invoicenum->invoicenum}'";
                $itemquery = $this->db->query($itemsql);
                $invoiceitems = $itemquery->result();
                $invoicenum->items = array();
                foreach ($invoiceitems as $invoiceitem) {
                    /*$this->db->where('id', $invoiceitem->itemid);
                    $companyitem = $this->db->get('item')->row();

                    if ($companyitem) {
                        $invoiceitem->itemcode = $companyitem->itemcode;
                        $invoiceitem->itemname = $companyitem->itemname;
                    }*/
                    $invoicenum->items[] = $invoiceitem;
                }
                $invoices[] = $invoicenum;
            }
            $item->invoices = $invoices;

            $status = 'complete';
            foreach ($item->items as $it) {
                if (100 > $it->received) {
                    $status = 'incomplete';
                }
            }
            $item->status = $status;
        }
        //}
        //echo '<pre>';print_r($item);die;
        return $item;
    }
    
            function getallawardedqtyduebids() {
        
        $query = $this->db->get('award');
		$itemarray = array();
        $items = $query->result();
        if (!$items)
            return false;
        //foreach($result as $item)
        //{
		foreach($items as $item){
        $this->db->select("quote.*,users.companyname");	
        $this->db->where('quote.id', $item->quote);
        $this->db->join("users",'quote.purchasingadmin = users.id');
        $query = $this->db->get('quote');
        if ($query->result()) {
            $item->quotedetails = $query->row();
            
            $query = "SELECT *, (quantity - received) quantityleft FROM  ".$this->db->dbprefix('awarditem')." WHERE award = '".$item->id."' and STR_TO_DATE(daterequested,'%m/%d/%Y') < curdate() and (quantity - received) > 0";
            $awarditems = array();
            foreach ($this->db->query($query)->result() as $awarditem) {
                $this->db->where('id', $awarditem->company);
                $query = $this->db->get('company');
                $awarditem->companyname = $query->row('title');
                $awarditem->companydetails = $query->row();


                $this->db->where('id', $awarditem->itemid);
                $companyitem = $this->db->get('item')->row();

                if ($companyitem) {
                    $awarditem->itemcode = $companyitem->itemcode;
                    $awarditem->itemname = $companyitem->itemname;
                }
                //print_r($companyitem);die;
                $awarditems[] = $awarditem;
            }
            $item->items = $awarditems;
            $itemarray[] =  $item; 
        }
       }        
        return $itemarray;
    } 

    function getawardedbidquote($quote) 
    {
        $this->db->where('quote', $quote);
        $query = $this->db->get('award');

        $item = $query->row();
        if (!$item)
            return false;
        //foreach($result as $item)
        //{

        $this->db->where('id', $item->quote);
        $query = $this->db->get('quote');
        if ($query->result()) {
            $item->quotedetails = $query->row();

            $this->db->where('award', $item->id);
            $query = $this->db->get('awarditem');
            $awarditems = array();
            foreach ($query->result() as $awarditem) {
                $this->db->where('id', $awarditem->company);
                $query = $this->db->get('company');
                $awarditem->companyname = $query->row('title');
                $awarditem->companydetails = $query->row();
                $awarditems[] = $awarditem;
            }
            $item->items = $awarditems;
            /*
              $invoicesql = "SELECT r.*, ai.itemname
              FROM
              ".$this->db->dbprefix('received')." r,
              ".$this->db->dbprefix('awarditem')." ai
              WHERE r.awarditem=ai.id AND ai.award='".$item->id."' ORDER BY id DESC";
              $invoicequery = $this->db->query($invoicesql);
              $invoices = $invoicequery->result();
             */
            $invoicesql = "SELECT distinct(invoicenum) invoicenum, 
            				   r.status, r.paymentstatus, r.paymenttype, r.refnum,
            				   ROUND(SUM(ai.ea * r.quantity),2) totalprice
							   FROM
							   " . $this->db->dbprefix('received') . " r,
							   " . $this->db->dbprefix('awarditem') . " ai
							  WHERE r.awarditem=ai.id AND ai.award='" . $item->id . "'
							  GROUP BY invoicenum
							  ";
            //echo $invoicesql;
            $invoicequery = $this->db->query($invoicesql);
            $invoicenums = $invoicequery->result();
            $invoices = array();
            foreach ($invoicenums as $invoicenum) {
                $itemsql = "SELECT r.status, r.paymentstatus, r.paymenttype, r.refnum,
                				r.quantity, ai.itemname,ai.received, ai.ea
							  FROM
							  " . $this->db->dbprefix('received') . " r,
							  " . $this->db->dbprefix('awarditem') . " ai
							  WHERE r.awarditem=ai.id AND r.invoicenum='{$invoicenum->invoicenum}'";
                $itemquery = $this->db->query($itemsql);
                $items = $itemquery->result();
                $invoicenum->items = $items;
                $invoices[] = $invoicenum;
            }
            $item->invoices = $invoices;

            $status = 'complete';
            foreach ($item->items as $it) {
                if ($it->quantity > $it->received) {
                    $status = 'incomplete';
                }
            }
            $item->status = $status;
        }
        //}
        //echo '<pre>';print_r($ret);die;
        return $item;
    }

    // retrieve bid by their id
    function getbidbyid($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('bid');
        if ($query->num_rows > 0) {
            $item = $query->row();
            $this->db->where('id', $item->quote);
            $query = $this->db->get('quote');
            $item->quotedetails = $query->row();

            $this->db->where('bid', $item->id);
            $query = $this->db->get('biditem');
            $item->items = $query->result();

            $this->db->where('id', $item->company);
            $query = $this->db->get('company');
            $item->companydetails = $query->row();
            $item->companyname = $item->companydetails->title;

            return $item;
        }
        return NULL;
    }
    
    function getcontractbidbyid($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('bid');
        if ($query->num_rows > 0) {
            $item = $query->row();
            $this->db->where('id', $item->quote);
            $query = $this->db->get('quote');
            $item->quotedetails = $query->row();

            $this->db->where('bid', $item->id);
            $query = $this->db->get('biditem');
            $item->items = $query->result();

            $this->db->where('id', $item->company);
            $query = $this->db->get('users');
            $item->companydetails = $query->row();
            $item->companyname = $item->companydetails->companyname;

            return $item;
        }
        return NULL;
    }

    function getinvoicebyid($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('received');
        return $query->row();
    }

    function getinvoices() {
        $search = '';
        $searches = array();
        //if (@$_POST['searchinvoice']) {
            //$searches[]= " invoicenum LIKE '%{$_POST['searchinvoice']}%' ";
            //$searches[]= " c.title LIKE '%{$_POST['searchinvoice']}%' ";
            //$searches[]= " itemcode LIKE '%{$_POST['searchinvoice']}%' ";
            //$searches[]= " itemname LIKE '%{$_POST['searchinvoice']}%' ";
        //}
        
        if(!@$_POST)
 		{
 			$fromdate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );;
 			$todate = date('Y-m-d');
 			$searches[] = " ( (receiveddate >= '$fromdate'
						AND date(receiveddate) <= '$todate') OR receiveddate IS NULL ) ";
 		}
        
        if (@$_POST['searchbycompany']) {
            $searches[] = " c.id = '{$_POST['searchbycompany']}' ";
        }
        if (@$_POST['searchstatus']) {
            $searches[] = " r.status = '{$_POST['searchstatus']}' ";
        }
        if (@$_POST['searchpaymentstatus']) {
            $searches[] = " r.paymentstatus = '{$_POST['searchpaymentstatus']}' ";
        }
        if (@$_POST['searchinvoicenum']) {
            $searches[] = " r.invoicenum LIKE '%{$_POST['searchinvoicenum']}%' ";
        }

        if (@$_POST['searchfrom'] && @$_POST['searchto']) {
            $fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
            $todate = date('Y-m-d', strtotime($_POST['searchto']));
            $searches[] = " ( (receiveddate >= '$fromdate'
						AND date(receiveddate) <= '$todate') OR receiveddate IS NULL ) ";
        } elseif (@$_POST['searchfrom']) {
            $fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
            $searches[] = " ( receiveddate >= '$fromdate' OR receiveddate IS NULL ) ";
        } elseif (@$_POST['searchto']) {
            $todate = date('Y-m-d', strtotime($_POST['searchto']));
            $searches[] = " ( date(receiveddate) <= '$todate' OR receiveddate IS NULL ) ";
        }
        if ($this->session->userdata('usertype_id') > 1) {
            $searches[] = " r.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "' ";
        }
        if ($searches) {
            $search = "  AND (" . implode(" AND ", $searches) . " )";
        }
        $managedprojectdetails = $this->session->userdata('managedprojectdetails');
        if ($managedprojectdetails) {
            $managedprojectdetails_id = $managedprojectdetails->id;
        } else {
            $managedprojectdetails_id = 0;
        }

        $managedprojectdetails_id_sql = ($managedprojectdetails_id) ? "AND q.pid='" . $managedprojectdetails_id . "'" : " ";


       $query = "SELECT invoicenum, ROUND(SUM(ai.ea * if(r.quantity=0,ai.quantity,r.quantity) ),2) totalprice, receiveddate, 
        			r.status, r.paymentstatus, r.paymenttype, r.refnum,  r.datedue
				   FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,
				   " . $this->db->dbprefix('company') . " c,
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q
				   
				  WHERE r.awarditem=ai.id AND ai.company=c.id 
				  AND ai.award=a.id AND a.quote=q.id AND q.potype <> 'Contract' " .
                $managedprojectdetails_id_sql
                . " $search GROUP BY invoicenum";
                
         $contractquery = "SELECT invoicenum, ROUND(SUM(ai.ea * if(r.quantity=0,ai.quantity,r.quantity) ),2) totalprice, receiveddate, 
        			r.status, r.paymentstatus, r.paymenttype, r.refnum,  r.datedue
				   FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,
				   " . $this->db->dbprefix('users') . " c,
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q
				   
				  WHERE r.awarditem=ai.id AND ai.company=c.id 
				  AND ai.award=a.id AND a.quote=q.id AND q.potype = 'Contract'  " .
                $managedprojectdetails_id_sql
                . " $search GROUP BY invoicenum";        
                
        //log_message('debug',var_export($query,true));
        $combquery = $query ." UNION ".$contractquery." ORDER BY STR_TO_DATE(receiveddate, '%m/%d/%Y') DESC";
        $invoicequery = $this->db->query($combquery);
        $items = $invoicequery->result();

        $invoices = array();
        foreach ($items as $invoice) {
             $quotesql = "SELECT q.*,ai.id as awarditemid
					   FROM 
					   " . $this->db->dbprefix('received') . " r,
					   " . $this->db->dbprefix('awarditem') . " ai,
					   " . $this->db->dbprefix('award') . " a,
					   " . $this->db->dbprefix('quote') . " q
					  WHERE r.awarditem=ai.id AND ai.award=a.id
					  AND a.quote=q.id AND invoicenum='{$invoice->invoicenum}'
					  ";
            
            $quotequery = $this->db->query($quotesql);
            $invoice->quote = $quotequery->row();

            $invoices[] = $invoice;
        }

        return $invoices;
    }
    
         
    function getinvoicesforpayment($invoicenum) {
        $search = '';
        $searches = array();
       
        if (@$invoicenum) {
            $searches[] = " r.invoicenum LIKE '%{$invoicenum}%' ";
        }

        if ($this->session->userdata('usertype_id') > 1) {
            $searches[] = " r.purchasingadmin='" . $this->session->userdata('purchasingadmin') . "' ";
        }
        if ($searches) {
            $search = "  AND (" . implode(" AND ", $searches) . " )";
        }
        $managedprojectdetails = $this->session->userdata('managedprojectdetails');
        if ($managedprojectdetails) {
            $managedprojectdetails_id = $managedprojectdetails->id;
        } else {
            $managedprojectdetails_id = 0;
        }

        $managedprojectdetails_id_sql = ($managedprojectdetails_id) ? "AND q.pid='" . $managedprojectdetails_id . "'" : " ";


        $query = "SELECT invoicenum, ROUND(SUM(ai.ea * if(r.quantity=0,ai.quantity,r.quantity) ),2) totalprice, receiveddate, 
        			r.status, r.paymentstatus, r.paymenttype, r.refnum,  r.datedue
				   FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,
				   " . $this->db->dbprefix('company') . " c,
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q
				   
				  WHERE r.awarditem=ai.id AND ai.company=c.id 
				  AND ai.award=a.id AND a.quote=q.id AND q.potype <> 'Contract' " .
                $managedprojectdetails_id_sql
                . " $search GROUP BY invoicenum";
                
        $contractquery = "SELECT invoicenum, ROUND(SUM(ai.ea * if(r.quantity=0,ai.quantity/100,r.quantity/100) ),2) totalprice, receiveddate, 
        			r.status, r.paymentstatus, r.paymenttype, r.refnum,  r.datedue
				   FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,
				   " . $this->db->dbprefix('users') . " u,
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q
				   
				  WHERE r.awarditem=ai.id AND ai.company=u.id 
				  AND ai.award=a.id AND a.quote=q.id AND q.potype = 'Contract'  " .
                $managedprojectdetails_id_sql
                . " $search GROUP BY invoicenum";        
                
        //log_message('debug',var_export($query,true));
        $combquery = $query ." UNION ".$contractquery." ORDER BY STR_TO_DATE(receiveddate, '%m/%d/%Y') DESC";
        $invoicequery = $this->db->query($combquery);
        $items = $invoicequery->result();

        $invoices = array();
        foreach ($items as $invoice) {
            $quotesql = "SELECT q.*
					   FROM 
					   " . $this->db->dbprefix('received') . " r,
					   " . $this->db->dbprefix('awarditem') . " ai,
					   " . $this->db->dbprefix('award') . " a,
					   " . $this->db->dbprefix('quote') . " q
					  WHERE r.awarditem=ai.id AND ai.award=a.id 
					  AND a.quote=q.id AND invoicenum='{$invoice->invoicenum}'
					  ";
            
            $quotequery = $this->db->query($quotesql);
            $invoice->quote = $quotequery->row();

            $invoices[] = $invoice;
        }

        return $invoices;
    }

    public function update_invoice_by_number($invoicenum, $update) {
        foreach ($update as $key => $value) {
            $this->db->set($key, $value);
        }
        $this->db->where('invoicenum', $invoicenum);
        $this->db->update($this->db->dbprefix('received'));


        if (in_array(strtolower($update['status']), array('paid', 'unpaid'))) {
            $itemsql = "SELECT 
                                r.*,ai.itemid, ai.itemcode, c.title companyname,
                                ai.itemname, ai.ea, ai.unit, ai.daterequested, ai.costcode, ai.notes 
                          FROM 
                          " . $this->db->dbprefix('received') . " r, 
                          " . $this->db->dbprefix('awarditem') . " ai,
                          " . $this->db->dbprefix('company') . " c
                          WHERE r.awarditem=ai.id AND ai.company=c.id 
                          AND invoicenum='{$invoicenum}'
                          ";
            $invoiceitems = $this->db->query($itemsql)->result();


            foreach ($invoiceitems as $invoiceitem) {

                $this->db->set('status', $update['status']);
                $this->db->where('id', $invoiceitem->id);
                $this->db->update($this->db->dbprefix('quote'));
//                var_dump($invoiceitem);
//                die;
            }
        }
    }

    function getinvoicebynum($invoicenum,$invoicequote) {

        $invoicesql = "SELECT invoicenum, ROUND(SUM(ai.ea * if(r.quantity=0,ai.quantity,r.quantity) ),2) totalprice, 
        			r.status, r.paymentstatus, r.paymenttype, r.refnum, r.datedue 
				   FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q 
				  WHERE r.awarditem=ai.id AND ai.award=a.id AND a.quote=q.id AND a.quote='{$invoicequote}' AND invoicenum='{$invoicenum}'
				  GROUP BY invoicenum
				  ";
        //echo $totalquery;
        $invoicequery = $this->db->query($invoicesql);
        $invoice = $invoicequery->row();

        $quotesql = "SELECT quote
				   FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,
				   " . $this->db->dbprefix('award') . " a
				  WHERE r.awarditem=ai.id AND ai.award=a.id AND a.quote='{$invoicequote}' AND invoicenum='{$invoicenum}'
				  ";
        $quotequery = $this->db->query($quotesql);
        $invoice->quote = $quotequery->row('quote');

       /* $itemsql = "SELECT 
					r.*,ai.itemid, ai.itemcode, c.title companyname, r.datedue,
					ai.itemname, ai.ea, ai.unit, ai.daterequested, ai.costcode, ai.notes,c.id as companyid,ai.award, s.shipdate   
				  FROM 
				  " . $this->db->dbprefix('received') . " r, 
				   " . $this->db->dbprefix('shipment') . " s,
				  " . $this->db->dbprefix('awarditem') . " ai,
				   " . $this->db->dbprefix('award') . " a,
				  " . $this->db->dbprefix('company') . " c
				  WHERE r.awarditem=ai.id AND r.awarditem = s.awarditem AND r.invoicenum = s.invoicenum AND ai.company=c.id AND ai.award=a.id AND a.quote='{$invoicequote}'
				  AND r.invoicenum='{$invoicenum}'
				  ";*/
        
         $itemsql = "SELECT 
					r.*,ai.itemid, ai.itemcode, c.title companyname, r.datedue,
					ai.itemname, ai.ea, ai.unit, ai.daterequested, ai.costcode, ai.notes,c.id as companyid,ai.award, s.shipdate, ai.quantity as aiquantity    
				  FROM 
				  " . $this->db->dbprefix('received') . " r  
				    LEFT JOIN " . $this->db->dbprefix('shipment') . " s ON r.awarditem = s.awarditem AND r.invoicenum = s.invoicenum
				    LEFT JOIN " . $this->db->dbprefix('awarditem') . " ai ON r.awarditem=ai.id
				    LEFT JOIN " . $this->db->dbprefix('award') . " a ON ai.award=a.id
				    LEFT JOIN " . $this->db->dbprefix('company') . " c ON ai.company=c.id
				  WHERE  a.quote='{$invoicequote}'
				  AND r.invoicenum='{$invoicenum}'
				  ";

        //echo $itemsql;
        $invoiceitems = $this->db->query($itemsql)->result();

        $invoice->items = array();
        foreach ($invoiceitems as $invoiceitem) {
            $this->db->where('id', $invoiceitem->itemid);
            $companyitem = $this->db->get('item')->row();

            if ($companyitem) {
                $invoiceitem->itemcode = $companyitem->itemcode;
                $invoiceitem->itemname = $companyitem->itemname;
            }
            $invoice->items[] = $invoiceitem;
        }
        return $invoice;
    }

    
    
        function geticontractnvoicebynum($invoicenum,$invoicequote) {

        $invoicesql = "SELECT invoicenum, ROUND(SUM(totalprice * r.quantity/100),2) totalprice, 
        			r.status, r.paymentstatus, r.paymenttype, r.refnum, r.datedue 
				   FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q 
				  WHERE r.awarditem=ai.id AND ai.award=a.id AND a.quote=q.id  AND a.quote='{$invoicequote}' AND invoicenum='{$invoicenum}'
				  GROUP BY invoicenum
				  ";
        //echo $totalquery;
        $invoicequery = $this->db->query($invoicesql);
        $invoice = $invoicequery->row();

        $quotesql = "SELECT quote
				   FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,
				   " . $this->db->dbprefix('award') . " a
				  WHERE r.awarditem=ai.id AND ai.award=a.id  AND a.quote='{$invoicequote}' AND invoicenum='{$invoicenum}'
				  ";
        $quotequery = $this->db->query($quotesql);
        $invoice->quote = $quotequery->row('quote');

        $itemsql = "SELECT 
					r.*,ai.itemid, ai.itemcode, u.companyname companyname, r.datedue,
					ai.itemname, ai.ea, ai.unit, ai.daterequested, ai.costcode, ai.notes,u.id as companyid,ai.award, ai.attach   
				  FROM 
				  " . $this->db->dbprefix('received') . " r, 
				  " . $this->db->dbprefix('awarditem') . " ai,
				  " . $this->db->dbprefix('award') . " a,
				  " . $this->db->dbprefix('users') . " u 
				  WHERE r.awarditem=ai.id AND ai.company=u.id AND ai.award=a.id AND a.quote='{$invoicequote}'
				  AND invoicenum='{$invoicenum}'
				  ";
        //echo $itemsql;
        $invoiceitems = $this->db->query($itemsql)->result();

        $invoice->items = array();
        foreach ($invoiceitems as $invoiceitem) {
            /*$this->db->where('id', $invoiceitem->itemid);
            $companyitem = $this->db->get('item')->row();

            if ($companyitem) {
                $invoiceitem->itemcode = $companyitem->itemcode;
                $invoiceitem->itemname = $companyitem->itemname;
            }*/
            $invoice->items[] = $invoiceitem;
        }
        return $invoice;
    }
    
    
    
       function getinvoicebybillnum($invoicenum,$invoicequote) {

  $invoicesql = "SELECT billname, ROUND(SUM(bi.ea * bi.quantity),2) totalprice, b.status, b.paymentstatus, " //b.paymenttype, b.refnum,
        			." b.customerduedate, c.name, c.email,c.address  
				   FROM 			   
				   " . $this->db->dbprefix('billitem') . " bi left join " . $this->db->dbprefix('bill') . " b on  bi.bill=b.id 
				   left join " . $this->db->dbprefix('quote') . " q on b.quote=q.id left join  
				   " . $this->db->dbprefix('customer') . " c on b.customerid = c.id    
				  WHERE b.quote='{$invoicequote}' AND b.id='{$invoicenum}'
				  GROUP BY b.id 
				  ";
        //echo $totalquery;
        $invoicequery = $this->db->query($invoicesql);
        $invoice = $invoicequery->row();
		//echo "<pre>",print_r($invoice); die;
       
        $invoice->quote = $invoicequote;

        $itemsql = "SELECT 
					bi.*, c.title companyname, b.customerduedate,  b.markuptotalpercent      
				  FROM 				 
				  " . $this->db->dbprefix('billitem') . " bi,
				   " . $this->db->dbprefix('bill') . " b,
				  " . $this->db->dbprefix('company') . " c 
				  WHERE b.id=bi.bill AND bi.company=c.id AND b.quote='{$invoicequote}'
				  AND b.id='{$invoicenum}'
				  ";

        //echo $itemsql;
        $invoiceitems = $this->db->query($itemsql)->result();

        $invoice->items = array();
        foreach ($invoiceitems as $invoiceitem) {
                 
            $invoice->items[] = $invoiceitem;
        }
        return $invoice;
    }
    
    function getbids($quote) {
        $this->db->where('quote', $quote);
        $query = $this->db->get('bid');

        $ret = array();
        $result = $query->result();

        foreach ($result as $item) {
            $messagesql = "SELECT * FROM " . $this->db->dbprefix('message') . " WHERE quote='{$quote}' AND company='{$item->company}' ORDER BY senton ASC";
            $item->messages = $this->db->query($messagesql)->result();
            $this->db->where('bid', $item->id);
            $query = $this->db->get('biditem');
            $biresult = $query->result();
            $biditems = array();
            //$sqlItem = "SELECT id FROM ". $this->db->dbprefix('item') ." WHERE itemcode = ?"; 
            foreach ($biresult as $biditem) {
                $sqlmin = "SELECT MIN(ea) minprice FROM " . $this->db->dbprefix('biditem') . " WHERE ea > 0 AND itemid='" . $biditem->itemid . "' ";
                if ($this->session->userdata('usertype_id') > 1)
                    $sqlmin .= " AND purchasingadmin=" . $this->session->userdata('purchasingadmin');
                //echo $sqlmin;
                
                if($this->session->userdata('usertype_id') == 1)
                	$sqlmin .= " AND purchasingadmin=" . $biditem->purchasingadmin;	
                
                $biditem->minprice = $this->db->query($sqlmin)->row()->minprice;

                $sqlreq = "SELECT ea FROM " . $this->db->dbprefix('quoteitem') . " WHERE quote='" . $quote . "' AND itemid='" . $biditem->itemid . "'";
                $rowreqprice = $this->db->query($sqlreq)->row();
                $biditem->reqprice = @$rowreqprice->ea ? $rowreqprice->ea : '';
                //$itemid = $this->db->query($sqlItem, array($biditem->itemcode))->row();
                //$biditem->itemid = isset($itemid->id)?$itemid->id:'';

                if ($this->session->userdata('usertype_id') > 1) {
                    $this->db->where('id', $biditem->itemid);
                    $companyitem = $this->db->get('item')->row();
                    if ($companyitem) {
                        $biditem->itemcode = $companyitem->itemcode;
                        $biditem->itemname = $companyitem->itemname;
                    }
                }

                $biditems[] = $biditem;
            }
            $item->items = $biditems;

            $this->db->where('id', $item->company);
            $query = $this->db->get('company');
            $item->companyname = $query->row('title');

            $ret[] = $item;
        }
        //echo '<pre>';print_r($ret);die;
        return $ret;
    }

    
        function getcontractbids($quote) {
        $this->db->where('quote', $quote);
        $query = $this->db->get('bid');

        $ret = array();
        $result = $query->result();

        foreach ($result as $item) {
            $messagesql = "SELECT * FROM " . $this->db->dbprefix('message') . " WHERE quote='{$quote}' AND company='{$item->company}' ORDER BY senton ASC";
            $item->messages = $this->db->query($messagesql)->result();
            $this->db->where('bid', $item->id);
            $query = $this->db->get('biditem');
            $biresult = $query->result();
            $biditems = array();
            //$sqlItem = "SELECT id FROM ". $this->db->dbprefix('item') ." WHERE itemcode = ?"; 
            foreach ($biresult as $biditem) {
                $sqlmin = "SELECT MIN(ea) minprice FROM " . $this->db->dbprefix('biditem') . " WHERE ea > 0 AND attach='" . $biditem->attach . "' ";
                if ($this->session->userdata('usertype_id') > 1)
                    $sqlmin .= " AND purchasingadmin=" . $this->session->userdata('purchasingadmin');
                //echo $sqlmin;
                
                if($this->session->userdata('usertype_id') == 1)
                	$sqlmin .= " AND purchasingadmin=" . $biditem->purchasingadmin;	
                
                $biditem->minprice = $this->db->query($sqlmin)->row()->minprice;

                $sqlreq = "SELECT ea FROM " . $this->db->dbprefix('quoteitem') . " WHERE quote='" . $quote . "' AND attach='" . $biditem->attach . "'";
                $rowreqprice = $this->db->query($sqlreq)->row();
                $biditem->reqprice = @$rowreqprice->ea ? $rowreqprice->ea : '';
                //$itemid = $this->db->query($sqlItem, array($biditem->itemcode))->row();
                //$biditem->itemid = isset($itemid->id)?$itemid->id:'';

                /*if ($this->session->userdata('usertype_id') > 1) {
                    $this->db->where('id', $biditem->itemid);
                    $companyitem = $this->db->get('item')->row();
                    if ($companyitem) {
                        $biditem->itemcode = $companyitem->itemcode;
                        $biditem->itemname = $companyitem->itemname;
                    }
                }*/

                $biditems[] = $biditem;
            }
            $item->items = $biditems;

            $this->db->where('id', $item->company);
            $query = $this->db->get('users');
            $item->companyname = $query->row('companyname');

            $ret[] = $item;
        }
        //echo '<pre>';print_r($ret);die;
        return $ret;
    }
    
    function getbidsquote($quote) {
        $this->db->where('quote', $quote);
        $query = $this->db->get('bid');

        $ret = array();
        $result = $query->result();

        foreach ($result as $item) {
            $messagesql = "SELECT id FROM " . $this->db->dbprefix('message') . " WHERE quote='{$quote}' AND company='{$item->company}' ORDER BY senton ASC";
            $item->messages = $this->db->query($messagesql)->result();
            $this->db->where('bid', $item->id);
            $query = $this->db->get('biditem');
            $biresult = $query->result();
            $biditems = array();
            $sqlItem = "SELECT id FROM " . $this->db->dbprefix('item') . " WHERE itemcode = ?";
            foreach ($biresult as $biditem) {
                $sqlmin = "SELECT MIN(ea) minprice FROM " . $this->db->dbprefix('biditem') . " WHERE ea > 0 AND itemcode='" . $biditem->itemcode . "'";
                $biditem->minprice = $this->db->query($sqlmin)->row()->minprice;

                $sqlreq = "SELECT ea FROM " . $this->db->dbprefix('quoteitem') . " WHERE quote='" . $quote . "' AND itemcode='" . $biditem->itemcode . "'";
                $rowreqprice = $this->db->query($sqlreq)->row();

                $biditem->reqprice = @$rowreqprice->ea ? $rowreqprice->ea : '';
                $itemid = $this->db->query($sqlItem, array($biditem->itemcode))->row();

                $biditem->itemid = isset($itemid->id) ? $itemid->id : '';

                $biditems[] = $biditem;
            }
            $item->items = $biditems;

            $this->db->where('id', $item->company);
            $query = $this->db->get('company');
            $item->companyname = $query->row('title');

            $ret[] = $item;
        }
        //echo '<pre>';print_r($ret);die;
        return $ret;
    }

    function getbiditemsbyids($biditemids) {
        $sql = "SELECT bi.*, b.company 
				FROM " . $this->db->dbprefix('biditem') . " bi,  " . $this->db->dbprefix('bid') . " b
				WHERE bi.bid=b.id AND bi.id IN ($biditemids)
		";
        //echo $sql;die;
        $query = $this->db->query($sql);
        if ($query->result()) {
            $rows = $query->result();
            return $rows;
        } else {
            return array();
        }
    }

    // for autocomplete
    function findcostcode($key) {
        $key = urldecode($key);
        $sql = "SELECT * FROM " . $this->db->dbprefix('costcode')
                . " WHERE code LIKE '%" . $key . "%'";
        if ($this->session->userdata('usertype_id') > 1)
            $sql .= " AND purchasingadmin='" . $this->session->userdata('purchasingadmin') . "'";
        if ($this->session->userdata('managedproject'))
            $sql .= " AND project='" . $this->session->userdata('managedproject') . "'";
        $query = $this->db->query($sql);
        if ($query->result()) {
            $rows = $query->result();
            return $rows;
        } else {
            return array();
        }
    }

    // for autocomplete
    function finditemcode($key) {
        $key = urldecode($key);
          $sql = "SELECT * FROM " . $this->db->dbprefix('item')
                . " WHERE itemcode LIKE '%" . $key . "%' OR itemname LIKE '%" . $key . "%'";        
                
                
        /*
          if($this->session->userdata('usertype_id')>1)
          {
          $sql = "SELECT * FROM ".$this->db->dbprefix('companyitem')
          ." WHERE itemcode LIKE '%".$key."%' AND type='Purchasing'
          AND company='".$this->session->userdata('purchasingadmin')."'";
          }
         */
        $query = $this->db->query($sql);
        if ($query->result()) {
            $rows = $query->result();
            return $rows;
        } else {
            return array();
        }
    }

    // for fetching all information of item code on blur
    function finditembycode($code) {
        //echo $code;die;
        $code = urldecode($code);

        //$sql = 'SELECT * FROM ' . $this->db->dbprefix('item') . ' WHERE itemcode LIKE "%' . $code . '%"';
        $sql = 'SELECT * FROM ' . $this->db->dbprefix('item') . ' WHERE itemcode="' . $code . '"';
        /*
          if($this->session->userdata('usertype_id')>1)
          {
          $sql = "SELECT * FROM ".$this->db->dbprefix('companyitem') ."
          WHERE itemcode = '".$code."' AND type='Purchasing' AND
          company='".$this->session->userdata('purchasingadmin')."'";
          }
         */
      
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->result()) {
            $row = $query->row();
            $row->itemid = $row->id;
            /*
              if($this->session->userdata('usertype_id')>1)
              {
              $this->db->where('id',$row->itemid);
              $org = $this->db->get('item')->row();
              $row->unit = $org->unit;
              $row->notes = $org->notes;
              }
              else
              {

              }
             */
            return $row;
        } else {
            return array();
        }
    }

    function SaveQuote() {
        $options = array(
            'pid' => $this->input->post('pid'),
            'potype' => $this->input->post('potype'),
            'ponum' => $this->input->post('ponum'),
            'podate' => $this->input->post('podate'),
            'subject' => $this->input->post('subject'),
            'company' => $this->input->post('company'),
            'deliverydate' => $this->input->post('deliverydate'),
            'duedate' => $this->input->post('duedate'),
            'startdate' => $this->input->post('startdate'),
            'subtotal' => $this->input->post('subtotal'),
            'taxtotal' => $this->input->post('taxtotal'),
            'total' => $this->input->post('total'),
            'creation_date' => date('Y-m-d'),
            'contracttype' => $this->input->post('contracttype')
        );
        $options['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        //print_r($options);exit;
        $this->db->insert('quote', $options);
        return $this->db->insert_id();
    }

    // updating pricing column
    function updateQuote($id) {
        $options = array(
            'ponum' => $this->input->post('ponum'),
            'potype' => $this->input->post('potype'),
            'podate' => $this->input->post('podate'),
            'subject' => $this->input->post('subject'),
            'company' => $this->input->post('company'),
            'deliverydate' => $this->input->post('deliverydate'),
            'duedate' => $this->input->post('duedate'),
            'startdate' => $this->input->post('startdate'),
            'subtotal' => $this->input->post('subtotal'),
            'taxtotal' => $this->input->post('taxtotal'),
            'total' => $this->input->post('total'),
            'itemchk' => $this->input->post('itemchk'),
            'contracttype' => $this->input->post('contracttype'),
        );
        //print_r($_POST);exit;
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('quote', $options);
    }

    // removing product
    function remove_quote($id) {
        //$this->db->where('id', $id);
        //$this->db->delete('quote');

        $delsql = array();
        $delsql[] = "DELETE FROM " . $this->db->dbprefix('quote') . " WHERE id='$id'";

        $delsql[] = "DELETE FROM " . $this->db->dbprefix('quoteitem') . " WHERE quote='" . $id . "'";
        $delsql[] = "DELETE FROM " . $this->db->dbprefix('bid') . " WHERE quote='" . $id . "'";
        $delsql[] = "DELETE FROM " . $this->db->dbprefix('invitation') . " WHERE quote='" . $id . "'";
        $delsql[] = "DELETE FROM " . $this->db->dbprefix('award') . " WHERE quote='" . $id . "'";

        $bidsql = "SELECT * FROM " . $this->db->dbprefix('bid') . " WHERE quote='" . $id . "'";
        $bidquery = $this->db->query($bidsql);
        $bids = $bidquery->result();
        foreach ($bids as $bid) {
            $delsql[] = "DELETE FROM " . $this->db->dbprefix('biditem') . " WHERE bid='" . $bid->id . "'";
        }

        $awardsql = "SELECT * FROM " . $this->db->dbprefix('award') . " WHERE quote='" . $id . "'";
        $awardquery = $this->db->query($awardsql);
        $awards = $awardquery->result();
        foreach ($awards as $award) {
            $delsql[] = "DELETE FROM " . $this->db->dbprefix('awarditem') . " WHERE award='" . $award->id . "'";
        }

        foreach ($delsql as $sql) {
            $this->db->query($sql);
        }
    }

    // retrieve product by their id
    function get_quotes_by_id($id) {
        $this->db->where('id', $id);
        
        $query = $this->db->get('quote');
        if ($query->num_rows > 0) {
            $ret = $query->row();
            $this->db->where('id', $ret->pid);
            $query = $this->db->get('project');
            $ret->project = $query->row();
            return $ret;
        }
        return NULL;
    }
    
    function getrevisionno($bidid, $admin) {
                
        	$sqlq = "SELECT revisionid FROM ".$this->db->dbprefix('quoterevisions')." qr WHERE bid='".$bidid."' AND purchasingadmin='".$admin."' order by id desc limit 1";
        	$data = $this->db->query($sqlq)->row();
        	return $data;
    }
    
    function get_quotes_error_log($quote)
    {
        $this->db->select("quote_errorlog.*,company.*");
        $this->db->where('quoteid', $quote);
        $this->db->join("company",'company.id = quote_errorlog.companyid');
        $query = $this->db->get('quote_errorlog');
          if ($query->num_rows > 0) {
            $ret = $query->result();
            return $ret;
          }
          return NULL;

    }
    function checkDuplicatePonum($ponum, $edit_id = 0,$pid) 
    {
        $pa = $this->session->userdata('purchasingadmin');
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id,'purchasingadmin' => $pa, 'ponum' => $ponum));
        } else {
            $this->db->where(array('purchasingadmin' => $pa, 'ponum' => $ponum,'pid'=>$pid));
        }
        $query = $this->db->get('quote');
        $result = $query->result();

        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function changeDateRequested($quoteid, $date) {
        $query = "UPDATE " . $this->db->dbprefix('bid') . " b, " . $this->db->dbprefix('biditem') . " bi
					SET bi.daterequested = '$date'
					WHERE bi.bid=b.id AND b.quote='$quoteid'";
        $this->db->query($query);
        $query = "UPDATE " . $this->db->dbprefix('award') . " a, " . $this->db->dbprefix('awarditem') . " ai
					SET ai.daterequested = '$date'
					WHERE ai.award=a.id AND a.quote='$quoteid'";
        $this->db->query($query);
        $query = "UPDATE " . $this->db->dbprefix('quoteitem') . " 
					SET daterequested = '$date'
					WHERE quote='$quoteid'";
        $this->db->query($query);
    }

    function deleteitem($itemid, $quoteid) {
        //$this->db->where('id', $id);
        //$this->db->delete('quote');

        $delsql = array();

        $delsql[] = "DELETE FROM " . $this->db->dbprefix('quoteitem') . " WHERE id='" . $itemid . "'";

        $itemcode = '';
        $this->db->where('id', $itemid);
        $query = $this->db->get('quoteitem');
        if ($query->num_rows > 0) {
            $itemcode = $query->row('itemcode');
        }

        if ($itemcode) {
            $bids = $this->getbids($quoteid);
            if ($bids)
                foreach ($bids as $bid)
                    if ($bid->items)
                        foreach ($bid->items as $bi)
                            if ($bi->itemcode == $itemcode)
                                $delsql[] = "DELETE FROM " . $this->db->dbprefix('biditem') . " WHERE id='" . $bi->id . "'";

            $awarded = $this->getawardedbid($quoteid);
            if ($awarded)
                if ($awarded->items)
                    foreach ($awarded->items as $ai)
                        if ($ai->itemcode == $itemcode)
                            $delsql[] = "DELETE FROM " . $this->db->dbprefix('awarditem') . " WHERE id='" . $ai->id . "'";
        }

        foreach ($delsql as $sql) {
            $this->db->query($sql);
        }
    }

    // Start ON 21st jan 2014
    function getcomplat($session_id) {
        $query = "select user_lat from " . $this->db->dbprefix('users') . " ";
        $itemcode = '';
        $this->db->where('id', $session_id);
        $query = $this->db->get('users');
        if ($query->num_rows > 0) {
            $itemcode = $query->row('user_lat');
        }
        return $itemcode;
    }

    function getcomplong($session_id) {
        $query = "select user_lng from " . $this->db->dbprefix('users') . " ";
        $itemcode = '';
        $this->db->where('id', $session_id);
        $query = $this->db->get('users');
        if ($query->num_rows > 0) {
            $itemcode = $query->row('user_lng');
        }
        return $itemcode;
    }
    
    
    function getpendinginvoices($purchaser)
	{
		
		$invoicesql = "SELECT r.id, invoicenum, r.paymentstatus, r.paymenttype, r.refnum, r.datedue, r.purchasingadmin,  ROUND(SUM(ai.ea * r.quantity),2) totalprice, r.alertsentdate   
				   FROM 
				   ".$this->db->dbprefix('received')." r,
				   ".$this->db->dbprefix('awarditem')." ai
				  WHERE r.awarditem=ai.id 				 
				  AND ai.purchasingadmin=$purchaser 
				  AND r.paymentstatus <> 'Paid'
				  AND r.datedue < CURDATE() 
				  GROUP BY invoicenum
				  ";
		//echo $invoicesql;
		$invoicequery = $this->db->query($invoicesql);
		$invoice = $invoicequery->result();
		return $invoice;
	}
	
	
	function getpaymentrequestedorders($purchaser)
	{
		
		$invoicesql = "SELECT r.id, invoicenum, r.paymentstatus, r.paymenttype, r.refnum, r.datedue, r.purchasingadmin,  ROUND(SUM(ai.ea * r.quantity),2) totalprice, r.alertsentdate   
				   FROM 
				   ".$this->db->dbprefix('received')." r,
				   ".$this->db->dbprefix('awarditem')." ai
				  WHERE r.awarditem=ai.id 				 
				  AND ai.purchasingadmin=$purchaser 
				  AND r.paymentstatus <> 'Paid'
				  AND r.alertsentdate IS NOT NULL   
				  GROUP BY invoicenum
				  ";
		//echo $invoicesql;
		$invoicequery = $this->db->query($invoicesql);
		$invoice = $invoicequery->result();
		return $invoice;
	}
	
	function getBacktracks($purchaser)
	{
		if(@$purchaser)
			$this->db->where('purchasingadmin',$purchaser);
			$this->db->where('duedate < CURDATE()');
			$this->db->order_by("podate", "asc");
			$quotes = $this->db->get('quote')->result();
		
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
							if($item->received < $item->quantity)
							{
								$items[$quote->ponum]['quote'] = $quote;
								$item->ponum = $quote->ponum;
								$item->duequantity = $item->quantity - $item->received;
								if(!isset($items[$quote->ponum]['items']))
									$items[$quote->ponum]['items'] = array();
								$items[$quote->ponum]['items'][]=$item;
							}
						}
						
					}
				}
			}
		}
		return $items;
	}
	
	
	function get_items() {
        
        $sql = "SELECT * FROM " . $this->db->dbprefix('event') . "
        	WHERE purchasingadmin='".$this->session->userdata('purchasingadmin')."' and evtdate >= CURDATE() ";

        $query = $this->db->query($sql);
        if ($query->result()) {
            $result = $query->result();
            $ret = array();
            foreach ($result as $item) {
                $ret[] = $item;
            }
            //print_r($ret);die;
            return $ret;
        } else {
            return null;
        }
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
				if($item->received)
					return true;
			
		}
		return false;
	}
	
	
	
	function getcontractinvoices($company)
	{
		$search='';
		$searches = array();
		if(@$_POST['searchkeyword'])
		{
			$searches[] = " r.invoicenum LIKE '%{$_POST['searchkeyword']}%'";
		}
		if(@$_POST['searchstatus'])
		{
			$searches[] = " r.status='{$_POST['searchstatus']}'";
		}
        if (@$_POST['searchpaymentstatus'])
        {
        	if($_POST['searchpaymentstatus'] == "Unpaid")
        	$searches[] = " (r.paymentstatus = 'Unpaid' || r.paymentstatus = 'Requested Payment') ";
        	else
            $searches[] = " r.paymentstatus = '{$_POST['searchpaymentstatus']}' ";
        }
		if(@$_POST['searchfrom'])
		{
			$fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
			$searches[] = " receiveddate >= '$fromdate'";
		}
		if(@$_POST['searchto'])
		{
			$todate = date('Y-m-d', strtotime($_POST['searchto']));
			$searches[] = " receiveddate <= '$todate'";
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



		$query = "SELECT invoicenum, ROUND(SUM(totalprice * r.quantity/100),2) totalprice,
					receiveddate, r.status, r.paymentstatus, r.paymenttype, r.refnum, r.paymentdate, r.datedue
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
		$query = "SELECT r.invoicenum,q.ponum,r.quantity,ROUND(SUM(ai.ea * r.quantity/100),2) totalprice, receiveddate, r.status, r.paymentstatus, r.paymenttype, r.refnum, r.datedue,u.username,ai.company,c.companyname as title,c.username as supplierusername,ai.itemid,ai.itemcode,ai.itemname, (ai.ea*r.quantity/100) as price,c.address,c.phone,date_format(datedue,'%m/%d/%Y') as DueDate,stgs.taxrate,u.email,a.awardedon
				   FROM
				   ".$this->db->dbprefix('received')." r
				   LEFT JOIN  ".$this->db->dbprefix('awarditem')." ai ON r.awarditem =ai.id
				   LEFT JOIN  ".$this->db->dbprefix('users')." u ON u.purchasingadmin = r.purchasingadmin
				   LEFT JOIN  ".$this->db->dbprefix('users')." c ON ai.company = c.id	
				   LEFT JOIN  ".$this->db->dbprefix('settings')." stgs ON c.id = stgs.purchasingadmin 			   				   
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
	
	
	function getbillsdetailsformail($invoicenumber)
	{		
		$pafilter = '';
		if(@$_POST['searchpurchasingadmin'])
			$pafilter = " AND r.purchasingadmin='".$_POST['searchpurchasingadmin']."'";
			//b.paymenttype, b.refnum,
		$query = "SELECT billname, ROUND(SUM(bi.ea * bi.quantity),2) totalprice, b.status, b.paymentstatus, b.customerduedate, b.purchasingadmin, bi.quantity, bi.ea, b.billedon, c.name, c.email, bi.itemid, bi.itemcode, bi.itemname  FROM 			   
				   " . $this->db->dbprefix('billitem') . " bi left join " . $this->db->dbprefix('bill') . " b on  bi.bill=b.id 
				   left join " . $this->db->dbprefix('quote') . " q on b.quote=q.id left join  
				   " . $this->db->dbprefix('customer') . " c on b.customerid = c.id    
				  WHERE 1=1 $pafilter AND b.id='{$invoicenumber}' 
				   GROUP BY bi.id ";
		$invoicequery = $this->db->query($query);
		$items = $invoicequery->result();

		return $items;
	}

    // End
}

?>