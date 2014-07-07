<?php

/*
 * Copyright (C) 2014 Nikolai Neff <admin@flatplane.de>.
 *
 * This file is part of Flatplane.
 *
 * Flatplane is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Flatplane is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Flatplane.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace de\flatplane\documentElements;

use de\flatplane\interfaces\documentElements\DocumentInterface;
use de\flatplane\utilities\PDF;
use RuntimeException;

/**
 * Description of Footnote
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Footnote
{
    use \de\flatplane\documentElements\traits\StyleFunctions;

    protected $text;
    protected $number;

    protected $hyphenate = true;

    protected $separatorLineWidth = 30;
    protected $numberSeparationWidth = 0.8;
    protected $textAlignment = 'L';

    protected $pdf;
    protected $document;

    public function __construct(
        $text,
        $number,
        PDF $pdf,
        DocumentInterface $document,
        array $settings = []
    ) {
        $this->setText($text);
        $this->setDocument($document);
        $this->setNumber($number);

        foreach ($settings as $key => $setting) {
            $name = 'set'.ucfirst($key);
            if (method_exists($this, $name)) {
                $this->$name($setting);
            } else {
                trigger_error(
                    "$key is not a valid Configuration option, ignoring",
                    E_USER_NOTICE
                );
            }
        }
    }

    public function __toString()
    {
        return (string) "Footnote: {$this->getNumber()}: {$this->getText()} \n";
    }

    public function generateOutput()
    {
        if (!($this->getPdf() instanceof PDF)) {
            throw new RuntimeException(
                'The PDF Object must be set before generating footnote-output'
            );
        }
        $pdf = $this->getPdf();

        $this->applyStyles();
        $separationWidth = $pdf->getHTMLUnitToUnits(
            $this->getNumberSeparationWidth().'em',
            $pdf->getFontSize()
        );

        $html = '<sup>'.$this->getNumber().'</sup>';

        $pdf->writeHTML($html, false, false, true);
        $pdf->SetX($pdf->getMargins()['left'] + $separationWidth);

        $text = $this->getText();

        $pdf->MultiCell(0, 0, $text, 0, $this->getTextAlignment(), false, 1);
    }

    /**
     *
     * @return PDF
     */
    public function getPdf()
    {
        return $this->getDocument()->getPDF();
    }

    public function setPdf(PDF $pdf)
    {
        $this->pdf = $pdf;
    }

    public function getText()
    {
        if (!($this->getDocument() instanceof DocumentInterface)) {
            throw new RuntimeException(
                'The required Document-Object is not set for '.$this
            );
        }

        if ($this->getHyphenate()) {
            $text = $this->getDocument()->hypenateText($this->text);
        } else {
            $text = $this->text;
        }
        return $text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getNumber()
    {
        return $this->number;
    }

    /**
     *
     * @return array
     *  keys: 'height', 'numPages', 'endYposition';
     */
    public function getSize()
    {
        $pdf = $this->getPDF();
        $pdf->startMeasurement();
        $this->generateOutput();
        return $pdf->endMeasurement();
    }

    public function getHyphenate()
    {
        return $this->hyphenate;
    }

    public function getSeparatorLineWidth()
    {
        return $this->separatorLineWidth;
    }

    public function getNumberSeparationWidth()
    {
        return $this->numberSeparationWidth;
    }

    public function getTextAlignment()
    {
        return $this->textAlignment;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function setHyphenate($hyphenate)
    {
        $this->hyphenate = $hyphenate;
    }

    public function setSeparatorLineWidth($separatorLineWidth)
    {
        $this->separatorLineWidth = $separatorLineWidth;
    }

    public function setNumberSeparationWidth($numberSeparationWidth)
    {
        $this->numberSeparationWidth = $numberSeparationWidth;
    }

    public function setTextAlignment($textAlignment)
    {
        $this->textAlignment = $textAlignment;
    }

    /**
     * @return DocumentInterface
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     *
     * @param \DocumentInterface $document
     */
    public function setDocument(DocumentInterface $document)
    {
        $this->document = $document;
    }
}
