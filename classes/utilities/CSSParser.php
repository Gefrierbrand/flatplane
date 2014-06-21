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

namespace de\flatplane\utilities;

use RuntimeException;
use Sabberworm\CSS\Parser;

/**
 * Description of CSSParser
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class CSSParser
{
    protected $parser;

    public function __construct($css)
    {
        $this->parser = new Parser($css);
    }

    public function getDimensions()
    {
        $declarationBlocks = $this->parser->parse()->getAllDeclarationBlocks();
        $width= $declarationBlocks[0]->getRules('width');
        $height= $declarationBlocks[0]->getRules('height');

        if (empty($width) || empty($height)) {
            return false;
        }

        if (!is_array($height) || !is_array($width)) {
            throw new RuntimeException(
                'invalid css parsing results; width or height may be unset'
            );
        }

        $wVal = $width[0]->getValue()->getSize();
        $wUnit = $width[0]->getValue()->getUnit();
        $hVal = $height[0]->getValue()->getSize();
        $hUnit = $height[0]->getValue()->getUnit();

        return ['width' => $wVal,
                'wUnit' => $wUnit,
                'height' => $hVal,
                'hUnit' => $hUnit];
    }
}
