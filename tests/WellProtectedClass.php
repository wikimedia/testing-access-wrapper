<?php

namespace Wikimedia;

class WellProtectedClass extends WellProtectedParentClass {
	protected static string $staticProperty = 'sp';
	private static string $staticPrivateProperty = 'spp';

	protected const CONSTANT = 'constant';
	private const PRIVATE_CONSTANT = 'private constant';

	protected int $property;
	private int $privateProperty;

	protected static function staticMethod(): string {
		return 'sm';
	}

	private static function staticPrivateMethod(): string {
		return 'spm';
	}

	public function __construct() {
		parent::__construct();
		$this->property = 1;
		$this->privateProperty = 42;
	}

	protected function incrementPropertyValue() {
		$this->property++;
	}

	private function incrementPrivatePropertyValue() {
		$this->privateProperty++;
	}

	public function getProperty(): int {
		return $this->property;
	}

	public function getPrivateProperty(): int {
		return $this->privateProperty;
	}

	protected function whatSecondArg( string $a, string $b ): string {
		return $b;
	}
}
