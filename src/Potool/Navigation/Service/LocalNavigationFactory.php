<?php

namespace Potool\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;

/**
 * Factory for the Potool internal navigation
 *
 * @package    Potool
 * @subpackage Navigation\Service
 */
class LocalNavigationFactory extends DefaultNavigationFactory
{
    /**
     * @{inheritdoc}
     */
    protected function getName()
    {
        return 'local';
    }
}
