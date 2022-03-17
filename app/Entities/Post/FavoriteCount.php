<?php
namespace Favorites\Entities\Post;

/**
* Returns the total number of favorites for a post
*/
class FavoriteCount
{
	/**
	 * @param $post_id
	 * @param $site_id
	 * @param $group_id
	 * @return int
	 */
	public function getCount($post_id, $site_id = null, $group_id = null)
	{
		if ($group_id === null) {
			$group_id = "1";
		}

		$countArray = $this->getCountArray($post_id, $site_id);

		$count = 0;
		if (isset($countArray[$group_id])) {
			$count = $countArray[$group_id];
		}

		return intval($count);
	}

	/**
	 * @param $post_id
	 * @param $site_id
	 * @return int
	 */
	public function getTotalCount($post_id, $site_id = null)
	{
		$countArray = $this->getCountArray($post_id, $site_id);

		$count = 0;
		foreach ($countArray as $countEntry) {
			$count += intval($countEntry);
		}

		return $count;
	}

	/**
	 * @param $post_id
	 * @param $site_id
	 * @return array
	 */
	public function getCountArray($post_id, $site_id = null)
	{
		if ( (is_multisite()) && (isset($site_id)) && ($site_id !== "") ) switch_to_blog(intval($site_id));
		$countArray = get_post_meta($post_id, 'simplefavorites_count_array', true);
		if ( (is_multisite()) && (isset($site_id) && ($site_id !== "")) ) restore_current_blog();
		if ( !is_array($countArray) ) {
			$countArray = [];
		}
		if ( isset($countArray[0]) ) {
			unset($countArray[0]);
		}
		return $countArray;
	}

	/**
	* Get the favorite count for all posts in a site
	*/
	public function getAllCount($site_id = null)
	{
		if ( (is_multisite()) && (isset($site_id)) && ($site_id !== "") ) switch_to_blog(intval($site_id));
		global $wpdb;
		$query = "SELECT SUM(meta_value) AS favorites_count FROM {$wpdb->prefix}postmeta WHERE meta_key = 'simplefavorites_count'";
		$count = $wpdb->get_var( $query );
		if ( (is_multisite()) && (isset($site_id) && ($site_id !== "")) ) restore_current_blog();
		return intval($count);
	}
}
