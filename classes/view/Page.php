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

namespace de\flatplane\view;

use de\flatplane\interfaces\PageInterface;

/**
 * Description of Page
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Page implements PageInterface
{
    protected $orientation;
    protected $size;
    protected $content;

    public function __construct($number)
    {
        trigger_error('BIN DA WER NOCH');
        $this->number = $number;
    }

    public function getOrientation()
    {
        return $this->orientation;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }
}
