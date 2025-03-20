<div class="event-grid-item">
    <?php $event_date = get_post_meta(get_the_ID(), '_event_date', true); ?>
    <?php $event_link = get_permalink(get_the_ID()); ?>

    <div class="event-image">
        <?php if (has_post_thumbnail()): ?>
            <a href="<?php echo esc_url($event_link); ?>">
                <?php the_post_thumbnail('medium'); ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="event-content"> <!-- New wrapper for text alignment -->
        <h3><a href="<?php echo esc_url($event_link); ?>"><?php the_title(); ?></a></h3>
        <p class="event-date"><strong>Date:</strong> <?php echo esc_html($event_date); ?></p>
        <p><?php the_excerpt(); ?></p>
    </div>
</div>
