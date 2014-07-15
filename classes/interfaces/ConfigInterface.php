<?php

/*
 * Copyright (C) 2014 Nikolai Neff <admin@flatplane.de>.
 *
 * This file is part of Flatplane.
 *
 * Flatplane is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Flatplane is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Flatplane.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace de\flatplane\interfaces;

/**
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface ConfigInterface
{
    /**
     * Class Constructor
     * @param string $configFile (optional)
     *  Path to configuration file
     * @param array $settings (optional)
     *  Key=>Value pairs of settings wich extend or overwrite the settings loaded
     *  from the configuration file
     */
    public function __construct($configFile = '', array $settings = []);

    /**
     * Overrides or extends the default options with the given settings array
     * @param array $settings
     */
    public function setSettings(array $settings);

    /**
     * @return array
     *  Associative array containint the configuration options
     */
    public function getSettings();
}
