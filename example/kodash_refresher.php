<?php
/**
* Guild Wars 2 SDK based WvWvW Widget-Refresher for [Fort]
*
* @author Thomas Winter
*/

/* Require SDK */
require (dirname(__FILE__).'/vesu/SDK/Gw2/Gw2SDK.php');
require (dirname(__FILE__) .'/vesu/SDK/Gw2/Gw2Exception.php');

use \vesu\SDK\Gw2\Gw2SDK;
use \vesu\SDK\Gw2\TwitchException;

/* Initiate SDK with caching */
$gw2 = new Gw2SDK(dirname(__FILE__).'/cache');

/*
You can initiate without caching, but I really wouldn't recommend it:
$gw2 = new Gw2SDK();

Default cache times are set in Gw2SDK.php. 1 day for mostly static calls
and 15 minutes for more dynamic ones. You can pass custom cache times in
seconds if you wish, e.g.
$gw2->getMatches(300); // will only cache for 300 seconds.

See Gw2SDK.php for more information.
*/

$world_id = 2201;

/*
Grab all matches by a world ID
Set Cache to 0 forces refresh
Request this page manually or add a cronjob to your WebServer to a time 
shortly after new match-ups are announced (Friday evening)
*/
$matches = $gw2->getMatchByWorldId($world_id, 0);

?>
