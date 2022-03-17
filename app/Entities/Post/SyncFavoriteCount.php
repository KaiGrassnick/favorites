<?php
namespace Favorites\Entities\Post;

use Favorites\Entities\User\UserRepository;

/**
* Updates the favorite count for a given post
*/
class SyncFavoriteCount
{
	/**
	* Post ID
	* @var int
	*/
	private $post_id;

	/**
	* Site ID
	* @var int
	*/
	private $site_id;

	/**
	 * Group ID
	 * @var int
	 */
	private $group_id;

	/**
	* Status
	* @var string
	*/
	private $status;

	/**
	* Favorite Count
	* @var object
	*/
	private $favorite_count;

	/**
	* User Repository
	*/
	private $user;

	public function __construct($post_id, $status, $site_id, $group_id = null)
	{
		$this->post_id = $post_id;
		$this->status = $status;
		$this->site_id = $site_id;
		$this->group_id = $group_id;
		$this->favorite_count = new FavoriteCount;
		$this->user = new UserRepository;
	}

	/**
	* Sync the Post Total Favorites
	*/
	public function sync()
	{
		if ( !$this->user->countsInTotal() ) return;
		$count = $this->favorite_count->getCount($this->post_id, $this->site_id, $this->group_id);
		$count = ( $this->status == 'active' ) ? $count + 1 : max(0, $count - 1);

		$countArray = $this->favorite_count->getCountArray($this->post_id, $this->site_id);
		$group_id = (string)(($this->group_id !== null) ? $this->group_id : "1");
		$countArray[$group_id] = $count;

		update_post_meta($this->post_id, 'simplefavorites_count_array', $countArray);
		update_post_meta($this->post_id, 'simplefavorites_count', $this->favorite_count->getTotalCount($this->post_id, $this->site_id));
	}
}
