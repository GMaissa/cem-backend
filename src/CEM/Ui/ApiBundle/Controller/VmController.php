<?php
/**
 * File part of the Cloud Environments Management Backend
 *
 * @category  CEM
 * @package   CEM.UI.ApiBundle
 * @author    Guillaume Maïssa <pro.g@maissa.fr>
 * @copyright 2017 Guillaume Maïssa
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CEM\Ui\ApiBundle\Controller;

use CEM\Domain\VirtualMachine\Model\VirtualMachineInterface;
use CEM\Ui\ApiBundle\Model\ApiError;
use CEM\Domain\VirtualMachine\Exception\VmNotFoundException;
use CEM\Domain\VirtualMachine\Exception\VmStateUpdateException;
use CEM\Domain\VirtualMachine\Repository\VmRepositoryInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Virtual Machine controller.
 */
class VmController extends FOSRestController
{
    /**
     * Get all development virtual machines
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
     *           "xxx@xxx.com"
     *         ],
     *         "keepalive": false,
     *         "autostart": true,
     *         "isinbookmarks": false
     *       }
     *     ]
     *
     * @ApiDoc(
     *     section="Virtual machines",
     *     statusCodes={
     *         Response::HTTP_OK = "Success",
     *         Response::HTTP_NO_CONTENT = "No virtual machine found"
     *     },
     *     output= { "class"=VirtualMachineInterface::class, "collection"=true, "groups"={"list"} }
     * )
     * @Rest\View()
     */
    public function getVmsAction()
    {
        /* @var VmRepositoryInterface $vmRepository */
        $vmRepository = $this->get("cem_virtual_machine.vm.repository");
        $vms          = $vmRepository->findBy(['vmTypes' => ['development']]);

        return (count($vms) ? $vms : null);
    }

    /**
     * Update a Virtual Machine state
     *
     * @ApiDoc(
     *     section="Virtual machines",
     *     requirements={
     *         {
     *             "name"="vmId",
     *             "dataType"="string",
     *             "requirement"="i-[a-z0-9]+",
     *             "description"="Virtual machine Id for which state should be updated"
     *         },
     *         {
     *             "name"="action",
     *             "dataType"="string",
     *             "requirement"="start|stop",
     *             "description"="Action to execute for the vm"
     *         }
     *     },
     *     statusCodes={
     *         Response::HTTP_OK = "State updated",
     *         Response::HTTP_NOT_FOUND = "No vm found with provided id",
     *         Response::HTTP_UNPROCESSABLE_ENTITY = "Impossible to the execute the requested action on the vm"
     *     },
     *     responseMap={
     *         Response::HTTP_OK = {"class"=VirtualMachineInterface::class},
     *         Response::HTTP_NOT_FOUND = {"class"=ApiError::class},
     *         Response::HTTP_UNPROCESSABLE_ENTITY = {"class"=ApiError::class}
     *     }
     * )
     * @Rest\Put(
     *     "/vms/{vmId}/{action}",
     *     requirements={
     *         "vmId" = "i-[a-z0-9]+",
     *         "action" = "start|stop"
     *     }
     * )
     * @Rest\View()
     *
     * @param string $vmId   the Virtual Machine id
     * @param string $action the action to execute
     *
     * @return VirtualMachineInterface
     */
    public function putVmsStateAction($vmId, $action)
    {
        /* @var VmRepositoryInterface $vmRepository */
        $vmRepository = $this->get("cem_virtual_machine.vm.repository");

        try {
            $virtualMachine = $vmRepository->find($vmId);
            $virtualMachine->{$action}();
            $vmRepository->save($virtualMachine);
        } catch (VmNotFoundException $e) {
            throw new HttpException(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (VmStateUpdateException $e) {
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e->getMessage()
            );
        }

        return $virtualMachine;
    }
}
