# Third party libraries

The Chester-le-Street-ASC/Membership software uses third party libraries for many functions.

## Composer libraries (PHP)

Versions as at 5 Feb 2020.

For the latest list of dependencies, view [composer.json](./composer.json).

```
brick/math 0.8.11 Arbitrary-precision arithmetic library
└──php >=7.1
brick/money 0.4.4 Money and currency library
├──brick/math ~0.7.3 || ~0.8.0
│  └──php >=7.1
└──php >=7.1
brick/phonenumber 0.2.1 Phone number library
├──giggsey/libphonenumber-for-php 7.* || 8.*
│  ├──ext-mbstring *
│  ├──giggsey/locale ^1.7
│  │  └──php >=5.3.2
│  └──php >=5.3.2
└──php >=7.1
brick/postcode 0.2.1 A library to format and validate postcodes
└──php >=7.1
bshaffer/oauth2-server-php v1.11.1 OAuth2 Server for PHP
└──php >=5.3.9
clsasc/equivalent-time v1.3 A class based implementation of the British Swimming/SPORTSYSTEMS Equivalent Time Algorithm.
└──php >=5.6
clsasc/sdif-parser dev-master A class based Standard Data Interchange Format parser for swimming data files.
└──php >=7.0
dg/twitter-php v3.8 Small and easy Twitter library for PHP
├──ext-curl *
└──php >=5.4.0
dompdf/dompdf v0.8.4 DOMPDF is a CSS 2.1 compliant HTML to PDF converter
├──ext-dom *
├──ext-mbstring *
├──phenx/php-font-lib ^0.5.1
├──phenx/php-svg-lib ^0.3.3
│  └──sabberworm/php-css-parser ^8.3
│     └──php >=5.3.2
└──php ^7.1
endroid/qr-code 3.7.5 Endroid QR Code
├──bacon/bacon-qr-code ^2.0
│  ├──dasprid/enum ^1.0
│  ├──ext-iconv *
│  └──php ^7.1
├──ext-gd *
├──khanamiryan/qrcode-detector-decoder ^1.0.2
│  └──php ^5.6|^7.0
├──myclabs/php-enum ^1.5
│  ├──ext-json *
│  └──php >=7.1
├──php >=7.2
├──symfony/http-foundation ^3.4||^4.2.12||^5.0
│  ├──php ^7.2.5
│  ├──symfony/mime ^4.4|^5.0
│  │  ├──php ^7.2.5
│  │  ├──symfony/polyfill-intl-idn ^1.10
│  │  │  ├──php >=5.3.3
│  │  │  ├──symfony/polyfill-mbstring ^1.3
│  │  │  │  └──php >=5.3.3
│  │  │  └──symfony/polyfill-php72 ^1.9
│  │  │     └──php >=5.3.3
│  │  └──symfony/polyfill-mbstring ^1.0
│  │     └──php >=5.3.3
│  └──symfony/polyfill-mbstring ~1.1
│     └──php >=5.3.3
├──symfony/options-resolver ^3.4||^4.0||^5.0
│  └──php ^7.2.5
└──symfony/property-access ^3.4||^4.0||^5.0
   ├──php ^7.2.5
   └──symfony/inflector ^4.4|^5.0
      ├──php ^7.2.5
      └──symfony/polyfill-ctype ~1.8
         └──php >=5.3.3
erusev/parsedown-extra 0.7.1 An extension of Parsedown that adds support for Markdown Extra.
└──erusev/parsedown ~1.4
   ├──ext-mbstring *
   └──php >=5.3.0
fabpot/goutte v3.3.0 A simple PHP Web Scraper
├──guzzlehttp/guzzle ^6.0
│  ├──ext-json *
│  ├──guzzlehttp/promises ^1.0
│  │  └──php >=5.5.0
│  ├──guzzlehttp/psr7 ^1.6.1
│  │  ├──php >=5.4.0
│  │  ├──psr/http-message ~1.0
│  │  │  └──php >=5.3.0
│  │  └──ralouphie/getallheaders ^2.0.5 || ^3.0.0
│  │     └──php >=5.6
│  └──php >=5.5
├──php ^7.1.3
├──symfony/browser-kit ^4.4|^5.0
│  ├──php ^7.2.5
│  └──symfony/dom-crawler ^4.4|^5.0
│     ├──php ^7.2.5
│     ├──symfony/polyfill-ctype ~1.8
│     │  └──php >=5.3.3
│     └──symfony/polyfill-mbstring ~1.0
│        └──php >=5.3.3
├──symfony/css-selector ^4.4|^5.0
│  └──php ^7.2.5
└──symfony/dom-crawler ^4.4|^5.0
   ├──php ^7.2.5
   ├──symfony/polyfill-ctype ~1.8
   │  └──php >=5.3.3
   └──symfony/polyfill-mbstring ~1.0
      └──php >=5.3.3
geoip2/geoip2 v2.10.0 MaxMind GeoIP2 PHP API
├──ext-json *
├──maxmind-db/reader ~1.5
│  └──php >=5.6
├──maxmind/web-service-common ~0.6
│  ├──composer/ca-bundle ^1.0.3
│  │  ├──ext-openssl *
│  │  ├──ext-pcre *
│  │  └──php ^5.3.2 || ^7.0 || ^8.0
│  ├──ext-curl *
│  ├──ext-json *
│  └──php >=5.6
└──php >=5.6
gocardless/gocardless-pro 1.7.0 GoCardless Pro PHP Client Library
├──ext-curl *
├──ext-json *
├──ext-mbstring *
├──guzzlehttp/guzzle ^6.0
│  ├──ext-json *
│  ├──guzzlehttp/promises ^1.0
│  │  └──php >=5.5.0
│  ├──guzzlehttp/psr7 ^1.6.1
│  │  ├──php >=5.4.0
│  │  ├──psr/http-message ~1.0
│  │  │  └──php >=5.3.0
│  │  └──ralouphie/getallheaders ^2.0.5 || ^3.0.0
│  │     └──php >=5.6
│  └──php >=5.5
└──php >=5.5
guzzlehttp/guzzle 6.5.2 Guzzle is a PHP HTTP client library
├──ext-json *
├──guzzlehttp/promises ^1.0
│  └──php >=5.5.0
├──guzzlehttp/psr7 ^1.6.1
│  ├──php >=5.4.0
│  ├──psr/http-message ~1.0
│  │  └──php >=5.3.0
│  └──ralouphie/getallheaders ^2.0.5 || ^3.0.0
│     └──php >=5.6
└──php >=5.5
league/oauth2-server 7.4.0 A lightweight and powerful OAuth 2.0 authorization and resource server library with support for all the core specification grants. This library will allow you to secure your API with OAuth and allow your applications users to approve apps that want to access their data from your API.
├──defuse/php-encryption ^2.1
│  ├──ext-openssl *
│  ├──paragonie/random_compat >= 2
│  │  └──php ^7
│  └──php >=5.4.0
├──ext-openssl *
├──lcobucci/jwt ^3.2.2
│  ├──ext-mbstring *
│  ├──ext-openssl *
│  └──php ^5.6 || ^7.0
├──league/event ^2.1
│  └──php >=5.4.0
├──php >=7.0.0
└──psr/http-message ^1.0.1
   └──php >=5.3.0
nezamy/route v1.2.6 Route - Fast, flexible routing for PHP, enabling you to quickly and easily build RESTful web applications.
├──nezamy/support 1.0.*
└──php >=5.4.0
pragmarx/google2fa v4.0.2 A One Time Password Authentication package, compatible with Google Authenticator.
├──paragonie/constant_time_encoding ~1.0|~2.0
│  └──php ^7|^8
├──paragonie/random_compat >=1
│  └──php ^7
├──php >=5.4
└──symfony/polyfill-php56 ~1.2
   ├──php >=5.3.3
   └──symfony/polyfill-util ~1.0
      └──php >=5.3.3
respect/validation 1.1.31 The most awesome validation engine ever created for PHP
├──php >=5.4
└──symfony/polyfill-mbstring ^1.2
   └──php >=5.3.3
sendgrid/sendgrid 7.4.2 This library allows you to quickly and easily send emails through Twilio SendGrid using PHP.
├──ext-curl *
├──ext-json *
├──ext-mbstring *
├──ext-openssl *
├──php >=5.6
└──sendgrid/php-http-client ~3.10
   └──php >=5.6
stripe/stripe-php v6.43.1 Stripe PHP Library
├──ext-curl *
├──ext-json *
├──ext-mbstring *
└──php >=5.4.0
twilio/sdk 5.42.1 A PHP wrapper for Twilio's API
└──php >=5.5.0
umpirsky/country-list 2.0.5 List of all countries with names and ISO 3166-1 codes in all languages and data formats.
└──php ^7.0
whichbrowser/parser v2.0.41 Useragent sniffing library for PHP
├──php >=5.4.0
└──psr/cache ^1.0
   └──php >=5.3.0
```

## JS Libraries

```
Chart.js
TinyMCE
jQuery
Popper.js
Bootstrap
BigNumber.js
```

## CSS Libraries

```
Bootstrap
```