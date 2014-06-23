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
    protected $startsNewLine = ['default' => true]; //not implemented yet
    protected $startsNewPage = ['default' => false];

    /**
     * @var string
     *  identifier used to separate different pagegroups
     */
    protected $pageGroup = 'default';

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

    /**
     *
     * @param type $level
     * @return type
     *  minimum free percentage of textheight needed to start a section on
     *  the current page instead of a new one.
     * @throws RuntimeException
     * @throws \OutOfBoundsException
     */
    public function getMinFreePage($level = 0)
    {
        if (isset($this->minFreePage[$level])) {
            $minFree = $this->minFreePage[$level];
        } elseif (isset($this->minFreePage['default'])) {
            $minFree = $this->minFreePage['default'];
        } else {
            throw new RuntimeException(
                'The required property minFreePage is not set.'
            );
        }
        if ($minFree <=0) {
            throw new \OutOfBoundsException('MinFreePage must be greater than 0');
        }
        return $minFree;
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

    public function getStartsNewPage($level = 'level0')
    {
        if (isset($this->startsNewPage[$level])) {
            return $this->startsNewPage[$level];
        } elseif (isset($this->startsNewPage['default'])) {
            return $this->startsNewPage['default'];
        } else {
            var_dump($this);
            throw new RuntimeException(
                'The required property startsNewPage is not set.'
            );
        }
    }

    public function setShowInDocument($showInDocument)
    {
        $this->showInDocument = (bool) $showInDocument;
    }

    public function setMinFreePage(array $minFreePage)
    {
        $this->minFreePage = array_merge($this->minFreePage, $minFreePage);
    }

    public function setStartsNewLine(array $startsNewLine)
    {
        $this->startsNewLine = array_merge($this->startsNewLine, $startsNewLine);
    }

    public function setStartsNewPage(array $startsNewPage)
    {
        $this->startsNewPage = array_merge($this->startsNewPage, $startsNewPage);
    }

    /**
     * todo: doc
     */
    public function generateOutput()
    {
        $pdf = $this->toRoot()->getPDF();
        //save old pagemargins
        $oldMargins = $pdf->getMargins();
        //adjust left and right margins according tho the elements settings
        $pdf->SetLeftMargin($oldMargins['left']+$this->getMargins('left'));
        $pdf->SetRightMargin($oldMargins['right']+$this->getMargins('right'));

        //add element top margins to current y-position
        echo "current Y: {$pdf->GetY()} Adding: {$this->getMargins('top')}\n";
        $pdf->SetY($pdf->GetY()+$this->getMargins('top'));
        echo "new Y: {$pdf->GetY()}\n";

        //set font size, color etc.
        $this->applyStyles('level'.$this->getLevel());

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

    public function getNumberSeparationWidth()
    {
        return $this->numberSeparationWidth;
    }

    public function setNumberSeparationWidth($numberSeparationWidth)
    {
        $this->numberSeparationWidth = $numberSeparationWidth;
    }

    public function getPageGroup()
    {
        return $this->pageGroup;
    }

    public function setPageGroup($pageGroup)
    {
        $this->pageGroup = $pageGroup;
    }
}
