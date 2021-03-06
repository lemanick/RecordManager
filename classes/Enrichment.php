<?php
/**
 * Enrichment Class
 *
 * PHP version 5
 *
 * Copyright (C) The National Library of Finland 2014.
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
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/KDK-Alli/RecordManager
 */

require_once 'HTTP/Request2.php';

/**
 * Enrichment Class
 *
 * This is a base class for enrichment of records.
 *
 * @category DataManagement
 * @package  RecordManager
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/KDK-Alli/RecordManager
 */

class Enrichment
{
    /**
     * Mongo DB connection
     *
     * @var MongoDB
     */
    protected $db;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $log;

    /**
     * Maximum age of cached data in seconds
     *
     * @var number
     */
    protected $maxCacheAge;

    /**
     * HTTP Request
     *
     * @var HTTP_Request2
     */
    protected $request = null;

    /**
     * Maximum number of HTTP request attempts
     *
     * @var int
     */
    protected $maxTries;

    /**
     * Delay between HTTP request attempts (seconds)
     *
     * @var int
     */
    protected $retryWait;

    /**
     * Constructor
     *
     * @param MongoDB $db  Database connection (for cache)
     * @param Logger  $log Logger
     */
    public function __construct($db, $log)
    {
        global $configArray;

        $this->db = $db;
        $this->log = $log;
        $this->maxCacheAge = isset($configArray['Enrichment']['cache_expiration'])
            ? $configArray['Enrichment']['cache_expiration'] * 60
            : 86400;
        $this->maxTries = isset($configArray['Enrichment']['max_tries'])
            ? $configArray['Enrichment']['max_tries']
            : 15;
        $this->retryWait = isset($configArray['Enrichment']['retry_wait'])
            ? $configArray['Enrichment']['retry_wait']
            : 30;
    }

    /**
     * Enrich the record and return any additions in solrArray
     *
     * @param string    $sourceId  Source ID
     * @param multitype $record    Record
     * @param array     $solrArray Metadata to be sent to Solr
     *
     * @return void
     */
    public function enrich($sourceId, $record, &$solrArray)
    {
        // Implemented in child classes
    }

    /**
     * A helper function that retrieves external metadata and caches it
     *
     * @param string   $url     URL to fetch
     * @param string   $id      ID of the entity to fetch
     * @param string[] $headers Optional headers to add to the request
     *
     * @return multitype Metadata (typically XML)
     */
    protected function getExternalData($url, $id, $headers = array())
    {
        global $configArray;
        $cached = $this->db->uriCache->findOne(
            array(
                '_id' => $id,
                'timestamp' => array(
                    '$gt' => new MongoDate(time() - $this->maxCacheAge)
                 )
            )
        );
        if ($cached) {
            return $cached['data'];
        }

        if (is_null($this->request)) {
            $this->request = new HTTP_Request2(
                $url,
                HTTP_Request2::METHOD_GET,
                array(
                    'ssl_verify_peer' => false,
                    'follow_redirects' => true
                )
            );
            $this->request->setHeader('Connection', 'Keep-Alive');
            $this->request->setHeader('User-Agent', 'RecordManager');
        } else {
            $this->request->setUrl($url);
        }
        if ($headers) {
            $this->request->setHeader($headers);
        }

        for ($try = 1; $try <= $this->maxTries; $try++) {
            try {
                $response = $this->request->send();
            } catch (Exception $e) {
                if ($try < $this->maxTries) {
                    $this->log->log(
                        'getExternalData',
                        "HTTP request for '$url' failed (" . $e->getMessage() . "), retrying in {$this->retryWait} seconds...",
                        Logger::WARNING
                    );
                    sleep($this->retryWait);
                    continue;
                }
                throw $e;
            }
            if ($try < $this->maxTries) {
                $code = $response->getStatus();
                if ($code >= 300 && $code != 404) {
                    $this->log->log(
                        'getExternalData',
                        "HTTP request for '$url' failed ($code), retrying in {$this->retryWait} seconds...",
                        Logger::WARNING
                    );
                    sleep($this->retryWait);
                    continue;
                }
            }
            break;
        }

        $code = $response->getStatus();
        if ($code >= 300 && $code != 404) {
            throw new Exception("Enrichment failed to fetch '$url': $code");
        }

        $data = $code != 404 ? $response->getBody() : '';

        $this->db->uriCache->save(
            array(
                '_id' => $id,
                'timestamp' => new MongoDate(),
                'data' => $data
            )
        );

        return $data;
    }
}
