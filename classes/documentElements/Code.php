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
    protected $type = 'code';
    protected $title = 'Code';
    protected $enumerate = true;
    protected $showInList = true;
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

        $pdf->SetY($pdf->GetY()+$this->getMargins('top'));

        $this->applyStyles('title');
        //todo: implement title/caption position & placement
        $html = '<b>Quelltext '.$this->getFormattedNumbers().':</b>  '.$this->getTitle();
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, false, true, 'C');

        $pdf->SetY($pdf->GetY()+$this->getMargins('title'));

        $this->applyStyles('default');

        $pdf->writeHTML(
            $this->getText(),
            false,
            false,
            true,
            false,
            $this->getTextAlignment()
        );

        $pdf->SetY($pdf->GetY() + $this->getMargins('bottom'));

        //return number of pagebreaks
        return $pdf->getPage() - $startPage;
    }
}
