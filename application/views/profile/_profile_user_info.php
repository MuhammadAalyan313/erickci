<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!--user profile info-->
<div class="row-custom">
    <div class="profile-details">
        <div class="left">
            <img src="<?php echo get_user_avatar($user); ?>" alt="<?php echo get_shop_name($user); ?>" class="img-profile" id="qrpopup-btn" data-toggle="modal" data-target="#qrPopup" data-slug= "<?php echo base_url().'/profile/' . $user->slug?>" >
        </div>
        <div class="right">
            <div class="row-custom row-profile-username">
                <h1 class="username">
                    <!-- <a href="<?php echo generate_profile_url($user->slug); ?>"> -->
                    
                     <?php 
                     
                     if($user->role_id == 2 || $user->role_id == 1){
                        echo get_shop_name($user);
                     }
                     else{
                        echo $user->first_name;
                     }
                    
                      ?>
                    <!-- </a> -->
                </h1>
                <?php if (is_vendor($user)): ?>
                    <?php if($user->acc_type == 1):
                    
                    elseif($user->acc_type == 2):?>
                        <i class="icon-verified icon-verified-member" style="color:orange"></i>
                    <?php else: ?>
                        <i class="icon-verified icon-verified-member" ></i>
                    <?php endif; ?>
                <?php endif;
                 echo " <span style='font-size:12px;display: inline-block;margin: 5px;width: fit-content;'>". $user->license_number ."</span> "
                ?>
            </div>
            
            <div class="row-custom">
                <p class="p-last-seen">
                    <span class="last-seen" style="padding:0 2px"><?= $user->username?></span>
                    <?php if($user->online_status == 1):?>
                        <span class="last-seen <?php echo (is_user_online($user->last_seen)) ? 'last-seen-online' : ''; ?>"> <i class="icon-circle"></i> <?php echo trans("last_seen"); ?>&nbsp;<?php echo time_ago($user->last_seen); ?></span>
                    <?php endif;?>
                </p>
            </div>
            
            <?php if (is_vendor($user)): ?>
                <div class="row-custom">
                    <p class="description">
                        <?php echo html_escape($user->about_me); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="row-custom user-contact">
                <span class="info"><?php echo trans("member_since"); ?>&nbsp;<?php echo helper_date_format($user->created_at, false); ?></span>
               <?php
               if($this->auth_check || !$this->auth_check):
                    if($this->auth_user->id != $user->id):
                            if($user->role_id == 1 || $user->role_id == 2):
                               
                
                                if ($this->general_settings->hide_vendor_contact_information != 1):
                                    if (!empty($user->phone_number) && $user->show_phone == 1): ?>
                                        <span class="info"><i class="icon-phone"></i>
                                        <a href="javascript:void(0)" id="show_phone_number"><?php echo trans("show"); ?></a>
                                        <a href="tel:<?php echo html_escape($user->phone_number); ?>" id="phone_number" class="display-none"><?php echo html_escape($user->phone_number); ?></a>
                                    </span>
                                    <?php endif; ?>
                                    <?php if (!empty($user->email) && $user->show_email == 1): ?>
                                    <span class="info"><i class="icon-envelope"></i><?php echo html_escape($user->email); ?></span>
                                <?php endif;
                                endif; ?>
                                <?php if (!empty(get_location($user)) && $user->show_location == 1): ?>
                                    <span class="info"><i class="icon-map-marker"></i><?php echo get_location($user); ?></span>
                                <?php endif; 
                        else:
                            
                            if($member_location == 1):
                               
                                   
                                        if (!empty($user->phone_number)): ?>
                                        <!-- <span class="info"><i class="icon-phone"></i>
                                        <a href="javascript:void(0)" id="show_phone_number"><?php //echo trans("show"); ?></a>
                                        <a href="tel:<?php //echo html_escape($user->phone_number); ?>" id="phone_number" class="display-none"><?php //echo html_escape($user->phone_number); ?></a> -->
                                    </span>
                                    <?php endif; ?>
                                    <?php if (!empty($user->email)): ?>
                                    <!-- <span class="info"><i class="icon-envelope"></i><?php //echo html_escape($user->email); ?></span> -->
                                <?php endif;
                                 ?>
                                <?php if (!empty(get_location($user))): ?>
                                    <!-- <span class="info"><i class="icon-map-marker"></i><?php //echo get_location($user); ?></span> -->
                                <?php endif; 
                               
                            endif;
                        endif; 
                    else:
                    
                        // code HERE
                    endif;
                 
                endif;
                ?>
            </div>

            <?php if ($this->general_settings->reviews == 1): ?>
                <div class="profile-rating">
                    <?php if ($user_rating->count > 0):
                        $this->load->view('partials/_review_stars', ['review' => $user_rating->rating]); ?>
                        &nbsp;<span>(<?php echo $user_rating->count; ?>)</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row-custom profile-buttons">
                <div class="buttons">
                    <?php if ($this->auth_check): ?>
                        <?php if ($this->auth_user->id != $user->id): ?>
                            <button class="btn btn-md btn-custom btn-sell-now" onclick="openCoversationContactButtons(<?=$user->id?>)"><i class="icon-envelope"></i><?php echo trans("ask_question") ?></button>
                            <!-- <button class="btn btn-md btn-custom btn-sell-now" data-toggle="modal" data-target="#messageModal"><i class="icon-envelope"></i><?php echo trans("ask_question") ?></button> -->
                            <!-- <button class="btn btn-md btn-custom btn-sell-now" data-toggle="modal" data-target="#success_pop"><i class="icon-envelope"></i><?php echo trans("ask_question") ?></button> -->

                            <!--form follow-->
                            <?php echo form_open('follow-unfollow-user-post', ['class' => 'form-inline']); ?>
                            <input type="hidden" name="following_id" value="<?php echo $user->id; ?>">
                            <input type="hidden" name="follower_id" value="<?php echo $this->auth_user->id; ?>">
                            <?php if (is_user_follows($user->id, $this->auth_user->id)): ?>
                                <button class="btn btn-md btn-outline-gray"><i class="icon-user-minus"></i><?php echo trans("unfollow"); ?></button>
                            <?php else: ?>
                                <button class="btn btn-md btn-outline-gray"><i class="icon-user-plus"></i><?php echo trans("follow"); ?></button>
                            <?php endif; ?>
                            <?php echo form_close(); ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="btn btn-md btn-custom btn-sell-now" data-toggle="modal" data-target="#loginModal"><i class="icon-envelope"></i><?php echo trans("ask_question") ?></button>
                        <button class="btn btn-md btn-outline-gray" data-toggle="modal" data-target="#loginModal"><i class="icon-user-plus"></i><?php echo trans("follow"); ?></button>
                    <?php endif; ?>
                </div>

                <?php if ($this->general_settings->hide_vendor_contact_information != 1): ?>
                    <div class="social">
                        <ul>
                            <?php if (!empty($user->personal_website_url)): ?>
                                <li><a href="<?= html_escape($user->personal_website_url); ?>" target="_blank"><i class="icon-globe"></i></a></li>
                            <?php endif;
                            if (!empty($user->facebook_url)): ?>
                                <li><a href="<?= html_escape($user->facebook_url); ?>" target="_blank"><i class="icon-facebook"></i></a></li>
                            <?php endif;
                            if (!empty($user->twitter_url)): ?>
                                <li><a href="<?= html_escape($user->twitter_url); ?>" target="_blank"><i class="icon-twitter"></i></a></li>
                            <?php endif;
                            if (!empty($user->instagram_url)): ?>
                                <li><a href="<?= html_escape($user->instagram_url); ?>" target="_blank"><i class="icon-instagram"></i></a></li>
                            <?php endif;
                            if (!empty($user->pinterest_url)): ?>
                                <li><a href="<?= html_escape($user->pinterest_url); ?>" target="_blank"><i class="icon-pinterest"></i></a></li>
                            <?php endif;
                            if (!empty($user->linkedin_url)): ?>
                                <li><a href="<?= html_escape($user->linkedin_url); ?>" target="_blank"><i class="icon-linkedin"></i></a></li>
                            <?php endif;
                            if (!empty($user->vk_url)): ?>
                                <li><a href="<?= html_escape($user->vk_url); ?>" target="_blank"><i class="icon-vk"></i></a></li>
                            <?php endif;
                            if (!empty($user->whatsapp_url)): ?>
                                <li><a href="<?= html_escape($user->whatsapp_url); ?>" target="_blank"><i class="icon-whatsapp"></i></a></li>
                            <?php endif;
                            if (!empty($user->telegram_url)): ?>
                                <li><a href="<?= html_escape($user->telegram_url); ?>" target="_blank"><i class="icon-telegram"></i></a></li>
                            <?php endif;
                            if (!empty($user->youtube_url)): ?>
                                <li><a href="<?= html_escape($user->youtube_url); ?>" target="_blank"><i class="icon-youtube"></i></a></li>
                            <?php endif;
                            if ($this->general_settings->rss_system == 1 && $user->show_rss_feeds == 1 && get_user_products_count($user->id) > 0): ?>
                                <li><a href="<?= lang_base_url() . "rss/" . get_route("seller", true) . $user->slug; ?>" target="_blank"><i class="icon-rss"></i></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div id="products" class="row-custom"></div>