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

use de\flatplane\interfaces\SectionStyleInterface;
use de\flatplane\utilities\Config;

/**
 * Description of SectionStyle
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class TableStyle extends AbstractGeneralStyles implements SectionStyleInterface
{
    protected $defaultConfigFile = 'config/sectionStyles.ini';
    protected $level;

    public function __construct($level = 0, Config $config = null)
    {
        parent::__construct($config);
        $this->level = $level;
    }

    public function getDrawColor()
    {
        return $this->config->getSettings('drawColor', $this->level);
    }

    public function getFont()
    {
        $font['type'] = $this->config->getSettings('fontType', $this->level);
        $font['size'] = $this->config->getSettings('fontSize', $this->level);
        $font['style'] = $this->config->getSettings('fontStyle', $this->level);
        $font['color'] = $this->config->getSettings('fontColor', $this->level);

        return $font;
    }

    public function setDrawColor(array $color)
    {
        $this->config->setSettings(['drawColor' => [$this->level => $color]]);
    }

    public function setFont($type, $size = 12, $style = '', array $color = [0,0,0])
    {
        //todo: validate, ggf addfonts to pdf
        $this->config->setSettings(
            [
                'fontType' => [$this->level => $type],
                'fontSize' => [$this->level => $size],
                'fontStyle' => [$this->level => $style],
                'fontColor' => [$this->level => $color]
            ]
        );
    }

    public function getStartsNewLine()
    {
        return $this->config->getSettings('startsNewLine', $this->level);
    }

    public function getMinFreePage()
    {
        return $this->config->getSettings('minFreePage', $this->level);
    }

    public function setStartsNewLine($startsNewLine)
    {
        $this->config->setSettings(
            ['startsNewLine' => [$this->level => $startsNewLine]]
        );
    }

    public function setMinFreePage($minFreePage)
    {
        $this->config->setSettings(
            ['minFreePage' => [$this->level => $minFreePage]]
        );
    }
}
