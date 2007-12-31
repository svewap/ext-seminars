<?php
/***************************************************************
* Copyright notice
*
* (c) 2007-2008 Oliver Klee (typo3-coding@oliverklee.de)
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
/**
 * Testcase for the events list class in the 'seminars' extension.
 *
 * @package		TYPO3
 * @subpackage	tx_seminars
 * @author		Oliver Klee <typo3-coding@oliverklee.de>
 */

require_once(PATH_t3lib.'class.t3lib_scbase.php');

require_once(t3lib_extMgm::extPath('oelib').'tests/fixtures/class.tx_oelib_testingframework.php');

require_once(t3lib_extMgm::extPath('seminars').'lib/tx_seminars_constants.php');
require_once(t3lib_extMgm::extPath('seminars').'mod2/class.tx_seminars_eventslist.php');

define('SEMINARS_SYSFOLDER_TITLE', 'tx_seminars unit test page');

class tx_seminars_eventslist_testcase extends tx_phpunit_testcase {
	private $fixture;
	private $testingFramework;

	/** PID of a dummy system folder */
	private $dummySysFolderPid = 0;

	/** a BE page object */
	private $page;

	public function setUp() {
		global $BACK_PATH;

		$this->testingFramework
			= new tx_oelib_testingframework('tx_seminars');

		$this->fixture = new tx_seminars_eventslist($this->page);

		$this->createDummySystemFolder();

		$this->page = new t3lib_SCbase();
		$this->page->id = $this->dummySysFolderPid;
		$this->page->pageInfo = array();
		$this->page->pageInfo['uid'] = $this->dummySysFolderPid;

		$this->page->doc = t3lib_div::makeInstance('bigDoc');
		$this->page->doc->backPath = $BACK_PATH;
		$this->page->doc->docType = 'xhtml_strict';
	}

	public function tearDown() {
		$this->deleteDummySystemFolder();
		$this->testingFramework->resetAutoIncrement('pages');
		$this->testingFramework->cleanUp();
		unset($this->page);
		unset($this->fixture);
		unset($this->testingFramework);
	}


	///////////////////////
	// Utility functions.
	///////////////////////

	/**
	 * Creates a system folder and stores its PID in $this->dummySysFolderPid.
	 */
	private function createDummySystemFolder() {
		$GLOBALS['TYPO3_DB']->exec_INSERTquery(
			'pages',
			array(
				'pid' => 0,
				'doktype' => 254,
				'title' => SEMINARS_SYSFOLDER_TITLE
			)
		);

		$this->dummySysFolderPid = $GLOBALS['TYPO3_DB']->sql_insert_id();
	}

	/**
	 * Deletes the dummy system folder that has been created using
	 * createDummySystemFolder.
	 *
	 * If no such folder has been created, this function is a no-op.
	 */
	private function deleteDummySystemFolder() {
		if ($this->dummySysFolderPid == 0) {
			return;
		}

		$GLOBALS['TYPO3_DB']->exec_DELETEquery(
			'pages',
			'uid = '.$this->dummySysFolderPid
		);

		$this->dummySysFolderPid = 0;
	}


	/////////////////////////////////////
	// Tests for the utility functions.
	/////////////////////////////////////

	public function testDummySystemFolderHasBeenCreated() {
		$this->assertNotEquals(
			0,
			$this->dummySysFolderPid
		);

		$this->assertNotEquals(
			0,
			$this->testingFramework->countRecords(
				'pages', 'uid='.$this->dummySysFolderPid
			)
		);
	}

	public function testDummySystemFolderCanBeDeleted() {
		$this->deleteDummySystemFolder();

		$this->assertEquals(
			0,
			$this->dummySysFolderPid
		);

		$this->assertEquals(
			0,
			$this->testingFramework->countRecords(
				'pages', 'title="'.SEMINARS_SYSFOLDER_TITLE.'"'
			)
		);
	}


	/////////////////////////////////////////
	// Tests for the events list functions.
	/////////////////////////////////////////

	public function testShowContainsNoBodyHeaderWithEmptySystemFolder() {
		$this->assertNotContains(
			'<td class="datecol">',
			$this->fixture->show()
		);
	}

	public function testShowContainsTableBodyHeaderForOneEvent() {
		$this->testingFramework->createRecord(
			SEMINARS_TABLE_SEMINARS,
			array('pid' => $this->dummySysFolderPid)
		);

		$this->assertContains(
			'<td class="datecol">',
			$this->fixture->show()
		);
	}

	public function testShowContainsNoBodyHeaderIfEventIsOnOtherPage() {
		// Puts this record on a non-existing page. This is intentional.
		$this->testingFramework->createRecord(
			SEMINARS_TABLE_SEMINARS,
			array('pid' => $this->dummySysFolderPid + 1)
		);

		$this->assertNotContains(
			'<td class="datecol">',
			$this->fixture->show()
		);
	}

	public function testShowContainsEventTitleForOneEvent() {
		$this->testingFramework->createRecord(
			SEMINARS_TABLE_SEMINARS,
			array(
				'pid' => $this->dummySysFolderPid,
				'title' => 'event_1'
			)
		);

		$this->assertContains(
			'event_1',
			$this->fixture->show()
		);
	}

	public function testShowContainsEventTitleForTwoEvents() {
		$this->testingFramework->createRecord(
			SEMINARS_TABLE_SEMINARS,
			array(
				'pid' => $this->dummySysFolderPid,
				'title' => 'event_1'
			)
		);
		$this->testingFramework->createRecord(
			SEMINARS_TABLE_SEMINARS,
			array(
				'pid' => $this->dummySysFolderPid,
				'title' => 'event_2'
			)
		);

		$this->assertContains(
			'event_1',
			$this->fixture->show()
		);
		$this->assertContains(
			'event_2',
			$this->fixture->show()
		);
	}

	public function testShowContainsEventTitleForOneHiddenEvent() {
		$this->testingFramework->createRecord(
			SEMINARS_TABLE_SEMINARS,
			array(
				'pid' => $this->dummySysFolderPid,
				'title' => 'event_1',
				'hidden' => 1
			)
		);

		$this->assertContains(
			'event_1',
			$this->fixture->show()
		);
	}

	public function testShowContainsEventTitleForOneTimedEvent() {
		$this->testingFramework->createRecord(
			SEMINARS_TABLE_SEMINARS,
			array(
				'pid' => $this->dummySysFolderPid,
				'title' => 'event_1',
				'endtime' => mktime() - 1000
			)
		);

		$this->assertContains(
			'event_1',
			$this->fixture->show()
		);
	}
}

?>
