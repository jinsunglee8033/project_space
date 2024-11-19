<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Repositories\Admin\BrandRepository;
use App\Repositories\Admin\TeamRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;
use App\Repositories\Admin\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $userRepository;
    private $teamRepository;

    public function __construct(UserRepository $userRepository,
                                TeamRepository $teamRepository,
                                ) // phpcs:ignore
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->teamRepository = $teamRepository;

        $this->data['currentAdminMenu'] = 'users';
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
                'created_at' => 'desc',
            ],
            'filter' => $params,
        ];

        $this->data['filter'] = $params;
        $this->data['users'] = $this->userRepository->findAll($options);

        $team_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes'
            ],
        ];
        $this->data['teams'] =$this->teamRepository->findAll($team_options);

        $this->data['functions'] = [
            'Product',
            'Marketing',
            'Design',
            'Collaboration',
            'Management',
            'Admin'
        ];

        $this->data['roles_'] = [
            'Team Lead',
            'Project Manager',
            'Task Manager',
            'Admin'
        ];

        $this->data['team'] = !empty($params['team']) ? $params['team'] : '';
        $this->data['function'] = !empty($params['function']) ? $params['function'] : '';
        $this->data['role_'] = !empty($params['role']) ? $params['role'] : '';

        return view('admin.users.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $team_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes'
            ],
        ];
        $this->data['teams'] =$this->teamRepository->findAll($team_options);

        $this->data['functions'] = [
            'Product',
            'Marketing',
            'Design',
            'Collaboration',
            'Management',
            'Admin'
        ];

        $this->data['roles_'] = [
            'Team Lead',
            'Project Manager',
            'Task Manager',
            'Admin'
        ];

        $this->data['access_levels'] = [
            'Affiliate',
            'Customer Service',
            'Ecommerce',
            'Customer Service / Ecommerce',
            'Admin',
            'Call Center',
            'IT'
        ];
        $this->data['is_active'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];
        $this->data['roleId'] = null;
        $this->data['access_level'] = null;
        $this->data['team'] = null;
        $this->data['function'] = null;
        $this->data['role_'] = null;
        $this->data['user_brand'] = null;
        $this->data['is_active_'] = null;

        return view('admin.users.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $params = $request->validated();

        $params['password'] = Hash::make($params['password']);

        if ($this->userRepository->create($params)) {
            return redirect('admin/users')
                ->with('success', __('users.success_create_message'));
        }

        return redirect('admin/users/create')
            ->with('error', __('users.fail_create_message'));
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

        return view('admin.users.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ( ($id != auth()->user()->id) && (auth()->user()->role !='Admin') ) {
            return redirect('admin/campaign')
                ->with('error', 'Could not change.');
        }

        $user = $this->userRepository->findById($id);

        $this->data['user'] = $user;
        $this->data['team'] = $user->team;
        $this->data['function'] = $user->function;
        $this->data['role_'] = $user->role;
        $this->data['user_brand'] = $user->user_brand;
        $this->data['is_active_'] = $user->is_active;
        $this->data['is_active'] = [
            'YES' => 'yes',
            'NO' => 'no',
        ];
        $team_options = [
            'order' => [
                'id' => 'asc',
            ],
            'filter' => [
                'is_active' => 'yes'
            ],
        ];
        $this->data['teams'] =$this->teamRepository->findAll($team_options);
        $this->data['functions'] = [
            'Product',
            'Marketing',
            'Design',
            'Collaboration',
            'Management',
            'Admin'
        ];

        $this->data['roles_'] = [
            'Team Lead',
            'Project Manager',
            'Task Manager',
            'Admin'
        ];
        return view('admin.users.form', $this->data);
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
        $user = $this->userRepository->findById($id);
        $param = $request->request->all();
        $log_user = auth()->user();

        if($log_user['role'] != 'Admin'){
            if($param['password'] == null){
                return redirect('admin/users/'.$id.'/edit')
                    ->with('error', 'Please enter your password to update');
            }
        }

        if (isset($param['user_brand'])) {
            $param['user_brand'] = implode(', ', $param['user_brand']);
        } else {
            $param['user_brand'] = '';
        }

        if ($this->userRepository->update($id, $param)) {
            return redirect('admin/users/'.$id.'/edit')
                ->with('success', __('users.success_updated_message', ['first_name' => $user->first_name]));
        }

        return redirect('admin/users/'.$id.'/edit')
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
