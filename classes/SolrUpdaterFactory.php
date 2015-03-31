<?php
/**
 * SolrUpdaterFactory Class
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2015.
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
 * SolrUpdaterFactory Class
 *
 * This is a factory class to build objects for updating Solr index.
 * It allows for using custom Solr updater classes.
 *
 * @category DataManagement
 * @package  RecordManager
 * @author   Leszek Manicki <leszek.z.manicki@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/lemanick/RecordManager
 */
class SolrUpdaterFactory
{
    /**
     * Construct solr updater classes based on the config.
     *
     * @param MongoDB $db       Database connection
     * @param string  $basePath RecordManager main directory
     * @param object  $log      Logger
     * @param boolean $verbose  Whether to output verbose messages
     *
     * @return object The Solr index updater object.
     * @throws Exception
     */
    static function createSolrUpdater($db, $basePath, $log, $verbose)
    {
        global $configArray;

        $class = isset($configArray['Site']['solr_updater'])
            ? $configArray['Site']['solr_updater']
            : 'SolrUpdater';
        include_once "$class.php";
        if (class_exists($class)) {
            $obj = new $class($db, $basePath, $log, $verbose);
            return $obj;
        }

        throw new Exception("Could not load solr updater class: '$class'");
    }
}
