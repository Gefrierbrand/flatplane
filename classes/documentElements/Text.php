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

use de\flatplane\interfaces\documentElements\TextInterface;

/**
 * Description of Text
 * todo: add line-height to styles
 * todo: cache text (vgl formulas, v.a. wegen hyphenation)
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
    protected $path = '';
    protected $parse = true; //parse special content like eqn, etc?
    protected $hyphenate = true;

    protected $textAlignment = 'J';

    public function __toString()
    {
        return 'Text ('.$this->getPath().')'.substr($this->getText(), 0, 15).'...';
    }

    public function getText()
    {
        if (empty($this->text)) {
            $this->readText();
        }
        return $this->text;
    }

    public function getParse()
    {
        return $this->parse;
    }

    public function readText()
    {
        //make document available to template
        $document = $this->toRoot();
        ob_start();
        include ($this->getPath());
        $this->text = ob_get_clean();
        if ($this->getHyphenate()) {
            $this->text = $this->toRoot()->hypenateText($this->text);
        }
    }

    public function getSize()
    {
        $this->applyStyles();
        $pdf = $this->toRoot()->getPdf();

        $pdf->startMeasurement();
        $this->generateOutput();
        $measurements = $pdf->endMeasurement();

        $width = $pdf->getPageWidth()
            - $pdf->getMargins()['left']
            - $pdf->getMargins()['right'];
        return ['height' => $measurements['height'], 'width' => $width];
    }

    public function generateOutput()
    {
        $pdf = $this->toRoot()->getPdf();
        $pdf->writeHTML(
            $this->getText(),
            false,
            false,
            false,
            false,
            $this->getTextAlignment()
        );
    }

    protected function setPath($path)
    {
        $this->path = $path;
    }

    protected function setParse($parse) //todo: rename parse
    {
        $this->parse = (bool) $parse;
    }

    public function getTextAlignment()
    {
        return $this->textAlignment;
    }

    protected function setTextAlignment($textAlignment)
    {
        $this->textAlignment = $textAlignment;
    }

    public function getLineHeight()
    {
        return $this->lineHeight;
    }

    protected function setLineHeight($lineHeight)
    {
        $this->lineHeight = $lineHeight;
    }

    public function getPath()
    {
        return $this->path;
    }
}
