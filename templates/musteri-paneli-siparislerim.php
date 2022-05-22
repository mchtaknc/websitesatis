<div class="tickets nuhost-filter-list-container">
    <div class="tickets-head"><h5>Siparişlerim</h5></div>
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
<script>
    document.addEventListener('DOMContentLoaded',function () {
        var table = $('.ordersTable').DataTable({
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
                }
            },
            deferRender: true,
            columnDefs: [
                {
                    targets: [5],
                    orderable: false
                }
            ],
        });
    })
</script>