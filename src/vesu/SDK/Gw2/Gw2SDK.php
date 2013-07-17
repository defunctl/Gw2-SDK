<?php

namespace vesu\SDK\Gw2;

require(dirname(__FILE__).'/Gw2Cache.php');

/**
 * Guild Wars 2 SDK for PHP
 * 
 * PHP SDK for interacting with the Guild Wars 2 API
 * 
 * @author Justin Frydman
 * @license https://github.com/defunctl/Gw2-SDK/blob/master/LICENSE.md MIT
 * @version 0.3 beta
 * @extended with map functions / thomas winter / 0.2
 * @extended with event details, server language / thomas winter / 0.3
 */
class Gw2SDK
{

    /** @var string The default language to return results */
    public $lang = 'de';

    /** @var string The Guild Wars 2 API Version to call */
    public $api_version = 'v1';

	/** @var integer Set timeout default. */
    public $timeout = 30;

	/** @var integer Set connect timeout */
    public $connect_timeout = 30;

 	/** @var boolean Verify SSL Cert */
    public $ssl_verifypeer = false;

	/** @var integer Contains the last HTTP status code returned */
    public $http_code = 0;

	/** @var array Contains the last Server headers returned */
    public $http_header = array();

	/** @var array Contains the last HTTP headers returned */
    public $http_info = array();

	/** @var boolean Throw cURL errors */
    public $throw_curl_errors = true;

    /** @var Gw2Cache Will store the Gw2Cache object */
    protected $cache;

	/** @var string Set the useragent */
	private $useragent = 'vesu Gw2SDK 0.3 beta';

	
	/** 
	 *	Gw2API URL's 
	 */
	const URL_API = 'https://api.guildwars2.com/';
	const URL_EVENTS = 'events.json?world_id=%d';
	const URL_MAP_EVENTS = 'events.json?world_id=%d&map_id=%d';
	const URL_EVENT_NAMES = 'event_names.json?lang=%s';                  //Edit by TW
	const URL_EVENT_DETAILS = 'event_details.json?event_id=%d&lang=%s';  //Edit by TW 
	const URL_MAP_NAMES = 'map_names.json?lang=%s';                      //Edit by TW
	const URL_WORLD_NAMES = 'world_names.json?lang=%s';
	const URL_MATCHES = '/wvw/matches.json';
	const URL_MATCH_DETAILS = '/wvw/match_details.json?match_id=%s';
	const URL_OBJECTIVE_NAMES = '/wvw/objective_names.json?lang=%s';
	const URL_ITEMS = 'items.json';
	const URL_ITEM_DETAILS = 'item_details.json?item_id=%d&lang=%s';
	const URL_RECIPES = 'recipes.json';
	const URL_RECIPE_DETAILS = 'recipe_details.json?recipe_id=%d&lang=%s';

    /**
     * SDK constructor
     * @param   directory $cache_dir A writable directory on the server
     * @throws  \vesu\SDK\Gw2\Gw2Exception
     */
    public function __construct($cache_dir = false)
    {
        if (!in_array('curl', get_loaded_extensions())) {
            throw new Gw2Exception('cURL extension is not installed and is required');
        }

        // Set up the cache object
        if(!empty($cache_dir)) {
        	$this->cache = new Gw2Cache($cache_dir);
        }
    }	

    /**
     * Get Events
     * @param integer $world_id The world ID
     * @param seconds $cache How long to cache this result for
     */
    public function getEvents($world_id, $cache = 900)
    {
    	$data = $this->request(sprintf(self::URL_EVENTS, $world_id), $cache);
    	return $data->events;
    }
	
	/**
     * Get Events Details
     * @param integer $event_id The Event ID
	 * @param string $lang The language to return e.g. 'de'
     * @param seconds $cache How long to cache this result for
     */
    public function getEventDetails($event_id, $cache = 900)
    {
    	return $this->request(sprintf(self::URL_EVENT_DETAILS, $event_id, $lang), $cache);
    }

    /** 
     * Get Event by Map ID
	 * @param integer $world_id The world ID
	 * @param integer $map_id The map ID
     * @param seconds $cache How long to cache this result for
     */
    public function getEventsByMapId($world_id, $map_id, $cache = 900)
    {
   		$data = $this->request(sprintf(self::URL_MAP_EVENTS, $world_id, $map_id), $cache);
		return $data->events;
		
    }	
	
    /**
     * Parse Event Name
     * @param integer $event_id The Event ID
	 * @param string $lang The language to return e.g. 'de'
     * @param seconds $cache How long to cache this result for
     */
    public function parseEventName($event_id, $lang = null, $cache = 86400)
    {
		if(!$lang)
   			$lang = $this->lang;
			
    	$events = $this->request(sprintf(self::URL_EVENT_NAMES, $lang), $cache);

    	foreach($events as $event) {
    		if($event->id == $event_id)
    			return $event->name;
    	}
    }
	
    /**
     * Get Matches
     * @param seconds $cache How long to cache this result for
     */
    public function getMatches($cache = 900)
    {
    	$data = $this->request(self::URL_MATCHES, $cache);
    	return $data->wvw_matches;
    }

    /**
     * Get Match Details
     * @param string $match_id The Match ID
     * @param seconds $cache How long to cache this result for
     */
    public function getMatchDetails($match_id, $cache = 900)
    {
    	return $this->request(sprintf(self::URL_MATCH_DETAILS, $match_id), $cache);
    }

    /** 
     * Get Match by Match ID
     * @param string $match_id The Match ID
     * @param seconds $cache How long to cache this result for
     */
    public function getMatchByMatchId($match_id, $cache = 900)
    {
    	$matches = $this->getMatches($cache);

    	$found = array();
   		foreach($matches->wvw_matches as $match) {
   			if($match->wvw_match_id == $match_id) {
				$found[] = $match;
			}
   		}

   		return $found;  	
    }

    /** 
     * Get Match by World ID
     * @param integer $world_id The World ID
     * @param seconds $cache How long to cache this result for
     * @return stdClass
     */
   	public function getMatchByWorldId($world_id, $cache = 86400)
   	{
   		$matches = $this->getMatches($cache);

   		$found = array();
   		foreach($matches as $match) {
   			if($match->red_world_id == $world_id || $match->blue_world_id == $world_id || $match->green_world_id == $world_id) {
				$found[] = $match;
			}
   		}

   		return (object) $found;
   	}

   	/**
   	 * Get Scores by Match ID
   	 * @param string $match_id The Match ID
   	 * @param seconds $cache How long to cache this result for
   	 * @return array
   	 */
   	public function getScoresByMatchId($match_id, $cache = 900)
   	{
   		$data = $this->getMatchDetails($match_id, $cache);
		return $data->scores;
   	}
   	
   	/**
   	 * Get Income by Match ID
   	 * @param string $match_id The Match ID
   	 * @return ?
   	 */
   	public function getIncomeByMatchId($match_id)
   	{
   		// this is a nightmare right now with objectives not having values in the API yet
   	}

   	/**
   	 * Get Objectives
   	 * @param string $lang The language to return e.g. 'de'
   	 * @param seconds $cache How long to cache this result for
   	 * @return stdClass
   	 */
   	public function getObjectives($lang = null, $cache = 86400)
   	{
   		if(!$lang)
   			$lang = $this->lang;

   		return $this->request(sprintf(self::URL_OBJECTIVE_NAMES, $lang), $cache);
   	}

   	/**
   	 * Parse Objective Name
   	 * @param integer $objective_id The Objective ID
   	 * @param string $lang The language to return e.g. 'de'
   	 * @param seconds $cache How long to cache this result for
   	 */
   	public function parseObjectiveName($objective_id, $lang = null, $cache = 86400)
   	{
   		if(!$lang)
   			$lang = $this->lang;

   		$objectives = $this->getObjectives($lang, $cache);

   		foreach($objectives as $objective) {
   			if($objective->id == $objective_id)
   				return $objective->name;
   		}
   	}

   	/**
   	 * Get Worlds
   	 * @param bool $sort Whether to sort the world list alphabetically
   	 * @param string $lang The language to return e.g. 'de'
   	 * @param seconds $cache How long to cache this result for
   	 */
   	public function getWorlds($sort = true, $lang = null, $cache = 86400)
   	{
   		if(!$lang)
   			$lang = $this->lang;

   		if($sort === true) {
   			$worlds = $this->request(sprintf(self::URL_WORLD_NAMES, $lang), $cache);
   			usort($worlds, array($this, 'compareServerByName'));
   			return $worlds;
   		} else {
   			return $this->request(sprintf(self::URL_WORLD_NAMES, $lang), $cache);
   		}
   		
   	}

   	/**
   	 * Parse a Worlds's Name by ID
   	 * @param integer $server_id The Server ID
   	 * @param string $lang The language to return e.g. 'de'
   	 * @param seconds $cache How long to cache this result for
   	 */
   	public function parseWorldName($world_id, $lang = null, $cache = 86400)
   	{
   		if(!$lang)
   			$lang = $this->lang;

		$worlds = $this->getWorlds(false, $lang, $cache);

		foreach($worlds as $world) {
			if($world->id == $world_id)
				return $world->name;
		}
   	}
	
	/**
   	 * Parse a Worlds's Language by ID                                                     //add by TW
   	 * @param integer $server_id The Server ID
   	 */
   	public function parseWorldLanguage($world_id)
   	{
	    if($world_id > 1000 && $world_id < 2000) {
		    return "us";
		} else if($world_id > 2000 && $world_id < 2100) {
		    return "eu";
		} else if($world_id > 2100 && $world_id < 2200) {
		    return "fr";
		} else if($world_id > 2200 && $world_id < 2300) {
		    return "de";
		} else if($world_id > 2300 && $world_id < 2400) {
			return "es";
		} else {
		    return "xx";
		}
   	}
	
	/**
   	 * Get Maps                                                                            //add by TW
   	 * @param bool $sort Whether to sort the map list alphabetically
   	 * @param string $lang The language to return e.g. 'de'
   	 * @param seconds $cache How long to cache this result for
   	 */
   	public function getMaps($sort = true, $lang = null, $cache = 86400)
   	{
   		if(!$lang)
   			$lang = $this->lang;

   		if($sort === true) {
   			$maps = $this->request(sprintf(self::URL_MAP_NAMES, $lang), $cache);
   			usort($maps, array($this, 'compareServerByName'));
   			return $maps;
   		} else {
   			return $this->request(sprintf(self::URL_MAP_NAMES, $lang), $cache);
   		}
   		
   	}

   	/**
   	 * Parse a Maps's Name by ID                                                           //add by TW
   	 * @param integer $server_id The Server ID
   	 * @param string $lang The language to return e.g. 'de'
   	 * @param seconds $cache How long to cache this result for
   	 */
   	public function parseMapName($map_id, $lang = null, $cache = 86400)
   	{
   		if(!$lang)
   			$lang = $this->lang;

		$maps = $this->getMaps(false, $lang, $cache);

		foreach($maps as $map) {
			if($map->id == $map_id)
				return $map->name;
		}
   	}

   	/** 
   	 * Get Items
   	 * @return stdClass
   	 */
   	public function getItems()
   	{
   		$data = $this->request(self::URL_ITEMS);
   		return $data->items;
   	}

   	/**
   	 * Get Item Details
   	 * @param integer $item_id The Item ID
   	 * @param string $lang The language to return e.g. 'de'
   	 * @param seconds $cache How long to cache this result for
   	 * @return stdClass
   	 */
   	public function getItemDetails($item_id, $lang = null, $cache = 86400)
   	{
   		if(!$lang)
   			$lang = $this->lang;

   		return $this->request(sprintf(self::URL_ITEM_DETAILS, $item_id, $lang), $cache);   		
   	}

   	/**
   	 * Parse Item Name
   	 * @param integer $item_id The Item ID
   	 * @param string $lang The language to return e.g. 'de'
   	 * @param seconds $cache How long to cache this result for
   	 */
   	public function parseItemName($item_id, $lang = null, $cache = 86400)
   	{
   		if(!$lang)
   			$lang = $this->lang;

   		$data = $this->getItemDetails($item_id, $lang, $cache);
   		return $data->name;
   	}

   	/** 
   	 * Get Recipes
   	 * @param seconds $cache How long to cache this result for
   	 */
   	public function getRecipes($cache = 86400) 
   	{
   		return $this->request(self::URL_RECIPES, $cache);
   	}

   	/**
   	 * Get Recipe Details
   	 * @param integer $recipe_id The Recipe ID
   	 * @param string $lang The language to return e.g. 'de'
   	 * @param seconds $cache How long to cache this result for
   	 */
   	public function getRecipeDetails($recipe_id, $lang = null, $cache = 86400)
   	{
   		if(!$lang)
   			$lang = $this->lang;

   		return $this->request(sprintf(self::URL_RECIPE_DETAILS, $recipe_id, $lang), $cache);
   	}


 	/**
     * Gw2API request
     * @param   string $uri The URI portion of the API URL
     * @param 	integer $cache The amount of time to cache a
     * @param   string $method GET or POST (post is untested at the moment)
     * @param   string $postfields Optional postfields (untested)
     * @param 	bool $assoc return as an associated array rather than an object
     * @return  mixed
     * @throws  \vesu\SDK\Gw2\Gw2Exception
     */
    private function request($uri, $cache = 900, $method = 'GET', $postfields = null, $assoc = false)
    {
    	$request_url = self::URL_API . $this->api_version . '/' . $uri;   

    	// Check for a cached version first
    	if(is_object($this->cache)) {
    		if(($json = $this->cache->get($request_url, $cache)) !== false) {
				return json_decode($json, $assoc); 
			}
    	}
    	
        $this->http_info = array();

        $crl = curl_init();
        curl_setopt($crl, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        curl_setopt($crl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($crl, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($crl, CURLOPT_HEADER, false);

        switch ($method) {
            case 'POST':
                curl_setopt($crl, CURLOPT_POST, true);
                if (!is_null($postfields)) {
                    curl_setopt($crl, CURLOPT_POSTFIELDS, ltrim($postfields, '?'));
                }
                break;
            case 'DELETE':
                curl_setopt($crl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!is_null($postfields)) {
                    $request_url = self::URL_API . $this->api_version . '/' . $uri . $postfields;
                }
        }

        curl_setopt($crl, CURLOPT_URL, $request_url);

        $response = curl_exec($crl);

        $this->http_code = curl_getinfo($crl, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($crl));

        if (curl_errno($crl) && $this->throw_curl_errors === true) {
            throw new Gw2Exception(curl_error($crl), curl_errno($crl));
        }

        curl_close($crl);

        // write cache, if available
        if(is_object($this->cache)) {
        	$this->cache->set($request_url, $response);
        }
        
        return json_decode($response, $assoc);
    } 

	/**
     * Get the header info to store
     */
    private function getHeader($ch, $header)
    {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }

        return strlen($header);
    } 

    /**
	 * Sorts an array with usort
	 * @param object $a
	 * @param object $b
	 * @return array
	 */
	private function compareServerByName($a, $b) {
		return strcmp($a->name, $b->name);
	}    

}
