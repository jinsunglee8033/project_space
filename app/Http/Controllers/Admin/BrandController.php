<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Repositories\Admin\BrandRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;

use App\Repositories\Admin\CampaignBrandsRepository;

use Illuminate\Support\Facades\Hash;

class BrandController extends Controller
{
    private $brandsRepository;

    public function __construct(BrandRepository $brandsRepository) // phpcs:ignore
    {
        parent::__construct();

        $this->brandsRepository = $brandsRepository;

        $this->data['currentAdminMenu'] = 'brands';
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
                'name' => 'asc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
        $this->data['q'] = !empty($params['q']) ? $params['q'] : '';

        $this->data['brands'] = $this->brandsRepository->findAll($options);

        return view('admin.brands.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['brand']       = null;
        $this->data['id']           = null;
        $this->data['name']         = null;
        $this->data['is_active']    = null;

        $this->data['is_active_list'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];

        return view('admin.brands.form', $this->data);
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

        if ($this->brandsRepository->create($params)) {
            return redirect('admin/brands')
                ->with('success', 'Success to create new Brand');
        }

        return redirect('admin/brands/create')
            ->with('error', 'Fail to create new Brand');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->data['brand'] = $this->userRepository->findById($id);

        return view('admin.brands.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = $this->brandsRepository->findById($id);

        $this->data['brand'] = $brand;

        $this->data['is_active_list'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];

        $this->data['id']   = $brand->id;
        $this->data['name'] = $brand->name;
        $this->data['is_active'] = $brand->is_active;

        return view('admin.brands.form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(request $request, $id)
    {
        $param = $request->all();
        $brand = $this->brandsRepository->findById($id);

        if ($this->brandsRepository->update($id, $param)) {
            return redirect('admin/brands')
                ->with('success', __('users.success_updated_message', ['first_name' => $brand->name]));
        }

        return redirect('admin/brands')
            ->with('error', __('users.fail_to_update_message', ['first_name' => $brand->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->userRepository->findById($id);

        if ($user->id == auth()->user()->id) {
            return redirect('admin/users')
                ->with('error', 'Could not delete yourself.');
        }

        if ($this->userRepository->delete($id)) {
            return redirect('admin/users')
                ->with('success', __('users.success_deleted_message', ['first_name' => $user->first_name]));
        }
        return redirect('admin/users')
                ->with('error', __('users.fail_to_delete_message', ['first_name' => $user->first_name]));
    }
}
