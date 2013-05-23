# Guild Wars 2 SDK for PHP

> This is an unofficial [Gw2 SDK for PHP](https://github.com/defunctl/Gw2-SDK).
The SDK is currently under development, so functions, readme and/or examples will
change. As Arenanet provides more options, I'll try to add them in. Feel free to 
fork and help out!

## Requirements

Gw2 SDK for PHP requires PHP 5.3.0 or later with cURL.

## Installation

```php
require '/path/to/libs/vesu/SDK/Gw2/Gw2SDK.php';
require '/path/to/libs/vesu/SDK/Gw2/Gw2Exception.php';

use \vesu\SDK\Gw2\Gw2SDK;
use \vesu\SDK\Gw2\Gw2Exception;

$gw2 = new Gw2SDK;
...
```

### Usage

#### Basic usage (public functions only)

```php
$gw2 = new Gw2SDK(dirname(__FILE__).'/cache'); // path to a webserver writable folder
$matches = $gw2->getMatches();
print_r($matches);
...
```

#### More Examples

An example folder was included so you can test this out on your webserver.

### About Caching

Use a caching directory as it will greatly improve peformance. Original Cache class was written by [Oliver Schwarz](https://github.com/oliverschwarz/). Vielen Dank!

## Licenses

Refer to the LICENSE.md file for license information

## Reference

[Gw2 SDK](https://github.com/defunctl/Gw2-SDK), 
[Guild Wars 2](http://www.guildwars2.com/), 
[Guild Wars 2 API](https://forum-en.guildwars2.com/forum/community/api),
