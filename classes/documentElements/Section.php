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
    protected $showInBookmarks = true;
    protected $ignoreTopMarginAtPageStart = ['default' => true];

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

    /**
     * @return bool
     */
    public function getShowInDocument()
    {
        return $this->showInDocument;
    }

    /**
     *
     * @param string $level (optional)
     * @return float
     *  minimum free percentage of textheight needed to start a section on
     *  the current page instead of a new one.
     * @throws RuntimeException
     * @throws \OutOfBoundsException
     */
    public function getMinFreePage($level = 'default')
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

    /**
     * Gets the StartsNewLine option for the level defined by $level
     * @param string $level (optional)
     * @return bool
     * @throws RuntimeException
     */
    public function getStartsNewLine($level = 'default')
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

    /**
     * Gets the StartsNewPage option for the level defined by $level
     * @param string $level
     * @return bool
     * @throws RuntimeException
     */
    public function getStartsNewPage($level = 'default')
    {
        if (isset($this->startsNewPage[$level])) {
            return $this->startsNewPage[$level];
        } elseif (isset($this->startsNewPage['default'])) {
            return $this->startsNewPage['default'];
        } else {
            throw new RuntimeException(
                'The required property startsNewPage is not set.'
            );
        }
    }

    /**
     * Enable/Disable the display of the section-headline in the document.
     * Does not affect the sections content
     * @param bool $showInDocument
     */
    public function setShowInDocument($showInDocument)
    {
        $this->showInDocument = (bool) $showInDocument;
    }

    /**
     * Set the minimaly needed remaining free space as percentage of the
     * textheight to start the section on the current page. If remaing space is
     * less than the resulting value, a pagebreak is inserted
     * @param array $minFreePage
     *  value depending on the sections level as keys
     */
    public function setMinFreePage(array $minFreePage)
    {
        $this->minFreePage = array_merge($this->minFreePage, $minFreePage);
    }

    /**
     * Sets the startsNewLine option depening on the level
     * @param array $startsNewLine
     */
    public function setStartsNewLine(array $startsNewLine)
    {
        $this->startsNewLine = array_merge($this->startsNewLine, $startsNewLine);
    }

    /**
     * Sets the startsNewpage option depening on the level
     * @param array $startsNewPage
     */
    public function setStartsNewPage(array $startsNewPage)
    {
        $this->startsNewPage = array_merge($this->startsNewPage, $startsNewPage);
    }

    /**
     * todo: doc
     * @return int Number of pagebreaks
     */
    public function generateOutput()
    {
        if (!$this->getShowInDocument()) {
            return 0;
        }
        $pdf = $this->getPDF();
        $startPage = $pdf->getPage();
        //save old pagemargins
        $oldMargins = $pdf->getMargins();
        //adjust left and right margins according tho the elements settings
        $pdf->SetLeftMargin(
            $oldMargins['left'] + $this->getMargins('left'.$this->getLevel())
        );
        $pdf->SetRightMargin(
            $oldMargins['right'] + $this->getMargins('right'.$this->getLevel())
        );

        //add element top margins to current y-position
        if (!$this->getIgnoreTopMarginAtPageStart('level'.$this->getLevel())
            || $pdf->GetY() > $this->toRoot()->getPageMargins('top')
        ) {
            $pdf->SetY($pdf->GetY()+$this->getMargins('top'.$this->getLevel()));
        }

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
        $pdf->SetY($pdf->GetY()+$this->getMargins('bottom'.$this->getLevel()));
        return $pdf->getPage() - $startPage;
    }

    /**
     * Get the space in between the Numbering and the text of the headline (in em)
     * @return float
     */
    public function getNumberSeparationWidth()
    {
        return $this->numberSeparationWidth;
    }

    /**
     * Set the space in between the Numbering and the text of the headline (in em)
     * @param float $numberSeparationWidth
     */
    public function setNumberSeparationWidth($numberSeparationWidth)
    {
        $this->numberSeparationWidth = $numberSeparationWidth;
    }

    /**
     * Get a non-hyphenated version of the titlesting
     * @return string
     */
    public function getNonHyphenTitle()
    {
        //replace UTF-8 shy char.
        //todo: check encoding and remove only "\xAD" if needed
        return str_replace("\xC2\xAD", '', $this->getTitle());
    }

    /**
     * @return bool
     */
    public function getShowInBookmarks()
    {
        return $this->showInBookmarks;
    }

    /**
     * Enable/disable the display of the section in the PDFs bookmarks
     * @param type $showInBookmarks
     */
    public function setShowInBookmarks($showInBookmarks)
    {
        $this->showInBookmarks = $showInBookmarks;
    }

    /**
     * @param string $level
     * @return bool
     */
    public function getIgnoreTopMarginAtPageStart($level = null)
    {
        if ($level !== null && isset($this->ignoreTopMarginAtPageStart[$level])) {
            return $this->ignoreTopMarginAtPageStart[$level];
        } else {
            return $this->ignoreTopMarginAtPageStart['default'];
        }
    }

    /**
     * Enables/Disables the ignoring of the sections top margins at the pagestart
     * for each level defined in the keys separately
     * @param array $ignoreTopMarginAtPageStart
     */
    public function setIgnoreTopMarginAtPageStart(array $ignoreTopMarginAtPageStart)
    {
        $this->ignoreTopMarginAtPageStart = array_merge(
            $this->ignoreTopMarginAtPageStart,
            $ignoreTopMarginAtPageStart
        );
    }

    /**
     * Adds a manual pagebreak as content.
     */
    public function addPageBreak()
    {
        $content = new PageBreak([]);
        $content->setParent($this);
        $this->addContent($content);
    }
}
