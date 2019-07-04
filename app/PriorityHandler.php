<?php
namespace App;

use App\Exceptions\PriorityWeightsException;

class PriorityHandler {

    public $priorities = [];
    public $ratios = [];

    public function __construct($priorities = null) {
        if ($priorities){
            $this->priorities = $priorities;
        }
        $this->calculatePriorityRatio();
        $this->checkWeights();
    }

    public function calculatePriorityRatio() {
        $max = max(array_column($this->priorities, 'max'));
        foreach ($this->priorities as $field_name => $field_value){
            $this->ratios[$field_name] = $max / $this->getMax($field_name);
        }
    }

    public function checkWeights() {
        $weights = 0;
        foreach ($this->priorities as $field => $value){
            $weights += $this->getWeight($field);
        }
        if ($weights !== (float)1){
            throw new PriorityWeightsException();
        };
    }

    public function getWeight($name) {
        return $this->priorities[$name]['weight'] ?? 0;
    }

    public function getMax($name) {
        return $this->priorities[$name]['max'] ?? 0;
    }

    public function getRatio($name) {
        return $this->ratios[$name] ?? 0;
    }

    public function getScore($data) {
        $score = 0;
        foreach ($data as $field_name => $field_value){
            if (key_exists($field_name, $this->priorities) and key_exists($field_name, $this->ratios)){
                $score += $this->getValue($field_name, $field_value) * $this->getRatio($field_name) * $this->getWeight($field_name);
            }
        }
        return $score;
    }

    public function getValue($name, $value) {
        return $value;
    }
}