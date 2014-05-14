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
    protected $path = '';
    protected $parse = true; //parse special content like eqn, etc?
    protected $hyphenate = true;

    protected $hyphenation = ['file' => '',
                              'dictionary' => [],
                              'leftMin' => 2,
                              'rightMin' => 2,
                              'charMin' => 1,
                              'charMax' => 8];

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

    public function getHyphenate()
    {
        return $this->hyphenate;
    }

    public function readText()
    {
        $document = $this->toRoot();
        ob_start();
        include $this->path;
        $this->text = ob_get_clean();
        if ($this->hyphenate) {
            $this->text = $this->hypenateText($this->text);
        }
    }

    public function getSize()
    {
        $pdf = $this->toRoot()->getPdf();
        $pdf->startTransaction();
        $pdf->AddPage();
        $pdf->writeHTML($this->getText());
        //todo: measure
        $pdf->rollbackTransaction(true);
    }

    protected function hypenateText($text)
    {
        $doc = $this->toRoot();
        return $doc->getPdf()->hyphenateText(
            $text,
            $this->hyphenation['file'],
            $this->hyphenation['dictionary'],
            $this->hyphenation['leftMin'],
            $this->hyphenation['rightMin'],
            $this->hyphenation['charMin'],
            $this->hyphenation['charMax']
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

    protected function setHyphenate($hyphenate)
    {
        $this->hyphenate = (bool) $hyphenate;
    }

    public function getHyphenationPatternFile()
    {
        return $this->hyphenationPatternFile;
    }

    protected function setHyphenation(array $hyphenation)
    {
        foreach ($hyphenation as $key => $option) {
            if (array_key_exists($key, $this->hyphenation)) {
                if ($option == ['']) {
                    $option = [];
                }
                $this->hyphenation[$key] = $option;
            }
        }
    }
}
