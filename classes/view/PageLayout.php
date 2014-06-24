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

namespace de\flatplane\view;

use de\flatplane\interfaces\documentElements\DocumentInterface;
use de\flatplane\interfaces\documentElements\SectionInterface;
use de\flatplane\iterators\RecursiveContentIterator;
use de\flatplane\utilities\Counter;
use de\flatplane\utilities\Number;
use RecursiveIteratorIterator;
use RuntimeException;

/**
 * Description of PageLayout
 * @todo: use abstract class and/or factory for layout?
 * @author Nikolai Neff <admin@flatplane.de>
 */
class PageLayout
{
    use \de\flatplane\documentElements\traits\NumberingFunctions;

    protected $linearPageNumberCounter;
    protected $currentYPosition;
    protected $currentPageGroup;
    protected $document;

    public function __construct(DocumentInterface $document)
    {
        //add a sequential page counter
        $this->linearPageNumberCounter = new Counter(0);

        $this->document = $document;
        $content = $document->getContent();
        $pdf = $document->getPDF();
        //todo: check for headers?

        //set first Page Y Position:
        $this->setCurrentYPosition($pdf->getMargins()['top']);


        //layout each element according to its type
        $recItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($recItIt as $pageElement) {
            $type = $pageElement->getType();
            $methodName = 'layout'.ucfirst($type);
            if (method_exists($this, $methodName)) {
                $this->$methodName($pageElement);
            } else {
                throw new RuntimeException('Invalid element type "'.$type.'"');
            }
        }
    }

    /**
     * Increments the pageCounter according to the current page group
     * @return int
     */
    protected function addPage()
    {
        $document = $this->getDocument();
        $pageGroup = $this->getCurrentPageGroup();
        $pageNumStartValue = $document->getPageNumberStartValue($pageGroup);

        //add a new counter for each new pagegroup or increment the already
        //existing counter for old pagegroups
        if (!array_key_exists($pageGroup, $this->getCounter())) {
            $this->addCounter(new Counter($pageNumStartValue), $pageGroup);
        } else {
            $this->getCounter($pageGroup)->add();
        }

        //increment the linar page Number
        //does not use the counter array to avoid collisions with user counters
        $this->getLinearPageNumberCounter()->add();

        //return the current grouped counter value as formatted number
        return $this->getCurrentPageNumber($pageGroup);
    }

    protected function getLinearPageNumberCounter()
    {
        return $this->linearPageNumberCounter;
    }

    /**
     * fixme: return numeric here and format in list display
     * @param string $pageGroup
     * @return string
     */
    protected function getCurrentPageNumber($pageGroup = 'default')
    {
        $number = new Number($this->getCounter($pageGroup)->getValue());
        $pageNumStyle = $this->getDocument()->getPageNumberStyle($pageGroup);
        return $number->getFormattedValue($pageNumStyle);
        //return $this->getCounter($pageGroup)->getValue();
    }

    /**
     *
     * @param SectionInterface $section
     */
    protected function layoutSection(SectionInterface $section)
    {
        //change the current pagegroup according to the sections settings
        $this->setCurrentPageGroup($section->getPageGroup());

        //if the section is not shown in the document, only set the current
        //pagenumber and return (this can be used used to add entries to the
        //TOC without adding something visible in the document)
        if ($section->getShowInDocument() == false) {
            $section->setPage(
                $this->getCurrentPageNumber($section->getPageGroup())
            );
            return;
        }

        //check, if the section forces a new page
        if ($section->getStartsNewPage('level'.$section->getLevel())) {
            echo "section adds new Page\n";
            $this->addPage();
            $section->setPage($this->getCurrentPageNumber($section->getPageGroup()));
            $section->setLinearPage($this->getLinearPageNumber());
            return;
        }

        //check free space on current page
        $availableVerticalSpace = $this->getAvailableSpace();
        $textHeight = $this->getDocument()->getPageMeasurements()['textHeight'];

        //calculate minimum space needed to start a section on the current page
        $percentage = $section->getMinFreePage('level'.$section->getLevel())/100;
        $minSpace = $percentage*$textHeight;

        //add a new page if needed (minspace includes the space needed for the
        //section title itself)
        if ($availableVerticalSpace < $minSpace) {
            echo "section adds new page due to space: Min: $minSpace Avail: $availableVerticalSpace\n";
            $this->addPage();
        }

        //check if the section title fits on the page
        $sectionSize = $section->getSize($this->getCurrentYPosition());
        if ($sectionSize['numPages'] > 1) {
            //automatic page break occured, so increment page counter
            //todo: add appropriate amount of pages instead of just one
            $this->addPage();
        }

        //set the current page for the current section
        $section->setPage($this->getCurrentPageNumber($section->getPageGroup()));
        $section->setLinearPage($this->getLinearPageNumber());
    }

    protected function getAvailableSpace()
    {
        $pageSize = $this->getDocument()->getPageSize();
        $pageMargins = $this->getDocument()->getPageMargins();

        $availableSpace = $pageSize['height']
                        - $pageMargins['bottom']
                        - $this->getCurrentYPosition();
        return $availableSpace;
    }

    protected function layoutImage()
    {

    }

    protected function layoutFormula()
    {

    }

    protected function layoutText()
    {

    }

    protected function layoutList()
    {

    }

    protected function layoutTable()
    {

    }

    protected function layoutSource()
    {

    }

    /**
     * @return int
     */
    protected function getLinearPageNumber()
    {
        return $this->getLinearPageNumberCounter()->getValue();
    }

    /**
     * @return float
     */
    protected function getCurrentYPosition()
    {
        return $this->currentYPosition;
    }

    protected function getDocument()
    {
        return $this->document;
    }

    protected function setCurrentYPosition($currentYPosition)
    {
        $this->currentYPosition = $currentYPosition;
    }

    protected function getCurrentPageGroup()
    {
        return $this->currentPageGroup;
    }

    protected function setCurrentPageGroup($currentPageGroup)
    {
        $this->currentPageGroup = $currentPageGroup;
        //add Counter for pagrgroup if not already existing
        if (!array_key_exists($currentPageGroup, $this->getCounter())) {
            $startValue = $this->getDocument()->getStartIndex('page');
            $counter = new Counter($startValue);
            $this->addCounter($counter, $currentPageGroup);
        }
    }
}
