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

    public function __construct(
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = true,
        $encoding = 'UTF-8',
        $diskcache = false,
        $pdfa = false
    ) {
        $this->pageNumber = new Number(0);
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
        $endPageNumber = $this->getPage();
        // calculate height
        $height = 0;

        if ($endPageNumber == $this->measureStartPage) {
            $height = $endYPosition - $this->measureStartY;
        } else {
            for ($page = $this->measureStartPage; $page <= $endPageNumber; ++$page) {
                $this->setPage($page);
                if ($page == $this->measureStartPage) {
                    // first page
                    $height += $this->getH()
                        - $this->measureStartY
                        - $this->getMargins()['bottom'];
                } elseif ($page == $endPageNumber) {
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
        $numPages = ($endPageNumber - $this->measureStartPage) + 1;

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

//    public function getFormattedPageNumber()
//    {
//        return $this->formattedPageNumber;
//    }
//
//    public function setFormattedPageNumber($formattedPageNumber)
//    {
//        $this->formattedPageNumber = $formattedPageNumber;
//    }

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
        $this->Cell($width/2, 0, $this->getLeftHeader(), 'B');
        $this->Cell($width/2, 0, $this->getRightHeader(), 'B', 0, 'R');
    }

    public function Footer()
    {
        //parent::Footer();
        $width = $this->getPageWidth()
                 - $this->getMargins()['left']
                 - $this->getMargins()['right'];
        $this->Cell($width/2, 0, $this->getLeftFooter(), 'T');
        $this->Cell($width/2, 0, $this->getRightFooter(), 'T', 0, 'R');
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
}
