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

namespace de\flatplane\utilities;

/**
 * Description of Settings
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Settings
{
    protected $settings;
    protected $defaultConfigFile = 'config/documentSettings.ini';

    public function __construct(array $settings = null, $configFile = '')
    {
        if ($configFile == '') {
            $configFile = $this->defaultConfigFile;
        }

        //load default settings from ini file
        if (!is_readable($configFile)) {
            throw new \RuntimeException($configFile. ' is not readable');
        }

        $this->settings = parse_ini_file($configFile);
        if ($this->settings === false) {
            throw new \RuntimeException($configFile. ' could not be parsed');
        }

        //replace defaults with given settings
        foreach ($settings as $key => $value) {
            if (array_key_exists($key, $this->settings)) {
                $this->settings[$key] = $value;
            }
        }
    }

    /**
     * @param string $key (optional)
     * @return \de\flatplane\utilities\Settings
     * @throws InvalidArgumentException
     */
    public function getSettings($key = null)
    {
        if ($key === null) {
            return $this->settings;
        } else {
            if (array_key_exists($key, $this->settings)) {
                return $this->settings[$key];
            } else {
                //TODO: maybe just warn?
                throw new InvalidArgumentException('Settings key '.$key.' not found');
            }
        }
    }
}
