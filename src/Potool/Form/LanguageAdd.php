<?php
namespace Potool\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 3/26/13
 * Time: 6:31 PM
 * To change this template use File | Settings | File Templates.
 */
class LanguageAdd extends \Zend\Form\Form
{
    function translate($key){}

    function __construct()
    {
        parent::__construct('language-add');

        $inputFilter = new InputFilter();
        $factory     = new InputFactory();

        $this->add(array(
            'name' => 'module',
            'attributes' => array(
                'type' => 'hidden',
                'name' => 'module'
            )
        ));

        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text'
            ),
            'options' => array(
                'label' => 'Language name'
            )
        ));


        $inputFilter->add($factory->createInput(array(
            'name'     => 'name',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 5,
                    ),
                ),
                array(
                    'name'    => 'Regex',
                    'options' => array(
                        'pattern' => '/^[a-zA-Z_]+$/'
                    ),
                )
            ),
        )));




        $this->add(array(
            'name' => 'add',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Add'
            )
        ));





        $this->setInputFilter($inputFilter);
    }
}
