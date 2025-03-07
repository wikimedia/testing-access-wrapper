<?php

namespace Wikimedia;

use DomainException;
use Error;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * @covers \Wikimedia\TestingAccessWrapper
 */
class TestingAccessWrapperTest extends TestCase {
	/** @var WellProtectedClass */
	protected $raw;
	/** @var TestingAccessWrapper */
	protected $wrapped;
	/** @var TestingAccessWrapper */
	protected $wrappedStatic;

	protected function setUp(): void {
		parent::setUp();

		$this->raw = new WellProtectedClass();
		$this->wrapped = TestingAccessWrapper::newFromObject( $this->raw );
		$this->wrappedStatic = TestingAccessWrapper::newFromClass( WellProtectedClass::class );
	}

	public function testConstructorException() {
		$this->expectException( InvalidArgumentException::class );
		// @phan-suppress-next-line PhanTypeMismatchArgumentProbablyReal
		TestingAccessWrapper::newFromObject( WellProtectedClass::class );
	}

	public function testStaticConstructorException() {
		$this->expectException( InvalidArgumentException::class );
		// @phan-suppress-next-line PhanTypeMismatchArgumentProbablyReal
		TestingAccessWrapper::newFromClass( new WellProtectedClass() );
	}

	public function testGetProperty() {
		$this->assertSame( 1, $this->wrapped->property );
		$this->assertSame( 42, $this->wrapped->privateProperty );
		$this->assertSame( 9000, $this->wrapped->privateParentProperty );
		$this->assertSame( 'sp', $this->wrapped->staticProperty );
		$this->assertSame( 'spp', $this->wrapped->staticPrivateProperty );
		$this->assertSame( 'sp', $this->wrappedStatic->staticProperty );
		$this->assertSame( 'spp', $this->wrappedStatic->staticPrivateProperty );
	}

	/** @dataProvider constantProvider */
	public function testConstant( $expected, $constName ) {
		$this->assertSame(
			$expected,
			TestingAccessWrapper::constant( WellProtectedClass::class, $constName )
		);
	}

	public static function constantProvider() {
		return [
			[ 'constant', 'CONSTANT' ],
			[ 'private constant', 'PRIVATE_CONSTANT' ],
			[ 'parent constant', 'PARENT_CONSTANT' ],
		];
	}

	public function testConstantException() {
		$this->expectException( ReflectionException::class );
		TestingAccessWrapper::constant( WellProtectedClass::class, 'NONEXISTENT_CONTENT' );
	}

	public function testConstructException() {
		$this->expectException( Error::class );
		// @phan-suppress-next-line PhanAccessMethodProtected
		return new WellProtectedParentClass();
	}

	public function testConstruct() {
		$parent = TestingAccessWrapper::construct( WellProtectedParentClass::class );
		$this->assertInstanceOf( WellProtectedParentClass::class, $parent );
		$wrapped = TestingAccessWrapper::newFromObject( $parent );
		$this->assertSame( 9000, $wrapped->privateParentProperty );
	}

	public function testConstructArg() {
		$parent = TestingAccessWrapper::construct( WellProtectedParentClass::class, 1234 );
		$this->assertInstanceOf( WellProtectedParentClass::class, $parent );
		$wrapped = TestingAccessWrapper::newFromObject( $parent );
		$this->assertSame( 1234, $wrapped->privateParentProperty );
	}

	public function testGetException_nonStatic() {
		$this->expectException( DomainException::class );
		$this->wrappedStatic->property;
	}

	public function testGetException_missing() {
		$this->expectException( DomainException::class );
		$this->wrappedStatic->privateParentStaticProperty;
	}

	public function testSetProperty() {
		$this->wrapped->property = 10;
		$this->assertSame( 10, $this->wrapped->property );
		$this->assertSame( 10, $this->raw->getProperty() );

		$this->wrapped->privateProperty = 11;
		$this->assertSame( 11, $this->wrapped->privateProperty );
		$this->assertSame( 11, $this->raw->getPrivateProperty() );

		$this->wrapped->privateParentProperty = 12;
		$this->assertSame( 12, $this->wrapped->privateParentProperty );
		$this->assertSame( 12, $this->raw->getPrivateParentProperty() );

		$this->wrapped->staticProperty = 'x';
		$this->assertSame( 'x', $this->wrapped->staticProperty );
		$this->assertSame( 'x', $this->wrappedStatic->staticProperty );

		$this->wrapped->staticPrivateProperty = 'y';
		$this->assertSame( 'y', $this->wrapped->staticPrivateProperty );
		$this->assertSame( 'y', $this->wrappedStatic->staticPrivateProperty );

		$this->wrappedStatic->staticProperty = 'X';
		$this->assertSame( 'X', $this->wrapped->staticProperty );
		$this->assertSame( 'X', $this->wrappedStatic->staticProperty );

		$this->wrappedStatic->staticPrivateProperty = 'Y';
		$this->assertSame( 'Y', $this->wrapped->staticPrivateProperty );
		$this->assertSame( 'Y', $this->wrappedStatic->staticPrivateProperty );

		// don't rely on PHPUnit to restore static properties
		$this->wrapped->staticProperty = 'sp';
		$this->wrapped->staticPrivateProperty = 'spp';
	}

	public function testSetException() {
		$this->expectException( DomainException::class );
		$this->wrappedStatic->property = 1;
	}

	public function testMissingPropertyException() {
		$this->expectException( ReflectionException::class );
		$this->wrapped->missingProperty = 1;
	}

	public function testCallMethod() {
		$this->wrapped->incrementPropertyValue();
		$this->assertSame( 2, $this->wrapped->property );
		$this->assertSame( 2, $this->raw->getProperty() );

		$this->wrapped->incrementPrivatePropertyValue();
		$this->assertSame( 43, $this->wrapped->privateProperty );
		$this->assertSame( 43, $this->raw->getPrivateProperty() );

		$this->wrapped->incrementPrivateParentPropertyValue();
		$this->assertSame( 9001, $this->wrapped->privateParentProperty );
		$this->assertSame( 9001, $this->raw->getPrivateParentProperty() );

		$this->assertSame( 'sm', $this->wrapped->staticMethod() );
		$this->assertSame( 'spm', $this->wrapped->staticPrivateMethod() );
		$this->assertSame( 'sm', $this->wrappedStatic->staticMethod() );
		$this->assertSame( 'spm', $this->wrappedStatic->staticPrivateMethod() );
	}

	public function testCallMethodTwoArgs() {
		$this->assertSame( 'two', $this->wrapped->whatSecondArg( 'one', 'two' ) );
	}

	public function testCallMethodException() {
		$this->expectException( DomainException::class );
		$this->wrappedStatic->incrementPropertyValue();
	}

}
