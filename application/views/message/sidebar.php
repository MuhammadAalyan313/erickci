<div class="message-sidebar-custom-scrollbar">
    <?php //die(print_r($unread_conversations));?>
                        <div class="row-custom messages-sidebar">
                       
                            <?php 
                             $fontWeight = 600;
                             $subFontWeight = '';
                            foreach ($get_conversations as $item):
                                  foreach($unread_conversations as $unread):
                                    if ($unread->id == $item->id) {
                                     $fontWeight = 800;
                                     $subFontWeight = 800;
                                       # code...
                                     
                                       break;
                                    }
                                    else{
                                        $fontWeight = 600;
                                        $subFontWeight = '';
                                      
                                        continue;
                                    }
                                endforeach;
                              
                               
                                
                                $user_id = 0;
                                if ($item->receiver_id != $this->auth_user->id) {
                                    $user_id = $item->receiver_id;
                                } else {
                                    $user_id = $item->sender_id;
                                }
                                $user = get_user($user_id);
                                if (!empty($user)):?>
                                    <div class="conversation-item <?php echo ($item->id == $conversation->id) ? 'active-conversation-item' : ''; ?>">
                                        <a href="<?php echo generate_url("messages", "conversation"); ?>/<?php echo $item->id; ?>" class="conversation-item-link">
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
                                                    <p class="subject" style="font-weight:<?=  $subFontWeight?>"><?php echo html_escape(character_limiter($item->subject, 28, '...')); ?></p>
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
                            endforeach; ?>
                        </div>
                    </div>