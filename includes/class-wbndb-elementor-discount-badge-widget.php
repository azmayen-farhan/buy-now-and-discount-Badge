<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

/**
 * Elementor widget wrapping the "Save X" discount badge.
 *
 * Renders via wbndb_get_discount_badge_html() (see main plugin file) so the
 * price-calculation logic stays identical to the [loop_save_price]
 * shortcode — only the label, format, and appearance are editable here.
 */
class WBNDB_Elementor_Discount_Badge_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'wbndb_discount_badge';
    }

    public function get_title() {
        return esc_html__( 'Discount Badge', 'woo-buy-now-discount-badge' );
    }

    public function get_icon() {
        return 'eicon-price-list';
    }

    public function get_categories() {
        return array( 'wbndb-category' );
    }

    public function get_keywords() {
        return array( 'discount', 'sale', 'badge', 'woocommerce', 'save', 'percent off' );
    }

    protected function register_controls() {

        /* ============================== CONTENT ============================== */

        $this->start_controls_section(
            'section_content',
            array(
                'label' => esc_html__( 'Badge', 'woo-buy-now-discount-badge' ),
            )
        );

        $this->add_control(
            'wbndb_notice',
            array(
                'type'            => \Elementor\Controls_Manager::RAW_HTML,
                'raw'             => esc_html__( 'This badge only renders on the frontend when the current product is on sale.', 'woo-buy-now-discount-badge' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            )
        );

        $this->add_control(
            'badge_format',
            array(
                'label'   => esc_html__( 'Show As', 'woo-buy-now-discount-badge' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'amount',
                'options' => array(
                    'amount'  => esc_html__( 'Amount saved (e.g. Save $10)', 'woo-buy-now-discount-badge' ),
                    'percent' => esc_html__( 'Percentage off (e.g. Save 20%)', 'woo-buy-now-discount-badge' ),
                ),
            )
        );

        $this->add_control(
            'prefix_text',
            array(
                'label'       => esc_html__( 'Prefix Text', 'woo-buy-now-discount-badge' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => esc_html__( 'Save', 'woo-buy-now-discount-badge' ),
                'placeholder' => esc_html__( 'Save', 'woo-buy-now-discount-badge' ),
                'label_block' => false,
            )
        );

        $this->add_responsive_control(
            'align',
            array(
                'label'     => esc_html__( 'Alignment', 'woo-buy-now-discount-badge' ),
                'type'      => \Elementor\Controls_Manager::CHOOSE,
                'options'   => array(
                    'left'    => array(
                        'title' => esc_html__( 'Left', 'woo-buy-now-discount-badge' ),
                        'icon'  => 'eicon-text-align-left',
                    ),
                    'center'  => array(
                        'title' => esc_html__( 'Center', 'woo-buy-now-discount-badge' ),
                        'icon'  => 'eicon-text-align-center',
                    ),
                    'right'   => array(
                        'title' => esc_html__( 'Right', 'woo-buy-now-discount-badge' ),
                        'icon'  => 'eicon-text-align-right',
                    ),
                    'justify' => array(
                        'title' => esc_html__( 'Justified', 'woo-buy-now-discount-badge' ),
                        'icon'  => 'eicon-text-align-justify',
                    ),
                ),
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .wbndb-elementor-discount-badge' => 'text-align: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();

        /* ============================== STYLE ============================== */

        $this->start_controls_section(
            'section_style',
            array(
                'label' => esc_html__( 'Badge', 'woo-buy-now-discount-badge' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'typography',
                'selector' => '{{WRAPPER}} .wbndb-discount-badge',
            )
        );

        $this->add_control(
            'text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'woo-buy-now-discount-badge' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wbndb-discount-badge' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'woo-buy-now-discount-badge' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wbndb-discount-badge' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            array(
                'name'     => 'border',
                'selector' => '{{WRAPPER}} .wbndb-discount-badge',
            )
        );

        $this->add_responsive_control(
            'border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'woo-buy-now-discount-badge' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .wbndb-discount-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'box_shadow',
                'selector' => '{{WRAPPER}} .wbndb-discount-badge',
            )
        );

        $this->add_responsive_control(
            'badge_padding',
            array(
                'label'      => esc_html__( 'Padding', 'woo-buy-now-discount-badge' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .wbndb-discount-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'badge_margin',
            array(
                'label'      => esc_html__( 'Margin', 'woo-buy-now-discount-badge' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .wbndb-discount-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $badge_html = wbndb_get_discount_badge_html(
            array(
                'format'  => $settings['badge_format'],
                'prefix'  => $settings['prefix_text'],
                'classes' => 'wbndb-discount-badge',
            )
        );

        if ( '' === $badge_html ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="elementor-alert elementor-alert-info">' .
                    esc_html__( 'This badge will render here on the frontend once the product is on sale.', 'woo-buy-now-discount-badge' ) .
                    '</div>';
            }
            return;
        }

        // $badge_html is already escaped field-by-field inside
        // wbndb_get_discount_badge_html(); nothing further to sanitize here.
        echo '<div class="wbndb-elementor-discount-badge">' . $badge_html . '</div>';
    }
}
