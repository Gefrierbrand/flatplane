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
 * Description of Table
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class TitlePage extends AbstractDocumentContentElement
{
    protected $type = 'titlepage';
    protected $title = 'TitlePage';

    protected $enumerate = false;
    protected $showInList = false;

    protected $showHeader = false;
    protected $showFooter = false;

    protected $enumeratePage = false;

    public function generateOutput()
    {
        //Overwrite this method in your own implementation
        $pdf = $this->getPDF();

        $pdf->SetFontSize(64);
        $pdf->Write(0, 'TITELSEITE');
        return 0;
    }

    public function getShowHeader()
    {
        return $this->showHeader;
    }

    public function getShowFooter()
    {
        return $this->showFooter;
    }

    public function getEnumeratePage()
    {
        return $this->enumeratePage;
    }

    public function setShowHeader($showHeader)
    {
        $this->showHeader = $showHeader;
    }

    public function setShowFooter($showFooter)
    {
        $this->showFooter = $showFooter;
    }

    public function setEnumeratePage($enumeratePage)
    {
        $this->enumeratePage = $enumeratePage;
    }
}
