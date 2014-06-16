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

use de\flatplane\controller\Flatplane;

/**
 * Description of OSPaths
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class OSPaths
{
    public static function getPhantomJsPath()
    {
        $osTpye = php_uname('s');
        $osArchitecture = php_uname('m');

        $pathSuffix = '';

        switch (strtolower($osTpye)) {
            case 'winnt':
            case 'windows':
            case 'windows nt':
                $pathSuffix = '_win'.DIRECTORY_SEPARATOR.'phantomjs.exe';
                break;
            case 'linux':
                if ($osArchitecture == 'x86_64' || $osArchitecture == 'amd64') {
                    $pathSuffix = '_linux64'.DIRECTORY_SEPARATOR.'bin'
                                  .DIRECTORY_SEPARATOR.'phantomjs';
                } else {
                    $pathSuffix = '_linux32'.DIRECTORY_SEPARATOR.'bin'
                                  .DIRECTORY_SEPARATOR.'phantomjs';
                }
                break;
            case 'darwin': //Mac-Os
                $pathSuffix = '_osx'.DIRECTORY_SEPARATOR.'bin'
                              .DIRECTORY_SEPARATOR.'phantomjs';
                break;
            default:
                Flatplane::log(
                    'Operating System could not be determined, defaulting to windows'
                );
                $pathSuffix = '_win'.DIRECTORY_SEPARATOR.'phantomjs.exe';
                break;
        }

        $pantomPath = 'vendor'.DIRECTORY_SEPARATOR.'phantomjs'
                      .DIRECTORY_SEPARATOR.'phantomjs'.$pathSuffix;
        return $pantomPath;
    }
}
