<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/Content.php" );

class Propety extends Content {

    public function get_list( $properties ){

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
            )

        );

        $table_property = array(
            'header' => $header
        );

    foreach ( $properties as $property ) {

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
                )
            )
        );  


        $table_property['body'][] = $body;
    }
    return $table_property;
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

    $selectcustomer_id   = $customer[ 'customer_id' ];
    $selectcustomer_name = $customer[ 'name' ];

    $customer_selectform->add_element( new Element( 'button' , [
        'name'  => $selectcustomer_name,
        'value' => 'Add',
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

}




?>