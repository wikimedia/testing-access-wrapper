<?php
declare( strict_types = 1 );

namespace Wikimedia;

class WellProtectedParentClass {

	/** @var int */
	private $privateParentProperty;

	/** @var int */
	private static $privateParentStaticProperty;

	private const PARENT_CONSTANT = 'parent constant';

	protected function __construct( int $parentProperty = 9000 ) {
		$this->privateParentProperty = $parentProperty;
	}

	private function incrementPrivateParentPropertyValue(): void {
		$this->privateParentProperty++;
	}

	/**
	 * @return int
	 */
	public function getPrivateParentProperty(): int {
		return $this->privateParentProperty;
	}
}
