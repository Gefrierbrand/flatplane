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
use de\flatplane\utilities\Config;
use InvalidArgumentException;

/**
 * Description of ElementFactory
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ElementFactory
{
    //todo: maybe aliases?

    /**
     * @var array
     *  Defines paths to default config files for each element type
     */
    protected $configFiles = [
        'section' => 'config/sectionSettings.ini',
        'formula' => 'config/formulaSettings.ini',
        'image' => 'config/imageSettings.ini',
        'list' => 'config/listSettings.ini',
    ];

    /**
     * @var array
     *  Array containing references to named prototype-page-elements
     */
    protected $prototypes;

    /**
     * Factory method for creating new DocumentElements, uses prototypes to
     * reduce the number of neccesary object initialisations. This method
     * also tries to create new prototypes if the requested type does not
     * already exist.
     * @param string $type
     *  Type of the element to be created. E.g. 'section', 'formula'
     * @param array $settings (optional)
     *  Key => Value pairs of settings for the new element
     * @return DocumentElementInterface
     */
    public function createElement($type, array $settings = [])
    {
        $type = strtolower($type);
        if (!isset($this->prototypes[$type])) {
            $prototype = $this->createPrototype($type, $settings);
            $this->addPrototype($type, $prototype);
        }
        $erg = clone $this->prototypes[$type];
        if (is_array($settings)) {
            $erg->setSettings($settings);
        }
        return $erg;
    }

    /**
     * @param string $type
     * @param array $settings
     * @return DocumentElementInterface
     * @throws InvalidArgumentException
     */
    protected function createPrototype($type, array $settings)
    {
        switch (strtolower($type))
        {
            case 'section':
                return $this->createSection($settings);
            case 'list':
                return $this->createList($settings);
            default:
                throw new InvalidArgumentException(
                    "The requested type $type is not a valid element type"
                );
        }
    }

    /**
     *
     * @param string $type
     * @param DocumentElementInterface $prototype
     */
    protected function addPrototype($type, DocumentElementInterface $prototype)
    {
        $this->prototypes[$type] = $prototype;
    }

    /**
     * @param array $settings
     * @return \de\flatplane\documentContents\Section
     */
    protected function createSection(array $settings)
    {
        $config = new Config($this->configFiles['section'], $settings);
        $section = new Section($config->getSettings());
        return $section;
    }

    /**
     * @param array $settings
     * @return \de\flatplane\documentContents\ListOfContents
     */
    protected function createList(array $settings)
    {
        $config = new Config($this->configFiles['list'], $settings);
        $list = new ListOfContents($config->getSettings());
        return $list;
    }
}
