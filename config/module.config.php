<?php
return array(
    'sds' => array(
        'user' => array(
            'enableAccessControl' => false,
            'documentManager' => 'doctrine.documentmanager.odm_default',
            'serializer' => 'sds.doctrineExtensions.serializer',
            'validator' => 'sds.doctrineExtensions.validator',
            'userClass' => 'Sds\UserModule\DataModel\User',
        ),
        'doctrineExtensions' => array(
            'extensionConfigs' => array(
                'Sds\DoctrineExtensions\Readonly' => null,
                'Sds\DoctrineExtensions\Serializer' => null,
                'Sds\DoctrineExtensions\Validator' => new Sds\DoctrineExtensions\Validator\ExtensionConfig(['validateOnFlush' => false]),
                'Sds\DoctrineExtensions\Crypt' => null,
                'Sds\DoctrineExtensions\DojoModel' => array(
                    'destPaths' => array(
                        'all' => array(
                            'filter' => 'Sds\UserModule',
                            'path' => 'vendor/dojo'
                        ),
                    ),
                ),
            ),
        ),
        'accessControl' => array(
            'controllers' => array(
                'user' => array(
                    'actions' => array(
                        'recoverPassword' => array(
                            'roles' => array(
                                \Sds\Common\AccessControl\Constant\Role::guest
                            ),
                        ),
                    ),
                ),
                'userRest' => array(
                    'actions' => array(
                        'create' => array(
                            'roles' => array(
                                \Sds\Common\AccessControl\Constant\Role::guest
                            ),
                        ),
                        'read' => array(
                            'roles' => array(
                                \Sds\Common\AccessControl\Constant\Role::guest
                            ),
                        ),
                        'update' => array(
                            'roles' => array(
                                \Sds\Common\AccessControl\Constant\Role::guest
                            ),
                        ),
                        'delete' => array(
                            'roles' => array(
                                \Sds\Common\AccessControl\Constant\Role::guest
                            ),
                        ),
                    ),
                ),
            ),
        ),

        //The path to place smd generated by the generate-json-rpc-smd command line tool
        'jsonRpcSmdGenerator' => array(
            'sds.user' => array(
                'path' => 'vendor/dojo/Sds/UserModule/Smd.js',
                'format' => 'dojo', // dojo | json
                'target' => 'http://localhost/ZendSkeletonApplication/user', //Override this target in your own config
            ),
        ),
    ),

    'router' => array(
        'routes' => array(
            'sds.user' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/user',
                    'defaults' => array(
                        'controller' => 'sds.user',
                    ),
                ),
            ),
            'sds.userRest' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/user/rest',
                    'defaults' => array(
                        'controller' => 'sds.userRest',
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'factories' => array(
            'sds.user' => 'Sds\UserModule\Service\UserControllerFactory',
            'sds.userRest' => 'Sds\UserModule\Service\UserRestControllerFactory'
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'Sds\UserModule\DataModel' => 'sds.user'
                ),
            ),
            'sds.user' => array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'paths' => array(
                    'vendor/superdweebie/user-module/src/Sds/UserModule/DataModel'
                ),
            ),
        ),
    ),
);
