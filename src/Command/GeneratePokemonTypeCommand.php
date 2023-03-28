<?php
// src/Command/GeneratePokemonTypeCommand.php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Pokemon;

class GeneratePokemonTypeCommand extends Command {
    protected static $defaultName = 'app:generate-pokemon-type';

    protected static $defaultDescription = "Generate pokemons and types in DB from API";

    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher) {
        $this->em = $entityManager;
        $this->hasher = $hasher;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $em = $this->em;
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://pokeapi.co/api/v2/pokemon?limit=9");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $output_api = curl_exec($curl);
        $json = json_decode($output_api, true);

        foreach ($json["results"] as $pokemon) {
            $pokemon_name= $pokemon["name"];
            $output->writeln($pokemon_name);

            $curl2 = curl_init ();
            curl_setopt($curl2, CURLOPT_URL, $pokemon["url"]);
            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1);
            $output_api2 = curl_exec($curl2);
            $json2 = json_decode($output_api2, true);
            foreach ($json2["types"] as $types) {
                $output->writeln($types["type"]["name"]);
            }
            $output->writeln("\n");
            curl_close($curl2);

            $pokemon = new Pokemon();
            $pokemon->setName($pokemon_name);
            $pokemon->setDescription("Description Test");

            $em->persist($pokemon);
        }
        $em->flush();
        curl_close($curl);

        $output->writeln("Test Succes");
        return Command::SUCCESS;
    }
}