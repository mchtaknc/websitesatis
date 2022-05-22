<?php include_once $path . "templates/headerv3.php"; ?>
<section class="padding-60-0-100">
    <div class="container the_breadcrumb_conatiner_page">
        <div class="the_breadcrumb">
            <div class="breadcrumbs"><i class="fas fa-home"></i><a href="<?php echo $domain ?>">Anasayfa</a> / <a href="<?php echo $domain ?>blog">Blog</a> / <?php echo $query->title ?></div>
        </div>
    </div>

    <div class="container blog-container-page">
        <div class="row justify-content-left mr-tp-60">
            <div class="col-md-8">
                <div class="home-blog-te special-in-blog-page"><!-- blog container -->
                    <div class="post-thumbn parallax-window" style="background: url('<?php echo $domain.$query->featured_image_thumb ?>') no-repeat center;"></div><!-- post thumbnail -->
                    <div class="post-bodyn">
                        <ul class="post-info">
                            <li class="admin"><a href="#"><i class="fa fa-user"></i>Sitedeposu</a></li>
                            <li class="date"><i class="fa fa-calendar"></i><?php echo date('d-m-Y',strtotime($query->created_at)) ?></li>
                        </ul>
                        <br>
                        <h5><?php echo $query->title ?></h5><!-- post title -->
                        <div class="post-bodyn-text"><?php echo unclear($query->description) ?></div>
                    </div>
                </div><!-- end blog container -->
            </div>
            <aside class="col-md-4 blog-sidebar">
                <div class="blog-aside-widget">
                    <h4 class="blog-aside-widget-title">Kategoriler</h4>
                    <ol class="list-unstyled mb-0">
                        <?php foreach($kategoriler as $item) { ?>
                            <li><a href="<?php echo $domain.'kategori/'.$item->category_slug ?>"><?php echo $item->category_name ?></a></li>
                        <?php } ?>
                    </ol>
                </div>
            </aside><!-- /.blog-sidebar -->
        </div>
    </div>
</section>