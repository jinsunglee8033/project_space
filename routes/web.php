<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\CampaignController as AdminCampaign;
use App\Http\Controllers\Admin\ProjectController as AdminProject;
use App\Http\Controllers\Admin\QraRequestController as AdminQraRequest;
use App\Http\Controllers\Admin\RaRequestController as AdminRaRequest;
use App\Http\Controllers\Admin\LegalRequestController as AdminLegalRequest;
use App\Http\Controllers\Admin\PeRequestController as AdminPeRequest;
use App\Http\Controllers\Admin\NpdDesignRequestController as AdminNpdDesignRequest;
use App\Http\Controllers\Admin\MmRequestController as AdminMmRequest;
use App\Http\Controllers\Admin\NpdPlannerRequestController as AdminNpdPlannerRequest;
use App\Http\Controllers\Admin\NpdPoRequestController as AdminNpdPoRequest;
use App\Http\Controllers\Admin\DisplayRequestController as AdminDisplayRequest;
use App\Http\Controllers\Admin\QcRequestController as AdminQcRequest;
use App\Http\Controllers\Admin\ProductReceivingController as AdminProductReceiving;
use App\Http\Controllers\Admin\ArchivesController as AdminArchives;
use App\Http\Controllers\Admin\DeletedController as AdminDeleted;
use App\Http\Controllers\Admin\AssetController as AdminAsset;
use App\Http\Controllers\Admin\TaskController as AdminTask;
use App\Http\Controllers\Admin\RoleController as AdminRole;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\BrandController as AdminBrand;
use App\Http\Controllers\Admin\FormController as AdminForm;
use App\Http\Controllers\Admin\DevController as AdminDev;
use App\Http\Controllers\Admin\AssetOwnerController as AdminAssetOwner;
use App\Http\Controllers\Admin\AssetLeadTimeController as AdminAssetLeadTime;
use App\Http\Controllers\Admin\SettingController as AdminSetting;
use App\Http\Controllers\Admin\VendorController as AdminVendor;
use App\Http\Controllers\Admin\TeamController as AdminTeam;
use App\Http\Controllers\Admin\PlantController as AdminPlant;
use App\Http\Controllers\Admin\ProductCategoryController as AdminProductCategory;
use App\Http\Controllers\Admin\ProductSegmentController as AdminProductSegment;
use App\Http\Controllers\HomeController as HomeController;
use App\Http\Controllers\NotifyController as NotifyController;
use App\Http\Controllers\Auth\ForgotPasswordController as ForgotPasswordController;
use App\Mail\SendMail as SendMail;
use App\Mail\MyDemoMail as MyDemoMail;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('/auth/login');
});

//Route for mailing
Route::get('/email', function() {
    Mail::to('jinsunglee.8033@gmail.com')->send(new SendMail());
    return new SendMail();
});

Route::get('/email_test', [SendMail::class, 'email_send'])->name('email_send');
//Route::get('/my_demo_mail',[MyDemoMail::class, 'myDemoMail'])->name('my_demo_mail');

Route::get('/email_copy_request', [NotifyController::class, 'copy_request']);
Route::get('/email_copy_review', [NotifyController::class, 'copy_review']);

Route::get('/notification/reminder_email', [NotifyController::class, 'reminder_email']);
Route::get('/notification/clean_up_projects', [NotifyController::class, 'clean_up_projects']);
Route::get('/notification/test', [NotifyController::class, 'test']);

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/project_status', [AdminDashboard::class, 'project_status'])->name('dashboard.project_status');
    Route::get('/dashboard/test', [AdminDashboard::class, 'test'])->name('dashboard.test');

    //// Project NPD ///////////////////////////////
    Route::get('project', [AdminProject::class, 'index'])->name('project.index');
    Route::get('project/create', [AdminProject::class, 'create'])->name('project.create');
    Route::post('project/store', [AdminProject::class, 'store'])->name('project.store');
    Route::post('project/update/{id}', [AdminProject::class, 'update'])->name('project.update');
    Route::get('project/{id}/edit', [AdminProject::class, 'edit'])->name('project.edit');
//    Route::resource('project', AdminProject::class);

    ///// Pre-Approve NPD /////////////////////////
    Route::get('project_pre_approve_list', [AdminProject::class, 'project_pre_approve_list'])->name('project.project_pre_approve_list');

    Route::get('project/approve_project/{id}', [AdminProject::class, 'approveProject'])->name('project.approveProject');
    Route::get('project/resubmit_project/{id}', [AdminProject::class, 'resubmitProject'])->name('project.resubmitProject');

    Route::get('project/projectRemove/{c_id}', [AdminProject::class, 'projectRemove'])->name('project.projectRemove');
    Route::get('project/fileRemove/{id}', [AdminProject::class, 'fileRemove'])->name('project.fileRemove');
    Route::get('project/taskRemove/{t_id}/{type}', [AdminProject::class, 'taskRemove'])->name('project.taskRemove');
    Route::get('project/skippedTaskRemove/{t_id}/{type}', [AdminProject::class, 'skippedTaskRemove'])->name('project.skippedTaskRemove');
    Route::post('project/project_add_note', [AdminProject::class, 'project_add_note'])->name('project.project_add_note');

    //// Project General ///////////////////////////////
    Route::get('project_general', [AdminProject::class, 'index_general'])->name('project.index_general');
    Route::get('project/create_general', [AdminProject::class, 'create_general'])->name('project.create_general');
    Route::post('project/store_general', [AdminProject::class, 'store_general'])->name('project.store_general');
    Route::post('project/update_general/{id}', [AdminProject::class, 'update_general'])->name('project.update_general');
    Route::get('project/{id}/edit_general', [AdminProject::class, 'edit_general'])->name('project.edit_general');

    Route::post('project/revision_reason', [AdminProject::class, 'revision_reason'])->name('project.revision_reason');

    //// Project Promotion ///////////////////////////////
    Route::get('project_promotion', [AdminProject::class, 'index_promotion'])->name('project.project_promotion');
    Route::get('project/create_general', [AdminProject::class, 'create_general'])->name('project.create_general');

    //// Task Routes //////////////////////////
    Route::post('project/add_concept_development', [AdminProject::class, 'add_concept_development'])->name('project.add_concept_development');
    Route::post('project/edit_concept_development/{task_id}',[AdminProject::class, 'edit_concept_development'])->name('project.edit_concept_development');

    Route::post('project/add_product_information', [AdminProject::class, 'add_product_information'])->name('project.add_product_information');
    Route::post('project/edit_product_information/{task_id}',[AdminProject::class, 'edit_product_information'])->name('project.edit_product_information');
    Route::post('project/add_product_brief', [AdminProject::class, 'add_product_brief'])->name('project.add_product_brief');
    Route::post('project/edit_product_brief/{task_id}',[AdminProject::class, 'edit_product_brief'])->name('project.edit_product_brief');

    Route::post('project/add_qra_request', [AdminProject::class, 'add_qra_request'])->name('project.add_qra_request');
    Route::post('project/edit_qra_request/{task_id}',[AdminProject::class, 'edit_qra_request'])->name('project.edit_qra_request');



    // Add MM Request
    Route::post('project/add_mm_request', [AdminProject::class, 'add_mm_request'])->name('project.add_mm_request');
    Route::post('project/edit_mm_request/{task_id}',[AdminProject::class, 'edit_mm_request'])->name('project.edit_mm_request');

    // Add NPD PLANNER Request
    Route::post('project/add_npd_planner_request', [AdminProject::class, 'add_npd_planner_request'])->name('project.add_npd_planner_request');

    // Add LEGAL Request
    Route::post('project/add_legal_request', [AdminProject::class, 'add_legal_request'])->name('project.add_legal_request');
    Route::post('project/edit_legal_request/{task_id}',[AdminProject::class, 'edit_legal_request'])->name('project.edit_legal_request');

    // Add RA Request
    Route::post('project/add_ra_request', [AdminProject::class, 'add_ra_request'])->name('project.add_ra_request');
    Route::post('project/edit_ra_request/{task_id}',[AdminProject::class, 'edit_ra_request'])->name('project.edit_ra_request');

    // Add NPD DESIGN Request
    Route::post('project/add_npd_design_request', [AdminProject::class, 'add_npd_design_request'])->name('project.add_npd_design_request');

    // Add DISPLAY & PE Request
    Route::post('project/add_pe_request', [AdminProject::class, 'add_pe_request'])->name('project.add_pe_request');

    // Add PO Request (Not use)
    Route::post('project/add_npd_po_request', [AdminProject::class, 'add_npd_po_request'])->name('project.add_npd_po_request');
    Route::post('project/edit_npd_po_request/{task_id}',[AdminProject::class, 'edit_npd_po_request'])->name('project.edit_npd_po_request');

    Route::post('project/add_qc_request', [AdminProject::class, 'add_qc_request'])->name('project.add_qc_request');
    Route::post('project/edit_qc_request/{task_id}',[AdminProject::class, 'edit_qc_request'])->name('project.edit_qc_request');

    Route::post('project/add_product_receiving', [AdminProject::class, 'add_product_receiving'])->name('project.add_product_receiving');







    ///// Task ///////////////
    Route::get('task/actionInProgress/{id}', [AdminTask::class, 'actionInProgress'])->name('task.actionInProgress');
    Route::get('task/actionReview/{id}', [AdminTask::class, 'actionReview'])->name('task.actionReview');
    Route::get('task/actionComplete/{id}', [AdminTask::class, 'actionComplete'])->name('task.actionComplete');
    Route::get('task/actionSkip/{id}/{type}', [AdminTask::class, 'actionSkip'])->name('task.actionSkip');

    // MM Request stuff ///////////////////////////////////////
    Route::get('mm_request', [AdminMmRequest::class, 'index'])->name('mm_request.index');
    Route::get('mm_request/board', [AdminMmRequest::class, 'board'])->name('mm_request.board');
    Route::get('mm_request_list', [AdminMmRequest::class, 'index_list'])->name('mm_request.index_list');
    Route::get('mm_request/{id}/edit', [AdminMmRequest::class, 'edit'])->name('mm_request.edit');

    Route::post('mm_request/add_mm_request', [AdminMmRequest::class, 'add_mm_request'])->name('mm_request.add_mm_request');
    Route::post('mm_request/edit_mm_request/{task_id}', [AdminMmRequest::class, 'edit_mm_request'])->name('mm_request.edit_mm_request');

    Route::post('mm_request/add_new', [AdminMmRequest::class, 'add_new'])->name('mm_request.add_new');
    Route::post('mm_request/edit_new/{request_type_id}', [AdminMmRequest::class, 'edit_new'])->name('mm_request.edit_new');
    Route::post('mm_request/add_update', [AdminMmRequest::class, 'add_update'])->name('mm_request.add_update');
    Route::post('mm_request/edit_update/{request_type_id}', [AdminMmRequest::class, 'edit_update'])->name('mm_request.edit_update');
    Route::post('mm_request/add_dimensions', [AdminMmRequest::class, 'add_dimensions'])->name('mm_request.add_dimensions');
    Route::post('mm_request/edit_dimensions/{request_type_id}', [AdminMmRequest::class, 'edit_dimensions'])->name('mm_request.edit_dimensions');
    Route::post('mm_request/add_price', [AdminMmRequest::class, 'add_price'])->name('mm_request.add_price');
    Route::post('mm_request/edit_price/{request_type_id}', [AdminMmRequest::class, 'edit_price'])->name('mm_request.edit_price');

    Route::get('mm_request/actionReSubmit/{id}', [AdminMmRequest::class, 'actionReSubmit'])->name('mm_request.actionReSubmit');
    Route::get('mm_request/actionInProgress/{id}', [AdminMmRequest::class, 'actionInProgress'])->name('mm_request.actionInProgress');
    Route::get('mm_request/actionReview/{id}', [AdminMmRequest::class, 'actionReview'])->name('mm_request.actionReview');
    Route::get('mm_request/actionComplete/{id}', [AdminMmRequest::class, 'actionComplete'])->name('mm_request.actionComplete');

    Route::post('mm_request/revision_reason', [AdminMmRequest::class, 'revision_reason'])->name('mm_request.revision_reason');

    Route::get('mm_request/requestTypeRemove/{request_type_id}/{type}', [AdminMmRequest::class, 'requestTypeRemove'])->name('mm_request.requestTypeRemove');
    Route::get('mm_request/fileRemove/{id}', [AdminMmRequest::class, 'fileRemove'])->name('mm_request.fileRemove');
    Route::post('mm_request/add_note', [AdminMmRequest::class, 'add_note'])->name('mm_request.add_note');


    // Legal Request stuff ///////////////////////////////////
    Route::get('legal_request', [AdminLegalRequest::class, 'index'])->name('legal_request.index');
    Route::get('legal_request/{id}/edit', [AdminLegalRequest::class, 'edit'])->name('legal_request.edit');
    Route::get('legal_request/board', [AdminLegalRequest::class, 'board'])->name('legal_request.board');
    Route::get('legal_request/registration_list', [AdminLegalRequest::class, 'registration_list'])->name('legal_request.registration_list');

    Route::post('legal_request/add_contract', [AdminLegalRequest::class, 'add_contract'])->name('legal_request.add_contract');
    Route::post('legal_request/edit_contract/{request_type_id}', [AdminLegalRequest::class, 'edit_contract'])->name('legal_request.edit_contract');
    Route::post('legal_request/add_claim', [AdminLegalRequest::class, 'add_claim'])->name('legal_request.add_claim');
    Route::post('legal_request/edit_claim/{request_type_id}', [AdminLegalRequest::class, 'edit_claim'])->name('legal_request.edit_claim');
    Route::post('legal_request/add_trademark', [AdminLegalRequest::class, 'add_trademark'])->name('legal_request.add_trademark');
    Route::post('legal_request/edit_trademark/{request_type_id}', [AdminLegalRequest::class, 'edit_trademark'])->name('legal_request.edit_trademark');
    Route::post('legal_request/add_patent', [AdminLegalRequest::class, 'add_patent'])->name('legal_request.add_patent');
    Route::post('legal_request/edit_patent/{request_type_id}', [AdminLegalRequest::class, 'edit_patent'])->name('legal_request.edit_patent');

    Route::get('legal_request/fileRemove/{id}', [AdminLegalRequest::class, 'fileRemove'])->name('legal_request.fileRemove');
    Route::post('legal_request/add_note', [AdminLegalRequest::class, 'add_note'])->name('legal_request.add_note');

    Route::get('legal_request/requestTypeRemove/{request_type_id}/{type}', [AdminLegalRequest::class, 'requestTypeRemove'])->name('legal_request.requestTypeRemove');

    Route::get('legal_request/actionReSubmit/{id}', [AdminLegalRequest::class, 'actionReSubmit'])->name('legal_request.actionReSubmit');
    Route::get('legal_request/actionInProgress/{id}', [AdminLegalRequest::class, 'actionInProgress'])->name('legal_request.actionInProgress');
    Route::get('legal_request/actionReview/{id}', [AdminLegalRequest::class, 'actionReview'])->name('legal_request.actionReview');
    Route::get('legal_request/actionComplete/{id}', [AdminLegalRequest::class, 'actionComplete'])->name('legal_request.actionComplete');

    Route::post('legal_request/revision_reason', [AdminLegalRequest::class, 'revision_reason'])->name('legal_request.revision_reason');

    // RA Request stuff
    Route::get('ra_request', [AdminRaRequest::class, 'index'])->name('ra_request.index');
    Route::get('ra_request/{id}/edit', [AdminRaRequest::class, 'edit'])->name('ra_request.edit');
    Route::get('ra_request/board', [AdminRaRequest::class, 'board'])->name('ra_request.board');
    Route::get('ra_request/request_list', [AdminRaRequest::class, 'request_list'])->name('ra_request.request_list');
    Route::get('ra_request/registration_list', [AdminRaRequest::class, 'registration_list'])->name('ra_request.registration_list');

    Route::post('ra_request/add_formula_review', [AdminRaRequest::class, 'add_formula_review'])->name('ra_request.add_formula_review');
    Route::post('ra_request/edit_formula_review/{request_type_id}', [AdminRaRequest::class, 'edit_formula_review'])->name('ra_request.edit_formula_review');
    Route::post('ra_request/add_label_review', [AdminRaRequest::class, 'add_label_review'])->name('ra_request.add_label_review');
    Route::post('ra_request/edit_label_review/{request_type_id}', [AdminRaRequest::class, 'edit_label_review'])->name('ra_request.edit_label_review');
    Route::post('ra_request/add_us_launch', [AdminRaRequest::class, 'add_us_launch'])->name('ra_request.add_us_launch');
    Route::post('ra_request/edit_us_launch/{request_type_id}', [AdminRaRequest::class, 'edit_us_launch'])->name('ra_request.edit_us_launch');
    Route::post('ra_request/add_canada_launch', [AdminRaRequest::class, 'add_canada_launch'])->name('ra_request.add_canada_launch');
    Route::post('ra_request/edit_canada_launch/{request_type_id}', [AdminRaRequest::class, 'edit_canada_launch'])->name('ra_request.edit_canada_launch');
    Route::post('ra_request/add_eu_launch', [AdminRaRequest::class, 'add_eu_launch'])->name('ra_request.add_eu_launch');
    Route::post('ra_request/edit_eu_launch/{request_type_id}', [AdminRaRequest::class, 'edit_eu_launch'])->name('ra_request.edit_eu_launch');
    Route::post('ra_request/add_uk_launch', [AdminRaRequest::class, 'add_uk_launch'])->name('ra_request.add_uk_launch');
    Route::post('ra_request/edit_uk_launch/{request_type_id}', [AdminRaRequest::class, 'edit_uk_launch'])->name('ra_request.edit_uk_launch');
    Route::post('ra_request/add_latam_launch', [AdminRaRequest::class, 'add_latam_launch'])->name('ra_request.add_latam_launch');
    Route::post('ra_request/edit_latam_launch/{request_type_id}', [AdminRaRequest::class, 'edit_latam_launch'])->name('ra_request.edit_latam_launch');

    Route::get('ra_request/actionInProgress/{id}', [AdminRaRequest::class, 'actionInProgress'])->name('ra_request.actionInProgress');
    Route::get('ra_request/raRevision/{id}', [AdminRaRequest::class, 'raRevision'])->name('ra_request.raRevision');

    Route::post('ra_request/revision_reason', [AdminRaRequest::class, 'revision_reason'])->name('ra_request.revision_reason');

    Route::get('ra_request/reviewComplete/{id}', [AdminRaRequest::class, 'reviewComplete'])->name('ra_request.reviewComplete');
    Route::get('ra_request/raResubmit/{id}', [AdminRaRequest::class, 'raResubmit'])->name('ra_request.raResubmit');
    Route::get('ra_request/raComplete/{id}', [AdminRaRequest::class, 'raComplete'])->name('ra_request.raComplete');

    Route::get('ra_request/actionReview/{id}', [AdminRaRequest::class, 'actionReview'])->name('ra_request.actionReview');
    Route::get('ra_request/actionComplete/{id}', [AdminRaRequest::class, 'actionComplete'])->name('ra_request.actionComplete');

    Route::get('ra_request/fileRemove/{id}', [AdminRaRequest::class, 'fileRemove'])->name('ra_request.fileRemove');
    Route::post('ra_request/add_note', [AdminRaRequest::class, 'add_note'])->name('ra_request.add_note');
    Route::get('ra_request/requestTypeRemove/{request_type_id}/{type}', [AdminRaRequest::class, 'requestTypeRemove'])->name('ra_request.requestTypeRemove');


    // NPD Design Request stuff ///////////////////////////////////
    Route::get('npd_design_request', [AdminNpdDesignRequest::class, 'index'])->name('npd_design_request.index');
    Route::get('npd_design_request/{id}/edit', [AdminNpdDesignRequest::class, 'edit'])->name('npd_design_request.edit');
    Route::get('npd_design_request/assign_page', [AdminNpdDesignRequest::class, 'assign_page'])->name('npd_design_request.assign_page');
    Route::get('npd_design_request/board', [AdminNpdDesignRequest::class, 'board'])->name('npd_design_request.board');

    Route::post('npd_design_request/add_npd_design_request', [AdminNpdDesignRequest::class, 'add_npd_design_request'])->name('npd_design_request.add_npd_design_request');
    Route::post('npd_design_request/edit_npd_design_request/{task_id}', [AdminNpdDesignRequest::class, 'edit_npd_design_request'])->name('npd_design_request.edit_npd_design_request');

    Route::get('npd_design_request/fileRemove/{id}', [AdminNpdDesignRequest::class, 'fileRemove'])->name('npd_design_request.fileRemove');
    Route::post('npd_design_request/add_note', [AdminNpdDesignRequest::class, 'add_note'])->name('npd_design_request.add_note');
    Route::get('npd_design_request/requestTypeRemove/{request_type_id}/{type}', [AdminNpdDesignRequest::class, 'requestTypeRemove'])->name('npd_design_request.requestTypeRemove');

    Route::get('npd_design_request/actionReSubmit/{id}', [AdminNpdDesignRequest::class, 'actionReSubmit'])->name('npd_design_request.actionReSubmit');
    Route::get('npd_design_request/actionDecline/{id}', [AdminNpdDesignRequest::class, 'actionDecline'])->name('npd_design_request.actionDecline');
    Route::get('npd_design_request/actionInProgress/{id}', [AdminNpdDesignRequest::class, 'actionInProgress'])->name('npd_design_request.actionInProgress');
    Route::get('npd_design_request/updateRequired/{id}', [AdminNpdDesignRequest::class, 'updateRequired'])->name('npd_design_request.updatedRequired');
    Route::get('npd_design_request/actionReview/{id}', [AdminNpdDesignRequest::class, 'actionReview'])->name('npd_design_request.actionReview');
    Route::get('npd_design_request/actionComplete/{id}', [AdminNpdDesignRequest::class, 'actionComplete'])->name('npd_design_request.actionComplete');

    Route::post('npd_design_request/revision_reason_update_request', [AdminNpdDesignRequest::class, 'revision_reason_update_request'])->name('npd_design_request.revision_reason_update_request');
    Route::post('npd_design_request/revision_reason_action_decline', [AdminNpdDesignRequest::class, 'revision_reason_action_decline'])->name('npd_design_request.revision_reason_action_decline');


    // PE Request stuff ///////////////////////////////////
    Route::get('pe_request', [AdminPeRequest::class, 'index'])->name('pe_request.index');
    Route::get('pe_request/{id}/edit', [AdminPeRequest::class, 'edit'])->name('pe_request.edit');

    Route::get('pe_request/board', [AdminPeRequest::class, 'board'])->name('pe_request.board');

    Route::get('pe_request/assign_page', [AdminPeRequest::class, 'assign_page'])->name('pe_request.assign_page');

    Route::post('pe_request/add_rendering', [AdminPeRequest::class, 'add_rendering'])->name('pe_request.add_rendering');
    Route::post('pe_request/edit_rendering/{request_type_id}', [AdminPeRequest::class, 'edit_rendering'])->name('pe_request.edit_rendering');

    Route::post('pe_request/add_display', [AdminPeRequest::class, 'add_display'])->name('pe_request.add_display');
    Route::post('pe_request/edit_display/{request_type_id}', [AdminPeRequest::class, 'edit_display'])->name('pe_request.edit_display');

    Route::post('pe_request/add_engineering_drawing', [AdminPeRequest::class, 'add_engineering_drawing'])->name('pe_request.add_engineering_drawing');
    Route::post('pe_request/edit_engineering_drawing/{request_type_id}', [AdminPeRequest::class, 'edit_engineering_drawing'])->name('pe_request.edit_engineering_drawing');
    Route::post('pe_request/add_sample', [AdminPeRequest::class, 'add_sample'])->name('pe_request.add_sample');
    Route::post('pe_request/edit_sample/{request_type_id}', [AdminPeRequest::class, 'edit_sample'])->name('pe_request.edit_sample');
    Route::post('pe_request/add_mold', [AdminPeRequest::class, 'add_mold'])->name('pe_request.add_mold');
    Route::post('pe_request/edit_mold/{request_type_id}', [AdminPeRequest::class, 'edit_mold'])->name('pe_request.edit_mold');

    Route::get('pe_request/actionReSubmit/{id}', [AdminPeRequest::class, 'actionReSubmit'])->name('pe_request.actionReSubmit');
    Route::get('pe_request/actionInProgress/{id}', [AdminPeRequest::class, 'actionInProgress'])->name('pe_request.actionInProgress');
    Route::get('pe_request/actionReview/{id}', [AdminPeRequest::class, 'actionReview'])->name('pe_request.actionReview');
    Route::get('pe_request/actionComplete/{id}', [AdminPeRequest::class, 'actionComplete'])->name('pe_request.actionComplete');
    Route::get('pe_request/actionApprove/{id}', [AdminPeRequest::class, 'actionApprove'])->name('pe_request.actionApprove');

    Route::post('pe_request/revision_reason', [AdminPeRequest::class, 'revision_reason'])->name('pe_request.revision_reason');


    Route::get('pe_request/fileRemove/{id}', [AdminPeRequest::class, 'fileRemove'])->name('pe_request.fileRemove');
    Route::post('pe_request/add_note', [AdminPeRequest::class, 'add_note'])->name('pe_request.add_note');
    Route::get('pe_request/requestTypeRemove/{request_type_id}/{type}', [AdminPeRequest::class, 'requestTypeRemove'])->name('pe_request.requestTypeRemove');

    // NPD Planner Request stuff
    Route::get('npd_planner_request', [AdminNpdPlannerRequest::class, 'index'])->name('npd_planner_request.index');

    Route::get('npd_planner_board_red_index', [AdminNpdPlannerRequest::class, 'index_red'])->name('npd_planner_request.index_red');
    Route::get('npd_planner_board_red', [AdminNpdPlannerRequest::class, 'board_red'])->name('npd_planner_request.board_red');
    Route::get('npd_planner_board_ivy_index', [AdminNpdPlannerRequest::class, 'index_ivy'])->name('npd_planner_request.index_ivy');
    Route::get('npd_planner_board_ivy', [AdminNpdPlannerRequest::class, 'board_ivy'])->name('npd_planner_request.board_ivy');
    Route::get('npd_planner_list_ivy', [AdminNpdPlannerRequest::class, 'list_ivy'])->name('npd_planner_request.list_ivy');

    Route::get('npd_planner_request_list', [AdminNpdPlannerRequest::class, 'index_list'])->name('npd_planner_request.index_list');
    Route::get('npd_planner_request/{id}/edit', [AdminNpdPlannerRequest::class, 'edit'])->name('npd_planner_request.edit');
    Route::post('npd_planner_request/add_project_planner', [AdminNpdPlannerRequest::class, 'add_project_planner'])->name('npd_planner_request.add_project_planner');
    Route::post('npd_planner_request/edit_project_planner/{task_id}', [AdminNpdPlannerRequest::class, 'edit_project_planner'])->name('npd_planner_request.edit_project_planner');
    Route::post('npd_planner_request/add_change_request', [AdminNpdPlannerRequest::class, 'add_change_request'])->name('npd_planner_request.add_change_request');
    Route::post('npd_planner_request/edit_change_request/{task_id}', [AdminNpdPlannerRequest::class, 'edit_change_request'])->name('npd_planner_request.edit_change_request');
    Route::post('npd_planner_request/add_presale_plan', [AdminNpdPlannerRequest::class, 'add_presale_plan'])->name('npd_planner_request.add_presale_plan');
    Route::post('npd_planner_request/edit_presale_plan/{task_id}', [AdminNpdPlannerRequest::class, 'edit_presale_plan'])->name('npd_planner_request.edit_presale_plan');

    Route::get('npd_planner_request/actionReSubmit/{id}', [AdminNpdPlannerRequest::class, 'actionReSubmit'])->name('npd_planner_request.actionReSubmit');
    Route::get('npd_planner_request/actionInProgress/{id}', [AdminNpdPlannerRequest::class, 'actionInProgress'])->name('npd_planner_request.actionInProgress');
    Route::get('npd_planner_request/actionReview/{id}', [AdminNpdPlannerRequest::class, 'actionReview'])->name('npd_planner_request.actionReview');
    Route::get('npd_planner_request/actionComplete/{id}', [AdminNpdPlannerRequest::class, 'actionComplete'])->name('npd_planner_request.actionComplete');
    Route::get('npd_planner_request/actionUpload/{id}', [AdminNpdPlannerRequest::class, 'actionUpload'])->name('npd_planner_request.actionUpload');

    Route::post('npd_planner_request/revision_reason_update_request', [AdminNpdPlannerRequest::class, 'revision_reason_update_request'])->name('npd_planner_request.revision_reason_update_request');
    Route::post('npd_planner_request/revision_reason_action_decline', [AdminNpdPlannerRequest::class, 'revision_reason_action_decline'])->name('npd_planner_request.revision_reason_action_decline');

    Route::get('npd_planner_request/requestTypeRemove/{request_type_id}/{type}', [AdminNpdPlannerRequest::class, 'requestTypeRemove'])->name('npd_planner_request.requestTypeRemove');
    Route::get('npd_planner_request/fileRemove/{id}', [AdminNpdPlannerRequest::class, 'fileRemove'])->name('npd_planner_request.fileRemove');
    Route::post('npd_planner_request/add_note', [AdminNpdPlannerRequest::class, 'add_note'])->name('npd_planner_request.add_note');

    // NPD PO Request stuff
    Route::get('npd_po_request', [AdminNpdPoRequest::class, 'index'])->name('npd_po_request.index');
    Route::get('npd_po_request_list', [AdminNpdPoRequest::class, 'index_list'])->name('npd_po_request.index_list');
    Route::get('npd_po_request_temp_list', [AdminNpdPoRequest::class, 'index_temp_list'])->name('npd_po_request.index_temp_list');
    Route::get('npd_po_request/{id}/edit', [AdminNpdPoRequest::class, 'edit'])->name('npd_po_request.edit');
    Route::post('npd_po_request/add_npd_po_request', [AdminNpdPoRequest::class, 'add_npd_po_request'])->name('npd_po_request.add_npd_po_request');
    Route::post('npd_po_request/edit_npd_po_request/{task_id}', [AdminNpdPoRequest::class, 'edit_npd_po_request'])->name('npd_po_request.edit_npd_po_request');

    Route::get('npd_po_request/actionReSubmit/{id}', [AdminNpdPoRequest::class, 'actionReSubmit'])->name('npd_po_request.actionReSubmit');
    Route::get('npd_po_request/actionInProgress/{id}', [AdminNpdPoRequest::class, 'actionInProgress'])->name('npd_po_request.actionInProgress');
    Route::get('npd_po_request/actionReview/{id}', [AdminNpdPoRequest::class, 'actionReview'])->name('npd_po_request.actionReview');
    Route::get('npd_po_request/actionComplete/{id}', [AdminNpdPoRequest::class, 'actionComplete'])->name('npd_po_request.actionComplete');
    Route::get('npd_po_request/actionFinalPrice/{id}', [AdminNpdPoRequest::class, 'actionFinalPrice'])->name('npd_po_request.actionFinalPrice');

    Route::get('npd_po_request/fileRemove/{id}', [AdminNpdPoRequest::class, 'fileRemove'])->name('npd_po_request.fileRemove');
    Route::post('npd_po_request/add_note', [AdminNpdPoRequest::class, 'add_note'])->name('npd_po_request.add_note');
    Route::get('npd_po_request/requestTypeRemove/{request_type_id}/{type}', [AdminNpdPoRequest::class, 'requestTypeRemove'])->name('npd_po_request.requestTypeRemove');

    Route::post('npd_po_request/revision_reason', [AdminNpdPoRequest::class, 'revision_reason'])->name('npd_po_request.revision_reason');

    // Display Request Stuff /////////////////////////////////////////
    Route::get('display_request', [AdminDisplayRequest::class, 'index'])->name('display_request.index');
    Route::get('display_request/{id}/edit', [AdminDisplayRequest::class, 'edit'])->name('display_request.edit');
    Route::post('display_request/add_display_request', [AdminDisplayRequest::class, 'add_display_request'])->name('display_request.add_display_request');
    Route::post('display_request/edit_display_request/{task_id}', [AdminDisplayRequest::class, 'edit_display_request'])->name('display_request.edit_display_request');

    Route::get('display_request/actionReSubmit/{id}', [AdminDisplayRequest::class, 'actionReSubmit'])->name('display_request.actionReSubmit');
    Route::get('display_request/actionApprove/{id}', [AdminDisplayRequest::class, 'actionApprove'])->name('display_request.actionApprove');
    Route::get('display_request/actionInProgress/{id}', [AdminDisplayRequest::class, 'actionInProgress'])->name('display_request.actionInProgress');
    Route::get('display_request/actionReview/{id}', [AdminDisplayRequest::class, 'actionReview'])->name('display_request.actionReview');
    Route::get('display_request/actionComplete/{id}', [AdminDisplayRequest::class, 'actionComplete'])->name('display_request.actionComplete');

    Route::post('display_request/revision_reason', [AdminDisplayRequest::class, 'revision_reason'])->name('display_request.revision_reason');

    Route::get('display_request/fileRemove/{id}', [AdminDisplayRequest::class, 'fileRemove'])->name('display_request.fileRemove');
    Route::post('display_request/add_note', [AdminDisplayRequest::class, 'add_note'])->name('display_request.add_note');

    // Onsite QC Request stuff ///////////////////////////////////////
    Route::get('qc_request', [AdminQcRequest::class, 'index'])->name('qc_request.index');
    Route::get('qc_request/{id}/edit', [AdminQcRequest::class, 'edit'])->name('qc_request.edit');
    Route::post('qc_request/add_qc_request', [AdminQcRequest::class, 'add_qc_request'])->name('qc_request.add_qc_request');
    Route::post('qc_request/edit_qc_request/{task_id}', [AdminQcRequest::class, 'edit_qc_request'])->name('qc_request.edit_qc_request');

    Route::get('qc_request/actionInProgress/{id}', [AdminQcRequest::class, 'actionInProgress'])->name('qc_request.actionInProgress');
    Route::get('qc_request/actionReview/{id}', [AdminQcRequest::class, 'actionReview'])->name('qc_request.actionReview');
    Route::get('qc_request/actionComplete/{id}', [AdminQcRequest::class, 'actionComplete'])->name('qc_request.actionComplete');

    Route::get('qc_request/fileRemove/{id}', [AdminQcRequest::class, 'fileRemove'])->name('qc_request.fileRemove');
    Route::post('qc_request/add_note', [AdminQcRequest::class, 'add_note'])->name('qc_request.add_note');
    Route::get('qc_request/requestTypeRemove/{request_type_id}/{type}', [AdminQcRequest::class, 'requestTypeRemove'])->name('qc_request.requestTypeRemove');

    Route::post('qc_request/revision_reason_action_decline', [AdminQcRequest::class, 'revision_reason_action_decline'])->name('qc_request.revision_reason_action_decline');

//    Route::resource('qc_request', AdminQcRequest::class);

    // Product Receiving stuff ////////////////////////////////////////
    Route::get('product_receiving', [AdminProductReceiving::class, 'index'])->name('product_receiving.index');
    Route::get('product_receiving/{id}/edit', [AdminProductReceiving::class, 'edit'])->name('product_receiving.edit');
    Route::post('product_receiving/edit_product_receiving/{task_id}', [AdminProductReceiving::class, 'edit_product_receiving'])->name('product_receiving.edit_product_receiving');

    Route::get('product_receiving/actionInProgress/{id}', [AdminProductReceiving::class, 'actionInProgress'])->name('product_receiving.actionInProgress');
    Route::get('product_receiving/actionReview/{id}', [AdminProductReceiving::class, 'actionReview'])->name('product_receiving.actionReview');
    Route::get('product_receiving/actionComplete/{id}', [AdminProductReceiving::class, 'actionComplete'])->name('product_receiving.actionComplete');

    // Setting /////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////

    // Vendors
    Route::resource('vendors', AdminVendor::class);
    Route::get('project/autocomplete_vendor', [AdminProject::class, 'autocomplete_vendor'])->name('project.autocomplete_vendor');

    // Brands
    Route::resource('brands', AdminBrand::class);
    Route::get('project/autocomplete_brand', [AdminProject::class, 'autocomplete_brand'])->name('project.autocomplete_brand');

    // Teams
    Route::resource('teams', AdminTeam::class);

    // Plants
    Route::resource('plants', AdminPlant::class);

    // Product Category
    Route::resource('product_category', AdminProductCategory::class);
    Route::get('project/autocomplete_product_category', [AdminProject::class, 'autocomplete_product_category'])->name('project.autocomplete_product_category');

    // Product Segment
    Route::resource('product_segment', AdminProductSegment::class);
    Route::get('project/autocomplete_product_segment', [AdminProject::class, 'autocomplete_product_segment'])->name('project.autocomplete_product_segment');










    ////// ETC /////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    Route::post('store_qr_code', [AdminForm::class, 'store_qr_code'])->name('form.store_qr_code');
    Route::get('settings/remove/{id}', [AdminSetting::class, 'remove'])->name('settings.update');
    Route::get('settings', [AdminSetting::class, 'index'])->name('settings.update');
    Route::post('settings', [AdminSetting::class, 'update'])->name('settings.update');

    Route::get('campaign', [AdminCampaign::class, 'index'])->name('campaign.index');
    Route::resource('campaign', AdminCampaign::class);
    Route::get('campaign/archives', [AdminCampaign::class, 'archives'])->name('campaign.archives');

    Route::get('asset_approval', [AdminAsset::class, 'asset_approval'])->name('asset.approval');
    Route::get('asset_kpi_copy', [AdminAsset::class, 'asset_kpi_copy'])->name('asset.kpi_copy');
    Route::get('asset_kpi', [AdminAsset::class, 'asset_kpi'])->name('asset.kpi');
    Route::get('asset_kpi_content', [AdminAsset::class, 'asset_kpi_content'])->name('asset.kpi_content');
    Route::get('asset_kpi_web', [AdminAsset::class, 'asset_kpi_web'])->name('asset.kpi_web');
    Route::get('asset_approval_copy', [AdminAsset::class, 'asset_approval_copy'])->name('asset.approval_copy');
    Route::get('asset_approval_content', [AdminAsset::class, 'asset_approval_content'])->name('asset.approval_content');
    Route::get('asset_approval_web', [AdminAsset::class, 'asset_approval_web'])->name('asset.approval_web');
    Route::get('asset_assign', [AdminAsset::class, 'asset_assign'])->name('asset.assign');
    Route::get('asset_jira', [AdminAsset::class, 'asset_jira'])->name('asset.jira');
    Route::get('asset_jira_content', [AdminAsset::class, 'asset_jira_content'])->name('asset.jira_content');
    Route::get('asset_jira_web', [AdminAsset::class, 'asset_jira_web'])->name('asset.jira_web');
    Route::get('asset_jira_kec', [AdminAsset::class, 'asset_jira_kec'])->name('asset.jira_kec');
    Route::get('asset_jira_copywriter', [AdminAsset::class, 'asset_jira_copywriter'])->name('asset.jira_copywriter');
    Route::get('asset/{a_id}/{c_id}/{a_type}/detail', [AdminAsset::class, 'asset_detail'])->name('asset.detail');
    Route::get('asset/{a_id}/{c_id}/{a_type}/{brand}/detail_copy', [AdminAsset::class, 'asset_detail_copy'])->name('asset.detail_copy');

    Route::post('asset/team_change', [AdminAsset::class, 'asset_team_change'])->name('asset.team_change');
    Route::post('asset/assign', [AdminAsset::class, 'asset_assign'])->name('asset.assign');
    Route::post('asset/assign_copy', [AdminAsset::class, 'asset_assign_copy'])->name('asset.assign_copy');
    Route::post('asset/assign_change', [AdminAsset::class, 'asset_assign_change'])->name('asset.assign_change');
    Route::post('asset/copy_writer_change', [AdminAsset::class, 'asset_copy_writer_change'])->name('asset.copy_writer_change');
    Route::post('asset/decline_copy', [AdminAsset::class, 'asset_decline_copy'])->name('asset.decline_copy');
    Route::post('asset/decline_creative', [AdminAsset::class, 'asset_decline_creative'])->name('asset.decline_creative');
    Route::post('asset/decline_kec', [AdminAsset::class, 'asset_decline_kec'])->name('asset.decline_kec');

    Route::post('asset/asset_notification_user', [AdminAsset::class, 'asset_notification_user'])->name('asset.asset_notification_user');
    Route::post('asset/asset_owner_change', [AdminAsset::class, 'asset_owner_change'])->name('asset.asset_owner_change');
    Route::post('asset/asset_owner_change_mapping', [AdminAsset::class, 'asset_owner_change_mapping'])->name('asset.asset_owner_change_mapping');
    Route::post('asset/asset_add_note', [AdminAsset::class, 'asset_add_note'])->name('asset.asset_add_note');

    Route::get('asset/copyReview/{id}', [AdminAsset::class, 'copyReview'])->name('campaign.copyReview');
    Route::get('asset/copyComplete/{id}', [AdminAsset::class, 'copyComplete'])->name('campaign.copyComplete');
    Route::get('asset/copyInProgress/{id}', [AdminAsset::class, 'copyInProgress'])->name('campaign.copyInProgress');
    Route::get('asset/inProgress/{id}', [AdminAsset::class, 'inProgress'])->name('campaign.inProgress');
    Route::get('asset/done/{id}', [AdminAsset::class, 'done'])->name('campaign.done');
    Route::get('asset/finalApproval/{id}', [AdminAsset::class, 'finalApproval'])->name('campaign.finalApproval');








    // DEV Stuff
    Route::resource('dev', AdminDev::class);
    Route::get('dev/create', [AdminDev::class, 'create'])->name('dev.create');
    Route::get('dev_jira', [AdminDev::class, 'dev_jira'])->name('dev.dev_jira');
    Route::get('campaign/fileRemove/{id}', [AdminCampaign::class, 'fileRemove'])->name('campaign.fileRemove');
    Route::get('dev/fileRemove/{id}', [AdminDev::class, 'fileRemove'])->name('dev.fileRemove');
    Route::get('dev_approval', [AdminDev::class, 'dev_approval'])->name('dev.approval');
    Route::get('dev_archives', [AdminDev::class, 'dev_archives'])->name('dev.archives');
    Route::post('dev/dev_add_note', [AdminDev::class, 'dev_add_note'])->name('dev.dev_add_note');
    Route::post('dev/assign', [AdminDev::class, 'dev_assign'])->name('dev.assign');
    Route::get('dev/dev_in_progress/{id}', [AdminDev::class, 'dev_in_progress'])->name('dev.dev_in_progress');
    Route::get('dev/dev_review/{id}', [AdminDev::class, 'dev_review'])->name('dev.dev_review');
    Route::get('dev/dev_done/{id}', [AdminDev::class, 'dev_done'])->name('dev.dev_done');

    Route::resource('users', AdminUser::class);
    Route::resource('brands', AdminBrand::class);
    Route::resource('asset_lead_time', AdminAssetLeadTime::class);
    Route::resource('asset_owners', AdminAssetOwner::class);

    Route::get('campaign/send_archive/{id}', [AdminCampaign::class, 'sendArchive'])->name('campaign.sendArchive');
    Route::get('campaign/send_active/{id}', [AdminCampaign::class, 'sendActive'])->name('campaign.sendActive');

    Route::get('archives', [AdminArchives::class, 'index'])->name('archives.index');
    Route::resource('archives', AdminArchives::class);

    Route::get('deleted', [AdminDeleted::class, 'index'])->name('deleted.index');
    Route::resource('deleted', AdminDeleted::class);

    Route::get('campaign/fileRemove/{id}', [AdminCampaign::class, 'fileRemove'])->name('campaign.fileRemove');

    Route::get('campaign/assetRemove/{a_id}/{type}', [AdminCampaign::class, 'assetRemove'])->name('campaign.assetRemove');
    Route::get('campaign/campaignRemove/{c_id}', [AdminCampaign::class, 'campaignRemove'])->name('campaign.campaignRemove');

    Route::post('campaign/add_email_blast', [AdminCampaign::class, 'add_email_blast'])->name('campaign.add_email_blast');


});
