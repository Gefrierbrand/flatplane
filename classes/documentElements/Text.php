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

use de\flatplane\controller\Flatplane;
use de\flatplane\interfaces\documentElements\TextInterface;
use RuntimeException;

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

    public function getHash($startYposition)
    {
        return sha1(
            $this->getText()
            .$this->getTextAlignment()
            .$startYposition
            .$this->getFontSize()
            .$this->getFontType()
            .$this->getFontStyle()
            .$this->getFontSpacing()
            .$this->getFontStretching()
        );
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

    public function getSize($startYposition = null)
    {
        if ($this->isCached($startYposition)) {
            return $this->getCachedSize($startYposition);
        } else {
            $size = parent::getSize($startYposition);
            $this->writeCache($startYposition, $size);
            return $size;
        }
    }

    protected function getCachedSize($startYposition)
    {
        $filename = $this->getCacheFileName($startYposition);
        if (!is_readable($filename)) {
            throw new RuntimeException("cache for $this is not readable");
        }
        $size = unserialize(file_get_contents($filename));
        return $size;
    }

    protected function writeCache($startYposition, array $size)
    {
        $filename = $this->getCacheFileName($startYposition);
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        if (!is_writable($dir)) {
            trigger_error('Text cache directory is not writable', E_USER_WARNING);
        }

        file_put_contents($filename, serialize($size));
    }

    protected function getCacheFileName($startYposition)
    {
        $filename = Flatplane::getCacheDir().DIRECTORY_SEPARATOR.
        'text'.DIRECTORY_SEPARATOR.$this->getHash($startYposition).'.txt';
        return $filename;
    }

    protected function isCached($startYposition)
    {
        $filename = $this->getCacheFileName($startYposition);
        if ($this->getUseCache()
            && file_exists($filename)
            && is_readable($filename)
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function generateOutput()
    {
        $pdf = $this->toRoot()->getPDF();
        $startPage = $pdf->getPage();

        $this->applyStyles();

        if ($this->getSplitInParagraphs()) {
            $splitText = explode($this->getSplitAtStr(), $this->getText());
        } else {
            $splitText = [$this->getText()];
        }

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
