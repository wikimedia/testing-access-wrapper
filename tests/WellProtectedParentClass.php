<?php

namespace Wikimedia;

class WellProtectedParentClass {

	/** @var int */
	private $privateParentProperty;

	private const PARENT_CONSTANT = 'parent constant';

	public function __construct() {
		$this->privateParentProperty = 9000;
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
