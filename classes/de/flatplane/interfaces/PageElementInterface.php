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

use de\flatplane\pageelements\PageElement;
use de\flatplane\utilities\Number;

/**
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface PageElementInterface
{
    public function hasContent();
    public function addContent(PageElement $content);

    public function getParent();
    public function getType();
    public function getNumber();
    public function getFullNumber();
    public function getLevel();

    public function getSize();
    public function getPage();

    public function getEnumerate();
    public function getShowInIndex();
    public function getTitle();
    public function getAltTitle();
    public function getCaption();

    public function getContent();


    public function setParent($parent);
    public function setType($type);
    public function setNumber(Number $number);

    public function setEnumerate($enumerate);
    public function setShowInIndex($showInIndex);
    public function setTitle($title);
    public function setAltTitle($altTitle);
    public function setCaption($caption);
}