<?php

declare(strict_types=1);

use OliverKlee\PhpUnit\TestCase;

/**
 * Test case.
 *
 * @author Niels Pardon <mail@niels-pardon.de>
 */
class Tx_Seminars_Tests_Unit_Mapper_TimeSlotTest extends TestCase
{
    /**
     * @var \Tx_Oelib_TestingFramework
     */
    private $testingFramework = null;

    /**
     * @var \Tx_Seminars_Mapper_TimeSlot
     */
    private $subject = null;

    protected function setUp()
    {
        $this->testingFramework = new \Tx_Oelib_TestingFramework('tx_seminars');

        $this->subject = new \Tx_Seminars_Mapper_TimeSlot();
    }

    protected function tearDown()
    {
        $this->testingFramework->cleanUp();
    }

    //////////////////////////
    // Tests concerning find
    //////////////////////////

    /**
     * @test
     */
    public function findWithUidReturnsTimeSlotInstance()
    {
        self::assertInstanceOf(
            \Tx_Seminars_Model_TimeSlot::class,
            $this->subject->find(1)
        );
    }

    /**
     * @test
     */
    public function findWithUidOfExistingRecordReturnsRecordAsModel()
    {
        $uid = $this->testingFramework->createRecord('tx_seminars_timeslots');

        /** @var \Tx_Seminars_Model_TimeSlot $model */
        $model = $this->subject->find($uid);

        self::assertSame($uid, $model->getUid());
    }

    //////////////////////////////////
    // Tests regarding the speakers.
    //////////////////////////////////

    /**
     * @test
     */
    public function getSpeakersReturnsListInstance()
    {
        $uid = $this->testingFramework->createRecord('tx_seminars_timeslots');

        /** @var \Tx_Seminars_Model_TimeSlot $model */
        $model = $this->subject->find($uid);
        self::assertInstanceOf(\Tx_Oelib_List::class, $model->getSpeakers());
    }

    /**
     * @test
     */
    public function getSpeakersWithOneSpeakerReturnsListOfSpeakers()
    {
        $timeSlotUid = $this->testingFramework->createRecord(
            'tx_seminars_timeslots'
        );
        $speaker = \Tx_Oelib_MapperRegistry::get(\Tx_Seminars_Mapper_Speaker::class)
            ->getNewGhost();
        $this->testingFramework->createRelationAndUpdateCounter(
            'tx_seminars_timeslots',
            $timeSlotUid,
            $speaker->getUid(),
            'speakers'
        );

        /** @var \Tx_Seminars_Model_TimeSlot $model */
        $model = $this->subject->find($timeSlotUid);
        self::assertInstanceOf(
            \Tx_Seminars_Model_Speaker::class,
            $model->getSpeakers()->first()
        );
    }

    /**
     * @test
     */
    public function getSpeakersWithOneSpeakerReturnsOneSpeaker()
    {
        $timeSlotUid = $this->testingFramework->createRecord(
            'tx_seminars_timeslots'
        );
        $speaker = \Tx_Oelib_MapperRegistry::get(\Tx_Seminars_Mapper_Speaker::class)
            ->getNewGhost();
        $this->testingFramework->createRelationAndUpdateCounter(
            'tx_seminars_timeslots',
            $timeSlotUid,
            $speaker->getUid(),
            'speakers'
        );

        /** @var \Tx_Seminars_Model_TimeSlot $model */
        $model = $this->subject->find($timeSlotUid);
        self::assertEquals(
            $speaker->getUid(),
            $model->getSpeakers()->getUids()
        );
    }

    ///////////////////////////////
    // Tests regarding the place.
    ///////////////////////////////

    /**
     * @test
     */
    public function getPlaceWithoutPlaceReturnsNull()
    {
        $uid = $this->testingFramework->createRecord('tx_seminars_timeslots');

        /** @var \Tx_Seminars_Model_TimeSlot $model */
        $model = $this->subject->find($uid);
        self::assertNull(
            $model->getPlace()
        );
    }

    /**
     * @test
     */
    public function getPlaceWithPlaceReturnsPlaceInstance()
    {
        $place = \Tx_Oelib_MapperRegistry::get(\Tx_Seminars_Mapper_Place::class)->getNewGhost();
        $timeSlotUid = $this->testingFramework->createRecord(
            'tx_seminars_timeslots',
            ['place' => $place->getUid()]
        );

        /** @var \Tx_Seminars_Model_TimeSlot $model */
        $model = $this->subject->find($timeSlotUid);
        self::assertInstanceOf(\Tx_Seminars_Model_Place::class, $model->getPlace());
    }

    /*
     * Tests regarding the seminar.
     */

    /**
     * @test
     */
    public function getSeminarWithoutSeminarReturnsNull()
    {
        $uid = $this->testingFramework->createRecord('tx_seminars_timeslots');

        /** @var \Tx_Seminars_Model_TimeSlot $model */
        $model = $this->subject->find($uid);
        self::assertNull(
            $model->getSeminar()
        );
    }

    /**
     * @test
     */
    public function getSeminarWithSeminarReturnsEventInstance()
    {
        $seminar = \Tx_Oelib_MapperRegistry::get(\Tx_Seminars_Mapper_Event::class)->getNewGhost();
        $timeSlotUid = $this->testingFramework->createRecord(
            'tx_seminars_timeslots',
            ['seminar' => $seminar->getUid()]
        );

        /** @var \Tx_Seminars_Model_TimeSlot $model */
        $model = $this->subject->find($timeSlotUid);
        self::assertInstanceOf(
            \Tx_Seminars_Model_Event::class,
            $model->getSeminar()
        );
    }
}
