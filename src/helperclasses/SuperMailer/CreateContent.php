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
    $this->plainContent = html_entity_decode($plainContent);
  }

  public function getPlainContent() {
    if ($this->plainContent != null) {
      return $this->plainContent;
    } else {
      return html_entity_decode(strip_tags($this->getHtmlContent()));
    }
  }

  public function getFormattedHtml() {
    $fontStack = '"Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"';
    if ((app()->tenant->isCLS())) {
      $fontStack = '"Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"';
    }
    $head = "
    <!DOCTYPE html>
    <html lang=\"en-gb\">
    <head>
      <meta charset=\"utf-8\">";
      if ((app()->tenant->isCLS())) {
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
        if (app()->tenant->isCLS()) { 
        $head .= "<img src=\"" . autoUrl("public/img/notify/NotifyLogo.png") . "\"
        style=\"width:300px;max-width:100%;\" srcset=\"" .
        autoUrl("public/img/notify/NotifyLogo@2x.png") . " 2x, " .
        autoUrl("public/img/notify/NotifyLogo@3x.png") . " 3x\" alt=\"" . htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) . " Logo\">";
        } else if (isset(app()->tenant) && $logos = app()->tenant->getKey('LOGO_DIR')) {
          $head .= "<img src=\"" . autoUrl($logos . 'logo-150.png') . "\" srcset=\"" .
          autoUrl($logos . 'logo-150@2x.png') . " 2x, " .
          autoUrl($logos . 'logo-150@3x.png') . " 3x\" style=\"max-width:100%;max-height:150px;\" alt=\"" . htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) . " Logo\">";
        } else if (isset(app()->tenant)) {
          $head .= htmlspecialchars(app()->tenant->getKey('CLUB_NAME'));
        } else {
          $head .= 'SCDS Membership MT';
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
    class=\"bottom text-center\">";
    if (isset(app()->tenant)) {
      $foot .= "
      <p class=\"small\" align=\"center\"><strong>" . htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) . "</strong><br>";
      $addr = json_decode(app()->tenant->getKey('CLUB_ADDRESS'));
      if ($addr) {
        for ($i = 0; $i < sizeof($addr); $i++) {
          $foot .= htmlspecialchars($addr[$i]) . '<br>';
          if (isset($addr[$i+1]) && $addr[$i+1] == "") {
            break;
          }
        }
      }
    } else {
      $foot .= "<p class=\"small\" align=\"center\">SCDS Membership MT";
    }
    $foot .= "</p>";
    if (isset(app()->tenant)) {
    $foot .= "
    <p class=\"small\" align=\"center\">This email was sent via the " . htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) . " Membership System.</p>";
    $foot .= "<p class=\"small\" align=\"center\">Have questions? Contact us at <a
    href=\"mailto:" . htmlspecialchars(app()->tenant->getKey('CLUB_EMAIL')) . "\">" . htmlspecialchars(app()->tenant->getKey('CLUB_EMAIL')) . "</a>.</p>
    <p class=\"small\" align=\"center\">To control your email options, go to <a href=\"" .
    autoUrl("myaccount/email") . "\">My Account</a>.</p>";
    if ($this->allowUnsubscribe) {
      $foot .= '<p class="small" align="center"><a href="-unsub_link-">Click to Unsubscribe</a></p>';
    }
    $foot .= "
    <p class=\"small\" align=\"center\">&copy; " . htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) . " " . date("Y") . ", Design &copy; SCDS</p>";
    }
    $foot .= "
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

    if (isset(app()->tenant)) {
      $foot = "\r\n\n\n " . app()->tenant->getKey('CLUB_NAME') . "\r\n\r\n";
      $foot .= app()->tenant->getKey('CLUB_NAME') . "\r\n";
      $addr = json_decode(app()->tenant->getKey('CLUB_ADDRESS'));
      if ($addr) {
        for ($i = 0; $i < sizeof($addr); $i++) {
          $foot .= $addr[$i] . "\r\n";
          if (isset($addr[$i+1]) && $addr[$i+1] == "") {
            break;
          }
        }
      }
      $foot .= "\r\nThis email was sent automatically by the " . app()->tenant->getKey('CLUB_NAME') . " Membership System.\r\n\r\n";
      $foot .= "Have questions? Contact us at " . app()->tenant->getKey('CLUB_EMAIL') . ".\r\n\r\n";
      $foot .= "To control your email options go to My Account at " . autoUrl("myaccount") . ".\r\n\r\n";
      if ($this->allowUnsubscribe) {
        $foot .= "Unsubscribe at -unsub_link-\r\n\r\n";
      }
      $foot .= "Content copyright " . date("Y") . " " . app()->tenant->getKey('CLUB_NAME') . ", Design copyright SCDS";
    } else {
      $foot = "Copyright SCDS. Membership MT.";
    }

    return $head . $this->getPlainContent() . $foot;
  }
}
