<?php

namespace RankingsDB;

/**
 * Event obj
 *
 * @package RankingsDB
 */
class Event
{
    static protected $_event_map = [
        1 => Stroke::FREESTYLE . "50",
        2 => Stroke::FREESTYLE . "100",
        3 => Stroke::FREESTYLE . "200",
        4 => Stroke::FREESTYLE . "400",
        5 => Stroke::FREESTYLE . "800",
        6 => Stroke::FREESTYLE . "1500",
        7 => Stroke::BREASTSTROKE . "50",
        8 => Stroke::BREASTSTROKE . "100",
        9 => Stroke::BREASTSTROKE . "200",
        10 => Stroke::BUTTERFLY . "50",
        11 => Stroke::BUTTERFLY . "100",
        12 => Stroke::BUTTERFLY . "200",
        13 => Stroke::BACKSTROKE . "50",
        14 => Stroke::BACKSTROKE . "100",
        15 => Stroke::BACKSTROKE . "200",
        18 => Stroke::INDIVIDUAL_MEDLEY . "100",
        16 => Stroke::INDIVIDUAL_MEDLEY . "200",
        17 => Stroke::INDIVIDUAL_MEDLEY . "400"
    ];
    static protected $_reverse_map = [];
    protected $_stroke;
    protected $_distance;

    public function __construct($stroke, $distance)
    {
        $this->_stroke = $stroke;
        $this->_distance = $distance;

        if (count(Event::$_reverse_map) == 0) {
            Event::$_reverse_map = array_flip(Event::$_event_map);
        }
    }

    public static function fromEventCode($code)
    {
        $event_id = Event::$_event_map[$code];
        return Event::fromEventID($event_id);
    }

    /**
     * Creates an event from an event ID
     *
     * @param $event_id
     * @return Event
     */
    public static function fromEventID($event_id)
    {
        $stroke = substr($event_id, 0, 2);
        $distance = intval(substr($event_id, 2));
        return new Event($stroke, $distance);
    }

    /**
     * Stroke
     *
     * @return Stroke
     */
    public function stroke()
    {
        return $this->_stroke;
    }

    /**
     * Distance
     *
     * @return int
     */
    public function distance()
    {
        return $this->_distance;
    }

    /**
     * Returns the event code for this event object
     *
     * @return int
     */
    public function eventCode()
    {
        return Event::$_reverse_map[$this->eventID()];
    }

    /**
     * Returns the event ID for this event object
     *
     * @return string
     */
    public function eventID()
    {
        return $this->_stroke . $this->_distance;
    }

}