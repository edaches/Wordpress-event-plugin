<li class="event-list-item">
    <?php $event_date = get_post_meta(get_the_ID(), '_event_date', true); ?>
    <?php $event_link = get_permalink(get_the_ID()); ?>

    <!-- Clickable event image -->
    <?php if (has_post_thumbnail()): ?>
        <a href="<?php echo esc_url($event_link); ?>">
            <?php the_post_thumbnail('medium'); ?>
        </a><br>
    <?php endif; ?>

    <!-- Clickable event title -->
    <h3><a href="<?php echo esc_url($event_link); ?>"><?php the_title(); ?></a></h3>

    <!-- Event date -->
    <p class="event-date"><strong>Date:</strong> <?php echo esc_html($event_date); ?></p>
    
    <!-- Event description -->
    <p><?php the_excerpt(); ?></p>
</li>
