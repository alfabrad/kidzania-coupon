<?php
if ( !empty( $action ) )
{
    $data      = array();
    try
    {
        switch ( $action )
        {
            case 'contact':
                $data[ "first_name" ]       = stripslashes ( strip_tags( trim( $_POST[ 'first_name' ] ) ) );
                $data[ "last_name" ]        = stripslashes ( strip_tags( trim( $_POST[ 'last_name' ] ) ) );
                $data[ "email" ]            = stripslashes ( strip_tags( trim( $_POST[ 'email' ] ) ) );
                $data[ "privacy_policy" ]   = stripslashes ( strip_tags( trim( $_POST[ 'privacy_policy' ] ) ) );

                /*
                $cc = array(
                    array( 'mail'  => 'jesus.garciav@me.com', 'name'  => 'Jesús' )
                );
                */
                $cc = [];

                $rules      = [ 'first_name' => [
                                    'requerido' => 1,
                                    'validador' => 'esAlfaNumerico',
                                    'mensaje'   => utf8_encode( 'La primera pregunta es obligatoria.' )
                                ],
                                'last_name' => [
                                    'requerido' => 1,
                                    'validador' => 'esAlfaNumerico',
                                    'mensaje'   => utf8_encode( 'La segunda pregunta es obligatoria.' )
                                ],
                                'email'     => [
                                    'requerido' => 1,
                                    'validador' => 'esEmail',
                                    'mensaje'   => utf8_encode( 'La tercera pregunta es obligatoria.' )
                                ],
                                'privacy_policy' => [
                                    'requerido' => 1,
                                    'validador' => 'esAlfaNumerico',
                                    'mensaje'   => utf8_encode( 'La cuarta pregunta es obligatoria.' )
                                ]
                            ];
                $config = Common::getConfig();


                $formValidated  = new Validator( $data, $rules );
                if ( $formValidated->validate() )
                {
                    $data[ "date_answer" ]      = date( "Y-m-d H:i:s" );

                    $contact   = new Contact( $dbh, $config['database']['db_table'] );
                    $contact->setTemplate( "share.tpl" );
                    $contact->setSubject( "El Verano está en KidZania. Visítanos" );
                    $contact->setCorreo( $data[ "email" ] );
                    $contact->setCC( $cc );

                    $contact->setInfo( $data );
                    $userSaved    = $contact->insertInfo( $formValidated );

                    if ( $userSaved )
                    {
                        $response = $contact->sendEmail( );
                    }
                    else
                    {
                        $response = false;
                    }
                }
                else
                {
                    $message    = $formValidated->getMessage();
                    $contact    = [ 'response' => 'error', 'message' => $message ];
                }
                header( 'Location: ' . BASE_URL . 'gracias.html' );
                break;
            default:
                header( 'Location: ' . BASE_URL );
                break;
        }
        echo $data;
    }
    catch ( Exception $e )
    {
        switch ( $e->getCode() )
        {
            case 5910 :
                echo 'DATA BASE ERROR: '.$e->getMessage();
                $message = 'Lo sentimos, ocurrió un error inesperado al tratar de guardar los datos.';
                break;
            case 5810 :
                echo 'MAILER ERROR: '. $e->getMessage();
                $message = 'Lo sentimos, ocurrió un error inesperado al tratar de enviar el correo.';
                break;
            default : $message = $e->getMessage();
        }
        $data = [ 'response' => false , 'message' => $message ] ;
        echo json_encode( $data );
    }
}