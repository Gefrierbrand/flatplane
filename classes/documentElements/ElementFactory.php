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

    protected $documentConfig  = 'config/documentSettings.ini';
    protected $sectionConfig   = 'config/sectionSettings.ini';
    protected $listConfig      = 'config/listSettings.ini';
    protected $formulaConfig   = 'config/formulaSettings.ini';
    protected $textConfig      = 'config/textSettings.ini';
    protected $imageConfig     = 'config/imageSettings.ini';
    protected $sourceConfig    = 'config/sourceSettings.ini';
    protected $codeConfig      = 'config/codeSettings.ini';
    protected $titlePageConfig = 'config/titlePageSettings.ini';
    protected $tableConfig     = 'config/tableSettings.ini';
    protected $footnoteConfig  = 'config/footnoteSettings.ini';

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
        $config = new Config($this->documentConfig, $settings);
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
        $config = new Config($this->sectionConfig);
        return new Section($config->getSettings());
    }

    /**
     * @return ListOfContents
     */
    protected function createList()
    {
        $config = new Config($this->listConfig);
        return new ListOfContents($config->getSettings());
    }

    /**
     * @return Formula
     */
    protected function createFormula()
    {
        $config = new Config($this->formulaConfig);
        return new Formula($config->getSettings());
    }

    /**
     *
     * @return Text
     */
    protected function createText()
    {
        $config = new Config($this->textConfig);
        return new Text($config->getSettings());
    }

    /**
     *
     * @return Text
     */
    protected function createCode()
    {
        $config = new Config($this->codeConfig);
        return new Code($config->getSettings());
    }

    /**
     *
     * @return Image
     */
    protected function createImage()
    {
        $config = new Config($this->imageConfig);
        return new Image($config->getSettings());
    }

    /**
     *
     * @return Source
     */
    protected function createSource()
    {
        $config = new Config($this->sourceConfig);
        return new Source($config->getSettings());
    }

    /**
     *
     * @return TitlePage
     */
    protected function createTitlePage()
    {
        $config = new Config($this->titlePageConfig);
        return new TitlePage($config->getSettings());
    }

    /**
     *
     * @return Table
     */
    protected function createTable()
    {
        $config = new Config($this->tableConfig);
        return new Table($config->getSettings());
    }

    /**
     * @param string $text
     * @param string $number
     * @param DocumentInterface $document
     * @return Footnote
     */
    public function createFootnote($text, $number, PDF $pdf, DocumentInterface $document)
    {
        $config = new Config($this->footnoteConfig);
        return new Footnote($text, $number, $pdf, $document, $config->getSettings());
    }
}
