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
 * TODO: doc!
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Section extends AbstractDocumentContentElement implements SectionInterface
{
    protected $type = 'section';

    protected $altTitle = '';
    protected $showInDocument = true;
    protected $minFreePage = ['default' => 25];
    protected $startsNewLine = ['default' => true];
    protected $startsNewPage = ['default' => false];

    public function __construct(array $config)
    {
        parent::__construct($config);

        if (empty($this->getTitle())) {
            trigger_error('The section title is empty', E_USER_WARNING);
        }

        if (empty($this->getAltTitle())) {
            $this->altTitle = $this->getTitle();
        }
    }

    public function __toString()
    {
        return (string) $this->getAltTitle();
    }

    public function getAltTitle()
    {
        return $this->altTitle;
    }

    public function getShowInDocument()
    {
        return $this->showInDocument;
    }

    public function getMinFreePage($level = 0)
    {
        if (isset($this->minFreePage[$level])) {
            return $this->minFreePage[$level];
        } elseif (isset($this->minFreePage['default'])) {
            return $this->minFreePage['default'];
        } else {
            throw new RuntimeException(
                'The required property minFreePage is not set.'
            );
        }
    }

    public function getStartsNewLine($level = 0)
    {
        if (isset($this->startsNewLine[$level])) {
            return $this->startsNewLine[$level];
        } elseif (isset($this->startsNewLine['default'])) {
            return $this->startsNewLine['default'];
        } else {
            throw new RuntimeException(
                'The required property minFreePage is not set.'
            );
        }
    }

    public function getStartsNewPage($level = 0)
    {
        if (isset($this->startsNewPage[$level])) {
            return $this->startsNewPage[$level];
        } elseif (isset($this->startsNewPage['default'])) {
            return $this->startsNewPage['default'];
        } else {
            throw new RuntimeException(
                'The required property minFreePage is not set.'
            );
        }
    }
}
