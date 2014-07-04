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

use de\flatplane\interfaces\documentElements\TableInterface;

/**
 * Description of Table
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Table extends Text implements TableInterface
{
    protected $type = 'table';
    protected $title = 'Table';
    protected $containsPageReference = false;
    protected $splitInParagraphs = false;

    protected $titlePosition = ['top', 'center'];

    protected $caption;
    protected $captionPosition = ['bottom', 'center'];

    protected $hyphenate = false;

    public function __toString()
    {
        return (string) "Table: ".$this->getTitle();
    }

    public function generateOutput()
    {
        $pdf = $this->toRoot()->getPDF();
        $startPage = $pdf->getPage();

        $pdf->SetY($pdf->GetY()+$this->getMargins('top'));

        $this->applyStyles('title');
        //todo: implement title/caption position & placement
        $pdf->MultiCell(0, 0, $this->getTitle(), 0, 'C');

        $this->applyStyles('default');

        $pdf->writeHTML(
            $this->getText(),
            true,
            false,
            false,
            false,
            $this->getTextAlignment()
        );

        $this->applyStyles('caption');
        $pdf->MultiCell(0, 0, $this->getCaption(), 0, 'C');

        $pdf->SetY($pdf->GetY() + $this->getMargins('bottom'));

        //return number of pagebreaks
        return $pdf->getPage() - $startPage;
    }

    public function getTitlePosition()
    {
        return $this->titlePosition;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function getCaptionPosition()
    {
        return $this->captionPosition;
    }

    public function setTitlePosition($titlePosition)
    {
        $this->titlePosition = $titlePosition;
    }

    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    public function setCaptionPosition($captionPosition)
    {
        $this->captionPosition = $captionPosition;
    }
}
