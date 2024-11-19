<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Admin\QrCodeRequest;
use App\Models\CampaignTypeAssetAttachments;
use App\Models\FormQrCode;
use App\Repositories\Admin\FormQrCodeRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\FormRequest;
use App\Repositories\Admin\CampaignTypeAssetAttachmentsRepository;


use App\Repositories\Admin\UserRepository;

use App\Repositories\Admin\CampaignBrandsRepository;

use App\Authorizable;
use Illuminate\Support\Facades\Hash;

class FormController extends Controller
{
    private $userRepository;
    private $campaignBrandsRepository;
    private $formQrCodeRepository;
    private $campaignTypeAssetAttachmentsRepository;

    public function __construct(UserRepository $userRepository,
                                CampaignTypeAssetAttachmentsRepository $campaignTypeAssetAttachmentsRepository,
                                FormQrCodeRepository $formQrCodeRepository,
                                CampaignBrandsRepository $campaignBrandsRepository) // phpcs:ignore
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->campaignTypeAssetAttachmentsRepository = $campaignTypeAssetAttachmentsRepository;
        $this->formQrCodeRepository = $formQrCodeRepository;
        $this->campaignBrandsRepository = $campaignBrandsRepository;
    }

    public function create_qr_code()
    {

        $this->data['currentAdminMenu'] = 'qr_code';
        $params['bejour'] = 'no';
        $options = [
            'filter' => $params,
        ];
        $this->data['brands'] = $this->campaignBrandsRepository->findAll($options)->pluck('campaign_name', 'id');

        $this->data['departments'] = [
            "Creative",
            "Global Marketing",
            "NPU",
            "LPU",
            "KPU",
            "CPU",
            "EPU",
            "Sales",
            "KDO",
        ];

        return view('admin.form.form_qr_code', $this->data);
    }

    public function store_qr_code(QrCodeRequest $request)
    {

        $form_qr_code = new FormQrCode();
        $form_qr_code['name'] = $request['name'];
        $form_qr_code['email'] = $request['email'];
        $form_qr_code['qr_code_for'] = $request['qr_code_for'];
        $form_qr_code['brand'] = $request['brand'];
        $form_qr_code['department'] = $request['department'];
        $form_qr_code['link_to'] = $request['link_to'];
        $form_qr_code['date_1'] = $request['date_1'];
        $form_qr_code['date_2'] = $request['date_2'];
        $form_qr_code['date_3'] = $request['date_3'];
        $form_qr_code['information'] = $request['information'];
        $form_qr_code->save();
        $form_qr_code_id = $form_qr_code->id;

//        if($form_qr_code_id){
//            return redirect('/create_qr_code')
//                ->with('success', __('Your Request has been submitted. ID : ' . $form_qr_code_id));
//        }
//
//        return redirect('admin/create_qr_code')
//            ->with('error', __('Your Request not be saved.'));

        if($form_qr_code_id){
            return redirect('admin/create_qr_code')
                ->with('success', __('Your Request has been submitted. ID : ' . $form_qr_code_id));
        }

        return redirect('admin/create_qr_code')
            ->with('error', __('Your Request not be saved.'));

    }

    public function index_qr_code(Request $request)
    {
        $params = $request->all();
        $this->data['currentAdminMenu'] = 'index_qr_code';

        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'created_at' => 'desc',
            ],
            'filter' => $params,
        ];
        $this->data['filter'] = $params;
        $this->data['qr_code_requests'] = $this->formQrCodeRepository->findAll($options);

        return view('admin.form.index_qr_code', $this->data);
    }

    public function edit_qr_code($id)
    {

        $this->data['currentAdminMenu'] = 'index_qr_code';
        $options = [
            'order' => [
                'first_name' => 'asc',
            ]
        ];
        $this->data['qr_code'] = $this->formQrCodeRepository->findById($id);
        $this->data['brands'] = $this->campaignBrandsRepository->findAll($options)->pluck('campaign_name', 'id');
        $this->data['departments'] = [
            "Creative",
            "Global Marketing",
            "NPU",
            "LPU",
            "KPU",
            "CPU",
            "EPU",
            "Sales",
            "KDO",
        ];

        $this->data['attachment'] = $this->campaignTypeAssetAttachmentsRepository->findQrCodeById($id);

//        ddd($this->data['$attachment']);

        return view('admin.form.edit_qr_code', $this->data);
    }

    public function update_qr_code(Request $request, $id)
    {
        $this->data['currentAdminMenu'] = 'index_qr_code';
        $param = $request->all();

        $params['url_destination_link'] = $param['url_destination_link'];
        $params['short_url'] = $param['short_url'];

        $user = auth()->user();

        if($request->file('c_attachment')){
            foreach ($request->file('c_attachment') as $file) {
                $campaign_type_asset_attachments = new CampaignTypeAssetAttachments();

                // file check if exist.
                $originalName = $file->getClientOriginalName();
                $destinationFolder = 'storage/qr_code/'.$id.'/'.$originalName;

                // If exist same name file, add numberning for version control
                if(file_exists($destinationFolder)){
                    if ($pos = strrpos($originalName, '.')) {
                        $new_name = substr($originalName, 0, $pos);
                        $ext = substr($originalName, $pos);
                    }
                    $newpath = 'storage/qr_code/'.$id.'/'.$originalName;
                    $uniq_no = 1;
                    while (file_exists($newpath)) {
                        $tmp_name = $new_name .'_'. $uniq_no . $ext;
                        $newpath = 'storage/qr_code/'.$id.'/'.$tmp_name;
                        $uniq_no++;
                    }
                    $file_name = $tmp_name;
                }else{
                    $file_name = $originalName;
                }
                $fileName =$file->storeAs('qr_code/'.$id, $file_name);

                $campaign_type_asset_attachments['id'] = $id;
                $campaign_type_asset_attachments['asset_id'] = 0;
                $campaign_type_asset_attachments['type'] = 'qr_code';
                $campaign_type_asset_attachments['author_id'] = $user->id;
                $campaign_type_asset_attachments['attachment'] = '/' . $fileName;
                $campaign_type_asset_attachments['file_ext'] = pathinfo($fileName, PATHINFO_EXTENSION);
                $campaign_type_asset_attachments['file_type'] = $file->getMimeType();
                $campaign_type_asset_attachments['file_size'] = $file->getSize();
                $campaign_type_asset_attachments['date_created'] = Carbon::now();
                $campaign_type_asset_attachments->save();
                $attachment_id = $campaign_type_asset_attachments->attachment_id;
            }
        }

        $params['qr_code_image'] = $attachment_id;

        if($this->formQrCodeRepository->update($id, $params)){

            return redirect('admin/edit_qr_code/'.$id)
                ->with('success', __('Your Request has been submitted. ID : ' . $id));
        }
        return redirect('admin/edit_qr_code/'.$id)
            ->with('error', __('Your Request not be saved.'));
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
        if ( ($id != auth()->user()->id) && (auth()->user()->role !='admin') ) {
            return redirect('admin/campaign')
                ->with('error', 'Could not change.');
        }

        $user = $this->userRepository->findById($id);

        $this->data['user'] = $user;
        $this->data['team'] = $user->team;
        $this->data['role_'] = $user->role;
        $this->data['user_brand'] = $user->user_brand;
        $this->data['brands'] = $this->campaignBrandsRepository->findAll();
        $this->data['teams'] = [
            'KDO',
            'Creative',
            'Global Marketing'
        ];
        $this->data['roles_'] = [
            'Admin' => 'admin',
            'Copywriter' => 'copywriter',
            'Ecommerce Specialist' => 'ecommerce specialist',
            'Social Media Manager' => 'social media manager',
            'Marketing' => 'marketing',
            'Creative Director' => 'creative director',
            'Graphic Designer' => 'graphic designer',
            'Content Manager' => 'content manager',
            'Content Creator' => 'content creator',
            'Web Production Manager' => 'web production manager',
            'Web Production' => 'web production',
            'Videographer' => 'videographer',
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

        if($param['password'] == null){
            return redirect('admin/users/'.$id.'/edit')
                ->with('error', 'Please enter your password to update');
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
