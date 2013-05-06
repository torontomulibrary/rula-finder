<?php
/**
 *
 * Copyright (C) Serials Solutions 2009.
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
 */

require_once 'HTTP/Request.php';

/**
 * Summon REST API Interface
 *
 * @version     $Revision$
 * @author      Andrew S. Nagy <asnagy@gmail.com>
 * @access      public
 */
class Summon {
    /**
     * A boolean value determining whether to print debug information
     * @var bool
     */
    public $debug = false;

    /**
     * The HTTP_Request object used for API transactions
     * @var object HTTP_Request
     */
    public $client;
    
    /**
     * The HTTP_Request object used for API transactions
     * @var object HTTP_Request
     */
    public $host;

    /**
     * The secret Key used for authentication
     * @var string
     */
    public $apiKey;

    /**
     * The Client ID used for authentication
     * @var string
     */
    public $apiId;

    /**
     * The session for the current transaction
     * @var string
     */
    public $sessionId;

    /**
     * Constructor
     *
     * Sets up the Summon API Client
     *
     * @access  public
     */     
    function __construct($apiId, $apiKey)
    {
        global $configArray;
        
        if ($configArray['System']['debug']) {
            $this->debug = true;
        }        
        
        $this->host = 'http://ryerson.summon.serialssolutions.com';
        $this->apiId = $apiId;
        $this->apiKey = $apiKey;
        $this->client = new HTTP_Request(null, array('useBrackets' => false));
    }

    /**
     * Retrieves a document specified by the ID.
     *
     * @param   string  $id         The document to retrieve from the Summon API
     * @access  public
     * @throws  object              PEAR Error
     * @return  string              The requested resource
     */
    function getRecord($id)
    {
        if ($this->debug) {
            echo "<pre>Get Record: $id</pre>\n";
        }

        // Query String Parameters
        $options = array('s.st' => "id,$id");
        $result = $this->call($options);
        if (PEAR::isError($result)) {
            PEAR::raiseError($result);
        }

        return $result;
    }

    /**
     * Execute a search.
     *
     * @param   string  $query      The search query
     * @param   array   $filter     The fields and values to filter results on
     * @param   string  $start      The record to start with
     * @param   string  $limit      The amount of records to return
     * @param   string  $sortBy     The value to be used by for sorting
     * @param   string  $facets     An array of facets to return.  Default list is used if null.
     * @access  public
     * @throws  object              PEAR Error
     * @return  array               An array of query results
     */
    function query($query, $filterList = null, $start = 1, $limit = 20, $sortBy = null, $facets = null)
    {
        if ($this->debug) {
            echo '<pre>Query: ';
            print_r($query);
            if (isset($filter)) {
                echo "\nFilterQuery: ";
                foreach ($filter as $filterItem) {
                    echo " $filterItem";
                }
            }
            echo "</pre>\n";
        }

        $options = array();
		

        // Query String Parameters

        // Define search query
        // if given as an array, merge into options.
        if (is_array($query)) {
            $options = array_merge($options, $query);
        } else if ($query != '') {
            $options['s.q'] = $query;
        }
		
		//$options['s.fvf'] = 's.fvf=';
		
		
        // Define filters to be applied
        if (isset($filter)) {
        }

        // Define which sorting to use
        if (isset($sort)) {
            $options['s.sort'] = $sortBy;
        }
        
        // Define Paging Parameters
        $options['s.pn'] = $start;
		$options['s.ps'] = $limit;
        

        // Define Visibility 
        if (!isset($options['s.ho']))
            $options['s.ho'] = 'true';

        $result = $this->call($options);
        if (PEAR::isError($result)) {
            PEAR::raiseError($result);
        }
		
		if ($this->debug) {
             echo "<pre>OPTIONS: ";
            print_r($options);
            echo "</pre>\n";
        }
       
        return $result;
    }

    /**
     * Submit REST Request
     *
     * @param   array       $params     An array of parameters for the request
     * @param   string      $service    The API Service to call
     * @param   string      $method     The HTTP Method to use
     * @param   bool        $raw        Whether to return raw XML or processed
     * @return  string                  The response from the Summon API
     * @access  private
     */	
    private function call($params = array(), $service = 'search', $method = 'POST', $raw = false)
    {
        $this->client->setURL($this->host . '/' . $service);
        //$this->client->setMethod($method);
        $this->client->setMethod('GET');

        // Build Query String
        $query = array();
        foreach ($params as $function => $value) {
            if(is_array($value)) {
                foreach ($value as $additional) {
                    $additional = urlencode($additional);
                    $query[] = "$function=$additional";
                }
            } else {
                $value = urlencode($value);
                $query[] = "$function=$value";
            }
        }
		
		$query[] = 's.fq=SourceType%3A%28%22Library+Catalog%22%29';
		$query[] = 's.fvf=Library%2CInternet+Resource%2Ct';
		
        asort($query);
        $queryString = implode('&', $query);
        $this->client->addRawQueryString($queryString);

        if ($this->debug) {
            echo "<pre>$method: ";
            print_r($this->host . "/$service?" . $queryString);
            echo "</pre>\n";
        }

        // Build Authorization Headers
        $headers = array('Accept' => 'application/json',
                         'x-summon-date' => date('D, d M Y H:i:s T'),
                         'Host' => 'ryerson.summon.serialssolutions.com');
        $data = implode($headers, "\n") . "\n/$service\n" . urldecode($queryString) . "\n";
        $hmacHash = $this->hmacsha1($this->apiKey, $data);
        foreach ($headers as $key => $value) {
            $this->client->addHeader($key, $value);
        }
        $this->client->addHeader('Authorization', "Summon $this->apiId;$hmacHash");
        if ($this->sessionId) {
            $this->client->addHeader('x-summon-session-id', $this->sessionId);
        }

        // Send Request
        // $result = $this->client->sendRequest($service . '?' . $url);
        $result = $this->client->sendRequest($service);
        if (!PEAR::isError($result)) {
            // return $this->_process($this->client->getResponseBody());
            return $this->client->getResponseBody();
        } else {
            return $result;
        }
    }
	
    function _process($result)
    {
        // Unpack JSON Data
        if ($result = json_decode($result, true)) {
            // Catch errors from Summon
            if (!$result) {
                PEAR::raiseError(new PEAR_Error('Unable to process query<br>Server Returned:' . $errorMsg));
            }
        } else {
            return null;
        }

        return $result;
    }

    function hmacsha1($key,$data)
    {
        $blocksize=64;
        $hashfunc='sha1';
        if (strlen($key)>$blocksize) {
            $key=pack('H*', $hashfunc($key));
        }
        $key=str_pad($key,$blocksize,chr(0x00));
        $ipad=str_repeat(chr(0x36),$blocksize);
        $opad=str_repeat(chr(0x5c),$blocksize);
        $hmac = pack(
                    'H*',$hashfunc(
                        ($key^$opad).pack(
                            'H*',$hashfunc(
                                ($key^$ipad).$data
                            )
                        )
                    )
                );
        return base64_encode($hmac);
    }
	
		
}

?>
