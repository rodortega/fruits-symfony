<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Fruit;

/**
 * The FetchFruitsCommand fetches fruits from the Fruityvice API and saves them to the database.
 */
class FetchFruitsCommand extends Command
{
    private $httpClient;
    private $entityManager;
    private $mailer;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        parent::__construct();

        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    /**
     * Configures the command name and description.
     */
    protected function configure(): void
    {
        $this->setName('fruits:fetch')
            ->setDescription('Fetch fruits from Fruityvice API and save to the database');
    }

    /**
     * Executes the command to fetch fruits, save them to the database, and send an email notification.
     *
     * @param InputInterface $input The input interface.
     * @param OutputInterface $output The output interface.
     *
     * @return int The exit code (0 for success, 1 for failure).
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fetching fruits from Fruityvice API...');

        $apiUrl = 'https://www.fruityvice.com/api/fruit/all';

        try {
            $response = $this->httpClient->request('GET', $apiUrl);
            $fruitsData = $response->toArray();

            foreach ($fruitsData as $fruitData) {
                $fruit = new Fruit();
                $fruit->setName($fruitData['name']);
                $fruit->setFamily($fruitData['family']);
                $fruit->setFruitOrder($fruitData['order']);
                $fruit->setGenus($fruitData['genus']);
                $fruit->setCalories($fruitData['nutritions']['calories']);
                $fruit->setFat($fruitData['nutritions']['fat']);
                $fruit->setSugar($fruitData['nutritions']['sugar']);
                $fruit->setCarbohydrates($fruitData['nutritions']['carbohydrates']);
                $fruit->setProtein($fruitData['nutritions']['protein']);
                $fruit->setIsFavorite(false);

                $this->entityManager->persist($fruit);
            }

            $this->entityManager->flush();
            $output->writeln('Fruits saved to the database.');
            $this->sendEmailNotification();
        } catch (\Exception $e) {
            $output->writeln('Error fetching or saving fruits: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }

    /**
     * Sends an email notification when all fruits are successfully saved.
     */
    private function sendEmailNotification()
    {
        $email = (new Email())
            ->from('rodcarlos2121@gmail.com')
            ->to('rodcarlos2121@gmail.com')
            ->subject('Fruits Saved Successfully')
            ->text('All fruits have been saved to the database.');

        $this->mailer->send($email);
    }
}
