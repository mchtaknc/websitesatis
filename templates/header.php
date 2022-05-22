<!DOCTYPE html>
<!-- start html tag -->
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" href="<?php echo assets_url('theme/img/sitedeposu-fav.png')?>">
    <title><?php echo $seo_title ?></title>
    <meta name="description" content="<?php echo $seo_description ?>">
    <meta property="og:url" content="<?php echo $seo_url ?>" />
    <meta property="og:title" content="<?php echo $seo_title ?>" />
    <meta property="og:description" content="<?php echo $seo_description ?>" />
    <meta name="robots" content="all" />
    <!-- Bootstrap core CSS -->
    <link href="<?php echo assets_url('theme/css/bootstrap.min.css')?>" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="<?php echo assets_url('theme/css/alertify.min.css')?>" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link href="<?php echo assets_url('theme/css/themes/semantic.min.css')?>" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?php echo assets_url('theme/css/dataTables.bootstrap4.min.css')?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <!-- flaticon styles -->
    <link href="<?php echo assets_url('theme/icons/flaticon.css')?>" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <!-- flaticon styles -->
    <link href="<?php echo assets_url('theme/icons-t/flaticon.css')?>" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <!-- main template styles -->
    <link href="<?php echo assets_url('theme/css/main.css?v='.filemtime($path.'assets/theme/css/main.css'))?>" rel="stylesheet">
    <!-- bootstrap offcanvas styles -->
    <link href="<?php echo assets_url('theme/css/bootstrap.offcanvas.min.css')?>" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <!-- fontawesome styles -->
    <link href="<?php echo assets_url('theme/css/fontawesome-all.min.css')?>" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <!-- animate styles -->
    <link href="<?php echo assets_url('theme/css/animate.css')?>" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <!-- responsive template styles -->
    <link href="<?php echo assets_url('theme/css/responsive.css?v='.filemtime($path.'assets/theme/css/responsive.css'))?>" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <!-- flickity Stylesheets -->
    <link rel="stylesheet" href="<?php echo assets_url('theme/css/flickity.min.css')?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <!-- Owl Stylesheets -->
    <link rel="preload" href="<?php echo assets_url('theme/owlcarousel/assets/owl.carousel.min.css')?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="<?php echo assets_url('theme/owlcarousel/assets/owl.theme.default.min.css')?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <!-- technology icon Stylesheets -->
    <link rel="preload" href="<?php echo assets_url('theme/technology-icon/flaticon.css')?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/owlcarousel/assets/owl.theme.default.min.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/owlcarousel/assets/owl.carousel.min.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/css/flickity.min.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/css/responsive.css?v='.filemtime($path.'assets/theme/css/responsive.css'))?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/css/animate.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/css/fontawesome-all.min.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/css/bootstrap.offcanvas.min.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/icons-t/flaticon.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/technology-icon/flaticon.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/icons/flaticon.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/css/dataTables.bootstrap4.min.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/css/themes/semantic.min.css')?>"></noscript>
    <noscript><link rel="stylesheet" href="<?php echo assets_url('theme/css/alertify.min.css')?>"></noscript>
    <script>
        var domain = "<?php echo $domain ?>";
    </script>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MWJX7JW');</script>
    <!-- End Google Tag Manager -->
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GTM-MWJX7JW"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-1EHH6JYP42');
    </script>
</head>
<body><!-- start body tag -->