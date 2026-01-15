<?php
namespace AnnotationProfiles\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Helper: use annotation profile configured for the resource template.
 *
 * Uses module settings:
 *  - annotationprofiles_profiles (profiles definitions)
 *  - annotationprofiles_template_profiles (templateId -> profileKey)
 *
 * Returns same structure as RelatedAnnotatedItems, or [] if no profile.
 */
class RelatedAnnotatedItemsByTemplate extends AbstractHelper
{
    public function __invoke($item)
    {
        $view = $this->getView();

        $template = $item->resourceTemplate();
        if (!$template) {
            return [];
        }

        $templateId = $template->id();

        // Read module settings via view "setting" helper.
        $profiles         = $view->setting('annotationprofiles_profiles') ?: [];
        $templateProfiles = $view->setting('annotationprofiles_template_profiles') ?: [];

        if (empty($templateProfiles[$templateId])) {
            return [];
        }

        $profileKey = $templateProfiles[$templateId];
        if (empty($profiles[$profileKey])) {
            return [];
        }

        $profile = $profiles[$profileKey];

        if (empty($profile['relation_property'])) {
            return [];
        }

        $options = [
            'relation_property' => $profile['relation_property'],
        ];

        if (!empty($profile['annotation_properties']) && is_array($profile['annotation_properties'])) {
            $options['annotation_properties'] = $profile['annotation_properties'];
        }

        if (!empty($profile['resource_type'])) {
            $options['resource_type'] = $profile['resource_type'];
        }

        // Call the generic helper.
        return $view->relatedAnnotatedItems($item, $options);
    }
}