<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Repositories\Admin\PlantRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;


use Illuminate\Support\Facades\Hash;

class PlantController extends Controller
{
    private $plantRepository;

    public function __construct(PlantRepository $plantRepository) // phpcs:ignore
    {
        parent::__construct();

        $this->plantRepository = $plantRepository;

        $this->data['currentAdminMenu'] = 'teams';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params = $request->all();

        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'id' => 'asc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
        $this->data['q'] = !empty($params['q']) ? $params['q'] : '';

        $this->data['plants'] = $this->plantRepository->findAll($options);

        return view('admin.plants.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['plant']       = null;
        $this->data['id']           = null;
        $this->data['name']         = null;
        $this->data['is_active']    = null;

        $this->data['is_active_list'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];

        return view('admin.plants.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $request->all();

        if ($this->plantRepository->create($params)) {
            return redirect('admin/plants')
                ->with('success', 'Success to create new plant');
        }

        return redirect('admin/plants/create')
            ->with('error', 'Fail to create new plant');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->data['plant'] = $this->plantRepository->findById($id);

        return view('admin.plants.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plant = $this->plantRepository->findById($id);

        $this->data['plant'] = $plant;

        $this->data['is_active_list'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];

        $this->data['id']   = $plant->id;
        $this->data['name'] = $plant->name;
        $this->data['is_active'] = $plant->is_active;

        return view('admin.plants.form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $param = $request->all();
        $plant = $this->plantRepository->findById($id);

        if ($this->plantRepository->update($id, $param)) {
            return redirect('admin/plants')
                ->with('success', __('users.success_updated_message', ['first_name' => $plant->name]));
        }

        return redirect('admin/plants')
                ->with('error', __('users.fail_to_update_message', ['first_name' => $plant->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->plantRepository->findById($id);

        if ($this->plantRepository->delete($id)) {
            return redirect('admin/plants')
                ->with('success', __('users.success_deleted_message', ['first_name' => $user->first_name]));
        }
        return redirect('admin/plants')
                ->with('error', __('users.fail_to_delete_message', ['first_name' => $user->first_name]));
    }
}
