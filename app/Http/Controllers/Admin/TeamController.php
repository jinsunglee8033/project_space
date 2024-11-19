<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Repositories\Admin\TeamRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;


use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    private $teamRepository;

    public function __construct(TeamRepository $teamRepository) // phpcs:ignore
    {
        parent::__construct();

        $this->teamRepository = $teamRepository;

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

        $this->data['teams'] = $this->teamRepository->findAll($options);

        return view('admin.teams.index', $this->data);
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
        $this->data['npd']         = null;
        $this->data['is_active']    = null;

        $this->data['npd_list'] = [
            'NO',
            'YES'
        ];

        $this->data['is_active_list'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];

        return view('admin.teams.form', $this->data);
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

        if ($this->teamRepository->create($params)) {
            return redirect('admin/teams')
                ->with('success', 'Success to create new team');
        }

        return redirect('admin/teams/create')
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
        $this->data['team'] = $this->teamRepository->findById($id);

        return view('admin.teams.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $team = $this->teamRepository->findById($id);

        $this->data['team'] = $team;

        $this->data['npd_list'] = [
            'NO',
            'YES'
        ];

        $this->data['is_active_list'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];

        $this->data['id']   = $team->id;
        $this->data['name'] = $team->name;
        $this->data['npd'] = $team->npd;
        $this->data['is_active'] = $team->is_active;

        return view('admin.teams.form', $this->data);
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
        $team = $this->teamRepository->findById($id);

        if ($this->teamRepository->update($id, $param)) {
            return redirect('admin/teams')
                ->with('success', __('users.success_updated_message', ['first_name' => $team->name]));
        }

        return redirect('admin/teams')
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
        $user = $this->teamRepository->findById($id);

        if ($this->teamRepository->delete($id)) {
            return redirect('admin/teams')
                ->with('success', __('users.success_deleted_message', ['first_name' => $user->first_name]));
        }
        return redirect('admin/teams')
                ->with('error', __('users.fail_to_delete_message', ['first_name' => $user->first_name]));
    }
}
