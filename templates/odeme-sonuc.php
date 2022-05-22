<?php include_once $path . "templates/headerv3.php"; ?>
<?php
if ($request[0] == 'odeme-sonuc' && (isset($_SESSION['order']) || isset($_SESSION['repayment']))) {
    $totalPrice = !isset($_SESSION['repayment']) ? $_SESSION['cart']['taxTotal'] : 0;
    /*$order = $db->prepare("select * from orders where order_no = ? and customer_id = ?");
    $order->execute([$siparis, $_SESSION['front']['customer_id']]);
    if ($order->rowCount() == 0) {
        redirect($domain);
    }
    $order = $order->fetch();*/
    if(!isset($_SESSION['repayment'])) {
        $db->prepare("insert into orders (
            customer_id,
            firstname,
            lastname,
            phone,
            company_name,
            tax_id,
            tax_office,
            address,
            order_no,
            subtotal,
            vat,
            total,
            status,
            payment_method,
            note
        ) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute([
            $_SESSION['order']['customer_id'],
            $_SESSION['order']['firstname'],
            $_SESSION['order']['lastname'],
            $_SESSION['order']['phone'],
            $_SESSION['order']['companyname'],
            $_SESSION['order']['tax_id'],
            $_SESSION['order']['tax_office'],
            $_SESSION['order']['address'],
            $_SESSION['order']['order_no'],
            $_SESSION['cart']['totalPrice'],
            $_SESSION['cart']['taxPrice'],
            $_SESSION['cart']['taxTotal'],
            'waiting',
            $_SESSION['order']['payment_method'],
            $_SESSION['order']['notes']
        ]);
        $order_id = $db->lastInsertId();
        $order = $db->query("select * from orders where order_id = '{$order_id}'")->fetch();
        if (empty($order)) {
            redirect($domain);
        }
        foreach ($_SESSION['cart']['items'] as $item) {
            $db->prepare("insert into order_products (order_id,item_type,item_id,item_quantity,price,domain,domain_type) values (?,?,?,?,?,?,?)")->execute([
                $order_id,
                'theme',
                $item['item']['theme_id'],
                1,
                $item['price'],
                $item['domain']['domain'],
                $item['domain']['domain_type']
            ]);
        }
        //iyzico
        /*if (isset($_POST['token'])) {
            $token = $_POST['token'];
            $iyzico = new Iyzico();
            $conversationId = 123456789;
            $response = $iyzico->callbackForm($token, $conversationId);
            $paymentStatus = $response->getPaymentStatus();
            $db->prepare("update orders set status = ?,payment_details = ? where order_id = ?")->execute([
                strtolower($paymentStatus),
                $response->getRawResult(),
                $order->order_id
            ]);
            $status = $response->getStatus();
            $error = $response->getErrorCode();
            $errorMessage = $response->getErrorMessage();
        }*/
        $db->prepare("update orders set sessions = ? where order_id = ?")->execute([
            json_encode([
                $_SESSION['cart'],
                $_SESSION['coupon']
            ]),
            $order->order_id
        ]);
    } else {
        $db->query("update orders set payment_method = 'credit_card', order_no ='{$_SESSION['repayment']['orderNo']}' where order_id = '{$_SESSION['repayment']['orderID']}'");
        $order = $db->query("select * from orders where order_id = '{$_SESSION['repayment']['orderID']}'")->fetch();
    }
    unset($_SESSION['cart'], $_SESSION['order'], $_SESSION['coupon'], $_SESSION['repayment']);
    $paymentStatus = $_GET['type'];
} else {
    redirect($domain);
}
?>
<?php if ($order->payment_method == 'credit_card') { ?>
    <section class="padding-100-0">
        <div class="container">
            <div class="succec-domain-search-mesage">
                İŞLEM SONUCU
            </div>
            <div class="row mr-tp-40 justify-content-left">
                <div class="col-md-12">
                    <?php if (isset($paymentStatus) && $paymentStatus == 'success') { ?>
                        <p class="alert alert-success">Siparişiniz oluşturulmuştur ve ödemeniz başarıyla alınmıştır.</p>
                    <?php }
                    if (isset($paymentStatus) && $paymentStatus == 'failure') { ?>
                        <p class="alert alert-danger">Ödeme başarısız. İşlem yapılırken bir hata meydana geldi.</p>
                    <?php } ?>
                    <div class="text-center">
                        <a class="header-order-button-slid" href="https://sitedeposu.com/musteri-paneli">Müşteri
                            Paneli</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } else { ?>
    <section class="padding-100-0">
        <div class="container">
            <div class="succec-domain-search-mesage">
                BANKA HAVALESİ
            </div>
            <div class="row mr-tp-40 justify-content-left">
                <div class="col-md-12">
                    <p class="alert alert-success">Siparişiniz başarıyla alınmıştır. Mevcut ödemeniz gereken
                        tutar. <?php echo $totalPrice ?> TL'dir. Ödemenizi aşağıdaki banka hesap bilgilerinden
                        gerçekleştirebilirsiniz.</p>
                    <div class="text-center">
                        <a class="header-order-button-slid" href="https://sitedeposu.com/musteri-paneli">Müşteri
                            Paneli</a>
                    </div>
                </div>
            </div>
            <div class="banks">
                <div class="bank">
                    <div class="image">
                        <img src="<?php echo assets_url('theme/img/qnb.png')?>" class="img-fluid">
                    </div>
                    <div><b>Qnb Finansbank</b></div>
                    <div>
                        <div><b>Alıcı</b>: Mücahit Akıncı</div>
                        <div><b>Şube</b>: Ankara</div>
                        <div><b>Şube </b>Kodu: 03663</div>
                        <div><b>Hesap Numarası</b>: 60120097</div>
                        <div><b>IBAN</b>: TR40 0011 1000 0000 0060 1200 97</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        cartUpdate();
    })
</script>
