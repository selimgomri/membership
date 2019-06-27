<?php

class ParsedownForMembership extends ParsedownExtra {

  private $allHeaders;

  protected function inlineImage($excerpt) {
    $image = parent::inlineImage($excerpt);

    if (!isset($image)) {
      return null;
    }

    $image['element']['attributes']['src'] = autoUrl($image['element']['attributes']['src']);

    return $image;
  }

  protected function blockHeader($Line) {
    $Block = parent::blockHeader($Line);
    if (preg_match('/[ #]*{('.$this->regexAttribute.'+)}[ ]*$/', $Block['element']['handler']['argument'], $matches, PREG_OFFSET_CAPTURE))
    {
      $attributeString = $matches[1][0];
      $Block['element']['attributes'] = $this->parseAttributeData($attributeString);
      $Block['element']['handler']['argument'] = substr($Block['element']['handler']['argument'], 0, $matches[0][1]);
    }

    if ($this->allHeaders === null) {
      $this->allHeaders = [];
    }
    $this->allHeaders[] = $Block['element']['text'];

    return $Block;
  }

  public function getHeadings() {
    return $this->allHeaders;
  }

}