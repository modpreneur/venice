<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 06.11.15
 * Time: 14:45
 */

namespace AppBundle\Services;


/**
* From passed form create will create nice serialized array prepared for json response
* Source: https://gist.github.com/Graceas/6505663
* Note: Small changes on lines 50 and 52
* Hint: Using $flat_array = true and $add_form_name = true, keys represents Ids of input fields (probably in call cases)
* Class FormErrorSerializer.
*/
class FormErrorSerializer
{
    public function serializeFormErrors(\Symfony\Component\Form\Form $form, $flat_array = false, $add_form_name = false, $glue_keys = '_')
    {
        $errors = array();
        $errors['global'] = array();
        $errors['fields'] = array();

        foreach ($form->getErrors() as $error) {
            $errors['global'][] = $error->getMessage();
        }

        $errors['fields'] = $this->serialize($form);

        if ($flat_array) {
            $errors['fields'] = $this->arrayFlatten($errors['fields'],
                $glue_keys, (($add_form_name) ? $form->getName() : ''));
        }

        return $errors;
    }

    private function serialize(\Symfony\Component\Form\Form $form)
    {
        $local_errors = array();
        foreach ($form->getIterator() as $key => $child) {
            foreach ($child->getErrors() as $error) {
                $local_errors[$key] = $error->getMessage();
            }

            if (count($child->getIterator()) > 0 && ($child instanceof \Symfony\Component\Form\Form)) {
                $childErrors = $this->serialize($child);
                if (!empty($childErrors)) {
                    $local_errors[$key] = $childErrors;
                }
            }
        }

        return $local_errors;
    }

    private function arrayFlatten($array, $separator = '_', $flattened_key = '')
    {
        $flattenedArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flattenedArray = array_merge($flattenedArray,
                    $this->arrayFlatten($value, $separator,
                        (strlen($flattened_key) > 0 ? $flattened_key.$separator : '').$key)
                );
            } else {
                $flattenedArray[(strlen($flattened_key) > 0 ? $flattened_key.$separator : '').$key] = $value;
            }
        }

        return $flattenedArray;
    }
}