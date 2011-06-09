<?php
	
	/**
	 * @package libs
	 */
	
	require_once TOOLKIT . '/class.textformatter.php';
	
	class Formatter extends TextFormatter {
		/**
		 * Does a text formatter exist?
		 * @param string $handle The handle of the text formatter.
		 * @access public
		 * @static
		 */
		static public function exists($handle) {
			foreach (new FormatterIterator() as $formatter) {
				if ($formatter->handle == $handle) return true;
			}

			return false;
		}
		
		/**
		 * Load a text formatter.
		 * @param string $path The full path to the text formatter.
		 * @access public
		 * @static
		 */
		static public function load($path) {
			if (file_exists($path)) {
				$handle = self::findHandleFromPath($path);
				$class = self::findClassNameFromPath($path);
			}
			
			else {
				$handle = $path;
				$path = self::findPathFromHandle($path);
				$class = self::findClassNameFromPath($path);
			}
			
			foreach (new FormatterIterator() as $formatter) {
				if ($formatter->handle == $handle) {
					return $formatter;
				}
			}
		}
		
		/**
		 * Extract the class name from a path.
		 * @param string $path A valid test case path.
		 * @access public
		 * @static
		 */
		static public function findClassNameFromPath($path) {
			$handle = self::findHandleFromPath($path);
			$class = ucwords(str_replace('-', ' ', Lang::createHandle($handle)));
			$class = 'Formatter' . str_replace(' ', null, $class);

			return $class;
		}
		
		/**
		 * Extract the handle from a path.
		 * @param string $path A valid test case path.
		 * @access public
		 * @static
		 */
		static public function findHandleFromPath($path) {
			return preg_replace('%^formatter\.|\.php$%', null, basename($path));
		}
		
		/**
		 * Find the first test case that has the supplied handle.
		 * @param string $path A valid test case handle.
		 * @access public
		 * @static
		 */
		static public function findPathFromHandle($handle) {
			foreach (new FormatterIterator() as $formatter) {
				if ($formatter->handle == $handle) return $formatter->path;
			}

			return null;
		}
		
		/**
		 * Override the default constructor so we don't need to set _Parent.
		 */
		public function __construct() {
			
		}
		
		public function about() {
			
		}
		
		/**
		 * Append the formatter interface to a page.
		 *
		 * @param Field $field
		 * @param XMLElement $element
		 */
		public function displayPublishPanel(Field $field, XMLElement $element) {
			
		}
		
		/**
		 * Append the formatter interface to a page.
		 *
		 * @param XMLElement $element
		 */
		public function displaySettingsPanel(XMLElement $wrapper) {
			
		}
		
		/**
		 * Given an input, apply the formatter and return the result.
		 *
		 * @param string $source
		 * @return string
		 */
		public function run($source) {
			
		}
		
		/**
		 * Validate the template and populate the error object.
		 */
		public function validate() {
			$this->errors = new StdClass();
			$valid = true;
			
			if (!isset($this->data->name) || trim($this->data->name) == '') {
				$this->errors->name = __('Name must not be empty.');
				$valid = false;
			}
			
			if (!isset($this->data->subject) || trim($this->data->subject) == '') {
				$this->errors->subject = __('Subject must not be empty.');
				$valid = false;
			}
			
			if (!isset($this->data->sender_name) || trim($this->data->sender_name) == '') {
				$this->errors->sender_name = __('Sender Name must not be empty.');
				$valid = false;
			}
			
			if (!isset($this->data->sender_address) || trim($this->data->sender_address) == '') {
				$this->errors->sender_address = __('Sender Address must not be empty.');
				$valid = false;
			}
			
			if (!isset($this->data->recipient_address) || trim($this->data->recipient_address) == '') {
				$this->errors->recipient_address = __('Recipient Address must not be empty.');
				$valid = false;
			}
			
			if (!isset($this->data->page_id) || trim($this->data->page_id) == '') {
				$this->errors->page_id = __('You must choose a template page.');
				$valid = false;
			}
			
			foreach ($this->overrides as $order => $override) {
				$valid = (
					$override->validate()
						? $valid
						: false
				);
			}
			
			return $valid;
		}
	}
	
?>