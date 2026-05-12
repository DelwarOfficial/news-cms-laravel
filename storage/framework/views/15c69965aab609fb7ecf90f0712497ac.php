<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'NewsCore - Latest Updates'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Your source for breaking news, features, and analysis.'); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&family=Inter:wght@400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php if (isset($component)) { $__componentOriginal95950f824213f5cf8d19afcb8f4ecb86 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal95950f824213f5cf8d19afcb8f4ecb86 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => '8cb219c4cf0735bee5cac90fadf0a458::styles','data' => ['theme' => 'richtextlaravel']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rich-text::styles'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['theme' => 'richtextlaravel']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal95950f824213f5cf8d19afcb8f4ecb86)): ?>
<?php $attributes = $__attributesOriginal95950f824213f5cf8d19afcb8f4ecb86; ?>
<?php unset($__attributesOriginal95950f824213f5cf8d19afcb8f4ecb86); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal95950f824213f5cf8d19afcb8f4ecb86)): ?>
<?php $component = $__componentOriginal95950f824213f5cf8d19afcb8f4ecb86; ?>
<?php unset($__componentOriginal95950f824213f5cf8d19afcb8f4ecb86); ?>
<?php endif; ?>
    <?php echo $__env->yieldPushContent('head'); ?>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif']
                    },
                    colors: {
                        primary: { 600:'#2563eb', 700:'#1d4ed8' }
                    }
                }
            }
        }
    </script>
    <style>
        [lang="bn"], .font-bengali { font-family: 'Noto Sans Bengali', 'Inter', sans-serif; }
        .trix-content .text-right { text-align: right; }
        .trix-content img { height: auto; max-width: 100%; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased flex flex-col min-h-screen">

    
    <div class="bg-red-600 text-white px-4 py-2 text-sm font-bold flex items-center justify-center gap-4 overflow-hidden relative">
        <div class="flex-shrink-0 uppercase tracking-widest"><i class="fas fa-bolt mr-1"></i> Breaking</div>
        <div class="flex-grow whitespace-nowrap overflow-hidden">
            <div class="inline-block animate-[ticker_20s_linear_infinite]">
                <a href="#" class="hover:underline mx-4">Global markets hit record highs as tech stocks surge...</a>
                <span class="opacity-50">&bull;</span>
                <a href="#" class="hover:underline mx-4">New climate agreement signed by 50 nations in Paris...</a>
                <span class="opacity-50">&bull;</span>
                <a href="#" class="hover:underline mx-4">Major scientific breakthrough in renewable energy...</a>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes ticker {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
    </style>

    
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-8">
                    <a href="<?php echo e(route('home')); ?>" class="text-3xl font-serif font-black tracking-tight flex items-center gap-2">
                        <i class="fas fa-bolt text-blue-600"></i> NewsCore
                    </a>
                    <nav class="hidden md:flex space-x-8">
                        <a href="<?php echo e(route('home')); ?>" class="text-sm font-bold text-gray-900 uppercase tracking-wider hover:text-blue-600 transition">Latest</a>
                        <a href="#" class="text-sm font-bold text-gray-600 uppercase tracking-wider hover:text-blue-600 transition">Politics</a>
                        <a href="#" class="text-sm font-bold text-gray-600 uppercase tracking-wider hover:text-blue-600 transition">Technology</a>
                        <a href="#" class="text-sm font-bold text-gray-600 uppercase tracking-wider hover:text-blue-600 transition">Business</a>
                        <a href="#" class="text-sm font-bold text-gray-600 uppercase tracking-wider hover:text-blue-600 transition">Culture</a>
                    </nav>
                </div>
                <div class="flex items-center gap-5">
                    <button class="text-gray-500 hover:text-gray-900"><i class="fas fa-search text-lg"></i></button>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="bg-black text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-gray-800 transition">Dashboard</a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    
    <main class="flex-grow">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <footer class="bg-black text-white py-16 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-1">
                <a href="<?php echo e(route('home')); ?>" class="text-2xl font-serif font-black tracking-tight mb-6 block text-white">
                    <i class="fas fa-bolt text-blue-500"></i> NewsCore
                </a>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    Delivering trusted journalism and independent reporting to millions of readers globally.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-blue-600 transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-blue-600 transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-blue-600 transition"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wider mb-6 text-sm">Sections</h4>
                <ul class="space-y-4 text-gray-400 text-sm">
                    <li><a href="#" class="hover:text-white transition">Politics</a></li>
                    <li><a href="#" class="hover:text-white transition">Technology</a></li>
                    <li><a href="#" class="hover:text-white transition">Business</a></li>
                    <li><a href="#" class="hover:text-white transition">Science</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wider mb-6 text-sm">About</h4>
                <ul class="space-y-4 text-gray-400 text-sm">
                    <li><a href="#" class="hover:text-white transition">Our Story</a></li>
                    <li><a href="#" class="hover:text-white transition">Careers</a></li>
                    <li><a href="#" class="hover:text-white transition">Ethics Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold uppercase tracking-wider mb-6 text-sm">Subscribe</h4>
                <p class="text-gray-400 text-sm mb-4">Get the latest news directly in your inbox.</p>
                <form class="flex">
                    <input type="email" placeholder="Your email address" class="bg-white/10 border border-white/20 text-white px-4 py-3 rounded-l-lg w-full outline-none focus:border-blue-500">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-5 py-3 rounded-r-lg font-bold transition">Join</button>
                </form>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 pt-8 border-t border-white/10 text-center text-sm text-gray-500">
            &copy; <?php echo e(date('Y')); ?> NewsCore. All rights reserved.
        </div>
    </footer>

</body>
</html>
<?php /**PATH D:\websie\news-cms\resources\views/front/layouts/app.blade.php ENDPATH**/ ?>