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

namespace de\flatplane\pageelements;

use de\flatplane\interfaces\PageConentInterface;

/**
 * Description of PageElement
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
abstract class PageElement implements PageConentInterface
{
    protected $showInIndex;
    protected $enumerate;
    protected $parent;
    protected $type;
    protected $number;

    public function getShowInIndex()
    {
        return $this->showInIndex;
    }

    public function getEnumerate()
    {
        return $this->enumerate;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getFullNumber()
    {
         //TODO: Implement me
    }

    public function getLevel()
    {
        //TODO: Implement me
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getPage()
    {
         //TODO: Implement me
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setShowInIndex($showInIndex)
    {
        $this->showInIndex = $showInIndex;
    }

    public function setEnumerate($enumerate)
    {
        $this->enumerate = $enumerate;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }
}
