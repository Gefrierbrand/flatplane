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

use de\flatplane\interfaces\ConfigInterface;
use de\flatplane\interfaces\DocumentElementInterface;
use de\flatplane\interfaces\StyleInterface;

//todo: formattierungsobjekte: newline, newpage, (h/v-space), clearpage?

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
    use traits\ElementSettings;

    /**
     * @var DocumentElementInterface
     *  Contains a reference to the parent DocumentElement instance
     */
    protected $parent = null;
    protected $type = 'PageElement';

    /**
     * @var StyleInterface
     *  Contains a reference to the style object
     */
    protected $style = null;

    /**
     * @var ConfigInterface
     *  Contains a reference to the configuration object
     */
    protected $config = null;


    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function __clone()
    {
        $this->setConfig(clone $this->getConfig());
        //$this->setStyle(clone $this->getStyle());
        //$this->setParent(clone $this->getParent());
    }
    /**
     * @return ConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Sets the elements parent to another PageElement or the Document
     * @param DocumentElementInterface $parent
     */
    public function setParent(DocumentElementInterface $parent)
    {
        $this->parent = $parent;
    }

    /**     *
     * @return DocumentElementInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setType($type)
    {
        if (!is_array($type)) {
            $type = [$type];
        }
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
     *
     * @return StyleInterface
     */
    public function getStyle()
    {
        return $this->style;
    }
}
