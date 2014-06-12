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

namespace de\flatplane\documentElements\traits;

use de\flatplane\interfaces\DocumentElementInterface;

/**
 * Description of Reference
 * todo: make this a separate class and use DI?
 * @author Nikolai Neff <admin@flatplane.de>
 */
trait DocumentReferences
{
    protected $validLabelTypes = ['page', 'title', 'number', 'source'];
    protected $unresolvedReferenceMarker = '?';
    protected $assumedPageNumberWidth = 3;
    protected $assumedStructureNumberWidth = 4;
    protected $assumedTitleWidth = 20;

    public function getReference($label, $type = 'number')
    {
        if (!in_array($type, $this->validLabelTypes)) {
            trigger_error(
                "$type is not a valid label type. Defaulting to Number",
                E_USER_WARNING
            );
            $type = 'number';
        }
        if (array_key_exists($label, $this->getLabels())) {
            return $this->getReferenceValue($this->getLabels()[$label], $type);
        } else {
            return $this->getDefaultReferenceValue($type);
        }
    }

    protected function getReferenceValue(DocumentElementInterface $instance, $type)
    {
        switch ($type) {
            case 'number':
                return $instance->getFormattedNumbers();
                break;
            case 'title':
                return $instance->getTitle();
                break;
            case 'page':
                $num = $instance->getPage();
                if (!empty($num) && is_numeric($num)) {
                    return $num;
                } else {
                    return $this->getDefaultReferenceValue('page');
                }
                break;
            default:
                trigger_error(
                    'Invalid reference type, defaulting to number',
                    E_USER_NOTICE
                );
                return $instance->getFormattedNumbers();
        }
    }

    protected function getDefaultReferenceValue($type)
    {
        switch ($type) {
            case 'number':
                $width = $this->getAssumedStructureNumberWidth();
                //add num-1 to the width to account for number separation
                $width += ($width-1);
                return str_repeat($this->getUnresolvedReferenceMarker(), $width);
            case 'title':
                $width = $this->getAssumedTitleWidth();
                return str_repeat($this->getUnresolvedReferenceMarker(), $width);
            case 'page':
                $width = $this->getAssumedPageNumberWidth();
                return str_repeat($this->getUnresolvedReferenceMarker(), $width);
            default:
                trigger_error(
                    'Invalid reference type, defaulting to number',
                    E_USER_NOTICE
                );
                $width = $this->getAssumedStructureNumberWidth();
                //add num-1 to the width to account for number separation
                $width += ($width-1);
                return str_repeat($this->getUnresolvedReferenceMarker(), $width);
        }
    }

    public function getAssumedPageNumberWidth()
    {
        return $this->assumedPageNumberWidth;
    }

    public function getAssumedStructureNumberWidth()
    {
        return $this->assumedStructureNumberWidth;
    }

    public function getAssumedTitleWidth()
    {
        return $this->assumedTitleWidth;
    }

    public function getUnresolvedReferenceMarker()
    {
        return $this->unresolvedReferenceMarker;
    }

    protected function setUnresolvedReferenceMarker($marker)
    {
        $this->unresolvedReferenceMarker = $marker;
    }

    protected function setAssumedPageNumberWidth($assumedPageNumberWidth)
    {
        $this->assumedPageNumberWidth = (int) $assumedPageNumberWidth;
    }

    protected function setAssumedStructureNumberWidth($assumedStructureNumberWidth)
    {
        $this->assumedStructureNumberWidth = (int) $assumedStructureNumberWidth;
    }

    protected function setAssumedTitleWidth($assumedTitleWidth)
    {
        $this->assumedTitleWidth = (int) $assumedTitleWidth;
    }
}
