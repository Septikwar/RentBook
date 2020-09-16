<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rent;
use App\Form\BookType;
use App\Form\RentType;
use App\Repository\BookRepository;
use App\Repository\RentRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Rest\Route("/api")
 */
class BookController extends AbstractFOSRestController
{
    /**
     * @var BookRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct
    (
        BookRepository $repository,
        EntityManagerInterface $em
    )
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @SWG\Tag(name="Book")
     * @Rest\Get("/book")
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
     *     description="Вывод всех книг",
     *     @Model(type=Book::class)
     * )
     * @param ParamFetcherInterface $paramFetcher
     * @return JsonResponse|string
     */
    public function getAllBooks(ParamFetcherInterface $paramFetcher)
    {
        $repository = $this->repository;

        try {
            return $repository->findAllBooks(
                $paramFetcher->get('pagesize'),
                $paramFetcher->get('page')
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * @Rest\Get("/book/{id}")
     * @SWG\Tag(name="Book")
     * @SWG\Response(
     *     response="200",
     *     description="Вывод книги по ID",
     *     @Model(type=Book::class)
     * )
     * @param $id
     * @return JsonResponse
     */
    public function getBook(int $id)
    {
        return $this->repository->findOneById($id);
    }

    /**
     * @Rest\Post("/book")
     * @SWG\Tag(name="Book")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @SWG\Schema(
     *         @SWG\Property(property="name", type="string"),
     *         @SWG\Property(property="quantity", type="integer"),
     *         @SWG\Property(property="price", type="integer")
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Добавление книги",
     *     @Model(type=Book::class)
     * )
     * @param Request $request
     * @return FormInterface|JsonResponse|string
     */
    public function createRent(Request $request)
    {
        $rent = new Book();

        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(BookType::class, $rent, [
            'method' => 'POST',
            'em' => $this->em
        ]);

        try {
            $form->handleRequest($request);
            $form->submit($data);
            if ($form->isValid()) {
                $this->em->persist($rent);
                $this->em->flush();

                return $this->getBook($rent->getId());
            }
            return $form;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @SWG\Tag(name="Book")
     * @Rest\Delete("/book/{id}")
     * @SWG\Response(
     *     response="200",
     *     description="Удаление книги",
     *     @Model(type=Book::class)
     * )
     * @param int $id
     * @return bool
     */
    public function deleteBook(int $id)
    {
        try {
            return $this->repository->deleteBook($id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}