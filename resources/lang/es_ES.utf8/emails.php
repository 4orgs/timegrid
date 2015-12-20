<?php

return [
  'user' => [
    'welcome'     => ['subject' => 'Bienvenido a timegrid.io'],
    'appointment' => [
      'reserved'  => ['subject' => 'Información de tu reserva'],
      'confirmed' => ['subject' => 'Tu cita en :business fue confirmada'],
    ],
  ],
  'manager' => [
    'appointment' => [
      'reserved' => ['subject' => 'Te hicieron una reserva'],
    ],
    'business' => [
      'report' => ['subject' => ':date Agenda de :business'],
    ],
  ],
];
