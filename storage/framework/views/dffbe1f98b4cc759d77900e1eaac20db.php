<?php extract((new \Illuminate\Support\Collection($attributes->getAttributes()))->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['id','name','value','class']));

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

foreach (array_filter((['id','name','value','class']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<?php if (isset($component)) { $__componentOriginal9e934835e8e2c4815c7aff23fbc7ec7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9e934835e8e2c4815c7aff23fbc7ec7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8cb219c4cf0735bee5cac90fadf0a458::trix','data' => ['id' => $id,'name' => $name,'value' => $value,'class' => $class]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rich-text::trix'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($id),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value),'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($class)]); ?>

<?php echo e($slot ?? ""); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9e934835e8e2c4815c7aff23fbc7ec7c)): ?>
<?php $attributes = $__attributesOriginal9e934835e8e2c4815c7aff23fbc7ec7c; ?>
<?php unset($__attributesOriginal9e934835e8e2c4815c7aff23fbc7ec7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9e934835e8e2c4815c7aff23fbc7ec7c)): ?>
<?php $component = $__componentOriginal9e934835e8e2c4815c7aff23fbc7ec7c; ?>
<?php unset($__componentOriginal9e934835e8e2c4815c7aff23fbc7ec7c); ?>
<?php endif; ?><?php /**PATH D:\Antigravity\news-cms-laravel\storage\framework\views/ad8d5790ff6f09f058e3dd6b18ab50ca.blade.php ENDPATH**/ ?>