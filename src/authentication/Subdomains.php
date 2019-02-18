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
		$this->domain = $domain;
		$this->sub_domains[$domain] = [];
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
}