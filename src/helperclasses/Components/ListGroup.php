<?php

namespace CLSASC\BootstrapComponents;

/**
 * User Class to store in session
 *
 * @copyright Chester-le-Street ASC https://github.com/Chester-le-Street-ASC
 * @author Chris Heppell https://github.com/clheppell
 */
class ListGroup {
  private $items;

  public function __construct($json) {
    $this->items = json_decode($json);
  }

  public function render($current = false) {
    $output = '';
    $listGroupClass = '';

    if (strlen($this->items->title) > 0) {
      $output .= '<div class="card">
        <div class="card-header">' . $this->items->title . '</div>';
      $listGroupClass = ' list-group-flush ';
    }

    $output .= '<div class="list-group ' . $listGroupClass . '">';

    foreach ($this->items->links as $link) {
      if ((!isset($link->exclude) && !isset($link->include)) || (isset($link->exclude) && !in_array($_SESSION['AccessLevel'], $link->exclude)) || (isset($link->include) && in_array($_SESSION['AccessLevel'], $link->include))) {
        $active = '';
        if ($link->id == $current) {
          $active = ' active ';
        }

        $target = '';
        if (strlen($link->target) > 0) {
          $target = 'target="' . $link->target . '"';
        } else {
          $target = 'target="_self"';
        }

        $title = '';
        if (strlen($link->$title) > 0) {
          $target = 'title="' . $link->title . '"';
        } else {
          $target = 'title="' . $link->name . '"';
        }

        $url = $link->link;
        if (!$link->external) {
          $url = autoUrl($link->link);
        }

        $output .= '<a href="' . $url . '" ' . $target . ' ' . $title . ' class="list-group-item list-group-item-action ' . $active . '">' . $link->name . '</a>';
      }
    }

    $output .= '</div>';

    if (strlen($this->items->title) > 0) {
      $output .= '</div>';
    }

    return $output;
  }
}
