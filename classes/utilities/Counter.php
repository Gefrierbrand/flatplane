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

namespace de\flatplane\utilities;

use de\flatplane\interfaces\CounterInterface;

/**
 * Description of counter
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Counter implements CounterInterface
{

    protected $value;
    protected $increment = 1;

    public function __construct($startValue = 0, $increment = 1)
    {
        $this->value = $startValue;
        $this->increment = $increment;
        $this->validate();
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Add the provided amount to the current value or increment by the standard
     * increment value for this counter, which defaults to 1. Negative values
     * are possible to decrease the counters value.
     * @param int|float $increment (optional)
     * @return int|float
     *  the counters current value
     */
    public function add($increment = 1)
    {
        $this->validate();
        if (func_num_args() == 0 || !is_numeric($increment)) {
            $this->value += $this->increment;
        } else {
            $this->value += $increment;
        }
        return $this->value;
    }

    /**
     * @return int|float
     *  the counters current value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the counters value to a specific quantity
     * @param int|float $val
     */
    public function setValue($val)
    {
        $this->value = $val;
        $this->validate();
    }

    /**
     * get the current default increment value
     * @return float
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * Set the current default increment value which will be used if add gets
     * called without (or with invalid) parameters
     * @param int|float $inc
     *  new increment value
     */
    public function setIncrement($inc)
    {
        $this->increment = $inc;
        $this->validate();
    }

    /**
     * reset the counters value to zero
     */
    public function resetValue()
    {
        $this->value = 0;
    }

    /**
     * checks if the counters value and increment are numeric
     */
    protected function validate()
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
