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

namespace de\flatplane\interfaces\documentElements;

use de\flatplane\documentContents\ElementFactory;
use de\flatplane\interfaces\DocumentElementInterface;

/**
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface DocumentInterface extends DocumentElementInterface
{
    public function addLabel(DocumentElementInterface $label);

    public function getNumberingLevel($type = '');
    public function getNumberingFormat($type = '');
    public function getStartIndex($type = '');
    public function getNumberingPrefix($type = '');
    public function getNumberingPostfix($type = '');
    public function getNumberingSeparator($type = '');

    public function getLabels();
    public function getAuthor();
    public function getTitle();
    public function getDescription();
    public function getSubject();
    public function getKeywords();
    public function getUnit();
    public function getPageSize();
    public function getOrientation();

    public function getPages();

    public function getElementFactory();
    public function setElementFactory(ElementFactory $elementFactory);
}
