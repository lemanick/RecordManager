<?php
/**
 * RecordManager custom constraint test class
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

require_once 'tests/PHPUnit/RecordManager/Constraint/SolrEquals.php';

/**
 * RecordManager custom constraint test class
 *
 * Element is either flat array (not containing nested arrays) or string.
 * Array containing single string is considered equal to the string element
 * if strings are equal.
 * Arrays are considered equal if both contain same string elements and only those.
 * Array element order is irrelevant.
 *
 * @category DataManagement
 * @package  RecordManager
 * @author   Leszek Manicki <leszek.z.manicki@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/lemanick/RecordManager
 */

class RecordManager_ConstraintTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test SolrEquals constraint
     *
     * @return void
     */
    public function testSolrEquals()
    {
        $constraint = new PHPUnit_RecordManager_Constraint_SolrEquals('foo');

        $this->assertTrue($constraint->evaluate('foo', '', true));
        $this->assertFalse($constraint->evaluate('bar', '', true));
        $this->assertTrue($constraint->evaluate(array('foo'), '', true));
        $this->assertFalse($constraint->evaluate(array('bar'), '', true));
        $this->assertFalse($constraint->evaluate(array('foo', 'bar'), '', true));
        $this->assertFalse($constraint->evaluate(array(), '', true));
    }

    /**
     * Test SolrEquals constraint
     *
     * @return void
     */
    public function testSolrEquals2()
    {
        $constraint = new PHPUnit_RecordManager_Constraint_SolrEquals(array('foo', 'bar'));

        $this->assertTrue($constraint->evaluate(array('foo', 'bar'), '', true));
        $this->assertTrue($constraint->evaluate(array('bar', 'foo'), '', true));
        $this->assertFalse($constraint->evaluate(array('foo', 'bar', 'baz'), '', true));
        $this->assertFalse($constraint->evaluate('foo', '', true));
        $this->assertFalse($constraint->evaluate(array('foo'), '', true));
        $this->assertFalse($constraint->evaluate(array('bar'), '', true));
        $this->assertFalse($constraint->evaluate(array(), '', true));
    }

}
