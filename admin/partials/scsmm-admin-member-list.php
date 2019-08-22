<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e("Membership List", $this->plugin_name); ?></h1>
    <hr class="wp-header-end">
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