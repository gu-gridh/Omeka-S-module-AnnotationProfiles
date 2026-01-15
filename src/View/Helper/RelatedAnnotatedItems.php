<?php
namespace AnnotationProfiles\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Generic extractor for reverse-related items + their annotated values.
 *
 * Options:
 *  - relation_property (required): term like "dcterms:subject"
 *  - annotation_properties (optional): array of terms; if omitted, all annotations returned
 *  - resource_type (optional): API resource type, default "items"
 *  - return_scalar (optional): if true, "related" will be the resource ID instead of the representation
 *
 * Returns:
 * [
 *   [
 *     'related' => ItemRepresentation|int,
 *     'annotations' => [
 *         'schema:pageNumber' => ['2', '5'],
 *         'schema:role' => ['editor'],
 *     ],
 *   ],
 *   ...
 * ]
 */
class RelatedAnnotatedItems extends AbstractHelper
{
    public function __invoke($item, array $options = [])
    {
        $relationProperty = $options['relation_property'] ?? null;
        if (!$relationProperty) {
            throw new \InvalidArgumentException('relatedAnnotatedItems: "relation_property" option is required.');
        }

        $annotationProps = $options['annotation_properties'] ?? null; // null = all
        $resourceType    = $options['resource_type'] ?? 'items';
        $returnScalar    = $options['return_scalar'] ?? false;

        $view = $this->getView();
        $api  = $view->api();
        $itemId = $item->id();
 
        // 1) Get IDs of all resources that reference the given item.
        $relatedList = $api->search($resourceType, [
            'property' => [[
                'property' => $relationProperty,
                'type'     => 'res',
                'text'     => $itemId,
            ]],
            'limit' => 25,
        ], ['returnScalar' => 'id'])->getContent();
 
        $ids = array_map(function($resource) {
             return $resource->id();
        }, $relatedList);
 
        if (!$ids) {
            return [];
        }
 
        $results = [];
 
        // 2) Extract matching annotated values.
        foreach ($relatedList as $related) {
            $collected = null;

        foreach ($related->value($relationProperty, ['all' => true]) as $value) {
            $valueResource = $value->valueResource();
            if (!$valueResource || $valueResource->id() !== $itemId) {
                continue;
            }

            $annotation = $value->valueAnnotation();

            $values = $annotation->values();
            foreach ($values as $term => $propertyData) {
                $propertyValues = $propertyData['values'];
                $property = $propertyData['property'];
                $propertyLabel = $propertyData['alternate_label'] ?: $property->label();
                foreach ($propertyValues as $value) {
                    $collected[$propertyLabel][] = $value->value();
                }
            }
 
        }

    if ($collected !== null) {
                $results[] = [
                    'related'     => $returnScalar ? $related->id() : $related,
                    'annotations' => $collected,
                ];
            }
        }

        return $results;
    }
}

