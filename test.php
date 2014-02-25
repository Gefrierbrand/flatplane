<?php

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

	// set auto page breaks
	$pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__file__) . '/lang/eng.php'))
	{
		require_once (dirname(__file__) . '/lang/eng.php');
		$pdf->setLanguageArray($l);
	}


	// set default font subsetting mode
	$pdf->setFontSubsetting(true);

	$pdf->AddPage();

    $pdf->setRasterizeVectorImages(false);
    //$pdf->Write(1,'test');

    $pdf->ImageSVG('test.svg', $x=5, $y=100, $w=200, $h=100, $link='', $align='', $palign='', $border=0, $fitonpage=false);
	// ---------------------------------------------------------

	$pdf->Output('output/example_001.pdf', 'F');

?>