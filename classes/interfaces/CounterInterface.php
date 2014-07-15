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
interface CounterInterface
{
    /**
     * Add the provided amount to the current value or increment by the standard
     * increment value for this counter, which defaults to 1. Negative values
     * are possible to decrease the counters value.
     * @param int|float $increment (optional)
     * @return int|float
     *  the new value of the counter
     */
    public function add($increment = 1);

    /**
     * Get the counters current value
     * @return int|float
     */
    public function getValue();

    /**
     * Set the counters value to a specific quantity
     * @param int|float $val
     */
    public function setValue($val);

    /**
     * Reset the counters value to zero
     */
    public function resetValue();

    /**
     * Get the current default increment value
     * @return float
     */
    public function getIncrement();

    /**
     * Set the current default increment value which will be used if add gets
     * called without (or with invalid) parameters
     * @param int|float $inc
     *  new increment value
     */
    public function setIncrement($inc);
}
