<?php


namespace RankingsDB;

use SoapClient as _SoapClient;

class SoapClient extends _SoapClient
{
    public $wsdl = true;

    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        // Fetch default User-Agent
        $old_ua = ini_get("user_agent");

        // Override User-Agent
        ini_set("user_agent", $this->get_user_agent());

        try {
            $response = parent::__doRequest($request, $location, $action, $version, $one_way);
        } finally {
            // Reset User-Agent to default
            ini_set("user_agent", $old_ua);
        }
        return $response;
    }

    protected function get_user_agent()
    {
        if ($this->wsdl) {
            return "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        }
        return "SOAP Toolkit 3.0";
    }

}