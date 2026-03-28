<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/Content.php" );

class Customer extends Content {

    public function get_list( $customers ){

        $header = array(
            array(
                'value' => 'Customer Name'
            )
        );

        $table_customer = array(
            'header' => $header
        );

    foreach ( $customers as $customer ) {

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
        'value' => 'Add',
        'data_id' =>  $selectproperty_id,
        'class' => 'label-required select-property-by-id'
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