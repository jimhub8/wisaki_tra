<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('CWSInfo')) :
	class CWSInfo {
		public $settings;
		public $plugname = 'cloudways';
		public $brandname = 'Cloudways Migrate';
		public $badgeinfo = 'cwsbadge';
		public $ip_header_option = 'cwsipheader';
		public $brand_option = 'cwsbrand';
		public $version = '3.4';
		public $webpage = 'https://www.cloudways.com';
		public $appurl = 'https://migrate.blogvault.net';
		public $slug = 'bv-cloudways-automated-migration/cloudways.php';
		public $plug_redirect = 'cwsredirect';
		public $logo = '../img/hdr-logo-cw.png';

		public function __construct($settings) {
			$this->settings = $settings;
		}

		public function getBrandInfo() {
			return $this->settings->getOption($this->brand_option);
		}

		public function getBrandName() {
			$brand = $this->getBrandInfo();
			if ($brand && array_key_exists('menuname', $brand)) {
				return $brand['menuname'];
			}
			return $this->brandname;
		}

		public function getMonitTime() {
			$time = $this->settings->getOption('bvmonittime');
			return ($time ? $time : 0);
		}

		public function appUrl() {
			if (defined('BV_APP_URL')) {
				return BV_APP_URL;
			} else {
				$brand = $this->getBrandInfo();
				if ($brand && array_key_exists('appurl', $brand)) {
					return $brand['appurl'];
				}
				return $this->appurl;
			}
		}
		
		public function isActivePlugin() {
			$expiry_time = time() - (3 * 24 * 3600);
			return ($this->getMonitTime() > $expiry_time);
		}

		public function isProtectModuleEnabled() {
			return ($this->settings->getOption('bvptplug') === $this->plugname) &&
				$this->isActivePlugin();
		}

		public function isDynSyncModuleEnabled() {
			return ($this->settings->getOption('bvdynplug') === $this->plugname) &&
				$this->isActivePlugin();
		}
		public function isActivateRedirectSet() {
			return ($this->settings->getOption($this->plug_redirect) === 'yes') ? true : false;
		}

		public function isMalcare() {
			return $this->getBrandName() === 'MalCare - Pro';
		}

		public function isBlogvault() {
			return $this->getBrandName() === 'BlogVault';
		}

		public function respInfo() {
			return array(
				"bvversion" => $this->version,
				"sha1" => "true"
			);
		}
	}
endif;