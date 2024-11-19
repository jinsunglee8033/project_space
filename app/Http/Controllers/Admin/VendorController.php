<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Repositories\Admin\VendorRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;


use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    private $vendorRepository;

    public function __construct(VendorRepository $vendorRepository) // phpcs:ignore
    {
        parent::__construct();

        $this->vendorRepository = $vendorRepository;

        $this->data['currentAdminMenu'] = 'vendors';
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
                'code' => 'asc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
        $this->data['q'] = !empty($params['q']) ? $params['q'] : '';

        $this->data['vendors'] = $this->vendorRepository->findAll($options);

        return view('admin.vendors.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['vendor']       = null;
        $this->data['id']           = null;
        $this->data['code']         = null;
        $this->data['name']         = null;
        $this->data['is_active']    = null;

        $this->data['is_active_list'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];

        return view('admin.vendors.form', $this->data);
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

        if ($this->vendorRepository->create($params)) {
            return redirect('admin/vendors')
                ->with('success', 'Success to create new Vendor');
        }

        return redirect('admin/vendors/create')
            ->with('error', 'Fail to create new Vendor');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->data['vendor'] = $this->userRepository->findById($id);

        return view('admin.vendors.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vendor = $this->vendorRepository->findById($id);

        $this->data['vendor'] = $vendor;

        $this->data['is_active_list'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];

        $this->data['id']   = $vendor->id;
        $this->data['code'] = $vendor->code;
        $this->data['name'] = $vendor->name;
        $this->data['is_active'] = $vendor->is_active;

        return view('admin.vendors.form', $this->data);
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
        $vendor = $this->vendorRepository->findById($id);

        if ($this->vendorRepository->update($id, $param)) {
            return redirect('admin/vendors')
                ->with('success', __('users.success_updated_message', ['first_name' => $vendor->name]));
        }

        return redirect('admin/vendors')
                ->with('error', __('users.fail_to_update_message', ['first_name' => $vendor->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->vendorRepository->findById($id);

        if ($this->vendorRepository->delete($id)) {
            return redirect('admin/vendors')
                ->with('success', __('users.success_deleted_message', ['first_name' => $user->first_name]));
        }
        return redirect('admin/vendors')
                ->with('error', __('users.fail_to_delete_message', ['first_name' => $user->first_name]));
    }
}
