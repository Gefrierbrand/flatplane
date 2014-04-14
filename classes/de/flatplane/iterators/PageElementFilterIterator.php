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

namespace de\flatplane\iterators;

/**
 * Description of TocElementFilterIterator
 * TODO: document!
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */


class PageElementFilterIterator extends \FilterIterator
{
    protected $type;

    public function __construct(\Iterator $iterator, $type)
    {
        $this->type = $type;
        parent::__construct($iterator);
    }

    public function accept()
    {
        $content = parent::current();
        return ($content->getType() == $this->type);
    }
}