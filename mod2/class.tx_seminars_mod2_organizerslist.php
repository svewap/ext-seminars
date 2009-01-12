<?php
/***************************************************************
* Copyright notice
*
* (c) 2007-2009 Niels Pardon (mail@niels-pardon.de)
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('seminars') . 'lib/tx_seminars_constants.php');

/**
 * Class 'organizers list' for the 'seminars' extension.
 *
 * @package TYPO3
 * @subpackage tx_seminars
 *
 * @author Niels Pardon <mail@niels-pardon.de>
 */
class tx_seminars_mod2_organizerslist extends tx_seminars_mod2_backendlist {
	/** the table we're working on */
	protected $tableName = SEMINARS_TABLE_ORGANIZERS;

	/** the organizer which we want to list/show */
	private $organizer = null;

	/**
	 * Frees as much memory that has been used by this object as possible.
	 */
	public function __destruct() {
		if ($this->organizer) {
			$this->organizer->__destruct();
			unset($this->organizer);
		}

		parent::__destruct();
	}


	/**
	 * Generates and prints out a organizers list.
	 *
	 * @return string the HTML source code to display
	 */
	public function show() {
		global $LANG;

		// Initialize the variable for the HTML source code.
		$content = '';

		// Set the table layout of the event list.
		$tableLayout = array(
			'table' => array(
				TAB.TAB
					.'<table cellpadding="0" cellspacing="0" class="typo3-dblist">'
					.LF,
				TAB.TAB
					.'</table>'.LF
			),
			array(
				'tr' => array(
					TAB.TAB.TAB
						.'<thead>'.LF
						.TAB.TAB.TAB.TAB
						.'<tr>'.LF,
					TAB.TAB.TAB.TAB
						.'</tr>'.LF
						.TAB.TAB.TAB
						.'</thead>'.LF
				),
				'defCol' => array(
					TAB.TAB.TAB.TAB.TAB
						.'<td class="c-headLineTable">'.LF,
					TAB.TAB.TAB.TAB.TAB
						.'</td>'.LF
				)
			),
			'defRow' => array(
				'tr' => array(
					TAB.TAB.TAB
						.'<tr>'.LF,
					TAB.TAB.TAB
						.'</tr>'.LF
				),
				'defCol' => array(
					TAB.TAB.TAB.TAB
						.'<td>'.LF,
					TAB.TAB.TAB.TAB
						.'</td>'.LF
				)
			)
		);

		// Fill the first row of the table array with the header.
		$table = array(
			array(
				'',
				TAB.TAB.TAB.TAB.TAB.TAB
					.'<span style="color: #ffffff; font-weight: bold;">'
					.$LANG->getLL('organizerlist.title').'</span>'.LF,
				TAB.TAB.TAB.TAB.TAB.TAB
					.'&nbsp;'.LF
			)
		);

		$builder = t3lib_div::makeInstance('tx_seminars_OrganizerBagBuilder');

		$pageData = $this->page->getPageData();
		$builder->setSourcePages($pageData['uid']);

		$organizerBag = $builder->build();

		foreach ($organizerBag as $this->organizer) {
			// Add the result row to the table array.
			$table[] = array(
				TAB.TAB.TAB.TAB.TAB
					.$this->organizer->getRecordIcon().LF,
				TAB.TAB.TAB.TAB.TAB
					.$this->organizer->getTitle().LF,
				TAB.TAB.TAB.TAB.TAB
					.$this->getEditIcon(
						$this->organizer->getUid()
					)
					.$this->getDeleteIcon(
						$this->organizer->getUid()
					).LF
			);
		}
		$organizerBag->__destruct();

		$content .= $this->getNewIcon($pageData['uid']);

		// Output the table array using the tableLayout array with the template
		// class.
		$content .= $this->page->doc->table($table, $tableLayout);

		$content .= $organizerBag->checkConfiguration();

		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/seminars/mod2/class.tx_seminars_mod2_organizerslist.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/seminars/mod2/class.tx_seminars_mod2_organizerslist.php']);
}
?>