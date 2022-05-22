<div class="mb-3 orders nuhost-filter-list-container">
    <h5>Son Siparişlerim</h5>
    <table class="ordersTable datatable table table-hover table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Sipariş No</th>
                <th>Fiyat</th>
                <th>Durum</th>
                <th>Ödeme Türü</th>
                <th>Sipariş Tarihi</th>
                <th>İşlemler</th>
            </tr>
        </thead>
    </table>
</div>
<div class="mb-3 tickets nuhost-filter-list-container">
    <div class="tickets-head">
        <h5>Son Destek Taleplerim</h5>
        <a href="<?php echo $domain ?>musteri-paneli/talep-olustur" class="btn btn-sm btn-primary mb-2"><i class="fa fa-plus"></i> Yeni Talep Oluştur</a>
    </div>
    <table class="ticketsTable table table-hover table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Talep ID</th>
                <th>Konu</th>
                <th>Durum</th>
                <th>Oluşturulma Tarihi</th>
                <th>İşlemler</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    document.addEventListener('DOMContentLoaded',function () {
        var tickets = $('.ticketsTable').DataTable({
            language:{
                url: '<?php echo assets_url('theme/js/Turkish.json') ?>',
            },
            order: [],
            lengthChange: false,
            responsive: true,
            processing: true,
            info: true,
            pageLength: 25,
            ajax: {
                url: domain + "scripts/user.php",
                type: "POST",
                data: {
                    action: "tickets",
                    last: true
                }
            },
            deferRender: true,
            columnDefs: [
                {
                    targets: [4],
                    orderable: false
                }
            ],
        });
        var orders = $('.ordersTable').DataTable({
            language:{
                url: '<?php echo assets_url('theme/js/Turkish.json') ?>',
            },
            order: [],
            lengthChange: false,
            responsive: true,
            processing: true,
            info: true,
            pageLength: 25,
            ajax: {
                url: domain + "scripts/user.php",
                type: "POST",
                data: {
                    action: "orders",
                    last: true
                }
            },
            deferRender: true,
            columnDefs: [
                {
                    targets: [4],
                    orderable: false
                }
            ],
        });
    })
</script>