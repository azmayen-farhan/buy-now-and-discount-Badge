<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

/**
 * Elementor widget wrapping the Buy Now button.
 *
 * Renders via wbndb_get_buy_now_button_html() (see main plugin file) so the
 * checkout-redirect / variation-picker behaviour stays identical to the
 * [buy_now_button] shortcode — only the label and appearance are editable
 * here.
 */
class WBNDB_Elementor_Buy_Now_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'wbndb_buy_now';
    }

    public function get_title() {
        return esc_html__( 'Buy Now Button', 'woo-buy-now-discount-badge' );
    }

    public function get_icon() {
        return 'eicon-cart-medium';
    }

    public function get_categories() {
        return array( 'wbndb-category' );
    }

    public function get_keywords() {
        return array( 'buy now', 'woocommerce', 'button', 'checkout', 'add to cart' );
    }

    /**
     * Only makes sense on a single product context.
     */
    public function get_custom_help_url() {
        return '';
    }

    protected function register_controls() {

        /* ============================== CONTENT ============================== */

        $this->start_controls_section(
            'section_content',
            array(
                'label' => esc_html__( 'Button', 'woo-buy-now-discount-badge' ),
            )
        );

        $this->add_control(
            'wbndb_notice',
            array(
                'type'            => \Elementor\Controls_Manager::RAW_HTML,
                'raw'             => esc_html__( 'This button only renders on a single WooCommerce product page (it needs a product to link to).', 'woo-buy-now-discount-badge' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            )
        );

        $this->add_control(
            'button_text',
            array(
                'label'       => esc_html__( 'Text', 'woo-buy-now-discount-badge' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__( 'Buy Now', 'woo-buy-now-discount-badge' ),
                'placeholder' => esc_html__( 'Buy Now', 'woo-buy-now-discount-badge' ),
                'label_block' => false,
            )
        );

        $this->add_control(
            'position',
            array(
                'label'   => esc_html__( 'Position', 'woo-buy-now-discount-badge' ),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'options' => array(
                    'left'    => array(
                        'title' => esc_html__( 'Left', 'woo-buy-now-discount-badge' ),
                        'icon'  => 'eicon-h-align-left',
                    ),
                    'center'  => array(
                        'title' => esc_html__( 'Center', 'woo-buy-now-discount-badge' ),
                        'icon'  => 'eicon-h-align-center',
                    ),
                    'right'   => array(
                        'title' => esc_html__( 'Right', 'woo-buy-now-discount-badge' ),
                        'icon'  => 'eicon-h-align-right',
                    ),
                    'stretch' => array(
                        'title' => esc_html__( 'Stretch', 'woo-buy-now-discount-badge' ),
                        'icon'  => 'eicon-h-align-stretch',
                    ),
                ),
                'default' => 'left',
                'toggle'  => false,
            )
        );

        $this->end_controls_section();

        /* ============================== STYLE ============================== */

        $this->start_controls_section(
            'section_style',
            array(
                'label' => esc_html__( 'Button', 'woo-buy-now-discount-badge' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'typography',
                'selector' => '{{WRAPPER}} .wbndb-buy-now-btn',
            )
        );

        $this->start_controls_tabs( 'style_tabs' );

        /* --- Normal --- */
        $this->start_controls_tab(
            'style_tab_normal',
            array(
                'label' => esc_html__( 'Normal', 'woo-buy-now-discount-badge' ),
            )
        );

        $this->add_control(
            'text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'woo-buy-now-discount-badge' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    // !important: themes/WooCommerce style .button with more
                    // specific selectors (e.g. ".woocommerce .button") than
                    // Elementor's generated widget CSS, so without this the
                    // theme color wins even though a color is set here.
                    '{{WRAPPER}} .wbndb-buy-now-btn' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'woo-buy-now-discount-badge' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wbndb-buy-now-btn' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'border_style',
            array(
                'label'     => esc_html__( 'Border Style', 'woo-buy-now-discount-badge' ),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'options'   => array(
                    ''       => esc_html__( 'Default', 'woo-buy-now-discount-badge' ),
                    'none'   => esc_html__( 'None', 'woo-buy-now-discount-badge' ),
                    'solid'  => esc_html__( 'Solid', 'woo-buy-now-discount-badge' ),
                    'dashed' => esc_html__( 'Dashed', 'woo-buy-now-discount-badge' ),
                    'dotted' => esc_html__( 'Dotted', 'woo-buy-now-discount-badge' ),
                    'double' => esc_html__( 'Double', 'woo-buy-now-discount-badge' ),
                ),
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .wbndb-buy-now-btn' => 'border-style: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_responsive_control(
            'border_width',
            array(
                'label'      => esc_html__( 'Border Width', 'woo-buy-now-discount-badge' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'condition'  => array( 'border_style!' => array( '', 'none' ) ),
                'selectors'  => array(
                    '{{WRAPPER}} .wbndb-buy-now-btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );

        $this->add_control(
            'border_color',
            array(
                'label'     => esc_html__( 'Border Color', 'woo-buy-now-discount-badge' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'condition' => array( 'border_style!' => array( '', 'none' ) ),
                'selectors' => array(
                    '{{WRAPPER}} .wbndb-buy-now-btn' => 'border-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_responsive_control(
            'border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'woo-buy-now-discount-badge' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .wbndb-buy-now-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'box_shadow',
                'selector' => '{{WRAPPER}} .wbndb-buy-now-btn',
            )
        );

        $this->end_controls_tab();

        /* --- Hover --- */
        $this->start_controls_tab(
            'style_tab_hover',
            array(
                'label' => esc_html__( 'Hover', 'woo-buy-now-discount-badge' ),
            )
        );

        $this->add_control(
            'hover_text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'woo-buy-now-discount-badge' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wbndb-buy-now-btn:hover' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'hover_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'woo-buy-now-discount-badge' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wbndb-buy-now-btn:hover' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'hover_border_color',
            array(
                'label'     => esc_html__( 'Border Color', 'woo-buy-now-discount-badge' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wbndb-buy-now-btn:hover' => 'border-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'hover_animation',
            array(
                'label' => esc_html__( 'Hover Animation', 'woo-buy-now-discount-badge' ),
                'type'  => \Elementor\Controls_Manager::HOVER_ANIMATION,
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'button_padding',
            array(
                'label'      => esc_html__( 'Padding', 'woo-buy-now-discount-badge' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .wbndb-buy-now-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'button_margin',
            array(
                'label'      => esc_html__( 'Margin', 'woo-buy-now-discount-badge' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .wbndb-buy-now-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Elementor auto-enqueues this handle (and only this handle) whenever
     * the widget is actually used on a page — see wbndb_register_widget_styles()
     * in the main plugin file for where it's registered.
     */
    public function get_style_depends() {
        return array( 'wbndb-elementor-widgets' );
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $text = ( '' !== trim( (string) $settings['button_text'] ) )
            ? $settings['button_text']
            : esc_html__( 'Buy Now', 'woo-buy-now-discount-badge' );

        $extra_classes = 'wbndb-buy-now-btn';
        if ( ! empty( $settings['hover_animation'] ) ) {
            $extra_classes .= ' elementor-animation-' . sanitize_html_class( $settings['hover_animation'] );
        }

        $button_html = wbndb_get_buy_now_button_html(
            array(
                'text'    => $text,
                'classes' => $extra_classes,
            )
        );

        if ( '' === $button_html ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="elementor-alert elementor-alert-info">' .
                    esc_html__( 'The Buy Now button will render here on an actual single product page.', 'woo-buy-now-discount-badge' ) .
                    '</div>';
            }
            return;
        }

        $position      = ! empty( $settings['position'] ) ? $settings['position'] : 'left';
        $wrapper_class = 'wbndb-elementor-buy-now wbndb-position-' . sanitize_html_class( $position );

        // $button_html is already escaped field-by-field inside
        // wbndb_get_buy_now_button_html(); nothing further to sanitize here.
        echo '<div class="' . esc_attr( $wrapper_class ) . '">' . $button_html . '</div>';
    }
}
