<?php echo $__env->make('version', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<meta name="_token" content="<?php echo e(csrf_token()); ?>" />
<title><?php echo e($allsettings->site_title); ?></title>
<?php if($allsettings->site_favicon != ''): ?>
<link rel="apple-touch-icon" href="<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_favicon); ?>">
<link rel="icon" href="<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_favicon); ?>" type="image/x-icon">
<link rel="shortcut icon" href="<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_favicon); ?>" type="image/x-icon">
<?php endif; ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/vendors/bootstrap/dist/css/bootstrap.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('admin/vendors/font-awesome/css/font-awesome.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('admin/vendors/themify-icons/css/themify-icons.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('admin/vendors/flag-icon-css/css/flag-icon.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('admin/vendors/selectFX/css/cs-skin-elastic.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('admin/vendors/jqvmap/dist/jqvmap.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('admin/vendors/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('admin/vendors/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/style.css')); ?>">
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="<?php echo e(asset('admin/datepicker/picker.css')); ?>"> 
<link href="<?php echo e(asset('admin/template/dragdrop/css/jquery.filer.css')); ?>" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('admin/dropzone/min/dropzone.min.css')); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('admin/font-select/fontselect-alternate.css')); ?>" /><?php /**PATH C:\laragon\www\autoscripthub\resources\views/admin/stylesheet.blade.php ENDPATH**/ ?>