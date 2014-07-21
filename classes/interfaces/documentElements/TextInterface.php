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
    public function getHyphenate();
    public function getText();

    //public function readText();

    public function getHash($startYposition);
    public function getParse();

    public function getReference($label, $type = 'number');
    public function getTextAlignment();
    public function setPath($path);
    public function setParse($parse);

    public function setTextAlignment($textAlignment);

    public function getPath();
    public function getSplitInParagraphs();
    public function getSplitAtStr();

    public function getUseCache();
    public function getContainsPageReference();

    public function setSplitInParagraphs($splitInParagraphs);
    public function setSplitAtStr($splitAtStr);
    public function setUseCache($useCache);
    public function setContainsPageReference($containsPageReference);
    public function setText($text);
    public function addFootnote($text);
}
