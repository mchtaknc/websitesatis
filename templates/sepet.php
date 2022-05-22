<?php include_once $path . "templates/headerv3.php";?>
<section class="padding-100-0">
    <div class="container">
        <div class="succec-domain-search-mesage mb-5">
            SEPET
        </div>
        <?php if($_SESSION['cart']) { ?>
        <div class="row mr-tp-40 justify-content-left">
            <div class="col-md-8">
                <div class="dedicated-container table-responsive">
                    <table class="table">
                        <thead>
                        <tr class="dedicated-head">
                            <th scope="col"><span>Ürün Adı</span></th>
                            <th scope="col"><span>Fiyat</span></th>
                            <th scope="col"><span>İşlem</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        foreach ($_SESSION['cart']['items'] as $item) { $i++; ?>
                        <tr>
                            <td>
                                <span class="plan-num"><?php echo $i; ?></span> <?php echo $item['item']['name']; ?><br>
                                <span class="font-weight-light"><b class="font-weight-bold">Alan Adı:</b> <?php echo $item['domain']['domain'] ?></span><br>
                            </td>
                            <td><?php echo $item['item']['price'] ?> TL</td>
                            <td>
                                <a href="javascript:;" class="btn btn-sm btn-light removeCartItem" data-item="<?php echo $item['item']['theme_id'] ?>"><i class="fa fa-times"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="list-group mb-3 search-box-filter-domains">
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div class="padding-domain-filter">
                            <h5 class="domain-filter-tab-title">SİPARİŞ DETAYI</h5>
                            <div class="form-price-sujjestion-domain totalPrice">
                                <span><b>Ara Toplam:</b> <?php echo number_format($_SESSION['cart']['totalPrice'],2) ?> TL</span>
                                <span><b>%18 KDV:</b> <?php echo number_format($_SESSION['cart']['taxPrice'],2) ?> TL</span>
                                <span><b>İndirim:</b> <?php echo number_format($_SESSION['cart']['discount'],2) ?> TL</span>
                                <span><b>Toplam:</b> <?php echo number_format($_SESSION['cart']['taxTotal'],2) ?> TL</span>
                            </div>
                        </div>
                    </li>
                    <?php if(isset($_SESSION['coupon'])) { ?>
                    <li class="list-group-item justify-content-between bg-light coupon-discount d-flex">
                        <div class="text-success">
                            <h5 class="domain-filter-tab-title">Kupon Kodu <span>indiriminiz</span></h5>
                            <small class="code"><?php echo $_SESSION['coupon']['code'] ?></small>
                        </div>
                        <span class="text-success discount">- <?php echo number_format($_SESSION['coupon']['discount'],2) ?> TL</span>
                    </li>
                    <?php } ?>
                </ul>
                <form method="post" class="card search-box-filter-domains-promo">
                    <div class="input-group">
                        <input type="text" class="form-control" name="code" placeholder="Kupon Kodu">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-secondary">Kodu Kullan</button>
                        </div>
                    </div>
                </form>
                <form method="post" class="text-right" action="<?php echo $domain?>odeme">
                    <button type="submit" class="btn btn-success mt-3">Alışverişi Tamamla</button>
                </form>
            </div>
        </div>
        <?php } else { ?>
        <div class="dedicated-container table-responsive">
            <table class="table ">
                <thead>
                <tr class="dedicated-head">
                    <th scope="col"><span>Ürün Adı</span></th>
                    <th scope="col"><span>Fiyat</span></th>
                    <th scope="col"><span>İşlem</span></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td rowspan="5">Sepetinizde hiç ürün bulunmamaktadır.</td>
                </tr>
                </tbody>
            </table>

        </div>
        <?php } ?>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded',function(){
        $(".removeCartItem").click(function(e){
            e.preventDefault();
            let el = $(this);
            let product = $(this).data('item');
            $.ajax({
                url: domain + "scripts/ajax.php",
                method: "post",
                dataType: "json",
                data: {
                    action: "remove-item",
                    product: product
                },
                success: function(response) {
                    alertify.success(response.message);
                    cartUpdate();
                    getTotalPrice(".totalPrice");
                    $(el).parents('tr').remove();
                    if(response.reload == 1) {
                        window.location.reload();
                    }
                },
                error: function(response) {
                    alertify.error(response.responseJSON.message);
                }
            });
        });
        $(".search-box-filter-domains-promo").submit(function(e){
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(form[0]);
            formData.append("action","assign-coupon");
            $.ajax({
                url: domain + "scripts/ajax.php",
                method:"post",
                dataType: "json",
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    $(".search-box-filter-domains").append(response.html);
                    getTotalPrice(".totalPrice");
                    cartUpdate();
                },
                error: function(response) {
                    if(!response.responseJSON.coupon) {
                        $(".search-box-filter-domains li").not(':first').remove();
                        getTotalPrice(".totalPrice");
                        cartUpdate();
                    }
                    alertify.error(response.responseJSON.message);
                }
            }).complete(function(){
                $(".search-box-filter-domains-promo input").val("");
            });
        });
    })
</script>
