<?php

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

	private function incrementPrivateParentPropertyValue() {
		$this->privateParentProperty++;
	}

	/**
	 * @return int
	 */
	public function getPrivateParentProperty() {
		return $this->privateParentProperty;
	}
}
