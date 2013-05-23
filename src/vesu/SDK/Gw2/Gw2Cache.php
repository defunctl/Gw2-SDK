<?php

namespace vesu\SDK\Gw2;

/**
 * Guild Wars 2 Api cache
 * 
 * Provides a simple filesystem cache for temporarily store single requests
 * to the api.
 * 
 * @package Core
 * @author  Oliver Schwarz <oliver.schwarz@gmail.com>
 * @author Justin Frydman (modified) 
 */

/**
 * Guild Wars 2 Api response cache
 * 
 * A very simple filesystem cache based on the requested api resource and the
 * URL parameters. Allows different lifetime of singular caches, so that you
 * may decide which response you want to store for a longer time than others.
 * Uses the resource and the requested api URL to identify single caches.
 * Cached data is stored in JSON format as given by the official api.
 * 
 * @package Core
 * @author  Oliver Schwarz <oliver.schwarz@gmail.com>
 * 
 * @todo Cache should extend a base version or be built upon an interface.
 */
class Gw2Cache
{

    /**
     * Cache directory
     * 
     * Directory where the cache files are stored. Must chmod to 777.
     * 
     * @var string
     */
    protected $cachedir;

    /**
     * Constructor
     * 
     * Requires the cache directory as input. The cache directory needs to be
     * chmod to 777 to be able to read and write JSON-containing files. Removes
     * the trailing slash automagically.
     * 
     * @param string $cachedir Directory to read and write cache
     * 
     * @return void
     */
    public function __construct($cachedir)
    {
        $this->cachedir = rtrim($cachedir, '/');
    }

    /**
     * Set cache content
     * 
     * Writes the content into a cache file in the cache directory. Requires
     * the request URL to uniquely identify the resource.
     * 
     * @param string $request_url Requested URL where the response came from
     * @param string $data        Data to write to cache, usually JSON response
     * 
     * @return void
     * 
     * @todo Should contain some sort of exception handling if cache can't be written
     */
    public function set($request_url, $data)
    {
        $filename = sprintf('%s/%s.json',
            $this->cachedir,
            md5($request_url));
            
            if(!file_put_contents($filename, $data)) {
                throw new Gw2Exception('Could now write file: '.$filename.'. Please ensure this directory is writable by the webserver!');
            }

            return true;
    }

    /**
     * Get cached data
     * 
     * Tries to fetch data from the cache if a) the cache file exists and b)
     * the lifetime of the cache is still valid. Returns false otherwise.
     * 
     * @param string  $request_url Request URL to identify the cache file
     * @param integer $lifetime    Allowed cache lifetime in seconds [optional, default 1 hour]
     * 
     * @return mixed Cached JSON data or false
     */
    public function get($request_url, $lifetime = 3600)
    {
        $filename = sprintf('%s/%s.json',
            $this->cachedir,
            md5($request_url));

        // Check whether file exists for this resource
        if (!file_exists($filename)) {
            return false;
        }

        // Check whether lifetime is still valid
        if ((time() - $lifetime) > filemtime($filename)) {
            return false;
        }
        
        // Checks are both valid: Return data
        return file_get_contents($filename);
        
    }
    
}