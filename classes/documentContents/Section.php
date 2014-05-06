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

use de\flatplane\documentContents\AbstractDocumentContentElement;
use de\flatplane\interfaces\documentelements\SectionInterface;

/**
 * Description of section
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Section extends AbstractDocumentContentElement implements SectionInterface
{
    protected $type = 'section';

    protected $settings = ['title' => '',
                           'altTitle' => '',
                           'enumerate' => true,
                           'showInList' => true,
                           'showInDocument' => true,
                           'allowSubContent' => true,
                           'isSplitable' => false,
                           'minFreePage' => 25,
                           'startsNewLine' => false,
                           'startsNewPage' => false];

    public function __construct(array $config)
    {
        parent::__construct($config);

        if (empty($this->getSettings('title'))) {
            trigger_error('The section title is empty', E_USER_WARNING);
        }

        if (empty($this->getSettings('altTitle'))) {
            $this->setSettings(['altTitle' => $this->getSettings('title')]);
        }
    }

    //TODO: fixme!
    public function __toString()
    {
        if ($this->getEnumerate()) {
            //this will not work if the section has no parent!
            $numStr = $this->getFormattedNumbers().' ';
        } else {
            $numStr = '';
        }
        return (string) $numStr. $this->getSettings('title');
    }

    /**
     * @return bool
     */
    public function getShowInDocument()
    {
        return (bool) $this->getSettings('showInDocument');
    }

    /**
     * @param bool $showInDocument
     */
    public function setShowInDocument($showInDocument)
    {
        $this->setSettings(['showInDocument' => (bool) $showInDocument]);
    }

    /**
     * @return bool
     */
    public function getStartsNewLine()
    {
        return (bool) $this->getSettings('startsNewLine', $this->getLevel());
    }

    public function getMinFreePage()
    {
        return $this->getSettings('minFreePage', $this->getLevel());
    }
}
