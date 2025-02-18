<?php

namespace App\Tests\Controller;

use App\Entity\Facture;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FactureControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $factureRepository;
    private string $path = '/fact/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->factureRepository = $this->manager->getRepository(Facture::class);

        foreach ($this->factureRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Facture index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'facture[montant]' => 'Testing',
            'facture[date]' => 'Testing',
            'facture[id_rendez_vous]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->factureRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Facture();
        $fixture->setMontant('My Title');
        $fixture->setDate('My Title');
        $fixture->setId_rendez_vous('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Facture');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Facture();
        $fixture->setMontant('Value');
        $fixture->setDate('Value');
        $fixture->setId_rendez_vous('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'facture[montant]' => 'Something New',
            'facture[date]' => 'Something New',
            'facture[id_rendez_vous]' => 'Something New',
        ]);

        self::assertResponseRedirects('/fact/');

        $fixture = $this->factureRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getMontant());
        self::assertSame('Something New', $fixture[0]->getDate());
        self::assertSame('Something New', $fixture[0]->getId_rendez_vous());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Facture();
        $fixture->setMontant('Value');
        $fixture->setDate('Value');
        $fixture->setId_rendez_vous('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/fact/');
        self::assertSame(0, $this->factureRepository->count([]));
    }
}
