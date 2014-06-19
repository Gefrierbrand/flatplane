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

namespace de\flatplane\interfaces;

/**
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface DocumentElementInterface
{
    public function __construct(array $config);
    public function __toString();
    public function __clone();

    public function addCounter(CounterInterface $counter, $name);
    public function checkLocalCounter(DocumentElementInterface $content);
    public function hasContent();
    public function toRoot();
    public function toParentAtLevel($level);
    public function applyStyles($key = null);

    public function addElement($type, array $settings);
    public function addSection($title, array $settings);
    public function addFormula($code, array $settings);
    public function addImage($path, array $settings);
    public function addTable(array $data, array $settings);
    public function addText($text, array $settings);

    public function getParent();
    public function getLevel();
    public function getContent();
    public function getType();
    public function getSize();
    public function getPage();
    public function getNumbers();
    public function getFormattedNumbers();
    public function getCounter($name);
    public function getLabel();
    public function getAllowSubContent();
    public function getEnumerate();
    public function getShowInList();
    public function getIsSplitable();
    public function getTitle();
    public function getAltTitle();

    public function getFontType($key = null);
    public function getFontSize($key = null);
    public function getFontStyle($key = null);
    public function getFontColor($key = null);
    public function getDrawColor($key = null);
    public function getFillColor($key = null);

    public function getMargins($key = null);
    public function getPaddings($key = null);


    public function setParent(DocumentElementInterface $parent);
    //public function setType($type);
    public function setNumbers(array $numbers);
    //public function setAllowSubContent($allowSubContent);
    //public function setEnumerate($enumerate);
    //public function setShowInList($showInIndex);
    public function setLabel($label);
    //public function setSize(array $zize); ?
    public function setPage($page);

    public function hyphenateTitle();
    public function generateOutput();
}
