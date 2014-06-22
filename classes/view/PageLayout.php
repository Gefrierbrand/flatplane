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
use de\flatplane\utilities\Counter;
use de\flatplane\utilities\Number;
use RuntimeException;

/**
 * Description of PageLayout
 * @todo: use abstract class and/or factory for layout?
 * @author Nikolai Neff <admin@flatplane.de>
 */
class PageLayout
{
    use \de\flatplane\documentElements\traits\NumberingFunctions;

    protected $currentYPosition; //fixme: usage?
    protected $currentPageGroup;
    protected $document;

    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
        $content = $document->getContent();
        $pdf = $document->getPDF();
        //todo: check for headers?
        //add first page (still empty, but sets y position)
        $pdf->addPage();

        //add a sequential page counter
        $this->addCounter(new Counter(1), 'linearPageNumberCounter');

        //layout each element according to its type
        foreach ($content as $pageElement) {
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
        //fixme: add as property to avoid collisions with userCounters
        $this->getCounter('linearPageNumberCounter')->add();

        //return the Counters value as formatted Number
        return $this->getCurrentPageNumber($pageGroup);
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
            $this->addPage();
            return;
        }

        //check free space on current page
        $availableVerticalSpace = $this->getAvailableSpace();
        $textHeight = $this->getDocument()->getPageMeasurements()['textHeight'];

        //calculate minimum space needed to start a section on the current page
        $percentage = $section->getMinFreePage('level'.$section->getLevel())/100;
        $minSpace = $percentage*$textHeight;

        //add a new page if needed
        if ($minSpace < $availableVerticalSpace) {
            $this->addPage();
        }

        //set the current page for the current section
        $section->setPage($this->getCurrentPageNumber($section->getPageGroup()));
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

    protected function getLinearPageNumber()
    {
        return $this->getCounter('linearPageNumberCounter')->getValue();
    }

    /**
     * todo: use direct value from pdf?
     * @return type
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
    }
}
