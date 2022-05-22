<?php
isLoggedRedirect();
include_once $path."templates/headerv3.php";
if (!isset($request[1])) {
    $request[1] = 'default';
}
if (!file_exists("templates/" . $request[0] . "-" . $request[1] . ".php")) {
    redirect($domain);
}
?>
<section class="padding-60-0-100">
    <div class="container blog-container-page">
        <div class="row justify-content-left mr-tp-60">
            <aside class="col-md-3 blog-sidebar">
                <div class="question-area-answer-navs">
                    <div class="nuhost-filter-list-container min-height-auto">
                        <h5 class="font-weight-bold pb-2" style="border-bottom: 1px solid #ddd"><i class="fa fa-user"></i> <?php echo $user->firstname.' '.$user->lastname ?></h5>
                        <ul id="nuhost-filter-list">
                            <li<?=($request[0] == 'musteri-paneli' && $request[1] == 'default' ? ' class="active"' : null)?>><a href="<?=$domain?>musteri-paneli">Panel Anasayfa <i class="fas fa-angle-right"></i></a></li>
                            <li<?=($request[0] == 'musteri-paneli' && $request[1] == 'bilgilerim' ? ' class="active"' : null)?>><a href="<?=$domain?>musteri-paneli/bilgilerim">Bilgilerim <i class="fas fa-angle-right"></i></a></li>
                            <li<?=($request[0] == 'musteri-paneli' && $request[1] == 'sifre-degistir' ? ' class="active"' : null)?>><a href="<?=$domain?>musteri-paneli/sifre-degistir">Şifre Değiştir <i class="fas fa-angle-right"></i></a></li>
                            <li<?=($request[0] == 'musteri-paneli' && $request[1] == 'siparislerim' ? ' class="active"' : null)?>><a href="<?=$domain?>musteri-paneli/siparislerim">Siparişlerim <i class="fas fa-angle-right"></i></a></li>
                            <li<?=($request[0] == 'musteri-paneli' && $request[1] == 'destek' ? ' class="active"' : null)?>><a href="<?=$domain?>musteri-paneli/destek">Destek <i class="fas fa-angle-right"></i></a></li>
                            <li><a href="<?php echo $domain ?>cikis">Çıkış Yap <i class="fas fa-angle-right"></i></a></li>
                        </ul>
                    </div>
                </div>
            </aside>
            <div class="col-md-9">
                <div class="dashboard">
                    <?php require_once("templates/" . $request[0] . "-" . $request[1] . ".php"); ?>
                </div>
            </div>
        </div>
    </div>
</section>