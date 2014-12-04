<?php
class Messagemodel extends Model 
{
	
	function Messagemodel() 
	{
		parent::Model ();
	}
	
	function getconfigurations()
	{
	    $query = $this->db->get ('settings' );
	    $result = $query->result ();
	    return $result [0];
	}
	
	function getmessage($key)
	{
		$this->db->where('messagekey',$key);
		$query = $this->db->get('message');
		if($query->num_rows>0)
			return $query->row();
		else
			return NULL;
	}
	
	function getnewnotifications()
	{
		$company = $this->session->userdata('company');
		log_message("debug",var_export($company,true));
		if(!$company)
			return array();
		$this->db->where('isread',0);
		$this->db->where('company',$company->id);
		$this->db->order_by('senton','desc');
		$this->db->limit(5,0);
		$nots = $this->db->get('notification')->result();
		log_message("debug",var_export($nots,true));
		$ret = array();
		foreach($nots as $not)
		{
			$not->tago = $this->tago(strtotime($not->senton));
			$this->db->where('id',$not->purchasingadmin);
			$purchasingadmin = $this->db->get('users')->row();
			//if($purchasingadmin) {
			if($not->category=='Order')
			{
			
				$not->class='info';
				$this->db->where('ordernumber',$not->ponum);
				$ordertyperesult = $this->db->get('order')->result();
		
				if(isset($ordertyperesult[0]->type) && $ordertyperesult[0]->type)
				$ordertype = $ordertyperesult[0]->type;
				else 
				$ordertype = "";
				$not->message =  "You have received a new order request store with order# $not->ponum"."&nbsp;&nbsp;&nbsp;&nbsp;Type:&nbsp;".$ordertype;
				$this->db->where('orderid',$not->quote);
				$this->db->where('company',$company->id);
				$not->submessage = $this->db->count_all_results('orderdetails') . " items requested.";
				
				$not->link = site_url('order/details/'.$not->quote);
			}
			if($not->category=='Invitation(Direct)')
			{
				$this->db->where('company',$company->id);
				$this->db->where('quote',$not->quote);
				$invitation = @$this->db->get('invitation')->row()->invitation;
				$not->class='info';
				$not->message =  "You have received a new purchase order request from $purchasingadmin->companyname, $purchasingadmin->fullname, for the PO# $not->ponum";
				$this->db->where('quote',$not->quote);
				$this->db->where('company',$company->id);
				$not->submessage = $this->db->count_all_results('quoteitem') . " items requested.";
				
				$not->link = site_url('quote/direct/'.$invitation);
			}
			if($not->category=='Invitation' || $not->category=='Invitation Revision' || $not->category=='Invitation Reminder')
			{
				$this->db->where('company',$company->id);
				$this->db->where('quote',$not->quote);
				$invitation = @$this->db->get('invitation')->row()->invitation;
				$not->class='info';
				$not->message =  "You have received a new bid request from $purchasingadmin->companyname, $purchasingadmin->fullname, for the PO# $not->ponum";
				$this->db->where('quote',$not->quote);
				$not->submessage = $this->db->count_all_results('quoteitem') . " bid items requested.";
				
				$not->link = site_url('quote/invitation/'.$invitation);
			}
			if($not->category=='Backorder')
			{
				$this->db->where('quote',$not->quote);
				$award = $this->db->get('award')->row();
				$not->class='danger';
				$not->message = "You received ETA request from $purchasingadmin->companyname, $purchasingadmin->fullname, for the PO# $not->ponum";
				$items = 0;
				
				$this->db->where('award',$award->id);
				$this->db->where('company',$company->id);
				$this->db->where('quantity - received >',0);
				$not->submessage = $this->db->count_all_results('awarditem') . " bid items backordered.";
				$not->link = site_url('quote/viewbacktrack/'.$not->quote);
			}
			if($not->category=='Award')
			{
				$not->class='success';
				$not->message = "You have been awarded by $purchasingadmin->companyname, $purchasingadmin->fullname, for the PO# $not->ponum";
				$items = 0;
				$this->db->where('quote',$not->quote);
				$award = $this->db->get('award')->row();
				if(!$award)
					continue;
				$this->db->where('award',$award->id);
				$this->db->where('company',$company->id);
				$att = $this->db->count_all_results('awarditem');
				$not->submessage = $att . " items awarded";
				
				$this->db->where('award',$award->id);
				$ata = $this->db->count_all_results('awarditem');
				
				if($ata > $att)
					$not->submessage .= ' (partially)';
					
				$not->link = site_url('quote/items/'.$not->quote);
			}
			$ret[]=$not;
		  //}	
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
}
?>