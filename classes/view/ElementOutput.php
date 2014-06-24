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

namespace de\flatplane\view;

use de\flatplane\interfaces\documentElements\DocumentInterface;
use de\flatplane\iterators\RecursiveContentIterator;
use Exception;
use RecursiveIteratorIterator;

/**
 * Description of ElementOutput
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class ElementOutput
{
    protected $currentLinearPage = 0;
    protected $document;

    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
        //add first page
        $document->getPDF()->AddPage();

        $content = $document->getContent();

        $recItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

//        foreach ($recItIt as $pageElement) {
//            echo $pageElement->getTitle().' '.$pageElement->getLinearPage().' '.$pageElement->getStartsNewPage().PHP_EOL;
//        }

        foreach ($recItIt as $pageElement) {
            $page = $pageElement->getLinearPage();
            if ($page == $this->currentLinearPage) {
                //display element
                echo "writing on current page: ";
                $numPageBreaks = $pageElement->generateOutput();
                echo $numPageBreaks.' Pages'.PHP_EOL;
            } elseif ($page == $this->currentLinearPage + 1) {
                echo "writing on next page: ";
                $this->addPage();
                $numPageBreaks = $pageElement->generateOutput();
                echo $numPageBreaks.' Pages'.PHP_EOL;
            } else {
                throw new Exception('Invalid Page number: '.$page. 'expected: '.$this->currentLinearPage.' or '.($this->currentLinearPage+1));
            }
            $inc = $numPageBreaks;
            echo "incrementing: $inc".PHP_EOL;
            $this->currentLinearPage += $inc;
        }
    }

    protected function addPage()
    {
        $this->document->getPDF()->AddPage();
        $this->currentLinearPage ++;
    }
}
