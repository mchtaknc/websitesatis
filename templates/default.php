<?php include_once $path."templates/headerv1.php";?>
<section class="padding-60-0-100">
    <div class="container blog-container-page">
        <?php
        $themes = $db->query("select * from themes where status = 1 order by theme_id desc limit 6")->fetchAll();
        ?>
        <div class="tittle-simple-one mb-5"><h5>Öne Çıkan Temalar</h5></div><!-- title -->
        <div class="row">
            <?php
            foreach($themes as $item) {
                $image = $db->prepare("select * from theme_images where theme_id = ? and (featured = 1 or featured = 0) limit 1");
                $image->execute([$item->theme_id]);
                $image = $image->fetch();
                $category = $db->query("select * from theme_categories where category_id = '{$item->category_id}'")->fetch();
                ?>
            <div class="col-md-4">
                <div class="themes">
                    <a href="<?php echo $domain.$item->slug?>" class="image">
                        <img src="<?php echo $domain.$image->image ?>">
                    </a>
                    <h6>
                        <a href="<?php echo $domain.$item->slug?>"><?php echo $item->name ?></a>
                        <a href="<?php echo $domain ?>hazir-web-sitesi?kategori=<?php echo $category->slug ?>">Kategori: <?php echo $category->name ?></a>
                    </h6>
                    <div class="themesFooter">
                        <span class="view"><i class="fa fa-eye"></i> <?php echo $item->view ?></span>
                        <span class="price"><?php echo number_format($item->price,2) ?> TL</span>
                    </div>
                    <div class="detail"><a href="<?php echo $domain.$item->slug?>" class="header-order-button-slid">Görüntüle</a></div>
                </div>
            </div>
            <?php } ?>
        </div>
        <a href="<?php echo $domain ?>hazir-web-sitesi" class="btn-order-default-nuhost morethemeBtn">Daha Fazlasını Göster</a>
    </div>
</section>
<!--<div class="container padding-100-0">
    <div class="row justify-content-center domain-homepage-anouncement-hero m-0">
        <div class="col-md-6 mt-auto mb-auto">
            <h4 class="domain-homepage-anouncement-title">Why do you need a <br>domain name?</h4>
            <p class="domain-homepage-anouncement-sub-title"> The most successful businesses use the same set of words and images in all customer touchpoints – on their website, in their emails and order confirmations, on their signs, etc.</p>
            <p class="domain-homepage-anouncement-sub-title-two"> This is branding at its simplest. And the digital pieces of your brand all spring from your domain name.</p>
            <br>
            <div id="fisrt-domains-offre-content" class="domain-homepage-anouncement-speacial">
                <span class="domain-tci">.tel</span>
                <a id="fisrt-domains-offre" class="domain-tci-order"><span>register</span></a>
                <div class="domain-tci-buttons">
                    <i id="fisrt-domains" data-toggle="tooltip" data-placement="top" title="" class="far fa-check-circle domain-tci-check" data-original-title="add to cart"></i>
                    <i id="fisrt" data-toggle="tooltip" data-placement="top" title="" class="far fa-times-circle domain-tci-cancel" data-original-title="cancel order"></i>
                </div>
                <form class="domain-homepage-anouncement-speacial-form">
                    <input placeholder="entre your domain name" type="text" class="form-control input-search-text-special" required="">
                </form>
                <div class="domain-tci-added-to-card-mesage">
                    <span>this domain added successfully to your cart <a href="#">checkout</a></span>
                </div>
            </div>
            <div id="second-domains-offre-content" class="domain-homepage-anouncement-speacial">
                <span class="domain-tci">.one</span>
                <a id="second-domains-offre" class="domain-tci-order"><span>register</span></a>
                <div class="domain-tci-buttons">
                    <i id="second-domains" data-toggle="tooltip" data-placement="top" title="" class="far fa-check-circle domain-tci-check" data-original-title="add to cart"></i>
                    <i id="second" data-toggle="tooltip" data-placement="top" title="" class="far fa-times-circle domain-tci-cancel" data-original-title="cancel order"></i>
                </div>
                <form class="domain-homepage-anouncement-speacial-form">
                    <input placeholder="entre your domain name" type="text" class="form-control input-search-text-special" required="">
                </form>
                <div class="domain-tci-added-to-card-mesage">
                    <span>this domain added successfully to your cart <a href="#">checkout</a></span>
                </div>
            </div>
            <div id="third-domains-offre-content" class="domain-homepage-anouncement-speacial">
                <span class="domain-tci">.hosting</span>
                <a id="third-domains-offre" class="domain-tci-order"><span>register</span></a>
                <div class="domain-tci-buttons">
                    <i id="third-domains" data-toggle="tooltip" data-placement="top" title="" class="far fa-check-circle domain-tci-check" data-original-title="add to cart"></i>
                    <i id="third" data-toggle="tooltip" data-placement="top" title="" class="far fa-times-circle domain-tci-cancel" data-original-title="cancel order"></i>
                </div>
                <form class="domain-homepage-anouncement-speacial-form">
                    <input placeholder="entre your domain name" type="text" class="form-control input-search-text-special" required="">
                </form>
                <div class="domain-tci-added-to-card-mesage">
                    <span>this domain added successfully to your cart <a href="#">checkout</a></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 row justify-content-center phone-no-sidepadding">
            <div class="col-md-6">
                <div class="domain-homepage-anouncement-box">
                    <div class="domain-homepage-anouncement-box-img">
                        <img src="img/svgs/book.svg" alt="">
                    </div>
                    <h5>Stake your claim</h5>
                    <p>Registering domains related to your big idea or business name keeps others from using those names to pull traffic away from your website.</p>
                </div>
                <div class="domain-homepage-anouncement-box">
                    <div class="domain-homepage-anouncement-box-img">
                        <img src="img/svgs/ring.svg" alt="">
                    </div>
                    <h5>Take control</h5>
                    <p>With a domain name, you can send customers, friends and prospects wherever you want – whether that’s a website, blog, social page or storefront.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="domain-homepage-anouncement-box margin-top-150">
                    <div class="domain-homepage-anouncement-box-img">
                        <img src="img/svgs/dollar.svg" alt="">
                    </div>
                    <h5>Protect your rights</h5>
                    <p>Your domain gives you an exclusive piece of digital real estate that cannot be used by anyone else as long as it’s registered to you.</p>
                </div>
            </div>
        </div>
    </div>
</div>-->
<section class="form-contact-home-section home-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <img class="img-fluid" src="<?php echo assets_url('theme/img/3adim-min2.png')?>">
            </div>
            <div class="col-md-7 text-tab-content-algo">
                <div class="text-absoo">
                    <h5>Web Sitenizi Çok Fazla Vakit Harcamadan Oluşturun</h5><!-- title -->
                    <p>Size uygun paketlerimizden birini seçerek işlemlerinizi tamamlayabilir ve site panelinin kullanım
                        kolaylığı sayesinde sitenizi hızlıca yayına hazır hale getirebilirisiniz.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-7 text-tab-content-algo">
                <div class="text-absoo">
                    <h5>Tüm Ekranlara %100 Duyarlı Tasarımlar</h5><!-- title -->
                    <p>Bilgisayar, tablet, telefon vb. tüm ekranlara duyarlı, pratik ve hızlı tasarımlar sayesinde sitenize her cihazdan kolay kullanım.</p><!-- text -->
                </div>
            </div>
            <div class="col-md-5">
                <img src="<?php echo assets_url('theme/img/mobiluyumluluk-min2.png')?>" class="img-fluid">
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-5">
                <img class="img-fluid" src="<?php echo assets_url('theme/img/destek-min.png')?>">
            </div>
            <div class="col-md-7 text-tab-content-algo">
                <div class="text-absoo">
                    <h5>Seo Ayarları</h5><!-- title -->
                    <p>Web sitenizin Google ve diğer arama motorlarında üst sıralarda yer alabilmesi için gerekli ayarlar düzenlenmiş olarak teslim edilir. Ayrıca Facebook, Twitter meta etiketleri sayesinde aramalarda daha iyi bir sonuç alabilirsiniz.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="form-contact-home-section">
    <!-- start contact us section -->
    <div class="container">
        <!-- start container -->
        <div class="row justify-content-center">
            <!-- start row -->
            <form class="col-md-8 row justify-content-center form-contain-home" id="ajax-contact" method="post">
                <!-- start form -->
                <h5 style="width: 100%;">Detaylı Bilgi Almak İçin<span>Hemen İletişime Geçin</span></h5><!-- title -->

                <div id="form-messages"></div><!-- form message -->

                <div class="col-md-6">
                    <!-- start col -->
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="text" id="name" name="name" required=""><!-- input -->
                        <span class="input-group-prepend">Ad-Soyad</span><!-- label -->
                    </div>
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="text" id="phone" name="phone" required=""><!-- input -->
                        <span class="input-group-prepend">Telefon</span><!-- label -->
                    </div>
                </div><!-- end col -->

                <div class="col-md-6">
                    <!-- start col -->
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="email" id="email" name="email" required="">
                        <!-- input -->
                        <span class="input-group-prepend">E-Posta</span><!-- label -->
                    </div>
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="text" id="subject" name="subject" required=""><!-- input -->
                        <span class="input-group-prepend">Konu</span><!-- label -->
                    </div>
                </div><!-- end col -->

                <div class="col-md-12">
                    <!-- start col -->
                    <div class="field input-field">
                        <textarea class="form-contain-home-input" id="message" name="message" required=""></textarea>
                        <!-- textarea -->
                        <span class="input-group-prepend">Mesaj</span><!-- label -->
                    </div>
                </div><!-- end col -->

                <div class="btn-holder-contect">
                    <button type="submit">Gönder</button><!-- submit button -->
                </div>
            </form>
        </div><!-- end container -->
    </div><!-- end container -->
</section>