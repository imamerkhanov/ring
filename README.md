Ring extension
==============

Это расширение делалось для теста методов консистентного хеширования


Installation
------------

Предпочтительный способ установить это расширение через [composer](http://getcomposer.org/download/).

В свой composer.json добовляем
```
"repositories": [{
    "type": "vcs",
    "url": "https://github.com/imamerkhanov/ring"
}]
```
и в секцию require-dev
```
"imamerkhanov/ring": "*",
```
Usage
-----

```php
$r = new StandardRing(['nodesCount'=>100]);
$r->getNodeId($i);
```