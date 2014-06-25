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

    public function startMeasurement($startYPosition = null)
    {
        $this->startTransaction();

        $this->AddPage();

        //set the vertical position to a given starting value
        if (is_numeric($startYPosition)) {
            $this->SetY($startYPosition);
        }

        $this->measureStartY = $this->GetY();
        $this->measureStartPage = $this->getPage();
    }

    /**
     * todo: doc
     * @param type $rollback
     * @return array height of transaction in user units & number of pages needed
     */
    public function endMeasurement($rollback = true)
    {
        $endYPosition = $this->GetY();
        $endPageNumber = $this->getPage();
        // calculate height
        $height = 0;

        if ($endPageNumber == $this->measureStartPage) {
            $height = $endYPosition - $this->measureStartY;
        } else {
            for ($page = $this->measureStartPage; $page <= $endPageNumber; ++$page) {
                $this->setPage($page);
                if ($page == $this->measureStartPage) {
                    // first page
                    $height += $this->getH()
                        - $this->measureStartY
                        - $this->getMargins()['bottom'];
                } elseif ($page == $endPageNumber) {
                    // last page
                    $height += $endYPosition - $this->getMargins()['top'];
                } else {
                    // other pages
                    $height += $this->getH()
                        - $this->getMargins()['top']
                        - $this->getMargins()['bottom'];
                }
            }
        }

        //todo: use start transaction page?
        $numPages = ($endPageNumber - $this->measureStartPage) + 1;

        if ($rollback) {
            $this->rollbackTransaction(true);
        } else {
            $this->commitTransaction();
        }
        return ['height' => $height,
                'numPages' => $numPages,
                'endYposition' => $endYPosition];
    }
}
