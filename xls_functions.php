<?php
function formatPriceNew($price)
{

	if($price =='' || $price==0)
	{
		$price = $price.chr(160);
		return $price;
	}	
				
	$found = 0;
	
	if(strpos("$",$price)>-1)
	{
		$found = 1;
		$price = str_replace("$",'',$price);
	}	
	
	$price = trim($price);
		
	$num_1 =  number_format($price);
	
	$num_1  = "".$num_1.chr(160);
	
	
	if($found == 1)
	{
		$num_1 = '$'.$num_1;
	}
	
	
	return $num_1;
}




function createXls($sheet_name, $sheet1)
{

chdir('phpxls');

require_once 'Writer.php';

chdir('..');

$workbook = new Spreadsheet_Excel_Writer();

$format_und =& $workbook->addFormat();
$format_und->setBottom(2);//thick
$format_und->setBold();
$format_und->setColor('black');
$format_und->setFontFamily('Arial');
$format_und->setSize(8);
$format_und->setTextWrap();

$format_reg =& $workbook->addFormat();
$format_reg->setColor('black');
$format_reg->setFontFamily('Arial');
$format_reg->setSize(8);
$format_reg->setTextWrap();



//------------------------------------------------------------------------


$colwidth_arr = array();


foreach($sheet1 as $sh_rows)
{
	
	for($i=0 ; $i < sizeof($sh_rows) ; $i++)
	{
		if(isset($colwidth_arr[$i]))
		{
			if($colwidth_arr[$i] < strlen($sh_rows[$i]))
			{
				$colwidth_arr[$i] = strlen($sh_rows[$i]);
			}
		}
		else
		{
			$colwidth_arr[$i] = strlen($sh_rows[$i])+4;
		}	
	}
}

//-----------------------------------------------------------------------




//d($colwidth_arr);









$arr = array($sheet_name=>$sheet1 );

foreach($arr as $wbname=>$rows)
{
    $rowcount = count($rows);
    $colcount = count($rows[0]);

    $worksheet =& $workbook->addWorksheet($wbname);

   // $worksheet->setColumn(0,0, 6.14);
	
	//setColumn(startcol,endcol,float)
  
  
  //----------------------------------------------------------------------------------
	
	for($i=0 ; $i < sizeof($colwidth_arr) ; $i++)
	{		
		$w_value = $colwidth_arr[$i];		
		if($w_value > 40)
		{
			$w_value = 40;
		}	
		$worksheet->setColumn(0,$i, $w_value);
	}
	
	//------------------------------------------------------------------------------	
	
  
  
   // $worksheet->setColumn(1,3,15.00);
   // $worksheet->setColumn(4,4, 8.00);
  
    
    for( $j=0; $j<$rowcount; $j++ )
    {
        for($i=0; $i<$colcount;$i++)
        {
            $fmt  =& $format_reg;
            if ($j==0)
                $fmt =& $format_und;

            if (isset($rows[$j][$i]))
            {
                $data=$rows[$j][$i];
                $worksheet->write($j, $i, $data, $fmt);
            }
        }
    }
}

$workbook->send($sheet_name.'.xls');
$workbook->close();

}


function d($a)
{
	echo '<pre>';
	print_r($a);
	die();
}



//-----------------------------------------------------------------------------
?>