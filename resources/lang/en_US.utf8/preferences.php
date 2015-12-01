<?php

return [
  'controls' =>
  [
    'select' =>
    [
      'no' => 'No',
      'yes' => 'Yes',
    ],
  ],
  'App\\Models\\Business' =>
  [
    'appointment_annulation_pre_hs' =>
    [
      'format' => 'quantity of hours',
      'help' => 'How many hours in advance for annulating appointment ?',
      'label' => 'Appointment annulation policy',
    ],
    'appointment_code_length' =>
    [
      'format' => '1234',
      'help' => 'How long should the appointment code be ?',
      'label' => 'Appointment Code Length',
    ],
    'appointment_take_today' =>
    [
      'format' => 'yes/no',
      'help' => 'Permit reservations for the same day ?',
      'label' => 'Do you take reservations for the same day the user books ?',
    ],
    'show_map' =>
    [
      'format' => 'yes/no',
      'help' => 'Display map based on your location ?',
      'label' => 'Show map',
    ],
    'show_phone' =>
    [
      'format' => 'yes/no',
      'help' => 'Show telephone ?',
      'label' => 'Show telephone number',
    ],
    'show_postal_address' =>
    [
      'format' => 'yes/no',
      'help' => 'Display postal address ?',
      'label' => 'Show your postal address',
    ],
    'start_at' =>
    [
      'format' => 'hh:mm:ss',
      'help' => 'What time does your venue open ?',
      'label' => 'Opening hour',
    ],
  ],
];
