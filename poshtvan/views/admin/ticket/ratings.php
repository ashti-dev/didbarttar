<div class="ratings" data-title="<?php echo esc_attr_e('User Satisfaction', 'poshtvan'); ?>" poshtvan-ticket-rating="<?php echo esc_attr($ticketID); ?>">
    <div class="rating-stars">
        <span class="star-item <?php echo esc_attr(sprintf('star-%s', $currentRateValue))?> active-star" data-title="<?php echo esc_attr(\poshtvan\app\tickets::getRateTitle($currentRateValue)); ?>" rate-value="<?php echo esc_attr($currentRateValue); ?>">
            <img src="<?php echo esc_url(POSHTVAN_DIR_URL . 'assets/img/star-' . $currentRateValue . '.png'); ?>" alt="<?php echo esc_attr(sprintf('%s-star', $currentRateValue)); ?>">
        </span>        
    </div>
</div>