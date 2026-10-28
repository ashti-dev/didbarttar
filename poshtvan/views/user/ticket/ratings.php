<div class="ratings" handle-rating-click="true" data-title="<?php echo esc_attr(\poshtvan\app\options::getTicketRatingSectionMessageInUserTicketPanel()); ?>" poshtvan-ticket-rating="<?php echo esc_attr($ticketID); ?>">
    <div class="rating-stars">
    <?php foreach(\poshtvan\app\tickets::getRateList() as $rateValue => $rateData): ?>
        <span class="star-item <?php echo esc_attr(sprintf('star-%s', $rateValue)); ?> <?php echo esc_attr($currentRateValue == $rateValue ? 'active-star' : ''); ?>" data-title="<?php echo esc_attr($rateData['title']); ?>" rate-value="<?php echo esc_attr($rateValue); ?>">
            <img src="<?php echo esc_url(POSHTVAN_DIR_URL . 'assets/img/' . $rateData['icon']); ?>" alt="<?php echo esc_attr(sprintf('%s-star', $rateValue)); ?>">
            <span class="star-animation-wrapper">
                <?php for($i = 1; $i <= 3; $i++): ?>
                    <img src="<?php echo esc_url(POSHTVAN_DIR_URL . 'assets/img/' . $rateData['icon']); ?>">
                <?php endfor; ?>
            </span>
        </span>
    <?php endforeach; ?>
    </div>
</div>