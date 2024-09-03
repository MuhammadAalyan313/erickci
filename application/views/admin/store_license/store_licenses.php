<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?php echo trans("users"); ?></h3>
        </div>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive">

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr role="row">
                                <th width="20"><?php echo trans("id"); ?></th>
                                <th><?php echo "License Name"; ?></th>
                                <th><?php echo "Action"; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($licenses as $license) : ?>
                                <tr>
                                    <td><?php echo $license->id; ?></td>
                                    <td>
                                        <?php echo $license->name; ?>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn bg-purple dropdown-toggle btn-select-option" type="button" data-toggle="dropdown"><?php echo trans("select_option"); ?>
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu options-dropdown">
                                                <li>
                                                    <a href="<?php echo admin_url(); ?>edit-store-license/<?php echo $license->id ?>"><i class="fa fa-edit option-icon"></i><?php echo trans("edit"); ?></a>
                                                </li>
                                                <li>
                                                    <a href="#" license-id="<?php echo $license->id ?>" onclick="delete_license(this)"><i class="fa fa-close option-icon"></i><?php echo trans("delete"); ?></a>
                                                </li>

                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (empty($licenses)) : ?>
                        <p class="text-center text-muted"><?= trans("no_records_found"); ?></p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="delete-pop-modal display-none cys" id="popup-modal">
    <div class="delete-popup-modal-container">
        <div class="swal-icon swal-icon--warning">
            <span class="swal-icon--warning__body">
                <span class="swal-icon--warning__dot"></span>
            </span>
        </div>
        <div class="swal-text" style="">Are you sure you want to delete this License?
        </div>
        <div class="swal-footer">
            <div class="swal-button-container">
                <button style="border-radius: 3px !important; padding: 8px 22px !important; background-color: #efefef; border-color: transparent !important;color: #555;font-weight: 600;font-size: 14px;" class="cancel" tabindex="0">Cancel</button>
            </div>
            <div class="swal-button-container">
               <?php echo form_open('license_controller/delete_license'); ?>
               <input type="hidden" name="license_id" >
               <button style="border-radius: 3px !important; padding: 8px 22px !important; background-color: #e64942;border-color: transparent !important;color: #fff; font-weight: 600;font-size: 14px;">OK</button>
               <?php echo  form_close(); ?>
            </div>
        </div>
    </div>

</div>
<script>
    function delete_license(element) {
        document.getElementById('popup-modal').classList.remove('display-none')
        document.querySelector('input[name="license_id"]').value = element.getAttribute('license-id')
        document.getElementById('popup-modal').addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-pop-modal')) {
                event.target.classList.add('display-none')
            }
            if (event.target.classList.contains('cancel')) {
                document.getElementById('popup-modal').classList.add('display-none')
            }

        })
        console.log(element.getAttribute('license-id'))
    }

</script>