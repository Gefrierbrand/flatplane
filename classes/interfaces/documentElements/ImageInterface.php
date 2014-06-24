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

namespace de\flatplane\interfaces\documentElements;

use de\flatplane\interfaces\DocumentElementInterface;

/**
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
interface ImageInterface extends DocumentElementInterface
{
    public function getTitlePosition();
    public function getPath();
    public function getImageType();
    public function getCaption();
    public function getCaptionPosition();
    public function getRotation();
    public function getScale();
    public function getResolution();
    public function getPlacement();

    public function setTitlePosition($titlePosition);
    public function setPath($path);
    public function setImageType($imageType);
    public function setCaption($caption);
    public function setCaptionPosition($captionPosition);
    public function setRotation($rotation);
    public function setScale($scale);
    public function setResolution($resolution);
    public function setPlacement($placement);

    public function getWidth();
    public function getHeight();

    public function setWidth($width);
    public function setHeight($height);

    public function getKeepAspectRatio();
    public function setKeepAspectRatio($keepAspectRatio);

    public function getFitOnPage();
    public function getAlignment();

    public function setFitOnPage($fitOnPage);
    public function setAlignment($alignment);

    public function getTitleMargin();
    public function getCaptionMargin();

    public function getTitlePrefix();
    public function getCaptionPrefix();

    public function getDescriptionSeparator();
    public function getNumberingPosition();

    public function setTitlePrefix($titlePrefix);
    public function setCaptionPrefix($captionPrefix);

    public function setDescriptionSeparator($descriptionSeparator);
    public function setNumberingPosition($numberingPosition);
}
