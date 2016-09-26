<?php

	namespace MicroRest;
	
	/**
	 * UriRouter
	 * Représente un routeur d'URI simple pour une application Web.
	 *
	 * @author Fabien LH.
	 */
	class UriRouter {
		
		/**
		 * Le tableau de routes enregistrées dans le routeur.
		 */
		private $routes = [];
		
		/**
		 * Constructeur de la classe
		 * @param $routes Un tableau de routes déjà rempli.
		 */
		public function __construct($routes = []) {
			error_reporting(E_ALL & ~E_NOTICE);
			
			if (!is_null($routes) && is_array($routes)) {
				$this->routes = $routes;
			}
		}
		
		/**
		 * Ajoute une route au routeur
		 * @param $pattern Le motif de correspondance de la route (i.e / , /accueil , ou encore /page/{noPage})
		 * @param $action Fonction anonyme à exécuter quand la route correspond à la requête.
		 * @throws RouterException Lève une exception si la route existe déjà ou qu'une erreur est survenue à sa création.
		 */		 
		public function addRoute($pattern, \Closure $action) {
			$route = new Route($pattern, $action);
			
			if (in_array($route, $this->routes)) 
				throw new RouterException("This route is already registered in the router.");
			
			if (is_null($route))
				throw new RouterException("The route to add is not valid.");
			
			$this->routes[] = $route;
		}
		
		/**
		 * Démarre le routeur.
		 * Celui-ci inspecte la requête HTTP et essaie de la faire correspondre avec une des routes du routeur.
		 * Exécute l'action appropriée.
		 * @throws RouterException Lève une exception si aucune route ne satisfait 
		 * la requête et qu'aucune action par défaut n'a été définie.
		 */
		public function run() {
			$request = UriRouter::getAbsoluteRequest();
			$match = false;
			$response = "";

			foreach($this->routes as $route) { 
				if (($route instanceof Route) && ($route->match($request))) {
					$match = true;
					$action = $route->getAction();
					
					ob_start();
					
					// Affiche le résultat de l'action à l'écran !
					// On utilise la temporisation de sortie pour s'assurer qu'aucune sortie n'a été effectuée dans
					// le corps de la fonction anonyme de la route.
					$response = $action($request, $route->getParameters());
					
					$buffer = ob_get_contents();
					ob_end_clean();
					
					if ($buffer !== "") 
						throw new RouterException("You must not print on output buffer in route's action. echo() and print() are forbidden.");
				}	
			}
			
			
			
			if (!$match) 
				throw new RouterException("There is no route which matches with the request. : $request");
			
			header('Content-Type: text/html; charset=utf-8');
			echo $response;
		}
		
		/**
		 * Méthode statique qui renvoie la requête HTTP absolue, càd. prête à être utilisée.
		 * (i.e '/', '/accueil', '/hello/world', etc.)
		 * @static
		 */
		private static function getAbsoluteRequest() {
			$requestUri = $_SERVER["REQUEST_URI"];

			return self::stripGetParameters(substr($requestUri, strlen(self::extractScriptFilePath())));
		}
		
		/**
		 * Méthode statique qui renvoie le chemin du script.
		 * @static
		 */
		private static function extractScriptFilePath() {
			$path = $_SERVER['PHP_SELF'];
			return substr($path, 0, strrpos($path, "/"));
		}
		
		/**
		 * Méthode statique qui retire les paramètres encodés dans les requête de type GET.
		 * @static
		 */
		private static function stripGetParameters($request) {
			$position = ((strpos($request, "?") !== false) ? 
				strpos($request, "?") : 
				strlen($request)) ;

			return substr($request, 0, $position);
		}
	}