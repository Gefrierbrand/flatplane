<?php

/*
 * Copyright (C) 2014 Nikolai Neff <admin@flatplane.de>.
 *
 * This file is part of Flatplane.
 *
 * Flatplane is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Flatplane is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace de\flatplane;

/**
 * Description of counter
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Counter
{

    protected $value;
    //protected $format = 'int';
    protected $increment = 1;

    public function __construct($startValue = 0, $increment = 1)
    {
        $this->value = $startValue;
        //$this->format = $format;
        $this->increment = $increment;
        $this->validate();
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    public function add($increment = 1)
    {
        $this->validate();
        if (func_num_args() == 0) {
            $this->value += $this->increment;
        } else {
            $this->value += $increment;
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($val)
    {
        $this->value = $val;
        $this->validate();
    }

    public function getIncrement()
    {
        return $this->increment;
    }

    public function setIncrement($inc)
    {
        $this->increment = $inc;
        $this->validate();
    }

    public function resetValue()
    {
        $this->value = 0;
    }

    protected function validate() //TODO: gf schöne Fehlermeldungen
    {
        if (!is_numeric($this->increment)) {
            trigger_error(
                'Counter-Increment should be numeric, ' .
                gettype($this->increment) . ' was given.',
                E_USER_NOTICE
            );
        }
        if (!is_numeric($this->value)) {
            trigger_error(
                'Value should be numeric, ' .
                gettype($this->value) . ' was given.',
                E_USER_NOTICE
            );
        }
    }
}
