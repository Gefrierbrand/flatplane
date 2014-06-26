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
 * Description of Footnote
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Source extends AbstractDocumentContentElement
{
    protected $type='source';
    protected $title = '';

    protected $altTitle;
    protected $fieldsToShow = ['sourceAuthor',
                               'sourceTitle',
                                'sourcePublisher',
                                'sourceEdition',
                                'sourceYear'];

    protected $fieldSeparator = ';';

    protected $sourceAuthor;
    protected $sourceTitle;
    protected $sourceType;
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

    protected $hyphenate = false;

    //protected $quotingStyle = 'Chicago'; //alt: harvard

    protected function generateTitleString()
    {
        $titleString = '';
        //add each requested field to the string
        foreach ($this->getFieldsToShow() as $field) {
            $methodName = 'get'.ucfirst($field);
            if (method_exists($this, $methodName)) {
                $field = $this->$methodName();
                //if the field is not empty, add the separator and the fields
                //content to the string
                if (!empty($field)) {
                    $titleString .= $this->getFieldSeparator().' ';
                    //concatenate multiple fieldentries (more than one author...)
                    if (is_array($field)) {
                        $field = implode(', ', $field);
                    }
                    $titleString .= $field;
                }
            }
        }
        //remove first separator:
        $titleString = ltrim($titleString, $this->getFieldSeparator().' ');

        $this->setAltTitle($titleString);
    }

    //todo:  fix return values
    public function getSize($startYposition = null)
    {
        return ['width' => 0, 'height' => 0];
    }

    public function getTitle()
    {
        if (empty($this->title)) {
            $this->generateTitleString();
        }
        return $this->title;
    }

    public function getFieldsToShow()
    {
        return $this->fieldsToShow;
    }

    public function getFieldSeparator()
    {
        return $this->fieldSeparator;
    }

    public function getSourceAuthor()
    {
        return $this->sourceAuthor;
    }

    public function getSourceTitle()
    {
        return $this->sourceTitle;
    }

    public function getSourceType()
    {
        return $this->sourceType;
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

    public function setFieldsToShow($fieldsToShow)
    {
        $this->fieldsToShow = $fieldsToShow;
    }

    public function setFieldSeparator($fieldSeparator)
    {
        $this->fieldSeparator = $fieldSeparator;
    }

    public function setSourceAuthor($sourceAuthor)
    {
        $this->sourceAuthor = $sourceAuthor;
    }

    public function setSourceTitle($sourceTitle)
    {
        $this->sourceTitle = $sourceTitle;
    }

    public function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;
    }

    public function setSourceBooktitle($sourceBooktitle)
    {
        $this->sourceBooktitle = $sourceBooktitle;
    }

    public function setSourceJournal($sourceJournal)
    {
        $this->sourceJournal = $sourceJournal;
    }

    public function setSourcePublisher($sourcePublisher)
    {
        $this->sourcePublisher = $sourcePublisher;
    }

    public function setSourceOrganization($sourceOrganization)
    {
        $this->sourceOrganization = $sourceOrganization;
    }

    public function setSourceSchool($sourceSchool)
    {
        $this->sourceSchool = $sourceSchool;
    }

    public function setSourceMonth($sourceMonth)
    {
        $this->sourceMonth = $sourceMonth;
    }

    public function setSourceYear($sourceYear)
    {
        $this->sourceYear = $sourceYear;
    }

    public function setSourceVolume($sourceVolume)
    {
        $this->sourceVolume = $sourceVolume;
    }

    public function setSourceNumber($sourceNumber)
    {
        $this->sourceNumber = $sourceNumber;
    }

    public function setSourceChapter($sourceChapter)
    {
        $this->sourceChapter = $sourceChapter;
    }

    public function setSourcePages($sourcePages)
    {
        $this->sourcePages = $sourcePages;
    }

    public function setSourceNote($sourceNote)
    {
        $this->sourceNote = $sourceNote;
    }

    public function setSourceSeries($sourceSeries)
    {
        $this->sourceSeries = $sourceSeries;
    }

    public function setSourceEdition($sourceEdition)
    {
        $this->sourceEdition = $sourceEdition;
    }

    public function setSourceIsbn($sourceIsbn)
    {
        $this->sourceIsbn = $sourceIsbn;
    }

    public function setSourceHowpublished($sourceHowpublished)
    {
        $this->sourceHowpublished = $sourceHowpublished;
    }

    public function setSourceAddress($sourceAddress)
    {
        $this->sourceAddress = $sourceAddress;
    }

    public function setSourceUrl($sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;
    }

    public function generateOutput()
    {
        return false;
    }

    public function getPage()
    {
        return $this->getParent()->getPage();
    }
}
