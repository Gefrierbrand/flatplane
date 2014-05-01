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
class SectionStyle extends GeneralStyles implements SectionStyleInterface
{
    //TODO: use config file for defaults
    protected $startsNewLine = true;
    protected $minFreePage = 25; //percent

    public function __construct($level = 0, $configFile = '')
    {
        $this->loadDefaults($level, $configFile);
    }

    public function loadDefaults($level, $configFile)
    {
        $config = parent::loadDefaults();
        //$config[] = Config::loadFile($configFile);
        //todo: fixme
    }

    public function getStartsNewLine()
    {
        return $this->startsNewLine;
    }

    public function getMinFreePage()
    {
        return $this->minFreePage;
    }

    public function setStartsNewLine($startsNewLine)
    {
        $this->startsNewLine = $startsNewLine;
    }

    public function setMinFreePage($minFreePage)
    {
        $this->minFreePage = $minFreePage;
    }
}
