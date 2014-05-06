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

namespace de\flatplane\documentContents;

use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\interfaces\documentelements\DocumentInterface;

/**
 * This class represents the base document.
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Document extends AbstractDocumentContentElement implements DocumentInterface
{
    protected $type='document';
    protected $labels = [];

    //todo: fixsettings: arrays, defaults ?
    protected $settings = ['author' => '',
                           'title' => '',
                           'description' => '',
                           'subject' => '',
                           'keywords' => '',
                           'unit' => 'mm',
                           'pageSize' => 'A4',
                           'orientation' => 'P',
                           'defaultMargin' => 20,
                           'margin' => ['top' => 20,
                                        'left' =>20,
                                        'right' => 20,
                                        'bottom' => 20,],
                           'defaultFontType' => 'times',
                           'defaultFontSize' => 12,
                           'defaultFontStyle' => '',
                           'defaultFontColor' => [0, 0, 0],
                           'defaultDrawColor' => [0, 0, 0],
                           'defaultStartIndex' => 1,
                           'defaultNumberingLevel' => -1,
                           'defaultNumberingFormat' => 'int',
                           'defaultNumberingSeparator' => '.',
                           'defaultNumberingPrefix' =>'',
                           'defaultNumberingPostfix' => ']',
                           'allowSubContent' => true];

    /**
     * @var int
     *  Number of pages; used for internal representation.
     *  FIXME: Currently not used at all
     */
    private $pages;

    public function __toString()
    {
        return (string) $this->getSettings('title');
    }

    public function __clone()
    {
        //currently: do nothing
    }

    /**
     * @return Document
     */
    public function toRoot()
    {
        return $this;
    }

    /**
     * @return Document
     */
    public function getParent()
    {
        return $this;
    }

    public function setParent(DocumentElementInterface $parent)
    {
        //currently: do nothing
    }

    public function getLabelName()
    {
        return false;
    }

    public function addLabel(DocumentElementInterface $instance)
    {
        $this->labels[$instance->getLabelName()] = $instance;
    }
}
