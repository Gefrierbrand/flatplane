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
    public function addContent(DocumentElementInterface $content);
    public function toRoot();
    public function toParentAtLevel($level);

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
    public function getStyle();
    public function getAllowSubContent();
    public function getEnumerate();
    public function getShowInList();
    public function getIsSplitable();

    public function setParent(DocumentElementInterface $parent);
    public function setType($type);
    public function setNumbers(array $numbers);
    public function setStyle(StyleInterface $style);
    public function setAllowSubContent($allowSubContent);
    public function setEnumerate($enumerate);
    public function setShowInList($showInIndex);
    public function setLabel($label);
    //public function setSize(array $zize); ?
}
