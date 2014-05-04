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

namespace de\flatplane\documentcontents\traits;

/**
 * Description of StyleFunctions
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class StyleFunctions
{
    public function getDrawColor()
    {
        return $this->config->getSettings('defaultDrawColor');
    }

    /**
     *
     * @return array
     *  Returns array with the keys 'type', 'size', 'style' and 'color'
     */
    public function getFont()
    {
        return $this->config->getSettings('defaultFont');
    }

    public function setDrawColor(array $color)
    {
        $this->config->setSettings(['drawColor' => $color]);
    }

    public function setFont($type, $size = 12, $style = '', array $color = [0,0,0])
    {
        //todo: validate, ggf addfonts to pdf
        $this->config->setSettings(
            ['defaultFont' =>
                [
                    'type' => $type,
                    'size' => $size,
                    'style' => $style,
                    'color' => $color
                ]
            ]
        );
    }
}
