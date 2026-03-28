<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/Content.php" );

class Appointments extends Content {

    public function get_listappointments( $appointments ){

        $header = array(
            array(
                'value' => 'Customer Name'
            ),
            array(
                'value' => 'Property Name'
            ),
            array(
                'value' => 'Project Name'
            ),array(
                'value' => 'Appointment Type'
            ),
            array(
                'value' => 'Start'
            ),
            array(
                'value' => 'End'
            ),
            array(
                'value' => 'Description'
            )

        );

        $table_appointments = array(
            'header' => $header
        );

    foreach ( $appointments as $appointment ) {

        $body = array(
            'data'  => array(
                array(
                    'attr'  => 'appointment_id',
                    'value' => $appointment['appointment_id']
                )
            ),
            'cells' => array(
                array(
                    'value' => $appointment['customer_name']
                ),
                array(
                    'value' => $appointment['property_name']
                ),array(
                    'value' => $appointment['project_name']
                ),
                array(
                    'value' => $appointment['appointment_name']
                ),
                array(
                    'value' => $appointment['start']
                ),
                array(
                    'value' => $appointment['end']
                ),
                array(
                    'value' => $appointment['description']
                )
            )
        );  


        $table_appointments['body'][] = $body;
    }
    return $table_appointments;
}


public function get_listproject( $projects ){

    $header = array(
        array(
            'value' => 'Customer Name'
        ),
        array(
            'value' => 'Property Name'
        ),
        array(
            'value' => 'Project Name'
        ),array(
            'value' => 'Description'
        ),array(
            'value' => 'Action'
        )

    );

    $table_projects = array(
        'header' => $header
    );

foreach ( $projects as $project ) {

    require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );
    $project_selectform = new Form( $form_templates['main_form'] );

    $selectproject_id = $project[ 'project_id' ];
    $selectproject_name = $project[ 'project_name' ];

    $project_selectform->add_element( new Element( 'button' , [
        'name'  => $selectproject_name,
        'value' => 'Select',
        'data_id' =>  $selectproject_id,
        'class' => 'label-required select-appproject-by-id'
    ] ));


    $body = array(
        'data'  => array(
            array(
                'attr'  => 'project_id',
                'value' => $project['project_id']
            )
        ),
        'cells' => array(
            array(
                'value' => $project['customer_name']
            ),
            array(
                'value' => $project['property_name']
            ),array(
                'value' => $project['project_name']
            ),
            array(
                'value' => $project['description']
            ),
            array(
                'value' => $project_selectform->render()
            )
        )
    );  


    $table_projects['body'][] = $body;
}
return $table_projects;
}


}


?>