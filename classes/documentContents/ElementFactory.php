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
use RuntimeException;

/**
 * Description of ElementFactory
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ElementFactory
{
    //Todo: required keys, default styles, default config, aliases?

    /**
     * @var array
     *  Defines required keys for all element types
     */
    protected $requiredKeys = [
        'section' =>
            ['title',
             'enumerate',
             'showInIndex',
             'showInDocument',
             'fontType',
             'fontSize',
             'fontStyle',
             'fontColor',
             'startsNewLine',
             'minFreePage'],
        'formula' =>
            ['type', 'font', 'code'],
        'image' =>
            ['path'],
        ];

    /**
     * @var array
     *  Defines paths to default config files for each element type
     */
    protected $configFiles = [
        'section' => 'config/sectionSettings.ini',
        'formula' => 'config/formulaSettings.ini',
        'image' => 'config/imageSettings.ini',
    ];

    /**
     * @var array
     *  Array containing references to prototype page-elements
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
    public function createElement($type, array $settings = null)
    {
        $type = strtolower($type);
        if (!isset($this->prototypes[$type])) {
            $prototype = $this->createPrototype($type);
            $this->addPrototype($type, $prototype);
        }
        $erg = clone $this->prototypes[$type];
        if (is_array($settings)) {
            $erg->getConfig()->setSettings($settings);
        }
        return $erg;
    }

    /**
     * @param string $type
     * @return DocumentElementInterface
     * @throws InvalidArgumentException
     */
    protected function createPrototype($type)
    {
        switch (strtolower($type))
        {
            case 'section':
                return $this->createSection();
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

    /**     *
     * @return Section
     */
    protected function createSection()
    {
        $config = new Config($this->configFiles['section']);
        $section = new Section($config);
        $this->checkRequiredKeys($section, $this->requiredKeys['section']);
        return $section;
    }

    /**
     * Checks if all required keys are set and not empty
     * @param DocumentElementInterface $element
     * @param array $requiredKeys
     * @throws RuntimeException
     */
    protected function checkRequiredKeys(
        DocumentElementInterface $element,
        array $requiredKeys
    ) {
        $definedKeys = $element->getConfig()->getSettings();
        foreach ($requiredKeys as $required) {
            if (empty($definedKeys[$required])) {
                throw new RuntimeException(
                    "The required key $required is not set in the configuration"
                    . "for $element"
                );
            }
        }
    }
}
