<?php

namespace CLSASC\SuperMailer;

/**
 * A PHP Class to extend SendGrid
 *
 * @copyright Chester-le-Street ASC https://github.com/Chester-le-Street-ASC
 * @author Chris Heppell https://github.com/clheppell
 */
class CreateMail {
  private $htmlContent;
  private $plainContent;
  private $allowUnsubscribe;
  private $name;

  function __construct() {
  }

  public function setUnsubscribable() {
    $this->allowUnsubscribe = true;
  }

  public function setForced() {
    $this->allowUnsubscribe = false;
  }

  public function showName($name = null) {
    $this->showName = true;
    $this->name = $name;
  }

  public function hideName() {
    $this->showName = false;
  }

  public function setHtmlContent($htmlContent) {
    $this->htmlContent = $htmlContent;
  }

  public function getHtmlContent() {
    return $this->htmlContent;
  }

  public function setPlainContent($plainContent) {
    $this->plainContent = $plainContent;
  }

  public function getPlainContent() {
    if ($this->plainContent != null) {
      return $this->plainContent;
    } else {
      return strip_tags($this->getHtmlContent());
    }
  }

  public function getFormattedHtml() {
    $fontStack = '"Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"';
    if ((bool(env('IS_CLS')))) {
      $fontStack = '"Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"';
    }
    $head = "
    <!DOCTYPE html>
    <html lang=\"en-gb\">
    <head>
      <meta charset=\"utf-8\">";
      if ((bool(env('IS_CLS')))) {
        $head .= "<link href=\"https://fonts.googleapis.com/css?family=Open+Sans:400,700\" rel=\"stylesheet\" type=\"text/css\">";
      } else {
        $head .= "<link href=\"https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700\" rel=\"stylesheet\" type=\"text/css\">";
      }
      $head .= "
      <style type=\"text/css\">
        html, body {
          font-family: " . $fontStack . ";
          font-size: 16px;
          background: #e3eef6;
        }

        p, h1, h2, h3, h4, h5, h6, ul, ol, img, .table, blockquote {
          margin: 0 0 16px 0;
          font-family: " . $fontStack . ";
        }

        .small {
          font-size: 11px;
          color: #868e96;
          margin-bottom: 11px;
        }

        .text-center {
          text-align: center;
        }

        .bottom {
          margin: 16px 0 0 0;
        }

        cell {
          display: table;
          background: #eee;
          padding: 1rem;
          margin 0 0 1rem 0;
          width: 100%;
        }
      </style>
    </head>
    <body>
    <div style=\"background:#e3eef6;\">
      <table style=\"width:100%;border:0px;text-align:left;padding:10px 0px 10px 0px;background:#e3eef6;\"><tr><td align=\"center\">
        <table style=\"width:100%;max-width:700px;border:0px;text-align:center;background:#ffffff;padding:10px 10px 0px 10px;\"><tr><td>";
        if (env('IS_CLS') != null) { 
        $head .= "<img src=\"" . autoUrl("public/img/notify/NotifyLogo.png") . "\"
        style=\"width:300px;max-width:100%;\" srcset=\"" .
        autoUrl("public/img/notify/NotifyLogo@2x.png") . " 2x, " .
        autoUrl("public/img/notify/NotifyLogo@3x.png") . " 3x\" alt=\"" . htmlspecialchars(env('CLUB_NAME')) . " Logo\">";
        } else {
          $head .= htmlspecialchars(env('CLUB_NAME'));
        }
        $head .= "</td></tr></table>
        <table style=\"width:100%;max-width:700px;border:0px;text-align:left;background:#ffffff;padding:0px 10px;\"><tr><td>
    ";
    if (isset($this->showName) && $this->showName && (!isset($this->name) || $this->name == null)) {
      $head .= '<p class="small text-muted">Hello -name-, </p>';
    } else if (isset($this->showName) && $this->showName && isset($this->name) && $this->name != null) {
      $head .= '<p class="small text-muted">Hello  ' . htmlspecialchars($this->name) .  ', </p>';
    }

    $foot = "</td></tr></table>
    <table style=\"width:100%;max-width:700px;border:0px;background:#f8fcff;padding:0px 10px;\"><tr><td>
    <div
    class=\"bottom text-center\">
    <p class=\"small\" align=\"center\"><strong>" . htmlspecialchars(env('CLUB_NAME')) . "</strong><br>";
    $club = json_decode(CLUB_JSON);
    for ($i = 0; $i < sizeof($club->ClubAddress); $i++) {
    $foot .= $club->ClubAddress[$i] . "<br>";
    }
    $foot .= "</p>
    <p class=\"small\" align=\"center\">This email was sent automatically by the " . env('CLUB_NAME') . " Membership System.</p>";
    if (!(bool(env('IS_CLS')))) {
    $foot .= '<p class="small" align="center">The Membership System was built by Chester-le-Street ASC.</p>';
    }
    $foot .= "<p class=\"small\" align=\"center\">Have questions? Contact us at <a
    href=\"mailto:" . $club->ClubEmails->Main . "\">" . $club->ClubEmails->Main . "</a>.</p>
    <p class=\"small\" align=\"center\">To control your email options, go to <a href=\"" .
    autoUrl("myaccount/email") . "\">My Account</a>.</p>";
    if ($this->allowUnsubscribe) {
      $foot .= '<p class="small" align="center"><a href="-unsub_link-">Click to Unsubscribe</a></p>';
    }
    $foot .= "
    <p class=\"small\" align=\"center\">&copy; " . htmlspecialchars(env('CLUB_NAME')) . " " . date("Y") . "</p>
      </div>
      </table>
    </table>
    </div>
    </body>
    </html>";

    return $head . $this->getHtmlContent() . $foot;
  }

  public function getFormattedPlain() {
    $head = "";

    if (isset($this->showName) && $this->showName && (!isset($this->name) || $this->name == null)) {
      $head .= "Hello -name-,\r\n\r\n";
    } else if (isset($this->showName) && $this->showName && isset($this->name) && $this->name != null) {
      $head .= "Hello " . htmlspecialchars($this->name) . ",\r\n\r\n";
    }

    $foot = "\r\n\n\n " . env('CLUB_NAME') . "\r\n\r\n";
    $foot .= env('CLUB_NAME') . "\r\n";
    $club = json_decode(CLUB_JSON);
    for ($i = 0; $i < sizeof($club->ClubAddress); $i++) {
    $foot .= $club->ClubAddress[$i] . "\r\n";
    }
    $foot .= "\r\nThis email was sent automatically by the " . env('CLUB_NAME') . " Membership System.\r\n\r\n";
    if (!(bool(env('IS_CLS')))) {
      $foot .= "The Membership System was built by Chester-le-Street ASC.\r\n\r\n";
    }
    $foot .= "Have questions? Contact us at " . $club->ClubEmails->Main . ".\r\n\r\n";
    $foot .= "To control your email options go to My Account at " . autoUrl("myaccount") . ".\r\n\r\n";
    if ($this->allowUnsubscribe) {
      $foot .= "Unsubscribe at -unsub_link-\r\n\r\n";
    }
    $foot .= "Copyright " . env('CLUB_NAME');

    return $head . $this->getPlainContent() . $foot;
  }
}
