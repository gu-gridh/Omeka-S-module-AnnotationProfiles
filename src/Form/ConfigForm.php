<?php
namespace AnnotationProfiles\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;

class ConfigForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'profiles_json',
            'type' => Element\Textarea::class,
            'options' => [
                'label' => 'Annotation profiles (JSON)',
                'info' => 'Define annotation profiles. Example:
            {
            "persons": {
                "label": "Persons",
                "relation_property": "dcterms:subject",
                "annotation_properties": ["bibo:pages"],
                "resource_type": "items"
            }
            }',
            ],
            'attributes' => [
                'rows' => 18,
                'class' => 'setting-field',
            ],
        ]);

        $this->add([
            'name' => 'template_profiles_json',
            'type' => Element\Textarea::class,
            'options' => [
                'label' => 'Resource template → profile mapping (JSON)',
                'info' => 'Map resource template IDs to annotation profile keys. Example: {"2": "persons"}',
            ],
            'attributes' => [
                'rows' => 10,
                'class' => 'setting-field',
            ],
        ]);
    }
}