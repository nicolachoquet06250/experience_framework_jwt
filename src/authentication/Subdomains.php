<?php

namespace MiladRahimi\Jwt\Authentication;


class Subdomains {
	private $sub_domains = [];
	private $domain;
	protected static $instance;

	private function __construct() {}

	public static function create() {
		if(is_null(self::$instance)) {
			self::$instance = new Subdomains();
		}
		return self::$instance;
	}

	public function set_domain($domain) {
		$domain = explode('.', $domain);
		if(count($domain) >= 3) {
			array_shift($domain);
		}
		$domain = implode('.', $domain);
		$domain = explode(':', $domain)[0];
		$this->domain = $domain;
		$this->sub_domains[$domain] = [];
		return $this;
	}

	public function set_sub_domain($sub_domain) {
		$this->sub_domains[$this->domain][] = $sub_domain;
	}

	public function get_sub_domains() {
		return $this->sub_domains[$this->domain];
	}

	public function get_main_domain() {
		return $this->domain;
	}

	public function get_curent_domain() {
		return $_SERVER['HTTP_HOST'];
	}
}