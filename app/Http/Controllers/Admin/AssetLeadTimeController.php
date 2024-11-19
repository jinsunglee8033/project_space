<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;

use App\Repositories\Admin\CampaignBrandsRepository;
use App\Repositories\Admin\AssetOwnerAssetsRepository;
use App\Repositories\Admin\AssetLeadTimeRepository;

use Illuminate\Support\Facades\Hash;

class AssetLeadTimeController extends Controller
{
    private $assetOwnerAssetsRepository;
    private $campaignBrandsRepository;
    private $userRepository;


    public function __construct(AssetOwnerAssetsRepository $assetOwnerAssetsRepository,
                                AssetLeadTimeRepository $assetLeadTimeRepository,
                                CampaignBrandsRepository $campaignBrandsRepository,
                                UserRepository $userRepository) // phpcs:ignore
    {
        parent::__construct();

        $this->assetOwnerAssetsRepository = $assetOwnerAssetsRepository;
        $this->assetLeadTimeRepository = $assetLeadTimeRepository;
        $this->campaignBrandsRepository = $campaignBrandsRepository;
        $this->userRepository = $userRepository;

        $this->data['currentAdminMenu'] = 'asset_lead_time';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->data['currentAdminMenu'] = 'asset_lead_time';

        $options = [
            'order' => [
                'order_no' => 'asc',
            ],
        ];

        $this->data['assets'] = $this->assetLeadTimeRepository->findAll($options);

        return view('admin.asset_lead_time.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $this->data['brands'] = $this->assetOwnerAssetsRepository->findAll();

        return view('admin.asset_lead_time.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params['campaign_name'] = $request['campaign_name'];

        if ($this->assetOwnerAssetsRepository->create($params)) {
            return redirect('admin/asset_lead_time')
                ->with('success', 'Success to create new Brand');
        }

        return redirect('admin/asset_lead_time/create')
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
        $this->data['user'] = $this->userRepository->findById($id);

        return view('admin.asset_lead_time.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->findById($id);

        $this->data['user'] = $user;
        $this->data['team'] = $user->team;
        $this->data['role_'] = $user->role;
        $this->data['brands'] = $this->campaignBrandsRepository->findAll();
        $this->data['teams'] = [
            'KDO',
            'Brand',
            'Creative'
        ];
        $this->data['roles_'] = [
            'Admin' => 'admin',

            'Ecommerce Specialist' => 'ecommerce specialist',
            'Marketing' => 'marketing',
            'Social Media Manager' => 'social media manager',

            'Graphic Designer' => 'graphic designer',
            'Videographer' => 'videographer',

            'Creative Director' => 'creative director',

            'Copywriter' => 'copywriter'
        ];
        return view('admin.asset_lead_time.form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = $this->userRepository->findById($id);

        if ($this->userRepository->update($id, $request->validated())) {
            return redirect('admin/asset_lead_time')
                ->with('success', __('users.success_updated_message', ['first_name' => $user->first_name]));
        }

        return redirect('admin/asset_lead_time')
                ->with('error', __('users.fail_to_update_message', ['first_name' => $user->first_name]));
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
            return redirect('admin/asset_lead_time')
                ->with('error', 'Could not delete yourself.');
        }

        if ($this->userRepository->delete($id)) {
            return redirect('admin/asset_lead_time')
                ->with('success', __('users.success_deleted_message', ['first_name' => $user->first_name]));
        }
        return redirect('admin/asset_lead_time')
                ->with('error', __('users.fail_to_delete_message', ['first_name' => $user->first_name]));
    }

}
