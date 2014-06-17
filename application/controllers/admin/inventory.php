<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends CI_Controller 
{
	public function Inventory()
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
		$this->load->dbforge();
		$this->load->library ('form_validation');
		$this->load->library ( array ('table', 'validation', 'session'));
		$this->load->helper ( 'form', 'url');
		$this->load->model ('admin/inventory_model', '', TRUE);
		$this->load->model ('admin/inventory_model', '', TRUE);
		$this->load->model('admin/company_model');
		$this->load->model('admin/quote_model');
		$this->load->model('admin/settings_model');
		$data['pendingbids'] = $this->quote_model->getpendingbids();
		$this->form_validation->set_error_delimiters ('<div class="red">', '</div>');
		$data ['title'] = 'Administrator';
		$this->load = new My_Loader();
		$this->load->template ( '../../templates/admin/template', $data);
	}

	public function index()
	{
		$items = $this->inventory_model->getItems($this->session->userdata('id'));
		$data['items'] = $items;
		$this->load->view('admin/inventory/items',$data);
	}
	
	public function updateitem()
	{
		if(!@$_POST)
		{
			die;
		}
		if(!@$_POST['itemid'])
		{
			die;
		}
		$this->db->where('itemid',$_POST['itemid']);
		$this->db->where('company',$this->session->userdata('id'));
		$this->db->where('type','Purchasing');
		$existing = $this->db->get('companyitem')->row();
		if($existing)
		{
			$this->db->where('itemid',$_POST['itemid']);
			$this->db->where('company',$this->session->userdata('id'));
		    $this->db->where('type','Purchasing');
			$this->db->update('companyitem',$_POST);
		}
		else
		{
			$_POST['company'] = $this->session->userdata('id');
			$_POST['type'] = 'Purchasing';
			$this->db->insert('companyitem',$_POST);
		}
	}
	
	public function details($itemid)
	{
		$item = $this->inventory_model->getitemdetails($itemid);
		
		if(!$item->companyitem)
		{
			redirect('admin/inventory', 'refresh'); 
		}
		for( $i = 0; $i<count($item->minprices); $i++) 
		{
			$item->minprices[$i]->substitute = $item->minprices[$i]->substitute?'Substitute':'';
			$item->minprices[$i]->price = '$'.$item->minprices[$i]->price;
		}
		
                
		$totalminprice = $this->inventory_model->getlowestquoteprice($itemid);
		$daysavgprice = $this->inventory_model->getdaysmeanprice($itemid);
		
		if($daysavgprice > $totalminprice)
			$trend = 'HIGH';
		elseif($daysavgprice < $totalminprice)
			$trend = 'LOW';
		else
			$trend = 'EQUAL';
		if($daysavgprice == null)
			$trend = 'NO DATA';
		
		$data['itempricetrend'] = $trend;
		$data['item'] = $item;
		//print_r($data);die;
		$this->load->view ('admin/inventory/details', $data);
	}

	
	function gethistory()
	{
		$company = $_POST['companyid'];
		$itemid = $_POST['itemid'];
		//print_r($_POST);die;
		$sql1 = "SELECT ai.quantity, ai.ea, q.ponum, a.quote, a.submitdate `date`, 'quoted'
			   	FROM
				".$this->db->dbprefix('biditem')." ai, ".$this->db->dbprefix('bid')." a, 
				".$this->db->dbprefix('quote')." q
				WHERE
				ai.bid=a.id AND a.quote=q.id AND ai.itemid='$itemid'
				AND a.company='$company'
				AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."' 
				";
		
		$sql2 = "SELECT ai.quantity, ai.ea, q.ponum, a.quote, a.awardedon `date`, 'awarded'
			   	FROM
				".$this->db->dbprefix('awarditem')." ai, ".$this->db->dbprefix('award')." a, 
				".$this->db->dbprefix('quote')." q
				WHERE
				ai.award=a.id AND a.quote=q.id AND ai.itemid='$itemid'		
				AND ai.company='$company'
				AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."' 
				";
		
		$sql = $sql1 ." UNION ".$sql2;
		$query = $this->db->query($sql);
		if($query->num_rows>0)
		{
			$result = $query->result ();
			$avgforpricedays = $this->inventory_model->getdaysmeanprice($itemid);
			$avgforpricedays = number_format($avgforpricedays, 2);
			$sqlavg = "SELECT AVG(ea) avgprice FROM ".$this->db->dbprefix('awarditem')." ai, ".$this->db->dbprefix('award')." a
				  WHERE ai.itemid='$itemid' AND ai.award=a.id AND ai.company='$company'
				  AND ai.purchasingadmin='".$this->session->userdata('purchasingadmin')."' 
			";
			$companyavgpricefordays = $this->db->query($sqlavg)->row()->avgprice;
			$companyavgpricefordays = number_format($companyavgpricefordays, 2);
			if($companyavgpricefordays > $avgforpricedays)
				$overalltrend = 'HIGH';
			elseif($companyavgpricefordays < $avgforpricedays)
				$overalltrend = 'LOW';
			else
				$overalltrend = 'GOOD';
			$overalltrend = "<b><font color='red'>$overalltrend</font></b>";
			
			$pricedays = $this->settings_model->get_current_settings ()->pricedays;
			
			$trendstring = 'Price Trend: '.$overalltrend."(item avg for $pricedays days: $avgforpricedays, company avg price: $companyavgpricefordays.)<br/>";
			if($avgforpricedays == 0)
				$trendstring .= 'Item not awarded for set days.';
			if($companyavgpricefordays == null)
				$trendstring .= 'Item not awarded to this company.';
				
			$ret = $trendstring;
			
			$ret .= '<table class="table table-bordered">';
			$ret .= '<tr><th>Date</th><th>Status</th><th>PO#</th><th>Trend</th><th>Qty.</th><th>Price</th></tr>';
			foreach($result as $item)
			{
				if($item->ea > $avgforpricedays)
					$trend = 'HIGH';
				elseif($item->ea < $avgforpricedays)
					$trend = 'LOW';
				else
					$trend = 'GOOD';
				if($avgforpricedays == null)
					$trend = 'NO DATA';
				$ret .= '<tr><td>'.$item->date.'</td><td>'.$item->quoted.'</td><td>'.$item->ponum.'</td><td>'.$trend.'</td><td>'.$item->quantity.'</td><td>'.$item->ea.'</td></tr>';
			}
			$ret .= '</table>';
			echo $ret;
		}
		die;
	}
}
