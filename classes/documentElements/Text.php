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
    protected $allowSubContent = ['footnote'];
    protected $isSplitable = true;

    protected $text = '';
    protected $path = '';
    protected $splitInParagraphs = true;
    protected $splitAtStr = "<br>";
    protected $hyphenate = true;
    protected $isHyphenated = false;

    protected $useCache = true;
    protected $inGetHash = false;
    protected $containsPageReference;
    protected $containsFootnotes;
    protected $textAlignment = 'J';

    protected $inGetSize = false;
    protected $parse = true;

    public function __toString()
    {
        return 'Text ('.$this->getPath().')';
    }

    /**
     * Gets the defined, possibly hyphenated text. This Method might read the
     * textfile if needed and cause sideeffects like adding footnotes
     * @return string
     */
    public function getText()
    {
        if (empty($this->text)
            || $this->getContainsPageReference()
            || $this->getContainsFootnotes()
        ) {
            $this->readText();
        }

        if ($this->getHyphenate() && !$this->isHyphenated) {
            $this->text = $this->toRoot()->hypenateText($this->text);
            $this->isHyphenated = true;
        }
        return $this->text;
    }

    /**
     * Get a hashed representation of the text and its settings. This is used
     * as part of the cache-filename
     * @param float $startYposition
     * @return string
     */
    public function getHash($startYposition)
    {
        $this->inGetHash = true;
        $hashInput = $this->getText()
            .$this->getParse()
            .$this->getHyphenate()
            .$this->getTextAlignment()
            .$this->getContainsFootnotes()
            .$startYposition
            .$this->getFontSize()
            .$this->getFontType()
            .$this->getFontStyle()
            .$this->getFontSpacing()
            .$this->getFontStretching()
            .$this->getMargins('default')
            .$this->getMargins('top')
            .$this->getMargins('bottom')
            .$this->getMargins('left')
            .$this->getMargins('right')
            .$this->toRoot()->getPageMargins('default')
            .$this->toRoot()->getPageMargins('top')
            .$this->toRoot()->getPageMargins('bottom')
            .$this->toRoot()->getPageMargins('left')
            .$this->toRoot()->getPageMargins('right');

        $this->inGetHash = false;
        return sha1($hashInput);
    }

    protected function readText()
    {
        ob_start();
        include ($this->getPath());
        $this->text = ob_get_clean();
        $this->isHyphenated = false;
    }

    /**
     * Get the Number, Page or title of the referenced object defined by the
     * $label, which has to be set on the target object using its setLabel()
     * Method
     * @param string $label
     * @param string $type (optional)
     *  The type of reference to use, defaults to 'number'
     * @return string
     *  The requested references properties or an errorstring containg
     *  unresolvedReferenceMarker with an estimated length appropriate to the
     *  requested type
     * @see Document::getReferenceValue()
     */
    public function getReference($label, $type = 'number')
    {
        if (strtolower($type) == 'page') {
            $this->setContainsPageReference(true);
        }
        return $this->toRoot()->getReference($label, $type);
    }

    public function getSize($startYposition = null)
    {
        $this->inGetSize = true;
        if ($this->isCached($startYposition)) {
            $size = $this->getCachedSize($startYposition);
        } else {
            $size = parent::getSize($startYposition);
            $this->writeCache($startYposition, $size);
        }

        $this->inGetSize = false;
        return $size;
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
        $pdf = $this->getPDF();
        $startPage = $pdf->getPage();

        $pdf->SetY($pdf->GetY()+$this->getMargins('top'));

        if ($this->getSplitInParagraphs()) {
            $splitText = explode(
                $this->getSplitAtStr(),
                $this->getText()
            );
        } else {
            $splitText = [$this->getText()];
        }

        //styles must be applied after changing the y position in order to
        //work properly with TCPDF
        $this->applyStyles();

        foreach ($splitText as $line) {
            if ($this->getParse()) {
                $pdf->writeHTML(
                    $line,
                    true,
                    false,
                    true,
                    false,
                    $this->getTextAlignment()
                );
            } else {
                $pdf->MultiCell(0, 0, $line, 0, $this->getTextAlignment(), false, 1);
            }
        }

        $pdf->SetY($pdf->GetY() + $this->getMargins('bottom'));

        //return number of pagebreaks
        return $pdf->getPage() - $startPage;
    }

    /**
     * Set the path to the file containig the text
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Enable/disable the parsing of the content as HTML if set to true or
     * as plaintext if set to false
     * @param bool $parse
     */
    public function setParse($parse) //todo: rename parse
    {
        $this->parse = (bool) $parse;
    }

    /**
     * Get the text HTML-parsing setting
     * @return bool
     */
    public function getParse()
    {
        return $this->parse;
    }

    /**
     * Get the text alignment setting
     * @return string
     */
    public function getTextAlignment()
    {
        return $this->textAlignment;
    }

    /**
     * Set the text alignmet setting
     * @param string $textAlignment
     *  available options: 'L' for left, 'C' for center, 'R' for right
     *  and 'J' for jusify
     */
    public function setTextAlignment($textAlignment)
    {
        $this->textAlignment = $textAlignment;
    }

    /**
     * Get the path to the content-file
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return bool
     */
    public function getSplitInParagraphs()
    {
        return $this->splitInParagraphs;
    }

    /**
     * @return string
     */
    public function getSplitAtStr()
    {
        return $this->splitAtStr;
    }

    /**
     * Get the setting for the usage of the text-sizecache
     * @return bool
     */
    public function getUseCache()
    {
        return $this->useCache;
    }

    /**
     * Returns true if the text containes references to the page property of an
     * element
     * @return bool
     */
    public function getContainsPageReference()
    {
        return $this->containsPageReference;
    }

    /**
     * Enable/disable the splitting of the text at specific chars/strings to
     * limit the amount of text sent to the outputting methods at once
     * @see setSplitAtStr()
     * @param bool $splitInParagraphs
     */
    public function setSplitInParagraphs($splitInParagraphs)
    {
        $this->splitInParagraphs = $splitInParagraphs;
    }

    /**
     * Set the char/string at which the text will get split
     * @param string $splitAtStr
     * @see setSplitInParagraphs()
     */
    public function setSplitAtStr($splitAtStr)
    {
        $this->splitAtStr = $splitAtStr;
    }

    /**
     * Enable/disable the usage of the size-cache
     * @param bool $useCache
     */
    public function setUseCache($useCache)
    {
        $this->useCache = $useCache;
    }

    /**
     * Sets the containsPageReference flag to indicate whether the textfile needs
     * to be read again to resolve the references
     * @param bool $containsPageReference
     */
    public function setContainsPageReference($containsPageReference)
    {
        $this->containsPageReference = $containsPageReference;
    }

    /**
     * Sets the text to be displayed. Content defined with this method can't
     * contain references, footnotes or other dynamicly generated elements
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Add a footnote at the current position within the text. Only call this
     * from within the text definition files
     * @param string $text
     *  the content of the footnote. may contain HTML
     * @return string
     *  the number of the footnote sourrounded by <sup> tags
     */
    public function addFootnote($text)
    {
        $footnote = $this->toRoot()->getElementFactory()->createFootnote(
            $text,
            $this->toRoot()
        );

        if ($this->inGetSize) {
            $number = str_repeat(
                $this->toRoot()->getUnresolvedReferenceMarker(),
                $this->toRoot()->getAssumedFootnoteNumberWidth()
            );
            if (!$this->inGetHash) {
                $this->getPDF()->increaseBottomMargin($footnote);

            }
        } else {
            $number = $this->getPDF()->addFootnote($footnote);
        }
        $this->setContainsFootnotes(true);
        return '<sup>'.$number.'</sup>';
    }

    public function getContainsFootnotes()
    {
        return $this->containsFootnotes;
    }

    public function setContainsFootnotes($containsFootnotes)
    {
        $this->containsFootnotes = $containsFootnotes;
    }
}
