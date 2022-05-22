<?php include_once $path . "templates/headerv2.php";?>
<section class="white-gray-border-top" style="padding-top: 100px;">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="product-detail-image">
                    <a href="#" data-fancybox="gallery">
                        <?php if($firstImg !== false) { ?>
                        <img class="img-fluid" src="<?php echo $domain.$images[$firstImg]->image ?>">
                        <?php } ?>
                        <?php if($firstImg === false && !empty($images)) { ?>
                        <img class="img-fluid" src="<?php echo $domain.$images[0]->image ?>">
                        <?php } ?>
                        <?php if(empty($images)) { ?>
                        <img class="img-fluid" src="<?php echo assets_url('theme/img/unnamed.png') ?>">
                        <?php } ?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-details">
                    <h4><?php echo $theme->name ?></h4>
                    <?php if (!empty(json_decode(unclear($theme->featured_specifications,1)))) { ?>
                    <ul>
                        <?php foreach (json_decode(unclear($theme->featured_specifications),1) as $item) { ?>
                        <li><i class="fa fa-check"></i> <?php echo $item['value'] ?></li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                    <div class="product-details-footer">
                        <p>Fiyat: <?php echo $theme->price ?> TL</p>
                        <div class="shop-details">
                            <h5 class="font-weight-bold mb-3">Kurulum Bilgileri</h5>
                            <form id="add_domain" class="add_domain" method="post">
                                <div class="new-domain">
                                    <label class="custom-radio">
                                        Kendime ait alan adım var.
                                        <input type="radio" class="domainreg" name="installDomain" value="owndomain" checked="">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="custom-radio">
                                        Yeni alan adı kaydetmek istiyorum.
                                        <input type="radio" class="domainreg" name="installDomain" value="domainregister">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label class="custom-radio">
                                        Alan adımı transfer edeceğim.
                                        <input type="radio" class="domainreg" name="installDomain" value="domaintransfer">
                                        <span class="checkmark"></span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control domaincontrol" name="domain" placeholder="Örn: alanadiniz.com">
                                    </div>
                                    <button type="button" class="btn header-order-button-primary domaincontrolbtn" style="display: none;"><i class="fa fa-search"></i> Kontrol Et
                                    </button>
                                </div>
                                <input type="hidden" name="product" value="<?php echo $theme->theme_id ?>">
                            </form>
                        </div>
                        <a href="<?php echo $theme->demo_link ?>" class="header-order-button-slid" target="_blank">Demoyu İncele</a>
                        <button type="submit" class="header-order-button-primary orderBtn border-0" form="add_domain">Satın Al</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tabs" style="padding-bottom: 100px;">
        <div class="container">
            <ul class="nav mr-tp-70 resslers-features-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="Applications-tab" data-toggle="tab" href="#Applications" role="tab"
                       aria-controls="Applications" aria-selected="true">Detaylar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pay-tab" data-toggle="tab" href="#pay" role="tab"
                       aria-controls="pay" aria-selected="false">Ödeme Yöntemleri</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="Applications" role="tabpanel"
                     aria-labelledby="Applications-tab">
                    <div class="row text-left">
                        <div class="col-md-10 resslers-tabs-content-with-image d-block">
                            <div class="resslers-tabs-content-with-image-text">
                                <?php echo unclear($theme->description) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pay" role="tabpanel" aria-labelledby="pay-tab">
                    <div class="row">
                        <div class="col-md-10 resslers-tabs-content-with-image d-block">
                            <div class="resslers-tabs-content-with-image-text">
                                <h5>
                                    Banka havalesi ve kredi kartına taksitle ödeme yapabilirsiniz.
                                    <br>
                                    Sipariş sayfasında ödeme tipini seçebilirsiniz.
                                </h5>
                                <div class="pt-5">
                                    <img src="<?php echo assets_url('theme/img/cards/axess.png')?>" />
                                    <img src="<?php echo assets_url('theme/img/cards/bonus.png')?>" />
                                    <img src="<?php echo assets_url('theme/img/cards/cardfinans.png')?>" />
                                    <img src="<?php echo assets_url('theme/img/cards/maximum.png')?>" />
                                    <img src="<?php echo assets_url('theme/img/cards/paraf.png')?>" />
                                    <img src="<?php echo assets_url('theme/img/cards/world.png')?>" />
                                    <img src="<?php echo assets_url('theme/img/cards/visa.png')?>" />
                                    <img src="<?php echo assets_url('theme/img/cards/mastercard.png')?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded',function(){
        $(".domainreg").change(function (e) {
            var val = $(this).val();
            if (val === 'domainregister') {
                $(".domaincontrolbtn").fadeIn();
                $(".orderBtn").attr('disabled',true);
            } else {
                $('.domaincontrol').parent('.input-group').find('span').remove();
                $(".orderBtn").removeAttr('disabled');
                $(".domaincontrolbtn").fadeOut();
            }
        });
        $(".domaincontrolbtn").click(function (e) {
            e.preventDefault();
            var inpDomain = $('.domaincontrol').val();
            $.ajax({
                url: domain + "scripts/ajax.php",
                method: "post",
                dataType: "json",
                data: {
                    action: "whois",
                    domain: inpDomain
                },
                success: function (response) {
                    $(".domaincontrol").parent('.input-group').find('span').remove();
                    var cls = 'text-success d-block w-100';
                    if (response.status == 0 || response.status == -1 || response == '') {
                        cls = 'text-danger d-block w-100';
                        $('.domaincontrol').parent('.input-group').append($('<span/>', {
                            class: cls,
                            text: "Geçersiz alan adı.",
                        }));
                        $(".orderBtn").attr('disabled',true);
                    }
                    if (response.status == 1 && response.available == false) {
                        console.log("test");
                        cls = 'text-danger d-block w-100';
                        $('.domaincontrol').parent('.input-group').append($('<span/>', {
                            class: cls,
                            text: response.domain + " alan adı kullanımdadır.",
                        }));
                        $(".orderBtn").attr('disabled',true);
                    }
                    if (response.status == 1 && response.available == true) {
                        $('.domaincontrol').parent('.input-group').append($('<span/>', {
                            class: cls,
                            text: response.domain + " alan adı müsait",
                        }));
                        $(".orderBtn").removeAttr('disabled');
                    }
                }
            });
        });
        $("#add_domain").on('keyup keypress',function(e){
            if(e.key == 'Enter') {
                e.preventDefault();
                return false;
            }
        });
        $("#add_domain").submit(function(e){
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(form[0]);
            formData.append('action','add-item');
            $.ajax({
                url: domain + "scripts/ajax.php",
                method: "post",
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    $(form).trigger('reset');
                    alertify.success("Ürün sepetinize eklendi.");
                    cartUpdate();
                },
                error: function(response) {
                    alertify.error(response.responseJSON.message);
                }
            });
        });
    })
</script>