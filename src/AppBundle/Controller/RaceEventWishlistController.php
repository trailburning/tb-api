<?php

namespace AppBundle\Controller;

use AppBundle\Model\APIResponse;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * RaceEventWishlist controller.
 */
class RaceEventWishlistController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Post(
     *     path="/user/raceevents/{id}/wishlist",
     *     summary="Add a race event to the wishlist",
     *     description="Adds a race event to the wishlist of a user.",
     *     tags={"User"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="Authorization", type="string", in="header", description="The authentication token", required=true),
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(response=201, description="Successful operation."),
     *     @SWG\Response(response="404", description="RaceEvent not found."),
     *     @SWG\Response(response="400", description="Invalid data."),
     *     @SWG\Response(
     *         response=401,
     *         description="Acecss Denied",
     *     )
     * )
     *
     * @Post("/user/raceevents/{id}/wishlist")
     *
     * @param string  $id
     * @param string  $userId
     *
     * @return APIResponse
     */
    public function postAction(string $id)
    {
        $raceEventCompletedHandler = $this->get('app.handler.race_event_wishlist');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        return $raceEventCompletedHandler->handleCreate($id, $user->getId());
    }

    /**
     * @SWG\Delete(
     *     path="/user/raceevents/{id}/wishlist",
     *     summary="Remove a race event from the wishlist",
     *     description="Removes a race event from the wishlist of a user.",
     *     tags={"User"},
     *     @SWG\Parameter(name="Authorization", type="string", in="header", description="The authentication token", required=true),
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="404", description="RaceEvent not found"),
     *     @SWG\Response(
     *         response=401,
     *         description="Acecss Denied",
     *     )
     * )
     *
     * @Delete("/user/raceevents/{id}/wishlist")
     *
     * @param string $id
     * @param string $userId
     *
     * @return APIResponse
     */
    public function deleteAction(string $id)
    {
        $raceEventCompletedHandler = $this->get('app.handler.race_event_wishlist');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        return $raceEventCompletedHandler->handleDelete($id, $user->getId());
    }
}
