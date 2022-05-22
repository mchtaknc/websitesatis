<?php
require_once("_com/functions.php");
$seo_url = $domain.substr($request_uri,1);
$seo_title = "Sitedeposu | Hazır Web Site | Web Tasarım";
$seo_description = "Hazır Site, Hazır Web Sitesi, İnternet Sitesi, ve Web Tasarım Hizmeti Veren Sitedeposu Tüm İhtiyaçlarınız İçin Her Zaman Yanınızda.";
$activeMenu[$request[0]] = 'active';

$theme = $db->prepare("select * from themes where slug = ? and status = 1");
$theme->execute([$request[0]]);

if($request[0] == 'cikis') {
    logOut();
}

if($request[0] == 'hazir-web-sitesi' && !isset($request[1])) {
    $request[0] = "themes";
    $seo_title = "Hazır Web Sitesi | Sitedeposu";
    $title = "Hazır Web Sitesi";
    $breadcrumb = [
        [
            'url' => 'hazir-web-sitesi',
            'name' => 'Hazır Web Sitesi'
        ]
    ];
}

if($request[0] == 'hakkimizda') {
    $title = "Hakkımızda";
    $breadcrumb = [
        [
            'url' => 'hakkimizda',
            'name' => 'Hakkımızda',
        ]
    ];
}

if($theme->rowCount() > 0) {
    $theme = $theme->fetch();
    $seo_title = $theme->name . ' | Hazır Web Sitesi | Sitedeposu';
    $title = $theme->name;
    $images = $db->query("select * from theme_images where theme_id = '{$theme->theme_id}'")->fetchAll();
    $firstImg = array_search(1,array_column($images,'featured'));
    $db->query("update themes set view = view +1 where theme_id = '{$theme->theme_id}'");
    $breadcrumb = [
        [
            'url' => 'hazir-web-sitesi',
            'name' => 'Hazır Web Sitesi',
        ],
        [
            'url' => $theme->slug,
            'name' => $theme->name
        ]
    ];
    $request[0] = 'theme';
}

if($request[0] == 'iletisim') {
    $title = 'İletişim';
    $breadcrumb = [
        [
            'url' => 'iletisim',
            'name' => 'İletişim',
        ]
    ];
}

if($request[0] == 'cerez-politikasi') {
    $seo_title = "Çerez Politikası";
    $title = $seo_title;
    $breadcrumb = [
        [
            'url' => 'cerez-politikasi',
            'name' => 'Çerez Politikası',
        ]
    ];
}
if($request[0] == 'blog' && $request[1] != '') {
    $kategoriler = $db->query("select * from blog_categories where status = 'published'")->fetchAll();
    $query = $db->prepare("select * from blog where status = 'published' and seo_url = ?");
    $query->execute([$request[1]]);
    if($query->rowCount() == 0) {
        redirect($domain."blog");
    }
    $query = $query->fetch();
    $seo_title = $query->title;
    $title = $seo_title;
    $seo_description = $query->seo_description;
    $request[0] = 'blog-single';
}
if($request[0] == 'blog' || $request[0] == 'kategori' && $request[1] != '') {
    $seo_title = "Sitedeposu Blog";
    $title = $seo_title;
    $kategoriler = $db->query("select * from blog_categories where status = 'published'")->fetchAll();
    $sql = "select * from blog where status = 'published'";
    $limit = 9;
    $sayfa = isset($get['sayfa']) ? $get['sayfa'] : 1;
    $baslangic = ($sayfa - 1) * $limit;
    $query = $db->query($sql);
    $toplam_icerik = $query->rowCount();
    $sql .= " limit $baslangic,$limit";
    $query = $db->query($sql)->fetchAll();
    if($request[0] == 'kategori' && $request[1] != '') {
        $kategori = $db->prepare("select * from blog_categories where category_slug = ?");
        $kategori->execute([$request[1]]);
        if($kategori->rowCount() == 0) {
            redirect($domain.'blog');
        }
        $kategori = $kategori->fetch();
        $sql = "select * from blog where status = 'published' and category_id = '{$kategori->category_id}'";
        $query = $db->query($sql);
        $toplam_icerik = $query->rowCount();
        $sql .= " limit $baslangic,$limit";
        $query = $db->query($sql)->fetchAll();
        $seo_title = $kategori->category_name;
        $title = $seo_title;
    }
    $sonSayfa = ceil($toplam_icerik / $limit);
    $request[0] = 'blog';
}

if ($request[0] == '') {
    $request[0] = 'default';
}
if (!file_exists($path."templates/" . $request[0] . ".php")) {
    redirect($domain);
} else {
    require_once($path."templates/header.php");
    require_once($path."templates/" . $request[0] . ".php");
    require_once($path."templates/footer.php");
}