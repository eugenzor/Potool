<?php

namespace Potool\Controller;

use Zend\Mvc\Controller\AbstractActionController;
/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 1/29/13
 * Time: 6:18 PM
 * To change this template use File | Settings | File Templates.
 */
class ParserController extends AbstractActionController
{
    function helpAction()
    {
        return 'help';
    }

    function searchAction()
    {
        return 'search';
    }

    function updateAction()
    {
        return 'update';
    }
}
