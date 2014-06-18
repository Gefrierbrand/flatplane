<?php
/*
 * Copyright (C) 2014 Nikolai Neff <admin@flatplane.de>.
 *
 * This file is part of Flatplane.
 *
 * Flatplane is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or(at your option) any later version.
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

use de\flatplane\documentElements\AbstractDocumentContentElement;
use de\flatplane\interfaces\documentElements\SectionInterface;
use \RuntimeException;

/**
 * Description of section
 * TODO: doc!
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Section extends AbstractDocumentContentElement implements SectionInterface
{
    protected $type = 'section';
    protected $title = 'section';
    protected $showInDocument = true;
    protected $minFreePage = ['default' => 25];
    protected $startsNewLine = ['default' => true];
    protected $startsNewPage = ['default' => false];

    protected $numberSeparationWidth = 1.5;

    public function setConfig(array $config)
    {
        if (!array_key_exists('altTitle', $config)) {
            $config['altTitle'] = '';
        }
        parent::setConfig($config);
    }

    public function __toString()
    {
        return (string) $this->getAltTitle();
    }

    public function getShowInDocument()
    {
        return $this->showInDocument;
    }

    public function getMinFreePage($level = 0)
    {
        if (isset($this->minFreePage[$level])) {
            return $this->minFreePage[$level];
        } elseif (isset($this->minFreePage['default'])) {
            return $this->minFreePage['default'];
        } else {
            throw new RuntimeException(
                'The required property minFreePage is not set.'
            );
        }
    }

    public function getStartsNewLine($level = 0)
    {
        if (isset($this->startsNewLine[$level])) {
            return $this->startsNewLine[$level];
        } elseif (isset($this->startsNewLine['default'])) {
            return $this->startsNewLine['default'];
        } else {
            throw new RuntimeException(
                'The required property minFreePage is not set.'
            );
        }
    }

    public function getStartsNewPage($level = 0)
    {
        if (isset($this->startsNewPage[$level])) {
            return $this->startsNewPage[$level];
        } elseif (isset($this->startsNewPage['default'])) {
            return $this->startsNewPage['default'];
        } else {
            throw new RuntimeException(
                'The required property minFreePage is not set.'
            );
        }
    }

    protected function setShowInDocument($showInDocument)
    {
        $this->showInDocument = (bool) $showInDocument;
    }

    protected function setMinFreePage(array $minFreePage)
    {
        $this->minFreePage = array_merge($this->minFreePage, $minFreePage);
    }

    protected function setStartsNewLine($startsNewLine)
    {
        $this->startsNewLine = (bool) $startsNewLine;
    }

    protected function setStartsNewPage($startsNewPage)
    {
        $this->startsNewPage = (bool) $startsNewPage;
    }

    public function getSize()
    {
        $pdf = $this->toRoot()->getPdf();
        $pdf->startMeasurement(false);
        $this->generateOutput();
        return $pdf->endMeasurement(false);
    }

    /**
     * todo: doc
     */
    public function generateOutput()
    {
        $pdf = $this->toRoot()->getPdf();
        //save old pagemargins
        $oldMargins = $pdf->getMargins();
        //adjust left and right margins according tho the elements settings
        $pdf->SetLeftMargin($oldMargins['left']+$this->getMargins('left'));
        $pdf->SetRightMargin($oldMargins['right']+$this->getMargins('right'));

        //add element top margins to current y-position
        $pdf->SetY($pdf->GetY()+$this->getMargins('top'));

        //set font size, color etc.
        $this->applyStyles();

        //display a number, if neccesary
        if ($this->getEnumerate()) {
            //calculate the formatted numbers width
            $numWidth = $pdf->GetStringWidth($this->getFormattedNumbers());
            //add the number-title separation distance
            $numWidth += $pdf->getHTMLUnitToUnits(
                $this->getNumberSeparationWidth(),
                $pdf->getFontSize(),
                'em'
            );
            //add internal cell paddings (default to 0)
            $numWidth += $pdf->getCellPaddings()['L']
                         + $pdf->getCellPaddings()['R'];
            //output numbers
            $pdf->Cell($numWidth, 0, $this->getFormattedNumbers());
        } else {
            $numWidth = 0;
        }

        //set xposition for title
        $pdf->SetX($oldMargins['left'] + $numWidth + $this->getMargins('left'));

        //output title (might be more than one line)
        $pdf->MultiCell(0, 0, $this->getTitle(), 0, 'L');

        //rest page margins
        $pdf->SetLeftMargin($oldMargins['left']);
        //workaround for TCPDF Bug #940:
        $pdf->SetX($oldMargins['left']);
        $pdf->SetRightMargin($oldMargins['right']);

        //add bottom margin to y-position
        $pdf->SetY($pdf->GetY()+$this->getMargins('bottom'));
    }

    public function applyStyles()
    {
        $pdf = $this->toRoot()->getPdf();
        $level = 'level'.$this->getLevel();
        $pdf->SetFont(
            $this->getFontType($level),
            $this->getFontStyle($level),
            $this->getFontSize($level)
        );
        $pdf->setColorArray('text', $this->getFontColor($level));
        $pdf->setColorArray('draw', $this->getDrawColor($level));
        $pdf->setColorArray('fill', $this->getFillColor($level));
        $pdf->setFontSpacing($this->getFontSpacing($level));
        $pdf->setFontStretching($this->getFontStretching($level));

        $cellMargins = $this->getCellMargins();
        $pdf->setCellMargins(
            $cellMargins['left'],
            $cellMargins['top'],
            $cellMargins['right'],
            $cellMargins['bottom']
        );
        $cellPaddings = $this->getCellPaddings();

        $pdf->setCellPaddings(
            $cellPaddings['left'],
            $cellPaddings['top'],
            $cellPaddings['right'],
            $cellPaddings['bottom']
        );
    }

    public function getNumberSeparationWidth()
    {
        return $this->numberSeparationWidth;
    }

    protected function setNumberSeparationWidth($numberSeparationWidth)
    {
        $this->numberSeparationWidth = $numberSeparationWidth;
    }
}
