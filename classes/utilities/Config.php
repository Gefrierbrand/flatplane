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
    protected static $settings = null;
    protected static $defaultConfigFile = 'config/documentSettings.ini';

    /**
     * loads the default settings from a configuration file into an array
     * @throws \RuntimeException
     */
    public static function loadFile($file = '')
    {
        if ($file == '') {
            $file = self::$defaultConfigFile;
        }

        if (!is_readable($file)) {
            throw new \RuntimeException($file. ' is not readable');
        }

        self::$settings = parse_ini_file($file);
        if (self::$settings === false) {
            throw new \RuntimeException($file. ' could not be parsed');
        }
    }

    /**
     * Overrides or extends the default options with the given settings array
     * @param array $settings
     */
    public static function setSettings(array $settings)
    {
        if (self::$settings === null) {
            self::loadFile();
        }

        //replace defaults with given settings
        foreach ($settings as $key => $value) {
            if (array_key_exists($key, self::$settings)) {
                if (is_array($value)) {
                    //TODO: DOC!
                    self::$settings[$key] = array_merge(
                        self::$settings[$key],
                        $value
                    );
                } else {
                    self::$settings[$key] = $value;
                }
            }
        }
    }

    /**
     * @param string $key (optional)
     * @return array
     * @throws InvalidArgumentException
     */
    public static function getSettings($key = null)
    {
        if (self::$settings === null) {
            self::loadFile();
        }

        //FIXME: fallback / errors?
        if ($key === null) {
            return self::$settings;
        } else {
            if (array_key_exists($key, self::$settings)) {
                return self::$settings[$key];
            } else {
                return null;
            }
        }
    }
}
