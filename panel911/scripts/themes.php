<?php
require_once(__DIR__ . "/../../_com/functions.php");
if (!isLogged('admin')) {
    redirect($domain_admin . 'login');
}
if (!staffCheck(session('back.staff_id'))) {
    $error['type']++;
}
if (isset($_POST['action']) && $_POST['action'] == 'list') {
    $array['data'] = array();
    if ($error['type'] == 0) {
        $themes = $db->query("select * from themes where status != 2 order by theme_id desc")->fetchAll();
        $array['data'] = array();
        foreach($themes as $item) {
            $data = array();
            $category = $db->prepare("select * from theme_categories where category_id = ?");
            $category->execute([$item->category_id]);
            $category = $category->fetch();
            $html = '<a href="' . $domain_admin . 'themes/edit/' . $item->theme_id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><span class="svg-icon svg-icon-md"><i class="la la-edit"></i></span></a>
                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon remove-gallery" data-theme="' . $item->theme_id . '" title="Delete"><span class="svg-icon svg-icon-md"><i class="la la-trash"></i></span</a>';
            $data[] = $item->name;
            $data[] = $category->name;
            $data[] = number_format($item->price,2);
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
    $_FILES['image'] = rearrangeUploadArray($_FILES['image']);
    $count = count($_FILES['image']);
    $required = array(
        'category' => 'Kategori',
        'name' => 'Tema Adı',
        'description' => 'Tema Açıklaması',
        'price' => 'Tema Fiyat',
        'demo' => 'Demo Linki',
    );
    $_POST['price'] = str_replace(',','',$_POST['price']);
    $category = $db->prepare("select * from theme_categories where category_id = ?");
    $category->execute([$_POST['category']]);
    for ($i = 0; $i < $count; $i++) {
        $size = $_FILES['image'][$i]['size'];
        $fileError = $_FILES['image'][$i]['error'];
        $tmpName = $_FILES['image'][$i]['tmp_name'];
        $fileSize = filesize($tmpName);
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        @$fileType = finfo_file($fileInfo, $tmpName);
        if ($fileError > 0) {
            $error['type']++;
            $error['message'] = $uploadErrors[$fileError];
            break;
        }
        if ($fileSize > $maxFileSize) {
            $error['type']++;
            $error['message'] = "Resim boyutu {$maxFileSizeString} MB'dan büyük olamaz.";
            break;
        }
        if (!in_array($fileType, array_keys($allowedTypes))) {
            $error['type']++;
            $error['message'] = "Resim yüklemesi desteklenmeyen bir uzantıya sahiptir. Desteklenen uzantılar: " . implode(', ', array_values($allowedTypes));
            break;
        }
        if (!is_uploaded_file($tmpName)) {
            $error['type']++;
        }
    }
    if ($_FILES['image'][0]['size'] == 0) {
        $error['type']++;
        $error['message'] = "Lütfen en az bir adet resim yükleyiniz.";
    }
    requireCheck($required);
    if($category->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $title = $_POST['name'];
        $db->prepare("insert into themes (category_id,name,description,featured_specifications,demo_link,price) values (?,?,?,?,?,?)")->execute([
           $_POST['category'],
           $_POST['name'],
           $_POST['description'],
           $_POST['featured_specifications'],
           $_POST['demo'],
           $_POST['price'],
        ]);
        $theme_id = $db->lastInsertId();
        $slug = slugCheck($title,'theme');
        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['image'][$i]['size'] > 0) {
                $detail = uploadImage($_FILES['image'][$i], ['resize' => ['x' => 800, 'y' => 600]], 'themes');
                $value = $detail['path'] . $detail['fullName'];
                $imageSize['250x300'] = uploadImage($_FILES['image'][$i],['fit' => ['x' => 250,'y' => 300]],'themes');
                $main = 0;
                if($i == 0) {
                    $main = 1;
                }
                $db->prepare("insert into theme_images (theme_id,image,image_sizes,featured) values (?,?,?,?)")->execute([$theme_id,$value,json_encode($imageSize),$main]);
            }
        }
        $db->prepare("update themes set slug = ? where theme_id = ?")->execute([$slug,$theme_id]);
        echo json_encode('Tema başarıyla eklendi.');
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $theme_id = isset($_POST['theme']) ? $_POST['theme'] : 0;
    $_FILES['image'] = rearrangeUploadArray($_FILES['image']);
    $count = count($_FILES['image']);
    $required = array(
        'category' => 'Kategori',
        'name' => 'Tema Adı',
        'description' => 'Tema Açıklaması',
        'price' => 'Tema Fiyat',
        'demo' => 'Demo Linki',
    );
    $_POST['price'] = str_replace(',','',$_POST['price']);
    $theme = $db->prepare("select * from themes where status = 1 and theme_id = ?");
    $theme->execute([$theme_id]);
    $category = $db->prepare("select * from theme_categories where category_id = ?");
    $category->execute([$_POST['category']]);
    for ($i = 0; $i < $count; $i++) {
        $size = $_FILES['image'][$i]['size'];
        if($size > 0) {
            $fileError = $_FILES['image'][$i]['error'];
            $tmpName = $_FILES['image'][$i]['tmp_name'];
            $fileSize = filesize($tmpName);
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            @$fileType = finfo_file($fileInfo, $tmpName);
            if ($fileError > 0) {
                $error['type']++;
                $error['message'] = $uploadErrors[$fileError];
                break;
            }
            if ($fileSize > $maxFileSize) {
                $error['type']++;
                $error['message'] = "Resim boyutu {$maxFileSizeString} MB'dan büyük olamaz.";
                break;
            }
            if (!in_array($fileType, array_keys($allowedTypes))) {
                $error['type']++;
                $error['message'] = "Resim yüklemesi desteklenmeyen bir uzantıya sahiptir. Desteklenen uzantılar: " . implode(', ', array_values($allowedTypes));
                break;
            }
            if (!is_uploaded_file($tmpName)) {
                $error['type']++;
            }
        }
    }
    requireCheck($required);
    if($theme->rowCount() == 0 || $category->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $db->prepare("update themes set category_id = ?, name = ?, description = ?, featured_specifications = ?, demo_link = ?, price = ? where theme_id = ?")->execute([
            $_POST['category'],
            $_POST['name'],
            $_POST['description'],
            $_POST['featured_specifications'],
            $_POST['demo'],
            $_POST['price'],
            $theme_id
        ]);
        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['image'][$i]['size'] > 0) {
                $detail = uploadImage($_FILES['image'][$i], ['resize' => ['x' => 800, 'y' => 600]], 'themes');
                $imageSize['250x300'] = uploadImage($_FILES['image'][$i],['fit' => ['x' => 250,'y' => 300]],'themes');
                $value = $detail['path'] . $detail['fullName'];
                $db->prepare("insert into theme_images (theme_id,image,image_sizes) values (?,?,?)")->execute([$theme_id,$value,json_encode($imageSize)]);
            }
        }
        echo json_encode(['message' => 'Tema başarıyla güncellendi.', 'theme' => $theme_id]);
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'theme-images') {
    $theme_id = isset($_POST['theme']) ? $_POST['theme'] : 0;
    $theme = $db->prepare("select * from themes where theme_id = ?");
    $theme->execute([$theme_id]);
    if($theme->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        http_response_code(200);
        $html = "";
        $images = $db->prepare("select * from theme_images where theme_id = ? order by image_order asc");
        $images->execute([$theme_id]);
        $images = $images->fetchAll();
        foreach ($images as $image) {
            $html .= '<div id="theme_image-'.$image->id.'" class="control-image">
                <img src="'.$domain.$image->image.'" width="150px">';
            if ($image->featured == 0) {
                $html .= '<a href="#" class="main-listing-image" data-theme="' . $theme_id . '" data-image="' . $image->id . '"><i class="la la-home"></i></a>';
            }
            $html .= '<a href="#" class="remove-listing-image" data-theme="' . $theme_id . '" data-image="' . $image->id . '"><i class="la la-trash"></i></a>
            </div>';
        }
        echo $html;
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'remove-theme-image') {
    $theme_id = isset($_POST['theme']) ? $_POST['theme'] : 0;
    $image_id = isset($_POST['image']) ? $_POST['image'] : 0;

    $theme = $db->prepare("select * from themes where theme_id = ?");
    $theme->execute([$theme_id]);

    $theme_image = $db->prepare("select * from theme_images where theme_id = ? and id = ?");
    $theme_image->execute([$theme_id,$image_id]);

    $imageCounts = $db->prepare("select * from theme_images where theme_id = ?");
    $imageCounts->execute([$theme_id]);

    if($imageCounts->rowCount() == 1) {
        $error['type']++;
        $error['message'] = "Bu resmi silebilmek için yeni bir resim yüklemeniz gerekmektedir.";
    }
    if($theme->rowCount() == 0 || $theme_image->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $theme_image = $theme_image->fetch();

        if(file_exists($path.'../'.$theme_image->image)) {
            unlink($path . '../' . $theme_image->image);
            if($theme_image->image_sizes != '') {
                $json = json_decode($theme_image->image_sizes,1);
                foreach($json as $item) {
                    if(file_exists($path.'../'.$item['path'].$item['fullName'])) {
                        unlink($path.'../'.$item['path'].$item['fullName']);
                    }
                }
            }
        }
        $db->prepare("delete from theme_images where id = ? and theme_id = ?")->execute([$image_id,$theme_id]);
        http_response_code(200);
        echo json_encode(['message' => 'Tema resmi başarıyla silindi.']);
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'main-theme-image') {
    $theme_id = isset($_POST['ads']) ? $_POST['ads'] : 0;
    $image_id = isset($_POST['image']) ? $_POST['image'] : 0;

    $theme_id = isset($_POST['theme']) ? $_POST['theme'] : 0;
    $theme = $db->prepare("select * from themes where theme_id = ?");
    $theme->execute([$theme_id]);

    $theme_image = $db->prepare("select * from theme_images where theme_id = ? and id = ?");
    $theme_image->execute([$theme_id,$image_id]);

    if($theme->rowCount() == 0 || $theme_image->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $db->prepare("update theme_images set featured = 0 where theme_id = ?")->execute([$theme_id]);
        $db->prepare("update theme_images set featured = 1 where theme_id = ? and id = ?")->execute([$theme_id,$image_id]);
        http_response_code(200);
        echo json_encode(['message' => 'Temanın varsayılan resmi güncellendi.']);
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $theme_id = $_POST['theme'];
    $sorgu = $db->prepare("select * from themes where theme_id = ?");
    $sorgu->execute([$theme_id]);

    if ($sorgu->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $db->prepare("update themes set status = 2 where theme_id = ?")->execute([$theme_id]);
        http_response_code(200);
        echo json_encode("Tema başarıyla silindi.");
    } else {
        http_response_code(404);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] == 'category-list') {
    $array['data'] = array();
    if ($error['type'] == 0) {
        $cats = $db->query("select * from theme_categories")->fetchAll();
        $array['data'] = array();
        foreach ($cats as $item) {
            $data = array();
            $html = '<a href="' . $domain_admin . 'themes/category/edit/' . $item->category_id . '" class="btn btn-sm btn-clean btn-icon" data-category="' . $item->category_id . '" title="Edit"><span class="svg-icon svg-icon-md"><i class="la la-edit"></i></span></a>
                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon remove-category" data-category="' . $item->category_id . '" title="Delete"><span class="svg-icon svg-icon-md"><i class="la la-trash"></i></span</a>';
            $data[] = $item->name;
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
if (isset($_POST['action']) && $_POST['action'] === 'category-add') {
    $required = array(
        'name' => 'Name',
    );
    $slug = slugCheck($_POST['name'],'theme_category');

    foreach ($required as $key => $field) {
        if (!isset($_POST[$key]) || $_POST[$key] == '') {
            $error['type']++;
            $error['message'] = "{$required[$key]} is required.";
            break;
        }
    }

    if ($error['type'] == 0) {
        $db->prepare("insert into theme_categories (name,slug) values (?,?)")->execute([$_POST['name'],$slug]);
        echo json_encode('Tema kategorisi başarıyla eklendi.');
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'category-edit') {
    $category_id = isset($_POST['category']) ? $_POST['category'] : 0;
    $required = array(
        'name' => 'Name',
    );
    $kategori = $db->prepare("select * from theme_categories where category_id = ?");
    $kategori->execute([$category_id]);

    if ($kategori->rowCount() == 0) {
        $error['type']++;
    }
    foreach ($required as $key => $field) {
        if (!isset($_POST[$key]) || $_POST[$key] == '') {
            $error['type']++;
            $error['message'] = "{$required[$key]} is required.";
            break;
        }
    }
    if ($error['type'] == 0) {
        $db->prepare("update theme_categories set name = ? where category_id = ? ")->execute([$_POST['name'],$category_id]);
        echo json_encode('Kategori başarıyla güncellendi.');
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'category-delete') {
    $category_id = $_POST['category'];
    $sorgu = $db->prepare("select * from theme_categories where category_id = ?");
    $sorgu->execute([$category_id]);

    $themes = $db->prepare("select * from themes where category_id = ?");
    $themes->execute([$category_id]);
    if ($themes->rowCount() > 0) {
        $error['type']++;
        $error['message'] = "Bu kategoriye bağlı içerikler bulunmaktadır. Silme işlemi yapılamadı.";
    }
    if ($sorgu->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $db->prepare("delete from theme_categories where category_id = ?")->execute([$category_id]);
        http_response_code(200);
        echo json_encode("Kategori başarıyla silindi.");
    } else {
        http_response_code(404);
        echo json_encode($error);
    }
}