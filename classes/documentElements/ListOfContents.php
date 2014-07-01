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

use de\flatplane\interfaces\documentElements\ListInterface;
use de\flatplane\iterators\RecursiveContentIterator;
use de\flatplane\iterators\ShowInListFilterIterator;
use OutOfRangeException;
use RecursiveIteratorIterator;

/**
 * Description of ListOfContent
 * todo: generate lists for individual sections, not just the whole document
 * todo: entry-margins
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ListOfContents extends AbstractDocumentContentElement implements ListInterface
{
    /**
     * @var int
     *  type of the element
     */
    protected $type = 'list';

    protected $title = '';
    /**
     * @var mixed
     *  use a bool to completely allow/disallow subcontent for the element or
     *  define allowed types as array values: e.g. ['section', 'formula']
     */
    protected $allowSubContent = false;

    /**
     * @var bool
     *  indicates if the element can be split across multiple pages
     */
    protected $isSplitable = true;

    /**
     * @var int
     *  Determines to wich level inside the documenttree the
     *  contents are displayed inside the list. The document is at level -1.
     *  Contents given on the "top level" are therefore at depth 0.
     *  The relative depth might differ from the contents level-property,
     *  as subtrees can also be processed by this function.
     *  Use -1 for unlimited depth.
     */
    protected $maxDepth = -1;

    /**
     * @var array
     *  Array containing the content-types to be included in the list.
     *  For example use ['image', 'table'] to list all images and all tables
     *  wich have their 'showInList' property set to true.
     */
    protected $displayTypes = ['section'];

    /**
     * @var bool
     *  Toggle the display of page-numbers
     */
    protected $showPages = true;

    /**
     * @var array(bool)
     *  Toggle lines from the entry to its page for each level
     */
    protected $drawLinesToPage = ['default' => true];


    /**
     * @var bool
     *  toggle Hyphenation
     */
    protected $hyphenate = true;


    /**
     * @var array
     *  Array of linestyles for each level. Valid keys are:
     *  <ul><li>mode:
     *      <ul><li>solid: a continuous line will be drawn</li>
     *          <li>dotted: a line of dots with spacing <i>distance</i>
     *              and size <i>size</i> (in pt) will be used
     *          <li>dashed: a line of dashes with spacing <i>distance</i>
     *              and size <i>size</i> (in pt) will be used
     *      </ul></li>
     *      <li>distance: space between dots or dashes in pt</li>
     *      <li>color: array of 1, 3 or 4 elements for grayscale, RGB or CMYK</li>
     *      <li>size: font-size to use for points or dashes in pt</li>
     *  </ul>
     */
    protected $lineStyle = ['default' => ['mode' => 'solid',
                                          'distance' => 0,
                                          'color' => [0, 0, 0],
                                          'size' => 1]];

    /**
     * @var array
     *  todo: doc
     *  set maxlevel to 0 to disable indent
     */
    protected $indent = ['maxLevel' => -1, 'mode' => 'relative', 'amount' => 10];

    /**
     * @var float
     *  minimum distance between the right end of the index-element and the left
     *  end of the page number-column in user units
     */
    protected $minPageNumDistance = 15;

    /**
     * @var float
     *  Minmum width reserved for the actual display of the titles in the list
     *  in percent of the lists width (which currently equals the textWidth
     *  of the page)
     */
    protected $minTitleWidthPercentage = 20;

    /**
     * @var float
     *  Width of the pageNumber column in user units
     */
    protected $pageNumberWidth = 8;

    /**
     * @var float
     *  approx. space between the numbering and the text in the list. (in em)
     */
    protected $numberSeparationWidth = 1;

    /**
     * Array containig the lists raw data for outputting
     * @var array
     */
    protected $data = [];

    /**
     * Generates a new list of arbitrary content elements. Used to create a
     * Table of Contents (TOC) or List of Figures (LOF) and so on.
     * @param array $config
     *  Array containing key=>value pairs of configuration and style options
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function __toString()
    {
        return (string) 'List of: '. implode(', ', $this->getDisplayTypes());
    }

    /**
     * todo: order by: level, structure, content-type
     * This method traverses the document-tree and filters for the desired
     * contenttypes to be displayed. It then generates an array corresponding to
     * a line in the finished list.
     * @param array $content
     *  Array containing objects implementing DocumentElementInterface
     * @return array
     *  Array with information for each line: formatted Number, absolute and
     *  relative depth, Text determined by the elements altTitle property
     */
    public function generateStructure(array $content = [])
    {
        //todo: validate content type and parent
        if (empty($content)) {
            $content = $this->toRoot()->getContent();
            //trigger_error('no content to generate structure for', E_USER_NOTICE);
        }

        $RecItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $RecItIt->setMaxDepth($this->getMaxDepth());

        $FilterIt = new ShowInListFilterIterator(
            $RecItIt,
            $this->getDisplayTypes()
        );

        $key = 0; //todo: order/sorting!
        foreach ($FilterIt as $element) {
            // current iteration depth
            $this->data[$key]['iteratorDepth'] = $RecItIt->getDepth();
            // element depth regarding document structure
            $this->data[$key]['level'] = $element->getLevel();
            if ($element->getEnumerate()) {
                $this->data[$key]['numbers'] = $element->getFormattedNumbers();
            } else {
                $this->data[$key]['numbers'] = '';
            }
            //use the alternative title (if available) for list entries
            $this->data[$key]['text'] = $element->getAltTitle();
            $this->data[$key]['page'] = $element->getPage();
            $this->data[$key]['link'] = $element->getLink();
            $key ++;
        }

        //fixme: return?
        return $this->data;
    }

    public function getSize($startYposition = null)
    {
        if (empty($this->getData())) {
            $this->generateStructure($this->toRoot()->getContent());
        }
        return parent::getSize($startYposition);
    }

    /**
     * todo: doc
     * todo: element-margins (left/right)
     */
    public function generateOutput()
    {
        if (empty($this->getData())) {
            $this->generateStructure();
        }
        $pdf = $this->toRoot()->getPDF();
        $startPage = $pdf->getPage();
        $textWidth = $this->getPageMeasurements()['textWidth'];

        //save old pagemargins from before listoutput
        $oldMargins = $pdf->getMargins();
        $oldCellMargins = $pdf->getCellMargins();
        $oldCellPaddings = $pdf->getCellPaddings();

        //adjust left and right margins according tho the elements settings
        //todo: implement this fully:
        //$pdf->SetLeftMargin($oldMargins['left']+$this->getMargins('left'));
        //$pdf->SetRightMargin($oldMargins['right']+$this->getMargins('right'));

        //add element top margins to current y-position
        $pdf->SetY($pdf->GetY()+$this->getMargins('top'));

        //todo: validate amounts
        $indentAmounts = $this->calculateIndentAmounts();

        //calculate minimum titleWidth
        $minTitleWidth = $this->getMinTitleWidthPercentage()/100*$textWidth;

        //adjust cell paddings to zero to prevent them from interfering
        //with the layout.
        $pdf->SetCellPaddings(0, '', 0); //left, top, right

        $i = 0;
        //display each individual item as line(s) with indent
        foreach ($this->getData() as $line) {
            //get all indent amounts for the current level
            $textIndent = $indentAmounts[$line['iteratorDepth']]['text'];
            $numberIndent = $indentAmounts[$line['iteratorDepth']]['number'];

            //calculate the maximum available space for the title-display
            $maxTitleWidth = $textWidth
                             - $this->getPageNumberWidth()
                             - $this->getMinPageNumDistance()
                             - $textIndent;

            if ($maxTitleWidth < $minTitleWidth) {
                trigger_error(
                    'The remaining space for the title-display of '
                    .$maxTitleWidth.' is lower than the set minimum of '
                    .$minTitleWidth,
                    E_USER_WARNING
                );
                //todo: reduce indent amounts etc ?
            }

            $this->applyStyles('level'.$line['iteratorDepth']);

            //x-positions for numbers and text
            $textXPos = $textIndent+$pdf->getMargins()['left'];
            $numXPos = $numberIndent+$pdf->getMargins()['left'];

            //add a margin of about one line before each entry on level 0 for
            //better visual chapter differentiation
            if ($line['iteratorDepth'] == 0 && $i != 0) {
                $topMargin = $pdf->getCellHeight($pdf->getFontSize());
            } else {
                $topMargin = 0;
            }

            //set left/right cellmargins to zero and set topmargin for level0
            $pdf->setCellMargins(0, $topMargin, 0);

            //display number for the entry
            $pdf->Text($numXPos, $pdf->GetY(), $line['numbers']);

            //calculate and set new margins to use correct text-wrapping
            //for entries longer than one line
            $leftLineMargin = $textXPos;

            //set the pdf pagemargins for the indentation
            $pdf->SetLeftMargin($leftLineMargin);
            $rightLineMargin = $oldMargins['right']
                                + $this->getPageNumberWidth()
                                + $this->getMinPageNumDistance();

            $pdf->SetRightMargin($rightLineMargin);

            //write line content
            $pdf->SetX($textXPos);
            if (isset($line['link'])) {
                $pdf->Write(0, $line['text'], $line['link']);
            } else {
                $pdf->Write(0, $line['text']);
            }

            //draw line or dots to pages, as the x-position is now behind the
            //last char of the title
            if ($this->getShowPages()) {
                $this->printLineToPages($textWidth, $oldMargins);

                //calculate position of page-numbers
                $pageNumXPos = $textWidth
                                + $oldMargins['left']
                                - $this->getPageNumberWidth();

                //reset pagemargins to allow text printing on the right side
                $pdf->SetMargins(
                    $oldMargins['left'],
                    $oldMargins['top'],
                    $oldMargins['right']
                );

                //print page numbers
                $pdf->SetX($pageNumXPos);
                $pdf->Cell(0, 0, $line['page'], 0, 1, 'R');
            } else {
                //Set internal cursor to next line. This is needed to be able
                //to use cell-paddings and margins for vertical distances
                $pdf->Ln();
            }

            //reset page margins to original value
            $pdf->SetMargins(
                $oldMargins['left'],
                $oldMargins['top'],
                $oldMargins['right']
            );

            //workaround for TCPDF Bug #940: not needed here?
            //$pdf->SetX($oldMargins['left']);

            $i++; //increment element counter
        }
        //reset cell-margins: (keys are set this way by TCPDF and therefore
        //differ from the usual keys), splat operator (php 5.6) can't be used
        //either as it only works with numeric indices
        $pdf->setCellMargins(
            $oldCellMargins['L'],
            $oldCellMargins['T'],
            $oldCellMargins['R'],
            $oldCellMargins['B']
        );
        $pdf->setCellPaddings(
            $oldCellPaddings['L'],
            $oldCellPaddings['T'],
            $oldCellPaddings['R'],
            $oldCellPaddings['B']
        );

        //add bottom margin to y-position
        $pdf->SetY($pdf->GetY()+$this->getMargins('bottom'));
        return $pdf->getPage() - $startPage;
    }

    protected function printLineToPages($textWidth, $oldMargins)
    {
        $pdf = $this->toRoot()->getPDF();
        //calculate space left for dots to pagenumber
        $dotsXStartPos = $pdf->GetX();
        $dotsXEndPos = $textWidth
                        + $oldMargins['left']
                        - $this->getPageNumberWidth();
        $dotsDelta = $dotsXEndPos - $dotsXStartPos;

        //todo: use line-options (solid, dots, none, ...)

        //generate string of dots and spaces
        $s = '';
        //approximate space to leave at right end of title before dots start
        $spaceCorrection = $pdf->GetStringWidth('  ');
        do {
            $s .= ' .';
        } while ($pdf->GetStringWidth($s) < $dotsDelta - $spaceCorrection);

        //print dots right-aligned
        $pdf->Cell($dotsDelta, 0, $s, 0, 0, 'R');
    }

    /**
     * todo: doc
     * number könnte mehrstellig sein, daher nötig alle zu testen und dies vor
     * der ausgabe zu messen
     * @return array(float)
     */
    protected function calculateIndentAmounts()
    {
        $data = $this->getData();
        $pdf = $this->toRoot()->getPDF();
        $maxItDepth = 0;
        $longestNumberWidth = [];
        $indentAmounts = [];
        //loop through all lines of the list and save the size of the longest
        //string for each depth
        foreach ($data as $line) {
            if ($this->getIndent()['maxLevel'] != -1
                && $line['iteratorDepth'] > $this->getIndent()['maxLevel']
            ) {
                //end iteration if the level of the line is deeper than the
                //maximum indentation-level
                break;
            }

            //use the longest string from each nunbering level to determine
            //the min space needed for the indentation. The width will change
            //with different fonts and fontsizes etc, so apply the specific
            //styles first.
            $this->applyStyles('level'.$line['iteratorDepth']);
            $strWidth = $pdf->GetStringWidth($line['numbers']);

            if (!isset($longestNumberWidth[$line['iteratorDepth']])
                || $strWidth > $longestNumberWidth[$line['iteratorDepth']]
            ) {
                $longestNumberWidth[$line['iteratorDepth']] = $strWidth;
            }

            //calculate the distance from the numbers to the text.
            $numDist[$line['iteratorDepth']] = $pdf->getHTMLUnitToUnits(
                $this->getNumberSeparationWidth(),
                $pdf->getFontSize(),
                'em'
            );

            if ($line['iteratorDepth'] > $maxItDepth) {
                $maxItDepth = $line['iteratorDepth'];
            }
        }

        //calculate indetamounts based on the current depth and the previous
        //indent amounts
        for ($i=0; $i <= $maxItDepth; $i++) {
            //FIXME: offsets for deeper levels!
            //workaround to supress error:
            if (!isset($longestNumberWidth[$i])) {
                $longestNumberWidth[$i] = 0;
            }
            if (!isset($numDist[$i])) {
                $numDist[$i] = 0;
            }

            if (isset($indentAmounts[$i-1]['text'])) {
                $indentAmounts[$i]['text'] = $indentAmounts[$i-1]['text']
                                           + $longestNumberWidth[$i]
                                           + $numDist[$i];
                //numbers of the current level are aligned with the text of the
                //previous level
                $indentAmounts[$i]['number'] = $indentAmounts[$i-1]['text'];
            } else {
                $indentAmounts[$i]['text'] = $longestNumberWidth[$i]
                                           + $numDist[$i];
                $indentAmounts[$i]['number'] = 0;
            }
        }

        return $indentAmounts;
    }

    public function getDisplayTypes()
    {
        return $this->displayTypes;
    }

    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    public function setMaxDepth($maxDepth)
    {
        //cast to int as all settings from ini files are returned as strings
        $maxDepth = (int) $maxDepth;
        if ($maxDepth < -1) {
            trigger_error('Invalid Max depth, defaulting to -1', E_USER_NOTICE);
            $maxDepth = -1;
        }
        $this->maxDepth = $maxDepth;
    }

    public function setDisplayTypes(array $displayTypes)
    {
        $this->displayTypes = $displayTypes;
    }

    public function setIndent(array $indent)
    {
        $this->indent = array_merge($this->indent, $indent);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getShowPages()
    {
        return $this->showPages;
    }

    public function getDrawLinesToPage($key = null)
    {
        if ($key !== null && isset($this->drawLinesToPage[$key])) {
            return $this->drawLinesToPage[$key];
        } else {
            return $this->drawLinesToPage['default'];
        }
    }

    public function getLineStyle($key = null)
    {
        if ($key !== null && isset($this->lineStyle[$key])) {
            return $this->lineStyle[$key];
        } else {
            return $this->lineStyle['default'];
        }
    }

    public function setShowPages($showPages)
    {
        $this->showPages = $showPages;
    }

    public function setDrawLinesToPage(array $drawLinesToPage)
    {
        $this->drawLinesToPage = array_merge(
            $this->drawLinesToPage,
            $drawLinesToPage
        );
    }

    public function setLineStyle(array $lineStyle)
    {
        $this->lineStyle = array_merge($this->lineStyle, $lineStyle);
    }

    public function getMinPageNumDistance()
    {
        return $this->minPageNumDistance;
    }

    public function setMinPageNumDistance($minPageNumDistance)
    {
        $this->minPageNumDistance = $minPageNumDistance;
    }

    public function getIndent()
    {
        if ($this->indent['maxLevel'] < -1) {
            throw new OutOfRangeException('maxLevel can\'t be smaller than -1');
        }
        return $this->indent;
    }

    public function getMinTitleWidthPercentage()
    {
        return $this->minTitleWidthPercentage;
    }

    public function getPageNumberWidth()
    {
        return $this->pageNumberWidth;
    }

    public function setMinTitleWidthPercentage($minTitleWidthPercentage)
    {
        $this->minTitleWidthPercentage = $minTitleWidthPercentage;
    }

    public function setPageNumberWidth($pageNumberWidth)
    {
        $this->pageNumberWidth = $pageNumberWidth;
    }

    public function getNumberSeparationWidth()
    {
        return $this->numberSeparationWidth;
    }

    public function setNumberSeparationWidth($numberSeparationWidth)
    {
        $this->numberSeparationWidth = $numberSeparationWidth;
    }

    public function getAltTitle()
    {
        return (string) $this;
    }
}
