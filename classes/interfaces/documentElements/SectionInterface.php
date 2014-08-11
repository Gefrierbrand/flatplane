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

namespace de\flatplane\interfaces\documentElements;

use de\flatplane\interfaces\DocumentElementInterface;

/**
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface SectionInterface extends DocumentElementInterface
{
    /**
     * @return bool
     */
    public function getShowInDocument();

    /**
     * Gets the StartsNewLine option for the level defined by $level
     * @param string $level (optional)
     * @return bool
     * @throws RuntimeException
     */
    public function getStartsNewLine($level = 'default');

    /**
     * Gets the StartsNewPage option for the level defined by $level
     * @param string $level
     * @return bool
     * @throws RuntimeException
     */
    public function getStartsNewPage($level = 'default');

    /**
     * @param string $level (optional)
     * @return float
     *  minimum free percentage of textheight needed to start a section on
     *  the current page instead of a new one.
     * @throws RuntimeException
     * @throws \OutOfBoundsException
     */
    public function getMinFreePage($level = 'default');

    /**
     * Get a non-hyphenated version of the titlesting
     * @return string
     */
    public function getNonHyphenTitle();

    /**
     * @return bool
     */
    public function getShowInBookmarks();

    /**
     * Enable/disable the display of the section in the PDFs bookmarks
     * @param type $showInBookmarks
     */
    public function setShowInBookmarks($showInBookmarks);

    /**
     * Enable/Disable the display of the section-headline in the document.
     * Does not affect the sections content
     * @param bool $showInDocument
     */
    public function setShowInDocument($showInDocument);

    /**
     * Set the minimaly needed remaining free space as percentage of the
     * textheight to start the section on the current page. If remaing space is
     * less than the resulting value, a pagebreak is inserted
     * @param array $minFreePage
     *  value depending on the sections level as keys
     */
    public function setMinFreePage(array $minFreePage);

    /**
     * Sets the startsNewLine option depening on the level
     * @param array $startsNewLine
     */
    public function setStartsNewLine(array $startsNewLine);

    /**
     * Sets the startsNewpage option depening on the level
     * @param array $startsNewPage
     */
    public function setStartsNewPage(array $startsNewPage);

    /**
     * Get the space in between the Numbering and the text of the headline (in em)
     * @return float
     */
    public function getNumberSeparationWidth();

    /**
     * Set the space in between the Numbering and the text of the headline (in em)
     * @param float $numberSeparationWidth
     */
    public function setNumberSeparationWidth($numberSeparationWidth);

    /**
     * @param string $level
     * @return bool
     */
    public function getIgnoreTopMarginAtPageStart($level = null);

    /**
     * Enables/Disables the ignoring of the sections top margins at the pagestart
     * for each level defined in the keys separately
     * @param array $ignoreTopMarginAtPageStart
     */
    public function setIgnoreTopMarginAtPageStart(array $ignoreTopMarginAtPageStart);

    /**
     * Adds a manual pagebreak as content.
     */
    public function addPageBreak();
}
