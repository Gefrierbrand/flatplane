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
 * Description of PageBreak
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class PageBreak extends AbstractDocumentContentElement
{
    protected $type = 'pagebreak';
    protected $title = 'Pagebreak';
    protected $enumerate = false;
    protected $showInList = false;
    protected $allowSubContent = false;

    public function generateOutput()
    {
        //do nothing
    }
}
