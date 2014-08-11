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
interface TextInterface extends DocumentElementInterface
{
    /**
     * Gets the defined, possibly hyphenated text. This Method might read the
     * textfile if needed and cause sideeffects like adding footnotes
     * @return string
     */
    public function getText();

    /**
     * Get a hashed representation of the text and its settings. This is used
     * as part of the cache-filename
     * @param float $startYposition
     * @return string
     */
    public function getHash($startYposition);

    /**
     * Get the text HTML-parsing setting
     * @return bool
     */
    public function getParse();

    /**
     * Get the Number, Page or title of the referenced object defined by the
     * $label, which has to be set on the target object using its setLabel()
     * Method
     * @param string $label
     * @param string $type (optional)
     *  The type of reference to use, defaults to 'number'
     * @return string
     *  The requested references properties or an errorstring containg
     *  unresolvedReferenceMarker with an estimated length appropriate to the
     *  requested type
     * @see Document::getReferenceValue()
     */
    public function getReference($label, $type = 'number');

    /**
     * Get the text alignment setting
     * @return string
     */
    public function getTextAlignment();

    /**
     * Set the path to the file containig the text
     * @param string $path
     */
    public function setPath($path);

    /**
     * Enable/disable the parsing of the content as HTML if set to true or
     * as plaintext if set to false
     * @param bool $parse
     */
    public function setParse($parse);


    /**
     * Set the text alignmet setting
     * @param string $textAlignment
     *  available options: 'L' for left, 'C' for center, 'R' for right
     *  and 'J' for jusify
     */
    public function setTextAlignment($textAlignment);


    /**
     * Get the path to the content-file
     * @return string
     */
    public function getPath();

    /**
     * @return bool
     */
    public function getSplitInParagraphs();

    /**
     * @return string
     */
    public function getSplitAtStr();

    /**
     * Get the setting for the usage of the text-sizecache
     * @return bool
     */
    public function getUseCache();

    /**
     * Returns true if the text containes references to the page property of an
     * element
     * @return bool
     */
    public function getContainsPageReference();

    /**
     * Enable/disable the splitting of the text at specific chars/strings to
     * limit the amount of text sent to the outputting methods at once
     * @see setSplitAtStr()
     * @param bool $splitInParagraphs
     */
    public function setSplitInParagraphs($splitInParagraphs);

    /**
     * Set the char/string at which the text will get split
     * @param string $splitAtStr
     * @see setSplitInParagraphs()
     */
    public function setSplitAtStr($splitAtStr);

    /**
     * Enable/disable the usage of the size-cache
     * @param bool $useCache
     */
    public function setUseCache($useCache);

    /**
     * Sets the containsPageReference flag to indicate whether the textfile needs
     * to be read again to resolve the references
     * @param bool $containsPageReference
     */
    public function setContainsPageReference($containsPageReference);

    /**
     * Sets the text to be displayed. Content defined with this method can't
     * contain references, footnotes or other dynamicly generated elements
     * @param string $text
     */
    public function setText($text);

    /**
     * Add a footnote at the current position within the text. Only call this
     * from within the text definition files
     * @param string $text
     *  the content of the footnote. may contain HTML
     * @return string
     *  the number of the footnote sourrounded by <sup> tags
     */
    public function addFootnote($text);
}
