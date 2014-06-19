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

namespace de\flatplane\view;

use de\flatplane\interfaces\documentElements\SectionInterface;
use RuntimeException;

/**
 * Description of PageLayout
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class PageLayout
{
    protected $pages;

    public function __construct(array $content)
    {
        foreach ($content as $pageElement) {
            $type = $pageElement->getType();
            $methodName = 'layout'.  ucfirst($type);
            if (method_exists($this, $methodName)) {
                $this->$methodName($pageElement);
            } else {
                throw new RuntimeException('Invalid element type "'.$type.'"');
            }
        }
    }

    protected function addPage()
    {
        $this->pages[] = new Page(1);
    }

    protected function layoutSection(SectionInterface $section)
    {
        //check if a page already exists
        if (empty($this->getPages())) {
            $this->addPage();
        }
        //check free space on current page
        $section->getMinFreePage('level'.$section->getLevel());
    }

    protected function layoutImage()
    {

    }

    protected function layoutFormula()
    {

    }

    protected function layoutText()
    {

    }

    protected function layoutList()
    {

    }

    protected function layoutTable()
    {

    }

    protected function layoutSource()
    {

    }

    public function getPages()
    {
        return $this->pages;
    }
}
