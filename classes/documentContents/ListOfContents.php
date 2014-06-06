<?php

/*
 * Copyright (C) 2014 Nikolai Neff <admin@flatplane.de>.
 *
 * This file is part of Flatplane.
 *
 * Flatplane is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or(at your option) any later version.
 *
 * Flatplane is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Flatplane.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace de\flatplane\documentContents;

use de\flatplane\interfaces\documentElements\ListInterface;
use de\flatplane\iterators\ShowInListFilterIterator;
use de\flatplane\iterators\RecursiveContentIterator;
use RecursiveIteratorIterator;

/**
 * Description of ListOfContent
 * todo: generate lists for individual sections, not the whole document
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ListOfContents extends AbstractDocumentContentElement implements ListInterface
{
    /**
     * @var int
     *  type of the element
     */
    protected $type = 'list';

    protected $title = 'list';
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
     * @var int
     *  depth of currently examined content line style
     */
    protected $contentStyleLevel = 0;

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
     *  in percent of the lists width (which currently equals the textwidth
     *  of the page)
     */
    protected $minTitleWidthPercentage = 20;

    /**
     * @var float
     *  Width of the pageNumber column in user units
     */
    protected $pageNumberWidth = 8;

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

    /**
     * todo: order by: level, structure, content-type
     * This method traverses the document-tree and filters for the desired
     * contenttypes to be displayed. It then generates an array corresponding to
     * a line in the finished list.
     * @param array $content
     *  Array containing objects implementing DocumentElementInterface
     * @return array
     *  Array with information for each line: formatted Number, absolute and
     *  relative depth, Text determined by the elements __toString() method.
     */
    public function generateStructure(array $content)
    {
        //todo: validate content type and parent
        if (empty($content)) {
            //$content = $this->getParent()->toRoot()->getContent();
            trigger_error('no content to generate structure for', E_USER_NOTICE);
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
                $this->data[$key]['numbers'] = null;
            }
            $this->data[$key]['text'] = $element->getAltTitle();
            $this->data[$key]['page'] = $element->getPage();
            $key ++;
        }

        //fixme: return?
        return $this->data;
    }

    public function getSize()
    {
        //todo: automatically generate structure here?
        if (empty($this->getData())) {
            throw new \RuntimeException(
                'The structure must be generated before calculating the size',
                E_USER_ERROR
            );

        }
        $indentAmounts = $this->calculateIndentAmounts();
        $this->measureOutput($indentAmounts);
    }

    protected function measureOutput($indentAmounts)
    {
        $pdf = $this->toRoot()->getPdf();
        $pdf->startMeasurement();

        $this->generatePseudoOutput($indentAmounts);

        list($height, $numpages) = $pdf->endMeasurement(false);
    }

    /**
     * todo: doc, move to sane place?
     * @param array $indentAmounts
     */
    protected function generatePseudoOutput(array $indentAmounts)
    {
        //todo: validate amounts
        $pdf = $this->toRoot()->getPdf();
        $textWidth = $this->getPageMeasurements()['textwidth'];
        foreach ($this->getData() as $line) {
            //add all indent amounts for the current level
            $totalIndentWidth = 0;
            for ($i=0; $i < $line['iteratorDepth']; $i++) {
                echo "i: $i; indentamount: {$indentAmounts[$i]}\n";
                $totalIndentWidth += $indentAmounts[$i];
            }
            //calculate the maximum available space for the title-display
            $maxTitleWidth = $textWidth
                             - $this->getPageNumberWidth()
                             - $this->getMinPageNumDistance()
                             - $totalIndentWidth;
            //calculate minimum titleWidth
            $minTitleWidth = $this->getMinTitleWidthPercentage()/100*$textWidth;
            if ($maxTitleWidth < $minTitleWidth) {
                trigger_error(
                    'The remaining space for the title-display of '
                    .$maxTitleWidth.' is lower than the set minimum of '
                    .$minTitleWidth,
                    E_USER_WARNING
                );
                //todo: reduce indent amounts etc
            }

            //set style according to depth
            $this->setContentStyleLevel($line['iteratorDepth']);
            $this->applyStyles();

            //print demo-output and check number of needed lines
            $numlines = $pdf->MultiCell(
                $maxTitleWidth, //cellwidth
                0, //cellheight: aotomatic
                $line['numbers'].$line['text'], //text
                1, //border
                'L', //text-alignment
                false, //fill
                1, //ln(): next line
                $totalIndentWidth+$pdf->getMargins()['left'] //x-position
            );

            //todo in actual output:
            //draw dots/lines to pagenum (in final version)
            //determine actual string width -> multicell getx danach?
        }
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
        $formattedNumberString = [];
        //loop through all lines of the list and save the highest formatted
        //number for each depth by overwriting the old number each iteration.
        foreach ($data as $line) {
            if ($this->getIndent()['maxLevel'] != -1
                && $line['iteratorDepth'] > $this->getIndent()['maxLevel']
            ) {
                //end iteration if the level of the line is deeper then the
                //maximum indentation depth
                break;
            }
            if ($line['numbers'] !== null) {
                //add two spaces to conform to DIN 5008, which requires at least
                //two spaces between a section number and its text (for top
                //level sections)
                //todo: add ability to customize amount
                $numbers = $line['numbers'].'  ';
            } else {
                //the number might keep empty for a specific level if the
                //enumerate-property of all contents on that depth is set to false.
                //therefore the numberingstring for those levels is set to two
                //spaces to keep indenting possible, unless the level in
                //question is at depth 0
                if ($line['iteratorDepth'] == 0) {
                    $numbers = '';
                } else {
                    $numbers = '  ';
                }
            }
            //add the lines numbers to the number list overwriting the last one
            //for each level
            //
            //FIXME: use longest string instead of highest number due to
            //prefix & separator etc.
            $formattedNumberString[$line['iteratorDepth']] = $numbers;
        }

        $pdf = $this->toRoot()->getPdf();
        //calculate the string width in user-units for the longest
        //number string of each level according to the respective styles
        foreach ($formattedNumberString as $level => $string) {
            $this->setContentStyleLevel($level);
            $this->applyStyles();
            $amount[$level] = $pdf->GetStringWidth($string);
        }

        return $amount;
    }

    public function applyStyles()
    {
        $level = $this->getContentStyleLevel();
        $pdf = $this->toRoot()->getPdf();
        $pdf->SetFont(
            $this->getFontType($level),
            $this->getFontStyle($level),
            $this->getFontSize($level)
        );
        $pdf->setColorArray('text', $this->getFontColor($level));
        $pdf->setColorArray('draw', $this->getDrawColor($level));
        $pdf->setColorArray('fill', $this->getFillColor($level));
        $pdf->setFontSpacing($this->getFontSpacing($level));
        $pdf->setFontStretching($this->getFontStretching($level));
    }

    public function getDisplayTypes()
    {
        return $this->displayTypes;
    }

    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    protected function setMaxDepth($maxDepth)
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

    protected function setShowPages($showPages)
    {
        $this->showPages = $showPages;
    }

    protected function setDrawLinesToPage(array $drawLinesToPage)
    {
        $this->drawLinesToPage = array_merge(
            $this->drawLinesToPage,
            $drawLinesToPage
        );
    }

    protected function setLineStyle(array $lineStyle)
    {
        $this->lineStyle = array_merge($this->lineStyle, $lineStyle);
    }

    public function getMinPageNumDistance()
    {
        return $this->minPageNumDistance;
    }

    protected function setMinPageNumDistance($minPageNumDistance)
    {
        $this->minPageNumDistance = $minPageNumDistance;
    }

    public function getIndent()
    {
        if ($this->indent['maxLevel'] < -1) {
            throw new \OutOfRangeException('maxLevel can\'t be smaller than -1');
        }
        return $this->indent;
    }

    protected function getContentStyleLevel()
    {
        return $this->contentStyleLevel;
    }

    protected function setContentStyleLevel($contentStyleLevel)
    {
        $this->contentStyleLevel = $contentStyleLevel;
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
}
