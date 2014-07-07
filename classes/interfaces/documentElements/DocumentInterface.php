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

use de\flatplane\documentElements\ElementFactory;
use de\flatplane\documentElements\TitlePage;
use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\utilities\PDF;

/**
 * todo: doc
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface DocumentInterface extends DocumentElementInterface
{
    public function addLabel(DocumentElementInterface $label);
    public function cite($source, $extras = '');
    public function addSource($label, array $settings);
    public function addBibTexSources($BibTexFile);

    public function getNumberingLevel($type = '');
    public function getNumberingFormat($type = '');
    public function getStartIndex($type = '');
    public function getNumberingPrefix($type = '');
    public function getNumberingPostfix($type = '');
    public function getNumberingSeparator($type = '');

    public function setNumberingLevel(array $numberingLevel);
    public function setNumberingFormat(array $numberingFormat);
    public function setStartIndex(array $startIndex);
    public function setNumberingPrefix(array $numberingPrefix);
    public function setNumberingPostfix(array $numberingPostfix);
    public function setNumberingSeparator(array $numberingSeparator);

    public function getLabels();
    public function getReference($label, $type = 'number');

    public function getAuthor();
    public function getTitle();
    public function getDocTitle();
    public function getSubject();
    public function getKeywords();
    public function getUnit();
    public function getPageSize();
    public function getOrientation();
    public function getPageMargins($dir = '');

    public function setAuthor($author);
    public function setTitle($title);
    public function setDocTitle($docTitle);
    public function setSubject($subject);
    public function setKeywords($keywords);
    public function setUnit($unit);
    public function setPageSize(array $pageSize);
    public function setOrientation($orientation);
    public function setPageMargins(array $margins);

    public function getNumPages();

    /**
     * @return PDF
     */
    public function getPDF();
    public function setPDF(PDF $pdf);

    /**
     * return ElementFactory
     */
    public function getElementFactory();
    public function setElementFactory(ElementFactory $elementFactory);

    public function getHyphenationPatterns();
    public function getHyphenationOptions();

    public function getPageNumberStyle($pageGroup = 'default');
    public function getPageNumberStartValue($pageGroup = 'default');

    public function setPageNumberStyle(array $pageNumberStyle);
    public function setPageNumberStartValue($pageNumberStartValue);

    public function getPageFormat();

    /**
     *
     * @param string $format
     */
    public function setPageFormat($format);

    /**
     *
     * @param string $text
     * @return string
     */
    public function hypenateText($text);
    public function setHyphenationOptions(array $hyphenation);


    public function getSources();

    /**
     *
     * @return string
     */
    public function getCitationStyle();

    /**
     *
     * @param array $sources
     */
    public function setSources(array $sources);

    /**
     *
     * @param string $style
     */
    public function setCitationStyle($style);

    /**
     *
     * @param array $settings
     * @return TitlePage
     */
    public function addTitlePage(array $settings = []);
}
