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

namespace de\flatplane\utilities;

/**
 * Description of Highlighter
 *
 * @author Nikolai Neff <admin@flatplane.de>
 */
class Highlighter
{
//    /**
//     *
//     * @param string $string
//     * @return string
//     */
//    public function highlightString($string)
//    {
//        //Strip code and first span
//        $code = substr(highlight_string($string, true), 36, -15);
//        //Split lines
//        $lines = explode('<br />', $code);
//        //Count
//        $lineCount = count($lines);
//        //Calc pad length
//        $padLength = strlen($lineCount);
//        //Re-Print the code and span again
//        $highlightedCode =
//            '<span style="color: #000000; font-family: monospace; font-size: 9pt;">';
//
//        //Loop lines
//        foreach ($lines as $i => $line) {
//            //Create line number
//            $lineNumber = str_pad($i + 1, $padLength, '0', STR_PAD_LEFT);
//            //Print line
//            $highlightedCode .= sprintf(
//                '<br><span style="color: #999999">%s | </span>%s',
//                $lineNumber,
//                $line
//            );
//        }
//
//        //Close span
//        $highlightedCode .= '</span>';
//
//        return $highlightedCode;
//    }

    public function highlightFile($path)
    {
        //Strip code and first span
        $code = substr(highlight_file($path, true), 36, -15);
        //Split lines
        $lines = explode('<br />', $code);
        //Count
        $lineCount = count($lines);
        //Calc pad length
        $padLength = strlen($lineCount);
        //Re-Print the code and span again
        $highlightedCode = '';

        //Loop lines
        foreach ($lines as $i => $line) {
            //Create line number
            $lineNumber = str_pad($i + 1, $padLength, '0', STR_PAD_LEFT);
            //Print line
            if (empty(trim($line))) {
                $eol = PHP_EOL;
            } else {
                $eol = '';
            }

            $highlightedCode .= sprintf(
                '<span style="color: #999999">%s | </span>%s<br>%s',
                $lineNumber,
                $line,
                $eol
            );
        }

        return str_replace(
            ['@see',
             '@return',
             '@param',
             '@author',
             '@var',
             '@throws',
             '@todo',
             '@ignore'],
            ['<i>@see</i>',
             '<i>@return</i>',
             '<i>@param</i>',
             '<i>@author</i>',
             '<i>@var</i>',
             '<i>@throws</i>',
             '<i>@todo</i>',
             '<i>@ignore</i>'],
            $highlightedCode
        );
    }
}
