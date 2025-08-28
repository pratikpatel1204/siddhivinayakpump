<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-name" content="quixlab" />
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('admin/images/favicon.png')); ?>">
    <link href="<?php echo e(asset('admin/plugins/pg-calendar/css/pignose.calendar.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('admin/plugins/chartist/css/chartist.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/plugins/chartist-plugin-tooltips/css/chartist-plugin-tooltip.css')); ?>">
    <link href="<?php echo e(asset('admin/plugins/tables/css/datatable/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link href="<?php echo e(asset('admin/css/style.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('admin/fontawesome/css/all.min.css')); ?>">
    <link href="<?php echo e(asset('admin/plugins/toastr/css/toastr.min.css')); ?>" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo e(asset('admin/plugins/toastr/js/toastr.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/toastr/js/toastr.init.js')); ?>"></script>
</head>
<body>
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <div id="main-wrapper">
        <?php echo $__env->make('layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; Designed & Developed by <a href="https://qubetatechnolab.com/" target="_blank">Qubeta Technolab</a> <?php echo e(date('Y')); ?></p>
        </div>
    </div>
    <script src="<?php echo e(asset('admin/plugins/common/common.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/settings.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/gleek.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/styleSwitcher.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/chart.js/Chart.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/circle-progress/circle-progress.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/d3v3/index.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/topojson/topojson.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/datamaps/datamaps.world.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/raphael/raphael.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/morris/morris.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/moment/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/pg-calendar/js/pignose.calendar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/chartist/js/chartist.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/dashboard/dashboard-1.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/tables/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/tables/js/datatable/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/plugins/tables/js/datatable-init/datatable-basic.min.js')); ?>"></script>    
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\siddhivinayakpump\resources\views/layout/main-layout.blade.php ENDPATH**/ ?>