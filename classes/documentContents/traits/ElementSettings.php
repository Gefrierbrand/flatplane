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

namespace de\flatplane\documentContents\traits;

/**
 * Description of ElementSettings
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
trait ElementSettings
{
    public function setEnumerate($enumerate)
    {
        if ($this->parent !== null) {
            trigger_error(
                'setEnumerate() should not be called after adding the element'.
                ' as content',
                E_USER_WARNING
            );
        }
        $this->getConfig()->setSettings(['enumerate' => $enumerate]);
    }

    public function setShowInIndex($showInIndex)
    {
        if ($this->parent !== null) {
            trigger_error(
                'setShowInIndex() should not be called after adding the element'.
                ' as content',
                E_USER_WARNING
            );
        }
        $this->getConfig()->setSettings(['showInIndex' => $showInIndex]);
    }

    public function getEnumerate()
    {
        return $this->getConfig()->getSettings('enumerate');
    }

    public function getShowInIndex()
    {
        return $this->getConfig()->getSettings('showInIndex');
    }

    public function getTitle()
    {
        return $this->getConfig()->getSettings('title');
    }

    public function getCaption()
    {
        return $this->getConfig()->getSettings('caption');
    }

    public function setTitle($title)
    {
        $this->getConfig()->setSettings(['title' => $title]);
    }

    public function setAltTitle($altTitle)
    {
        $this->getConfig()->setSettings(['altTitle' => $altTitle]);
    }

    public function setCaption($caption)
    {
        $this->getConfig()->setSettings(['caption' => $caption]);
    }

    public function getAltTitle()
    {
        try {
            $erg = $this->getConfig()->getSettings('altTitle');
        } catch (\Exception $e) {
            $erg = $this->getConfig()->getSettings('title');
        }
        return $erg;
    }
}
