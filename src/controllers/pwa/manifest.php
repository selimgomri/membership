<?php

header('Content-Type: application/manifest+json');

$icons = [];

$themeColour = "#bd0000";
if (app()->tenant->getKey('SYSTEM_COLOUR')) {
  $themeColour = app()->tenant->getKey('SYSTEM_COLOUR');
}

if (app()->tenant->isCLS()) { 
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/apple-touch-icon-precomposed.png'),
    'sizes' => '57x57',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/apple-touch-icon-72x72-precomposed.png'),
    'sizes' => '72x72',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/apple-touch-icon-76x76-precomposed.png'),
    'sizes' => '76x76',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/apple-touch-icon-114x114-precomposed.png'),
    'sizes' => '114x114',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/apple-touch-icon-120x120-precomposed.png'),
    'sizes' => '120x120',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/apple-touch-icon-144x144-precomposed.png'),
    'sizes' => '144x144',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/apple-touch-icon-152x152-precomposed.png'),
    'sizes' => '152x152',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/apple-touch-icon-180x180-precomposed.png'),
    'sizes' => '180x180',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/touch-icon-192x192-precomposed.png'),
    'sizes' => '192x192',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/touch-icon-196x196.png'),
    'sizes' => '196x196',
    'type' => 'image/png'
  ];
} else {
  $icons[] = [
    'src' => autoUrl('public/img/corporate/icons/apple-touch-icon.png'),
    'sizes' => '57x57',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/corporate/icons/apple-touch-icon-72x72.png'),
    'sizes' => '72x72',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/corporate/icons/apple-touch-icon-76x76.png'),
    'sizes' => '76x76',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/corporate/icons/apple-touch-icon-114x114.png'),
    'sizes' => '114x114',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/corporate/icons/apple-touch-icon-120x120.png'),
    'sizes' => '120x120',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/corporate/icons/apple-touch-icon-144x144.png'),
    'sizes' => '144x144',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/corporate/icons/apple-touch-icon-152x152.png'),
    'sizes' => '152x152',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/corporate/icons/apple-touch-icon-180x180.png'),
    'sizes' => '180x180',
    'type' => 'image/png'
  ];
}

$data = [
  'name' => app()->tenant->getKey('CLUB_NAME') . ' Membership',
  'short_name' => 'My Club',
  'start_url' => autoUrl(''),
  'display' => 'minimal-ui',
  'background_color' => '#fff',
  'description' => 'My ' . app()->tenant->getKey('CLUB_NAME') . ' Membership',
  'icons' => $icons,
  'theme_color' => $themeColour,
  'lang' => 'en-GB',
  'scope' => autoUrl("")
];

echo json_encode($data);