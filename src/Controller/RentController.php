<?php

namespace App\Controller;

use App\Entity\Rent;
use App\Form\RentType;
use App\Repository\RentRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Service\RentService;
use Swagger\Annotations as SWG;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Rest\Route("/api")
 */
class RentController extends AbstractFOSRestController
{
    /**
     * @var RentRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RentService
     */
    private $service;

    public function __construct
    (
        RentRepository $repository,
        EntityManagerInterface $em,
        RentService $service
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->service = $service;
    }

    /**
     * @SWG\Tag(name="Rent")
     * @Rest\Get("/rent")
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
     *     description="Вывод отданных в аренду книг",
     *     @Model(type=Rent::class)
     * )
     * @param ParamFetcherInterface $paramFetcher
     * @return JsonResponse|string
     */
    public function getAllRents(ParamFetcherInterface $paramFetcher)
    {
        $repository = $this->repository;

        try {
            return $repository->findAllRents(
                $paramFetcher->get('pagesize'),
                $paramFetcher->get('page')
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * @Rest\Get("/rent/{id}")
     * @SWG\Tag(name="Rent")
     * @SWG\Response(
     *     response="200",
     *     description="Вывод аренды по ID",
     *     @Model(type=Rent::class)
     * )
     * @param $id
     * @return JsonResponse
     */
    public function getRent(int $id)
    {
        return $this->repository->findOneById($id);
    }

    /**
     * @Rest\Post("/rent")
     * @SWG\Tag(name="Rent")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @SWG\Schema(
     *         @SWG\Property(property="book", type="integer"),
     *         @SWG\Property(property="renter", type="integer"),
     *         @SWG\Property(property="quantity", type="integer"),
     *         @SWG\Property(property="sum", type="integer"),
     *         @SWG\Property(property="days", type="integer")
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Добавление аренды",
     *     @Model(type=Rent::class)
     * )
     * @param Request $request
     * @return FormInterface|JsonResponse|string
     */
    public function createRent(Request $request)
    {
        $rent = new Rent();

        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(RentType::class, $rent, [
            'method' => 'POST',
            'em' => $this->em
        ]);

        try {
            $form->handleRequest($request);
            $form->submit($data);
            if ($form->isValid()) {
                $this->service->checkSumToRent($rent);
                $this->service->substractQuantity($rent);

                $this->em->persist($rent);
                $this->em->flush();

                return $this->getRent($rent->getId());
            }
            return $form;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @SWG\Tag(name="Rent")
     * @Rest\Delete("/rent/{id}")
     * @SWG\Response(
     *     response="200",
     *     description="Удаление аренды",
     *     @Model(type=Rent::class)
     * )
     * @param int $id
     * @return bool
     */
    public function deleteRent(int $id)
    {
        try {
            $rent = $this->repository->findOneById($id);

            $this->service->returnBooksToShop($rent);
            return $this->repository->deleteRent($id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}