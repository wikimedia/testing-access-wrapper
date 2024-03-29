[![Latest Stable Version]](https://packagist.org/packages/wikimedia/testing-access-wrapper) [![License]](https://packagist.org/packages/wikimedia/testing-access-wrapper)

Wikimedia Testing Access Wrapper
================================

Testing Access Wrapper is a simple helper for writing unit tests which provides
convenient shortcuts for using reflection to access non-public properties/methods.

The code was originally part of MediaWiki. See composer.json for a list of authors.

Usage
-----

```php
use Wikimedia\TestingAccessWrapper;

class NonPublic {
	protected $prop;
	protected const CONSTANT = 4;
	protected function func() {}
	protected static function staticFunc() {}
}

class NonPublicCtor {
	protected function __construct() {}
}

$object = new NonPublic();
// or:
// $object = TestingAccessWrapper::construct( NonPublicCtor::class );

$wrapper = TestingAccessWrapper::newFromObject( $object );
$classWrapper = TestingAccessWrapper::newFromClass( NonPublic::class );

$wrapper->prop = 'foo';
$wrapper->func();
$classWrapper->staticFunc();

$value = TestingAccessWrapper::constant( NonPublic::class, 'CONSTANT' );
```

Running tests
-------------

    composer install
    composer test


---
[Latest Stable Version]: https://poser.pugx.org/wikimedia/testing-access-wrapper/v/stable.svg
[License]: https://poser.pugx.org/wikimedia/testing-access-wrapper/license.svg
