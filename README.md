# twig-scanner

twig-scanner statically analyzes the given code and extracts macros components and attributes usage.


## Installation

```
composer require kur4tor/twig-scanner
```

## Usage

```php
bin/twig-scanner --dir=./templates --output=result.json
```
or 
```php
$dir = __DIR__ . '/templates';

$scanner = new \Kur4tor\TwigScanner\Scanner();
$scanner->scan($dir);
```

Output in result.json:

```json
{
  "macroFunction": [
    {
      "instances": 2,
      "attributes": {
        "attribute1": 2,
        "attribute2": 1
      }
    }
  ]
}
```