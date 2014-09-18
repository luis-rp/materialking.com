<?php

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);  

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH );
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
$pdf->SetFont('helvetica', 'B', 20);
$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->Write(10, 'Cost Code Management', '', 0, 'L', true, 0, false, false, 0);

$pdf->SetFont('helvetica', '', 8);



// ---------------------------------------------------------
 
$tbl='<table border="0" cellpadding="3" cellspacing="1" width="100%"> 
 <tr style="background-color:#00AEEF;font-size:14px;color:#ffffff;">
  <th align="left">Country</th>
  <th align="left">Capital</th>
  <th align="left">Area (sq km)</th>
  <th align="left">Pop. (thousands)</th>
 </tr>';
 for($i=0;$i<=10;$i++)
 {
 $tbl.=' <tr style="background-color:#D8D9DA;font-size:12px;color:#000000;">
  <td>Austria</td>
  <td>Vienna</td>
  <td>Vienna</td>
  <td>Vienna</td>
 </tr>';
 }
$tbl.='</table>';
 

$pdf->writeHTML($tbl, true, false, true, false, '');
 
 

// -------------------------------------------------------------------
header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="index.pdf"');
//Close and output PDF document
$pdf->Output('index.pdf', 'D');
unlink('index.pdf');
//$pdf->Output('index.pdf', 'F');

//============================================================+
// END OF FILE
//============================================================+
