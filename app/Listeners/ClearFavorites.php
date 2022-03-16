<?php 
namespace Favorites\Listeners;

use Favorites\Entities\Favorite\SyncAllFavorites;
use Favorites\Entities\Post\SyncFavoriteCount;

class ClearFavorites extends AJAXListenerBase
{
	/**
	* Favorites Sync
	*/
	private $favorites_sync;

	public function __construct()
	{
		parent::__construct();
		$this->favorites_sync = new SyncAllFavorites;
		$this->setFormData();
		$this->clearFavorites();
		$this->sendResponse();
	}

	/**
	* Set Form Data
	*/
	private function setFormData()
	{
		$this->data['siteid'] = intval(sanitize_text_field($_POST['siteid']));
		$this->data['old_favorites'] = $this->user_repo->formattedFavorites();
	}

	/**
	* Remove all user's favorites from the specified site
	*/
	private function clearFavorites()
	{
		$user = ( is_user_logged_in() ) ? get_current_user_id() : null;
		do_action('favorites_before_clear', $this->data['siteid'], $user);
		$favorites = $this->user_repo->getAllFavorites();
		foreach($favorites as $key => $site_favorites){
			if ( $site_favorites['site_id'] == $this->data['siteid'] ) {
				$this->updateFavoriteCounts($site_favorites);
				unset($favorites[$key]);
			}
		}
		$this->favorites_sync->sync($favorites);
		
		do_action('favorites_after_clear', $this->data['siteid'], $user);
	}

	/**
	* Update all the cleared post favorite counts
	*/
	private function updateFavoriteCounts($site_favorites)
	{
		foreach($site_favorites['posts'] as $favorite){
			$count_sync = new SyncFavoriteCount($favorite, 'inactive', $this->data['siteid']);
			$count_sync->sync();
		}
	}

	/**
	* Set and send the response
	*/
	private function sendResponse()
	{
		$favorites = $this->user_repo->formattedFavorites();
		$this->response([
			'status' => 'success',
			'old_favorites' => $this->data['old_favorites'],
			'favorites' => $favorites
		]);
	}
}
