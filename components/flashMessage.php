<?php if (isset_flash_message_by_type(FLASH_SUCCESS)) : ?>
    <div class="notif success">
        <p><?php display_flash_message_by_type(FLASH_SUCCESS); ?></p>
    </div>
<?php elseif (isset_flash_message_by_type(FLASH_ERROR)) : ?>
    <div class="notif error">
        <p><?php display_flash_message_by_type(FLASH_ERROR); ?></p>
    </div>
<?php elseif (isset_flash_message_by_type(FLASH_WARNING)) : ?>
    <div class="notif warning">
        <p><?php display_flash_message_by_type(FLASH_WARNING); ?></p>
    </div>
<?php endif; ?>