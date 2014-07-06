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

namespace de\flatplane\documentElements;


/**
 * Description of Footnote
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Footnote extends AbstractDocumentContentElement
{
    protected $type='footnote';
    protected $title = 'Footnote';
    protected $text;

    protected $separatorLineWidth = 30;
    protected $numberSeparationWidth = 0.8;
    protected $textAlignment = 'L';

    public function generateOutput()
    {
        //todo: implement
    }

    public function getText()
    {
        return $this->text;
    }

    public function getSeparatorLineWidth()
    {
        return $this->separatorLineWidth;
    }

    public function getNumberSeparationWidth()
    {
        return $this->numberSeparationWidth;
    }

    public function getTextAlignment()
    {
        return $this->textAlignment;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setSeparatorLineWidth($separatorLineWidth)
    {
        $this->separatorLineWidth = $separatorLineWidth;
    }

    public function setNumberSeparationWidth($numberSeparationWidth)
    {
        $this->numberSeparationWidth = $numberSeparationWidth;
    }

    public function setTextAlignment($textAlignment)
    {
        $this->textAlignment = $textAlignment;
    }
}
