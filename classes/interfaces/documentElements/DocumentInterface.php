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
use de\flatplane\interfaces\DocumentElementInterface;

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

    public function getLabels();
    public function getReference($label, $type = 'number');

    public function getAuthor();
    public function getTitle();
    public function getDescription();
    public function getSubject();
    public function getKeywords();
    public function getUnit();
    public function getPageSize();
    public function getOrientation();
    public function getPageMargins($dir = '');

    /**
     * @return PDF
     */
    public function getPDF();

    public function getElementFactory();
    public function setElementFactory(ElementFactory $elementFactory);

    public function getHyphenationPatterns();

    public function getPageNumberStyle($pageGroup = 'default');
    public function getPageNumberStartValue($pageGroup = 'default');

    public function setPageNumberStyle($pageNumberStyle);
    public function setPageNumberStartValue($pageNumberStartValue);
}
