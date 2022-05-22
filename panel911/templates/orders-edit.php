<?php
if (!empty($request[2]) && is_numeric($request[2])) {
    $sonuc = $db->prepare("select * from orders where order_id = ?");
    $sonuc->execute([$request[2]]);
    $sonuc = $sonuc->fetch();
    if (empty($sonuc)) {
        redirect($domain_admin . 'orders');
    }
    $orderProducts = $db->prepare("select * from order_products where order_id = ?");
    $orderProducts->execute([$sonuc->order_id]);
    $orderProducts = $orderProducts->fetchAll();
    $customerDetail = $db->query("select * from customers where customer_id = '{$sonuc->customer_id}'")->fetch();
    $sessions = json_decode($sonuc->sessions,1);
} else {
    redirect($domain_admin);
}
?>
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold my-1 mr-5">Sipariş Düzenle</h5>
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
                        <a href="#" class="text-muted">Sipariş Düzenle</a>
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
        <div class="row">
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-body">
                        <form>
                            <div class="form-group">
                                <label for="customer">Müşteri</label>
                                <select name="customer" id="customer" class="form-control form-control-sm">
                                    <option value="" selected>Müşteri Seçiniz..</option>
                                    <?php
                                    $cats = $db->query("select * from customers")->fetchAll();
                                    foreach($cats as $item) {
                                        echo '<option value="'.$item->customer_id.'" '.($item->customer_id == $sonuc->customer_id ? 'selected' : null).'>'.$item->firstname.' '.$item->lastname.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="orderStatus">Durum</label>
                                <select class="form-control form-control-sm" name="orderStatus">
                                    <option value="">Seçiniz...</option>
                                    <option value="success" <?php echo ($sonuc->status == 'success' ? 'selected' : null) ?>>Başarılı</option>
                                    <option value="waiting" <?php echo ($sonuc->status == 'waiting' ? 'selected' : null) ?>>Ödeme Bekliyor</option>
                                    <option value="failure" <?php echo ($sonuc->status == 'failure' ? 'selected'  : null) ?>>Başarısız</option>
                                    <option value="return" <?php echo ($sonuc->status == 'return' ? 'selected'  : null) ?>>İade</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="order_payment_method">Ödeme Yöntemi</label>
                                <select class="form-control form-control-sm" name="order_payment_method">
                                    <option value="">Seçiniz...</option>
                                    <option value="bank_transfer" <?php echo ($sonuc->payment_method == 'bank_transfer' ? 'selected' : null) ?>>Banka Havalesi</option>
                                    <option value="credit_card" <?php echo ($sonuc->payment_method == 'credit_card' ? 'selected' : null) ?>>Kredi Kartı</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="price">Ücret <small>KDV hariç fiyat giriniz.</small></label>
                                <input type="text" class="form-control form-control-sm" id="price" name="price" value="<?php echo $sonuc->subtotal ?>">
                            </div>
                            <div class="form-group">
                                <label>Sipariş Notu</label>
                                <textarea name="note" class="form-control form-control-sm" cols="30" rows="10"><?php echo nl2br($sonuc->note) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Hizmetler</label>
                                <div class="products">
                                    <?php
                                    foreach ($orderProducts as $item) {
                                        $product = $db->prepare("select * from themes where theme_id = ?");
                                        $product->execute([$item->item_id]);
                                        $product = $product->fetch();
                                        ?>
                                        <div class="product">
                                            <table class="form table table-bordered" style="width: 300px">
                                                <tr>
                                                    <td>Ürün\Hizmet</td>
                                                    <td>
                                                        <?php echo $product->name ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Alan Adı</td>
                                                    <td>
                                                        <?php echo $item->domain ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <p class="font-size-lg">Sipariş No:</p>
                                    <p class="font-size-lg"><?php echo $sonuc->order_no ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">Firma Adı:</p>
                                    <p class="font-size-lg"><?php echo ($sonuc->company_name == '' ? '-' : $sonuc->company_name) ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">Vergi Daire:</p>
                                    <p class="font-size-lg"><?php echo ($sonuc->tax_office == '' ? '-' : $sonuc->tax_office) ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">Vergi No:</p>
                                    <p class="font-size-lg"><?php echo ($sonuc->tax_id == '' ? '-' : $sonuc->tax_id) ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">Adres:</p>
                                    <p class="font-size-lg"><?php echo nl2br($sonuc->address) ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">Sipariş Tarihi:</p>
                                    <p class="font-size-lg"><?php echo date('d-m-Y H:i:s',strtotime($sonuc->order_date)) ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">Ödeme Tarihi:</p>
                                    <p class="font-size-lg"><?php echo date('d-m-Y H:i:s',strtotime($sonuc->payment_date)) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <p class="font-size-lg">Ad Soyad:</p>
                                    <p class="font-size-lg"><?php echo $customerDetail->firstname.' '.$customerDetail->lastname ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">Telefon:</p>
                                    <p class="font-size-lg"><?php echo $customerDetail->phone ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">E-Posta:</p>
                                    <p class="font-size-lg"><?php echo $customerDetail->email ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">Kupon:</p>
                                    <p class="font-size-lg"><?php echo (isset($sessions[1]['coupon']) ? $sessions[1]['coupon']['code'] : '-') ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">İndirim Öncesi Tutar:</p>
                                    <p class="font-size-lg"><?php echo $sessions[0]['oldPrice']. ' TL' ?></p>
                                </div>
                                <div class="form-group">
                                    <p class="font-size-lg">İndirim Sonrası Tutar:</p>
                                    <p class="font-size-lg"><?php echo $sessions[0]['taxTotal']. ' TL' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script>
    $("#price").inputmask('currency',{
        rightAlign: false,
        allowMinus: false,
    });
    $("form").submit(function () {
        var form = $(this);
        var formData = new FormData($(this)[0]);
        formData.append('action','edit');
        formData.append('order','<?php echo $sonuc->order_id ?>');
        $.ajax({
            url: "<?= $domain_admin; ?>scripts/orders.php",
            dataType: "json",
            method: "post",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                toastr.success(response);
            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
        return false;
    });
</script>