<?php
namespace MiladRahimi\Jwt\Claims;

use core\External_confs;
use Exception;
use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Signer;
use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Verifier;
use MiladRahimi\Jwt\Cryptography\Keys\PrivateKey;
use MiladRahimi\Jwt\Cryptography\Keys\PublicKey;
use MiladRahimi\Jwt\JwtGenerator;
use MiladRahimi\Jwt\JwtParser;

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
	 * @param $access_token
	 * @return array|array[]
	 * @throws Exception
	 */
	public function init_with_token($access_token) {
		$external_confs = External_confs::create();

		$publicKey = new PublicKey($external_confs->get_git_dependencies_dir().'/jwt/keys/public.pem');
		$verifier = new RS256Verifier($publicKey);
		$parser = new JwtParser($verifier);
		$this->claims = $parser->parse($access_token);

		return $this->claims;
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