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

    protected $currentYPosition;
    protected $document;

    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
        $content = $document->getContent();

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
     * @param SectionInterface $section
     * @return string
     * todo: don't rely on section:
     * -> properties: current section, current pagegroup, ggf current section of level(x)
     */
    protected function addPage(SectionInterface $section)
    {
        $document = $this->getDocument();
        $pageGroup = $section->getPageGroup();
        $pageNumStartValue = $document->getPageNumberStartValue($pageGroup);

        //add a new counter for each new pagegroup or increment the already
        //existing counter for old pagegroups
        if (!array_key_exists($pageGroup, $this->getCounter())) {
            $this->addCounter(new Counter($pageNumStartValue), $pageGroup);
        } else {
            $this->getCounter($pageGroup)->add();
        }

        //increment the linar page Number
        $this->getCounter('linearPageNumberCounter')->add();

        //return the Counters value as formatted Number
        return $this->getCurrentPageNumber($pageGroup);
    }

    protected function getCurrentPageNumber($pageGroup = 'default')
    {
        $number = new Number($this->getCounter($pageGroup)->getValue());
        $pageNumStyle = $this->getDocument()->getPageNumberStyle($pageGroup);
        return $number->getFormattedValue($pageNumStyle);
    }

    protected function layoutSection(SectionInterface $section)
    {
        //check if a page exists
        //todo: move this in foreach loop
        //todo: make it work
        $this->checkForFirstPage();

        //if the section is not shown in the document, only set the current
        //pagenumber and return (this can be used used to add entries to the
        //TOC without adding something visible in the document)
        if ($section->getShowInDocument() == false) {
            $section->setPage($this->getCurrentPageNumber($section->getPageGroup()));
            return;
        }

        //check free space on current page
        $pageSize = $this->getDocument()->getPageSize();
        $pageMargins = $this->getDocument()->getPageMargins();
        $textHeight = $this->getDocument()->getPageMeasurements()['textHeight'];

        $availableVerticalSpace = $pageSize['height']
                                  - $pageMargins['bottom']
                                  - $this->getCurrentYPosition();

        //calculate minimum space needed to start a section on the current page
        $percentage = $section->getMinFreePage('level'.$section->getLevel())/100;
        $minSpace = $percentage*$textHeight;

        //add a new page if needed
        if ($minSpace < $availableVerticalSpace) {
            $this->addPage($section);
        } else { //set the section on the current page
            $section->setPage(
                $this->getCurrentPageNumber($section->getPageGroup())
            );
        }
    }

    protected function checkForFirstPage()
    {
        //todo: implement
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

    public function getLinearPageNumber()
    {
        return $this->getCounter('linearPageNumberCounter')->getValue();
    }

    public function getCurrentYPosition()
    {
        return $this->currentYPosition;
    }

    public function getDocument()
    {
        return $this->document;
    }

    protected function setCurrentYPosition($currentYPosition)
    {
        $this->currentYPosition = $currentYPosition;
    }
}
