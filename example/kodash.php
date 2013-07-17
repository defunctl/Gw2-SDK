<?php
/**
* Guild Wars 2 SDK based WvWvW Widget for [Fort]
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

/* Grab a match by a world ID */
$matches = $gw2->getMatchByWorldId($world_id);

/* Parse matches and fetch the scores for each match */
foreach($matches as $match):
// This grabs just the scores from the match details
$scores = $gw2->getScoresByMatchId($match->wvw_match_id);
// This grabs the bracket from the match
$bracket = array_pop(explode("-",$match->wvw_match_id));
// This grabs the start time from the match
$start_date = implode(".",array_reverse(explode("-",array_shift(explode("T",$match->start_time)))));
// This grabs the end time from the match
$end_date = implode(".",array_reverse(explode("-",array_shift(explode("T",$match->end_time)))));
?>

<div style="font-size: 14px; width: 100%; float: left; padding: -1em; font-weight: bold;">
  <table border="0" frame="void" rules="none" align="center">
    <tbody>
      <tr>
        <td style="text-align: center;" colspan="3" >World vs World <?= $start_date ?> - <?= $end_date ?> <?= $bracket ?>. Braket</td>
      </tr>
      <tr>
        <td style="color: #FFFF99; font-weight: bold; text-align: center; background-image: url('/images/wvw/wvwgreen.png'); background-repeat: no-repeat; background-position:right; width: 300px; height: 30px;">
          <?php echo $gw2->parseWorldName($match->red_world_id) ?>:
  	  <span><?php echo number_format($scores[0]); ?></span>
		  <img src="/images/wvw/<?php echo $gw2->parseWorldLanguage($match->red_world_id) ?>.png" style="vertical-align: middle;" />
		  7
		  <img src="/images/wvw/down.png" style="vertical-align: middle;" />
		</td>
        <td style="color: #FFFF99; font-weight: bold; text-align: center; background-image: url('/images/wvw/wvwred.png'); background-repeat: no-repeat; background-position:center; width: 258px; height: 30px;">
          <?php echo $gw2->parseWorldName($match->blue_world_id) ?>:
		  <span><?php echo number_format($scores[1]); ?></span>
		  <img src="/images/wvw/<?php echo $gw2->parseWorldLanguage($match->blue_world_id) ?>.png" style="vertical-align: middle;" />
		  13
		  <img src="/images/wvw/down.png" style="vertical-align: middle;" />
		</td>
        <td style="color: #FFFF99; font-weight: bold; text-align: center; background-image: url('/images/wvw/wvwblue.png'); background-repeat: no-repeat; background-position:left; width: 300px; height: 30px;">
          <?php echo $gw2->parseWorldName($match->green_world_id) ?>:
		  <span><?php echo number_format($scores[2]); ?></span>
		  <img src="/images/wvw/<?php echo $gw2->parseWorldLanguage($match->green_world_id) ?>.png" style="vertical-align: middle;" />
		  15
		  <img src="/images/wvw/up.png" style="vertical-align: middle;" />
		</td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
    </tbody>
  </table>
</div>
<?php endforeach; ?>
