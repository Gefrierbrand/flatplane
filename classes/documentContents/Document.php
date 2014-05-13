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
use de\flatplane\interfaces\documentElements\DocumentInterface;
use de\flatplane\utilities\PDF;

//todo: count unresolved references
/**
 * This class represents the base document.
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Document extends AbstractDocumentContentElement implements DocumentInterface
{
    //todo: maybe without traits?
    use \de\flatplane\documentContents\traits\DocumentReferences;
    use \de\flatplane\documentContents\traits\DocumentGetterSetter;
    /**
     * @var int
     *  type of the element
     */
    protected $type='document';
    protected $labels = [];
    protected $isSplitable = true;

    protected $author =  '';
    protected $title = '';
    protected $description = '';
    protected $subject = '';
    protected $keywords = '';
    protected $unit = 'mm';
    protected $pageSize = 'A4';
    protected $orientation = 'P';

    protected $numberingFormat = ['default' => 'int'];
    protected $numberingLevel = ['default' => -1];
    protected $numberingPrefix = ['default' => ''];
    protected $numberingPostfix = ['default' => ''];
    protected $numberingSeparator = ['default' => '.'];
    protected $startIndex = ['default' => 1];

    /**
     * @var PDF
     */
    protected $pdf = null;

    /**
     * @var ElementFactory
     */
    protected $elementFactory;

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

    /**
     * @return Document
     */
    public function toRoot()
    {
        return $this;
    }

    public function addLabel(DocumentElementInterface $instance)
    {
        $this->labels[$instance->getLabel()] = $instance;
    }
}
