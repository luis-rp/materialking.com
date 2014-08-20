<?php
class report extends CI_Controller 
{
	private $limit = 10;
	
	function report() 
	{
		parent::__construct ();
		$this->load->library('session');
	    if(!$this->session->userdata('id'))
		{
			redirect('admin/login/index', 'refresh'); 
		}
		if($this->session->userdata('usertype_id')==3)
		{
			redirect('admin/dashboard', 'refresh'); 
		}
		$this->load->library ('form_validation');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$this->load->model('admin/report_model');
		$this->load->model('admin/settings_model');
		$this->load->model('admin/company_model');
		$this->load->model('admin/quote_model');
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$data ['title'] = "Administrator";
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}
	
		
	function export($offset = 0)
	{
		$uri_segment = 4;
		$offset = $this->uri->segment ($uri_segment);
		$reports = $this->report_model->get_reports ();
	
		$count = count ($reports);
		$items = array();
		if ($count >= 1)
		{
			foreach ($reports as $report)
			{
				$items[] = $report;
			}
			//echo '<pre>';print_r($items);die;
			$data['reports'] = $items;
		}
		if(!$items)
		{
			$this->data['message'] = 'No Records';
		}
		$query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
		$data['companies'] = $this->db->query($query)->result();
	
		$data['settings'] = $this->settings_model->get_current_settings();
	
		$data ['addlink'] = '';
		$data ['heading'] = 'Report';
	
		//=========================================================================================
		
		
		
		$header[] = array('Report Type' , 'Billing Report','' , '' , '' , '' , '' , '','','','' ,'','' ,'');	
		
		if($this->session->userdata('managedprojectdetails'))
		{
			$header[] = array('Project Title', $this->session->userdata('managedprojectdetails')->title,'' , '' , '' , '' , '' , '','','','' ,'','' ,'');
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'','' ,'');			
		}	
			
		
		$settings = $data['settings'];
		$tax = $settings->taxpercent;
	
			
		$totalallquantity = 0;
		$totalallprice = 0;
		$totalallpaid = 0;
			
		$i = 0;
					
		foreach($data['reports'] as $report)
		{
	
			$header[] = array('Company' , 'PO#','Item Code' , 'Item Name' , 'Unit' , 'Qty.' , 'EA' , 'Total','Payment','Verification','Notes' ,'Invoice#','Cost Code','Due Date');
	
			$totalquantity = 0;
			$totalprice    = 0;
			$totalpaid     = 0;
			$totalremaining = 0;
	
			if(!$report->totalpaid) $report->totalpaid = 0;
			$report->totalpaid = $report->totalpaid + ($report->totalpaid*$tax/100);
			$report->totalpaid = round($report->totalpaid,2);
				
			$report->totalprice = $report->totalprice + ($report->totalprice*$tax/100);
			$report->totalprice = round($report->totalprice,2);
				
			$totalallquantity+=$report->totalquantity;
			$totalallpaid += $report->totalpaid;
				
	
			foreach($report->items as $item)
			{
	
	
				$amount = $item->quantity * $item->ea;
				$amount = round($amount + ($amount*$tax/100),2);
				$totalallprice += $amount;
					
					
				$aaaa_due_date = '';
				if(isset($item->datedue) && $item->datedue!="")
				{ $aaaa_due_date =  date("m/d/Y", strtotime($item->datedue)); }
	
				$header[] = array($item->companyname , $item->ponum,$item->itemcode , $item->itemname , $item->unit , $item->quantity , $item->ea.chr(160), '$'.round($amount,2).chr(160),$item->paymentstatus,$item->status,$item->notes ,$item->invoicenum,$item->costcode,$aaaa_due_date);
			}
	
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'','' ,'');
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'','' ,'');
	
			$header[] = array('DATE' , $report->receiveddate,'' , '' , '' , '' , '' , '','','','' ,'','' ,'');
			$header[] = array('TOTAL QUANTITY' , $report->totalquantity,'' , '' , '' , '' , '' , '','','','' ,'','' ,'');
				
			$header[] = array('TOTAL AMOUNT' , '$'.$report->totalprice.chr(160),'' , '' , '' , '' , '' , '','','','' ,'','' ,'');
				
			$header[] = array('TOTAL PAID' , '$'.$report->totalpaid.chr(160),'' , '' , '' , '' , '' , '','','','' ,'','' ,'');
				
			$totalremaining = $totalprice - $totalpaid;
				
			$header[] = array('TOTAL REMAINING' , '$'.($report->totalprice - $report->totalpaid).chr(160),'' , '' , '' , '' , '' , '','','','' ,'','' ,'');
	
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'','' ,'');
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'','' ,'');
	
		}
	
			
		if(@$reports)
		{
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'','' ,'');
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'','' ,'');
	
			$date_range = $_POST['searchfrom'].' - '.$_POST['searchto'];
	
			$header[] = array('DATE' , $date_range,'' , '' , '' , '' , '' , '','','','' ,'');
	
			$header[] = array('TOTAL All QUANTITY' , $totalallquantity,'' , '' , '' , '' , '' , '','','','' ,'','' ,'');
				
			$header[] = array('TOTAL All AMOUNT' , '$'.$totalallprice.chr(160),'' , '' , '' , '' , '' , '','','','' ,'','' ,'');
				
			$header[] = array('TOTAL All PAID' , '$'.$totalallpaid.chr(160),'' , '' , '' , '' , '' , '','','','' ,'','' ,'');
	
			$totalallremaining = $totalallprice - $totalallpaid;
	
			$header[] = array('TOTAL All REMAINING' , '$'.$totalallremaining.chr(160),'' , '' , '' , '' , '' , '','','','' ,'','' ,'');
	
				
	
		}
			
		createXls('report', $header);
		die();
			
		//===============================================================================
			
	}
	
	
	function index($offset = 0) 
	{
		$uri_segment = 4;
		$offset = $this->uri->segment ($uri_segment);
		$reports = $this->report_model->get_reports ();
		
		$count = count ($reports);
		$items = array();
		if ($count >= 1) 
		{
			foreach ($reports as $report) 
			{
				$items[] = $report;
			}
			//echo '<pre>';print_r($items);die;
		    $data['reports'] = $items;
		}
		if(!$items)
		{
		    $this->data['message'] = 'No Records';
		}
        $query = "SELECT c.* FROM ".$this->db->dbprefix('company')." c, ".$this->db->dbprefix('network')." n
        		  WHERE c.id=n.company AND n.purchasingadmin='".$this->session->userdata('purchasingadmin')."'";
        $data['companies'] = $this->db->query($query)->result();
        
        $data['settings'] = $this->settings_model->get_current_settings();
        
		$data ['addlink'] = '';
		$data ['heading'] = 'Report';
		$this->load->view ('admin/report', $data);
	}
	
	function payinvoice()
	{
	    $this->db->where($_POST)->update('received',array('paymentstatus'=>'Paid'));
	    redirect('admin/report');
	}
	
}
?>