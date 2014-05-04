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

use de\flatplane\interfaces\ConfigInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * This class holds configuration settings and is used by various other classes
 * The settings can either be loaded from a configurationfile or be provided
 * as key=>value pairs at initialisation
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Config implements ConfigInterface
{
    /**
     * @var mixed
     *  Holds Settings as array or null if unitialized
     */
    protected $settings = null;

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
        if (!empty($configFile)) {
            $this->loadFile($configFile);
        }

        if (!empty($settings)) {
            $this->setSettings($settings);
        }
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
     * is thrown. If no settings are present, a RuntimeException is thrown
     * @param string $key (optional)
     * @param string $subKey (optional)
     * @return mixed
     *  Returns the value of the requested setting for the $key or $subkey or
     *  the whole settings-array if no key is specified.
     * @throws InvalidArgumentException, RuntimeException
     */
    public function getSettings($key = null, $subKey = null)
    {
        if ($this->settings === null) {
            throw new RuntimeException(
                'There are no settings in the current elements Config'
            );
        }

        if ($key === null) {
            $value = $this->settings;
        } else {
            if (array_key_exists($key, $this->settings)) {
                if ($subKey == null) {
                    $value = $this->settings[$key];
                } else {
                    if (!isset($this->settings[$key][$subkey])) {
                        throw new \RuntimeException('Subkey does not exist');
                    } else {
                        $value = $this->settings[$key][$subKey];
                    }
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
                'The key "'.$key.'" does not exist in the configuration.'
            );
        }
        return $value;
    }

    /**
     * This method parses a given INI-File and returns the read settings as
     *  array or false on failure
     * @param string $file
     *  path to the configuration file
     * @return mixed
     */
    protected function parse($file)
    {
        if (!$erg = parse_ini_file($file)) {
            return false;
        }
        //replace specific strings by an array, as INI-file-values are always
        //returned as strings
        array_walk_recursive($erg, [$this, 'checkSettingsValues']);
        return $erg;
    }

    /**
     * This method replaces a string with an array if the strings content match
     * a pattern. Otherwise the string is left unchanged. This method
     * operateds on a reference and therefore the original string is altered!
     * @param string $value
     *  Reference to an entry in the settings-array
     */
    protected function checkSettingsValues(&$value)
    {
        //match strings of the with an array-structure: '[a, b, c, ... , n]'
        $pattern = '/^\[([^\[\],]*,+[^,\[\]]{1})+\]$/';
        if (is_string($value) && preg_match($pattern, $value)) {
            $value = explode(',', trim($value, '[]'));
        }
    }

    /**
     * loads the settings from a configuration file into an array
     * @param string $file path to configuration file (absolut or relative)
     * @throws RuntimeException
     */
    protected function loadFile($file)
    {
        if (!is_readable($file)) {
            throw new RuntimeException($file. ' is not readable');
        }

        $this->settings = $this->parse($file);
        if ($this->settings === false) {
            throw new RuntimeException($file. ' could not be parsed');
        }
    }
}
