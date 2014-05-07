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

//TODO: DOC!!!!
//Todo: implement validation for setters
//Todo: implement addfont

abstract class AbstractGeneralStyles implements StyleInterface
{
    protected $margins = ['top' => 0, 'bottom' => 0, 'left' => 0, 'right' => 0];
    protected $paddings = ['top' => 0, 'bottom' => 0, 'left' => 0, 'right' => 0];
    protected $font = ['type' => 'times', 'size' => 12, 'style' => '', 'color' => [0,0,0]];
    protected $drawColor = [0,0,0];

    public function __construct(array $config)
    {
        foreach ($config as $key => $setting) {
            $name = 'set'.ucfirst($key);
            if (method_exists($this, $name)) {
                $this->{$name}($setting);
            }
        }
    }

    public function addFont($type, array $files)
    {
        //todo: implement
    }

    public function getMargins()
    {
        return $this->margins;
    }

    public function getPaddings()
    {
        return $this->paddings;
    }

    public function getFont()
    {
        return $this->font;
    }

    public function getDrawColor()
    {
        return $this->drawColor;
    }

    public function setMargins(array $margins)
    {
        $this->margins = $margins;
    }

    public function setPaddings(array $paddings)
    {
        $this->paddings = $paddings;
    }

    public function setFont(array $font)
    {
        $this->font = $font;
    }

    public function setDrawColor(array $drawColor)
    {
        $this->drawColor = $drawColor;
    }
}
