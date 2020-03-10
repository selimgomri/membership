<?php

header('Content-Type: application/manifest+json');

$icons = [];

$themeColour = "#bd0000";
if (false) {
  $themeColour = "";
}

if (bool(env('IS_CLS'))) { 
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/touch-icon-72x72.png'),
    'sizes' => '72x72',
    'type' => 'image/png'
  ];
  $icons[] = [
    'src' => autoUrl('public/img/touchicons/touch-icon-192x192.png'),
    'sizes' => '192x192',
    'type' => 'image/png'
  ];
} else {
  $icons[] = [
    'src' => autoUrl('public/img/corporate/scds.png'),
    'sizes' => '800x800',
    'type' => 'image/png'
  ];
}

$data = [
  'name' => env('CLUB_NAME') . ' Membership',
  'short_name' => 'My Club',
  'start_url' => autoUrl('pwa'),
  'display' => 'standalone',
  'background_color' => '#fff',
  'description' => 'My ' . env('CLUB_NAME') . ' Membership',
  'icons' => $icons,
  'theme_color' => $themeColour
];

echo json_encode($data);