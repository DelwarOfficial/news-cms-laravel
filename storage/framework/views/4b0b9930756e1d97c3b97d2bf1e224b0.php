<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['id', 'name' => null, 'value' => '']));

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

foreach (array_filter((['id', 'name' => null, 'value' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div
    <?php echo e($attributes->whereDoesntStartWith('wire:')); ?>

    <?php if($attributes->has('wire:model')): ?>
    x-data="{ content: $wire.entangle('<?php echo e($attributes->wire('model')->value()); ?>') }"
    x-on:trix-initialize="$refs.input.value = content"
    x-on:trix-change="content = $refs.input.value"
    <?php endif; ?>
>
    <input
        type="hidden"
        <?php if($name ?? false): ?>
        name="<?php echo e($name); ?>"
        <?php endif; ?>
        id="<?php echo e($id); ?>_input"
        value="<?php echo e($value); ?>"
        <?php if($attributes->has('wire:model')): ?>
        x-on:change="$refs.input.value = $event.target.value"
        <?php echo e($attributes->whereStartsWith('wire:')); ?>

        <?php endif; ?>
    />

    <trix-toolbar
        id="<?php echo e($id); ?>_toolbar"
        <?php if($attributes->has('wire:model')): ?>
        wire:ignore
        <?php endif; ?>
    ></trix-toolbar>

    <trix-editor
        id="<?php echo e($id); ?>"
        toolbar="<?php echo e($id); ?>_toolbar"
        input="<?php echo e($id); ?>_input"
        class="trix-content"
        <?php if($attributes->has('wire:model')): ?>
        x-ref="input"
        wire:ignore
        <?php endif; ?>
    ></trix-editor>
</div>
<?php /**PATH D:\Antigravity\news-cms-laravel\vendor\tonysm\rich-text-laravel\resources\views\components/trix.blade.php ENDPATH**/ ?>