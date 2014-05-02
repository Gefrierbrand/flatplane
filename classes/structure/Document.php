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

namespace de\flatplane\structure;

use de\flatplane\interfaces\DocumentContentStructureInterface;
use de\flatplane\utilities\Config;

/**
 * This class represents the base document.
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Document implements DocumentContentStructureInterface
{
    use \de\flatplane\documentContents\traits\NumberingFunctions;

    /**
     * @var int
     *  Number of pages; used for internal representation.
     *  FIXME: Currently not used at all
     */
    private $pages;

    /**
     * Document Constructor
     * @param array $config (optional)
     *  key => value pairs of options overriding the defaults
     */
    public function __construct(Config $config = null)
    {
        if (empty($config)) {
            $this->config = new Config();
        } else {
            $this->config = $config;
        }
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

    public function setParent(DocumentContentStructureInterface $parent)
    {
        //currently: do nothing
    }
}
