<?php
include_once $path . "templates/headerv3.php";
if(session('front')) {
    redirect($domain);
}
?>
<section class="form-contact-home-section">
    <!-- start contact us section -->
    <div class="container">
        <!-- start container -->
        <div class="row justify-content-center">
            <!-- start row -->
            <form class="col-md-8 row justify-content-center form-contain-home" style="margin-bottom: 100px;" id="loginForm" method="post">
                <!-- start form -->
                <h5 style="width: 100%;">Giriş Yapın</h5><!-- title -->
                <div class="col-md-12">
                    <!-- start col -->
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="email" id="email" name="email" required><!-- input -->
                        <span class="input-group-prepend">E-Posta Adresi</span><!-- label -->
                    </div>
                </div><!-- end col -->

                <div class="col-md-12">
                    <!-- start col -->
                    <div class="field input-field">
                        <input class="form-contain-home-input" type="password" id="password" name="password" required>
                        <!-- input -->
                        <span class="input-group-prepend">Şifre</span><!-- label -->
                    </div>
                </div><!-- end col -->
                <div class="btn-holder-contect text-center">
                    <div class="form-group text-center">
                        <div class="d-block">
                            <a class="btn btn-link text-muted" href="#">
                                Şifremi Unuttum
                            </a>
                            <a class="btn btn-link text-muted" href="<?php echo $domain ?>kayitol">
                                Hesap Oluştur
                            </a>
                        </div>
                    </div>
                    <button type="submit" class=>GİRİŞ YAP</button><!-- submit button -->
                </div>
            </form><!-- end form -->

        </div><!-- end container -->
    </div><!-- end container -->
</section>