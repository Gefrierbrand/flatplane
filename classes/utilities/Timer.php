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

    public function __construct()
    {
        $now = microtime();
        $this->time = $now;
        $this->last = $now;
    }

    public function now($desc)
    {
        $now = microtime();
        $diff_total = $now - $this->time;
        $diff_last  = $now - $this->last;
        echo PHP_EOL . "$desc: To Last: $diff_last Total: $diff_total". PHP_EOL;
        $this->last = $now;
    }

    public function __destruct()
    {
        $this->now('Gesamte Skriptlaufzeit');
    }
}
