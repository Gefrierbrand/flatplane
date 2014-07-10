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

use de\flatplane\utilities\Highlighter;

/**
 * Description of Code
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Code extends Text
{
    protected $type = 'Code';
    protected $enumerate = false;
    protected $showInList = false;
    protected $allowSubContent = false;
    protected $isSplitable = true;

    protected $text = '';
    protected $path = '';
    protected $splitInParagraphs = false;
    protected $splitAtStr = PHP_EOL;
    protected $hyphenate = false;

    protected $useCache = true;
    protected $containsPageReference = false;
    protected $textAlignment = 'L';

    public function readText()
    {
        $highlighter = new Highlighter();
        $this->text = $highlighter->highlightFile($this->getPath());
    }

    public function generateOutput()
    {
        $pdf = $this->getPDF();
        $startPage = $pdf->getPage();

        $this->applyStyles();

        //file_put_contents('grVars'.microtime().'.txt', serialize($pdf->getGraphicVars()));

        if ($this->getSplitInParagraphs()) {
            $splitText = explode($this->getSplitAtStr(), $this->getText());
        } else {
            $splitText = [$this->getText()];
        }

        foreach ($splitText as $line) {
            $pdf->writeHTML(
                $line,
                false,
                false,
                true,
                false,
                $this->getTextAlignment()
            );
        }

        //return number of pagebreaks
        return $pdf->getPage() - $startPage;
    }
}
