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
    /**
	 * @return float
     *  Current height of page in user unit.
	 */
    public function getH()
    {
        return $this->h;
    }

    public function estimateHeight($html)
    {
        $this->startTransaction();
        // store starting values
        $start_y = $this->GetY();
        $start_page = $this->getPage();
        // call your printing functions with your parameters
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        $this->writeHTML($txt, true, false, false, false, 'J');
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // get the new Y
        $end_y = $this->GetY();
        $end_page = $this->getPage();
        // calculate height
        $height = 0;
        $this->SetFillColor(255, 255, 255);

        if ($end_page == $start_page) {
            $height = $end_y - $start_y;

            echo "transaction height (onepage): ".number_format($height, 2, '.', '') . PHP_EOL;
            $this->Line(8, $start_y, 8, $end_y, ['width'=>0.1]);
            $textheight = $this->getStringHeight(0, 'Ü');
            $y = $start_y+($height-$textheight)/2;
            $this->SetXY(7, $y);
            $this->Write(0, number_format($height, 2, '.', '')." mm", '', true);

        } else {
            for ($page = $start_page; $page <= $end_page; ++$page) {
                $this->setPage($page);
                if ($page == $start_page) {
                    // first page
                    $height = $this->getH() - $start_y - $this->getMargins()['bottom'];

                    echo "transaction height (first): ".number_format($height, 2, '.', '') . PHP_EOL;
                    $this->Line(8, $start_y, 8, $this->getH()-$this->getMargins()['bottom'], ['width'=>0.1]);
                    $textheight = $this->getStringHeight(0, 'Ü');
                    $y = $start_y+($height-$textheight)/2;

                    $this->SetXY(7, $y);
                    $this->Write(0, number_format($height, 2, '.', '')." mm", '', true);
                } elseif ($page == $end_page) {
                    // last page
                    $height = $end_y - $this->getMargins()['top'];

                    echo "transaction height (last): ".number_format($height, 2, '.', '') . PHP_EOL;
                    $this->Line(8, $this->getMargins()['top'], 8, $end_y, ['width'=>0.1]);
                    $textheight = $this->getStringHeight(0, 'Ü');
                    $y = $this->getMargins()['top']+($height-$textheight)/2;
                    $this->SetXY(7, $y);
                    $this->Write(0, number_format($height, 2, '.', '')." mm", '', true);
                } else {
                    // other pages
                    $height = $this->getH() - $this->getMargins()['top'] - $this->getMargins()['bottom'];

                    echo "transaction height: ".number_format($height, 2, '.', '') . PHP_EOL;
                    $this->Line(8, $this->getMargins()['top'], 8, $this->getH()-$this->getMargins()['bottom'], ['width'=>0.1]);
                    $textheight = $this->getStringHeight(0, 'Ü');
                    $y = $this->getMargins()['top']+($this->getH()-$this->getMargins()['top'])/2-$textheight/2;

                    $this->SetXY(7, $y);
                    $this->Write(0, number_format($height, 2, '.', '')." mm", '', true);
                }
            }
        }

        $this->commitTransaction();
    }
}
