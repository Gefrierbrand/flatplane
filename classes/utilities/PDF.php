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

namespace de\flatplane\utilities;

use de\flatplane\documentElements\Footnote;
use de\flatplane\interfaces\documentElements\DocumentInterface;
use TCPDF;

/**
 * Description of myPDF
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class PDF extends TCPDF
{
    protected $measureStartY;
    protected $measureStartPage;

    protected $leftHeader;
    protected $rightHeader;
    protected $leftFooter;
    protected $rightFooter;

    protected $pageNumberStyle = 'int';

    /**
     *
     * @var Number
     */
    protected $pageNumber;
    protected $footnoteObjects = array();
    protected $footnoteCounter;
    protected $firstFootnoteOnPage = true;

    protected $defaultBottomMargin = 20;

    public function __construct(
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = true,
        $encoding = 'UTF-8',
        $diskcache = false,
        $pdfa = false
    ) {
        //todo: use page-number start-index /off by -1
        $this->pageNumber = new Number(0);
        $this->footnoteCounter = new Counter();
        parent::__construct(
            $orientation,
            $unit,
            $format,
            $unicode,
            $encoding,
            $diskcache,
            $pdfa
        );
        $this->tcpdflink = false;
    }

    /**
	 * @return float
     *  Current height of page in user unit.
	 */
    public function getH()
    {
        return $this->h;
    }

    public function setDefaultBottomMargin($margin)
    {
        $this->defaultBottomMargin = $margin;
    }

    public function getDefaultBottomMargin()
    {
        return $this->defaultBottomMargin;
    }

    public function startMeasurement($startYPosition = null)
    {
        $this->startTransaction();

        $this->AddPage();

        //set the vertical position to a given starting value
        if (is_numeric($startYPosition)) {
            $this->SetY($startYPosition);
        }

        $this->measureStartY = $this->GetY();
        $this->measureStartPage = $this->getPage();
    }

    /**
     * todo: doc
     * @param type $rollback
     * @return array height of transaction in user units & number of pages needed
     */
    public function endMeasurement($rollback = true)
    {
        $endYPosition = $this->GetY();
        $endPageNum = $this->getPage();
        // calculate height
        $height = 0;

        if ($endPageNum == $this->measureStartPage) {
            $height = $endYPosition - $this->measureStartY;
        } else {
            for ($page = $this->measureStartPage; $page <= $endPageNum; ++$page) {
                $this->setPage($page);
                if ($page == $this->measureStartPage) {
                    // first page
                    $height += $this->getH()
                        - $this->measureStartY
                        - $this->getMargins()['bottom'];
                } elseif ($page == $endPageNum) {
                    // last page
                    $height += $endYPosition - $this->getMargins()['top'];
                } else {
                    // other pages
                    $height += $this->getH()
                        - $this->getMargins()['top']
                        - $this->getMargins()['bottom'];
                }
            }
        }

        //todo: use start transaction page?
        //todo: use num pagebreaks instead (don't add +1)
        $numPages = ($endPageNum - $this->measureStartPage) + 1;

        if ($rollback) {
            $this->rollbackTransaction(true);
        } else {
            $this->commitTransaction();
        }

        return ['height' => $height,
                'numPages' => $numPages,
                'endYposition' => $endYPosition];
    }

    public function getLeftHeader()
    {
        return $this->leftHeader;
    }

    public function getRightHeader()
    {
        return $this->rightHeader;
    }

    public function setLeftHeader($leftHeader)
    {
        $this->leftHeader = $leftHeader;
    }

    public function setRightHeader($rightHeader)
    {
        $this->rightHeader = $rightHeader;
    }

    public function getLeftFooter()
    {
        return $this->leftFooter;
    }

    public function getRightFooter()
    {
        return $this->rightFooter;
    }

    public function setLeftFooter($leftFooter)
    {
        $this->leftFooter = $leftFooter;
    }

    public function setRightFooter($rightFooter)
    {
        $this->rightFooter = $rightFooter;
    }

    protected function incrementPageNumber()
    {
        $this->pageNumber->setValue($this->pageNumber->getValue() + 1);
        $this->setRightFooter(
            $this->pageNumber->getFormattedValue($this->getPageNumberStyle())
        );
    }

    public function Header()
    {
        //parent::Header();
        $width = $this->getPageWidth()
                 - $this->getMargins()['left']
                 - $this->getMargins()['right'];
        $this->Cell($width/2, 0, $this->getLeftHeader(), 'B', 0, 'L', false, '', 1);
        $this->Cell($width/2, 0, $this->getRightHeader(), 'B', 0, 'R', false, '', 1);
    }

    public function Footer()
    {
        $width = $this->getPageWidth()
                 - $this->getMargins()['left']
                 - $this->getMargins()['right'];
        $this->Cell($width/2, 0, $this->getLeftFooter(), 'T', 0, 'L', false, '', 1);
        $this->Cell($width/2, 0, $this->getRightFooter(), 'T', 0, 'R', false, '', 1);

        $this->firstFootnoteOnPage = true;

        if (!empty($this->footnoteObjects)) {
            $this->displayFootnotes();
        } else {
            //reset page margins
            $this->resetBottomMargin();
        }
    }

    public function resetBottomMargin()
    {
        $this->SetAutoPageBreak(
            $this->getAutoPageBreak(),
            $this->defaultBottomMargin
        );
        $this->firstFootnoteOnPage = true;
    }

    protected function displayFootnotes()
    {
        $oldX = $this->GetX();
        $oldY = $this->GetY();

        $x = $this->getMargins()['left'];
        $y = $this->getPageHeight() - $this->getMargins()['bottom'];

        //reset page margins
        $this->SetAutoPageBreak(
            $this->getAutoPageBreak(),
            $this->defaultBottomMargin
        );

        //set xy
        $this->SetXY($x, $y);

        //calculate separation line width (given in percentage of textwidth)
        $textwidth = $this->getPageWidth() - $x - $this->getMargins()['right'];
        $key = key($this->footnoteObjects);
        $separationLineWidth =
            ($this->footnoteObjects[$key]->getSeparatorLineWidth()/100)
            * $textwidth;

        //display footnotes
        $this->Line($this->getMargins()['left'], $y, $x + $separationLineWidth, $y);
        $this->SetY($y + $this->footnoteObjects[$key]->getSeparatorLineVerticalmargin());

        //remove already displayed footnotes from footnote-array
        foreach ($this->footnoteObjects as $key => $footnote) {
            $footnote->generateOutput();
            unset($this->footnoteObjects[$key]);
        }

        //reset xy
        $this->SetXY($oldX, $oldY);
    }

    public function AddPage(
        $orientation = '',
        $format = '',
        $keepmargins = false,
        $tocpage = false
    ) {
        $this->incrementPageNumber();
        parent::AddPage($orientation, $format, $keepmargins, $tocpage);
    }

    public function getPageNumberStyle()
    {
        return $this->pageNumberStyle;
    }

    public function setPageNumberStyle($pageNumberStyle)
    {
        $this->pageNumberStyle = $pageNumberStyle;
    }

    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    public function setPageNumber(Number $pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    public function addFootnote(Footnote $footnote)
    {
        $number = $this->footnoteCounter->add();
        $footnote->setNumber($number);
        $this->footnoteObjects[] = $footnote;

        $this->increaseBottomMargin($footnote);
        return $number;
    }

    public function increaseBottomMargin(Footnote $footnote)
    {
        $bottomMargin = $this->getMargins()['bottom'];
        $amount = $footnote->getHeight();
        $lineDistance = $footnote->getSeparatorLineVerticalmargin();

        if ($this->firstFootnoteOnPage) {
            $amount += $lineDistance;
            $this->firstFootnoteOnPage = false;
        }

        //set new bottom margin
        $this->SetAutoPageBreak(
            $this->getAutoPageBreak(),
            $bottomMargin + $amount
        );
    }
}
