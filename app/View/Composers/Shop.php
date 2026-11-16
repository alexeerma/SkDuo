<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

/**
 * Product archive (shop + product category). Reads WooCommerce's main query,
 * so catalog visibility, ordering and per-page settings stay Woo's.
 */
class Shop extends Composer
{
    protected static $views = [
        'archive-product',
    ];

    public function with(): array
    {
        global $wp_query;

        $pageOptions = function_exists('skduo_get_page_copy_options') ? skduo_get_page_copy_options('epood') : [];
        $shopPageId = function_exists('wc_get_page_id') ? (int) wc_get_page_id('shop') : 0;
        $shopUrl = $shopPageId > 0 ? get_permalink($shopPageId) : get_post_type_archive_link('product');
        $queried = get_queried_object();
        $isCategory = function_exists('is_product_category') && is_product_category();
        $currentCategoryId = $isCategory && isset($queried->term_id) ? (int) $queried->term_id : 0;

        $title = $isCategory && isset($queried->name)
            ? $queried->name
            : ($shopPageId > 0 ? get_the_title($shopPageId) : __('Fännipood', 'skduo'));

        $description = $isCategory && ! empty($queried->description)
            ? wp_strip_all_tags($queried->description)
            : ($pageOptions['hero_text'] ?? '');

        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        $categories = [];
        if (! is_wp_error($terms)) {
            foreach ($terms as $term) {
                $categories[] = [
                    'name' => $term->name,
                    'url' => get_term_link($term),
                    'active' => $currentCategoryId === (int) $term->term_id,
                ];
            }
        }

        $cards = [];
        if (function_exists('wc_get_product') && function_exists('skduo_product_card_data')) {
            foreach ($wp_query->posts as $post) {
                $product = wc_get_product($post);
                if ($product) {
                    $cards[] = skduo_product_card_data($product, $pageOptions);
                }
            }
        }

        $pagination = paginate_links([
            'total' => (int) $wp_query->max_num_pages,
            'current' => max(1, (int) get_query_var('paged')),
            'prev_text' => __('← Eelmised', 'skduo'),
            'next_text' => __('Järgmised →', 'skduo'),
        ]);

        return [
            'pageOptions' => $pageOptions,
            'shopUrl' => $shopUrl,
            'archiveTitle' => $title,
            'archiveDescription' => $description,
            'isAllActive' => $currentCategoryId === 0,
            'categories' => $categories,
            'productCount' => (int) $wp_query->found_posts,
            'productCards' => $cards,
            'pagination' => $pagination ?: '',
        ];
    }
}
