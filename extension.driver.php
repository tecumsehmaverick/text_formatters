<?php
	
	/**
	 * @package formatters
	 */
	
	require_once EXTENSIONS . '/text_formatters/lib/class.formatter.php';
	require_once EXTENSIONS . '/text_formatters/lib/class.iterator.php';
	require_once EXTENSIONS . '/text_formatters/lib/class.page.php';
	
	/**
	 * Provide a text formatter editor interface for Symphony.
	 */
	class Extension_Text_Formatters extends Extension {
		/**
		 * True when no navigation group has been specified.
		 */
		protected $missing_navigation_group;
		
		/**
		 * Extension information.
		 */
		public function about() {
			return array(
				'name'			=> 'Text Formatters',
				'version'		=> '0.1',
				'release-date'	=> '2011-05-05',
				'author'		=> array(
					array(
						'name'			=> 'Rowan Lewis',
						'website'		=> 'http://rowanlewis.com/',
						'email'			=> 'me@rowanlewis.com'
					)
				)
			);
		}
		
		/**
		 * Cleanup installation.
		 */
		public function uninstall() {
			Symphony::Configuration()->remove('formatters');
		}
		
		/**
		 * Create configuration.
		 */
		public function install() {
			Symphony::Configuration()->set('navigation_group', __('Blueprints'), 'formatters');
			Administration::instance()->saveConfig();
			
			return true;
		}
		
		/**
		 * Listen for these delegates.
		 */
		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'viewPreferences'
				),
				array(
					'page' => '/system/preferences/',
					'delegate' => 'Save',
					'callback' => 'actionsPreferences'
				),
				array(
					'page'		=> '/backend/',
					'delegate'	=> 'AppendTextFormatter',
					'callback'	=> 'appendTextFormatter'
				),
				array(
					'page'		=> '/backend/',
					'delegate'	=> 'ModifyTextareaFieldPublishWidget',
					'callback'	=> 'appendTextFormatter'
				),
				array(
					'page'		=> '/backend/',
					'delegate'	=> 'ModifyTextBoxFullFieldPublishWidget',
					'callback'	=> 'appendTextFormatter'
				)
			);
		}
		
		/**
		 * Add navigation items.
		 */
		public function fetchNavigation() {
			$group = $this->getNavigationGroup();
			
			return array(
				array(
					'location'	=> $group,
					'name'		=> __('Text Formatters'),
					'link'		=> '/index/'
				),
				array(
					'location'	=> $group,
					'name'		=> __('Edit'),
					'link'		=> '/edit/',
					'visible'	=> 'no'
				),
				array(
					'location'	=> $group,
					'name'		=> __('New'),
					'link'		=> '/new/',
					'visible'	=> 'no'
				)
			);
		}
		
		/**
		 * Listen for traditional delegates and use modern formatters.
		 */
		public function appendTextFormatter($context) {
			$field = $context['field'];
			$formatter_handle = $field->get('text_formatter');
			$textarea = (
				isset($context['input'])
					? $context['input']
					: $context['textarea']
			);
			
			if (false === Formatter::exists($formatter_handle)) {
				throw new Exception(__(
					"Unable to find text formatter '%s'.",
					array(
						$formatter_handle
					)
				));
			}
			
			$formatter = Formatter::load($formatter_handle);
			$formatter->displayPublishPanel($field, $textarea);
		}
		
		/**
		 * Get the name of the desired navigation group.
		 */
		public function getNavigationGroup() {
			if ($this->missing_navigation_group === true) return null;
			
			return Symphony::Configuration()->get('navigation_group', 'formatters');
		}
		
		/**
		 * Get a list of available navigation groups.
		 */
		public function getNavigationGroups() {
			$sectionManager = new SectionManager(Symphony::Engine());
			$sections = $sectionManager->fetch(null, 'ASC', 'sortorder');
			$options = array();
			
			if (is_array($sections)) foreach ($sections as $section) {
				$options[] = $section->get('navigation_group');
			}
			
			$options[] = __('Blueprints');
			$options[] = __('System');
			
			return array_unique($options);
		}
		
		/**
		 * Validate preferences before saving.
		 * @param array $context
		 */
		public function actionsPreferences($context) {
			if (
				!isset($context['settings']['formatters']['navigation_group'])
				|| trim($context['settings']['formatters']['navigation_group']) == ''
			) {
				$context['errors']['formatters']['navigation_group'] = __('This is a required field.');
				$this->missing_navigation_group = true;
			}
		}
		
		/**
		 * View preferences.
		 * @param array $context
		 */
		public function viewPreferences($context) {
			$wrapper = $context['wrapper'];
			$errors = Symphony::Engine()->Page->_errors;
			
			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', __('Text Formatters')));
			
			$label = Widget::Label(
				__('Navigation Group')
				. ' <i>'
				. __('Created if it does not exist')
				. '</i>'
			);
			$label->appendChild(Widget::Input(
				'settings[formatters][navigation_group]',
				$this->getNavigationGroup()
			));
			
			if (isset($errors['formatters']['navigation_group'])) {
				$label = Widget::wrapFormElementWithError($label, $errors['formatters']['navigation_group']);
			}
			
			$fieldset->appendChild($label);
			
			$list = new XMLElement('ul');
			$list->setAttribute('class', 'tags singular');
			
			foreach ($this->getNavigationGroups() as $group) {
				$list->appendChild(new XMLElement('li', $group));
			}
			
			$fieldset->appendChild($list);
			$wrapper->appendChild($fieldset);
		}
	}

?>