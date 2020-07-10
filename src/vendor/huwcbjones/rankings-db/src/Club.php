<?php

namespace RankingsDB;

/**
 * Club object
 *
 * @package RankingsDB
 */
class Club
{
    protected $_club;
    protected $_county;
    protected $_ranked;

    public function __construct($club_code, $ranked)
    {
        $this->_club = substr($club_code, 0, 4);
        $this->_county = substr($club_code, 4, 4);
        $this->_ranked = $ranked;
    }

    /**
     * Club Code
     *
     * @return string
     */
    public function Club()
    {
        return $this->_club;
    }

    /**
     * County Code
     *
     * @return string
     */
    public function County()
    {
        return $this->_county;
    }

    /**
     * Is this club a member's ranked club
     *
     * @return boolean true if this club is a member's ranked club
     */
    public function IsRanked()
    {
        return $this->_ranked;
    }

    public function __toString()
    {
        return $this->_club . ":" . $this->_county;
    }


}