<?php

namespace App\Repositories\Admin;

use Carbon\Carbon;
use DB;

use App\Repositories\Admin\Interfaces\UserRepositoryInterface;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

//use App\Models\Role;

class UserRepository implements UserRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $users = new User();
//        $users = (new User())->with('roles');

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $users = $users->orderBy($field, $sort);
            }
        }

        if (!empty($options['filter']['team'])) {
            $users = $users->Where('team', '=', "{$options['filter']['team']}");
        }

        if (!empty($options['filter']['role'])) {
            $users = $users->Where('role', '=', "{$options['filter']['role']}");
        }

        if (!empty($options['filter']['function'])) {
            $users = $users->Where('function', '=', "{$options['filter']['function']}");
        }

        if (!empty($options['filter']['q'])) {
            $users = $users->Where('first_name', 'LIKE', "%{$options['filter']['q']}%")
                ->orWhere('last_name', 'LIKE', "%{$options['filter']['q']}%")
                ->orWhere('email', 'LIKE', "%{$options['filter']['q']}%");
        }

        if ($perPage) {
            return $users->paginate($perPage);
        }

        return $users->get();
    }

    public function findById($id)
    {
        return User::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $user = User::create($params);
//            $this->syncRolesAndPermissions($params, $user);

            return $user;
        });
    }

    public function update($id, $params = [])
    {
        $user = User::findOrFail($id);

        if (!isset($params['password'])) {
            unset($params['password']);
        }

        return DB::transaction(function () use ($params, $user) {

            if(isset($params['password'])){
                $params['password'] = Hash::make($params['password']);
            }
            $user->update($params);
//            $this->syncRolesAndPermissions($params, $user);

            return $user;
        });
    }

    public function delete($id)
    {
        $user  = User::findOrFail($id);

        return $user->delete();
    }

    public function findByBrandName($brand_name)
    {
        $users = new User();
        $users = $users->Where('user_brand', 'LIKE', "%$brand_name%");
        return $users->get();
    }

    public function getEmailByDesignerName($first_name)
    {
        $users = new User();
        $users = $users->Where('first_name', '=', "$first_name")->Where('role', '=', 'graphic designer');
        return $users->get();
    }

    public function getEmailByCopyWriterName($first_name)
    {
        $users = new User();
        $users = $users->Where('first_name', '=', "$first_name")
            ->WhereIn('role', array('copywriter', 'copywriter manager'));
        return $users->get();
    }

    public function getJoahDirector()
    {
        $users = new User();
        $users = $users->Where('role', '=', "creative director")->Where('user_brand', 'LIKE', "%JOAH Beauty%");
        return $users->get();
    }

    public function getCreativeDirector()
    {
        $users = new User();
        $users = $users->Where('role', '=', "creative director")->Where('user_brand', 'NOT LIKE', "%JOAH Beauty%");
        return $users->get();
    }

    public function getContentManager()
    {
        $users = new User();
        $users = $users->Where('role', '=', "content manager");
        return $users->get();
    }

    public function getWebProductionManager()
    {
        $users = new User();
        $users = $users->Where('role', '=', "web production manager");
        return $users->get();
    }

    public function getWriterByBrandName($brand_name)
    {
        $users = new User();
        $users = $users->Where('role', '=', "copywriter")->Where('user_brand', 'LIKE', "%$brand_name%");
        return $users->get();
    }

    public function getCopyWriterManager()
    {
        $users = new User();
        $users = $users->Where('role', '=', "copywriter manager");
        return $users->get();
    }

    public function getCopywriterByFirstName($first_name)
    {
        $users = new User();
        $users = $users->Where('first_name', '=', "$first_name")
            ->WhereIn('role', array('copywriter', 'copywriter manager'));
        return $users->get();
    }

    public function getAllCopyWriters()
    {
        return DB::select('
            select * from users where role in ("copywriter", "copywriter manager") order by first_name desc
        ');
    }

    public function getBrandsAssignedWriters()
    {
        return DB::select('
            select * from users where role ="copywriter" order by char_length(user_brand) desc
        ');
    }

    public function getDesignerByFirstName($first_name)
    {
        $users = new User();
        $users = $users->Where('first_name', '=', "$first_name")
            ->WhereIn('role', array('graphic designer', 'creative director'));
        return $users->get();
    }

    public function getContentByFirstName($first_name)
    {
        $users = new User();
        $users = $users->Where('first_name', '=', "$first_name")
            ->WhereIn('role', array('content creator', 'content manager'));
        return $users->get();
    }

    public function getWebByFirstName($first_name)
    {
        $users = new User();
        $users = $users->Where('first_name', '=', "$first_name")
            ->WhereIn('role', array('web production', 'web production manager'));
        return $users->get();
    }

    public function getKissUsers()
    {
        $users = new User();
        $users = $users->Where('is_active', '=' ,'yes')->get();

        $names = [];

        foreach ($users as $user){
            $full_name = $user['first_name'].' '.$user['last_name'];
            $names[$full_name] = $user['email'];
        }

        return $names;

    }

    public static function getWritersNameByBrand($brand)
    {
        return DB::select('
            select * from users where role = "copywriter" and user_brand like "%'.$brand.'%"
        ');
    }

    public static function getAssetOwnerNameById($id)
    {
        return DB::select('
            select first_name from users where id ='.$id.'
        ');
    }

    public function getDeveloperManager()
    {
        $users = new User();
        $users = $users->Where('role', '=', "developer manager");
        return $users->get();
    }

    public function getDeveloperAssignee()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('role', array('developer', 'developer manager'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getCreativeAssignee()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('role', array('graphic designer', 'creative director'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getContentAssignee()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('role', array('content creator', 'content manager'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getCopyWriterAssignee()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('role', array('copywriter', 'copywriter manager'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getWebAssignee()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('role', array('web production', 'web production manager'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getMKTGroupByBrand($brand_name)
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->Where('team', '=', "Global Marketing")
            ->Where('user_brand', 'LIKE', "%$brand_name%")
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getAdminGroup()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->Where('role', '=', "admin");
        return $users->get();
    }

    public function getAssetOwners()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('KDO', 'Omni Channel Sales'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getMmSecondAssigneeList()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('MDM'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getNpdPoBuyerList()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Purchasing'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getPeAndDisplayAssigneeList()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('PE (D&P)', 'Display (D&P)'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getPeAssigneeList()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('PE (D&P)'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getDisplayAssigneeList()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Display (D&P)'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }


    public function getLegalAssigneeList()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Legal'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getRaAssigneeList()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Legal RA'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }


    public function getPoBuyerList()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Purchasing'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getDesignerListKissNail()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Kiss Nail (ND)'))
            ->Where('function', '=', 'Design')
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getDesignerListKissLash()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Kiss Lash (LD)'))
            ->Where('function', '=', 'Design')
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getDesignerListKissHair()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Kiss Hair Care (C&H)', 'Kiss A&A (Red)'))
            ->Where('function', '=', 'Design')
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getDesignerListOthers()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Brand Design'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getDesignerListProductionDesign()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Production Design'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getDesignerListIndustrialDesign()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Industrial Design'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getAssigneeListRedMarketing()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('Red Trade Marketing (A&A)'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getAssigneeListIvyMarketing()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('B2B Marketing'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getAssigneeListKissMarketing()
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', array('CSS'))
            ->orderBy('first_name', 'asc');
        return $users->get();
    }

    public function getPageAccess($user)
    {
        if($user->role == 'Team Lead'){
            if($user->function == 'Management'){ // 본부장님 (자기 디비전꺼 모두 보기) (management)
                $manage_team = $user->team;
                $team_aa_group = ['Kiss A&A (Red)',
                    'Red Appliance (A&A)',
                    'Red Accessory & Jewelry (A&A)',
                    'Red Fashion & Hair Cap (A&A)',
                    'Red Brush & Implement (A&A)',
                    'Red Trade Marketing (A&A)',
                ];
                $team_ch_group = ['Kiss Hair Care (C&H)',
                    'Ivy Cosmetic (C&H)',
                    'Ivy Hair Care (C&H)'
                ];
                $team_ld_group = [
                    'Kiss Lash (LD)',
                    'Ivy Lash (LD)'
                ];
                $team_nd_group = ['Kiss Nail (ND)'];
                if(in_array($manage_team, $team_aa_group)){
                    $string = "'" . implode("', '", $team_aa_group) . "'";
                    $cur_user = ' and p.team in (' . $string . ') ';
                }else if(in_array($manage_team, $team_ch_group)){
                    $string = "'" . implode("', '", $team_ch_group) . "'";
                    $cur_user = ' and p.team in (' . $string . ') ';
                }else if(in_array($manage_team, $team_ld_group)){
                    $string = "'" . implode("', '", $team_ld_group) . "'";
                    $cur_user = ' and p.team in (' . $string . ') ';
                }else if(in_array($manage_team, $team_nd_group)){
                    $string = "'" . implode("', '", $team_nd_group) . "'";
                    $cur_user = ' and p.team in (' . $string . ') ';
                }
            }else if($user->function == 'Product' || $user->function == 'Marketing'){ // 디비전 팀리드 (자기팀꺼 모두 보기) (division_team_lead)
                $cur_user = ' and p.team ="' . $user->team . '" ';
            }else{
                $cur_user = ' and i.author_id ="' . $user->id . '" '; // Product 아닌 팀리드 (non_division_team_lead)
            }
        }else if($user->role == 'Project Manager'){ // 디비전 Project Manager (division_project_manager)
            $cur_user = ' and i.author_id ="' . $user->id . '" ';
        }else{
            $cur_user = ' and i.author_id ="' . $user->id . '" '; // 디비전 사람 아님 (others)
        }
        return $cur_user;
    }

    public function getAccessTeamArray($user)
    {
        if($user->role == 'Team Lead'){
            if($user->function == 'Management'){ // 본부장님 (자기 디비전꺼 모두 보기) (management)
                $manage_team = $user->team;
                $team_aa_group = ['Kiss A&A (Red)',
                    'Red Appliance (A&A)',
                    'Red Accessory & Jewelry (A&A)',
                    'Red Fashion & Hair Cap (A&A)',
                    'Red Brush & Implement (A&A)',
                    'Red Trade Marketing (A&A)',
                ];
                $team_ch_group = ['Kiss Hair Care (C&H)',
                    'Ivy Cosmetic (C&H)',
                    'Ivy Hair Care (C&H)'
                ];
                $team_ld_group = [
                    'Kiss Lash (LD)',
                    'Ivy Lash (LD)'
                ];
                $team_nd_group = ['Kiss Nail (ND)'];
                if(in_array($manage_team, $team_aa_group)){
                    $string = "'" . implode("', '", $team_aa_group) . "'";
                    $cur_user = ' and p.team in (' . $string . ') ';
                }else if(in_array($manage_team, $team_ch_group)){
                    $string = "'" . implode("', '", $team_ch_group) . "'";
                    $cur_user = ' and p.team in (' . $string . ') ';
                }else if(in_array($manage_team, $team_ld_group)){
                    $string = "'" . implode("', '", $team_ld_group) . "'";
                    $cur_user = ' and p.team in (' . $string . ') ';
                }else if(in_array($manage_team, $team_nd_group)){
                    $string = "'" . implode("', '", $team_nd_group) . "'";
                    $cur_user = ' and p.team in (' . $string . ') ';
                }
            }else if($user->function == 'Product' || $user->function == 'Marketing'){ // 디비전 팀리드 (자기팀꺼 모두 보기) (division_team_lead)
                $cur_user = ' and p.team ="' . $user->team . '" ';

            }else{
                $cur_user = ' and i.author_id ="' . $user->id . '" '; // Product 아닌 팀리드 (non_division_team_lead)
            }
        }else if($user->role == 'Project Manager'){ // 디비전 Project Manager (division_project_manager)
            $cur_user = ' and i.author_id ="' . $user->id . '" ';
        }else{
            $cur_user = ' and i.author_id ="' . $user->id . '" '; // 디비전 사람 아님 (others)
        }
        return $cur_user;
    }

    function user_array_for_access($user)
    {
        if($user->role == 'Team Lead'){
            if($user->function == 'Management'){ // 본부장님 (자기 디비전꺼 모두 보기) (management)
                $manage_team = $user->team;
                $team_aa_group = ['Kiss A&A (Red)',
                    'Red Appliance (A&A)',
                    'Red Accessory & Jewelry (A&A)',
                    'Red Fashion & Hair Cap (A&A)',
                    'Red Brush & Implement (A&A)',
                    'Red Trade Marketing (A&A)',
                ];
                $team_ch_group = ['Kiss Hair Care (C&H)',
                    'Ivy Cosmetic (C&H)',
                    'Ivy Hair Care (C&H)'
                ];
                $team_ld_group = [
                    'Kiss Lash (LD)',
                    'Ivy Lash (LD)'
                ];
                $team_nd_group = ['Kiss Nail (ND)'];
                if(in_array($manage_team, $team_aa_group)){
                    $cur_user = $this->get_users_by_teams($team_aa_group);
                }else if(in_array($manage_team, $team_ch_group)){
                    $cur_user = $this->get_users_by_teams($team_ch_group);
                }else if(in_array($manage_team, $team_ld_group)){
                    $cur_user = $this->get_users_by_teams($team_ld_group);
                }else if(in_array($manage_team, $team_nd_group)){
                    $cur_user = $this->get_users_by_teams($team_nd_group);
                }
            }else if($user->function == 'Product' || $user->function == 'Marketing'){ // 디비전 팀리드 (자기팀꺼 모두 보기) (division_team_lead)
                $cur_user = $this->get_users_by_teams([$user->team]);
            }else{
                $cur_user[] = $user->id; // Product 아닌 팀리드 (non_division_team_lead)
            }
        }else if($user->role == 'Project Manager'){ // 디비전 Project Manager (division_project_manager)
            $cur_user[] = $user->id;
        }else{
            $cur_user[] = $user->id; // 디비전 사람 아님 (others)
        }
        return $cur_user;
    }

    function get_users_by_teams($user_team)
    {
        $users = new User();
        $users = $users
            ->Select('id')
            ->Where('is_active', '=', 'yes')
            ->WhereIn('team', $user_team);
        return $users->get();
    }

    function get_receiver_emails_by_team($team)
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->Where('team', '=', $team);
        return $users->get();
    }

    function get_product_team_lead_emails_by_team($team)
    {
        $users = new User();
        $users = $users
            ->Where('is_active', '=', 'yes')
            ->Where('team', '=', $team)
            ->Where('function', '=', 'Product')
            ->Where('role', '=', 'Team Lead');
        return $users->get();
    }

//    /**
//     * Sync roles and permissions
//     *
//     * @param Request $request
//     * @param $user
//     * @return string
//     */
//    private function syncRolesAndPermissions($params, $user)
//    {
//        // Get the submitted roles
//        $roles = isset($params['role_id']) ? [$params['role_id']] : [];
//        $permissions = isset($params['permissions']) ? $params['permissions'] : [];
//
//        // Get the roles
//        $roles = Role::find($roles);
//
//        // check for current role changes
//        if (!$user->hasAllRoles($roles)) {
//            // reset all direct permissions for user
//            $user->permissions()->sync([]);
//        } else {
//            // handle permissions
//            $user->syncPermissions($permissions);
//        }
//
//        $user->syncRoles($roles);
//
//        return $user;
//    }
}
