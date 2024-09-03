<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="<?= $this->selected_lang->short_form ?>">

<head>
  <meta charset="utf-8">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= xss_clean($title); ?> - <?= xss_clean($this->settings->site_title); ?></title>
  <meta name="description" content="<?= xss_clean($description); ?>" />
  <meta name="keywords" content="<?= xss_clean($keywords); ?>" />
  <meta name="author" content="<?= xss_clean($this->general_settings->application_name); ?>" />
  <link rel="shortcut icon" type="image/png" href="<?= get_favicon($this->general_settings); ?>" />
  <meta property="og:locale" content="en-US" />
  <meta property="og:site_name" content="<?= xss_clean($this->general_settings->application_name); ?>" />
  <?php if (isset($show_og_tags)) : ?>
    <meta property="og:type" content="<?= !empty($og_type) ? $og_type : 'website'; ?>" />
    <meta property="og:title" content="<?= !empty($og_title) ? $og_title : 'index'; ?>" />
    <meta property="og:description" content="<?= $og_description; ?>" />
    <meta property="og:url" content="<?= $og_url; ?>" />
    <meta property="og:image" content="<?= $og_image; ?>" />
    <meta property="og:image:width" content="<?= !empty($og_width) ? $og_width : 250; ?>" />
    <meta property="og:image:height" content="<?= !empty($og_height) ? $og_height : 250; ?>" />
    <meta property="article:author" content="<?= !empty($og_author) ? $og_author : ''; ?>" />
    <meta property="fb:app_id" content="<?= $this->general_settings->facebook_app_id; ?>" />
    <?php if (!empty($og_tags)) : foreach ($og_tags as $tag) : ?>
        <meta property="article:tag" content="<?= $tag->tag; ?>" />
    <?php endforeach;
    endif; ?>
    <meta property="article:published_time" content="<?= !empty($og_published_time) ? $og_published_time : ''; ?>" />
    <meta property="article:modified_time" content="<?= !empty($og_modified_time) ? $og_modified_time : ''; ?>" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@<?= xss_clean($this->general_settings->application_name); ?>" />
    <meta name="twitter:creator" content="@<?= xss_clean($og_creator); ?>" />
    <meta name="twitter:title" content="<?= xss_clean($og_title); ?>" />
    <meta name="twitter:description" content="<?= xss_clean($og_description); ?>" />
    <meta name="twitter:image" content="<?= $og_image; ?>" />
  <?php else : ?>
    <meta property="og:image" content="<?= get_logo($this->general_settings); ?>" />
    <meta property="og:image:width" content="160" />
    <meta property="og:image:height" content="60" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= xss_clean($title); ?> - <?= xss_clean($this->settings->site_title); ?>" />
    <meta property="og:description" content="<?= xss_clean($description); ?>" />
    <meta property="og:url" content="<?= base_url(); ?>" />
    <meta property="fb:app_id" content="<?= $this->general_settings->facebook_app_id; ?>" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@<?= xss_clean($this->general_settings->application_name); ?>" />
    <meta name="twitter:title" content="<?= xss_clean($title); ?> - <?= xss_clean($this->settings->site_title); ?>" />
    <meta name="twitter:description" content="<?= xss_clean($description); ?>" />
  <?php endif; ?>
  <?php if ($this->general_settings->pwa_status == 1) : ?>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="<?= xss_clean($this->general_settings->application_name); ?>">
    <meta name="msapplication-TileImage" content="<?= base_url(); ?>assets/img/pwa/144x144.png">
    <meta name="msapplication-TileColor" content="#2F3BA2">
    <link rel="manifest" href="<?= base_url(); ?>manifest.json">
    <link rel="apple-touch-icon" href="<?= base_url(); ?>assets/img/pwa/144x144.png">
  <?php endif; ?>
  <link rel="canonical" href="<?= current_full_url(); ?>" />
  <?php if ($this->general_settings->multilingual_system == 1) :
    foreach ($this->languages as $language) : ?>
      <link rel="alternate" href="<?= convert_url_by_language($language); ?>" hreflang="<?= $language->language_code ?>" />
  <?php endforeach;
  endif; ?>
  <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/font-icons/css/mds-icons.min.css" />
  <?= !empty($this->fonts->site_font_url) ? $this->fonts->site_font_url : ''; ?>
  <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/style-2.1.min.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/custom_style.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/plugins-2.1.css" />
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/messenger.css" />
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <?php $this->load->view("partials/_css_js_header"); ?>
  <?php if ($this->rtl == true) : ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/rtl-2.1.min.css">
  <?php endif; ?>
  <?= $this->general_settings->custom_css_codes; ?>
  <?= $this->general_settings->google_adsense_code; ?>

  <style>
    /* The Modal (background) */
    .qrpopup {
      display: none;
      /* Hidden by default */
      position: fixed;
      /* Stay in place */
      z-index: 999;
      /* Sit on top */
      /* Location of the box */
      left: 0;
      top: 0;
      width: 100%;
      /* Full width */
      height: 100%;
      /* Full height */
      overflow: auto;
      /* Enable scroll if needed */
      background-color: rgb(0, 0, 0);
      /* Fallback color */
      background-color: rgba(0, 0, 0, 0.5);
      /* Black w/ opacity */
      align-items: center;
      align-content: center;
      justify-content: center;
    }

    /* Modal Content */
    /*.qrpopup-content {
   top: 50%; 
    position: absolute;
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    /* width: 50%; 
    display: flex;
    justify-content: center;
}*/
    #qrcode canvas {
      width: 287px;
      height: 287px;
      border: solid 20px #fff;
    }

    #qrcode canvas img {
      border-radius: 50%;
    }

    /* The Close Button */
    .close {
      color: #aaaaaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: #000;
      text-decoration: none;
      cursor: pointer;
    }
  </style>




</head>

<body>
  <!-- Age conformation module -->
  <div class="fullpage_box" id="fullpage-box">
    <div class="conformation_div">
      <div>
        <div class="logo">
          <img src="<?= base_url() ?>assets/img/AVlogo.png" alt="">
        </div>
        <div class="text_div">
          <p>Welcome to The Plug List <br> Are you 21 or older?</p>

          <button id="over-age" class="age_btn">YES</button>
          <button class="left age_btn" id="link-btn">NO</button></a>
        </div>
      </div>
    </div>
  </div>
  <header id="header">
    <?php //$this->load->view("partials/_top_bar"); ?>
    <div class="main-menu">
      <div class="container-fluid">
        <div class="row">
          <div class="nav-top">
            <div class="container">
              <div class="row align-items-center">
                <div class="col-lg-7 col-md-6 nav-top-left">
                  <div class="row-align-items-center">
                    <div class="logo">
                      <a href="<?php echo lang_base_url(); ?>"><img src="<?php echo get_logo($this->general_settings); ?>" alt="logo"></a>
                    </div>
                    <div class="top-search-bar<?= $this->general_settings->multi_vendor_system != 1 ? ' top-search-bar-single-vendor' : ''; ?>">
                      <?php echo form_open(generate_url('search'), ['id' => 'form_validate_search', 'class' => 'form_search_main', 'method' => 'get']); ?>
                      <div class="left">
                        <div class="dropdown search-select">
                          <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><?= !empty($input_search_category) ? category_name($input_search_category) : trans("all_categories"); ?></button>
                          <i class="icon-arrow-down search-select-caret"></i>
                          <input type="hidden" name="search_category_input" id="input_search_category" value="<?= !empty($input_search_category) ? $input_search_category->id : 'all'; ?>">
                          <div class="dropdown-menu search-categories">
                            <a class="dropdown-item" data-value="all" href="javascript:void(0)"><?= trans("all_categories"); ?></a>
                            <?php if (!empty($this->parent_categories)) :
                              foreach ($this->parent_categories as $search_cat) : ?>
                                <a class="dropdown-item" data-value="<?= $search_cat->id; ?>" href="javascript:void(0)"><?= html_escape($search_cat->name); ?></a>
                            <?php endforeach;
                            endif; ?>
                            <a class="dropdown-item" data-value="negotiable" href="javascript:void(0)"><?= trans("is_negotiable") ?></a>
                          </div>
                        </div>
                      </div>
                      <div class="right">
                        <input type="text" name="search" maxlength="300" pattern=".*\S+.*" id="input_search" class="form-control input-search" value="<?php echo (!empty($filter_search)) ? $filter_search : ''; ?>" placeholder="<?php echo trans("search_exp"); ?>" required autocomplete="off">
                        <button class="btn btn-default btn-search"><i class="icon-search"></i></button>
                        <div id="response_search_results" class="search-results-ajax"></div>
                      </div>
                      <?php echo form_close(); ?>
                      <div class="right">
                        <div class="input-group input-group-location">
                          <i class="icon-map-marker" style="top:8px"></i>
                          <input type="text" id="input_location" class="form-control form-input" value="<?= $this->default_location_input; ?>" style="background-color: #f6f6f6; border:0; border-left:1px; height:auto" placeholder="<?php echo trans("enter_location") ?>" autocomplete="off">
                          <a href="javascript:void(0)" class="btn-reset-location-input<?= (empty($this->default_location->country_id)) ? ' hidden' : ''; ?>"><i class="icon-close"></i></a>
                        </div>
                        <div class="search-results-ajax" id="search-results-ajax">
                          <div class="search-results-location">
                            <div id="response_search_location"></div>
                          </div>
                        </div>
                        <div id="location_id_inputs">
                          <input type="hidden" name="country" value="<?= $this->default_location->country_id; ?>" class="input-location-filter">
                          <input type="hidden" name="state" value="<?= $this->default_location->state_id; ?>" class="input-location-filter">
                          <input type="hidden" name="city" value="<?= $this->default_location->city_id; ?>" class="input-location-filter">
                        </div>
                        
                      </div>

                      <!-- <div class="form-group">
                        <button type="button" id="btn_submit_location" class="btn btn-md btn-custom btn-block"><?php echo trans("update_location"); ?></button>
                      </div> -->
                      
                    </div>
                    <?php
                      if(isset($_SESSION['mds_default_location'])):
                    ?>
                    <div class="text-right" style="font-size: 12px; cursor:pointer;position:absolute;right:7px;bottom:-13px;
                    "><a href="<?php echo base_url()."reset"?>">reset location</a></div>
                    <?php
                      endif;
                    ?>
                  </div>
                </div>
                <div class="col-lg-5 col-md-6 nav-top-right">
                  <ul class="nav align-items-center">
                    <?php if ($this->auth_check) :
                      $notifications = get_notification();
                    ?>
                      <li class="nav-item dropdown profile-dropdown p-r-0">
                        <a style="padding-bottom:5px;height: 66px; text-align: center; width: 53px;" class="nav-link dropdown-toggle a-profile" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false" id="noti-link">
                          <i style="height: 28px;padding-top: 8px;margin:0" class="fa fa-bell" ></i>
                          <span><?php echo "Alerts"; ?></span>
                          <?php 
                          if ($notifications['notification_count'] > 0) : ?>
                            <span class="message-notification"  id="message-notify" style="top:8px; left:30px"><?= $notifications['notification_count']; ?></span>
                          <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu notification-menu" id="notification-list">
                          <?php

                          if ($notifications['notification_count'] > 0) :
                            foreach ($notifications['notifications'] as $notification) {
                              if ($notification->user->role_id == 2 || $notification->user->role_id == 1) {
                                $name = get_shop_name($notification->user);
                              } else {
                                $name =  $notification->user->first_name;
                              }
                              if ($notification->notification_type == 3) {
                          ?>
                                <li style="display:flex" id="notification-<?= $notification->id ?>">
                                  <a href="<?php echo base_URL() . $notification->product->slug; ?>" style="width:80%; display:flex">


                                    <?php  //echo "<i class='icon-comment'></i>`
                                    ?>
                                    <div style="display: flex; align-items: center;"><span style="display:block; width:30px; height:30px; overflow:hidden; border-radius:50%"><img src="<?= base_URL() . '/' . $notification->user->avatar ?>" alt="" style="width:100%; height:100%"></span></div>
                                    <div style="padding-left:5px"><span><?= "`" . $name . "`  reviewed your product <br>" ?>
                                        <span style="display:block; text-align:right; color:#a19c9c;"><?= time_ago($notification->created_at) ?></span>
                                      </span></div>
                                  </a>
                                  <span style="display: flex; align-items: center; justify-content:center; width:20%; text-align: center;"><i class="fa fa-close" style="font-size:16px" onclick="deleteNotification(event, '<?= $notification->id ?>')"></i></span>
                                </li>
                              <?php
                              }
                              if ($notification->notification_type == 2) {
                              ?>
                                <li style="display:flex" id="notification-<?= $notification->id ?>">
                                  <a href="<?php echo base_URL() . $notification->product->slug; ?>" style="width:80%; display:flex">
                                    <?php  //echo "<i class='icon-comment'></i>`
                                    ?>
                                    <div style="display: flex; align-items: center;"><span style="display:block; width:30px; height:30px; overflow:hidden; border-radius:50%"><img src="<?= base_URL() . '/' . $notification->user->avatar ?>" alt="" style="width:100%; height:100%"></span></div>
                                    <div style="padding-left:5px"><span><?= "`" . $name . "`  post a comment on your product <br> " ?>
                                        <span style="display:block; text-align:right; color:#a19c9c;"><?= time_ago($notification->created_at) ?></span>
                                      </span></div>
                                  </a>
                                  <span style="display: flex; align-items: center; justify-content:center; width:20%; text-align: center;"><i class="fa fa-close" style="font-size:16px" onclick="deleteNotification(event, '<?= $notification->id ?>')"></i></span>
                                </li>
                                <?php
                              }
                              if ($notification->notification_type == 4) {
                                if ($notification->deleted_by != $this->auth_user->id || $notification->deleted_by == NULL) {
                                 
                                  if ($notification->commit_parent == $this->auth_user->id) {
                                    // if($notification->auth_id != $this->auth_user->id){
                                ?>
                                    <li style="display:flex" id="notification-<?= $notification->id ?>">
                                      <a href="<?php echo base_URL() . $notification->product->slug; ?>" style="width:80%; display:flex">


                                        <?php  //echo "<i class='icon-comment'></i>`
                                        ?>
                                        <div style="display: flex; align-items: center;"><span style="display:block; width:30px; height:30px; overflow:hidden; border-radius:50%"><img src="<?= base_URL() . '/' . $notification->user->avatar ?>" alt="" style="width:100%; height:100%"></span></div>
                                        <div style="padding-left:5px"><span><?= "`" . $name . "`  replied on your comment<br>" ?>
                                            <span style="display:block; text-align:right; color:#a19c9c;"><?= time_ago($notification->created_at) ?></span>
                                          </span></div>


                                      </a>
                                      <span style="display: flex; align-items: center; justify-content:center; width:20%; text-align: center;"><i class="fa fa-close" style="font-size:16px" onclick="deleteNotification(event,'<?= $notification->id ?>')"></i></span>
                                    </li>
                                  <?php
                                    // }
                                  } else {
                                  ?>
                                    <li style="display:flex" id="notification-<?= $notification->id ?>">
                                      <a href="<?php echo base_URL() . $notification->product->slug; ?>" style="width:80%; display:flex">


                                        <?php  //echo "<i class='icon-comment'></i>`
                                        ?>
                                        <div style="display: flex; align-items: center;"><span style="display:block; width:30px; height:30px; overflow:hidden; border-radius:50%"><img src="<?= base_URL() . '/' . $notification->user->avatar ?>" alt="" style="width:100%; height:100%"></span></div>
                                        <div style="padding-left:5px"><span><?= "`" . $name . "`  replied to a comment on your product<br>" ?>
                                            <span style="display:block; text-align:right; color:#a19c9c;"><?= time_ago($notification->created_at) ?></span>
                                          </span></div>


                                      </a>
                                      <span style="display: flex; align-items: center; justify-content:center; width:20%; text-align: center;"><i class="fa fa-close" style="font-size:16px" onclick="deleteNotification(event,'<?= $notification->id ?>')"></i></span>
                                    </li>
                                <?php
                                  }
                                }
                                ?>

                            <?php
                              }
                            }
                            ?>

                          <?php else : ?>
                            <li style="padding:4px 15px">
                              No Notification Right Now
                            </li>
                          <?php endif; ?>

                        </ul>
                      </li>
                    <?php endif; ?>

                    <?php if ($this->is_sale_active) : ?>
                      <li class="nav-item nav-item-cart li-main-nav-right">
                        <a href="<?php echo generate_url("cart"); ?>">
                          <i class="icon-cart"></i>
                          <span class="label-nav-icon"><?php echo trans("cart"); ?></span>
                          <?php $cart_product_count = get_cart_product_count(); ?>
                          <span class="notification span_cart_product_count <?= $cart_product_count <= 0 ? 'visibility-hidden' : ''; ?>"><?php echo $cart_product_count; ?></span>
                        </a>
                      </li>
                    <?php endif; ?>
                    <?php //if ($this->auth_check):
                    ?>
                    <!-- <li  class="nav-item nav-item-cart li-main-nav-right">
<a href="<?php echo generate_url("messages"); ?>">
<i class="icon-mail"></i>
<?php echo trans("messages"); ?>&nbsp;
<span id='me-load-3'>
  <span id='message-count-3'>
<?php if ($unread_message_count > 0) : ?>
<span class="message-notification" ><?= $unread_message_count; ?></span>

<?php endif; ?>
  </span>
</span>
</a> 
</li>-->
                    <!-- <li  class="nav-item nav-item-cart li-main-nav-right">
<a style="padding-bottom:4px;height: 66px; text-align: center; width: 53px;" href="<?php // echo base_URL().'map'; 
                                                                                    ?>">
<span style="display: flex; height: 28px;text-align: center; justify-content: center; align-items: end;">
<img class="map-icon" src="<?php //echo base_url().'/assets/img/map.png'
                            ?>"  style="height: 24px;" alt="" srcset="">
<img class ="map-icon-hover"src="<?php //echo base_url().'/assets/img/mapColored.png'
                                  ?>"  style="height: 24px;" alt="" srcset=""></span>
<span><?php //echo "Map"; 
      ?></span>
</a> 
</li> -->
                    <li class="nav-item nav-item-cart li-main-nav-right">
                      <a style="padding-bottom:4px" href="<?php echo base_URL() . 'shops'; ?>">
                        <i style="height: 28px;padding-top: 8px; margin:0" class="fa fa-store"></i>
                        <span><?php echo trans("shop"); ?></span>
                      </a>
                    </li>
                    <?php //endif; 
                    ?>
                    <?php if ($this->auth_check) : ?>
                      <!-- <li class="nav-item li-main-nav-right">
<a href="<?php echo generate_url("wishlist") . "/" . $this->auth_user->slug; ?>">
<i class="icon-heart-o"></i>
<span class="label-nav-icon"><?php echo trans("wishlist"); ?></span>
</a>
</li> -->
                      <li class="nav-item nav-item-cart li-main-nav-right">
                        <a style="padding-bottom:4px" href="<?php echo generate_url("wishlist") . "/" . $this->auth_user->slug; ?>">
                          <i style="height: 28px;padding: 5px;margin:0" class="icon-heart"></i>
                          <span><?php echo trans("wishlist"); ?></span>
                        </a>
                      </li>
                    <?php else : ?>



                    <?php endif; ?>
                    <?php if ($this->auth_check) : ?>
                      <li class="nav-item dropdown profile-dropdown p-r-0">
                        <a class="nav-link dropdown-toggle a-profile" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                          <img src="<?php echo get_user_avatar($this->auth_user); ?>" alt="<?php echo get_shop_name($this->auth_user); ?>">
                          <?php //echo character_limiter(get_shop_name($this->auth_user), 15, '..'); 
                          ?>
                          <!-- <i class="icon-arrow-down"></i> -->
                          <!-- <span id='me-load-4'>
  <span id='message-count-4'>
<?php if ($unread_message_count > 0) : ?>
<span class="message-notification"><?= $unread_message_count; ?></span>
<?php endif; ?>
</span>
</span> -->
                        </a>
                        <ul class="dropdown-menu">
                          <?php if (has_permission('admin_panel')) : ?>
                            <li>
                              <a href="<?php echo admin_url(); ?>">
                                <i class="icon-admin"></i>
                                <?php echo trans("admin_panel"); ?>
                              </a>
                            </li>
                          <?php endif; ?>
                          <?php if (is_vendor()) : ?>
                            <li>
                              <a href="<?= dashboard_url(); ?>">
                                <i class="icon-dashboard"></i>
                                <?php echo trans("dashboard"); ?>
                              </a>
                            </li>
                          <?php endif; ?>
                          <li>
                            <a href="<?php echo generate_profile_url($this->auth_user->slug); ?>">
                              <i class="icon-user"></i>
                              <?php echo trans("profile"); ?>
                            </a>
                          </li>
                          <?php if ($this->is_sale_active) : ?>
                            <li>
                              <a href="<?php echo generate_url("orders"); ?>">
                                <i class="icon-shopping-basket"></i>
                                <?php echo trans("orders"); ?>
                              </a>
                            </li>
                            <?php if (is_bidding_system_active()) : ?>
                              <li>
                                <a href="<?php echo generate_url("quote_requests"); ?>">
                                  <i class="icon-price-tag-o"></i>
                                  <?php echo trans("quote_requests"); ?>
                                </a>
                              </li>
                            <?php endif; ?>
                            <?php if ($this->general_settings->digital_products_system == 1) : ?>
                              <li>
                                <a href="<?php echo generate_url("downloads"); ?>">
                                  <i class="icon-download"></i>
                                  <?php echo trans("downloads"); ?>
                                </a>
                              </li>
                            <?php endif; ?>
                            <li>
                              <a href="<?php echo generate_url("refund_requests"); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="mds-svg-icon">
                                  <path d="M0 3a2 2 0 0 1 2-2h13.5a.5.5 0 0 1 0 1H15v2a1 1 0 0 1 1 1v8.5a1.5 1.5 0 0 1-1.5 1.5h-12A2.5 2.5 0 0 1 0 12.5V3zm1 1.732V12.5A1.5 1.5 0 0 0 2.5 14h12a.5.5 0 0 0 .5-.5V5H2a1.99 1.99 0 0 1-1-.268zM1 3a1 1 0 0 0 1 1h12V2H2a1 1 0 0 0-1 1z" />
                                </svg>
                                <?php echo trans("refund"); ?>
                              </a>
                            </li>
                          <?php endif; ?>
                          <li>
                            <a class="link-hover" onclick="createMessageWidget()" style="cursor:pointer">
                              <i class="icon-mail"></i>
                              <?php echo trans("messages"); ?>&nbsp;
                              <span id='me-load-2'>
                                <span id='message-count-2'>

                                  <span class="span-message-count displayNone" id="messages_count-2"></span>

                                </span>
                              </span>
                            </a>
                          </li>
                          <li>
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#locationModal" class="nav-link btn-modal-location">
                              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="#888888" class="mds-svg-icon">
                                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                              </svg>
                              <?= !empty($this->default_location_input) ? $this->default_location_input : trans("location"); ?>
                            </a>
                          </li>
                          <li>
                            <a href="<?php echo generate_url("settings", "update_profile"); ?>">
                              <i class="icon-settings"></i>
                              <?php echo trans("settings"); ?>
                            </a>
                          </li>
                          <li>
                            <a href="<?php echo base_url(); ?>logout" class="logout">
                              <i class="icon-logout"></i>
                              <?php echo trans("logout"); ?>
                            </a>
                          </li>
                        </ul>
                      </li>
                    <?php else : ?>
                      <li class="nav-item">

                        <a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal" class="nav-link"><i class="icon-user"></i><?php echo trans("login"); ?></a>
                        <!-- <span class="auth-sep">/</span>
<a href="<?php //echo generate_url("register"); 
          ?>" class="nav-link"><?php //echo trans("register"); 
                                ?></a> -->
                      </li>
                      <li class="nav-item">
                        <!-- <a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal" class="nav-link"><?php // echo trans("login"); 
                                                                                                                          ?></a> -->
                        <!-- <span class="auth-sep">/</span>-->
                        <a href="<?php echo generate_url("register"); ?>" class="nav-link"><i class="icon-user-plus"></i><?php echo trans("register"); ?></a>
                      </li>
                    <?php endif; ?>
                    <?php if ($this->auth_check) : ?>
                      <?php if (is_multi_vendor_active()) : ?>
                        <li class="nav-item m-r-0"><a href="<?php echo generate_dash_url("add_product"); ?>" class="btn btn-md btn-custom btn-sell-now m-r-0"><?= trans("add_listing"); ?></a></li>
                      <?php endif; ?>
                    <?php else : ?>
                      <?php if (is_multi_vendor_active()) : ?>
                        <li class="nav-item m-r-0"><a href="javascript:void(0)" class="btn btn-md btn-custom btn-sell-now m-r-0" data-toggle="modal" data-target="#loginModal"><?= trans("add_listing"); ?></a></li>
                      <?php endif; ?>
                    <?php endif; ?>

                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="nav-main">
            <?php $this->load->view("partials/_nav_main"); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="mobile-nav-container">
      <div class="nav-mobile-header">
        <div class="container-fluid">
          <div class="row">
            <div class="nav-mobile-header-container">
              <div class="menu-icon">
                <a href="javascript:void(0)" class="btn-open-mobile-nav"><i class="icon-menu"></i></a>
              </div>
              <div class="mobile-logo">
                <a href="<?php echo lang_base_url(); ?>"><img src="<?php echo get_logo($this->general_settings); ?>" alt="logo" class="logo"></a>
              </div>
              <div class="mobile-search">
                <a class="search-icon"><i class="icon-search"></i></a>
              </div>
              <div class="mobile-cart<?= !$this->is_sale_active ? ' hidden' : ''; ?>">
                <a href="<?php echo generate_url("cart"); ?>"><i class="icon-cart"></i>
                  <?php $cart_product_count = get_cart_product_count(); ?>
                  <span class="notification span_cart_product_count"><?php echo $cart_product_count; ?></span>
                </a>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="top-search-bar mobile-search-form <?= $this->general_settings->multi_vendor_system != 1 ? ' top-search-bar-single-vendor' : ''; ?>">
              <?php echo form_open(generate_url('search'), ['id' => 'form_validate_search_mobile', 'method' => 'get']); ?>
              <div class="left">
                <div class="dropdown search-select">
                  <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><?= !empty($input_search_category) ? category_name($input_search_category) : trans("all_categories"); ?></button>
                  <i class="icon-arrow-down search-select-caret"></i>
                  <input type="hidden" name="search_category_input" id="input_search_category_mobile" value="<?= !empty($input_search_category) ? $input_search_category->id : 'all'; ?>">
                  <div class="dropdown-menu search-categories">
                    <a class="dropdown-item" data-value="all" href="javascript:void(0)"><?= trans("all_categories"); ?></a>
                    <?php if (!empty($this->parent_categories)) :
                      foreach ($this->parent_categories as $search_cat) : ?>
                        <a class="dropdown-item" data-value="<?= $search_cat->id; ?>" href="javascript:void(0)"><?= html_escape($search_cat->name); ?></a>
                    <?php endforeach;
                    endif; ?>
                  </div>
                </div>
              </div>
              <div class="right">
                <input type="text" id="input_search_mobile" name="search" maxlength="300" pattern=".*\S+.*" class="form-control input-search" value="<?php echo (!empty($filter_search)) ? $filter_search : ''; ?>" placeholder="<?php echo trans("search"); ?>" required autocomplete="off">
                <button class="btn btn-default btn-search"><i class="icon-search"></i></button>
                <div id="response_search_results_mobile" class="search-results-ajax"></div>
              </div>
              <?php echo form_close(); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
  <div id="overlay_bg" class="overlay-bg"></div>
  <!--include mobile menu-->
  <?php $this->load->view("partials/_nav_mobile"); ?>
  <input type="hidden" class="search_type_input" name="search_type" value="product">
  <?php if (!$this->auth_check) : ?>
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" role="dialog">
      <div class="modal-dialog modal-dialog-centered login-modal" role="document">
        <div class="modal-content">
          <div class="auth-box">
            <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
            <h4 class="title"><?php echo trans("login"); ?></h4>
            <!-- form start -->
            <form id="form_login" novalidate="novalidate">
              <div class="social-login">
                <?php $this->load->view("partials/_social_login", ["or_text" => trans('or_c')]); ?>
              </div>
              <!-- include message block -->
              <div id="result-login" class="font-size-13"></div>
              <div class="form-group">
                <input type="text" name="email" class="form-control auth-form-input" placeholder="<?php echo trans("phone_number") . ', ' . trans("username") . ' or ' . trans("email_address"); ?>" maxlength="255" required>
              </div>
              <div class="form-group">
                <input type="password" name="password" class="form-control auth-form-input" placeholder="<?php echo trans("password"); ?>" minlength="4" maxlength="255" required>
              </div>
              <div class="form-group text-right">
                <a href="<?php echo generate_url("forgot_password"); ?>" class="link-forgot-password"><?php echo trans("forgot_password"); ?></a>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-md btn-custom btn-block"><?php echo trans("login"); ?></button>
              </div>

              <p class="p-social-media m-0 m-t-5"><?php echo trans("dont_have_account"); ?>&nbsp;<a href="<?php echo generate_url("register"); ?>" class="link"><?php echo trans("register"); ?></a></p>
            </form>
            <!-- form end -->
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($this->general_settings->location_search_header == 1) : ?>
    <div class="modal fade" id="locationModal" role="dialog">
      <div class="modal-dialog modal-dialog-centered login-modal location-modal" role="document">
        <div class="modal-content">
          <div class="auth-box">
            <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
            <h4 class="title"><?php echo trans("select_location"); ?></h4>
            <p class="location-modal-description"><?= trans_with_field("location_explanation", $this->general_settings->application_name); ?></p>
            <div class="form-group m-b-20">
              <div class="input-group input-group-location">
                <i class="icon-map-marker"></i>
                <input type="text" id="input_location_modal" class="form-control form-input" value="<?= $this->default_location_input; ?>" placeholder="<?php echo trans("enter_location") ?>" autocomplete="off">
                <a href="javascript:void(0)" class="btn-reset-location-input btn-reset-location-input_modal <?= (empty($this->default_location->country_id)) ? ' hidden' : ''; ?>"><i class="icon-close"></i></a>
              </div>
              <div class="search-results-ajax">
                <div class="search-results-location">
                  <div id="response_search_location_modal"></div>
                </div>
              </div>
              <div id="location_id_inputs_modal">
                <input type="hidden" name="country" value="<?= $this->default_location->country_id; ?>" class="input-location-filter">
                <input type="hidden" name="state" value="<?= $this->default_location->state_id; ?>" class="input-location-filter">
                <input type="hidden" name="city" value="<?= $this->default_location->city_id; ?>" class="input-location-filter">
              </div>
            </div>
            <!-- modal location -->
            <div class="form-group">
             
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($this->general_settings->newsletter_status == 1 && $this->general_settings->newsletter_popup == 1) : ?>
    <div id="modal_newsletter" class="modal fade modal-center modal-newsletter" role="dialog">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal"><i class="icon-close" aria-hidden="true"></i></button>
            <h4 class="modal-title"><?= trans("join_newsletter"); ?></h4>
            <p class="modal-desc"><?= trans("newsletter_desc"); ?></p>
            <form id="form_newsletter_modal" class="form-newsletter" data-form-type="modal">
              <div class="form-group">
                <div class="modal-newsletter-inputs">
                  <input type="email" name="email" class="form-control form-input newsletter-input" placeholder="<?= trans('enter_email') ?>">
                  <button type="submit" id="btn_modal_newsletter" class="btn"><?= trans("subscribe"); ?></button>
                </div>
              </div>
              <input type="text" name="url">
              <div id="modal_newsletter_response" class="text-center modal-newsletter-response">
                <div class="form-group text-center m-b-0 text-close">
                  <button type="button" class="text-close" data-dismiss="modal"><?= trans("no_thanks"); ?></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <div id='messenger_container'></div>
  <?php if ($this->auth_check) : ?>
    <div class="widgetButton" onclick="createMessageWidget()"><i class='fa fa-comment' style='font-size:24px'></i>
      <span class="messages_count displayNone" id="messages_count">1</span>
    </div>
  <?php endif; ?>
  <div id="menu-overlay"></div>
  <?php $this->load->view("profile/qrpopup"); ?>
  <?php $this->load->view("partials/_message_image_modal"); ?>
  <div class="context-menu displayNone" id="context-menu">
    <ul>
      <li id="markAsImportant" onclick="markAsImportant(this.id)">Important</li>
      <li id="deleteCon" onclick="deleteconversation(this.id)">Delete</li>
      <li id="id">sadf</li>
    </ul>
  </div>