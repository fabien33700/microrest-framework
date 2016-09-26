<?php

	require_once("./autoloader.php");
	(new Psr4AutoloaderClass())
		->addNamespace("MicroRest", "./microrest")
		->register();
	
	use MicroRest\UriRouter,
		MicroRest\RouterException;
		
	/**
	 * Une application web simple qui utilise UriRouter.
	 */
	class MyWebApp {
		
		/**
		 * Instance du routeur
		 */
		private $router = null;
		
		/**
		 * Constructeur
		 */
		public function __construct() {
			$this->routeur = new UriRouter();
		}
		
		/**
		 * Point d'entrée de l'application.
		 */
		public function execute() {
			
			$this->router = new UriRouter();
			
			try {
				$this->router->addRoute("/", function($req, $params) {
					return "Bienvenue sur mon site Web !";
				});

				$this->router->addRoute("/bonjour/{prenom}", function($req, $params) {
					$prenom = $params['prenom'];
					return "Bonjour " . ucfirst($prenom) . "!";
				});
				
				$this->router->run();
				
			} catch (RouterException $e) {
				echo "Erreur ! " . $e->getMessage();
			}
		}
	}

