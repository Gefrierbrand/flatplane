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

namespace de\flatplane\styles;

use de\flatplane\interfaces\StyleInterface;

abstract class GeneralStyles implements StyleInterface
{
    protected $fontColor = [0,0,0];
    protected $drawColor = [0,0,0];
    protected $fontType = 'times';
    protected $fontSize = 12;

    public function getDrawColor()
    {
        return $this->drawColor;
    }

    public function getFontColor()
    {
        return $this->fontColor;
    }

    public function getFontSize()
    {
        return $this->fontSize;
    }

    public function getFontType()
    {
        return $this->fontType;
    }

    public function setDrawColor($color)
    {
        $this->drawColor = $color;
    }

    public function setFontColor($color)
    {
        $this->fontColor = $color;
    }

    public function setFontSize($size)
    {
        $this->fontSize = $size;
    }

    public function setFontType($type)
    {
        $this->fontType = $type;
    }
}
