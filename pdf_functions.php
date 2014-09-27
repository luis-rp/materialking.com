<?php
require_once('TCPDF/examples/tcpdf_include.php'); 
require_once('TCPDF/tcpdf.php');


function createPDF($sheet_name, $pdfdata,$headername)
{
		// Include the main TCPDF library (search for installation path).
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);  
		
		//	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH,'','', PDF_HEADER_STRING);
		//$pdf->xheadercolor = array(238,238,238);
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		
		// -------------------------------------------------------------------
		
		// add a page
		$pdf->AddPage();
		 
		 // set font
		
		$pdf->SetAutoPageBreak(TRUE, 0);	
		//$pdf->SetFont('aealarabiya', '', 16);	
		 
		// $pdf->SetXY(230, 5);
		 $pdf->Rect(227, 6, 55, 15, 'F', array(), array(0,174,239));
		// $pdf->Rect(227, 6, 55, 15, 'F', array(), array(221,221,221));
		//$pdf->Image(PDF_HEADER_LOGO, '', '', 50, 12, '', '', 'R', true, 300, '', false, false, 100, false, false, false);
	 		
		$pdf->Image(PDF_HEADER_LOGO, 230, 7, 50, 13, 'PNG', '', 'T', true, 300, '', false, false, 100, false, false, false);
		 $pdf->SetXY(15, 5);
		 $pdf->SetAutoPageBreak(true, 30);
		  $pdf->SetFont('dejavuserif', 'B', 20);	 
		$pdf->Write(10, $headername, '', 0, 'L', true, 0, false, false, 0);	
		$pdf->SetFont('dejavuserif', '', 8);		 
		$arr = array($sheet_name=>$pdfdata );		
		$tbl= '<br/><br/><br/><br/><table  border="0" cellpadding="4" cellspacing="1" width="100%">'; 		 
		foreach($arr as $wbname=>$rows)
		{
			$rowcount = count($rows);
			$colcount = count($rows[0]);  
			//Table Heading
			$tbl.='<thead><tr style="background-color:#00AEEF;font-size:14px;color:#ffffff;">';
		   
				for($i=0; $i<$colcount;$i++)
				{
					$data=$rows[0][$i];					 
					$tbl.='<th align="left"><b>'.$data.'</b></th>';			
				}
		   
				$tbl.='</tr></thead><tbody>';
				 
				 
			$pdf->SetFont('pdfatimes', '', 8);		
			// Data Show
			for( $j=1; $j<$rowcount; $j++ )
			{
					$tbl.='<tr style="background-color:#EEEEEE;font-size:12px;color:#000000;">';
					for($i=0; $i<$colcount;$i++)
					{ 	 
						if (isset($rows[$j][$i]))
						{
							$data2 = $rows[$j][$i];
							 
							$tbl.='<td style="color:#000000;margin-left:4px;">'.utf8_encode($data2).'&nbsp;</td>';                
						}
					}
					 
					$tbl.='</tr>';
					 
				
			}
		}  
		
		$tbl.='</tbody></table>';
		 $pdf->SetAutoPageBreak(TRUE, 10);
		$pdf->writeHTML( $tbl, true, false, true, false, '');
		 //echo $tbl; die();
		 
		
		// -------------------------------------------------------------------
		//header('Content-type: application/pdf');
		//header('Content-Disposition: attachment; filename="'.$sheet_name.'.pdf'"');
		//Close and output PDF document
		$pdf->Output($sheet_name.'_.pdf', 'I');
		//unlink('index.pdf');
		//$pdf->Output('index.pdf', 'F');

}

// 2nd Template
function createOtherPDF($sheet_name, $pdfdata,$headername)
{
 		// Include the main TCPDF library (search for installation path).
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);  
		
		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH );
		//$pdf->xheadercolor = array(238,238,238);
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		
		// -------------------------------------------------------------------
		
		// add a page
		$pdf->AddPage();
		 
		 $pdf->SetAutoPageBreak(TRUE, 0);	
		//$pdf->SetFont('aefurat', 'B', 16);	 
		// $pdf->SetXY(230, 5);
		 $pdf->Rect(227, 6, 55, 15, 'F', array(), array(0,174,239));
		// $pdf->Rect(227, 6, 55, 15, 'F', array(), array(221,221,221));
		//$pdf->Image(PDF_HEADER_LOGO, '', '', 50, 12, '', '', 'R', true, 300, '', false, false, 100, false, false, false);
	 		
		$pdf->Image(K_PATH_IMAGES.PDF_HEADER_LOGO, 230, 7, 50, 13, 'PNG', '', 'T', true, 300, '', false, false, 100, false, false, false);
		 $pdf->SetXY(15, 5);
		 $pdf->SetAutoPageBreak(true, 30);
		  $pdf->SetFont('dejavuserif', 'B', 20);	
		$pdf->Write(10, $headername, '', 0, 'L', true, 0, false, false, 0);	
		
		
		$arr = array($sheet_name=>$pdfdata );
		 
		$tbl_header = '<table border="0" cellpadding="4" cellspacing="1" width="100%">'; 
		$tbl_footer = '</table>';
		$tbl = '';
		 $tcl = '';
		 $tdl = '';
		foreach($arr as $wbname=>$rows)
		{
			$rowcount = count($rows);
			$colcount = count($rows[0]);  
			//Table Heading
			 
			$tbl.='<thead><tr style="background-color:#FFFFFF;font-size:14px;color:#000000;">';
			$tbl.='<th align="left" width="16%"><h4 style="text-transform:uppercase;font-size:18px;font-weight:normal;margin-bottom:0px;">'.$rows[0][0].'</h4></th>';
			$tbl.='<th align="left" width="40%"><h4 style="text-transform:uppercase;font-size:18px;font-weight:normal;margin-bottom:0px;">'.$rows[0][1].'</h4></th>';			
			$pdf->SetFont('dejavuserif', '', 10);	 
		   
			$tbl.='</tr></thead>';
			 
			$tcl.='<thead><tr style="background-color:#00AEEF;color:#ffffff;">';
	    
			for($i=0; $i<$colcount;$i++)
			{
				$data=$rows[1][$i];					 
				$tcl.='<th align="left" style="font-size:14px;margin-left:4px;">'.utf8_encode($data).'</th>';			
			}
	  
			$tcl.='</tr></thead>';			
			$tcl.='<tbody>';
			
			// Data Show
			for( $j=2; $j<$rowcount; $j++ )
			{
					$tcl.='<tr style="background-color:#EEEEEE;font-size:12px;color:#000000;">';
					for($i=0; $i<$colcount;$i++)
					{ 	 
						if (isset($rows[$j][$i]))
						{
							$data2 = $rows[$j][$i];
							 
							$tcl.='<td style="color:#000000;margin-left:4px;">'.utf8_encode($data2).'</td>';                
						}
					}
					 
					$tcl.='</tr>';
					 
				
			}
		}  
		
		$tcl.='</tbody>';
		$pdf->SetAutoPageBreak(TRUE, 10);
		$pdf->writeHTML($tbl_header . $tbl . $tbl_footer, true, false, true, false, '');
		//$pdf->writeHTML($tbl_header . $tdl . $tbl_footer, true, false, true, false, '');
		$pdf->writeHTML($tbl_header . $tcl . $tbl_footer, true, false, true, false, '');
		 //echo $tbl; die();
		// -------------------------------------------------------------------
		//header('Content-type: application/pdf');
		//header('Content-Disposition: attachment; filename="'.$sheet_name.'.pdf'"');
		//Close and output PDF document
		$pdf->Output($sheet_name.'.pdf', 'I');
		//unlink('index.pdf');
		//$pdf->Output('index.pdf', 'F');
}

// 3rd Template
function createOtherPDF2($sheet_name, $pdfdata,$headername)
{
 		// Include the main TCPDF library (search for installation path).
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);  
		
		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH );
		//$pdf->xheadercolor = array(238,238,238);
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		
		// -------------------------------------------------------------------
		
		// add a page
		$pdf->AddPage();
		 
		 $pdf->SetAutoPageBreak(TRUE, 0);	
		//$pdf->SetFont('aefurat', 'B', 16);	 
		// $pdf->SetXY(230, 5);
		 $pdf->Rect(227, 6, 55, 15, 'F', array(), array(0,174,239));
		// $pdf->Rect(227, 6, 55, 15, 'F', array(), array(221,221,221));
		//$pdf->Image(PDF_HEADER_LOGO, '', '', 50, 12, '', '', 'R', true, 300, '', false, false, 100, false, false, false);
	 		
		$pdf->Image(K_PATH_IMAGES.PDF_HEADER_LOGO, 230, 7, 50, 13, 'PNG', '', 'T', true, 300, '', false, false, 100, false, false, false);
		 $pdf->SetXY(15, 5);
		 $pdf->SetAutoPageBreak(true, 30);
		  $pdf->SetFont('dejavuserif', 'B', 20);	
		$pdf->Write(10, $headername, '', 0, 'L', true, 0, false, false, 0);	
		
		
		$arr = array($sheet_name=>$pdfdata );
		 
		$tbl_header = '<table border="0" cellpadding="4" cellspacing="1" width="100%">'; 
		$tbl_footer = '</table>';
		$tbl = '';
		 $tcl = '';
		 $tdl = '';
		foreach($arr as $wbname=>$rows)
		{
			$rowcount = count($rows);
			$colcount = count($rows[0]);  
			//Table Heading
			 
			$tbl.='<thead><tr style="background-color:#FFFFFF;font-size:14px;color:#000000;">';
			$tbl.='<th align="left" width="20%"><h4 style="text-transform:uppercase;font-size:18px;font-weight:normal;margin-bottom:0px;">'.$rows[0][0].'</h4></th>';
			$tbl.='<th align="left" width="40%"><h4 style="text-transform:uppercase;font-size:18px;font-weight:normal;margin-bottom:0px;">'.$rows[0][1].'</h4></th>';			
			$pdf->SetFont('dejavuserif', '', 10);	 
		   
			$tbl.='</tr></thead>';
			 
			/*$tcl.='<thead><tr style="background-color:#FFFFFF;color:#ffffff;">';
	    
			for($i=0; $i<$colcount;$i++)
			{
				$data=$rows[1][$i];					 
				$tcl.='<th align="left" style="font-size:14px;margin-left:4px;">'.utf8_encode($data).'</th>';			
			}
	  
			$tcl.='</tr></thead>';	*/		
			$tcl.='<tbody>';
			
			// Data Show
			for( $j=2; $j<$rowcount; $j++ )
			{
					$tcl.='<tr style="background-color:#EEEEEE;font-size:12px;color:#000000;">';
					for($i=0; $i<$colcount;$i++)
					{ 	 
						if (isset($rows[$j][$i]))
						{
							$data2 = $rows[$j][$i];
							 
							$tcl.='<td style="color:#000000;margin-left:4px;">'.utf8_encode($data2).'</td>';                
						}
					}
					 
					$tcl.='</tr>';
					 
				
			}
		}  
		
		$tcl.='</tbody>';
		$pdf->SetAutoPageBreak(TRUE, 10);
		$pdf->writeHTML($tbl_header . $tbl . $tbl_footer, true, false, true, false, '');
		//$pdf->writeHTML($tbl_header . $tdl . $tbl_footer, true, false, true, false, '');
		$pdf->writeHTML($tbl_header . $tcl . $tbl_footer, true, false, true, false, '');
		 //echo $tbl; die();
		// -------------------------------------------------------------------
		//header('Content-type: application/pdf');
		//header('Content-Disposition: attachment; filename="'.$sheet_name.'.pdf'"');
		//Close and output PDF document
		$pdf->Output($sheet_name.'.pdf', 'I');
		//unlink('index.pdf');
		//$pdf->Output('index.pdf', 'F');
}

?>