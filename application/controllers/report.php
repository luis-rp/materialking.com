<?php
class report extends CI_Controller 
{
	private $limit = 10;
	
	function report() 
	{
	    parent::__construct ();
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$data ['title'] = 'Dashboard';
		$this->load->dbforge();
		$this->load->model ('homemodel', '', TRUE);
		$this->load->model ('reportmodel', '', TRUE);
		$this->load->model ('quotemodel', '', TRUE);
		$this->load->model ('companymodel', '', TRUE);
		$this->load->model ('messagemodel', '', TRUE);
		$this->load->model ('admin/settings_model', '', TRUE);
		if($this->session->userdata('company')) $data['newquotes'] = $this->quotemodel->getnewinvitations($this->session->userdata('company')->id);
		$data['newnotifications'] = $this->messagemodel->getnewnotifications();
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/front/template', $data);
	}
	
	function index($offset = 0) 
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$uri_segment = 4;
		$filter = '';		
		if(@$_POST['purchasingadmin'])
		{
			$filter = " AND i.purchasingadmin='".$_POST['purchasingadmin']."'";
			$this->db->where('purchasingadmin',$_POST['purchasingadmin']);
			$projects = $this->db->get('project')->result();
			
			$data['projects'] = array();
			foreach($projects as $project)
			{
				$sql = "SELECT * FROM ".$this->db->dbprefix('quote')." q, ".$this->db->dbprefix('bid')." b
					WHERE b.quote=q.id AND q.pid=".$project->id;
				if($this->db->query($sql)->result())
				{
					$data['projects'][]=$project;
				}
			}
		}
		$offset = $this->uri->segment ($uri_segment);
		$reports = $this->reportmodel->get_reports ();
		
		$count = count ($reports);
		$items = array();
		if ($count >= 1) 
		{
			foreach ($reports as $report) 
			{
			    //echo '<pre>';print_r($report);die;
				$items[] = $report;
			}
			//echo '<pre>';print_r($items);die;
		    $data['reports'] = $items;
		}
		if(!$items)
		{
		    $this->data['message'] = 'No Records';
		}
        $query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('network')." n
        		  WHERE u.id=n.purchasingadmin AND n.company='".$company->id."'";
        //$query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u WHERE usertype_id=2 AND username IS NOT NULL";
        $data['purchasingadmins'] = $this->db->query($query)->result();
        
		$data ['addlink'] = '';
		$data ['heading'] = 'Report';
		$this->load->view ('company/report', $data);
	}
	
	function export($offset = 0)
	{
		$company = $this->session->userdata('company');
		if(!$company)
			redirect('company/login');
		$uri_segment = 4;
		$offset = $this->uri->segment ($uri_segment);
		$reports = $this->reportmodel->get_reports ();
			
		$count = count ($reports);
		$items = array();
		if ($count >= 1)
		{
			foreach ($reports as $report)
			{
				$items[] = $report;
			}
			$data['reports'] = $items;
		}
		if(!$items)
		{
			$this->data['message'] = 'No Records';
		}
		$query = "SELECT u.* FROM ".$this->db->dbprefix('users')." u, ".$this->db->dbprefix('network')." n
					  WHERE u.id=n.purchasingadmin AND n.company='".$company->id."'";
			
		$data['purchasingadmins'] = $this->db->query($query)->result();
			
		$data ['addlink'] = '';
		$data ['heading'] = 'Report';
	
		//=========================================================================================
	
		$totalallquantity = 0;
		$totalallprice    = 0;
		$totalallpaid     = 0;
			
		$i = 0;
		foreach($data['reports'] as $report)
		{
			$header[] = array('Company' , 'PO#','Item Code' , 'Item Name' , 'Unit' , 'Qty.' , 'EA' , 'Total','Payment','Verification','Notes' ,'Invoice#');
	
			$totalquantity  = 0;
			$totalprice     = 0;
			$totalpaid      = 0;
			$totalremaining = 0;
			foreach($report->items as $item)
			{
				$amount = $item->quantity * $item->ea;
				$amount = round($amount + ($amount*$item->taxpercent/100),2);
				$totalallprice += $amount;
					
				$totalquantity += $item->quantity;
				$totalprice += $amount;
				if($item->paymentstatus=='Paid')
				{
					$totalpaid += $amount;
					$totalallpaid += $amount;
				}
					
				$header[] = array($item->companyname , $item->ponum,$item->itemcode , $item->itemname , $item->unit , $item->quantity , formatPriceNew($item->ea), '$'.formatPriceNew(round($amount,2)),$item->paymentstatus,$item->status,$item->notes ,$item->invoicenum);
					
			}
	
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'');
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'');
	
			$totalremaining = $totalprice - $totalpaid;
	
			$header[] = array('DATE' , $report->receiveddate,'' , '' , '' , '' , '' , '','','','' ,'');
			$header[] = array('TOTAL QUANTITY' , $totalquantity,'' , '' , '' , '' , '' , '','','','' ,'');
				
			$header[] = array('TOTAL AMOUNT' , '$'.formatPriceNew($totalprice),'' , '' , '' , '' , '' , '','','','' ,'');
				
			$header[] = array('TOTAL PAID' , '$'.formatPriceNew($totalpaid),'' , '' , '' , '' , '' , '','','','' ,'');
				
			$header[] = array('TOTAL REMAINING' , '$'.formatPriceNew($totalremaining),'' , '' , '' , '' , '' , '','','','' ,'');
	
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'');
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'');
	
	
		}
			
			
		$reports = $data['reports'];
			
		if(@$reports)
		{
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'');
	
			$header[] = array('' , '','' , '' , '' , '' , '' , '','','','' ,'');
	
			$date_range = $_POST['searchfrom'].' - '.$_POST['searchto'];
	
			$header[] = array('DATE' , $date_range,'' , '' , '' , '' , '' , '','','','' ,'');
	
			$header[] = array('TOTAL All QUANTITY' , $totalallquantity,'' , '' , '' , '' , '' , '','','','' ,'');
				
			$header[] = array('TOTAL All AMOUNT' , '$'.formatPriceNew($totalallprice),'' , '' , '' , '' , '' , '','','','' ,'');
				
			$header[] = array('TOTAL All PAID' , '$'.formatPriceNew($totalallpaid),'' , '' , '' , '' , '' , '','','','' ,'');
	
			$totalallremaining = $totalallprice - $totalallpaid;
	
			$header[] = array('TOTAL All REMAINING' , '$'.formatPriceNew($totalallremaining),'' , '' , '' , '' , '' , '','','','' ,'');
		}
			
			
			
			
			
			
		createXls('report', $header);
		die();
			
		//===============================================================================
	
	}
}
?>