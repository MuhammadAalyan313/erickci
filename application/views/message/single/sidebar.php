<div class="message-sidebar-custom-scrollbar" id ="message-sidebar-custom-scrollbar-id">
    <?php //die(print_r($unread_conversations));?>
                        <div class="row-custom messages-sidebar">
                       
                            <?php 
                             $fontWeight = 600;
                             $subFontWeight = '';
                             
                            if($get_conversations){
                                foreach ($get_conversations as $item):                    
                                    $user_id = 0;
                                    if ($item->receiver_id != $this->auth_user->id) {
                                        $user_id = $item->receiver_id;
                                    } else {
                                        $user_id = $item->sender_id;
                                    }
                                    $user = get_user($user_id);
                                    if (!empty($user)):
                                    if($conversation){
                                        ?><div class="conversation-item <?php echo ($item->id == $conversation->id) ? 'active-conversation-item' : ''; ?>"><?php
                                    }
                                    else{
                                        ?><div class="conversation-item"><?php
                                    }
                                    ?>
                                    
                                        
                                            <a href="<?= base_URL()?>messages_view/conversation/<?php echo $item->id; ?>" class="conversation-item-link">
                                                <div class="middle">
                                                    <img src="<?php echo get_user_avatar($user); ?>" alt="<?= html_escape(get_shop_name($user)); ?>">
                                                </div>
                                                <div class="right">
                                                    <div class="row-custom">
                                                    <?php if($user->role_id == 2 || $user->role_id == 1):
                                                    if($user->acc_type == 3){
                                                        $icon = '<i class="icon-verified icon-verified-member" style="float:right"></i>';
                                                    }
                                                    elseif($user->acc_type == 2){
                                                        $icon = '<i class="icon-verified icon-verified-member" style="color:orange;float:right"></i>';
                                                    }
                                                    else{
                                                        $icon = '';
                                                    }
                                                        ?> 
                                                        <strong class="username link-hover" style="font-weight:<?=  $fontWeight?>"><?= html_escape(get_shop_name($user)); ?></strong><?=$icon?>
                                                      
                                                    <?php 
                                                    else: ?>
                                                        <strong class="username link-hover" style="font-weight:<?=  $fontWeight?>"><?= html_escape($user->first_name); ?></strong>
                                                    <?php endif; ?>
                                                        
                                                      
                                                        
                                                    </div>
                                                    <div class="row-custom m-b-0">
                                                        <!-- <p class="subject" style="font-weight:<?=  $subFontWeight?>"><?php echo html_escape(character_limiter($user->username, 28, '...')); ?></p> -->
                                                    </div>
                                                </div>
                                            </a>
                                            <?php
                                              if ($item->important == 0) {
                                                ?>
                                                 <a href="javascript:void(0)" class="important-conversation-link" onclick='add_fav_conversation(<?php echo $item->id; ?>,"<?php echo trans("confirm_message"); ?>");'><i class="icon-star"></i></a>
                                                <?php
                                            } else {
                                                ?>
                                                 <a href="javascript:void(0)" class="important-conversation-link linkColor" onclick='delete_fav_conversation(<?php echo $item->id; ?>,"<?php echo trans("confirm_message"); ?>");'><i class="icon-star"></i></a>
                                                <?php
                                            }
                                            ?>
                                           
                                            <a href="javascript:void(0)" class="delete-conversation-link" onclick='delete_conversation(<?php echo $item->id; ?>,"<?php echo trans("confirm_message"); ?>");'><i class="icon-trash"></i></a>
                                        </div>
                                    <?php endif;
                                endforeach;
                            }
                            else{
                                $user_id = $new_user_id;
                                    $user = get_user($user_id);
                                    if (!empty($user)):
                                   
                                        ?>
                                        <div class="conversation-item 'active-conversation-item'"><?php
                                    
                                    
                                    ?>
                                    
                                        
                                            <a href="<?= base_URL()?>messages_view/conversation/user/<?php echo $user->id; ?>" class="conversation-item-link">
                                                <div class="middle">
                                                    <img src="<?php echo get_user_avatar($user); ?>" alt="<?= html_escape(get_shop_name($user)); ?>">
                                                </div>
                                                <div class="right">
                                                    <div class="row-custom">
                                                    <?php if($user->role_id == 2 || $user->role_id == 1):
                                                        ?> 
                                                        <strong class="username link-hover" style="font-weight:<?=  $fontWeight?>"><?= html_escape(get_shop_name($user)); ?></strong>
                                                      
                                                    <?php 
                                                    else: ?>
                                                        <strong class="username link-hover" style="font-weight:<?=  $fontWeight?>"><?= html_escape($user->first_name); ?></strong>
                                                    <?php endif; ?>
                                                        
                                                      
                                                        
                                                    </div>
                                                    <div class="row-custom m-b-0">
                                                        <!-- <p class="subject" style="font-weight:<?=  $subFontWeight?>"><?php echo html_escape(character_limiter($user->username, 28, '...')); ?></p> -->
                                                    </div>
                                                </div>
                                            </a>
                                            <?php
                                            //   if ($item->important == 0) {
                                                ?>
                                                 <!-- <a href="javascript:void(0)" class="important-conversation-link" onclick='add_fav_conversation(<?php echo $item->id; ?>,"<?php echo trans("confirm_message"); ?>");'><i class="icon-star"></i></a> -->
                                                <?php
                                            // } else {
                                                ?>
                                                 <!-- <a href="javascript:void(0)" class="important-conversation-link linkColor" onclick='delete_fav_conversation(<?php echo $item->id; ?>,"<?php echo trans("confirm_message"); ?>");'><i class="icon-star"></i></a> -->
                                                <?php
                                            // }
                                            ?>
                                           
                                            <!-- <a href="javascript:void(0)" class="delete-conversation-link" onclick='delete_conversation(<?php echo $item->id; ?>,"<?php echo trans("confirm_message"); ?>");'><i class="icon-trash"></i></a> -->
                                        </div>
                                    <?php endif;
                                
                            }
                            ?>
                        </div>
                    </div>