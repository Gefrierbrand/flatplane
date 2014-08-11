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
    /**
     * Get the title position
     * @return string
     */
    public function getTitlePosition();

    /**
     * Get the path to the image file
     * @return string
     */
    public function getPath();

    /**
     * Get the images file-type. If it is not manually set, the type is
     * estimated using the file-extension
     * @return sting
     */
    public function getImageType();

    /**
     * Get the image caption
     * @return string
     */
    public function getCaption();

    /**
     * Get the image captions positioning parameters
     * @return array
     */
    public function getCaptionPosition();

    /**
     * Get the images rotation in degrees (clockwise);
     * @return float
     */
    public function getRotation();

    /**
     * Get the images resolution
     * @return float
     *  value in ppi (pixels per inch)
     */
    public function getResolution();

    /**
     * Set the title-string positioning
     * @param array $titlePosition
     */
    public function setTitlePosition(array $titlePosition);

    /**
     * Set the path tho the imagefile
     * @param string $path
     */
    public function setPath($path);

    /**
     * Set the images file-type
     * @param string $imageType
     */
    public function setImageType($imageType);

    /**
     * Set the images caption
     * @param string $caption
     */
    public function setCaption($caption);

    /**
     * Set the images caption-positioning parameters
     * @param array $captionPosition
     */
    public function setCaptionPosition(array $captionPosition);

    /**
     * Set the images rotation in degrees (clockwise)
     * @param float $rotation
     */
    public function setRotation($rotation);

    /**
     * Set the images display-resolution in ppi (pixels per inch)
     * @param float $resolution
     */
    public function setResolution($resolution);

    /**
     * Get the images width in user-units. Does not include the title or caption
     * @see getSize()
     * @return float
     */
    public function getWidth();

    /**
     * Get the images height in user-units. Does not include the title or caption
     * @see getSize()
     * @return float
     */
    public function getHeight();

    /**
     * Set the images width in user-units. Does not include the title or caption
     * @param float $width
     */
    public function setWidth($width);

    /**
     * Set the images height in user-units. Does not include the title or caption
     * @param float $height
     */
    public function setHeight($height);

    /**
     * @return bool
     */
    public function getKeepAspectRatio();

    /**
     * Enable/Disable maintaining the aspect ratio if width or height are
     * adjusted independendly
     * @param bool $keepAspectRatio
     */
    public function setKeepAspectRatio($keepAspectRatio);

    /**
     * @return bool
     */
    public function getFitOnPage();

    /**
     * Get the images horizontal alignment
     * @return string
     */
    public function getAlignment();

    /**
     * Enables / disables the automatic scaling of the image to fit on the page
     * if its dimensions are to large
     * @param bool $fitOnPage
     */
    public function setFitOnPage($fitOnPage);

    /**
     * Sets the images horizontal alignment
     * @param string $alignment
     */
    public function setAlignment($alignment);

    /**
     * Get the vertival distance between the title an the image in user-units
     * @return float
     */
    public function getTitleMargin();

    /**
     * Get the vertical distance between the caption and the image in user-units
     * @return float
     */
    public function getCaptionMargin();

    /**
     * Get the titlesting prefix
     * @return string
     */
    public function getTitlePrefix();

    /**
     * Get the captionstring prefix
     * @return string
     */
    public function getCaptionPrefix();

    /**
     * @return String
     */
    public function getDescriptionSeparator();

    /**
     * Get the numbering positioning options
     * @return array
     */
    public function getNumberingPosition();

    /**
     * Set the title prefix
     * @param string $titlePrefix
     */
    public function setTitlePrefix($titlePrefix);

    /**
     * Set the caption prefix
     * @param String $captionPrefix
     */
    public function setCaptionPrefix($captionPrefix);

    /**
     * @param string $descriptionSeparator
     */
    public function setDescriptionSeparator($descriptionSeparator);

    /**
     * Set the numering positioning options
     * @param array $numberingPosition
     */
    public function setNumberingPosition(array $numberingPosition);

    /**
     * Get weather the cache is used
     * @return bool
     */
    public function getUseCache();

    /**
     * Get the path to the cache file
     * @return string
     */
    public function getCachePath();

    /**
     * Enable/Disable the usage of the imagesize-cache
     * @param bool $useCache
     */
    public function setUseCache($useCache);

    /**
     * Set the path to the cachefile
     * @param string $cachePath
     */
    public function setCachePath($cachePath);
}
