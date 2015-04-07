<?php
namespace Wave\Framework\Adapters\Link;

use \Wave\Framework\Common\Link;

/**
 * Interface Linkable
 *
 * @package Wave\Framework\Adapters\Link
 *
 * Defines the methods which should be implemented by each Linkable object
 * also only objects implementing this class are allowed to be pushed to a
 * link.
 */
interface Linkable
{
    /**
     * Performs a transparent update on all links, only the Destination objects
     * should be aware of the linkages (although it is optional, it is recommended).
     *
     * @return mixed Triggers the Link::notify so that the destinations are
     * updated and perform the necessary actions.
     */
    public function notify();

    /**
     * Pushes the link in the object, so that the object can independently
     * trigger the update process.
     *
     * @param \Wave\Framework\Common\Link $link
     * @return mixed
     */
    public function addLink(Link $link);

    /**
     * @return mixed Cloned instance of the object which is being transparent
     */
    public function getState();
}
