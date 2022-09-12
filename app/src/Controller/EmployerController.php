<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\FileUploadForm;
use App\Form\SearchForm;
use App\Repository\EmployerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployerController extends AbstractController
{
    private const ITEMS_PER_PAGE = 50;

    private EmployerRepository $employerRepository;

    public function __construct(EmployerRepository $employerRepository)
    {
        $this->employerRepository = $employerRepository;
    }

    /**
     * @Route("/list", name="list", methods={"GET"})
     */
    public function getList(Request $request): Response
    {
        $id = null;
        $searchForm = $this->createForm(SearchForm::class, ['method' => Request::METHOD_GET]);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $id = $searchForm->get('employerId')->getData();
        }

        $page = $request->query->getInt('page', 1);
        $pagination = $this->employerRepository->getListOfEmployers($id, $page, self::ITEMS_PER_PAGE);

        return $this->render(
            'list.html.twig',
            [
                'pagination' => $pagination,
                'form'  => $searchForm->createView()
            ]
        );
    }

    /**
     * @Route("/load", name="load_file", methods={"GET", "POST"})
     */
    public function loadFileForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FileUploadForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();

            $file = $uploadedFile->getRealPath();
            $this->employerRepository->saveDataFormFile($file);

            return $this->redirectToRoute('list');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/detail/{id}", name="detail", methods={"GET"})
     */
    public function detail(string $id, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->employerRepository->getAllChildrenByEmployer($id, $page, self::ITEMS_PER_PAGE);

        return $this->render('detail.html.twig', ['pagination' => $pagination, 'id' => $id]);
    }
}
