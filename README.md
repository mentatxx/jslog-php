jslog-php
=========

Error logger for PHP

Logs all PHP errors, events and messages to the cloud so you can easily handle and proceed them.

### Installation 

#### Using Composer

```bash
composer require mentatxx/jslog-php
```

Register at [jslog.me](http://jslog.me), get an API key.

Add logging support to scripts

```php
require_once __DIR__.'/vendor/autoload.php';

use JsLog\Logger;

$jslog = new Logger(array('key' => "<MY-API-KEY>"));
$jslog->log('My test message');
```
