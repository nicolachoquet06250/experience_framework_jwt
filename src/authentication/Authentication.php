<?php

namespace MiladRahimi\Jwt\Authentication;

use Exception;
use MiladRahimi\Jwt\Claims\JWTClaims;
use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Signer;
use MiladRahimi\Jwt\Cryptography\Algorithms\Rsa\RS256Verifier;
use MiladRahimi\Jwt\Cryptography\Keys\PrivateKey;
use MiladRahimi\Jwt\Cryptography\Keys\PublicKey;
use MiladRahimi\Jwt\Enums\PublicClaimNames;
use MiladRahimi\Jwt\Exceptions\InvalidKeyException;
use MiladRahimi\Jwt\JwtGenerator;
use MiladRahimi\Jwt\JwtParser;

class Authentication {
	protected $private_key_path = __DIR__.'/../../keys/private.pem';
	protected $public_key_path = __DIR__.'/../../keys/public.pem';
	protected $sub_domains;
	protected static $user;
	protected $claims;

	public function __construct() {
		$this->sub_domains = Subdomains::create();
		$this->claims = JWTClaims::create();
	}

	/**
	 * @param string $private
	 * @param string $public
	 * @return $this
	 */
	public function set_keys_path(string $private, string $public) {
		$this->private_key_path = $private;
		$this->public_key_path = $public;
		return $this;
	}

	/**
	 * @return mixed|string
	 * @throws \Exception
	 */
	public function get_token() {
		if($this->authenticated()) {
			return $_COOKIE['access_token'];
		}
		$claims = JWTClaims::create();
		if(!$claims->isEmpty()) {
			try {
				$privateKey = new PrivateKey($this->private_key_path);
				$signer = new RS256Signer($privateKey);
				$generator = new JwtGenerator($signer);
				return $generator->generate($claims->get());
			}
			catch (InvalidKeyException $e) {
				throw new Exception('Private key not valid');
			}
		}
		return '';
	}

	/**
	 * @return mixed|string
	 * @throws Exception
	 */
	public function get_refresh_token() {
		if($this->refresh_token_exists()) {
			return $_COOKIE['refresh_access_token'];
		}
		return $this->get_token();
	}

	/**
	 * @return array|array[]
	 * @throws Exception
	 */
	public function get_user() {
		return self::$user;
	}

	/**
	 * @return array|mixed
	 * @throws Exception
	 */
	public function get_user_id() {
		$publicKey = new PublicKey($this->public_key_path);
		$verifier = new RS256Verifier($publicKey);
		$parser = new JwtParser($verifier);
		$claims = $parser->parse($this->get_token());
		return $claims[PublicClaimNames::ID];
	}

	/**
	 * @param array $user
	 * @throws Exception
	 */
	public function set_user(array $user) {
		self::$user = $user;
		$this->addClaim(PublicClaimNames::ID, $user['id']);
	}

	/**
	 * @throws Exception
	 */
	public function authenticate() {
		$token = $this->get_token();
		setcookie('access_token', $token, time() + 2592000, '/', '.'.$this->sub_domains->get_main_domain());
		$_COOKIE['access_token'] = $token;
		return $this->authenticated();
	}

	/**
	 * @return bool
	 */
	public function disconnect() {
		setcookie('access_token', '', 1, '/', '.'.$this->sub_domains->get_main_domain());
		return !$this->authenticated();
	}

	/**
	 * @return bool
	 */
	public function authenticated() {
		return isset($_COOKIE['access_token']);
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	public function set_refresh_token() {
		$refresh_token = $this->get_refresh_token();
		setcookie('refresh_access_token', $refresh_token, time() + 2592000, '/', '.'.$this->sub_domains->get_main_domain());
		$_COOKIE['refresh_access_token'] = $refresh_token;
		return $this->refresh_token_exists();
	}

	/**
	 * @return bool
	 */
	public function delete_refresh_token() {
		unset($_COOKIE['refresh_access_token']);
		setcookie('refresh_access_token', '', 1, '/', '.'.$this->sub_domains->get_main_domain());
		return !$this->refresh_token_exists();
	}

	/**
	 * @return bool
	 */
	public function refresh_token_exists() {
		return isset($_COOKIE['refresh_access_token']);
	}

	/**
	 * @param string $claim
	 * @param mixed $value
	 * @return Authentication
	 * @throws \Exception
	 */
	public function addClaim(string $claim, $value) {
		$this->claims->add($claim)->set($claim, $value);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getClaims() {
		return $this->claims->get();
	}
}