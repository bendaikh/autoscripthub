<!DOCTYPE HTML>
<html lang="en">
<head>
<title><?php echo e($addition_settings->site_home_title); ?> - <?php echo e($allsettings->site_title); ?></title>
<?php echo $__env->make('meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body>
<?php echo $__env->make('header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<section class="hero-section-modern" style="position: relative; overflow: hidden; min-height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
      
      <!-- Animated Background Elements -->
      <div class="hero-bg-animation">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        <div class="floating-shape shape-4"></div>
        <div class="floating-shape shape-5"></div>
      </div>
      
      <!-- Floating Icons -->
      <div class="floating-icons">
        <i class="dwg-shopping-bag floating-icon icon-1"></i>
        <i class="dwg-download floating-icon icon-2"></i>
        <i class="dwg-star-filled floating-icon icon-3"></i>
        <i class="dwg-cart floating-icon icon-4"></i>
        <i class="dwg-code floating-icon icon-5"></i>
        <i class="dwg-image floating-icon icon-6"></i>
        <i class="dwg-rocket floating-icon icon-7"></i>
        <i class="dwg-heart floating-icon icon-8"></i>
      </div>
      
      <!-- Particle Effect Container -->
      <canvas id="particle-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;"></canvas>
      
      <div class="py-5" style="position: relative; z-index: 10;">
        <div class="container">
          <div class="row mb-4">
            <div class="col-lg-8 col-md-10 text-center mx-auto">
              <h1 class="text-white line-height-base hero-title-animated" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; text-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                <?php echo e($allsettings->site_banner_heading); ?>

              </h1>
              <h2 class="h5 text-white font-weight-light hero-subtitle-animated" style="opacity: 0.95; text-shadow: 0 2px 10px rgba(0,0,0,0.2); margin-bottom: 2rem;">
                <?php echo e($allsettings->site_banner_subheading); ?>

              </h2>
            </div>
          </div>
          
          <form action="<?php echo e(route('shop')); ?>" id="search_form" method="post" class="form-noborder searchbox" enctype="multipart/form-data">
          <?php echo e(csrf_field()); ?>

          <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
              <div class="hero-search-box" style="backdrop-filter: blur(20px); background: rgba(255, 255, 255, 0.15); border-radius: 50px; padding: 6px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.18);">
                <div class="d-flex align-items-center flex-wrap" style="gap: 0;">
                  <div style="position: relative; flex: 1; min-width: 200px;">
                    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); z-index: 5; color: #667eea;">
                      <i class="dwg-search" style="font-size: 1.1rem;"></i>
                    </span>
                    <input class="form-control" 
                           type="text" 
                           id="product_item" 
                           name="product_item" 
                           placeholder="<?php echo e(__('Search your products...')); ?>"
                           style="background: rgba(255, 255, 255, 0.95); border: none; font-size: 0.95rem; padding: 0.85rem 1rem 0.85rem 2.8rem; border-radius: 50px 0 0 50px; height: 50px;">
                  </div>
                  <?php if(count($category['view']) != 0): ?>
                  <select name="category_names[]" 
                          id="product_cat" 
                          class="custom-select"
                          style="background: rgba(255, 255, 255, 0.95); border: none; border-left: 2px solid rgba(102, 126, 234, 0.2); font-size: 0.95rem; padding: 0.85rem 1rem; border-radius: 0; height: 50px; flex: 0 0 auto; width: auto; min-width: 150px;">
                      <option value=""><?php echo e(__('All categories')); ?></option>
                      <?php $__currentLoopData = $category['view']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e('category_'.$cat->cat_id); ?>"><?php echo e($cat->category_name); ?></option>
                      <?php $__currentLoopData = $cat->subcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e('subcategory_'.$sub_category->subcat_id); ?>"> - <?php echo e($sub_category->subcategory_name); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                  </select>
                  <?php endif; ?>
                  <button class="btn btn-primary hero-search-btn" 
                          type="submit"
                          style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; padding: 0.85rem 2rem; border-radius: 0 50px 50px 0; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4); height: 50px; white-space: nowrap;">
                    <?php echo e(__('Search Now')); ?>

                  </button>
                </div>
              </div>
            </div>
          </div>
          </form>
        </div>
      </div>
      
      <style>
        /* Animated Background Shapes */
        .hero-bg-animation {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          overflow: hidden;
          z-index: 1;
        }
        
        .floating-shape {
          position: absolute;
          background: rgba(255, 255, 255, 0.1);
          border-radius: 50%;
          animation: float 20s infinite ease-in-out;
        }
        
        .shape-1 {
          width: 300px;
          height: 300px;
          top: -50px;
          left: -50px;
          animation-delay: 0s;
          background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
        }
        
        .shape-2 {
          width: 200px;
          height: 200px;
          top: 50%;
          right: -30px;
          animation-delay: 3s;
          animation-duration: 15s;
          background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
        }
        
        .shape-3 {
          width: 150px;
          height: 150px;
          bottom: 20%;
          left: 10%;
          animation-delay: 5s;
          animation-duration: 18s;
          background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
        }
        
        .shape-4 {
          width: 250px;
          height: 250px;
          top: 30%;
          left: 60%;
          animation-delay: 2s;
          animation-duration: 22s;
          background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }
        
        .shape-5 {
          width: 180px;
          height: 180px;
          bottom: -50px;
          right: 15%;
          animation-delay: 4s;
          animation-duration: 16s;
          background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, transparent 70%);
        }
        
        @keyframes float {
          0%, 100% {
            transform: translate(0, 0) scale(1);
          }
          25% {
            transform: translate(30px, -30px) scale(1.1);
          }
          50% {
            transform: translate(-20px, 20px) scale(0.9);
          }
          75% {
            transform: translate(20px, 30px) scale(1.05);
          }
        }
        
        /* Floating Icons */
        .floating-icons {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          z-index: 2;
          pointer-events: none;
        }
        
        .floating-icon {
          position: absolute;
          font-size: 2rem;
          color: rgba(255, 255, 255, 0.3);
          animation: floatIcon 15s infinite ease-in-out;
        }
        
        .icon-1 {
          top: 10%;
          left: 5%;
          animation-delay: 0s;
          animation-duration: 12s;
        }
        
        .icon-2 {
          top: 20%;
          right: 8%;
          animation-delay: 2s;
          animation-duration: 14s;
        }
        
        .icon-3 {
          top: 60%;
          left: 15%;
          animation-delay: 1s;
          animation-duration: 13s;
        }
        
        .icon-4 {
          top: 70%;
          right: 12%;
          animation-delay: 3s;
          animation-duration: 15s;
        }
        
        .icon-5 {
          top: 40%;
          left: 8%;
          animation-delay: 4s;
          animation-duration: 11s;
        }
        
        .icon-6 {
          top: 30%;
          right: 20%;
          animation-delay: 1.5s;
          animation-duration: 16s;
        }
        
        .icon-7 {
          top: 80%;
          left: 25%;
          animation-delay: 2.5s;
          animation-duration: 14s;
        }
        
        .icon-8 {
          top: 15%;
          left: 35%;
          animation-delay: 3.5s;
          animation-duration: 13s;
        }
        
        @keyframes floatIcon {
          0%, 100% {
            transform: translateY(0) rotate(0deg);
            opacity: 0.3;
          }
          25% {
            transform: translateY(-20px) rotate(5deg);
            opacity: 0.5;
          }
          50% {
            transform: translateY(-40px) rotate(-5deg);
            opacity: 0.3;
          }
          75% {
            transform: translateY(-20px) rotate(3deg);
            opacity: 0.4;
          }
        }
        
        /* Text Animations */
        .hero-title-animated {
          animation: fadeInUp 1s ease-out;
        }
        
        .hero-subtitle-animated {
          animation: fadeInUp 1.2s ease-out;
        }
        
        .hero-search-box {
          animation: fadeInUp 1.4s ease-out;
        }
        
        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(30px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
        
        /* Search Button Hover Effect */
        .hero-search-btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 25px rgba(245, 87, 108, 0.6) !important;
        }
        
        /* Glassmorphism effect enhancement */
        .hero-search-box:hover {
          background: rgba(255, 255, 255, 0.2);
          border: 1px solid rgba(255, 255, 255, 0.3);
          transition: all 0.3s ease;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
          .hero-section-modern {
            min-height: 350px !important;
          }
          .hero-search-box .d-flex {
            flex-direction: column !important;
          }
          .hero-search-box .d-flex > div,
          .hero-search-box .d-flex > select,
          .hero-search-box .d-flex > button {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            border-radius: 25px !important;
            margin-bottom: 8px;
          }
          .hero-search-box .d-flex > div:last-child,
          .hero-search-box .d-flex > button:last-child {
            margin-bottom: 0;
          }
          .hero-search-box .d-flex input {
            border-radius: 25px !important;
          }
          .hero-search-box .d-flex select {
            border-left: none !important;
            border-top: 2px solid rgba(102, 126, 234, 0.2) !important;
          }
        }
        
        @media (max-width: 768px) {
          .hero-section-modern {
            min-height: 320px !important;
          }
          .hero-title-animated {
            font-size: 1.75rem !important;
          }
          .hero-subtitle-animated {
            font-size: 0.95rem !important;
          }
          .floating-icon {
            font-size: 1.5rem;
          }
          .floating-shape {
            opacity: 0.5;
          }
        }
      </style>
      
      <script>
        // Particle Animation
        document.addEventListener('DOMContentLoaded', function() {
          const canvas = document.getElementById('particle-canvas');
          if (!canvas) return;
          
          const ctx = canvas.getContext('2d');
          canvas.width = canvas.offsetWidth;
          canvas.height = canvas.offsetHeight;
          
          const particles = [];
          const particleCount = 50;
          
          class Particle {
            constructor() {
              this.reset();
            }
            
            reset() {
              this.x = Math.random() * canvas.width;
              this.y = Math.random() * canvas.height;
              this.size = Math.random() * 3 + 1;
              this.speedX = Math.random() * 2 - 1;
              this.speedY = Math.random() * 2 - 1;
              this.opacity = Math.random() * 0.5 + 0.2;
            }
            
            update() {
              this.x += this.speedX;
              this.y += this.speedY;
              
              if (this.x > canvas.width || this.x < 0 || this.y > canvas.height || this.y < 0) {
                this.reset();
              }
            }
            
            draw() {
              ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
              ctx.beginPath();
              ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
              ctx.fill();
            }
          }
          
          for (let i = 0; i < particleCount; i++) {
            particles.push(new Particle());
          }
          
          function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            for (let i = 0; i < particles.length; i++) {
              particles[i].update();
              particles[i].draw();
              
              for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < 100) {
                  ctx.strokeStyle = `rgba(255, 255, 255, ${0.2 - distance / 500})`;
                  ctx.lineWidth = 1;
                  ctx.beginPath();
                  ctx.moveTo(particles[i].x, particles[i].y);
                  ctx.lineTo(particles[j].x, particles[j].y);
                  ctx.stroke();
                }
              }
            }
            
            requestAnimationFrame(animate);
          }
          
          animate();
          
          // Resize handler
          window.addEventListener('resize', function() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
          });
        });
      </script>
    </section>
    <?php if(in_array('home',$top_ads)): ?>
    <section class="container mb-lg-1" data-aos="fade-up" data-aos-delay="200">
      <div class="row">
          <div class="col-lg-12 mb-1" align="center">
             <?php echo html_entity_decode($addition_settings->top_ads); ?>
          </div>
       </div>   
    </section>   
    <?php endif; ?>
<?php if(count($featured['items']) != 0): ?>
<section class="container mb-lg-1" data-aos="fade-up" data-aos-delay="200">
      <!-- Heading-->
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100"><?php echo e(__('Featured Files')); ?></h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="<?php echo e(URL::to('/')); ?>/featured-items"><?php echo e(__('Browse All Items')); ?><i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
      <!-- Grid-->
      <div class="row pt-2 mx-n2 flash-sale">
        <!-- Product-->
        <?php $no = 1; ?>
        <?php $__currentLoopData = $featured['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featured): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        $price = Helper::price_info($featured->item_flash,$featured->extended_price);
        $count_rating = Helper::count_rating($featured->ratings);
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 px-2 mb-grid-gutter">
          <!-- Product-->
          <div class="card product-card-alt">
            <?php if($custom_settings->author_key == Helper::Key_Owner()): ?>
            <?php if($featured->item_flash == 1): ?>
            <div class="ribbon ribbon-top-left"><span><?php echo e($addition_settings->flash_sale_value); ?>% <?php echo e(__('OFF')); ?></span></div>
            <?php endif; ?>
            <?php endif; ?>
            <div class="product-thumb">
              <?php if(Auth::guest()): ?> 
              <a class="btn-wishlist btn-sm" href="<?php echo e(url('/')); ?>/login"><i class="dwg-heart"></i></a>
              <?php endif; ?>
              <?php if(Auth::check()): ?>
              <?php if($featured->user_id != Auth::user()->id): ?>
              <a class="btn-wishlist btn-sm" href="<?php echo e(url('/item')); ?>/<?php echo e(base64_encode($featured->item_id)); ?>/favorite/<?php echo e(base64_encode($featured->item_liked)); ?>"><i class="dwg-heart"></i></a>
              <?php endif; ?>
              <?php endif; ?>
              <div class="product-card-actions"><a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-eye"></i></a>
              <?php
              $checkif_purchased = Helper::if_purchased($featured->item_token);
              ?>
              
              <?php if($addition_settings->guest_checkout == 1): ?>
              <!--- Guest Checkout ON  -->
              <?php if($checkif_purchased == 0): ?>
              <?php if($featured->free_download == 0): ?>
              <?php if(Auth::check()): ?>
              <?php if(Auth::user()->id != 1 && $featured->user_id != Auth::user()->id): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/add-to-cart')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/add-to-cart')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <?php if(Auth::guest()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/login')); ?>"><i class="dwg-download"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/download/<?php echo e(base64_encode($featured->item_token)); ?>"><i class="dwg-download"></i></a>
              <?php endif; ?>
              <?php endif; ?> 
              <?php endif; ?>
              <!--- Guest Checkout ON  -->
              <?php else: ?>
              <!--- Guest Checkout OFF -->
              <?php if($checkif_purchased == 0): ?>
              <?php if($featured->free_download == 0): ?>
              <?php if(Auth::check()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <?php if(Auth::guest()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/login')); ?>"><i class="dwg-download"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/download/<?php echo e(base64_encode($featured->item_token)); ?>"><i class="dwg-download"></i></a>
              <?php endif; ?>
              <?php endif; ?>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
               <!--- Guest Checkout OFF -->
              <?php endif; ?>  
              
              </div><a class="product-thumb-overlay" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"></a>
                            <?php if($featured->item_preview!=''): ?>
                            <img class="lazy" src="<?php echo e(Helper::Image_Path($featured->item_preview,'no-image.png')); ?>" alt="<?php echo e($featured->item_name); ?>" width="300" height="200">
                            <?php else: ?>
                            <img class="lazy" src="<?php echo e(url('/')); ?>/public/img/no-image.png" alt="<?php echo e($featured->item_name); ?>" width="300" height="200">
                            <?php endif; ?>
                          </div>
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-start pb-2">
                <div class="text-muted font-size-xs mr-1"><a class="product-meta font-weight-medium" href="<?php echo e(URL::to('/shop')); ?>/item-type/<?php echo e($featured->item_type); ?>"><?php echo e(Helper::ItemTypeIdGetData($featured->item_type_id)); ?></a></div>
                <div class="star-rating">
                    <?php if($count_rating == 0): ?>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 1): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 2): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 3): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 4): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 5): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <?php endif; ?>
                </div>
               </div>
              <h3 class="product-title font-size-sm mb-2"><a href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><?php if($addition_settings->item_name_limit != 0): ?><?php echo e(mb_substr($featured->item_name,0,$addition_settings->item_name_limit,'utf-8').'...'); ?>

		      <?php else: ?> <?php echo e($featured->item_name); ?> <?php endif; ?></a></h3>
              <div class="card-footer d-flex align-items-center font-size-xs">
              <a class="blog-entry-meta-link" href="<?php echo e(URL::to('/user')); ?>/<?php echo e($featured->username); ?>">
                    <div class="blog-entry-author-ava">
                    <?php if($featured->user_photo!=''): ?>
                    <img class="lazy" src="<?php echo e(url('/')); ?>/public/storage/users/<?php echo e($featured->user_photo); ?>" alt="<?php echo e($featured->username); ?>" width="26" height="26">
                    <?php else: ?>
                    <img class="lazy" src="<?php echo e(url('/')); ?>/public/img/no-user.png" alt="<?php echo e($featured->username); ?>" width="26" height="26">
                    <?php endif; ?>
                    </div>
					<?php if($addition_settings->author_name_limit != 0): ?><?php echo e(mb_substr($featured->username,0,$addition_settings->author_name_limit,'utf-8')); ?> <?php else: ?> <?php echo e($featured->username); ?> <?php endif; ?> 
					<?php if($addition_settings->subscription_mode == 1): ?> <?php if($featured->user_document_verified == 1): ?> <span class="badges-success"><i class="dwg-check-circle danger"></i> <?php echo e(__('verified')); ?></span><?php endif; ?> <?php endif; ?></a>
                  <div class="ml-auto text-nowrap"><i class="dwg-time"></i> <?php echo e(date('d M Y',strtotime($featured->updated_item))); ?></div>
                </div>
              <div class="d-flex flex-wrap justify-content-between align-items-center">
                <?php if($featured->file_type == 'serial'): ?> 
                <?php
                if($featured->item_delimiter == 'comma')
                {
                $result_count = substr_count($featured->item_serials_list, ",");
                }
                else
                {
                $result_count = substr_count($featured->item_serials_list, "\n");
                }
                ?>
                <div class="font-size-sm mr-2"><i class="dwg-cart text-muted mr-1"></i><?php echo e($result_count); ?><span class="font-size-xs ml-1"><?php echo e(__('Stock')); ?></span>
                </div>
                <?php else: ?>
                <div class="font-size-sm mr-2">
                
                </div>
                <?php endif; ?>
                <div>
                <?php if($featured->free_download == 0): ?>
                <?php if($featured->item_flash == 1): ?><del class="price-old"><?php echo e(Helper::price_format($allsettings->site_currency_position,$featured->extended_price,$currency_symbol,$multicurrency)); ?></del><?php endif; ?> <span class="bg-faded-accent text-accent rounded-sm py-1 px-2"><?php echo e(Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency)); ?></span>
                <?php else: ?>
                <span class="price-badge rounded-sm py-1 px-2"><?php echo e(__('Free')); ?></span> 
                <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Product-->
        <?php $no++; ?>
	    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       </div>
    </section>
    <?php endif; ?>
    <?php if(count($popular['items']) != 0): ?>
<section class="container mb-lg-1 flash-sale" data-aos="fade-up" data-aos-delay="200">
      <!-- Heading-->
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100"><?php echo e(__('Popular Items')); ?></h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="<?php echo e(URL::to('/')); ?>/popular-items"><?php echo e(__('Browse All Items')); ?><i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
      <!-- Grid-->
      <div class="row pt-2 mx-n2">
        <!-- Product-->
        <?php $no = 1; ?>
        <?php $__currentLoopData = $popular['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featured): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        $price = Helper::price_info($featured->item_flash,$featured->extended_price);
        $count_rating = Helper::count_rating($featured->ratings);
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 px-2 mb-grid-gutter">
          <!-- Product-->
          <div class="card product-card-alt">
            <?php if($custom_settings->author_key == Helper::Key_Owner()): ?>
            <?php if($featured->item_flash == 1): ?>
            <div class="ribbon ribbon-top-left"><span><?php echo e($addition_settings->flash_sale_value); ?>% <?php echo e(__('OFF')); ?></span></div>
            <?php endif; ?>
            <?php endif; ?>
            <div class="product-thumb">
              <?php if(Auth::guest()): ?> 
              <a class="btn-wishlist btn-sm" href="<?php echo e(url('/')); ?>/login"><i class="dwg-heart"></i></a>
              <?php endif; ?>
              <?php if(Auth::check()): ?>
              <?php if($featured->user_id != Auth::user()->id): ?>
              <a class="btn-wishlist btn-sm" href="<?php echo e(url('/item')); ?>/<?php echo e(base64_encode($featured->item_id)); ?>/favorite/<?php echo e(base64_encode($featured->item_liked)); ?>"><i class="dwg-heart"></i></a>
              <?php endif; ?>
              <?php endif; ?>
              <div class="product-card-actions"><a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-eye"></i></a>
              <?php
              $checkif_purchased = Helper::if_purchased($featured->item_token);
              ?>
              
              <?php if($addition_settings->guest_checkout == 1): ?>
              <!--- Guest Checkout ON  -->
              <?php if($checkif_purchased == 0): ?>
              <?php if($featured->free_download == 0): ?>
              <?php if(Auth::check()): ?>
              <?php if(Auth::user()->id != 1 && $featured->user_id != Auth::user()->id): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/add-to-cart')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/add-to-cart')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <?php if(Auth::guest()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/login')); ?>"><i class="dwg-download"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/download/<?php echo e(base64_encode($featured->item_token)); ?>"><i class="dwg-download"></i></a>
              <?php endif; ?>
              <?php endif; ?> 
              <?php endif; ?>
              <!--- Guest Checkout ON  -->
              <?php else: ?>
              <!--- Guest Checkout OFF -->
              <?php if($checkif_purchased == 0): ?>
              <?php if($featured->free_download == 0): ?>
              <?php if(Auth::check()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <?php if(Auth::guest()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/login')); ?>"><i class="dwg-download"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/download/<?php echo e(base64_encode($featured->item_token)); ?>"><i class="dwg-download"></i></a>
              <?php endif; ?>
              <?php endif; ?>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
               <!--- Guest Checkout OFF -->
              <?php endif; ?>
                 
              </div><a class="product-thumb-overlay" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"></a>
                            <?php if($featured->item_preview!=''): ?>
                            <img class="lazy" src="<?php echo e(Helper::Image_Path($featured->item_preview,'no-image.png')); ?>" alt="<?php echo e($featured->item_name); ?>" width="300" height="200">
                            <?php else: ?>
                            <img class="lazy" src="<?php echo e(url('/')); ?>/public/img/no-image.png" alt="<?php echo e($featured->item_name); ?>" width="300" height="200">
                            <?php endif; ?>
                          </div>
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-start pb-2">
                <div class="text-muted font-size-xs mr-1"><a class="product-meta font-weight-medium" href="<?php echo e(URL::to('/shop')); ?>/item-type/<?php echo e($featured->item_type); ?>"><?php echo e(Helper::ItemTypeIdGetData($featured->item_type_id)); ?></a></div>
                <div class="star-rating">
                    <?php if($count_rating == 0): ?>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 1): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 2): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 3): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 4): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 5): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <?php endif; ?>
                </div>
               </div>
              <h3 class="product-title font-size-sm mb-2"><a href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>">
			  <?php if($addition_settings->item_name_limit != 0): ?>
			  <?php echo e(mb_substr($featured->item_name,0,$addition_settings->item_name_limit,'utf-8').'...'); ?>

		      <?php else: ?>
			  <?php echo e($featured->item_name); ?>	  
			  <?php endif; ?>
			  </a></h3>
              <div class="card-footer d-flex align-items-center font-size-xs">
              <a class="blog-entry-meta-link" href="<?php echo e(URL::to('/user')); ?>/<?php echo e($featured->username); ?>">
                    <div class="blog-entry-author-ava">
                    <?php if($featured->user_photo!=''): ?>
                    <img class="lazy" src="<?php echo e(url('/')); ?>/public/storage/users/<?php echo e($featured->user_photo); ?>"  alt="<?php echo e($featured->username); ?>" width="26" height="26">
                    <?php else: ?>
                    <img class="lazy" src="<?php echo e(url('/')); ?>/public/img/no-user.png" alt="<?php echo e($featured->username); ?>" width="26" height="26">
                    <?php endif; ?>
                    </div>
					<?php if($addition_settings->author_name_limit != 0): ?><?php echo e(mb_substr($featured->username,0,$addition_settings->author_name_limit,'utf-8')); ?> <?php else: ?> <?php echo e($featured->username); ?> <?php endif; ?>
					<?php if($addition_settings->subscription_mode == 1): ?> <?php if($featured->user_document_verified == 1): ?> <span class="badges-success"><i class="dwg-check-circle danger"></i> <?php echo e(__('verified')); ?></span><?php endif; ?> <?php endif; ?></a>
                  <div class="ml-auto text-nowrap"><i class="dwg-time"></i> <?php echo e(date('d M Y',strtotime($featured->updated_item))); ?></div>
                </div>
              <div class="d-flex flex-wrap justify-content-between align-items-center">
                <?php if($featured->file_type == 'serial'): ?> 
                <?php
                if($featured->item_delimiter == 'comma')
                {
                $result_count = substr_count($featured->item_serials_list, ",");
                }
                else
                {
                $result_count = substr_count($featured->item_serials_list, "\n");
                }
                ?>
                <div class="font-size-sm mr-2"><i class="dwg-cart text-muted mr-1"></i><?php echo e($result_count); ?><span class="font-size-xs ml-1"><?php echo e(__('Stock')); ?></span>
                </div>
                <?php else: ?>
                <div class="font-size-sm mr-2">
                
                </div>
                <?php endif; ?>
                <div>
                <?php if($featured->free_download == 0): ?>
                <?php if($featured->item_flash == 1): ?><del class="price-old"><?php echo e(Helper::price_format($allsettings->site_currency_position,$featured->extended_price,$currency_symbol,$multicurrency)); ?></del><?php endif; ?> <span class="bg-faded-accent text-accent rounded-sm py-1 px-2"><?php echo e(Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency)); ?></span>
                <?php else: ?>
                <span class="price-badge rounded-sm py-1 px-2"><?php echo e(__('Free')); ?></span> 
                <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Product-->
        <?php $no++; ?>
	    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       </div>
    </section>
    <?php endif; ?>
    <?php if(count($flash['items']) != 0): ?>
<section class="container mb-lg-1 flash-sale" data-aos="fade-up" data-aos-delay="200">
      <!-- Heading-->
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100"><?php echo e(__('Flash Sale')); ?></h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="<?php echo e(URL::to('/')); ?>/flash-sale"><?php echo e(__('Browse All Items')); ?><i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
      <!-- Grid-->
      <div class="row pt-2 mx-n2">
        <!-- Product-->
        <?php $no = 1; ?>
        <?php $__currentLoopData = $flash['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featured): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        $price = Helper::price_info($featured->item_flash,$featured->extended_price);
        $count_rating = Helper::count_rating($featured->ratings);
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 px-2 mb-grid-gutter">
          <!-- Product-->
          <div class="card product-card-alt">
            <?php if($custom_settings->author_key == Helper::Key_Owner()): ?>
            <?php if($featured->item_flash == 1): ?>
            <div class="ribbon ribbon-top-left"><span><?php echo e($addition_settings->flash_sale_value); ?>% <?php echo e(__('OFF')); ?></span></div>
            <?php endif; ?>
            <?php endif; ?>
            <div class="product-thumb">
              <?php if(Auth::guest()): ?> 
              <a class="btn-wishlist btn-sm" href="<?php echo e(url('/')); ?>/login"><i class="dwg-heart"></i></a>
              <?php endif; ?>
              <?php if(Auth::check()): ?>
              <?php if($featured->user_id != Auth::user()->id): ?>
              <a class="btn-wishlist btn-sm" href="<?php echo e(url('/item')); ?>/<?php echo e(base64_encode($featured->item_id)); ?>/favorite/<?php echo e(base64_encode($featured->item_liked)); ?>"><i class="dwg-heart"></i></a>
              <?php endif; ?>
              <?php endif; ?>
              <div class="product-card-actions"><a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-eye"></i></a>
              <?php
              $checkif_purchased = Helper::if_purchased($featured->item_token);
              ?>
              
              <?php if($addition_settings->guest_checkout == 1): ?>
              <!--- Guest Checkout ON  -->
              <?php if($checkif_purchased == 0): ?>
              <?php if($featured->free_download == 0): ?>
              <?php if(Auth::check()): ?>
              <?php if(Auth::user()->id != 1 && $featured->user_id != Auth::user()->id): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/add-to-cart')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/add-to-cart')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <?php if(Auth::guest()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/login')); ?>"><i class="dwg-download"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/download/<?php echo e(base64_encode($featured->item_token)); ?>"><i class="dwg-download"></i></a>
              <?php endif; ?>
              <?php endif; ?> 
              <?php endif; ?>
              <!--- Guest Checkout ON  -->
              <?php else: ?>
              <!--- Guest Checkout OFF -->
              <?php if($checkif_purchased == 0): ?>
              <?php if($featured->free_download == 0): ?>
              <?php if(Auth::check()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <?php if(Auth::guest()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/login')); ?>"><i class="dwg-download"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/download/<?php echo e(base64_encode($featured->item_token)); ?>"><i class="dwg-download"></i></a>
              <?php endif; ?>
              <?php endif; ?>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
               <!--- Guest Checkout OFF -->
              <?php endif; ?>
                
              </div><a class="product-thumb-overlay" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"></a>
                            <?php if($featured->item_preview!=''): ?>
                            <img class="lazy" src="<?php echo e(Helper::Image_Path($featured->item_preview,'no-image.png')); ?>" alt="<?php echo e($featured->item_name); ?>" width="300" height="200">
                            <?php else: ?>
                            <img class="lazy" src="<?php echo e(url('/')); ?>/public/img/no-image.png" alt="<?php echo e($featured->item_name); ?>" width="300" height="200">
                            <?php endif; ?>
                          </div>
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-start pb-2">
                <div class="text-muted font-size-xs mr-1"><a class="product-meta font-weight-medium" href="<?php echo e(URL::to('/shop')); ?>/item-type/<?php echo e($featured->item_type); ?>"><?php echo e(Helper::ItemTypeIdGetData($featured->item_type_id)); ?></a></div>
                <div class="star-rating">
                    <?php if($count_rating == 0): ?>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 1): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 2): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 3): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 4): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 5): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <?php endif; ?>
                </div>
               </div>
              <h3 class="product-title font-size-sm mb-2"><a href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>">
			  <?php if($addition_settings->item_name_limit != 0): ?>
			  <?php echo e(mb_substr($featured->item_name,0,$addition_settings->item_name_limit,'utf-8').'...'); ?>

		      <?php else: ?>
			  <?php echo e($featured->item_name); ?>	  
			  <?php endif; ?>
			  </a></h3>
              <div class="card-footer d-flex align-items-center font-size-xs">
              <a class="blog-entry-meta-link" href="<?php echo e(URL::to('/user')); ?>/<?php echo e($featured->username); ?>">
                    <div class="blog-entry-author-ava">
                    <?php if($featured->user_photo!=''): ?>
                    <img class="lazy" src="<?php echo e(url('/')); ?>/public/storage/users/<?php echo e($featured->user_photo); ?>" alt="<?php echo e($featured->username); ?>" width="26" height="26">
                    <?php else: ?>
                    <img class="lazy" src="<?php echo e(url('/')); ?>/public/img/no-user.png" alt="<?php echo e($featured->username); ?>" width="26" height="26">
                    <?php endif; ?>
                    </div>
					<?php if($addition_settings->author_name_limit != 0): ?>
					<?php echo e(mb_substr($featured->username,0,$addition_settings->author_name_limit,'utf-8')); ?>

				    <?php else: ?>
				    <?php echo e($featured->username); ?>	  
				    <?php endif; ?>
					<?php if($addition_settings->subscription_mode == 1): ?> <?php if($featured->user_document_verified == 1): ?> <span class="badges-success"><i class="dwg-check-circle danger"></i> <?php echo e(__('verified')); ?></span><?php endif; ?> <?php endif; ?></a>
                  <div class="ml-auto text-nowrap"><i class="dwg-time"></i> <?php echo e(date('d M Y',strtotime($featured->updated_item))); ?></div>
                </div>
              <div class="d-flex flex-wrap justify-content-between align-items-center">
                <?php if($featured->file_type == 'serial'): ?> 
                <?php
                if($featured->item_delimiter == 'comma')
                {
                $result_count = substr_count($featured->item_serials_list, ",");
                }
                else
                {
                $result_count = substr_count($featured->item_serials_list, "\n");
                }
                ?>
                <div class="font-size-sm mr-2"><i class="dwg-cart text-muted mr-1"></i><?php echo e($result_count); ?><span class="font-size-xs ml-1"><?php echo e(__('Stock')); ?></span>
                </div>
                <?php else: ?>
                <div class="font-size-sm mr-2">
                
                </div>
                <?php endif; ?>
                <div><?php if($featured->item_flash == 1): ?><del class="price-old"><?php echo e(Helper::price_format($allsettings->site_currency_position,$featured->extended_price,$currency_symbol,$multicurrency)); ?></del><?php endif; ?> <span class="price-badge rounded-sm py-1 px-2"><?php echo e(Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency)); ?></span></div>
              </div>
            </div>
          </div>
        </div>
        <!-- Product-->
        <?php $no++; ?>
	    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       </div>
    </section>
    <?php endif; ?>
    <?php if(count($free['items']) != 0): ?>
<section class="container mb-lg-1 flash-sale" data-aos="fade-up" data-aos-delay="200">
      <!-- Heading-->
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100"><?php echo e(__('Free Items')); ?></h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="<?php echo e(URL::to('/')); ?>/free-items"><?php echo e(__('Browse All Items')); ?><i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
      <!-- Grid-->
      <div class="row pt-2 mx-n2">
        <!-- Product-->
        <?php $no = 1; ?>
        <?php $__currentLoopData = $free['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featured): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        $price = Helper::price_info($featured->item_flash,$featured->extended_price);
        $count_rating = Helper::count_rating($featured->ratings);
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 px-2 mb-grid-gutter">
          <!-- Product-->
          <div class="card product-card-alt">
            <?php if($custom_settings->author_key == Helper::Key_Owner()): ?>
            <?php if($featured->item_flash == 1): ?>
            <div class="ribbon ribbon-top-left"><span><?php echo e($addition_settings->flash_sale_value); ?>% <?php echo e(__('OFF')); ?></span></div>
            <?php endif; ?>
            <?php endif; ?>
            <div class="product-thumb">
              <?php if(Auth::guest()): ?> 
              <a class="btn-wishlist btn-sm" href="<?php echo e(url('/')); ?>/login"><i class="dwg-heart"></i></a>
              <?php endif; ?>
              <?php if(Auth::check()): ?>
              <?php if($featured->user_id != Auth::user()->id): ?>
              <a class="btn-wishlist btn-sm" href="<?php echo e(url('/item')); ?>/<?php echo e(base64_encode($featured->item_id)); ?>/favorite/<?php echo e(base64_encode($featured->item_liked)); ?>"><i class="dwg-heart"></i></a>
              <?php endif; ?>
              <?php endif; ?>
              <div class="product-card-actions"><a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-eye"></i></a>
              <?php
              $checkif_purchased = Helper::if_purchased($featured->item_token);
              ?>
              
              <?php if($addition_settings->guest_checkout == 1): ?>
              <!--- Guest Checkout ON  -->
              <?php if($checkif_purchased == 0): ?>
              <?php if($featured->free_download == 0): ?>
              <?php if(Auth::check()): ?>
              <?php if(Auth::user()->id != 1 && $featured->user_id != Auth::user()->id): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/add-to-cart')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/add-to-cart')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <?php if(Auth::guest()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/login')); ?>"><i class="dwg-download"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/download/<?php echo e(base64_encode($featured->item_token)); ?>"><i class="dwg-download"></i></a>
              <?php endif; ?>
              <?php endif; ?> 
              <?php endif; ?>
              <!--- Guest Checkout ON  -->
              <?php else: ?>
              <!--- Guest Checkout OFF -->
              <?php if($checkif_purchased == 0): ?>
              <?php if($featured->free_download == 0): ?>
              <?php if(Auth::check()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
              <?php else: ?>
              <?php if(Auth::guest()): ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/login')); ?>"><i class="dwg-download"></i></a>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/download/<?php echo e(base64_encode($featured->item_token)); ?>"><i class="dwg-download"></i></a>
              <?php endif; ?>
              <?php endif; ?>
              <?php else: ?>
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><i class="dwg-cart"></i></a>
              <?php endif; ?>
               <!--- Guest Checkout OFF -->
              <?php endif; ?>
              
                
              </div><a class="product-thumb-overlay" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"></a>
                            <?php if($featured->item_preview!=''): ?>
                            <img class="lazy" src="<?php echo e(Helper::Image_Path($featured->item_preview,'no-image.png')); ?>" alt="<?php echo e($featured->item_name); ?>" width="300" height="200">
                            <?php else: ?>
                            <img class="lazy" src="<?php echo e(url('/')); ?>/public/img/no-image.png" alt="<?php echo e($featured->item_name); ?>" width="300" height="200">
                            <?php endif; ?>
                          </div>
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-start pb-2">
                <div class="text-muted font-size-xs mr-1"><a class="product-meta font-weight-medium" href="<?php echo e(URL::to('/shop')); ?>/item-type/<?php echo e($featured->item_type); ?>"><?php echo e(Helper::ItemTypeIdGetData($featured->item_type_id)); ?></a></div>
                <div class="star-rating">
                    <?php if($count_rating == 0): ?>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 1): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 2): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 3): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 4): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <?php endif; ?>
                    <?php if($count_rating == 5): ?>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <?php endif; ?>
                </div>
               </div>
              <h3 class="product-title font-size-sm mb-2"><a href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>">
			  <?php if($addition_settings->item_name_limit != 0): ?>
			  <?php echo e(mb_substr($featured->item_name,0,$addition_settings->item_name_limit,'utf-8').'...'); ?>

		      <?php else: ?>
			  <?php echo e($featured->item_name); ?>	  
			  <?php endif; ?>
			  </a></h3>
              <div class="card-footer d-flex align-items-center font-size-xs">
              <a class="blog-entry-meta-link" href="<?php echo e(URL::to('/user')); ?>/<?php echo e($featured->username); ?>">
                    <div class="blog-entry-author-ava">
                    <?php if($featured->user_photo!=''): ?>
                    <img class="lazy" src="<?php echo e(url('/')); ?>/public/storage/users/<?php echo e($featured->user_photo); ?>" alt="<?php echo e($featured->username); ?>" width="26" height="26">
                    <?php else: ?>
                    <img class="lazy" src="<?php echo e(url('/')); ?>/public/img/no-user.png" alt="<?php echo e($featured->username); ?>" width="26" height="26">
                    <?php endif; ?>
                    </div>
					<?php if($addition_settings->author_name_limit != 0): ?>
					<?php echo e(mb_substr($featured->username,0,$addition_settings->author_name_limit,'utf-8')); ?>

				    <?php else: ?>
				    <?php echo e($featured->username); ?>	  
				    <?php endif; ?> 
					<?php if($addition_settings->subscription_mode == 1): ?> <?php if($featured->user_document_verified == 1): ?> <span class="badges-success"><i class="dwg-check-circle danger"></i> <?php echo e(__('verified')); ?></span><?php endif; ?> <?php endif; ?></a>
                  <div class="ml-auto text-nowrap"><i class="dwg-time"></i> <?php echo e(date('d M Y',strtotime($featured->updated_item))); ?></div>
                </div>
              <div class="d-flex flex-wrap justify-content-between align-items-center">
                <?php if($featured->file_type == 'serial'): ?> 
                <?php
                if($featured->item_delimiter == 'comma')
                {
                $result_count = substr_count($featured->item_serials_list, ",");
                }
                else
                {
                $result_count = substr_count($featured->item_serials_list, "\n");
                }
                ?>
                <div class="font-size-sm mr-2"><i class="dwg-cart text-muted mr-1"></i><?php echo e($result_count); ?><span class="font-size-xs ml-1"><?php echo e(__('Stock')); ?></span>
                </div>
                <?php else: ?>
                <div class="font-size-sm mr-2">
                
                </div>
                <?php endif; ?>
                <div>
                <?php if($featured->free_download == 0): ?>
                <?php if($featured->item_flash == 1): ?><del class="price-old"><?php echo e(Helper::price_format($allsettings->site_currency_position,$featured->extended_price,$currency_symbol,$multicurrency)); ?></del><?php endif; ?> <span class="bg-faded-accent text-accent rounded-sm py-1 px-2"><?php echo e(Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency)); ?></span>
                <?php else: ?>
                <span class="price-badge rounded-sm py-1 px-2"><?php echo e(__('Free')); ?></span> 
                <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Product-->
        <?php $no++; ?>
	    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       </div>
    </section>
    <?php endif; ?>
    <?php if(count($newest['items']) != 0): ?>
    <section class="container pb-4 pb-md-5" data-aos="fade-up" data-aos-delay="200">
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100"><?php echo e(__('New Releases')); ?></h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="<?php echo e(URL::to('/new-releases')); ?>"><?php echo e(__('Browse All Items')); ?><i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
      <div class="row">
        <!-- Bestsellers-->
            <?php $no = 1; ?>
            <?php $__currentLoopData = $newest['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featured): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
            $price = Helper::price_info($featured->item_flash,$featured->extended_price);
        $count_rating = Helper::count_rating($featured->ratings);
            ?>
          <div class="col-lg-4 col-md-6 mb-2 py-3">
           <div class="widget">    
            <div class="media align-items-center pb-2 border-bottom">
            <a class="d-block mr-2" href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>">
            <?php if($featured->item_preview!=''): ?>
            <img class="lazy" src="<?php echo e(Helper::Image_Path($featured->item_preview,'no-image.png')); ?>"  alt="<?php echo e($featured->item_name); ?>" width="64" height="48">
           <?php else: ?>
           <img class="lazy" src="<?php echo e(url('/')); ?>/public/img/no-image.png" alt="<?php echo e($featured->item_name); ?>" width="64" height="48">
           <?php endif; ?>
            </a>
              <div class="media-body">
                <h6 class="widget-product-title"><a href="<?php echo e(URL::to('/item')); ?>/<?php echo e($featured->item_slug); ?>"><?php echo e($featured->item_name); ?></a></h6>
                <div class="widget-product-meta">
                <?php if($featured->free_download == 0): ?>
                <span class="text-accent"><?php echo e(Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency)); ?></span> <?php if($featured->item_flash == 1): ?><del class="price-old"><?php echo e(Helper::price_format($allsettings->site_currency_position,$featured->extended_price,$currency_symbol,$multicurrency)); ?></del><?php endif; ?>
                <?php else: ?>
                <span class="text-accent"><?php echo e(__('Free')); ?></span>
                <?php endif; ?>
                </div>
              </div>
            </div>
           </div>
        </div>
            <?php $no++; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       </div>
    </section>
    <?php endif; ?>
    <?php if($allsettings->site_features_display == 1): ?>
    <section class="bg-size-cover bg-position-center pt-5 pb-4 pb-lg-5 feature-panel" style="background-image: url('<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_banner); ?>');">
      <div class="container pt-lg-3" data-aos="fade-up" data-aos-delay="200">
        <h2 class="h3 mb-3 pb-4 text-light text-center"><?php echo e(__('Why Choose')); ?> <?php echo e($allsettings->site_title); ?>?</h2>
        <div class="row pt-lg-2 text-center">
          <div class="col-lg-3 col-md-3 col-sm-12 mb-grid-gutter" data-aos="fade-right" data-aos-delay="200">
            <div class="d-inline-block">
              <div class="media media-ie-fix align-items-center text-left"><span class="<?php echo e($allsettings->site_icon1); ?>"></span>
                <div class="media-body pl-3">
                  <h6 class="text-light font-size-base mb-1"><?php echo e($allsettings->site_text1); ?></h6>
                  <p class="text-light font-size-ms opacity-70 mb-0"><?php echo e($allsettings->site_sub_text1); ?></p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 mb-grid-gutter" data-aos="fade-right" data-aos-delay="200">
            <div class="d-inline-block">
              <div class="media media-ie-fix align-items-center text-left"><span class="<?php echo e($allsettings->site_icon2); ?>"></span>
                <div class="media-body pl-3">
                  <h6 class="text-light font-size-base mb-1"><?php echo e($allsettings->site_text2); ?></h6>
                  <p class="text-light font-size-ms opacity-70 mb-0"><?php echo e($allsettings->site_sub_text2); ?></p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 mb-grid-gutter" data-aos="fade-right" data-aos-delay="200">
            <div class="d-inline-block">
              <div class="media media-ie-fix align-items-center text-left"><span class="<?php echo e($allsettings->site_icon3); ?>"></span>
                <div class="media-body pl-3">
                  <h6 class="text-light font-size-base mb-1"><?php echo e($allsettings->site_text3); ?></h6>
                  <p class="text-light font-size-ms opacity-70 mb-0"><?php echo e($allsettings->site_sub_text3); ?></p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 mb-grid-gutter" data-aos="fade-right" data-aos-delay="200">
            <div class="d-inline-block">
              <div class="media media-ie-fix align-items-center text-left"><span class="<?php echo e($allsettings->site_icon4); ?>"></span>
                <div class="media-body pl-3">
                  <h6 class="text-light font-size-base mb-1"><?php echo e($allsettings->site_text4); ?></h6>
                  <p class="text-light font-size-ms opacity-70 mb-0"><?php echo e($allsettings->site_sub_text4); ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>
    <?php if($allsettings->site_blog_display == 1): ?>
    <?php if($allsettings->home_blog_display == 1): ?>
    <?php if(count($blog['data']) != 0): ?>
    <section class="container pb-4 pb-md-5 homeblog" data-aos="fade-up" data-aos-delay="200">
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100"><?php echo e(__('Our Blog')); ?></h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="<?php echo e(URL::to('/blog')); ?>"><?php echo e(__('Ream more posts')); ?><i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
        <div class="row">
          <?php $no = 1; ?>
          <?php $__currentLoopData = $blog['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-4 col-md-6 mb-2 py-3">
              <div class="card">
              <a class="blog-entry-thumb" href="<?php echo e(URL::to('/single')); ?>/<?php echo e($post->post_slug); ?>" title="<?php echo e($post->post_title); ?>">
              <?php if($post->post_image!=''): ?>
              <img class="lazy card-img-top" src="<?php echo e(url('/')); ?>/public/storage/post/<?php echo e($post->post_image); ?>" alt="<?php echo e($post->post_title); ?>" width="388" height="240">
              <?php else: ?>
              <img class="lazy card-img-top" src="<?php echo e(url('/')); ?>/public/img/no-image.png" width="388" height="240">
              <?php endif; ?>
              </a>
                <div class="card-body">
                  <h2 class="h6 blog-entry-title"><a href="<?php echo e(URL::to('/single')); ?>/<?php echo e($post->post_slug); ?>"><?php echo e($post->post_title); ?></a></h2>
                  <p class="font-size-sm">
				  <?php if($addition_settings->post_short_desc_limit != 0): ?>
				  <?php echo e(mb_substr($post->post_short_desc,0,$addition_settings->post_short_desc_limit,'utf-8').'...'); ?>

                  <?php else: ?>
				  <?php echo e($post->post_short_desc); ?>	  
                  <?php endif; ?>					  
				  </p>
                  <div class="font-size-xs text-nowrap"><span class="blog-entry-meta-link text-nowrap"><?php echo e(date('d M Y', strtotime($post->post_date))); ?></span><span class="blog-entry-meta-divider mx-2"></span><span class="blog-entry-meta-link text-nowrap"><i class="dwg-message"></i><?php echo e($comments->has($post->post_id) ? count($comments[$post->post_id]) : 0); ?></span></div>
                </div>
              </div>
            </div>
            <?php $no++; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
         </div>
        <!-- More button-->
     </section>
     <?php endif; ?> 
     <?php endif; ?>
     <?php endif; ?>
     <?php if(in_array('home',$bottom_ads)): ?>
    <section class="container pt-2" data-aos="fade-up" data-aos-delay="200">
      <div class="row">
          <div class="col-lg-12 mb-3" align="center">
             <?php echo html_entity_decode($addition_settings->bottom_ads); ?>
          </div>
       </div>   
    </section>   
    <?php endif; ?> 
    <?php if($custom_settings->promotion_popup == 1): ?>
    <?php /** promoation offer ***/ ?>
    <div id="my-welcome-message">
        <div class="header">
           <h3><?php echo e($custom_settings->pr_promo_heading); ?></h3>
        </div>
        <div class="bodies">
          <div class="container-fluid p-0">
           <div class="row no-gutters">
              <div class="col-md-12 textboxrow" style="background-image: url('<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($custom_settings->promotion_bg_image); ?>'); background-size:cover;">
              <h1><?php echo e($custom_settings->pr_promo_title_one); ?></h1>
              <h5><?php echo e($custom_settings->pr_promo_title_two); ?></h5>
              <p class="saledate"><?php echo e(__('Sales end')); ?> <?php echo e(date('M d, Y', strtotime($custom_settings->pr_promo_date))); ?></p>
              <a class="btn btn-outline-accent" href="<?php echo e($custom_settings->pr_promo_button_link); ?>"><?php echo e(__('Download Now')); ?></a>
              </div>
           </div>
          </div>    
         </div> 
        </div> 
        <?php endif; ?> 
<?php echo $__env->make('footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html><?php /**PATH C:\laragon\www\autoscripthub\resources\views/pages/index.blade.php ENDPATH**/ ?>