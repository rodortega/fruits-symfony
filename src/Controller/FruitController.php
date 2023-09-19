<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use App\Form\FruitFilterType;
use App\Entity\Fruit;

class FruitController extends AbstractController
{
    /**
     *
     * Displays a paginated list of fruits and a filter form.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/', name: 'fruit_index')]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $form = $this->createForm(FruitFilterType::class);
        $form->handleRequest($request);

        $fruitsQuery = $entityManager->getRepository(Fruit::class)->findAllFruitsQuery();

        $filter = $request->query->all('fruit_filter');

        if (!empty($filter)) {
            if ($filter['name']) {
                $fruitsQuery->andWhere('f.name LIKE :name')
                    ->setParameter('name', '%' . $filter['name'] . '%');
            }
            if ($filter['family']) {
                $fruitsQuery->andWhere('f.family LIKE :family')
                    ->setParameter('family', '%' . $filter['family'] . '%');
            }
        }

        $fruits = $paginator->paginate(
            $fruitsQuery,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('fruit/index.html.twig', [
            'fruits' => $fruits,
            'form' => $form->createView(),
            'filter' => $filter
        ]);
    }

    /**
     *
     * Adds a fruit to the user's favorites.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/set-favorite', name: 'set_favorite', methods: 'POST')]
    public function addToFavorites(Request $request, EntityManagerInterface $entityManager)
    {
        $fruitId = $request->request->get('fruit_id');
        $fruits = $entityManager->getRepository(Fruit::class)->find($fruitId);

        if (!$fruits) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error saving as favorite. Fruit not found.'
            ]);
        }

        if ($fruits->isIsFavorite()) {
            $fruits->setIsFavorite(false);
            $message = 'Removed as favorite!';
        }
        else {
            $totalFavorites = $entityManager->getRepository(Fruit::class)->countFavoriteFruits();

            if ($totalFavorites >= 10) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Error saving as favorite. Maximum of 10 favorites.'
                ]);
            }

            $fruits->setIsFavorite(true);
            $message = 'Added as favorite!';
        }

        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => $message
        ]);
    }
}
