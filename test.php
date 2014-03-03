<?php
    $beginn = microtime(true);

	require_once ('tcpdf-code/tcpdf.php');
	date_default_timezone_set('Europe/Berlin');

	// create new PDF document
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

	// set some language-dependent strings (optional)
	if (file_exists(dirname(__file__) . '/lang/eng.php'))
	{
            require_once (dirname(__file__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
	}


	// set default font subsetting mode
	$pdf->setFontSubsetting(true);

	$pdf->AddPage();

    $pdf->setRasterizeVectorImages(false);
    $pdf->ImageSVG('test.svg', $x=5, $y=10, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->ImageSVG('test2.svg', $x=5, $y=40, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->ImageSVG('test3.svg', $x=5, $y=70, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->ImageSVG('test4.svg', $x=5, $y=100, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->ImageSVG('test5.svg', $x=5, $y=130, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->ImageSVG('test6.svg', $x=5, $y=160, $w=200, $h=40, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->ImageSVG('test7.svg', $x=5, $y=210, $w=200, $h=80, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->ImageSVG('test8.svg', $x=5, $y=250, $w=50, $h=80, $link='', $align='', $palign='', $border=0, $fitonpage=false);

    // ---------------------------------------------------------
    $pdf->Output('output/example_002.pdf', 'F');
    $dauer = microtime(true) - $beginn;
    echo "Verarbeitung des Skripts: $dauer Sek.";
?>