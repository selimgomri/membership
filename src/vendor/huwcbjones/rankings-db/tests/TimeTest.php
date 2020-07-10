<?php
/**
 * Author: huw
 * Since: 2019-10-26
 */

namespace RankingsDB\Tests;

use PHPUnit\Framework\TestCase;
use RankingsDB\Event;
use RankingsDB\Stroke;
use RankingsDB\Time;

class TimeTest extends TestCase
{

    public function testGetTimeInSecondsHundredths()
    {
        $event = new Event(Stroke::FREESTYLE, 50);
        $time = new Time($event, "000012");
        $this->assertEquals(0.12, $time->getTimeInSeconds());
    }

    public function testGetTimeInSecondsSeconds()
    {
        $event = new Event(Stroke::FREESTYLE, 50);
        $time = new Time($event, "001234");
        $this->assertEquals(12.34, $time->getTimeInSeconds());
    }

    public function testGetTimeInSecondsMinutes()
    {
        $event = new Event(Stroke::FREESTYLE, 50);
        $time = new Time($event, "123456");
        $this->assertEquals(754.56, $time->getTimeInSeconds());
    }

    public function testGetTimeInHundredthsHundredths()
    {
        $event = new Event(Stroke::FREESTYLE, 50);
        $time = new Time($event, "000012");
        $this->assertEquals(12, $time->getTimeInHundredths());
    }

    public function testGetTimeInHundredthsSeconds()
    {
        $event = new Event(Stroke::FREESTYLE, 50);
        $time = new Time($event, "001234");
        $this->assertEquals(1234, $time->getTimeInHundredths());
    }

    public function testGetTimeInHundredthssMinutes()
    {
        $event = new Event(Stroke::FREESTYLE, 50);
        $time = new Time($event, "123456");
        $this->assertEquals(75456, $time->getTimeInHundredths());
    }

    public function testGetTimeNoLeadingZeroes()
    {
        $event = new Event(Stroke::FREESTYLE, 50);
        $time = new Time($event, "003456");
        $this->assertEquals("34.56", $time->getTime(false));
    }

    public function testGetTimeWithLeadingZeroes()
    {
        $event = new Event(Stroke::FREESTYLE, 50);
        $time = new Time($event, "003456");
        $this->assertEquals("0:34.56", $time->getTime(true));
    }
}
