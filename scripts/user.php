<?php
require_once(__DIR__ . "/../_com/functions.php");
if (!isLogged()) {
    $error['type']++;
}
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case "change-password":
            $required = array(
                'current_password' => 'Eski Şifre',
                'new_password' => 'Yeni Şifre',
                'new_password_confirmation' => 'Yeni Şifre Tekrar'
            );
            $oldPass = hash('sha256', $_POST['current_password']);
            $sorgu = $db->prepare("select * from customers where customer_id = ?");
            $sorgu->execute([$_SESSION['front']['customer_id']]);
            if ($_POST['new_password'] != $_POST['new_password_confirmation']) {
                $error['type']++;
                $error['message'] = "Şifreleriniz eşleşmiyor.";
            } else {
                if (strlen($_POST['new_password']) < 8 || strlen($_POST['new_password_confirmation']) < 8) {
                    $error['type']++;
                    $error['message'] = "Yeni şifreniz en az 8 karakterden oluşmalıdır.";
                }
            }
            requireCheck($required);
            if ($sorgu->rowCount() == 0) {
                $error['type']++;
            } else {
                $sonuc = $sorgu->fetch();
                if ($sonuc->password != $oldPass) {
                    $error['type']++;
                    $error['message'] = "Geçerli şifrenizi yanlış girdiniz.";
                }
            }
            if ($error['type'] == 0) {
                $newHash = hash('sha256', $_POST['new_password']);
                $query = $db->prepare("update customers set password = ? where customer_id = ?")->execute([$newHash, $_SESSION['front']['customer_id']]);
                if ($query) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Şifreniz başarıyla güncellenmiştir.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Bir şeyler ters gitti.']);
                }
            } else {
                http_response_code(400);
                echo json_encode($error);
            }
            break;
        case "update-information":
            $required = array(
                'accountType' => 'Hesap Türü',
                'firstname' => 'Ad',
                'lastname' => 'Soyad',
                'email' => 'E-Posta',
                'phonenumber' => 'Telefon Numarası',
                'address' => 'Adres',
                'companyname' => 'Firma Adı',
                'tax_office' => 'Vergi Dairesi',
                'tax_id' => 'Vergi No'
            );
            $sorgu = $db->prepare("select * from customers where customer_id = ?");
            $sorgu->execute([$_SESSION['front']['customer_id']]);
            $kontrol = $db->prepare("select * from customers where customer_id != ? and email = ?");
            $kontrol->execute([$_SESSION['front']['customer_id'], $_POST['email']]);
            if ($kontrol->rowCount() > 0) {
                $error['type']++;
                $error['message'] = "Bu e-posta adresi daha alınmış. Farklı bir e-posta adresiyle tekrar deneyiniz.";
            }
            requireCheck($required);
            if ($_POST['accountType'] == 'individual') {
                unset($required['companyname'], $required['tax_office'], $required['tax_id']);
            }
            if ($sorgu->rowCount() == 0) {
                $error['type']++;
            }
            if ($error['type'] == 0) {
                $query = $db->prepare("update customers set
                    type = ?,
                    firstname = ?,
                    lastname = ?,
                    phone = ?,
                    company_name = ?,
                    tax_id = ?,
                    tax_office = ?,
                    email = ?,
                    address = ?
                where customer_id = ?")->execute([
                    $_POST['accountType'],
                    $_POST['firstname'],
                    $_POST['lastname'],
                    $_POST['phonenumber'],
                    $_POST['companyname'],
                    $_POST['tax_id'],
                    $_POST['tax_office'],
                    $_POST['email'],
                    $_POST['address'],
                    $_SESSION['front']['customer_id']
                ]);
                if ($query) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Bilgileriniz başarıyla güncellenmiştir.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Bir şeyler ters gitti.']);
                }
            } else {
                http_response_code(400);
                echo json_encode($error);
            }
            break;
        case "orders":
            $array['data'] = array();
            if ($error['type'] == 0) {
                $last = isset($_POST['last']) && $_POST['last'] == 'true' ? 'limit 10' : null;
                $datas = $db->query("select * from orders where customer_id = '{$_SESSION['front']['customer_id']}' order by order_date desc {$last}")->fetchAll();
                foreach ($datas as $item) {
                    $data = array();
                    $status = '';
                    if ($item->status == 'failure') {
                        $status = '<span class="badge badge-danger">Ödeme başarısız.</span>';
                    }
                    if ($item->status == 'return') {
                        $status = '<span class="badge badge-danger">İade.</span>';
                    }
                    if ($item->status == 'success') {
                        $status = '<span class="badge badge-success">Ödeme başarılı.</span>';
                    }
                    if ($item->status == 'waiting') {
                        $status = '<span class="badge badge-warning">Ödeme bekliyor.</span>';
                    }
                    $data[] = $item->order_no;
                    $data[] = $item->total.' TL';
                    $data[] = $status;
                    $data[] = $item->payment_method == 'credit_card' ? 'Kredi Kartı' : 'Banka Havalesi';
                    $data[] = date('d-m-Y H:i:s', strtotime($item->order_date));
                    $data[] = '<a href="' . $domain . 'musteri-paneli/siparis/' . $item->order_no . '" class="btn btn-success btn-sm">Görüntüle</a>';
                    $array['data'][] = $data;
                }
                echo json_encode($array);
            } else {
                http_response_code(400);
                echo json_encode($array);
            }
            break;
        case "tickets":
            $array['data'] = array();
            if ($error['type'] == 0) {
                $last = isset($_POST['last']) && $_POST['last'] == 'true' ? 'limit 10' : null;
                $datas = $db->query("select * from tickets where customer = '{$_SESSION['front']['customer_id']}' order by created_at desc {$last}")->fetchAll();
                foreach ($datas as $item) {
                    $data = array();
                    $status = '';
                    if ($item->resolved == 1) {
                        $status = '<span class="badge badge-danger">Kapalı</span>';
                    } elseif ($item->resolved == 0) {
                        $status = '<span class="badge badge-success">Açık</span>';
                    }
                    $data[] = $item->uniqid;
                    $data[] = $item->title;
                    $data[] = $status;
                    $data[] = date('d-m-Y H:i:s', strtotime($item->created_at));
                    $data[] = '<a href="' . $domain . 'musteri-paneli/talep/' . $item->uniqid . '" class="btn btn-success btn-sm">Görüntüle</a>';
                    $array['data'][] = $data;
                }
                echo json_encode($array);
            } else {
                http_response_code(400);
                echo json_encode($array);
            }
            break;
        case "ticket-messages":
            $html = "";
            $ticket_id = isset($_POST['ticket']) ? $_POST['ticket'] : 0;
            $ticket = $db->prepare("select * from tickets where ticket_id = ? and customer = ?");
            $ticket->execute([$ticket_id, $_SESSION['front']['customer_id']]);
            if ($ticket->rowCount() == 0) {
                $error['type']++;
            }
            if ($error['type'] == 0) {
                $ticket = $ticket->fetch();
                $ticketReplies = $db->prepare("select * from ticket_replies where ticket_id = ? order by created_at desc");
                $ticketReplies->execute([$ticket->ticket_id]);
                $ticketReplies = $ticketReplies->fetchAll();
                foreach ($ticketReplies as $ticketReply) {
                    $username = "";
                    if ($ticketReply->user_type == 'staff') {
                        $userDetail = $db->query("select * from staff where staff_id = '{$ticketReply->user}'")->fetch();
                        $username = $userDetail->name;
                    } else {
                        $username = $user->firstname . ' ' . $user->lastname;
                    }
                    $html .= '<div class="card my-2">
                        <div class="card-header bg-transparent">
                            <span>'.$username.'</span>
                            <span class="date">'.date('d/m/Y (H:i)',strtotime($ticketReply->created_at)).'</span>
                        </div>
                        <div class="card-body">
                            '.($ticketReply->user_type == 'user' ? nl2br($ticketReply->message) : unclear(nl2br($ticketReply->message))).'
                        </div>
                    </div>';
                }
                $html .= '<div class="card my-2">
                    <div class="card-header bg-transparent">
                        <span>'.$username.'</span>
                        <span class="date">'.date('d/m/Y (H:i)',strtotime($ticket->created_at)).'</span>
                    </div>
                    <div class="card-body">
                        '.nl2br($ticket->init_msg).'
                    </div>
                </div>';
            }
            echo $html;
            break;
        case "ticket-reply-add":
            $required = array(
                'message' => 'Mesaj'
            );
            $ticket_id = isset($_POST['ticket']) ? $_POST['ticket'] : 0;
            $ticket = $db->prepare("select * from tickets where ticket_id = ? and customer = ?");
            $ticket->execute([$ticket_id, $_SESSION['front']['customer_id']]);
            if ($ticket->rowCount() == 0) {
                $error['type']++;
            }
            requireCheck($required);
            if ($error['type'] == 0) {
                $query = $db->prepare(
                    "insert into ticket_replies (user,user_type,message,ticket_id) values (?,?,?,?)"
                )->execute([$_SESSION['front']['customer_id'], 'customer', $_POST['message'], $ticket_id]);
                if ($query) {
                    $db->query("update tickets set resolved = 0 where ticket_id = '{$ticket_id}'");
                    http_response_code(200);
                    echo json_encode(['message' => 'Mesajınız başarıyla gönderilmiştir.','ticket' => $ticket_id]);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Bir şeyler yanlış gitti.']);
                }
            } else {
                http_response_code(400);
                echo json_encode($error);
            }
            break;
        case "ticket-add":
            $required = array(
                'subject' => 'Konu',
                'message' => 'Mesaj',
            );
            requireCheck($required);
            if ($error['type'] == 0) {
                $query = $db->prepare("insert into tickets (
                    uniqid,
                    customer,
                    title,
                    init_msg,
                    last_reply,
                    user_read,
                    admin_read,
                    resolved
                ) values (?,?,?,?,?,?,?,?)")->execute([uniqid(), $_SESSION['front']['customer_id'], $_POST['subject'], $_POST['message'], 0, 0, 0, 0]);
                if ($query) {
                    $ticket_id = $db->lastInsertId();
                    $ticket = $db->query("select * from tickets where ticket_id = '{$ticket_id}'")->fetch();
                    http_response_code(200);
                    echo json_encode(['message' => 'Destek talebiniz başarıyla oluşturulmuştur.', 'ticket' => $ticket->uniqid]);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Bir şeyler yanlış gitti.']);
                }
            } else {
                http_response_code(400);
                echo json_encode($error);
            }
            break;
        default:
            echo json_encode($error);
            break;
    }
}