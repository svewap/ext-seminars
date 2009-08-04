<?php
/***************************************************************
* Copyright notice
*
* (c) 2009 Bernd Schönbach <bernd@oliverklee.de>
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

require_once(t3lib_extMgm::extPath('oelib') . 'class.tx_oelib_Autoloader.php');

/**
 * Testcase for the tx_seminars_Mapper_FrontEndUserGroup class in the 'seminars' extension.
 *
 * @package TYPO3
 * @subpackage tx_seminars
 *
 * @author Bernd Schönbach <bernd@oliverklee.de>
 */
class tx_seminars_Mapper_FrontEndUserGroup_testcase extends tx_phpunit_testcase {
	/**
	 * @var tx_seminars_Mapper_FrontEndUserGroup the object to test
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new tx_seminars_Mapper_FrontEndUserGroup();
	}

	public function tearDown() {
		$this->fixture->__destruct();
		unset($this->fixture);
	}


	//////////////////////////////////////
	// Tests for the basic functionality
	//////////////////////////////////////

	public function test_Mapper_ForGhost_ReturnsSeminarsFrontEndUserGroupInstance() {
		$this->assertTrue(
			$this->fixture->getNewGhost()
				instanceof tx_seminars_Model_FrontEndUserGroup
		);
	}


	//////////////////////////////////
	// Tests concerning the reviewer
	//////////////////////////////////

	public function test_FrontEndUserGroup_CanReturnBackEndUserModel() {
		$backEndUser = tx_oelib_ObjectFactory::make(
			'tx_oelib_Mapper_BackEndUser')->getNewGhost();
		$frontEndUserGroup = $this->fixture->getLoadedTestingModel(
			array('tx_seminars_reviewer' => $backEndUser->getUid())
		);

		$this->assertTrue(
			$this->fixture->find($frontEndUserGroup->getUid())->getReviewer()
				instanceof tx_oelib_Model_BackEndUser
		);
	}
}
?>