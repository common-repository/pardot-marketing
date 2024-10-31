<?php
/**
 * Pardot forms table
 *
 * @package PardotMarketing
 * @since 1.1.0
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class PardotMarketing_Forms_Table extends WP_List_Table {
  function __construct() {
    global $status, $page;

    $args = [
      'singular'  => __( 'Pardot Form', 'pardotmarketing' ),
      'plural'    => __( 'Pardot Forms', 'pardotmarketing' ),
      'ajax'      => false
    ];
    parent::__construct( $args );
  }

  // Register columns
  function get_columns() {
    // Render a checkbox instead of text
    $columns = [
      //'cb'          => '<input type="checkbox" />',
      'id'          => __( 'ID', 'pardotmarketing' ),
      'name'        => __( 'Name', 'pardotmarketing' ),
      'campaign'    => __( 'Campaign', 'pardotmarketing' ),
      //'crm_fid'     => __( 'CRM Field ID', 'pardotmarketing' ),
      'created_at'  => __( 'Created', 'pardotmarketing' ),
      'updated_at'  => __( 'Updated', 'pardotmarketing' ),
      'embed_code'  => __( 'Embed Code', 'pardotmarketing' ),
    ];

    return $columns;
  }

  // Sortable columns
  function get_sortable_columns() {
    $sortable_columns = [
      'id'               => [ 'id', false ],
      'created_at'       => [ 'created_at', false ],
      'updated_at'       => [ 'updated_at', false ],
    ];

    return $sortable_columns;
  }

  // Checkbox column
  function column_cb( $item ) {
    return sprintf(
        '<input type="checkbox" name="%1$s[]" value="%2$s" />',
        /*$1%s*/ 'ids',
        /*$2%s*/ $item['prospect_id']
    );
  }

  // Render column
  function column_default( $item, $column_name ) {
    switch( $column_name ) {
      case 'id':
        return '<a href="https://pi.pardot.com/form/read/id/' . $item['id'] . '" target="_blank" rel="noreferrer noopener">' . $item['id'] . '</a>';
      break;
      case 'embed_code':
        ob_start();
        ?>
        <button class="button pardotmarketing-modal-trigger" data-id="<?php echo $item['id']; ?>"><?php _e( 'View Embed', 'pardotmarketing' ); ?></button>
        <div class="pardotmarketing-modal" id="pardotmarketing-modal-<?php echo $item['id']; ?>">
          <div class="pardotmarketing-modal-inside">
            <?php echo $item['embedCode']; ?>
            <label><?php _e( 'Embed Code', 'pardotmarketing' ); ?></label>
            <textarea class="pardotmarket-textarea code" readonly><?php echo $item['embedCode']; ?></textarea>
          </div>
        </div>
        <?php
        return ob_get_clean();
      break;
      case 'crm_fid':
        return $item['crm_fid'];
      break;
      case 'name':
        return $item['name'];
      break;
      case 'campaign':
        $campaign = '';

        if ( ! empty( $item['campaign']['name'] ) ) {
          $campaign = $item['campaign']['name'];
        }

        if ( ! empty( $item['campaign']['id'] ) ) {
          $campaign .= ' (' . $item['campaign']['id'] . ')';
        }
        return $campaign;
      break;
      case 'created_at':
        return date( 'M j, Y g:ia', $item['created_at'] );
      break;
      case 'updated_at':
        return date( 'M j, Y g:ia', $item['updated_at'] );
      break;
    }
  }

  // Register bulk actions
  function get_bulk_actions() {
    //$actions = [ 'delete' => __( 'Delete', 'pardotmarketing' ) ];
    $actions = [];

    return $actions;
  }

  /**
   * Define which columns are hidden
   *
   * @return Array
   */
  public function get_hidden_columns() {
    return [];
  }

  function extra_tablenav( $which ) {
    global $cat_id;

    if ( 'top' !== $which ) {
      return;
    }
    ?>
    <div class="alignleft actions">
      <?php
      echo '<label class="screen-reader-text" for="filter-by-assigned">' . __( 'Filter by assigned' ) . '</label>';
      $current_created_after  = ! empty( $_REQUEST['created_after'] ) ? sanitize_text_field( $_REQUEST['created_after'] ) : false;
      $current_created_before = ! empty( $_REQUEST['created_before'] ) ? sanitize_text_field( $_REQUEST['created_before'] ) : false;
      ?>
      <select name="created_after" id="filter-by-created-after">
        <option value=""><?php _e( '- Created after -', 'wpzerospam' ); ?></option>
        <option<?php if ( $current_created_after == 'today' ): ?> selected="selected" <?php endif; ?> value="today"><?php _e( 'Today', 'wpzerospam' ); ?></option>
        <option<?php if ( $current_created_after == 'yesterday' ): ?> selected="selected" <?php endif; ?> value="yesterday"><?php _e( 'Yesterday', 'wpzerospam' ); ?></option>
        <option<?php if ( $current_created_after == 'last_7_days' ): ?> selected="selected" <?php endif; ?> value="last_7_days"><?php _e( 'Last 7 Days', 'wpzerospam' ); ?></option>
        <option<?php if ( $current_created_after == 'this_month' ): ?> selected="selected" <?php endif; ?> value="this_month"><?php _e( 'This Month', 'wpzerospam' ); ?></option>
        <option<?php if ( $current_created_after == 'last_month' ): ?> selected="selected" <?php endif; ?> value="last_month"><?php _e( 'Last Month', 'wpzerospam' ); ?></option>
      </select>

      <select name="created_before" id="filter-by-created-before">
        <option value=""><?php _e( '- Created before -', 'wpzerospam' ); ?></option>
        <option<?php if ( $current_created_before == 'today' ): ?> selected="selected" <?php endif; ?> value="today"><?php _e( 'Today', 'wpzerospam' ); ?></option>
        <option<?php if ( $current_created_before == 'yesterday' ): ?> selected="selected" <?php endif; ?> value="yesterday"><?php _e( 'Yesterday', 'wpzerospam' ); ?></option>
        <option<?php if ( $current_created_before == 'last_7_days' ): ?> selected="selected" <?php endif; ?> value="last_7_days"><?php _e( 'Last 7 Days', 'wpzerospam' ); ?></option>
        <option<?php if ( $current_created_before == 'this_month' ): ?> selected="selected" <?php endif; ?> value="this_month"><?php _e( 'This Month', 'wpzerospam' ); ?></option>
        <option<?php if ( $current_created_before == 'last_month' ): ?> selected="selected" <?php endif; ?> value="last_month"><?php _e( 'Last Month', 'wpzerospam' ); ?></option>
      </select>
      <?php
      submit_button( __( 'Filter' ), '', 'filter_action', false );
      ?>
    </div>
    <?php
  }

  // Get results
  function prepare_items($args = []) {
    $this->process_bulk_action();

    $columns  = $this->get_columns();
    $hidden   = $this->get_hidden_columns();
    $sortable = $this->get_sortable_columns();

    $per_page     = 50;
    $current_page = $this->get_pagenum();
    $offset       = $per_page * $current_page;
    $order        = ! empty( $_REQUEST['order'] ) ? sanitize_text_field( $_REQUEST['order'] ) : 'desc';
    $orderby      = ! empty( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'created_at';

    $query_args = [
      'limit'      => $per_page,
      'offset'     => $offset,
      'sort_order' => ( $order == 'asc' ) ? 'ascending' : 'descending',
      'sort_by'    => $orderby,
    ];

    // Filter by created_after
    $created_after = ! empty( $_REQUEST['created_after'] ) ? sanitize_text_field( $_REQUEST['created_after'] ) : false;
    if ( $created_after ) {
      $query_args['created_after'] = $created_after;
    }

    // Filter by created_before
    $created_before = ! empty( $_REQUEST['created_before'] ) ? sanitize_text_field( $_REQUEST['created_before'] ) : false;
    if ( $created_before ) {
      $query_args['created_before'] = $created_before;
    }

    // Fix for passing filters to paging
    // @link https://wordpress.stackexchange.com/questions/67669/how-to-stop-wpnonce-and-wp-http-referer-from-appearing-in-url/185006#185006
    $paging_options = $query_args;
    unset( $paging_options['offset'] );
    $_SERVER['REQUEST_URI'] = add_query_arg( $paging_options, $_SERVER['REQUEST_URI'] );

    $data = pardotmarketing_get_forms( $query_args );
    if ( ! $data ) { return false; }

    $forms = $data['forms'];

    $total_items = $data['total_results'];

    $this->set_pagination_args([
      'total_items' => $total_items,
      'per_page'    => $per_page,
      'total_pages'	=> ceil( $total_items / $per_page ),
      'orderby'	    => $orderby,
			'order'		    => $order
    ]);

    $this->_column_headers = [ $columns, $hidden, $sortable ];
    $this->items           = $forms;
  }

  // Process bulk actions
  function process_bulk_action() {
    global $wpdb;

    /*$ids = ( isset( $_REQUEST['ids'] ) ) ? $_REQUEST['ids'] : '';

    switch( $this->current_action() ) {
      // Delete
      case 'delete':
        $nonce = ( isset( $_REQUEST['pardotmarketing_nonce'] ) ) ? $_REQUEST['pardotmarketing_nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, 'pardotmarketing_nonce' ) ) return false;

        if ( ! empty ( $ids ) && is_array( $ids ) ) {
          // Delete query
          foreach( $ids as $k => $referrer_id ) {
            $wpdb->delete( $wpdb->prefix . 'referrer_analytics', [ 'referrer_id' => $referrer_id  ] );
          }
        }
      break;
    }*/
  }
}
