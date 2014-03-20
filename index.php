<?php
/* 
 * Copyright (C) 2014 Nikolai Neff
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
$beginn = microtime(true);

	require_once ('tcpdf-code/tcpdf.php');
	date_default_timezone_set('Europe/Berlin');

	// create new PDF document
    //$orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Nikolai Neff');
	$pdf->SetTitle('Titel des Dokuments');
	$pdf->SetSubject('Thema');
	$pdf->SetKeywords('KEYWORD1, keyword2');

	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

	// set auto page breaks
	$pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


	// set default font subsetting mode
	$pdf->setFontSubsetting(true);

	$pdf->AddPage();

    $pdf->setRasterizeVectorImages(false);
    $path= dirname(__FILE__);
    $tex = '\mathcal{F}(f)(t) = \frac{1}{\left(2\pi\right)^{\frac{n}{2}}} \int_{\mathbb{R}^n} f(x)\,e^{-\mathrm{i} t \cdot x} \,\mathrm{d} x';
    $tex2 = '\int_a^b(f(x)+c)\,\mathrm dx=\int_a^b f(x)\,\mathrm dx+(b-a)\cdot c';
    $tex3 = 'Z = \sum_{i=1}^{n} a_i~;~~~a_i = k_i \cdot b^i~;~~~b=2~;~~~k_i \in \{0,1\}~;~~~i\in \mathbb{N}';
    $tex4 = '\overline{\overline{\left(A\, \wedge\, B\right)}\, \wedge\, C} \neq\overline{ A\, \wedge\, \overline{\left(B\, \wedge\,C \right)}}';
    
    $dauer = microtime(true) - $beginn;
    echo "Preparing page: $dauer Sek. \n";
    
    /*
    * Available Fonts:
    * TeX, STIX-Web, Asana-Math, Neo-Euler, Gyre-Pagella, Gyre-Termes and Latin-Modern
    */
    
    $cmd = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --font 'TeX' '$tex'");
    $cmd2 = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --font 'STIX-Web' '$tex'");
    $cmd3 = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --font 'Asana-Math' '$tex'");
    $cmd4 = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --font 'Neo-Euler' '$tex'");
    $cmd5 = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --font 'Gyre-Pagella' '$tex'");
    $cmd6 = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --font 'Gyre-Termes' '$tex'");
    $cmd7 = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --font 'Latin-Modern' '$tex'");
    
    //TODO: check for valid result
    
    $svgdata = '@'.shell_exec($cmd);
    $svgdata2 = '@'.shell_exec($cmd2);
    $svgdata3 = '@'.shell_exec($cmd3);
    $svgdata4 = '@'.shell_exec($cmd4);
    $svgdata5 = '@'.shell_exec($cmd5);
    $svgdata6 = '@'.shell_exec($cmd6);
    $svgdata7 = '@'.shell_exec($cmd7);
    
    
    $dauer = microtime(true) - $beginn;
    echo "After Jax: $dauer Sek. \n";
    
    $pdf->setFontSubsetting(false);
    $pdf->SetFont('Times', '', 14);
    
    $pdf->Text(0, 5, 'Font: TeX');
    $pdf->ImageSVG($svgdata, $x=5, $y=10, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->Text(0, 35, 'Font: STIX-Web');
    $pdf->ImageSVG($svgdata2, $x=5, $y=40, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->Text(0, 65, 'Font: Asana-Math');
    $pdf->ImageSVG($svgdata3, $x=5, $y=70, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->Text(0, 95, 'Font: Neo-Euler');
    $pdf->ImageSVG($svgdata4, $x=5, $y=100, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->Text(0, 125, 'Font: Gyre-Pagella');
    $pdf->ImageSVG($svgdata5, $x=5, $y=130, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->Text(0, 155, 'Font: Gyre-Termes');
    $pdf->ImageSVG($svgdata6, $x=5, $y=160, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->Text(0, 185, 'Font: Latin-Modern');
    $pdf->ImageSVG($svgdata7, $x=5, $y=190, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    
    $pdf->Text(0, 0, date('d.m.Y H:i:s'));
    
    $dauer = microtime(true) - $beginn;
    echo "After Image/Text: $dauer Sek. \n";

    $pdf->Output('output/test.pdf', 'F');
    $dauer = microtime(true) - $beginn;
    echo "Verarbeitung des Skripts: $dauer Sek. \n";

?>