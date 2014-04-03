<?php

/*
 * Copyright (C) 2014 Nikolai Neff <admin@flatplane.de>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace de\flatplane\iterators;

/**
 * Description of recursiveSectionIterator
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class RecursiveSectionIterator implements \RecursiveIterator
{

    private $position;
    private $sections;

    public function __construct($sections = array())
    {
        $this->sections = $sections;
    }

    public function valid()
    {
        return isset($this->sections[$this->position]);
    }

    public function hasChildren()
    {
        return $this->sections[$this->position]->hasChildren();
    }

    public function next()
    {
        $this->position++;
    }

    public function current()
    {
        return $this->sections[$this->position];
    }

    public function getChildren()
    {
        return new recursiveSectionIterator($this->sections[$this->position]->getSubSections());
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function key()
    {
        return $this->position;
    }
}
?>