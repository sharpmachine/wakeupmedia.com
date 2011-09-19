<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php 
if ( is_network_admin() )
	$post_types = get_site_option('ct_custom_post_types');
else 
	$post_types = $this->post_types;
?>

<?php $this->render_admin('update-message'); ?>

<form action="" method="post" class="ct-form-single-btn">
    <input type="submit" class="button-secondary" name="redirect_add_post_type" value="<?php _e('Add Post Type', $this->text_domain); ?>" />
</form>

<table class="widefat">
    <thead>
        <tr>
            <th><?php _e('Post Type', $this->text_domain); ?></th>
            <th><?php _e('Name', $this->text_domain); ?></th>
            <th><?php _e('Description', $this->text_domain); ?></th>
            <th><?php _e('Menu Icon', $this->text_domain); ?></th>
            <th><?php _e('Supports', $this->text_domain); ?></th>
            <th><?php _e('Capability Type', $this->text_domain); ?></th>
            <th><?php _e('Public', $this->text_domain); ?></th>
            <th><?php _e('Hierarchical', $this->text_domain); ?></th>
            <th><?php _e('Rewrite', $this->text_domain); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th><?php _e('Post Type', $this->text_domain); ?></th>
            <th><?php _e('Name', $this->text_domain); ?></th>
            <th><?php _e('Description', $this->text_domain); ?></th>
            <th><?php _e('Menu Icon', $this->text_domain); ?></th>
            <th><?php _e('Supports', $this->text_domain); ?></th>
            <th><?php _e('Capability Type', $this->text_domain); ?></th>
            <th><?php _e('Public', $this->text_domain); ?></th>
            <th><?php _e('Hierarchical', $this->text_domain); ?></th>
            <th><?php _e('Rewrite', $this->text_domain); ?></th>
        </tr>
    </tfoot>
    <tbody>
        <?php if ( !empty( $post_types )): ?>
            <?php $i = 0; foreach ( $post_types as $name => $post_type ): ?>
            <?php $class = ( $i % 2) ? 'ct-edit-row alternate' : 'ct-edit-row'; $i++; ?>
            <tr class="<?php echo $class; ?>">
                <td>
                    <strong>
                        <a href="<?php echo( self_admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=post_type&ct_edit_post_type=' . $name ) ); ?>"><?php echo $name; ?></a>
                    </strong>
                    <div class="row-actions" id="row-actions-<?php echo $name; ?>">
                        <span class="edit">
                            <a title="<?php _e('Edit the post type', $this->text_domain); ?>" href="<?php echo self_admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=post_type&ct_edit_post_type=' . $name ); ?>"><?php _e('Edit', $this->text_domain); ?></a> |
                        </span>
                        <span class="trash">
                            <a class="submitdelete" href="#" onclick="javascript:content_types.toggle_delete('<?php echo( $name ); ?>'); return false;"><?php _e('Delete', $this->text_domain); ?></a>
                        </span>
                    </div>
                    <form action="" method="post" id="form-<?php echo( $name ); ?>" class="del-form">
                        <?php wp_nonce_field('delete_post_type'); ?>
                        <input type="hidden" name="post_type_name" value="<?php echo( $name ); ?>" />
                        <input type="submit" class="button confirm" value="<?php _e( 'Confirm', $this->text_domain ); ?>" name="submit" />
                        <input type="submit" class="button cancel"  value="<?php _e( 'Cancel', $this->text_domain ); ?>" onClick="content_types.cancel('<?php echo( $name ); ?>'); return false;" />
                    </form>
                </td>
                <td><?php if ( isset( $post_type['labels']['name'] ) ) echo $post_type['labels']['name']; ?></td>
                <td><?php if ( isset( $post_type['description'] ) ) echo $post_type['description']; ?></td>
                <td>
                    <img src="<?php if ( isset( $post_type['menu_icon'] ) ) echo $post_type['menu_icon']; else echo $this->plugin_url . 'ui-admin/images/default-menu-icon.png'; ?>" alt="<?php if ( empty( $post_type['menu_icon'] ) ) echo( 'No Icon'); ?>" />
                </td>
                <td class="ct-supports">
                    <?php foreach ( $post_type['supports'] as $value ): ?>
                        <?php echo( $value ); ?>
                    <?php endforeach; ?>
                </td>
                <td><?php echo( $post_type['capability_type'] ); ?></td>
                <td class="ct-tf-icons-wrap">
                    <?php if ( $post_type['public'] === NULL ): ?>
                        <img class="ct-tf-icons" src="<?php echo $this->plugin_url . 'ui-admin/images/advanced.png'; ?>" alt="<?php _e('Advanced', $this->text_domain); ?>" title="<?php _e('Advanced', $this->text_domain); ?>" />
                    <?php elseif ( $post_type['public'] ): ?>
                        <img class="ct-tf-icons" src="<?php echo $this->plugin_url . 'ui-admin/images/true.png'; ?>" alt="<?php _e('True', $this->text_domain); ?>" title="<?php _e('True', $this->text_domain); ?>" />
                    <?php else: ?>
                        <img class="ct-tf-icons" src="<?php echo $this->plugin_url . 'ui-admin/images/false.png'; ?>" alt="<?php _e('False', $this->text_domain); ?>" title="<?php _e('False', $this->text_domain); ?>" />
                    <?php endif; ?>
                </td>
                <td class="ct-tf-icons-wrap">
                    <?php if ( $post_type['hierarchical'] ): ?>
                        <img class="ct-tf-icons" src="<?php echo $this->plugin_url . 'ui-admin/images/true.png'; ?>" alt="<?php _e('True', $this->text_domain); ?>" title="<?php _e('True', $this->text_domain); ?>" />
                    <?php else: ?>
                        <img class="ct-tf-icons" src="<?php echo $this->plugin_url . 'ui-admin/images/false.png'; ?>" alt="<?php _e('False', $this->text_domain); ?>" title="<?php _e('False', $this->text_domain); ?>" />
                    <?php endif; ?>
                </td>
                <td class="ct-tf-icons-wrap">
                    <?php if ( $post_type['rewrite'] ): ?>
                        <img class="ct-tf-icons" src="<?php echo $this->plugin_url . 'ui-admin/images/true.png'; ?>" alt="<?php _e('True', $this->text_domain); ?>" title="<?php _e('True', $this->text_domain); ?>" />
                    <?php else: ?>
                        <img class="ct-tf-icons" src="<?php echo $this->plugin_url . 'ui-admin/images/false.png'; ?>" alt="<?php _e('False', $this->text_domain); ?>" title="<?php _e('False', $this->text_domain); ?>" />
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<form action="" method="post" class="ct-form-single-btn">
    <input type="submit" class="button-secondary" name="redirect_add_post_type" value="<?php _e('Add Post Type', $this->text_domain); ?>" />
</form>
