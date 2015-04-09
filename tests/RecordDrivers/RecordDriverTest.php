<?php
/**
 * Generic Record Driver test class
 *
 * PHP version 5
 *
 * Copyright (C) Eero Heikkinen 2013.
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
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @author   Leszek Manicki <leszek.z.manicki@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/KDK-Alli/RecordManager
 */

require_once 'tests/PHPUnit/RecordManager/Constraint/SolrEquals.php';
require_once 'classes/RecordFactory.php';
require_once 'classes/MetadataUtils.php';

/**
 * Generic Record Driver Test Class
 *
 * @category DataManagement
 * @package  RecordManager
 * @author   Eero Heikkinen <eero.heikkinen@gmail.com>
 * @author   Leszek Manicki <leszek.z.manicki@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/KDK-Alli/RecordManager
 */
abstract class RecordDriverTest extends PHPUnit_Framework_TestCase
{
    // Override this from subclass
    protected $driver;

    /**
     * Initialize arrays used by MetadataUtils.
     *
     * @return void
     * @access public
     */
    public static function setUpBeforeClass()
    {
        MetadataUtils::$abbreviations = array();
        MetadataUtils::$articles = array();
    }

    /**
     * Standard setup method.
     *
     * @return void
     * @access public
     */
    public function setUp()
    {
        if(empty($this->driver))
            $this->markTestIncomplete('Record driver needs to be set in subclass.');
    }

    /**
     * Process a sample record
     *
     * @param string $sample Sample record file
     *
     * @return array SOLR record array
     */
    protected function processSample($sample)
    {
        $actualdir = dirname(__FILE__);
        $sample = file_get_contents($actualdir . "/../samples/" . $sample);
        $record = RecordFactory::createRecord($this->driver, $sample, "__unit_test_no_id__", "__unit_test_no_source__");
        return $record->toSolrArray();
    }

    /**
     * Asserts that two Solr array entries are equal:
     *  - array containg single element is considered to be
     *    equal to the same separate element (not in array),
     *  - order of elements in array is irrelevant.
     *
     * @param mixed  $expected Expected value
     * @param mixed  $actual   Tested value
     * @param string $message  Optional message
     *
     * @return mixed
     */
    public static function assertSolrEquals($expected, $actual, $message='')
    {
        PHPUnit_Framework_Assert::assertThat(
            $actual,
            new PHPUnit_RecordManager_Constraint_SolrEquals($expected),
            $message
        );
    }

}
