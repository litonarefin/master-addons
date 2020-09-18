<?php
namespace ExclusiveAddons\Elements;

if ( ! defined( 'ABSPATH' ) ) exit; // If this file is called directly, abort.

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Control_Media;
use \Elementor\Utils;
use \Elementor\Widget_Base;

class Logo_Box extends Widget_Base {

	public function get_name() {
		return 'exad-logo';
	}

	public function get_title() {
		return esc_html__( 'Logo Box', MELA_TD );
	}

	public function get_icon() {
		return 'exad-element-icon eicon-logo';
	}

	public function get_categories() {
		return [ 'master-addons' ];
	}

	protected function _register_controls() {

        /*
        * Logo Image
        */
        $this->start_controls_section(
            'exad_section_logo_image',
            [
                'label' => esc_html__( 'Content', MELA_TD )
            ]
        );

        $this->add_control(
            'exad_logo_image',
            [
                'label'   => esc_html__( 'Logo Image', MELA_TD ),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src()
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'      => 'thumbnail',
                'default'   => 'full',
                'condition' => [
                    'exad_logo_image[url]!' => ''
                ]
            ]
        );

        $this->add_control(
            'exad_logo_box_enable_link',
            [
                'label'        => __( 'Enable Link', MELA_TD ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Show', MELA_TD ),
                'label_off'    => __( 'Hide', MELA_TD ),
                'return_value' => 'yes',
                'default'      => 'no'
            ]
        );

        $this->add_control(
            'exad_logo_box_link',
            [
                'label'         => __( 'Link', MELA_TD ),
                'type'          => Controls_Manager::URL,
                'placeholder'   => __( 'https://your-link.com', MELA_TD ),
                'show_external' => true,
                'default'       => [
                    'url'         => '',
                    'is_external' => true
                ],
                'condition'     => [
                    'exad_logo_box_enable_link' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

        /*
        * Logo Style
        *
        */
    	$this->start_controls_section(
    		'exad_section_logo_style',
    		[
                'label' => esc_html__( 'Style', MELA_TD ),
                'tab'   => Controls_Manager::TAB_STYLE
    		]
        );

        $this->start_controls_tabs( 'exad_logo_tabs' );

    	# Normal tab
        $this->start_controls_tab( 'normal', [ 'label' => esc_html__( 'Normal', MELA_TD ) ] );

            $this->add_control(
        		'exad_logo_background_style',
        			[
                    'label' => __( 'Background Style', MELA_TD ),
                    'type'  => Controls_Manager::HEADING
        			]
            );

            $this->add_group_control(
        		Group_Control_Background::get_type(),
    			[
                    'name'      => 'exad_logo_background',
                    'types'     => [ 'classic', 'gradient' ],
                    'separator' => 'before',
                    'selector'  => '{{WRAPPER}} .exad-logo .exad-logo-item'
    			]
            );

            $this->add_control(
        		'exad_logo_opacity_style',
        		[
                    'label' => __( 'Opacity', MELA_TD ),
                    'type'  => Controls_Manager::HEADING
        		]
            );

            $this->add_control(
                'exad_logo_opacity',
                [
                    'label' => __('Opacity', MELA_TD),
                    'type'  => Controls_Manager::NUMBER,
                    'range' => [
                        'min'   => 0,
                        'max'   => 1
            		],
                    'selectors' => [
                        '{{WRAPPER}} .exad-logo .exad-logo-item img' => 'opacity: {{VALUE}};'
                    ]
                ]
            );

            $this->add_control(
    			'exad_logo_shadow_style',
    			[
                    'label' => __( 'Box Shadow', MELA_TD ),
                    'type'  => Controls_Manager::HEADING
    			]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name'     => 'exad_logo_box_shadow',
                    'selector' => '{{WRAPPER}} .exad-logo .exad-logo-item'
                ]
            );

        $this->end_controls_tab();

    	# Hover tab
        $this->start_controls_tab( 'exad_exclusive_button_hover', [ 'label' => esc_html__( 'Hover', MELA_TD ) ] );

            $this->add_control(
    			'exad_logo_hover_background',
    			[
                    'label' => __( 'Background Style', MELA_TD ),
                    'type'  => Controls_Manager::HEADING
    			]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name'      => 'exad_logo_hover_background_hover',
                    'types'     => [ 'classic', 'gradient' ],
                    'separator' => 'before',
                    'selector'  => '{{WRAPPER}} .exad-logo .exad-logo-item:hover'
                ]
            );

            $this->add_control(
        		'exad_logo_opacity_hover_style',
        		[
                    'label' => __( 'Opacity', MELA_TD ),
                    'type'  => Controls_Manager::HEADING
        		]
            );

            $this->add_control(
                'exad_logo_hover_opacity',
                [
                    'label'     => __('Opacity', MELA_TD),
                    'type'      => Controls_Manager::NUMBER,
                    'range'     => [
                        'min'   => 0,
                        'max'   => 1
                    ],
                    'default'   => __( 'From 0.1 to 1', MELA_TD ),
                    'selectors' => [
                        '{{WRAPPER}} .exad-logo .exad-logo-item:hover img' => 'opacity: {{VALUE}};'
                    ]
                ]
            );

            $this->add_control(
                'exad_logo_shadow_hover_style',
                [
                    'label' => __( 'Box Shadow', MELA_TD ),
                    'type'  => Controls_Manager::HEADING
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name'     => 'exad_logo_box_hover_shadow',
                    'selector' => '{{WRAPPER}} .exad-logo .exad-logo-item:hover'
                ]
            );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'exad_logo_padding',
            [
                'label'      => __( 'Padding', MELA_TD ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'separator'  => 'before',
                'default'    => [
                    'top'    => 20,
                    'right'  => 20,
                    'bottom' => 20,
                    'left'   => 20,
                    'unit'   => 'px'
                ],
                'selectors'  => [
                    '{{WRAPPER}} .exad-logo .exad-logo-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'border',
                'selector' => '{{WRAPPER}} .exad-logo .exad-logo-item'
            ]
        );
        $this->add_control(
    		'exad_logo_border_radius',
            [
                'label'      => __( 'Border Radius', MELA_TD ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .exad-logo .exad-logo-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_section();
	}
	protected function render() {
        $settings       = $this->get_settings_for_display();
        $logo_image     = $settings['exad_logo_image'];
        $logo_image_url = Group_Control_Image_Size::get_attachment_image_src( $logo_image['id'], 'thumbnail', $settings );
        $exad_logo_link = $settings['exad_logo_box_link']['url'];

        if( $exad_logo_link ) {
            $this->add_render_attribute( 'exad_logo_box_link', 'href', esc_url( $exad_logo_link ) );
            if( $settings['exad_logo_box_link']['is_external'] ) {
                $this->add_render_attribute( 'exad_logo_box_link', 'target', '_blank' );
            }
            if( $settings['exad_logo_box_link']['nofollow'] ) {
                $this->add_render_attribute( 'exad_logo_box_link', 'rel', 'nofollow' );
            }
        }

		if ( empty( $logo_image_url ) ) {
			$logo_image_url = $logo_image['url'];
		}  else {
			$logo_image_url = $logo_image_url;
        }

        echo '<div class="exad-logo one">';
            echo '<div class="exad-logo-item">';
                if( ! empty( $settings['exad_logo_image'] ) ) :

                    if( !empty( $exad_logo_link ) && 'yes' === $settings['exad_logo_box_enable_link'] ) :
                        echo '<a '.$this->get_render_attribute_string( 'exad_logo_box_link' ).'>';
                    endif;

                    echo '<img src="'.esc_url( $logo_image_url ).'" alt="'.Control_Media::get_image_alt( $settings['exad_logo_image'] ).'">';

                    if( !empty( $exad_logo_link ) && 'yes' === $settings['exad_logo_box_enable_link'] ) :
                        echo '</a>';
                    endif;
                endif;
            echo '</div>';
        echo '</div>';
	}

    /**
     * Render logo box widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _content_template() {
        ?>
        <#
            if ( settings.exad_logo_image.url || settings.exad_logo_image.id ) {
                var image = {
                    id: settings.exad_logo_image.id,
                    url: settings.exad_logo_image.url,
                    size: settings.thumbnail_size,
                    dimension: settings.thumbnail_custom_dimension,
                    class: 'exad-logo-box-img',
                    model: view.getEditModel()
                };

                var image_url = elementor.imagesManager.getImageUrl( image );
            }

            var target   = settings.exad_logo_box_link.is_external ? ' target="_blank"' : '';
            var nofollow = settings.exad_logo_box_link.nofollow ? ' rel="nofollow"' : '';
        #>
        <div class="exad-logo one">
            <div class="exad-logo-item">
                <# if ( image_url ) { #>
                    <# if ( settings.exad_logo_box_link && 'yes' === settings.exad_logo_box_enable_link ) { #>
                        <a href="{{{ settings.exad_logo_box_link.url }}}"{{{ target }}}{{{ nofollow }}}>
                    <# } #>
                    <img src="{{{ image_url }}}">
                    <# if ( settings.exad_logo_box_link && 'yes' === settings.exad_logo_box_enable_link ) { #>
                        </a>
                    <# } #>
                <# } #>
            </div>
        </div>
        <?php
    }
}