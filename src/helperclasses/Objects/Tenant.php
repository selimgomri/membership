<?php

class Tenant
{
  private int $id;
  private string $name;
  private $code;
  private $website;
  private $email;
  private $verified;
  private $keys;
  private string $uuid;
  private $goCardlessAccessToken;
  private $goCardlessOrganisationId;
  private $goCardlessLoaded = false;
  private $stripeAccountId;

  public function __construct($details)
  {
    $this->id = $details['ID'];
    $this->name = $details['Name'];
    $this->code = $details['Code'];
    $this->website = $details['Website'];
    $this->email = $details['Email'];
    $this->verified = $details['Verified'];
    $this->uuid = $details['UniqueID'];

    $this->getKeys();
  }

  /**
   * Get a tenant by ID
   * 
   * @param int id
   * @return Tenant|null tenant object if exists
   */
  public static function fromId(int $id)
  {
    $db = app()->db;
    $getTenant = $db->prepare("SELECT `ID`, `Name`, `Code`, `Website`, `Email`, `Verified`, `UniqueID` FROM tenants WHERE ID = ?");
    $getTenant->execute([
      $id
    ]);
    $tentantDetails = $getTenant->fetch(PDO::FETCH_ASSOC);

    if ($tentantDetails) {
      return new Tenant($tentantDetails);
    }
    return null;
  }

  /**
   * Get a tenant by four letter code
   * 
   * @param string code
   * @return Tenant|null tenant object if exists
   */
  public static function fromCode(string $code)
  {
    $db = app()->db;
    $getTenant = $db->prepare("SELECT `ID`, `Name`, `Code`, `Website`, `Email`, `Verified`, `UniqueID` FROM tenants WHERE Code COLLATE utf8mb4_general_ci = ?");
    $getTenant->execute([
      $code
    ]);
    $tentantDetails = $getTenant->fetch(PDO::FETCH_ASSOC);

    if ($tentantDetails) {
      return new Tenant($tentantDetails);
    }
    return null;
  }

  /**
   * Get tenant keys from db
   */
  private function getKeys()
  {
    $db = app()->db;
    $getKeys = $db->prepare("SELECT Option, Value FROM tenantOptions WHERE Tenant = ?");
    $getKeys->execute([
      $this->id
    ]);
    $keys = $getKeys->fetchAll(PDO::FETCH_KEY_PAIR);

    $defaultKeys = [
      'CLUB_NAME' => null,
      'CLUB_SHORT_NAME' => null,
      'ASA_CLUB_CODE' => null,
      'CLUB_EMAIL' => null,
      'CLUB_TRIAL_EMAIL' => null,
      'CLUB_WEBSITE' => null,
      'GOCARDLESS_USE_SANDBOX' => null,
      'GOCARDLESS_SANDBOX_ACCESS_TOKEN' => null,
      'GOCARDLESS_ACCESS_TOKEN' => null,
      'GOCARDLESS_WEBHOOK_KEY' => null,
      'CLUB_ADDRESS' => null,
      'SYSTEM_COLOUR' => '#007bff',
      'ASA_DISTRICT' => 'E',
      'ASA_COUNTY' => 'NDRE',
      'STRIPE' => null,
      'STRIPE_PUBLISHABLE' => null,
      'STRIPE_APPLE_PAY_DOMAIN' => null,
      'EMERGENCY_MESSAGE' => false,
      'EMERGENCY_MESSAGE_TYPE' => 'NONE',
    ];

    foreach ($keys as $key => $value) {
      $defaultKeys[$key] = $value;
    }

    $defaultKeys['CLUB_NAME'] = $this->name;
    if ($this->code) {
      $defaultKeys['ASA_CLUB_CODE'] = mb_strtoupper($this->code);
    }

    $this->keys = $defaultKeys;
  }

  /**
   * Get the tenant id
   * 
   * @return int tenant id
   */
  public function getId()
  {
    return $this->id;
  }

  public function __toString() {
    if ($this->uuid) {
      return $this->uuid;
    }
    return spl_object_hash($this->uuid);
  }

  /**
   * Get the club code
   * 
   * @return string club code
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Get the club name
   * 
   * @return string club name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Get the club website
   * 
   * @return string club website address
   */
  public function getWebsite()
  {
    return $this->website;
  }

  /**
   * Get club admin contact email
   * 
   * @return string email address
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * Is this club account verified?
   * 
   * @return boolean verification status
   */
  public function isVerified()
  {
    return $this->verified;
  }

  /**
   * Get tenant key
   * 
   * @param string key
   * @return string value
   */
  public function getKey(string $key)
  {
    if (isset($this->keys[$key])) {
      return $this->keys[$key];
    }
    return null;
  }

  /**
   * Get tenant key as a boolean
   * 
   * @param string key
   * @return boolean value
   */
  public function getBooleanKey(string $key)
  {
    return filter_var($this->getKey($key), FILTER_VALIDATE_BOOLEAN);
  }

  /**
   * Set tenant key
   * 
   * @param string key
   * @param mixed value
   * @return boolean status
   * @throws PDOException
   */
  public function setKey(string $key, $value)
  {
    $db = app()->db;

    if (!is_numeric($value) && $value == "") {
      $value = null;
    }

    // Update value in memory
    $this->keys[$key] = $value;

    // Any PDO exceptions will be propagated
    $query = $db->prepare("SELECT COUNT(*) FROM tenantOptions WHERE `Option` = ? AND `Tenant` = ?");
    $query->execute([$key, $this->id]);
    $result = $query->fetchColumn();

    $res = false;
    if ($result == 0 && $value == null) {
      $query = $db->prepare("DELETE FROM tenantOptions WHERE `Option` = ? AND `Tenant` = ?");
      $res = $query->execute([$key, $this->id]);
    } else if ($result == 0) {
      $query = $db->prepare("INSERT INTO tenantOptions (`Option`, `Value`, `Tenant`) VALUES (?, ?, ?)");
      $res = $query->execute([$key, $value, $this->id]);
    } else {
      $query = $db->prepare("UPDATE tenantOptions SET `Value` = ? WHERE `Option` = ? AND `Tenant` = ?");
      $res = $query->execute([$value, $key, $this->id]);
    }

    return $res;
  }

  /**
   * Get the code or id
   * 
   * @return string|int code if exists, else id
   */
  public function getCodeId()
  {
    if ($this->code) {
      return mb_strtolower($this->code);
    }
    return $this->id;
  }

  /**
   * Get a tenant's UUID
   * 
   * @return string uuid
   */
  public function getUuid() {
    return $this->uuid;
  }

  /**
   * Fetch GoCardless details from the database
   * 
   * To be called only once when required
   */
  private function loadGoCardless() {
    $db = app()->db;
    $getKey = $db->prepare("SELECT OrganisationId, AccessToken FROM gcCredentials WHERE Tenant = ?");
    $getKey->execute([
      $this->id
    ]);

    $keys = $getKey->fetch(PDO::FETCH_ASSOC);

    if ($keys) {
      $this->goCardlessAccessToken = $keys['AccessToken'];
      $this->goCardlessOrganisationId = $keys['OrganisationId'];
    }
    $this->goCardlessLoaded = true;
  }

  /**
   * Get a tenant's GoCardless access token
   * 
   * @return string token
   */
  public function getGoCardlessAccessToken() {
    if (!$this->goCardlessLoaded) {
      $this->loadGoCardless();
    }
    if ($this->goCardlessAccessToken) {
      return $this->goCardlessAccessToken;
    }
    return null;
  }

  /**
   * Assign GoCardless details to a tenant
   * 
   * @param string the GC Org ID
   * @param string the GC Access Token
   */
  public function setGoCardlessAccessToken(string $orgId, string $accessToken) {
    $count = app()->db->prepare("SELECT COUNT(*) FROM gcCredentials WHERE OrganisationId = ? OR Tenant = ?");
    $count->execute([
      $orgId,
      $this->id
    ]);

    if ($count->fetchColumn() > 0) {
      throw new Exception('Exists already');
    }

    $insert = app()->db->prepare("INSERT INTO gcCredentials (OrganisationId, AccessToken, Tenant) VALUES (?, ?, ?)");
    $insert->execute([
      $orgId,
      $accessToken,
      $this->id
    ]);

    $this->goCardlessAccessToken = $accessToken;
    $this->goCardlessOrganisationId = $orgId;
  }

  /**
   * Get a tenant's GoCardless Org ID
   * 
   * @return string org id
   */
  public function getGoCardlessOrgId() {
    if (!$this->goCardlessLoaded) {
      $this->loadGoCardless();
    }
    if ($this->goCardlessOrganisationId) {
      return $this->goCardlessOrganisationId;
    }
    return null;
  }

  /**
   * Get a 
   * 
   * @return string stripe account id
   */
  public function getStripeAccount() {
    if ($this->stripeAccountId) {
      return $this->stripeAccountId;
    }

    $sid = $this->getKey('STRIPE_ACCOUNT_ID');

    if ($sid) {
      return $sid;
    }

    return null;
  }

  /**
   * Test if this club is Chester-le-Street ASC (special legacy code reasons)
   * 
   * @return boolean true if clse
   */
  public function isCLS() {
    return $this->code == 'CLSE';
  }
}
