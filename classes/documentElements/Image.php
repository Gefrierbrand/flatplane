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

use de\flatplane\interfaces\documentElements\ImageInterface;
use de\flatplane\utilities\SVGSize;
use Imagick;
use RuntimeException;
use SplFileInfo;
use SplFileObject;

/**
 * Description of Image
 * todo: title/desc/numbering
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Image extends AbstractDocumentContentElement implements ImageInterface
{
    protected $type = 'image';
    protected $allowSubContent = ['image'];

    protected $path;
    protected $imageType;

    protected $title = 'Image';
    protected $titlePosition = ['top', 'center'];

    protected $caption;
    protected $captionPosition = ['bottom', 'center'];

    protected $rotation = 0;
    protected $resolution; //dpi
    protected $width;
    protected $height;
    protected $fitOnPage = true; //not used atm

    protected $alignment = 'center';

    protected $titlePrefix = 'Figure';
    protected $captionPrefix = '';
    protected $descriptionSeparator = ':';
    protected $numberingPosition = ['title', 'afterPrefix'];

    protected $margins = ['default' => 0, 'title' => 5, 'caption' => 5];

    /**
     * Returns image-type and -path as string
     * @return string
     */
    public function __toString()
    {
        return (string) 'Image: ('.$this->getImageType().') '.$this->getPath();
    }

    /**
     * todo: margins: top/bottom
     * @return int
     */
    public function generateOutput()
    {
        $pdf = $this->getPDF();
        $startPage = $pdf->getPage();
        $dim = $this->getImageDimensions();

        if ($dim['width'] < $this->getPageMeasurements()['textWidth']) {
            //todo: use textwidth and margins, only center if required
            $xCenterPos = ($this->getPageMeasurements()['pageWidth'] - $dim['width'])/2;
        } else {
            $xCenterPos = '';
        }

        $pdf->SetY($pdf->GetY() + $this->getMargins('top'));
        //todo: center images
        if ($this->getImageType() == 'svg') {
            $pdf->ImageSVG(
                $this->getPath(),
                $xCenterPos,
                '',
                $dim['width'],
                $dim['height'],
                '',
                'N'
            );
        } elseif ($this->getImageType() == 'eps' || $this->getImageType() == 'ai') {
            $pdf->ImageEps(
                $this->getPath(),
                $xCenterPos,
                '',
                $dim['width'],
                $dim['height'],
                '',
                true,
                'N'
            );
        } else {
            $pdf->Image(
                $this->getPath(),
                $xCenterPos,
                '',
                $dim['width'],
                $dim['height'],
                '',
                '',
                'N'
            );
        }
        $pdf->SetY($pdf->GetY() + $this->getMargins('caption'));
        $this->applyStyles();
        //todo: implement title/caption position & placement
        $html = '<b>'.$this->getTitlePrefix().' '.$this->getFormattedNumbers().':</b>  '.$this->getTitle();
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 0, false, true, 'C');
        //$pdf->MultiCell(0, 0, $this->getCompleteTitle(), 0, 'C', false, 0);

        //$this->applyStyles('caption');
        //$pdf->MultiCell(0, 0, $this->getCompleteCaption(), 0, 'C');

        $pdf->SetY($pdf->GetY() + $this->getMargins('top'));
        return $pdf->getPage() - $startPage;
    }

    protected function getCompleteTitle()
    {
        $prefix = $this->getTitlePrefix();
        if (empty($prefix)) {
            $separator = '';
        } else {
            $separator = $this->getDescriptionSeparator();
        }
        if ($this->getEnumerate()) {
            if (strtolower($this->getNumberingPosition()) == 'title') {
                return $prefix.' '.$this->getFormattedNumbers()
                       .$separator.$this->getTitle();
            } else {
                return $prefix.$separator.$this->getTitle();
            }
        } else {
            return $prefix.$separator.$this->getTitle();
        }
    }

    protected function getCompleteCaption()
    {
        $prefix = $this->getCaptionPrefix();
        if (empty($prefix)) {
            $separator = '';
        } else {
            $separator = $this->getDescriptionSeparator();
        }
        if ($this->getEnumerate()) {
            if (strtolower($this->getNumberingPosition()) == 'caption') {
                return $prefix.' '.$this->getFormattedNumbers()
                       .$separator.$this->getCaption();
            } else {
                return $prefix.$separator.$this->getCaption();
            }
        } else {
            return $prefix.$separator.$this->getCaption();
        }
    }

    /**
     * estimates the type of the image
     * @return string
     */
    protected function estimateImageType()
    {
        //todo: use MIME-types and EXIF Data? / use imagick?
        $info = new SplFileInfo($this->getPath());
        return strtolower($info->getExtension());
    }

    /**
     * This method returns the dimensions of the image in user units.
     * These are either userdefined or estimated from the file itself.
     * @return array
     *  array containing width & height in user units.
     */
    protected function getImageDimensions()
    {
        if (empty($this->getWidth()) && empty($this->getHeight())) {
            $dimensions = $this->getImageDimensionsFromFile();
        } else {
            if (is_numeric($this->getWidth()) && is_numeric($this->getHeight())) {
                //return the size defined by the user
                $dimensions['width'] = $this->getWidth();
                $dimensions['height'] = $this->getHeight();
            } else {
                //parse if the user-provides sizes are not numeric
                //(e.g. for values like "textWidth")
                $dimensions = $this->parseDimensions();
            }
        }
        return $this->adjustDimensionsToPage($dimensions);
    }

    /**
     * This method tries to adjust the image dimensions to fit the page while
     * keeping the aspect ratio of the image constant.
     * The image is not resampled, just the drawing size is changed, which might
     * lead to higher pixel-densities on the output medium.
     * @param array $dimensions
     *  original image size
     * @return array
     *  new adjusted image size
     * @todo: factor in descriptions (title and caption)
     */
    protected function adjustDimensionsToPage(array $dimensions)
    {
        //check if the dimensions are set and not zero
        $this->validateDimensions($dimensions);

        //only change if the fitOnPage property is true
        if ($this->getFitOnPage()) {
            //get the available space on the current page
            $pageMeasurements = $this->getPageMeasurements();
            //return the old dimensions if they both fit in the available space
            //todo: provide option to use max available space (with or without
            //image upscaling/resampling)
            if ($dimensions['width'] <= $pageMeasurements['textWidth']
                && $dimensions['height'] <= $pageMeasurements['textHeight']
            ) {
                return $dimensions;
            }

            //the width is usually the constraining direction, so set it to
            //the maximum size and ajust the height according to the original
            //aspect ratio
            $aspectRatio = $dimensions['width']/$dimensions['height'];
            $newWidth = $pageMeasurements['textWidth'];
            $newHeight = $newWidth/$aspectRatio;

            //if the height is still to big, adjust the image again, this time
            //setting the height to the maximum available space and adjusting
            //the width
            if ($newHeight >= $pageMeasurements['textHeight']) {
                $newHeight = $pageMeasurements['textHeight'];
                $newWidth = $aspectRatio*$newHeight;
            }
            return ['width' => $newWidth, 'height' => $newHeight];
        } else {
            return $dimensions;
        }
    }

    /**
     * This method checks if the required keys 'height' and 'width' are set and
     * greater than 0. An error is triggered if they are missing.
     * @param array $dimensions
     *  array to check
     * @param bool $units
     *  if true, also check if the keys 'wUnit' and 'hUnit' are present and non-zero
     */
    protected function validateDimensions(array $dimensions, $units = false)
    {
        if (empty($dimensions['width']) || empty($dimensions['height'])) {
            trigger_error('Image dimensions are unset or zero', E_USER_WARNING);
        } else {
            if ($dimensions['width'] < 0 or $dimensions['height'] < 0) {
                trigger_error('image dimensions are negaive!', E_USER_WARNING);
            }
        }
        if ($units) {
            if (empty($dimensions['wUnit']) || empty($dimensions['hUnit'])) {
                trigger_error('Image units are unset or zero', E_USER_WARNING);
            }
        }
    }

    /**
     * This method parses a given string into an optional factor and a reference-
     * size. The string has to be in the format "factor*referencesize".
     * The factor and '*' might be omitted and will then default to 1.
     * Currently available reference-sizes are defined in getPageMeasurements().
     * These are then evaluated and the resulting dimensions are returned.
     * @return array
     *  dimensions in user units
     * @throws RuntimeException
     * @see getPageMeasurements()
     */
    protected function parseDimensions()
    {
        //split the with & height strings at the * sign
        $componets['width'] = explode('*', strtolower($this->getWidth()));
        $componets['height'] = explode('*', strtolower($this->getHeight()));

        //check if the results are valid reference-sizes
        if (!$this->checkComponents($componets)) {
            trigger_error(
                'Invalid Width/Height arguments supplied,'
                .' trying to read dimensions from file instead.',
                E_USER_WARNING
            );
            //get the dimensions from the file if the string evaluation failed
            return $this->getImageDimensionsFromFile();
        }

        //get the available reference-sizes
        $pageMeasurements = $this->getPageMeasurements();

        //analyze the components of the strings for width & height and split
        //them into factor and value
        foreach ($componets as $key => $direction) {
            //direction (width or height) has 2 components if a factor is given
            if (count($direction) == 2) {
                $factor[$key] = $direction[0];
                $value[$key] = $direction[1];
            } else {
                //default to factor 1 if not otherwise given
                $factor[$key] = 1;
                $value[$key] = $direction[0];
            }
            //check if the requested reference-size is a defined page-measurement
            if (array_key_exists($value[$key], $pageMeasurements)) {
                $value[$key] = $pageMeasurements[$key];
            } else {
                throw new RuntimeException('invalid imagesize-component value');
            }
        }

        //evaulate the resulting sizes
        $width = $factor['width']*$value['width'];
        $height = $factor['height']*$value['height'];

        return ['width' => $width, 'height' => $height];
    }

    /**
     * todo: doc
     * @param array $components
     * @return boolean
     * FIXME: allow secend direction to be empty -> autocalc using aspect ratio (if available)
     */
    protected function checkComponents(array $components)
    {
        //todo: set this as property?
        $allowedReferenceValues = ['textwidth', 'pagewidth', 'pageheight'];
        foreach ($components as $direction) {
            if (!is_array($direction)) {
                return false;
            }
            switch (count($direction))
            {
                case 1:
                    $direction[0] = strtolower($direction[0]);
                    if (!in_array($direction[0], $allowedReferenceValues, true)) {
                        return false;
                    }
                    break;
                case 2:
                    if (!is_numeric($direction[0])) {
                        return false;
                    }
                    $direction[1] = strtolower($direction[1]);
                    if (!in_array($direction[1], $allowedReferenceValues, true)) {
                        return false;
                    }
                    break;
                default:
                    return false;
            }
        }
        return true;
    }

    /**
     * This method returns the size of the image based on the actual file.
     * @return array
     */
    protected function getImageDimensionsFromFile()
    {
        if ($this->getImageType() == 'svg') {
            return $this->getSVGMeasurementsFromFile();
        } elseif ($this->getImageType() == 'eps') {
            return $this->getEPSMeasurementsFromFile();
        } else {
            return $this->getGDMeasurementsFromFile();
        }
    }


    /**
     * todo: doc
     * @return array
     */
    protected function getSVGMeasurementsFromFile()
    {
        $svgSize = new SVGSize($this->getPath());
        $dimensions = $svgSize->getDimensions();

        return $this->convertImageSizeToUserUnits($dimensions);
    }

    /**
     * todo: implement, doc
     * @return array
     */
    protected function getEPSMeasurementsFromFile()
    {
        //search boundingbox
        $boundingBox = $this->findBoundingBox();
        if (!$boundingBox) {
            throw new RuntimeException(
                'EPS file did not contain valid BoundingBox.'
            );
        }

        $dimensions = ['width' => $boundingBox['width'],
                       'wUnit' => 'pt',
                       'height' => $boundingBox['height'],
                       'hUnit' => 'pt'];

        //convert to user-units & return
        return $this->convertImageSizeToUserUnits($dimensions);
    }

    /**
     *
     * @return array|bool
     */
    protected function findBoundingBox()
    {
        //EPS syntax:
        //%%BoundingBox: llx lly urx ury
        $file = new SplFileObject($this->getPath());
        foreach ($file as $line) {
            $regEx = '/^(%%BoundingBox:){1}[ ]?(\d+)\ {1}(\d+)\ {1}(\d+)\ {1}(\d+)$/';
            if (preg_match_all($regEx, trim($line), $matches)) {
                $width = $matches[2] - $matches[0];
                $height = $width = $matches[3] - $matches[1];
                return ['width' => $width, 'height' => $height];
            }
        }
        return false;
    }
    /**
     * todo: doc
     * @return type
     * @throws RuntimeException
     */
    protected function getGDMeasurementsFromFile()
    {
        $filename = $this->getPath();
        $imageInfos = getimagesize($filename);
        if ($imageInfos == false) {
            throw new RuntimeException(
                'imagesize of '.$filename.' can\'t be determined; check if the '.
                'file is not corrupted and the image-format supported'
            );
        }

        return $this->convertImageSizeToUserUnits(
            ['width' => $imageInfos[0],
             'wUnit' => 'px',
             'height' => $imageInfos[1],
             'hUnit' => 'px']
        );
    }

    /**
     * todo: doc
     * @param array $dimensions
     * @return type
     */
    protected function convertImageSizeToUserUnits(array $dimensions)
    {
        $this->validateDimensions($dimensions, true);
        $resolution = $this->estimateImageResolution(); //result in dpi

        $pdf = $this->getPDF();

        $oldImageScale = $pdf->getImageScale();
        //scale to the default TCPDF resolution of 72 dpi
        $pdf->setImageScale($resolution/72);

        $newWidth = $pdf->getHTMLUnitToUnits(
            $dimensions['width'],
            1,
            $dimensions['wUnit'],
            false
        );
        $newHeight = $pdf->getHTMLUnitToUnits(
            $dimensions['height'],
            1,
            $dimensions['hUnit'],
            false
        );

        //restore previous image scale
        $pdf->setImageScale($oldImageScale);

        return ['width' => $newWidth,
                'height' => $newHeight];
    }

    /**
     * This method tries to use ImageMagic to determine the resolution of the
     * image in question if no resolution is otherwise specified. The default
     * value of 72 dpi is returned if imagick fails.
     * @return float
     *  Image resolution in DPI (dots per inch)
     */
    protected function estimateImageResolution()
    {
        if (!empty($this->getResolution())) {
            return $this->getResolution();
        } else {
            //we cannot savely rely on imagick being installed and working
            //correctly, as it's installation is tricky at best on windows platforms
            if (extension_loaded('imagick')) {
                $image = new Imagick($this->getPath());
                if (empty($image->queryformats())) {
                    trigger_error(
                        'Imagick has no supported formats, please check'
                        .' its installation. Defaulting to 72 dpi.',
                        E_USER_NOTICE
                    );
                    return 72;
                }
                //ges basic information about the image
                $imageStats = $image->identifyimage();

                if (empty($imageStats['resolution'])
                    || !is_array($imageStats['resolution'])
                ) {
                    trigger_error(
                        'Image resolution could not be determined, assuming 72 dpi',
                        E_USER_NOTICE
                    );
                    return 72;
                } else {
                    //currently different resolutions per axis are not supported
                    //use x-resolution for both.
                    $resolution = $imageStats['resolution']['x'];
                }
                //the resolution reported back from ImageMagick is dependend on
                //the filetype. JPEG resolution is usually given in DPI while
                //PNG defaults to PPCM (pixels per centimeter)
                if (empty($imageStats['units'])) {
                    $unit = 'PixelsPerInch'; //default to dpi if the unit is unset
                } else {
                    $unit = $imageStats['units'];
                }
                if ($unit == 'PixelsPerCentimeter') {
                    $resolution = $resolution * 2.54; //convert PPCM to DPI
                }
                return $resolution;
            } else {
                return 72; //default to 72 dpi if imagick is unavailable
            }
        }
    }

    public function getTitlePosition()
    {
        return $this->titlePosition;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getImageType()
    {
        if (empty($this->imageType)) {
            $this->imageType = $this->estimateImageType();
        }
        return $this->imageType;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function getCaptionPosition()
    {
        return $this->captionPosition;
    }

    public function getRotation()
    {
        return $this->rotation;
    }

    public function getScale()
    {
        return $this->scale;
    }

    public function getResolution()
    {
        return $this->resolution;
    }

    public function getPlacement()
    {
        return $this->placement;
    }

    public function setTitlePosition($titlePosition)
    {
        $this->titlePosition = $titlePosition;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setImageType($imageType)
    {
        $this->imageType = $imageType;
    }

    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    public function setCaptionPosition($captionPosition)
    {
        $this->captionPosition = $captionPosition;
    }

    public function setRotation($rotation)
    {
        $this->rotation = $rotation;
    }

    public function setScale($scale)
    {
        $this->scale = $scale;
    }

    public function setResolution($resolution)
    {
        $this->resolution = $resolution;
    }

    public function setPlacement($placement)
    {
        $this->placement = $placement;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getKeepAspectRatio()
    {
        return $this->keepAspectRatio;
    }

    public function setKeepAspectRatio($keepAspectRatio)
    {
        $this->keepAspectRatio = $keepAspectRatio;
    }

    public function getFitOnPage()
    {
        return $this->fitOnPage;
    }

    public function getAlignment()
    {
        return $this->alignment;
    }

    public function setFitOnPage($fitOnPage)
    {
        $this->fitOnPage = $fitOnPage;
    }

    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;
    }

    public function getTitleMargin()
    {
        return $this->titleMargin;
    }

    public function getCaptionMargin()
    {
        return $this->captionMargin;
    }

    public function getTitlePrefix()
    {
        return $this->titlePrefix;
    }

    public function getCaptionPrefix()
    {
        return $this->captionPrefix;
    }

    public function getDescriptionSeparator()
    {
        return $this->descriptionSeparator;
    }

    public function getNumberingPosition()
    {
        return $this->numberingPosition;
    }

    public function setTitlePrefix($titlePrefix)
    {
        $this->titlePrefix = $titlePrefix;
    }

    public function setCaptionPrefix($captionPrefix)
    {
        $this->captionPrefix = $captionPrefix;
    }

    public function setDescriptionSeparator($descriptionSeparator)
    {
        $this->descriptionSeparator = $descriptionSeparator;
    }

    public function setNumberingPosition($numberingPosition)
    {
        $this->numberingPosition = $numberingPosition;
    }
}
