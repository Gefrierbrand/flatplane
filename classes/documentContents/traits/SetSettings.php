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
 * Description of SetSettings
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
trait SetSettings
{
    /**
     * Overrides or extends the default options with the given settings array
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        //replace defaults with given settings
        foreach ($settings as $key => $value) {
            if (array_key_exists($key, $this->settings)
                && is_array($value)
                && is_array($this->settings[$key])
            ) {
                //Merges the given settings with already existing settings
                //instead of completely overwriting them with (possibly)
                //incomplete data
                $this->settings[$key] = array_merge(
                    $this->settings[$key],
                    $value
                );
            } else {
                $this->settings[$key] = $value;
            }
        }
    }
}
