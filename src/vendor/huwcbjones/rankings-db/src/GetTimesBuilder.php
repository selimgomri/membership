<?php

namespace RankingsDB;

use DateTime;
use Exception;

/**
 * Builder to create options for GetTimes
 *
 * @package RankingsDB
 */
class GetTimesBuilder
{
    protected $_member_id;
    protected $_from_date = null;
    protected $_to_date = null;
    protected $_course = "S";
    protected $_level = 0;
    protected $_include_masters = true;
    protected $_include_relays = false;

    /**
     * Create a GetTimesBuilder object
     *
     * @param $member int|MemberDetails SE Membership ID, or Member Details object
     */
    public function __construct($member)
    {
        if (is_int($member)) {
            $this->_member_id = $member;
        } else {
            $this->_member_id = $member->MemberID();
        }
    }

    /**
     * Include times from masters events
     *
     * @param bool $include_masters
     * @return GetTimesBuilder
     */
    public function setIncludeMasters($include_masters)
    {
        $this->_include_masters = $include_masters;
        return $this;
    }

    /**
     * Include times from relays
     *
     * @param bool $include_relays
     * @return GetTimesBuilder
     */
    public function setIncludeRelays($include_relays)
    {
        $this->_include_relays = $include_relays;
        return $this;
    }

    /**
     * @return int
     */
    public function getMemberID()
    {
        return $this->_member_id;
    }

    /**
     * @return DateTime
     */
    public function getFromDate()
    {
        if ($this->_from_date === null) {
            try {
                return (new DateTime("1970-01-01"))->setTime(0, 0, 0);
            } catch (Exception $e) {

            }
        }
        return $this->_from_date;
    }

    /**
     * Set the from date
     *
     * @param null|DateTime $from_date
     * @return GetTimesBuilder
     */
    public function setFromDate($from_date)
    {
        $this->_from_date = $from_date;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getToDate()
    {
        if ($this->_to_date === null) {
            try {
                return (new DateTime());
            } catch (Exception $e) {

            }
        }
        return $this->_to_date;
    }

    /**
     * Set the to date
     *
     * @param null|DateTime $to_date
     * @return GetTimesBuilder
     */
    public function setToDate($to_date)
    {
        $this->_to_date = $to_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getCourse()
    {
        return $this->_course;
    }

    /**
     * Set the course code
     *
     * @param string $course
     * @return GetTimesBuilder
     */
    public function setCourse($course)
    {
        $this->_course = $course;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->_level;
    }

    /**
     * Set the minimum required competition level
     *
     * - 0: ALL
     * - 1: Level 1
     * - 2: Level 2
     * - 3: Level 3
     *
     * @param int $level
     * @return GetTimesBuilder
     */
    public function setLevel($level)
    {
        $this->_level = $level;
        return $this;
    }

    /**
     * @return bool
     */
    public function includeMasters()
    {
        return $this->_include_masters;
    }

    /**
     * @return bool
     */
    public function includeRelays()
    {
        return $this->_include_relays;
    }

}