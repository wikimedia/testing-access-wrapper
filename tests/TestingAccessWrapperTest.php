<?php
declare( strict_types = 1 );

namespace Wikimedia;

use DomainException;
use Error;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use TypeError;

/**
 * @covers \Wikimedia\TestingAccessWrapper
 */
class TestingAccessWrapperTest extends TestCase {
	protected WellProtectedClass $raw;
	protected TestingAccessWrapper $wrapped;
	protected TestingAccessWrapper $wrappedStatic;

	protected function setUp(): void {
		parent::setUp();

		$this->raw = new WellProtectedClass();
		$this->wrapped = TestingAccessWrapper::newFromObject( $this->raw );
		$this->wrappedStatic = TestingAccessWrapper::newFromClass( WellProtectedClass::class );
	}

	public function testConstructorException(): void {
		$this->expectException( TypeError::class );
		// @phan-suppress-next-line PhanTypeMismatchArgumentReal
		TestingAccessWrapper::newFromObject( WellProtectedClass::class );
	}

	public function testStaticConstructorException(): void {
		$this->expectException( TypeError::class );
		// @phan-suppress-next-line PhanTypeMismatchArgumentReal
		TestingAccessWrapper::newFromClass( new WellProtectedClass() );
	}

	public function testGetProperty(): void {
		$this->assertSame( 1, $this->wrapped->property );
		$this->assertSame( 42, $this->wrapped->privateProperty );
		$this->assertSame( 9000, $this->wrapped->privateParentProperty );
		$this->assertSame( 'sp', $this->wrapped->staticProperty );
		$this->assertSame( 'spp', $this->wrapped->staticPrivateProperty );
		$this->assertSame( 'sp', $this->wrappedStatic->staticProperty );
		$this->assertSame( 'spp', $this->wrappedStatic->staticPrivateProperty );
	}

	/** @dataProvider constantProvider */
	public function testConstant( string $expected, string $constName ): void {
		$this->assertSame(
			$expected,
			TestingAccessWrapper::constant( WellProtectedClass::class, $constName )
		);
	}

	public static function constantProvider(): iterable {
		return [
			[ 'constant', 'CONSTANT' ],
			[ 'private constant', 'PRIVATE_CONSTANT' ],
			[ 'parent constant', 'PARENT_CONSTANT' ],
		];
	}

	public function testConstantException(): void {
		$this->expectException( ReflectionException::class );
		TestingAccessWrapper::constant( WellProtectedClass::class, 'NONEXISTENT_CONTENT' );
	}

	public function testConstructException(): void {
		$this->expectException( Error::class );
		// @phan-suppress-next-line PhanAccessMethodProtected,PhanNoopNew
		new WellProtectedParentClass();
	}

	public function testConstruct(): void {
		$parent = TestingAccessWrapper::construct( WellProtectedParentClass::class );
		$this->assertInstanceOf( WellProtectedParentClass::class, $parent );
		$wrapped = TestingAccessWrapper::newFromObject( $parent );
		$this->assertSame( 9000, $wrapped->privateParentProperty );
	}

	public function testConstructArg(): void {
		$parent = TestingAccessWrapper::construct( WellProtectedParentClass::class, 1234 );
		$this->assertInstanceOf( WellProtectedParentClass::class, $parent );
		$wrapped = TestingAccessWrapper::newFromObject( $parent );
		$this->assertSame( 1234, $wrapped->privateParentProperty );
	}

	public function testGetException_nonStatic(): void {
		$this->expectException( DomainException::class );
		$this->wrappedStatic->property;
	}

	public function testGetException_missing(): void {
		$this->expectException( DomainException::class );
		$this->wrappedStatic->privateParentStaticProperty;
	}

	public function testSetProperty(): void {
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

	public function testSetException(): void {
		$this->expectException( DomainException::class );
		$this->wrappedStatic->property = 1;
	}

	public function testMissingPropertyException(): void {
		$this->expectException( ReflectionException::class );
		$this->wrapped->missingProperty = 1;
	}

	public function testCallMethod(): void {
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

	public function testCallMethodTwoArgs(): void {
		$this->assertSame( 'two', $this->wrapped->whatSecondArg( 'one', 'two' ) );
	}

	public function testCallMethodException(): void {
		$this->expectException( DomainException::class );
		$this->wrappedStatic->incrementPropertyValue();
	}

}
