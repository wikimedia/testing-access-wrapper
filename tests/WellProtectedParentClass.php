<?php

namespace Wikimedia;

class WellProtectedParentClass {
	private $privateParentProperty;

	public function __construct() {
		$this->privateParentProperty = 9000;
	}

	private function incrementPrivateParentPropertyValue() {
		$this->privateParentProperty++;
	}

	public function getPrivateParentProperty() {
		return $this->privateParentProperty;
	}
}

