<?php
namespace MiladRahimi\Jwt\Claims;

use Exception;

class JWTClaims {
	protected $claims = [];
	protected static $instance;

	private function __construct() {}

	public static function create() {
		if(is_null(self::$instance)) {
			self::$instance = new JWTClaims();
		}
		return self::$instance;
	}

	/**
	 * @param string $claim
	 * @return $this
	 */
	public function add(string $claim) {
		$this->claims[$claim] = true;
		return $this;
	}

	/**
	 * @param string $claim
	 * @param mixed $value
	 * @return JWTClaims
	 * @throws Exception
	 */
	public function set(string $claim, $value) {
		if(!isset($this->claims[$claim]))
			throw new Exception('Claim `'.$claim.'` not found');
		$this->claims[$claim] = $value;
		return $this;
	}

	public function isEmpty() {
		return empty($this->claims);
	}

	public function get() {
		return $this->claims;
	}
}