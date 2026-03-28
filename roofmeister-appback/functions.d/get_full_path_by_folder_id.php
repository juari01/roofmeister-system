<?php

	function get_full_path_by_folder_id( $folder_id, $separator = '/' ) {
	/**
	 * Returns the full path, including parent folders, given a folder ID.
	 *
	 * @param int    folder_id - The ID of the inner-most folder.
	 * @param string separator - The separator to use between each folder name.
	 *
	 * @return string - The full path
	 */

		$breadcrumbs = folder_breadcrumbs( $folder_id );

		$path = '';

		foreach ( $breadcrumbs as $folder ) {
			$path = $separator . $folder['name'] . $path;
		}

		return $path;
	}

?>
