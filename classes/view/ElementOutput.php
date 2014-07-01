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
use RecursiveIteratorIterator;
use RuntimeException;

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
    }

    /**
     * @todo: doc
     * @todo: return value?
     * @throws RuntimeException
     */
    public function generateOutput()
    {
        $content = $this->getDocument()->getContent();

        $recItIt = new RecursiveIteratorIterator(
            new RecursiveContentIterator($content),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($recItIt as $pageElement) {
            $page = $pageElement->getLinearPage();
            //if the current page is equal to the elements page property, display
            //the element on the current page, otherwise add a new page and
            //display the element there
            if ($page == $this->getCurrentLinearPage()) {
                //display element on current page
                $numPageBreaks = $pageElement->generateOutput();
            } elseif ($page == $this->getCurrentLinearPage() + 1) {
                //add page and then display element
                $this->addPage();
                $numPageBreaks = $pageElement->generateOutput();
            } else {
                throw new RuntimeException(
                    "($pageElement) Invalid Page number: ".var_export($page, true)
                    .' expected: '.$this->getCurrentLinearPage().' or '
                    .($this->getCurrentLinearPage()+1)
                );
            }
            //increment the page number by the amount of pagebreaks caused by
            //the displaying of the element
            $this->setCurrentLinearPage(
                $this->getCurrentLinearPage()+ $numPageBreaks
            );
        }
    }

    protected function addPage()
    {
        //add page in PDF
        $this->getDocument()->getPDF()->AddPage();
        //increment page counter
        //todo: use methods here
        $this->currentLinearPage ++;
    }

    /**
     *
     * @return DocumentInterface
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     *
     * @return int
     */
    public function getCurrentLinearPage()
    {
        return $this->currentLinearPage;
    }

    /**
     *
     * @param int $currentLinearPage
     */
    public function setCurrentLinearPage($currentLinearPage)
    {
        $this->currentLinearPage = $currentLinearPage;
    }
}
