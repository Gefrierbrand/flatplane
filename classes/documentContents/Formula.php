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
 * Description of formula
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Formula extends DocumentContentElement
{
    protected $numberingLevel = -1;

    protected $type='formula';
    protected $allowSubContent = ['formula'];

    protected $title='Formula';

    protected $code;
    protected $font;
    protected $format;
    protected $availableFonts = ['TeX', 'STIX-Web', 'Asana-Math', 'Neo-Euler',
                                'Gyre-Pagella', 'Gyre-Termes', 'Latin-Modern'];
    protected $availableFormats = ['TeX','MathML','AsciiMath'];

    public function __construct(
        $code,
        $font = 'TeX',
        $format = 'TeX',
        $showInIndex = true,
        $enumerate = true
    ) {
        $this->code = $code;
        $this->showInIndex = $showInIndex;
        $this->enumerate = $enumerate;

        if (in_array($font, $this->availableFonts, true)) {
            $this->font = $font;
        } else {
            trigger_error(
                "Font $font not available, defaulting to TeX",
                E_USER_NOTICE
            );
            $this->font = 'TeX';
        }

        if (in_array($format, $this->availableFormats, true)) {
            $this->format = $format;
        } else {
            trigger_error(
                "Format $format not available, defaulting to TeX",
                E_USER_NOTICE
            );
            $this->format = 'TeX';
        }
    }
}
