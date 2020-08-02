<?php
    /**
     * Author Name: Liton Arefin
     * Author URL: https://jeweltheme.com
     * Date: 9/4/19
     */

    if (!defined('ABSPATH')) { exit; } // No, Direct access Sir !!!



    if( !class_exists('Master_Addons_Promotions') ) {
        class Master_Addons_Promotions {

            private static $instance = null;

            public static function get_instance() {
                if ( ! self::$instance ) {
                    self::$instance = new self;
                }
                return self::$instance;
            }

            public function __construct(){
                $this->init();

                // Admin Notices
                global $pagenow;
                add_action('admin_print_styles', [$this, 'jltma_admin_notice_styles']);

                // if ( ( 'admin.php' === $pagenow ) && ( 'master-addons-settings' === $_GET['page'] ) ) {}
                add_action( 'admin_notices', [$this, 'jltma_latest_blog_update'], 10 );
                add_action( 'admin_notices', [$this, 'jltma_request_review_after_seven_days'], 10 );
                add_action( 'admin_notices', [$this, 'jltma_request_review_after_ten_days'], 10 );
                add_action( 'admin_notices', [$this, 'jltma_request_review_after_fifteen_days'], 10 );
                add_action( 'admin_notices', [$this, 'jltma_request_review_after_tweenty_days'], 10 );
                add_action( 'admin_notices', [$this, 'jltma_request_review_after_thirty_days'], 10 );
                add_action( 'admin_notices', [$this, 'jltma_request_review_after_fourty_five_days'], 10 );
                add_action( 'admin_notices', [$this, 'jltma_request_review_after_ninety_days'], 10 );
            }

            public function init(){
                include_once MELA_PLUGIN_PATH . '/inc/classes/admin-notices.php';
            }

            public function jltma_admin_notice_styles(){ ?>
                <style type="text/css">
                    #master-addons-review-notice .notice-dismiss{padding:0 0 0 26px}#master-addons-review-notice .notice-dismiss:before{display:none}#master-addons-review-notice.master-addons-review-notice{padding:10px 10px 10px 0;background-color:#fff;border-radius:3px;border-left:4px solid transparent}#master-addons-review-notice .master-addons-review-thumbnail{width:114px;float:left;line-height:80px;text-align:center;border-right:4px solid transparent}#master-addons-review-notice .master-addons-review-thumbnail img{width:60px;vertical-align:middle}#master-addons-review-notice .master-addons-review-text{overflow:hidden}#master-addons-review-notice .master-addons-review-text h3{font-size:24px;margin:0 0 5px;font-weight:400;line-height:1.3}#master-addons-review-notice .master-addons-review-text p{font-size:13px;margin:0 0 5px}#master-addons-review-notice .master-addons-review-ul{margin:0;padding:0}#master-addons-review-notice .master-addons-review-ul li{display:inline-block;margin-right:15px}#master-addons-review-notice .master-addons-review-ul li a{display:inline-block;color:#4b00e7;text-decoration:none;padding-left:26px;position:relative}#master-addons-review-notice .master-addons-review-ul li a span{position:absolute;left:0;top:-2px}
                </style>
            <?php }

            public function jltma_get_total_interval($interval, $type){
                switch($type){
                    case 'years':
                        return $interval->format('%Y');
                        break;
                    case 'months':
                        $years = $interval->format('%Y');
                        $months = 0;
                        if($years){
                            $months += $years*12;
                        }
                        $months += $interval->format('%m');
                        return $months;
                        break;
                    case 'days':
                        return $interval->format('%a');
                        break;
                    case 'hours':
                        $days = $interval->format('%a');
                        $hours = 0;
                        if($days){
                            $hours += 24 * $days;
                        }
                        $hours += $interval->format('%H');
                        return $hours;
                        break;
                    case 'minutes':
                        $days = $interval->format('%a');
                        $minutes = 0;
                        if($days){
                            $minutes += 24 * 60 * $days;
                        }
                        $hours = $interval->format('%H');
                        if($hours){
                            $minutes += 60 * $hours;
                        }
                        $minutes += $interval->format('%i');
                        return $minutes;
                        break;
                    case 'seconds':
                        $days = $interval->format('%a');
                        $seconds = 0;
                        if($days){
                            $seconds += 24 * 60 * 60 * $days;
                        }
                        $hours = $interval->format('%H');
                        if($hours){
                            $seconds += 60 * 60 * $hours;
                        }
                        $minutes = $interval->format('%i');
                        if($minutes){
                            $seconds += 60 * $minutes;
                        }
                        $seconds += $interval->format('%s');
                        return $seconds;
                        break;
                    case 'milliseconds':
                        $days = $interval->format('%a');
                        $seconds = 0;
                        if($days){
                            $seconds += 24 * 60 * 60 * $days;
                        }
                        $hours = $interval->format('%H');
                        if($hours){
                            $seconds += 60 * 60 * $hours;
                        }
                        $minutes = $interval->format('%i');
                        if($minutes){
                            $seconds += 60 * $minutes;
                        }
                        $seconds += $interval->format('%s');
                        $milliseconds = $seconds * 1000;
                        return $milliseconds;
                        break;
                    default:
                        return NULL;
                }
            }

            public function jltma_days_differences(){

                $install_date = get_option( 'jltma_activation_time' );
                // $install_date = strtotime('2020-07-12 12:00:00'); // Testing date
                $jltma_datetime1 = \DateTime::createFromFormat( 'U', $install_date );
                $jltma_datetime2 = \DateTime::createFromFormat( 'U', strtotime("now") );

                $jltma_date_format = 'Y-m-d H:i:s';
                $jltma_datetime_format_1 = $jltma_datetime1->format( $jltma_date_format );
                $jltma_datetime_format_2 = $jltma_datetime2->format( $jltma_date_format );

                $interval = $jltma_datetime2->diff($jltma_datetime1);

                $jltma_days_diff = $this->jltma_get_total_interval($interval, 'days');

                return $jltma_days_diff;

            }

            public function jltma_admin_upgrade_pro_notice( $notice_key ){ ?>

                <div data-dismissible="<?php echo esc_attr($notice_key);?>" class="updated notice notice-success is-dismissible">
                    <div id="master-addons-review-notice" class="master-addons-review-notice">
                        We’re with you! We're offering <strong>50% Discount</strong> on all pricing due to the impact of COVID-19! Coupon Code: <strong>COVID5</strong>
                         <a href="<?php echo ma_el_fs()->get_upgrade_url();?>" target="_blank">Upgrade Pro</a> now
                    </div>
                </div>

            <?php }


            public function jltma_admin_notice_ask_for_review( $notice_key ){ ?>

                <div data-dismissible="<?php echo esc_attr($notice_key);?>" class="updated notice notice-success is-dismissible">
                    <div id="master-addons-review-notice" class="master-addons-review-notice">
                        <div class="master-addons-review-thumbnail">
                            <img src="https://ps.w.org/master-addons/assets/icon-256x256.png" alt="">
                        </div>
                        <div class="master-addons-review-text">

                            <h3><?php _e( 'Enjoying <strong>Master Addons</strong>?', MELA_TD ) ?></h3>
                            <p><?php _e( 'Seems like you are enjoying <strong>Master Addons</strong>. Would you please show us a little Love by rating us in the <a href="https://wordpress.org/support/plugin/master-addons/reviews/#postform" target="_blank"><strong>WordPress.org</strong></a>?', MELA_TD ) ?></p>

                            <ul class="master-addons-review-ul">
                                <li><a href="https://wordpress.org/support/plugin/master-addons/reviews/#postform"
                                       target="_blank"><span class="dashicons dashicons-external"></span><?php _e( 'Sure! I\'d love to!', MELA_TD ) ?></a></li>
                                <li><a href="#" class="notice-dismiss"><span class="dashicons dashicons-smiley"></span><?php _e( 'I\'ve already left a review', MELA_TD ) ?></a></li>
                                <li><a href="#" class="notice-dismiss"><span class="dashicons dashicons-dismiss"></span><?php _e( 'Never show again', MELA_TD ) ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php }

            public function jltma_latest_blog_update(){
                if ( ! PAnD::is_admin_notice_active( 'disable-done-notice-forever' ) ) {
                    return;
                }
                $blog_update_message = sprintf(
                    __( '%1$s got HUGE Updates!!! %2$s %3$s %4$s %5$s Check Blog Post for <a href="%6$s" target="__blank">%7$s</a>', MELA_TD ),

                    '<strong>' . esc_html__( 'Master Addons for Elementor', MELA_TD ) . '</strong>',
                    '<br>' . esc_html__( '✅ Custom Breakpoints', MELA_TD ) . '<br>',
                    esc_html__( '✅ Post/Page Duplicator', MELA_TD ) . '<br>',
                    esc_html__( '✅ 30+ Header & Footer Templates', MELA_TD ) . '<br>',
                    esc_html__( '✅ Live Demo, Documentation, Video help links on On/Off tooltips', MELA_TD ) . '<br>',
                    esc_url_raw('https://master-addons.com/master-addons-v1-4-5/'),
                    esc_html__( 'More Details', MELA_TD )
                );

                printf( '<div data-dismissible="disable-done-notice-forever" class="updated notice notice-success is-dismissible"><p>%1$s</p></div>', $blog_update_message );
            }

            public function jltma_request_review_after_seven_days(){

                if ( ! PAnD::is_admin_notice_active( 'jltma-days-7' ) ) {
                    return;
                }

                $jltma_seven_day_notice = $this->jltma_days_differences();

                if( $jltma_seven_day_notice >= 7 && $jltma_seven_day_notice < 15){
                    $this->jltma_admin_notice_ask_for_review( 'jltma-days-7' );
                }
            }

            public function jltma_request_review_after_ten_days(){

                if ( ! PAnD::is_admin_notice_active( 'jltma-days-10' ) ) { return; }

                $jltma_seven_day_notice = $this->jltma_days_differences();

                if( $jltma_seven_day_notice > 7 && $jltma_seven_day_notice < 10){
                    $this->jltma_admin_upgrade_pro_notice( 'jltma-days-10' );
                }
            }

            public function jltma_request_review_after_fifteen_days(){

                if ( ! PAnD::is_admin_notice_active( 'jltma-days-15' ) ) { return; }

                $jltma_seven_day_notice = $this->jltma_days_differences();

                if( $jltma_seven_day_notice > 7 && $jltma_seven_day_notice < 15 ){
                    $this->jltma_admin_notice_ask_for_review( 'jltma-days-15' );
                }
            }


            public function jltma_request_review_after_tweenty_days(){

                if ( ! PAnD::is_admin_notice_active( 'jltma-days-20' ) ) { return; }

                $jltma_seven_day_notice = $this->jltma_days_differences();

                if( $jltma_seven_day_notice > 20){
                    $this->jltma_admin_upgrade_pro_notice( 'jltma-days-20' );
                }
            }

            public function jltma_request_review_after_thirty_days(){

                if ( ! PAnD::is_admin_notice_active( 'jltma-days-30' ) ) { return; }

                $jltma_seven_day_notice = $this->jltma_days_differences();

                if( $jltma_seven_day_notice > 30){
                    $this->jltma_admin_notice_ask_for_review( 'jltma-days-30' );
                }
            }


            public function jltma_request_review_after_fourty_five_days(){

                if ( ! PAnD::is_admin_notice_active( 'jltma-days-45' ) ) { return; }

                $jltma_seven_day_notice = $this->jltma_days_differences();

                if( $jltma_seven_day_notice > 45){
                    $this->jltma_admin_upgrade_pro_notice( 'jltma-days-45' );
                }
            }

            public function jltma_request_review_after_ninety_days(){

                if ( ! PAnD::is_admin_notice_active( 'jltma-days-90' ) ) { return; }

                $jltma_seven_day_notice = $this->jltma_days_differences();

                if( $jltma_seven_day_notice > 90){
                    $this->jltma_admin_upgrade_pro_notice( 'jltma-days-90' );
                }
            }



        }

        Master_Addons_Promotions::get_instance();
    }