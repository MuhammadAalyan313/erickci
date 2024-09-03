 <div id="fail_popup<?= $user->id?>" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo "Decline Reason"; ?></h4>
                    </div>
                   
                    <div class="modal-body">
                    <?php echo form_open('membership_controller/approve_shop_opening_request'); ?>
                    <input type="hidden" name="id" value="<?php echo $user->id; ?>">
                        <div class="form-group">
                            <h4 class="modal-title"><?php echo "Reason"; ?></h4>
                            <input type="text" name="decline_reason" class="form-control form-input" required>
                            
                           
                          
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button name="submit" value="0"  type="submit" class="btn btn-danger btn-list-button"><?php echo trans('decline'); ?></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans('close'); ?></button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>