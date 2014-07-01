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
    protected $splitInParagraphs = true;
    protected $splitAtStr = "<br>";
    protected $hyphenate = true;

    protected $useCache = true;
    protected $containsPageReference;
    protected $textAlignment = 'J';

    public function __toString()
    {
        return 'Text ('.$this->getPath().')'.substr($this->getText(), 0, 15).'...';
    }

    public function getText()
    {
        if (empty($this->text) || $this->getContainsPageReference()) {
            $this->readText();
        }
        return $this->text;
    }

    public function getHash()
    {
        return sha1($this->getText().$this->getTextAlignment().$this->getHyphenate());
    }

    public function getParse()
    {
        return $this->parseText();
    }

    public function readText()
    {
        //make document available to template
        //$document = $this->toRoot();
        ob_start();
        include ($this->getPath());
        $this->text = ob_get_clean();
        if ($this->getHyphenate()) {
            $this->text = $this->toRoot()->hypenateText($this->text);
        }
    }

    public function getReference($label, $type = 'number')
    {
        if (strtolower($type) == 'page') {
            $this->setContainsPageReference(true);
        }
        return $this->toRoot()->getReference($label, $type);
    }

    public function generateOutput()
    {
        $this->applyStyles();
        $pdf = $this->toRoot()->getPDF();
        $startPage = $pdf->getPage();

        $splitText = explode('<br>', $this->getText());
        foreach ($splitText as $line) {
            $pdf->writeHTML(
                $line,
                true,
                false,
                false,
                false,
                $this->getTextAlignment()
            );
        }

        //return number of pagebreaks
        return $pdf->getPage() - $startPage;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setParse($parse) //todo: rename parse
    {
        $this->parse = (bool) $parse;
    }

    public function getTextAlignment()
    {
        return $this->textAlignment;
    }

    public function setTextAlignment($textAlignment)
    {
        $this->textAlignment = $textAlignment;
    }

    public function getLineHeight()
    {
        return $this->lineHeight;
    }

    public function setLineHeight($lineHeight)
    {
        $this->lineHeight = $lineHeight;
    }

    public function getPath()
    {
        return $this->path;
    }
    public function getSplitInParagraphs()
    {
        return $this->splitInParagraphs;
    }

    public function getSplitAtStr()
    {
        return $this->splitAtStr;
    }

    public function getUseCache()
    {
        return $this->useCache;
    }

    public function getContainsPageReference()
    {
        return $this->containsPageReference;
    }

    public function setSplitInParagraphs($splitInParagraphs)
    {
        $this->splitInParagraphs = $splitInParagraphs;
    }

    public function setSplitAtStr($splitAtStr)
    {
        $this->splitAtStr = $splitAtStr;
    }

    public function setUseCache($useCache)
    {
        $this->useCache = $useCache;
    }

    public function setContainsPageReference($containsPageReference)
    {
        $this->containsPageReference = $containsPageReference;
    }
}
