<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

/**
 * Class WP_Optimization_trash
 */
class WP_Optimization_trash extends WP_Optimization {

	public $available_for_auto = true;

	public $auto_default = true;

	public $setting_default = true;

	public $available_for_saving = true;
	
	public $ui_sort_order = 3010;

	protected $setting_id = 'trash';

	protected $auto_id = 'trash';

	/**
	 * Do actions after optimize() function.
	 */
	public function after_optimize() {
		$message = sprintf(_n('%s post removed from Trash', '%s posts removed from Trash', $this->processed_count, 'wp-optimize'), number_format_i18n($this->processed_count));

		if ($this->is_multisite_mode()) {
			$message .= ' ' . sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->logger->info($message);
		$this->register_output($message);
	}

	/**
	 * Do optimization.
	 */
	public function optimize() {

		$remove_ids_sql = "SELECT ID FROM `" . $this->wpdb->posts . "` WHERE post_status = 'trash'";

		if ('true' == $this->retention_enabled) {
			$remove_ids_sql .= ' AND post_modified < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}

		// get trashed post ids.
		$post_remove_ids = $this->wpdb->get_col($remove_ids_sql);

		// remove related data for trashed posts.
		if (!empty($post_remove_ids)) {
			$post_remove_ids = join(',', $post_remove_ids);

			// remove related postmeta.
			$clean = "DELETE FROM `" . $this->wpdb->postmeta . "` WHERE post_id IN (" . $post_remove_ids . ");";
			$this->wpdb->query($clean);

			// remove related term relationships.
			$clean = "DELETE FROM `" . $this->wpdb->term_relationships . "` WHERE object_id IN (" . $post_remove_ids . ");";
			$this->wpdb->query($clean);

			// remove related comments and commentmeta.
			$clean = "DELETE c, cm FROM `" . $this->wpdb->comments . "` c LEFT JOIN `" . $this->wpdb->commentmeta . "` cm ON c.comment_ID = cm.comment_id WHERE c.comment_post_ID IN (" . $post_remove_ids . ");";
			$this->wpdb->query($clean);
		}

		$clean = "DELETE FROM `" . $this->wpdb->posts . "` WHERE post_status = 'trash'";

		if ('true' == $this->retention_enabled) {
			$clean .= ' AND post_modified < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}

		$clean .= ';';

		// remove trashed posts.
		$posttrash = $this->query($clean);
		$this->processed_count += $posttrash;

	}

	/**
	 * Do actions after get_info() function.
	 */
	public function after_get_info() {
		if ($this->found_count > 0) {
			$message = sprintf(_n('%s trashed post in your database', '%s trashed posts in your database', $this->found_count, 'wp-optimize'), number_format_i18n($this->found_count));
		} else {
			$message = __('No trashed posts found', 'wp-optimize');
		}

		if ($this->is_multisite_mode()) {
			$message .= ' ' . sprintf(_n('across %s site', 'across %s sites', count($this->blogs_ids), 'wp-optimize'), count($this->blogs_ids));
		}

		$this->register_output($message);
	}

	/**
	 * Return info about not optimized records
	 */
	public function get_info() {

		$sql = "SELECT COUNT(*) FROM `" . $this->wpdb->posts . "` WHERE post_status = 'trash'";
		if ('true' == $this->retention_enabled) {
			$sql .= ' and post_modified < NOW() - INTERVAL ' . $this->retention_period . ' WEEK';
		}
		$sql .= ';';

		$trash = $this->wpdb->get_var($sql);
		$this->found_count += $trash;

	}

	/**
	 * Return settings label
	 *
	 * @return string|void
	 */
	public function settings_label() {

		if ('true' == $this->retention_enabled) {
			return sprintf(__('Clean trashed posts which are older than %d weeks', 'wp-optimize'), $this->retention_period);
		} else {
			return __('Clean all trashed posts', 'wp-optimize');
		}
	}

	/**
	 * Return description
	 *
	 * @return string|void
	 */
	public function get_auto_option_description() {
		return __('Remove trashed posts', 'wp-optimize');
	}
}
