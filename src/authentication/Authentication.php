<?php

namespace MiladRahimi\Jwt\Authentication;

use MiladRahimi\Jwt\Claims\JWTClaims;
use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Signer;
use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Verifier;
use MiladRahimi\Jwt\Cryptography\Keys\PrivateKey;
use MiladRahimi\Jwt\Cryptography\Keys\PublicKey;
use MiladRahimi\Jwt\JwtGenerator;
use MiladRahimi\Jwt\JwtParser;

class Authentication {
	protected $private_key_path = __DIR__.'/../../keys/private.pem';
	protected $public_key_path = __DIR__.'/../../keys/public.pem';
	protected $sub_domains;

	public function __construct() {
		$this->sub_domains = Subdomains::create();
	}

	public function get_token() {
		if($this->authenticated()) {
			return $_COOKIE['access_token'];
		}
		$claims = JWTClaims::create();
		if(!$claims->isEmpty()) {
			$privateKey = new PrivateKey($this->private_key_path);
			$signer = new RS256Signer($privateKey);
			$generator = new JwtGenerator($signer);
			return $generator->generate($claims->get());
		}
		return '';
	}

	public function get_refresh_token() {
		return $this->get_token();
	}

	/**
	 * @return array|array[]
	 */
	public function get_user() {
		$publicKey = new PublicKey($this->public_key_path);
		$verifier = new RS256Verifier($publicKey);
		$parser = new JwtParser($verifier);
		$claims = $parser->parse($this->get_token());
		return $claims;
	}

	public function authenticate() {
		setcookie('access_token', $this->get_token(), time() + 2592000, '/', '.'.$this->sub_domains->get_main_domain());
	}

	public function disconnect() {
		setcookie('access_token', '', 1, '/', '.'.$this->sub_domains->get_main_domain());
	}

	public function authenticated() {
		return isset($_COOKIE['access_token']);
	}

	/**
	 * @param $claim
	 * @param $value
	 * @return Authentication
	 * @throws \Exception
	 */
	public function addClaim($claim, $value) {
		JWTClaims::create()->addClaim($claim)->setClaimValue($claim, $value);
		return $this;
	}
}