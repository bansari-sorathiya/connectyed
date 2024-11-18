<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Connectyed.com</title>

        <!-- site favicon -->
        <link rel="icon" type="image/png" href="assets/images/favicon.png">
        <!-- Place favicon.ico in the root directory -->

        <!-- All stylesheet and icons css  -->
        <link rel="stylesheet" href="<?=url('/');?>/assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?=url('/');?>/assets/css/animate.css">
        <link rel="stylesheet" href="<?=url('/');?>/assets/css/all.min.css">
        <link rel="stylesheet" href="<?=url('/');?>/assets/css/swiper.min.css">
        <link rel="stylesheet" href="<?=url('/');?>/assets/css/lightcase.css">
        <link rel="stylesheet" href="<?=url('/');?>/assets/css/connectyed.css">        
        @vite(['resources/js/app.js'])
    </head>
    <body class="bg-connectyed-body-light">
        <!-- preloader start here -->
        <div class="preloader">
            <div class="preloader-inner">
                <div class="preloader-icon">
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
        <!-- preloader ending here -->

        <!-- scrollToTop start here -->
        <a href="#" class="scrollToTop"><i class="fa-solid fa-angle-up"></i></a>
        <!-- scrollToTop ending here -->
        
        <div id="app">
            <router-view></router-view>
        </div>

        <!-- All Needed JS -->
        <script src="<?=url('/');?>/assets/js/vendor/jquery-3.6.0.min.js"></script>
        <script src="<?=url('/');?>/assets/js/vendor/modernizr-3.11.2.min.js"></script>
        <script src="<?=url('/');?>/assets/js/isotope.pkgd.min.js"></script>
        <script src="<?=url('/');?>/assets/js/swiper.min.js"></script>
        <!-- <script src="assets/js/all.min.js"></script> -->
        <script src="<?=url('/');?>/assets/js/wow.js"></script>
        <script src="<?=url('/');?>/assets/js/counterup.js"></script>
        <script src="<?=url('/');?>/assets/js/jquery.countdown.min.js"></script>
        <script src="<?=url('/');?>/assets/js/lightcase.js"></script>
        <script src="<?=url('/');?>/assets/js/waypoints.min.js"></script>
        <script src="<?=url('/');?>/assets/js/vendor/bootstrap.bundle.min.js"></script>
        <script src="<?=url('/');?>/assets/js/plugins.js"></script>
        <script src="<?=url('/');?>/assets/js/main.js"></script>
    </body>
</html>
