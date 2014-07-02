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
use de\flatplane\interfaces\documentElements\FormulaInterface;
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
 * @todo: use general layout function for image/formula/etc
 * @author Nikolai Neff <admin@flatplane.de>
 */
class PageLayout
{
    use \de\flatplane\documentElements\traits\NumberingFunctions;

    protected $linearPageNumberCounter;
    protected $currentYPosition;
    protected $currentPageGroup = 'default';
    protected $newPageGroup;
    protected $document;

    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
        $pdf = $document->getPDF();
        //set bookmark for first page as document root
        $pdf->Bookmark($document->getTitle(), 0, -1, 1);

        //add a sequential page counter
        $this->linearPageNumberCounter = new Counter(0);

        //initialise the page counter for the default page group
        $startValue = $this->getDocument()->getPageNumberStartValue('default');
        $counter = new Counter($startValue);
        $this->addCounter($counter, 'default');

        //set first Page Y Position:
        //todo: use Document / Page Margins ?
        $this->setCurrentYPosition($pdf->getMargins()['top']);
    }

    /**
     * layout each DocumentElement according to its type
     * @throws RuntimeException
     */
    public function layout()
    {
        $content = $this->getDocument()->getContent();

        $recItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($recItIt as $pageElement) {

            //change the current pagegroup according to the sections settings
            //if the sections pagegroup is 'default', then it will adapt to the
            //currently active pagegroup
            if ($pageElement->getPageGroup() !== 'default') {
                $this->newPageGroup = true;
                $this->setCurrentPageGroup($pageElement->getPageGroup());
            } else {
                $this->newPageGroup = false;
                $pageElement->setPageGroup($this->getCurrentPageGroup());
            }

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
     */
    protected function incrementPageNumber()
    {
        $document = $this->getDocument();
        $pageGroup = $this->getCurrentPageGroup();

        //add a new counter for each new pagegroup or increment the already
        //existing counter for old pagegroups
        if (array_key_exists($pageGroup, $this->getCounterArray())) {
            if (!$this->newPageGroup) {
                $this->getCounter($pageGroup)->add();
            }
        } else {
            throw new RuntimeException(
                'Required counter '.$pageGroup.' does not exist'
            );
        }

        //increment the linar page Number
        //does not use the page-group-counter array to avoid collisions
        //with user counters
        $this->getLinearPageNumberCounter()->add();

        //reset Y position to top of page
        $this->setCurrentYPosition($document->getPageMargins('top'));

        //return the current grouped counter value as formatted number
        //return $this->getCurrentFormattedPageNumber($pageGroup);
    }

    /**
     *
     * @return Counter
     */
    protected function getLinearPageNumberCounter()
    {
        return $this->linearPageNumberCounter;
    }

    /**
     * fixme: return numeric here and format in list display
     * @param string $pageGroup
     * @return string
     */
    protected function getCurrentFormattedPageNumber($pageGroup = 'default')
    {
        $number = new Number($this->getCounter($pageGroup)->getValue());
        $pageNumStyle = $this->getDocument()->getPageNumberStyle($pageGroup);
        return $number->getFormattedValue($pageNumStyle);
    }

    /**
     * todo: doc
     * @param SectionInterface $section
     * @param type $useCurrentPagePosition
     */
    protected function setSectionPageAndLink(
        SectionInterface $section,
        $useCurrentPagePosition = false
    ) {
        $pdf = $this->getDocument()->getPDF();

        if ($useCurrentPagePosition) {
            $fontSize = $pdf->getFontSize();
            $yPos = $this->getCurrentYPosition() - $pdf->getCellHeight($fontSize);
        } else {
            $yPos = 0;
        }

        $section->setPage(
            $this->getCounter($this->getCurrentPageGroup())->getValue()
        );
        $section->setLinearPage($this->getLinearPageNumber());
        $pdfpageNum = $section->getLinearPage() + 1;
        $pdf->Bookmark(
            $section->getNonHyphenTitle(),
            $section->getLevel(),
            $yPos,
            $pdfpageNum
        );
        $section->setLink($pdf->AddLink());
        $pdf->SetLink($section->getLink(), $yPos, $pdfpageNum);
    }

    /**
     * todo: doc
     * @param SectionInterface $section
     */
    protected function layoutSection(SectionInterface $section)
    {
        //if the section is not shown in the document, only set the current
        //pagenumber and return (this can be used used to add entries to the
        //TOC without adding something visible in the document)
        if ($section->getShowInDocument() == false) {
            $this->setSectionPageAndLink($section);
            return;
        }

        //check if the section forces a new page
        if ($section->getStartsNewPage('level'.$section->getLevel())) {
            Flatplane::log("Section: ($section) requires pagebreak [user]");
            //add the page
            $this->incrementPageNumber();
            //set the sections page properties and add links/bookmarks
            $this->setSectionPageAndLink($section);

            //set the Y position on the new Page to the end of the Section
            //this assumes a section title fits (comfortably) on one page
            $sectionSize = $section->getSize($this->getCurrentYPosition());
            $this->setCurrentYPosition($sectionSize['endYposition']);
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
            $this->incrementPageNumber();
        }

        //check if the section title fits on the page
        $sectionSize = $section->getSize($this->getCurrentYPosition());
        if ($sectionSize['numPages'] > 1) {
            //automatic page break occured, so increment page counter
            //todo: add appropriate amount of pages instead of just one
            Flatplane::log("section: ($section) requires pagebreak [size]");
            $this->incrementPageNumber();
        }

        //set the current page for the current section
        $this->setSectionPageAndLink($section, true);

        //set the y position to the end of the section
        $this->setCurrentYPosition($sectionSize['endYposition']);

//        $numPageBreaks = $sectionSize['numPages'] - 1;
//        echo "section ($section): adding $numPageBreaks\n";
//        $this->getCounter($this->getCurrentPageGroup())->add($numPageBreaks);
//        $this->getLinearPageNumberCounter()->add($numPageBreaks);
    }

    /**
     *
     * @return float
     */
    protected function getAvailableSpace()
    {
        $pageSize = $this->getDocument()->getPageSize();
        $pageMarginBottom = $this->getDocument()->getPageMargins('bottom');

        $availableSpace = $pageSize['height']
                        - $pageMarginBottom
                        - $this->getCurrentYPosition();
        return $availableSpace;
    }

    /**
     *
     * @param ImageInterface $image
     */
    protected function layoutImage(ImageInterface $image)
    {
        $pdf = $this->getDocument()->getPDF();
        //check free space on current page
        $availableVerticalSpace = $this->getAvailableSpace();

        //check if the image fits on the page
        $imageSize = $image->getSize($this->getCurrentYPosition());
        if ($imageSize['height'] > $availableVerticalSpace
            || $imageSize['numPages'] > 1
        ) {
            Flatplane::log("Image: ($image) requires pagebreak [size]");
            $this->incrementPageNumber();
        }

        $this->setCurrentYPosition($imageSize['endYposition']);

        //set the current page for the current section
        $image->setPage(
            $this->getCounter($this->getCurrentPageGroup())->getValue()
        );
        $image->setLinearPage($this->getLinearPageNumber());

        //add linkt target for list of figures
        $image->setLink($pdf->AddLink());
        $pdf->SetLink($image->getLink(), 0, $image->getLinearPage() + 1);
    }

    /**
     *
     * @param \FormulaInterface$formula
     */
    protected function layoutFormula(FormulaInterface $formula)
    {
        $pdf = $this->getDocument()->getPDF();
        //check free space on current page
        $availableVerticalSpace = $this->getAvailableSpace();

        //check if the image fits on the page
        $formulaSize = $formula->getSize($this->getCurrentYPosition());
        if ($formulaSize['height'] > $availableVerticalSpace
            || $formulaSize['numPages'] > 1
        ) {
            Flatplane::log("Formula: ($formula) requires pagebreak [size]");
            $this->incrementPageNumber();
        }
        $this->setCurrentYPosition($formulaSize['endYposition']);

        //set the current page for the current section
        $formula->setPage(
            $this->getCounter($this->getCurrentPageGroup())->getValue()
        );
        $formula->setLinearPage($this->getLinearPageNumber());

        //add linkt target for list of figures
        $formula->setLink($pdf->AddLink());
        $pdf->SetLink($formula->getLink(), 0, $formula->getLinearPage() + 1);
    }

    /**
     *
     * @param TextInterface $text
     */
    protected function layoutText(TextInterface $text)
    {
        $textSize = $text->getSize($this->getCurrentYPosition());
        $this->setCurrentYPosition($textSize['endYposition']);

        //set the current page for the current section
        $text->setPage(
            $this->getCounter($this->getCurrentPageGroup())->getValue()
        );
        $text->setLinearPage($this->getLinearPageNumber());

        $numPageBreaks = $textSize['numPages'] - 1;
        $this->getCounter($this->getCurrentPageGroup())->add($numPageBreaks);
        $this->getLinearPageNumberCounter()->add($numPageBreaks);
    }

    protected function layoutCode(TextInterface $code)
    {
        $this->layoutText($code);
    }

    protected function layoutList(ListInterface $list)
    {
        $listSize = $list->getSize($this->getCurrentYPosition());

        $this->setCurrentYPosition($listSize['endYposition']);

        //set the current page for the current section
        $list->setPage(
            $this->getCounter($this->getCurrentPageGroup())->getValue()
        );
        $list->setLinearPage($this->getLinearPageNumber());

        $numPageBreaks = $listSize['numPages'] - 1;
        $this->getCounter($this->getCurrentPageGroup())->add($numPageBreaks);
        $this->getLinearPageNumberCounter()->add($numPageBreaks);
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

        //add Counter for pagegroup if not already existing
        if (!array_key_exists($this->currentPageGroup, $this->getCounterArray())) {
            $startValue = $this->getDocument()->getPageNumberStartValue(
                $this->currentPageGroup
            );
            $counter = new Counter($startValue);
            $this->addCounter($counter, $this->currentPageGroup);
            //echo "adding counter for: {$this->currentPageGroup}\n";
        }
    }
}
