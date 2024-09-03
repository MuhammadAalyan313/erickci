<?php defined('BASEPATH') or exit('No direct script access allowed'); 
if($product->acc_type == 1):
    $acc_ver  = '';
elseif($product->acc_type == 2):
    $acc_ver = '<i class="icon-verified " style= "color:orange"></i>';
else:
    $acc_ver='<i class="icon-verified " style= "color:#09b1ba"></i>';
endif;
?>
<div class="product-item">
    <div class="row-custom<?php echo (!empty($product->image_second)) ? ' product-multiple-image' : ''; ?>">
        <a class="item-wishlist-button item-wishlist-enable <?php echo (is_product_in_wishlist($product) == 1) ? 'item-wishlist' : ''; ?>" data-product-id="<?php echo $product->id; ?>"></a>
        <div class="img-product-container">
            <?php if (!empty($is_slider)): ?>
                <a href="<?php echo generate_product_url($product); ?>">
                    <img src="<?php echo base_url() . IMG_BG_PRODUCT_SMALL; ?>" data-lazy="<?php echo get_product_item_image($product); ?>" alt="<?php echo get_product_title($product); ?>" class="img-fluid img-product">
                    <?php if (!empty($product->image_second)): ?>
                        <img src="<?php echo base_url() . IMG_BG_PRODUCT_SMALL; ?>" data-lazy="<?php echo get_product_item_image($product, true); ?>" alt="<?php echo get_product_title($product); ?>" class="img-fluid img-product img-second">
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <a href="<?php echo generate_product_url($product); ?>" class="">
                    <!-- <div id="watermark"> -->
                        <img src="<?php echo base_url() . IMG_BG_PRODUCT_SMALL; ?>" data-src="<?php echo get_product_item_image($product); ?>" alt="<?php echo get_product_title($product); ?>" class="lazyload img-fluid img-product">
                    
                    <?php if (!empty($product->image_second)): ?>
                        <!-- <div id="watermark"> -->
                            <img src="<?php echo base_url() . IMG_BG_PRODUCT_SMALL; ?>" data-src="<?php echo get_product_item_image($product, true); ?>" alt="<?php echo get_product_title($product); ?>" class="lazyload img-fluid img-product img-second">
                       
                    <?php endif; ?>
                    <!-- </div> -->
                </a>
            <?php endif; ?>
            <div class="product-item-options">
                <a href="javascript:void(0)" class="item-option btn-add-remove-wishlist" data-toggle="tooltip" data-placement="left" data-product-id="<?php echo $product->id; ?>" data-type="list" title="<?php echo trans("wishlist"); ?>">
                    <?php if (is_product_in_wishlist($product) == 1): ?>
                        <i class="icon-heart"></i>
                    <?php else: ?>
                        <i class="icon-heart-o"></i>
                    <?php endif; ?>
                </a>
                <?php if (($product->listing_type == "sell_on_site" || $product->listing_type == "bidding") && $product->is_free_product != 1):
                    if (!empty($product->has_variation) || $product->listing_type == "bidding"):?>
                        <a href="<?= generate_product_url($product); ?>" class="item-option" data-toggle="tooltip" data-placement="left" data-product-id="<?php echo $product->id; ?>" data-reload="0" title="<?php echo trans("view_options"); ?>">
                            <i class="icon-cart"></i>
                        </a>
                    <?php else:
                        $item_unique_id = uniqid();
                        if ($product->stock > 0):?>
                            <a href="javascript:void(0)" id="btn_add_cart_<?= $item_unique_id; ?>" class="item-option btn-item-add-to-cart" data-id="<?= $item_unique_id; ?>" data-toggle="tooltip" data-placement="left" data-product-id="<?php echo $product->id; ?>" data-reload="0" title="<?php echo trans("add_to_cart"); ?>">
                                <i class="icon-cart"></i>
                            </a>
                        <?php endif;
                    endif;
                endif; ?>
            </div>
            <?php if (!empty($product->discount_rate) && !empty($discount_label)): ?>
                <span class="badge badge-discount">-<?= $product->discount_rate; ?>%</span>
            <?php endif; ?>
        </div>
        <?php if ($product->is_promoted && $this->general_settings->promoted_products == 1 && isset($promoted_badge) && $promoted_badge == true): ?>
            <span class="badge badge-dark badge-promoted"><?php echo trans("featured"); ?></span>
        <?php endif; ?>
    </div>
    <div class="row-custom item-details">
        
        <h3 class="product-title">
            <?php if(isset($user)):
                $logo = get_user_avatar(get_user($product->user_id));
                $shop = get_shop_name($user);
            else:
                $logo = get_user_avatar(get_user($product->user_id));
                $shop = $product->shop_name;
            endif;
                ?>
            <a href="<?php echo generate_product_url($product); ?>"><?= get_product_title($product); ?></a>
        </h3>
        <p class="product-user text-truncate">
            <div class='image-box'><img src="<?php echo  $logo; ?>" alt="<?php echo $shop; ?>" class="img-profile" id="qrpopup-btn2" data-toggle="modal" data-target="#qrPopup" data-slug= "<?php echo base_url().'/profile/' . $product->user_slug ?>"></div>
            <a href="<?php echo generate_profile_url($product->user_slug); ?>">
                <?php echo get_shop_name_product($product); ?><?= $acc_ver; ?>
            </a>
        </p>
        <div class="product-item-rating">
            <?php if ($this->general_settings->reviews == 1) {
                $this->load->view('partials/_review_stars', ['review' => $product->rating]);
            } ?>
            
            <span class="item-wishlist items-reviews"><i class="icon-comment"></i><?php echo get_product_comment_count($product->id); ?></span>
            <span class="item-wishlist items-reviews"><i class="icon-heart-o"></i><?php echo $product->wishlist_count; ?></span> 
            <!-- <span class="item-wishlist items-reviews"><i class="icon-eye"></i><?php echo $product->pageviews; ?></span> -->
        </div>
        <div class="item-meta">
        <?php
            $negotiableTag = '';
            if($product->is_negotiable == 1){
            $negotiableTag = "<span class=\" negotiable-tag \">N</span>";
            }
        
            ?>
            <?php $this->load->view('product/_price_product_item', ['product' => $product, 'negotiableTag' => $negotiableTag]); ?>
        </div>
    </div>
</div>