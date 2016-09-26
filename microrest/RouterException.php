<?php

	namespace MicroRest;

	/**
	 * Classe représentant une erreur générique du routeur d'URI.
	 */
	class RouterException extends \Exception {
		
		public function __construct($message) {
			parent::__construct($message);
		}
	}
