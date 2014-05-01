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
    //TODO: use config file for defaults
    protected $font =  ['type'  => 'times',
                        'size'  => 12,
                        'style' => '',
                        'color' => [0,0,0]];
    protected $drawColor = [0,0,0];
    protected $defaultConfigFile = 'config/generalStyles';

    //todo: fixme;
    public function loadDefaults($configFile)
    {
        $config = Config::loadFile($configFile);
    }

    public function getDrawColor()
    {
        return $this->drawColor;
    }

    public function getFont()
    {
        return $this->font;
    }

    public function setDrawColor(array $color)
    {
        //todo: validate;
        $this->drawColor = $color;
    }

    public function setFont($type, $size, $style = '', $color = array())
    {
        //todo: validate
        $this->font['type'] = $type;
        $this->font['size'] = $size;
        $this->font['style'] = $style;
        $this->font['color'] = $color;
    }
}
