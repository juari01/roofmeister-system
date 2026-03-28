<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/Content.php" );

class Project extends Content {

    public function get_list( $projects ){

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
            )

        );

        $table_projects = array(
            'header' => $header
        );

    foreach ( $projects as $project ) {

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
                )
            )
        );  


        $table_projects['body'][] = $body;
    }
    return $table_projects;
}


public function get_list_selectcustomer( $customers ){

    $header = array(
        array(
            'value' => 'Customer Name'
        ),
        array(
            'value' => 'Action'
        )
    );

    $table_customer = array(
        'header' => $header
    );

foreach ( $customers as $customer ) {

    require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );
    $customer_selectform = new Form( $form_templates['main_form'] );

    $selectcustomer_id = $customer[ 'customer_id' ];
    $selectcustomer_name = $customer[ 'name' ];

    $customer_selectform->add_element( new Element( 'button' , [
        'name'  => $selectcustomer_name,
        'value' => 'Select',
        'data_id' =>  $selectcustomer_id,
        'class' => 'label-required select-customer-by-id'
    ] ));


    $body = array(
        'data'  => array(
            array(
                'attr'  => 'customer_id',
                'value' => $customer['customer_id']
            )
        ),
        'cells' => array(
            array(
                'value' => $customer['name']
            ),
            array(
                'value' => $customer_selectform->render()
            )
        )
    );  

    $table_customer['body'][] = $body;
}
return $table_customer;
}


public function get_list_selectproperty( $properties ){

    $header = array(
        array(
            'value' => 'Property Name'
        ),
        array(
            'value' => 'Address 1'
        ),
        array(
            'value' => 'Address 2'
        ),array(
            'value' => 'City'
        ),
        array(
            'value' => 'State'
        ),array(
            'value' => 'Zip'
        ),
        array(
            'value' => 'Action'
        )
    );

    $table_property = array(
        'header' => $header
    );

foreach ( $properties as $property ) {

    require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );
    $property_selectform = new Form( $form_templates['main_form'] );

    $selectproperty_id = $property[ 'property_id' ];
    $selectproperty_name = $property[ 'name' ];

    $property_selectform->add_element( new Element( 'button' , [
        'name'  => $selectproperty_name,
        'value' => 'Select',
        'data_id' =>  $selectproperty_id,
        'class' => 'label-required select-projproperty-by-id'
    ] ));

    $body = array(
        'data'  => array(
            array(
                'attr'  => 'property_id',
                'value' => $property['property_id']
            )
        ),
        'cells' => array(
            array(
                'value' => $property['name']
            ),
            array(
                'value' => $property['address1']
            ),array(
                'value' => $property['address2']
            ),
            array(
                'value' => $property['city']
            ),
            array(
                'value' => $property['state']
            ),
            array(
                'value' => $property['zip']
            ),
            array(
                'value' => $property_selectform->render()
            )
        )
    );  


    $table_property['body'][] = $body;
}
return $table_property;

}



}




?>