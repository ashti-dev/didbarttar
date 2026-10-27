<div>
    <div class="notice-wrapper">
        <?php
        \poshtvan\app\notice::show_notices('admin-panel-tickets-menu');
        \poshtvan\app\notice::show_notices('admin-panel-tickets-menu', 'cookie');
        ?>
    </div>
</div>
<form id="poshtvan-option-panel-form" method="post" action="options.php">
    <?php settings_fields(\poshtvan\app\options::get_setting_group_name('tickets')); ?>
    <div class="option_section" id="ticket_custom_status_field">
        <h3 class="option_section_title"><?php esc_html_e('Ticket status', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('You can add extra statuses for your ticket system', 'poshtvan') ?></p>
        <p class="option_section_description">
            <span class="btn-item" id="add_new_ticket_status"><?php esc_html_e('Add new', 'poshtvan') ?></span>
        </p>
        <div class="option_section no-padding" id="custom_status_fields_wrapper">
            <?php
            $ticketCustomStatus = \poshtvan\app\options::getCustomTicketStatus();
            if ($ticketCustomStatus && is_array($ticketCustomStatus)) :
                foreach ($ticketCustomStatus as $statusItem) :
            ?>
                    <div class="option_field option_row_field no-padding">
                        <input type="text" value="<?php echo esc_attr($statusItem['slug']); ?>" placeholder="<?php esc_attr_e('Slug', 'poshtvan') ?>" name="mwtc_poshtvan_ticket_custom_status[slug][]">
                        <input type="text" value="<?php echo esc_attr($statusItem['name']); ?>" placeholder="<?php esc_attr_e('Name', 'poshtvan') ?>" name="mwtc_poshtvan_ticket_custom_status[name][]">
                        <span class="icon"><span class="dashicons dashicons-trash delete-field"></span></span>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </div>
    <div class="option_section" id="auto_ticket_field">
        <h3 class="option_section_title"><?php esc_html_e('Auto Tickets', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Send ticket to user after change ticket status', 'poshtvan') ?></p>

        <div class="option_field option_row_field">
            <label><?php esc_html_e('Choose Operator', 'poshtvan') ?></label>
            <?php
            $operatorUsersList = \poshtvan\app\options::getOperatorUsersList();
            $autoTicketOperatorUser = \poshtvan\app\options::getAutoTicketOperatorUser();

            ?>
            <select class="select2" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('auto_ticket_operator_user')) ?>" id="auto_ticket_operator_user">
                <?php if ($operatorUsersList) : ?>
                    <option value="0" disabled selected><?php esc_html_e('Choose Operator', 'poshtvan'); ?></option>
                    <?php foreach ($operatorUsersList as $operatorUser) : ?>
                        <option <?php selected($autoTicketOperatorUser, $operatorUser->ID) ?> value="<?php echo esc_attr($operatorUser->ID) ?>"><?php echo esc_html(sprintf('%s (%s)', $operatorUser->user_login, $operatorUser->display_name)) ?></option>
                    <?php endforeach; ?>
                <?php else : ?>
                    <option value="0" disabled selected><?php esc_html_e("Didn't find any operator", 'poshtvan'); ?></option>
                <?php endif; ?>
            </select>
        </div>
        
        <p class="option_section_description">
            <span class="btn-item" id="add_new_auto_ticket_item"><?php esc_html_e('Add new', 'poshtvan') ?></span>
        </p>
        <div class="option_section no-padding" id="auto_ticket_items_fields_wrapper">
            <?php
            $autoTicketItems = \poshtvan\app\options::getAutoTicketItems();
            $ticketStatusList = \poshtvan\app\tickets::getAutoTicketStatusList();
            if($autoTicketItems):
                foreach($autoTicketItems as $autoTicketItemKey => $autoTicketItemData): ?>
                    <?php foreach($autoTicketItemData as $autoTicketItemContent): ?>
                        <div class="option_field option_row_field no-padding">
                            <select name="mwtc_poshtvan_auto_ticket_item[status][]">
                                <?php if ($ticketStatusList) : ?>
                                    <?php foreach ($ticketStatusList as $statusItem) : ?>
                                        <option <?php selected($autoTicketItemKey, $statusItem['name'])?> value="<?php echo esc_attr($statusItem['name']) ?>"><?php echo esc_html($statusItem['title']); ?></option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <option value="0"><?php esc_html_e('No any items found', 'poshtvan') ?></option>
                                <?php endif; ?>
                            </select>
                            <textarea name="mwtc_poshtvan_auto_ticket_item[content][]" placeholder="<?php echo esc_html('Ticket content', 'poshtvan') ?>"><?php echo esc_textarea($autoTicketItemContent); ?></textarea>
                            <span class="icon"><span class="dashicons dashicons-trash delete-field"></span></span>
                        </div>
                    <?php endforeach; ?>

            <?php endforeach; endif; ?>
        </div>
    </div>
    <div class="option_section" id="ticket_auto_close_field">
        <h3 class="option_section_title"><?php esc_html_e('Ticket auto close', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('Auto close ticket after a certain days', 'poshtvan') ?></p>
        <div class="option_field option_row_field">
            <p class="solid_checkbox">
                <input <?php checked(\poshtvan\app\options::isTicketAutoCloseActive())?> type="checkbox" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('poshtvan_is_ticket_auto_close_active')); ?>" id="poshtvan_is_ticket_auto_close_active" value="1">
                <label for="poshtvan_is_ticket_auto_close_active"><?php esc_html_e('Activate', 'poshtvan')?></label>
            </p>
        </div>
        <div class="option_field option_row_field" mwdeps="poshtvan_is_ticket_auto_close_active">
            <label><?php esc_html_e('Auto close after day', 'poshtvan') ?></label>
            <input type="number" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('poshtvan_auto_close_after_day')) ?>" id="ticket_auto_close_after_day" value="<?php echo esc_attr(\poshtvan\app\options::getAutoCloseDayCounter()) ?>">
        </div>
        
        <div class="option_field option_row_field" mwdeps="poshtvan_is_ticket_auto_close_active">
            <label><?php esc_html_e('Closed ticket message in user ticket panel', 'poshtvan') ?></label>
            <textarea name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('poshtvan_closed_ticket_message')) ?>" id="poshtvan_closed_ticket_message"><?php echo esc_textarea(\poshtvan\app\options::getClosedTicketMessageInUserDashboard()) ?></textarea>
        </div>
    </div>

    <div class="option_section" id="ticket_rating_fields">
        <h3 class="option_section_title"><?php esc_html_e('Ticket rating', 'poshtvan') ?></h3>
        <p class="option_section_description"><?php esc_html_e('User can rate the ticket after it is closed', 'poshtvan') ?></p>
        <div class="option_field option_row_field">
            <p class="solid_checkbox">
                <input <?php checked(\poshtvan\app\options::isTicketRatingActive())?> type="checkbox" name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('poshtvan_is_ticket_rating_active')); ?>" id="poshtvan_is_ticket_rating_active" value="1">
                <label for="poshtvan_is_ticket_rating_active"><?php esc_html_e('Activate', 'poshtvan')?></label>
            </p>
        </div>
        <div class="option_field option_row_field" mwdeps="poshtvan_is_ticket_rating_active">
            <label><?php esc_html_e('Rating section message', 'poshtvan') ?></label>
            <textarea name="<?php echo esc_attr(\poshtvan\app\options::get_setting_name('poshtvan_rating_section_message')) ?>" id="poshtvan_rating_section_message"><?php echo esc_textarea(\poshtvan\app\options::getTicketRatingSectionMessageInUserTicketPanel()) ?></textarea>
        </div>
    </div>
</form>