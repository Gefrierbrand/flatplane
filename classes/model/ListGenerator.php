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

namespace de\flatplane\model;

use de\flatplane\documentContents\ListOfContents;

/**
 * Description of ListGenerator
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ListGenerator
{
    protected $content;
    public function __construct()
    {
        ;
    }

    public function generate(ListOfContents $element)
    {
        $this->content = $element->getData();
        $this->outputList();
    }

    protected function outputList()
    {
        echo PHP_EOL, 'DEMO OUTPUT: LIST', PHP_EOL;
        foreach ($this->content as $line) {
            $indent = str_repeat(' ', $line['level']);
            echo $indent.$line['numbers'].' '.$line['text'].PHP_EOL;
        }
        echo PHP_EOL;
    }
}
