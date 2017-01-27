<?php
require('fpdf.php');

class PDF_Invoice extends FPDF
{
// private variables
var $colonnes;
var $format;
var $angle=0;

// private functions
function RoundedRect($x, $y, $w, $h, $r, $style = '')
{
    $k = $this->k;
    $hp = $this->h;
    if($style=='F')
        $op='f';
    elseif($style=='FD' || $style=='DF')
        $op='B';
    else
        $op='S';
    $MyArc = 4/3 * (sqrt(2) - 1);
    $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
    $xc = $x+$w-$r ;
    $yc = $y+$r;
    $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

    $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
    $xc = $x+$w-$r ;
    $yc = $y+$h-$r;
    $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
    $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
    $xc = $x+$r ;
    $yc = $y+$h-$r;
    $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
    $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
    $xc = $x+$r ;
    $yc = $y+$r;
    $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
    $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
    $this->_out($op);
}

function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
{
    $h = $this->h;
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
                        $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
}

function Rotate($angle, $x=-1, $y=-1)
{
    if($x==-1)
        $x=$this->x;
    if($y==-1)
        $y=$this->y;
    if($this->angle!=0)
        $this->_out('Q');
    $this->angle=$angle;
    if($angle!=0)
    {
        $angle*=M_PI/180;
        $c=cos($angle);
        $s=sin($angle);
        $cx=$x*$this->k;
        $cy=($this->h-$y)*$this->k;
        $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
    }
}

function _endpage()
{
    if($this->angle!=0)
    {
        $this->angle=0;
        $this->_out('Q');
    }
    parent::_endpage();
}

//public functions
function sizeOfText( $texte, $largeur )
{
    $index    = 0;
    $nb_lines = 0;
    $loop     = TRUE;
    while ( $loop )
    {
        $pos = strpos($texte, "\n");
        if (!$pos)
        {
            $loop  = FALSE;
            $ligne = $texte;
        }
        else
        {
            $ligne  = substr( $texte, $index, $pos);
            $texte = substr( $texte, $pos+1 );
        }
        $length = floor( $this->GetStringWidth( $ligne ) );
        $res = 1 + floor( $length / $largeur) ;
        $nb_lines += $res;
    }
    return $nb_lines;
}

function addSociete( $nom, $adresse )
{
    $x1 = 10;
    $y1 = 8;
    $this->SetXY( $x1, $y1 );
    $this->SetFont('Arial','B',12);
    $length = $this->GetStringWidth( $nom );
    $this->Cell( $length, 2, $nom);
    $this->SetXY( $x1, $y1 + 4 );
    $this->SetFont('Arial','',10);
    $length = $this->GetStringWidth( $adresse );
    $lignes = $this->sizeOfText( $adresse, $length) ;
    $this->MultiCell($length, 4, $adresse);
}

// Invoice
// Client address
function addClientAdresse( $adresse )
{
	$this->SetFont( "Arial", "B", 20);
    $r1  = $this->w - 58;
    $r2     = $r1 + 68;
    $y1     = 20;
    $this->SetXY( $r1, $y1);
    $this->MultiCell( 60, 4, $adresse);
}

function addReglement( $mode )
{
    $r1  = 10;
    $r2  = $r1 + 60;
    $y1  = 40;
    $y2  = $y1+10;
	$this->SetFillColor(255, 179, 179);
    $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'FD');
    $this->SetXY( $r1 + ($r2-$r1)/2 - 5 , $y1 + 3 );
    $this->SetFont( "Arial", "B", 12);
    $this->Cell(10,4, "DATE: ".$mode , 0, 0, "C");
}

function addNumTVA($tva)
{
    $this->SetFont( "Arial", "B", 12);
    $r1  = $this->w - 80;
    $r2  = $r1 + 70;
    $y1  = 40;
    $y2  = $y1+10;
	$this->SetFillColor(255, 179, 179);
    $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'FD');
    $this->SetXY( $r1 + 16 , $y1 + 3 );
    $this->Cell(40, 4, "AMOUNT IN RUPEES: ".$tva, '', '', "C");
}

function addCols( $tab )
{
    global $colonnes;
    $this->SetFont( "Arial", "B", 12);
    $r1  = 10;
    $r2  = $this->w - ($r1 * 2) ;
    $y1  = 80;
    $y2  = $this->h - 203 - $y1;
    $this->SetXY( $r1, $y1 );
    $this->Line( $r1, $y1+6, $r1+$r2, $y1+6);
    $colX = $r1;
    $colonnes = $tab;
    while ( list( $lib, $pos ) = each ($tab) )
    {
        $this->SetXY( $colX, $y1+2 );
		$this->SetTextColor(102, 0, 0);
        $this->Cell( $pos, 1, $lib, 0, 0, "C");
        $colX += $pos;
    }
}

function addLineFormat( $tab )
{
    global $format, $colonnes;
    
    while ( list( $lib, $pos ) = each ($colonnes) )
    {
        if ( isset( $tab["$lib"] ) )
            $format[ $lib ] = $tab["$lib"];
    }
}

function addLine( $ligne, $tab )
{
    global $colonnes, $format;

    $ordonnee     = 10;
    $maxSize      = $ligne;

    reset( $colonnes );
    while ( list( $lib, $pos ) = each ($colonnes) )
    {
        $longCell  = $pos -2;
        $texte     = $tab[ $lib ];
        $length    = $this->GetStringWidth( $texte );
        $tailleTexte = $this->sizeOfText( $texte, $length );
        $formText  = $format[ $lib ];
        $this->SetXY( $ordonnee, $ligne-1);
        $this->MultiCell( $longCell, 4 , $texte, 0, $formText);
        if ( $maxSize < ($this->GetY()  ) )
            $maxSize = $this->GetY() ;
        $ordonnee += $pos;
    }
    return ( $maxSize - $ligne );
}

function total($total)
{
    $this->SetFont( "Arial", "B", 12);
    $r1  = $this->w - 80;
    $r2  = $r1 + 70;
    $y1  = 130;
    $y2  = $y1+10;
	$this->SetFillColor(255, 179, 179);
    $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'FD');
    $this->SetXY( $r1 + 16 , $y1 + 3 );
    $this->Cell(40, 4, "TOTAL: ".$total, '', '', "C");
}

function payment($payment)
{
    $this->SetFont( "Arial", "B", 12);
    $r1  = $this->w - 80;
    $r2  = $r1 + 70;
    $y1  = 150;
    $y2  = $y1+10;
	$this->SetFillColor(255, 179, 179);
    $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'FD');
    $this->SetXY( $r1 + 16 , $y1 + 3 );
    $this->Cell(40, 4, "PAYMENT MADE: ".$payment, '', '', "C");
}

function balance($balance)
{
    $this->SetFont( "Arial", "B", 12);
    $r1  = $this->w - 80;
    $r2  = $r1 + 70;
    $y1  = 170;
    $y2  = $y1+10;
	$this->SetFillColor(102, 0, 0);
	$this->SetTextColor(255, 255, 255);
    $this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'FD');
    $this->SetXY( $r1 + 16 , $y1 + 3 );
    $this->Cell(40, 4, "BALANCE DUE: ".$balance, '', '', "C");
}

function thanks( $thanks )
{
	$this->SetFont( "Arial", "B", 16);
	$this->SetTextColor(102, 0, 0);
    $r1  = 25;
    $y1  = 130;
    $this->SetXY( $r1, $y1);
    $this->Cell(40, 4, $thanks , '', '', "C");
}

function lastblock( $lastblock )
{
	$this->SetFont( "Arial", "B", 12);
	$this->SetTextColor(102, 0, 0);
    $r1  = 20;
    $y1  = 210;
    $this->SetXY( $r1, $y1);
    $this->Cell(100, 4, $lastblock , '', '', "C");
}

function lastblock2( $lastblock2 )
{
	$this->SetFont( "Arial", "B", 12);
	$this->SetTextColor(102, 0, 0);
    $r1  = 100;
    $y1  = 260;
    $this->SetXY( $r1, $y1);
    $this->Cell(100, 4, $lastblock2 , '', '', "C");
}
}
?>
