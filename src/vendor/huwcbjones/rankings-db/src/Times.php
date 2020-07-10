<?php

namespace RankingsDB;

use Iterator;

class Times implements Iterator
{
    protected $_times = [];
    protected $_pointer = 0;

    public function __construct($times)
    {
        foreach ($times as $time) {
            $event = Event::fromEventCode($time->Event);
            $this->_times[$event->eventID()] = new Time($event, $time->Time);
        }
    }

    static protected function _evtKey($distance, $stroke)
    {
        return (new Event($stroke, $distance))->eventID();
    }

    /**
     * Get the time for a given distance/stroke
     *
     * You can iterate over this object in a foreach loop
     *
     * @param $distance int
     * @param $stroke Stroke
     *
     * @return Time|null time, or null if not time
     */
    public function getTime($distance, $stroke)
    {
        $evt_key = $this->_evtKey($distance, $stroke);
        if (!array_key_exists($evt_key, $this->_times)) {
            return null;
        }
        return $this->_times[$evt_key];
    }

    protected function _getKeyAtPos($position)
    {
        $keys = array_keys($this->_times);
        return $keys[$position];
    }

    protected function _getTimeAtPos($position)
    {
        return $this->_times[$this->_getKeyAtPos($position)];
    }

    //<editor-fold desc="Iterator Interface">
    public function current()
    {
        return $this->_getTimeAtPos($this->_pointer);
    }

    public function next()
    {
        ++$this->_pointer;
    }

    public function key()
    {
        return $this->_getKeyAtPos($this->_pointer);
    }

    public function valid()
    {
        return $this->_pointer < count($this->_times);
    }

    public function rewind()
    {
        $this->_pointer = 0;
    }
    //</editor-fold>
}