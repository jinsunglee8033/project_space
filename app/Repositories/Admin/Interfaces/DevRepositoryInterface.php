<?php

namespace App\Repositories\Admin\Interfaces;

interface DevRepositoryInterface
{
    public function findAll($options = []);
    public function findById($id);
    public function create($params = []);
    public function update($id, $params = []);
    public function delete($id);
}
