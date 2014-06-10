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

/**
 * Description of Footnote
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Source extends AbstractDocumentContentElement
{
    protected $type='source';

    protected $altTitle = '';
    protected $fieldsToShow = ['sourceAuthor',
                               'sourceTitle',
                                'sourcePublisher',
                                'sourceEdition',
                                'sourceYear'];

    protected $fieldSeparator = ';';

    protected $sourceAuthor;
    protected $sourceTitle;
    protected $sourceBooktitle;
    protected $sourceJournal;
    protected $sourcePublisher;
    protected $sourceOrganization;
    protected $sourceSchool;
    protected $sourceMonth;
    protected $sourceYear;
    protected $sourceVolume;
    protected $sourceNumber;
    protected $sourceChapter;
    protected $sourcePages;
    protected $sourceNote;
    protected $sourceSeries;
    protected $sourceEdition;
    protected $sourceIsbn;
    protected $sourceHowpublished;
    protected $sourceAddress;
    protected $sourceUrl;

    //protected $quotingStyle = 'Chicago'; //alt: harvard

    public function getSize()
    {
        return ['width' => 0, 'height' => 0];
    }

    public function getAltTitle()
    {
        //todo: handle content display
        parent::getAltTitle();
    }

    public function getSourceAuthor()
    {
        return $this->sourceAuthor;
    }

    public function getSourceTitle()
    {
        return $this->sourceTitle;
    }

    public function getSourceBooktitle()
    {
        return $this->sourceBooktitle;
    }

    public function getSourceJournal()
    {
        return $this->sourceJournal;
    }

    public function getSourcePublisher()
    {
        return $this->sourcePublisher;
    }

    public function getSourceOrganization()
    {
        return $this->sourceOrganization;
    }

    public function getSourceSchool()
    {
        return $this->sourceSchool;
    }

    public function getSourceMonth()
    {
        return $this->sourceMonth;
    }

    public function getSourceYear()
    {
        return $this->sourceYear;
    }

    public function getSourceVolume()
    {
        return $this->sourceVolume;
    }

    public function getSourceNumber()
    {
        return $this->sourceNumber;
    }

    public function getSourceChapter()
    {
        return $this->sourceChapter;
    }

    public function getSourcePages()
    {
        return $this->sourcePages;
    }

    public function getSourceNote()
    {
        return $this->sourceNote;
    }

    public function getSourceSeries()
    {
        return $this->sourceSeries;
    }

    public function getSourceEdition()
    {
        return $this->sourceEdition;
    }

    public function getSourceIsbn()
    {
        return $this->sourceIsbn;
    }

    public function getSourceHowpublished()
    {
        return $this->sourceHowpublished;
    }

    public function getSourceAddress()
    {
        return $this->sourceAddress;
    }

    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    protected function setSourceAuthor($sourceAuthor)
    {
        $this->sourceAuthor = $sourceAuthor;
    }

    protected function setSourceTitle($sourceTitle)
    {
        $this->sourceTitle = $sourceTitle;
    }

    protected function setSourceBooktitle($sourceBooktitle)
    {
        $this->sourceBooktitle = $sourceBooktitle;
    }

    protected function setSourceJournal($sourceJournal)
    {
        $this->sourceJournal = $sourceJournal;
    }

    protected function setSourcePublisher($sourcePublisher)
    {
        $this->sourcePublisher = $sourcePublisher;
    }

    protected function setSourceOrganization($sourceOrganization)
    {
        $this->sourceOrganization = $sourceOrganization;
    }

    protected function setSourceSchool($sourceSchool)
    {
        $this->sourceSchool = $sourceSchool;
    }

    protected function setSourceMonth($sourceMonth)
    {
        $this->sourceMonth = $sourceMonth;
    }

    protected function setSourceYear($sourceYear)
    {
        $this->sourceYear = $sourceYear;
    }

    protected function setSourceVolume($sourceVolume)
    {
        $this->sourceVolume = $sourceVolume;
    }

    protected function setSourceNumber($sourceNumber)
    {
        $this->sourceNumber = $sourceNumber;
    }

    protected function setSourceChapter($sourceChapter)
    {
        $this->sourceChapter = $sourceChapter;
    }

    protected function setSourcePages($sourcePages)
    {
        $this->sourcePages = $sourcePages;
    }

    protected function setSourceNote($sourceNote)
    {
        $this->sourceNote = $sourceNote;
    }

    protected function setSourceSeries($sourceSeries)
    {
        $this->sourceSeries = $sourceSeries;
    }

    protected function setSourceEdition($sourceEdition)
    {
        $this->sourceEdition = $sourceEdition;
    }

    protected function setSourceIsbn($sourceIsbn)
    {
        $this->sourceIsbn = $sourceIsbn;
    }

    protected function setSourceHowpublished($sourceHowpublished)
    {
        $this->sourceHowpublished = $sourceHowpublished;
    }

    protected function setSourceAddress($sourceAddress)
    {
        $this->sourceAddress = $sourceAddress;
    }

    protected function setSourceUrl($sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;
    }

    public function getFieldsToShow()
    {
        return $this->fieldsToShow;
    }

    protected function setFieldsToShow($fieldsToShow)
    {
        $this->fieldsToShow = $fieldsToShow;
    }

    public function getFieldSeparator()
    {
        return $this->fieldSeparator;
    }

    protected function setFieldSeparator($fieldSeparator)
    {
        $this->fieldSeparator = $fieldSeparator;
    }
}
