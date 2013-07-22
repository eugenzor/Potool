<?php
namespace Potool\View\Helper;
use Zend\View\Helper\AbstractHelper;

class FlashMessages extends AbstractHelper
{

    protected $flashMessenger;

    public function setFlashMessenger( $flashMessenger )
    {
        $this->flashMessenger = $flashMessenger ;
    }


    public function __invoke( )
    {

//        $namespaces = array(
//            'error' ,'success',
//            'info','default'
//        );
        $namespaces = array('default');

        // messages as string
        $messageString = '';

        foreach ( $namespaces as $ns ) {

            $this->flashMessenger->setNamespace( $ns );

            $messages = array_merge(
                $this->flashMessenger->getMessages(),
                $this->flashMessenger->getCurrentMessages()
            );


            if ( ! $messages ) continue;
            if ($ns == 'default'){
                $ns = 'success';
            }
            $messageString .= "<div class='alert alert-$ns'>"
                . '<button type="button" class="close" data-dismiss="alert">&times;</button>'
                . implode( '<br />', $messages )
                . '</div>';
        }

        return $messageString ;
    }
}
