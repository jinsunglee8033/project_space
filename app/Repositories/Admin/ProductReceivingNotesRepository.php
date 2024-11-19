<?php

namespace App\Repositories\Admin;

use App\Models\ProductReceivingNotes;
use App\Repositories\Admin\Interfaces\ProductReceivingNotesRepositoryInterface;
use DB;

use Illuminate\Database\Eloquent\Model;

class ProductReceivingNotesRepository implements ProductReceivingNotesRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];
        $id = $options['id'] ?? [];

        $productReceivingNotes = new ProductReceivingNotes();

        if ($id) {
            $productReceivingNotes = $productReceivingNotes
                ->where('task_id', $id);
        }

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $productReceivingNotes = $productReceivingNotes->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $productReceivingNotes->paginate($perPage);
        }

        $productReceivingNotes = $productReceivingNotes->get();

        return $productReceivingNotes;
    }

    public function findById($id)
    {
        return ProductReceivingNotes::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $productReceivingNotes = ProductReceivingNotes::create($params);
//            $this->syncRolesAndPermissions($params, $campaignBrand);

            return $productReceivingNotes;
        });
    }

    public function update($id, $params = [])
    {
        $productReceivingNotes = ProductReceivingNotes::findOrFail($id);

        return DB::transaction(function () use ($params, $productReceivingNotes) {
            $productReceivingNotes->update($params);

            return $productReceivingNotes;
        });
    }

    public function delete($id)
    {
        $productReceivingNotes  = ProductReceivingNotes::findOrFail($id);

        return $productReceivingNotes->delete();
    }
}
