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
use de\flatplane\interfaces\documentElements\FormulaInterface;
use de\flatplane\utilities\SVGSize;
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
    protected static $availableFonts = ['TeX', 'STIX-Web', 'Asana-Math', 'Neo-Euler',
                                'Gyre-Pagella', 'Gyre-Termes', 'Latin-Modern'];
    protected static $availableCodeFormats = ['TeX','MML'];

    protected $formulaStyle = 'display'; //options: display, inline
    protected $numberPosition = 'right'; //todo: implement

    protected $useCache = true;
    protected $path;

    protected $scalingFactor = 0.85;

    public function getCode()
    {
        return $this->code;
    }

    public function getCodeFormat()
    {
        return $this->codeFormat;
    }

    public static function getAvailableFonts()
    {
        return self::$availableFonts;
    }

    public static function getAvailableCodeFormats()
    {
        return self::$availableCodeFormats;
    }

    /**
     * @param float $startYposition
     * @return array
     * @throws RuntimeException
     */
    public function getSize($startYposition = null)
    {
        if (empty($this->getPath())) {
            throw new RuntimeException('formula size requested before render');
        }
        return parent::getSize($startYposition);
    }

    public function generateOutput()
    {
        if (empty($this->getPath()) || !is_readable($this->getPath())) {
            throw new \RuntimeException(
                'The path to the formula SVG file must be set before outputting it'
            );
        }
        $this->applyStyles();
        $size = $this->applyScalingFactor($this->getSizeFromFile());

        $pdf = $this->toRoot()->getPDF();
        $startPage = $pdf->getPage();

        $pdf->SetY($pdf->GetY()+$this->getMargins('top'));
        $pdf->ImageSVG(
            $this->getPath(),
            $this->getMargins('left') + $this->toRoot()->getPageMargins('left'),
            $pdf->GetY(),
            $size['width'],
            $size['height']
        );
        $numberXPos = $this->getPageMeasurements()['textWidth']
                        - $this->getMargins('right')
                        - $pdf->GetStringWidth($this->getFormattedNumbers())
                        + $this->toRoot()->getPageMargins('left');
        $numberYPos = $pdf->GetY()
                        + $size['height']/2
                        - $pdf->getStringHeight(0, $this->getFormattedNumbers())/2;
        $pdf->SetXY($numberXPos, $numberYPos);
        $pdf->Cell(0, 0, $this->getFormattedNumbers());
        $pdf->SetY($pdf->GetY() + $this->getMargins('bottom') + $size['height']);

        return $pdf->getPage() - $startPage;
    }


    protected function applyScalingFactor(array $size)
    {
        $factor = $this->getScalingFactor();
        return ['width' => $size['width'] * $factor,
                'height' => $size['height'] * $factor];
    }

    /**
     * todo: doc
     * @return array
     */
    protected function getSizeFromFile()
    {
        $svgSize = new SVGSize($this->getPath());
        $dimensions = $svgSize->getDimensions();

        $pdf = $this->toRoot()->getPDF();

        //convert given unit (usually "ex") to user-units
        $width = $pdf->getHTMLUnitToUnits(
            $dimensions['width'],
            $pdf->getFontSize(),
            $dimensions['wUnit']
        );
        $height = $pdf->getHTMLUnitToUnits(
            $dimensions['height'],
            $pdf->getFontSize(),
            $dimensions['hUnit']
        );

        return ['width' => $width,
                'height' => $height];
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
        if (empty($this->path)) {
            $filename = Flatplane::getCacheDir().DIRECTORY_SEPARATOR.
            'formulas'.DIRECTORY_SEPARATOR.$this->getHash().'.svg';
            $this->setPath($filename);
        }
        return $this->path;
    }

    public function getFormulaFont()
    {
        return $this->formulaFont;
    }

    public function setPath($path)
    {
        //todo: check permissions
        $this->path = $path;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setFormulaFont($font)
    {
        if (!in_array($font, self::$availableFonts, true)) {
            trigger_error(
                "Font $font not available, defaulting to TeX",
                E_USER_NOTICE
            );
            $font = 'TeX';
        }
        $this->formulaFont = $font;
    }

    public function setCodeFormat($codeFormat)
    {
        if (!in_array($codeFormat, self::$availableCodeFormats, true)) {
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

    public function setUseCache($useCache)
    {
        $this->useCache = (bool) $useCache;
    }

    public function getFormulaStyle()
    {
        return $this->formulaStyle;
    }

    public function setFormulaStyle($formulaStyle)
    {
        $this->formulaStyle = $formulaStyle;
    }

    //todo: test me more! (remove styles etc)
    public function applyStyles($key = null)
    {
        parent::applyStyles($key);
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

    public function getScalingFactor()
    {
        return $this->scalingFactor;
    }

    public function setScalingFactor($scalingFactor)
    {
        $this->scalingFactor = $scalingFactor;
    }

    public function getNumberPosition()
    {
        return $this->numberPosition;
    }

    public function setNumberPosition($numberPosition)
    {
        $this->numberPosition = $numberPosition;
    }
}
