# Membership
Membership, a new project from [Chester-le-Street
ASC](https://www.chesterlestreetasc.co.uk/) aims to make swimming club
management simpler. It is available under the Apache open-source license, so you
 are free to use our code in any way possible.

This software is continuously developed in accordance with the business and
operational needs to Chester-le-Street ASC. We use a version of this software
for our day-to-day club management, though it is further specialised to our
needs and legal obligations, such as GDPR compliance.

If you install this application on your own server, you must set up the SQL
database tables yourself. You will need to modify the `config-template.php`
file to connect to your database and rename it `config.php`. A Schema is
provided for setting up your Database, though you will need to manually create
an admin, where the password is a salted hash (preferrerably BCrypt). Use the
PHP `password_hash()` functions for this.

Chester-le-Street ASC accept no liability for any issues, including all legal
issues, with this software. As mentioned, we use a slightly different version
of this software which is better suited to deployment environments.

## Features
### Automatic Member Management
The application is built on a database of club members. Members are assigned to
squads and parents can link swimmers to their account. This allows us to
automatically calculate monthly fees and other things.

### Online Gala Entries
Galas are added to the system by admins. Parents can enter their children into
swims by selecting their name, gala and swims. This cuts down on duplicated data
 from existing arrangements. Parents recieve emails detailing their entries.

### Online Attendance Records
Attendance records are online, facilitating automatic attendance calculation.
Squads are managed online and swimmer moves between squads can be scheduled in
the system.

### Notify
Notify is our E-Mail and SMS mailing list solution. Administrators can send
emails to selected groups of parents for each squad. The system is GDPR
compliant and users can opt in or out of receiving emails at any time.

### Direct Debit Payments
This application has been integrated with GoCardless and their APIs to allow
Chester-le-Street ASC to bill members by Direct Debit. The GoCardless client
library which is included in this software is copyright of GoCardless.

### Online Membership Renewal and Registration
We're able to walk parents through the annual renewal process, including
checking their details, updating details for their swimmers such as medical
information and photography permissions as well as agreeing to the club code of
conduct and terms and conditions. At the end of the process, we charge users
their ASA Fees by Direct Debit.

## Legal Notices for Third Party Libraries

This application contains third party client libraries. These are managed via
Composer. They will come with the application, so we recommend that you do not
update them via composer yourself, as this may cause issues.

### Included Packages
* bacon/bacon-qr-code                 1.0.3    BaconQrCode is a QR code
* generator for PHP. bshaffer/oauth2-server-php          v1.10.0  OAuth2 Server
* for PHP clsasc/equivalent-time              v1.1     A class based
* implementation of the British Swimming/SPORTSYSTEMS E... composer/ca-bundle
* 1.1.2    Lets you find a path to the system CA bundle, and includes a
* fallba... defuse/php-encryption               v2.2.1   Secure PHP Encryption
* Library dg/twitter-php                      v3.7     Small and easy Twitter
* library for PHP endroid/installer                   1.0.5 endroid/qr-code
* 3.4.4    Endroid QR Code fabpot/goutte                       v3.2.3   A simple
* PHP Web Scraper geoip2/geoip2                       v2.9.0   MaxMind GeoIP2
* PHP API gocardless/gocardless-pro           1.7.0    GoCardless Pro PHP Client
* Library guzzlehttp/guzzle                   6.3.3    Guzzle is a PHP HTTP
* client library guzzlehttp/promises                 v1.3.1   Guzzle promises
* library guzzlehttp/psr7                     1.4.2    PSR-7 message
* implementation that also provides common utility methods
* khanamiryan/qrcode-detector-decoder 1.0.2    QR code decoder / reader
* lcobucci/jwt                        3.2.4    A simple library to work with
* JSON Web Token and JSON Web Signature league/event
* 2.1.2    Event package league/oauth2-server                7.2.0    A
* lightweight and powerful OAuth 2.0 authorization and resource ser...
* maxmind-db/reader                   v1.3.0   MaxMind DB Reader API
* maxmind/web-service-common          v0.5.0   Internal MaxMind Web Service API
* myclabs/php-enum                    1.6.2    PHP Enum implementation
* nezamy/route                        v1.1.0   Route - Fast, flexible routing
* for PHP, enabling you to quickly and... paragonie/random_compat
* v9.99.99 PHP 5.x polyfill for random_bytes() and random_int() from PHP 7
* psr/cache                           1.0.1    Common interface for caching
* libraries psr/http-message                    1.0.1    Common interface for
* HTTP messages respect/validation                  1.1.23   The most awesome
* validation engine ever created for PHP sendgrid/php-http-client
* 3.9.6    HTTP REST client, simplified for PHP sendgrid/sendgrid
* 7.2.0    This library allows you to quickly and easily send emails through
* S... symfony/browser-kit                 v4.1.3   Symfony BrowserKit Component
* symfony/css-selector                v4.1.3   Symfony CssSelector Component
* symfony/dom-crawler                 v4.1.3   Symfony DomCrawler Component
* symfony/inflector                   v4.1.3   Symfony Inflector Component
* symfony/options-resolver            v4.1.3   Symfony OptionsResolver Component
* symfony/polyfill-ctype              v1.9.0   Symfony polyfill for ctype
* functions symfony/polyfill-mbstring           v1.9.0   Symfony polyfill for
* the Mbstring extension symfony/property-access             v4.1.3   Symfony
* PropertyAccess Component whichbrowser/parser                 v2.0.37
* Useragent sniffing library for PHP 
This product includes GeoLite2 data created by MaxMind, available from
[http://www.maxmind.com](http://www.maxmind.com).
