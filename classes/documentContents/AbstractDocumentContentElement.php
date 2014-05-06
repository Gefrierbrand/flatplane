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

use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\interfaces\StyleInterface;

//todo: formattierungsobjekte: newline, newpage, (h/v-space), clearpage?
//todo: complete documentation!
//todo: methoden sortieren

/**
 * Abstract class for all page elements like sections, text, images, formulas, ...
 * Provides basic common functionality.
 * @author Nikolai Neff <admin@flatplane.de>
 */
abstract class AbstractDocumentContentElement implements DocumentElementInterface
{
    //import functionality horizontally from traits (reduces code length)
    use traits\ContentFunctions;
    use traits\NumberingFunctions;
    use traits\SetSettings;

    /**
     * @var DocumentElementInterface
     *  Contains a reference to the parent DocumentElement instance
     */
    protected $parent = null;
    protected $type = 'PageElement';

    protected $enumerate = true;
    protected $showInList = true;
    protected $allowSubContent = true;
    protected $isSplitable = false;
    protected $label = '';

    protected $style;

    public function __construct(array $config, StyleInterface $style)
    {
        foreach ($config as $key => $setting) {
            $name = 'set'.ucfirst($setting);
            if (method_exists($this, $name)) {
                $this->{$name}($setting);
            }
        }
        $this->style = $style;
    }

    public function __clone()
    {
        //todo: make this work ?
        //$this->setParent(clone $this->getParent());
        //$this->setStyle(clone $this->getStyle());
    }

    /**
     * Sets the elements parent to another PageElement or the Document
     * @param DocumentElementInterface $parent
     */
    public function setParent(DocumentElementInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return DocumentElementInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSize()
    {
        //todo: IMPLEMENT : probably best in subclasses / content! //maybe as abstract?
    }

    public function getPage()
    {
        //TODO: Implement me
    }

    /**
     * @param StyleInterface $style
     */
    public function setStyle(StyleInterface $style)
    {
        $this->style = $style;
    }

    /**
     * @return StyleInterface
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param bool $enumerate
     */
    public function setEnumerate($enumerate)
    {
        if ($this->parent !== null) {
            trigger_error(
                'setEnumerate() should not be called after adding the element'.
                ' as content',
                E_USER_WARNING
            );
        }
        $this->enumerate = $enumerate;
    }

    /**
     * @param bool $showInList
     */
    public function setShowInList($showInList)
    {
        if ($this->parent !== null) {
            trigger_error(
                'setShowInIndex() should not be called after adding the element'.
                ' as content',
                E_USER_WARNING
            );
        }
        $this->showInList = $showInList;
    }

    /**
     * @return bool
     */
    public function getEnumerate()
    {
        return $this->enumerate;
    }

    /**
     * @return bool
     */
    public function getShowInList()
    {
        return $this->showInList;
    }

    public function getAllowSubContent()
    {
        return $this->allowSubContent;
    }

    public function getIsSplitable()
    {
        return $this->isSplitable;
    }

    public function setAllowSubContent($allowSubContent)
    {
        $this->allowSubContent = $allowSubContent;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }
}
