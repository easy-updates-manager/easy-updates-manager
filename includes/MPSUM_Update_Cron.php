<?php

if (!class_exists('MPSUM_Cron_Scheduler')) {

class MPSUM_Update_Cron {

	/**
	 * Adds a method to run when premium cron event fires
	 */
	private function __construct() {
		add_filter('cron_schedules', array($this, 'cron_schedules'));
	}

	public function cron_schedules($schedules) {
		$schedules['eum_twice_daily' ] = array( 'interval' => 86400/2, 'display' => __( 'Twice Daily', 'stops-core-theme-and-plugin-updates' ) );
		$schedules['eum_daily'] = array('interval' => 86400, 'display' => __( 'Once Daily', 'stops-core-theme-and-plugin-updates' ) );
		$schedules['eum_weekly'] = array('interval' => 86400 * 7, 'display' => __( 'Once Weekly', 'stops-core-theme-and-plugin-updates' ) );
		$schedules['eum_fortnightly'] = array('interval' => 86400 * 14, 'display' => __( 'Once Every Fortnight', 'stops-core-theme-and-plugin-updates' ) );
		$schedules['eum_monthly'] = array('interval' => 86400 * 30, 'display' => __( 'Once Every Month', 'stops-core-theme-and-plugin-updates' ) );
		return $schedules;
	}

	/**
	 * Returns singleton instance of this class
	 *
	 * @return null|MPSUM_Cron_Scheduler Singleton Instance
	 */
	public static function get_instance() {
		static $instance = null;
		if (null === $instance) {
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * Clear the WordPress crons
	 *
	 */
	 public function clear_wordpress_crons() {
		wp_clear_scheduled_hook('wp_update_plugins');
		wp_clear_scheduled_hook('wp_update_themes');
		wp_clear_scheduled_hook('wp_version_check');
	 }

	/**
	 * Returns next event timestamp
	 *
	 * @param  timestamp $event timestamp of next scheduled event
	 * @return timestamp | false
	 */
	public function cron_next_event() {
		$cron_events = get_option('cron');
		ksort($cron_events);
		$eum_cron_event = array();
		foreach ($cron_events as $timestamp => $schedule) {
			if (!is_array($schedule)) continue;
			foreach ($schedule as $key => $value) {
				if ( 'wp_update_plugins' === $key || 'wp_update_themes' === $key || 'wp_version_check' === $key ) {
					$eum_cron_event[$timestamp][$key] = $value;
				}
			}
		}

		$keys = array_keys($eum_cron_event);

		if (!empty($keys)) {
			return $keys[0];
		}
		return false;
	}

	/**
	 * Activate twice daily events
	 *
	 * @param  array   $event   Details of event
	 * @param  integer time
	 * @return void
	 */
	public function set_twice_daily_cron( $shedule, $time ) {
		$selected_schedule = "eum_twice_daily";

		$cron_schedule_user_date_time = strtotime(date("Y-m-d") . ' ' . $time);
		$gmt_offset = HOUR_IN_SECONDS * get_option('gmt_offset');
		$cron_schedule_date_time = $cron_schedule_user_date_time - $gmt_offset;

		if ($cron_schedule_date_time < time()) {
			$cron_schedule_date_time += DAY_IN_SECONDS/2;
		}
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_update_plugins' );
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_update_themes' );
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_version_check' );
	}

	/**
	 * Activate daily events
	 *
	 * @param  array   $event   Details of event
	 * @param  integer $cron_id ID of cron schedule
	 * @return void
	 */
	public function set_daily_cron( $shedule, $time ) {
		$selected_schedule = "eum_daily";

		$cron_schedule_user_date_time = strtotime(date("Y-m-d") . ' ' . $time);
		$gmt_offset = HOUR_IN_SECONDS * get_option('gmt_offset');
		$cron_schedule_date_time = $cron_schedule_user_date_time - $gmt_offset;

		if ($cron_schedule_date_time < time()) {
			$cron_schedule_date_time += DAY_IN_SECONDS;
		}
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_update_plugins' );
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_update_themes' );
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_version_check' );
	}

	/**
	 * Activate weekly events
	 *
	 * @param  array   $event   Details of event
	 * @param  integer $cron_id ID of cron schedule
	 * @return void
	 */
	public function set_weekly_cron( $schedule, $time ) {
		$selected_schedule = "eum_weekly";

		$today_day_number = date('N');
		$cron_schedule_user_date_time = strtotime(date("Y-m-d") . ' ' . $time);
		$week_offset = 7 * DAY_IN_SECONDS;
		$gmt_offset = HOUR_IN_SECONDS * get_option('gmt_offset');
		$cron_schedule_date_time = $cron_schedule_user_date_time - $gmt_offset + $week_offset;

		if ($cron_schedule_date_time < time()) {
			$cron_schedule_date_time += WEEK_IN_SECONDS;
		}
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_update_plugins' );
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_update_themes' );
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_version_check' );
	}


	/**
	 * Activate fortnightly events
	 *
	 * @param  array   $event   Details of event
	 * @param  integer $cron_id ID of cron schedule
	 * @return void
	 */
	public function set_fortnightly_cron($event, $time) {
		$selected_schedule = "eum_fortnightly";

		$cron_schedule_user_date_time = strtotime(date("Y-m-d") . ' ' . $time);
		$week_offset = 14 * DAY_IN_SECONDS;
		$gmt_offset = HOUR_IN_SECONDS * get_option('gmt_offset');
		$cron_schedule_date_time = $cron_schedule_user_date_time - $gmt_offset + $week_offset;

		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_update_plugins' );
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_update_themes' );
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_version_check' );
	}

	/**
	 * Activate monthly events
	 *
	 * @param  array   $event   Details of event
	 * @param  integer $cron_id ID of cron schedule
	 * @return void
	 */
	public function set_monthly_cron($event, $time) {
		$selected_schedule = 'eum_monthly';
		$cron_schedule_user_date_time = strtotime(date("Y-m-d") . ' ' . $time);
		$user_day_number =date("t");
		$schedule_day_number = $user_day_number;
		$month_offset = 30 * DAY_IN_SECONDS;
		$gmt_offset = HOUR_IN_SECONDS * get_option('gmt_offset');
		$cron_schedule_date_time = $cron_schedule_user_date_time - $gmt_offset + $month_offset;

		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_update_plugins' );
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_update_themes' );
		wp_schedule_event( $cron_schedule_date_time, $selected_schedule, 'wp_version_check' );
	}

}

}
