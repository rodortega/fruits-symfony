<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Fruit;

class FavoriteController extends AbstractController
{
    /**
     *
     * Displays a list of user's favorites and the sum of its nutritional contents
     *
     * @param EntityManagerInterface $entityManager
     */
    #[Route('/favorites', name: 'favorites_index')]
    public function favorites(EntityManagerInterface $entityManager)
    {
        $fruits = $entityManager->getRepository(Fruit::class)->findBy([
            'is_favorite' => 1,
        ]);

        $totalNutrition = [
            'calories' => 0,
            'carbohydrates' => 0,
            'fat' => 0,
            'sugar' => 0,
            'protein' => 0,
        ];

        foreach ($fruits as $fruit) {
            $totalNutrition['calories'] += $fruit->getCalories();
            $totalNutrition['carbohydrates'] += $fruit->getCarbohydrates();
            $totalNutrition['fat'] += $fruit->getFat();
            $totalNutrition['sugar'] += $fruit->getSugar();
            $totalNutrition['protein'] += $fruit->getProtein();
        }

        return $this->render('favorite/index.html.twig', [
            'fruits' => $fruits,
            'total_nutrition' => $totalNutrition
        ]);
    }
}
