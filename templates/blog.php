<?php include_once $path . "templates/headerv3.php"; ?>
<section class="padding-60-0-100">
    <div class="container the_breadcrumb_conatiner_page">
        <div class="the_breadcrumb">
            <div class="breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="<?php echo $domain ?>">Anasayfa</a> /
                <?php if($request[1] != '') { ?>
                <a href="<?php echo $domain ?>blog">Blog</a> /
                Kategori / <?php echo $kategori->category_name ?>
                <?php } else { ?>
                Blog
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="container blog-container-page">
        <div class="row justify-content-left mr-tp-60">
            <div class="col-md-8">
                <?php foreach($query as $item) { ?>
                    <div class="home-blog-te special-in-blog-page"><!-- blog container -->
                        <div class="post-thumbn parallax-window" style="background: url('<?php echo $domain.$item->featured_image_thumb ?>') no-repeat center;"></div><!-- post thumbnail -->
                        <div class="post-bodyn">
                            <h5><a href="<?php echo $domain.'blog/'.$item->seo_url ?>"><?php echo $item->title ?></a></h5><!-- post title -->
                            <div class="post-bodyn-text"><?php echo mb_substr(unclear($item->description),0,300,'UTF-8') ?>...</div>
                            <p><i class="far fa-calendar"></i><?php echo date('d-m-Y',strtotime($item->created_at)) ?></p><!-- post date -->
                        </div>
                    </div><!-- end blog container -->
                <?php } ?>
                <?php if(empty($query)) { ?>
                    <div class="post-bodyn">
                        <p class="post-bodyn-text"><b>İçerik bulunamadı.</b></p>
                    </div>
                <?php } ?>
                <?php if($toplam_icerik > $limit) { ?>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center pagination-nuhost">
                            <?php
                            $x = 2;
                            if($sayfa > 1) {
                                $onceki = $sayfa - 1;
                                echo '<li class="page-item"><a class="page-link" href="?sayfa='.$onceki.'"><i class="fas fa-angle-left"></i></a></li>';
                            }
                            if($sayfa == 1) {
                                echo '<li class="page-item active disabled"><a class="page-link" href="#">1</a></li>';
                            } else {
                                echo '<li class="page-item"><a class="page-link" href="?sayfa=1">1</a></li>';
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
                                    echo '<li class="page-item"><a class="page-link" href="?sayfa='.$i.'">'.$i.'</a></li>';
                                }
                                if($i == $sonSayfa) break;
                            }
                            if($sayfa + $x < $sonSayfa - 1) {
                                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                echo '<li class="page-item"><a class="page-link" href="?sayfa='.$sonSayfa.'">'.$sonSayfa.'</a></li>';
                            } elseif($sayfa + $x == $sonSayfa - 1) {
                                echo '<li class="page-item"><a class="page-link" href="?sayfa='.$sonSayfa.'">'.$sonSayfa.'</a></li>';
                            }

                            if($sayfa < $sonSayfa) {
                                $sonraki = $sayfa + 1;
                                echo '<li class="page-item">
                                    <a class="page-link" href="?sayfa='.$sonraki.'"><i class="fas fa-angle-right"></i></a>
                                </li>';
                            }
                            ?>
                        </ul>
                    </nav>
                <?php } ?>
            </div>
            <aside class="col-md-4 blog-sidebar">
                <div class="blog-aside-widget">
                    <h4 class="blog-aside-widget-title">Kategoriler</h4>
                    <ol class="list-unstyled mb-0">
                        <?php foreach($kategoriler as $item) { ?>
                            <li><a href="<?php echo $domain.'kategori/'.$item->category_slug ?>" <?php echo (isset($kategori) && $kategori->category_id == $item->category_id ? 'class="active"' : null) ?>><?php echo $item->category_name ?></a></li>
                        <?php } ?>
                    </ol>
                </div>
            </aside><!-- /.blog-sidebar -->
        </div>
    </div>
</section>