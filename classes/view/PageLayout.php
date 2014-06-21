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

use de\flatplane\interfaces\documentElements\SectionInterface;
use de\flatplane\utilities\Counter;
use de\flatplane\utilities\Number;
use RuntimeException;

/**
 * Description of PageLayout
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class PageLayout
{
    use \de\flatplane\documentElements\traits\NumberingFunctions;

    public function __construct(array $content)
    {
        //todo: add linarpageNumber?
        foreach ($content as $pageElement) {
            $type = $pageElement->getType();
            $methodName = 'layout'.  ucfirst($type);
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
     */
    protected function addPage(SectionInterface $section)
    {
        $document = $section->toRoot();
        $pageGroup = $section->getPageGroup();
        $pageNumStartValue = $document->getPageNumberStartValue($pageGroup);
        $pageNumStyle = $document->setPageNumberStyle($pageGroup);

        //add a new counter for each new pagegroup or increment the already
        //existing counter for old pagegroups
        if (!array_key_exists($pageGroup, $this->getCounter())) {
            $this->addCounter(new Counter($pageNumStartValue), $pageGroup);
        } else {
            $this->getCounter($pageGroup)->add();
        }
        //return the Counters value as formatted Number
        $number = new Number($this->getCounter($pageGroup));
        return $number->getFormattedValue($pageNumStyle);
    }

    protected function layoutSection(SectionInterface $section)
    {
        //check if a page already exists
        if (empty($this->getCurrentPageNumber())) {
            $this->addPage($section);
        }
        //check if section should be displayed
        if ($section->getShowInDocument() == false) {
            $section->setPage($this->getCurrentPageNumber());
            return;
        }

        //check free space on current page
        //todo: get remaining space
        $section->getMinFreePage('level'.$section->getLevel());
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

    public function getCurrentPageNumber()
    {
        //todo: get current page number for current page group
    }
}
