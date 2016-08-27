<?php

namespace Venice\AppBundle\Services;

use Monolog\Processor\WebProcessor;

/**
 * Class RequestProcessor.
 */
class RequestProcessor extends WebProcessor
{
    public function processRecord(array $record)
    {
        $record['extra']['serverData'] = '';

        foreach ($_SERVER as $key => $value) {
            if (is_array($value)) {
                $value = print_r($value, true);
            }

            $record['extra']['serverData'] .= $key.': '.$value."\n";
        }

        return $record;
    }
}
