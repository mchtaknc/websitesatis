<div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold my-1 mr-5">Destek Talepleri</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= $domain_admin; ?>" class="text-muted">Anasayfa</a>
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
                    <h3 class="card-label">Destek Talepleri</h3>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
                    <thead>
                    <tr>
                        <th>Talep ID</th>
                        <th>Konu</th>
                        <th>Talep Oluşturan</th>
                        <th>Durum</th>
                        <th>Oluşturulma Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<script>
    var tablo = function () {
        var table = $("#kt_datatable");
        var initTable = function () {
            table.DataTable().clear().destroy();
            table.DataTable({
                order: [],
                pageLength: 25,
                responsive: true,
                processing: true,
                info: true,
                ajax: {
                    url: "<?=$domain_admin;?>scripts/tickets.php",
                    type: "POST",
                    data: {
                        action: "list",
                    }
                },
                deferRender: true,
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        width: '80px',
                    },
                ]
            });
        };
        var reloadTable = function() {
            table.DataTable().ajax.reload();
        }
        return {
            init: function () {
                initTable();
            },
            reload: function() {
                reloadTable();
            }
        }
    }();

    /*$(document).on("click",".removeUser",function(e){
        e.preventDefault();
        var staff = $(this).data('id');
        $.ajax({
            url: "<?=$domain_admin;?>scripts/staff.php",
            dataType: "json",
            method: "post",
            data: {
                action: "delete",
                staff: staff
            },
            success: function (response) {
                toastr.success(response);
                tablo.reload();
            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
    })*/
    $(function () {
        tablo.init();
    });
</script>
