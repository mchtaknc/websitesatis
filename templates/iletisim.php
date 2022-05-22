<?php include_once $path . "templates/headerv2.php"; ?>
<section class="padding-100-0">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center about-us-page-title contact-us-background">
                <h6 class="about-us-page-title-title"> Hala sorularınız mı var? </h6>
                <h2 class="about-us-page-title-sub-title">İletişim formunu kullanarak veya <br> e-posta göndererek bizimle iletişim kurabilirsiniz.</h2>
                <a class="about-us-contact-way" href="mailto:info@sitedeposu.com"><i class="fas fa-at"></i> info@sitedeposu.com</a>
                <a class="about-us-contact-way" href="tel:05444257679"><i class="fas fa-phone"></i> 0544 425 76 79</a>

            </div>
        </div>

        <div class="row question-area-page justify-content-left mr-tp-120">
            <div class="col-md-8">
                <div class="question-area-answer-banner">
                    <form class="row justify-content-center form-contain-home contact-page-form-send" id="ajax-contact" method="post">
                        <!-- start form -->
                        <h5>İletişime Geçin <span>Hizmetlerimizin işinizi nasıl büyüteceğini keşfedin.</span></h5>
                        <div id="form-messages"></div><!-- form message -->

                        <div class="col-md-6">
                            <!-- start col -->
                            <div class="field input-field">
                                <input class="form-contain-home-input" type="text" id="name" name="name" required><!-- input -->
                                <span class="input-group-prepend">Ad-Soyad</span><!-- label -->
                            </div>
                            <div class="field input-field">
                                <input class="form-contain-home-input" type="text" name="phone" required><!-- input -->
                                <span class="input-group-prepend">Telefon</span><!-- label -->
                            </div>
                        </div><!-- end col -->

                        <div class="col-md-6">
                            <!-- start col -->
                            <div class="field input-field">
                                <input class="form-contain-home-input" type="email" id="email" name="email" required>
                                <!-- input -->
                                <span class="input-group-prepend">E-Posta</span><!-- label -->
                            </div>
                            <div class="field input-field">
                                <input class="form-contain-home-input" type="text" name="subject" required><!-- input -->
                                <span class="input-group-prepend">Konu</span><!-- label -->
                            </div>
                        </div><!-- end col -->

                        <div class="col-md-12">
                            <!-- start col -->
                            <div class="field input-field">
                                <textarea class="form-contain-home-input" id="message" name="message"
                                          required></textarea><!-- textarea -->
                                <span class="input-group-prepend">Mesajınızı Giriniz</span><!-- label -->
                            </div>
                        </div><!-- end col -->

                        <div class="btn-holder-contect">
                            <button type="submit">Gönder</button><!-- submit button -->
                        </div>

                    </form><!-- end form -->
                </div>
            </div>

            <div class="col-md-4">
                <div class="contact-other-method-box">
                    <h5>Yardım mı gerekiyor ? <span>zaten müşterimiz misin ? o halde yeni bir destek talebi oluşturabilirsin</span></h5>
                    <a class="btn-order-default-nuhost" href="<?php echo $domain ?>talep-olustur">Yeni Talep Oluştur</a>
                </div>
                <div class="contact-other-method-box">
                    <h5>Yardım Merkezi</h5>
                    <a class="btn-order-default-nuhost" href="#">Yardım Merkezine Git</a>
                </div>
            </div>

        </div>


    </div>
</section>