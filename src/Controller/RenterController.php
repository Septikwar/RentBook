<?php

namespace App\Controller;

use App\Entity\Rent;
use App\Entity\Renter;
use App\Form\RenterType;
use App\Form\RentType;
use App\Repository\RenterRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Form\FormInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Rest\Route("/api")
 */
class RenterController extends AbstractFOSRestController
{
    /**
     * @var RenterRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct
    (
        RenterRepository $repository,
        EntityManagerInterface $em
    )
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @SWG\Tag(name="Renter")
     * @Rest\Get("/renter")
     * @Rest\QueryParam(
     *     name="pagesize",
     *     requirements="\d+",
     *     nullable=false,
     *     default="20",
     *     strict=true
     * )
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default="1",
     *     strict=true
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Вывод всех арендаторов",
     *     @Model(type=Rent::class)
     * )
     * @param ParamFetcherInterface $paramFetcher
     * @return JsonResponse|string
     */
    public function getAllRenters(ParamFetcherInterface $paramFetcher)
    {
        $repository = $this->repository;

        try {
            return $repository->findAllRenters(
                $paramFetcher->get('pagesize'),
                $paramFetcher->get('page')
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * @Rest\Get("/renter/{id}")
     * @SWG\Tag(name="Renter")
     * @SWG\Response(
     *     response="200",
     *     description="Вывод арендатора по ID",
     *     @Model(type=Renter::class)
     * )
     * @param $id
     * @return JsonResponse
     */
    public function getRenter(int $id)
    {
        return $this->repository->findOneById($id);
    }

    /**
     * @Rest\Post("/renter")
     * @SWG\Tag(name="Renter")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @SWG\Schema(
     *         @SWG\Property(property="name", type="string"),
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Добавление арендатора",
     *     @Model(type=Renter::class)
     * )
     * @param Request $request
     * @return FormInterface|JsonResponse|string
     */
    public function createRenter(Request $request)
    {
        $rent = new Renter();

        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(RenterType::class, $rent, [
            'method' => 'POST',
            'em' => $this->em
        ]);

        try {
            $form->handleRequest($request);
            $form->submit($data);
            if ($form->isValid()) {
                $this->em->persist($rent);
                $this->em->flush();

                return $this->getRenter($rent->getId());
            }
            return $form;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @SWG\Tag(name="Renter")
     * @Rest\Delete("/renter/{id}")
     * @SWG\Response(
     *     response="200",
     *     description="Удаление арендатора",
     *     @Model(type=Renter::class)
     * )
     * @param int $id
     * @return bool
     */
    public function deleteRenter(int $id)
    {
        try {
            return $this->repository->deleteRenter($id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}