<?php

namespace RankingsDB;

use RankingsDB\exceptions\ConnectionException;
use RankingsDB\exceptions\InvalidPersonalKey;
use RankingsDB\exceptions\MemberNotFound;
use RankingsDB\exceptions\UnknownException;
use SoapFault;
use SoapVar;
use TypeError;

class RankingsClient
{
    private static $_user = "6719218280CF981A54BFE34E37074B7F";
    private static $_pass = "2633963A462E2831B6D5F424B195D2DB";
    public $_soap_client;
    protected $_personal_key;
    protected $_personal_key_member_number;
    private $_soap_user;
    private $_soap_pass;
    private $_soap_pk;
    private $_soap_pk_id;

    /***
     * Construct a RankingsDB Client
     *
     * Personal key should be fetched from www.swimmingresults.org.
     * The Membership Number is the membership number for the user that requested the personal key.
     *
     * E.g.: If it is your personal key, then membership number is your membership number.
     *
     * @param $personal_key string Personal Key
     * @param $membership_number int Membership Number for Personal Key
     *
     * @throws TypeError If the membership number is not an integer
     * @throws ConnectionException If a connection could not be made to the Rankings DB
     * @throws InvalidPersonalKey If the personal key/member ID is invalid
     */
    public function __construct($personal_key, $membership_number)
    {
        $this->_soap_user = new SoapVar(RankingsClient::$_user, XSD_STRING);
        $this->_soap_pass = new SoapVar(RankingsClient::$_pass, XSD_STRING);

        if (!is_int($membership_number)) {
            throw new TypeError("Personal key number must be an integer");
        }

        $this->_personal_key = $personal_key;
        $this->_personal_key_member_number = $membership_number;

        $this->_soap_pk = new SoapVar($this->_personal_key, XSD_STRING);
        $this->_soap_pk_id = new SoapVar($this->_personal_key_member_number, XSD_INT);

        try {
            $this->_soap_client = new SoapClient(
                null,
                [
                    "location" => "https://www.swimmingresults.org/soap/BritSwimWebServ.php",
                    "uri" => "https://www.swimmingresults.org/soap",
                    "trace" => 1
                ]
            );
            $this->_soap_client->wsdl = false;
            $this->_checkConnection();
        } catch (SoapFault $fault) {
            throw new ConnectionException("Failed to connect to rankings DB - " . $fault->getMessage());
        } catch (UnknownException $fault) {
            throw new ConnectionException("Failed to connect to rankings DB - " . $fault->getMessage());
        }
    }

    /**
     * Check connection to service
     *
     * @throws ConnectionException
     * @throws InvalidPersonalKey
     * @throws UnknownException if something unknown failed
     */
    protected function _checkConnection()
    {

        $result = $this->_soap_client->CheckConnection2Service($this->_soap_user, $this->_soap_pass);
        if ($result != "Okay") {
            throw new ConnectionException("Failed to connect to rankings DB - an unknown error occurred.");
        }

        // Check to see if personal key is valid
        try {
            $this->getMemberDetails(-1);
        } catch (SoapFault $fault) {
            if (strpos($fault->getMessage(), "Invalid Personal Key") !== false || strpos($fault->getMessage(), "No Personal Key") !== false) {
                throw new InvalidPersonalKey("Invalid personal key");
            }
        } catch (MemberNotFound $exception) {
            // We know this will throw if the personal key is correct
        }
    }

    /**
     * Fetches a member's details from their ID number
     *
     * @param $member_number int SE Membership ID
     *
     * @return MemberDetails
     *
     * @throws TypeError if member ID is not an integer
     * @throws MemberNotFound if a member could not be found
     * @throws UnknownException if something unknown failed
     */
    public function getMemberDetails($member_number)
    {
        if (!is_int($member_number)) {
            throw new TypeError("Membership number must be an integer");
        }
        $soap_member_id = new SoapVar($member_number, XSD_INT);
        try {
            $member = $this->_soap_client->MembDetailsSingleMembV2(
                $this->_soap_user,
                $this->_soap_pass,
                $soap_member_id,
                $this->_soap_pk,
                $this->_soap_pk_id
            );
            return new MemberDetails($member[0]);
        } catch (SoapFault $fault) {
            if (strpos($fault->getMessage(), "No member found") !== false) {
                throw new MemberNotFound("Member with ID '" . $member_number . "' was not found");
            }
            throw new UnknownException("Could not fetch member, an unknown error occurred. " . $fault->getMessage());
        }
    }

    /**
     * Fetch all a member's times
     *
     * @param $options GetTimesBuilder Search criteria
     *
     * @return Times Swimmer's times
     *
     * @throws UnknownException
     */
    public function getTimes($options)
    {
        $soap_member_id = new SoapVar($options->getMemberID(), XSD_INT);
        try {
            $times = $this->_soap_client->MembTimeAllEventsV3(
                $this->_soap_user,
                $this->_soap_pass,
                $soap_member_id,
                new SoapVar($options->getFromDate()->format("Y-m-d"), XSD_STRING),
                new SoapVar($options->getToDate()->format("Y-m-d"), XSD_STRING),
                new SoapVar($options->getCourse(), XSD_STRING),
                new SoapVar($options->getLevel(), XSD_INT),
                new SoapVar($options->includeMasters(), XSD_BOOLEAN),
                new SoapVar($options->includeRelays(), XSD_BOOLEAN)
            );
            return new Times($times);
        } catch (SoapFault $fault) {
            if (strpos($fault->getMessage(), "No swims found")) {
                return new Times([]);
            }
            throw new UnknownException("Could not fetch times, an unknown error occurred. " . $fault->getMessage());
        }
    }

    /**
     * Fetch a time for specified event
     *
     * @param $event Event Event to fetch times for
     * @param $options GetTimesBuilder Search criteria
     *
     * @return Time|null Time or null if no time found
     *
     * @throws UnknownException
     */
    public function getTimeForEvent($event, $options)
    {
        $soap_member_id = new SoapVar($options->getMemberID(), XSD_INT);
        try {
            $time = $this->_soap_client->MembTimeSingleEventV3(
                $this->_soap_user,
                $this->_soap_pass,
                $soap_member_id,
                new SoapVar($options->getFromDate()->format("Y-m-d"), XSD_STRING),
                new SoapVar($options->getToDate()->format("Y-m-d"), XSD_STRING),
                new SoapVar($options->getCourse(), XSD_STRING),
                new SoapVar($event->eventCode(), XSD_INT),
                new SoapVar($options->getLevel(), XSD_INT),
                new SoapVar($options->includeMasters(), XSD_BOOLEAN),
                new SoapVar($options->includeRelays(), XSD_BOOLEAN)
            )[0];
            return new Time(Event::fromEventCode($time->Event), $time->Time);
        } catch (SoapFault $fault) {
            if (strpos($fault->getMessage(), "No swims found") !== false) {
                return null;
            }
            throw new UnknownException("Could not fetch time, an unknown error occurred. " . $fault->getMessage());
        }
    }

}