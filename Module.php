<?php
namespace AnnotationProfiles;

use Omeka\Module\AbstractModule;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\Form\Form;
use Laminas\Form\Element;

class Module extends AbstractModule
{
    const SETTINGS_PROFILES = 'annotationprofiles_profiles';
    const SETTINGS_TEMPLATE_PROFILES = 'annotationprofiles_template_profiles';

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Laminas\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src',
                ],
            ],
        ];
    }

    /**
     * Admin module configuration form (simple: two JSON textareas).
     */
    public function getConfigForm(PhpRenderer $renderer)
    {
        $services = $this->getServiceLocator();
        $settings = $services->get('Omeka\Settings');

        $form = $services->get(\AnnotationProfiles\Form\ConfigForm::class);
        $form->init();

        // populate the form with existing settings
        $form->get('profiles_json')->setValue(
            json_encode($settings->get(self::SETTINGS_PROFILES, []), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $form->get('template_profiles_json')->setValue(
            json_encode($settings->get(self::SETTINGS_TEMPLATE_PROFILES, []), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        // Omeka handles CSRF and markup
        return $renderer->formCollection($form);
    }

    /**
     * Save module config.
     */
    public function handleConfigForm(AbstractController $controller)
    {
        $services = $this->getServiceLocator();
        $form = $services->get(\AnnotationProfiles\Form\ConfigForm::class);
        $form->init();

        $data = $controller->getRequest()->getPost()->toArray();
        $form->setData($data);

        if (!$form->isValid()) {
            $controller->messenger()->addError('Invalid form submission.');
            return;
        }

        $data = $form->getData();

        $decode = function ($json) {
            $array = json_decode($json, true);
            return is_array($array) ? $array : [];
        };

        $settings = $services->get('Omeka\Settings');
        $settings->set(self::SETTINGS_PROFILES, $decode($data['profiles_json']));
        $settings->set(self::SETTINGS_TEMPLATE_PROFILES, $decode($data['template_profiles_json']));

        $controller->messenger()->addSuccess('Annotation profile settings saved.');
    }
}
