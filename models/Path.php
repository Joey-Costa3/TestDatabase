<?php
/*
	A common class for obtaining paths to files.
*/
if(!class_exists('Path')){
	class Path {
		static public function externalRoot() {
			return 'http://13.58.9.106';
		}

		static public function base() {
			return '/var/www/html/';
		}
		static public function subDirectory() {
			return '';
		}
		static public function css() {
			return Path::base().'css/';
		}

		static public function js() {
			return Path::base().'js/';
		}

		static public function models() {
			return Path::base().'models/';
		}

		static public function partials() {
			return Path::base().'partials/';
		}

		static public function img() {
			return Path::base().'img/';
		}	
		
		static public function tests() {
			return Path::base().'tests/';
		}

		static public function vendor() {
			return Path::base().'vendor/';
		}

		static public function controllers(){
			return Path::base().'controllers/';
		}

		static public function dbSettings() {
			return Path::base() . 'nc-db-settings.php';
		}

		static public function uploads(){
			return Path::base() . 'uploads/';
		}

	}	
}

?>
