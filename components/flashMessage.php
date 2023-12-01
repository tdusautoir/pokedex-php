<?php if (isset_flash_message_by_type(FLASH_SUCCESS)) : ?>
    <div class="notif success">
        <i class="fa fa-circle-check"></i>
        <p><?php display_flash_message_by_type(FLASH_SUCCESS); ?></p>
    </div>
<?php elseif (isset_flash_message_by_type(FLASH_ERROR)) : ?>
    <div class="notif error">
        <i class="fa fa-cirlce-xmark"></i>
        <p><?php display_flash_message_by_type(FLASH_ERROR); ?></p>
    </div>
<?php elseif (isset_flash_message_by_type(FLASH_WARNING)) : ?>
    <div class="notif warning">
        <i class="fa fa-circle-exclamation"></i>
        <p><?php display_flash_message_by_type(FLASH_WARNING); ?></p>
    </div>
<?php endif; ?>