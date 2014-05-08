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
    //todo: doc!

    protected $documentConfig = 'config/documentSettings.ini';
    protected $sectionConfig  = 'config/sectionSettings.ini';
    protected $listConfig     = 'config/listSettings.ini';
    protected $formulaConfig  = 'config/formulaSettings.ini';

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
            $prototype = $this->createPrototype($type);
            $this->addPrototype($type, $prototype);
        }
        $erg = clone $this->prototypes[$type];
        if (!empty($settings)) {
            $erg->setConfig($settings);
        }
        return $erg;
    }

    /**
     * @param array $settings
     * @return \de\flatplane\documentContents\Document
     */
    public function createDocument(array $settings = [])
    {
        $config = new Config($this->documentConfig, $settings);
        $doc = new Document($config->getSettings());
        $doc->setElementFactory($this);
        return $doc;
    }

    /**
     * @param string $type
     * @param array $settings
     * @return DocumentElementInterface
     * @throws InvalidArgumentException
     */
    protected function createPrototype($type)
    {
        $name = 'create'.ucfirst($type);
        if (method_exists($this, $name)) {
            return $this->{$name}();
        } else {
            throw new InvalidArgumentException("$type is not a valid element type");
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
     * @return \de\flatplane\documentContents\Section
     */
    protected function createSection()
    {
        $config = new Config($this->sectionConfig);
        return new Section($config->getSettings());
    }

    /**
     * @return \de\flatplane\documentContents\ListOfContents
     */
    protected function createList()
    {
        $config = new Config($this->listConfig);
        return new ListOfContents($config->getSettings());
    }

    /**
     * @return \de\flatplane\documentContents\Formula
     */
    protected function createFormula()
    {
        $config = new Config($this->formulaConfig);
        return new Formula($config->getSettings());
    }
}
