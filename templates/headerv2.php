<div id="header" class="homepagetwostyle d-flex mx-auto flex-column not-index-header">
    <div class="header-animation">
        <div id="particles-bg"></div>
        <div class="video-bg-nuhost-header">
            <div id="video_cover"></div>
            <video autoplay muted loop>
            <span class="video-bg-nuhost-header-bg"></span>
        </div>
        <span class="courve-gb-hdr-top"></span>
    </div>
    <?php include_once $path."templates/nav.php"; ?>

    <main class="inner cover header-heeadline-title mb-auto">
        <h5><span class="blok-on-phon"><?php echo $title ?></span></h5>
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb not-index-breadcrumb-header justify-content-center">
                    <li class="breadcrumb-item"><a href="<?php echo $domain ?>">Anasayfa</a></li>
                    <?php
                    foreach($breadcrumb as $item) { ?>
                        <li class="breadcrumb-item"><a href="<?php echo $domain.$item['url'] ?>"><?php echo $item['name']?></a></li>
                    <?php } ?>
                </ol>
            </nav>
        </div>
    </main>
    <div class="mt-auto"></div>
</div>
<!-- start header -->