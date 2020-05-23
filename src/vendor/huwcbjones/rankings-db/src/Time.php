<?php

namespace RankingsDB;

/**
 * Time object
 *
 * Contains time and event details
 *
 * @package RankingsDB
 */
class Time
{
    protected $_event;
    protected $_minutes = 0;
    protected $_seconds = 0;
    protected $_hundredths = 0;

    /**
     * Time constructor.
     *
     * @param $event Event
     * @param $time_str string
     */
    public function __construct($event, $time_str)
    {
        $this->_event = $event;
        $this->_hundredths = intval(substr($time_str, strlen($time_str) - 2, 2));
        $this->_seconds = intval(substr($time_str, strlen($time_str) - 4, 2));
        $this->_minutes = intval(substr($time_str, 0, strlen($time_str) - 4));
    }

    /**
     * Stroke
     *
     * @return Stroke
     */
    public function stroke()
    {
        return $this->_event->stroke();
    }

    /**
     * Distance
     *
     * @return int
     */
    public function distance()
    {
        return $this->_event->distance();
    }

    /**
     * Event Object
     *
     * @return Event
     */
    public function event()
    {
        return $this->_event;
    }

    /**
     * Returns the time in seconds
     *
     * E.g.:
     *
     * 2:02.72 => 122.72
     *
     * @return float
     */
    public function getTimeInSeconds()
    {
        return $this->_minutes * 60 + $this->_seconds + $this->_hundredths / 100;
    }

    /**
     * Returns the time in hundredths of seconds
     *
     * E.g.:
     *
     * 2:02.72 => 12272
     *
     * @return int
     */
    public function getTimeInHundredths()
    {
        return 100 * ($this->_minutes * 60 + $this->_seconds) + $this->_hundredths;
    }

    public function __toString()
    {
        return $this->_event->eventID() . ": " . $this->getTime();
    }

    /**
     * Get the time formatted in m:s.0
     *
     * If `$leading_zeroes` is `true`, then anytime less than 60 seconds will be prefixed with `0:`.
     *
     * E.g.:
     *
     * '56.78' => '0:56.78'
     *
     * @param bool $leading_zeroes include minutes if 0
     * @return string
     */
    public function getTime($leading_zeroes = true)
    {
        $time = sprintf("%02d.%02d", $this->_seconds, $this->_hundredths);
        if ($this->_minutes !== 0 || $leading_zeroes) {
            $time = $this->_minutes . ":" . $time;
        }
        return $time;
    }


}