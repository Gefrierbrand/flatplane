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

namespace de\flatplane\utilities;

use DOMDocument;
use DOMNode;
use RuntimeException;

/**
 * This class provides the ability to estimate the intended size of a SVG file
 * in user-units. The results might vary depending on the given unit of the file
 * and it is highly recommended, to either define an absolute unit in the file
 * itself or in the corresponding properties of the image element.
 * For further information, consult the SVG specification:
 * Scalable Vector Graphics (SVG) 1.1 (Second Edition), Chapter 7.10: Units
 * @see http://www.w3.org/TR/SVG/coords.html#Units
 * @author Nikolai Neff <admin@flatplane.de>
 */
class SVGSize
{
    protected $path;
    protected $dom;
    protected $svg;

    /**
     * @param string $path
     * @throws RuntimeException
     */
    public function __construct($path)
    {
        if (!is_readable($path)) {
            throw new RuntimeException('formula svg path not readable');
        }
        $this->path = $path;

        $this->dom = new DOMDocument();
        $this->dom->load($this->getPath());
        $this->getSVGElement();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return DOMDocument
     */
    public function getDom()
    {
        return $this->dom;
    }

    /**
     * @return DOMNode
     */
    public function getSVG()
    {
        return $this->svg;
    }

    /**
     * todo: doc
     * @throws RuntimeException
     */
    protected function getSVGElement()
    {
        //get all SVG elements
        $svgElementList = $this->getDom()->getElementsByTagName('svg');

        //only first element is relevant
        $svgElement = $svgElementList->item(0);

        //if there is no svg element, then the file is invalid
        if (empty($svgElement)) {
            throw new RuntimeException('svg file is invalid');
        }
        $this->svg = $svgElement;
    }

    /**
     * @return array
     */
    public function getDimensions()
    {
        //read width/height attributes
        $width = $this->getSVG()->getAttribute('width');
        $height = $this->getSVG()->getAttribute('height');

        //if the width/height attributes are not present, try the style attribute
        if (empty($width) || empty($height)) {
            return $this->getDimensionsFromStyle();
        } else {
            return $this->getDimensionsFromWH($width, $height);
        }
    }

    /**
     * @return array
     * @throws RuntimeException
     */
    protected function getDimensionsFromStyle()
    {
        $style = $this->svg->getAttribute('style');

        if (empty($style)) {
            throw new RuntimeException(
                'no valid size information found in file '.$this->getPath()
            );
        }

        //add brackets to get a non-inline style definition
        $css = '{'.$style.'}';

        //parse css
        $parser = new CSSParser($css);
        return $parser->getDimensions();
    }

    /**
     * @param mixed $width
     * @param mixed $height
     * @return array
     * @throws RuntimeException
     */
    protected function getDimensionsFromWH($width, $height)
    {
        //the content of the width/heigt attribute may lack units
        //in that case values in pixels are used, wich are the specific
        //TCPDF-pixels and my not relate to your monitor or other outputs
        if (!is_numeric($width) || !is_numeric($height)) {
            //search result string for value and units
            $regEx = '/(\d*\.?\d+)([\w]*)/';
            if (!preg_match($regEx, $width, $matches['width'])) {
                throw new RuntimeException(
                    'no valid size information found in file '.$this->getPath()
                );
            }

            if (!preg_match($regEx, $width, $matches['height'])) {
                throw new RuntimeException(
                    'no valid size information found in file '.$this->getPath()
                );
            }
            //use regular-expression result-groups to split into value and unit
            $width = $matches['width'][0];
            $height = $matches['height'][0];
            $wUnit = $matches['width'][1];
            $hUnit = $matches['height'][1];

        } else {
            $wUnit = 'px';
            $hUnit = 'px';
        }

        $dimensions = ['width' => $width,
                       'wUnit' => $wUnit,
                       'height' => $height,
                       'hUnit' => $hUnit];
        return $dimensions;
    }
}
