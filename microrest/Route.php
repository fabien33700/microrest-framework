<?php

	namespace MicroRest;

	/**
	 * Classe qui représente une route.
	 *
	 * @author Fabien LH.
	 */
	class Route {
		
		private static $regexParamsMatch = "/\\{([\\w\\d\\_]+)\\}/";	
				
		/**
		 * L'action de la route sous forme de fonction anonymes (Closure)
		 */
		private $action = null;
		
		/**
		 * Le motif de correspondance de la route.
		 */
		private $pattern = "";
		
		/**
		 * Les paramètres récupérés dans la requête.
		 */
		private $params = [];
		
		/**
		 * Constructeur de la classe Route
		 * @param $pattern Le motif de correspondance de la route
		 * @param $action La fonction anonyme qui exécute l'action de la route
		 */
		public function __construct($pattern, \Closure $action) {
			$this->action = $action;
			$this->pattern = $pattern;
		}
		
		/**
		 * Accesseurs
		 */
		public function getAction() {
			return $this->action;
		}
		
		public function getPattern() {
			return $this->pattern;
		}
		
		public function getParameters() {
			return $this->params;
		}
		
		/**
		 * Appelée par la classe UriRouter pour demander à la route si elle correspond
		 * à la requête HTTP reçu
		 */		 
		public function match($request) {	
			
			// Si la requete est différente du motif
			if ($request !== $this->pattern) {
			
				// On découpe simultanément requête et motif, et on supprime le premier élément vide.
				$requestParts = explode("/", $request);			array_shift($requestParts);
				$patternParts = explode("/", $this->pattern);	array_shift($patternParts);
				
				// Si on a pas le même nombre de parties dans les URI, pas de correspondance
				if (count($patternParts) != count($requestParts)) 
					return false;
					
				// Pour chaque parties des URI	
				for ($i = 0; $i < count($patternParts); $i++) {
					// S'agit-il d'un paramètre (i.e {prenom} )
					$isParam = (preg_match(self::$regexParamsMatch, $patternParts[$i], $matches) === 1);
					
					// Si oui
					if ($isParam)  {
						// On remplit le tableau de paramètre
						$paramName = $matches[1];
						$this->params[$paramName] = $requestParts[$i];	
					} else {
						// Si ce n'est pas un paramètre, il faut que les deux parties soient identiques
						// pour qu'il y ait correspondance.
						if ($requestParts[$i] !== $patternParts[$i])
							return false;
					}
				}
			}
			
			// Si identique, on signale la correspondance directement
			return true;
		}	
		
	}
