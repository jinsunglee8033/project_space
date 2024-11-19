<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Repositories\Admin\ProductSegmentRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;


use Illuminate\Support\Facades\Hash;

class ProductSegmentController extends Controller
{
    private $productSegmentRepository;

    public function __construct(ProductSegmentRepository $productSegmentRepository) // phpcs:ignore
    {
        parent::__construct();

        $this->productSegmentRepository = $productSegmentRepository;

        $this->data['currentAdminMenu'] = 'product_segment';
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

        $this->data['product_segment_list'] = $this->productSegmentRepository->findAll($options);

        return view('admin.product_segment.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['team']       = null;
        $this->data['id']           = null;
        $this->data['name']         = null;
        $this->data['is_active']    = null;

        $this->data['is_active_list'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];

        return view('admin.product_segment.form', $this->data);
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

        if ($this->productSegmentRepository->create($params)) {
            return redirect('admin/product_segment')
                ->with('success', 'Success to create new team');
        }

        return redirect('admin/product_segment/create')
            ->with('error', 'Fail to create new team');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->data['team'] = $this->productSegmentRepository->findById($id);

        return view('admin.product_segment.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rs = $this->productSegmentRepository->findById($id);

        $this->data['product_segment'] = $rs;

        $this->data['is_active_list'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];

        $this->data['id']   = $rs->id;
        $this->data['name'] = $rs->name;
        $this->data['is_active'] = $rs->is_active;

        return view('admin.product_segment.form', $this->data);
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
        $team = $this->productSegmentRepository->findById($id);

        if ($this->productSegmentRepository->update($id, $param)) {
            return redirect('admin/product_segment')
                ->with('success', __('users.success_updated_message', ['first_name' => $team->name]));
        }

        return redirect('admin/product_segment')
                ->with('error', __('users.fail_to_update_message', ['first_name' => $team->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->productSegmentRepository->findById($id);

        if ($this->productSegmentRepository->delete($id)) {
            return redirect('admin/product_segment')
                ->with('success', __('users.success_deleted_message', ['first_name' => $user->first_name]));
        }
        return redirect('admin/product_segment')
                ->with('error', __('users.fail_to_delete_message', ['first_name' => $user->first_name]));
    }
}
