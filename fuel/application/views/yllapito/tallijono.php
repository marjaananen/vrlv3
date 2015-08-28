<h2>Tallijono</h2>

<?php if($this->session->flashdata('return_status') != '') : ?>
    <div class="alert alert-<?php echo $this->session->flashdata('return_status'); ?>" role="alert">
        <p>
            <?php echo $this->session->flashdata('return_info'); ?>
        </p>
    </div>
<?php endif; ?>

<?php if ($view_status === 'queue_status') : ?>

    <?php echo $queue_status_html; ?>

<?php elseif ($view_status === 'next_queue_item'): ?>
    
    <?php echo $queue_item_html; ?>

<?php endif; ?>
