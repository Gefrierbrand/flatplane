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

use de\flatplane\interfaces\documentElements\FormulaInterface;
use RuntimeException;

/**
 * Description of formula
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Formula extends AbstractDocumentContentElement implements FormulaInterface
{
    protected $type='formula';
    protected $allowSubContent = ['formula'];
    protected $isSplitable = false;
    protected $title='Formula';

    protected $code;
    protected $formulaFont = 'TeX';
    protected $codeFormat = 'TeX';
    protected $availableFonts = ['TeX', 'STIX-Web', 'Asana-Math', 'Neo-Euler',
                                'Gyre-Pagella', 'Gyre-Termes', 'Latin-Modern'];
    protected $availableCodeFormats = ['TeX','MML'];
    protected $formulaStyle = 'display'; //options: display, inline

    protected $useCache = true;
    protected $path;

    public function getCode()
    {
        return $this->code;
    }

    public function getCodeFormat()
    {
        return $this->codeFormat;
    }

    public function getAvailableFonts()
    {
        return $this->availableFonts;
    }

    public function getAvailableCodeFormats()
    {
        return $this->availableCodeFormats;
    }

    public function getSize()
    {
        if (!empty($this->getPath())) {
            $size = $this->getSizeFromFile();
        } else {
            trigger_error('formula size requested before render', E_USER_WARNING);
            $size = ['height' => 0, 'width' => 0, 'numPages' => 0];
        }
        return $size;
    }

    /**
     * todo: doc
     * @return array
     */
    protected function getSizeFromFile()
    {
        if (!is_readable($this->getPath())) {
            trigger_error('formula svg path not readable', E_USER_WARNING);
        }
        //extract dimensions frome the SVGs style-tag using simplexml
        $xml = simplexml_load_file($this->getPath());
        $attrib = explode('; ', $xml->attributes()->style[0]);

        //extract numeric information
        $regExMatchWidth = preg_match(
            '/(^width:[ ]*)([-+]?[0-9]*\.?[0-9]+)([ ]?ex$)/',
            $attrib[0],
            $widthMatches
        );
        $regExMatchHeight = preg_match(
            '/(^height:[ ]*)([-+]?[0-9]*\.?[0-9]+)([ ]?ex$)/',
            $attrib[1],
            $heightMatches
        );

        if (!$regExMatchWidth || !$regExMatchHeight) {
            throw new RuntimeException(
                'SVG did not contain valid size information'
            );
        }

        if (!isset($widthMatches[2], $heightMatches[2])) {
            trigger_error('Invalid SVG-size RegEx Result', E_USER_WARNING);
        }
        $width_ex = $widthMatches[2];
        $height_ex = $heightMatches[2];

        $pdf = $this->toRoot()->getPdf();

        $width = $pdf->getHTMLUnitToUnits($width_ex, $pdf->getFontSize(), 'ex');
        $height = $pdf->getHTMLUnitToUnits($height_ex, $pdf->getFontSize(), 'ex');

        return ['width' => $width,
                'height' => $height,
                'width_ex' => $width_ex,
                'height_ex' => $height_ex];
    }

    /**
     * This method returns a (pseudo-unique) hash for the current instance
     * depending on the formula-code and formula-font to be used as filename
     * for the SVG-generation
     * @return string
     */
    public function getHash()
    {
        return sha1($this->getCode().$this->getFormulaFont());
    }

    /**
     * Returns the path to the SVG file corresponding to the current instance if
     * it exists or null otherwise.
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getFormulaFont()
    {
        return $this->formulaFont;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    protected function setCode($code)
    {
        $this->code = $code;
    }

    protected function setFormulaFont($font)
    {
        if (!in_array($font, $this->availableFonts, true)) {
            trigger_error(
                "Font $font not available, defaulting to TeX",
                E_USER_NOTICE
            );
            $font = 'TeX';
        }
        $this->formulaFont = $font;
    }

    protected function setCodeFormat($codeFormat)
    {
        if (!in_array($codeFormat, $this->availableCodeFormats, true)) {
            trigger_error(
                "Format $codeFormat not available, defaulting to TeX",
                E_USER_NOTICE
            );
            $codeFormat = 'TeX';
        }
        $this->codeFormat = $codeFormat;
    }

    public function getUseCache()
    {
        return $this->useCache;
    }

    protected function setUseCache($useCache)
    {
        $this->useCache = (bool) $useCache;
    }

    public function getFormulaStyle()
    {
        return $this->formulaStyle;
    }

    protected function setFormulaStyle($formulaStyle)
    {
        $this->formulaStyle = $formulaStyle;
    }

    //todo: test me more! (remove styles etc)
    public function applyStyles()
    {
        if ($this->getCodeFormat() == 'TeX') {
            switch ($this->getFormulaStyle()) {
                case 'inline':
                    if (strpos($this->code, '\displaystyle') === 0) {
                        $this->code = substr($this->code, 12);
                    }
                    break;
                case 'display':
                    if (strpos($this->code, '\displaystyle') !== 0) {
                        $this->code = '\displaystyle{'.$this->code.'}';
                    }
                    break;
                default:
                    break;
            }
        }
    }
}
