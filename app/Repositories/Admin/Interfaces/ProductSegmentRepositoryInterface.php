<?php

namespace App\Repositories\Admin\Interfaces;

interface ProductSegmentRepositoryInterface
{
    public function findAll($options = []);
    public function findById($id);
    public function create($params = []);
    public function update($id, $params = []);
    public function delete($id);
}
