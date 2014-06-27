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

use de\flatplane\controller\Flatplane;
use de\flatplane\interfaces\documentElements\DocumentInterface;
use de\flatplane\interfaces\documentElements\ImageInterface;
use de\flatplane\interfaces\documentElements\ListInterface;
use de\flatplane\interfaces\documentElements\SectionInterface;
use de\flatplane\interfaces\documentElements\TextInterface;
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
    protected $currentPageGroup = 'default';
    protected $document;

    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
        $pdf = $document->getPDF();
        //set bookmark for first page as dokument root
        $pdf->Bookmark($document->getTitle(), 0, -1, 1);

        //add a sequential page counter
        $this->linearPageNumberCounter = new Counter(0);

        //initialise the page counter for the default page group
        $startValue = $this->getDocument()->getPageNumberStartValue('default');
        $counter = new Counter($startValue);
        $this->addCounter($counter, 'default');

        //set first Page Y Position:
        //todo: use Document / Page Margins
        $this->setCurrentYPosition($pdf->getMargins()['top']);
    }

    public function layout()
    {
        $content = $this->getDocument()->getContent();

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
    protected function addPage($newPageGroup = false)
    {
        $document = $this->getDocument();
        $pageGroup = $this->getCurrentPageGroup();

        //add a new counter for each new pagegroup or increment the already
        //existing counter for old pagegroups
        if (array_key_exists($pageGroup, $this->getCounterArray())) {
            if (!$newPageGroup) {
                $this->getCounter($pageGroup)->add();
            }
        } else {
            throw new RuntimeException(
                'Required counter '.$pageGroup.' does not exist'
            );
        }

        //increment the linar page Number
        //does not use the counter array to avoid collisions with user counters
        $this->getLinearPageNumberCounter()->add();

        //reset Y position to top of page
        $this->setCurrentYPosition($document->getPageMargins('top'));

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
     * @todo: redundanz entfernen!
     * @param SectionInterface $section
     */
    protected function layoutSection(SectionInterface $section)
    {
        //change the current pagegroup according to the sections settings

        $newPagegroup = false;
        if ($section->getPageGroup() !== 'default') {
            $newPagegroup = true;
            $this->setCurrentPageGroup($section->getPageGroup());
        }

        //if the section is not shown in the document, only set the current
        //pagenumber and return (this can be used used to add entries to the
        //TOC without adding something visible in the document)
        if ($section->getShowInDocument() == false) {
            $section->setPage(
                $this->getCurrentPageNumber($section->getPageGroup())
            );
            //todo: test this, also : linear page number?
            //$this->getDocument()->getPDF()->Bookmark($section->getTitle(), $section->getLevel(), 0, $section->getLinearPage()+1);
            return;
        }

        //check if the section forces a new page
        if ($section->getStartsNewPage('level'.$section->getLevel())) {
            Flatplane::log("Section: ($section) requires pagebreak [user]");
            //add the page
            $this->addPage($newPagegroup);
            //set the sections page properties
            $section->setPage(
                $this->getCurrentPageNumber(
                    $section->getPageGroup()
                )
            );
            $section->setLinearPage($this->getLinearPageNumber());
            //set the Y position on the new Page to the end of the Section
            //this assumes a section title fits (comfortably) on one page
            $sectionSize = $section->getSize($this->getCurrentYPosition());
            $this->setCurrentYPosition($sectionSize['endYposition']);
            //echo "1 adding bookmark for $section: {$section->getLevel()}\n";
            $this->getDocument()->getPDF()->Bookmark($section->getNonHyphenTitle(), $section->getLevel(), 0, $section->getLinearPage()+1);
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
            Flatplane::log("Section: ($section) requires pagebreak [MinSpace]");
            $this->addPage($newPagegroup);
        }

        //check if the section title fits on the page
        //echo "testingSize from {$this->getCurrentYPosition()} for $section".PHP_EOL;
        $sectionSize = $section->getSize($this->getCurrentYPosition());
        $this->setCurrentYPosition($sectionSize['endYposition']);
        if ($sectionSize['numPages'] > 1) {
            //automatic page break occured, so increment page counter
            //todo: add appropriate amount of pages instead of just one
            Flatplane::log("section: ($section) requires pagebreak [size]");
            $this->addPage($newPagegroup);
        }

        //set the current page for the current section
        $section->setPage(
            $this->getCurrentPageNumber(
                $this->getCurrentPageGroup()
            )
        );
        $section->setLinearPage($this->getLinearPageNumber());
        //echo "2 adding bookmark for $section: {$section->getLevel()}\n";
        $this->getDocument()->getPDF()->Bookmark($section->getNonHyphenTitle(), $section->getLevel(), 0, $section->getLinearPage()+1);
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

    protected function layoutImage(ImageInterface $image)
    {
        //check free space on current page
        $availableVerticalSpace = $this->getAvailableSpace();

        //check if the image fits on the page
        $imageSize = $image->getSize($this->getCurrentYPosition());
        if ($imageSize['height'] > $availableVerticalSpace
            || $imageSize['numPages'] > 1
        ) {
            Flatplane::log("Image: ($image) requires pagebreak [size]");
            $this->addPage();
        }

        $this->setCurrentYPosition($imageSize['endYposition']);

        //set the current page for the current section
        $image->setPage($this->getCurrentPageNumber($this->getCurrentPageGroup()));
        $image->setLinearPage($this->getLinearPageNumber());
    }

    protected function layoutFormula()
    {

    }

    protected function layoutText(TextInterface $text)
    {
        $textSize = $text->getSize($this->getCurrentYPosition());
        $this->setCurrentYPosition($textSize['endYposition']);

        //set the current page for the current section
        $text->setPage($this->getCurrentPageNumber($this->getCurrentPageGroup()));
        $text->setLinearPage($this->getLinearPageNumber());


        $this->getLinearPageNumberCounter()->add($textSize['numPages']-1);
    }

    protected function layoutList(ListInterface $list)
    {
        //check free space on current page
        $availableVerticalSpace = $this->getAvailableSpace();

        //check if the image fits on the page
        $listSize = $list->getSize($this->getCurrentYPosition());
        if ($listSize['height'] > $availableVerticalSpace
            || $listSize['numPages'] > 1
        ) {
            Flatplane::log("Image: ($list) requires pagebreak [size]");
            $this->addPage();
        }

        $this->setCurrentYPosition($listSize['endYposition']);

        //set the current page for the current section
        $list->setPage($this->getCurrentPageNumber($this->getCurrentPageGroup()));
        $list->setLinearPage($this->getLinearPageNumber());
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
//        echo "traying to set new pagegroup to: $currentPageGroup\n";
//
//        $this->oldPageGroup = $this->getCurrentPageGroup();
//        if ($this->oldPageGroup !== $currentPageGroup
//            && $currentPageGroup !== 'default'
//        ) {
//            $this->currentPageGroup = $currentPageGroup;
//            echo "setting!\n";
//        } else {
//            $this->currentPageGroup = $this->oldPageGroup;
//            echo "not setting, keeping at: {$this->oldPageGroup}\n";
//        }
        $this->currentPageGroup = $currentPageGroup;

        //add Counter for pagegroup if not already existing
        if (!array_key_exists($this->currentPageGroup, $this->getCounterArray())) {
            $startValue = $this->getDocument()->getPageNumberStartValue(
                $this->currentPageGroup
            );
            $counter = new Counter($startValue);
            $this->addCounter($counter, $this->currentPageGroup);
            echo "adding counter for: {$this->currentPageGroup}\n";
        }
    }
}
