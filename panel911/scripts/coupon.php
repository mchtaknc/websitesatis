<?php
require_once(__DIR__ . "/../../_com/functions.php");
if (!isLogged('admin')) {
    redirect($domain_admin . 'login');
}
if (isset($_POST['action'])) {
    if (isset($_POST['action']) && $_POST['action'] == 'list') {
        $array['data'] = array();
        if ($error['type'] == 0) {
            $sorgu = $db->query("select * from coupons where status != 'removed'")->fetchAll();
            foreach($sorgu as $value) {
                $data = array();
                $class = 'label-light-success';
                $status = 'Aktif';
                if ($value->status == 'passive') {
                    $class = 'label-light-danger';
                    $status = 'Pasif';
                }
                $html = '<a href="' . $domain_admin . 'coupons/edit/' . $value->id . '" class="btn btn-sm btn-clean btn-icon" data-coupon="' . $value->id . '" title="Düzenle"><span class="svg-icon svg-icon-md"><i class="la la-edit"></i></span></a>
                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon remove-coupon" data-coupon="' . $value->id . '" title="Sil"><span class="svg-icon svg-icon-md"><i class="la la-trash"></i></span</a>';
                $data[] = $value->code;
                $data[] = $value->value;
                $data[] = $value->max_usage;
                $data[] = '<span class="label label-lg font-weight-bold ' . $class . ' label-inline">' . $status . '</span>';
                $data[] = $html;
                $array['data'][] = $data;
            }
            http_response_code(200);
            echo json_encode($array);
        } else {
            http_response_code(400);
            echo json_encode($array);
        }
    }
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $required = array(
            'code' => 'Kupon Kodu',
            'discount' => 'İndirim Tutarı',
            'maxUsage' => 'Max Kullanım',
        );
        $_POST['discount'] = str_replace(',','',$_POST['discount']);
        $query = $db->query("select * from coupons where code = '{$_POST['code']}'");
        if ($query->rowCount() > 0) {
            $error['type']++;
            $error['message'] = "Bu kod daha önce eklenmiştir. Başka bir kod girerek tekrar deneyiniz.";
        }
        requireCheck($required);
        if ($error['type'] == 0) {
            $query = $db->prepare("insert into coupons (status,code,value,max_usage) values (?,?,?,?)")->execute([
                'active',
                $_POST['code'],
                $_POST['discount'],
                $_POST['maxUsage']
            ]);
            if($query) {
                http_response_code(200);
                echo json_encode(['message' => 'Kupon başarıyla eklenmiştir.']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Bir şeyler ters gitti.']);
            }
        } else {
            http_response_code(400);
            echo json_encode($error);
        }
    }
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $data_id = isset($_POST['coupon']) ? $_POST['coupon'] : 0;
        $required = array(
            'code' => 'Kupon Kodu',
            'discount' => 'İndirim Tutarı',
            'maxUsage' => 'Max Kullanım',
        );
        $_POST['discount'] = str_replace(',','',$_POST['discount']);
        requireCheck($required);
        $data = $db->prepare("select * from coupons where id = ?");
        $data->execute([$data_id]);
        if ($data->rowCount() == 0) {
            $error['type']++;
        }
        if ($error['type'] == 0) {
            $query = $db->prepare("update coupons set code = ?, value = ?, max_usage = ? where id = ?")->execute([
                $_POST['code'],
                $_POST['discount'],
                $_POST['maxUsage'],
                $data_id
            ]);
            if($query) {
                http_response_code(200);
                echo json_encode(['message' => 'Kupon başarıyla düzenlendi.']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Bir şeyler ters gitti.']);
            }
        } else {
            http_response_code(400);
            echo json_encode($error);
        }
    }
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $data_id = $_POST['coupon'];
        $sorgu = $db->prepare("select * from coupons where id = ?");
        $sorgu->execute([$data_id]);
        if ($sorgu->rowCount() == 0) {
            $error['type']++;
        }
        if ($error['type'] == 0) {
            $query = $db->query("update coupons set status = 'removed' where id = '{$data_id}'");
            if($query) {
                http_response_code(200);
                echo json_encode(['message' => 'Kupon başarıyla silindi.']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Bir şeyler ters gitti.']);
            }
        } else {
            http_response_code(404);
            echo json_encode($error);
        }
    }
}