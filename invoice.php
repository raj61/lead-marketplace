<?php

require('invoice_functions.php');

$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
$pdf->AddPage();
$pdf->addSociete( "Bill To:",
                  "MonAdresse\n" .
                  "75000 PARIS\n".
                  "R.C.S. PARIS B 000 000 007\n" .
                  "Capital : 18000 " );
$pdf->addClientAdresse("INVOICE");
$pdf->addReglement("03/01/2017");
$pdf->addNumTVA("100/-");
$cols=array( "ITEM"    => 63,
             "QUANTITY"  => 64,
             "RATE"     => 63,);
$pdf->addCols( $cols);
$cols=array( "ITEM"    => "C",
             "QUANTITY"  => "C",
             "RATE"     => "C",);
$pdf->addLineFormat( $cols);
$pdf->addLineFormat($cols);

$y    = 89;
$line = array( "ITEM"    => "EDUCASH",
               "QUANTITY"  => "50",
               "RATE"     => "100 Rs.");
$size = $pdf->addLine( $y, $line );
$y   += $size + 2;

$pdf->total("Rs. 100/-");
$pdf->payment("Rs. 100/-");
$pdf->balance("Rs. 0/-");
$pdf->thanks("Thanks For Your Business");
$pdf->lastblock("EduGorilla is an initiative of Oprotech Technologies Pvt. Ltd.");
$pdf->lastblock2("EduGorilla Community India");

$pdf->Image("https://electronicsguide.000webhostapp.com/wp-content/uploads/2017/01/eg_logo.jpg",10,220,58.3898305,65);

$pdf->Output("reprt.pdf","");
?>
