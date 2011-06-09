<?php
	
	/**
	 * @package libs
	 */
	
	/**
	 * Fetches all available text formatters.
	 */
	class FormatterIterator extends ArrayIterator {
		/**
		 * The pattern used to match text formatters.
		 */
		const GLOB_PATTERN = 'text-formatters/formatter.*.php';
		
		/**
		 * The pattern used to extract handles.
		 */
		const HANDLE_PATTERN = '/^formatter\.(?<handle>.*?)\.php$/';
		
		/**
		 * Cached list of test cases.
		 */
		static protected $cache;
		
		/**
		 * Finds all text formatters.
		 */
		public function __construct() {
			if (!isset(self::$cache)) {
				$paths = array(
					SYMPHONY . '/' . self::GLOB_PATTERN,
					WORKSPACE . '/' . self::GLOB_PATTERN
				);
				$files = $objects = array();
				
				foreach (Symphony::ExtensionManager()->listInstalledHandles() as $handle) {
					$paths[] = sprintf(
						'%s/%s/' . self::GLOB_PATTERN,
						EXTENSIONS, $handle
					);
				}
				
				foreach ($paths as $path) {
					$found = glob($path, GLOB_NOSORT);

					if (empty($found)) continue;

					$files = array_merge($files, $found);
				}
				
				foreach ($files as $file) {
					preg_match(self::HANDLE_PATTERN, basename($file), $match);
					
					$class = 'Formatter' . $match['handle'];
					
					require_once $file;
					
					/**
					 * @todo Remove this when $this->_Parent is obsoleted.
					 */
					$object = new $class(Symphony::Engine());
					
					if (!$object instanceof Formatter) continue;
					
					$object->handle = $match['handle'];
					$object->file = $file;
					$objects[] = $object;
				}
				
				usort($objects, function($a, $b) {
					$a = $a->about();
					$b = $b->about();
					
					return strcasecmp($a['name'], $b['name']);
				});
				
				self::$cache = $objects;
			}

			parent::__construct(self::$cache);
		}
	}
	
?>