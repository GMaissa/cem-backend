<?php
/**
 * File part of the VirtualMachine Dashboard
 *
 * @category  CEM
 * @package   CEM.Domain.VirtualMachine
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace CEM\Ui\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use CEM\Domain\Bookmark\Exception\VmAlreadyBookmarkedException;
use CEM\Domain\Bookmark\Exception\VmBookmarkNotFoundException;
use CEM\Domain\Bookmark\Factory\VmBookmarkFactoryInterface;
use CEM\Domain\Bookmark\Repository\VmBookmarkRepositoryInterface;
use CEM\Domain\VirtualMachine\Exception\VmNotFoundException;
use CEM\Domain\VirtualMachine\Repository\VmRepositoryInterface;
use CEM\Ui\ApiBundle\Model\ApiError;

/**
 * Bookmarks controller.
 */
class VmBookmarksController extends FOSRestController
{
    /**
     * Add virtual machine to user's bookmarks
     *
     * @ApiDoc(
     *     section="Virtual machines user bookmarks",
     *     requirements={
     *         {
     *             "name"="vmId",
     *             "dataType"="string",
     *             "requirement"="i-[a-z0-9]+",
     *             "description"="Vm Id to be added to user's bookmarks"
     *         }
     *     },
     *     statusCodes={
     *         Response::HTTP_CREATED = "Virtual machine added to bookmarks",
     *         Response::HTTP_BAD_REQUEST = "Unvalid virtual machine id",
     *         Response::HTTP_NOT_FOUND = "No virtual machine found with provided id",
     *         Response::HTTP_CONFLICT = "Virtual machine already in bookmarks"
     *     },
     *     responseMap={
     *         Response::HTTP_BAD_REQUEST = {"class"=ApiError::class},
     *         Response::HTTP_NOT_FOUND = {"class"=ApiError::class},
     *         Response::HTTP_CONFLICT = {"class"=ApiError::class}
     *     }
     * )
     * @Rest\View(
     *     statusCode=201
     * )
     * @Rest\Post("/vms/bookmarks", name="_vm_bookmark")
     *
     * @param Request $request the request object
     */
    public function postAction(Request $request)
    {
        /* @var VmBookmarkRepositoryInterface $vmBookmarkRepo */
        $vmBookmarkRepo = $this->get("cem_bookmark.bookmark.repository");
        $vmId           = $request->get('vmId');
        if (!$vmId) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST, 'Unvalid virtual machine id'
            );
        }
        /* @var VmRepositoryInterface $vmRepo */
        $vmRepo = $this->get("cem_virtual_machine.vm.repository");

        /* @var VmBookmarkFactoryInterface $bookmarkFactory */
        $bookmarkFactory = $this->get("cem_bookmark.bookmark.factory");

        try {
            $vm       = $vmRepo->find($vmId);
            $user     = $this->getUser();
            $bookmark = $bookmarkFactory->build($user, $vm);
            $vmBookmarkRepo->save($bookmark);
        } catch (VmNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (VmAlreadyBookmarkedException $e) {
            throw new HttpException(Response::HTTP_CONFLICT, $e->getMessage());
        }
    }

    /**
     * Remove virtual machine from user's bookmarks
     *
     * @ApiDoc(
     *     section="Virtual machines user bookmarks",
     *     requirements={
     *         {
     *             "name"="vmId",
     *             "dataType"="string",
     *             "description"="Virtual machine Id to be deleted from user's bookmarks"
     *         }
     *     },
     *     statusCodes={
     *         204 = "Virtual machine removed from user's bookmarks",
     *         404 = "No virtual machine found with provided id in user's bookmarks"
     *     },
     *     responseMap={
     *         404 = {"class"=ApiError::class}
     *     }
     * )
     * @Rest\View()
     * @Rest\Delete("/vms/bookmarks/{vmId}", name="_vm_bookmark")
     *
     * @param string $vmId the virtual machine id to remove from user's bookmarks
     */
    public function deleteAction($vmId)
    {
        /* @var VmBookmarkRepositoryInterface $vmBookmarkRepo */
        $vmBookmarkRepo = $this->get("cem_bookmark.bookmark.repository");
        /* @var VmRepositoryInterface $vmRepo */
        $vmRepo = $this->get("cem_virtual_machine.vm.repository");

        try {
            $vm       = $vmRepo->find($vmId);
            $user     = $this->getUser();
            $bookmark = $vmBookmarkRepo->findVmBookmarked($user, $vm);
            $vmBookmarkRepo->delete($bookmark);
        } catch (VmBookmarkNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        }
    }

    /**
     * Get user's virtual machine bookmarks
     *
     * Response format :
     *
     *     [
     *       {
     *         "id": "i-XXXXXXXX",
     *         "name": "Test",
     *         "type": "t2.micro",
     *         "state": "stopped",
     *         "az": "eu-west-2",
     *         "privateip": "127.0.0.1",
     *         "publicip": "",
     *         "mailinglist": [
     *           "xxx@xxxxxx.xx"
     *         ],
     *         "keepalive": false,
     *         "autostart": true,
     *         "isinbookmarks": false
     *       }
     *     ]
     *
     * @ApiDoc(
     *     section="Virtual machines user bookmarks",
     *     statusCodes={
     *         200 = "Success",
     *         204 = "No virtual machine found"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/vms/bookmarks", name="_vm_bookmarks" )
     *
     * @return mixed
     */
    public function getAction()
    {
        /* @var VmBookmarkRepositoryInterface $vmBookmarkRepo */
        $vmBookmarkRepo = $this->get("cem_bookmark.bookmark.repository");
        /* @var VmRepositoryInterface $vmRepo */
        $vmRepo = $this->get("cem_virtual_machine.vm.repository");
        $user   = $this->getUser();

        $bookmarks = $vmBookmarkRepo->findAllByUser($user);
        if (count($bookmarks) == 0) {
            return null;
        }

        $bookmarkIds = [];
        foreach ($bookmarks as $bookmark) {
            $bookmarkIds[] = $bookmark->getVmId();
        }
        $bookmarkedVms = $vmRepo->findBy(['vmIds' => $bookmarkIds]);

        return (count($bookmarkedVms)) ? $bookmarkedVms : null;
    }
}
