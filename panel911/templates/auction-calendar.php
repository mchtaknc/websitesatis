<?php isLoggedRedirect('admin'); ?>
<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold my-1 mr-5">Auction Calendar</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="#" class="text-muted">Auction Calendar</a>
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
                    <h3 class="card-label">Auction Calendar</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="takvim-container">
                    <?php
                    $thisMonth = date('n');
                    $arr = array();
                    for ($i = 1; $i <= 12; $i++) {
                        $month = $thisMonth;
                        $year = date('Y');
                        if ($thisMonth > 12) {
                            $month = $thisMonth - 12;
                            $year++;
                        }
                        $arr[] = $year . '-' . $month;
                        $thisMonth++;
                    }
                    foreach ($arr as $item) {
                        [$yil, $i] = explode('-', $item);
                        if ($i == 1) {
                            $ay = 'January';
                        } elseif ($i == 2) {
                            $ay = 'February';
                        } elseif ($i == 3) {
                            $ay = 'March';
                        } elseif ($i == 4) {
                            $ay = 'April';
                        } elseif ($i == 5) {
                            $ay = 'May';
                        } elseif ($i == 6) {
                            $ay = 'June';
                        } elseif ($i == 7) {
                            $ay = 'July';
                        } elseif ($i == 8) {
                            $ay = 'August';
                        } elseif ($i == 9) {
                            $ay = 'September';
                        } elseif ($i == 10) {
                            $ay = 'October';
                        } elseif ($i == 11) {
                            $ay = 'November';
                        } elseif ($i == 12) {
                            $ay = 'December';
                        }
                        $mktime = mktime(0, 0, 0, $i, 1, $yil);
                        echo '<div class="takvim-ay round"><h2>' . $ay . ' - ' . $yil . '</h2>';
                        $gunler = '<div class="takvim-gun">Mon</div><div class="takvim-gun">Tue</div><div class="takvim-gun">Wed</div><div class="takvim-gun">Thu</div><div class="takvim-gun">Fri</div><div class="takvim-gun">Sat</div><div class="takvim-gun">Sun</div>';
                        if (date("N", $mktime) > 1) {
                            for ($j = 1; $j < date("N", $mktime); $j++) {
                                $gunler .= '<div class="takvim-gun">&nbsp;</div>';
                            }
                        }
                        for ($j = 1; $j <= date("t", $mktime); $j++) {
                            $tarih = date("Y-m-d", mktime(0, 0, 0, $i, $j, $yil));
                            $takvim_gun_class = "";
                            if (!empty($appointment)) {
                                $takvim_gun_class = " takvim-orange";
                            }
                            if (!empty($blocks)) {
                                $takvim_gun_class = " takvim-red";
                            }
                            if ($tarih === date('Y-m-d')) {
                                $takvim_gun_class = " takvim-green";
                            }
                            $dates = mysqli_fetch_assoc(mysqli_query($db, "select COUNT(ads_end_date) as count from oa_ads where ads_end_date = '" . date('Ymd', strtotime($tarih)) . "'"));
                            $gunler .= '<div title="Auctions: ' . $dates['count'] . '" class="takvim-gun round' . $takvim_gun_class . '" data-date="' . $tarih . '">' . $j . '</div>';
                        }
                        echo $gunler . '</div>';
                    }
                    ?>
                </div>
                <div class="mt-3">
                    <h3 class="py-5 datedetail"></h3>
                    <table class="table table-bordered table-hover table-checkable" id="kt_datatable"
                           style="margin-top: 13px !important">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Start Price</th>
                            <th>Reserve Price</th>
                            <th>Bid Step</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var dateDetail = function(date) {
        $.ajax({
            url: domain + "scripts/ads.php",
            method: "post",
            data: {
                action: "date-auctions",
                date: date
            },
            success: function(response) {
                $(".datedetail").text(response);
            }
        });
    }
    var table = function () {
        var tbl = $("#kt_datatable");
        var initTable = function (date) {
            tbl.DataTable().clear().destroy();
            tbl.DataTable({
                order: [],
                pageLength: 25,
                responsive: true,
                processing: true,
                info: true,
                ajax: {
                    url: "<?=$domain_admin;?>scripts/ads.php",
                    type: "POST",
                    data: {
                        action: "auction-dates",
                        date: date
                    }
                },
                deferRender: true,
                columnDefs: [
                    {
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        width: '100px',
                    },
                ],
                createdRow: function (row, data, dataIndex) {
                    console.log(data);
                    $(row).attr('id', data[9]);
                },
            });
        };
        var reloadTable = function () {
            tbl.DataTable().ajax.reload();
        }
        return {
            init: function (date) {
                initTable(date);
            },
            reload: function () {
                reloadTable();
            }
        }
    }();
    $(".takvim-gun.round").click(function (e) {
        e.preventDefault();
        let formData = new FormData();
        let date = $(this).data('date');
        let el = $(this);
        table.init(date);
        dateDetail(date);
    });
    <?php
    /*$(document).on("click", ".removeData", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= $domain_admin; ?>scripts/themes.php",
                    dataType: "json",
                    method: "post",
                    data: {
                    date: id,
                        action: "delete-date",
                    },
                    success: function (response) {
                    table.reload();
                    toastr.success(response);
                },
                    error: function (response) {
                    toastr.error(response.responseJSON.message, "Error");
                }
                });
            }
        });
    });*/
    ?>
    $(function () {
        $(".takvim-gun").tooltip();
        table.init();
        dateDetail();
        $("#kt_datatable tbody").sortable({
            handle: '.handle',
            update: function() {
                var data = $(this).sortable('toArray');
                orderUpdate('dates',data);
                table.reload();
            }
        });
        $("#kt_datatable tbody").disableSelection();
    })
</script>