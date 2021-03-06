<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2017 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU AGPLv3 <https://www.gnu.org/licenses/agpl.html>
 * @link		https://github.com/timble/openpolice-platform
 */

use Nooku\Library;

/**
 * Abstract Controller Permission
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Component\Application
 */
abstract class ApplicationControllerPermissionAbstract extends Library\ControllerPermissionAbstract
{
    /**
     * Authorize handler for render actions
     *
     * @return  boolean  Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canRender()
    {
        if(parent::canRender())
        {
            $application = $this->getObject('application');
            $user        = $this->getUser();
            $request     = $this->getRequest();

            if(!($application->getCfg('offline') && !$user->isAuthentic()))
            {
                $page = $request->query->get('Itemid', 'int');

                if($this->isDispatched())
                {
                    if($this->getObject('application.pages')->isAuthorized($page, $user)) {
                        return true;
                    }
                }
                else return true;
            }
        }

        return false;
    }

    /**
     * Authorize handler for add actions
     *
     * @return  boolean  Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canAdd()
    {
        if(parent::canAdd() && $this->getUser()->getRole() > 18) {
            return true;
        }

        return false;
    }

    /**
     * Authorize handler for edit actions
     *
     * @return  boolean  Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canEdit()
    {
        if(parent::canEdit() && $this->getUser()->getRole() > 19) {
            return true;
        }

        return false;
    }

    /**
     * Authorize handler for delete actions
     *
     * @return  boolean  Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canDelete()
    {
        if(parent::canDelete() && $this->getUser()->getRole() > 20) {
            return true;
        }

        return false;
    }
}