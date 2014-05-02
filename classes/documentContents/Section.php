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

namespace de\flatplane\documentContents;

/**
 * Description of section
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Section extends DocumentContentElement
{
    protected $showInDocument = true;
    protected $type = 'section';
    protected $defaultConfigFile = 'config/sectionSettings.ini';

    public function __construct(
        $title,
        $altTitle = '',
        $showInIndex = true,
        $enumerate = true,
        $showInDocument = true
        //$configFile = ''
    ) {
        //$this->loadDefaults($configFile);??

        $this->title = $title;
        $this->showInIndex = $showInIndex;
        $this->enumerate = $enumerate;
        $this->showInDocument = $showInDocument;
        if ($altTitle == '') {
            $this->altTitle = $title;
        } else {
            $this->altTitle = $altTitle;
        }
    }

    public function loadDefaults($configFile)
    {
        if (empty($configFile)) {
            $configFile = $this->defaultConfigFile;
        }

        $config = new Config($configFile);
        $this->setFont(
            $config->getSettings('fontType', $this->level),
            $config->getSettings('fontSize', $this->level),
            $config->getSettings('fontStyle', $this->level),
            $config->getSettings('fontColor', $this->level)
        );
    }
}
