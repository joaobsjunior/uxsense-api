<?php

namespace App\Models;

class Subgroup {

    protected $id, $name, $complement, $group, $group_id;

    public function __construct($data = []) {
        $this->id = @$data['id'];
        $this->name = @$data['name'];
        $this->complement = @$data['complement'];
        $this->group = @$data['group'];
        @$this->setGroupId(@$data['group_id']);
    }

    /* GETS */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getComplement() {
        return $this->complement;
    }

    public function getGroup() {
        return $this->group;
    }

    public function getGroupId() {
        return $this->group_id;
    }

    public function getData() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'complement' => $this->getComplement(),
            'group' => $this->getGroup()->getData(),
            'group_id' => $this->getGroupId(),
        ];
    }

    public function getDataDiff() {
        return [
            'name' => $this->getName(),
            'complement' => $this->getComplement(),
            'group_id' => $this->getGroupId(),
        ];
    }

    /* SETS */

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setComplement($complement) {
        $this->complement = $complement;
    }

    public function setGroup(Group $group) {
        $this->group = $group;
    }

    public function setGroupId($group_id) {
        $this->group_id = $group_id;
        if (!$this->getGroup() && $group_id){
            $this->setGroup(GroupBO::get($group_id));
        }
    }

}
