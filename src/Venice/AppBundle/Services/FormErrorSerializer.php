<?php

namespace Venice\AppBundle\Services;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

/**
 * From passed form create will create nice serialized array prepared for json response
 * Source: https://gist.github.com/Graceas/6505663
 * Hint: Using $flatArray=true and $addFormName=true, keys represents Ids of input fields (probably in call cases)
 * Class FormErrorSerializer.
 */
class FormErrorSerializer
{
    public function serializeFormErrors(
        FormInterface $form,
        $flatArray = false,
        $addFormName = false,
        $glueKeys = '_'
    ) {
        $errors = [];
        $errors['global'] = [];
        $errors['fields'] = [];

        foreach ($form->getErrors() as $error) {
            $errors['global'][] = $error->getMessage();
        }

        $errors['fields'] = $this->serialize($form);

        if ($flatArray) {
            $errors['fields'] = $this->arrayFlatten(
                $errors['fields'],
                $glueKeys,
                $addFormName ? $form->getName() : ''
            );
        }

        return $errors;
    }

    private function serialize(Form $form)
    {
        $localErrors = [];
        foreach ($form->getIterator() as $key => $child) {
            foreach ($child->getErrors() as $error) {
                $localErrors[$key] = $error->getMessage();
            }

            if (count($child->getIterator()) > 0 && ($child instanceof Form)) {
                $childErrors = $this->serialize($child);
                if (!empty($childErrors)) {
                    $localErrors[$key] = $childErrors;
                }
            }
        }

        return $localErrors;
    }

    private function arrayFlatten($array, $separator = '_', $flattenedKey = '')
    {
        $flattenedArray = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flattenedArray = \array_merge(
                    $flattenedArray,
                    $this->arrayFlatten(
                        $value,
                        $separator,
                        (strlen($flattenedKey) > 0 ? $flattenedKey.$separator : '').$key
                    )
                );
            } else {
                $flattenedArray[(strlen($flattenedKey) > 0 ? $flattenedKey.$separator : '').$key] = $value;
            }
        }

        return $flattenedArray;
    }
}
