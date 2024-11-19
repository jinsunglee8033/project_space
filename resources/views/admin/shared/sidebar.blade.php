@php
    $activeClass = 'active';
@endphp
<aside id="sidebar-wrapper">
    <div class="sidebar-brand"
         style="display: flex; justify-content: center; align-items: center;">
{{--        <a href="{{ url('/admin/dashboard')}}">PROJECT SPACE</a>--}}
        <img src="<?php echo e(asset('/storage/ps_logo.png')); ?>" class="logo" alt="Project Space"
             style="max-width: 60%; max-height: 60%; object-fit: contain;">
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ url('/admin/dashboard')}}">PS</a>
    </div>
    <ul class="sidebar-menu">

        <li class="{{ ($currentAdminMenu == 'dashboard') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/dashboard') }}"><i class="fas fa-cog fa-spin"></i> <span>HOME</span></a></li>

        <li class="menu-header">Projects</li>
        <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Product' || auth()->user()->function == 'Management'){ ?>
        <li class="{{ ($currentAdminMenu == 'project') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/project') }}"><i class="fas fa-calendar"></i> <span>Project NPD</span></a></li>
        <?php } ?>
        <?php if(auth()->user()->function == 'Admin' || (auth()->user()->function == 'Product' && auth()->user()->role == 'Team Lead') || (auth()->user()->function == 'Management' && auth()->user()->role == 'Team Lead')){ ?>
        <li class="{{ ($currentAdminMenu == 'project_pre_approve_list') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/project_pre_approve_list') }}"><i class="fas fa-check-circle"></i> <span>NPD Approval List</span></a></li>
        <?php } ?>

        <li class="{{ ($currentAdminMenu == 'project_general') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/project_general') }}"><i class="fas fa-cogs"></i> <span>Interdepartmental</span></a></li>
        <li class="{{ ($currentAdminMenu == 'project_promotion') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/project_promotion') }}"><i class="fas fa-calendar"></i> <span>Project Promotion</span></a></li>



        {{--        <li class="{{ ($currentAdminMenu == 'archives') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/archives') }}"><i class="fas fa-archive"></i> <span>Project Archives</span></a></li>--}}


        <li class="menu-header">MDM</li>
        <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Product' || auth()->user()->function == 'Management' || auth()->user()->team == 'MDM'){ ?>
        <li class="{{ ($currentAdminMenu == 'mm_request') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/mm_request') }}"><i class="fas fa-check-square"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="MDM">MM Overview</span></a></li>
        <li class="{{ ($currentAdminMenu == 'mm_request_board') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/mm_request/board') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="MDM">MM Board</span></a></li>
            <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Management' || auth()->user()->team == 'MDM'){ ?>
            <li class="{{ ($currentAdminMenu == 'mm_request_list') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/mm_request_list') }}"><i class="fas fa-list"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="MDM">MM Request List</span></a></li>
            <?php } ?>
        <?php } ?>

        <li class="menu-header">NPD Planner</li>
        <?php if(auth()->user()->function == 'Admin'
            || ( (auth()->user()->function == 'Product' || auth()->user()->function == 'Management') && (auth()->user()->team == 'Red Appliance (A&A)'
                        || auth()->user()->team == 'Red Accessory & Jewelry (A&A)'
                        || auth()->user()->team == 'Red Fashion & Hair Cap (A&A)'
                        || auth()->user()->team == 'Red Brush & Implement (A&A)') )
            || auth()->user()->team == 'Red Trade Marketing (A&A)' || auth()->user()->team == 'SOM' ){ ?>
        <li class="{{ ($currentAdminMenu == 'npd_planner_request_red_index') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_planner_board_red_index') }}"><i class="fas fa-check-square"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Red Trade Marketing (A&A)">RED Planner Overview</span></a></li>
        <li class="{{ ($currentAdminMenu == 'npd_planner_request_red') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_planner_board_red') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Red Trade Marketing (A&A)">RED Planner Board</span></a></li>
        <?php } ?>
        <?php if(auth()->user()->function == 'Admin'
            || ( (auth()->user()->function == 'Product' || auth()->user()->function == 'Management' ) && (auth()->user()->team == 'Ivy Nail (ND)'
                || auth()->user()->team == 'Ivy Lash (LD)'
                || auth()->user()->team == 'Kiss Nail (ND)'
                || auth()->user()->team == 'Ivy Cosmetic (C&H)'
                || auth()->user()->team == 'Ivy Hair Care (C&H)') )
            || auth()->user()->team == 'CSS' || auth()->user()->team == 'B2B Marketing' || auth()->user()->team == 'SOM'){ ?>
        <li class="{{ ($currentAdminMenu == 'npd_planner_request_ivy_index') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_planner_board_ivy_index') }}"><i class="fas fa-check-square"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="IVY Trade Marketing, SOM">IVY Planner Overview</span></a></li>
        <li class="{{ ($currentAdminMenu == 'npd_planner_request_ivy') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_planner_board_ivy') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="B2B Marketing, SOM">IVY Planner Board</span></a></li>
        <li class="{{ ($currentAdminMenu == 'npd_planner_request_list_lvy') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_planner_list_ivy') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="B2B Marketing, SOM">IVY Planner List</span></a></li>
        <?php } ?>

        <li class="menu-header">LEGAL</li>
        <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Product' || auth()->user()->function == 'Management' || auth()->user()->team == 'Legal'){ ?>
        <li class="{{ ($currentAdminMenu == 'legal_request') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/legal_request') }}"><i class="fas fa-check-square"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Legal">Legal Overview</span></a></li>
        <li class="{{ ($currentAdminMenu == 'legal_board') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/legal_request/board') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Legal">Legal Board</span></a></li>
        <li class="{{ ($currentAdminMenu == 'legal_registration_list') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/legal_request/registration_list') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Legal">Legal Registration List</span></a></li>
        <?php } ?>
        <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Product' || auth()->user()->function == 'Management' || auth()->user()->team == 'Legal RA'){ ?>
{{--        <li class="{{ ($currentAdminMenu == 'qra_request') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/qra_request') }}"><i class="fas fa-calendar"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Legal RA">QRA Overview</span></a></li>--}}
        <li class="{{ ($currentAdminMenu == 'ra_request') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/ra_request') }}"><i class="fas fa-check-square"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Legal RA">RA Overview</span></a></li>
        <li class="{{ ($currentAdminMenu == 'ra_board') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/ra_request/board') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Legal RA">RA Board</span></a></li>
        <li class="{{ ($currentAdminMenu == 'ra_request_list') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/ra_request/request_list') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Legal RA">RA Request List</span></a></li>
        <li class="{{ ($currentAdminMenu == 'ra_registration_list') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/ra_request/registration_list') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Legal RA">RA Registration List</span></a></li>
        <?php } ?>

{{--        <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Product' || auth()->user()->team == 'Purchasing'){ ?>--}}
{{--        <li class="menu-header">IVY / RED</li>--}}
{{--        <li class="{{ ($currentAdminMenu == 'npd_po_request') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_po_request') }}"><i class="fas fa-th"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="IVY / RED">NPD Planner Board</span></a></li>--}}
{{--        <?php } ?>--}}


        <li class="menu-header">Purchasing</li>
        <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Product' || auth()->user()->function == 'Management' || auth()->user()->team == 'Purchasing'){ ?>
        <li class="{{ ($currentAdminMenu == 'npd_po_request') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_po_request') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Purchasing">NPD PO Board</span></a></li>
        <li class="{{ ($currentAdminMenu == 'npd_po_request_temp_list') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_po_request_temp_list') }}"><i class="fas fa-list"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Purchasing">NPD PO Temp List</span></a></li>
            <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Management' || auth()->user()->team == 'Purchasing'){ ?>
            <li class="{{ ($currentAdminMenu == 'npd_po_request_list') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_po_request_list') }}"><i class="fas fa-list"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Purchasing">NPD PO List</span></a></li>
            <?php } ?>
        <?php } ?>

        <li class="menu-header">Design</li>
        <?php if( (auth()->user()->function == 'Admin') || (auth()->user()->function == 'Product') || (auth()->user()->function == 'Management') || (auth()->user()->function == 'Marketing')
        || (auth()->user()->function == 'Design') || ( auth()->user()->team == 'Brand Design') || (auth()->user()->team == 'Production Design' ) || (auth()->user()->team == 'Industrial Design' ) ){ ?>
{{--        <li class="{{ ($currentAdminMenu == 'npd_design_request') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_design_request') }}"><i class="fas fa-check-square"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Design">NPD Design Overview</span></a></li>--}}
        <li class="{{ ($currentAdminMenu == 'npd_design_board') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_design_request/board') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Design">NPD Design Board</span></a></li>
        <?php } ?>
        <?php if( (auth()->user()->function == 'Admin') || (
            ( (auth()->user()->role == 'Team Lead')
                && (auth()->user()->function == 'Design' || auth()->user()->function == 'Collaboration')
                &&  (auth()->user()->team == 'Kiss Nail (ND)' || auth()->user()->team == 'Kiss Hair Care (C&H)' || auth()->user()->team == 'Kiss Nail (ND)' || auth()->user()->team == 'Kiss Lash (LD)' || auth()->user()->team == 'Brand Design' || auth()->user()->team == 'Production Design' || auth()->user()->team == 'Industrial Design' ) ) )
        ){ ?>
        <li class="{{ ($currentAdminMenu == 'npd_design_assign') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/npd_design_request/assign_page') }}"><i class="fas fa-users"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Design">NPD Designer Assign</span></a></li>
        <?php } ?>

{{--        <li class="menu-header">DISPLAY</li>--}}
{{--        <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Product' || auth()->user()->function == 'Management' || auth()->user()->team == 'Display (D&P)'){ ?>--}}
{{--        <li class="{{ ($currentAdminMenu == 'display_board') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/display_request') }}"><i class="fas fa-th"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="Display">Display Board</span></a></li>--}}
{{--        <?php } ?>--}}

        <li class="menu-header">DISPLAY & PE</li>
        <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Product' || auth()->user()->function == 'Marketing' || auth()->user()->function == 'Management' || auth()->user()->team == 'PE (D&P)' || auth()->user()->team == 'Display (D&P)'){ ?>
        <li class="{{ ($currentAdminMenu == 'pe_request') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/pe_request') }}"><i class="fas fa-check-square"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="PE">D&P Overview</span></a></li>
        <li class="{{ ($currentAdminMenu == 'pe_board') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/pe_request/board') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="PE">D&P Board</span></a></li>

        <?php if(auth()->user()->function == 'Admin' || auth()->user()->role == 'Team Lead') { ?>
        <li class="{{ ($currentAdminMenu == 'pe_assign') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/pe_request/assign_page') }}"><i class="fas fa-users"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="PE">D&P Assign</span></a></li>
        <?php } ?>
        <?php } ?>

        <li class="menu-header">QM QA</li>
        <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Product' || auth()->user()->function == 'Management' || auth()->user()->team == 'QM QA'){ ?>
        <li class="{{ ($currentAdminMenu == 'qc_request') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/qc_request') }}"><i class="fas fa-th-large"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="QM QA">QA Board</span></a></li>
        <?php } ?>

        <?php if(auth()->user()->function == 'Admin' || auth()->user()->function == 'Product' || auth()->user()->function == 'Management' || auth()->user()->team == 'QM QC'){ ?>

{{--        <li class="{{ ($currentAdminMenu == 'product_receiving') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/product_receiving') }}"><i class="fas fa-calendar"></i> <span data-toggle="tooltip" data-placement="right" data-original-title="QM QC">Product Receiving Board</span></a></li>--}}
        <?php } ?>

        <?php if(auth()->user()->role == 'Admin'){ ?>
            <li class="menu-header">Account</li>
            <li class="{{ ($currentAdminMenu == 'users') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/users')}}"><i class="fas fa-user"></i> <span>Users</span></a></li>

            <li class="menu-header">Settings</li>
            <?php if(auth()->user()->role == 'Admin'){ ?>
            <li class="{{ ($currentAdminMenu == 'vendors') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/vendors')}}"><i class="fas fa-address-book"></i> <span>Vendors</span></a></li>
            <li class="{{ ($currentAdminMenu == 'brands') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/brands')}}"><i class="fas fa-address-book"></i> <span>Brands</span></a></li>
            <li class="{{ ($currentAdminMenu == 'teams') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/teams')}}"><i class="fas fa-address-book"></i> <span>Teams</span></a></li>
            <li class="{{ ($currentAdminMenu == 'plants') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/plants')}}"><i class="fas fa-address-book"></i> <span>Plants</span></a></li>
            <li class="{{ ($currentAdminMenu == 'product_category') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/product_category')}}"><i class="fas fa-address-book"></i> <span>Product Categories</span></a></li>
            <li class="{{ ($currentAdminMenu == 'product_segment') ? $activeClass : '' }}"><a class="nav-link" href="{{ url('admin/product_segment')}}"><i class="fas fa-address-book"></i> <span>Product Segments</span></a></li>
            <?php } ?>

        <?php } ?>

        <li class="menu-header" style="color: #FF332E">Passion</li>
        <li class="menu-header" style="color: #0C67EA">Challenge</li>
        <li class="menu-header" style="color: #FF9331">Innovation</li>
        <li class="menu-header" style="color: #0BC27F">Collaboration</li>
        <li class="menu-header" style="color: #D733FF">Results</li>

    </ul>
</aside>
