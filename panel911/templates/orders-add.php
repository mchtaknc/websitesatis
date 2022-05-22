<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold my-1 mr-5">Sipariş Ekle</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin.'orders'; ?>" class="text-muted"> Siparişler</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="#" class="text-muted">Sipariş Ekle</a>
                    </li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page Heading-->
        </div>
        <!--end::Info-->
    </div>
</div>

<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Sipariş Ekle</h3>
                </div>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="customer">Müşteri</label>
                        <select name="customer" id="customer" class="form-control form-control-sm">
                            <option value="" selected>Müşteri Seçiniz..</option>
                            <?php
                            $cats = $db->query("select * from customers")->fetchAll();
                            foreach($cats as $item) {
                                echo '<option value="'.$item->customer_id.'">'.$item->firstname.' '.$item->lastname.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="orderStatus">Durum</label>
                        <select class="form-control form-control-sm" name="orderStatus">
                            <option value="">Seçiniz...</option>
                            <option value="success">Başarılı</option>
                            <option value="waiting">Ödeme Bekliyor</option>
                            <option value="failure">Başarısız</option>
                            <option value="return">İade</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="order_payment_method">Ödeme Yöntemi</label>
                        <select class="form-control form-control-sm" name="order_payment_method">
                            <option value="">Seçiniz...</option>
                            <option value="bank_transfer">Banka Havalesi</option>
                            <option value="credit_card">Kredi Kartı</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Ücret <small>KDV hariç fiyat giriniz.</small></label>
                        <input type="text" class="form-control form-control-sm" id="price" name="price">
                    </div>
                    <div class="form-group">
                        <div class="products">
                            <div class="product savedBlock">
                                <table class="form table-bordered" style="width:500px">
                                    <tr>
                                        <td>Ürün\Hizmet</td>
                                        <td>
                                            <select class="form-control form-control-sm productprice" name="order_packages[]">
                                                <option value="">Seçiniz...</option>
                                                <?php
                                                $themes = $db->query("select * from themes where status = 1")->fetchAll();
                                                foreach ($themes as $theme) {
                                                    echo '<option value="'.$theme->theme_id.'">'.$theme->name.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Alan Adı</td>
                                        <td>
                                            <input type="url" class="form-control form-control-sm" name="order_domain[]" required>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-flat btn-light addproduct"><i class="fa fa-plus-circle"></i> Başka Hizmet Ekle</button>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script>
    function getPrice(item,itemType,qty,amount = 0) {
        $.ajax({
            url: "<?=$domain_admin?>scripts/orders.php",
            dataType: "json",
            method: "post",
            data: {
                action: "getPrice",
                item: item,
                itemType: itemType,
                qty: qty,
                amount: amount
            },
            success: function(response) {
                $("#price").val(response.price);
            }
        });
    }
    $("#price").inputmask('currency',{
        rightAlign: false,
        allowMinus: false,
    });
    $(document).on('change',".productprice",function(){
        if(this.value != '') {
            var item = this.value;
            getPrice(item,'theme',1,$("#price").val())
        }
    });
    $(".addproduct").click(function(){
        var prodtemplate = $(".products .product:first").clone().removeClass('savedBlock');
        prodtemplate.appendTo('.products');
    });
    $("form").submit(function () {
        var form = $(this);
        var formData = new FormData($(this)[0]);
        formData.append('action','add');
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/orders.php",
            dataType: "json",
            method: "post",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                $(form).trigger('reset');
                $(".products").children('.product').not('.savedBlock').remove();
                toastr.success(response);
            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
        return false;
    });
</script>