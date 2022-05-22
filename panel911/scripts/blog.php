<?php
require_once(__DIR__ . "/../../_com/functions.php");
if (!isLogged('admin')) {
    redirect($domain_admin . 'login');
}
if (isset($_POST['action'])) {
    if (isset($_POST['action']) && $_POST['action'] == 'list') {
        $array['data'] = array();
        if ($error['type'] == 0) {
            $sorgu = $db->query(
                "select *, blog.status as blog_status from blog 
                    inner join blog_categories on blog_categories.category_id = blog.category_id
                    where blog_categories.status = 'published' and blog.status = 'published'"
            )->fetchAll();
            foreach ($sorgu as $value) {
                $data = array();
                $class = 'label-light-success';
                $status = 'Yayınlandı';
                $html = '<a href="' . $domain_admin . 'blog/edit/' . $value->blog_id . '" class="btn btn-sm btn-clean btn-icon" data-blog="' . $value->blog_id . '" title="Edit"><span class="svg-icon svg-icon-md"><i class="la la-edit"></i></span></a>
                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon remove-blog" data-blog="' . $value->blog_id . '" title="Delete"><span class="svg-icon svg-icon-md"><i class="la la-trash"></i></span</a>';
                if ($value->blog_status == 'draft') {
                    $class = 'label-light-warning';
                    $status = 'Taslak';
                }
                if ($value->blog_status == 'removed') {
                    $class = 'label-light-danger';
                    $status = 'Silindi';
                }
                $data[] = $value->title;
                $data[] = $value->category_name;
                $data[] = '<span class="label label-lg font-weight-bold ' . $class . ' label-inline">' . $status . '</span>';
                $data[] = date('d.m.Y', strtotime($value->created_at));
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
        $count = count($_FILES['featured_image']);
        $required = array(
            'category' => 'Kategori',
            'title' => 'Başlık',
            'description' => 'İçerik',
            'seo_description' => 'Seo Açıklaması'
        );
        requireCheck($required);
        $kategori_id = isset($_POST['category']) ? $_POST['category'] : 0;
        $slug = $slugify->slugify($_POST['title']);
        $slugKontrol = $db->query("select COUNT(*) as count from blog where seo_url LIKE '{$slug}%'")->fetch();
        $kategori = $db->prepare("select * from blog_categories where category_id = ? and status = 'published'");
        $kategori->execute([$kategori_id]);
        if ($_FILES['featured_image']['size'] == 0) {
            $error['type']++;
            $error['message'] = "Lütfen öne çıkan resim yükleyiniz.";
        } else {
            $size = $_FILES['featured_image']['size'];
            $fileError = $_FILES['featured_image']['error'];
            $tmpName = $_FILES['featured_image']['tmp_name'];
            $fileSize = filesize($tmpName);
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            @$fileType = finfo_file($fileInfo, $tmpName);
            if ($fileError > 0) {
                $error['type']++;
                $error['message'] = $uploadErrors[$fileError];
            }
            if ($fileSize > $maxFileSize) {
                $error['type']++;
                $error['message'] = "Resim boyutu {$maxFileSizeString} MB'dan büyük olamaz.";
            }
            if (!in_array($fileType, array_keys($allowedTypes))) {
                $error['type']++;
                $error['message'] = "Resim yüklemesi desteklenmeyen bir uzantıya sahiptir. Desteklenen uzantılar: " . implode(', ', array_values($allowedTypes));
            }
            if (!is_uploaded_file($tmpName)) {
                $error['type']++;
            }
        }
        if ($kategori->rowCount() == 0) {
            $error['type']++;
        }
        if ($slugKontrol->count > 0) {
            $slug = $slug . '-' . $slugKontrol->count;
        }
        if ($error['type'] == 0) {
            $query = $db->prepare("insert into blog (status,category_id,title,description,seo_url,seo_description) values (?,?,?,?,?,?)")->execute([
                'published',
                $_POST['category'],
                $_POST['title'],
                $_POST['description'],
                $slug,
                $_POST['seo_description']
            ]);
            if($query) {
                $blog_id = $db->lastInsertId();
                $detail = uploadImage($_FILES['featured_image'], ['resize' => ['x' => 1280, 'y' => 720]], 'blog');
                $featured = $detail['path'] . $detail['fullName'];
                $detail = uploadImage($_FILES['featured_image'], ['fit' => ['x' => 750, 'y' => 350]], 'blog');
                $featuredThumb = $detail['path'].$detail['fullName'];
                $db->prepare("update blog set featured_image = ?, featured_image_thumb = ? where blog_id = ?")->execute([
                    $featured,
                    $featuredThumb,
                    $blog_id
                ]);
                http_response_code(200);
                echo json_encode(['message' => 'Blog başarıyla eklenmiştir.']);
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
        $blog_id = isset($_POST['blog']) ? $_POST['blog'] : 0;
        $required = array(
            'category' => 'Kategori',
            'title' => 'Başlık',
            'description' => 'İçerik',
            'seo_description' => 'Seo Açıklaması'
        );
        requireCheck($required);
        $kategori_id = isset($_POST['category']) ? $_POST['category'] : 0;
        $kategori = $db->prepare("select * from blog_categories where category_id = ? and status = 'published'");
        $kategori->execute([$kategori_id]);
        $blog = $db->prepare("select * from blog where blog_id = ?");
        $blog->execute([$blog_id]);
        if ($_FILES['featured_image']['size'] > 0) {
            $size = $_FILES['featured_image']['size'];
            $fileError = $_FILES['featured_image']['error'];
            $tmpName = $_FILES['featured_image']['tmp_name'];
            $fileSize = filesize($tmpName);
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            @$fileType = finfo_file($fileInfo, $tmpName);
            if ($fileError > 0) {
                $error['type']++;
                $error['message'] = $uploadErrors[$fileError];
            }
            if ($fileSize > $maxFileSize) {
                $error['type']++;
                $error['message'] = "Resim boyutu {$maxFileSizeString} MB'dan büyük olamaz.";
            }
            if (!in_array($fileType, array_keys($allowedTypes))) {
                $error['type']++;
                $error['message'] = "Resim yüklemesi desteklenmeyen bir uzantıya sahiptir. Desteklenen uzantılar: " . implode(', ', array_values($allowedTypes));
            }
            if (!is_uploaded_file($tmpName)) {
                $error['type']++;
            }
        }
        if ($kategori->rowCount() == 0 || $blog->rowCount() == 0) {
            $error['type']++;
        }
        if ($error['type'] == 0) {
            $query = $db->prepare("update blog set category_id = ?, title = ?, description = ?, seo_description = ? where blog_id = ?")->execute([
                $_POST['category'],
                $_POST['title'],
                $_POST['description'],
                $_POST['seo_description'],
                $blog_id
            ]);
            if ($_FILES['featured_image']['size'] > 0) {
                $blog = $blog->fetch();
                unlink($path . $blog->featured_image);
                unlink($path . $blog->featured_image_thumb);

                $detail = uploadImage($_FILES['featured_image'], ['resize' => ['x' => 1280, 'y' => 720]], 'blog');
                $featured = $detail['path'] . $detail['fullName'];
                $detail = uploadImage($_FILES['featured_image'], ['fit' => ['x' => 750, 'y' => 350]], 'blog');
                $featuredThumb = $detail['path'].$detail['fullName'];
                $db->prepare("update blog set featured_image = ?, featured_image_thumb = ? where blog_id = ?")->execute([
                    $featured,
                    $featuredThumb,
                    $blog_id
                ]);
            }
            echo json_encode(['message' => 'Blog başarıyla güncellendi.']);
        } else {
            http_response_code(400);
            echo json_encode($error);
        }
    }
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $blog_id = $_POST['blog'];
        $sorgu = mysqli_query($db, "select * from mini_blog where blog_id = '{$blog_id}'");
        $sorgu = $db->prepare("select * from blog where blog_id = ?");
        $sorgu->execute([$blog_id]);
        if ($sorgu->rowCount() == 0) {
            $error['type']++;
        }
        if ($error['type'] == 0) {
            $db->query("update blog status = 'removed' where blog_id = '{$blog_id}'");
            http_response_code(200);
            echo json_encode("Blog başarıyla silindi.");
        } else {
            http_response_code(404);
            echo json_encode($error);
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == 'category-list') {
        $array['data'] = array();
        if ($error['type'] == 0) {
            $sorgu = $db->query("select * from blog_categories where status = 'published'")->fetchAll();
            foreach($sorgu as $value) {
                $data = array();
                $class = 'label-light-success';
                $status = 'Yayınlandı';
                if ($value->status == 'removed') {
                    $class = 'label-light-danger';
                    $status = 'Silindi';
                }
                $html = '<a href="' . $domain_admin . 'blog-categories/edit/' . $value->category_id . '" class="btn btn-sm btn-clean btn-icon" data-category="' . $value->category_id . '" title="Düzenle"><span class="svg-icon svg-icon-md"><i class="la la-edit"></i></span></a>
                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon remove-category" data-category="' . $value->category_id . '" title="Sil"><span class="svg-icon svg-icon-md"><i class="la la-trash"></i></span</a>';
                $data[] = $value->category_name;
                $data[] = '<span class="label label-lg font-weight-bold ' . $class . ' label-inline">' . $status . '</span>';
                $data[] = date('d.m.Y', strtotime($value->created_at));
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
            'title' => 'Kategori Adı',
            'seo_description' => 'Seo Açıklaması'
        );
        $slug = $slugify->slugify($_POST['title']);
        $slugKontrol = $db->query("select COUNT(*) as count from blog_categories where category_slug LIKE '{$slug}%'")->fetch();
        if ($slugKontrol->count > 0) {
            $slug = $slug . '-' . $slugKontrol->count;
        }
        requireCheck($required);
        if ($error['type'] == 0) {
            $query = $db->prepare("insert into blog_categories (category_name,category_slug,category_seo_description) values (?,?,?)")->execute([
                $_POST['title'],
                $slug,
                $_POST['seo_description']
            ]);
            if($query) {
                http_response_code(200);
                echo json_encode(['message' => 'Kategori başarıyla eklenmiştir.']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Bir şeyler ters gitti.']);
            }
        } else {
            http_response_code(400);
            echo json_encode($error);
        }
    }
    if (isset($_POST['action']) && $_POST['action'] === 'category-edit') {
        $category_id = isset($_POST['category']) ? $_POST['category'] : 0;
        $required = array(
            'title' => 'Kategori Adı',
            'seo_description' => 'Seo Açıklaması'
        );
        requireCheck($required);
        $kategori = $db->prepare("select * from blog_categories where category_id = ?");
        $kategori->execute([$category_id]);
        if ($kategori->rowCount() == 0) {
            $error['type']++;
        }
        if ($error['type'] == 0) {
            $query = $db->prepare("update blog_categories set category_name = ?, category_seo_description = ? where category_id = ?")->execute([
                $_POST['title'],
                $_POST['seo_description'],
                $category_id
            ]);
            if($query) {
                http_response_code(200);
                echo json_encode(['message' => 'Kategori başarıyla düzenlendi.']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Bir şeyler ters gitti.']);
            }
        } else {
            http_response_code(400);
            echo json_encode($error);
        }
    }
    if (isset($_POST['action']) && $_POST['action'] === 'category-delete') {
        $category_id = $_POST['category'];
        $sorgu = $db->prepare("select * from blog_categories where category_id = ?");
        $sorgu->execute([$category_id]);

        $blog = $db->prepare("select * from blog where category_id = ?");
        $blog->execute([$category_id]);
        if ($blog->rowCount() > 0) {
            $error['type']++;
            $error['message'] = "Bu kategoride içerikleriniz bulunmaktadır. Silme işlemi başarısız.";
        }
        if ($sorgu->rowCount() == 0) {
            $error['type']++;
        }
        if ($error['type'] == 0) {
            $query = $db->query("update blog_categories set status = 'removed' where category_id = '{$category_id}'");
            if($query) {
                http_response_code(200);
                echo json_encode(['message' => 'Kategori başarıyla silindi.']);
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