<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Wrapper -->
<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo lang_base_url(); ?>"><?php echo trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo trans("messages"); ?></li>
                    </ol>
                </nav>
                <h1 class="page-title"><?php echo trans("messages"); ?></h1>
            </div>
        </div>
        <div class="row row-col-messages">
            
                <div class="col-sm-12 col-md-12 col-lg-3 col-message-sidebar">
                    <div class="message-labels">
                        <div  class="<?= $inbox?>"><a href="<?= base_url()?>messages/">Inbox</a></div>
                        <div class="<?= $unread?>"><a href="<?= base_url()?>messages/unread" >Unread</a></div>
                        <div class="<?= $important?> last-div"> <a href="<?= base_url()?>messages/important" >Important</a></div>
                    </div>
                    <?php if (empty($get_conversations)): ?>
                        <div class="col-12">
                            <p class="text-center"><?php echo trans("no_messages_found"); ?></p>
                        </div>
                    <?php else: 
                        $this->load->view('/message/sidebar');
                        ?>
                    
                </div>

                <div class="col-sm-12 col-md-12 col-lg-9 col-message-content">
                    
                    <?php 
                    if(!empty($conversations)):
                    $this->load->view('/message/conversation'); 
                    else:?>
                        <div class="col-12">
                            <p class="text-center"><?php echo "Messages Shown Here" ?></p>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Wrapper End-->
