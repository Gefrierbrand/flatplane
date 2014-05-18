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
 * Description of Timer
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */

class Timer
{
    protected $time = null;
    protected $last = null;

    public function __construct($showCacheStats = false)
    {
        if ($showCacheStats) {
            $this->showCacheStats();
        }

        $now = microtime(true);
        $this->time = $now;
        $this->last = $now;
    }

    protected function showCacheStats()
    {
        if (extension_loaded('Zend OPcache') && opcache_get_status(true) !== false) {
            echo 'Opcache is on, starting timer<br>'.PHP_EOL;
            echo 'Cached Scripts:<br>'.PHP_EOL;
            $scripts = opcache_get_status(true);
            foreach ($scripts['scripts'] as $key => $script) {
                echo $key.'<br>'.PHP_EOL;
            }
        } else {
            echo 'Opcache is disabled or no scripts are cached<br>'.PHP_EOL;
        }
    }

    public function now($desc)
    {
        $now = microtime(true);
        $diff_total = number_format($now - $this->time, 3, '.', '');
        $diff_last  = number_format($now - $this->last, 3, '.', '');
        echo PHP_EOL.$desc.PHP_EOL;
        echo "Time for last step: $diff_last s";
        echo "\tTotal time: $diff_total s".PHP_EOL.PHP_EOL;
        $this->last = $now;
    }

    public function __destruct()
    {
        $this->now('Gesamte Skriptlaufzeit');
        echo "Peak Memory Usage: ",
              memory_get_peak_usage(true)/ 1024 / 1024,
              " MiB".PHP_EOL;
    }
}
