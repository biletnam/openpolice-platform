<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2017 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU AGPLv3 <https://www.gnu.org/licenses/agpl.html>
 * @link		https://github.com/timble/openpolice-platform
 */

namespace Nooku\Component\Tags;

use Nooku\Library;

/**
 * Tag Database Row
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Nooku\Component\Tag
 */
class DatabaseRowTag extends Library\DatabaseRowTable
{
	/**
	 * Deletes the tag form the database.
	 *
	 * If only one relationship exists in the actual tag will also be deleted. Otherwise only the relation will be
     * removed.
	 *
	 * @return DatabaseRowTag
	 */
	public function delete()
	{
		//Delete the tag
		$relation = $this->getObject('com:tags.database.row.relation');
		$relation->tags_tag_id = $this->id;

		if($relation->count() <= 1) {
			parent::delete();
		}

		//Delete the relation
		if($this->row && $this->table)
 		{
			$relation = $this->getObject('com:tags.database.row.relation', array('status' => Database::STATUS_LOADED));
			$relation->tags_tag_id = $this->id;
	   		$relation->row		   = $this->row;
			$relation->table	   = $this->table;
			$relation->delete();
 		}

		return true;
	}
}