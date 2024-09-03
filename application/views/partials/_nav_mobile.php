<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="navMobile" class="nav-mobile">
  <div class="nav-mobile-sc">
    <div class="nav-mobile-inner">
      <div class="row" style="position: sticky; top:0; background:#fff; z-index:10">
        <div class="col-sm-12 mobile-nav-buttons">
          <?php if (is_multi_vendor_active()):
            if ($this->auth_check): ?>
              <a href="<?= generate_dash_url("add_product"); ?>" class="btn btn-md btn-custom btn-block"><?= trans("add_listing"); ?></a>
            <?php else: ?>
              <a href="javascript:void(0)" class="btn btn-md btn-custom btn-block close-menu-click" data-toggle="modal" data-target="#loginModal"><?php echo trans("add_listing"); ?></a>
          <?php endif;
          endif; ?>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12 nav-mobile-links">
          <div id="navbar_mobile_back_button"></div>
          <ul id="navbar_mobile_categories" class="navbar-nav">
            <?php if (!empty($this->parent_categories)):
              foreach ($this->parent_categories as $category):
                if ($category->show_on_main_menu == 1):
                  if ($category->has_subcategory > 0): ?>
                    <li class="nav-item">
                      <a href="javascript:void(0)" class="nav-link" data-id="<?= $category->id; ?>" data-parent-id="<?= $category->parent_id; ?>"><?php echo category_name($category); ?><i class="icon-arrow-right"></i></a>
                    </li>
                  <?php else: ?>
                    <li class="nav-item">
                      <a href="<?php echo generate_category_url($category); ?>" class="nav-link"><?php echo category_name($category); ?></a>
                    </li>
                <?php endif;
                endif; ?>
            <?php endforeach;
            endif; ?>
          </ul>
          <ul id="navbar_mobile_links" class="navbar-nav">
            <?php if ($this->auth_check): ?>
              <li class="nav-item">
                <a href="<?php echo generate_url("wishlist") . "/" . $this->auth_user->slug; ?>" class="nav-link">
                  <?php echo trans("wishlist"); ?>
                </a>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a href="<?php echo generate_url("wishlist"); ?>" class="nav-link">
                  <?php echo trans("wishlist"); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($this->auth_check):
              $notifications = get_notification();
            ?>
              <li class="nav-item">
                <a href="javascript:void(0)" id="alert-btn" class="nav-link">
                  <?php echo "Alerts"; ?><i class="icon-arrow-right"></i>
                  <?php if ($notifications['notification_count'] > 0): ?>
                    <span class="span-message-count" id="span-message-count">(<?= $notifications['notification_count']; ?>)</span>
                  <?php endif; ?>
                </a>
              </li>

            <?php endif; ?>
            <li class="nav-item">
              <a href="<?php echo generate_url("shops"); ?>" class="nav-link">
                <?php echo trans("shop"); ?>
              </a>
            </li>

            <!-- <li class="nav-item">
<a href="<?php //echo generate_url("map"); 
          ?>" class="nav-link">
<?php //echo "Map"; 
?>
</a>
</li> -->
            <?php if (!empty($this->menu_links)):
              foreach ($this->menu_links as $menu_link):
                if ($menu_link->page_default_name == 'blog' || $menu_link->page_default_name == 'contact' || $menu_link->location == 'top_menu'):
                  $item_link = generate_menu_item_url($menu_link);
                  if (!empty($menu_link->page_default_name)):
                    $item_link = generate_url($menu_link->page_default_name);
                  endif; ?>
                  <li class="nav-item"><a href="<?= $item_link; ?>" class="nav-link"><?= html_escape($menu_link->title); ?></a></li>
            <?php endif;
              endforeach;
            endif; ?>
            <?php if ($this->general_settings->location_search_header == 1 && item_count($this->countries) > 0): ?>
              <li class="nav-item nav-item-messages" style="position: relative;">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#locationModal" class="nav-link btn-modal-location close-menu-click">
                  <!-- <i class="icon-map-marker float-left"></i> -->
                  &nbsp;<?= !empty($this->default_location_input) ? $this->default_location_input : trans("location"); ?>
                </a>
                <?php
                if (isset($_SESSION['mds_default_location'])):
                ?>
                  <div class="text-left" style="font-size: 12px; cursor:pointer; "><a href="<?php echo base_url() . "reset" ?>">reset location</a></div>
                <?php
                endif;
                ?>
              </li>

            <?php endif; ?>
            <?php if ($this->auth_check): ?>
              <li class="dropdown profile-dropdown nav-item">
                <a href="#" class="dropdown-toggle image-profile-drop nav-link" data-toggle="dropdown" aria-expanded="false">
                  <!-- <span id='me-load-1-1'>
    <span  id='message-count-1'>
<?php if ($unread_message_count > 0): ?>
<span class="message-notification message-notification-mobile"><?= $unread_message_count; ?></span>
<?php endif; ?>
</span>
</span> -->
                  <img src="<?php echo get_user_avatar($this->auth_user); ?>" alt="<?php echo html_escape($this->auth_user->username); ?>">
                  <?php
                  if ($this->auth_user->role_id == 2 || $this->auth_user->role_id == 1):
                    echo get_shop_name($this->auth_user); ?> <span class="icon-arrow-down"></span>
                  <?php
                  else:
                    echo $this->auth_user->first_name; ?>
                    <span class="icon-arrow-down"></span>
                  <?php
                  endif;
                  ?>

                </a>
                <ul class="dropdown-menu">
                  <?php if (is_admin()): ?>
                    <li>
                      <a href="<?php echo admin_url(); ?>">
                        <i class="icon-admin"></i>
                        <?php echo trans("admin_panel"); ?>
                      </a>
                    </li>
                  <?php endif; ?>
                  <?php if (is_vendor()): ?>
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
                  <?php if ($this->is_sale_active): ?>
                    <li>
                      <a href="<?php echo generate_url("orders"); ?>">
                        <i class="icon-shopping-basket"></i>
                        <?php echo trans("orders"); ?>
                      </a>
                    </li>
                    <?php if (is_bidding_system_active()): ?>
                      <li>
                        <a href="<?php echo generate_url("quote_requests"); ?>">
                          <i class="icon-price-tag-o"></i>
                          <?php echo trans("quote_requests"); ?>
                        </a>
                      </li>
                    <?php endif; ?>
                    <?php if ($this->general_settings->digital_products_system == 1): ?>
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
                  <!-- <li>
<a href="<?php echo generate_url("messages"); ?>">
<i class="icon-mail"></i>
<?php echo trans("messages"); ?>&nbsp;
<span id='me-load-1'>
    <span  id='message-count'>
<?php if ($unread_message_count > 0): ?>
<span class="span-message-count">(<?= $unread_message_count; ?>)</span>
<?php endif; ?>
</span>
</span>
</a>
</li> -->
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
            <?php else: ?>
              <li class="nav-item"><a href="javascript:void(0)" data-toggle="modal" data-target="#loginModal" class="nav-link close-menu-click"><?php echo trans("login"); ?></a></li>
              <li class="nav-item"><a href="<?php echo generate_url("register"); ?>" class="nav-link"><?php echo trans("register"); ?></a></li>
            <?php endif; ?>



            <?php //if (!empty($this->currencies)): 
            ?>
            <!-- <li class="nav-item dropdown language-dropdown currency-dropdown currency-dropdown-mobile">
<a href="javascript:void(0)" class="nav-link dropdown-toggle" data-toggle="dropdown">
<= $this->selected_currency->code; ?>&nbsp;(<= $this->selected_currency->symbol; ?>)<i class="icon-arrow-down"></i> -->
            </a>
            <!-- 
<?php //echo form_open('set-selected-currency-post'); 
?>
<ul class="dropdown-menu">
<?php //foreach ($this->currencies as $currency):
//if ($currency->status == 1):
?>
<li>
<button type="submit" name="currency" value="<= $currency->code; ?>"><= $currency->code; ?>&nbsp;(<= $currency->symbol; ?>)</button>
</li>
<?php //endif;
//endforeach; 
?>
</ul>
<?php //echo form_close(); 
?>
</li>
<?php //endif; 
?> -->

            <?php if ($this->general_settings->multilingual_system == 1 && count($this->languages) > 1): ?>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <?php echo trans("language"); ?>
                </a>
                <ul class="mobile-language-options">
                  <?php foreach ($this->languages as $language): ?>
                    <li>
                      <a href="<?= convert_url_by_language($language); ?>" class="dropdown-item <?php echo ($language->id == $this->selected_lang->id) ? 'selected' : ''; ?>">
                        <?= html_escape($language->name); ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </li>
            <?php endif; ?>
          </ul>
          <div class="alert-div display-none slide-in-150s" id="alert-div">
            <a href="javascript:void(0)" id="alert-go-back" class="nav-link" style="text-align:left; border-bottom: 1px solid rgba(0,0,0,.05);margin:0;padding: 13px 0;">
              <i class="icon-angle-left"></i> <strong>Alerts</strong>
            </a>
            <ul class="navbar-nav" id="m-notification-list">
              <?php

              if ($notifications['notification_count'] > 0):
                foreach ($notifications['notifications'] as $notification) {
                  if ($notification->user->role_id == 2 || $notification->user->role_id == 1) {
                    $name = get_shop_name($notification->user);
                  } else {
                    $name =  $notification->user->first_name;
                  }
                  if ($notification->notification_type == 3) {
              ?>
                    <li class="nav-item" style="display:flex" id="m-notification-<?= $notification->id ?>">
                      <a class="nav-link" href="<?php echo base_URL() . $notification->product->slug; ?>" style="width:80%; display:flex">


                        <?php  //echo "<i class='icon-comment'></i>`
                        ?>
                        <div style="display: flex; align-items: center;"><span style="display:block; width:30px; height:30px; overflow:hidden; border-radius:50%"><img src="<?= base_URL() . '/' . $notification->user->avatar ?>" alt="" style="width:100%; height:100%"></span></div>
                        <div style="padding-left:5px"><span><?= "`" . $name . "` reviewed your product " ?>
                            <span style="display:block; text-align:right; margin-top: 7px; color:#a19c9c;"><?= date("Y-m-d", strtotime($notification->created_at)) ?></span>
                          </span></div>


                      </a>
                      <span style="display: flex; align-items: center; justify-content:center; width:20%; text-align: center;"><i class="fa fa-close" style="font-size:16px" onclick="deleteNotification(event,'<?= $notification->id ?>')"></i></span>
                    </li>
                  <?php
                  }
                  if ($notification->notification_type == 2) {
                  ?>
                    <li class="nav-item" style="display:flex" id="m-notification-<?= $notification->id ?>">
                      <a class="nav-link" href="<?php echo base_URL() . $notification->product->slug; ?>" style="width:80%; display:flex">


                        <?php  //echo "<i class='icon-comment'></i>`
                        ?>
                        <div style="display: flex; align-items: center;"><span style="display:block; width:30px; height:30px; overflow:hidden; border-radius:50%"><img src="<?= base_URL() . '/' . $notification->user->avatar ?>" alt="" style="width:100%; height:100%"></span></div>
                        <div style="padding-left:5px"><span><?= "`" . $name . "`  post a comment on your product " ?>
                            <span style="display:block; text-align:right; margin-top: 7px; color:#a19c9c;"><?= date("Y-m-d", strtotime($notification->created_at)) ?></span>
                          </span></div>


                      </a>
                      <span style="display: flex; align-items: center; justify-content:center; width:20%; text-align: center;"><i class="fa fa-close" style="font-size:16px" onclick="deleteNotification(event,'<?= $notification->id ?>')"></i></span>
                    </li>
                    <?php
                  }
                  if ($notification->notification_type == 4) {
                    if ($notification->deleted_by != $this->auth_user->id or $notification->deleted_by == NULL) {
                      if ($notification->commit_parent == $this->auth_user->id && $notification->auth_id != $this->auth_user->id) {
                    ?>
                        <li class="nav-item" style="display:flex" id="m-notification-<?= $notification->id ?>">
                          <a class="nav-link" href="<?php echo base_URL() . $notification->product->slug; ?>" style="width:80%; display:flex">


                            <?php  //echo "<i class='icon-comment'></i>`
                            ?>
                            <div style="display: flex; align-items: center;"><span style="display:block; width:30px; height:30px; overflow:hidden; border-radius:50%"><img src="<?= base_URL() . '/' . $notification->user->avatar ?>" alt="" style="width:100%; height:100%"></span></div>
                            <div style="padding-left:5px"><span><?= "`" . $name . "`  replied on your comment" ?>
                                <span style="display:block; text-align:right; margin-top: 7px; color:#a19c9c;"><?= date("Y-m-d", strtotime($notification->created_at)) ?></span>
                              </span></div>


                          </a>
                          <span style="display: flex; align-items: center; justify-content:center; width:20%; text-align: center;"><i class="fa fa-close" style="font-size:16px" onclick="deleteNotification(event,'<?= $notification->id ?>')"></i></span>
                        </li>
                      <?php
                      } else {
                      ?>
                        <li class="nav-item" style="display:flex" id="m-notification-<?= $notification->id ?>">
                          <a class="nav-link" href="<?php echo base_URL() . $notification->product->slug; ?>" style="width:80%; display:flex">


                            <?php  //echo "<i class='icon-comment'></i>`
                            ?>
                            <div style="display: flex; align-items: center;"><span style="display:block; width:30px; height:30px; overflow:hidden; border-radius:50%"><img src="<?= base_URL() . '/' . $notification->user->avatar ?>" alt="" style="width:100%; height:100%"></span></div>
                            <div style="padding-left:5px"><span><?= "`" . $name . "`  replied to a comment on your product" ?>
                                <span style="display:block; text-align:right; margin-top: 7px; color:#a19c9c;"><?= date("Y-m-d", strtotime($notification->created_at)) ?></span>
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
              <?php else: ?>
                <li class="nav-item"><a href="javascript:void(0)" class="nav-link">No Notification Right Now</a></li>
              <?php endif; ?>
            </ul>
          </div>

        </div>
      </div>
    </div>
  </div>
  <div class="nav-mobile-footer" style="z-index: 10000000;">
    <?php $this->load->view('partials/_social_links', ['show_rss' => true]); ?>
  </div>
</div>