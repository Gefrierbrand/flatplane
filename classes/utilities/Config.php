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

use InvalidArgumentException;
use RuntimeException;

/**
 * This class holds configuration settings and is used by various other classes
 * The settings can either be loaded from a configurationfile or be provided
 * as key=>value pairs at initialisation
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Config
{
    /**
     * @var mixed
     *  Holds Settings as array or null if unitialized
     */
    protected $settings = null;

    /**
     * @var string
     *  Path to default configuration file
     */
    protected $defaultConfigFile = 'config/documentSettings.ini';

    /**
     * @var string
     *  Path to currently loaded configuration file
     */
    protected $loadedConfigFile;

    /**
     * Class Constructor
     * @param string $configFile (optional)
     *  Path to configuration file
     * @param array $settings (optional)
     *  Key=>Value pairs of settings wich extend or overwrite the settings loaded
     *  from the configuration file
     */
    public function __construct($configFile = '', array $settings = [])
    {
        if (empty($configFile)) {
            $configFile = $this->defaultConfigFile;
        }
        $this->loadFile($configFile);
        if (!empty($settings)) {
            $this->setSettings($settings);
        }
    }

    /**
     * loads the settings from a configuration file into an array
     * @param string $file path to configuration file (absolut or relative)
     * @throws RuntimeException
     */
    public function loadFile($file)
    {
        if (!is_readable($file)) {
            throw new RuntimeException($file. ' is not readable');
        }

        $this->settings = $this->parse($file);
        if ($this->settings === false) {
            throw new RuntimeException($file. ' could not be parsed');
        }
        $this->loadedConfigFile = $file;
    }

    /**
     * Overrides or extends the default options with the given settings array
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        if (is_array($this->settings)) {
            //replace defaults with given settings
            foreach ($settings as $key => $value) {
                if (array_key_exists($key, $this->settings)) {
                    if (is_array($value)) {
                        //Merges the given settings with (possibly) already existing
                        //settings instead of overwriting them with an (possibly)
                        //incomplete array
                        $this->settings[$key] = array_merge(
                            $this->settings[$key],
                            $value
                        );
                    } else {
                        $this->settings[$key] = $value;
                    }
                }
            }
        } else {
            $this->settings = $settings;
        }
    }

    /**
     * This method returns the value of a specific setting for a given
     * $key or $key/$subkey-pair. If the $key does not exist, it tries to return
     * the value of a default key. If this also fails, an InvalidArgumentExeption
     * is thrown.
     * @param string $key (optional)
     * @param string $subKey (optional)
     * @return mixed
     *  Returns the value of the requested setting for the $key or $subkey or
     *  the whole settings-array if no key is specified.
     * @throws InvalidArgumentException
     */
    public function getSettings($key = null, $subKey = null)
    {
        if ($this->settings === null) {
            $this->loadFile($this->defaultConfigFile);
        }

        if ($key === null) {
            $value = $this->settings;
        } else {
            if (array_key_exists($key, $this->settings)) {
                if ($subKey !== null &&
                    is_array($this->settings[$key]) &&
                    array_key_exists($subKey, $this->settings[$key])
                ) {
                    $value = $this->settings[$key][$subKey];
                } else {
                    $value = $this->searchDefaults($key);
                }
            } else {
                $value = $this->searchDefaults($key);
            }
        }
        return $value;
    }

    /**
     *
     * @param string $key
     * @throws InvalidArgumentException
     * @returns mixed
     */
    protected function searchDefaults($key)
    {
        //fall back to default setting if specific setting does not exist
        $defaultKey = 'default'.ucfirst($key);
        if (array_key_exists($defaultKey, $this->settings)) {
            $value = $this->settings[$defaultKey];
        } else {
            throw new InvalidArgumentException(
                'The key "'.$key.'" does not exist in the configuration.'.
                ' Loaded configfile: "'.$this->loadedConfigFile.'"'
            );
        }
        return $value;
    }

    protected function parse($file)
    {
        if (!$erg = parse_ini_file($file)) {
            return false;
        }
        array_walk_recursive($erg, [$this, 'checkSettingsValues']);
        return $erg;
    }

    protected function checkSettingsValues(&$value)
    {
        //match strings of the with an array-structure: '[a, b, c, ... , n]'
        $pattern = '/^\[([^\[\],]*,+[^,\[\]]{1})+\]$/';
        if (is_string($value) && preg_match($pattern, $value)) {
            $value = explode(',', trim($value, '[]'));
        }
    }
}
