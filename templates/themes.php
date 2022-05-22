<?php include_once $path . "templates/headerv2.php"; ?>
<section class="padding-60-0-100">
    <div class="container blog-container-page">
        <div class="row justify-content-left mr-tp-60">
            <aside class="col-md-3 blog-sidebar mb-5">
                <div class="question-area-answer-navs">
                    <div class="nuhost-filter-list-container min-height-auto">
                        <h5 class="font-weight-bold pb-2" style="border-bottom: 1px solid #ddd">Kategoriler</h5>
                        <!-- q&a filter list container -->
                        <ul id="nuhost-filter-list">
                            <li><a href="<?php echo $domain ?>hazir-web-sitesi" <?php echo (!isset($get['kategori']) ? 'class="active"' : null) ?>>Hepsi<i class="fas fa-angle-right"></i></a></li>
                            <?php
                            $categories = $db->query("select * from theme_categories order by name asc");
                            foreach($categories as $item) {
                                echo '<li><a '.(isset($get['kategori']) && $get['kategori'] == $item->slug ? 'class="active"' : null).' href="'.$domain.'hazir-web-sitesi?kategori='.$item->slug.'">'.$item->name.'<i class="fas fa-angle-right"></i></a></li>';
                            }
                            ?>
                        </ul> <!-- end q&a filter item list -->
                    </div> <!-- end q&a filter -->

                </div>
            </aside><!-- /.blog-sidebar -->
            <div class="col-md-9">
                <?php
                $web = $domain.'hazir-web-sitesi';
                $limit = 9;
                $sayfa = isset($get['sayfa']) ? $get['sayfa'] : 1;
                $baslangic = ($sayfa - 1) * $limit;
                $sql = "select *,name as themeName, slug as themeSlug from themes where status = 1 order by price asc";
                $themes = $db->query($sql);
                $toplam_icerik = $themes->rowCount();
                $sql .= " limit $baslangic,$limit";
                $themes = $db->query($sql)->fetchAll();
                if(isset($get['kategori'])) {
                    $sql = "select *,themes.slug as themeSlug, themes.name as themeName from themes 
                    inner join theme_categories on theme_categories.category_id = themes.category_id
                    where themes.status = 1 and theme_categories.slug = ? order by themes.theme_id desc";
                    $themes = $db->prepare($sql);
                    $themes->execute([$get['kategori']]);
                    $toplam_icerik = $themes->rowCount();
                    $sql .= " limit $baslangic, $limit";
                    $themes = $db->prepare($sql);
                    $themes->execute([$get['kategori']]);
                    $themes = $themes->fetchAll();
                }
                $sonSayfa = ceil($toplam_icerik / $limit);
                ?>
                <div class="row">
                    <?php
                    foreach($themes as $item) {
                        $image = $db->prepare("select * from theme_images where theme_id = ? and (featured = 1 or featured = 0) limit 1");
                        $image->execute([$item->theme_id]);
                        $image = $image->fetch();
                        $category = $db->query("select * from theme_categories where category_id = '{$item->category_id}'")->fetch();
                        ?>
                        <div class="col-md-4">
                            <div class="themes">
                                <a href="<?php echo $domain.$item->themeSlug ?>" class="image">
                                    <img src="<?php echo $domain.$image->image ?>">
                                </a>
                                <h6>
                                    <a href="<?php echo $domain.$item->themeSlug?>"><?php echo $item->themeName ?></a>
                                    <a href="<?php echo $domain ?>hazir-web-sitesi?kategori=<?php echo $category->slug ?>">Kategori: <?php echo $category->name ?></a>
                                </h6>
                                <div class="themesFooter">
                                    <span class="view"><i class="fa fa-eye"></i> <?php echo $item->view ?></span>
                                    <span class="price"><?php echo number_format($item->price,2) ?> TL</span>
                                </div>
                                <div class="detail"><a href="<?php echo $domain.$item->themeSlug?>" class="header-order-button-slid">Görüntüle</a></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php if($toplam_icerik > $limit) { ?>
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center pagination-nuhost">
                        <?php
                        $x = 2;
                        if($sayfa > 1) {
                            $onceki = $sayfa - 1;
                            echo '<li class="page-item"><a class="page-link" href="'.(isset($get['kategori']) ? $web.'?kategori='.$get['kategori'].'&sayfa='.$onceki : $web.'?sayfa='.$onceki).'"><i class="fas fa-angle-left"></i></a></li>';
                        }
                        if($sayfa == 1) {
                            echo '<li class="page-item active disabled"><a class="page-link" href="#">1</a></li>';
                        } else {
                            echo '<li class="page-item"><a class="page-link" href="'.(isset($get['kategori']) ? $web.'?kategori='.$get['kategori'].'&sayfa=1' : $web.'?sayfa=1').'">1</a></li>';
                        }
                        if($sayfa - $x > 2) {
                            echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                            $i = $sayfa - $x;
                        } else {
                            $i = 2;
                        }

                        for($i; $i<=$sayfa + $x; $i++) {
                            if($i == $sayfa) {
                                echo '<li class="page-item active"><a class="page-link" href="#">'.$i.'</a></li>';
                            } else {
                                echo '<li class="page-item"><a class="page-link" href="'.(isset($get['kategori']) ? $web.'?kategori='.$get['kategori'].'&sayfa='.$i : $web.'?sayfa='.$i).'">'.$i.'</a></li>';
                            }
                            if($i == $sonSayfa) break;
                        }
                        if($sayfa + $x < $sonSayfa - 1) {
                            echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                            echo '<li class="page-item"><a class="page-link" href="'.(isset($get['kategori']) ? $web.'?kategori='.$get['kategori'].'&sayfa='.$sonSayfa : $web.'?sayfa='.$sonSayfa).'">'.$sonSayfa.'</a></li>';
                        } elseif($sayfa + $x == $sonSayfa - 1) {
                            echo '<li class="page-item"><a class="page-link" href="'.(isset($get['kategori']) ? $web.'?kategori='.$get['kategori'].'&sayfa='.$sonSayfa : $web.'?sayfa='.$sonSayfa).'">'.$sonSayfa.'</a></li>';
                        }

                        if($sayfa < $sonSayfa) {
                            $sonraki = $sayfa + 1;
                            echo '<li class="page-item">
                                    <a class="page-link" href="'.(isset($get['kategori']) ? $web.'?kategori='.$get['kategori'].'&sayfa='.$sonraki : $web.'?sayfa='.$sonraki).'"><i class="fas fa-angle-right"></i></a>
                                </li>';
                        }
                        ?>
                    </ul>
                </nav>
                <?php } ?>
            </div>
        </div>
    </div>
</section>