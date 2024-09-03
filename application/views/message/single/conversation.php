<?php
//var_dump($last_conversation_id);
    $profile_id ='';
    if($messages_by_conversation_id){
        $profile_id = $conversation->sender_id;
        if ($this->auth_user->id == $conversation->sender_id) {
            $profile_id = $conversation->receiver_id;
        }
    }
    else{
        $profile_id = $user_id;
    }

    $profile = get_user($profile_id);


    if (!empty($profile)):?>
        <div class="row-custom messages-head">
            <div class="sender-head">
                <div class="left">
                    <a href="<?php echo generate_profile_url($profile->slug); ?>">
                        <img src="<?php echo get_user_avatar($profile); ?>" alt="<?= html_escape(get_shop_name($profile)); ?>" class="img-profile">
                    </a>
                </div>
                <div class="right">
                    <a href="<?php echo generate_profile_url($profile->slug); ?>">
                    <?php 
                    $icon ='';
                     if($profile->role_id == 2 || $profile->role_id == 1):
                        if($profile->acc_type == 3){
                            $icon = "<i class=\"icon-verified icon-verified-member\"style=\"float:right\" ></i>";
                            
                        }
                        else{
                            $icon = "<i class=\"icon-verified icon-verified-member\" style=\"color:orange;float:right\"></i>";
                        }
                        echo '<div style="display:table"><strong class="username" style="float:left"> '.html_escape(get_shop_name($profile)). $icon.'</strong></div>';
                    else:
                        echo '<div style="display:table"><strong class="username" style="float:left">'.$profile->first_name. $icon.'</strong></div>';
                    endif;
                    ?>
                        <!-- <div style="display:table">
                        <strong class="username" style="float:left"><?= html_escape(get_shop_name($profile)).$icon; ?></strong></div> -->
                    </a>
                    <p class="p-last-seen">
                        <span class="last-seen <?php echo (is_user_online($profile->last_seen)) ? 'last-seen-online' : ''; ?>"> <i class="icon-circle"></i> <?php echo trans("last_seen"); ?>&nbsp;<?php echo time_ago($profile->last_seen); ?></span>
                    </p>
                    <?php if (!empty($conversation->sender_id)):
                        $product = get_product($conversation->sender_id);
                        if (!empty($product)):?>
                            <!-- <p class="subject m-0 font-600"><a href="<?= generate_product_url($sender_id); ?>" class="link-black link-underlined"><?= html_escape($conversation->sender_id); ?></a></p> -->
                        <?php endif;
                    else: ?>
                        <!-- <p class="subject m-0 font-600"><?= html_escape($conversation->sender_id); ?></p> -->
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="row-custom messages-content">
        <div id="message-custom-scrollbar" class="messages-list">
            <?php foreach ($messages as $item):
                if ($item->deleted_user_id != $this->auth_user->id): ?>
                    <?php if ($this->auth_user->id == $item->receiver_id): ?>
                        <div class="message-list-item">
                            <div class="message-list-item-row-received">
                                <div class="user-avatar">
                                    <div class="message-user">
                                        <a href="<?php echo get_profile_url_by_id($item->sender_id); ?>">
                                            <img src="<?php echo get_user_avatar_by_id($item->sender_id); ?>" alt="" class="img-profile">
                                        </a>
                                    </div>
                                </div>
                                <div class="user-message">
                                    <div class="message-text">
                                        <?php echo nl2br(html_escape($item->content)); ?>
                                    </div>
                                    <span class="time"><?php echo time_ago($item->created_at); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="message-list-item">
                            <div class="message-list-item-row-sent">
                                <div class="user-message">
                                    <div class="message-text" style="background-color:#FA7348">
                                        <?php echo nl2br(html_escape($item->content)); ?>
                                    </div>
                                    <span class="time"><?php echo time_ago($item->created_at); ?></span>
                                </div>
                                <div class="user-avatar">
                                    <div class="message-user">
                                        <a href="<?php echo get_profile_url_by_id($item->sender_id); ?>"> 
                                            <img src="<?php echo get_user_avatar_by_id($item->sender_id); ?>" alt="" class="img-profile">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="message-reply">
            <!-- form start -->
            <?php echo form_open('send-message-post', ['id' => 'singleForm']); ?>
            <input type="hidden" name="conversation_id" value="<?php echo ($conversation) ? $conversation->id : 0; ?>">
            <?php if($conversation):?>
                <?php if ($this->auth_user->id == $conversation->sender_id): ?>
                    <input type="hidden" name="receiver_id" value="<?php echo $conversation->receiver_id; ?>">
                <?php else: ?>
                    <input type="hidden" name="receiver_id" value="<?php echo $conversation->sender_id; ?>">
                <?php endif; ?>
            <?php else:?>
                <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
            <?php endif;?>
            <div class="form-group m-b-10">
                <textarea id="myTextarea" class="form-control form-textarea" name="message" placeholder="<?php echo trans('write_a_message'); ?>" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" id="submitBtn" class="btn btn-md btn-custom float-right"><i class="icon-send"></i> <?php echo trans("send"); ?></button>
            </div>
            <?php echo form_close(); ?>
            <!-- form end -->
        </div>
    </div>