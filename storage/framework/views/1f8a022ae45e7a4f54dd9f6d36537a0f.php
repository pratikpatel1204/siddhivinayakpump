<div class="nav-header">
    <div class="brand-logo">
        <a href="#">
            <b class="logo-abbr"><img src="<?php echo e(asset('admin/images/logo.png')); ?>" alt="" style="width: 15px;"> </b>
            <span class="logo-compact"><img src="<?php echo e(asset('admin/images/logo-compact.png')); ?>" alt=""></span>
            <span class="brand-title">
                <img src="<?php echo e(asset('admin/images/logo-compact.png')); ?>" alt="" style="width: 100px;">
            </span>
        </a>
    </div>
</div>
<div class="header">
    <div class="header-content clearfix">
        <div class="nav-control">
            <div class="hamburger">
                <span class="toggle-icon"><i class="icon-menu"></i></span>
            </div>
        </div>
        <div class="header-left">
            <div class="input-group icons">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-transparent border-0 pr-2 pr-sm-3" id="basic-addon1"><i class="mdi mdi-magnify"></i></span>
                </div>
                <input type="search" class="form-control" placeholder="Search Dashboard" aria-label="Search Dashboard">
                <div class="drop-down animated flipInX d-md-none">
                    <form action="#">
                        <input type="text" class="form-control" placeholder="Search">
                    </form>
                </div>
            </div>
        </div>
        <div class="header-right">
            <ul class="clearfix">
                <li class="icons dropdown d-none d-md-flex">
                    <h6><?php echo e(auth()->user()->name); ?></h6>
                </li>
                <li class="icons dropdown">
                    <div class="user-img c-pointer position-relative" data-toggle="dropdown">
                        <span class="activity active"></span>
                        <img src="<?php echo e(asset(auth()->user()->profile_image)); ?>" height="40" width="40" alt="">
                    </div>
                    <div class="drop-down dropdown-profile animated fadeIn dropdown-menu">
                        <div class="dropdown-content-body">
                            <ul>
                                <li><a href="<?php echo e(route('show.profile')); ?>"><i class="icon-user"></i> <span>Profile</span></a></li>
                                <hr class="my-2">
                                <li><a href="<?php echo e(route('logout')); ?>"><i class="icon-key"></i> <span>Logout</span></a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="nk-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">           
            <li>
                <a href="<?php echo e(route('show.dashboard')); ?>" aria-expanded="false">
                    <i class="icon-speedometer menu-icon"></i><span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('expired.reward')); ?>" aria-expanded="false">
                    <i class="icon-speedometer menu-icon"></i><span class="nav-text">Update Expired Reward</span>
                </a>
            </li>
            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
            <li>
                <a href="<?php echo e(route('show.all.report.list')); ?>" aria-expanded="false">
                    <i class="fas fa-file-alt menu-icon"></i><span class="nav-text">All Report</span>
                </a>
            </li>       
            <li>
                <a href="<?php echo e(route('customer.report')); ?>" aria-expanded="false">
                    <i class="fas fa-file-alt menu-icon"></i><span class="nav-text">Customer Reports</span>
                </a>
            </li>   
            <li>
                <a href="<?php echo e(route('show.reward.report.list')); ?>" aria-expanded="false">
                    <i class="fas fa-file-alt menu-icon"></i><span class="nav-text">Reward Report</span>
                </a>
            </li>          
            <li>
                <a href="<?php echo e(route('show.gift.report.list')); ?>" aria-expanded="false">
                    <i class="fas fa-file-alt menu-icon"></i><span class="nav-text">Gift Report</span>
                </a>
            </li>          
            <li>
                <a href="<?php echo e(route('show.employee.list')); ?>" aria-expanded="false">
                    <i class="fas fa-users menu-icon"></i><span class="nav-text">Employee List</span>
                </a>
            </li>           
            <li>
                <a href="<?php echo e(route('employee.permissions')); ?>" aria-expanded="false">
                    <i class="fas fa-server menu-icon"></i><span class="nav-text">Employee Permission</span>
                </a>               
            </li>          
            <li>
                <a href="<?php echo e(route('reward.master')); ?>" aria-expanded="false">
                    <i class="fas fa-rupee-sign menu-icon"></i><span class="nav-text">Reward Master</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('service.master')); ?>" aria-expanded="false">
                    <i class="fas fa-sliders-h menu-icon"></i><span class="nav-text">Service Master</span>
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="<?php echo e(route('add.customer')); ?>" aria-expanded="false">
                    <i class="fas fa-user-lock menu-icon"></i><span class="nav-text">Create Customer</span>
                </a>
            </li>
            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
            <li>
                <a href="<?php echo e(route('customer.list')); ?>" aria-expanded="false">
                    <i class="fas fa-user-lock menu-icon"></i><span class="nav-text">Customer List</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('reward.management')); ?>" aria-expanded="false">
                    <i class="fas fa-money-check-alt menu-icon"></i><span class="nav-text">Reward List</span>
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="<?php echo e(route('show.redeem')); ?>" aria-expanded="false">
                    <i class="fab fa-bitcoin menu-icon"></i><span class="nav-text">Reward Redeem</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('redeem.reward.history')); ?>" aria-expanded="false">
                    <i class="fab fa-bitcoin menu-icon"></i><span class="nav-text">Reward History</span>
                </a>
            </li>
            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
            <li>
                <a href="<?php echo e(route('gift.list')); ?>" aria-expanded="false">
                    <i class="fab fa-bitcoin menu-icon"></i><span class="nav-text">Gift List</span>
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="<?php echo e(route('show.gift')); ?>" aria-expanded="false">
                    <i class="fab fa-bitcoin menu-icon"></i><span class="nav-text">Gift Redeem</span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('redeem.gift.history')); ?>" aria-expanded="false">
                    <i class="fab fa-bitcoin menu-icon"></i><span class="nav-text">Gift History</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<?php /**PATH D:\xampp\htdocs\siddhivinayakpump\resources\views/layout/header.blade.php ENDPATH**/ ?>