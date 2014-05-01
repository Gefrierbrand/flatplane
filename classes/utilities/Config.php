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
class Config
{
    protected $settings = null;
    protected $defaultConfigFile = 'config/documentSettings.ini';

    public function __construct($configFile = '')
    {
        if (empty($configFile)) {
            $configFile = $this->defaultConfigFile;
        }
        $this->loadFile($configFile);
    }

    /**
     * loads the settings from a configuration file into an array
     * @param string $file Path to configuration file (absolut or relative)
     * @throws \RuntimeException
     */
    public function loadFile($file)
    {
        if (!is_readable($file)) {
            throw new \RuntimeException($file. ' is not readable');
        }

        $this->settings = parse_ini_file($file);
        if ($this->settings === false) {
            throw new \RuntimeException($file. ' could not be parsed');
        }
    }

    /**
     * Overrides or extends the default options with the given settings array
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        if ($this->settings === null) {
            $this->loadFile($this->defaultConfigFile);
        }

        //replace defaults with given settings
        foreach ($settings as $key => $value) {
            if (array_key_exists($key, $this->settings)) {
                if (is_array($value)) {
                    //TODO: DOC!
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

    /**
     * FIXME: Make this nice!
     * @param string $key (optional)
     * @param string $subKey (optional)
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getSettings($key = null, $subKey = null)
    {
        if ($this->settings === null) {
            $this->loadFile();
        }

        if ($key === null) {
            $value = $this->settings;
        } else {
            if (array_key_exists($key, $this->settings)) {
                $value = $this->settings[$key];
            } else {
                //fall back to default setting if specific setting does not exist
                $defaultKey = 'default'.ucfirst($key);
                if (array_key_exists($defaultKey, $this->settings)) {
                    $value = $this->settings[$defaultKey];
                } else {
                    throw new \InvalidArgumentException(
                        'The key '.$key.' does not exist in the configuration.'
                    );
                }
            }
        }

        if (is_array($value) && $subKey !== null) {
            if (array_key_exists($subKey, $value)) {
                $value = $value[$subKey];
            }
        }
        return $value;
    }
}
