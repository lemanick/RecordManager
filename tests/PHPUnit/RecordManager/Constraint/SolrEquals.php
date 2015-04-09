<?php
/**
 * Constraint that checks whether the Solr index element is equal to another.
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
 * Constraint that checks whether the Solr index element is equal to another.
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

class PHPUnit_RecordManager_Constraint_SolrEquals extends PHPUnit_Framework_Constraint
{
    protected $expected;

    /**
     * Creates the constraint object
     *
     * @param mixed $expected value or array the other elements are evaluated against
     */
    public function __construct($expected)
    {
        parent::__construct();
        if (is_array($expected) && count($expected) == 1) {
            $expected = $expected[0];
        }
        $this->expected = $expected;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other String or array to evaluate.
     *
     * @return bool
     */
    protected function matches($other)
    {
        if (is_array($other) && count($other) == 1) {
            $other = $other[0];
        }
        if (is_array($this->expected) && is_array($other)) {
            return false == array_merge(array_diff($this->expected, $other), array_diff($other, $this->expected));
        }
        return $this->expected === $other;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        if (is_string($this->expected)) {
            return 'is equal to ' . $this->expected;
        }
        return 'is equal to ' . $this->exporter->export($this->expected);
    }
}
