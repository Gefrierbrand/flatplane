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
    use \de\flatplane\documentContents\traits\SetSettings;
    /**
     * @var mixed
     *  Holds Settings as array or null if unitialized
     */
    protected $settings = [];

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
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
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
