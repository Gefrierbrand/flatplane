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

namespace de\flatplane\documentContents;

use de\flatplane\interfaces\documentElements\TextInterface;

/**
 * Description of Text
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Text extends AbstractDocumentContentElement implements TextInterface
{
    protected $type = 'text';
    protected $enumerate = false;
    protected $showInList = false;
    protected $allowSubContent = false;
    protected $isSplitable = true;

    protected $text = '';
    protected $parse = true; //parse special content like eqn, etc?
    protected $hyphenate = true;

    public function getText()
    {
        return $this->text;
    }

    public function getParse()
    {
        return $this->parse;
    }

    public function getHyphenate()
    {
        return $this->hyphenate;
    }

    protected function setText($text)
    {
        $this->text = $text;
    }

    protected function setParse($parse) //todo: rename parse
    {
        $this->parse = (bool) $parse;
    }
    protected function setHyphenate($hyphenate)
    {
        $this->hyphenate = (bool) $hyphenate;
    }
}
