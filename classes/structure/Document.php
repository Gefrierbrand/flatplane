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

use de\flatplane\documentContents\ContentFunctions;
use de\flatplane\interfaces\DocumentContentStructureInterface;
use de\flatplane\utilities\Settings;
use InvalidArgumentException;

/**
 * This class represents the base document.
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Document implements DocumentContentStructureInterface
{
    use ContentFunctions;
    /**
     * @var DocumentSettings
     *  Holds an instance of the DocumentSettings configuration Object
     */
    private $settings;

    /**
     * @var int
     *  Number of pages; used for internal representation.
     *  FIXME: Currently not used at all
     */
    private $pages;

    /**
     *  Document Constructor
     *  @param array $settings
     *   Array containing key=>value pairs of document-wide settings     *
     */
    public function __construct(array $settings = null, $configFile = 'config/defaultDocumentSettings.ini')
    {
        $settings = new Settings($settings, $configFile);
        $this->settings = $settings->getSettings();
    }

    /**
     * @param string $key (optional)
     * @return Settings
     * @throws InvalidArgumentException
     */
    public function getSettings($key = null)
    {
        if ($key === null) {
            return $this->settings;
        } else {
            if (array_key_exists($key, $this->settings)) {
                return $this->settings[$key];
            } else {
                //TODO: maybe just warn?
                throw new InvalidArgumentException('Settings key '.$key.' not found');
            }
        }
    }

    public function toRoot()
    {
        return $this;
    }

    public function getParent()
    {
        return $this;
    }

    public function setParent(DocumentContentStructureInterface $parent)
    {
        //currently: do nothing
    }
}
