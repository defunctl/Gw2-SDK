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

$world_id = 2201;
/*
if(!empty($_GET['world']))
  $world_id = filter_input(INPUT_GET, 'world', FILTER_SANITIZE_NUMBER_INT);
	
$lang = "de";
*/

$map_id = 0;
if(!empty($_GET['map']))
	$map_id = filter_input(INPUT_GET, 'map', FILTER_SANITIZE_NUMBER_INT);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Atlan Guild Wars 2 PHP Test for [Fort]</title>
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

			.event {
				width: 98%;
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
			
			<!-- EVENTS -->
			<div id="events" class="section">	
				<h1>Guild Wars 2 - Events</h1>
				<div>
					<?php 
						echo $gw2->parseWorldName($world_id, $lang);
					?>
				</div>
				<form action="">
					<label for="map">Show Events For:</label>
					<select name="map" id="map">
						<option value="">Select a Map</option>
						<?php
							/* Grab our maps and sort them in alphabetical order */
							$maps = $gw2->getMaps();
							foreach($maps as $map):
						?>
							<option value="<?php echo $map->id ?>"<?php if($map_id == $map->id):?>  selected = "selected"<?php endif; ?>><?php echo $map->name ?></option>
						<?php endforeach; ?>
					</select>

					<input type="submit" value="Submit">
				</form>
				
				<?php	
					if(!empty($_GET['map'])) {
						/* Grab the events by a world and map ID */
						$events = $gw2->getEventsByMapId($world_id, $map_id);
					} else {
						/* Grab all the events */
						$events = $gw2->getEvents();
					}
				?>

				<div class="event">
				<?php
					/* Parse events and fetch the state for each event */
					foreach($events as $event):
				?>	
						<div><?php echo $gw2->parseWorldName($event->world_id) ?>: <?php echo $gw2->parseMapName($event->map_id) ?>: <?php echo $gw2->parseEventName($event->event_id) ?>:
						<span><?php echo $event->state; ?></span></div>
						
				<?php endforeach; ?>			
				</div>
			</div>	

		</div>
		<!-- / MAIN -->
	</body>
</html>
