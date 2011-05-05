<?php
	
	/**
	 * @package content
	 */
	
	require_once EXTENSIONS . '/text_formatters/lib/class.iterator.php';
	require_once EXTENSIONS . '/text_formatters/lib/class.page.php';
	
	/**
	 * Display a table view of available test cases.
	 */
	class ContentExtensionText_FormattersIndex extends TextFormatterPage {
		/**
		 * Greate the page form.
		 */
		public function view() {
			$formatters = new TextFormatterIterator();
			
			$this->setPageType('table');
			$this->setTitle(__(
				'%1$s &ndash; %2$s',
				array(
					__('Symphony'),
					__('Tests')
				)
			));
			
			$this->appendSubheading(__('Tests'));
			
			$table = new XMLElement('table');
			$table->appendChild(
				Widget::TableHead(array(
					array(__('Name'), 'col'),
					array(__('Location'), 'col'),
					array(__('Description'), 'col'),
					array(__('Preview'), 'col')
				))
			);
			
			if (!$formatters->valid()) {
				$table->appendChild(Widget::TableRow(array(
					Widget::TableData(
						__('None Found.'),
						'inactive',
						null, 4
					)
				)));
			}
			
			else foreach ($formatters as $formatter) {
				$info = (object)$formatter->about();
				$row = new XMLElement('tr');
				
				if ($formatter instanceof EditableTextFormatter) {
					$row->appendChild(Widget::TableData(
						Widget::Anchor(
							$info->name,
							sprintf(
								'%s/test/%s/',
								$this->root_url,
								$test->handle
							)
						)
					));
				}
				
				else {
					$row->appendChild(Widget::TableData(
						$info->name
					));
				}
				
				if ($info->{'in-extension'}) {
					$extension = (object)Symphony::ExtensionManager()->create($info->{'extension'})->about();
					
					$row->appendChild(Widget::TableData(
						$extension->name
					));
				}
				
				else if ($info->{'in-symphony'}) {
					$row->appendChild(Widget::TableData(
						__('Symphony'), 'inactive'
					));
				}
				
				else if ($info->{'in-workspace'}) {
					$row->appendChild(Widget::TableData(
						__('Workspace'), 'inactive'
					));
				}
				
				else {
					$row->appendChild(Widget::TableData(
						__('None'), 'inactive'
					));
				}
				
				if ($info->description) {
					$row->appendChild(Widget::TableData(
						$info->description
					));
				}
				
				else {
					$row->appendChild(Widget::TableData(
						__('None'), 'inactive'
					));
				}
				
				$row->appendChild(Widget::TableData(
					Widget::Anchor(
						'Preview &rarr;',
						sprintf(
							'%s/preview/%s/',
							$this->root_url,
							$formatter->handle
						)
					)
				));
				
				$table->appendChild($row);
			}
			
			$this->Form->appendChild($table);
		}
	}
	
?>