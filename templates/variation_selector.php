<?php if ( isset( $available_variations ) && !empty( $available_variations ) ) : ?>
    <div class="afq-variation-selector">
        <select id="afq-variation">
            <option value=""><?php _e('Select an option', 'text-domain'); ?></option>
            <?php foreach ( $available_variations as $variation ) : ?>
                <option value="<?php echo esc_attr( $variation['variation_id'] ); ?>">
                    <?php echo esc_html( implode( ' / ', array_values( $variation['attributes'] ) ) ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
<?php endif; ?>