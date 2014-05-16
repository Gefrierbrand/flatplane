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
namespace de\flatplane;

use de\flatplane\utilities\Timer;

//todo: doc;

define('FLATPLANE_VERSION', '0.1-dev');
define('FLATPLANE_PATH', __DIR__);
define('FLATPLANE_IMAGE_PATH', FLATPLANE_PATH.DIRECTORY_SEPARATOR.'images');
define('FLATPLANE_OUTPUT_PATH', FLATPLANE_PATH.DIRECTORY_SEPARATOR.'output');
define('FLATPLANE_VERBOSE', true);

include(
    FLATPLANE_PATH.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php'
);

$t = new Timer();
