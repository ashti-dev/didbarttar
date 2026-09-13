<section class="section section-dark" style="padding-top:0;">
    <div class="container">
        <div class="stats-grid">
            <?php
            $stats = [
                ['target' => 6, 'suffix' => '+', 'label' => 'سال تجربه'],
                ['target' => 500, 'suffix' => '+', 'label' => 'پروژه موفق'],
                ['target' => 24, 'suffix' => '/7', 'label' => 'پشتیبانی'],
                ['target' => 98, 'suffix' => '%', 'label' => 'رضایت مشتری'],
            ];
            foreach ($stats as $i => $s): ?>
            <div class="stat-card" data-reveal="up" data-delay="<?php echo $i * 100; ?>">
                <div class="stat-number" data-target="<?php echo $s['target']; ?>">0<span class="accent"><?php echo $s['suffix']; ?></span></div>
                <div class="stat-label"><?php echo esc_html($s['label']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>