<?php

class quote extends CI_Controller 
{
    private $limit = 10;

    function quote() 
    {
        parent::__construct();
        ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 1500);
        ini_set("set_time_limit", 200);
        $this->load->library('session');
        if (!$this->session->userdata('id')) {
            redirect('admin/login/index', 'refresh');
        }

        $this->load->dbforge();
        $this->load->library('form_validation');

        $this->load->library(array('table', 'validation', 'session'));
        $this->load->helper('form', 'url');
        $this->load->model('admin/quote_model');
        $data['pendingbids'] = $this->quote_model->getpendingbids();
        $this->form_validation->set_error_delimiters('<div class="red">', '</div>');
        $data ['title'] = "Administrator";

        $this->load->model('admin/settings_model');
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $this->load->model('admin/project_model');
        $this->load->model('admin/company_model');
        $this->load->model('admin/itemcode_model');
        $this->load = new My_Loader();
        $this->load->template('../../templates/admin/template', $data);
    }

    function calendar() 
    {
        $data = array();
        $this->load->view('admin/calendar', $data);
    }

    function jsonlist() 
    {
        //echo '<pre>';
        $mp = $this->session->userdata('managedprojectdetails');
        $quotes = $this->quote_model->get_quotes('',$mp ? $mp->id : '' );
        $quotelist = array();
        $added_date = array();
        foreach ($quotes as $quote) {
            $quote->awardedbid = $this->quote_model->getawardedbid($quote->id);
            if ($quote->awardedbid) {
                if (@$quote->awardedbid->items) {
                    //echo $quote->id;
                    $added_date[$quote->id] = array();
                    $quote->url = site_url('admin/quote/track/' . $quote->id);
                    if ($this->session->userdata('usertype_id') == 3)
                        $quote->url = "javascript:void(0)";
                    $quote->title = $quote->ponum;
                    foreach ($quote->awardedbid->items as $item) {
                        if (!in_array($item->daterequested, $added_date[$quote->id])) {
                            $added_date[$quote->id][] = $item->daterequested;
                            $date = date("Y-m-d", strtotime($item->daterequested));
                            $quote->start = $date;
                            $quote->end = $date;

                            $obj = array();
                            $obj['url'] = $quote->url;
                            $obj['title'] = $quote->title;
                            $obj['start'] = $date;
                            $obj['end'] = $date;
                            if ($this->session->userdata('usertype_id') == 3) {
                                $checkauth = array('quote' => $quote->id, 'userid' => $this->session->userdata('id'));
                                $this->db->where($checkauth);

                                $checkauth = $this->db->get('quoteuser')->num_rows;
                                if ($checkauth)
                                    $quotelist[] = $obj;
                            }
                            else {
                                $quotelist[] = $obj;
                            }
                        }
                    }
                }
            }
        }
        //print_r($quotelist);
        //fwrite(fopen('test.txt',"w+"), print_r($quotelist,true));
        echo json_encode($quotelist);
    }

    function index($pid) 
    {
        $temp['managedproject'] = $data['pid'] = $pid;
        //$this->load->model('admin/project_model');
        $temp['managedprojectdetails'] = $this->project_model->get_projects_by_id($pid);
        if ($this->session->userdata('usertype_id') == 2 && $temp['managedprojectdetails']->purchasingadmin != $this->session->userdata('id')) {
            //redirect('admin/dashboard', 'refresh'); 
        }
        $this->session->set_userdata($temp);

        $quotes = $this->quote_model->get_quotes('',$pid);

        $config ['total_rows'] = $this->quote_model->total_quote();

        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('ID', 'Name', 'Actions');
		
        $data['counts'] = count($quotes);
        
        $count = count($quotes);
        $items = array();
        if ($count >= 1) {
            
            foreach ($quotes as $quote) { //echo $quod = $this->quote_model->getbidsjag($quote->id);exit;
                $quote->invitations = $this->quote_model->getInvitedquote($quote->id);
                $quote->pendingbids = $this->quote_model->getbidsquote($quote->id);
                $quote->awardedbid = $this->quote_model->getawardedbidquote($quote->id);
                //print_r($quote->awardedbid);
                $quoteponum = $quote->ponum;
                $quote->pricerank = '-';
                if (!$quote->awardedbid)
                    $quote->pricerank = '-';
                elseif (!@$quote->awardedbid->items)
                    $quote->pricerank = '-';
                else {
                    $quote->ponum = '<a href="javascript:void(0)" onclick="viewitems(\'' . $quote->id . '\')">' . $quote->ponum . '</a>';
					/*
                    $totalcount = count($quote->awardedbid->items);
                    $lowcount = 0;
                    foreach ($quote->awardedbid->items as $ai) {
                        $itemlowest = $this->itemcode_model->getlowestquoteprice($ai->itemid);
                        
                        if ($ai->ea <= $itemlowest)
                            $lowcount++;
                    }
                    
                    if ($lowcount >= ($totalcount * 0.8))
                        $quote->pricerank = 'great';
                    elseif ($lowcount >= ($totalcount * 0.7))
                        $quote->pricerank = 'good';
                    elseif ($lowcount >= ($totalcount * 0.5))
                        $quote->pricerank = 'fair';
                    else
                        $quote->pricerank = 'poor';
                    */
                    if($quote->awardedbid->pricerank)
                    {
	                    $quote->pricerank = $quote->awardedbid->pricerank;
	                    $quote->pricerank = '<img src="'.site_url('templates/admin/images/rank'.$quote->pricerank.'.png').'"/>';
                	}
                }
                //$quote->awardedcompany = $quote->awardedbid?$quote->awardedbid->companyname:'-';
                $quote->podate = $quote->podate ? $quote->podate : '';
                $quote->status = $quote->awardedbid ? 'AWARDED' : ($quote->pendingbids ? 'PENDING AWARD' : ($quote->invitations ? 'NO BIDS' : ($quote->potype == 'Direct' ? '-' : 'NO INVITATIONS')));
                //echo '<pre>';print_r($quote->awardedbid);die;
                if ($quote->status == 'AWARDED') {
                    $quote->status = $quote->status . ' - ' . strtoupper($quote->awardedbid->status);
                }
                $quote->actions = $quote->awardedbid?'':
                anchor('admin/quote/items/' . $quote->id, '<span class="icon-2x icon-search"></span>', array('class' => 'view', 'title' => 'view quote items'))
                ;
                if (empty($quote->awardedbid)) {
                    $quote->actions .=

                            anchor('admin/quote/update/' . $quote->id, '<span class="icon-2x icon-edit"></span>', array('class' => 'update'))
                            . ' ' .
                            anchor('admin/quote/delete/' . $quote->id, '<span class="icon-2x icon-trash"></span>', array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"))
                    ;
                } else {
                    $quote->actions .= anchor('admin/quote/delete/' . $quote->id, '<span class="icon-2x icon-trash"></span>', array('class' => 'delete', 'onclick' => "return confirm('Are you sure want to Delete this Records?')"))
                    ;
                    //$quote->actions .= anchor ('admin/quote/update/' . $quote->id,'<span class="icon-2x icon-edit"></span>',array ('class' => 'update' ) );
                }
                //$quote->sent ='';
                //if($quote->invitations && !$quote->awardedbid)	{
                $quote->sent = '<div class="badgepos"><span class="badge badge-blue">' . count($quote->invitations) . '</span></div>'
                ;
                //}
                if ($quote->awardedbid) {
                    //$quote->actions.= ' ' . 
                    //anchor ( 'admin/quote/bids/' . $quote->id, '<span class="icon-2x icon-search"></span> ', array ('class' => 'view','alt' => 'awarded bid','title' => 'awarded bid' ) ) 
                    //;
                    $quote->actions.= ' ' .
                            anchor('admin/quote/track/' . $quote->id, '<span class="label label-pink">Track</span> ', array('class' => 'view', 'alt' => 'awarded bid', 'title' => 'awarded bid'))
                    ;
                }
                $quote->recived = '';
                if ($quote->pendingbids) {
                    $quote->recived = anchor('admin/quote/bids/' . $quote->id, '<div class="badgepos"><span class="badge badge-red">' . count($quote->pendingbids) . '</span></div>', array('class' => 'view'))
                    ;
                }
                $quote->actions .=
                        '<a href="javascript:void(0)" onclick="duplicate(\'' . $quote->id . '\')" ><span class="icon-2x icon-copy"></span></a>'
                ;
                if ($this->session->userdata('usertype_id') == 2) {
                    $quote->actions .=
                            ' <a href="javascript: void(0)" onclick="quotepermission(' . $quote->id . ',\'' . $quoteponum . '\')"><span class="icon-2x icon-key"></span></a>';
                    ;
                }
                if (@$_POST['postatus']) {
                    if ($quote->status == $_POST['postatus']) {
                        $items[] = $quote;
                    }
                } else {
                    $items[] = $quote;
                }
            }
            $data['items'] = $items;
            $data['jsfile'] = 'quotejs.php';
        } else {
            $this->data['message'] = 'No Records';
        }
        //$data['companies'] = $this->db->get('company')->result();
        $data ['addlink'] = '';
        $data ['heading'] = "Quote &amp; Purchase Order Management - " . $this->session->userdata('managedprojectdetails')->title;
        $data ['table'] = $this->table->generate();
        $data ['addlink'] = '<a class="btn btn-green" href="' . base_url() . 'admin/quote/add/' . $pid . '">Add Quote</a>&nbsp;';
        $data ['addlink'].= '<a class="btn btn-green" href="' . base_url() . 'admin/quote/add/' . $pid . '/Direct">Add Purchase Order</a>';
        $mess= $this->session->flashdata('message');
        if(isset($mess) && $this->session->flashdata('message')!=""){
        	$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Permissions assigned.</div></div>');
        }
        
        $this->load->view('admin/quotelist', $data);
    }

    function items($id) 
    {
        $quote = $this->quote_model->get_quotes_by_id($id);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre>';print_r($quote);die;
        if (!$quote) {
            die;
        }
        $quoteitems = $this->quote_model->getitems($id);
        $data['quote'] = $quote;
        $data['quoteitems'] = $quoteitems;
        //$this->load->model('admin/project_model');
        $data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $data['heading'] = "Original quote items: ".$quote->ponum;
        $this->load->view('admin/quotedetails', $data);
    }

    function getitemsajax() 
    {
        $id = $_POST['quote'];
        $quote = $this->quote_model->get_quotes_by_id($id);
        //echo '<pre>';print_r($quote);die;
        if (!$quote) {
            die;
        }
        $quoteitems = $this->quote_model->getitems($id);
        if (!$quoteitems) {
            die('No items');
        }
        //$this->load->model('admin/project_model');
        $awarded = $this->quote_model->getawardedbid($id);
        $awardeditems = array();
        if ($awarded) {
            if ($awarded->items) {
                $awardeditems = $awarded->items;
            }
        }
        $ret = '<h5>PO#:' . $quote->ponum . '&nbsp; &nbsp;' . anchor('admin/quote/track/' . $quote->id, '<span class="label label-pink">Track</span> ', array('class' => 'view')) . '</h5>';
        $ret .= '<table class="table table-bordered">';
        $ret .= '<tr><th>Itemcode</th><th>Price Status</th><th>Qty.</th><th>Qty. received</th><th>Qty. due</th><th>Status</th></tr>';
        foreach ($quoteitems as $item) 
        {
            $awarded = false;
            $status = '-';
            $paidprice = '';
            foreach ($awardeditems as $ai) 
            {
                if ($ai->itemcode == $item->itemcode) 
                {
                    $awarded = true;
                    $paidprice = $ai->ea;
                    $status = 'Not Complete';
                    if ($ai->quantity == $ai->received) 
                    {
                        $status = 'Complete';
                    }
                    $item->received = $ai->received;
                    
                }
            }
            //$avgprice = $this->itemcode_model->getdaysmeanprice($item->itemcode);
            $lowestprice = $this->itemcode_model->getlowestquoteprice($item->itemid);
            if ($lowestprice < $paidprice)
                $ps = 'high';
            elseif ($lowestprice > $paidprice)
                $ps = 'good1';
            else
                $ps = 'equal';
            //$ps = 'paid-'.$paidprice. $ps.' avg for 120 days-'. $avgprice;
             if($awarded)
                $ret .= '<tr><td><a href="javascript:void(0)" onclick="viewitems2(\''.$item->itemid.'\')">'.$item->itemcode.'</a></td><td width="64"><img src="' . site_url('templates/admin/images/'.$ps.'.png') . '" width="64"/></td><td>' . $item->quantity . '</td><td>' . $item->received . '</td><td>' . ($item->quantity - $item->received) . '</td><td>' . $status . '</td></tr>';
        }
        $ret .= '</table>';
        echo $ret;
    }

    function duplicate() 
    {
        $post = $this->input->post();

        if (!$post)
            die;
        if (!$post['id'])
            die;
        if (!$post['ponum'])
            die;
        $quote = (array) $this->quote_model->get_quotes_by_id($post['id']);
        
        if ($this->session->userdata('usertype_id') == 2 && $quote['purchasingadmin'] != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $quoteitems = $this->quote_model->getitems($post['id']);

        $quote['ponum'] = $post['ponum'];
        //$quote['potype'] = $post['potype'];
        unset($quote['id']);
        unset($quote['project']);
        $this->quote_model->db->insert('quote', $quote);
        $quoteid = $this->quote_model->db->insert_id();
        if ($quoteitems)
            foreach ($quoteitems as $item) {
                $item = (array) $item;
                $item['quote'] = $quoteid;
                unset($item['id']);
                $this->quote_model->db->insert('quoteitem', $item);
            }
        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Purchase Order Duplicated Successfully</div></div>');
        redirect('admin/quote/update/' . $quoteid);
    }

    function checkpo($ponum) 
    {
        if ($this->quote_model->checkDuplicatePonum($ponum, 0))
            echo 'Duplicate';
        else
            echo 'Allow';
        die;
    }

    function add($pid, $potype = "Bid") 
    {
        $this->_set_fields();
        $data['pid'] = $pid;
        $data['potype'] = $potype;
        $data ['heading'] = $potype == "Bid" ? 'Add New Quote' : "Add New Purchase Order";
        $data ['message'] = '';
        $data['companylist'] = $this->quote_model->getcompanylist();
        $data['quoteitems'] = array();
        $data ['action'] = site_url('admin/quote/add_quote/' . $pid . '/' . $potype);

        if ($this->session->userdata('defaultdeliverydate'))
            $this->validation->deliverydate = $this->session->userdata('defaultdeliverydate');
        $this->validation->potype = $potype;
        if ($potype == 'Bid')
            $this->load->view('admin/quote', $data);
        else
            $this->load->view('admin/direct', $data);
    }

    function add_quote($pid, $potype = "Bid") 
    {
        if (!@$data)
            $data = array();
        $data = array_merge($data, $_POST);
        $data ['heading'] = $potype == "Bid" ? 'Add New Quote' : "Add New Purchase Order";
        $data ['action'] = site_url('admin/quote/add_quote/' . $pid . '/' . $potype);
        $data['companylist'] = $this->quote_model->getcompanylist();
        $data['quoteitems'] = array();
        $data['pid'] = $pid;
        $data['potype'] = $potype;
        $this->validation->potype = $potype;

        $this->_set_fields();
        $this->_set_rules();
        if ($this->validation->run() == FALSE) {
            $this->load->view('admin/quote', $data);
        } elseif ($this->quote_model->checkDuplicatePonum($this->input->post('ponum'), 0)) {
            $data ['message'] = 'Duplicate PO#';
            //$this->load->view ('admin/quote', $data);
            if ($potype == 'Bid')
                $this->load->view('admin/quote', $data);
            else
                $this->load->view('admin/direct', $data);
        }
        else {
            if ($this->input->post('makedefaultdeliverydate') == '1') {
                $temp['defaultdeliverydate'] = $this->input->post('deliverydate');
                $this->session->set_userdata($temp);
            }
            $itemid = $this->quote_model->SaveQuote();
            $itemtype = $potype == "Bid" ? 'Quote' : 'Purchase Order';
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">' . $itemtype . ' Added Successfully , Please add items below</div></div>');
            redirect('admin/quote/update/' . $itemid);
        }
    }

    function update($id) 
    {
        $this->_set_fields();
        $config = (array) $this->settings_model->get_current_settings();

        $item = $this->quote_model->get_quotes_by_id($id);
        if ($this->session->userdata('usertype_id') == 2 && $item->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }

        $data['quote'] = $item;
        $this->validation->id = $id;
        $this->validation->pid = $data['pid'] = $item->pid;
        $this->validation->potype = $data['potype'] = $item->potype;
        $this->validation->ponum = $item->ponum;
        $this->validation->podate = $item->podate;
        $this->validation->duedate = $item->duedate;
        $this->validation->subject = $item->subject;
        $this->validation->company = $item->company;
        $this->validation->quoteattachment = $item->quoteattachment;
        $this->validation->itemchk = $item->itemchk;
        $this->validation->deliverydate = $item->deliverydate;
        $this->validation->subtotal = $this->quote_model->getsubtotal($id);

        $this->validation->taxtotal = $this->validation->subtotal * $config['taxpercent'] / 100;
        $this->validation->total = $this->validation->subtotal + $this->validation->taxtotal;

        //echo($this->validation->company);die;

        $data['quoteitems'] = $this->quote_model->getitems($id);
        $data['companylist'] = $this->quote_model->getcompanylist();
        $data['invited'] = $this->quote_model->getInvited($id);
        $data['reminder'] = $this->quote_model->getInvitedButNotBid($id);
        
        $data['costcodes'] = $this->db->where('project',$item->pid)->get('costcode')->result();

        $this->db->where('quote', $id);
        $invitations = $this->db->get('invitation')->result();

        $data['invitations'] = array();
        foreach ($invitations as $inv) {
            $data['invitations'][$inv->company] = $inv;
        }
        
        $data['awarded'] = $this->quote_model->getawardedbid($id);
        $data['bids'] = $this->quote_model->getbids($id);

        $data ['heading'] = $data['potype'] == "Bid" ? 'Update Quote Item' : 'Update Purchase Order Item';
        $data['categorymenu'] = $this->items_model->getCategoryMenu();
        $data['categorymenuitems'] = $this->items_model->getCategoryMenuItems();
        $data ['message'] = '';
        $data ['action'] = site_url('admin/quote/updatequote');
        //$this->load->view ('admin/quote', $data);

        if ($data['potype'] == 'Bid')
            $this->load->view('admin/quote', $data);
        else
            $this->load->view('admin/direct', $data);
    }

    function updatequote() 
    {
        $data ['heading'] = 'Update Quote Item';
        $data ['action'] = site_url('message/updatequote');
        $this->_set_fields();
        $this->_set_rules();

        $itemid = $this->input->post('id');
        $pid = $this->input->post('pid');
        $data['costcodes'] = $this->db->where('project',$pid)->get('costcode')->result();

        if ($this->validation->run() == FALSE) {
            $data['quoteitems'] = $this->quote_model->getitems($itemid);
            $data['companylist'] = $this->quote_model->getcompanylist();
            $data['pid'] = $this->input->post('pid');
            $data ['action'] = site_url('admin/quote/updatequote');
            $this->load->view('admin/quote', $data);

            if ($this->input->post('potype') == 'Bid')
                $this->load->view('admin/quote', $data);
            else
                $this->load->view('admin/direct', $data);
        }
        else 
        {
            if ($this->input->post('makedefaultdeliverydate') == '1') 
            {
                $temp['defaultdeliverydate'] = $this->input->post('deliverydate');
                $this->session->set_userdata($temp);

                $this->quote_model->changeDateRequested($itemid, $this->input->post('deliverydate'));
            }
            $pid = $this->input->post('pid');
            $this->quote_model->updateQuote($itemid);
            $data ['message'] = '<div class="success">Quote has been updated.</div>';

            $itemtype = $this->input->post('potype') == "Bid" ? 'Quote' : 'Purchase Order';
            $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">' . $itemtype . ' Saved</div></div>');
            
            $quoteitems = $this->quote_model->getitems($itemid);
    		$emailitems = '<table>';
    		$emailitems.= '<tr>';
    		$emailitems.= '<th>Itemcode</th>';
    		$emailitems.= '<th>Itemname</th>';
    		$emailitems.= '<th>Qty</th>';
    		$emailitems.= '<th>Price</th>';
    		$emailitems.= '<th>Unit</th>';
    		$emailitems.= '<th>Notes</th>';
    		$emailitems.= '</tr>';
    		foreach($quoteitems as $q)
    		{
    		    $emailitems.= '<tr>';
        		$emailitems.= '<td>'.$q->itemcode.'</td>';
        		$emailitems.= '<td>'.$q->itemname.'</td>';
        		$emailitems.= '<td>'.$q->quantity.'</td>';
        		$emailitems.= '<td>'.$q->ea.'</td>';
        		$emailitems.= '<td>'.$q->unit.'</td>';
        		$emailitems.= '<td>'.$q->notes.'</td>';
        		$emailitems.= '</tr>';
    		}
    		$emailitems .= '</table>';
            
            $invitees = $this->input->post('invitees');
            if ($invitees) 
            {
                $companies = $this->quote_model->getcompanylistbyids($invitees);
                $companynames = array();
                foreach ($companies as $c) 
                {
                    $companynames[] = $c->title;
                    $key = md5($c->id . '-' . $itemid . '-' . date('YmdHisu'));
                    $insertarray = array(
                        'quote' => $itemid,
                        'company' => $c->id,
                        'senton' => date('Y-m-d'),
                        'invitation' => $key,
                        'purchasingadmin' => $this->session->userdata('purchasingadmin'),
                        'itemcheck' => $this->input->post('itemchk')
                    );

                    $this->quote_model->db->insert('invitation', $insertarray);

                    $link = base_url() . 'home/quote/' . $key;
                    $body = "Dear " . $c->title . ",<br><br>
				    		 
				  	Please click following link for the quote PO# " . $this->input->post('ponum') . " :  <br><br>		 
				    <a href='$link' target='blank'>$link</a>.<br><br/>
				    Please find the details below:<br/><br/>
		  	        $emailitems
				    ";
                    //$this->load->model('admin/settings_model');
                    $settings = (array) $this->settings_model->get_current_settings();
                    $this->load->library('email');

                    $this->email->from($settings['adminemail'], "Administrator");

                    $this->email->to($settings['adminemail'] . ',' . $c->primaryemail);
                    /* $emails = explode(',',$c->email);
                      if($emails)
                      foreach($emails as $email)
                      {
                      $this->email->cc($email);
                      } */

                    $this->email->subject('Request for Quote Proposal PO# ' . $this->input->post('ponum'));
                    $this->email->message($body);
                    $this->email->set_mailtype("html");
                    $this->email->send();

                    $notification = array(
                        'quote' => $itemid,
                        'company' => $c->id,
                        'ponum' => $this->input->post('ponum'),
                        'category' => 'Invitation',
                        'senton' => date('Y-m-d H:i'),
                        'isread' => '0',
                        'purchasingadmin' => $this->session->userdata('purchasingadmin')                        
                    );
                    $this->db->insert('notification', $notification);
                }
                $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Quote Sent to Companies: ' . implode(', ', $companynames) . '</div></div>');
            
            }

            $reminders = $this->input->post('reminders');
            if ($reminders) 
            {
                $companies = $this->quote_model->getcompanylistbyids($reminders);
                $companynames = array();
                foreach ($companies as $c) 
                {
                    $companynames[] = $c->title;

                    $key = $this->quote_model->getInvitationKey($itemid, $c->id);

                    $link = base_url() . 'home/quote/' . $key;
                    $body = "Dear " . $c->title . ",<br><br>
				    This is a reminder email for earlier invitation for the quote.<br><br>
				  	Please click following link for the quote PO# " . $this->input->post('ponum') . "):  <br><br>	 
				    <a href='$link' target='blank'>$link</a>
				    ";
                    //$this->load->model('admin/settings_model');
                    $settings = (array) $this->settings_model->get_current_settings();
                    $this->load->library('email');
                    $this->email->clear(true);
                    $this->email->from($settings['adminemail'], "Administrator");

                    $this->email->to( $c->title . ',' . $c->primaryemail);

                    $this->email->subject('Request for Quote Proposal PO# ' . $this->input->post('ponum'));
                    $this->email->message($body);
                    $this->email->set_mailtype("html");
                    $this->email->send();

                    $this->db->where('quote', $itemid);
                    $this->db->where('company', $c->id);
                    $this->db->update('invitation', array('remindedon' => date('Y-m-d'),'itemcheck' => $this->input->post('itemchk')));

                    $notification = array(
                        'quote' => $itemid,
                        'company' => $c->id,
                        'ponum' => $this->input->post('ponum'),
                        'category' => 'Invitation Reminder',
                        'senton' => date('Y-m-d H:i'),
                        'isread' => '0',
                        'purchasingadmin' => $this->session->userdata('purchasingadmin')
                    );
                    $this->db->insert('notification', $notification);
                }
                $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Quote Sent to Companies: ' . implode(', ', $companynames) . '</div></div>');
            }

            $revisions = $this->input->post('revisions');
            if ($revisions) {
                $companies = $this->quote_model->getcompanylistbyids($revisions);
                $companynames = array();
                foreach ($companies as $c) {
                    $companynames[] = $c->title;

                    $key = $this->quote_model->getInvitationKey($itemid, $c->id);

                    $link = base_url() . 'home/quote/' . $key;
                    $body = "Dear " . $c->title . ",<br><br>
				    This is to notify you that there is a revision about the quote.<br><br>
				  	Please click following link for the quote PO# " . $this->input->post('ponum') . "):  <br><br>	 
				    <a href='$link' target='blank'>$link</a>
				    ";
                    //$this->load->model('admin/settings_model');
                    $settings = (array) $this->settings_model->get_current_settings();
                    $this->load->library('email');
                    $this->email->clear(true);
                    $this->email->from($settings['adminemail'], "Administrator");

                    $this->email->to($settings['adminemail'] . ',' . $c->primaryemail);

                    $this->email->subject('Request for Quote Proposal PO# ' . $this->input->post('ponum'));
                    $this->email->message($body);
                    $this->email->set_mailtype("html");
                    $this->email->send();

                    $this->db->where('quote', $itemid);
                    $this->db->where('company', $c->id);
                    $this->db->update('invitation', array('revisionsenton' => date('Y-m-d')));

                    $notification = array(
                        'quote' => $itemid,
                        'company' => $c->id,
                        'ponum' => $this->input->post('ponum'),
                        'category' => 'Invitation Revision',
                        'senton' => date('Y-m-d H:i'),
                        'isread' => '0',
                        'purchasingadmin' => $this->session->userdata('purchasingadmin')
                    );
                    $this->db->insert('notification', $notification);
                }
                $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Quote Sent to Companies: ' . implode(', ', $companynames) . '</div></div>');
            }

            redirect('admin/quote/update/' . $itemid);
            //redirect('admin/quote/index/'.$pid); 
        }
    }

    function assignpo() 
    {
        $post = $this->input->post();

        if (!$post)
            die;
        $quote = $this->quote_model->get_quotes_by_id($post['id']);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $project = $this->project_model->get_projects_by_id($quote->pid);
        if ($this->session->userdata('usertype_id') == 2 && $project->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        

        $items = $this->quote_model->getitems($quote->id);
        $invitees = array();
        $companyrows = array();
        foreach($items as $item)
        {
            $invitees[$item->company] = $item->company;
            
            if(!isset($companyrows[$item->company]))
            {
            	$companyrows[$item->company] = array();
            }
    		    $companyrow = '<tr>';
        		$companyrow.= '<td>'.$item->itemcode.'</td>';
        		$companyrow.= '<td>'.$item->itemname.'</td>';
        		$companyrow.= '<td>'.$item->quantity.'</td>';
        		$companyrow.= '<td>'.$item->ea.'</td>';
        		$companyrow.= '<td>'.$item->unit.'</td>';
        		$companyrow.= '<td>'.$item->notes.'</td>';
        		$companyrow.= '</tr>';
        	
        	$companyrows[$item->company][] = $companyrow;
        }
        $companies = $this->quote_model->getcompanylistbyids(implode(',',$invitees));
        $companynames = array();
        foreach ($companies as $c) 
        {
    		$emailitems = '<table>';
    		$emailitems.= '<tr>';
    		$emailitems.= '<th>Itemcode</th>';
    		$emailitems.= '<th>Itemname</th>';
    		$emailitems.= '<th>Qty</th>';
    		$emailitems.= '<th>Price</th>';
    		$emailitems.= '<th>Unit</th>';
    		$emailitems.= '<th>Notes</th>';
    		$emailitems.= '</tr>';
    		$emailitems.= implode('',$companyrows[$c->id]);
    		$emailitems.= '</table>';
    		
            $companynames[] = $c->title;
            $key = md5($c->id . '-' . $quote->ponum . '-' . date('YmdHisu'));
            $insertarray = array(
                'quote' => $quote->id,
                'company' => $c->id,
                'senton' => date('Y-m-d'),
                'invitation' => $key,
                'purchasingadmin' => $this->session->userdata('purchasingadmin')
            );

            $this->quote_model->db->insert('invitation', $insertarray);

            $link = base_url() . 'quote/direct/' . $key;
            $body = "Dear " . $c->title . ",<br><br>
		    		 
		  	Please click on following link to review the purchase order(PO# " . $quote->ponum . "):  <br><br>		 
		    <a href='$link' target='blank'>$link</a><br><br>
		    The PO Details are:<br><br>
		    $emailitems
		    ";
            //$this->load->model('admin/settings_model');
            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');

            $this->email->from($settings['adminemail'], "Administrator");
            $this->email->to($settings['adminemail'] . ',' . $c->primaryemail);
            $this->email->subject('Request for Quote Proposal (PO# ' . $quote->ponum.')');
            $this->email->message($body);
            $this->email->set_mailtype("html");
            $this->email->send();

            $notification = array(
                'quote' => $quote->id,
                'company' => $c->id,
                'ponum' => $quote->ponum,
                'category' => 'Invitation(Direct)',
                'senton' => date('Y-m-d H:i'),
                'isread' => '0',
                'purchasingadmin' => $this->session->userdata('purchasingadmin')
            );
            $this->db->insert('notification', $notification);
        }
        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
        	<div class="msgBox">Purchase Order Sent to Companies: ' . implode(', ', $companynames) . '</div></div>');
        
        redirect('admin/quote/index/' . $project->id);
        
        /*
        return 1;
        $awardarray = array(
            'quote' => $quote->id,
            'awardedon' => date('Y-m-d'),
            'shipto' => $post['shipto'],
            'purchasingadmin' => $this->session->userdata('purchasingadmin')
        );
        $this->quote_model->db->insert('award', $awardarray);
        $awardedid = $this->quote_model->db->insert_id();

        $companies = array();
        foreach ($items as $item) 
        {
            $item = (array) $item;
            $insertarray = array();
            $insertarray['award'] = $awardedid;
            while (list($k, $v) = each($item)) 
            {
                if ($k != 'id' && $k != 'quote') {
                    $insertarray[$k] = $item[$k];
                }
            }
            $insertarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->quote_model->db->insert('awarditem', $insertarray);

            if (!isset($companies[$item['company']])) {
                $bidarray = array('quote' => $quote->id, 'submitdate' => date('Y-m-d'), 'company' => $item['company'], 'complete' => 'Yes', 'draft' => 'No');
                $bidarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                $this->quote_model->db->insert('bid', $bidarray);
                $companies[$item['company']] = $this->quote_model->db->insert_id();
            }

            $biditem = $item;
            unset($biditem['id']);
            unset($biditem['quote']);
            unset($biditem['company']);
            $biditem['bid'] = $companies[$item['company']];
            $biditem['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->quote_model->db->insert('biditem', $biditem);
        }
        $this->sendawardemail($quote->id);
        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
													<div class="msgBox">Purcnase Order Assigned Sucessfully.</div></div>');
		*/
        redirect('admin/quote/index/' . $project->id);
    }

    function updateitems($qid) 
    {
        
        $items = $this->quote_model->getitems($qid);
        $quote = $this->quote_model->get_quotes_by_id($qid);
        //echo '<pre>';print_r($_POST);die;
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id'))
        {
        	redirect('admin/dashboard', 'refresh');
        }
        foreach ($items as $item) 
        {
        	$itemcode = @$_POST['itemcode' . $item->id];
            if ( $itemcode && !$this->db->where('itemcode',$itemcode)->get('item')->row() ) 
            {die('asdf');
                $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a>
    			<div class="msgBox">Itemcode "'.$itemcode.'" does not exist.</div></div>');
                redirect('admin/quote/update/' . $qid);
            }
        }
      //  die;
        foreach ($items as $item) 
        {
            if($quote->potype=='Direct')
            if(!@$_POST['ea' . $item->id] || !@$_POST['ea' . $item->id]=='0.00')
            {
                $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a>
    			<div class="msgBox">Item price cannot be 0</div></div>');
                continue;
            }
            $updatearray = array();
            $key = $item->id;
            while (list($k, $v) = each($item)) 
            {
                if ($k != 'id' && $k != 'quote' && $k != 'purchasingadmin')
                    $updatearray[$k] = @$_POST[$k . $key];
                if ($k == 'ea' || $k == 'totalprice')
                    $updatearray[$k] = str_replace('$ ', '', $updatearray[$k]);
            }
            //print_r($updatearray);die;
            $updatearray['totalprice'] = $updatearray['quantity'] * $updatearray['ea'];
            $this->quote_model->db->where('id', $key);
            $this->quote_model->db->update('quoteitem', $updatearray);
            if (!$this->quote_model->finditembycode($updatearray['itemcode'])) {
                $itemcode = array(
                    'itemcode' => $updatearray['itemcode'],
                    'itemname' => $updatearray['itemname'],
                    'unit' => $updatearray['unit'],
                    'ea' => $updatearray['ea'],
                    'notes' => $updatearray['notes']
                );
                $this->quote_model->db->insert('item', $itemcode);
            }
        }
        redirect('admin/quote/update/' . $qid);
    }

    function do_upload1($qid) 
    {
        //$this->load->helper(array('form', 'url'));
        $this->load->library('upload');
        $config['upload_path'] = './uploads/quote/';
        $config['allowed_types'] = '*';
        $this->upload->initialize($config);

        if (!$this->upload->do_upload()) {
            $error = array('error' => $this->upload->display_errors());
        } else {
            $error = array('upload_data' => $this->upload->data());
        }
        return $error;
    }

    function updateattach() 
    {
        //$items = $this->quote_model->getitems($qid);
        $qid = $_POST['quoteid'];
        //var_dump($qid); exit;
        //$err = $this->do_upload1($qid);
        //echo $qid;
		//print_r($_FILES);
        if(is_uploaded_file($_FILES['userfile']['tmp_name']))
        {//echo 'uploaded';
        	$ext = end(explode('.', $_FILES['userfile']['name']));
        	$nfn = md5(uniqid().date('YmdHi')).'.'.$ext;
        	if(move_uploaded_file($_FILES['userfile']['tmp_name'], "uploads/quote/".$nfn))
        	{//echo $nfn;
        		$updatearray = array();
		        $updatearray['quoteattachment'] = $nfn;
		        $this->quote_model->db->where('id', $qid);
		        $this->quote_model->db->update('quote', $updatearray);
        	}
        }
        else
        {

        	$this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
			<div class="msgBox">File could not uploaded.</div></div>');
        	 
        }
		//die;
        redirect('admin/quote/update/' . $qid);
    }

    function additem($qid) 
    {
        //print_r($_POST);die;

    	$itemcode = @$_POST['itemcode'];
    	if ( $itemcode && !$this->db->where('itemcode',$itemcode)->get('item')->row() )
    	{
    		$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a>
    			<div class="msgBox">Itemcode "'.$itemcode.'" does not exist.</div></div>');
    		redirect('admin/quote/update/' . $qid);
    	}
        $_POST['totalprice'] = $_POST['quantity'] * $_POST['ea'];
        $_POST['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $quote = $this->quote_model->get_quotes_by_id($qid);
        if($quote->potype=='Direct')
        if(!$_POST['ea'] || $_POST['ea']=='0.00')
        {
            $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a>
    			<div class="msgBox">Item price cannot be 0</div></div>');
    
            redirect('admin/quote/update/' . $qid);
    
        }
        $this->quote_model->db->insert('quoteitem', $_POST);
        if (!$this->quote_model->finditembycode($_POST['itemcode'])) 
        {
            $itemcode = array(
                'itemcode' => $_POST['itemcode'],
                'itemname' => $_POST['itemname'],
                'unit' => $_POST['unit'],
                'ea' => $_POST['ea'],
                'notes' => $_POST['notes']
            );
            $this->quote_model->db->insert('item', $itemcode);
        }
        redirect('admin/quote/update/' . $qid);
    }

    function deleteitem($itemid, $qid) {
        $this->quote_model->deleteitem($itemid, $qid);

        $this->session->set_flashdata('message', '<div class="alert alert-success"><a data-dismiss="alert" class="close" href="#">X</a>
			<div class="msgBox">Item Deleted Sucessfully.</div></div>');

        redirect('admin/quote/update/' . $qid);
    }

    function bids_export($qid)
    {
    	if ($this->session->userdata('usertype_id') == 3)
    		redirect('admin/purchasingadmin/bids/' . $qid);
    	 
    
    	$bids       = $this->quote_model->getbids($qid);
    	$quoteitems = $this->quote_model->getitems($qid);
    	$awarded    = $this->quote_model->getawardedbid($qid);
    	$quote      = $this->quote_model->get_quotes_by_id($qid);
    
    	if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
    		redirect('admin/dashboard', 'refresh');
    	}
    
    	if (!$bids) {
    		$this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Bids Yet Placed.</div></div>');
    		redirect('admin/quote/update/' . $qid);
    	}
    
    	if (!$awarded)
    		$data['isawarded'] = 'No';
    	else {
    		$data['isawarded'] = 'Yes';
    		$data['awarded']   = $awarded;
    	}
    
    	$minimum  = array();
    	$maximum  = array();
    	$viewbids = array();
    	 
    
    	foreach ($bids as $bid)
    	{
    		$totalprice = 0;
    		foreach ($bid->items as $item) {
    			foreach ($quoteitems as $qi) {
    				if ($qi->itemcode == $item->itemcode) {
    					$item->originaldate = $qi->daterequested;
    				}
    			}
    			$totalprice += $item->totalprice;
    			$key = $item->itemcode;
    			if (!isset($minimum[$key])) {
    				$minimum[$key] = $item->ea;
    				$maximum[$key] = $item->totalprice;
    			} elseif ($minimum[$key] > $item->ea) {
    				$minimum[$key] = $item->ea;
    			} else if ($maximum[$key] < $item->totalprice) {
    				$maximum[$key] = $item->totalprice;
    			}
    		}
    		if (!isset($minimum['totalprice']))
    			$minimum['totalprice'] = $totalprice;
    		elseif ($minimum['totalprice'] > $totalprice)
    		$minimum['totalprice'] = $totalprice;
    
    		$revisionquote = $this->quote_model->getrevisionno($bid->id,$quote->purchasingadmin);
    		if(isset($revisionquote->revisionid))
    			$bid->revisionno = $revisionquote->revisionid;
    		else
    			$bid->revisionno = 1;
    
    	}
    
    	$data['quote']      = $this->quote_model->get_quotes_by_id($qid);
    	$data['quoteitems'] = $quoteitems;
    	$data['project']    = $this->project_model->get_projects_by_id($data['quote']->pid);
    	$data['config']     = (array) $this->settings_model->get_current_settings();
    	$data['bids']       = $bids;
    	$data['minimum']    = $minimum;
    	$data['maximum']    = $maximum;
    	$data['costcodes']  = $this->db->where('project',$quote->pid)->get('costcode')->result();
    	$data['heading']    = $data['quote']->potype == 'Bid' ? "Bids Placed" : "PO Review";
    	 
    
    	//=========================================================================================
    		
    	if(isset($data['isawarded']))
    		$isawarded   = $data['isawarded'];
    
    
    	$quote       = $data['quote'];
    	$quoteitems  = $data['quoteitems'];
    	$project     = $data['project'];
    	$config      = $data['config'];
    	$bids        = $data['bids'];
    	$minimum     = $data['minimum'];
    	$maximum     = $data['maximum'];
    	$costcodes   = $data['costcodes'];
    	$heading     = $data['heading'];
    
    	if(isset($data['awarded']))
    		$awarded     = $data['awarded'];
    
    
    
    		
    	//$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );
    
    
    	$alltotal=0;
    
    
    	foreach($bids as $bid)
    	{
    		 
    		$alltotal = '';
    			
    		$header[] = array('PO #:',$quote->ponum , 'Company:' , $bid->companyname , 'Submitted:' , date('m/d/Y', strtotime($bid->submitdate)) , '','','', '','', '' );
    
    		$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );
    		$header[] = array('Item Code','Item Name' , 'Qty.' , 'Unit' , '60 day Low. Price' , 'Price EA' , 'Price Requested','Total Price','Date Available', 'Cost Code','Notes', 'Compare' );
    
    			
    		if($bid->items)
    		{
    
    			foreach($bid->items as $q)
    			{
    				if($q->itemcode)
    				{
    					$alltotal += $q->quantity * $q->ea;
    					$key  = $q->itemcode;
    					$diff = $q->ea - $minimum[$key];
    					$diff = number_format($diff,2);
    
    					$k_compare = '';
    
    					$k_compare =  ($diff==0?$diff==0?'Lowest Unit Price':$diff:($diff<0?'- $':'+ $'.$diff));
    
    					$low_price = '$ '.$q->ea;
    					if($diff=='0')
    					{
    						$low_price = '$ '. $q->ea;
    					}
    
    					if($q->minprice >= $q->ea)
    					{
    						$low_price = '$ '.$q->ea;
    						//$low_price = '*New Low Price';
    					}
    						
    					$pr_requested = $q->reqprice;
    					if($q->reqprice > 0)
    					{
    						//--------
    					}
    					else
    					{
    						$pr_requested = $pr_requested.' (RFQ)';
    					}
    
    					$k_costcode = '';
    
    					if($isawarded )
    					{
    						$k_costcode = $q->costcode;
    					}
    					else
    					{
    						$k_costcode = '-';
    					}
    
    					$k_total_price = round($q->quantity * $q->ea,2);
    						
    					$header[] = array($q->itemcode, $q->itemname , $q->quantity , $q->unit , '$ '.formatPriceNew($q->minprice) , formatPriceNew($low_price) , $pr_requested,'$ '.formatPriceNew($k_total_price) , $q->daterequested, $k_costcode,$q->notes, $k_compare );
    
    				}
    					
    					
    					
    			}
    			$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );
    			$header[] = array('','' , '' , '' , '' , '' , '','','', '','', '' );
    				
    			$alltotal   = round($alltotal,2);
    			$taxtotal   = $alltotal * $config['taxpercent'] / 100;
    			$taxtotal   = round($taxtotal,2);
    			$grandtotal = $alltotal + $taxtotal;
    			$grandtotal = round($grandtotal,2);
    			$diff       = $alltotal - $minimum['totalprice'];
    
    			$header[] = array('Subtotal','$ '.formatPriceNew(number_format($alltotal,2)) , '' , '' , '' , '' , '','','', '','', '' );
    			$header[] = array('Tax','$ '.formatPriceNew($taxtotal), '' , '' , '' , '' , '','','', '','', '' );
    			$header[] = array('Total','$ '.formatPriceNew($grandtotal) , '' , '' , '' , '' , '','','', '','', '' );
    
    
    
    
    		}
    
    	}
    	createXls('bids_export_'.$qid, $header);
    	die();
    
    	//===============================================================================
    		
    		
    }
    
    function bids($qid) 
    {
        if ($this->session->userdata('usertype_id') == 3)
            redirect('admin/purchasingadmin/bids/' . $qid);
        $bids = $this->quote_model->getbids($qid);
        $quoteitems = $this->quote_model->getitems($qid);
        $awarded = $this->quote_model->getawardedbid($qid);
        $quote = $this->quote_model->get_quotes_by_id($qid);
        
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre> bids ';print_r($awarded);echo '</pre>';//die;
        if (!$bids) {
            $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Bids Yet Placed.</div></div>');
            redirect('admin/quote/update/' . $qid);
        }

        if (!$awarded)
            $data['isawarded'] = 'No';
        else {
            $data['isawarded'] = 'Yes';
            $data['awarded'] = $awarded;
        }
        $minimum = array();
        $maximum = array();
        $viewbids = array();
        foreach ($bids as $bid) {

            $totalprice = 0;
            foreach ($bid->items as $item) {
                foreach ($quoteitems as $qi) {
                    if ($qi->itemcode == $item->itemcode) {
                        $item->originaldate = $qi->daterequested;
                    }
                }
                $totalprice += $item->totalprice;
                $key = $item->itemcode;
                if (!isset($minimum[$key])) {
                    $minimum[$key] = $item->ea;
                    $maximum[$key] = $item->totalprice;
                } elseif ($minimum[$key] > $item->ea) {
                    $minimum[$key] = $item->ea;
                } else if ($maximum[$key] < $item->totalprice) {
                    $maximum[$key] = $item->totalprice;
                }
            }
            if (!isset($minimum['totalprice']))
                $minimum['totalprice'] = $totalprice;
            elseif ($minimum['totalprice'] > $totalprice)
                $minimum['totalprice'] = $totalprice;
                
                $revisionquote = $this->quote_model->getrevisionno($bid->id,$quote->purchasingadmin);
	    	if(isset($revisionquote->revisionid))
	    		$bid->revisionno = $revisionquote->revisionid;
	    	else 
	    		$bid->revisionno = 1;
                
        }
        //print_r($bids);die;
        //echo '<pre>';print_r(array_sum($maximum));echo '</pre>';//die;

        $data['quote'] = $this->quote_model->get_quotes_by_id($qid);
        $data['quoteitems'] = $quoteitems;
        //$this->load->model('admin/project_model');
        $data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $data['bids'] = $bids;
        $data['minimum'] = $minimum;
        $data['maximum'] = $maximum;
        $data['costcodes'] = $this->db->where('project',$quote->pid)->get('costcode')->result();
        $data['heading'] = $data['quote']->potype == 'Bid' ? "Bids Placed" : "PO Review";
        if($data['quote']->potype == 'Bid')
            $this->load->view('admin/bids', $data);
        else
            $this->load->view('admin/directbids', $data);
    }
    
    function confirmdirect()
    {
        $qid = $_POST['quote'];
        if(!$qid)
            die;
        if ($this->session->userdata('usertype_id') == 3)
            redirect('admin/purchasingadmin/bids/' . $qid);
        $bids = $this->quote_model->getbids($qid);
        
        $quote = $this->quote_model->get_quotes_by_id($qid);
        //echo '<pre>';print_r($qutoe);//die;
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {die;
            redirect('admin/dashboard1', 'refresh');
        }
        //echo '<pre> bids ';print_r($awarded);echo '</pre>';//die;
        if (!$bids) {
            $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Bids Yet Placed.</div></div>');
            redirect('admin/quote/update/' . $qid);
        }
        
        
        $awardarray = array();
        $awardarray['quote'] = $qid;
        $awardarray['awardedon'] = date('Y-m-d');
        $awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $this->quote_model->db->insert('award', $awardarray);
        $awardid = $this->quote_model->db->insert_id();
        
        foreach($bids as $bid)
        {
            foreach($bid->items as $item)
            {
                if($item->postatus=='Accepted')
                {
                    $item = (array) $item;
                    $itemarray = array();
                    $itemarray['award'] = $awardid;
                    $itemarray['company'] = $bid->company;
                    $itemarray['itemid'] = $item['itemid'];
                    $itemarray['itemcode'] = $item['itemcode'];
                    $itemarray['itemname'] = $item['itemname'];
                    $itemarray['quantity'] = $item['quantity'];
                    $itemarray['unit'] = $item['unit'];
                    $itemarray['ea'] = $item['ea'];
                    $itemarray['totalprice'] = $item['quantity'] * $item['ea'];
                    $itemarray['daterequested'] = $item['daterequested'];
                    $itemarray['costcode'] = $item['costcode'];
                    $itemarray['notes'] = $item['notes'];
                    $itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                    
                    $this->quote_model->db->insert('awarditem', $itemarray);
                }
                else
                {
                    $this->db->where('id',$item->id);
                    $this->db->delete('biditem');
                }
            }
        }
        
        $this->quote_model->db->where('quote', $bid->quote);
        $this->quote_model->db->update('bid', array('complete' => 'Yes'));
        $this->sendawardemail($bid->quote);
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bid awarded to the company.</div></div>');
        redirect('admin/quote/index/' . $quote->pid);
    }

    function getquoteitem() 
    {
        $quote = $_POST['quote'];
        $itemcode = $_POST['itemcode'];

        $this->quote_model->db->where(array('quote' => $quote, 'itemcode' => $itemcode));
        $item = $this->db->get('quoteitem')->row();
        $ret = '<table class="table table-bordered">';
        $ret.= '<tr><td>Item Code</td><td>' . $item->itemcode . '</td></tr>';
        $ret.= '<tr><td>Item Name</td><td>' . $item->itemname . '</td></tr>';
        $ret.= '<tr><td>Quantity</td><td>' . $item->quantity . '</td></tr>';
        $ret.= '<tr><td>Unit</td><td>' . $item->unit . '</td></tr>';
        $ret.= '<tr><td>Price</td><td>' . $item->ea . '</td></tr>';
        $ret.= '<tr><td>Date Requested</td><td>' . $item->daterequested . '</td></tr>';
        $ret.= '<tr><td>Costcode</td><td>' . $item->costcode . '</td></tr>';
        $ret.= '<tr><td>Notes</td><td>' . $item->notes . '</td></tr>';
        $ret.= '<table>';

        echo $ret;
    }

    function delbiditem($id, $quoteid) {
        $quote = $this->quote_model->getbidbyid($quoteid);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $this->quote_model->db->where('id', $id);
        $this->quote_model->db->delete('biditem');
        redirect('admin/quote/bids/' . $quoteid);
    }

    function editbiditemqty($id, $qty, $total) {
        $this->quote_model->db->where('id', $id);
        $this->quote_model->db->update('biditem', array('quantity' => $qty, 'totalprice' => $total));
        echo 1;
    }

    function editbiditemcostcode($id, $costcode) {
        $this->quote_model->db->where('id', $id);
        $this->quote_model->db->update('biditem', array('costcode' => $costcode));
        echo 1;
    }

    function awardbid() 
    {
        if ($_POST['bid'])
        {
        	$this->awardbidbyid();
            $bid = $this->quote_model->getbidbyid($_POST['bid']);
            $quote = $this->quote_model->get_quotes_by_id($bid->quote);
        }
        elseif ($_POST['itemids'])
        {
            $this->awardbidbyitems();
            $quote = $this->quote_model->get_quotes_by_id($_POST['quote']);
        }
        if($quote)
        {
        	$quote->awardedbid = $this->quote_model->getawardedbid($quote->id);
        	
        	if(@$quote->awardedbid->items)
        	{
		        $totalcount = count($quote->awardedbid->items);
		        $lowcount = 0;
		        foreach ($quote->awardedbid->items as $ai) 
		        {
		        	$itemlowest = $this->itemcode_model->getlowestquoteprice($ai->itemid);
		        
		        	if ($ai->ea <= $itemlowest)
		        		$lowcount++;
		        }
		        
		        if ($lowcount >= ($totalcount * 0.8))
		        	$quote->pricerank = 'great';
		        elseif ($lowcount >= ($totalcount * 0.7))
		        	$quote->pricerank = 'good';
		        elseif ($lowcount >= ($totalcount * 0.5))
		        	$quote->pricerank = 'fair';
		        else
		        	$quote->pricerank = 'poor';
		        $this->db->where('id',$quote->awardedbid->id)->update('award',array('pricerank'=>$quote->pricerank));
		        
        	}
        }
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bid awarded to the selected supplier(s).</div></div>');
        redirect('admin/quote/index/' . $quote->pid);
    }

    function awardbidbyid() 
    {
        $bid = $this->quote_model->getbidbyid($_POST['bid']);
        if ($this->session->userdata('usertype_id') == 2 && $bid->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre>';print_r($bid);die;
        if (!$bid) {
            die;
        }
        $awardarray = array();
        $awardarray['quote'] = $bid->quote;
        $awardarray['shipto'] = $_POST['shipto'];
        $awardarray['awardedon'] = date('Y-m-d');
        $awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $this->quote_model->db->insert('award', $awardarray);
        $awardid = $this->quote_model->db->insert_id();

        foreach ($bid->items as $item) {
            $item = (array) $item;
            $itemarray = array();
            $itemarray['award'] = $awardid;
            $itemarray['company'] = $bid->company;
            $itemarray['itemid'] = $item['itemid'];
            $itemarray['itemcode'] = $item['itemcode'];
            $itemarray['itemname'] = $item['itemname'];
            $itemarray['quantity'] = $item['quantity'];
            $itemarray['unit'] = $item['unit'];
            $itemarray['ea'] = $item['ea'];
            $itemarray['totalprice'] = $item['quantity'] * $item['ea'];
            $itemarray['daterequested'] = $item['daterequested'];
            $itemarray['costcode'] = $item['costcode'];
            $itemarray['notes'] = $item['notes'];
            $itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');

            $awarditemid = $this->quote_model->db->insert('awarditem', $itemarray);
            
             $this->db->where('itemid',$item['itemid']);
            $this->db->where('company',$bid->company);
            $this->db->where('type', 'Supplier');
            $companyitem = $this->db->get('companyitem')->row();
            if($companyitem){
            	$bd['qtyavailable'] = $companyitem->qtyavailable-$item['quantity'];
            	$this->db->where('id',$companyitem->id);
            	$this->db->update('companyitem',$bd);
            }
        }
        $this->quote_model->db->where('quote', $bid->quote);
        $this->quote_model->db->update('bid', array('complete' => 'Yes'));
        $this->sendawardemail($bid->quote);
    }

    function awardbidbyitems() 
    {
        $quote = $this->quote_model->get_quotes_by_id($_POST['quote']);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $itemids = $_POST['itemids'];
        //echo '<pre>';print_r($bid);die;
        if (!$itemids) {
            die;
        }

        $items = $this->quote_model->getbiditemsbyids($itemids);
        //print_r($items);die; 
        if (!$items)
            die;
        $awardarray = array();
        $awardarray['quote'] = $_POST['quote'];
        $awardarray['shipto'] = $_POST['shipto'];
        $awardarray['awardedon'] = date('Y-m-d');
        $awardarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
        $this->quote_model->db->insert('award', $awardarray);
        $awardid = $this->quote_model->db->insert_id();

        foreach ($items as $item) {
            $item = (array) $item;
            $itemarray = array();
            $itemarray['award'] = $awardid;
            $itemarray['company'] = $item['company'];
            $itemarray['itemid'] = $item['itemid'];
            $itemarray['itemcode'] = $item['itemcode'];
            $itemarray['itemname'] = $item['itemname'];
            $itemarray['quantity'] = $item['quantity'];
            $itemarray['unit'] = $item['unit'];
            $itemarray['ea'] = $item['ea'];
            $itemarray['totalprice'] = $item['quantity'] * $item['ea'];
            $itemarray['daterequested'] = $item['daterequested'];
            $itemarray['costcode'] = $item['costcode'];
            $itemarray['notes'] = $item['notes'];
            $itemarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');

            $awarditemid = $this->quote_model->db->insert('awarditem', $itemarray);
            
            $this->db->where('itemid',$item['itemid']);
            $this->db->where('company',$item['company']);
            $this->db->where('type', 'Supplier');
            $companyitem = $this->db->get('companyitem')->row();
            if($companyitem){
            	$bd['qtyavailable'] = $companyitem->qtyavailable-$item['quantity'];
            	$this->db->where('id',$companyitem->id);
            	$this->db->update('companyitem',$bd);
            }
        }
        $this->quote_model->db->where('quote', $_POST['quote']);
        $this->quote_model->db->update('bid', array('complete' => 'Yes'));
        $this->sendawardemail($_POST['quote']);
    }

    function getminprice($companyid) 
    {
        //print_r($_POST);die;
        $itemid = $_POST['itemid'];
        $this->quote_model->db->where(array('itemid' => $itemid, 'company' => $companyid, 'purchasingadmin'=>$this->session->userdata('purchasingadmin')));
        $query = $this->quote_model->db->get('minprice');
        if ($query->result())
            echo $query->row('price');
        else
            echo -1;
        die;
    }

    public function update_invoice_status() {
        $update['status'] = ucfirst($this->input->post('status'));
        $invoice = $this->quote_model->update_invoice_by_number($this->input->post('id'), $update);
        echo '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice Status Changed.</div></div>';
    }

    public function update_invoice_payment_status() 
    {
        //print_r($_POST);die;
        $_POST['paymentstatus'] = 'Paid';
        $_POST['status'] = 'Pending';
        $this->db->where('invoicenum', $_POST['invoicenum']);
        $amount = $_POST['amount'];
        unset($_POST['amount']);
        $_POST['paymentdate'] = date('Y-m-d');
        $this->db->update('received', $_POST);
    
        
        if($_POST['paymentstatus'] == 'Paid')
        {
    		$company = $this->db->select('company.*')
    		            ->from('received')
    		            ->join('awarditem','received.awarditem=awarditem.id')
    		            ->join('company','awarditem.company=company.id')
    		            ->where('invoicenum',$_POST['invoicenum'])
    		            ->get()->row();
    		$quote = $this->db->select('quote.*')
    		            ->from('received')
    		            ->join('awarditem','received.awarditem=awarditem.id')
    		            ->join('award','awarditem.award=award.id')
    		            ->join('quote','award.quote=quote.id')
    		            ->where('invoicenum',$_POST['invoicenum'])
    		            ->get()->row();
    		            
    		$pa = $this->db->where('id',$this->session->userdata('id'))->get('users')->row();
    		
    		$body = "Dear " . $company->title . ",<br><br>
    		". $pa->companyname." sent payment for the Invoice#: ".$_POST['invoicenum'].";
    		The following information sent:
    		<br/>
    		PO# : ".$quote->ponum."
    		<br/>
    		Payment By : ".$pa->companyname."
    		<br/>
    		Payment Type : ".$_POST['paymenttype']."
    		<br/>
    		Payment Amount : ".$amount."
    		<br/>
    		Ref# : ".$_POST['refnum']."
    		<br/>
    		Payment Date: ".date('m/d/Y')."
    		<br><br>";
    		
    		$this->load->library('email');
    		$this->email->from($pa->email, $pa->companyname);
    		$this->email->to($company->title . ',' . $company->primaryemail);
    		$this->email->subject('Payment made for the invoice: '.$_POST['invoicenum']);
    		$this->email->message($body);
    		$this->email->set_mailtype("html");
    		$this->email->send();
        }
        
        echo '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice Payment Status Changed.</div></div>';
    }
    
    function export()
    {
    	$invoices = $this->quote_model->getinvoices();
    	// $invoices = $this->quote_model->getinvoices_test();
    	 
    	//===============================================================================
    
    	$header[] = array('PO Number' , 'Invoice' , 'Received On' , 'Total Cost' , 'Payment Status' , 'Verification' , 'Date Due' );
    	foreach($invoices as $i)
    	{
    		$dddate = '';
    		if($i->quote->duedate)
    		{ $dddate = date("m/d/Y", strtotime($i->quote->duedate)); }
    			
    		$total_price = '';
    			
    		if($i->totalprice > 0)
    		{
    			$total_price = '$ '.$i->totalprice;
    		}
    			
    		$header[] = array($i->quote->ponum,  $i->invoicenum,  $i->receiveddate , formatPriceNew($total_price) , $i->paymentstatus ,$i->quote->status ,$dddate );
    	}
    		
    	createXls('invoices' , $header);
    	die();
    
    }
    

    function invoices() 
    {
        $invoices = $this->quote_model->getinvoices();
        //print_r($invoices);die;
        $count = count($invoices);
        $items = array();
        if ($count >= 1) 
        {
            $settings = $this->settings_model->get_current_settings();
            $available_statuses = array('pending', 'verified', 'error');
            $data['available_statuses'] = $available_statuses;
            foreach ($invoices as $invoice) 
            if($invoice->invoicenum && $invoice->quote->purchasingadmin == $this->session->userdata('purchasingadmin') )
            {
                $invoice->ponum = $invoice->quote->ponum;
                
                $company = $this->db->select('company.*')->from('received')
                           ->join('awarditem','received.awarditem=awarditem.id')
                           ->join('company','awarditem.company=company.id')
                           ->where('received.invoicenum',$invoice->invoicenum)
                           ->get()->row()
                           ;
                $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
                $invoice->bankaccount = $bankaccount;
                
                $invoice->companydetails = $company;
                $invoice->totalprice = $invoice->totalprice + ($invoice->totalprice*$settings->taxpercent/100);
                //$invoice->status = $invoice->quote->status;
                $invoice->actions = '<a href="javascript:void(0)" onclick="showInvoice(\'' . $invoice->invoicenum . '\')"><span class="icon-2x icon-search"></span></a>';
                $options = false;
                foreach ($available_statuses as $status_key => $status_text) 
                {

                    if (strtolower($invoice->status) == $status_text) {
                        $selected = " selected=\"selected\"";
                    } else {
                        $selected = '';
                    }
                    $options[] = "<option value=\"$status_text\" $selected>$status_text</option>";
                }
                $options_payment = array();
                $options_paymenttype = array();
                $options_payment[]="<option value=\"Paid\" ".($invoice->paymentstatus=='Paid'?" selected=\"selected\"":'').">Paid</option>";;
                //$options_payment[]="<option value=\"Requested Payment\" ".($invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Requested Paid</option>";;
                $options_payment[]="<option value=\"Unpaid\" ".($invoice->paymentstatus=='Unpaid'||$invoice->paymentstatus=='Requested Payment'?" selected=\"selected\"":'').">Unpaid</option>";;
                
                $options_paymenttype[]="<option value=\"\">Select Payment Type</option>";
                if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
                $options_paymenttype[]="<option value=\"Credit Card\" ".($invoice->paymenttype=='Credit Card'?" selected=\"selected\"":'').">Credit Card</option>";;
                $options_paymenttype[]="<option value=\"Cash\" ".($invoice->paymenttype=='Cash'?" selected=\"selected\"":'').">Cash</option>";;
                $options_paymenttype[]="<option value=\"Check\" ".($invoice->paymenttype=='Check'?" selected=\"selected\"":'').">Check</option>";;
                
                $txtrefnum = "<input type=\"text\" id=\"refnum_$invoice->invoicenum\" name=\"refnum\" value=\"$invoice->refnum\"/>";
                
                $update_button = "<button onclick=\"update_invoice_status('$invoice->invoicenum')\">update</button>";
                $update_payment_button = "<button onclick=\"update_invoice_payment_status('$invoice->invoicenum')\">update</button>";
                
                $status_html = "<select id=\"invoice_$invoice->invoicenum\" name=\"status_element\">" . implode("", $options) . "</select>" . $update_button;
                
                $payment_status_html = "<select id=\"invoice_payment_$invoice->invoicenum\" name=\"payment_status_element\">" . implode("", $options_payment) . "</select>";
                $payment_status_html .= "<select id=\"invoice_paymenttype_$invoice->invoicenum\" name=\"paymenttype_status_element\" onchange=\"paycc(this.value,'".$invoice->invoicenum."','".$invoice->totalprice."');\">" . implode("", $options_paymenttype) . "</select>";
                $payment_status_html .= $txtrefnum;
                $payment_status_html .= $update_payment_button;
                if($invoice->paymentstatus=='Requested Payment')
                {
                    $payment_status_html .= '<i class="icon-lightbulb">Payment Requested by Supplier</i>';
                }
                
                $invoice->status_selectbox = $status_html;
                $invoice->payment_status_selectbox = $payment_status_html;
                
                $invoice->totalprice = number_format($invoice->totalprice,2);

                $items[] = $invoice;
            }

            $data['items'] = $items;
            $data['jsfile'] = 'invoicejs.php';
        } else {
        	$data['items'] = array();
            $data['message'] = 'No Records';
        }
        //print_r($items);die;
        $data ['addlink'] = '';
        $data ['heading'] = 'Invoices';
        $query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
        $data['companies'] = $this->db->query($query)->result();
        $this->load->view('admin/invoices', $data);
    }

    function invoice() 
    {
        $invoicenum = @$_POST['invoicenum'];
        if (!$invoicenum)
            redirect('quote/invoices');
        $invoice = $this->quote_model->getinvoicebynum($invoicenum);
        $awarded = $this->quote_model->getawardedbid($invoice->quote);
        //print_r($invoice); echo $this->session->userdata('purchasingadmin');die;
        if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('purchasingadmin')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre>';print_r($invoice);die;

        $this->db->where('id', $this->session->userdata('purchasingadmin'));
        $pa = $this->db->get('users')->row();

        $quote = $awarded->quotedetails;
        $project = $this->project_model->get_projects_by_id($quote->pid);
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        
        $company = $this->db->from('received')
                    ->join('awarditem','received.awarditem=awarditem.id')
                    ->join('company','company.id=awarditem.company')
                    ->get()->row();

        $data['quote'] = $quote;
        $data['awarded'] = $awarded;
        $data['config'] = $config;
        $data['project'] = $project;
        $data['invoice'] = $invoice;
        $data['company'] = $company;
        $data['heading'] = "Invoice Details";
        $data['purchasingadmin'] = $pa;
        $this->load->view('admin/invoice', $data);
    }
    
    function trackexport($qid)
    {
    	if ($this->session->userdata('usertype_id') == 3)
    		redirect('admin/purchasinguser/bids/' . $qid);
    	$awarded = $this->quote_model->getawardedbid($qid);
    	if (!$awarded)
    		redirect('admin/quote/bids/' . $qid);
    	if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('id')) {
    		redirect('admin/dashboard', 'refresh');
    	}
    	 
    	$query = "SELECT s.*, c.title companyname FROM ".$this->db->dbprefix('shippingdoc')." s,
					 ".$this->db->dbprefix('company')." c WHERE s.company=c.id AND s.quote='$qid' ORDER BY uploadon DESC";
    
    	$docs = $this->db->query($query)->result();
    	$data['shippingdocs'] = $docs;
    	$messagesql = "SELECT * FROM " . $this->db->dbprefix('message') . " WHERE quote='{$qid}' ORDER BY senton ASC";
    	$msgresult = $this->db->query($messagesql)->result();
    	$messages = array();
    	foreach ($msgresult as $msg) {
    		$messages[$msg->company]['companydetails'] = $this->company_model->get_companys_by_id($msg->company);
    		$messages[$msg->company]['messages'][] = $msg;
    	}
    
    	if($awarded->status == 'complete')
    	{
    		$this->db->where('quote',$qid);
    		$feedbacks = $this->db->get('quotefeedback')->result();
    
    		$data['feedbacks'] = array();
    		foreach($feedbacks as $feedback)
    		{
    			if($feedback->company)
    				$data['feedbacks'][$feedback->company] = $feedback;
    		}
    	}
    		
    	$shipments = $this->db->select('shipment.*, item.itemname')
    	->from('shipment')->join('item','shipment.itemid=item.id')
    	->where('quote',$qid)->get()->result();
    		
    	$data['errorLog'] = $this->quote_model->get_quotes_error_log($awarded->quote);
    	$data['quote'] = $this->quote_model->get_quotes_by_id($awarded->quote);
    	$data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
    	$data['config'] = (array) $this->settings_model->get_current_settings();
    	$data['messages'] = $messages;
    	$data['awarded'] = $awarded;
    	$data['shipments'] = $shipments;
    	$data['heading'] = "TRACK Items";
    
    	$data['adquoteid'] = $qid;
    		
    
    	//$this->load->view('admin/track', $data);
    		
    	//=========================================================================================
    	$quote = $data['quote'];
    
    	$header[] = array('PO #' , $quote->ponum, '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		
    		
    		
    		
    	$header[] = array('Company' , 'Item Code' , 'Item Name' , 'Qty.' , 'Unit' , 'Price EA' , 'Total Price' , 'Date Requested' , 'Cost Code' , 'Notes' , 'Still Due' );
    
    	//-------------------------------------------
    		
    	$config   = $data['config'];
    	$project  = $data['project'];
    	$quote    = $data['quote'];
    	$errorLog = $data['errorLog'];
    	$shippingdocs = $data['shippingdocs'] ;
    		
    	if(isset($data['feedbacks']))
    	{
    		$feedbacks    = $data['feedbacks'];
    	}
    		
    		
    	$combocompanies = array();
    	$messagecompanies = array();
    	$recsum = 0;
    	$qntsum = 0;
    	foreach ($awarded->items as $q) {
    		$recsum = $recsum + $q->received;
    		$qntsum = $qntsum + $q->quantity;
    		if ($q->received < $q->quantity) {
    			if (isset($combocompanies[$q->company])) {
    				$combocompanies[$q->company]['value'][] = $q->id;
    			} else {
    				$combocompanies[$q->company] = array();
    				$combocompanies[$q->company]['value'] = array($q->id);
    				$combocompanies[$q->company]['id'] = $q->company;
    				$combocompanies[$q->company]['label'] = $q->companyname;
    			}
    		}
    			
    		if (isset($messagecompanies[$q->company])) {
    			$messagecompanies[$q->company]['value'][] = $q->id;
    		} else {
    			$messagecompanies[$q->company] = array();
    			$messagecompanies[$q->company]['value'] = array($q->id);
    			$messagecompanies[$q->company]['id'] = $q->company;
    			$messagecompanies[$q->company]['label'] = $q->companyname;
    		}
    	}
    		
    	if ($qntsum) {
    		$per = number_format(($recsum / $qntsum) * 100, 2);
    	}else{
    		$per = 0;
    	}
    	$per .='%';
    		
    		
    	//-------------------------------------------
    		
    	$alltotal = 0;
    	foreach ($awarded->items as $q)
    	{
    		$alltotal+=$q->totalprice;
    
    		$still_due = $q->quantity - $q->received;
    			
    		$header[] = array(@$q->companydetails->title , $q->itemcode , $q->itemname , $q->quantity , $q->unit, '$ '.formatPriceNew($q->ea) , '$ '.formatPriceNew($q->totalprice) , $q->daterequested , $q->costcode , $q->notes , $still_due );
    
    	}
    		
    	$taxtotal = $alltotal * $config['taxpercent'] / 100;
    	$grandtotal = $alltotal + $taxtotal;
    		
    		
    	$header[] = array('Subtotal:' , '$ '.formatPriceNew(round($alltotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('Tax:' ,  '$ '.formatPriceNew(round($taxtotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '', ''  );
    	$header[] = array('Total:' , '$ '.formatPriceNew(round($grandtotal, 2)) , '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		
    	$header[] = array('Received:' , $per.chr(160) , '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		
    		
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		
    		
    		
    	//---------shipments-----------------------------------------------
    		
    	if (@$shipments)
    	{
    		$header[] = array('Shipments:' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    			
    		$canacceptall = false;
    		$shipitemids = array();
    		foreach($shipments as $cs)
    		{
    			if($cs->accepted == 0)
    			{
    				$canacceptall = true;
    			}
    		}
    		foreach($shipments as $cs)
    		{
    			if(isset($shipitemids[$cs->awarditem]))
    			{
    				$canacceptall = false;
    				break;
    			}
    			$shipitemids[$cs->awarditem] = 1;
    		}
    
    		$header[] = array('Item' , 'Quantity', 'Reference #' , '' , '' , '' , '' , '' , '' , '' , '' );
    			
    		foreach($shipments as $s)
    		{
    			$header[] = array($s->itemname , $s->quantity , $s->invoicenum , '' , '' , '' , '' , '' , '' , '' , '' );
    		}
    	}
    		
    	//------------messages---------------------------------------
    		
    		
    	if (@$messages)
    	{
    		foreach ($messages as $c)
    		{
    			if (@$c['messages'])
    			{
    				$message_for = 'Messages for '. $c['companydetails']->title.' regarding PO# '. $quote->ponum;
    
    				$header[] = array($message_for , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    
    				$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    				$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    					
    					
    				$header[] = array('From' , 'To', 'Message' , 'Date/Time' , '' , '' , '' , '' , '' , '' , '' );
    					
    				foreach ($c['messages'] as $msg)
    				{
    					$header[] = array($msg->from , $msg->to, $msg->message, date("m/d/Y h:i A", strtotime($msg->senton)) , '' , '' , '' , '' , '' , '' , '' );
    						
    				}
    
    			}
    		}
    	}
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    
    		
    	//------------shipmentdoc---------------------------------------
    		
    	if($shippingdocs)
    	{
    		$header[] = array('Shipping Documents' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Company' , 'Date', 'Reference#' , '' , '' , '' , '' , '' , '' , '' , '' );
    
    		foreach($shippingdocs as $sd)
    		{
    			$header[] = array($sd->companyname, date("m/d/Y", strtotime($sd->uploadon)), $sd->invoicenum , '' , '' , '' , '' , '' , '' , '' , '' );
    		}
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	}
    		
    		
    	//-------------------invoices-----------------------------------------------------------------
    		
    	if ($awarded->invoices)
    	{
    		$header[] = array('Existing Invoices ' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Invoice #' , 'Total Cost', 'Tax' , 'Payment' ,  'Status' , '' , '' , '' , '' , '','' );
    
    		foreach ($awarded->invoices as $invoice)
    		{
    			$header[] = array($invoice->invoicenum , '$ '.formatPriceNew($invoice->totalprice), number_format($invoice->totalprice * $config['taxpercent'] / 100, 2) , $invoice->paymentstatus ,   $invoice->status , '' , '' , '' , '' , '','' );
    		}
    	}
    		
    		
    		
    	//--------------Time Line---------------------------------------------------------------
    		
    		
    		
    	if ($awarded->invoices)
    	{
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Time Line ' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('PO #' , 'Date', '' , '' ,  '' , '' , '' , '' , '' , '','' );
    		$header[]  = array($quote->ponum , date('m/d/Y', strtotime($awarded->awardedon)) ,'' , '',  '' , '' , '' , '' , '' , '','' );
    		//-----------------------------------------
    			
    		foreach ($awarded->invoices as $invoice)
    		{
    				
    			$header[] = array($invoice->invoicenum , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    
    			foreach ($invoice->items as $item)
    			{
    				$header[] = array($item->receiveddate, $item->itemname, $item->quantity.' Received' , '' , '' , '' , '' , '' , '' , '' , '' );
    			}
    				
    		}
    	}
    		
    		
    	//--------------------Feedbacks------------------------------------------------------------
    		
    	if ($awarded->status == 'complete')
    	{
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Feedbacks' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('Company' , 'Rating', 'Feedback' , '' ,  '' , '' , '' , '' , '' , '','' );
    			
    		foreach($messagecompanies as $combocompany)
    		{
    			if(isset($feedbacks[$combocompany['id']]))
    				$rating = '<div class="fixedrating" data-average="'.$feedbacks[$combocompany['id']]->rating.'" data-id="1"></div>';
    			else
    				$rating = '';
    			$feedback = isset($feedbacks[$combocompany['id']]) ? $feedbacks[$combocompany['id']]->feedback : '';
    			$header[] = array($combocompany['label'] , $rating, $feedback , '' ,  '' , '' , '' , '' , '' , '','' );
    		}
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    
    	}
    		
    
    	//--------------------errorLog------------------------------------------------------------
    		
    	if(!empty($errorLog))
    	{
    			
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('ERROR LOG' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('company' , 'Error', 'Item' , 'Qty' ,  'Invoice#' , 'Date' , '' , '' , '' , '','' );
    			
    		foreach($errorLog as $error)
    		{
    			$inv_date = (isset($error->date) && $error->date!="" && $error->date!="0000-00-00" && $error->date!="1969-12-31")?date("m/d/Y",  strtotime($error->date)):"";
    			$header[] = array($error->title , $error->error, $error->itemcode , $error->quantity ,  $error->invoicenum , $inv_date , '' , '' , '' , '','' );
    		}
    
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    		$header[] = array('' , '', '' , '' , '' , '' , '' , '' , '' , '' , '' );
    	}
    		
    
    	//--------------------errorLog------------------------------------------------------------
    
    	createXls('track_items', $header);
    	die();
    		
    	//===============================================================================
    
    
    }
    

    function track($qid) 
    {
        if ($this->session->userdata('usertype_id') == 3)
            redirect('admin/purchasinguser/bids/' . $qid);
        $awarded = $this->quote_model->getawardedbid($qid);
        if (!$awarded)
            redirect('admin/quote/bids/' . $qid);
        if ($this->session->userdata('usertype_id') == 2 && $awarded->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        //echo '<pre>';print_r($awarded);die;
        /*
        $this->db->select('shippingdoc.*, company.title');
        $this->db->from('shippingdoc')->join('company','shippingdoc.company=company.id');
		$this->db->where('quote',$qid);
		$this->db->order_by('uploadon','DESC');
		*/
		$query = "SELECT s.*, c.title companyname FROM ".$this->db->dbprefix('shippingdoc')." s, 
				 ".$this->db->dbprefix('company')." c WHERE s.company=c.id AND s.quote='$qid' ORDER BY uploadon DESC";
	    //echo $query;
		$docs = $this->db->query($query)->result();
		$data['shippingdocs'] = $docs;
		//echo '<pre>';print_r($docs);die;
		
        $messagesql = "SELECT * FROM " . $this->db->dbprefix('message') . " WHERE quote='{$qid}' ORDER BY senton ASC";
        $msgresult = $this->db->query($messagesql)->result();
        $messages = array();
        foreach ($msgresult as $msg) {
            $messages[$msg->company]['companydetails'] = $this->company_model->get_companys_by_id($msg->company);
            $messages[$msg->company]['messages'][] = $msg;
        }
    
		if($awarded->status == 'complete')
		{
		    $this->db->where('quote',$qid);
		    $feedbacks = $this->db->get('quotefeedback')->result();
		    
		    $data['feedbacks'] = array();
		    foreach($feedbacks as $feedback)
		    {
		        if($feedback->company)
		            $data['feedbacks'][$feedback->company] = $feedback;
		    }
		}
		
		$shipments = $this->db->select('shipment.*, item.itemname')
		             ->from('shipment')->join('item','shipment.itemid=item.id')
		             ->where('quote',$qid)->get()->result();
        
        $data['errorLog'] = $this->quote_model->get_quotes_error_log($awarded->quote); 
        $data['quote'] = $this->quote_model->get_quotes_by_id($awarded->quote);
        $data['project'] = $this->project_model->get_projects_by_id($data['quote']->pid);
        $data['config'] = (array) $this->settings_model->get_current_settings();
        $data['messages'] = $messages;
        $data['awarded'] = $awarded;
        $data['shipments'] = $shipments;
        $data['heading'] = "TRACK Items";
        $data['adquoteid'] = $qid;
        $this->load->view('admin/track', $data);
    }
    
    function acceptshipment()
    {
        $id = $_POST['id'];
        $this->db->where('id',$id)->update('shipment',array('accepted'=>1));
    }
    
    function acceptall()
    {
        $quoteid = $_POST['quote'];
        $this->db->where('quote',$quoteid)->update('shipment',array('accepted'=>1));
    }
    
    function savefeedback()
    {
        $_POST['ratedate'] = date('Y-m-d');
        $_POST['purchasingadmin'] = $this->session->userdata('purchasingadmin');
		//print_r($_POST);die;
        $this->db->insert('quotefeedback',$_POST);
        redirect('admin/quote/track/'.$_POST['quote']);
    }
    

    function changestatus($id) {
        $this->db->where('id', $id);
        $this->db->update('quote', $_POST);
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Quote Status Changed.</div></div>');
        redirect('admin/quote/track/' . $id);
    }

    function invoicestatus($id) {
        $this->db->where('invoicenum', $_POST['invoicenum']);
        $this->db->update('received', $_POST);
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice Status Changed.</div></div>');
        redirect('admin/quote/track/' . $id);
    }
    
    function invoicepaystatus($id) 
    {
        if($_POST['paymentstatus'] == 'Paid')
        {
            $_POST['status'] = 'Pending';
            $_POST['paymentdate'] = date('Y-m-d');
        }
        $this->db->where('invoicenum', $_POST['invoicenum']);
        $this->db->update('received', $_POST);
        
        $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice Status Changed.</div></div>');
        redirect('admin/quote/track/' . $id);
    }
    
    
    function payinvoicebycc()
    {
        $company = $this->db->select('company.*')->from('received')
                   ->join('awarditem','received.awarditem=awarditem.id')
                   ->join('company','awarditem.company=company.id')
                   ->where('received.invoicenum',$_POST['invoicenum'])
                   ->get()->row()
                   ;
        $bankaccount = $this->db->where('company',$company->id)->get('bankaccount')->row();
        if(!$bankaccount || !@$bankaccount->routingnumber || !@$bankaccount->accountnumber)
		{
		    $this->session->set_flashdata('message', '<div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Bank account missing for credit card payment.</div></div>');
		    redirect('admin/quote/invoices');
		}
        //print_r($company);die;
		ini_set('max_execution_time', 300);
		$config = (array)$this->settings_model->get_current_settings();
		$config = array_merge($config, $this->config->config);
		
		require_once($config['base_dir'].'application/libraries/payment/Stripe.php');
		Stripe::setApiKey($config['STRIPE_API_KEY']);
		//$myCard = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2015);
		$myCard = array('number' => $_POST['card'], 'exp_month' => $_POST['month'], 'exp_year' => $_POST['year']);
		$charge = Stripe_Charge::create(array('card' => $myCard, 'amount' => $_POST['amount'] * 100, 'currency' => 'usd' ));
		//echo $charge;
		$chargeobj = json_decode($charge);
		if(@$chargeobj->paid)
		{
			if($bankaccount && @$bankaccount->routingnumber && @$bankaccount->accountnumber)
			{
	          $recbankInfo = array(
	          			'country' =>'US', 
	          			'routing_number' => $bankaccount->routingnumber, 
	          			'account_number' => $bankaccount->accountnumber
	          );

              $recObj = Stripe_Recipient::create(array(
              "name" => $company->title, 
              "type" => "individual",
              "email" => $company->primaryemail,
              "bank_account" => $recbankInfo)
              );
              
              $obj = json_decode($recObj);
              $_POST['amount'] = round($_POST['amount'],2);
              $transferObj = Stripe_Transfer::create(array(
                  "amount" => $_POST['amount'] * 100, 
                  "currency" => "usd", 
                  "recipient" => $obj->id, 
                  "description" => "Transfer for ".$company->primaryemail )
              );
              $tobj = json_decode($transferObj);
              $update = array(
                          'paymentstatus'=>'Paid',
                          'status'=>'Pending',
                          'paymenttype'=>'Credit Card',
                          'refnum'=>$chargeobj->balance_transaction
                          );
              //echo $_POST['invoicenum'];
              //print_r($update);die;
              $query = "UPDATE ".$this->db->dbprefix('received')." SET 
              			paymentstatus='Paid',
              			status='Pending',
              			paymentdate='".date('Y-m-d')."',
              			paymenttype='Credit Card',
              			refnum='".$chargeobj->balance_transaction."'
              			WHERE invoicenum='".$_POST['invoicenum']."'";
              //echo $query;die;
              $this->db->query($query);
    		  $quote = $this->db->select('quote.*')
    		            ->from('received')
    		            ->join('awarditem','received.awarditem=awarditem.id')
    		            ->join('award','awarditem.award=award.id')
    		            ->join('quote','award.quote=quote.id')
    		            ->where('invoicenum',$_POST['invoicenum'])
    		            ->get()->row();
    		            
    		  $pa = $this->db->where('id',$this->session->userdata('id'))->get('users')->row();
              
              
              $transferbody = "Dear {$company->title},<br/><br/>
$ {$_POST['amount']} has been transfered to your bank account for invoice#{$_POST['invoicenum']}, 
with the transfer# {$tobj->id}.
<br>Payment by: ".$pa->companyname."
<br>PO#: ".$quote->ponum."
";
              $settings = (array)$this->settings_model->get_current_settings ();
    	      $this->load->library('email');
              $this->email->from($settings['adminemail'], "Administrator");
              $this->email->to($company->primaryemail); 
              $this->email->subject('Bank transfer notification from ezpzp');
              $this->email->message($transferbody);	
              $this->email->set_mailtype("html");
              $this->email->send();
              
              $this->session->set_flashdata('message', '<div class="alert alert-sucess"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">Invoice paid successfully.</div></div>');
        	}
		}
		redirect('admin/quote/invoices');
    }
 
    function savetrack($quoteid,$ajax=0) 
    {
        $awarded = $this->quote_model->getawardedbid($quoteid);
        //echo '<pre>';print_r($awarded);die;
        $quote = $awarded->quotedetails;
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $project = $this->project_model->get_projects_by_id($quote->pid);
        $shipto = $awarded->shipto;
        $received = array();
        $invoices = array();
        $credits = array();
        foreach ($awarded->items as $item) 
        {
            $received[$item->id] = (array) $item;
            $updatearray = array();
            $key = $item->id;
            $updatearray['received'] = $_POST['received' . $key] + $received[$item->id]['received'];
            if ($_POST['received' . $key] > $item->quantity - $item->received) {
                $this->session->set_flashdata('message', '<div class="alert alert-failure"><a data-dismiss="alert" class="close" href="#">X</a>
<div class="msgBox">Received quantity Cannot be more than due.</div></div>');

                redirect('admin/quote/track/' . $quote->id);
            }
            $received[$item->id]['received'] = $_POST['received' . $key];
            //print_r($updatearray);die;
            $this->quote_model->db->where('id', $key);
            $this->quote_model->db->update('awarditem', $updatearray);
            if ($received[$item->id]['received'] > 0) 
            {
                if ($this->input->post('makedefaultinvoicenum') == '1') {
                    $temp['defaultinvoicenum'] = $_POST['invoicenum' . $key];
                    $this->session->set_userdata($temp);
                }
                if ($this->input->post('makedefaultreceiveddate') == '1') {
                    $temp['defaultreceiveddate'] = $_POST['receiveddate' . $key];
                    $this->session->set_userdata($temp);
                }
                $insertarray = array('awarditem' => $item->id, 'quantity' => $received[$item->id]['received'], 'invoicenum' => trim($_POST['invoicenum' . $key]), 'receiveddate' => $_POST['receiveddate' . $key]);
                $insertarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
                $this->quote_model->db->insert('received', $insertarray);

				
                $insertarray['id'] = $item->id;
                $insertarray['itemname'] = $item->itemname;
                $insertarray['companyname'] = $item->companyname;
                $insertarray['daterequested'] = $item->daterequested;
                $insertarray['unit'] = $item->unit;
                $insertarray['ea'] = $item->ea;
                
                if (!isset($invoices[$_POST['invoicenum' . $key]])) {
                    $invoices[$_POST['invoicenum' . $key]] = array();
                    $invoices[$_POST['invoicenum' . $key]]['invoicenum'] = $_POST['invoicenum' . $key];
                    $invoices[$_POST['invoicenum' . $key]]['items'] = array($insertarray);
                    $invoices[$_POST['invoicenum' . $key]]['invoicenotes'] = $item->companydetails->invoicenote;
                } else {
                    $invoices[$_POST['invoicenum' . $key]]['items'][] = $insertarray;
                }
                if(isset($credits[$item->company]))
                {
                    $credits[$item->company]['amount'] += $insertarray['quantity'] * $insertarray['ea'];
                    $credits[$item->company]['items'][]=$insertarray;
                }
                else
                {
                    $credits[$item->company] = array();
                    $credits[$item->company]['amount'] = $insertarray['quantity'] * $insertarray['ea'];
                    $credits[$item->company]['items'] = array($insertarray);
                }
            }
        }
        //print_r($invoices);die;
        //echo '<pre>';print_r($credits);die;
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        $this->db->where('id',$this->session->userdata('purchasingadmin'));
        $cpa = $this->db->get('users')->row();
        
       // $company = $this->company_model->get_companys_by_id($cid);
        foreach ($invoices as $invoice) 
        {
            $pdfhtml = '
				<strong>Invoice #: ' . $invoice['invoicenum'] . '</strong><br/>
				<table width="100%" cellspacing="2" cellpadding="2">
				  <tr>
				    <td width="33%" align="left" valign="top">
				    <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></td>
				        </tr>
				      <tr>
				        <td width="33%" valign="top">Project Title</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $project->title . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Address</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $project->address . '</td>
				      </tr>
				    </table>
				    </td>
				    <td width="10" align="left" valign="top">&nbsp;</td>
				    <td width="65%" align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Purchase Order Information</strong></font></td>
			          </tr>
				      <tr>
				        <td width="33%" valign="top">PO#</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $quote->ponum . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Subject</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->subject . '</td>
				      </tr>
				      <tr>
				        <td valign="top">PO# Date</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->podate . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Invoice Date</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . date('m/d/Y') . '</td>
				      </tr>
				    </table></td>
				  </tr>
				  <tr>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				  </tr>
				  <tr>
				    <td align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>From</strong></font></td>
				      </tr>
				      <tr>
				        <td width="33%" valign="top">Contact</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">'.$cpa->fullname.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Company</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->companyname.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Address</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->address.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Phone</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->phone.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Fax</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->fax.'</td>
				      </tr>
				    </table></td>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <td bgcolor="#000033"><font color="#FFFFFF"><strong>Ship to</strong></font></td>
				      </tr>
				      <tr>
				        <td>' . nl2br($shipto) . '</td>
				      </tr>
				    </table></td>
				  </tr>
				  
			</table>
				
				<table width="100%" cellspacing="0" cellpadding="4">
				  <tr>
	              <td>Items:</td>
	              </tr>
	             </table>
	             
	             <br/>
	             
				<table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				  <thead>
				  <tr>
				    <th bgcolor="#000033"><font color="#FFFFFF">Item No</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Company</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Date Requested</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Date Received</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Quantity</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit Price</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Total Price</font></th>
				  </tr>
				  </thead>
				  ';
            $totalprice = 0;
            $i = 0;
            foreach ($invoice['items'] as $invoiceitem) {
                $totalprice += $invoiceitem['ea'] * $invoiceitem['quantity'];
                $pdfhtml.='<tr nobr="true">
				    <td style="border: 1px solid #000000;">' . ++$i . '</td>
				    <td style="border: 1px solid #000000;">' . htmlentities($invoiceitem['itemname']) . '</td>
				    <td style="border: 1px solid #000000;">' . htmlentities($invoiceitem['companyname']) . '</td>
				    <td style="border: 1px solid #000000;">' . $invoiceitem['daterequested'] . '</td>
				    <td style="border: 1px solid #000000;">' . $_POST['receiveddate' . $invoiceitem['id']] . '</td>
				    <td style="border: 1px solid #000000;">' . $received[$invoiceitem['id']]['received'] . '</td>
				    <td style="border: 1px solid #000000;">' . $invoiceitem['unit'] . '</td>
				    <td align="right" style="border: 1px solid #000000;">$ ' . $invoiceitem['ea'] . '</td>
				    <td align="right" style="border: 1px solid #000000;">$ ' . $invoiceitem['ea'] * $received[$invoiceitem['id']]['received'] . '</td>
				  </tr>
				  ';
            }
            $taxtotal = $totalprice * $config['taxpercent'] / 100;
            $grandtotal = $totalprice + $taxtotal;
            
            $pdfhtml.='<tr>
            <td colspan="5" rowspan="3">
            <div style="width:70%">
            <br/>
            <h4 class="semi-bold">Terms and Conditions</h4>
            <p>'.$invoice['invoicenotes'].'</p>
            <h5 class="text-right semi-bold">Thank you for your business</h5>
            </div>
            </td>
            <td align="right">Subtotal</td>
            <td align="right">$ ' . number_format($totalprice, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Tax</td>
            <td align="right">$ ' . number_format($taxtotal, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Total</td>
            <td align="right">$ ' . number_format($grandtotal, 2) . '</td>
            </tr></table>
            ';
         
            
            require_once($config['base_dir'] . 'application/libraries/tcpdf/config/lang/eng.php');
            require_once($config['base_dir'] . 'application/libraries/tcpdf/tcpdf.php');

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('');
            $pdf->SetTitle('');
            $pdf->SetSubject('');

            $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));

            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(true);

            $pdf->SetHeaderData('', '', $cpa->companyname . '', 'Invoice');

            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->AddPage('L', 'LETTER');

            $pdf->SetFont('helvetica', '', 8, '', true);
            $pdf->writeHTML($pdfhtml, true, 0, true, true);

            $pdf->lastPage();
            $pdfname = $config['base_dir'] . 'uploads/pdf/' . $quote->ponum . '_invoice_' . $invoice['invoicenum'] . '_' . date('YmdHis') . '.pdf';
            $pdf->Output($pdfname, 'f');

            $mailbody = "Please find the attachment invoice for PO#: " . $quote->ponum . ".<br/><br/>";
            $mailbody .= "You have been awarded by " . $cpa->companyname . ".  for PO#: " . $quote->ponum . ".<br/><br/>";
            
            $settings = (array) $this->settings_model->get_current_settings();
            
            $toemail = $settings['adminemail'];
            $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
	        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
            $purchaseusers = $this->db->query($sql)->result();
            foreach ($purchaseusers as $pu) {
                $toemail = $toemail . ',' . $pu->email;
            }
            
            $this->load->library('email');
            $this->email->clear(true);
            $this->email->from($settings['adminemail'], "Administrator");
            $this->email->to($toemail);

            $this->email->subject('Invoice for PO#:' . $quote->ponum);
            $this->email->message($mailbody);
            $this->email->set_mailtype("html");
            $this->email->attach($pdfname);
            $this->email->send();
        }
        
        if($credits)
        {
            $this->notifycredits($credits, $quote->ponum);
        }
        //echo '<pre>';print_r($received);die;
        //$this->sendbacktrack($quoteid);/// SENDING BACKTRACK IS NOW MANUAL
        if(!$ajax)
            redirect('admin/quote/track/' . $quoteid);
    }
    
    function notifycredits($credits, $ponum)
    {
        
        $this->db->where('id',$this->session->userdata('purchasingadmin'));
        $cpa = $this->db->get('users')->row();
        
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        foreach($credits as $cid=>$credit)
        {
            $amount = $credit['amount'];
            $items = $credit['items'];
            $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
            $this->db->where('company',$cid);
            $tier = $this->db->get('purchasingtier')->row();
            if($tier && $tier->creditlimit - $amount > 0 && $tier->creditfrom <= date('Y-m-d') && $tier->creditto >= date('Y-m-d') )
            {
                $this->db->where('purchasingadmin',$this->session->userdata('purchasingadmin'));
                $this->db->where('company',$cid);
                $this->db->update('purchasingtier',array('creditlimit'=>$tier->creditlimit-$amount));
                $company = $this->company_model->get_companys_by_id($cid);
                
                $mailbody = "Credit amount of ".$cpa->fullname.', '.$cpa->companyname." has been deducted by $".$amount.".<br>";
                $mailbody .= "Remaining available credit for ".$cpa->companyname." is $".$tier->creditlimit - $amount.".<br><br>";
                $mailbody .= "Find the details below:<br/><br/>";
                $mailbody .= "<table>";
                $mailbody .= "<tr><td>Name</td><td>Price</td><td>Quantity</td><td>Total</td></tr>";
                $totalamount = 0;
                foreach($items as $item)
                {
                    $amount = $item['quantity'] * $item['ea'];
                    $totalamount += $amount;
                    $mailbody .= "<tr>
                    				<td>{$item['itemname']}</td>
                    				<td>{$item['ea']}</td>
                    				<td>{$item['quantity']}</td>
                    				<td>".$amount."</td>
                    			</tr>";
                }
                $tax = $totalamount * $config['taxpercent'] / 100;
                $totalamountwithtax = $totalamount + $tax;
                $totalamountwithtax = number_format($totalamountwithtax,2);
                $mailbody .= "<tr>
                				<td>Total</td>
                				<td></td>
                				<td></td>
                				<td>".$totalamount."</td>
                			</tr>";
                $mailbody .= "<tr>
                				<td>Tax</td>
                				<td></td>
                				<td></td>
                				<td>".number_format($tax)."</td>
                			</tr>";
                $mailbody .= "<tr>
                				<td>Total</td>
                				<td></td>
                				<td></td>
                				<td>".$totalamountwithtax."</td>
                			</tr>";
                    
                $mailbody .= "</table>";
                
                $this->load->library('email');
                $this->email->clear(true);
                $this->email->from($config['adminemail'], "Administrator");
                $this->email->to($company->primaryemail.','.$cpa->email);
    
                $this->email->subject('Credit Alert');
                $this->email->message($mailbody);
                $this->email->set_mailtype("html");
                $this->email->send();
            }
        }
    }

    function sendbacktrack($quoteid) 
    {
        $quote = $this->quote_model->get_quotes_by_id($quoteid);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $awarded = $this->quote_model->getawardedbid($quoteid);
        $backtracks = array();
        foreach ($awarded->items as $item) {
            if ($item->quantity > $item->received) {
                if (!isset($backtracks[$item->company])) {
                    $backtracks[$item->company] = array();
                    $backtracks[$item->company]['company'] = $item->company;
                    $backtracks[$item->company]['items'] = array();
                }
                $backtracks[$item->company]['items'][] = $item;
            }
        }
        //print_r($backtracks);die;
        foreach ($backtracks as $backtrack) {
            $c = $this->company_model->get_companys_by_id($company); //$backtrack['company']);


            $this->quote_model->db->where(array(
                'quote' => $awarded->quote,
                'company' => $c->id
            ));
            $this->quote_model->db->delete('backtrack');
            $key = md5($c->id . '--' . date('YmdHisu'));
            $insertarray = array(
                'quote' => $awarded->quote,
                'company' => $c->id,
                'senton' => date('Y-m-d'),
                'invitation' => $key
            );
            $insertarray['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->quote_model->db->insert('backtrack', $insertarray);

            $link = base_url() . 'home/backtrack/' . $key;
            $body = "Dear " . $c->title . ",<br><br>
		    		 
		  	Please update us on the estimated delivery dates for the following still due items off of PO# " . $quote->ponum . ":  <br><br>		 
		    <a href='$link' target='blank'>$link</a>
		    And let us know the delivery date of remaining items.
		    ";
            //$this->load->model('admin/settings_model');
            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');

            $this->email->from($settings['adminemail'], "Administrator");

            $toemail = $settings['adminemail'] . ',' . $c->primaryemail;
            $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
	        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
            $purchaseusers = $this->db->query($sql)->result();
            foreach ($purchaseusers as $pu) {
                $toemail = $toemail . ',' . $pu->email;
            }
            $this->email->to($toemail);
            //$this->email->to($settings['adminemail'] . ',' . $c->email); 

            $this->email->subject('Backorder update for PO# ' . $quote->ponum);
            $this->email->message($body);
            $this->email->set_mailtype("html");
            $this->email->send();

            $notification = array(
                'quote' => $quote->id,
                'company' => $c->id,
                'ponum' => $quote->ponum,
                'category' => 'Backorder',
                'senton' => date('Y-m-d H:i'),
                'isread' => '0'
            );
            $notification['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->db->insert('notification', $notification);
        }
    }

    function sendawardemail($quoteid) 
    {
        $awarded = $this->quote_model->getawardedbid($quoteid);
        $quote = $awarded->quotedetails;
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $project = $this->project_model->get_projects_by_id($quote->pid);
        
        // notification to purchasing user
        $body = "Dear Admin,<br><br>
		    		 
		  	This email is to notify PO# {$quote->ponum} that is assigned to you is awarded.
		    ";
        $settings = (array) $this->settings_model->get_current_settings();
        $this->load->library('email');

        $this->email->from($settings['adminemail'], "Administrator");

        $toemail = array();
        $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
        $purchaseusers = $this->db->query($sql)->result();
        foreach ($purchaseusers as $pu) {
            $toemail[] = $pu->email;
        }
        $this->email->to(implode(',' , $toemail));
        //$this->email->to($settings['adminemail'] . ',' . $c->email); 

        $this->email->subject('Award PO notification for PO# ' . $quote->ponum);
        $this->email->message($body);
        $this->email->set_mailtype("html");
        $this->email->send();
        
        //print_r($awarded);die;
        $companies = array();
        foreach ($awarded->items as $item) {
            if (!isset($companies[$item->companydetails->id])) {
                $companies[$item->companydetails->id] = array();
                $companies[$item->companydetails->id]['id'] = $item->companydetails->id;
                $companies[$item->companydetails->id]['title'] = $item->companydetails->title;
                $companies[$item->companydetails->id]['primaryemail'] = $item->companydetails->primaryemail;
                $companies[$item->companydetails->id]['contact'] = $item->companydetails->contact;
                $companies[$item->companydetails->id]['invoicenote'] = $item->companydetails->invoicenote;
                $companies[$item->companydetails->id]['items'] = array($item);
            } else {
                $companies[$item->companydetails->id]['items'][] = $item;
            }
        }
        //print_r($companies);die;
        $config = (array) $this->settings_model->get_current_settings();
        $config = array_merge($config, $this->config->config);
        require_once($config['base_dir'] . 'application/libraries/tcpdf/config/lang/eng.php');
        require_once($config['base_dir'] . 'application/libraries/tcpdf/tcpdf.php');
        $this->db->where('id',$this->session->userdata('purchasingadmin'));
        $cpa = $this->db->get('users')->row();
        foreach ($companies as $company) {
            $pdfhtml = '
				<table width="100%" cellspacing="2" cellpadding="2">
				  <tr>
				    <td width="33%" align="left" valign="top">
				    <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></td>
				        </tr>
				      <tr>
				        <td width="33%" valign="top">Project Title</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $project->title . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Address</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $project->address . '</td>
				      </tr>
				    </table>
				    </td>
				    <td width="10" align="left" valign="top">&nbsp;</td>
				    <td width="65%" align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Purchase Order Information</strong></font></td>
			          </tr>
				      <tr>
				        <td width="33%" valign="top">PO#</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">' . $quote->ponum . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Subject</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->subject . '</td>
				      </tr>
				      <tr>
				        <td valign="top">PO# Date</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $quote->podate . '</td>
				      </tr>
				    </table></td>
				  </tr>
				  <tr>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				  </tr>
				  <tr>
				    <td align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>From</strong></font></td>
				      </tr>
				      <tr>
				        <td width="33%" valign="top">Contact</td>
				        <td width="7%" valign="top">&nbsp;</td>
				        <td width="60%" valign="top">'.$cpa->fullname.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Company</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->companyname.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Address</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->address.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Phone</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->phone.'</td>
				      </tr>
				      <tr>
				        <td valign="top">Fax</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">'.$cpa->fax.'</td>
				      </tr>
				    </table></td>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <td colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Supplier</strong></font></td>
				      </tr>
				      <tr>
				        <td width="33%" valign="top">Contact</td>
				        <td width="2%" valign="top">&nbsp;</td>
				        <td width="65%" valign="top">' . $company['contact'] . '</td>
				      </tr>
				      <tr>
				        <td valign="top">Company</td>
				        <td valign="top">&nbsp;</td>
				        <td valign="top">' . $company['title'] . '</td>
				      </tr>
				    </table></td>
				  </tr>
				  <tr>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				  </tr>
				  <tr>
				    <td align="left" valign="top">
	                <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				      <tr>
				        <td bgcolor="#000033"><font color="#FFFFFF"><strong>Ship to</strong></font></td>
				      </tr>
				      <tr>
				        <td>' . $awarded->shipto . '</td>
				      </tr>
				    </table></td>
				    <td align="left" valign="top">&nbsp;</td>
				    <td align="left" valign="top">&nbsp;</td>
				  </tr>
				  
			</table>
				
				<table width="100%" cellspacing="0" cellpadding="4">
				  <tr>
	              <td>PO Details:</td>
	              </tr>
	             </table>
	             
	             <br/>
	             
				<table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
				  <thead>
				  <tr>
				    <th bgcolor="#000033"><font color="#FFFFFF">Item No</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Date Requested</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Quantity</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Unit Price</font></th>
				    <th bgcolor="#000033"><font color="#FFFFFF">Total Price</font></th>
				  </tr>
				  </thead>
				  ';
            $i = 0;
            $totalprice = 0;
            foreach ($company['items'] as $item) {
                $pdfhtml.='<tr nobr="true">
					    <td style="border: 1px solid #000000;">' . ++$i . '</td>
					    <td style="border: 1px solid #000000;">' . htmlentities($item->itemname) . '</td>
					    <td style="border: 1px solid #000000;">' . ($item->willcall?'For Pickup/Will Call':$item->daterequested) . '</td>
					    <td style="border: 1px solid #000000;">' . $item->quantity . '</td>
					    <td style="border: 1px solid #000000;">' . $item->unit . '</td>
					    <td align="right" style="border: 1px solid #000000;">$ ' . $item->ea . '</td>
					    <td align="right" style="border: 1px solid #000000;">$ ' . $item->totalprice . '</td>
					  </tr>
					  ';
                $totalprice += $item->totalprice;
            }
            $config = (array) $this->settings_model->get_current_settings();
            $config = array_merge($config, $this->config->config);
            $taxtotal = $totalprice * $config['taxpercent'] / 100;
            $grandtotal = $totalprice + $taxtotal;
            
            $pdfhtml.='<tr>
            <td colspan="5" rowspan="3">
            <div style="width:70%">
            <br/>
            <h4 class="semi-bold">Terms and Conditions</h4>
             <p>'.$companies[$item->companydetails->id]['invoicenote'].'</p>
            <h5 class="text-right semi-bold">Thank you for your business</h5>
            </div>
            </td>
            <td align="right">Subtotal</td>
            <td align="right">$ ' . number_format($totalprice, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Tax</td>
            <td align="right">$ ' . number_format($taxtotal, 2) . '</td>
            </tr>
            <tr>
            <td align="right">Total</td>
            <td align="right">$ ' . number_format($grandtotal, 2) . '</td>
            </tr></table>
            ';

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('');
            $pdf->SetTitle('');
            $pdf->SetSubject('');

            $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));

            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(true);

            $pdf->SetHeaderData('', '', $cpa->companyname . '', 'Purchase Order');

            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->AddPage('L', 'LETTER');

            $pdf->SetFont('helvetica', '', 8, '', true);
            $pdf->writeHTML($pdfhtml, true, 0, true, true);
            //$pdf->AddPage();

            $pdf->lastPage();
            $pdfname = $config['base_dir'] . 'uploads/pdf/' . $quote->ponum . '_' . $company['id'] . '_accept.pdf';
            $pdf->Output($pdfname, 'f');
            $link = '<a href="' . site_url('quote/track/' . $quote->id) . '"></a>';
            $mailbody = "Please find the attachment for your Purchase order (PO#: " . $quote->ponum . ").<br/><br/>";
            $mailbody .= "You have been awarded by " . $cpa->companyname . ".  for PO#: " . $quote->ponum . ".<br/>";

            $settings = (array) $this->settings_model->get_current_settings();
            $this->load->library('email');
            $this->email->clear(true);
            $this->email->from($settings['adminemail'], "Administrator");

            $toemail = $settings['adminemail'] . ',' . $company['primaryemail'];
            $sql = "SELECT u.email FROM " . $this->db->dbprefix('users') . " u, " . $this->db->dbprefix('quoteuser') . " qu
	        		WHERE qu.userid=u.id AND qu.quote=" . $quote->id;
            $purchaseusers = $this->db->query($sql)->result();
            foreach ($purchaseusers as $pu) {
                $toemail = $toemail . ',' . $pu->email;
            }
            $this->email->to($toemail);

            $this->email->subject('Your Purchase order for PO#:' . $quote->ponum);
            $this->email->message($mailbody);
            $this->email->set_mailtype("html");
            $this->email->attach($pdfname);
            $this->email->send();

            $notification = array(
                'quote' => $quote->id,
                'company' => $company['id'],
                'ponum' => $quote->ponum,
                'category' => 'Award',
                'senton' => date('Y-m-d H:i'),
                'isread' => '0'
            );
            $notification['purchasingadmin'] = $this->session->userdata('purchasingadmin');
            $this->db->insert('notification', $notification);
        }
    }

    function findcostcode() 
    {
        $codes = $this->quote_model->findcostcode($_GET['term']);
        $items = array();
        foreach ($codes as $code) {
            $items[] = $code->code;
        }
        echo json_encode($items);
    }

    function finditemcode() 
    {
    	  log_message('debug',"texxxxxxxxxto");
        $codes = $this->quote_model->finditemcode($_GET['term']);
        $items = array();
        foreach ($codes as $code) {
            $item = array();
            $item['value'] = $code->itemcode;
            $item['label'] = '<font color="#990000">' . $code->itemcode . '</font> - ' . $code->itemname;
            $item['desc'] = $code->itemname;
            $items[] = $item;
            //$items[]= $code->itemcode.'<br/>'.$code->itemname;
        }
        echo json_encode($items);
    }
    function getitembycode() 
    {
        $code = $_POST['code'];
        if(isset($projectid))
        $projectid = $_POST['projectid'];
        //fwrite(fopen("sql.txt","a+"),print_r($code,true));
        $item = $this->quote_model->finditembycode($code);

		$this->db->where('itemid',$item->itemid);
		$this->db->where('type','Purchasing');
		$this->db->where('company',$this->session->userdata('purchasingadmin'));
		$companyitem = $this->db->get('companyitem')->row();
        //print_r($companyitem);
        
		if($companyitem)
		{
			if($companyitem->projectid){
				$arrproj = explode(",",$companyitem->projectid);
			
				if($companyitem->projectid != -1 && in_array($projectid,$arrproj)){
					$this->db->where('companyitemid',$companyitem->id);
					$companyprojectitem = $this->db->get('company_projectitem_notes')->row();
					if($companyprojectitem)
					$item->notes = $companyprojectitem->companynotes;
					else
					$item->notes = $companyitem->companynotes;
				}else
				$item->notes = $companyitem->companynotes;
			}else
				$item->notes = $companyitem->companynotes;
			
			//$item->notes = $companyitem->companynotes;
			$item->item_img = $companyitem->filename;
		}
		
        //fwrite(fopen("sql.txt","a+"),print_r($code,true));
        echo json_encode($item); // die;
    }

    function makedefaultcostcode() {
        if (!$_POST)
            die;
        if (!$_POST['defaultcostcode'])
            die;
        $temp = $_POST;
        $this->session->set_userdata($temp);
    }

    function updateitemnamewithcode() 
    {
        if (!$_POST)
            die;
        if (!$_POST['itemid'] || !$_POST['itemname'])
            die;
        if ($this->session->userdata('usertype_id') == 2) {
            $this->quote_model->db->where('type', 'Purchasing');
            $this->quote_model->db->where('id', $_POST['itemid']);
            $this->quote_model->db->where('company', $this->session->userdata('id'));
            $this->quote_model->db->update('item', $_POST);
        } elseif ($this->session->userdata('usertype_id') == 1) {
            $this->quote_model->db->where('id', $_POST['itemid']);
            unset($_POST['itemid']);
            $this->quote_model->db->update('item', $_POST);
        }
    }

    function delete($id) {
        $quote = $this->quote_model->get_quotes_by_id($id);
        if ($this->session->userdata('usertype_id') == 2 && $quote->purchasingadmin != $this->session->userdata('id')) {
            redirect('admin/dashboard', 'refresh');
        }
        $this->quote_model->remove_quote($id);
        redirect('admin/quote/index/' . $this->session->userdata('managedproject'), 'refresh');
    }

    function _set_fields() {
        $fields ['id'] = 'id';
        $fields ['pid'] = 'pid';
        $fields ['potype'] = 'potype';
        $fields ['ponum'] = 'ponum';
        $fields ['podate'] = 'PO Date';
        $fields ['duedate'] = 'Due Date';
        $fields ['company'] = 'company';
        $fields ['subject'] = 'subject';
        $fields ['deliverydate'] = 'delivery date';
        $fields ['subtotal'] = 'sub total';
        $fields ['taxtotal'] = 'tax total';
        $fields ['total'] = 'total';
        $this->validation->set_fields($fields);
    }

    function _set_rules() {
        $rules ['ponum'] = 'trim|required';
        $rules ['potype'] = 'trim|required';
        $this->validation->set_rules($rules);
        $this->validation->set_message('required', '* required');
        $this->validation->set_error_delimiters('<div class="error">', '</div>');
    }

    // Start ON 21st jan 2014
    function getcompany_ajax() {
        $localresult = isset($_POST['localresult']) ? $_POST['localresult'] : '';
        $supplyresult = isset($_POST['supplyresult']) ? $_POST['supplyresult'] : '1';
        $internetresult = isset($_POST['internetresult']) ? $_POST['internetresult'] : '';
        $radiusval = isset($_POST['radiusval']) ? $_POST['radiusval'] : '';
        $id = isset($_POST['id']) ? $_POST['id'] : '';

        $arr = array();
        $sql = "SELECT * FROM " . $this->db->dbprefix('company') . " WHERE 1=1";


        if ($localresult == 1) {
            $lat = $this->quote_model->getcomplat($this->session->userdata('id'));
            $lng = $this->quote_model->getcomplong($this->session->userdata('id'));

            $sql_radius = "SELECT  *,(3963.191 * acos( cos( radians({$lat}) ) * cos( radians( `com_lat` ) ) * cos( radians( `com_lng` ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( `com_lat` ) ) ) ) AS distance
                    FROM " . $this->db->dbprefix('company') . "
                    HAVING distance <= {$radiusval}
                    ORDER BY distance ASC";

            $sql_radius = $this->db->query($sql_radius);
            $dist = $sql_radius->result();

            foreach ($dist as $ret) {

                array_push($arr, $ret->id, true);
            }
            if (!empty($arr)) {
                $arr1 = implode(',', $arr);
                $sql .= " and id IN ($arr1)";
            } else {
                return '';
            }
        }
        if ($internetresult == 1) {
            
        }

        $str = '';

        $query = $this->db->query($sql);
        if ($query->result()) {
            $invited = $this->quote_model->getInvited($id);

            $companylist = $query->result();
            $i = 0;
            foreach ($companylist as $c) {
                if (!in_array($c->id, $invited)) {
                    $i++;
                    $str.= '<input type="checkbox" class="invite" value="' . $c->id . '" />&nbsp;&nbsp;' . $c->title . '<br/>';
                }
            }
            echo $str;
        } else {
            echo $str;
            exit;
            ;
        }
    }

    // End
}

?>