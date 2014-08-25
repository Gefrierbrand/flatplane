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

namespace de\flatplane\documentElements\traits;

use TCPDF;

/**
 * Description of StyleFunctions
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
trait StyleFunctions
{
    /**
     * @var array
     *  defines the paddings of text-content in cells in user units
     *  Standard keys are:
     *  'top', 'bottom', 'left', 'right'. Subclasses might define their own keys.
     *  If any of those are undefined, the value of the key 'default' is used.
     */
    protected $cellPaddings = ['default' => 0];

    /**
     * @var array
     *  defines the margins of text-content in cells in user units
     *  Standard keys are:
     *  'top', 'bottom', 'left', 'right'. Subclasses might define their own keys.
     *  If any of those are undefined, the value of the key 'default' is used.
     */
    protected $cellMargins = ['default' => 0];

    /**
     * @var array
     *  defines the font-type/name/family to be used. Possible values are the
     *  name of a font-file or the family-identifier used by TCPDF::addFont()
     * @see TCPDF::addFont()
     */
    protected $fontType = ['default' => 'times'];

    /**
     * @var array
     *  possible values: Font size in pt
     */
    protected $fontSize = ['default' => 12];

    /**
     * @var array
     *  possible values: Font variations as strings:
     *  <ul>
     *   <li>(empty): normal</li>
     *   <li>U: underline</li>
     *   <li>D: strikethrough</li>
     *   <li>B: bold</li>
     *   <li>I: italic</li>
     *   <li>O: overline</li>
     *  </ul>
     * The variations can be combined (in any order): for example use 'BIU' to
     * create bold-italic-underlined text
     */
    protected $fontStyle = ['default' => ''];

    /**
     * Color used for text
     * @var array
     *  possible values:
     *  array containing 1 value (0-255) for grayscale
     *  array containing 3 values (0-255) for RGB colors or
     *  array contining 4 values (0-100) for CMYK colors
     */
    protected $fontColor = ['default' => [0,0,0]];

    /**
	 * @var array
     *  value (float): amount to increase or decrease the space between
     *  characters in a text (0 = default spacing)
     * @see TCPDF::setFontSpacing()
     */
    protected $fontSpacing = ['default' => 0];

    /**
	 * @var int percentage of stretching (default value: 100)
     * @see TCPDF::setFontStretching()
     */
    protected $fontStretching = ['default' => 100];

    /**
     * Color used for drawings (includes some font-styles like underline)
     * @var array
     *  possible values:
     *  array containing 1 value (0-255) for grayscale
     *  array containing 3 values (0-255) for RGB colors or
     *  array contining 4 values (0-100) for CMYK colors
     */
    protected $drawColor = ['default' => [0,0,0]];

    /**
     * Color used for fillings like cell-backgrounds
     * @var array
     *  possible values:
     *  array containing 1 value (0-255) for grayscale
     *  array containing 3 values (0-255) for RGB colors or
     *  array contining 4 values (0-100) for CMYK colors
     * @ignore todo: associative keys?
     */
    protected $fillColor = ['default' => [255,255,255]];

    /**
     * @var float
     *  line-pitch scaling factor. Adjust thois to increase or decrease the
     *  vertical distance between lines relative to the font-size
     * @see TCPDF::setCellHeightRatio()
     */
    protected $linePitch = 1.25;

    /**
     * This method sets the current graphic state in the PDF. This includes
     * fonts, cell-margins and -paddings and colors.
     * @param string $key (optional)
     *  name of a specific configuration directive to use if multiple styles are
     *  defined for the object. e.g. 'level1'
     */
    public function applyStyles($key = null)
    {
        $pdf = $this->getPDF();
        $pdf->SetFont(
            $this->getFontType($key),
            $this->getFontStyle($key),
            $this->getFontSize($key)
        );
        $pdf->setColorArray('text', $this->getFontColor($key));
        $pdf->setColorArray('draw', $this->getDrawColor($key));
        $pdf->setColorArray('fill', $this->getFillColor($key));
        $pdf->setFontSpacing($this->getFontSpacing($key));
        $pdf->setFontStretching($this->getFontStretching($key));

        $pdf->setCellMargins(
            $this->getCellMargins('left'),
            $this->getCellMargins('top'),
            $this->getCellMargins('right'),
            $this->getCellMargins('bottom')
        );

        $pdf->setCellPaddings(
            $this->getCellPaddings('left'),
            $this->getCellPaddings('top'),
            $this->getCellPaddings('right'),
            $this->getCellPaddings('bottom')
        );

        $pdf->setCellHeightRatio($this->getLinePitch());
    }

    /**
     * Specify the font to use for the content defined by the array-keys
     * must contain entry 'default'
     * @param array $fontType
     */
    public function setFontType(array $fontType)
    {
        $this->fontType = array_merge($this->fontType, $fontType);
    }

    /**
     * Set the fontsize in pt (1 pt = 1/72 inch) for the content defined by the
     * array keys - must contain entry 'default'
     * @param array $fontSize
     */
    public function setFontSize(array $fontSize)
    {
        $this->fontSize = array_merge($this->fontSize, $fontSize);
    }

    /**
     * Set the font markup like bold or italic for the content defined by the
     * array keys - must contain entry 'default'
     * available values are B: bold; I: italic; U: underline; D: line-through;
     * O: overline or any combination of those
     * @see TCPDF::setFont()
     * @param array $fontStyle
     */
    public function setFontStyle(array $fontStyle)
    {
        $this->fontStyle = array_merge($this->fontStyle, $fontStyle);
    }

    /**
     * Sets the font color for the content defined by the array keys.
     * @see TCPDF::setColor();
     * @param array $fontColor
     *  Must contain the key 'default'
     *  The array values must also be arrays containing the color-values
     *  first entry: GRAY level, or Red for RGB (0-255), or CYAN for CMYK (0-100)
	 *  second entry: GREEN for RGB (0-255), or MAGENTA for CMYK (0-100)
	 *  third entry: BLUE for RGB (0-255), or YELLOW for CMYK (0-100)
     *  forth entry: KEY (Black) for CMYK (0-100)
     */
    public function setFontColor(array $fontColor)
    {
        $this->fontColor = array_merge($this->fontColor, $fontColor);
    }

    /**
     * Set the color used for drawings like lines (affects over & underline!)
     * @see setFontColor()
     * @param array $drawColor
     */
    public function setDrawColor(array $drawColor)
    {
        $this->drawColor = array_merge($this->drawColor, $drawColor);
    }

    /**
     * Get the fontspacing for the defined key or return the default value
     * @param string $key (optional)
     * @return float
     */
    public function getFontSpacing($key = null)
    {
        if ($key !== null && isset($this->fontSpacing[$key])) {
            return $this->fontSpacing[$key];
        } else {
            return $this->fontSpacing['default'];
        }
    }

    /**
     * Get the fontstretching for the defined key or return the default value
     * @param string $key (optional)
     * @return float
     */
    public function getFontStretching($key = null)
    {
        if ($key !== null && isset($this->fontStretching[$key])) {
            return $this->fontStretching[$key];
        } else {
            return $this->fontStretching['default'];
        }
    }

    /**
     * Set the fontspacing to adjust the whitespace between words.
     * @param array $fontSpacing
     *  must define key 'default'; standard value: 0
     * @see TCPDF::setFontSpacing()
     */
    public function setFontSpacing(array $fontSpacing)
    {
        $this->fontSpacing = array_merge($this->fontSpacing, $fontSpacing);
    }

    /**
     * Set the percentage of character stretching.
     * @param array $fontStretching
     *  must define key 'default'; standard value: 100
     * @see TCPDF::setFontStretching()
     */
    public function setFontStretching(array $fontStretching)
    {
        $this->fontStretching = array_merge(
            $this->fontStretching,
            $fontStretching
        );
    }

    /**
     * Get the font name for the given key (like 'level1'). If the key is omitted
     * or not defined in the elements configuration, a default value is returned.
     * @param string $key
     * @return string
     */
    public function getFontType($key = null)
    {
        if ($key !== null && isset($this->fontType[$key])) {
            return $this->fontType[$key];
        } else {
            return $this->fontType['default'];
        }
    }

    /**
     * Get the fontsize (in pt) for the given key (like 'level1'). If the key is
     * omitted or not defined in the elements configuration, a default value is
     * returned.
     * @param string $key
     * @return string|int
     *  Fontsize (in pt)
     */
    public function getFontSize($key = null)
    {
        if ($key !== null && isset($this->fontSize[$key])) {
            return $this->fontSize[$key];
        } else {
            return $this->fontSize['default'];
        }
    }

    /**
     * Get the font style (bold, italic, etc.) for the given key (like 'level1').
     * If the key is omitted or not defined in the elements configuration, a
     * default value is returned. The 'normal' font style is represented by an
     * empty string
     * @param string $key
     * @return string
     */
    public function getFontStyle($key = null)
    {
        if ($key !== null && isset($this->fontStyle[$key])) {
            return $this->fontStyle[$key];
        } else {
            return $this->fontStyle['default'];
        }
    }

    /**
     * Get the font color for the given key (like 'level1').
     * If the key is omitted or not defined in the elements configuration, a
     * default value is returned.
     * @param string $key
     * @return array
     *  Array containig 1, 3 or 4 numbers to represent a color in grayscale, RGB
     *  or CMYK
     * @see TCPDF::setColor()
     */
    public function getFontColor($key = null)
    {
        if ($key !== null && isset($this->fontColor[$key])) {
            return $this->fontColor[$key];
        } else {
            return $this->fontColor['default'];
        }
    }

    /**
     * Get the draw color for the given key (like 'level1'). This color is used
     * for borders, under- or overline and other non-text elements in the PDF.
     * If the key is omitted or not defined in the elements configuration, a
     * default value is returned.
     * @param string $key
     * @return array
     *  Array containig 1, 3 or 4 numbers to represent a color in grayscale, RGB
     *  or CMYK
     * @see TCPDF::setColor()
     */
    public function getDrawColor($key = null)
    {
        if ($key !== null && isset($this->drawColor[$key])) {
            return $this->drawColor[$key];
        } else {
            return $this->drawColor['default'];
        }
    }

    /**
     * Get the fill color for the given key (like 'level1'). This color is used
     * to fill cells in the PDF.
     * If the key is omitted or not defined in the elements configuration, a
     * default value is returned.
     * @param string $key
     * @return array
     *  Array containig 1, 3 or 4 numbers to represent a color in grayscale, RGB
     *  or CMYK
     * @see TCPDF::setColor()
     */
    public function getFillColor($key = null)
    {
        if ($key !== null && isset($this->fillColor[$key])) {
            return $this->fillColor[$key];
        } else {
            return $this->fillColor['default'];
        }
    }

    /**
     * @todo: rename&getall
     * @param string $key
     * @return float
     */
    public function getCellMargins($key = null)
    {
        if ($key !== null && isset($this->cellMargins[$key])) {
            return $this->cellMargins[$key];
        } else {
            return $this->cellMargins['default'];
        }
    }

    /**
     * @todo: s.o.
     * @param string $key
     * @return float
     */
    public function getCellPaddings($key = null)
    {
        if ($key !== null && isset($this->cellPaddings[$key])) {
            return $this->cellPaddings[$key];
        } else {
            return $this->cellPaddings['default'];
        }
    }

    /**
     *
     * @param array $cellMargins
     *  keys: 'top', 'bottom', 'left', 'right'
     *  values: (numeric) margin amount (user units)
     */
    public function setCellMargins(array $cellMargins)
    {
        $this->cellMargins = array_merge($this->cellMargins, $cellMargins);
    }

    /**
     *
     * @param array $cellPaddings
     *  keys: 'top', 'bottom', 'left', 'right'
     *  values: (numeric) margin amount (user units)
     */
    public function setCellPaddings(array $cellPaddings)
    {
        $this->cellPaddings = array_merge($this->cellPaddings, $cellPaddings);
    }

    /**     *
     * @return float
     */
    public function getLinePitch()
    {
        return $this->linePitch;
    }

    /**
     *
     * @param float $linePitch
     */
    public function setLinePitch($linePitch)
    {
        $this->linePitch = $linePitch;
    }
}
