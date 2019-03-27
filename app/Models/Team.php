<?php

namespace App\Models;

class Team {

    private $id, $name, $subgroup, $unit_id, $subgroup_id;

    public function __construct($data = []) {
        $this->id = @$data['id'];
        $this->name = @$data['name'];
        @$this->setUnitId(@$data['unit_id']);
        @$this->setSubgroupId(@$data['subgroup_id']);
    }

    /* GETS */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

   public function getUnit() {
        return $this->unit;
    }

    public function getSubgroup() {
        return $this->subgroup;
    }

    public function getUnitId() {
        return $this->unit_id;
    }

    public function getSubgroupId() {
        return $this->subgroup_id;
    }

    public function getData() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'unit' => $this->getUnit()->getData(),
            'subgroup' => $this->getSubgroup()->getData(),
            'unit_id' => $this->getUnitId(),
            'subgroup_id' => $this->getSubgroupId(),
        ];
    }

    public function getDataDiff() {
        return [
            'name' => $this->getName(),
            'idunit' => $this->getUnitId(),
            'idsubgroup' => $this->getSubgroupId(),
        ];
    }

    /* SETS */

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function setUnit(Unit $unit) {
        $this->unit = $unit;
    }

    public function setSubgroup(Subgroup $subgroup) {
        $this->subgroup = $subgroup;
    }

    public function setUnitId($unit_id) {
        $this->unit_id = $unit_id;
        if (!$this->unit) {
            $this->unit = UnitBO::get($unit_id);
        }
    }

    public function setSubgroupId($subgroup_id) {
        $this->subgroup_id = $subgroup_id;
        if (!$this->subgroup) {
            $this->subgroup = SubgroupBO::get($subgroup_id);
        }
    }

}
