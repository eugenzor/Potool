<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'potool' => __DIR__ . '/../view',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Potool\Controller\Parser' => 'Potool\Controller\ParserController',
            'Potool\Controller\Module' => 'Potool\Controller\ModuleController',
            'Potool\Controller\Language' => 'Potool\Controller\LanguageController',
        ),
    ),

    'potool' => array(
        'encoding' => 'utf-8',
        'tmp_dir' => '/tmp',
    ),

    'router' => array(
        'routes' => array(
            'potool'=>array(
                'type'=>'Segment',
                'options' => array(
                    'route'    => '/potool[/:controller[/:action[/:id[/:language]]]]',
                    'constraints' => array(
                        'controller'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language'         => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Potool\Controller',
                        'controller'=>'Module',
                        'action'=>'index'
                    ),
                ),

            ),
        ),
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'potool'=>array(
                    'type'=>'simple',
                    'options'=>array(
                        'route'=>'potool',
                        'defaults'=>array(
                            'controller'=>'Potool\Controller\Parser',
                            'action'=>'help'
                        )
                    ),
                ),
                'potool search'=>array(
                    'type'=>'simple',
                    'options'=>array(
                        'route'=>'potool search',
                        'defaults'=>array(
                            'controller'=>'Potool\Controller\Parser',
                            'action'=>'search'
                        )
                    ),
                ),
                'potool update'=>array(
                    'type'=>'simple',
                    'options'=>array(
                        'route'=>'potool search',
                        'defaults'=>array(
                            'controller'=>'Potool\Controller\Parser',
                            'action'=>'update'
                        )
                    ),
                ),
            )
        )
    ),


    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'local_navigation' => 'Potool\Navigation\Service\LocalNavigationFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'ru_RU',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),


    'navigation' => array(
        'admina' => array(
            'potool' => array(
                'label' => 'Potool',
                'route' => 'potool',
                'controller' => 'Module',
                'action' => 'index',
                'resource' => 'route/potool'
            )
        ),


        'local' => array(
            'modules' => array(
                'label' => 'Modules',
                'route' => 'potool',
                'controller' => 'Module',
                'action' => 'index'
            ),
            'languages' => array(
                'label' => 'Languages',
                'route' => 'potool',
                'controller' => 'Language',
                'action' => 'index'
            ),
            'upgrade' => array(
                'label' => 'Upgrade',
                'route' => 'potool',
                'controller' => 'Module',
                'action' => 'upgrade'
            ),
            'compile' => array(
                'label' => 'Compile',
                'route' =>'potool',
                'controller' => 'Module',
                'action' => 'compile'
            ),
            'add-language' => array(
                'label' => 'Add language',
                'route' => 'potool',
                'controller' => 'Language',
                'action' => 'add'
            ),
            'add-message' => array(
                'label' => 'Add message key',
                'route' => 'potool',
                'controller' => 'Language',
                'action' => 'add-message'
            )

        )
    ),

//    'navigation' => array(
//        'admin' => array(
//            'zfcuseradmin' => array(
//                'label' => 'Po tool',
//                'route' => 'zfcadmin/zfcuseradmin/list',
//                'pages' => array(
//                    'create' => array(
//                        'label' => 'New User',
//                        'route' => 'zfcadmin/zfcuseradmin/create',
//                    ),
//                ),
//            ),
//        ),
//    ),

);
