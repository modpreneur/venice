<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 12.02.16
 * Time: 18:09
 */

namespace Venice\AppBundle\Services;

use Symfony\Component\Config\Definition\Exception\Exception;

//todo: will be in trinity
class GridConfigurationBuilder
{
    private $configuration = array();

    public function __construct($url, $maxEntities=1)
    {
        $this->configuration['url'] = $url;
        $this->configuration['max'] = $maxEntities;
        $this->configuration['columns'] = array();
    }

    public function addColumn($name, $label='', $allowOrder=true){
        if($this->findColumnIndex($name) == -1){
            if(strlen($label) == 0){
                $label = $name;
            }

            array_push($this->configuration['columns'],
                array(
                    'name' => $name,
                    'label' => $label,
                    'allowOrder' => $allowOrder
                )
            );
        } else {
            throw new Exception('Column with name "'.$name.'" already exists!');
        }
    }

    public function removeColumn($name){
        $index = $this->findColumnIndex($name);
        if($index != -1){
            array_splice($this->configuration['columns'], $index, 1);
        }
    }

    private function findColumnIndex($name){
        foreach($this->configuration['columns'] as $index=>$column){
            if($column['name'] == $name){
                return $index;
            }
        }
        return -1;
    }

    public function getConfiguration(){
        $tmp = $this->configuration;
        return $tmp;
    }

    public function getJSON(){
        return json_encode($this->configuration);
    }

}