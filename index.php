<?php
require_once('functions.php');
//fake_products_generator();


//$conn = new mysqli('localhost', 'root', '', 'retrojuegos');
//
//if (session_status() == PHP_SESSION_NONE) 
//{
//    session_start();
//} 
//    
require_once('header.php');
?>
      <!-- Hero slider-->
      <section class="tns-carousel tns-controls-lg">
        <div class="tns-carousel-inner" data-carousel-options='{
         "mode": "gallery",
         "autoplay": true,
         "autoplayTimeout": 5000,
         "autoplayButtonOutput": false,
         "responsive": {
           "0": { "nav": true, "controls": false },
           "992": { "nav": false, "controls": true }
         }
         }'>
          <!-- Item-->
          <div class="px-lg-5" style="background-color: #3aafd2;">
            <div class="d-lg-flex justify-content-between align-items-center ps-lg-4"><img class="d-block order-lg-2 me-lg-n5 flex-shrink-0" src="img/home/hero-slider/01.png" alt="Castillocastke">
              <div class="position-relative mx-auto me-lg-n5 py-5 px-4 mb-lg-5 order-lg-1" style="max-width: 42rem; z-index: 10;">
                <div class="pb-lg-5 mb-lg-5 text-center text-lg-start text-lg-nowrap">
                  <h3 class="h2 text-light fw-light pb-1 from-start"><?= __('hero1_subtitle') ?></h3>
                  <h2 class="text-light display-5 from-start delay-1 hero-title"><?= __('hero1_title') ?></h2>
                  <p class="fs-lg text-light pb-3 from-start delay-2"><?= __('hero1_description') ?></p>
                  <div class="d-table scale-up delay-4 mx-auto mx-lg-0"><a class="btn btn-primary" href="shop.php"><?= __('hero1_button') ?><i class="ci-arrow-right ms-2 me-n1"></i></a></div>
                </div>
              </div>
            </div>
          </div>
          <!-- Item-->
          <div class="px-lg-5" style="background-color: #f5b1b0;">
            <div class="d-lg-flex justify-content-between align-items-center ps-lg-4"><img class="d-block order-lg-2 me-lg-n5 flex-shrink-0" src="img/home/hero-slider/02.png" alt="Cielonocturnonightsky">
              <div class="position-relative mx-auto me-lg-n5 py-5 px-4 mb-lg-5 order-lg-1" style="max-width: 42rem; z-index: 10;">
                <div class="pb-lg-5 mb-lg-5 text-center text-lg-start text-lg-nowrap">
                  <h3 class="h2 text-light fw-light pb-1 from-bottom"><?= __('hero2_subtitle') ?></h3>
                  <h2 class="text-light display-5 from-bottom delay-1 hero-title"><?= __('hero2_title') ?></h2>
                  <p class="fs-lg text-light pb-3 from-bottom delay-2"><?= __('hero2_description') ?></p>
                  <div class="d-table scale-up delay-4 mx-auto mx-lg-0"><a class="btn btn-primary" href="shop.php"><?= __('hero2_button') ?><i class="ci-arrow-right ms-2 me-n1"></i></a></div>
                </div>
              </div>
            </div>
          </div>
          <!-- Item-->
          <div class="px-lg-5" style="background-color: #eba170;">
            <div class="d-lg-flex justify-content-between align-items-center ps-lg-4"><img class="d-block order-lg-2 me-lg-n5 flex-shrink-0" src="img/home/hero-slider/03.png" alt="Callecomercialhighstreet">
              <div class="position-relative mx-auto me-lg-n5 py-5 px-4 mb-lg-5 order-lg-1" style="max-width: 42rem; z-index: 10;">
                <div class="pb-lg-5 mb-lg-5 text-center text-lg-start text-lg-nowrap">
                  <h3 class="h2 text-light fw-light pb-1 from-top"><?= __('hero3_subtitle') ?></h3>
                  <h2 class="text-light display-5 from-top delay-1 hero-title"><?= __('hero3_title') ?></h2>
                  <p class="fs-lg text-light pb-3 from-top delay-2"><?= __('hero3_description') ?></p>
                  <div class="d-table scale-up delay-4 mx-auto mx-lg-0"><a class="btn btn-primary" href="shop.php"><?= __('hero3_button') ?><i class="ci-arrow-right ms-2 me-n1"></i></a></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>    
<?php
require_once('footer.php');
?>   