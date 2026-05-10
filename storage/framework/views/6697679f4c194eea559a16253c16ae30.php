<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['theme' => 'default']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['theme' => 'default']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php $assets = app(\Tonysm\RichTextLaravel\AssetsManager::class); ?>

<?php if(config('rich-text-laravel.editor') === 'lexxy'): ?>
    <link <?php echo e($attributes); ?> rel="stylesheet" href="<?php echo e($assets->url('/lexxy.css')); ?>" />
    <link <?php echo e($attributes); ?> rel="stylesheet" href="<?php echo e($assets->url('/lexxy-rich-text-laravel-attachments.css')); ?>" />
    <?php if($theme === 'daisyui'): ?>
    <link <?php echo e($attributes); ?> rel="stylesheet" href="<?php echo e($assets->url('/lexxy-rich-text-laravel-daisyui.css')); ?>" />
    <?php elseif($theme === 'flux'): ?>
    <link <?php echo e($attributes); ?> rel="stylesheet" href="<?php echo e($assets->url('/lexxy-rich-text-laravel-flux.css')); ?>" />
    <?php endif; ?>
<?php elseif($theme === 'richtextlaravel'): ?>
    <link <?php echo e($attributes); ?> rel="stylesheet" href="<?php echo e($assets->url('/trix-rich-text-laravel.css')); ?>" />
    <link <?php echo e($attributes); ?> rel="stylesheet" href="<?php echo e($assets->url('/trix-rich-text-laravel-attachments.css')); ?>" />
<?php else: ?>
    <link <?php echo e($attributes); ?> rel="stylesheet" href="<?php echo e($assets->url('/trix.css')); ?>" />
    <link <?php echo e($attributes); ?> rel="stylesheet" href="<?php echo e($assets->url('/trix-rich-text-laravel-attachments.css')); ?>" />
    <?php if($theme === 'daisyui'): ?>
    <link <?php echo e($attributes); ?> rel="stylesheet" href="<?php echo e($assets->url('/trix-rich-text-laravel-daisyui.css')); ?>" />
    <?php elseif($theme === 'flux'): ?>
    <link <?php echo e($attributes); ?> rel="stylesheet" href="<?php echo e($assets->url('/trix-rich-text-laravel-flux.css')); ?>" />
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH D:\Antigravity\news-cms-laravel\vendor\tonysm\rich-text-laravel\resources\views\components/styles.blade.php ENDPATH**/ ?>