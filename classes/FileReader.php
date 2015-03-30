<?php
/**
 * File Reader Class
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2015
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category DataManagement
 * @package  RecordManager
 * @author   Leszek Manicki <leszek.z.manicki@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/lemanick/RecordManager
 */

/**
 * FileReader Class
 *
 * Base class for reading non-XML files and converting them to XML on the fly.
 *
 * @category DataManagement
 * @package  RecordManager
 * @author   Leszek Manicki <leszek.z.manicki@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/lemanick/RecordManager
 */
class FileReader
{
    protected $source; // Source ID
    protected $idPrefix; // Record ID prefix

    protected $transformation = null; // Transformation applied to records read

    /**
     * Constructor
     *
     * @param string $source                   Data source ID
     * @param string $idPrefix                 Record ID prefix
     * @param string $basePath                 RecordManager main directory location
     * @param string $transformationStylesheet Postprocess-transformation stylesheet
     */
    function __construct($source, $idPrefix, $basePath, $transformationStylesheet = null)
    {
        $this->source = $source;
        $this->idPrefix = $idPrefix;

        if (isset($transformationStylesheet)) {
            $style = new DOMDocument();
            if ($style->load("$basePath/transformations/$transformationStylesheet") === false) {
                throw new Exception("Could not load '$basePath/transformations/" . $transformationStylesheet . "'");
            }
            $this->transformation = new XSLTProcessor();
            $this->transformation->importStylesheet($style);
        }
    }

    /**
     * Read records from file and return as a XML string.
     * Return false on failure.
     *
     * @param string $file filename to read records from
     *
     * @return string|boolean
     */
    public function read($file)
    {
        die('Unimplemented method: read');
    }

    /**
     * Return a parameter specified in fileReaderParams[] of datasources.ini
     * Based on the Driver param feature of the BaseRecord class by Ere Maijala.
     *
     * @param string $parameter Parameter name
     * @param bool   $default   Default value if the parameter is not set
     *
     * @return mixed Value
     */
    protected function getFileReaderParam($parameter, $default = false)
    {
        global $configArray;

        if (!isset($configArray['dataSourceSettings'][$this->source]['fileReaderParams'])) {
            return $default;
        }
        $iniValues = parse_ini_string(
            implode(
                PHP_EOL,
                $configArray['dataSourceSettings'][$this->source]['fileReaderParams']
            )
        );

        return isset($iniValues[$parameter]) ? $iniValues[$parameter] : $default;
    }
}
