<?php isLoggedRedirect(); ?>
<?php
    if($request[0] == 'musteri-paneli' && $request[1] == 'siparis' && $request[2] != '') {
        $order = $db->prepare("select * from orders where order_no = ? and customer_id = ?");
        $order->execute([$request[2], $_SESSION['front']['customer_id']]);
        if ($order->rowCount() == 0) {
            redirect($domain);
        }
        $status = "";
        $order = $order->fetch();
        if ($order->status == 'failure') {
            $status = '<span class="badge badge-danger">Ödeme başarısız.</span>';
        }
        if ($order->status == 'return') {
            $status = '<span class="badge badge-danger">İade.</span>';
        }
        if ($order->status == 'success') {
            $status = '<span class="badge badge-success">Ödeme başarılı.</span>';
        }
        if ($order->status == 'waiting') {
            $status = '<span class="badge badge-warning">Ödeme bekliyor.</span>';
        }
        $products = $db->query("select * from order_products where order_id = '{$order->order_id}'")->fetchAll();
    }
?>
<div class="tickets nuhost-filter-list-container">
    <div class="tickets-head d-flex align-items-center"><a href="<?php echo $domain ?>musteri-paneli/siparislerim" class="btn btn-info btn-sm"><i class="fas fa-angle-left"></i> Geri Dön</a> <h5 class="mb-0"><?php echo $order->order_no ?> No'lu Sipariş Detayı</h5></div>
    <table class="orderDetail datatable table table-hover table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>Tema</th>
            <th>Alan Adı</th>
            <th>Fiyat</th>
            <th>Oluşturulma Tarihi</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($products as $item) {
            $theme = $db->query("select * from themes where theme_id = '{$item->item_id}'")->fetch();
            echo '<tr>
                <td>'.$theme->name.'</td>
                <td>'.$item->domain.'</td>
                <td>'.$item->price.'</td>
                <td>'.date('d-m-Y H:i:s',strtotime($order->order_date)).'</td>
            </tr>';
        }
        ?>
        </tbody>
    </table>
    <h6 class="mt-3 mb-0"><b>Sipariş Oluşturulma Tarihi:</b> <?php echo date('d-m-Y H:i:s',strtotime($order->order_date)) ?></h6>
    <h6 class="mt-3 mb-0"><b>Durum:</b> <?php echo $status ?></h6>
    <h6 class="mt-3 mb-0"><b>Toplam Tutar:</b> <?php echo $order->total ?> TL</h6>
    <?php if($order->status == 'waiting') { ?>
    <form method="post" action="<?php echo $domain?>odeme">
        <h5 class="mt-3 mb-0 font-weight-bold">Ödeme Yöntemi</h5>
        <label class="form-check form-check-inline form-check-solid my-3">
            <input class="form-check-input" name="payment_method" type="radio" value="credit_card" checked>
            <span class="font-weight-bold">Kredi Kartı (Online Ödeme)</span>
        </label>
        <input type="hidden" name="orderType" value="1">
        <input type="hidden" name="order" value="<?php echo $order->order_no ?>">
        <button type="submit" class="btn btn-success btn-sm ">ÖDEME YAP</button>
    </form>
    <?php } ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded',function () {
        var table = $('.orderDetail').DataTable({
            language:{
                url: '<?php echo assets_url('theme/js/Turkish.json') ?>',
            },
            order: [],
            lengthChange: false,
            responsive: true,
            processing: true,
            searching: false,
            info: false,
            paging: false,
            pageLength: 25,
            deferRender: true,
            columnDefs: [
                {
                    targets: '_all',
                    orderable: false
                }
            ],
        });
    })
</script>