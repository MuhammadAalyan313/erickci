<?php
    $profile_id = $conversation->sender_id;
    if ($this->auth_user->id == $conversation->sender_id) {
        $profile_id = $conversation->receiver_id;
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
                        <strong class="username"><?= html_escape(get_shop_name($profile)); ?></strong>
                    </a>
                    <p class="p-last-seen">
                        <span class="last-seen <?php echo (is_user_online($profile->last_seen)) ? 'last-seen-online' : ''; ?>"> <i class="icon-circle"></i> <?php echo trans("last_seen"); ?>&nbsp;<?php echo time_ago($profile->last_seen); ?></span>
                    </p>
                    <?php if (!empty($conversation->product_id)):
                        $product = get_product($conversation->product_id);
                        if (!empty($product)):?>
                            <p class="subject m-0 font-600"><a href="<?= generate_product_url($product); ?>" class="link-black link-underlined"><?= html_escape($conversation->subject); ?></a></p>
                        <?php endif;
                    else: ?>
                        <p class="subject m-0 font-600"><?= html_escape($conversation->subject); ?></p>
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
                                        <?php echo html_escape($item->message); ?>
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
                                        <?php echo html_escape($item->message); ?>
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
            <?php echo form_open('send-message-post', ['id' => 'theform']); ?>
            <input type="hidden" name="conversation_id" value="<?php echo $conversation->id; ?>">
            <?php if ($this->auth_user->id == $conversation->sender_id): ?>
                <input type="hidden" name="receiver_id" value="<?php echo $conversation->receiver_id; ?>">
            <?php else: ?>
                <input type="hidden" name="receiver_id" value="<?php echo $conversation->sender_id; ?>">
            <?php endif; ?>
            <div class="form-group m-b-10">
                <textarea id="myTextarea" class="form-control form-textarea" name="message" placeholder="<?php echo trans('write_a_message'); ?>" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-md btn-custom float-right"><i class="icon-send"></i> <?php echo trans("send"); ?></button>
            </div>
            <?php echo form_close(); ?>
            <!-- form end -->
        </div>
    </div>