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

namespace de\flatplane\interfaces\documentElements;

use de\flatplane\interfaces\DocumentElementInterface;

/**
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface FormulaInterface extends DocumentElementInterface
{
    /**
     * Get the TeX or MathML code defining the formula
     * @return string
     */
    public function getCode();

    /**
     * Get the codes format, wich is either TeX or MathML
     * @return string
     */
    public function getCodeFormat();

    /**
     * Get the font used for the formula
     * @return type
     */
    public function getFormulaFont();

    /**
     * Get all available formulafonts
     * @return array
     */
    public static function getAvailableFonts();

    /**
     * Get all available Code formats
     * @return array
     */
    public static function getAvailableCodeFormats();

    /**
     * This method returns a (pseudo-unique) hash for the current instance
     * depending on the formula-code and formula-font to be used as filename
     * for the SVG-generation
     * @return string
     */
    public function getHash();

    /**
     * Returns the path to the SVG file corresponding to the current instance if
     * it exists or null otherwise.
     * @return string|null
     */
    public function getPath();

    /**
     * Set the path to the SVG file representaion of the formula
     * @param string $path
     */
    public function setPath($path);

    /**
     * Set the TeX or MathML code representing the formula
     * @param string $code
     */
    public function setCode($code);

    /**
     * Set the formulas font.
     * @param string $font
     * @see getAvailableFonts()
     */
    public function setFormulaFont($font);

    /**
     * Set the codes format. Can currently either be TeX or MathML
     * @param string $codeFormat
     * @see getAvailableCodeFormats()
     */
    public function setCodeFormat($codeFormat);

    /**
     * Determine if the cache is used
     * @return bool
     * @deprecated might get removed since formulas always require the cache
     */
    public function getUseCache();

    /**
     * Enable or disable the cache usage
     * @param bool $useCache
     *  defaults to true
     * @deprecated might get removed since formulas always require the cache
     */
    public function setUseCache($useCache);

    /**
     * Get the formulas output style (inline or display)
     * @return string
     */
    public function getFormulaStyle();

    /**
     * Set the forumulas output style (inline or display)
     * @param string $formulaStyle
     */
    public function setFormulaStyle($formulaStyle);

    /**
     * Get the formulas image-scaling factor
     * @return float
     */
    public function getScalingFactor();

    /**
     * Set the formulas image-scaling factor to adjust its size to the text or
     * other pageelements
     * @param float $scalingFactor
     *  defaults to 0.85
     */
    public function setScalingFactor($scalingFactor);

    /**
     * Get the elements numbering position
     * @return string
     */
    public function getNumberPosition();

    /**
     * Set the elements numbering position
     * @param string $numberPosition
     *  can be 'left' or 'right'
     */
    public function setNumberPosition($numberPosition);
}
