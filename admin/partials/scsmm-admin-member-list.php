
<div class="wrap">
    <h2><?php _e('WP Member List', $this->plugin_name); ?></h2>

    <div id="scsmm-member-list">
        <div id="scsmm-post-body">
            <form id="scsmm-member-list-form" action="" method="get">
                <?php $this->member_list_table->display(); ?>
            </form>
        </div>
    </div>
</div>
