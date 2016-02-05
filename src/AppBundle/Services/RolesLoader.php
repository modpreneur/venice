<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.02.16
 * Time: 15:14
 */

namespace AppBundle\Services;

use Symfony\Component\Yaml\Parser;

class RolesLoader
{
    public function readRolesFile()
    {
        $parser = new Parser();

        return $parser->parse(file_get_contents(__DIR__.'/../Resources/config/roles.yml'));
    }
}