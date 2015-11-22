<?php
/*************************************************************************
 Generated via "php artisan localization:missing" at 2015/08/06 15:40:10 
*************************************************************************/

return array (
  //==================================== Translations ====================================//
  'appointments' => 
  array (
    'alert' =>
    array(
      'empty_list' => 'No tienes reserves en curso ahora.',
      'no_vacancies' => 'Lo sentimos, el prestador no puede tomar reservas al momento.',
    ),
    'btn' => 
    array (
      'book' => 'Reservar Turno',
      'book_in_biz' => 'Reservar Turno en :biz',
    ),
    'form' => 
    array (
      'btn' => 
      array (
        'submit' => 'Confirmar',
      ),
      'comments' => 
      array (
        'label' => 'Comentarios',
      ),
      'date' => 
      array (
        'label' => 'Fecha',
      ),
      'duration' => 
      array (
        'label' => 'Duración',
      ),
      'msg' => 
      array (
        'please_select_a_service' => 'Selecciona un servicio',
      ),
      'service' => 
      array (
        'label' => 'Servicio',
      ),
      'time' => 
      array (
        'label' => 'Hora',
      ),
      'timetable' => 
      array (
        'instructions' => 'Selecciona un servicio para reservar turno',
        'msg' => 
        array (
          'no_vacancies' => 'No hay disponibilidades para esta fecha',
        ),
        'title' => 'Reserva un Turno',
      ),
      'business' => 
      array (
        'label' => 'Comercio',
      ),
      'contact_id' => 
      array (
        'label' => 'Contacto',
      ),
    ),
    'index' => 
    array (
      'th' => 
      array (
        'business' => 'Prestador',
        'calendar' => 'Fecha',
        'code' => 'Código',
        'contact' => 'Cliente',
        'duration' => 'Duración',
        'finish_time' => 'Finaliza',
        'remaining' => 'Dentro de',
        'service' => 'Servicio',
        'start_time' => 'Comienza',
        'status' => 'Estado',
      ),
      'title' => 'Turnos',
    ),
  ),
  'booking' => 
  array (
    'msg' => 
    array (
      'store' => 
      array (
        'sorry_duplicated' => 'Lo sentimos, tu turno se duplica con el :code reservado anteriormente',
        'success' => '¡Tomá nota! Reservamos tu turno bajo el código :code',
      ),
      'you_are_not_suscribed_to_business' => 'Para pedir un turno debés suscribirte al prestador antes',
    ),
  ),
  'business' => 
  array (
    'btn' => 
    array (
      'suscribe' => 'Suscribir',
    ),
    'suscriptions_count' => '{0} ¡Sé el primer suscriptor! |Este prestador ya tiene :count usuario suscripto|Este prestador tiene :count usuarios suscriptos',
    'msg' => 
    array (
      'please_select_a_business' => 'Seleccioná un prestador',
    ),
  ),
  'businesses' => 
  array (
    'list' => [ 'alert' => [ 'not_found' => 'No encontramos prestador con esa dirección, seleccionalo de la lista' ] ],
    'index' => 
    array (
      'btn' => 
      array (
        'create' => 'Registrar prestador',
        'manage' => 'Mis prestadores',
        'power_create' => 'Registrá tu comercio ahora',
      ),
      'title' => 'Prestadores disponibles',
    ),
    'suscriptions' => 
    array (
      'title' => 'Suscripciones',
    ),
    'list' =>
    array(
      'alert' =>
      array(
        'not_found' => 'No podemos encontrar ese prestador, favor de escoger uno de la lista',
      ),
      'no_businesses' => 'No se econtraron prestadores.',
    ),
    'show' => 
    array (
      'btn' => 
      array (
        'book' => 'Reservar Turno',
        'change' => 'Cambiar',
      ),
    ),
  ),
  'contacts' => 
  array (
    'btn' => 
    array (
      'store' => 'Guardar',
      'update' => 'Editar',
    ),
    'create' => 
    array (
        'help' => '¡Bien hecho! Ya casi estas listo. Llena tu perfil por primera vez para que tu reserva se maneje sin consecuencia. Podrás cambiar esta información por empresa si deseas.',
      'title' => 'Mis datos',
    ),
    'msg' => 
    array (
      'store' => 
      array (
        'associated_existing_contact' => 'Se asoció tu perfil a los datos ya registrados',
        'success' => 'Guardado',
        'warning' => 
        array (
          'already_registered' => 'Se asoció tu perfil a los datos ya registrados',
          'showing_existing_contact' => 'Se asoció tu perfil a los datos ya registrados',
        ),
      ),
      'update' => 
      array (
        'success' => 'Actualizado',
      ),
    ),
  ),
  //================================== Obsolete strings ==================================//
);