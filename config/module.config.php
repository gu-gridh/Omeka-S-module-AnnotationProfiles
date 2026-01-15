<?php
namespace AnnotationProfiles;

return [
    'service_manager' => [
        'factories' => [
            \AnnotationProfiles\Form\ConfigForm::class => \Laminas\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'relatedAnnotatedItems' => View\Helper\RelatedAnnotatedItems::class,
            'relatedAnnotatedItemsByTemplate' => View\Helper\RelatedAnnotatedItemsByTemplate::class,
        ],
    ],
];
