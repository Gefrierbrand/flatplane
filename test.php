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


	// set default font subsetting mode
	$pdf->setFontSubsetting(true);

	$pdf->AddPage();

    $pdf->setRasterizeVectorImages(false);
    $path= dirname(__FILE__);
    $tex = '\mathcal{F}(f)(t) = \frac{1}{\left(2\pi\right)^{\frac{n}{2}}} \int_{\mathbb{R}^n} f(x)\,e^{-\mathrm{i} t \cdot x} \,\mathrm{d} x';
    $tex2 = '\int_a^b(f(x)+c)\,\mathrm dx=\int_a^b f(x)\,\mathrm dx+(b-a)\cdot c';
    $tex3 = 'Z = \sum_{i=1}^{n} a_i~;~~~a_i = k_i \cdot b^i~;~~~b=2~;~~~k_i \in \{0,1\}~;~~~i\in \mathbb{N}';
    $tex4 = '\overline{\overline{\left(A\, \wedge\, B\right)}\, \wedge\, C} \neq\overline{ A\, \wedge\, \overline{\left(B\, \wedge\,C \right)}}';
    
    $cmd = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --display '$tex'");
    $cmd2 = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --display '$tex2'");
    $cmd3 = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --display '$tex3'");
    $cmd4 = escapeshellcmd($path.DIRECTORY_SEPARATOR.'phantomjs'.DIRECTORY_SEPARATOR."phantomjs.exe $path".DIRECTORY_SEPARATOR."jax.js --display '$tex4'");
        
    $svgdata = '@'.shell_exec($cmd);
    $svgdata2 = '@'.shell_exec($cmd2);
    $svgdata3 = '@'.shell_exec($cmd3);
    $svgdata4 = '@'.shell_exec($cmd4);
    
    $pdf->ImageSVG($svgdata, $x=5, $y=10, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->ImageSVG($svgdata2, $x=5, $y=40, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->ImageSVG($svgdata3, $x=5, $y=70, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);
    $pdf->ImageSVG($svgdata4, $x=5, $y=100, $w=200, $h=20, $link='', $align='', $palign='', $border=0, $fitonpage=false);

    $pdf->Output('output/test.pdf', 'F');
    $dauer = microtime(true) - $beginn;
    echo "Verarbeitung des Skripts: $dauer Sek.";
?>