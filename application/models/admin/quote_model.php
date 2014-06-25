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

    function getcompanylistbyids($ids) {
        $sql = "SELECT *
		FROM
		" . $this->db->dbprefix('company') . " WHERE id IN ($ids)";

        $query = $this->db->query($sql);
        $ret = $query->result();
        return $ret;
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
        $this->db->where('quote', $id);
        $query = $this->db->get('quoteitem');

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
                $itemsql = "SELECT r.*, ai.itemid, ai.itemname, ai.ea 
							  FROM 
							  " . $this->db->dbprefix('received') . " r, 
							  " . $this->db->dbprefix('awarditem') . " ai
							  WHERE r.awarditem=ai.id AND r.invoicenum='{$invoicenum->invoicenum}'";
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
            $searches[] = " (STR_TO_DATE(receiveddate, '%m/%d/%Y') >= '$fromdate'
						AND STR_TO_DATE(receiveddate, '%m/%d/%Y') <= '$todate')";
        } elseif (@$_POST['searchfrom']) {
            $fromdate = date('Y-m-d', strtotime($_POST['searchfrom']));
            $searches[] = " STR_TO_DATE(receiveddate, '%m/%d/%Y') >= '$fromdate'";
        } elseif (@$_POST['searchto']) {
            $todate = date('Y-m-d', strtotime($_POST['searchto']));
            $searches[] = " STR_TO_DATE(receiveddate, '%m/%d/%Y') <= '$todate'";
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


        $query = "SELECT invoicenum, ROUND(SUM(ai.ea * r.quantity),2) totalprice, receiveddate, 
        			r.status, r.paymentstatus, r.paymenttype, r.refnum
				   FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai,
				   " . $this->db->dbprefix('company') . " c,
				   " . $this->db->dbprefix('award') . " a,
				   " . $this->db->dbprefix('quote') . " q
				   
				  WHERE r.awarditem=ai.id AND ai.company=c.id 
				  AND ai.award=a.id AND a.quote=q.id " .
                $managedprojectdetails_id_sql
                . " $search GROUP BY invoicenum 
                ORDER BY STR_TO_DATE(r.receiveddate, '%m/%d/%Y') DESC";
        //echo $query;
        $invoicequery = $this->db->query($query);
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

    function getinvoicebynum($invoicenum) {

        $invoicesql = "SELECT invoicenum, ROUND(SUM(ai.ea * r.quantity),2) totalprice, 
        			r.status, r.paymentstatus, r.paymenttype, r.refnum, r.datedue
				   FROM 
				   " . $this->db->dbprefix('received') . " r,
				   " . $this->db->dbprefix('awarditem') . " ai
				  WHERE r.awarditem=ai.id AND invoicenum='{$invoicenum}'
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
				  WHERE r.awarditem=ai.id AND ai.award=a.id AND invoicenum='{$invoicenum}'
				  ";
        $quotequery = $this->db->query($quotesql);
        $invoice->quote = $quotequery->row('quote');

        $itemsql = "SELECT 
					r.*,ai.itemid, ai.itemcode, c.title companyname, r.datedue,
					ai.itemname, ai.ea, ai.unit, ai.daterequested, ai.costcode, ai.notes 
				  FROM 
				  " . $this->db->dbprefix('received') . " r, 
				  " . $this->db->dbprefix('awarditem') . " ai,
				  " . $this->db->dbprefix('company') . " c
				  WHERE r.awarditem=ai.id AND ai.company=c.id 
				  AND invoicenum='{$invoicenum}'
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
                . " WHERE itemcode LIKE '%" . $key . "%'";
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

        $sql = "SELECT * FROM " . $this->db->dbprefix('item') . " WHERE itemcode = '" . $code . "'";
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
            'subtotal' => $this->input->post('subtotal'),
            'taxtotal' => $this->input->post('taxtotal'),
            'total' => $this->input->post('total'),
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
            'subtotal' => $this->input->post('subtotal'),
            'taxtotal' => $this->input->post('taxtotal'),
            'total' => $this->input->post('total'),
            'itemchk' => $this->input->post('itemchk'),
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

    function checkDuplicatePonum($ponum, $edit_id = 0) 
    {
        $pa = $this->session->userdata('purchasingadmin');
        if ($edit_id > 0) {
            $this->db->where(array('id !=' => $edit_id,'purchasingadmin' => $pa, 'ponum' => $ponum));
        } else {
            $this->db->where(array('purchasingadmin' => $pa, 'ponum' => $ponum));
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

    // End
}

?>