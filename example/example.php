<?php
/**
 * Guild Wars 2 SDK Example
 * 
 * A small example of what you can do with the Gw2 PHP SDK
 * 
 * @author Justin Frydman
 */

/* Require SDK */
require (dirname(__FILE__).'/../vesu/SDK/Gw2/Gw2SDK.php');
require (dirname(__FILE__) .'/../vesu/SDK/Gw2/Gw2Exception.php');

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

$world_id = 0;
if(!empty($_GET['world']))
	$world_id = filter_input(INPUT_GET, 'world', FILTER_SANITIZE_NUMBER_INT);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Guild Wars 2 PHP SDK Example</title>
		<meta charset="UTF-8">

		<style type="text/css">
			body {
				color: #000;
				font-size: 13px;
				font-family: Arial, Verdana, sans-serif;
				background: #fff;
			}

			#main {
				width: 940px;
				padding: 10px;
				margin: 0 auto;
				background: #efefef;
				-webkit-border-radius: 8px;
				-moz-border-radius: 8px;
				border-radius: 8px;
			}

			#main:after {
				content: ".";
			    display: block;
			    height: 0;
			    clear: both;
			    visibility: hidden;
			}

			form {
				padding-bottom: 10px;
				margin-bottom: 10px;
				border-bottom: 1px solid #cccccc;
			}

			.match {
				width: 20%;
				float: left;
				padding: 0.5em;				
				border: 1px solid #cccccc;
				margin: 0 2em 2em 0;
				-webkit-border-radius: 8px;
				-moz-border-radius: 8px;
				border-radius: 8px;	
				background: #fff;		
			}

			.section {
				clear: both;
			}

			.server span {
				color: #000;
				font-weight: bold;
			}

			.server.red {
				color: #b80202;
			}
			
			.server.blue {
				color: #3d63d1;
			}

			.server.green {
				color: green;
			}

		</style>
	</head>
	<body>

		<!-- MAIN -->
		<div id="main">
			
			<!-- MATCHES -->
			<div id="matches" class="section">	
				<h1>Guild Wars 2 - World vs World</h1>
				<form action="">
					<label for="world">Show Match For:</label>
					<select name="world" id="world">
						<option value="">Select a Server</option>
						<?php
							/* Grab our wolrds and sort them in alphabetical order */
							$worlds = $gw2->getWorlds();
							foreach($worlds as $world):
						?>
							<option value="<?php echo $world->id ?>"<?php if($world_id == $world->id):?>  selected = "selected"<?php endif; ?>><?php echo $world->name ?></option>
						<?php endforeach; ?>
					</select>

					<input type="submit" value="Submit">
				</form>
				
				<?php	
					if(!empty($_GET['world'])) {
						/* Grab a match by a world ID */
						$matches = $gw2->getMatchByWorldId($world_id);
					} else {
						/* Grab all the matches */
						$matches = $gw2->getMatches();
					}
					
					/* Parse matches and fetch the scores for each match */
					foreach($matches as $match):
						// This grabs just the scores from the match details
						$scores = $gw2->getScoresByMatchId($match->wvw_match_id);		
				?>			
					<div class="match">					
						<div class="server red"><?php echo $gw2->parseWorldName($match->red_world_id) ?>: <span><?php echo number_format($scores[0]); ?></span></div>
						<div class="server blue"><?php echo $gw2->parseWorldName($match->blue_world_id) ?>: <span><?php echo number_format($scores[1]); ?></span></div>
						<div class="server green"><?php echo $gw2->parseWorldName($match->green_world_id) ?>: <span><?php echo number_format($scores[2]); ?></span></div>
					</div>									
				<?php endforeach; ?>			
			</div>	

		</div>
		<!-- / MAIN -->
	</body>
</html>