<div class="top-header-nav-home mb-auto">
    <div class="container">
        <div class="topbar">
            <ul>
                <li><a href="#"><i class="fa fa-file-invoice"></i> Ödeme Bildirim Formu</a></li>
                <li><a href="<?php echo $domain ?>banka-hesaplari"><i class="fa fa-credit-card"></i> Banka Hesapları</a></li>
            </ul>
            <ul>
                <?php if(!isLogged()) { ?>
                <li><a href="<?php echo $domain ?>kayitol"><i class="fa fa-user-plus"></i> Hesap Oluştur</a></li>
                <li><a href="<?php echo $domain ?>giris"><i class="fa fa-sign"></i> Giriş Yap</a></li>
                <?php } else { ?>
                <li><a href="<?php echo $domain ?>musteri-paneli"><i class="fa fa-user-alt"></i> Müşteri Paneli</a></li>
                <?php } ?>
                <li><a href="<?php echo $domain ?>sepet"><i class="fa fa-shopping-basket"></i> Sepetim</a></li>
            </ul>
        </div>
        <nav class="navbar navbar-expand-md navbar-light header-nav-algo-coodiv header-nav-algo-coodiv-v2">
            <!-- start logo place -->
            <a class="navbar-brand" href="<?php echo $domain ?>">
                <img class="black-bg-logo" src="<?php echo assets_url('theme/img/logo-2.png') ?>"
                     alt="Sitedeposu Logo"/>
                <!-- black background logo -->
                <img class="white-bg-logo" src="<?php echo assets_url('theme/img/logo-1.png') ?>"
                     alt="Sitedeposu Logo"/>
                <!-- white background logo -->
            </a>
            <!-- end logo place -->
            <button class="navbar-toggle offcanvas-toggle menu-btn-span-bar ml-auto" data-toggle="offcanvas"
                    data-target="#offcanvas-menu-home">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <!-- start collapse navbar -->
            <div class="collapse navbar-collapse navbar-offcanvas" id="offcanvas-menu-home">
                <!-- start navbar -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $domain ?>">Anasayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $domain ?>hakkimizda">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $domain ?>hazir-web-sitesi">Temalar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $domain ?>blog">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $domain ?>iletisim">İletişim</a>
                    </li>
                </ul>
                <!-- end navbar -->
            </div>
            <!-- end collapse navbar -->
            <!-- start header account  -->
            <ul class="account-place-header-nav">
                <?php
                if (isLogged()) {
                    echo '<li class="nav-item dropdown">
                            <a data-toggle="dropdown" aria-haspopup="true" id="panel" aria-expanded="false" class="accouting-h dropdown-toggle" href="#">
                                <img src="' . assets_url('theme/img/svgs/avatar.svg') . '" alt="">
                            </a>
                            <div class="dropdown-menu login-drop-down-header" aria-labelledby="panel" style="min-width: 100%">
                                <a class="dropdown-item" href="' . $domain . 'musteri-paneli">Müşteri Paneli</a>
                                <a class="dropdown-item" href="' . $domain . 'cikis">Çıkış Yap</a>
                            </div>
                </li>';
                } else {
                    echo '<li class="nav-item dropdown">
                    <a class="accouting-h" style="color: #000" href="' . $domain . 'giris">
                        <img src="' . assets_url('theme/img/svgs/avatar.svg') . '" alt="">
                    </a>
                </li>';
                }
                ?>
            </ul>
            <ul class="account-place-header-nav">
                <li class="nav-item dropdown">
                    <a class="accouting-h cart" style="color: #000" href="<?php echo $domain ?>sepet">
                        <i class="fa fa-shopping-cart"></i>
                        <span><?php echo count($_SESSION['cart']['items']) ?></span>
                    </a>
                </li>
            </ul>
            <!-- end header account  -->
        </nav>
    </div><!-- end container -->
</div>