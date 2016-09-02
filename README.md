Ring extension
==============

This expansion was done to test methods of greasy hash


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist imamerkhanov/ring "*"
```

or add

```json
"imamerkhanov/ring": "*"
```
and 
```
"repositories": [{
    "type": "vcs",
    "url": "https://github.com/imamerkhanov/ring"
}]
```
to the require section of your composer.json.


Usage
-----

```php
$r = new StandardRing(['nodesCount'=>100]);
$r->getNodeId($i);
```
Credits
-----

Author: Ilshat Amerkhanov

Email: imamerkhanov@bars-open.ru