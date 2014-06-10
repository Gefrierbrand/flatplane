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

namespace de\flatplane\utilities;

/**
 * Description of myPDF
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class PDF extends \TCPDF
{
    protected $measureStartY;
    protected $measureStartPage;
    protected $oldPageBreak;

    /**
	 * @return float
     *  Current height of page in user unit.
	 */
    public function getH()
    {
        return $this->h;
    }

    public function startMeasurement()
    {
        $this->startTransaction();
        // store starting values
        $this->AddPage();
        $this->oldPageBreak = $this->getAutoPageBreak();
        $this->SetAutoPageBreak(true, $this->getMargins()['bottom']);

        $this->measureStartY = $this->GetY();
        $this->measureStartPage = $this->getPage();
    }

    public function endMeasurement($rollback = true)
    {
        $end_y = $this->GetY();
        $end_page = $this->getPage();
        // calculate height
        $height = 0;
        $numPages = 1;

        if ($end_page == $this->measureStartPage) {
            $height = $end_y - $this->measureStartY;
        } else {
            for ($page = $this->measureStartPage; $page <= $end_page; ++$page) {
                $this->setPage($page);
                $numPages ++;
                if ($page == $this->measureStartPage) {
                    // first page
                    $height += $this->getH()
                        - $this->measureStartY
                        - $this->getMargins()['bottom'];
                } elseif ($page == $end_page) {
                    // last page
                    $height += $end_y - $this->getMargins()['top'];
                } else {
                    // other pages
                    $height += $this->getH()
                        - $this->getMargins()['top']
                        - $this->getMargins()['bottom'];
                }
            }
        }

        if ($rollback) {
            $this->rollbackTransaction(true);
        } else {
            $this->commitTransaction();
        }

        $this->SetAutoPageBreak($this->oldPageBreak, $this->getMargins()['bottom']);
        return ['height' => $height, 'numPages' => $numPages];
    }

//    public function header()
//    {
//        $this->Write(0, "zeile1\nzeile2\nzeile3\nzeile4\nzeile5\nzeile6\nzeile7\nzeile8");
//    }
}
