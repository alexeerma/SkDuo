<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

/**
 * Single product page data. The add-to-cart form itself is still rendered by
 * WooCommerce in the view (needs the loop + global $product).
 */
class SingleProduct extends Composer
{
    protected static $views = [
        'single-product',
    ];

    public function with(): array
    {
        $product = function_exists('wc_get_product') ? wc_get_product(get_queried_object_id()) : null;

        if (! $product) {
            return ['productData' => null];
        }

        $pageOptions = function_exists('skduo_get_page_copy_options') ? skduo_get_page_copy_options('epood') : [];
        $productId = $product->get_id();
        $shopPageId = (int) wc_get_page_id('shop');
        $terms = get_the_terms($productId, 'product_cat');
        $primaryCategory = (! is_wp_error($terms) && ! empty($terms)) ? $terms[0] : null;
        $isExternal = $product->is_type('external');

        $imageIds = array_values(array_unique(array_filter(array_merge([(int) $product->get_image_id()], $product->get_gallery_image_ids()))));
        $images = [];
        foreach ($imageIds as $imageId) {
            $full = wp_get_attachment_image_url($imageId, 'large');
            if (! $full) {
                continue;
            }
            $images[] = [
                'full' => $full,
                'thumb' => wp_get_attachment_image_url($imageId, 'thumbnail') ?: $full,
                'alt' => get_post_meta($imageId, '_wp_attachment_image_alt', true) ?: $product->get_name(),
            ];
        }

        $attributesHtml = '';
        $hasVisibleAttributes = array_filter($product->get_attributes(), fn ($attribute) => $attribute->get_visible());
        if ($hasVisibleAttributes || $product->has_weight() || $product->has_dimensions()) {
            ob_start();
            wc_display_product_attributes($product);
            $attributesHtml = trim((string) ob_get_clean());
        }

        $related = [];
        if (function_exists('wc_get_related_products') && function_exists('skduo_product_card_data')) {
            foreach (wc_get_related_products($productId, 4) as $relatedId) {
                $relatedProduct = wc_get_product($relatedId);
                if ($relatedProduct) {
                    $related[] = skduo_product_card_data($relatedProduct, $pageOptions);
                }
            }
        }

        return [
            'pageOptions' => $pageOptions,
            'productData' => (object) [
                'name' => $product->get_name(),
                'shopUrl' => $shopPageId > 0 ? get_permalink($shopPageId) : get_post_type_archive_link('product'),
                'shopTitle' => $shopPageId > 0 ? get_the_title($shopPageId) : __('E-pood', 'skduo'),
                'category' => $primaryCategory ? ['name' => $primaryCategory->name, 'url' => get_term_link($primaryCategory)] : null,
                'images' => $images,
                'onSale' => $product->is_on_sale(),
                'priceHtml' => $product->get_price_html(),
                'shortDescription' => $product->get_short_description(),
                'description' => $product->get_description(),
                'isExternal' => $isExternal,
                'externalUrl' => $isExternal ? $product->get_product_url() : '',
                'externalText' => ($isExternal ? $product->get_button_text() : '') ?: ($pageOptions['external_button_fallback'] ?? __('Osta partneri e-poest', 'skduo')),
                'sku' => $product->get_sku(),
                'categoryListHtml' => $primaryCategory ? wc_get_product_category_list($productId) : '',
                'attributesHtml' => $attributesHtml,
                'infoItems' => function_exists('skduo_parse_product_info_items') ? skduo_parse_product_info_items((string) ($pageOptions['single_info_items'] ?? '')) : [],
                'related' => $related,
            ],
        ];
    }
}
