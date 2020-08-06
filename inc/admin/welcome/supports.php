<div class="wp-tab-panel" id="welcome">
    <div class="master_addons_features">
        <div class="master_addons_feature">

            <div class="parent">

                <div class="left_column">
                    <div class="left_block">
                        <ul class="master-addons-supports-list">
                            <li>
                                <div class="addons-supports-list-content">
                                    <a href="https://www.facebook.com/groups/2495256720521297/" target="_blank">
                                        <?php echo esc_html__('Facebook Community', MELA_TD);?>
                                    </a>
                                    <div class="addons-supports-list-icon">
                                        <img src="<?php echo MELA_ADMIN_ASSETS;?>icons/fb_group.svg" alt="Join our Facebook Community">
                                    </div><!-- /.addons-supports-list-icon -->
                                </div><!-- /.addons-supports-list-content -->
                            </li>
                            <li>
                                <div class="addons-supports-list-content">
                                    <a href="https://www.facebook.com/masteraddons" target="_blank">
                                        <?php echo esc_html__('Like Facebook Page', MELA_TD);?>
                                    </a>
                                    <div class="addons-supports-list-icon">
                                        <img src="<?php echo MELA_ADMIN_ASSETS;?>icons/fb_page.svg" alt="Like Facebook Page">
                                    </div><!-- /.addons-supports-list-icon -->
                                </div><!-- /.addons-supports-list-content -->
                            </li>
                            <li>
                                <div class="addons-supports-list-content">
                                    <a href="https://master-addons.com/contact-us/" target="_blank">
                                        <?php echo esc_html__('Email Support', MELA_TD);?>
                                    </a>
                                    <div class="addons-supports-list-icon">
                                        <img src="<?php echo MELA_ADMIN_ASSETS;?>icons/MA_icon.svg" alt="Contact Support">
                                    </div><!-- /.addons-supports-list-icon -->
                                </div><!-- /.addons-supports-list-content -->
                            </li>
                            <li>
                                <div class="addons-supports-list-content">
                                    <a href="https://wordpress.org/support/plugin/master-addons/" target="_blank">
                                        <?php echo esc_html__('WordPress.org Support', MELA_TD);?>
                                    </a>

                                    <div class="addons-supports-list-icon">
                                        <img src="<?php echo MELA_ADMIN_ASSETS;?>icons/ma_wp_support.svg" alt="Icon Image">
                                    </div><!-- /.addons-supports-list-icon -->
                                </div><!-- /.addons-supports-list-content -->
                            </li>
                            <li>
                                <div class="addons-supports-list-content">
                                    <a href="https://www.youtube.com/playlist?list=PLqpMw0NsHXV9V6UwRniXTUkabCJtOhyIf" target="_blank">
                                        <?php echo esc_html__('Video Tutorials', MELA_TD);?>
                                    </a>

                                    <div class="addons-supports-list-icon">
                                        <img src="<?php echo MELA_ADMIN_ASSETS;?>icons/video.svg" alt="Icon Image">
                                    </div><!-- /.addons-supports-list-icon -->
                                </div><!-- /.addons-supports-list-content -->
                            </li>

                            <li>
                                <div class="addons-supports-list-content">
                                    <a href="https://master-addons.com/docs/" target="_blank">
                                        <?php echo esc_html__('Documentation', MELA_TD);?>
                                    </a>

                                    <div class="addons-supports-list-icon">
                                        <img src="<?php echo MELA_ADMIN_ASSETS;?>icons/docs.svg" alt="Icon Image">
                                    </div><!-- /.addons-supports-list-icon -->
                                </div><!-- /.addons-supports-list-content -->
                            </li>
                        </ul>

                        <div class="master-addons-star-review">
                            <div class="review-content-left">
                                <img src="<?php echo MELA_ADMIN_ASSETS;?>icons/reviews.svg" alt="Icon Image">
                            </div><!-- /.review-content-left -->

                            <div class="review-content-right">
                                <h4>
                                    <?php echo esc_html__('Show Us Some Love', MELA_TD);?>
                                </h4>
                                <p>
                                    <?php echo esc_html__('If you like Master Addons and want to support our work, please rate us 5 Star in our WordPress plugin repo. It will inspire us to work more and bring some amazing elements for your website. Thanks for using Master Addons.', MELA_TD);?>
                                </p>

                                <a href="https://wordpress.org/support/plugin/master-addons/reviews/#new-post" target="_blank">
                                    <button class="master-addons-review-btn">
                                        <?php echo esc_html__('Give a Five star review', MELA_TD);?>
                                    </button>
                                </a>
                            </div><!-- /.review-content-right -->
                        </div><!-- /.master-addons-star-review -->

                        <div class="master-addons-support-faq">
                            <div class="master-addons-faq-content">
                                <h4>
                                    <?php echo esc_html__('What is Master Addons & How does it Work?', MELA_TD);?>
                                </h4>
                                <p>
                                    <?php echo esc_html__('It\'s an Elementor Addons pack plugin. This plugin adds more elements inside your elementor editor. Master Addons will work on any WordPress website which has Elementor page builder installed', MELA_TD);?>
                                </p>
                            </div><!-- /.master-addons-faq-content -->

                            <div class="master-addons-faq-content">
                                <h4>
                                    <?php echo esc_html__('How to Upgrade from Free to Pro?', MELA_TD);?>
                                </h4>
                                <p>
                                    <?php echo esc_html__('It\'s easy to upgrade your free version to pro with a few steps. We have explained details on Free to Pro Update here.', MELA_TD);?>
                                </p>
                            </div><!-- /.master-addons-faq-content -->

                            <a href="https://master-addons.com/pricing/" target="_blank">
                                <button class="master-addons-btn read-more">
                                    <?php echo esc_html__('Read more', MELA_TD);?>
                                </button>
                            </a>
                        </div><!-- /.master-addons-support-faq -->
                    </div>
                </div>

                <div class="right_column">

                    <?php if ( ma_el_fs()->is_not_paying() ) {?>
                        <div class="master-addons-banner">
                            <a href="https://master-addons.com/pricing" target="_blank">
                                <img class="tab-banner" src="<?php echo MELA_ADMIN_ASSETS;?>icons/upgrade-pro.png" alt="Upgrade to Pro Master Addons">
                            </a>
                        </div><!-- /.master-addons-banner -->
                    <?php } ?>

                    <div class="master-addons-right-column-widget">
                        <img class="icon-image" src="<?php echo MELA_ADMIN_ASSETS;?>icons/contribute.svg" alt="Contribute to Master Addons">
                        <h4>
                            <?php echo esc_html__('Contribute to Master Addons', MELA_TD);?>
                        </h4>
                        <p>
                            <?php echo esc_html__('If you need any unique elements for your website, please get in touch with us. We will hear your idea and develop element.', MELA_TD);?>
                        </p>
                        <a href="https://master-addons.com/contact-us/" target="_blank">
                            <button class="master-addons-widget-btn">
                                <?php echo esc_html__('Request a Feature', MELA_TD);?>
                            </button>
                        </a>
                    </div><!-- /.master-addons-right-column-widget -->

                    <div class="master-addons-right-column-widget">
                        <img class="icon-image" src="<?php echo MELA_ADMIN_ASSETS;?>icons/newsletter.svg" alt="Subscript to Newsletter">
                        <h4>
                            <?php echo esc_html__('Subscribe Newsletter', MELA_TD);?>
                        </h4>
                        <p>
                            <?php echo esc_html__('Newsletter is the best way for you to get informed about the latest news and updates. Subscribe now & stay updated.', MELA_TD);?>
                        </p>
                        <a href="https://master-addons.com/newsletter/" target="_blank">
                            <button class="master-addons-widget-btn">
                                <?php echo esc_html__('Subscribe Now', MELA_TD);?>
                            </button>
                        </a>
                    </div><!-- /.master-addons-right-column-widget -->

                </div>
            </div>

        </div>
    </div>
</div>
