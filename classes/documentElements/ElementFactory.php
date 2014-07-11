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

namespace de\flatplane\documentElements;

use de\flatplane\controller\Flatplane;
use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\interfaces\documentElements\DocumentInterface;
use de\flatplane\utilities\Config;
use de\flatplane\utilities\PDF;
use InvalidArgumentException;

/**
 * Description of ElementFactory
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ElementFactory
{
    //todo: doc!

    protected $documentConfigFile  = 'documentSettings.ini';
    protected $sectionConfigFile   = 'sectionSettings.ini';
    protected $listConfigFile      = 'listSettings.ini';
    protected $formulaConfigFile   = 'formulaSettings.ini';
    protected $textConfigFile      = 'textSettings.ini';
    protected $imageConfigFile     = 'imageSettings.ini';
    protected $sourceConfigFile    = 'sourceSettings.ini';
    protected $codeConfigFile      = 'codeSettings.ini';
    protected $titlePageConfigFile = 'titlePageSettings.ini';
    protected $tableConfigFile     = 'tableSettings.ini';
    protected $footnoteConfigFile  = 'footnoteSettings.ini';

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
     * @return Document
     */
    public function createDocument(array $settings = [], PDF $pdf = null)
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->documentConfigFile;
        $config = new Config($configFile);
        $doc = new Document($config->getSettings());
        $doc->setElementFactory($this);

        $orientation = $doc->getOrientation();
        $unit = $doc->getUnit();
        $format = $doc->getPageFormat();
        $marginTop = $doc->getPageMargins('top');
        $marginBot = $doc->getPageMargins('bottom');
        $marginLeft = $doc->getPageMargins('left');
        $marginRight = $doc->getPageMargins('right');
        $headerMargin = $doc->getPageMargins('header');
        $footerMargin = $doc->getPageMargins('footer');

        if ($pdf === null) {
            $pdf = new PDF($orientation, $unit, $format);
        }
        $pdf->SetMargins($marginLeft, $marginTop, $marginRight);
        $pdf->SetAutoPageBreak(true, $marginBot);

        $pdf->setHeaderMargin($headerMargin);
        $pdf->setFooterMargin($footerMargin);

        $pdf->SetAuthor($doc->getAuthor());
        $pdf->SetTitle($doc->getDocTitle());
        $pdf->SetSubject($doc->getSubject());
        $pdf->SetKeywords($doc->getKeywords());
        $pdf->SetCreator('Flatplane ('.Flatplane::VERSION.')');
        $pdf->setDefaultBottomMargin($marginBot);
        $doc->setPDF($pdf);
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
            return $this->$name();
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
     * @return Section
     */
    protected function createSection()
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->sectionConfigFile;
        $config = new Config($configFile);
        return new Section($config->getSettings());
    }

    /**
     * @return ListOfContents
     */
    protected function createList()
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->listConfigFile;
        $config = new Config($configFile);
        return new ListOfContents($config->getSettings());
    }

    /**
     * @return Formula
     */
    protected function createFormula()
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->formulaConfigFile;
        $config = new Config($configFile);
        return new Formula($config->getSettings());
    }

    /**
     *
     * @return Text
     */
    protected function createText()
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->textConfigFile;
        $config = new Config($configFile);
        return new Text($config->getSettings());
    }

    /**
     *
     * @return Text
     */
    protected function createCode()
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->codeConfigFile;
        $config = new Config($configFile);
        return new Code($config->getSettings());
    }

    /**
     *
     * @return Image
     */
    protected function createImage()
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->imageConfigFile;
        $config = new Config($configFile);
        return new Image($config->getSettings());
    }

    /**
     *
     * @return Source
     */
    protected function createSource()
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->sourceConfigFile;
        $config = new Config($configFile);
        return new Source($config->getSettings());
    }

    /**
     *
     * @return TitlePage
     */
    protected function createTitlePage()
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->titlePageConfigFile;
        $config = new Config($configFile);
        return new TitlePage($config->getSettings());
    }

    /**
     *
     * @return Table
     */
    protected function createTable()
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->tableConfigFile;
        $config = new Config($configFile);
        return new Table($config->getSettings());
    }

    /**
     * @param string $text
     * @param DocumentInterface $document
     * @return Footnote
     */
    public function createFootnote($text, DocumentInterface $document)
    {
        $configFile = Flatplane::getConfigDir()
                        .DIRECTORY_SEPARATOR.$this->footnoteConfigFile;
        $config = new Config($configFile);
        return new Footnote($text, $document, $config->getSettings());
    }
}
