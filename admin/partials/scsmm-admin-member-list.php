<div class="wrap">
    <h2><?php _e('WP Member List', $this->plugin_name); ?></h2>

    <div id="scsmm-member-list">
        <div id="scsmm-post-body">
            <form id="scsmm-member-list-form" action="" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php
                $this->member_list_table->search_box(__('Find', $this->plugin_name), 'scsmm-member-find');
                $this->member_list_table->display();
                ?>
            </form>
        </div>
    </div>
</div>